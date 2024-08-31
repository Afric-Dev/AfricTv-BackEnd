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
 
class SubscribtionController extends Controller
{
        public function subscribe(Request $request)
        {
            // Validate the request
            $request->validate([
                "subscriber_id" => "required|exists:users,id", 
                "subscriber_email" => "required|email",
            ]);

            // Check if the user has already subscribed
            $existingSubscription = Subscribtion::where('user_id', Auth::user()->id)
                                                ->where('subscriber_id', $request->subscriber_id)
                                                ->first();

            if ($existingSubscription) {
                return response()->json([
                    "status" => false,
                    "message" => "You have subscribed before",
                ]);
            }

            // Create a new subscription record
            $subscribtion = Subscribtion::create([
                "user_id" => Auth::user()->id,
                "user_email" => Auth::user()->email,
                "subscriber_id" => $request->subscriber_id,
                "subscriber_email" => $request->subscriber_email,
            ]);

            // Find the user by subscriber_id
            $user = User::find($request->subscriber_id);

            if ($user) {
                $user->subscribers_number += 1;
                $user->save();
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Unknown Error",
                ]);
            }

            // Send an email to the user
            Mail::to($subscribtion->subscriber_email)->send(new SubscribtionMail($subscribtion));

            // Return a JSON response
            return response()->json([
                "status" => true,
                "message" => "Subscribed Successfully",
            ]);
        }

        public function unsubscribe(Request $request)
        {
            // Validate the request
            $request->validate([
                "subscriber_id" => "required|exists:users,id", 
                "subscriber_email" => "required|email",
            ]);

            // Check if the user has already subscribed
            $existingSubscription = Subscribtion::where('user_id', Auth::user()->id)
                                                ->where('subscriber_id', $request->subscriber_id)
                                                ->first();

            if (!$existingSubscription) {
                return response()->json([
                    "status" => false,
                    "message" => "You did not subscribe to this user before",
                ]);
            }

            // Create a new subscription record
            $deleteSubscription = Subscribtion::where('user_id', Auth::user()->id)
                                                ->where('subscriber_id', $request->subscriber_id)
                                                ->delete();

            // Find the user by subscriber_id
            $user = User::find($request->subscriber_id);

            if ($user) {
                $user->subscribers_number -= 1;
                $user->save();
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Unknown Error",
                ]);
            }

            // Return a JSON response
            return response()->json([
                "status" => true,
                "message" => "Unsubscribed Successfully",
            ]);

        }

        public function viewsubscribers(Request $request)
        {
            // Validate that user_id is provided in the request
            $request->validate([
                "subscriber_id" => "required|integer",
            ]);

            // Fetch subscriptions where user_id matches the provided subscriber_id
            $subscriptions = Subscribtion::with('user')
                                         ->where('subscriber_id', $request->subscriber_id)
                                         ->get();

            return response()->json([
                'status' => true,
                'message' => 'Subscription data',
                'subscriptions' => $subscriptions,
            ]);
        }


        


}
 