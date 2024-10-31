<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\JsonResponse;  

class UploadMediaController extends Controller
{
        public function uploadImages(Request $request): JsonResponse
        {
            $request->validate([
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            try {
                $uploadedImages = [];

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $uploadCloudinary = cloudinary()->upload(
                            $image->getRealPath(),
                            [
                                'folder' => 'africtv/media_uploads_blogs_edu_images',
                                'resource_type' => 'auto',
                                'transformation' => [
                                    'quality' => 'auto',
                                    'fetch_format' => 'auto'
                                ]
                            ]
                        );

                        // Store the image path and ID in an array
                        $uploadedImages[] = [
                            'url' => $uploadCloudinary->getSecurePath(),
                            'id' => $uploadCloudinary->getPublicId()
                        ];
                    }
                }

                return response()->json([
                    "status" => true,
                    "message" => "Images uploaded", 
                    "images" => $uploadedImages
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    "status" => false,
                    "message" => 'Image upload failed: ' . $e->getMessage(),
                ], 500);
            }
        }

    public function uploadVideos(Request $request): JsonResponse
    {
        $request->validate([
            'videos.*' => 'required|mimes:mp4,avi,mov,wmv,flv',
        ]);

        
        function getVideoDuration($file)
        {
            return 600;
        }

        try {
            $uploadedVideos = [];

            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $video) {
                    $duration = getVideoDuration($video);

                    if ($duration > 7200) { // 7200 seconds = 2 hours
                        return response()->json([
                            'status' => false,
                            'message' => 'Video duration should not exceed 2 hours for video: ' . $video->getClientOriginalName(),
                        ]);
                    }

                    $uploadCloudinary = cloudinary()->upload(
                        $video->getRealPath(),
                        [
                            'folder' => 'africtv/media_uploads_blogs_edu_videos',
                            'resource_type' => 'video',
                            'transformation' => [
                                'quality' => 'auto',
                                'fetch_format' => 'auto'
                            ]
                        ]
                    );

                    // Store the video path and ID in an array
                    $uploadedVideos[] = [
                        'url' => $uploadCloudinary->getSecurePath(),
                        'id' => $uploadCloudinary->getPublicId()
                    ];
                }
            }

            return response()->json([
                "status" => true,
                "message" => "Videos uploaded", 
                "videos" => $uploadedVideos
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => 'Video upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deleteImage(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required'
        ]);

        if ($request->id) {
            Cloudinary::destroy($request->id);
        }

        return response()->json([
            "status" => true,
            "message" => "Image deleted"
        ]);
    }

    public function deleteVideo(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required'
        ]);

        if ($request->id) {
            $response = Cloudinary::destroy($request->id, ['resource_type' => 'video']);

            if ($response['result'] !== 'ok') {
                return response()->json([
                    "status" => false,
                    "message" => "Failed to delete video"
                ]);
            }
        }

        return response()->json([
            "status" => true,
            "message" => "Video deleted"
        ]);
    }

}
