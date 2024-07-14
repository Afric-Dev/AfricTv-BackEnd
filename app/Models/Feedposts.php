<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedposts extends Model
{
    use HasFactory;
     protected $fillable = [
        "avatar_path",
        'user_id',
        'user_name',
        'unique_id',
        'user_email',
        'post_img_path',
        'post_vid_path',
        'post_pdf_path',
        'post_song_path',
        'category',
        'link',
        'user',
        'hashtags',
        'post_body',
        'post_views',
        'post_id',
        'date',
    ];
}
