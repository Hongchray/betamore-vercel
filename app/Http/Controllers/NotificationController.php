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

    /**
     * Save FCM token for user
     */
    public function saveToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        // Save to database (assuming you have fcm_token column in users table)
        $user = auth()->user();
        if ($user) {
            $user->fcm_token = $request->fcm_token;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'FCM token saved successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'User not authenticated'
        ], 401);
    }

    /**
     * Send notification to authenticated user
     */
    public function sendToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $user = User::find($request->user_id);

        if (!$user->fcm_token) {
            return response()->json([
                'success' => false,
                'message' => 'User does not have FCM token'
            ], 400);
        }

        $result = $this->notificationService->sendToDevice(
            $user->fcm_token,
            $request->title,
            $request->body
        );

        return response()->json($result);
    }

    /**
     * Test endpoint to check configuration
     */
    public function testConfig()
    {
        $credPath = storage_path(config('services.firebase.credentials'));
        $projectId = config('services.firebase.project_id');
        
        $checks = [
            'credentials_file_exists' => file_exists($credPath),
            'credentials_path' => $credPath,
            'project_id' => $projectId,
            'project_id_set' => !empty($projectId),
        ];

        if (file_exists($credPath)) {
            $creds = json_decode(file_get_contents($credPath), true);
            $checks['credentials_valid'] = $creds !== null;
            $checks['has_private_key'] = isset($creds['private_key']);
            $checks['has_client_email'] = isset($creds['client_email']);
            $checks['project_id_match'] = $creds['project_id'] === $projectId;
        }

        return response()->json($checks);
    }
}