<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;


class Bookmark extends Model
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
        "post_id",
        "user_id"
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function educationals()
    {
        return $this->hasMany(Educational::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
