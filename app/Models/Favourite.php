<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    use HasFactory;


    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function educationals()
    {
        return $this->hasMany(Educational::class);
    }
}
