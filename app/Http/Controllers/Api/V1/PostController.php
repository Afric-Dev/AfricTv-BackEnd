<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Models\Post;
use App\Models\Blogview;
use App\Models\User;
use App\Models\Likes;
use App\Models\Ads;
use App\Models\Notification;
use App\Models\Subscribtion;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePostRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse; 



class PostController extends Controller
{
     public function uploadpost(StorePostRequest $request): JsonResponse
    { 
        // Data Validation
        $request->validated($request->all());
        $user_name = Auth::user()->name;
        $firstWord = strtok($user_name, ' ');
        // Remove emojis and other non-alphanumeric characters
        $firstWord = preg_replace('/[^\p{L}\p{N}]/u', '', $firstWord);
        // Generate a random four-digit number
        $randomNumber = rand(10000, 99999);


        $postID = '@' .$firstWord . $randomNumber;

        // Initialize an array to store image paths
        $imagePaths = [];
        // Check if the request has files under the 'post_img_path' key
        if ($request->hasFile('post_img_path')) {
            foreach ($request->file('post_img_path') as $file) {
                // Validate if the file is valid
                if ($file->isValid()) {
                    // Upload the file to Cloudinary
                    $uploadCloudinary = cloudinary()->upload(
                        $file->getRealPath(),
                        [
                            'folder' => 'africtv/blogs_images',
                            'resource_type' => 'auto',
                            'transformation' => [
                                'quality' => 'auto',
                                'fetch_format' => 'auto'
                            ]
                        ]
                    );

                    // Store the secure URL of the uploaded image
                    $imagePaths[] = $uploadCloudinary->getSecurePath();
                    $postimageId[] = $uploadCloudinary->getPublicId();
                } else {
                    $imagePaths[] = "File is not valid";
                }
            }
        } else {
            $imagePaths[] = "No images uploaded";
            $postimageId[] = "No images uploaded";
        }

            // Handle cover_image upload and resizing
            if ($request->hasFile('cover_image')) {
                $uploadCloudinary = cloudinary()->upload(
                    $request->file('cover_image')->getRealPath(),
                    [
                        'folder' => 'africtv/blogs_cover_images',
                        'resource_type' => 'auto',
                        'transformation' => [
                            'quality' => 'auto',
                            'fetch_format' => 'auto'
                        ]
                    ]
                );
                $coverimagePath = $uploadCloudinary->getSecurePath();
                $coverimageId = $uploadCloudinary->getPublicId();
             } else {
                $coverimagePath = "No images uploaded";
                $coverimageId = "No images uploaded";
            }


        // Function to get video duration
        function getVideoDuration($file)
        {
            return 0; 
        }

        if ($request->hasFile('post_vid_path')) {
            // Get the duration of the video
            $duration = getVideoDuration($request->file('post_vid_path'));

            // Validate video duration
            if ($duration > 7200) { // 7200 seconds = 2 hours
                return response()->json([
                    'status' => false,
                    'message' => 'Video duration should not exceed 2 hours.',
                ]);
            }

            try {
                $uploadCloudinary = cloudinary()->upload(
                    $request->file('post_vid_path')->getRealPath(),
                    [
                        'folder' => 'africtv/blogs_videos',
                        'resource_type' => 'auto',
                        'transformation' => [
                            'quality' => 'auto',
                            'fetch_format' => 'auto'
                        ]
                    ]
                );
                $videoPath = $uploadCloudinary->getSecurePath();
                $postvideoId = $uploadCloudinary->getPublicId();
            } catch (\Exception $e) {
                // Handle upload error
                return response()->json([
                    'status' => false,
                    'message' => 'Video upload failed: ' . $e->getMessage(),
                ]);
            }
        } else {
            $videoPath = "No Video Uploaded";
            $postvideoId = "No Video Uploaded";
        }


        //Handle document upload
        if ($request->hasFile('post_pdf_path')) {
            $docPath = $request->file('post_pdf_path')->store('public/documents');
            $docPath = str_replace('public/', '', $docPath);
        } else {
            $docPath = "no file uploaded";
        }

        //Handle song upload
        if ($request->hasFile('post_song_path')) {
            $songPath = $request->file('post_song_path')->store('public/songs');
            $songPath = str_replace('public/', '', $songPath);
        } else {
            $songPath = "no file uploaded";
        }

        // Storing post data
        $post = Post::create([
            "user_id" => Auth::user()->id,
            // "unique_id" => Auth::user()->unique_id,
            "post_id" => $postID,
            "cover_image" => $coverimagePath ?? 'no image passed',
            "coverimageId" => $coverimageId ?? 'no public id passed',
            "post_img_path" => json_encode($imagePaths),
            "postimageId" => json_encode($postimageId) ?? 'no public id passed',
            "post_vid_path" => $videoPath,
            "postvideoId" => $postvideoId ?? 'no public id passed',
            "post_pdf_path" => $docPath,
            "post_song_path" => $songPath,
            "category" => $request->category,
            "post_title" => $request->post_title,
            "PostbodyHtml" => $request->PostbodyHtml,
            "postbodyJson" => $request->postbodyJson,
            "postBodytext" => $request->postBodytext,
            "post_views" => $request->post_views ?? 0,
            "likes_count" => $request->likes_count ?? 0,
            "comments_count" => $request->comments_count ?? 0,
            "link" => $request->link,
            "hashtags" => $request->hashtags,
            //"post_ending" => $request->post_ending,
            //"date" => $request->date,
        ]);


        $user = Auth::user();

        // Retrieve all subscribers of the user
        $subscribers = Subscribtion::where('subscriber_id', $user->id)->get();

        // Define notification details
        $type = "BLOG POST";
        $title = "BLOG POST UPLOAD NOTIFICATION";
        $message = "A new blog post has been uploaded by " . $user->name . ". Check it out now!";

        // Loop through subscribers and send notifications
        foreach ($subscribers as $subscriber) {
            Notification::create([
                'receiver_id' => $subscriber->user_id, // The ID of the subscriber
                'user_id' => Auth::user()->id,
                'post_id' => $post->post_id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'is_read' => false,
            ]);
        }


        return response()->json([
            "status" => true,
            "message" => "Blog Uploaded Successfully"
        ]);
    }

