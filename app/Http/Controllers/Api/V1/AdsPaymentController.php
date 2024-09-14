<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdsPayment;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePaymentRequest;

class AdsPaymentController extends Controller
{  
     public function adsPayment(StorePaymentRequest $request)
    {
        // Initial validation for fields excluding clicks
        $validatedData = $request->validated();

        // Calculate clicksNumber based on the amount provided in the request
        $clicksNumber = $request->input('amount') / 0.10;

        // Validate that the calculated clicks number is greater than zero
        if ($clicksNumber <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'The calculated clicks number must be greater than zero.',
                'errors' => ['clicks' => ['The calculated clicks number must be greater than zero.']],
            ], 422);
        }

        $taken = "NO";

        // Storing payment data
        $payment = AdsPayment::create([
            "user_id" => Auth::user()->id,
            "amount" => $validatedData['amount'],
            "status" => $validatedData['status'],
            "method" => $validatedData['method'],
            "currency" => $validatedData['currency'],
            "ads_type" => $validatedData['ads_type'],
            "duration" => $validatedData['duration'],
            "status" => $validatedData['status'],
            "clicks" => $clicksNumber,
            "taken" => $taken,
        ]);

        return response()->json([
            "status" => true,
            "message" => "Ads Payment Made Successfully",
            'data' => array_merge($validatedData, ['clicks' => $clicksNumber]),
        ]);
      }

}
