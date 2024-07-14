<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Subscribtion;
use App\Mail\SubscribtionMail;
use Illuminate\Support\Facades\Auth;

 
class UnsubscribeController extends Controller
{
        public function unsubscribe(Request $request)
        {
            // Validate the request
            $request->validate([
                // "user_id" => "required",
                // "user_email" => "required",
                "subscriber_id" => "required",
                "subscriber_email" => "required",
            ]);

             $unsubscribe = "1";
            // Create a new subscription record
            $unsubscribe = Subscribtion::create([
                "user_id" => Auth::user()->id,,
                "user_email" => $Auth::user()->email,
                "subscriber_id" => $request->subscriber_id,
                "subscriber_email" => $request->subscriber_email,
                "unsubscribe" => $unsubscribe,
            ]);

            // Find the user by ID
            $user = User::find($request->user_id); 

            // If the user exists, increment the subscribers_number
            if ($user) {
                $user->subscribers_number -= 1;
                $user->save();
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Unknown Error"
                ]);
            }

            // Send an email to the user
            // Mail::to($subscribtion->user_email)->send(new SubscribtionMail($subscribtion));

            // Return a JSON response
            return response()->json([
                "status" => true,
                "message" => "Unsubscribed Successfully"
            ]);
        }

}
