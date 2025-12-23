<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ServiceAccountController;
use App\Http\Controllers\API\BroadcastNotificationController;
use App\Http\Controllers\API\ClientPortalController;
use App\Http\Controllers\API\ClientPortalDashboardController;
use App\Http\Controllers\API\ClientPortalDocumentController;
use App\Http\Controllers\API\ClientPortalWorkflowController;
use App\Http\Controllers\API\ClientPortalMessageController;
use App\Http\Controllers\API\ClientPortalPersonalDetailsController;
use App\Http\Controllers\API\ClientPortalCommonListingController;
use App\Http\Controllers\API\FCMController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes (no authentication required)
Route::post('/login', [ClientPortalController::class, 'login']);
Route::post('/admin-login', [ClientPortalController::class, 'adminLogin']);
Route::post('/refresh', [ClientPortalController::class, 'refresh']);
Route::post('/forgot-password', [ClientPortalController::class, 'forgotPassword']);
Route::post('/reset-password', [ClientPortalController::class, 'resetPassword']);

// Countries API (public route)
Route::get('/countries', [ClientPortalCommonListingController::class, 'getCountries']);

// Visa Types API (public route)
Route::get('/visa-types', [ClientPortalCommonListingController::class, 'getVisaTypes']);

// Search Occupations API (public route)
Route::get('/search-occupation', [ClientPortalCommonListingController::class, 'searchOccupationDetail']);


// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ClientPortalController::class, 'logout']);
    Route::post('/logout-all', [ClientPortalController::class, 'logoutAll']);
    Route::get('/profile', [ClientPortalController::class, 'getProfile']);
    Route::post('/profile', [ClientPortalController::class, 'updateProfile']);
    Route::post('/update-password', [ClientPortalController::class, 'updatePassword']);
    
    // Dashboard routes
    Route::get('/dashboard', [ClientPortalDashboardController::class, 'dashboard']);
    Route::get('/recent-cases', [ClientPortalDashboardController::class, 'recentCaseViewAll']);
    Route::get('/documents', [ClientPortalDashboardController::class, 'documentViewAll']);
    Route::get('/upcoming-deadlines', [ClientPortalDashboardController::class, 'upcomingDeadlinesViewAll']);
    Route::get('/recent-activity', [ClientPortalDashboardController::class, 'recentActivityViewAll']);
    
    // Matters routes
    Route::get('/matters', [ClientPortalDashboardController::class, 'getAllMatters']);
    
    // Client Personal Details routes
    Route::get('/get-client-personal-detail', [ClientPortalPersonalDetailsController::class, 'getClientPersonalDetail']);
    Route::post('/update-client-basic-detail', [ClientPortalPersonalDetailsController::class, 'updateClientBasicDetail']);
    Route::post('/update-client-phone-detail', [ClientPortalPersonalDetailsController::class, 'updateClientPhoneDetail']);
    Route::post('/update-client-email-detail', [ClientPortalPersonalDetailsController::class, 'updateClientEmailDetail']);
    Route::post('/update-client-address-detail', [ClientPortalPersonalDetailsController::class, 'updateClientAddressDetail']);
    Route::post('/update-client-travel-detail', [ClientPortalPersonalDetailsController::class, 'updateClientTravelDetail']);
    Route::post('/update-client-qualification-detail', [ClientPortalPersonalDetailsController::class, 'updateClientQualificationDetail']);
    Route::post('/update-client-experience-detail', [ClientPortalPersonalDetailsController::class, 'updateClientExperienceDetail']);
    Route::post('/update-client-occupation-detail', [ClientPortalPersonalDetailsController::class, 'updateClientOccupationDetail']);
    Route::post('/update-client-testscore-detail', [ClientPortalPersonalDetailsController::class, 'updateClientTestScoreDetail']);
    Route::post('/update-client-passport-detail', [ClientPortalPersonalDetailsController::class, 'updateClientPassportDetail']);
    Route::post('/delete-client-tab-detail', [ClientPortalPersonalDetailsController::class, 'deleteClientTabDetail']);
    Route::post('/delete-client-passport-detail', [ClientPortalPersonalDetailsController::class, 'deleteClientPassportDetail']); // Deprecated: Use delete-client-tab-detail instead
    Route::post('/update-client-visa-detail', [ClientPortalPersonalDetailsController::class, 'updateClientVisaDetail']);
    

    // Document Management routes
    Route::get('/documents/personal/categories', [ClientPortalDocumentController::class, 'getPersonalDocumentCategories']);
    Route::get('/documents/personal/checklist', [ClientPortalDocumentController::class, 'getPersonalDocumentChecklist']);
    Route::get('/documents/visa/categories', [ClientPortalDocumentController::class, 'getVisaDocumentCategories']);
    Route::get('/documents/visa/checklist', [ClientPortalDocumentController::class, 'getVisaDocumentChecklist']);
    Route::post('/documents/checklist', [ClientPortalDocumentController::class, 'addDocumentChecklist']);
    Route::post('/documents/upload', [ClientPortalDocumentController::class, 'uploadDocument']);
    
    // Workflow Management routes
    Route::get('/workflow/stages', [ClientPortalWorkflowController::class, 'getWorkflowStages']);
    Route::get('/workflow/stages/{stage_id}', [ClientPortalWorkflowController::class, 'getWorkflowStageDetails']);
   
    Route::get('/workflow/allowed-checklist', [ClientPortalWorkflowController::class, 'allowedChecklistForStages']);
    Route::post('/workflow/upload-allowed-checklist', [ClientPortalWorkflowController::class, 'uploadAllowedChecklistDocument']);
    
    // Messaging routes (specific routes first to avoid conflicts)
    Route::post('/messages/send', [ClientPortalMessageController::class, 'sendMessage']);
    Route::post('/messages/send-to-client', [ClientPortalMessageController::class, 'sendMessageToClient']);
    Route::get('/messages', [ClientPortalMessageController::class, 'getMessages']);
    Route::get('/messages/unread-count', [ClientPortalMessageController::class, 'getUnreadCount']);
    Route::post('/messages/{id}/read', [ClientPortalMessageController::class, 'markAsRead']);
    Route::get('/messages/{id}', [ClientPortalMessageController::class, 'getMessageDetails']);

    Route::post('/payments/create-payment-intent', function (Request $request) {
        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:50'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'customer' => ['sometimes', 'string'],
            'description' => ['sometimes', 'string', 'max:255'],
            'metadata' => ['sometimes', 'array'],
            'receipt_email' => ['sometimes', 'email'],
            'automatic_payment_methods.enabled' => ['sometimes', 'boolean'],
        ]);

        try {
            $stripeSecret = config('services.stripe.secret');

            if (!$stripeSecret) {
                return response()->json([
                    'message' => 'Stripe secret key is not configured.',
                ], 500);
            }

            $stripe = new \Stripe\StripeClient($stripeSecret);

            $payload = [
                'amount' => $validated['amount'],
                'currency' => strtolower($validated['currency'] ?? 'usd'),
                'automatic_payment_methods' => [
                    'enabled' => data_get($validated, 'automatic_payment_methods.enabled', true),
                ],
            ];

            if (isset($validated['customer'])) {
                $payload['customer'] = $validated['customer'];
            }

            if (isset($validated['description'])) {
                $payload['description'] = $validated['description'];
            }

            if (isset($validated['metadata'])) {
                $payload['metadata'] = $validated['metadata'];
            }

            if (isset($validated['receipt_email'])) {
                $payload['receipt_email'] = $validated['receipt_email'];
            }

            $paymentIntent = $stripe->paymentIntents->create($payload);

            return response()->json([
                'id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'client_secret' => $paymentIntent->client_secret,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
            ], 201);
        } catch (\Stripe\Exception\ApiErrorException $exception) {
            Log::error('Stripe PaymentIntent creation failed', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Unable to create payment intent.',
                'error' => $exception->getMessage(),
            ], 400);
        } catch (\Throwable $exception) {
            Log::error('Unexpected error creating PaymentIntent', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    });

    // Broadcast notifications
    Route::get('/notifications/broadcasts/unread', [BroadcastNotificationController::class, 'unread']);
    Route::post('/notifications/broadcasts', [BroadcastNotificationController::class, 'store']);
    Route::get('/notifications/broadcasts', [BroadcastNotificationController::class, 'index']);
    Route::get('/notifications/broadcasts/{batchUuid}', [BroadcastNotificationController::class, 'show']);
    Route::post('/notifications/broadcasts/{notificationId}/read', [BroadcastNotificationController::class, 'markAsRead']);
    
    // FCM Push Notification routes
    Route::post('/fcm/register-token', [FCMController::class, 'registerToken']);
    Route::post('/fcm/unregister-token', [FCMController::class, 'unregisterToken']);
    Route::post('/fcm/test', [FCMController::class, 'testNotification']);
    Route::post('/fcm/send-message', [FCMController::class, 'sendMessage']);
    
});