    //Not in use for now till after launch (Working on it)
    //   public function updateposts(Request $request)
    // {
    //     $request->validate([
    //         "id" => "required|exists:posts,id",
    //         "cover_image" => 'nullable|image|max:2048',
    //         'post_img_path' => 'array',
    //         'post_img_path.*' => 'nullable|image|max:2048',
    //         'post_vid_path' => 'nullable|mimes:mp4,avi,mov,wmv,flv',
    //         "post_pdf_path" => "nullable|mimes:pdf,doc,docx",
    //         "post_song_path" => "nullable|mimes:mp3,wav,aac,flac",
    //         "category" => "required",
    //         "post_title" => "required",
    //         "PostbodyHtml" => "required",
    //         "postbodyJson" => "required",
    //         "postBodytext" => "required",
    //         "link" => "nullable",
    //         "hashtags" => "nullable",
    //         "post_ending" => "nullable",
    //     ]);

    //     $postId = $request->input('id'); 
    //     $post = Post::find($postId);

    //     if ($post) {
    //         // Update post properties
    //         $post->post_title = $request->post_title;
    //         $post->category = $request->category;
    //         $post->link = $request->link;
    //         $post->PostbodyHtml = $request->PostbodyHtml;
    //         $post->postbodyJson = $request->postbodyJson;
    //         $post->postBodytext = $request->postBodytext;
    //         $post->hashtags = $request->hashtags;
    //         $post->post_ending = $request->post_ending;

    //     // Initialize an array to store image paths
    //     $imagePaths = [];
    //     // Check if the request has files under the 'post_img_path' key
    //     if ($request->hasFile('post_img_path')) {
    //         foreach ($request->file('post_img_path') as $file) {
    //             // Validate if the file is valid
    //             if ($file->isValid()) {
    //                 // Upload the file to Cloudinary
    //                 $uploadCloudinary = cloudinary()->upload(
    //                     $file->getRealPath(),
    //                     [
    //                         'folder' => 'africtv/blogs_images',
    //                         'resource_type' => 'auto',
    //                         'transformation' => [
    //                             'quality' => 'auto',
    //                             'fetch_format' => 'auto'
    //                         ]
    //                     ]
    //                 );

    //                 // Store the secure URL of the uploaded image
    //                 $imagePaths[] = $uploadCloudinary->getSecurePath();
    //             } else {
    //                 $imagePaths[] = "File is not valid";
    //             }
    //         }
    //     } else {
    //         $imagePaths[] = "No images uploaded";
    //     }


