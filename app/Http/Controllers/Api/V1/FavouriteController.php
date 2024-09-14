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
}
