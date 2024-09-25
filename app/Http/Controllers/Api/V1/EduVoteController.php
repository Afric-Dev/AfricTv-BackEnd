<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Eduvote;
use App\Models\Educational;
use Illuminate\Support\Facades\Auth;

class EduVoteController extends Controller
{
        public function eduvote(Request $request) {
        // Validate the incoming request
        $request->validate([
            "edu_id" => "required|regex:/^@\w+$/", 
            "reaction_type" => "required|max:55",
        ]);

        // Find the edu based on the edu_id format
        $educational = Educational::where('edu_id', $request->edu_id)->firstOrFail();
        if (!$educational) {
            return response()->json([
                "status" => false,
                "message" => "Oops! Not found"
            ]);
        }

        // Check if the user has already liked this edu
        $existingVote = Eduvote::where('user_id', Auth::user()->id)
                              ->where('edu_id', $educational->id)
                              ->first();

        if ($existingVote) {
            return response()->json([
                "status" => false,
                "message" => "You have already voted for this edu.",
            ], 400); 
        }

        // Create the vote/reaction
        $edu = Eduvote::create([
            "user_id" => Auth::user()->id,
            "edu_id" => $educational->id,
            "reaction_type" => $request->reaction_type,
        ]);

        // Increment the likes count
        $educational->increment('vote_count');
        $educational->save();

        return response()->json([
            "status" => true,
            "message" => "Vote Successfully",
            "eduvote" => $edu
        ]);
    }

       public function uneduvote(Request $request)
    {
        $request->validate([
            'vote_id' => 'required'
        ]);

        $voteId = $request->input('vote_id'); 
        $vote = Eduvote::find($voteId);

        if ($vote) {
            // Find the related edu
            $edu = Educational::find($vote->edu_id);

            if ($edu) {
                // Decrement the votes count
                $edu->decrement('vote_count');
                $edu->save();
            }

            // Delete the vote
            $vote->delete();

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

        public function readeduvotes($eduID)
        {
            // Find the educational vote by edu_id
            $eduVote = Educational::where('edu_id', $eduID)->first();

            // Ensure the educational vote exists before proceeding
            if (!$eduVote) {
                // Handle the case where the educational vote is not found
                return response()->json([
                    'status' => false,
                    'message' => 'Educational Vote Not Found',
                ], 404);
            }

            // Find all votes associated with the edu ID and include user data
            $votes = Eduvote::with('user')
                            ->where('edu_id', $eduVote->id)
                            ->get();

            // Check if votes exist
            if ($votes->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Oops! Not Found',
                ]);
            }

            // Return the votes in a JSON response
            return response()->json([
                'status' => true,
                'message' => 'Vote data',
                'data' => $votes,
            ]);
        }

}
