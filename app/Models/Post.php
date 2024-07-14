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
        'link',
        'hashtags',
        'post_ending',
        'post_id',
        'date',
    ];
}
