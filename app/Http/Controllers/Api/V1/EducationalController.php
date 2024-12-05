<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Educational;
use App\Models\User;
use App\Models\Eduview;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Cloudinary\Transformation\Video;
use Cloudinary\Transformation\Image;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;
use App\Models\Subscribtion;
use Illuminate\Http\JsonResponse;
use App\Models\Ads;

class EducationalController extends Controller
{
         public function educational(Request $request): JsonResponse
        {
            // Data Validation
            $request->validate([
                'edu_vid_path' => 'required|mimes:mp4,avi,mov,wmv,flv',
                'title' => 'required|max:255',
                'description' => 'required',
                'links' => 'nullable|max:255',
            ]);

            $user_name = Auth::user()->name;
            $firstWord = strtok($user_name, ' ');
            // Generate a random four-digit number
            $randomNumber = rand(100000, 999999);

            $eduID = '@' .$firstWord . $randomNumber;

            // Function to get video duration
            function getVideoDuration($file)
            {
                return 0; 
            }

            // Upload watermark image and get public ID
            $watermarkImage = cloudinary()->upload('https://res.cloudinary.com/dxbft8aci/image/upload/v1724838891/AfricTv_2_ov7mlc.png', [
                'folder' => 'africtv/watermarks',
                'resource_type' => 'image'
            ])->getPublicId();

            $videoPath = [];
            if ($request->hasFile('edu_vid_path')) {
                $files = $request->file('edu_vid_path');

            $transformations = [
                'overlay' => [
                    'public_id' => $watermarkImage,
                    'transformation' => [
                        'width' => 100,
                        'height' => 100,
                        'crop' => 'fit',
                        'opacity' => 30
                    ]
                ],
               'gravity' => 'north_west',
                'x' => 0.07,
                'y' => 0.01
            ];
                    $file = $files;
                    if ($file->isValid()) {
                        $duration = getVideoDuration($file);
                        if ($duration > 7200) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Video duration should not exceed 2 hours.',
                            ]);
                        }

