<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panel\Purchase\PurchaseRequest;
use App\Http\Requests\Panel\Purchase\PurchaseThroughTheBankRequest;
use App\Http\Requests\Panel\WalletCharging\WalletChargingRequest;
use App\Http\Traits\HasConfig;
use App\Jobs\SendAppAlertsJob;
use App\Models\Bank;
use App\Models\Doller;
use App\Models\FinanceTransaction;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Voucher;
use App\Notifications\IsEmptyUserInformationNotifaction;
use App\Services\BankService\Saman;
use App\Services\SmsService\SatiaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use AyubIRZ\PerfectMoneyAPI\PerfectMoneyAPI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Response;
use Morilog\Jalali\Jalalian;
use function Laravel\Prompts\alert;


class PanelController extends Controller
{
    use HasConfig;
    protected $inputsConfig;
    public function __construct()
    {
        $this->inputsConfig=$this;
        return true;
    }

    public function index()
    {
        return view('Panel.index');
    }

    public function contactUs()
    {
        return view('Panel.ContactUs.ContactUs');
    }


    public function purchase()
    {

        $banks = Bank::where('is_active', 1)->get();
        $dollar = Doller::orderBy('id', 'desc')->first();
        $user=Auth::user();
        return view('Panel.Utopia.index',compact('user','banks','dollar'));
    }

    public function store(PurchaseRequest $request)
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
                $voucherPrice=( floor(($dollar->DollarRateWithAddedValue() *  $service->amount) /10000 )*10000);

                if ($voucherPrice > $balance) {
                    return redirect()->route('panel.purchase.view')->withErrors(['Low_inventory' => "موجودی کیف پول شما کافی نیست"]);
                }

                $inputs['final_amount'] = $voucherPrice;
                $inputs['type'] = 'service';
                $inputs['status'] = 'requested';
                $inputs['time_price_of_dollars'] = $dollar->DollarRateWithAddedValue();
                $inputs['description'] = 'خرید کارت هدیه یوتوپیا';
                $invoice = Invoice::create($inputs);

                $this->generateVoucherUtopia($service->amount);

