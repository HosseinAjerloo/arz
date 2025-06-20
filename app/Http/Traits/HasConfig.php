<?php

namespace App\Http\Traits;


use App\Jobs\SendAppAlertsJob;
use App\Models\Invoice;
use App\Models\Transmission;
use App\Models\TransmissionsBank;
use App\Models\User;
use App\Models\Utopia;
use App\Models\Voucher;
use App\Models\VouchersBank;
use App\Rules\Panel\Transmission\DecimalRule;
use App\Rules\PAYERACCOUNTRule;
use AyubIRZ\PerfectMoneyAPI\PerfectMoneyAPI;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;


trait HasConfig
{
    protected $PMeVoucher = null;
    protected $redirectTo = 'panel.purchase.view';
    protected $message;

    protected $purchasePermitStatus = false;


    public function validationFiledUser()
    {
        $classOpp = get_class();
        if (isset($classOpp) and $classOpp == 'App\Models\User') {
            $user = $this;
        } else {
            $user = Auth::user();
        }
        if ($user) {
            if (empty($user->name) || empty($user->family) || empty($user->national_code) || empty($user->mobile) || empty($user->tel) || empty($user->address) || empty($user->email)) {
                return true;
            } else return false;
        }

    }

    protected function generateVoucher($amount)
    {
        $voucher = VouchersBank::where('status', 'new')->where("amount", $amount)->first();
        if ($voucher) {
            $this->PMeVoucher['VOUCHER_NUM'] = $voucher->serial;
            $this->PMeVoucher['VOUCHER_CODE'] = $voucher->code;
            $voucher->update(['status' => 'used']);
        } else {
            $PM = new PerfectMoneyAPI(env('PM_ACCOUNT_ID'), env('PM_PASS'));
            $PMeVoucher = $PM->createEV(env('PAYER_ACCOUNT'), $amount);
            if (is_array($PMeVoucher) and isset($PMeVoucher['VOUCHER_NUM']) and isset($PMeVoucher['VOUCHER_CODE'])) {
                $this->PMeVoucher = $PMeVoucher;
            }
        }

    }

