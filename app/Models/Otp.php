<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Otp extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable=['mobile','code','token','seen_at'];
    protected $casts=[];
    public function serializeDate(DateTimeInterface $dateTime)
    {
        return $dateTime->setTimezone('Asia/Tehran')->format('Y-m-d H:i:s');
    }

}
