<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Subscribtion;
use App\Models\Notification;
use App\Mail\SubscribtionMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;  
 
class SubscribtionController extends Controller
{
        public function subscribe(Request $request): JsonResponse
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

                $user = Auth::user();
                //Notification
                $type = "SUBSCRIBTION";
                $title = "SUBSCRIBTION NOTIFICATION";
                $message ="Great news! " .  $user->name . " just subscribe to your account. Keep creating awesome blogs!!";


                $notification = Notification::create([ 
                    'user_id' => Auth::user()->id,
                    'receiver_id' => $subscribtion->subscriber_id,
                    'subscriber_unique_id' => $user->unique_id,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'is_read' => false,
                ]);

            // Send an email to the user
            Mail::to($subscribtion->subscriber_email)->send(new SubscribtionMail($subscribtion, $user));

            // Return a JSON response
            return response()->json([
                "status" => true,
                "message" => "Subscribed Successfully",
            ]);
        }

        public function unsubscribe(Request $request): JsonResponse
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
 
         public function viewsubscribers($uniqid): JsonResponse
        {
            // Get the current authenticated user
            $authenticatedUser = Auth::user();

            // Find the user based on the unique_id
            $user = User::where('unique_id', $uniqid)->firstOrFail();

            // Retrieve subscribers of the target user
            $targetUserSubscribers = Subscribtion::with('user')
                ->where('subscriber_id', $user->id)
                ->get();

            // Retrieve subscriber IDs of the authenticated user
            $authenticatedUserSubscribers = Subscribtion::where('subscriber_id', $authenticatedUser->id)
                ->pluck('user_id');

            // If authenticatedUserSubscribers is empty, commonSubscribers will also be empty
            $commonSubscribers = $authenticatedUserSubscribers->isNotEmpty()
                ? Subscribtion::with('user')
                    ->where('subscriber_id', $user->id)
                    ->whereIn('user_id', $authenticatedUserSubscribers)
                    ->get()
                : collect();


            // Check if the target user has no subscribers at all
            if ($targetUserSubscribers->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Oops! No subscribers found!',
                ]);
            }

            // Return response with both groups of subscribers
            return response()->json([
                'status' => true,
                'message' => 'Subscriber data retrieved',
                'all_subscribers' => $targetUserSubscribers,
                //'authenticated_subscribers' => $authenticatedUserSubscribers,
                'common_auth_subscribers' => $commonSubscribers,
            ]);
        }


        public function subscribers(): JsonResponse
        {
            // Find the user based on the id format
            $user = Auth()->user()->id;

            // Retrieve subscriptions for the user
            $subscriptions = Subscribtion::with('user')
                                            ->where('user_id', $user)
                                            ->get();

            $subscripeto = Subscribtion::with('user')
                                            ->where('subscriber_id', $user)
                                            ->get();

            // Check if the user has no subscriptions
            if ($subscriptions->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Oops! No Subscribers found',
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Subscription data',
                'subscriptions' => $subscriptions,
                'subscripeto' => $subscriptions,
            ]);
        }




           


}
 