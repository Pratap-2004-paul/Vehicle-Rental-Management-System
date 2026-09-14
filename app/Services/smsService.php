<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an OTP to a phone number.
     * Right now this just logs it (development mode) so you can test
     * without a paid SMS account. See Part 9, Section 6, to wire in
     * a real provider (Fast2SMS, Twilio, MSG91, etc.) later.
     */
    public function sendOtp(string $phone, string $otp): void
    {
        Log::info("=== OTP for {$phone}: {$otp} (valid 5 minutes) ===");

        // ---- Real SMS provider goes here once you have an account ----
        // Example using Fast2SMS (common for Indian numbers), left commented:
        //
        // \Illuminate\Support\Facades\Http::asForm()->withHeaders([
        //     'authorization' => config('services.fast2sms.key'),
        // ])->post('https://www.fast2sms.com/dev/bulkV2', [
        //     'route' => 'otp',
        //     'variables_values' => $otp,
        //     'numbers' => $phone,
        // ]);
    }
}