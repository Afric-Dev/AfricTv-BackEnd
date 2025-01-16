<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        "card_name",
        "user_id",
        "user_email",
        "card_number",
        "card_cvc_number",
        "expiration_date",
    ];
}
