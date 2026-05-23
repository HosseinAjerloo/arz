<?php

namespace App\Services\BankService;

use App\Interface\BankInterface;
use App\Jobs\SendAppAlertsJob;
use App\Models\Bank;
use Illuminate\Support\Facades\Log;

class Mellat extends Service
{
    private static $classmap = array('bpVerifyRequest' => 'bpVerifyRequest'
    , 'bpVerifyRequestResponse' => 'bpVerifyRequestResponse'
    , 'bpRefundInquiryRequest' => 'bpRefundInquiryRequest'
    , 'bpRefundInquiryRequestResponse' => 'bpRefundInquiryRequestResponse'
    , 'bpRefundVerifyRequest' => 'bpRefundVerifyRequest'
    , 'bpRefundVerifyRequestResponse' => 'bpRefundVerifyRequestResponse'
    , 'bpSettleRequest' => 'bpSettleRequest'
    , 'bpSettleRequestResponse' => 'bpSettleRequestResponse'
    , 'bpDynamicPayRequest' => 'bpDynamicPayRequest'
    , 'bpDynamicPayRequestResponse' => 'bpDynamicPayRequestResponse'
    , 'bpVirtualPayRequest' => 'bpVirtualPayRequest'
    , 'bpVirtualPayRequestResponse' => 'bpVirtualPayRequestResponse'
    , 'bpReversalRequest' => 'bpReversalRequest'
    , 'bpReversalRequestResponse' => 'bpReversalRequestResponse'
    , 'bpCumulativeDynamicPayRequest' => 'bpCumulativeDynamicPayRequest'
    , 'bpCumulativeDynamicPayRequestResponse' => 'bpCumulativeDynamicPayRequestResponse'
    , 'bpPayRequest' => 'bpPayRequest'
    , 'bpPayRequestResponse' => 'bpPayRequestResponse'
    , 'bpSaleReferenceIdRequest' => 'bpSaleReferenceIdRequest'
    , 'bpSaleReferenceIdRequestResponse' => 'bpSaleReferenceIdRequestResponse'
    , 'bpChargePayRequest' => 'bpChargePayRequest'
    , 'bpChargePayRequestResponse' => 'bpChargePayRequestResponse'
    , 'bpInquiryRequest' => 'bpInquiryRequest'
    , 'bpInquiryRequestResponse' => 'bpInquiryRequestResponse'
    , 'bpRefundRequest' => 'bpRefundRequest'
    , 'bpRefundRequestResponse' => 'bpRefundRequestResponse'
    );


    protected $status = false;

    /**
     * Create a new class instance.
     */

    public function __construct()
    {
    }

    public function payment()
    {
        $request = $this->cullRequest($this->objectBank->url);
        $response = $request->bpPayRequest($this->data);
        if (is_array($response) and !empty($response)) {

        } else {
            Log::emergency('An error occurred while connecting to National Bank :' . PHP_EOL .
                $this->verifyTransaction($response));

            SendAppAlertsJob::dispatch('هنگام اتصال به بانک ملی خطایی رخ  داد و اتصال برقرار نشد')->onQueue('perfectmoney');
            dd($this->verifyTransaction($response));
            return false;
        }

    }

    public function GetToken()
    {
    }

    function cullRequest($url)
    {

        $this->generateData();
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
        $Token = request()->input("token");
        $ResCode = request()->input("ResCode");
        if (isset($Token) and isset($ResCode) and $ResCode == '0') {
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
        return $this->verifyTransaction($this->verify());
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

        if (property_exists($ErrorCode, 'return'))
            $property = (int)$ErrorCode->return;


        if (!in_array($property,array_keys($bpResponseCodes))){
            return $bpResponseCodes[34];
        }
        return $bpResponseCodes[$property];

    }

    protected function encrypt_pkcs7_meli($str, $key)
    {
        $key = base64_decode($key);
        $ciphertext = openssl_encrypt($str, "DES-EDE3", $key, OPENSSL_RAW_DATA);
        return base64_encode($ciphertext);
    }


    public function verify($amount = 0)
    {
        if (!$this->status) {
            $key = $this->objectBank->password;
            $ResCode = request()->input('ResCode');
            $Token = request()->input('token');

            if (isset($ResCode) and $ResCode == 0) {

                $this->data = array('Token' => $Token, 'SignData' => $this->encrypt_pkcs7_meli($Token, $key));
                $arrres = $this->cullRequest('https://sadad.shaparak.ir/vpg/api/v0/Advice/Verify');
                if ($arrres->ResCode != -1 && $arrres->ResCode == 0) {
                    $Refid = $arrres->SystemTraceNo;
                    if ($Refid == '') {
                        return $arrres->ResCode;
                    }
                    $RefNo = $arrres->RetrivalRefNo;
                    request()->request->add(['RefNum' => $Token]);
                    $this->status = true;
                    return true;
                } else {
                    return 1050;
                }
            }
            return 1050;
        }
        return $this->status;
    }

    public function connectionToBank($token)
    {
        return redirect()->away($this->getBankUrl() . $token);
    }

    public function setBankModel(Bank $bank)
    {
        $this->objectBank = $bank;
    }
}
