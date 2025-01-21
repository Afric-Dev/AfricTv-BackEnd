<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comments;
use App\Models\Post;
use App\Models\User;
use App\Models\InnerComment;
use Intervention\Image\Facades\Image;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;


class CommentsController extends Controller
{ 
    public function comments(Request $request): JsonResponse
    {
        // Validate the incoming request
        $request->validate([
            "post_id" => "required|regex:/^@\w+$/",
            "parent_id" => 'nullable|exists:comments,id',
            "comments" => "required",
            "comments_vid_path" => "nullable|mimes:mp4,avi,mov,wmv,flv|max:20480", // Max video file size: 20MB
            'comments_img_path' => 'array',
            'comments_img_path.*' => "nullable|image|max:2048",
            "comments_link" => "nullable|url", 
        ]);

        // Initialize an array to store image paths
        $imagePaths = [];
        // Check if the request has files under the 'comments_img_path' key
        if ($request->hasFile('comments_img_path')) {
            foreach ($request->file('comments_img_path') as $file) {
                // Validate if the file is valid
                if ($file->isValid()) {
                    // Upload the file to Cloudinary
                    $uploadCloudinary = cloudinary()->upload(
                        $file->getRealPath(),
                        [
                            'folder' => 'africtv/comment_images',
                            'resource_type' => 'auto',
                            'transformation' => [
                                'quality' => 'auto',
                                'fetch_format' => 'auto'
                            ]
                        ]
                    );

                    // Store the secure URL of the uploaded image
                    $imagePaths[] = $uploadCloudinary->getSecurePath();
                }
            }
        }

        // Function to get video duration
        function getVideoDuration($file)
        {
        
            return 0; 
        }

        // Process video upload if exists
        if ($request->hasFile('comments_vid_path')) {
            // Get the duration of the video
            $duration = getVideoDuration($request->file('comments_vid_path'));

            // Validate video duration
            if ($duration > 7200) { // 7200 seconds = 2 hours
                return response()->json([
                    'status' => false,
                    'message' => 'Video duration should not exceed 2 hours.',
                ]);
            }

            try {
                // Upload video to Cloudinary
                $uploadCloudinary = cloudinary()->upload(
                    $request->file('comments_vid_path')->getRealPath(),
                    [
                        'folder' => 'africtv/comment_videos',
                        'resource_type' => 'auto',
                        'transformation' => [
                            'quality' => 'auto',
                            'fetch_format' => 'auto'
                        ]
                    ]
                );
                $videoPath = $uploadCloudinary->getSecurePath();
            } catch (\Exception $e) {
                // Handle upload error
                return response()->json([
                    'status' => false,
                    'message' => 'Video upload failed: ' . $e->getMessage(),
                ]);
            }
        } else {
            $videoPath = null;
        }

        // Find the post by 'post_id'
        $post = Post::where('post_id', $request->post_id)->first();

        if (!$post) {
            return response()->json([
                "status" => false,
                "message" => "Oops! Not found"
            ]);
        }

        // Create the comment
        $comments = Comments::create([
            "post_id" => $post->id,
            "user_id" => Auth::user()->id,
            "parent_id" => $request->parent_id, // This will be null if it’s a top-level comment
            "comments" => $request->comments,
            "comments_vid_path" => $videoPath,
            "comments_img_path" => json_encode($imagePaths), 
            "comments_link" => $request->comments_link,

        ]);

        // Increment the comments count for the post
        $post->increment('comments_count');

        $user = Auth::user();

        if (empty($request->parent_id)) {
            //Notification
            $type = "THOUGHT";
            $title = "THOUGHT NOTIFICATION";
            $message = $user->name . " has just shared their thoughts on your blog post!";


            $notification = Notification::create([
                'user_id' => Auth::user()->id,
                'receiver_id' => $post->user_id,
                'post_id' => $post->post_id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'is_read' => false,
            ]);
        } else {
            //Notification
            $type = "THOUGHT";
            $title = "THOUGHT NOTIFICATION";
            $message = $user->name . " has just replied to your thoughts on a blog post!";


            $notification = Notification::create([
                'user_id' => Auth::user()->id,
                'receiver_id' => $post->user_id,
                'post_id' => $post->post_id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'is_read' => false,
            ]);
        }

        return response()->json([
            "status" => true,
            "message" => "Comment Uploaded Successfully",
            "comment" => $comments
        ]);
    }


     // public function updatecomments(Request $request)
     //    {
     //        $request->validate([
     //            "comment_id" => "required",
     //            "post_id" => "required",        
     //            "comments" => "required",
     //            "comments_vid_path" => "nullable|mimes:mp4,avi,mov,wmv,flv",
     //            'comments_img_path' => 'array',
     //            'comments_img_path.*' => "nullable|image|max:2048",
     //            "comments_link" => "nullable", 
     //        ]);

     //        $commentId = $request->input('comment_id'); 
     //        $comment = Comments::find($commentId);

     //        if ($comment) {
     //            // Update comment properties
     //            $comment->comments = $request->comments;
     //            $comment->comments = $request->comments;
              
     //            // Handle image upload
     //            $imagePaths = [];

