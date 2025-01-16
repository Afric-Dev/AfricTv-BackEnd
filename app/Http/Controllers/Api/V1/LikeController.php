<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Likes;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Subscribtion;
use Illuminate\Http\JsonResponse; 


class LikeController extends Controller
{

    public function like(Request $request): JsonResponse
    {
        // Validate the incoming request
        $request->validate([
            "post_id" => "required|regex:/^@\w+$/", 
            "reaction_type" => "required|max:55",
        ]);

        // Find the post based on the post_id format
        $post = Post::where('post_id', $request->post_id)->firstOrFail();

        if (!$post) {
            return response()->json([
                "status" => false,
                "message" => "Post not found",
            ], 401); 
        }

        // Check if the user has already liked this post
        $existingLike = Likes::where('user_id', Auth::user()->id)
                              ->where('post_id', $post->id)
                              ->first();

        if ($existingLike) {
            return response()->json([
                "status" => false,
                "message" => "You have already voted for this post.",
            ], 400); 
        }

        // Create the like/reaction
        $like = Likes::create([
            "user_id" => Auth::user()->id,
            "post_id" => $post->id,
            "user_email" => Auth::user()->email,
            "reaction_type" => $request->reaction_type,
        ]);

        // Increment the likes count
        $post->increment('likes_count');
        $post->save();
    
        $user = Auth::user();

        //Notification
        $type = "VOTE";
        $title = "VOTE NOTIFICATION";
        $message = "A new vote has been cast by " . $user->name . " Your post is booming!";

        $notification = Notification::create([
            'user_id' => Auth::user()->id,
            'receiver_id' => $post->user_id,
            'post_id' => $post->post_id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);

        return response()->json([
            "status" => true,
            "message" => "Vote Successfully",
            "like" => $like
        ]);
    }


    public function unlike(Request $request): JsonResponse
    {
        $request->validate([
            'like_id' => 'required'
        ]);

        $likeId = $request->input('like_id'); 
        $like = Likes::find($likeId);

        if ($like) {
            // Find the related post
            $post = Post::find($like->post_id);

            if ($post) {
                // Decrement the likes count
                $post->decrement('likes_count');
                $post->save();
            }

            // Delete the like
            $like->delete();

            return response()->json([
                "status" => true,
                "message" => "Vote deleted successfully"
            ]);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Vote not found"
            ]);
        }
    }

    public function readlikes($postID): JsonResponse
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

        // Find all likes associated with the post ID
        $likes = Likes::with('user')
                      ->where('post_id', $post->id)
                      ->get();

        // Check if likes exist
        if ($likes->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Oops! Not Found',
            ]);
        }

        // Return the likes in a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Like data',
            'data' => $likes,
        ]);
    }

}
