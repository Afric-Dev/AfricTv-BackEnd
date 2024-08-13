<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\Ads;
use App\Models\AdsPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class AdsController extends Controller
{
      public function adActive(Request $request)
      {
        $validatedData = $request->validate([
            "img_path" => 'array',
            "img_path*" => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            "vid_path" => 'nullable|mimes:mp4,avi,mov,wmv,flv',
            "title" => 'required|string|max:255',
            "description" => 'required|string',
            "link" => 'required|string|max:255',
            "ads_type" => 'required|string|max:25'
        ]);


        $imagePath = [];
        // Check if the request has files under the 'post_img_path' key
        if ($request->hasFile('img_path')) {
            foreach ($request->file('img_path') as $file) {
                // Validate if the file is valid
                if ($file->isValid()) {
                    // Upload the file to Cloudinary
                    $uploadCloudinary = cloudinary()->upload(
                        $file->getRealPath(),
                        [
                            'folder' => 'africtv/ad_img',
                            'resource_type' => 'auto',
                            'transformation' => [
                                'quality' => 'auto',
                                'fetch_format' => 'auto'
                            ]
                        ]
                    );

                    // Store the secure URL of the uploaded image
                    $imagePath[] = $uploadCloudinary->getSecurePath();
                } else {
                    $imagePath[] = "File is not valid";
                }
            }
        } else {
            $imagePath[] = "No Image Uploaded";
        }


        // Function to get video duration
        function getVideoDuration($file)
        {
            return 0; 
        }

        if ($request->hasFile('vid_path')) {
            // Get the duration of the video
            $duration = getVideoDuration($request->file('vid_path'));

            // Validate video duration
            if ($duration > 7200) { // 7200 seconds = 2 hours
                return response()->json([
                    'status' => false,
                    'message' => 'Video duration should not exceed 2 hours.',
                ]);
            }

            try {
                $uploadCloudinary = cloudinary()->upload(
                    $request->file('vid_path')->getRealPath(),
                    [
                        'folder' => 'africtv/ad_videos',
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
            $videoPath = "No Video Uploaded";
        }

        $userId = Auth::user()->id;
        $adPayment = AdsPayment::where('user_id', $userId)
                              ->where('taken', 'NO')
                              ->first();
        
        if (!$adPayment) {
            // No ad payment found or already used
            if (AdsPayment::where('user_id', $userId)->exists()) {
                // Payment exists but is already used
                return response()->json([
                    'status' => false,
                    'message' => 'You dont have any ads payment left.',
                ]);
            
            } else {
                // No ad payment record found
                return response()->json([
                    'status' => false,
                    'message' => 'You have to pay for ads first.',
                ]);
            }
        }

        $status = 'ACTIVE';
        $clicks = $adPayment->clicks;
        $ads_id = $adPayment->ads_id;

        // Update the ad payment record to mark as used
        $adPayment->taken = 'YES';
        $adPayment->save();

        // Storing ads data
        $ads = Ads::create([
            "user_id" => Auth::user()->id,
            "user_email" => Auth::user()->email,
            "img_path" => json_encode($imagePath),
            "vid_path" => $videoPath,
            "title" => $validatedData['title'],
            "description" => $validatedData['description'],
            "link" => $validatedData['link'],
            "status" => $status,
            "clicks" => $clicks,
            "ads_id" => $ads_id,
            "start_date" => Carbon::now(),
            "ads_type" => $validatedData['ads_type']
        ]);

        return response()->json([
            "status" => true,
            "message" => "Ads Uploaded Successfully",
            'data' => $ads,
        ]);


      }


    //not done with this work
    public function adInactive(Request $request)
    {
        // Fetch ads with zero clicks and status 'YES'
        $ads = Ads::where('status', 'ACTIVE')
                  ->where('clicks', 0)
                  ->get();

        // Check if there are any ads to update
        if ($ads->isNotEmpty()) {
            // Loop through each ad and update its status to 'INACTIVE'
            foreach ($ads as $ad) {
                $ad->status = 'INACTIVE';
                $ad->save();
            }

            return response()->json([
                'message' => 'Ads with zero clicks have been deactivated.'
            ]);
        } else {
            return response()->json([
                'message' => 'No ads found with zero clicks.'
            ]);
        }
    }


    public function AdsPerClicks(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'clicked' => 'required|boolean', //clicked is a boolean flag
            'ads_id' => 'required|integer|exists:ads,id' 
        ]);

        // Get the ad based on the validated ads_id
        $ad = Ads::find($validatedData['ads_id']);
        
        // Check if the ad exists
        if (!$ad) {
            return response()->json([
                'status' => false,
                'message' => 'Ad Not Found',
            ]);
        }

        // If clicked is true, decrement the clicks
        if ($validatedData['clicked']) {
            $ad->clicks -= 1;
            $ad->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Ad clicks updated successfully',
            'ad' => $ad
        ]);
    }
    public function ads(Request $request)
    {
         $ads = Ads::inRandomOrder()
         ->where('status', 'ACTIVE')
         ->get();

            // Check for ads 
            if (!$ads) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ad Not Found',
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Ads data',
                'data' => $ads,
            ]);
    }

    public function deleteads(Request $request)
    {
        // Validate the request
        $request->validate([
            'id' => 'required|integer'
        ]);

        // Get the ad with this id
        $adsId = $request->input('id');
        $ad = Ads::find($adsId);

        // Check if the ad exists
        if (!$ad) {
            return response()->json([
                "status" => false,
                "message" => "Ad not found"
            ]);
        }

        // Check if the authenticated user is the owner of the ad
        if (Auth::user()->id === $ad->user_id) {
            // Delete the ad
            $ad->delete();

            return response()->json([
                "status" => true,
                "message" => "Ad deleted successfully"
            ]);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Unauthorized action"
            ]);
        }
    }


}
