<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Utopia extends Model
{
    protected $connection = 'mysql_second';
    protected $table='utopia';
    protected $fillable=['validate','mobild','user_id','utopia_voucher','amount','hash','payment_id'];

}
