<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
     protected $fillable = [
        'user_id',
        'user_name',
        'unique_id',
        'user_email',
        'cover_image',
        'post_img_path',
        'post_vid_path',
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
