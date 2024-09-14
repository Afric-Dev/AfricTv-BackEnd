<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class AdsPayment extends Model
{

    protected $keyType = 'uuid';

    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            $customer->{$customer->getKeyName()} = (string) Str::uuid();
        });
    }
    use HasFactory;
     protected $fillable = [
        "user_id",
        "amount",
        "payment_status",
        "payment_method",
        "currency",
        "ads_type",
        "duration",
        "clicks",
        'taken'
    ];

}
