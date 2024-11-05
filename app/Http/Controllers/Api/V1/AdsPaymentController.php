<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdsPayment;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePaymentRequest;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;    
use Unicodeveloper\Paystack\Facades\Paystack;

class AdsPaymentController extends Controller
{  
    public function adsPayment(StorePaymentRequest $request): JsonResponse
    {
        // Data validation
        $validatedData = $request->validated();

        // Determine the amount based on ads type
        $amount = str_replace(',', '', $validatedData['amount']);
        switch ($validatedData['ads_type']) {
            case 'VID':
                $clicksNumber = (float)$amount / 0.20;
                break;
            case 'PIC':
                $clicksNumber = (float)$amount / 0.15;
                break;
            case 'LINK':
                $clicksNumber = (float)$amount / 0.10;
                break;
            default:
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid ad type.',
                ], 422);
        }

        // Ensure the clicks number is valid
        if ($clicksNumber <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'The calculated clicks number must be greater than zero.',
            ], 422);
        }

        // Check if the user has a pending or failed payment for this ad
        $paymentCheck = AdsPayment::where('user_id', Auth::id())
            ->where(function ($query) {
                $query->where('status', 'FAILED')
                      ->orWhere('status', 'PENDING');
            })
            ->first();

        $duration = "null";
        // Prepare ad payment data
        $paymentData = [
            "user_id" => Auth::id(),
            "amount" => $amount,
            "status" => $validatedData['status'] ?? 'PENDING',
            "method" => $validatedData['method'] ?? 'CASH',
            "currency" => $validatedData['currency'] ?? 'NGN',
            "ads_type" => $validatedData['ads_type'],
            "is_ads_type_sec" => $validatedData['is_ads_type_sec'] ?? false,
            "clicks" => $clicksNumber,
            "duration" => $duration,
            "taken" => "NO",
            "ads_id" => 'APAY' . '-' . 
                        strtoupper(substr($validatedData['method'], 0, 2)) . '-' . 
                        strtoupper(substr($validatedData['currency'], 0, 2)) . '-' . 
                        time(),
        ];

        // Update or create payment record
        $payment = $paymentCheck ? $paymentCheck->update($paymentData) : AdsPayment::create($paymentData);

        // Generate Paystack transaction reference
        $transactionReference = Paystack::genTranxRef();

        // Prepare Paystack payment request
        $paystackRequest = [
            "amount" => $paymentData['amount'] * 100, // Convert to kobo
            "email" => Auth::user()->email,
            "reference" => $transactionReference,
            "currency" => $paymentData['currency'],
            "callback_url" => route('adspayment.callback'),
        ];

        // Update payment record with transaction reference
        $payment->update(['reference' => $transactionReference]);

        try {
            // Get the Paystack authorization URL
            $authorizationUrl = Paystack::getAuthorizationUrl($paystackRequest)->url;

            // Return JSON response with the authorization URL
            return response()->json([
                'status' => true,
                'authorization_url' => $authorizationUrl,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Failed to process payment: " . $e->getMessage()
            ]);
        }
    }


    // Handle callback after payment
    public function callback()
    {
        // Get the payment details from Paystack
        $paymentDetails = Paystack::getPaymentData();

        // Check if payment was successful
        if ($paymentDetails['status'] == true && $paymentDetails['data']['status'] == 'success') {
            $payment = AdsPayment::where('reference', $paymentDetails['data']['reference'])->first();

            if ($payment) {
                $payment->update([
                    'status' => 'PAID',
                ]);
            }

            return response()->json([
                'message' => 'Payment successful',
                'paymentDetails' => $paymentDetails
            ]);
        }   

        return response()->json([
            'message' => 'Payment failed',
            'paymentDetails' => $paymentDetails
        ]);
    }

}
