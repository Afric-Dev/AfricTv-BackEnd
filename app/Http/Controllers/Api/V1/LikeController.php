<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Likes;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
     public function like(Request $request) {
        $request->validate([
            // "user_id" => "required",
            // "user_email" => "required",
            "post_id" => "required",
            "post_email" => "required",
            "reaction_type" => "required|max:55",
        ]);

        $likes = Likes::create([
            "user_id" => Auth::user()->id,
            "user_email" => Auth::user()->email,
            "post_id" => $request->post_id,
            "post_email" => $request->user_email,
            "reaction_type" => $request->user_name,
        ]);

        return response()->json([
            "status" => true,
            "message" => "Liked Successfully"
        ]);
    }

      public function unlike(Request $request)
        {
            $request->validate([
                'like_id' => 'required|integer'
            ]);

            $likeId = $request->input('like_id'); 
            $like = Likes::find($likeId);

            if ($like) {
                $like->delete();

                return response()->json([
                    "status" => true,
                    "message" => "Comment deleted successfully"
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Comment not found"
                ]);
            }
        }

        public function readlikes(Request $request)
        {
            $validated = $request->validate([
                'post_id' => 'required|integer',
            ]);

            $postId = $validated['post_id'];

            // Find all like associated with the post ID
            $like = likes::where('post_id', $postId)->get();

            // Return the like in a JSON response
            return response()->json([
                'status' => true,
                'message' => 'like data',
                'data' => $like,
            ]);
        }
}