     //           // Initialize an array to store image paths
     //            $imagePaths = [];
     //            // Check if the request has files under the 'comments_img_path' key
     //            if ($request->hasFile('comments_img_path')) {
     //                foreach ($request->file('comments_img_path') as $file) {
     //                    // Validate if the file is valid
     //                    if ($file->isValid()) {
     //                        // Upload the file to Cloudinary
     //                        $uploadCloudinary = cloudinary()->upload(
     //                            $file->getRealPath(),
     //                            [
     //                                'folder' => 'africtv/comment_images',
     //                                'resource_type' => 'auto',
     //                                'transformation' => [
     //                                    'quality' => 'auto',
     //                                    'fetch_format' => 'auto'
     //                                ]
     //                            ]
     //                        );

     //                        // Store the secure URL of the uploaded image
     //                        $imagePaths[] = $uploadCloudinary->getSecurePath();
     //                    } else {
     //                        $imagePaths[] = "File is not valid";
     //                    }
     //                }
     //            } else {
     //                $imagePaths[] = "No images uploaded";
     //            }




            // Function to get video duration
            // function getVideoDuration($file)
            // {
            //     return 0; 
            // }

            // if ($request->hasFile('comments_vid_path')) {
            //     // Get the duration of the video
            //     $duration = getVideoDuration($request->file('comments_vid_path'));

            //     // Validate video duration
            //     if ($duration > 7200) { // 7200 seconds = 2 hours
            //         return response()->json([
            //             'status' => false,
            //             'message' => 'Video duration should not exceed 2 hours.',
            //         ]);
            //     }

            //     try {
            //         $uploadCloudinary = cloudinary()->upload(
            //             $request->file('comments_vid_path')->getRealPath(),
            //             [
            //                 'folder' => 'africtv/comment_videos',
            //                 'resource_type' => 'auto',
            //                 'transformation' => [
            //                     'quality' => 'auto',
            //                     'fetch_format' => 'auto'
            //                 ]
            //             ]
            //         );
            //         $videoPath = $uploadCloudinary->getSecurePath();
            //     } catch (\Exception $e) {
            //         // Handle upload error
            //         return response()->json([
            //             'status' => false,
            //             'message' => 'Video upload failed: ' . $e->getMessage(),
            //         ]);
            //     }
            // } else {
            //     $videoPath = "No Video Uploaded";
            // }

                // Handle document upload
                // if ($request->hasFile('post_pdf_path')) {
                //     $docPath = $request->file('post_pdf_path')->store('public/documents');
                //     $post->post_pdf_path = $docPath;
                // }

                // // Handle song upload
                // if ($request->hasFile('post_song_path')) {
                //     $songPath = $request->file('post_song_path')->store('public/songs');
                //     $post->post_song_path = $songPath;
                // }

                // Save the updated post
               // $post->save();

                // // Send mail if it was successful
                // Mail::to($request->user_email)->send(new ProfileUpdateMail($post));

        //         return response()->json([
        //             "status" => true,
        //             "message" => "Comment Updated Successfully"
        //         ]);
        //     } else {
        //         return response()->json([
        //             "status" => false,
        //             "message" => "Comment Not Found"
        //         ]);
        //     }
        // }

        public function deletecomment(Request $request)
        {
            // Validate the request
            $request->validate([
                'comment_id' => 'required',
                "post_id" => "required|regex:/^@\w+$/",
            ]);

            // Get the comment that has this comment_id
            $commentId = $request->input('comment_id');
            $comment = Comments::find($commentId);

            // Check if the comment exists
            if (!$comment) {
                return response()->json([
                    "status" => false,
                    "message" => "comment not found"
                ]);
            }

            // Find the post by 'post_id'
            $post = Post::where('post_id', $comment->post_id)->first();

            if ($post) {
                // Decrement the comments count
                $post->decrement('comments_count');
                $post->save();
            }

            // Check if the authenticated user is the owner of the comment
            if (Auth::user()->id === $comment->user_id) {
                // Delete the comment
                $comment->delete();

                return response()->json([
                    "status" => true,
                    "message" => "Comment deleted successfully"
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "You are not permitted to delete this comment"
                ]);
            }
        }


        public function readComment($postID)
        {
            // Find the post by post_id
            $post = Post::where('post_id', $postID)->first();

            // Ensure the post exists before proceeding
            if (!$post) {
                // Handle the case where the post is not found
                return response()->json([
                    'status' => false,
                    'message' => 'Post Not Found',
                ], 404);
            }

            // Find the user associated with the post
            $user = User::where('id', $post->user_id)->first();

            // Check if the user exists
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User Not Found',
                ]);
            }

            // Find all comments associated with the post
            $comments = Comments::with('user')
                                ->with('replies.user')
                                ->where('post_id', $post->id)
                                ->where('parent_id', Null)
                                ->orderBy('created_at', 'desc')
                                ->get();

            // Check if comments exist
            if ($comments->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Oops! Not Found',
                ]);
            }

            // Return the comments in a JSON response
            return response()->json([
                'status' => true,
                'message' => 'Comments data',
                'data' => $comments,
            ]);
        }


}