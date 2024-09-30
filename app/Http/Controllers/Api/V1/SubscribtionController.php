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

                $user = Auth::user();
                //Notification
                $type = "SUBSCRIBTION";
                $title = "SUBSCRIBTION NOTIFICATION";
                $message ="Great news! " .  $user->name . " just subscribe to your account. Keep writing awesome blogs!!";


                $notification = Notification::create([
                    'user_id' => $subscribtion->subscriber_id,
                    'unique_id' => $user->unique_id,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'is_read' => false,
                ]);

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

        public function viewsubscribers($uniqid)
        {
            // Get the current authenticated user
            $authenticatedUser = Auth::user();
        
            // Find the user based on the unique_id
            $user = User::where('unique_id', $uniqid)->firstOrFail();
        
            // Retrieve subscribers to the user identified by $uniqid
            $targetUserSubscribers = Subscribtion::with('user')
                                                ->where('user_id', $user->id)
                                                ->get();
        
            // Retrieve subscribers to the authenticated user
            $authenticatedUserSubscribers = Subscribtion::where('user_id', $authenticatedUser->id)
                                                        ->pluck('subscriber_id'); // Get subscriber IDs only
        
            // Find subscribers who have subscribed to both the target user and the authenticated user
            $commonSubscribers = Subscribtion::with('user')
                                                ->where('user_id', $user->id)
                                                ->whereIn('subscriber_id', $authenticatedUserSubscribers)
                                                ->get();
        
            // Check if the user has no subscribers at all
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
                'common_auth_subscribers' => $commonSubscribers,  
            ]);
        }
        
        


        public function subscribers()
        {
            // Find the user based on the id format
            $user = Auth()->user()->id;

            // Retrieve subscriptions for the user
            $subscriptions = Subscribtion::with('user')
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
            ]);
        }



           


}
 