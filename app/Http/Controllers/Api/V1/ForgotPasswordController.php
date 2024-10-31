<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse; 

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid email address.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Send the reset link
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => true,
                'message' => 'Reset password link sent to your mail successfully.'
            ], 200);
        } elseif ($status === Password::RESET_THROTTLED) {
            return response()->json([
                'status' => false,
                'message' => 'Too many reset requests. Please wait before trying again.',
                //'error_code' => $status
            ], 429);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Failed to send reset link.',
                //'error_code' => $status,
            ], 500);
        }
    }


        public function reset(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid input.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Attempt to reset the user's password
        $status = Password::reset(
            $request->only('email', 'token', 'password', 'password_confirmation'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'status' => true,
                'message' => 'Password reset successful.'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Failed to reset password. Please try again.',
            ], 500);
        }
    }
}