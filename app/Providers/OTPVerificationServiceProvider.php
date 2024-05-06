<?php

namespace App\Providers;

use App\Models\OTPVerificationManager;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OTPVerificationServiceProvider
{
    /**
     * Generates and sends a secure One-Time Password (OTP) to the specified recipient.
     *
     * @param string $recipient The recipient's email address or phone number
     * @param string $channel Optional channel for sending OTP (e.g., 'email', 'sms')
     * @return array      The generated OTP if successful, null otherwise
     *
     * @throws Exception     If OTP generation or sending fails
     */
    public static function sendOTP(string $recipient, string $channel = 'sms', bool $forgot_password = false, string $after_verification_step = "", bool $re_init = false): array
    {
        if ($re_init) {
            OtpVerificationManager::where('mobile', $recipient)->delete();
        }

        // 1. Check for recent OTP requests within the last 2 minutes
        $recentOtp = OtpVerificationManager::where('mobile', $recipient)
            ->where('sent_at', '>', Carbon::now()->subMinutes(2))
            ->orderBy('sent_at', 'desc')
            ->first();

        if ($recentOtp) {
            $time_elapsed = Carbon::now()->diffInSeconds($recentOtp->sent_at);
            $retry_after = 120 - $time_elapsed;  # Adjust as needed (120 seconds = 2 minutes)

            // Reject the request with appropriate feedback
            return ['code' => 400, 'message' => "OTP can only be sent every 2 minutes. Please try again after $retry_after seconds.", 'retry_after_seconds' => $retry_after, 'hash' => $recentOtp->hash];
        }

        // 2. Securely Generate a Unique OTP
        if (Cache::get('app_cfg_sms_data', [])['status']) {
            $otp_code = random_int(1000, 9999);
        } else {
            $otp_code = "1000";
        }

        $otp_force_cycle = OtpVerificationManager::where(['mobile' => $recipient])->first();
        if ($otp_force_cycle && $forgot_password) {
            $otp_force_cycle->is_verified = false;
            $otp_force_cycle->forgot_password = true;
            $otp_force_cycle->save();
        }

        $recentOtp = OtpVerificationManager::where(['mobile' => $recipient, 'is_verified' => false])->first();
        if ($recentOtp) {
            $otp = $recentOtp;
        } else {
            $user = User::where('mobile', $recipient)->first();
            $otp = new OtpVerificationManager();
            $otp->user_id = $user->id;
            $otp->mobile = $recipient;
            $otp->hash = Str::orderedUuid()->toString();
        }
        $otp->otp_code = $otp_code;
        $otp->sent_at = now();
        if ($after_verification_step != "") {
            $otp->after_verification_step = $after_verification_step;
        }
        $otp->valid_till = Carbon::now()->addMinutes(2);
        $otp->save();

        if ($channel === 'sms') {
            // Send OTP via SMS using a secure SMS gateway
            $otpSent = SMSServiceProvider::sendSMS($recipient, "Your OTP is $otp_code. Do not share this code with anyone.");
        } else {
            return ['code' => 400, 'message' => "Invalid channel: $channel"];
        }

        if (!$otpSent) {
            // Handle failure to send OTP (e.g., log the error, retry mechanism)
            return ['code' => 400, 'message' => "Failed to send OTP", 'hash' => $otp->hash];
        }

        return ['code' => 200, 'hash' => $otp->hash];
    }

    /**
     * Verifies a given OTP using the provided hash.
     *
     * @param string $hash The unique hash associated with the OTP
     * @param string $otp The OTP entered by the user
     * @return array        Response indicating verification success or failure
     */
    public static function verifyOTP(string $hash, string $otp): array
    {
        // 1. Retrieve OTP from database using hash
        $otpRecord = OtpVerificationManager::where(['hash' => $hash, 'is_verified' => false])->first();

        if (!$otpRecord) {
            $message = 'Invalid OTP or hash';
            return ['code' => 400, 'message' => $message];
        }

        // 2. Check if OTP exists, is valid, and not expired
        if (!Hash::check($otp, $otpRecord->otp_code) || Carbon::now()->gt($otpRecord->valid_till)) {
            // Invalid OTP or hash, or expired OTP
            if (Carbon::now()->gt($otpRecord->valid_till)) {
                $message = 'OTP has expired. Please request a new one.';
            } else {
                $message = 'Invalid OTP or hash';
            }
            return ['code' => 400, 'message' => $message];
        }

        $otpRecord->update(['is_verified' => true]);

        // 3. Return success response
        return ['code' => 200, 'message' => 'OTP verified successfully', 'mobile' => $otpRecord->mobile, 'after_verification_step' => $otpRecord->after_verification_step];
    }

    public static function isOTPVerified(string $hash, bool $forgot_password = false): array
    {
        $otp_verified = OtpVerificationManager::where(['hash' => $hash, 'is_verified' => true, 'forgot_password' => $forgot_password])->first();
        if ($otp_verified) {
            return ['code' => 200, 'message' => 'OTP has been verified.', 'user_id' => $otp_verified->user_id];
        } else {
            return ['code' => 400, 'message' => 'OTP is not verified.'];
        }
    }

    public static function resendOTP(string $hash): array
    {
        // 1. Retrieve OTP from database using hash
        $otpRecord = OtpVerificationManager::where(['hash' => $hash])->first();

        if (!$otpRecord) {
            $message = 'Invalid OTP or hash';
            return ['code' => 400, 'message' => $message];
        }
        if (!$otpRecord->is_verified) {
            // 2. Check if OTP exists, is valid, and not expired
            if (Carbon::now()->gt($otpRecord->valid_till)) {
                // Invalid OTP or hash, or expired OTP
                return OTPVerificationServiceProvider::sendOTP(recipient: $otpRecord->mobile);
            } else {
                $time_elapsed = Carbon::now()->diffInSeconds($otpRecord->sent_at);
                $retry_after = 120 - $time_elapsed;  # Adjust as needed (120 seconds = 2 minutes)

                // Reject the request with appropriate feedback
                return ['code' => 400, 'message' => "OTP can only be sent every 2 minutes. Please try again after $retry_after seconds.", 'retry_after_seconds' => $retry_after, 'hash' => $otpRecord->hash];
            }
        } else {
            $message = 'OTP has already been verified.';
            return ['code' => 400, 'message' => $message, 'hash' => null, 'retry_after_seconds' => 0, 'verified' => true];
        }
    }
}
