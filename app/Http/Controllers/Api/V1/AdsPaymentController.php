<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdsPayment;

class AdsPaymentController extends Controller
{  
  public function adsPayment(Request $request)
{
    // Initial validation for fields excluding clicks
    $validatedData = $request->validate([
        "user_id" => "required",
        "user_name" => "required",
        "user_email" => "required|email",
        "amount" => "required|numeric|min:0",
        "payment_type" => "required",
        "payment_status" => "required",
        "payment_method" => "required",
        "currency" => "required",
        "ads_type" => "required",
        "duration" => "required",
    ]);

    // Calculate clicksNumber based on the amount provided in the request
    $clicksNumber = $request->input('amount') / 0.05;

    // Validate that the calculated clicks number is greater than zero
    if ($clicksNumber <= 0) {
        return response()->json([
            'status' => false,
            'message' => 'The calculated clicks number must be greater than zero.',
            'errors' => ['clicks' => ['The calculated clicks number must be greater than zero.']],
        ], 422);
    }

    // Storing payment data
    $payment = AdsPayment::create([
        "user_id" => $validatedData['user_id'],
        "user_name" => $validatedData['user_name'],
        "user_email" => $validatedData['user_email'],
        "amount" => $validatedData['amount'],
        "payment_type" => $validatedData['payment_type'],
        "payment_status" => $validatedData['payment_status'],
        "payment_method" => $validatedData['payment_method'],
        "currency" => $validatedData['currency'],
        "ads_type" => $validatedData['ads_type'],
        "duration" => $validatedData['duration'],
        "clicks" => $clicksNumber,
    ]);

    return response()->json([
        "status" => true,
        "message" => "Ads Payment Made Successfully",
        'data' => array_merge($validatedData, ['clicks' => $clicksNumber]),
    ]);
  }
}
