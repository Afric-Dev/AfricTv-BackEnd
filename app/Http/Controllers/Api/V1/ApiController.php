<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Mail\RegistrationMail;
use App\Mail\LoginMail;
use App\Mail\MeduimPaymentMail;
use App\Mail\PremuimPaymentMail;
use App\Mail\ProfileUpdateMail;
use Illuminate\Support\Facades\Mail;
use MailerSend\MailerSend;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use App\Models\Post;
use App\Models\Educational;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\UpdateUserRequest;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Carbon\Carbon;    
use Illuminate\Http\JsonResponse;

    
class ApiController extends Controller
{
        // Register Api(POST)
        public function register(StoreUserRequest $request): JsonResponse
        {
            // Data Validation
            $request->validated($request->all());

            // Handle avatar upload and resizing
            if ($request->hasFile('avatar')) {
                $uploadCloudinary = cloudinary()->upload(
                    $request->file('avatar')->getRealPath(),
                    [
                        'folder' => 'africtv/avatars',
                        'resource_type' => 'auto',
                        'transformation' => [
                            'quality' => 'auto',
                            'fetch_format' => 'auto'
                        ]
                    ]
                );
                $imageUrl = $uploadCloudinary->getSecurePath();
                $imageId = $uploadCloudinary->getPublicId();
            } else {
                $imageUrl = Null;
                $imageId = Null;
            }

            //Generating unique_id
            // Extract the first and last words from the name
            $nameParts = explode(' ', trim($request->name));
            $firstWord = $nameParts[0] ?? '';
            $lastWord = $nameParts[count($nameParts) - 1] ?? '';
            $baseId = "@{$firstWord}{$lastWord}";

            // Initialize unique_id with baseId
            $uniqueId = $baseId;

            // Check if the unique_id is taken using the User model
            $count = 0;
            while (\App\Models\User::where('unique_id', $uniqueId)->exists()) {
                $count++;

                if ($count == 1) {
                    // If first attempt, add an underscore between the words
                    $uniqueId = "@{$firstWord}_{$lastWord}";
                } else {
                    // If still taken, append a random 4-digit number
                    $uniqueId = "@{$firstWord}" . rand(1000, 9999);
                }
            }


            //Auto giving the user 0 subscribers
            $subscribers_number = "0";

            // Create User
            $user = User::create([
                "avatar" => $imageUrl,
                "imageId" => $imageId,
                "name" => $request->name,
                "unique_id" => $uniqueId,
                "email" => $request->email,
                "phone_number" => $request->phone_number,
                "subscribers_number" => $subscribers_number,
                "bio" => $request->bio,
                "link" => $request->link,
                "password" => Hash::make($request->password)
            ]);

            Mail::to($user->email)->send(new RegistrationMail($user));

            return response()->json([
                "status" => true,
                "message" => "Account Created Successfully"
            ]);
        }

        //Update Profile (PUT)
       public function updateprofile(UpdateUserRequest $request): JsonResponse
        {
            // Get the id of the Authenticated User
            $userId = Auth::id();
            $request->validated($request->all());

            $user = User::find($userId);
            if ($user->updated_at->gt(Carbon::now()->subMinute())) {
                return response()->json([
                    "status" => false,
                    "message" => "You cannot update your profile until 1 minute has passed."
                ]);
            }


            if ($user) {
                // Update user properties only if the input is provided, otherwise retain the current data
                $user->name = $request->input('name') ?: $user->name;
                $user->email = $user->email;
                $user->phone_number = $request->input('phone_number') ?: $user->phone_number;
                $user->bio = $request->input('bio') ?: $user->bio;
                $user->link = $request->input('link') ?: $user->link;
                // Handle password update only if it's provided
                if ($request->filled('password')) {
                    $user->password = Hash::make($request->password);
                }

                // Handle avatar upload if provided
                if ($request->hasFile('avatar')) {
                 
                  // Delete the user former avatar
                  if ($user->imageId) {
                        Cloudinary::destroy($user->imageId);
                  }

                    $uploadCloudinary = cloudinary()->upload(
                        $request->file('avatar')->getRealPath(),
                        [
                            'folder' => 'africtv/avatars',
                            'resource_type' => 'auto',
                            'transformation' => [
                                'quality' => 'auto',
                                'fetch_format' => 'auto'
                            ]
                        ]
                    );
                    $imageUrl = $uploadCloudinary->getSecurePath();
                    $imageId = $uploadCloudinary->getPublicId();
                    $user->imageId = $imageId;
                    $user->avatar = $imageUrl;
                }

                // Save the updated user
                $user->save();

                // Send mail if the update was successful
                Mail::to($user->email)->send(new ProfileUpdateMail($user));

                return response()->json([
                    "status" => true,
                    "message" => "Profile Updated Successfully"
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "User Not Found"
                ]);
            }
        }


