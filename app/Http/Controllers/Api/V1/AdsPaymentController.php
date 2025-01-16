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
                $clicksNumber = (float)$amount / 50;
                break;
            case 'PIC':
                $clicksNumber = (float)$amount / 30;
                break;
            case 'LINK':
                $clicksNumber = (float)$amount / 20;
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

        $method = $validatedData['method'] ?? 'PAYSTACK';
        $currency = $validatedData['currency'] ?? 'NGN';
        $duration = "null";
        // Prepare ad payment data
        $paymentData = [
            "user_id" => Auth::id(),
            "amount" => $amount,
            "status" => $validatedData['status'] ?? 'PENDING',
            "method" => $method,
            "currency" => $currency,
            "ads_type" => $validatedData['ads_type'],
            "is_ads_type_sec" => $validatedData['is_ads_type_sec'] ?? false,
            "clicks" => $clicksNumber,
            "duration" => $duration,
            "taken" => "NO",
            "ads_id" => 'APAY' . '-' . 
                        strtoupper(substr($method, 0, 2)) . '-' . 
                        strtoupper(substr($currency, 0, 2)) . '-' . 
                        time(),
        ];

        // Update or create payment record
        // $payment = $paymentCheck ? $paymentCheck->update($paymentData) : AdsPayment::create($paymentData);
        if ($paymentCheck) {
            $paymentCheck->update($paymentData);
            $payment = $paymentCheck; // Assign the updated model instance to $payment
        } else {
            $payment = AdsPayment::create($paymentData);
        }
        
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
                    'currency' => $paymentDetails['data']['currency'],
                ]);
            }

           

            return response("
                <!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <meta http-equiv='refresh' content='3;http://localhost:5173/'>
                    <title>Redirecting...</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #f9f9f9;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            height: 100vh;
                            margin: 0;
                        }
                        .popup {
                            background-color: #fff;
                            padding: 20px;
                            border-radius: 10px;
                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                            text-align: center;
                        }
                        .popup h2 {
                            margin: 0;
                            color: #28a745;
                        }
                        .popup p {
                            margin: 10px 0;
                            font-size: 14px;
                            color: #333;
                        }
                    </style>
                </head>
                <body>
                    <div class='popup'>
                        <h2>APay is verifying your payment...</h2>
                        <p>You will be redirected to the next page once your payment has been verifield...</p>
                    </div>
                </body>
                </html>
            ", 200)
            ->header('Content-Type', 'text/html');
        }   
                //if payment failded
                 return response("
                <!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <meta http-equiv='refresh' content=''>
                    <title>Redirecting...</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #f9f9f9;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            height: 100vh;
                            margin: 0;
                        }
                        .popup {
                            background-color: #fff;
                            padding: 20px;
                            border-radius: 10px;
                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                            text-align: center;
                        }
                        .popup h2 {
                            margin: 0;
                            color: #28a745;
                        }
                        .popup p {
                            margin: 10px 0;
                            font-size: 14px;
                            color: #333;
                        }
                    </style>
                </head>
                <body>
                    <div class='popup'>
                        <h2>Payment Failed! APAY could not get a success message from Paystack</h2>
                        <p>YKindly go back and try again</p>
                    </div>
                </body>
                </html>
            ", 200)
            ->header('Content-Type', 'text/html');
    }

        public function userPayments(Request $request): JsonResponse
        {
            $user = Auth::user();
            $payments = AdsPayment::where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->get();

            if (!$payments) {
                return response()->json([
                    'status' => false,
                    'message' => 'User have made no payment yet'
                ]);
            }

            return response()->json([
                'status' => true,
                'data' => $payments
            ]);
        }

}
