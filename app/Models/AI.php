<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class AI extends Model
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


    protected $table ="a_i_s";

    protected $fillable = [
        "user_id",
        "message",
        "response",
        "prediction_id",
        "chat_id",
    ];

    public function user()
    {
       return $this->belongsTo(User::class);
    }

    public function responses()
    {
        return $this->hasMany(AI::class, 'chat_id')->with('responses');
    }

}
