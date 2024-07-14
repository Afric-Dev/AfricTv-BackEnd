<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use App\Models\User;


class NewPasswordController extends Controller
{

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            // If the reset link was sent successfully
            $user = User::where('email', $request->email)->first();
            if ($user) {
                // Send reset password email to the user
                Mail::to($user->email)->send(new ResetPasswordMail($user));
            }

            return response()->json([
                'status' => true,
                'message' => 'Password Reset Link Sent to Email'
            ]);
        } else {
            // If an error occurred
            return response()->json([
                'status' => false,
                'message' => $status,
            ]);
        }
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', RulesPassword::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                "status" => true,
                "message"=> "Password reset successfully"
            ]);
        }

        return response()->json([
            "status" => false,
            "message"=> $status
        ], 500);

    }


}