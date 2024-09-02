<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Educational;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Cloudinary\Transformation\Video;
use Cloudinary\Transformation\Image;
use Illuminate\Support\Facades\Log;


class EducationalController extends Controller
{
         public function educational(Request $request)
        {
            // Data Validation
            $request->validate([
                'edu_vid_path' => 'required|mimes:mp4,avi,mov,wmv,flv',
                'title' => 'required|max:255',
                'description' => 'required',
                'links' => 'nullable|max:255',
            ]);

            $user_name = Auth::user()->name;
            $firstWord = strtok($user_name, ' ');
            // Generate a random four-digit number
            $randomNumber = rand(100000, 999999);

            $eduID = '@' .$firstWord . $randomNumber;

            // Function to get video duration
            function getVideoDuration($file)
            {
                return 0; 
            }

            // Upload watermark image and get public ID
            $watermarkImage = cloudinary()->upload('https://res.cloudinary.com/dxbft8aci/image/upload/v1724838891/AfricTv_2_ov7mlc.png', [
                'folder' => 'africtv/watermarks',
                'resource_type' => 'image'
            ])->getPublicId();

            $videoPath = [];
            if ($request->hasFile('edu_vid_path')) {
                $files = $request->file('edu_vid_path');

            $transformations = [
                'overlay' => [
                    'public_id' => $watermarkImage,
                    'transformation' => [
                        'width' => 100,
                        'height' => 100,
                        'crop' => 'fit',
                        'opacity' => 30
                    ]
                ],
               'gravity' => 'north_west',
                'x' => 0.07,
                'y' => 0.01
            ];
                    $file = $files;
                    if ($file->isValid()) {
                        $duration = getVideoDuration($file);
                        if ($duration > 7200) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Video duration should not exceed 2 hours.',
                            ]);
                        }

                        try {
                            $uploadCloudinary = cloudinary()->upload($file->getRealPath(), [
                                'folder' => 'africtv/edu_videos',
                                'resource_type' => 'video',
                                'transformation' => array_merge($transformations, [
                                    'quality' => 'auto',
                                    'fetch_format' => 'auto'
                                ])
                            ]);
                            $videoPath = $uploadCloudinary->getSecurePath();
                            $eduvideoId = $uploadCloudinary->getPublicId();
                        } catch (\Exception $e) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Video upload failed: ' . $e->getMessage(),
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Invalid file upload.',
                        ]);
                    }
                
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Video must be uploaded.'
                ]);
            }

            // Storing educational data
            $edu = Educational::create([
                'edu_id' => $eduID,
                'user_id' => Auth::user()->id,
                'unique_id' => Auth::user()->unique_id,
                'edu_vid_path' => $videoPath,
                'eduvideoId' => $eduvideoId,
                'title' => $request->title,
                'edu_views' => $request->edu_views ?? 0,
                'vote_count' => $request->vote_count ?? 0,
                'thoughts_count' => $request->thoughts_count ?? 0,
                'favourites_count' => $request->favourites_count ?? 0,
                'links' => $request->links,
                'description' => $request->description,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Educational Post Uploaded Successfully'
            ]);
        }


      public function deleteedupost(Request $request)
        {
            // Validate the request
            $request->validate([
                'edu_id' => 'required|regex:/^@\w+$/'
            ]);

            // Get the edu that has this id
            $eduId = $request->input('edu_id');
            $edu = Educational::where('edu_id', $eduId)->first();

            // Check if the educational post exists
            if (!$edu) {
                return response()->json([
                    "status" => false,
                    "message" => "Oops! Not found"
                ]);
            }

            // Check if the authenticated user is the owner of the edu post
            if (Auth::check() && Auth::user()->id === $edu->user_id) {
                // Delete the edu media
                
            if ($edu->eduvideoId) {
                // Attempt to delete the video from Cloudinary
                $response = Cloudinary::destroy($edu->eduvideoId, ['resource_type' => 'video']);
                
                // Log the full response for debugging
                Log::info('Cloudinary Deletion Response:', ['response' => $response]);

                // Check if the deletion was successful
                if ($response['result'] !== 'ok') {
                    return response()->json([
                        "status" => false,
                        "message" => "Failed to delete video from Cloudinary",
                        "cloudinary_response" => $response // Include the full response for debugging
                    ]);
                }
            }

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




            public function readspecificedupost($uniqid, $title) 
            {
                // Retrieve the edu with the given user uniqid and title 
                $edu = Educational::with('user')
                        ->where('title', $title)
                        ->where('unique_id', $uniqid)
                        ->first();


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

        public function eduviews(Request $request)
        {
            // Validate the incoming request
            $validatedData = $request->validate([
                'edu_viewed' => 'required|boolean',
                'edu_id' => 'required|regex:/^@\w+$/'
            ]);

            // Get the edu based on the validated edu_id
            $edu = Educational::where('edu_id', $validatedData['edu_id'])->first();
            
            // Check if the edu exists
            if (!$edu) {
                return response()->json([
                    'status' => false,
                    'message' => 'Edu Not Found',
                ]);
            }

            // If view is true, increment the views
            if ($validatedData['edu_viewed']) {
                $edu->edu_views += 1;
                $edu->save();
            }

            return response()->json([
                'status' => true,
                'message' => 'Edu view updated successfully',
                'edu' => $edu
            ]);
        }

            public function readedu()
            {
                // Retrieve educational records with user data
                $edus = Educational::with('user')
                    ->orderBy('created_at', 'desc')  
                    ->orderBy('edu_views', 'desc')       
                    ->get();
                $eduCount = $edus->count();

                return response()->json([
                    'status' => true,
                    'message' => 'Educational data',
                    'data' => $edus,
                    'count' => $eduCount,
                ]);
            }
} 