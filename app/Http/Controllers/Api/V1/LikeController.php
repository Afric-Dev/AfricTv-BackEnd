<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Likes;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{

    public function like(Request $request) {
        // Validate the incoming request
        $request->validate([
            "post_id" => "required|regex:/^@\w+$/", 
            "reaction_type" => "required|max:55",
        ]);

        // Find the post based on the post_id format
        $post = Post::where('post_id', $request->post_id)->firstOrFail();

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

        return response()->json([
            "status" => true,
            "message" => "Vote Successfully",
            "like" => $like
        ]);
    }


    public function unlike(Request $request)
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

    public function readlikes($postID)
    {
        // Find all likes associated with the post ID
        $likes = Likes::with('user')
                    ->where('post_id', $postID)
                    ->get();

        // Check if likes exist
        if (!$likes->isEmpty()) {
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
