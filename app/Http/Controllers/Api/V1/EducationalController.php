<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Educational;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

use Cloudinary\Transformation\Overlay;
use Cloudinary\Transformation\Resize;
use Cloudinary\Transformation\Adjust;
use Cloudinary\Transformation\Source;
use Cloudinary\Transformation\Position;
use Cloudinary\Transformation\Gravity;
use Cloudinary\Transformation\Compass;


class EducationalController extends Controller
{
         public function educational(Request $request)
        {
            // Data Validation
            $request->validate([
                'edu_vid_path' => 'array',
                'edu_vid_path.*' => 'required|mimes:mp4,avi,mov,wmv,flv',
                'title' => 'required|max:255',
                'edu_views' => 'nullable|max:55',
                'description' => 'required',
                'links' => 'nullable|max:255',
            ]);

            // Function to get video duration
            function getVideoDuration($file)
            {
                return 0; 
            }

            // Upload watermark image and get public ID
            $watermarkImage = cloudinary()->upload('https://res.cloudinary.com/dxbft8aci/image/upload/v1722239225/africtv/logos/fhccinkqednbtd2rc5ay.png', [
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
                            'crop' => 'scale',
                            'opacity' => 10
                        ]
                    ],
                    'gravity' => 'south_east',
                    'x' => 15,
                    'y' => 20
                ];

                if (is_array($files)) {
                    foreach ($files as $file) {
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
                                $videoPath[] = $uploadCloudinary->getSecurePath();
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
                    }
                } else {
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
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Video must be uploaded.'
                ]);
            }

            // Storing educational data
            $edu = Educational::create([
                'user_id' => Auth::user()->id,
                'unique_id' => Auth::user()->unique_id,
                'edu_vid_path' => json_encode($videoPath),
                'title' => $request->title,
                'edu_views' => $request->edu_views ?? 0,
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