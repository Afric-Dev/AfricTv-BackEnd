<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class Post extends Model
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
        'cover_image',
        'coverimageId',
        'post_img_path',
        'postimageId',
        'post_vid_path',
        'postvideoId',
        'post_pdf_path',
        'post_song_path',
        'category',
        'post_title',
        'PostbodyHtml',
        'postbodyJson',
        'postBodytext',
        'post_views',
        'likes_count',
        'comments_count',
        'link',
        'hashtags',
        'post_ending',
        'post_id',
        'date',
        "is_status"
    ];

    public function user()
   {
       return $this->belongsTo(User::class, 'user_id');
   }

    public function likes()
    {
        return $this->hasMany(Likes::class, 'post_id');
    }

    public function comments()
    {
        return $this->hasMany(Comments::class, 'post_id');
    }

}