                $voucher = Voucher::create(
                    [
                        'user_id' => $user->id,
                        'service_id' => $inputs['service_id'],
                        'invoice_id' => $invoice->id,
                        'status' => 'requested',
                        'description' => 'ارسال در خواست به سرویس یوتوپیا'
                    ]
                );
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
                        'amount' => $voucherPrice,
                        'type' => "withdrawal",
                        "creadit_balance" => ($balance - $voucherPrice),
                        'description' => "خرید کارت هدیه {$service->amount} دلاری و کسر مبغ از کیف پول",
                        'time_price_of_dollars' => $dollar->DollarRateWithAddedValue()

                    ]);
                    $invoice->update(['status' => 'finished']);
                    $payment_amount = $service->amount;
                    if ($this->validationFiledUser()) {
                        $request->session()->put('voucher_id', $voucher->id);
                        $request->session()->put('amount_voucher', $payment_amount);
                    }
                    $message = "سلام کارت هدیه  شما ایجاد شد اطلاعات بیشتر در قسمت سوابق قابل دسترس می باشد.";
                    $satiaService->send($message, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));
                    return redirect()->route('panel.delivery')->with(['voucher' => $voucher, 'payment_amount' => $payment_amount]);

                } else {
                    $voucher->delete();

                    $invoice->update(['status' => 'failed', 'description' => 'خرید کارت هدیه یوتوپیا ناموفق بود و عملیات کسر موجودی از کیف پول متوقف شد']);

                    Log::emergency("perfectmoney error : " . $this->PMeVoucher['ERROR']);
                    return redirect()->route('panel.purchase.view')->withErrors(['error' => "عملیات خرید ووچر ناموفق بود در صورت کسر موجودی از کیف پول شما با پشتیبانی تماس حاصل فرمایید."]);
                }

            } elseif (isset($inputs['custom_payment'])) {

                $voucherPrice=( floor(($dollar->DollarRateWithAddedValue() * $inputs['custom_payment']) /10000 )*10000);

                if ($voucherPrice > $balance) {
                    return redirect()->route('panel.purchase.view')->withErrors(['Low_inventory' => "موجودی کیف پول شما کافی نیست"]);
                }
                $inputs['final_amount'] = $voucherPrice;
                $inputs['type'] = 'service';
                $inputs['service_id_custom'] = $inputs['custom_payment'];
                $inputs['status'] = 'requested';
                $inputs['time_price_of_dollars'] = $dollar->DollarRateWithAddedValue();
                $inputs['description'] = 'خرید کارت هدیه یوتوپیا';
                $invoice = Invoice::create($inputs);

                $this->generateVoucherUtopia($inputs['custom_payment']);


                $voucher = Voucher::create(
                    [
                        'user_id' => $user->id,
                        'invoice_id' => $invoice->id,
                        'status' => 'requested',
                        'description' => 'ارسال در خواست به سرویس یوتوپیا',
                        "service_id_custom" => $inputs['custom_payment']
                    ]
                );
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
                        'amount' => $voucherPrice,
                        'type' => "withdrawal",
                        "creadit_balance" => ($balance - $voucherPrice),
                        'description' => "خرید کارت هدیه {$inputs['custom_payment']} دلاری و کسر مبغ از کیف پول",

                        'time_price_of_dollars' => $dollar->DollarRateWithAddedValue()
                    ]);
                    $invoice->update(['status' => 'finished']);


                    $payment_amount = $inputs['custom_payment'];
                    if ($this->validationFiledUser()) {
                        $request->session()->put('voucher_id', $voucher->id);
                        $request->session()->put('amount_voucher', $payment_amount);
                    }
                    $message = "سلام کارت هدیه  شما ایجاد شد اطلاعات بیشتر در قسمت سفارشات قابل دسترس می باشد.";
                    $satiaService->send($message, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));
                    return redirect()->route('panel.delivery')->with(['voucher' => $voucher, 'payment_amount' => $payment_amount]);


                } else {
                    $voucher->delete();

                    $invoice->update(['status' => 'failed', 'description' => 'خرید کارت هدیه یوتوپیا ناموفق بود و عملیات کسر موجودی از کیف پول متوقف شد']);

                    Log::emergency("perfectmoney error : " . $this->PMeVoucher['ERROR']);
                    return redirect()->route('panel.purchase.view')->withErrors(['error' => "عملیات خرید ووچر ناموفق بود در صورت کسر موجودی از کیف پول شما با پشتیبانی تماس حاصل فرمایید."]);
                }


            } else {
                return redirect()->route('panel.purchase.view')->withErrors(['SelectInvalid' => "انتخاب شما معتبر نمیباشد"]);
            }

        } catch (\Exception $exception) {
            SendAppAlertsJob::dispatch('در خرید یوتوپیا از طریق کیف پول خطایی به وجود آمد')->onQueue('perfectmoney');

            return redirect()->route('panel.purchase.view')->withErrors(['error' => "عملیات خرید ووچر ناموفق بود در صورت کسر موجودی از کیف پول شما با پشتیبانی تماس حاصل فرمایید."]);
        }

    }

    public function delivery()
    {

        if (session()->has('voucher') && session()->get('payment_amount')) {
            $voucher = session()->get('voucher');
            $payment_amount = session()->get('payment_amount');
            return view('Panel.Delivery.index', compact('voucher', 'payment_amount'));
        } else {
            return redirect()->route('panel.index');
        }
    }


    public function PurchaseThroughTheBank(PurchaseThroughTheBankRequest $request)
    {
        try {
            $dollar = Doller::orderBy('id', 'desc')->first();
            $inputs = $request->all();
            $user = Auth::user();
            $bank = Bank::find($inputs['bank']);
            $inputs['user_id'] = $user->id;
            $inputs['description'] = " خرید مستقیم ووچر از طریق $bank->name";
            $balance = Auth::user()->getCreaditBalance();

            if (isset($inputs['service_id'])) {
                $service = Service::find($inputs['service_id']);
                $voucherPrice=( floor(($dollar->DollarRateWithAddedValue() * $service->amount) /10000 )*10000);

            } elseif (isset($inputs['custom_payment'])) {
                $inputs['service_id_custom'] = $inputs['custom_payment'];
                $voucherPrice=( floor(($dollar->DollarRateWithAddedValue() * $inputs['custom_payment']) /10000 )*10000);

            } else {
                return redirect()->route('panel.purchase.view')->withErrors(['SelectInvalid' => "انتخاب شما معتبر نمیباشد"]);
            }
            $inputs['final_amount'] = $voucherPrice;
            $inputs['type'] = 'service';
            $inputs['status'] = 'requested';
            $inputs['bank_id'] = $bank->id;
            $inputs['time_price_of_dollars'] = $dollar->DollarRateWithAddedValue();
            $inputs['description'] = ' خرید کارت هدیه یوتوپیا از طریق ' . $bank->name;


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
            $objBank->setUrlBack(route('panel.Purchase-through-the-bank'));
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
                $invoice->update(['status' => 'failed', 'description' => "به دلیل عدم ارتباط با بانک $bank->name سفارش شما لغو شد "]);
                $financeTransaction->update(['description' => "به دلیل عدم ارتباط با بانک $bank->name سفارش شما لغو شد ", 'status' => 'fail']);
                return redirect()->route('panel.purchase.view')->withErrors(['error' => 'ارتباط با بانک فراهم نشد لطفا چند دقیقه بعد تلاش فرماید.']);
            }
            $token = $status;
            session()->put('payment', $payment->id);
            session()->put('financeTransaction', $financeTransaction->id);
            Log::channel('bankLog')->emergency(PHP_EOL . 'Connection with the bank payment gateway '
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
            SendAppAlertsJob::dispatch('در ارتباط بابانک برای خرید یوتوپیا خطایی به وجود آمد لطفا پیگیری شود')->onQueue('perfectmoney');
            Log::emergency(PHP_EOL . $e->getMessage() . PHP_EOL);
            return redirect()->route('panel.purchase.view')->withErrors(['error' => 'ارتباط با بانک فراهم نشد لطفا چند دقیقه بعد تلاش فرماید.']);
        }
    }

