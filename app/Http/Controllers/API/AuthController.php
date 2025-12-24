<?php

namespace App\Http\Controllers\API;

use App\Services\SMSService;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PhoneVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Http\Requests\PhoneVerificationRequest;
use App\Http\Requests\OTPVerificationRequest;
use App\Http\Requests\CompleteRegistrationRequest;
use App\Services\IdGeneratorService;
use App\Enums\UserType;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    protected $smsService;
    protected IdGeneratorService $idGenerator;

    public function __construct(SMSService $smsService, IdGeneratorService $idGenerator)
    {
        $this->smsService = $smsService;
        $this->idGenerator = $idGenerator;
    }

    /**
     * Step 1: Send OTP to phone number
     */
    public function sendOTP(PhoneVerificationRequest $request)
    {
        $validated = $request->validated();
        $otpCode = rand(1000, 9999);

        PhoneVerification::where('phone', $validated['phone'])->delete();

        $verification = PhoneVerification::create([
            'phone' => $validated['phone'],
            'otp_code' => $otpCode,
            'expires_at' => Carbon::now()->addMinutes(2),
            'is_verified' => DB::raw('false'), // OR: 'is_verified' => DB::raw('false'),
        ]);
        if ($this->smsService) {
            try {
                $message = $this->smsService->formatOTPRegistrationMessage($otpCode);
                $this->smsService->sendSms($validated['phone'], $message);
                
                return response()->json([
                    'message' => 'OTP sent successfully to your phone number.',
                    'phone' => $validated['phone']
                ], 200);
            } catch (\Exception $e) {
                Log::error('SMS sending failed: ' . $e->getMessage());
                return response()->json([
                    'message' => 'OTP generated but SMS sending failed.',
                    'phone' => $validated['phone'],
                    'otp' => $otpCode 
                ], 200);
            }
        } else {
            return response()->json([
                'message' => 'OTP generated successfully.',
                'phone' => $validated['phone'],
                'otp' => $otpCode 
            ], 200);
        }
    }

   
    /**
     * Step 2: Verify OTP
     */
    public function verifyOTP(OTPVerificationRequest $request)
    {
        $validated = $request->validated();

        $verification = PhoneVerification::where('phone', $validated['phone'])->first();

        if (!$verification) {
            return response()->json([
                'message' => 'No OTP found for this phone number.',
                'errors' => ['phone' => ['No OTP found for this phone number.']]
            ], 422);
        }

        if (!$verification->isValid($validated['otp_code'])) {
            return response()->json([
                'message' => 'Invalid or expired OTP.',
                'errors' => ['otp_code' => ['Invalid or expired OTP.']]
            ], 422);
        }

        $verificationToken = bin2hex(random_bytes(32));
        
        $verification->update([
            'is_verified' => DB::raw('true'),
            'verification_token' => $verificationToken
        ]);

        return response()->json([
            'message' => 'Phone number verified successfully. You can now complete your registration.',
            'verification_token' => $verificationToken,
            'verified' => true
        ], 200);
    }

    /**
     * Resend OTP (when expired or not received)
     */
    public function resendOTP(PhoneVerificationRequest $request)
    {
        $validated = $request->validated();
        
        $existingVerification = PhoneVerification::where('phone', $validated['phone'])->first();
        
        if ($existingVerification) {
            if (!$existingVerification->isExpired() && !$existingVerification->is_verified) {
                return response()->json([
                    'message' => 'OTP is still valid. Please check your messages or wait before requesting a new one.',
                    'phone' => $validated['phone'],
                    'expires_at' => $existingVerification->expires_at,
                    'remaining_time' => $existingVerification->expires_at->diffInSeconds(now()) . ' seconds'
                ], 429); // Too Many Requests
            }
            
            $existingVerification->delete();
        }
        
        $otpCode = rand(1000, 9999);
        
        $verification = PhoneVerification::create([
            'phone' => $validated['phone'],
            'otp_code' => $otpCode,
            'expires_at' => Carbon::now()->addMinutes(5),
            'is_verified' => DB::raw('false'),
        ]);
        
        if ($this->smsService) {
            try {
                $message = $this->smsService->formatOTPRegistrationMessage($otpCode);
                $this->smsService->sendSms($validated['phone'], $message);
                
                return response()->json([
                    'message' => 'OTP resent successfully to your phone number.',
                    'phone' => $validated['phone'],
                    'expires_at' => $verification->expires_at
                ], 200);
            } catch (\Exception $e) {
                Log::error('SMS resending failed: ' . $e->getMessage());
                return response()->json([
                    'message' => 'OTP generated but SMS sending failed.',
                    'phone' => $validated['phone'],
                    'otp' => $otpCode, // For development only
                    'expires_at' => $verification->expires_at
                ], 200);
            }
        } else {
            return response()->json([
                'message' => 'OTP resent successfully.',
                'phone' => $validated['phone'],
                'otp' => $otpCode, // For development only
                'expires_at' => $verification->expires_at
            ], 200);
        }
    }



    /**
     * Step 3: Complete registration with user details
     */
    public function completeRegistration(CompleteRegistrationRequest $request)
    {
        $validated = $request->validated();

        $verification = PhoneVerification::where('verification_token', $validated['verification_token'])
            ->where('is_verified', DB::raw('true'),)
            ->first();

        if (!$verification) {
            return response()->json([
                'message' => 'Invalid verification token.',
                'errors' => ['verification_token' => ['Invalid verification token.']]
            ], 422);
        }

        if ($verification->updated_at < Carbon::now()->subMinutes(30)) {
            return response()->json([
                'message' => 'Verification token expired. Please verify your phone number again.',
                'errors' => ['verification_token' => ['Verification token expired.']]
            ], 422);
        }

        $userId = $this->idGenerator->generateId('customer', 'users', 'user_id', 10);

        $user = User::create([
            'user_id' => $userId,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'] ?? null,
            'phone' => $verification->phone,
            'image' => $validated['image'] ?? null,
            'telegram' => $validated['telegram'] ?? null,
            'gender' => $validated['gender'],
            'type' => UserType::CUSTOMER,
            'password' => Hash::make($validated['password']),
            'phone_verified_at' => Carbon::now(),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $verification->delete();

        return response()->json([
            'message' => 'Registration completed successfully.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id'=> $user->id,
                'customer_id' => $user->user_id,
                'phone' => $user->phone,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'gender' => $user->gender,
                'type' => $user->type,
            ]
        ], 201);
    }

    /**
     * Customer Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)
                    ->where('type', UserType::CUSTOMER)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
                'errors' => [
                    'phone' => ['The provided credentials are incorrect.']
                ]
            ], 401);
        }

        if (!$user->phone_verified_at) {
            return response()->json([
                'message' => 'Phone number not verified. Please verify your phone number first.',
                'errors' => [
                    'phone' => ['Phone number not verified.']
                ]
            ], 422);
        }

        $token = $user->createToken('customer_app_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'customer_id' => $user->user_id,
                'phone' => $user->phone,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'gender' => $user->gender,
                'type' => $user->type,
                'phone_verified_at' => $user->phone_verified_at,
            ]
        ], 200);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.'
        ], 200);
    }

    /**
     * Change Password
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect.',
                'errors' => [
                    'current_password' => ['Current password is incorrect.']
                ]
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return response()->json([
            'message' => 'Password changed successfully.'
        ], 200);
    }


}
 