    protected function generateVoucherUtopia($amount)
    {
        $this->inputsConfig->payment_amount=$amount;
        $this->inputsConfig->type = 'merikhArz';
        $token = 'USD-' . rand(1, 9) . Str::random(3) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4);
        $this->verifay(strtoupper($token));
    }

    protected function verifay($token)
    {
        $user=Auth::user();
        $voucher = Voucher::where('code', $token)->first();
//        $utopia=Utopia::where('utopia_voucher',$token)->first();
        if ($voucher){

            $this->generateVoucherUtopia($this->inputsConfig->payment_amount);
        }
        else{

            $this->inputsConfig->hostValue=[
                'utopia_voucher' => $token,
                'validate' => $this->inputsConfig->type,
                'amount' => $this->inputsConfig->payment_amount,
                'mobile'=>$user->mobile??null
            ];

            if (!$this->requestToHost()){
                return false;
            }
            $this->PMeVoucher['VOUCHER_CODE'] = $token;
        }

    }





    protected function generateTokenTransmissionUtopia()
    {
        $token = bin2hex(openssl_random_pseudo_bytes(32));
       return $this->verifayTokenTransmissionUtopia(strtoupper($token));
    }

    protected function verifayTokenTransmissionUtopia($token)
    {
        $transmissionUtopia = Transmission::where('payment_batch_num', $token)->first();
//        $utopia=Utopia::where('hash',$token)->first();
        if ($transmissionUtopia)
            $this->generateTokenTransmissionUtopia();
        else
            return $token;

    }


    protected function transmissionUtopia($transmission, $amount)
    {
        if ($transmission != env('DESTINATION_REMITTANCE_Utopia'))
            return false;


            if ($this->customVoucherTransfer($amount))
            {
                return $this->sendReference();
            }
            return false;

    }

    protected function sendReference(): array
    {
        $PMeVoucher = [];
        $PMeVoucher['PAYMENT_AMOUNT'] = $this->inputsConfig->payment_amount;
        $PMeVoucher['PAYMENT_BATCH_NUM'] =  $this->inputsConfig->payment_batch_num;
        $PMeVoucher['Payer_Account'] = env('DESTINATION_REMITTANCE_Utopia');
        $PMeVoucher['Payee_Account'] = env('DESTINATION_REMITTANCE_Utopia');
        $PMeVoucher['Payee_Account_Name'] = 'merikhArz';
        return $PMeVoucher;
    }




    protected function purchasePermit($invoice, $payment)
    {

        $user = Auth::user();
        $balance = Auth::user()->getCreaditBalance();
        $invoice = Invoice::where('id', $invoice->id)->where('user_id', $user->id)->where("status", 'finished')->first();
        $voucher = $invoice->voucher;

        if ($voucher) {
            $this->message = ['error' => "این سفارش قبلا توسط شما خریداری شده است لطفا جهت مشاهده سفارش از منوی داشبورد به قسمت سفارشات مراجعه فرمایید. "];
            $this->purchasePermitStatus = true;
            return $this;
        }

        if ($balance < $payment->amount) {
            $this->message = ['error' => "شارژ کیف پول  شما جهت خریداری کارت هدیه کافی نمیباشد لطفا کیف پول خود را شارژ فرمایید و مجددا خرید فرمایید.  "];
            $this->purchasePermitStatus = true;
            return $this;
        }
        return $this;


    }

    protected function redirectFunction()
    {
        return redirect()->route($this->redirectTo)->withErrors($this->message);
    }


    protected function transferValidation()
    {
        return Validator::make(request()->all(),
            [
                'amount' => ['required', 'numeric', "max:" . env('Daily_Purchase_Limit'), 'min:0.1', new DecimalRule()],
                'account' => ["required", "min:9", "max:9", new PAYERACCOUNTRule()]
            ],
            [
                'amount.required' => 'وارد کردن مبلغ حواله الزامی است',
                'amount.numeric' => 'مبلغ حواله باید به صورت عددی باشد',
                'amount.max' => 'مبلغ حواله نباید بزرگ تر از 20 باشد',
                'amount.min' => 'مبلغ حواله نباید کوچک  تر از 1 باشد',
                'account.required' => 'وارد کردن شماره حساب حواله الزامی است',
                'account.max' => 'حداکثر طول شماره حساب حواله باید 9 کاراکتر باشد',
                'account.min' => 'حداقل طول شماره حساب حواله باید 9 کاراکتر باشد',
            ]
        );
    }
    protected function voucherValidation()
    {
        return Validator::make(request()->all(),
            [
                'amount' => [ 'numeric', "max:" . env('Safe_Daily_Purchase_Limit',60), 'min:0.1', new DecimalRule()],
            ],
            [
                'amount.numeric' => 'مبلغ حواله باید به صورت عددی باشد',
            ]
        );
    }
    protected function customVoucherTransfer($amount)
    {
        $user=Auth::user();
        $this->inputsConfig->payment_amount = $amount;
        $this->inputsConfig->payment_batch_num=$this->generateTokenTransmissionUtopia();
        $this->inputsConfig->type = 'merikhArz';

        $this->inputsConfig->hostValue=[
            'hash' => $this->inputsConfig->payment_batch_num,
            'validate' => $this->inputsConfig->type,
            'amount' => $this->inputsConfig->payment_amount,
            'mobile'=>$user->mobile??null

        ];
        if (!$this->requestToHost()){
            return false;
        }

        return true;
    }

    protected function insertUtopia():bool{
        try {
            $result=Utopia::create($this->inputsConfig->hostValue);
            return $result? true:false;
        }catch (\Exception $exception){
            Log::emergency("There was a problem connecting to the Turkish server to write the voucher code. ".PHP_EOL. $exception->getMessage().PHP_EOL.$exception->getMessage());
            return false;
        }

    }
    protected function requestToHost(): bool
    {
        try {
            $response = Http::timeout(50)->withHeaders([
                'token' => env('SAINAEX_TOKEN')
            ])->withoutVerifying()->post(env('SAINAEX_REQUEST'),
                $this->inputsConfig->hostValue
               );
            $body = json_decode($response->body());
            if (isset($body->success) and $body->success)
                return true;
            return false;
        } catch (\Exception $exception) {
            Log::emergency('con not connection to request host ' . env('SAINAEX_REQUEST') . ' ' . $exception->getMessage());
            SendAppAlertsJob::dispatch('خطا در برقراری ارتباط داخلی سرور')->onQueue('perfectmoney');
            return false;
        }
    }


}
