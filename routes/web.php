<?php

use App\Http\Controllers\CRM\ClientsController;
use App\Http\Controllers\CRM\ClientEoiRoiController;
use App\Http\Controllers\AdminConsole\AnzscoOccupationController;
use App\Http\Controllers\CRM\Clients\ClientNotesController;
use App\Http\Controllers\CRM\ClientPersonalDetailsController;
use App\Http\Controllers\CRM\PhoneVerificationController;
use App\Http\Controllers\CRM\EmailVerificationController;
use App\Http\Controllers\CRM\Leads\LeadController;
use App\Http\Controllers\CRM\Leads\LeadAssignmentController;
use App\Http\Controllers\CRM\Leads\LeadConversionController;
use App\Http\Controllers\CRM\Leads\LeadFollowupController;
use App\Http\Controllers\CRM\Leads\LeadAnalyticsController;
use App\Http\Controllers\CRM\DashboardController;
use App\Http\Controllers\CRM\AdminController;
use App\Http\Controllers\CRM\AssigneeController;
use App\Http\Controllers\CRM\EmailTemplateController;
use App\Http\Controllers\CRM\AuditLogController;
use App\Http\Controllers\Auth\AdminLoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*--------------------------------------------------
| SECTION: Root & General Routes
|--------------------------------------------------*/

// Root route - redirect to CRM login
Route::get('/', function() {
    return redirect()->route('crm.login');
});

// Cache clearing route - protected with authentication
Route::get('/clear-cache', function() {
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('route:cache');
    return response()->json([
        'success' => true,
        'message' => 'Cache cleared successfully'
    ]);
})->middleware('auth');

/*--------------------------------------------------
| SECTION: Exception Handling
|--------------------------------------------------*/
Route::get('/exception', [\App\Http\Controllers\ExceptionController::class, 'index'])->name('exception.index');
Route::post('/exception', [\App\Http\Controllers\ExceptionController::class, 'index'])->name('exception.store');

/*--------------------------------------------------
| SECTION: Authentication Routes
|--------------------------------------------------*/
// Auth::routes(); // Disabled - Using custom admin login at /admin and API login at /api/login instead

/*--------------------------------------------------
| SECTION: Email Manager Routes
|--------------------------------------------------*/
include_once 'emailUser.php';

/*--------------------------------------------------
| SECTION: Admin Console Routes
|--------------------------------------------------*/
require __DIR__ . '/adminconsole.php';

/*--------------------------------------------------
| SECTION: Authentication Routes
|--------------------------------------------------*/
// CRM authentication routes (no /admin prefix)
Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('crm.login');
Route::post('/login', [AdminLoginController::class, 'login'])->name('crm.login.post');
Route::post('/logout', [AdminLoginController::class, 'logout'])->name('crm.logout');
Route::get('/logout', function() {
    return redirect()->route('crm.login');
})->name('crm.logout.get');

