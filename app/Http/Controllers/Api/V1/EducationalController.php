<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Educational;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Illuminate\Support\Facades\Auth;

class EducationalController extends Controller
{
         public function educational(Request $request)
            {
                // Data Validation
                $request->validate([
                    'edu_vid_path' => 'array',
                    'edu_vid_path.*' => 'nullable|mimes:mp4,avi,mov,wmv,flv',
                    "title" => "required|max:255",
                    "edu_views" => "nullable|max:55",
                    "description" => "required",
                    "links" => "nullable|max:255",
                ]);

                $videoPaths = [];

                if ($request->hasFile('edu_vid_path')) {
                foreach ($request->file('edu_vid_path') as $file) {
                    // Ensure the file is a valid upload
                    if ($file->isValid()) {
                        // Store the temporary file path
                        $tempPath = $file->getPathname(); 

                        // Create an FFProbe instance
                        $ffprobe = FFProbe::create();

                        // Get the duration of the video
                        $duration = $ffprobe
                            ->format($tempPath) // extracts file informations
                            ->get('duration');

                        if ($duration > 7200) { // 7200 seconds = 2 hours
                            return response()->json([
                                'status' => false,
                                'message' => 'Video duration should not exceed 2 hours.',
                            ]);
                        }

                        // Store the video
                        $path = $file->store('public/eduvideos');
                        $videoPaths[] = str_replace('public/', '', $path);
                    } else {
                        return response()->json([
                            "status" => false,
                            "message" => "Invalid video file."
                        ]);
                    }
                }
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Video Must Be Uploaded"
                ]);
            }


                // Storing educational data
                $edu = Educational::create([
                    "user_id" => Auth::user()->id,
                    "unique_id" => Auth::user()->unique_id,
                    "edu_vid_path" => json_encode($videoPaths),
                    "title" => $request->title,
                    "edu_views" => $request->edu_views ?? 0,
                    "links" => $request->links,
                    "description" => $request->description,
                ]);

                return response()->json([
                    "status" => true,
                    "message" => "Educational Post Uploaded Successfully"
                ]);
            }


             public function deleteedupost(Request $request)
            {
                // Validate the request
                $request->validate([
                    'id' => 'required|integer'
                ]);

                // Get the edu that has this id
                $eduId = $request->input('id');
                $edu = Educational::find($eduId);

                // Check if the educational exists
                if (!$edu) {
                    return response()->json([
                        "status" => false,
                        "message" => "Oops! Not found"
                    ]);
                }

                // Check if the authenticated user is the owner of the edu post
                if (Auth::user()->id === $edu->user_id) {
                    // Delete the edu post
                    $edu->delete();

                    return response()->json([
                        "status" => true,
                        "message" => "Deleted successfully"
                    ]);
                } else {
                    return response()->json([
                        "status" => false,
                        "message" => "You are not permitted to delete this educational post"
                    ]);
                }
            }

            public function readspecificedupost($title) 
            {
                // Retrieve the edu with the given title
                $edu = Educational::where('title', $title)->first();

                // Check if post exists
                if (!$edu) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Edu Not Found',
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Edu data',
                    'data' => $edu,
                ]);
            }

            public function readedu()
            {
                $edus = Educational::inRandomOrder()->get();
                $eduCount = $edus->count();

                return response()->json([
                    'status' => true,
                    'message' => 'Educational data',
                    'data' => $edus,
                    'count' => $eduCount,
                ]);
            }
} 