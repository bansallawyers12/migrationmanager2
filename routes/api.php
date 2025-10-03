<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\ServiceAccountController;
use App\Http\Controllers\API\ClientPortalController;
use App\Http\Controllers\API\ClientPortalDashboardController;
use App\Http\Controllers\API\ClientPortalDocumentController;
use App\Http\Controllers\API\ClientPortalWorkflowController;
use App\Http\Controllers\API\ClientPortalMessageController;

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
Route::post('/refresh', [ClientPortalController::class, 'refresh']);
Route::post('/forgot-password', [ClientPortalController::class, 'forgotPassword']);
Route::post('/reset-password', [ClientPortalController::class, 'resetPassword']);

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ClientPortalController::class, 'logout']);
    Route::post('/logout-all', [ClientPortalController::class, 'logoutAll']);
    Route::get('/profile', [ClientPortalController::class, 'getProfile']);
    Route::put('/profile', [ClientPortalController::class, 'updateProfile']);
    Route::post('/update-password', [ClientPortalController::class, 'updatePassword']);
    
    // Dashboard routes
    Route::get('/dashboard', [ClientPortalDashboardController::class, 'dashboard']);
    Route::get('/recent-cases', [ClientPortalDashboardController::class, 'recentCaseViewAll']);
    Route::get('/documents', [ClientPortalDashboardController::class, 'documentViewAll']);
    Route::get('/upcoming-deadlines', [ClientPortalDashboardController::class, 'upcomingDeadlinesViewAll']);
    Route::get('/recent-activity', [ClientPortalDashboardController::class, 'recentActivityViewAll']);
    
    // Matters routes
    Route::get('/matters', [ClientPortalDashboardController::class, 'getAllMatters']);
    
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
    Route::get('/messages', [ClientPortalMessageController::class, 'getMessages']);
    Route::get('/messages/unread-count', [ClientPortalMessageController::class, 'getUnreadCount']);
    Route::get('/messages/recipients', [ClientPortalMessageController::class, 'getRecipients']);
    Route::get('/messages/{id}', [ClientPortalMessageController::class, 'getMessageDetails']);
    Route::put('/messages/{id}/read', [ClientPortalMessageController::class, 'markAsRead']);
    Route::delete('/messages/{id}', [ClientPortalMessageController::class, 'deleteMessage']);
    
});

// Broadcasting auth route for WebSocket authentication
Route::post('/broadcasting/auth', function (Request $request) {
    // For testing purposes, always allow authentication
    return response()->json([
        'auth' => 'test-auth-string',
        'socket_id' => $request->input('socket_id'),
        'channel_name' => $request->input('channel_name')
    ]);
});

// Service Account Token Generation
Route::post('/service-account/generate-token', [ServiceAccountController::class, 'generateToken']);


    