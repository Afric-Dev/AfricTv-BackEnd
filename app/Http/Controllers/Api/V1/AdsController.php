<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\Ads;
use App\Models\User;
use App\Models\AdsPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Mail\AdInactiveNotification;
use Illuminate\Support\Facades\Mail;

class AdsController extends Controller
{
      public function adActive(Request $request)
      {
        $validatedData = $request->validate([
            "img_path" => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            "vid_path" => 'nullable|mimes:mp4,avi,mov,wmv,flv',
            "title" => 'required|string|max:255',
            "description" => 'required|string',
            "link" => 'required|string|max:255',
            "ads_type" => 'required|in:PIC,VID,LINK'
        ]);


        if ($request->hasFile('img_path')) {
            $uploadCloudinary = cloudinary()->upload(
                $request->file('img_path')->getRealPath(),
                [
                    'folder' => 'africtv/ad_image',
                    'resource_type' => 'auto',
                    'transformation' => [
                        'quality' => 'auto',
                        'fetch_format' => 'auto'
                    ]
                ]
            );
            $imageUrl = $uploadCloudinary->getSecurePath();
            $imageId = $uploadCloudinary->getPublicId();
        } else {
            $imageUrl = "No File Uploaded";
            $imageId = Null;
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
                $videoId = $uploadCloudinary->getPublicId();
            } catch (\Exception $e) {
                // Handle upload error
                return response()->json([
                    'status' => false,
                    'message' => 'Video upload failed: ' . $e->getMessage(),
                ]);
            }
        } else {
            $videoPath = "No Video Uploaded";
            $videoId = Null;
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
            "img_path" => $imageUrl,
            "imageId" => $imageId,
            "vid_path" => $videoPath,
            "videoId" => $videoId,
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
        // Fetch ads with zero clicks and status 'ACTIVE'
        $ads = Ads::where('status', 'ACTIVE')
                  ->where('clicks', 0)
                  ->get();

        // Check if there are any ads to update
        if ($ads->isNotEmpty()) {
            // Loop through each ad and update its status to 'INACTIVE'
            foreach ($ads as $ad) {
                $ad->status = 'INACTIVE';
                $ad->save();

                //user relation in the Ads model to get the owner
                $owner = $ad->user; 
                
                // Send email notification to the owner
                if ($owner && $owner->email) {
                    Mail::to($owner->email)->send(new AdInactiveNotification($ad));
                }
            }
            
            return response()->json([
                'message' => 'Ads with zero clicks have been deactivated, and notifications have been sent to the owners.'
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
            'clicked' => 'required|boolean',
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
           'id' => 'required|exists:ads,id',
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
                    // Check if the authenticated user is the owner of the edu post
                    if (Auth::check() && Auth::user()->id === $ad->user_id) {
                        // Delete the edu media
                        
                    if ($ad->videoId) {
                        // Attempt to delete the video from Cloudinary
                        $response = Cloudinary::destroy($ad->videoId, ['resource_type' => 'video']);
                        
                        // Log the full response for debugging
                        // Log::info('Cloudinary Deletion Response:', ['response' => $response]);
        
                        // Check if the deletion was successful
                        if ($response['result'] !== 'ok') {
                            return response()->json([
                                "status" => false,
                                "message" => "Failed to delete video from Cloudinary",
                                "cloudinary_response" => $response 
                            ]);
                        }
                    }
                    
                    if ($ad->imageId) {
                        Cloudinary::destroy($ad->imageId);
                    }

                        // Delete the edu post
                        $ad->delete();
        
                        return response()->json([
                            "status" => true,
                            "message" => "Deleted successfully"
                        ]);
                    } else {
                        return response()->json([
                            "status" => false,
                            "message" => "You are not permitted to delete this ad post"
                        ]);
                    }
    }
}
