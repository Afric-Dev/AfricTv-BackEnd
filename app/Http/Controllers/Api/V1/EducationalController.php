<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Educational;
use FFMpeg;
use Illuminate\Support\Facades\Auth;

class EducationalController extends Controller
{
         public function educational(Request $request)
        { 
            // Data Validation
            $request->validate([ 
                // "user_id" => "required",
                // "unique_id" => "required",
                'post_vid_path' => 'array',
                'post_vid_path.*' => 'nullable|mimes:mp4,avi,mov,wmv,flv',
                "title" => "required|max:255",
                "edu_views" => "nullable|max:55",
                "description" => "required",
                "links" => "nullable|max:255",
            ]);

            $videoPaths = [];

            // Handle video upload
            if ($request->hasFile('post_vid_path')) {
                foreach ($request->file('post_vid_path') as $file) {
                    // Check video duration
                    $media = FFMpeg::open($file->getPathname());
                    $duration = $media->getDurationInSeconds();

                    if ($duration > 7200) { // 7200 seconds = 2 hours
                        return response()->json([
                            'status' => false,
                            'message' => 'Video duration should not exceed 2 hours.',
                        ]);
                    }

                    // Store the video
                    $path = $file->store('public/eduvideos');
                    $videoPaths[] = str_replace('public/', '', $path);
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
                "unique_id" =>Auth::user()->unique_id,,
                "post_vid_path" => json_encode($videoPaths),
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
} //shshdihdd'2