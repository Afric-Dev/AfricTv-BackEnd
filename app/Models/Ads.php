<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class Ads extends Model
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
        'img_path',
        'imageId',
        'vid_path',
        'videoId',
        'title',
        'description',
        'link',
        'start_date',
        'status',
        'clicks',
        'ads_id',
        'ads_type',
        'is_ads_type_sec',
    ];

     public function user()
    {
        return $this->belongsTo(User::class);
    }
}
