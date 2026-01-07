<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    use HasFactory;
    protected $fillable=['ticket_id','user_id','admin_id','message','seen_at','type'];

    public function image()
    {
        return $this->morphOne(File::class,'fileable');
    }
    public function serializeDate(DateTimeInterface $dateTime)
    {
        return $dateTime->setTimezone('Asia/Tehran')->format('Y-m-d H:i:s');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
