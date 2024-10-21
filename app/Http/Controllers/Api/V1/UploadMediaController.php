<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class UploadMediaController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            if ($request->hasFile('image')) {
                $uploadCloudinary = cloudinary()->upload(
                    $request->file('image')->getRealPath(),
                    [
                        'folder' => 'africtv/media_uploads_blogs_edu_images',
                        'resource_type' => 'auto',
                        'transformation' => [
                            'quality' => 'auto',
                            'fetch_format' => 'auto'
                        ]
                    ]
                );
                $imagePath = $uploadCloudinary->getSecurePath();
                $imageId = $uploadCloudinary->getPublicId();
            }

            return response()->json([
                "status" => true,
                "message" => "Image uploaded", 
                "url" => $imagePath,
                "id" => $imageId
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => 'Image upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function uploadVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|mimes:mp4,avi,mov,wmv,flv',
        ]);

        // Implement actual video duration fetching
        function getVideoDuration($file)
        {
           
            return 0;
        }

        if ($request->hasFile('video')) {
            $duration = getVideoDuration($request->file('video'));

            if ($duration > 7200) { // 7200 seconds = 2 hours
                return response()->json([
                    'status' => false,
                    'message' => 'Video duration should not exceed 2 hours.',
                ]);
            }

            try {
                $uploadCloudinary = cloudinary()->upload(
                    $request->file('video')->getRealPath(),
                    [
                        'folder' => 'africtv/media_uploads_blogs_edu_videos',
                        'resource_type' => 'auto',
                        'transformation' => [
                            'quality' => 'auto',
                            'fetch_format' => 'auto'
                        ]
                    ]
                );
                $videoPath = $uploadCloudinary->getSecurePath();
                $videoId = $uploadCloudinary->getPublicId();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Video upload failed: ' . $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            "status" => true,
            "message" => "Video uploaded", 
            "url" => $videoPath,
            "id" => $videoId
        ], 200);
    }

    public function deleteImage(Request $request)
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

    public function deleteVideo(Request $request)
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
