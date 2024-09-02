<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eduvote extends Model
{
    use HasFactory;

    protected $table = "eduvotes";

       protected $fillable = [
        'user_id',
        'edu_id',
        'reaction_type',
    ];

    public function user()
   {
       return $this->belongsTo(User::class, 'user_id');
   }
}