    //       //Handle blog post cover image (required)
    //       if ($request->hasFile('cover_image')) {
    //             $uploadCloudinary = cloudinary()->upload(
    //                 $request->file('cover_image')->getRealPath(),
    //                 [
    //                     'folder' => 'africtv/blogs_cover_images',
    //                     'resource_type' => 'auto',
    //                     'transformation' => [
    //                         'quality' => 'auto',
    //                         'fetch_format' => 'auto'
    //                     ]
    //                 ]
    //             );
    //             $coverimagePath = $uploadCloudinary->getSecurePath();
    //     } else {
    //         return response()->json([
    //             "status" => false,
    //             "message" => "Cover Image Required"
    //         ], 400);
    //     }

    //     // Function to get video duration
    //     function getVideoDuration($file)
    //     {
    //         return 0; 
    //     }

    //     if ($request->hasFile('post_vid_path')) {
    //         // Get the duration of the video
    //         $duration = getVideoDuration($request->file('post_vid_path'));

    //         // Validate video duration
    //         if ($duration > 7200) { // 7200 seconds = 2 hours
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Video duration should not exceed 2 hours.',
    //             ]);
    //         }

    //         try {
    //             $uploadCloudinary = cloudinary()->upload(
    //                 $request->file('post_vid_path')->getRealPath(),
    //                 [
    //                     'folder' => 'africtv/blogs_videos',
    //                     'resource_type' => 'auto',
    //                     'transformation' => [
    //                         'quality' => 'auto',
    //                         'fetch_format' => 'auto'
    //                     ]
    //                 ]
    //             );
    //             $videoPath = $uploadCloudinary->getSecurePath();
    //         } catch (\Exception $e) {
    //             // Handle upload error
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Video upload failed: ' . $e->getMessage(),
    //             ]);
    //         }
    //     } else {
    //         $videoPath = null;
    //     }

    //         // Handle document upload
    //         if ($request->hasFile('post_pdf_path')) {
    //             $docPath = $request->file('post_pdf_path')->store('public/documents');
    //             $docPath = str_replace('public/', '', $docPath);
    //         } else {
    //             $docPath = "no file uploaded";
    //         }

    //         // Handle song upload
    //         if ($request->hasFile('post_song_path')) {
    //             $songPath = $request->file('post_song_path')->store('public/songs');
    //             $songPath = str_replace('public/', '', $songPath);
    //         } else {
    //             $songPath = "no file uploaded";
    //         }
    //         // Save the updated post
    //         $post->save();

    //         // Send mail if it was successful (commented out)
    //         // Mail::to($request->user_email)->send(new ProfileUpdateMail($post));

    //         return response()->json([
    //             "status" => true,
    //             "message" => "BlogPost Updated Successfully"
    //         ]);
    //     } else {
    //         return response()->json([
    //             "status" => false,
    //             "message" => "BlogPost Not Found"
    //         ]);
    //     }
    // }


    public function deleteposts(Request $request): JsonResponse
    {
        // Validate the request
        $request->validate([
            'post_id' => 'required|regex:/^@\w+$/'
        ]);

        // Get the post by post_id
        $post = Post::where('post_id', $request->input('post_id'))->first();

        // Check if the post exists
        if (!$post) {
            return response()->json([
                "status" => false,
                "message" => "Post not found"
            ]);
        }

        // Check if the authenticated user is the owner of the post
        if (Auth::user()->id === $post->user_id) {
            
            // Delete the post media's
            if ($post->coverimageId) {
                Cloudinary::destroy($post->coverimageId);
            }
            if ($post->postimageId) {

            // Check if it's an array
            if (is_array($post->postimageId)) {
                // Convert array of IDs to a comma-separated string
                $imageIds = implode(',', $post->postimageId);
            } else {
                // If it's a single ID, just use it directly
                $imageIds = $post->postimageId;
            }
            Cloudinary::destroy($imageIds);
            }
            
            if ($post->postvideoId) {
                Cloudinary::destroy($post->postvideoId);
            }

            // Delete the post
            $post->delete();

            return response()->json([
                "status" => true,
                "message" => "Post deleted successfully"
            ]);
        } else {
            return response()->json([
                "status" => false,
                "message" => "You are not permitted to delete this post"
            ]);
        }
    }


