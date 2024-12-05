<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favourite;
use App\Models\Educational;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class FavouriteController extends Controller
{
    public function favourite(Request $request): JsonResponse
    {
        // Validate the incoming request
        $request->validate([
            "edu_id" => "required|regex:/^@\w+$/", 
        ]);

        // Find the educational record by edu_id
        $edu = Educational::where('edu_id', $request->edu_id)->firstOrFail();

        // Check if the favourite already exists
        $favouriteBefore = Favourite::where('user_id', Auth::id())
            ->where('edu_id', $edu->id) // Match with the edu's numeric ID
            ->first();

        if ($favouriteBefore) {
            return response()->json([
                "status" => false,
                "message" => "You've added this post to favourite before",
            ]);
        }

        // Create the Favourite
        $favourite = Favourite::create([
            "user_id" => Auth::id(),
            "edu_id" => $edu->id,
        ]);

        // Increment the favourites count
        $edu->increment('favourites_count');

        return response()->json([
            "status" => true,
            "message" => "Added to Favourite Successfully",
            "favourite" => $favourite
        ]);
    }

    public function deletefavourite(Request $request): JsonResponse
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
                $edu->decrement('favourites_count');
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

    public function readfavourites(Request $request): JsonResponse
    {
        $userId = Auth::user()->id;

        // Retrieve all favourites with related educational data
        $favourites = Favourite::with('educational')
                        ->where('user_id', $userId)
                        ->get();

        // Debugging: Check if educational data is being loaded
        foreach ($favourites as $favourite) {
            if (!$favourite->educational) {
                return response()->json([
                    'status' => false,
                    'message' => 'This video might have been deleted or moved',
                ]);
            }
        }

        // Return the favourites with educational data in a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Favourite data',
            'data' => $favourites,
        ]);
    }

    public function readfavouritespost($postID): JsonResponse
    {
        // Find the post by post_id
        $post = Educational::where('edu_id', $postID)->first();

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Video Post Not Found',
            ], 404);
        }

        // Retrieve favourites associated with the educational post
        $favourites = Favourite::with('user')
                        ->where('edu_id', $post->id) 
                        ->get();

        if ($favourites->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Oops! Empty',
            ]);
        }

        // Return the favourites
        return response()->json([
            'status' => true,
            'message' => 'Favourite data',
            'data' => $favourites,
        ]);
    }

}
