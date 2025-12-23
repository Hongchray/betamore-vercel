<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;

class FcmTokenController extends Controller
{
    public function update(Request $request): JsonResponse
{
    try {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'fcm_token' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find the user by ID
        $user = User::find($request->user_id);

        // Update FCM token
        $user->fcm_token = $request->fcm_token;
        $user->save();

        Log::info('FCM token updated successfully', [
            'user_id' => $user->id,
            'token_preview' => substr($request->fcm_token, 0, 20) . '...'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FCM token updated successfully',
            'data' => [
                'user_id' => $user->id,
                'fcm_token_set' => !empty($user->fcm_token)
            ]
        ], 200);

    } catch (\Exception $e) {
        Log::error('Failed to update FCM token', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to update FCM token',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
