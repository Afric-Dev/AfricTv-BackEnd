<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdsPayment;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePaymentRequest;
use Illuminate\Http\JsonResponse;

class AdsPaymentController extends Controller
{  
     public function adsPayment(StorePaymentRequest $request): JsonResponse
    {
        // Initial validation for fields excluding clicks
        $validatedData = $request->validated();

        // Calculate clicksNumber based on the amount provided in the request
        $amount = $request->input('amount');
        $cleanAmount = str_replace(',', '', $amount);
        if($validatedData['ads_type'] == 'VID') {
            $clicksNumber = (float)$cleanAmount / 0.20;
        } elseif ($validatedData['ads_type'] == 'PIC') {
            $clicksNumber = (float)$cleanAmount / 0.15;
        } elseif ($validatedData['ads_type'] == 'LINK') {
            $clicksNumber = (float)$cleanAmount / 0.10;
        } else {
            return response()->json([
                'status' => false,
                'message' => 'INVALID ATTEMPT',
            ], 422);
        }

        // Validate that the calculated clicks number is greater than zero
        if ($clicksNumber <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'The calculated clicks number must be greater than zero.',
                'errors' => ['clicks' => ['The calculated clicks number must be greater than zero.']],
            ], 422);
        }

        $taken = "NO";
        $duration = "NOT SET";

        $uniqueId = time();
        $AdsID = strtoupper(substr($request->status, 0, 2)) . '-' . 
                 strtoupper(substr($request->method, 0, 2)) . '-' . 
                 strtoupper(substr($request->currency, 0, 2)) . '-' . 
                 $uniqueId;


        // Storing payment data
        $payment = AdsPayment::create([
            "user_id" => Auth::user()->id,
            "amount" => $validatedData['amount'],
            "status" => $validatedData['status'],
            "method" => $validatedData['method'],
            "currency" => $validatedData['currency'],
            "ads_type" => $validatedData['ads_type'],
            "is_ads_type_sec" => $validatedData['is_ads_type_sec'],
            "duration" => $duration,
            "status" => $validatedData['status'],
            "clicks" => $clicksNumber,
            "taken" => $taken,
            "ads_id" => $AdsID
        ]);

        return response()->json([
            "status" => true,
            "message" => "Ads Payment Made Successfully",
            'data' => array_merge($validatedData, ['clicks' => $clicksNumber]),
        ]);
      }

}
