<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    use HasFactory;

    protected $fillable = [
        "post_id",
        "post_email",
        "user_id",
        "user_email",
        "user_name",
        "unique_id",
        "comments",
        "comments_vid_path",
        "comments_img_path",
        "comments_link",
        "date",
    ];
}
