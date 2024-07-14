<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Educational extends Model
{
    use HasFactory;

    protected $table = "education";

    protected $fillable = [
            "user_id",
            "unique_id",
            "title",
            "description",
            "links",
            "edu_vid_path",
            "edu_views",
    ];
}
