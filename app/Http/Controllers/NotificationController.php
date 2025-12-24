<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Show the notification test form
     */
    public function showForm()
    {
        return view('notifications.test-form');
    }

    /**
     * Send test notification
     */
    public function sendTest(Request $request)
    {
        try {
            // Log incoming request
            Log::info('Notification test request received', [
                'has_token' => $request->has('fcm_token'),
                'token_length' => $request->fcm_token ? strlen($request->fcm_token) : 0,
                'title' => $request->title,
            ]);

            // Validate request
            $request->validate([
                'fcm_token' => 'required|string',
                'title' => 'required|string|max:255',
                'body' => 'required|string',
            ]);

            // Check if credentials file exists
            $credPath = storage_path(config('services.firebase.credentials'));
            if (!file_exists($credPath)) {
                Log::error('Firebase credentials file not found', ['path' => $credPath]);
                return response()->json([
                    'success' => false,
                    'message' => 'Firebase credentials not configured',
                    'error' => "Credentials file not found at: {$credPath}"
                ], 500);
            }

            // Check project ID
            $projectId = config('services.firebase.project_id');
            if (!$projectId) {
                Log::error('Firebase project ID not configured');
                return response()->json([
                    'success' => false,
                    'message' => 'Firebase project ID not configured',
                    'error' => 'FIREBASE_PROJECT_ID not set in .env'
                ], 500);
            }

            Log::info('Attempting to send notification', [
                'project_id' => $projectId,
                'credentials_path' => $credPath,
            ]);

            // Send notification
            $result = $this->notificationService->sendToDevice(
                $request->fcm_token,
                $request->title,
                $request->body,
                ['custom_data' => 'test notification']
            );

            Log::info('Notification send result', $result);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification sent successfully!',
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send notification',
                    'error' => $result['error']
                ], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'error' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Exception in sendTest', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
   
}