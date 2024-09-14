<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class Subscribtion extends Model
{
    use HasFactory;

    protected $keyType = 'uuid';

    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            $customer->{$customer->getKeyName()} = (string) Str::uuid();
        });
    }
    
     protected $fillable = [
        'user_id',
        'user_email',
        'subscriber_id',
        'subscriber_email',
    ];

    public function user()
   {
       return $this->belongsTo(User::class, 'user_id');
   }

}
