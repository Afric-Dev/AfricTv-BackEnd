<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CardDetails;
use Illuminate\Support\Facades\Auth;


class UserCardDetails extends Controller
{
    public function carddetails(Request $request) {
    $request->validate([
        "card_name" => "required",
        // "user_id" => "required",
        // "user_email" => "required|email",
        "card_number" => "required",
        "card_cvc_number" => "required",
        "expiration_date" => "required",
    ]);

    // Create 
    $user = CardDetails::create([
        "card_name" => $request->card_name,
        "user_id" => Auth::user()->id,
        "user_email" => Auth::user()->email,
        "card_number" => $request->card_number,
        "card_cvc_number" => $request->card_cvc_number,
        "expiration_date" => $request->expiration_date,
    ]);

    // Mail::to($user->email)->send(new RegistrationMail($user));

    return response()->json([
        "status" => true,
        "message" => "Details Stored Successfully"
        ]);
    }

}
