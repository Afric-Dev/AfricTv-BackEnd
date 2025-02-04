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
use Illuminate\Http\JsonResponse;
 
class AdsController extends Controller
{
      public function adActive(Request $request): JsonResponse
      {
        $validatedData = $request->validate([
            "img_path" => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            "vid_path" => 'nullable|mimes:mp4,avi,mov,wmv,flv',
            "title" => 'required|string|max:255',
            "description" => 'required|string',
            "link" => 'required|string|max:255',
            "ads_id" => 'nullable'
        ]);

        $userId = Auth::user()->id;

        if (isset($validatedData['ads_id']) && $validatedData['ads_id']) {
            $adPayment = AdsPayment::where('ads_id', $validatedData['ads_id'])
                                  ->where('taken', 'NO')
                                  ->first();
        } else {
            $adPayment = AdsPayment::where('user_id', $userId)
                                  ->where('taken', 'NO')
                                  ->first();
        }

        if (!$adPayment) {
            return response()->json([
                'status' => false,
                'message' => 'You have paid for no ad! something seems wrong',
            ]);  
        }                      

        if ($adPayment->status === "PENDING") {
            return response()->json([
                'status' => false,
                'message' => 'Your ads payment is still PENDING.',
            ]);
        } 

        if ($adPayment->status === "FAILED") {
            return response()->json([
                'status' => false,
                'message' => 'Your ads payment FAILED.',
            ]);
        }

        if($adPayment->ads_type !== 'VID' && !empty($validatedData['vid_path'])) {
            return response()->json([
                'status' => false,
                'message' => 'Sorry! You did not pay for this type of ad. The  ads type you paid for  was: ' . $adPayment->ads_type,
            ], 422);
        } 
        if ($adPayment->ads_type !== 'PIC' && !empty($validatedData['img_path'])) {
            return response()->json([
                'status' => false,
                'message' => 'Sorry! You did not pay for this type of ad. The  ads type you paid for  was: ' . $adPayment->ads_type,
            ], 422);
        }
        if ($adPayment->ads_type == 'PIC' && empty($validatedData['img_path'])) {
            return response()->json([
                'status' => false,
                'message' => 'Image Field is required',
            ], 422);
        }
        if ($adPayment->ads_type == 'VID' && empty($validatedData['vid_path'])) {
            return response()->json([
                'status' => false,
                'message' => 'Video Field is required',
            ], 422);
        }

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


        
        if (!$adPayment) {
            // No ad payment found or already used
            if (AdsPayment::where('user_id', $userId)->exists()) {
                // Payment exists but is already used
                return response()->json([
                    'status' => false,
                    'message' => 'You dont have any ad payment left.',
                ]);
            
            } else {
                // No ad payment record found
                return response()->json([
                    'status' => false,
                    'message' => 'You have to pay for ad first.',
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
            "ads_type" => $adPayment->ads_type,
            "is_ads_type_sec" => $adPayment->is_ads_type_sec,
        ]);

        return response()->json([
            "status" => true,
            "message" => "Ad Uploaded Successfully",
            'data' => $ads,
        ]);

      }

    public function updateAd(Request $request, $adId): JsonResponse
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            "img_path" => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            "vid_path" => 'nullable|mimes:mp4,avi,mov,wmv,flv',
            "title" => 'required|string|max:255',
            "description" => 'required|string',
            "link" => 'required|string|max:255',
        ]);

        // Fetch the ad to be updated
        $ads = Ads::find($adId);
        if (!$ads) {
            return response()->json([
                'status' => false,
                'message' => 'Ad not found',
            ], 404);
        }
        $userId = Auth::user()->id;
        $adTaken = Ads::where('id' , $adId)
                  ->where('status', 'ACTIVE')
                  ->first();

        if($adTaken->ads_type !== 'VID' && !empty($validatedData['vid_path'])) {
            return response()->json([
                'status' => false,
                'message' => 'Sorry! You did not pay for this type of ads. The  ads type you paid for  was: ' . $adTaken->ads_type,
            ], 422);
        } 
        if ($adTaken->ads_type !== 'PIC' && !empty($validatedData['img_path'])) {
            return response()->json([
                'status' => false,
                'message' => 'Sorry! You did not pay for this type of ads. The  ads type you paid for  was: ' . $adTaken->ads_type,
            ], 422);
        }
        if ($adTaken->ads_type == 'PIC' && empty($validatedData['img_path'])) {
            return response()->json([
                'status' => false,
                'message' => 'Image Field is required',
            ], 422);
        }
        if ($adTaken->ads_type == 'VID' && empty($validatedData['vid_path'])) {
            return response()->json([
                'status' => false,
                'message' => 'Video Field is required',
            ], 422);
        }

