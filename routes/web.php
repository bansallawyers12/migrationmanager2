<?php

use App\Http\Controllers\Admin\ClientsController;
use App\Http\Controllers\Admin\ClientEoiRoiController;
use App\Http\Controllers\AdminConsole\AnzscoOccupationController;
use App\Http\Controllers\Admin\Clients\ClientNotesController;
use App\Http\Controllers\Admin\ClientPersonalDetailsController;
use App\Http\Controllers\Admin\PhoneVerificationController;
use App\Http\Controllers\Admin\EmailVerificationController;
use App\Http\Controllers\Admin\Leads\LeadController;
use App\Http\Controllers\Admin\Leads\LeadAssignmentController;
use App\Http\Controllers\Admin\Leads\LeadConversionController;
use App\Http\Controllers\Admin\Leads\LeadFollowupController;
use App\Http\Controllers\Admin\Leads\LeadAnalyticsController;

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

// Root route - redirect to admin login
Route::get('/', function() {
    return redirect()->route('admin.login');
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
Route::get('/exception', 'ExceptionController@index')->name('exception.index');
Route::post('/exception', 'ExceptionController@index')->name('exception.store');

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
| SECTION: Admin Panel Routes
|--------------------------------------------------*/
Route::prefix('admin')->group(function() {

    /*---------- Login and Logout ----------*/
    Route::get('/', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::get('/login', 'Auth\AdminLoginController@showLoginForm');
    Route::post('/login', 'Auth\AdminLoginController@login')->name('admin.login.post');
    Route::post('/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');

	/*---------- Dashboard Routes ----------*/
    Route::get('/dashboard', 'Admin\DashboardController@index')->name('admin.dashboard');
    Route::post('/dashboard/column-preferences', 'Admin\DashboardController@saveColumnPreferences')->name('admin.dashboard.column-preferences');
    Route::post('/dashboard/update-stage', 'Admin\DashboardController@updateStage')->name('admin.dashboard.update-stage');
    Route::post('/dashboard/extend-deadline', 'Admin\DashboardController@extendDeadlineDate')->name('admin.dashboard.extend-deadline');
    Route::post('/dashboard/update-task-completed', 'Admin\DashboardController@updateTaskCompleted')->name('admin.dashboard.update-task-completed');
    Route::get('/dashboard/fetch-notifications', 'Admin\AdminController@fetchnotification')->name('admin.dashboard.fetch-notifications');
    Route::get('/dashboard/fetch-office-visit-notifications', 'Admin\AdminController@fetchOfficeVisitNotifications')->name('admin.dashboard.fetch-office-visit-notifications');
    Route::post('/dashboard/mark-notification-seen', 'Admin\AdminController@markNotificationSeen')->name('admin.dashboard.mark-notification-seen');
    Route::get('/dashboard/fetch-visa-expiry-messages', 'Admin\AdminController@fetchvisaexpirymessages')->name('admin.dashboard.fetch-visa-expiry-messages');
    Route::get('/dashboard/fetch-in-person-waiting-count', 'Admin\AdminController@fetchInPersonWaitingCount')->name('admin.dashboard.fetch-in-person-waiting-count');
    Route::get('/dashboard/fetch-total-activity-count', 'Admin\AdminController@fetchTotalActivityCount')->name('admin.dashboard.fetch-total-activity-count');
    Route::post('/dashboard/check-checkin-status', 'Admin\DashboardController@checkCheckinStatus')->name('admin.dashboard.check-checkin-status');
    Route::post('/dashboard/update-checkin-status', 'Admin\DashboardController@updateCheckinStatus')->name('admin.dashboard.update-checkin-status');

	/*---------- General Admin Routes ----------*/
    Route::get('/my_profile', 'Admin\AdminController@myProfile')->name('admin.my_profile');
    Route::post('/my_profile', 'Admin\AdminController@myProfile')->name('admin.my_profile.update');
    Route::get('/change_password', 'Admin\AdminController@change_password')->name('admin.change_password');
    Route::post('/change_password', 'Admin\AdminController@change_password')->name('admin.change_password.update');
    Route::post('/update_action', 'Admin\AdminController@updateAction');
    Route::post('/approved_action', 'Admin\AdminController@approveAction');
    Route::post('/process_action', 'Admin\AdminController@processAction');
    Route::post('/archive_action', 'Admin\AdminController@archiveAction');
    Route::post('/declined_action', 'Admin\AdminController@declinedAction');
    Route::post('/delete_action', 'Admin\AdminController@deleteAction');
    Route::post('/move_action', 'Admin\AdminController@moveAction');

    Route::get('/appointments-education', 'Admin\AdminController@appointmentsEducation')->name('appointments-education');
    Route::get('/appointments-jrp', 'Admin\AdminController@appointmentsJrp')->name('appointments-jrp');
    Route::get('/appointments-tourist', 'Admin\AdminController@appointmentsTourist')->name('appointments-tourist');
    Route::get('/appointments-others', 'Admin\AdminController@appointmentsOthers')->name('appointments-others');

    Route::post('/add_ckeditior_image', 'Admin\AdminController@addCkeditiorImage')->name('add_ckeditior_image');
    Route::post('/get_chapters', 'Admin\AdminController@getChapters')->name('admin.get_chapters');
    Route::post('/get_states', 'Admin\AdminController@getStates');
    Route::get('/settings/taxes/returnsetting', 'Admin\AdminController@returnsetting')->name('admin.returnsetting');
    Route::post('/settings/taxes/savereturnsetting', 'Admin\AdminController@returnsetting')->name('admin.savereturnsetting');
    Route::get('/getsubcategories', 'Admin\AdminController@getsubcategories');
    Route::get('/getassigneeajax', 'Admin\AdminController@getassigneeajax');
    Route::get('/getpartnerajax', 'Admin\AdminController@getpartnerajax');
    Route::get('/checkclientexist', 'Admin\AdminController@checkclientexist');

	/*---------- CRM & User Management Routes ----------*/
    // All user management routes moved to routes/adminconsole.php
    // - Staff management: Use adminconsole.system.users routes
    // - User types/roles: Use adminconsole.system.roles routes

    /*---------- Leads Management (Modern Laravel Syntax) ----------*/
    // Lead CRUD operations
    Route::prefix('leads')->name('admin.leads.')->group(function () {
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
    Route::get('/get-notedetail', [LeadController::class, 'getnotedetail'])->name('admin.get-notedetail');

	/*---------- Email Templates ----------*/
    Route::get('/email_templates', 'Admin\EmailTemplateController@index')->name('admin.email.index');
    Route::get('/email_templates/create', 'Admin\EmailTemplateController@create')->name('admin.email.create');
    Route::post('/email_templates/store', 'Admin\EmailTemplateController@store')->name('admin.email.store');
    Route::get('/edit_email_template/{id}', 'Admin\EmailTemplateController@editEmailTemplate')->name('admin.edit_email_template');
    Route::post('/edit_email_template', 'Admin\EmailTemplateController@editEmailTemplate')->name('admin.edit_email_template.update');

	/*---------- API Settings ----------*/
    Route::get('/api-key', 'Admin\AdminController@editapi')->name('admin.edit_api');
    Route::post('/api-key', 'Admin\AdminController@editapi')->name('admin.edit_api.update');

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
	Route::get('/audit-logs', 'Admin\AuditLogController@index')->name('admin.auditlogs.index');

	/*---------- Notifications ----------*/
	Route::get('/fetch-notification', 'Admin\AdminController@fetchnotification');
	Route::get('/fetch-messages', 'Admin\AdminController@fetchmessages');
	Route::get('/fetch-office-visit-notifications', 'Admin\AdminController@fetchOfficeVisitNotifications');
	Route::post('/mark-notification-seen', 'Admin\AdminController@markNotificationSeen');
	Route::get('/check-checkin-status', 'Admin\AdminController@checkCheckinStatus');
	Route::post('/update-checkin-status', 'Admin\AdminController@updateCheckinStatus');
	Route::get('/all-notifications', 'Admin\AdminController@allnotification');
	Route::get('/fetch-InPersonWaitingCount', 'Admin\AdminController@fetchInPersonWaitingCount');
	Route::get('/fetch-TotalActivityCount', 'Admin\AdminController@fetchTotalActivityCount');

	/*---------- Assignee Module ----------*/
	Route::resource('/assignee', Admin\AssigneeController::class);
        Route::get('/assignee-completed', 'Admin\AssigneeController@completed'); //completed list only

        Route::post('/update-task-completed', 'Admin\AssigneeController@updatetaskcompleted'); //update task to be completed
        Route::post('/update-task-not-completed', 'Admin\AssigneeController@updatetasknotcompleted'); //update task to be not completed

        Route::get('/assigned_by_me', 'Admin\AssigneeController@assigned_by_me')->name('assignee.assigned_by_me'); //assigned by me
        Route::get('/assigned_to_me', 'Admin\AssigneeController@assigned_to_me')->name('assignee.assigned_to_me'); //assigned to me

        Route::delete('/destroy_by_me/{note_id}', 'Admin\AssigneeController@destroy_by_me')->name('assignee.destroy_by_me'); //assigned by me
        Route::delete('/destroy_to_me/{note_id}', 'Admin\AssigneeController@destroy_to_me')->name('assignee.destroy_to_me'); //assigned to me
        Route::get('/action_completed', 'Admin\AssigneeController@action_completed')->name('assignee.action_completed'); //action completed


        Route::delete('/destroy_activity/{note_id}', 'Admin\AssigneeController@destroy_activity')->name('assignee.destroy_activity'); //delete activity
        Route::delete('/destroy_complete_activity/{note_id}', 'Admin\AssigneeController@destroy_complete_activity')->name('assignee.destroy_complete_activity'); //delete completed activity

	/*---------- Task Management ----------*/
	// Task routes for email and contact uniqueness
        Route::post('/is_email_unique', 'Admin\Leads\LeadController@is_email_unique');
        Route::post('/is_contactno_unique', 'Admin\Leads\LeadController@is_contactno_unique');

	// Activity management
        Route::post('/extenddeadlinedate', 'Admin\AdminController@extenddeadlinedate');
        Route::post('/update-stage', 'Admin\AdminController@updateStage');

	// Get assigne list
	Route::post('/get_assignee_list', 'Admin\AssigneeController@get_assignee_list');

	// Update task
        Route::post('/update-task', 'Admin\AssigneeController@updateTask');
        Route::get('/action/counts','Admin\AssigneeController@getActionCounts' )->name('action.counts');

	// For datatable - Action list routes
	Route::get('/action', 'Admin\AssigneeController@action')->name('assignee.action');
	Route::get('/action/list','Admin\AssigneeController@getAction')->name('action.list');

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

