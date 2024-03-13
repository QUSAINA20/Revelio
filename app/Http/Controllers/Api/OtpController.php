<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OtpController extends Controller
{
    public function sendOTP(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $otp = $this->generateOTP();

        $user = User::where('email', $validatedData['email'])->first();


        // Store OTP code with user's email
        $user->update([
            'otp' => Hash::make($otp),
            'otp_expiry' => now()->addMinutes(5),
        ]);

        // Send OTP code via email
        Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($user) {
            $message->to($user->email)->subject('Your OTP Code');
        });

        return response()->json(['message' => 'OTP sent successfully']);
    }

    public function verifyOTP(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|digits:4', // OTP should be 4 digits
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Check if OTP is expired
        if ($user->otp_expiry < now()) {
            return response()->json(['error' => 'OTP has expired'], 400);
        }

        // Verify OTP code
        if (Hash::check($request->otp, $user->otp)) {
            // OTP is correct
            // Clear OTP from user record (optional)
            $user->otp = null;
            $user->otp_expiry = null;
            $user->save();

            return response()->json(['message' => 'OTP verified successfully']);
        } else {
            // OTP is incorrect
            return response()->json(['error' => 'Invalid OTP'], 400);
        }
    }

    public function resendOTP(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Regenerate OTP code
        $otp = $this->generateOTP();

        // Update OTP code and expiry in the user record
        $user->otp = Hash::make($otp);
        $user->otp_expiry = now()->addMinutes(5);
        $user->save();

        // Resend OTP code via email
        Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($user) {
            $message->to($user->email)->subject('Your OTP Code');
        });

        return response()->json(['message' => 'OTP resent successfully']);
    }
    public function changePassword(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Check if OTP is null
        if (!is_null($user->otp)) {
            return response()->json(['error' => 'OTP verification required before changing password'], 400);
        }

        // Update user's password
        $user->password = Hash::make($validatedData['password']);
        $user->save();

        return response()->json(['message' => 'Password changed successfully']);
    }

    private function generateOTP()
    {
        $otp = mt_rand(1000, 9999);

        return $otp;
    }
}
