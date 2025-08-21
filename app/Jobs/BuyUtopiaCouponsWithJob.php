<?php

namespace App\Jobs;

use App\Http\Traits\HasConfig;
use App\Models\Doller;
use App\Models\FinanceTransaction;
use App\Models\Voucher;
use App\Services\SmsService\SatiaService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\Skip;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Morilog\Jalali\Jalalian;

class BuyUtopiaCouponsWithJob implements ShouldQueue,ShouldBeUnique
{
    use Queueable,HasConfig;
    public $timeout = 0;
    public $tries = 3;
    protected $inputsConfig;
    /**
     * Create a new job instance.
     */
    public function __construct(public $invoice,public $payment)
    {
        $this->inputsConfig=$this;
        $this->onQueue('BuyUtopiaCouponsWithJob');
    }
    public function uniqueId(){
            return $this->invoice->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $satiaService=new SatiaService();
        try {
            $user =$this->invoice->user;
            $this->user=$this->invoice->user;
            $validationPurchasePermit = $this->purchasePermit($this->invoice, $this->payment);
            if ($validationPurchasePermit->purchasePermitStatus) {
                return;
            }


            $balance = $user->getCreaditBalance();
            $dollar = Doller::orderBy('id', 'desc')->first();
            $service = '';
            $amount = '';
            if (isset($this->invoice->service_id)) {
                $service = $this->invoice->service;
                $amount = $service->amount;
            } else {
                $amount = $this->invoice->service_id_custom;
            }

            $this->generateVoucherUtopia($amount);

            $voucher = Voucher::create(
                [
                    'user_id' => $user->id,
                    'invoice_id' => $this->invoice->id,
                    'status' => 'requested',
                    'description' => 'ارسال در خواست به سرویس یوتوپیا',
                ]
            );
            if (isset($this->invoice->service_id)) {

                $voucher->update([
                    'service_id' => $service->id
                ]);
            } else {
                $voucher->update([
                    "service_id_custom" => $amount
                ]);
            }
            if (is_array($this->PMeVoucher) and  isset($this->PMeVoucher['VOUCHER_CODE'])) {
                $voucher->update([
                    'status' => 'finished',
                    'description' => 'ارتباط با سرویس یوتوپیا موفقیت آمیز بود',
                    'code' => $this->PMeVoucher['VOUCHER_CODE']
                ]);
                Log::emergency("panel Controller :" . json_encode($this->PMeVoucher));

                FinanceTransaction::create([
                    'user_id' => $user->id,
                    'voucher_id' => $voucher->id,
                    'amount' => $this->invoice->amount,
                    'type' => "withdrawal",
                    "creadit_balance" => $balance - $this->payment->amount,
                    'description' => "خرید کارت هدیه {$amount} دلاری و کسر مبغ از کیف پول",
                    'payment_id' => $this->payment->id,
                    'time_price_of_dollars' => $dollar->DollarRateWithAddedValue()
                ]);
                if (isset($this->invoice->service_id)) {
                    $service = $this->invoice->service;
                    $payment_amount = $service->amount;
                } else {
                    $payment_amount = $this->invoice->service_id_custom;
                }

                $message = "سلام کارت هدیه  شما ایجاد شد کد ووچر {$this->PMeVoucher['VOUCHER_CODE']} اطلاعات بیشتر در قسمت سوابق قابل دسترس می باشد.";
                $satiaService->send($message, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));
                $voucher->finance=$voucher->financeTransaction->id;
                $voucher->jalaliDate=Jalalian::forge($voucher->created_at)->format('H:i:s Y/m/d');


            } else {
                $voucher->delete();
                $message = "پرداخت با موفقیت انجام شد به دلیل عدم ارتباط با یوتوپیا مبلغ کیف پول شما افزایش داده شد و شما میتوانید در یک ساعت آینده از کیف پول خود ووچر خودرا تهیه کنید";
                $satiaService->send($message, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));
            }
        } catch (\Exception $e) {
            Log::emergency(PHP_EOL . $e->getMessage() . PHP_EOL);
            SendAppAlertsJob::dispatch('در خرید یوتوپیا ازطریق جاوااسکریپت خطایی به وجود آمد لطفا سرویس یوتوپیا چک شود')->onQueue('perfectmoney');

        }
    }
}
