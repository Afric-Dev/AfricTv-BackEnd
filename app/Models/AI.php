<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AI extends Model
{
    use HasFactory;

    protected $table ="a_i_s";

    protected $fillable = [
        "user_id",
        "count"
    ];

}
