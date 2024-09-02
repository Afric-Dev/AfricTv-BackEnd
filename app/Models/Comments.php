<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    use HasFactory;

    protected $fillable = [
        "post_id",
        "user_id",
        "comments",
        "comments_vid_path",
        "comments_img_path",
        "comments_link",
    ];
   public function user()
   {
       return $this->belongsTo(User::class, 'user_id');
   }

}
