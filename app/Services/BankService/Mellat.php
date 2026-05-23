<?php

namespace App\Services\BankService;

use App\Interface\BankInterface;
use App\Jobs\SendAppAlertsJob;
use App\Models\Bank;
use Illuminate\Support\Facades\Log;

class Mellat extends Service
{


    protected $status = false;

    /**
     * Create a new class instance.
     */

    public function __construct()
    {
    }

    public function payment()
    {
        $response = $this->GetToken();
        $response = (array)$response;
        $response = explode(',', $response['return']);


        if (!empty($response) and count($response) >= 2) {
            return $response[1];
        } else {
            $response = $response['return'] ?? null;
            Log::emergency('An error occurred while connecting to National Bank :' . PHP_EOL .
                $this->verifyTransaction($response[0]));

            SendAppAlertsJob::dispatch('هنگام اتصال به بانک ملی خطایی رخ  داد و اتصال برقرار نشد')->onQueue('perfectmoney');
            return false;
        }

    }

    public function GetToken()
    {
        $this->generateData();
        $request = $this->cullRequest($this->objectBank->url);
        $response = $request->bpPayRequest($this->data);
        return $response;
    }

    function cullRequest($url)
    {


        try {
            $clinet = new \SoapClient($url);
            return $clinet;
        } catch (\Exception $e) {

            Log::emergency('An error occurred while connecting to National Bank ' . PHP_EOL .
                $e->getMessage());
            SendAppAlertsJob::dispatch('هنگام اتصال به بانک ملی خطایی رخ  داد و نوع خطا در لاگ ذخیره شد لطفا پیگیری کنید')->onQueue('perfectmoney');

            return false;
        }

    }

    public function backBank()
    {
        $Token = request()->input("CardHolderInfo");
        if (isset($Token)) {
            return true;
        } else
            return false;

    }

    protected function generateData()
    {
        $date = date("Ymd");
        $time = date("His");
        $this->data = array(
            'userName' => $this->objectBank->username,
            'terminalId' => $this->objectBank->terminal_id,
            'userPassword' => $this->objectBank->password,
            'orderId' => $this->getOrderID(),
            'amount' => $this->getTotalPrice(),
            'localDate' => $date,
            'localTime' => $time,
            'additionalData' => "order_" . $this->getOrderID(),
            'payerId' => 0,
            'callBackUrl' => $this->getUrlBack());


    }

    public function transactionStatus()
    {
        return $this->verifyTransaction(request()->input('ResCode'));
    }

    public function verifyTransaction($ErrorCode)
    {

        $bpResponseCodes = [
            0 => 'تراکنش با موفقیت انجام شد',
            11 => 'شماره کارت نامعتبر است',
            12 => 'موجودی کافی نیست',
            13 => 'رمز نادرست است',
            14 => 'تعداد دفعات وارد کردن رمز بیش از حد مجاز است',
            15 => 'کارت نامعتبر است',
            16 => 'دفعات برداشت وجه بیش از حد مجاز است',
            17 => 'کاربر از انجام تراکنش منصرف شده است',
            18 => 'تاریخ انقضای کارت گذشته است',
            19 => 'مبلغ برداشت وجه بیش از حد مجاز است',
            21 => 'پذیرنده نامعتبر است',
            23 => 'خطای امنیتی رخ داده است',
            24 => 'اطلاعات کاربری پذیرنده نامعتبر است',
            25 => 'مبلغ نامعتبر است',
            31 => 'پاسخی نامعتبر است',
            32 => 'فرمت اطلاعات وارد شده صحیح نمی باشد',
            33 => 'حساب نامعتبر است',
            34 => 'خطای سیستمی',
            35 => 'تاریخ نامعتبر است',
            41 => 'شماره درخواست تکراری است',
            42 => 'تراکنش Sale یافت نشد',
            43 => 'قبلاً درخواست Verify داده شده است',
            44 => 'درخواست Verify یافت نشد',
            45 => 'تراکنش Settle شده است',
            46 => 'تراکنش Settle نشده است',
            47 => 'تراکنش Settle یافت نشد',
            48 => 'تراکنش Reverse شده است',
            111 => 'صادر کننده کارت نامعتبر است',
            112 => 'خطای سوییچ صادر کننده کارت',
            113 => 'پاسخی از صادر کننده کارت دریافت نشد',
            114 => 'دارنده کارت مجاز به انجام این تراکنش نیست',
            415 => 'شناسه قبض نادرست است',
            416 => 'زمان جلسه کاری به پایان رسیده است',
            417 => 'خطا در ثبت اطلاعات',
            418 => 'شناسه پرداخت کننده نامعتبر است',
            419 => 'اشکال در تعریف اطلاعات مشتری',
            421 => 'IP سرور پذیرنده به سامانه اعلام نشده است',
        ];
        $property = 34;


        if (!in_array($ErrorCode, array_keys($bpResponseCodes))) {
            return $bpResponseCodes[34];
        }
        return $bpResponseCodes[$property];

    }


    public function verify($amount = 0)
    {

        $this->data = array(
            'terminalId' => $this->objectBank->terminal_id,
            'userName' => $this->objectBank->username,
            'userPassword' => $this->objectBank->password,
            'orderId' => request()->input('SaleOrderId'),
            'saleOrderId' => request()->input('SaleOrderId'),
            'saleReferenceId' => request()->input('SaleReferenceId'),
        );
        $request = $this->cullRequest($this->objectBank->url);
        $web_result = $request->bpVerifyRequest($this->data);
        $verify_result = (int)$web_result->return;
        if ($verify_result == 0) {
            return true;
        }
        return $verify_result;

    }

    public function connectionToBank($token)
    {
        return view('mellat', compact('token'));
    }

    public function setBankModel(Bank $bank)
    {
        $this->objectBank = $bank;
    }
}