          // Login Api(POST)
          public function login(LoginUserRequest $request): JsonResponse
        {
            // Data validation
            $request->validated($request->all());
            // Attempt authentication
            if (Auth::attempt([
                "email" => $request->email,
                "password" => $request->password
            ])) {
                $user = Auth::user();

                // Create a new token for the user
                $accessToken = $user->createToken('myToken')->plainTextToken;

                if ($user->status == 'BANNED') {
                        $user->tokens()->delete();
                        return response()->json([
                            "status" => false,
                            "message" => "We have observed a violation of our Terms and Conditions. Unfortunately, your account has been BANNED. This process may take up to 24 hours."
                        ]);
                }


                //To send email after user login in successfully
                Mail::to($user->email)->send(new LoginMail($user));

                //Notification
                $type = "LOGIN";
                $title = "LOGIN NOTIFICATION";
                $message = "We noticed a new login to your account. If this was you, no further action is needed. However, if you did not authorize this login, please secure your account immediately by resetting your password and reviewing your account activity.";

                $notification = Notification::create([
                    'receiver_id' => $user->id,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'is_read' => false,
                ]);
             
                return response()->json([
                    "status" => true,
                    "message" => "Login successful",
                    'access_token' => $accessToken,
                     "user" => $user
                ]);
            }

        return response()->json([
            "status" => false,
            "message" => "Invalid credentials"
        ]);
    }

    //Not in use for now
    // private function mapPaymentTypeToSubscriptionStatus($paymentType) {
    //     switch ($paymentType)
    // {
    //         case '1':
    //             return '1';
    //         case '2':
    //             return '2';
    //         default:
    //             return '0';
    // }
    // }

    // //Profile Api(POST);
    // public function payment(Request $request)
    // {
    //         // Data Validation
    //         $request->validate([
    //             "user_id" => "required",
    //             "user_name" => "required",
    //             "user_email" => "required|email",
    //             "amount" => "required",
    //             "payment_type" => "required",
    //             "payment_status" => "required",
    //             "payment_method" => "required",
    //             "currency" => "required",
    //         ]);

    //        // Storing payment data
    //         $payment = Payment::create([
    //             "user_id" => $request->user_id,
    //             "user_name" => $request->user_name,
    //             "user_email" => $request->user_email,
    //             "amount" => $request->amount,
    //             "payment_type" => $request->payment_type,
    //             "payment_status" => $request->payment_status,
    //             "payment_method" => $request->payment_method,
    //             "currency" => $request->currency,
    //         ]);   

    //         // Updating user subscription status based on payment type
    //        $user = User::find($request->user_id);

    //         if ($user) {
    //             // Mapping payment type to subscription status
    //             $subscriptionStatus = $this->mapPaymentTypeToSubscriptionStatus($request->payment_type);
                
    //             // Update subscription status
    //             $user->subscribtion_status = $subscriptionStatus;
    //             $user->save();
    //         } else {
    //             return response()->json([
    //                 "status" => false,
    //                 "message" => "User was not found"
    //             ]);
    //         }

    //         if ($request->payment_type == 1) {
    //             Mail::to($payment->user_email)->send(new MeduimPaymentMail($payment));
    //         } elseif ($request->payment_type == 2) {
    //            Mail::to($payment->user_email)->send(new PremuimPaymentMail($payment));
    //         }

    //         return response()->json([
    //             "status" => true,
    //             "message" => "Payment Made Successfully"
    //         ]);

    //     }


        //  public function posts()
        // {
        //     return $this->hasMany(Post::class);
        // }

        // Profile API (GET)
        public function profile()
        {
            $user = auth()->user();
            $userPosts = Post::where('user_id', $user->id)
                         ->orderBy('created_at', 'desc')
                         ->get();
            $eduPosts = Educational::where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->get();
            $postCount = $userPosts->count();
            $noofeduposts = $eduPosts->count();

            // $feedPosts = Feedposts::where('user_id', $user->id)->get();
            // $feedpostCount = $userPosts->count();
            return response()->json([
                'status' => true,
                'message' => 'Profile data',
                'user' => $user,
                'noofblogpost' => $postCount,
                'blogposts' => $userPosts,
                'eduposts' => $eduPosts,
                'noofeduposts' => $noofeduposts,
        ]);
      }


        // Logout API (GET)
        public function logout(Request $request)
        {
            $user = $request->user(); 

            // Revoke all tokens associated with the user
            $user->tokens()->delete();

            // Return a JSON response indicating successful logout
            return response()->json([
                "status" => true,
                "message" => "User logged out"
            ]);
        }

        public function IndividualProfile($uniqid)
        {
            $user = User::with(['posts', 'educationals'])
                        ->where('unique_id', $uniqid)
                        ->first();

            // Check if user exists
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User Not Found',
                ]);
            }

            if ($user->status === 'BANNED') {
                return response()->json([
                    'status' => false,
                    'message' => 'Sorry! This user has been BANNED due to a policy violation.',
                ]);
            }

            // Fetch posts and educational posts using the user's ID
            $userPosts = Post::where('user_id', $user->id)->get();
            $eduPosts = Educational::where('user_id', $user->id)->get();

            $postCount = $userPosts->count();
            $noofeduposts = $eduPosts->count();

            return response()->json([
                'status' => true,
                'message' => 'User data',
                'data' => $user,
                'noofblogpost' => $postCount,
                'blogposts' => $userPosts,
                'eduposts' => $eduPosts,
                'noofeduposts' => $noofeduposts,
            ]);
        }



}

