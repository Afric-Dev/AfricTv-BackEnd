<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class Socket extends Model
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
        'socket_id',
    ];

    public function user()
   {
       return $this->belongsTo(User::class, 'user_id');
   }

}
