<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panel\Purchase\PurchaseRequest;
use App\Http\Requests\Panel\Transmission\TransferRequest;
use App\Http\Requests\Panel\Transmission\TransmissionRequest;
use App\Http\Traits\HasConfig;
use App\Http\Traits\HasLogin;
use App\Jobs\SendAppAlertsJob;
use App\Models\Bank;
use App\Models\Doller;
use App\Models\FinanceTransaction;
use App\Models\Invoice;
use App\Models\Otp;
use App\Models\Payment;
use App\Models\Service;
use App\Models\SiteService;
use App\Models\Transmission;
use App\Models\User;
use App\Models\Voucher;
use App\Services\SmsService\SatiaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use function Laravel\Prompts\error;
use function Termwind\ask;

class TransmissionController extends Controller
{
    use HasConfig,HasLogin;

    protected $inputsConfig;

    public function __construct()
    {
        $this->inputsConfig = $this;
        $this->inputsConfig->type = 'merikhArz';
    }

    public function index()
    {
        $banks = Bank::where('is_active', 1)->get();
        $services = Service::all();
        $dollar = Doller::orderBy('id', 'desc')->first();
        $user = Auth::user();
        return view('Panel.Transmission.index', compact('services', 'dollar', 'banks', 'user'));
    }

    public function store(TransmissionRequest $request)
    {
        try {
            $satiaService = new SatiaService();
            $inputs = request()->all();

            $dollar = Doller::orderBy('id', 'desc')->first();
            $balance = Auth::user()->getCreaditBalance();
            $user = Auth::user();
            $inputs['user_id'] = $user->id;

            if (isset($inputs['service_id'])) {
                $service = Service::find($inputs['service_id']);
                $voucherPrice = (floor(($dollar->DollarRateWithAddedValue() * $service->amount) / 10000) * 10000);
                if ($voucherPrice > $balance) {
                    return redirect()->route('panel.transmission.view')->withErrors(['Low_inventory' => "موجودی کیف پول شما کافی نیست"]);
                }

                $inputs['final_amount'] = $voucherPrice;
                $inputs['type'] = 'transmission';
                $inputs['status'] = 'requested';
                $inputs['time_price_of_dollars'] = $dollar->DollarRateWithAddedValue();
                $inputs['description'] = 'انتقال حواله یوتوپیا';

                $invoice = Invoice::create($inputs);

                $transition = $this->transmissionUtopia($inputs['transmission'], $service->amount);
                if (is_array($transition)) {
                    $finance = FinanceTransaction::create([
                        'user_id' => $user->id,
                        'amount' => $voucherPrice,
                        'type' => "withdrawal",
                        "creadit_balance" => ($balance - $voucherPrice),
                        'description' => 'انتقال ووچر و کسر مبلغ از کیف پول',
                        'time_price_of_dollars' => $dollar->DollarRateWithAddedValue(),
                    ]);
                    $invoice->update(['status' => 'finished']);
                    $transitionDelivery = Transmission::create(
                        [
                            'user_id' => $user->id,
                            'finance_id' => $finance->id,
                            'invoice_id' => $invoice->id,
                            'payee_account_name' => $transition['Payee_Account_Name'],
                            'payee_account' => $transition['Payee_Account'],
                            'payer_account' => $transition['Payer_Account'],
                            'payment_amount' => $transition['PAYMENT_AMOUNT'],
                            'payment_batch_num' => $transition['PAYMENT_BATCH_NUM'],
                            'type' => $this->inputsConfig ? $this->inputsConfig->type : 'perfectmoney'

                        ]
                    );
                    $message = "سلام انتقال حواله یوتوپیا انجام شد اطلاعات بیشتر در قسمت سوابق قابل دسترس می باشد.";
                    $satiaService->send($message, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));
                    return redirect()->route('panel.transfer.information', $transitionDelivery);

                } else {
                    $invoice->update(['status' => 'failed', 'description' => 'انتقال حواله یوتوپیا ناموفق بود و عملیات کسر موجودی از کیف پول شما متوقف شد.']);
                    return redirect()->route('panel.transfer.fail');
                }


            } elseif (isset($inputs['custom_payment'])) {

                $voucherPrice = (floor(($dollar->DollarRateWithAddedValue() * $inputs['custom_payment']) / 10000) * 10000);
                if ($voucherPrice > $balance) {
                    return redirect()->route('panel.transmission.view')->withErrors(['Low_inventory' => "موجودی کیف پول شما کافی نیست"]);
                }
                $inputs['final_amount'] = $voucherPrice;
                $inputs['type'] = 'transmission';
                $inputs['service_id_custom'] = $inputs['custom_payment'];
                $inputs['status'] = 'requested';
                $inputs['time_price_of_dollars'] = $dollar->DollarRateWithAddedValue();
                $inputs['description'] = 'انتقال حواله  یوتوپیا';
                $invoice = Invoice::create($inputs);

                $transition = $this->transmissionUtopia($inputs['transmission'], $inputs['custom_payment']);

                if (is_array($transition)) {
                    $finance = FinanceTransaction::create([
                        'user_id' => $user->id,
                        'amount' => $voucherPrice,
                        'type' => "withdrawal",
                        "creadit_balance" => ($balance - $voucherPrice),
                        'description' => 'انتقال ووچر و کسر مبلغ از کیف پول',
                        'time_price_of_dollars' => $dollar->DollarRateWithAddedValue()
                    ]);
                    $invoice->update(['status' => 'finished']);

                    $transitionDelivery = Transmission::create(
                        [
                            'user_id' => $user->id,
                            'finance_id' => $finance->id,
                            'invoice_id' => $invoice->id,
                            'payee_account_name' => $transition['Payee_Account_Name'],
                            'payee_account' => $transition['Payee_Account'],
                            'payer_account' => $transition['Payer_Account'],
                            'payment_amount' => $transition['PAYMENT_AMOUNT'],
                            'payment_batch_num' => $transition['PAYMENT_BATCH_NUM'],
                            'type' => $this->inputsConfig ? $this->inputsConfig->type : 'perfectmoney'

                        ]
                    );
                    $message = "سلام انتقال حواله یوتوپیا انجام شد اطلاعات بیشتر در قسمت سوابق قابل دسترس می باشد.";
                    $satiaService->send($message, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));
                    return redirect()->route('panel.transfer.information', $transitionDelivery);

                } else {
                    $invoice->update(['status' => 'failed', 'description' => 'انتقال حواله یوتوپیا ناموفق بود و عملیات کسر موجودی از کیف پول شما متوقف شد.']);
                    return redirect()->route('panel.transfer.fail');
                }
            } else {
                return redirect()->route('panel.transmission.view')->withErrors(['SelectInvalid' => "انتخاب شما معتبر نمیباشد"]);
            }
        } catch
        (\Exception $exception) {
            SendAppAlertsJob::dispatch('در انتقال وچچر از طریف کیف پول خطایی رخ داد سرویس یوتوپیا و سایر موارد چک شود')->onQueue('perfectmoney');
            Log::emergency(PHP_EOL . $exception->getMessage() . PHP_EOL);

            return redirect()->route('panel.transmission.view')->withErrors(['error' => "عملیات انتقال ووچر ناموفق بود در صورت کسر موجودی از کیف پول شما با پشتیبانی تماس حاصل فرمایید."]);
        }

    }

    public function transferFromThePaymentGateway(TransmissionRequest $request)
    {
        try {
            $balance = Auth::user()->getCreaditBalance();

            $dollar = Doller::orderBy('id', 'desc')->first();
            $inputs = $request->all();
            $user = Auth::user();
            $bank = Bank::find($inputs['bank']);
            $inputs['user_id'] = $user->id;
            $inputs['description'] = " انتقال ووچر از طریق $bank->name";

            if (isset($inputs['service_id'])) {
                $service = Service::find($inputs['service_id']);
                $voucherPrice = (floor(($dollar->DollarRateWithAddedValue() * $service->amount) / 10000) * 10000);

            } elseif (isset($inputs['custom_payment'])) {
                $inputs['service_id_custom'] = $inputs['custom_payment'];
                $voucherPrice = (floor(($dollar->DollarRateWithAddedValue() * $inputs['custom_payment']) / 10000) * 10000);
            } else {
                return redirect()->route('panel.transmission.view')->withErrors(['SelectInvalid' => "انتخاب شما معتبر نمیباشد"]);
            }

            $inputs['final_amount'] = $voucherPrice;
            $inputs['type'] = 'transmission';
            $inputs['status'] = 'requested';
            $inputs['bank_id'] = $bank->id;
            $inputs['time_price_of_dollars'] = $dollar->DollarRateWithAddedValue();
            $inputs['description'] = ' انتقال حواله یوتوپیا از طریق ' . $bank->name;


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
            $objBank->setUrlBack(route('panel.back.transferFromThePaymentGateway'));
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
            if (!$status) {
                $invoice->update(['status' => 'failed', 'description' => "به دلیل عدم ارتباط با بانک $bank->name سفارش انتقال حواله یوتوپیا  شما لغو شد "]);
                $financeTransaction->update(['description' => "به دلیل عدم ارتباط با بانک $bank->name سفارش شما لغو شد ", 'status' => 'fail']);

                return redirect()->route('panel.transmission.view')->withErrors(['error' => 'ارتباط با بانک فراهم نشد لطفا چند دقیقه بعد تلاش فرماید.']);
            }
            $token = $status;
            session()->put('transmission', $inputs['transmission']);
            session()->put('payment', $payment->id);
            session()->put('financeTransaction', $financeTransaction->id);
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

            return redirect()->route('panel.transmission.view')->withErrors(['error' => 'ارتباط با بانک فراهم نشد لطفا چند دقیقه بعد تلاش فرماید.']);
        }
    }

    public function transferFromThePaymentGatewayBack(Request $request)
    {
        try {
            $satiaService = new SatiaService();

            $dollar = Doller::orderBy('id', 'desc')->first();
            $user = Auth::user();
            $balance = Auth::user()->getCreaditBalance();
            $payment = Payment::find(session()->get('payment'));
            $financeTransaction = FinanceTransaction::find(session()->get('financeTransaction'));

            $bank = $payment->bank;
            $objBank = new $bank->class;
            $objBank->setBankModel($bank);

            Log::channel('bankLog')->emergency(PHP_EOL . " Bank return response from voucher transfer " . PHP_EOL . json_encode($request->all()) . PHP_EOL .
                'Bank message: ' . PHP_EOL . $objBank->transactionStatus() . PHP_EOL .
                'user ID :' . $user->id
                . PHP_EOL
            );
            $inputs = array_merge(request()->all(), request()->request->all());
            $invoice = $payment->invoice;
            if (!$objBank->backBank()) {
                $payment->update(
                    [
                        'RefNum' => $inputs['RefNum'] ?? null,
                        'ResNum' => $payment->order_id,
                        'state' => 'failed'

                    ]);
                $bankErrorMessage = "درگاه بانک {$bank->name} تراکنش شمارا به دلیل " . $objBank->transactionStatus() . " ناموفق اعلام کرد باتشکر سایناارز" . PHP_EOL . "پشتیبانی بانک {$bank->name}" . PHP_EOL . '021-6422';
                $satiaService->send($bankErrorMessage, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));
                $invoice->update(['status' => 'failed', 'description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->transactionStatus()]);
                $financeTransaction->update(['description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->transactionStatus(), 'status' => 'fail']);

                return redirect()->route('panel.transmission.view')->withErrors(['error' => 'پرداخت موفقیت آمیز نبود' . $objBank->transactionStatus()]);
            }
            $back_price = $objBank->verify($payment->amount);


            if ($back_price !== true or Payment::where("order_id", $payment->order_id)->count() > 1) {

                $bankErrorMessage = "درگاه بانک {$bank->name} تراکنش شمارا به دلیل " . $objBank->verifyTransaction($back_price) . " ناموفق اعلام کرد باتشکر سایناارز" . PHP_EOL . "پشتیبانی بانک {$bank->name}" . PHP_EOL . '021-6422';
                $satiaService->send($bankErrorMessage, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));

                $invoice->update(['status' => 'failed', 'description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->verifyTransaction($back_price)]);
                $financeTransaction->update(['description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->verifyTransaction($back_price), 'status' => 'fail']);

                Log::channel('bankLog')->emergency(PHP_EOL . "Bank Credit VerifyTransaction from voucher transfer : " . json_encode($request->all()) . PHP_EOL .
                    'Bank message: ' . $objBank->verifyTransaction($back_price) .
                    PHP_EOL .
                    'user Id: ' . $user->id
                    . PHP_EOL
                );
                return redirect()->route('panel.error', $payment->id);
            }

            $payment->update(
                [
                    'RefNum' => $inputs['RefNum'] ?? null,
                    'ResNum' => $payment->order_id,
                    'state' => 'finished'
                ]);

            if (isset($invoice->service_id)) {
                $service = $invoice->service;
                $amount = $service->amount;
            } else {
                $amount = $invoice->service_id_custom;
            }


            $transition = $this->transmissionUtopia(session()->get('transmission'), $amount);
            $invoice->update(['status' => 'finished']);
            if (is_array($transition)) {

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
                    'description' => 'انتقال ووچر و برداشت مبلغ از کیف پول',
                    'payment_id' => $payment->id,
                    'time_price_of_dollars' => $dollar->DollarRateWithAddedValue()
                ]);

                $transitionDelivery = Transmission::create(
                    [
                        'user_id' => $user->id,
                        'finance_id' => $finance->id,
                        'payee_account_name' => $transition['Payee_Account_Name'],
                        'invoice_id' => $invoice->id,
                        'payee_account' => $transition['Payee_Account'],
                        'payer_account' => $transition['Payer_Account'],
                        'payment_amount' => $transition['PAYMENT_AMOUNT'],
                        'payment_batch_num' => $transition['PAYMENT_BATCH_NUM'],
                        'type' => $this->inputsConfig ? $this->inputsConfig->type : 'perfectmoney'
                    ]
                );
                $message = "سلام انتقال حواله یوتوپیا انجام شد اطلاعات بیشتر در قسمت سوابق قابل دسترس می باشد.";
                $satiaService->send($message, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));
                return redirect()->route('panel.transfer.information', $transitionDelivery);

            } else {
                $invoice->update(['status' => 'finished', 'description' => 'پرداخت با موفقیت انجام شد به دلیل عدم ارتباط با یوتوپیا مبلغ کیف پول شما افزایش داده شد و شما میتوانید در یک ساعت آینده از کیف پول خود جهت انتقال ووچر اقدام نمایید']);

                $financeTransaction->update([
                    'user_id' => $user->id,
                    'voucher_id' => null,
                    'amount' => $payment->amount,
                    'type' => "deposit",
                    "creadit_balance" => $balance + $payment->amount,
                    'description' => 'پرداخت با موفقیت انجام شد به دلیل عدم ارتباط با یوتوپیا مبلغ کیف پول شما افزایش داده شد و شما میتوانید در یک ساعت آینده از کیف پول خود جهت انتقال ووچر اقدام نمایید',
                    'payment_id' => $payment->id,
                    'time_price_of_dollars' => $dollar->DollarRateWithAddedValue()

                ]);
                return redirect()->route('panel.transfer.fail');
            }
        } catch (\Exception $e) {
            Log::emergency(PHP_EOL . $e->getMessage() . PHP_EOL);
            SendAppAlertsJob::dispatch('در انتقال ووچر از درگاه باتکی خطایی رخ داده است لطفا پیگیری شود.')->onQueue('perfectmoney');
            return redirect()->route('panel.index')->withErrors(['error' => 'یک خطای غیر منتظره رخ داد لفطا از طریق پشتیبانی تیکت برنید']);
        }
    }

    public function information(Request $request, Transmission $transitionDelivery)
    {
        $transitionDelivery = $transitionDelivery->where('user_id', Auth::user()->id)->where("id", $transitionDelivery->id)->first();
        if ($transitionDelivery)
            return view('Panel.Transmission.TransmissionSuccessfully', compact('transitionDelivery'));
        else
            abort(404);
    }

    public function transmissionFail(Request $request, Invoice $invoice)
    {
        $user = Auth::user();
        $balance = $user->getCreaditBalance();
        $invoice = $invoice->where("user_id", $user->id)->orderBy('id', 'desc')->first();
        return view('Panel.Transmission.DeliveryFail', compact('invoice', 'balance'));

    }

    public function transfer(Request $request)
    {
        try {
//            SiteService::where('token', $request->headers->get('token'))->where('is_active', 'active')->first();
//            $siteService = SiteService::find(1);
            $request->request->add(['amount' => $request->get('amount')]);
            $request->request->add(['account' => $request->get('account')]);
            $validation = $this->transferValidation();
            $inputs = $request->all();
            if (!$validation->fails()) {
                session()->put('transfer', $request->getUri());
                $dollar = Doller::orderBy('id', 'desc')->first();
                $inputs['rial'] = $dollar->DollarRateWithAddedValue() * $inputs['amount'];
                $inputs['rial'] = (int)floor($inputs['rial']);
                $inputs['rial'] = numberFormat(substr($inputs['rial'], 0, strlen($inputs['rial']) - 1));
                return view('Panel.Transfer.index', compact('inputs'));
            }
            return view('Panel.Transfer.index', compact('inputs'))->withErrors($validation->errors());

        } catch (\Exception $e) {
            return redirect()->route('panel.index')->withErrors(['Error' => 'خطایی رخ داد لطفا از طریق پشتیبانی تیکت ثبت کنید']);

        }


    }

    public function transferConnectionBank(TransferRequest $request)
    {
        try {
            $balance = Auth::user()->getCreaditBalance();

            $dollar = Doller::orderBy('id', 'desc')->first();
            $inputs = $request->all();
            $user = Auth::user();
            $bank = Bank::where('is_active', 1)->first();
            $inputs['user_id'] = $user->id;
            $inputs['description'] = " انتقال ووچر از طریق $bank->name";

            if (isset($inputs['custom_payment'])) {
                $inputs['service_id_custom'] = $inputs['custom_payment'];
                $voucherPrice = (floor(($dollar->DollarRateWithAddedValue() * $inputs['custom_payment']) / 10000) * 10000);

            } else {
                return redirect()->route('panel.transfer.external', ['account' => $inputs['transmission'], 'amount' => $inputs['custom_payment']])->withErrors(['SelectInvalid' => "انتخاب شما معتبر نمیباشد"]);
            }

            $inputs['final_amount'] = $voucherPrice;
            $inputs['type'] = 'transmission';
            $inputs['status'] = 'requested';
            $inputs['bank_id'] = $bank->id;
            $inputs['time_price_of_dollars'] = $dollar->DollarRateWithAddedValue();
            $inputs['description'] = ' انتقال حواله یوتوپیا از طریق ' . $bank->name;


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
            $objBank->setUrlBack(route('panel.transfer.external.back-bank'));
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
            if (!$status) {
                $invoice->update(['status' => 'failed', 'description' => "به دلیل عدم ارتباط با بانک $bank->name سفارش انتقال حواله یوتوپیا  شما لغو شد "]);
                $financeTransaction->update(['description' => "به دلیل عدم ارتباط با بانک $bank->name سفارش شما لغو شد ", 'status' => 'fail']);

                return redirect()->route('panel.transfer.external', ['account' => $inputs['transmission'], 'amount' => $inputs['custom_payment']])->withErrors(['error' => 'ارتباط با بانک فراهم نشد لطفا چند دقیقه بعد تلاش فرماید.']);
            }
            $token = $status;
            session()->put('transmission', $inputs['transmission']);
            session()->put('payment', $payment->id);
            session()->put('financeTransaction', $financeTransaction->id);
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

            return redirect()->route('panel.transfer.external', ['account' => $inputs['transmission'], 'amount' => $inputs['custom_payment']])->withErrors(['error' => 'ارتباط با بانک فراهم نشد لطفا چند دقیقه بعد تلاش فرماید.']);
        }
    }

    public function transferConnectionBackBank(Request $request)
    {
        try {
            $satiaService = new SatiaService();

            $dollar = Doller::orderBy('id', 'desc')->first();
            $user = Auth::user();
            $balance = Auth::user()->getCreaditBalance();
            $payment = Payment::find(session()->get('payment'));
            $financeTransaction = FinanceTransaction::find(session()->get('financeTransaction'));

            $bank = $payment->bank;
            $objBank = new $bank->class;
            $objBank->setBankModel($bank);

            Log::channel('bankLog')->emergency(PHP_EOL . " Bank return response from voucher transfer " . PHP_EOL . json_encode($request->all()) . PHP_EOL .
                'Bank message: ' . PHP_EOL . $objBank->transactionStatus() . PHP_EOL .
                'user ID :' . $user->id
                . PHP_EOL
            );
            $inputs = array_merge(request()->all(), request()->request->all());

            $invoice = $payment->invoice;
            if (!$objBank->backBank()) {
                $payment->update(
                    [
                        'RefNum' => $inputs['RefNum'] ?? null,
                        'ResNum' => $payment->order_id,
                        'state' => 'failed'

                    ]);
                $bankErrorMessage = "درگاه بانک {$bank->name} تراکنش شمارا به دلیل " . $objBank->transactionStatus() . " ناموفق اعلام کرد باتشکر سایناارز" . PHP_EOL . "پشتیبانی بانک {$bank->name}" . PHP_EOL . '021-6422';
                $satiaService->send($bankErrorMessage, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));
                $invoice->update(['status' => 'failed', 'description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->transactionStatus()]);
                $financeTransaction->update(['description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->transactionStatus(), 'status' => 'fail']);

                return redirect()->route('panel.transfer.external')->withErrors(['error' => 'پرداخت موفقیت آمیز نبود' . $objBank->transactionStatus()]);
            }


            $back_price = $objBank->verify($payment->amount);

            if ($back_price !== true or Payment::where("order_id", $payment->order_id)->count() > 1) {

                $bankErrorMessage = "درگاه بانک {$bank->name} تراکنش شمارا به دلیل " . $objBank->verifyTransaction($back_price) . " ناموفق اعلام کرد باتشکر سایناارز" . PHP_EOL . "پشتیبانی بانک {$bank->name}" . PHP_EOL . '021-6422';
                $satiaService->send($bankErrorMessage, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));

                $invoice->update(['status' => 'failed', 'description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->verifyTransaction($back_price)]);
                $financeTransaction->update(['description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->verifyTransaction($back_price), 'status' => 'fail']);

                Log::channel('bankLog')->emergency(PHP_EOL . "Bank Credit VerifyTransaction from voucher transfer : " . json_encode($request->all()) . PHP_EOL .
                    'Bank message: ' . $objBank->verifyTransaction($back_price) .
                    PHP_EOL .
                    'user Id: ' . $user->id
                    . PHP_EOL
                );
                return redirect()->route('panel.error', $payment->id);
            }

            $payment->update(
                [
                    'RefNum' => $inputs['RefNum'] ?? null,
                    'ResNum' => $payment->order_id,
                    'state' => 'finished'
                ]);

            if (isset($invoice->service_id)) {
                $service = $invoice->service;
                $amount = $service->amount;
            } else {
                $amount = $invoice->service_id_custom;
            }


            $transition = $this->transmissionUtopia(session()->get('transmission'), $amount);
            $invoice->update(['status' => 'finished']);
            if (is_array($transition)) {

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
                    'description' => 'انتقال ووچر و برداشت مبلغ از کیف پول',
                    'payment_id' => $payment->id,
                    'time_price_of_dollars' => $dollar->DollarRateWithAddedValue(),
                ]);

                $transitionDelivery = Transmission::create(
                    [
                        'user_id' => $user->id,
                        'finance_id' => $finance->id,
                        'payee_account_name' => $transition['Payee_Account_Name'],
                        'invoice_id' => $invoice->id,
                        'payee_account' => $transition['Payee_Account'],
                        'payer_account' => $transition['Payer_Account'],
                        'payment_amount' => $transition['PAYMENT_AMOUNT'],
                        'payment_batch_num' => $transition['PAYMENT_BATCH_NUM'],
                        'type' => $this->inputsConfig ? $this->inputsConfig->type : 'perfectmoney'

                    ]
                );
                $message = "سلام انتقال حواله یوتوپیا انجام شد اطلاعات بیشتر در قسمت سوابق قابل دسترس می باشد.";
                $satiaService->send($message, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));
                return redirect()->route('panel.transfer.information', $transitionDelivery);

            } else {
                $invoice->update(['status' => 'finished', 'description' => 'پرداخت با موفقیت انجام شد به دلیل عدم ارتباط با یوتوپیا مبلغ کیف پول شما افزایش داده شد و شما میتوانید در یک ساعت آینده از کیف پول خود جهت انتقال ووچر اقدام نمایید']);

                $financeTransaction->update([
                    'user_id' => $user->id,
                    'voucher_id' => null,
                    'amount' => $payment->amount,
                    'type' => "deposit",
                    "creadit_balance" => $balance + $payment->amount,
                    'description' => 'پرداخت با موفقیت انجام شد به دلیل عدم ارتباط با یوتوپیا مبلغ کیف پول شما افزایش داده شد و شما میتوانید در یک ساعت آینده از کیف پول خود جهت انتقال ووچر اقدام نمایید',
                    'payment_id' => $payment->id,
                    'time_price_of_dollars' => $dollar->DollarRateWithAddedValue()

                ]);
                return redirect()->route('panel.transfer.fail');
            }
        } catch (\Exception $e) {
            Log::emergency(PHP_EOL . $e->getMessage() . PHP_EOL);
            SendAppAlertsJob::dispatch('در انتقال ووچر از درگاه باتکی خطایی رخ داده است لطفا پیگیری شود.')->onQueue('perfectmoney');
            return redirect()->route('panel.transfer.external')->withErrors(['error' => 'یک خطای غیر منتظره رخ داد لفطا از طریق پشتیبانی تیکت برنید']);
        }
    }

    public function voucher_price(Request $request)
    {
        $dollar = Doller::orderBy('id', 'desc')->first();
        $dollar_price = numberFormat((floor($dollar->DollarRateWithAddedValue() * 1) / 10000));
        $carts = [['value' => 1], ['value' => 2], ['value' => 5]];
        return view('Panel.Voucher.price', compact('dollar_price', 'carts'));
    }

    public function voucher(Request $request)
    {
        try {
            $validation = $this->voucherValidation();
            $inputs = $request->all();
            $bank = Bank::where('is_active', 1)->first();
            $dollar = Doller::orderBy('id', 'desc')->first();
            $dollar_price = (floor($dollar->DollarRateWithAddedValue() /10000 )*10000) / 10 ; //rial to toman
            if (!$validation->fails()) {
                session()->put('voucher', $request->getUri());
                if (isset($inputs['amount'])) {
                    $inputs['rial'] = (floor(($dollar->DollarRateWithAddedValue() * $inputs['amount']) / 10000) * 10000);
                    $inputs['rial'] = numberFormat(substr($inputs['rial'], 0, strlen($inputs['rial']) - 1));

                    $inputs['amount_rial'] = (floor(($dollar->amount_to_rials * $inputs['amount']) / 10000) * 10000);
                    $inputs['amount_rial'] = numberFormat(substr($inputs['amount_rial'], 0, strlen($inputs['amount_rial']) - 1));

//                    $inputs['Commission'] = $dollar->DollarRateWithAddedValue() * $inputs['amount'];
//                    $inputs['Commission'] = numberFormat(substr($inputs['Commission'], 0, strlen($inputs['Commission']) - 1));
                }
                return view('Panel.Voucher.buy', compact('inputs', 'bank','dollar_price'));
            }
            return view('Panel.Voucher.buy', compact('inputs', 'bank','dollar_price'))->withErrors($validation->errors());

        } catch (\Exception $e) {
            return redirect()->route('panel.index')->withErrors(['Error' => 'خطایی رخ داد لطفا از طریق پشتیبانی تیکت ثبت کنید']);

        }
    }

    public function mobile_submit(Request $request)
    {
        $validation = Validator::make($request->all(),
            [
                'mobile' => ['required', 'size:11', 'regex:/09\d{9}/'],
            ],
            [
                'mobile.required' => 'وارد کردن شماره موبایل الزامی است',
                'mobile.size' => 'شماره موبایل باید 11 رقم باشد',
                'mobile' => 'سماره موبایل بدرستی وارد نشده است!'
            ]
        );
        if ($validation->fails())
            return response()->json([
                'success' => false,
                'message' => $validation->errors()->first()
            ]);

        if ($request->input('verify_User')) {
            $user = User::firstOrCreate([
                'mobile' => $request->input('mobile')
            ], [

            ]);
            Auth::loginUsingId($user->id);
            return response()->json([
                'success' => true,
                'message' => 'کاربر با موفقیت وارد شد'
            ]);
        }


        $otp = $this->generateCode($request);
        if (!isset($otp->token))
            return response()->json([
                'success' => false,
                'message' => 'ارسال پیامک ناموفق لطفا به پشتیبانی اطلاع دهید'
            ]);

        return response()->json([
            'success' => true,
            'token' => $otp->token,
            'message' => 'پیامک رمز ورود ارسال گردید'
        ]);
    }

    public function verification_code_submit(Request $request)
    {
        $validation = Validator::make($request->all(),
            [
                'token' => ['required'],
                'code' => ['required', 'size:5']
            ],
            [
                'token.required' => 'وارد کردن توکن الزامی است',
                'code.required' => 'وارد کردن کد پیامک شده الزامی است',
                'code.sie' => 'کد وارد شده باید 5 رقم باشد!'
            ]
        );
        if ($validation->fails())
            return response()->json([
                'success' => false,
                'message' => $validation->errors()->first()
            ]);

        $otp = Otp::where('token', $request->token)->latest()->first();
        if (!$otp)
            return response()->json([
                'success' => false,
                'message' => 'توکن نا معتبر'
            ]);
        if ($otp->created_at < Carbon::now()->subMinutes(2))
            return response()->json([
                'success' => false,
                'message' => 'اعتبار کد به پایان رسیده، لطفا کد جدید دریافت نمایید'
            ]);
        if ($otp->code != $request->code)
            return response()->json([
                'success' => false,
                'message' => 'کد وارد شده صحیح نمی باشد!'
            ]);


        $user = User::firstOrCreate(['mobile' => $otp->mobile], [
            'mobile' => $otp->mobile
        ]);

        $otp->update(['seen_at' => date('Y/m/d H:i:s', time())]);

        Auth::loginUsingId($user->id);

        return response()->json([
            'success' => true,
            'message' => 'کد وارد شده صحیح است!'
        ]);
    }

    public function transfer_logout(Request $request)
    {
        if (Auth::check())
            Auth::logout();
        return response()->json([
            'success' => true,
            'message' => 'خروج با موفقیت انجام شد'
        ]);
    }
}