        // Handling image upload if provided
        if ($request->hasFile('img_path')) {
           // Delete the user former avatar
            if ($ads->imageId) {
                    Cloudinary::destroy($ads->imageId);
            }
            $uploadCloudinary = cloudinary()->upload(
                $request->file('img_path')->getRealPath(),
                [
                    'folder' => 'africtv/ad_image',
                    'resource_type' => 'auto',
                    'transformation' => [
                        'quality' => 'auto',
                        'fetch_format' => 'auto',
                    ],
                ]
            );
            $ads->img_path = $uploadCloudinary->getSecurePath();
            $ads->imageId = $uploadCloudinary->getPublicId();
        }

        // Handling video upload if provided
        if ($request->hasFile('vid_path')) {
            if ($ads->videoId) {
                $response = Cloudinary::destroy($ad->videoId, ['resource_type' => 'video']);
            }
            $duration = getVideoDuration($request->file('vid_path'));
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
                            'fetch_format' => 'auto',
                        ],
                    ]
                );
                $ads->vid_path = $uploadCloudinary->getSecurePath();
                $ads->videoId = $uploadCloudinary->getPublicId();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Video upload failed: ' . $e->getMessage(),
                ]);
            }
        }

        // Update ad details with validated data
        $ads->title = $validatedData['title'];
        $ads->description = $validatedData['description'];
        $ads->link = $validatedData['link'];
        $ads->updated_at = Carbon::now();

        // Save the updated ad
        $ads->save();

        return response()->json([
            'status' => true,
            'message' => 'Ad updated successfully',
            'data' => $ads,
        ]);
    }
    
    public function adInactive(Request $request): JsonResponse
    {
        // Fetch ads with zero clicks and status 'ACTIVE'
        $ads = Ads::where('status', 'ACTIVE')
                  ->where('clicks', '<=', 0)
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
                'message' => 'No ad found with zero clicks.'
            ]);
        }
    }

    public function UserSetAdsInactive(Request $request): JsonResponse
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'ads_id' => 'required|exists:ads,id' 
        ]);

        // Get the ad based on the validated ads_id
        $ad = Ads::find($validatedData['ads_id']);

         if (Auth::check() && Auth::user()->id !== $ad->user_id) {
            return response()->json([
                'status' => false,
                'message' => 'INVALID ATTEMPT',
            ]);
         }

        // Check if the ad exists
        if (!$ad) {
            return response()->json([
                'status' => false,
                'message' => 'Ad Not Found',
            ]);
        }

        $ad->status = "INACTIVE";
        $ad->save();

        return response()->json([
            'status' => true,
            'message' => 'Ad successfully set to INACTIVE',
            'ad' => $ad
        ]);
    }

        public function UserSetAdsActive(Request $request): JsonResponse
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'ads_id' => 'required|exists:ads,id' 
        ]);

        // Get the ad based on the validated ads_id
        $ad = Ads::find($validatedData['ads_id']);

         if (Auth::check() && Auth::user()->id !== $ad->user_id) {
            return response()->json([
                'status' => false,
                'message' => 'INVALID ATTEMPT',
            ]);
         }

        // Check if the ad exists
        if (!$ad) {
            return response()->json([
                'status' => false,
                'message' => 'Ad Not Found',
            ]);
        }

                // Check if the ad exists
        if ($ad->clicks <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Oops! INVALID ATTEMPT',
            ]);
        }

        $ad->status = "ACTIVE";
        $ad->save();

        return response()->json([
            'status' => true,
            'message' => 'Ad successfully set to ACTIVE',
            'ad' => $ad
        ]);
    }

    public function AdsPerClicks(Request $request): JsonResponse
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'clicked' => 'required|boolean',
            'ads_id' => 'required|exists:ads,id' 
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
            if($ad->ads_type == 'VID') {
               $ad->clicks -= 1;
            } elseif ($ad->ads_type == 'PIC') {
               $ad->clicks -= 1;
            } elseif ($ad->ads_type == 'LINK') {
               $ad->clicks -= 1;
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'INVALID ATTEMPT',
                ], 422);
            }
            $ad->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Ad clicks updated successfully',
            'ad' => $ad
        ]);
    }

    public function ads(Request $request): JsonResponse
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

    public function userAds(Request $request): JsonResponse
    {
        $user = Auth::user();
        $ads = Ads::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        if (!$ads) {
            return response()->json([
                'status' => false,
                'message' => 'User has place no ads'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $ads
        ]);
    }

    public function deleteads(Request $request): JsonResponse
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
                    // Check if the authenticated user is the owner of the ads post
                    if (Auth::check() && Auth::user()->id === $ad->user_id) {
                        // Delete the ads media
                        
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

                        // Delete the ads post
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
