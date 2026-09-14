<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    // POST /api/forgot-password/send-otp
    public function sendOtp(Request $request, SmsService $sms)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('phone', $request->phone)->first();

        // Don't reveal whether the phone exists — always respond the same way.
        // (Prevents someone from using this form to check which numbers are registered.)
        if (! $user) {
            return response()->json([
                'message' => 'If that phone number is registered, an OTP has been sent.',
            ]);
        }

        // Remove any previous OTPs for this phone before issuing a new one
        Otp::where('phone', $request->phone)->delete();

        $code = (string) random_int(100000, 999999);

        Otp::create([
            'phone' => $request->phone,
            'code' => $code,
            'expires_at' => now()->addMinutes(5),
        ]);

        $sms->sendOtp($request->phone, $code);

        return response()->json([
            'message' => 'If that phone number is registered, an OTP has been sent.',
        ]);
    }

    // POST /api/forgot-password/reset-password
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'otp' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $otpRecord = Otp::where('phone', $request->phone)
            ->where('code', $request->otp)
            ->first();

        if (! $otpRecord) {
            return response()->json(['message' => 'Invalid OTP.'], 422);
        }

        if ($otpRecord->expires_at->isPast()) {
            $otpRecord->delete();
            return response()->json(['message' => 'This OTP has expired. Please request a new one.'], 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if (! $user) {
            return response()->json(['message' => 'No account found for this phone number.'], 404);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        // OTP is single-use
        $otpRecord->delete();

        return response()->json(['message' => 'Password reset successfully. You can now log in.']);
    }
}