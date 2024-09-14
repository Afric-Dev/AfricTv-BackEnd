<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
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

    
class ApiController extends Controller
{
        // Register Api(POST)
        public function register(StoreUserRequest $request)
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
            // Extract the first word from the name
            $firstWord = strtok($request->name, ' ');
            // Generate a random four-digit number
            $randomNumber = rand(1000, 9999);

            $unique_id = '@' .$firstWord . $randomNumber;

            //Auto giving the user 0 subscribers
            $subscribers_number = "0";

            // Create User
            $user = User::create([
                "avatar" => $imageUrl,
                "imageId" => $imageId,
                "name" => $request->name,
                "unique_id" => $unique_id,
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
       public function updateprofile(UpdateUserRequest $request)
        {
            // Get the id of the Authenticated User
            $userId = Auth::id();
            $request->validated($request->all());

            $user = User::find($userId);
            if ($user->updated_at->gt(Carbon::now()->subHour())) {
                return response()->json([
                    "status" => false,
                    "message" => "You cannot update your profile until 1 hour has passed."
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
          public function login(LoginUserRequest $request)
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

                //To send email after user login in successfully
                Mail::to($user->email)->send(new LoginMail($user));

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
            $userPosts = Post::where('user_id', $user->id)->get();
            $eduPosts = Educational::where('user_id', $user->id)->get();
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

