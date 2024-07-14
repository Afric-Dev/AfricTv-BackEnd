<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Payment extends Model
{
    use HasFactory, Notifiable;

     protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'amount',
        'payment_type',
        'payment_status',
        'payment_method',
        'currency',
    ];





}
  