/*--------------------------------------------------
| SECTION: CRM Application Routes (Protected)
|--------------------------------------------------*/
// Main CRM routes at root level with auth:admin middleware
Route::middleware(['auth:admin'])->group(function() {

	/*---------- Dashboard Routes ----------*/
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/column-preferences', [DashboardController::class, 'saveColumnPreferences'])->name('dashboard.column-preferences');
    Route::post('/dashboard/update-stage', [DashboardController::class, 'updateStage'])->name('dashboard.update-stage');
    Route::post('/dashboard/extend-deadline', [DashboardController::class, 'extendDeadlineDate'])->name('dashboard.extend-deadline');
    Route::post('/dashboard/update-task-completed', [DashboardController::class, 'updateTaskCompleted'])->name('dashboard.update-task-completed');
    Route::get('/dashboard/fetch-notifications', [AdminController::class, 'fetchnotification'])->name('dashboard.fetch-notifications');
    Route::get('/dashboard/fetch-office-visit-notifications', [AdminController::class, 'fetchOfficeVisitNotifications'])->name('dashboard.fetch-office-visit-notifications');
    Route::post('/dashboard/mark-notification-seen', [AdminController::class, 'markNotificationSeen'])->name('dashboard.mark-notification-seen');
    Route::get('/dashboard/fetch-visa-expiry-messages', [AdminController::class, 'fetchvisaexpirymessages'])->name('dashboard.fetch-visa-expiry-messages');
    Route::get('/dashboard/fetch-in-person-waiting-count', [AdminController::class, 'fetchInPersonWaitingCount'])->name('dashboard.fetch-in-person-waiting-count');
    Route::get('/dashboard/fetch-total-activity-count', [AdminController::class, 'fetchTotalActivityCount'])->name('dashboard.fetch-total-activity-count');
    Route::post('/dashboard/check-checkin-status', [DashboardController::class, 'checkCheckinStatus'])->name('dashboard.check-checkin-status');
    Route::post('/dashboard/update-checkin-status', [DashboardController::class, 'updateCheckinStatus'])->name('dashboard.update-checkin-status');

	/*---------- General Admin Routes ----------*/
    Route::get('/my_profile', [AdminController::class, 'myProfile'])->name('my_profile');
    Route::post('/my_profile', [AdminController::class, 'myProfile'])->name('my_profile.update');
    Route::get('/change_password', [AdminController::class, 'change_password'])->name('change_password');
    Route::post('/change_password', [AdminController::class, 'change_password'])->name('change_password.update');
    Route::post('/update_action', [AdminController::class, 'updateAction']);
    Route::post('/approved_action', [AdminController::class, 'approveAction']);
    Route::post('/process_action', [AdminController::class, 'processAction']);
    Route::post('/archive_action', [AdminController::class, 'archiveAction']);
    Route::post('/declined_action', [AdminController::class, 'declinedAction']);
    Route::post('/delete_action', [AdminController::class, 'deleteAction']);
    Route::post('/move_action', [AdminController::class, 'moveAction']);

    Route::get('/appointments-education', [AdminController::class, 'appointmentsEducation'])->name('appointments-education');
    Route::get('/appointments-jrp', [AdminController::class, 'appointmentsJrp'])->name('appointments-jrp');
    Route::get('/appointments-tourist', [AdminController::class, 'appointmentsTourist'])->name('appointments-tourist');
    Route::get('/appointments-others', [AdminController::class, 'appointmentsOthers'])->name('appointments-others');

    Route::post('/add_ckeditior_image', [AdminController::class, 'addCkeditiorImage'])->name('add_ckeditior_image');
    Route::post('/get_chapters', [AdminController::class, 'getChapters'])->name('get_chapters');
    Route::post('/get_states', [AdminController::class, 'getStates']);
    Route::get('/settings/taxes/returnsetting', [AdminController::class, 'returnsetting'])->name('returnsetting');
    Route::post('/settings/taxes/savereturnsetting', [AdminController::class, 'returnsetting'])->name('savereturnsetting');
    Route::get('/getsubcategories', [AdminController::class, 'getsubcategories']);
    Route::get('/getassigneeajax', [AdminController::class, 'getassigneeajax']);
    Route::get('/getpartnerajax', [AdminController::class, 'getpartnerajax']);
    Route::get('/checkclientexist', [AdminController::class, 'checkclientexist']);

	/*---------- CRM & User Management Routes ----------*/
    // All user management routes moved to routes/adminconsole.php
    // - Staff management: Use adminconsole.system.users routes
    // - User types/roles: Use adminconsole.system.roles routes

    /*---------- Leads Management (Modern Laravel Syntax) ----------*/
    // Lead CRUD operations
    Route::prefix('leads')->name('leads.')->group(function () {
        // List & Detail
        Route::get('/', [LeadController::class, 'index'])->name('index');
        Route::get('/detail/{id}', [LeadController::class, 'detail'])->name('detail');
        Route::get('/history/{id}', [LeadController::class, 'history'])->name('history');
        
        // Create
        Route::get('/create', [LeadController::class, 'create'])->name('create');
        Route::post('/store', [LeadController::class, 'store'])->name('store');
        
        // Edit & Update (RESTful pattern)
        Route::get('/{id}/edit', [LeadController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LeadController::class, 'update'])->name('update');
        Route::patch('/{id}', [LeadController::class, 'update'])->name('patch');
        
        // Assignment operations
        Route::post('/assign', [LeadAssignmentController::class, 'assign'])->name('assign');
        Route::post('/bulk-assign', [LeadAssignmentController::class, 'bulkAssign'])->name('bulk_assign');
        Route::get('/assignable-users', [LeadAssignmentController::class, 'getAssignableUsers'])->name('assignable_users');
        
        // Conversion operations
        Route::get('/convert', [LeadConversionController::class, 'convertToClient'])->name('convert');
        Route::post('/convert-single', [LeadConversionController::class, 'convertSingleLead'])->name('convert_single');
        Route::post('/bulk-convert', [LeadConversionController::class, 'bulkConvertToClient'])->name('bulk_convert');
        Route::get('/conversion-stats', [LeadConversionController::class, 'getConversionStats'])->name('conversion_stats');
        
        // Follow-up System
        Route::prefix('followups')->name('followups.')->group(function () {
            Route::get('/', [LeadFollowupController::class, 'index'])->name('index');
            Route::get('/dashboard', [LeadFollowupController::class, 'myFollowups'])->name('dashboard');
            Route::post('/', [LeadFollowupController::class, 'store'])->name('store');
            Route::post('/{id}/complete', [LeadFollowupController::class, 'complete'])->name('complete');
            Route::post('/{id}/reschedule', [LeadFollowupController::class, 'reschedule'])->name('reschedule');
            Route::post('/{id}/cancel', [LeadFollowupController::class, 'cancel'])->name('cancel');
        });
        Route::get('/{leadId}/followups', [LeadFollowupController::class, 'getLeadFollowups'])->name('followups.get');
        
        // Analytics (Admin/Team Lead only)
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/', [LeadAnalyticsController::class, 'index'])->name('index');
            Route::get('/trends', [LeadAnalyticsController::class, 'getTrends'])->name('trends');
            Route::get('/export', [LeadAnalyticsController::class, 'export'])->name('export');
            Route::post('/compare-agents', [LeadAnalyticsController::class, 'compareAgents'])->name('compare');
        });
        
        // Legacy routes (deprecated functionality)
        Route::get('/notes/delete/{id}', [LeadController::class, 'leaddeleteNotes'])->name('notes.delete');
        Route::get('/pin/{id}', [LeadController::class, 'leadPin'])->name('pin');
    });
    
    // Global route (outside leads prefix) - kept for backward compatibility
    Route::get('/get-notedetail', [LeadController::class, 'getnotedetail'])->name('get-notedetail');

	/*---------- Email Templates ----------*/
    Route::get('/email_templates', [EmailTemplateController::class, 'index'])->name('email.index');
    Route::get('/email_templates/create', [EmailTemplateController::class, 'create'])->name('email.create');
    Route::post('/email_templates/store', [EmailTemplateController::class, 'store'])->name('email.store');
    Route::get('/edit_email_template/{id}', [EmailTemplateController::class, 'editEmailTemplate'])->name('edit_email_template');
    Route::post('/edit_email_template', [EmailTemplateController::class, 'editEmailTemplate'])->name('edit_email_template.update');

	/*---------- API Settings ----------*/
    Route::get('/api-key', [AdminController::class, 'editapi'])->name('api');
    Route::post('/api-key', [AdminController::class, 'editapi'])->name('api.update');

	/*--------------------------------------------------
	| SECTION: Client Management Routes
	|--------------------------------------------------*/
	// All client routes moved to routes/clients.php
	// Includes: CRUD, documents, verification, invoices, EOI/ROI, notes, agreements
	require __DIR__ . '/clients.php';

	/*--------------------------------------------------
	| SECTION: Applications & Office Visits Routes
	|--------------------------------------------------*/
	// All application, office visit, and appointment routes moved to routes/applications.php
	require __DIR__ . '/applications.php';

	/*---------- Audit Logs ----------*/
	Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('auditlogs.index');

	/*---------- Notifications ----------*/
	Route::get('/fetch-notification', [AdminController::class, 'fetchnotification']);
	Route::get('/fetch-messages', [AdminController::class, 'fetchmessages']);
	Route::get('/fetch-office-visit-notifications', [AdminController::class, 'fetchOfficeVisitNotifications']);
	Route::post('/mark-notification-seen', [AdminController::class, 'markNotificationSeen']);
	Route::get('/check-checkin-status', [AdminController::class, 'checkCheckinStatus']);
	Route::post('/update-checkin-status', [AdminController::class, 'updateCheckinStatus']);
	Route::get('/all-notifications', [AdminController::class, 'allnotification']);
	Route::get('/fetch-InPersonWaitingCount', [AdminController::class, 'fetchInPersonWaitingCount']);
	Route::get('/fetch-TotalActivityCount', [AdminController::class, 'fetchTotalActivityCount']);

	/*---------- Assignee Module ----------*/
	Route::resource('/assignee', AssigneeController::class);
        Route::get('/assignee-completed', [AssigneeController::class, 'completed']); //completed list only

        Route::post('/update-task-completed', [AssigneeController::class, 'updatetaskcompleted']); //update task to be completed
        Route::post('/update-task-not-completed', [AssigneeController::class, 'updatetasknotcompleted']); //update task to be not completed

        Route::get('/assigned_by_me', [AssigneeController::class, 'assigned_by_me'])->name('assignee.assigned_by_me'); //assigned by me
        Route::get('/assigned_to_me', [AssigneeController::class, 'assigned_to_me'])->name('assignee.assigned_to_me'); //assigned to me

        Route::delete('/destroy_by_me/{note_id}', [AssigneeController::class, 'destroy_by_me'])->name('assignee.destroy_by_me'); //assigned by me
        Route::delete('/destroy_to_me/{note_id}', [AssigneeController::class, 'destroy_to_me'])->name('assignee.destroy_to_me'); //assigned to me
        Route::get('/action_completed', [AssigneeController::class, 'action_completed'])->name('assignee.action_completed'); //action completed


        Route::delete('/destroy_activity/{note_id}', [AssigneeController::class, 'destroy_activity'])->name('assignee.destroy_activity'); //delete activity
        Route::delete('/destroy_complete_activity/{note_id}', [AssigneeController::class, 'destroy_complete_activity'])->name('assignee.destroy_complete_activity'); //delete completed activity

	/*---------- Task Management ----------*/
	// Task routes for email and contact uniqueness
        Route::post('/is_email_unique', [LeadController::class, 'is_email_unique']);
        Route::post('/is_contactno_unique', [LeadController::class, 'is_contactno_unique']);

	// Activity management
        Route::post('/extenddeadlinedate', [AdminController::class, 'extenddeadlinedate']);
        Route::post('/update-stage', [AdminController::class, 'updateStage']);

	// Get assigne list
	Route::post('/get_assignee_list', [AssigneeController::class, 'get_assignee_list']);

	// Update task
        Route::post('/update-task', [AssigneeController::class, 'updateTask']);
        Route::get('/action/counts', [AssigneeController::class, 'getActionCounts'])->name('action.counts');

	// For datatable - Action list routes
	Route::get('/action', [AssigneeController::class, 'action'])->name('assignee.action');
	Route::get('/action/list', [AssigneeController::class, 'getAction'])->name('action.list');

	/*---------- End of Admin Routes ----------*/


	});

/*--------------------------------------------------
| SECTION: Document Signature Routes (Admin & Public)
|--------------------------------------------------*/
// Admin document management and public client signing
// Loaded outside admin group to allow proper prefix handling
require __DIR__ . '/documents.php';

/*--------------------------------------------------
| SECTION: Public Email Verification
|--------------------------------------------------*/
// Public email verification route loaded from clients.php

