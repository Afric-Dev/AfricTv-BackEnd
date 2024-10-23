<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favourite;
use App\Models\Educational;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    public function favourite(Request $request) {
        // Validate the incoming request
        $request->validate([
            "edu_id" => "required|regex:/^@\w+$/", 
        ]);

        // Find the edu based on the edu_id format
        $edu = Educational::where('edu_id', $request->edu_id)->firstOrFail();
        $favouriteBefore = Favourite::where('user_id', Auth::user()->id)
        ->where('edu_id', $request->edu_id)
        ->firstOrFail();

        if($favouriteBefore) {
            return response()->json([
                "status" => false,
                "message" => "You've added this post to favourite before",
               ]);
        }
        // Create the Bookmark
        $favourite = Favourite::Create(
            [
                "user_id" => Auth::user()->id,
                "edu_id" => $edu->id,
            ],
        );

        // Increment the bookmarks count
        $edu->increment('favourites_count');
        $edu->save();

        return response()->json([
            "status" => true,
            "message" => "Added to Favourite Successfully",
            "favourite" => $favourite
        ]);
    }

    public function deletefavourite(Request $request)
    {
        $request->validate([
            'fav_id' => 'required'
        ]);

        $favId = $request->input('fav_id'); 
        $fav = Favourite::find($favId);

        if ($fav) {
            // Find the related post
            $edu = Educational::find($fav->edu_id);

            if ($edu) {
                // Decrement the likes count
                $edu->decrement('favmark_count');
                $edu->save();
            }

            // Delete the fav
            $fav->delete();

            return response()->json([
                "status" => true,
                "message" => "Favourite removed successfully"
            ]);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Favourite not found"
            ]);
        }
    }

    public function readfavourites(Request $request)
    {
        $userId = Auth::user()->id;

        // Find all favourites associated with the educational ID
        $favourites = Favourite::with('educational')
                    ->where('user_id', $userId)
                    ->get();

        // Return the favourites in a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Favourite data',
            'data' => $favourites,
        ]);
    }
    public function readfavouritespost($postID)
    {
        // Find the post by post_id
        $post = Educational::where('edu_id', $postID)->first();

        // Ensure the post exists before proceeding
        if (!$post) {
            // Handle the case where the post is not found
            return response()->json([
                'status' => false,
                'message' => 'Post Not Found',
            ], 404);
        }

        // Find all favourites associated with the post ID
        $favourites = Favourite::with('user')
                      ->where('edu_id', $post->id)
                      ->get();

        // Check if favourites exist
        if ($favourites->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Oops! Empty',
            ]);
        }

        // Return the favourites in a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Like data',
            'data' => $favourites,
        ]);
    }
}
