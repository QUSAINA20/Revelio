<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|string|min:4|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $otp = mt_rand(1000, 9999);

        $user = User::create(
            [
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'password' => $validatedData['password'],
                'otp' => Hash::make($otp),
                'otp_expiry' => now()->addMinutes(5),
            ]
        );

        // Send OTP code via email
        Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($user) {
            $message->to($user->email)->subject('Your Registration OTP Code');
        });

        return response()->json(['message' => 'Registration OTP sent successfully', 'email' => $request->email]);
    }

    public function verifyOTP(Request $request)
    {

        // Validate request data
        $validatedData = $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|digits:4', // OTP should be 4 digits
        ]);

        // Find user by email
        $user = User::where('email', $validatedData['email'])->first();

        // Check if user exists and OTP is valid
        if (!$user || !$this->isValidOTP($user, $validatedData['otp'])) {
            return response()->json(['error' => 'Invalid OTP'], 400);
        }


        // OTP is valid, complete user registration
        // Save additional registration data if needed
        $user->update([
            'otp' => null, // Clear the OTP after successful registration
            'otp_expiry' => null,
            'email_verified_at' => now(), // Update email_verified_at to current timestamp
        ]);

        return response()->json(['message' => 'User verified successfully']);
    }



    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if the user exists
        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'User not found'
            ], 404);
        }

        // Check if the user is verified
        if (!$user->isVerified()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Email address is not verified'
            ], 401);
        }

        // Check if the password is correct
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Generate token
        $token = $user->createToken('token-name')->plainTextToken;

        $response = [
            'status' => 'success',
            'message' => 'User is logged in successfully.',
            'data' => [
                'token' => $token,
                'user' => $user,
            ],
        ];

        return response()->json($response, 200);
    }


    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'User is logged out successfully'
        ], 200);
    }


    private function isValidOTP($user, $otp)
    {
        // Check if OTP exists and is not expired
        return $user->otp && Hash::check($otp, $user->otp) && $user->otp_expiry > now();
    }
}