                        try {
                            $uploadCloudinary = cloudinary()->upload($file->getRealPath(), [
                                'folder' => 'africtv/edu_videos',
                                'resource_type' => 'video',
                                'transformation' => array_merge($transformations, [
                                    'quality' => 'auto',
                                    'fetch_format' => 'auto'
                                ])
                            ]);
                            $videoPath = $uploadCloudinary->getSecurePath();
                            $eduvideoId = $uploadCloudinary->getPublicId();
                        } catch (\Exception $e) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Video upload failed: ' . $e->getMessage(),
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Invalid file upload.',
                        ]);
                    }
                
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Video must be uploaded.'
                ]);
            }

            // Storing educational data
            $edu = Educational::create([
                'edu_id' => $eduID,
                'user_id' => Auth::user()->id,
                'unique_id' => Auth::user()->unique_id,
                'edu_vid_path' => $videoPath,
                'eduvideoId' => $eduvideoId,
                'title' => $request->title,
                'edu_views' => $request->edu_views ?? 0,
                'vote_count' => $request->vote_count ?? 0,
                'thoughts_count' => $request->thoughts_count ?? 0,
                'favourites_count' => $request->favourites_count ?? 0,
                'links' => $request->links,
                'description' => $request->description,
            ]);

        $user = Auth::user();

        // Retrieve all subscribers of the user
        $subscribers = Subscribtion::where('subscriber_id', $user->id)->get();

        // Define notification details
        $type = "VIDEO POST";
        $title = "VIDEO POST UPLOAD NOTIFICATION";
        $message = "A new video has been uploaded by " . $user->name . ". Check it out now!";

        // Loop through subscribers and send notifications
        foreach ($subscribers as $subscriber) {
            Notification::create([
                'user_id' => $subscriber->user_id, // The ID of the subscriber
                'edu_id' => $edu->edu_id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'is_read' => false,
            ]);
        }


            return response()->json([
                'status' => true,
                'message' => 'Video Post Uploaded Successfully'
            ]);
        }


      public function deleteedupost(Request $request): JsonResponse
        {
            // Validate the request
            $request->validate([
                'edu_id' => 'required|regex:/^@\w+$/'
            ]);

            // Get the edu that has this id
            $eduId = $request->input('edu_id');
            $edu = Educational::where('edu_id', $eduId)->first();

            // Check if the educational post exists
            if (!$edu) {
                return response()->json([
                    "status" => false,
                    "message" => "Oops! Not found"
                ]);
            }

            // Check if the authenticated user is the owner of the edu post
            if (Auth::check() && Auth::user()->id === $edu->user_id) {
                // Delete the edu media
                
            if ($edu->eduvideoId) {
                // Attempt to delete the video from Cloudinary
                $response = Cloudinary::destroy($edu->eduvideoId, ['resource_type' => 'video']);
                
                // Log the full response for debugging
                Log::info('Cloudinary Deletion Response:', ['response' => $response]);

                // Check if the deletion was successful
                if ($response['result'] !== 'ok') {
                    return response()->json([
                        "status" => false,
                        "message" => "Failed to delete video",
                        //"cloudinary_response" => $response 
                    ]);
                }
            }

                // Delete the edu post
                $edu->delete();

                return response()->json([
                    "status" => true,
                    "message" => "Deleted successfully"
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "You are not permitted to delete this educational post"
                ]);
            }
        }




        public function readspecificedupost($edu_id, $title): JsonResponse
        {
            // Find the user by their unique_id
            $edu = Educational::where('edu_id', $edu_id)->first();
    
            // Ensure $edu is not null before trying to access its user_id
            if ($edu) {
                $user = User::where('id', $edu->user_id)->first();
            } else {
                // Handle the case where the edu is not found
                return response()->json([
                    'message' => 'Edu not found'
                ], 404);
            }
    
            // Check if user exists
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User Not Found',
                ]);
            }
    
            // Find the edu using the user's ID and edu_title
            $edu = Educational::with('user')
                        ->where('title', $title)
                        ->where('user_id', $user->id)
                        ->first();
    
            // Check if edu exists
            if (!$edu) {
                return response()->json([
                    'status' => false,
                    'message' => 'Edu Not Found',
                ]);
            }
            
            if ($edu->is_status == "INACTIVE") {
                return response()->json([
                    'status' => false,
                    'message' => 'Apologies, but this post has been removed due to violations of our privacy policy.',
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'edu data',
                'data' => $edu,
            ]);
        }

        public function eduviews(Request $request): JsonResponse
        {
            // Validate the incoming request
            $validatedData = $request->validate([
                'session_id' => 'nullable|string',
                'edu_viewed' => 'required|boolean',
                'edu_id' => 'required|regex:/^@\w+$/',
            ]);

            // Get the educational record based on the validated edu_id
            $edu = Educational::where('edu_id', $validatedData['edu_id'])->first();

            if (!$edu) {
                return response()->json([
                    'status' => false,
                    'message' => 'Edu Not Found',
                ]);
            }

            // Check if the edu has already been viewed
            if (Auth::check()) {
                $userId = Auth::id();
                $alreadyViewed = Eduview::where('user_id', $userId)
                    ->where('edu_id', $validatedData['edu_id'])
                    ->exists();

                if ($alreadyViewed) {
                    return response()->json([
                        'status' => false,
                        'message' => 'User has viewed this educational content before',
                    ]);
                }

                Eduview::create([
                    'user_id' => $userId,
                    'edu_id' => $validatedData['edu_id'],
                ]);
            } else {
                $sessionId = $validatedData['session_id'];
                if (!$sessionId) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Session ID is required for unauthenticated users',
                    ]);
                }

                $alreadyViewed = Eduview::where('session_id', $sessionId)
                    ->where('edu_id', $validatedData['edu_id'])
                    ->exists();

                if ($alreadyViewed) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Session has viewed this educational content before',
                    ]);
                }

                Eduview::create([
                    'session_id' => $sessionId,
                    'edu_id' => $validatedData['edu_id'],
                ]);
            }

            // Increment the edu views count
            $edu->increment('edu_views');

            return response()->json([
                'status' => true,
                'message' => 'Edu view updated successfully',
                'edu' => $edu,
            ]);
        }

    public function readedu(): JsonResponse
    {
        // Get the authenticated user
        $user = auth()->user();

        // Define a static cache key for the session
        $cacheKey = $user ? "user_educational_{$user->id}" : "guest_educational";

        // Check if educational data is already cached for this session
        if (session()->has($cacheKey)) {
            // Retrieve cached educational records
            $edus = session($cacheKey);
        } else {
            // Initialize the query for educational records
            $query = Educational::with('user')
                ->where('is_status', 'ACTIVE')
                ->orderBy('created_at', 'desc') // Sort by creation date (latest first)
                ->orderBy('edu_views', 'desc'); // Then by views (most viewed)

            $otherEdus = collect(); // Initialize for non-subscribed educational posts

            if ($user) {
                // Posts from users the authenticated user is subscribed to
                $subscriptions = $user->subscriptions()->pluck('subscribed_to_id');

                // Add condition for subscriptions
                $query->whereIn('user_id', $subscriptions);

                // Fetch a few posts from users the user is not subscribed to
                $otherEdus = Educational::whereNotIn('user_id', $subscriptions)
                    ->where('is_status', 'ACTIVE')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            } else {
                // For unauthenticated users or users following no one, fetch posts randomly
                $query->inRandomOrder()->limit(20); // Adjust limit as needed
            }

            // Get the main educational posts
            $edus = $query->get();

            // Calculate trending score for the educational posts
            $edus->map(function ($edu) {
                $edu->edu_score = ($edu->edu_views * 1.5) + ($edu->bookmark_count * 0.3);
                return $edu;
            });

            // Sort posts based on user authentication
            if ($user) {
                // Logged-in users: Sort by trending score and created_at
                $edus = $edus->sortByDesc(function ($edu) {
                    return $edu->edu_score + (strtotime($edu->created_at) / 1000000);
                });
            } else {
                // Guests: Randomly shuffle posts and use created_at for slight ordering
                $edus = $edus->shuffle()->sortByDesc(function ($edu) {
                    return rand(0, 100) + (strtotime($edu->created_at) / 1000000);
                });
            }

            // Add randomization and mix with other posts (if available)
            if ($otherEdus->isNotEmpty()) {
                $edus = $edus->merge($otherEdus);
            }

            // Shuffle the educational posts for variety
            $edus = $edus->shuffle();

            // Cache the educational posts in the session
            session([$cacheKey => $edus]);
        }

        // Fetch ads from the Ads model where ads_type = 'VID'
        $ads = Ads::where('ads_type', 'VID')
                  ->where('status', 'ACTIVE')
                  ->inRandomOrder()
                  ->limit(5)
                  ->get();

        // Inject ads into the educational posts list after every 5th post
        $finalEdus = collect();
        $edus->values()->each(function ($edu, $index) use ($ads, &$finalEdus) {
            $finalEdus->push($edu);
            // Add an ad after every 5th post
            if (($index + 1) % 3 === 0 && $ads->isNotEmpty()) {
                $finalEdus->push($ads->shift()); // Take the next ad
            }
        });

        // Count total educational posts after processing
        $eduCount = $finalEdus->count();

        // Prepare the response
        return response()->json([
            'status' => true,
            'message' => 'Educational data with ads',
            'data' => $finalEdus->values()->all(),
            'count' => $eduCount,
        ]);
    }

} 