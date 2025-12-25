<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FcmTokenController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'fcm_token' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], status: 422);
            }

            // ✅ Get authenticated user from token
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            // Update FCM token
            $user->update([
                'fcm_token' => $request->fcm_token,
            ]);

            Log::info('FCM token updated successfully', [
                'user_id' => $user->id,
                'token_preview' => substr($request->fcm_token, 0, 20) . '...',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FCM token updated successfully',
                'data' => [
                    'user_id' => $user->id,
                    'fcm_token_set' => true,
                ],
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Failed to update FCM token', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update FCM token',
            ], 500);
        }
    }
}
