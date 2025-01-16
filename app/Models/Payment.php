<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class Payment extends Model
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
    
    use HasFactory, Notifiable;

     protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'amount',
        'payment_status',
        'payment_method',
        'currency',
        'status'
    ];





}
  