    public function postviews(Request $request): JsonResponse
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'session_id' => 'nullable|string',
            'post_viewed' => 'required|boolean',
            'post_id' => 'required|exists:posts,id',
        ]);

        // Get the post based on the validated post_id
        $post = Post::find($validatedData['post_id']);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post Not Found',
            ]);
        }

        // Check if the post has already been viewed
        if (Auth::check()) {
            $userId = Auth::id();
            $alreadyViewed = Blogview::where('user_id', $userId)
                ->where('post_id', $validatedData['post_id'])
                ->exists();

            if ($alreadyViewed) {
                return response()->json([
                    'status' => false,
                    'message' => 'User has viewed this post before',
                ]);
            }

            Blogview::create([
                'user_id' => $userId,
                'post_id' => $validatedData['post_id'],
            ]);
        } else {
            $sessionId = $validatedData['session_id'];
            if (!$sessionId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Session ID is required for unauthenticated users',
                ]);
            }

            $alreadyViewed = Blogview::where('session_id', $sessionId)
                ->where('post_id', $validatedData['post_id'])
                ->exists();

            if ($alreadyViewed) {
                return response()->json([
                    'status' => false,
                    'message' => 'Session has viewed this post before',
                ]);
            }

            Blogview::create([
                'session_id' => $sessionId,
                'post_id' => $validatedData['post_id'],
            ]);
        }

        // Increment the post views count
        $post->increment('post_views');

        return response()->json([
            'status' => true,
            'message' => 'Post view updated successfully',
            // 'post' => $post,
        ]);
    }
    //not in use
    // public function readpost(): JsonResponse
    // {
    //     $user = auth()->user();
    //     $cacheKey = $user ? "user_posts_{$user->id}" : "guest_posts";

    //     if (session()->has($cacheKey)) {
    //         $posts = session($cacheKey);
    //     } else {
    //         $query = Post::with('user')
    //             ->where('is_status', 'ACTIVE')
    //             ->withCount(['likes', 'comments']);

    //         $otherPosts = collect();

    //         if ($user) {
    //             // Get IDs of users the authenticated user is subscribed to
    //             $subscriptions = Subscribtion::where('subscriber_id', $user->id)
    //                 ->pluck('user_id');

    //             // Add subscription condition to the query
    //             $query->whereIn('user_id', $subscriptions);

    //             // Fetch posts from non-subscribed users
    //             $otherPosts = Post::whereNotIn('user_id', $subscriptions)
    //                 ->where('is_status', 'ACTIVE')
    //                 ->withCount(['likes', 'comments'])
    //                 ->inRandomOrder()
    //                 ->limit(5)
    //                 ->get();
    //         } else {
    //             // For unauthenticated users, randomize posts
    //             $query->orderByRaw('RAND() * TIMESTAMPDIFF(SECOND, created_at, NOW()) ASC')->limit(20);
    //         }

    //         $posts = $query->get();

    //         $posts->map(function ($post) {
    //             $post->post_score = ($post->likes_count * 1.5) +
    //                                 ($post->post_views * 1) +
    //                                 ($post->bookmark_count * 0.3);
    //             return $post;
    //         });

    //         if ($user) {
    //             $posts = $posts->shuffle()->sortByDesc(function ($post) {
    //                 return $post->post_score + (strtotime($post->created_at) / 1000000);
    //             });
    //         } else {
    //             $posts = $posts->shuffle()->sortByDesc(function ($post) {
    //                 return rand(0, 100) + (strtotime($post->created_at) / 1000000);
    //             });
    //         }

    //         if ($otherPosts->isNotEmpty()) {
    //             $posts = $posts->merge($otherPosts);
    //         }

    //         $posts = $posts->shuffle();
    //         session([$cacheKey => $posts]);
    //     }

    //  $ads = Ads::where(function($query) {
    //                   $query->where('ads_type', 'PIC')
    //                         ->orWhere('ads_type', 'LINK');
    //               })
    //               ->where('status', 'ACTIVE')
    //               ->inRandomOrder()
    //               ->limit(5)
    //               ->get();

    //     $finalPosts = collect();
    //     $posts->values()->each(function ($post, $index) use ($ads, &$finalPosts) {
    //         $finalPosts->push($post);
    //         if (($index + 1) % 5 === 0 && $ads->isNotEmpty()) {
    //             $finalPosts->push($ads->shift());
    //         }
    //     });

    //     $postCount = $finalPosts->count();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Post data with ads',
    //         'data' => $finalPosts->values()->all(),
    //         'count' => $postCount,
    //     ]);
    // }

    public function readpost(): JsonResponse
    {
        $user = auth()->user();

        if ($user) {
            // Get IDs of users the authenticated user is subscribed to
            $subscriptions = Subscribtion::where('user_id', $user->id)
                                ->pluck('subscriber_id');

            // Fetch posts from subscribed users
            $subscribedPosts = Post::with('user')
                ->whereIn('user_id', $subscriptions)
                ->where('is_status', 'ACTIVE')
                ->withCount(['likes', 'comments'])
                ->get();

            // Fetch posts from non-subscribed users (limited to 5)
            $otherPosts = Post::with('user')
                ->whereNotIn('user_id', $subscriptions)
                ->where('is_status', 'ACTIVE')
                ->withCount(['likes', 'comments'])
                ->inRandomOrder()
                ->limit(5)
                ->get();

            // Merge both sets of posts
            $posts = $subscribedPosts->merge($otherPosts);
        } else {
            // For guests, simply fetch all active posts
            $posts = Post::with('user')
                ->where('is_status', 'ACTIVE')
                ->withCount(['likes', 'comments'])
                ->get();
        }

        // Score each post to favor newly created posts
        // Newer posts will have a higher timestamp value plus a small random factor
        $posts->each(function ($post) {
            $post->score = $post->created_at->timestamp + rand(0, 100);
        });

        // Sort posts descending by score so newer posts appear more prominently
        $posts = $posts->sortByDesc('score')->values();

        // Retrieve ads in random order (mixing both PIC and LINK types)
        $ads = Ads::where(function ($query) {
                    $query->where('ads_type', 'PIC')
                          ->orWhere('ads_type', 'LINK');
                })
                ->where('status', 'ACTIVE')
                ->inRandomOrder()
                ->limit(5)
                ->get();

        // Merge posts and ads, then shuffle to mix them together
        $finalItems = $posts->merge($ads)->shuffle()->values();

        return response()->json([
            'status'  => true,
            'message' => 'Post data with ads mixed',
            'data'    => $finalItems->all(),
            'count'   => $finalItems->count(),
        ]);
    }



      public function toppost(): JsonResponse
    {
        // Fetch active posts created within the last 24 hours
        $posts = Post::with('user')
            ->where('created_at', '>=', now()->subDay())
            ->where('is_status', 'ACTIVE')
            ->withCount(['likes', 'comments']) 
            ->get();

        // Calculate a trending score for each post
        $posts->map(function ($post) {
            // Formula for trending score
            $post->post_score = ($post->likes_count * 1.5) +
                                ($post->post_views * 1) +
                                ($post->bookmark_count * 0.3);
            return $post;
        });

        // Filter posts with a score between 1,000 and 2,000
        $filteredPosts = $posts->filter(function ($post) {
            return $post->post_score >= 1000 && $post->post_score <= 2000;
        });

        // Sort by score in descending order and take the top 7 posts
        $topPosts = $filteredPosts->sortByDesc('post_score')->take(7);

        // Shuffle posts if needed, prioritizing high-scoring ones
        $shuffledPosts = $topPosts->shuffle();

        $postCount = $shuffledPosts->count();

        // Return the result as a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Post data',
            'data' => $shuffledPosts->values()->all(),
            'count' => $postCount,
        ]);
    }

    public function readspecificpost($post_id, $post_title): JsonResponse
    {
        // Find the user by their unique_id
        $post = Post::where('post_id', $post_id)->first();

        // Ensure $post is not null before trying to access its user_id
        if ($post) {
            $user = User::where('id', $post->user_id)->first();
        } else {
            // Handle the case where the post is not found
            return response()->json([
                'message' => 'Post not found'
            ], 404);
        }

        // Check if user exists
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }

        // Find the post using the user's ID and post_title
        $post = Post::with('user')
                    ->where('post_title', $post_title)
                    ->where('user_id', $user->id)
                    ->first();

        // Check if post exists
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post Not Found',
            ]);
        }

        if ($post->is_status == "INACTIVE") {
            return response()->json([
                'status' => false,
                'message' => 'Apologies, but this blog has been removed due to violations of our privacy policy.',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Post data',
            'data' => $post,
        ]);
    }



}