<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ads;
use Illuminate\Support\Facades\Auth;
use App\Mail\AdInactiveNotification;
use Illuminate\Support\Facades\Mail;


class AdsController extends Controller
{
    public function index()
    {
        $ads = Ads::orderBy('created_at', 'desc')
                ->with('user')
                ->paginate(20);

        return view('admin.ads', compact('ads'));
    }  
    
    public function updateAdsInactive()
    {
        // Fetch ads with zero clicks and status 'ACTIVE'
        $ads = Ads::where('status', 'ACTIVE')
                  ->where(function ($query) {
                      $query->where('clicks', '<=', 0)
                            ->orWhere('clicks', '=', 0);
                  })
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
            
            return redirect()->back()->with('message', 'Ads with zero clicks have been deactivated, and notifications have been sent to the owners.');
        } else {
            return redirect()->back()->with('error', 'No ads found with zero clicks.');
        }
    }
}
