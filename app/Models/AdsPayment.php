<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdsPayment extends Model
{

   protected static function boot()
   {
       parent::boot();

       static::creating(function (Model $model) {
           $model->ads_id = strval(rand(0, 100000));
       });
   }


    use HasFactory;
     protected $fillable = [
        "user_id",
        "user_name",
        "user_email",
        "amount",
        "payment_type",
        "payment_status",
        "payment_method",
        "currency",
        "ads_type",
        "duration",
        "clicks",
        'taken'
    ];

}
