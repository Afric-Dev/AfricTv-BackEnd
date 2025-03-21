<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Cloudinary\Api\Admin\AdminApi;

use Illuminate\Http\JsonResponse; 
use App\Models\Educational;

class ReelsController extends Controller
{
    public function getReels()
    {
        $videos = Educational::where('is_status', 'ACTIVE')
            ->with('user')
            ->get();

        $adminApi = new AdminApi();
        $filteredVideos = [];

        foreach ($videos as $video) {
            try {
                if (empty($video->eduvideoId)) {
                    \Log::warning("Missing eduvideoId for EDU {$video->edu_id}");
                    continue;
                }

                // Get Cloudinary asset and convert to array
                $asset = $adminApi->asset($video->eduvideoId, [
                    'resource_type' => 'video',
                    'image_metadata' => true
                ])->getArrayCopy(); // Convert response to array

                // Proper logging with array conversion
                \Log::debug('Cloudinary Response for ' . $video->eduvideoId, (array)$asset);

                // Extract duration safely
                $duration = $this->extractDuration($asset);

                if ($duration !== null && $duration <= 90) {
                    $filteredVideos[] = $this->formatVideoResponse($video, $duration);
                }

            } catch (\Exception $e) {
                \Log::error("Cloudinary error for {$video->eduvideoId}: " . $e->getMessage());
            }
        }

        shuffle($filteredVideos);

        return response()->json([
            'status' => 'success',
            'reels' => $filteredVideos,
            'processed' => $videos->count(),
            'filtered' => count($filteredVideos)
        ]);
    }

    private function extractDuration(array $asset): ?float
    {
        try {
            // Handle nested duration formats
            if (isset($asset['duration'])) {
                return floatval($asset['duration']);
            }
            
            // Some Cloudinary versions nest duration in 'video' metadata
            if (isset($asset['video_metadata']['duration'])) {
                return floatval($asset['video_metadata']['duration']);
            }

            \Log::warning("Duration missing in Cloudinary response", ['asset' => $asset]);
            return null;
        } catch (\Exception $e) {
            \Log::error("Duration extraction failed: " . $e->getMessage());
            return null;
        }
    }

    private function formatVideoResponse($video, float $duration): array
    {
        return array_merge($video->toArray(), [
            'duration' => $duration,
            'user' => $video->user, // Assuming user relationship is loaded
            'thumbnail' => $video->edu_vid_path
        ]);
    }

}
