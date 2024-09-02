<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Educational extends Model
{
    use HasFactory;

    protected $table = "education";

    protected $fillable = [
            "edu_id",
            "user_id",
            "unique_id",
            "title",
            "description",
            "links",
            "edu_vid_path",
            "eduvideoId",
            "edu_views", 
            "vote_count",
            "thoughts_count",
            "favourites_count"
           
    ];
    
    public function user()
   {
       return $this->belongsTo(User::class, 'user_id');
   }
}
