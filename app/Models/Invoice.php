<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'service_id', 'disscount_code_id', 'final_amount', 'type', 'service_id_custom', 'status', 'time_price_of_dollars', 'bank_id','description','siteService_id'];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function payment()
    {
        return $this->hasOne(Payment::class,'invoice_id');
    }
    public function voucher()
    {
        return $this->hasOne(Voucher::class,'invoice_id');
    }

    public function transferm()
    {
        return $this->hasOne(Transmission::class,'invoice_id');
    }


    public function voucherAmount()
    {
        if ($this->service_id) {
            return $this->service->amount;
        } elseif ($this->service_id_custom) {
            return $this->service_id_custom;
        } else {
            return false;
        }
    }
    public function persianType()
    {
            return match ($this->type)
            {
                "service"=>"خرید کارت هدیه پرفکت مانی",
                "wallet"=>"افزایش کیف پول",
                "transmission"=>"انتقال حواله کارت هدیه پرفکت مانی",
                default =>''
            };
    }
}