//
    public function backPurchaseThroughTheBank(Request $request)
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
            Log::channel('bankLog')->emergency(PHP_EOL . "Return from the bank and the bank's response to the purchase of the service " . PHP_EOL . json_encode($request->all()) . PHP_EOL .
                'Bank message: ' . PHP_EOL . $objBank->transactionStatus() . PHP_EOL .
                'user ID :' . $user->id
                . PHP_EOL
            );
            $inputs = array_merge(request()->all(),request()->request->all());

            $invoice = $payment->invoice;
            if (!$objBank->backBank()) {
                $payment->update(
                    [
                        'RefNum' => $inputs['RefNum']??null,
                        'ResNum' => $payment->order_id,
                        'state' => 'failed'

                    ]);
                $invoice->update(['status' => 'failed', 'description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->transactionStatus()]);
                $financeTransaction->update(['description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->transactionStatus(), 'status' => 'fail']);

                $bankErrorMessage = "درگاه بانک {$bank->name} تراکنش شمارا به دلیل " . $objBank->transactionStatus() . " ناموفق اعلام کرد باتشکر سایناارز" . PHP_EOL . "پشتیبانی بانک {$bank->name}" . PHP_EOL . '021-6422';
                $satiaService->send($bankErrorMessage, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));

                return redirect()->route('panel.purchase.view')->withErrors(['error' => ' پرداخت موفقیت آمیز نبود ' . $objBank->transactionStatus()]);
            }

            $back_price = $objBank->verify($payment->amount);

            if ($back_price !== true or Payment::where("order_id", $payment->order_id)->count() > 1) {
                $invoice->update(['status' => 'failed', 'description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->verifyTransaction($back_price)]);
                $financeTransaction->update(['description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->verifyTransaction($back_price), 'status' => 'fail']);

                $bankErrorMessage = " درگاه بانک {$bank->name} تراکنش شمارا به دلیل " . $objBank->verifyTransaction($back_price) . " ناموفق اعلام کرد باتشکر سایناارز" . PHP_EOL . "پشتیبانی بانک {$bank->name}" . PHP_EOL . '021-6422';

                $satiaService->send($bankErrorMessage, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));
                Log::channel('bankLog')->emergency(PHP_EOL . "Bank Credit VerifyTransaction Purchase Voucher : " . json_encode($request->all()) . PHP_EOL .
                    'Bank message: ' . $objBank->verifyTransaction($back_price) .
                    PHP_EOL .
                    'user Id: ' . $user->id
                    . PHP_EOL
                );
                return redirect()->route('panel.error', $payment->id);
            }

            $payment->update(
                [
                    'RefNum' => $inputs['RefNum']??null,
                    'ResNum' => $payment->order_id,
                    'state' => 'finished'
                ]);

            $financeTransaction->update([
                'user_id' => $user->id,
                'amount' => $payment->amount,
                'type' => "deposit",
                "creadit_balance" => $balance + $payment->amount,
                'description' => ' افزایش کیف پول',
                'payment_id' => $payment->id,
                'time_price_of_dollars' => $dollar->DollarRateWithAddedValue()
            ]);

            $invoice->update(['status' => 'finished']);

            return redirect()->route('panel.deliveryVoucherBankView', [$invoice, $payment]);
        } catch (\Exception $e) {
            Log::emergency("panel Controller :" . $e->getMessage());
            SendAppAlertsJob::dispatch('در خرید یوتوپیا از درگاه بانکی خطایی به وجود آمد لطفا درگاه و یوتوپیا را چک کنید(توجه این خطا در برگشت از بانک رخ داده است)')->onQueue('perfectmoney');

            return redirect()->route('panel.purchase.view')->withErrors(['error' => "خطایی رخ داد از صبر و شکیبایی شما مچکریم لطفا جهت پیگیری در خواست تیکت ثبت کنید"]);

        }

    }

    public function deliveryVoucherBankView(Request $request, Invoice $invoice, Payment $payment)
    {
        $validationPurchasePermit = $this->purchasePermit($invoice, $payment);
        if ($validationPurchasePermit->purchasePermitStatus) {
            return $validationPurchasePermit->redirectFunction();
        }
        return view('Panel.Delivery.bankDelivery', compact('invoice', 'payment'));
    }

    public function deliveryVoucherBank(Request $request, Invoice $invoice, Payment $payment, SatiaService $satiaService)
    {


        try {
            $validationPurchasePermit = $this->purchasePermit($invoice, $payment);
            if ($validationPurchasePermit->purchasePermitStatus) {
                return $validationPurchasePermit->redirectFunction();
            }
            $balance = Auth::user()->getCreaditBalance();
            $dollar = Doller::orderBy('id', 'desc')->first();
            $user = Auth::user();
            $service = '';
            $amount = '';
            if (isset($invoice->service_id)) {
                $service = $invoice->service;
                $amount = $service->amount;
            } else {
                $amount = $invoice->service_id_custom;
            }

            $this->generateVoucherUtopia($amount);

            $voucher = Voucher::create(
                [
                    'user_id' => $user->id,
                    'invoice_id' => $invoice->id,
                    'status' => 'requested',
                    'description' => 'ارسال در خواست به سرویس یوتوپیا',
                ]
            );
            if (isset($invoice->service_id)) {

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
                    'amount' => $payment->amount,
                    'type' => "withdrawal",
                    "creadit_balance" => $balance - $payment->amount,
                    'description' => "خرید کارت هدیه {$amount} دلاری و کسر مبغ از کیف پول",
                    'payment_id' => $payment->id,
                    'time_price_of_dollars' => $dollar->DollarRateWithAddedValue()
                ]);
                if (isset($invoice->service_id)) {
                    $service = $invoice->service;
                    $payment_amount = $service->amount;
                } else {
                    $payment_amount = $invoice->service_id_custom;
                }

                $message = "سلام کارت هدیه  شما ایجاد شد اطلاعات بیشتر در قسمت سوابق قابل دسترس می باشد.";
                $satiaService->send($message, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));
                $voucher->finance=$voucher->financeTransaction->id;
                $voucher->jalaliDate=Jalalian::forge($voucher->created_at)->format('H:i:s Y/m/d');
                return Response::json(['voucher' => $voucher, 'payment_amount' => $payment_amount, 'status' => true]);


            } else {
                $voucher->delete();
                $message = "پرداخت با موفقیت انجام شد به دلیل عدم ارتباط با یوتوپیا مبلغ کیف پول شما افزایش داده شد و شما میتوانید در یک ساعت آینده از کیف پول خود ووچر خودرا تهیه کنید";
                $satiaService->send($message, $user->mobile, env('SMS_Number'), env('SMS_Username'), env('SMS_Password'));
                return Response::json(['status' => false]);
            }
        } catch (\Exception $e) {
            Log::emergency(PHP_EOL . $e->getMessage() . PHP_EOL);
            SendAppAlertsJob::dispatch('در خرید یوتوپیا ازطریق جاوااسکریپت خطایی به وجود آمد لطفا سرویس یوتوپیا چک شود')->onQueue('perfectmoney');

            return Response::json(['status' => false]);
        }
    }

    public function walletCharging(Request $request)
    {
        $bank = Bank::where('is_active', 1)->first();
        $user = Auth::user();
        return view("Panel.RechargeWallet.index", compact('user', 'bank'));
    }

    public function walletChargingPreview(WalletChargingRequest $request)
    {

        $user = Auth::user();
        $inputs = $request->all();
        $payment = Payment::create(
            [
                'state' => 'requested',
            ]);

        $payment->update(['order_id' => $payment->id + Payment::transactionNumber]);
        $inputs['orderID'] = $payment->id + Payment::transactionNumber;
        session()->put('payment', $payment->id);
        return view("Panel.RechargeWallet.FinalApproval", compact('inputs', 'user', 'payment'));
    }

    public function walletChargingStore(WalletChargingRequest $request)
    {
        if (session()->has('payment')) {
            $inputs = $request->all();
            $payment = Payment::find(session()->get('payment'));
            $inputs['price'] .= 0;
            $inputs['price'] = floor($inputs['price']);
            $bank = Bank::find($inputs['bank_id']);
            $user = Auth::user();
            $balance = Auth::user()->getCreaditBalance();

            $inputs['final_amount'] = $inputs['price'];
            $inputs['type'] = 'wallet';
            $inputs['status'] = 'requested';
            $inputs['bank_id'] = $bank->id;
            $inputs['user_id'] = $user->id;
            $inputs['description'] = ' افزایش مبلغ کیف پول ' . $bank->name;
            $invoice = Invoice::create($inputs);


            $objBank = new $bank->class;
            $objBank->setTotalPrice($inputs['price']);
            $objBank->setBankUrl($bank->url);

            $objBank->setOrderID($payment->id + Payment::transactionNumber);
            $objBank->setTerminalId($bank->terminal_id);
            $objBank->setUrlBack(route('panel.wallet.charging.back'));
            $objBank->setBankModel($bank);


            session()->put('payment', $payment->id);
            session()->put('invoice', $invoice->id);
            $payment->update(
                [
                    'bank_id' => $bank->id,
                    'amount' => $inputs['price'],
                    'invoice_id' => $invoice->id

                ]);
            $financeTransaction = FinanceTransaction::create([
                'user_id' => $user->id,
                'amount' => $payment->amount,
                'type' => "bank",
                "creadit_balance" => $balance,
                'description' => " ارتباط با بانک $bank->name",
                'payment_id' => $payment->id,
            ]);
            session()->put('financeTransaction', $financeTransaction->id);

            $status = $objBank->payment();
            if (!$status) {
                $invoice->update(['status' => 'failed', 'description' => "به دلیل عدم ارتباط با بانک $bank->name شارژ کیف پول انجام نشد "]);
                $financeTransaction->update(['description' => "به دلیل عدم ارتباط با بانک $bank->name سفارش شما لغو شد ", 'status' => 'fail']);

                return redirect()->route('panel.index')->withErrors(['error' => 'ارتباط با بانک فراهم نشد لطفا چند دقیقه بعد تلاش فرماید.']);
            }
            $token = $status;
            Log::channel('bankLog')->emergency(PHP_EOL . 'Connection with the bank payment gateway to charge the wallet '
                . PHP_EOL .
                'Name of the bank: ' . $bank->name
                . PHP_EOL .
                'payment price: ' . $inputs['price']
                . PHP_EOL .
                'payment date: ' . Carbon::now()->toDateTimeString()
                . PHP_EOL .
                'user ID: ' . $user->id
                . PHP_EOL

            );


            return $objBank->connectionToBank($token);

        } else {
            return redirect()->route('panel.index')->withErrors(['error' => 'خطایی رخ داد لفا مجدد بعدا تلاش فرمایید.']);
        }
    }

    public function walletChargingBack(Request $request)
    {
        try {

            $user = Auth::user();
            $lastBalance = $user->financeTransactions()->orderBy('id', 'desc')->first();
            $payment = Payment::find(session()->get('payment'));
            $financeTransaction = FinanceTransaction::find(session()->get('financeTransaction'));
            $bank = $payment->bank;
            $objBank = new $bank->class;
            $objBank->setBankModel($bank);

            Log::channel('bankLog')->emergency(PHP_EOL . "Back from the bank and the bank's response to charging the wallet " . PHP_EOL . json_encode($request->all()) . PHP_EOL .
                'Bank message: ' . PHP_EOL . $objBank->transactionStatus() . PHP_EOL .
                'user ID :' . $user->id
                . PHP_EOL
            );
            $inputs = array_merge(request()->all(),request()->request->all());

            $invoice = Invoice::find(session()->get('invoice'));
            if (!$objBank->backBank()) {
                $payment->update(
                    [
                        'RefNum' => $inputs['RefNum']??null,
                        'ResNum' => $payment->order_id,
                        'state' => 'failed'

                    ]);
                $invoice->update(['status' => 'failed', 'description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->transactionStatus()]);
                $financeTransaction->update(['description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->transactionStatus(), 'status' => 'fail']);

                return redirect()->route('panel.index')->withErrors(['error' => ' پرداخت موفقیت آمیز نبود ' . $objBank->transactionStatus()]);
            }

            $back_price = $objBank->verify($payment->amount);

            if ($back_price !== true or Payment::where("order_id", $payment->order_id)->count() > 1) {
                $invoice->update(['status' => 'failed', 'description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->verifyTransaction($back_price)]);
                $financeTransaction->update(['description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->verifyTransaction($back_price), 'status' => 'fail']);

                Log::channel('bankLog')->emergency(PHP_EOL . "Bank Credit VerifyTransaction wallet recharge  : " . json_encode($request->all()) . PHP_EOL .
                    'Bank message: ' . $objBank->verifyTransaction($back_price)
                    . PHP_EOL .
                    'user ID :' . $user->id
                    . PHP_EOL
                );
                return redirect()->route('panel.error', $payment->id);
            }
            $payment->update(
                [
                    'RefNum' => $inputs['RefNum']??null,
                    'ResNum' => $payment->order_id,
                    'state' => 'finished'

                ]);
            $invoice->update(['status' => 'finished']);
            if ($lastBalance) {
                $amount = $payment->amount + $lastBalance->creadit_balance;
            } else {
                $amount = $payment->amount;
            }


            $financeTransaction->update([
                'user_id' => $user->id,
                'amount' => $payment->amount,
                'type' => "deposit",
                "creadit_balance" => $amount,
                'description' => 'افزایش   مبلغ کیف پول',
                'payment_id' => $payment->id

            ]);
            return redirect()->route('panel.index')->with(['success' => 'پرداخت باموفقیت انجام شد و مبلغ کیف پول شما فزایش داده شد']);
        } catch (\Exception $e) {
            Log::emergency("panel Controller :" . $e->getMessage());
            SendAppAlertsJob::dispatch('شارژکیف پول به مشکل خورده است لطفا درگاه بانکی  وسایر موارد چک شود')->onQueue('perfectmoney');
            return redirect()->route('panel.index')->withErrors(['error' => "خطایی رخ داد از صبر و شکیبایی شما مچکریم لطفا جهت پیگیری در خواست تیکت ثبت کنید"]);

        }
    }

    public function error(Request $request, Payment $payment)
    {
        $user = Auth::user();
        if ($payment->invoice->user->id == $user->id)
            return view('bank.bankErrorPage', compact('payment'));
        else
            return redirect()->route('panel.index');
    }

    public function rules()
    {
        return view('Panel.Rules.index');
    }


}
