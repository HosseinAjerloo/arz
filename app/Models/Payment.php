<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    Const transactionNumber=30000000;
    use HasFactory,SoftDeletes;
    protected $fillable=['bank_id','invoice_id','state','amount','RefNum','ResNum','order_id'];

    public function bank()
    {
        return $this->belongsTo(Bank::class,'bank_id');
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class,'invoice_id');
    }
}
