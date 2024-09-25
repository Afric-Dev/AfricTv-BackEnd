<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Educational;
use App\Models\Eduthought;
use Illuminate\Support\Facades\Auth;

class EduThoughtsController extends Controller
{
        public function eduthoughts(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            "edu_id" => "required|regex:/^@\w+$/",
            "thoughts" => "required",
            // "thoughts_vid_path" => "nullable|mimes:mp4,avi,mov,wmv,flv|max:20480", // Max video file size: 20MB
            // 'thoughts_img_path' => 'array',
            // 'thoughts_img_path.*' => "nullable|image|max:2048",
            "thoughts_link" => "nullable|url", 
        ]);

        // Initialize an array to store image paths
        $imagePaths = [];
        // Check if the request has files under the 'thoughts_img_path' key
        if ($request->hasFile('thoughts_img_path')) {
            foreach ($request->file('thoughts_img_path') as $file) {
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
        if ($request->hasFile('thoughts_vid_path')) {
            // Get the duration of the video
            $duration = getVideoDuration($request->file('thoughts_vid_path'));

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
                    $request->file('thoughts_vid_path')->getRealPath(),
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

        // Find the edu by 'edu_id'
        $edu = Educational::where('edu_id', $request->edu_id)->firstOrFail();

        // Create the comment
        $educational = Eduthought::create([
            "edu_id" => $edu->id,
            "user_id" => Auth::user()->id,
            "thoughts" => $request->thoughts,
            "thoughts_vid_path" => $videoPath,
            "thoughts_img_path" => json_encode($imagePaths), 
            "thoughts_link" => $request->thoughts_link,

        ]);

        // Increment the educational count for the edu
        $edu->increment('thoughts_count');

        return response()->json([
            "status" => true,
            "message" => "Thoughts Uploaded Successfully",
            "comment" => $educational
        ]);
    }
    public function deleteeduthoughts(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'thoughts_id' => 'required',
            "edu_id" => "required|regex:/^@\w+$/",
        ]);

        // Retrieve the thought by its ID
        $thoughts = Eduthought::find($validated['thoughts_id']);

        // Check if the thought exists
        if (!$thoughts) {
            return response()->json([
                "status" => false,
                "message" => "Oops! Not found"
            ], 404);
        }

        // Retrieve the Educational record by 'edu_id'
        $edu = Educational::where('edu_id', $thoughts->edu_id)->first();

        // Check if the Educational record exists
        if ($edu) {
            // Decrement the thoughts_count
            $edu->decrement('thoughts_count');
        } else {
            return response()->json([
                "status" => false,
                "message" => "Educational record not found"
            ], 404);
        }

        // Check if the authenticated user is the owner of the thought
        if (Auth::id() === $thoughts->user_id) {
            // Delete the thought
            $thoughts->delete();

            return response()->json([
                "status" => true,
                "message" => "Deleted successfully"
            ]);
        } else {
            return response()->json([
                "status" => false,
                "message" => "You are not permitted to delete this thought"
            ], 403);
        }
    }


    public function readeduthoughts($eduID)
    {
        // Find the educational thought by edu_id
        $eduThought = Educational::where('edu_id', $eduID)->first();

        // Ensure the educational thought exists before proceeding
        if (!$eduThought) {
            // Handle the case where the educational thought is not found
            return response()->json([
                'status' => false,
                'message' => 'Educational Thought Not Found',
            ], 404);
        }

        // Find all thoughts associated with the edu ID and include user data
        $thoughts = Eduthought::with('user') 
                              ->where('edu_id', $eduThought->id)
                              ->get();

        // Check if thoughts exist
        if ($thoughts->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Oops! Not Found',
            ]);
        }

        // Return the thoughts in a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Thought data',
            'data' => $thoughts,
        ]);
    }



}
