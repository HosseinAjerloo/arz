<?php

namespace App\Http\Controllers\panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panel\Transmission\TransferRequest;
use App\Jobs\SendAppAlertsJob;
use App\Models\Bank;
use App\Models\Doller;
use App\Models\FastPayment;
use App\Models\FinanceTransaction;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Rules\Panel\Transmission\DecimalRule;
use App\Rules\PAYERACCOUNTRule;
use App\Services\SmsService\SatiaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FastPaymentController extends Controller
{
    public function index(Request $request){
        try {
            $request->request->add(['amount' => $request->get('amount')]);
            $request->request->add(['account' => $request->get('account')]);
            $request->request->add(['pay_id' => $request->get('pay_id')]);
            $request->request->add(['url_back' => $request->get('url_back')]);
            $validation = $this->voucherValidation();
            $inputs = $request->all();
            $bank = Bank::find(2);
            $dollar = Doller::orderBy('id', 'desc')->first();
            $dollar_price = (floor($dollar->DollarRateWithAddedValue() / 10000) * 10000) / 10; //rial to toman
            if (!$validation->fails()) {
                session()->put('voucher', $request->getUri());
                if (isset($inputs['amount'])) {
                    $inputs['rial'] = (floor(($dollar->DollarRateWithAddedValue() * $inputs['amount']) / 10000) * 10000);
                    $inputs['rial'] = numberFormat(floor($inputs['rial']/10));
                    $inputs['amount_rial'] = (floor(($dollar->amount_to_rials * $inputs['amount']) / 10000) * 10000);
                    $inputs['amount_rial'] = numberFormat(floor($inputs['amount_rial']/10));
                }
                return view('Panel.Gateway.index', compact('inputs', 'bank', 'dollar_price'));
            }
            return view('Panel.Gateway.index', compact('inputs', 'bank', 'dollar_price'))->withErrors($validation->errors());

        } catch (\Exception $e) {
            return redirect()->route('panel.index')->withErrors(['Error' => 'خطایی رخ داد لطفا از طریق پشتیبانی تیکت ثبت کنید']);

        }
    }

    public function gatewayFastPayment(TransferRequest $request){
        try {
            $balance = Auth::user()->getCreaditBalance();

            $dollar = Doller::orderBy('id', 'desc')->first();
            $inputs = $request->all();
            $user =  User::firstOrCreate([
                'mobile' => $inputs['mobile']
            ]);
            if (!Auth::hasUser())
            {
                Auth::loginUsingId($user->id);
            }
            $bank = Bank::find(2);
            $inputs['user_id'] = $user->id;
            $inputs['description'] = " انتقال سریع ووچر $bank->name";

            if (isset($inputs['custom_payment'])) {
                $inputs['service_id_custom'] = $inputs['custom_payment'];
                $voucherPrice = (floor(($dollar->DollarRateWithAddedValue() * $inputs['custom_payment']) / 10000) * 10000);

            } else {
                return redirect()->route('panel.fast-gateway-view', ['account' => $inputs['transmission']??'', 'amount' => $inputs['custom_payment']??'','pay_id'=>$inputs['pay_id']??'','url_back'=>urldecode($inputs['url_back']??'')])->withErrors(['SelectInvalid' => "انتخاب شما معتبر نمیباشد"]);
            }

            $inputs['final_amount'] = $voucherPrice;
            $inputs['type'] = 'fastPayment';
            $inputs['status'] = 'requested';
            $inputs['bank_id'] = $bank->id;
            $inputs['time_price_of_dollars'] = $dollar->DollarRateWithAddedValue();
            $inputs['description'] = 'پرداخت سریع حواله از طریق ' . $bank->name;


            $invoice = Invoice::create($inputs);
            $objBank = new $bank->class;
            $objBank->setTotalPrice($voucherPrice);
            $payment = Payment::create(
                [
                    'bank_id' => $bank->id,
                    'invoice_id' => $invoice->id,
                    'amount' => $voucherPrice,
                    'state' => 'requested',

                ]
            );
            $payment->update(['order_id' => $payment->id + Payment::transactionNumber]);
            $objBank->setOrderID($payment->id + Payment::transactionNumber);
            $objBank->setBankUrl($bank->url);
            $objBank->setTerminalId($bank->terminal_id);
            $objBank->setUrlBack(route('panel.fast-gateway-payment-back'));
            $objBank->setBankModel($bank);


            $status = $objBank->payment();
            $financeTransaction = FinanceTransaction::create([
                'user_id' => $user->id,
                'amount' => $payment->amount,
                'type' => "bank",
                "creadit_balance" => $balance,
                'description' => " ارتباط با بانک $bank->name",
                'payment_id' => $payment->id,
            ]);
            $fastPayment = FastPayment::create([
                'invoice_id' => $invoice->id,
                'amount' => $inputs['custom_payment'],
                'account' => $inputs['transmission'],
                'pay_id' => $inputs['pay_id'],
                'url_back' => $inputs['url_back'],
                'finance_id' => $financeTransaction->id
            ]);
            if (!$status) {

                $invoice->update(['status' => 'failed', 'description' => "به دلیل عدم ارتباط با بانک $bank->name سفارش انتقال حواله یوتوپیا  شما لغو شد "]);
                $financeTransaction->update(['description' => "به دلیل عدم ارتباط با بانک $bank->name سفارش شما لغو شد ", 'status' => 'fail']);
                return redirect()->route('panel.fast-gateway-view', ['account' => $inputs['transmission']??'', 'amount' => $inputs['custom_payment']??'','pay_id'=>$inputs['pay_id']??'','url_back'=>urldecode($inputs['url_back']??'')])->withErrors(['SelectInvalid' => "انتخاب شما معتبر نمیباشد"]);
            }
            $token = $status;
            session()->put('transmission', $inputs['transmission']);
            session()->put('payment', $payment->id);
            session()->put('financeTransaction', $financeTransaction->id);
            session()->put('fastPayment', $fastPayment->id);

            Log::channel('bankLog')->emergency(PHP_EOL . 'Connect to the bank to transfer the voucher '
                . PHP_EOL .
                'Name of the bank: ' . $bank->name
                . PHP_EOL .
                'payment price: ' . $voucherPrice
                . PHP_EOL .
                'payment date: ' . Carbon::now()->toDateTimeString()
                . PHP_EOL .
                'user ID: ' . $user->id
                . PHP_EOL
            );
            return $objBank->connectionToBank($token);

        } catch (\Exception $e) {
            Log::emergency(PHP_EOL . $e->getMessage() . PHP_EOL);
            SendAppAlertsJob::dispatch('در ارتباط با درگاه پرداخت برای انتقال ووچر خطایی رخ داده است لطفا پیگیری کنید')->onQueue('perfectmoney');
            return redirect()->route('panel.fast-gateway-view', ['account' => $inputs['transmission']??'', 'amount' => $inputs['custom_payment']??'','pay_id'=>$inputs['pay_id']??'','url_back'=>urldecode($inputs['url_back']??'')])->withErrors(['SelectInvalid' => "خطایی رخ داد لطفا مجددا تلاش فرمایید"]);

        }
    }
    public function gatewayFastPaymentBack(Request $request){
        try {
            $satiaService = new SatiaService();

            $dollar = Doller::orderBy('id', 'desc')->first();
            $user = Auth::user();
            $balance = Auth::user()->getCreaditBalance();
            $inputs = $request->all();
            $payment = Payment::find(session()->get('payment'));
            $financeTransaction = FinanceTransaction::find(session()->get('financeTransaction'));
            $fastPayment = FastPayment::find(session()->get('fastPayment'));

            $bank = $payment->bank;
            $objBank = new $bank->class;
            $objBank->setBankModel($bank);

            Log::channel('bankLog')->emergency(PHP_EOL . " Bank return response from voucher transfer " . PHP_EOL . json_encode($request->all()) . PHP_EOL .
                'Bank message: ' . PHP_EOL . $objBank->transactionStatus() . PHP_EOL .
                'user ID :' . $user->id
                . PHP_EOL
            );
            $invoice = $payment->invoice;
            if (!$objBank->backBank()) {
                $payment->update(
                    [
                        'RefNum' => null,
                        'ResNum' => $inputs['ResNum'],
                        'state' => 'failed'

                    ]);
                $bankErrorMessage = "درگاه {$bank->name} تراکنش شمارا به دلیل " . $objBank->transactionStatus() . " ناموفق اعلام کرد باتشکر سایناارز";
                $satiaService->send($bankErrorMessage, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));
                $invoice->update(['status' => 'failed', 'description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->transactionStatus()]);
                $financeTransaction->update(['description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->transactionStatus(), 'status' => 'fail']);

                return redirect()->route('panel.fast-gateway-status', $fastPayment)->withErrors(['error' => 'پرداخت موفقیت آمیز نبود' . $objBank->transactionStatus()]);
            }


            $back_price = $objBank->verify($payment->amount);

            if ($back_price !== true or Payment::where("order_id", $inputs['ResNum'])->count() > 1) {

                $bankErrorMessage = "درگاه بانک {$bank->name} تراکنش شمارا به دلیل " . $objBank->verifyTransaction($back_price);
                $satiaService->send($bankErrorMessage, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));

                $invoice->update(['status' => 'failed', 'description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->verifyTransaction($back_price)]);
                $financeTransaction->update(['description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->verifyTransaction($back_price), 'status' => 'fail']);

                Log::channel('bankLog')->emergency(PHP_EOL . "Bank Credit VerifyTransaction from voucher transfer : " . json_encode($request->all()) . PHP_EOL .
                    'Bank message: ' . $objBank->verifyTransaction($back_price) .
                    PHP_EOL .
                    'user Id: ' . $user->id
                    . PHP_EOL
                );
                return redirect()->route('panel.fast-gateway-status', $fastPayment)->withErrors('پرداخت موفقیت آمیزنبود در صورت کسر بدهی در 48 ساعت آینده به حساب شما عودت میگردد');
            }

            $payment->update(
                [
                    'RefNum' => $inputs['RefNum'],
                    'ResNum' => $inputs['ResNum'],
                    'state' => 'finished'
                ]);


            $invoice->update(['status' => 'finished']);

            $financeTransaction->update([
                'user_id' => $user->id,
                'amount' => $payment->amount,
                'type' => "deposit",
                "creadit_balance" => $balance + $payment->amount,
                'description' => ' افزایش کیف پول',
                'payment_id' => $payment->id,
                'time_price_of_dollars' => $dollar->DollarRateWithAddedValue()
            ]);

            $finance = FinanceTransaction::create([
                'user_id' => $user->id,
                'amount' => $payment->amount,
                'type' => "withdrawal",
                "creadit_balance" => $financeTransaction->creadit_balance - $payment->amount,
                'description' => 'پرداخت سریع از کیف پول',
                'payment_id' => $payment->id,
                'time_price_of_dollars' => $dollar->DollarRateWithAddedValue(),
            ]);

            \App\Jobs\ReportTheSituationToHologateJob::dispatch($fastPayment)->onQueue('HologateReport')->delay(now()->addMinutes(1));

            $message = "با سلام پرداخت شما با شماره سفارش {$fastPayment->pay_id} باموفقیت انجام شد  باتشکر".PHP_EOL.'ساینا ارز';
            $satiaService->send($message, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));
            return redirect()->route('panel.fast-gateway-status', $fastPayment);


        } catch (\Exception $e) {
            Log::emergency(PHP_EOL . $e->getMessage() . PHP_EOL);
            SendAppAlertsJob::dispatch('در پرداخت سریع از درگاه باتکی خطایی رخ داده است لطفا پیگیری شود.')->onQueue('perfectmoney');
            return redirect()->route('panel.transfer.external')->withErrors(['error' => 'یک خطای غیر منتظره رخ داد لفطا از طریق پشتیبانی تیکت برنید']);
        }
    }

    public function fastPaymentStatus(FastPayment $fastPayment){
        dd($fastPayment);
    }
    protected function voucherValidation()
    {
        return Validator::make(request()->all(),
            [
                'amount' => [ 'numeric', "max:" . env('Safe_Daily_Purchase_Limit',60), 'min:0.1', new DecimalRule()],
                'account' => ["required", "min:9", "max:9", new PAYERACCOUNTRule()],
                'pay_id' => ["required"],
                'url_back' => ["required"],
            ],
            [
                'amount.required' => 'وارد کردن مبلغ حواله الزامی است',
                'url_back.required' => 'آدرس پذیرنده الزامی است',
                'pay_id.required' => 'شماره فاکتور نامعتبر میباشد',
                'amount.numeric' => 'مبلغ حواله باید به صورت عددی باشد',
                'amount.max' => 'مبلغ حواله نباید بزرگ تر از 20 باشد',
                'amount.min' => 'مبلغ حواله نباید کوچک  تر از 1 باشد',
                'account.required' => 'وارد کردن شماره حساب حواله الزامی است',
                'account.max' => 'حداکثر طول شماره حساب حواله باید 9 کاراکتر باشد',
                'account.min' => 'حداقل طول شماره حساب حواله باید 9 کاراکتر باشد',
            ]

        );
    }
}