// Broadcasting auth route for WebSocket authentication
Route::post('/broadcasting/auth', function (Request $request) {
    try {
        // Get the authorization header
        $authHeader = $request->header('Authorization');
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Extract token
        $token = substr($authHeader, 7);
        
        // Get request data - handle both form and JSON content types
        $socketId = $request->input('socket_id');
        $channelName = $request->input('channel_name');
        
        // Validate token using Sanctum
        $user = \Laravel\Sanctum\PersonalAccessToken::findToken($token)?->tokenable;
        
        if (!$user) {
            Log::error('Invalid token provided for channel auth', ['token' => substr($token, 0, 10) . '...']);
            return response()->json(['error' => 'Invalid token'], 401);
        }
        
        // Log the request details for debugging
        Log::info('Broadcasting auth request', [
            'content_type' => $request->header('Content-Type'),
            'socket_id' => $socketId,
            'channel_name' => $channelName,
            'user_id' => $user->id,
            'request_data' => $request->all()
        ]);
        
        // Set the authenticated user for the request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        
        // Validate channel name format and authorization
        if (!preg_match('/^private-(user|matter)\.\d+$/', $channelName)) {
            Log::warning('Invalid channel format', ['user_id' => $user->id, 'channel' => $channelName]);
            return response()->json(['error' => 'Invalid channel format'], 403);
        }
        
        // Ensure we have required parameters
        if (!$socketId || !$channelName) {
            Log::warning('Missing required parameters', [
                'socket_id' => $socketId,
                'channel_name' => $channelName,
                'user_id' => $user->id
            ]);
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        
        // Check channel authorization based on channel type
        if (str_starts_with($channelName, 'private-user.')) {
            $requestedUserId = (int) substr($channelName, 13); // Remove 'private-user.'
            if ($user->id !== $requestedUserId) {
                Log::warning('User cannot access another user\'s channel', [
                    'user_id' => $user->id, 
                    'requested_user_id' => $requestedUserId,
                    'channel' => $channelName
                ]);
                return response()->json(['error' => 'Channel access denied'], 403);
            }
        } elseif (str_starts_with($channelName, 'private-matter.')) {
            $matterId = (int) substr($channelName, 15); // Remove 'private-matter.'
            
            // Check if user is associated with this matter or is superadmin
            $isAssociated = DB::table('client_matters')
                ->where('id', $matterId)
                ->where(function($query) use ($user) {
                    $query->where('sel_migration_agent', $user->id)
                          ->orWhere('sel_person_responsible', $user->id)
                          ->orWhere('sel_person_assisting', $user->id);
                })
                ->exists();
            
            $isSuperAdmin = $user->role == 1;
            
            if (!$isAssociated && !$isSuperAdmin) {
                Log::warning('User cannot access matter channel', [
                    'user_id' => $user->id, 
                    'matter_id' => $matterId,
                    'channel' => $channelName
                ]);
                return response()->json(['error' => 'Channel access denied'], 403);
            }
        }
        
        Log::info('Channel auth successful', ['user_id' => $user->id, 'channel' => $channelName]);

        // Generate auth response using Pusher Cloud
        $pusher = new \Pusher\Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            [
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'useTLS' => config('broadcasting.connections.pusher.options.useTLS', true),
                'encrypted' => config('broadcasting.connections.pusher.options.encrypted', true),
            ]
        );

        $authResponse = $pusher->authorizeChannel($channelName, $socketId);

        return response($authResponse, 200, [
            'Content-Type' => 'text/plain'
        ]);
        
    } catch (\Exception $e) {
        Log::error('Broadcasting auth error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request_data' => [
                'socket_id' => $request->input('socket_id'),
                'channel_name' => $request->input('channel_name'),
                'auth_header' => $request->header('Authorization') ? 'Present' : 'Missing'
            ]
        ]);
        return response()->json(['error' => 'Authentication failed: ' . $e->getMessage()], 500);
    }
});

// Service Account Token Generation
Route::post('/service-account/generate-token', [ServiceAccountController::class, 'generateToken']);

// ANZSCO Occupation API Routes moved to web.php for proper authentication

    