<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class InnerComment extends Model
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
        "comment_id",
        "user_id",
        "comments",
        "comments_vid_path",
        "comments_img_path",
        "comments_link",
    ];
   public function user()
   {
       return $this->belongsTo(User::class);
   }

}
