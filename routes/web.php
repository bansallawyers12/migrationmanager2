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
Auth::routes();

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
    Route::get('/sessions', 'Admin\AdminController@sessions')->name('admin.sessions');
    Route::post('/sessions', 'Admin\AdminController@sessions')->name('admin.sessions.update');
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
    // User management routes moved to routes/adminconsole.php

    Route::get('/staff', 'Admin\StaffController@index')->name('admin.staff.index');
    Route::get('/staff/create', 'Admin\StaffController@create')->name('admin.staff.create');
    Route::post('/staff/store', 'Admin\StaffController@store')->name('admin.staff.store');
    Route::get('/staff/edit/{id}', 'Admin\StaffController@edit')->name('admin.staff.edit');
    Route::post('/staff/edit', 'Admin\StaffController@edit')->name('admin.staff.edit');

    Route::get('/usertype', 'Admin\UsertypeController@index')->name('admin.usertype.index');
    Route::get('/usertype/create', 'Admin\UsertypeController@create')->name('admin.usertype.create');
    Route::post('/usertype/store', 'Admin\UsertypeController@store')->name('admin.usertype.store');
    Route::get('/usertype/edit/{id}', 'Admin\UsertypeController@edit')->name('admin.usertype.edit');
    Route::post('/usertype/edit', 'Admin\UsertypeController@edit')->name('admin.usertype.edit');

    // User role routes moved to routes/adminconsole.php

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
    Route::post('/edit_email_template', 'Admin\EmailTemplateController@editEmailTemplate')->name('admin.edit_email_template');

	/*---------- API Settings ----------*/
    Route::get('/api-key', 'Admin\AdminController@editapi')->name('admin.edit_api');
    Route::post('/api-key', 'Admin\AdminController@editapi')->name('admin.edit_api');

		/*---------- Clients Management ----------*/
		Route::get('/clients', [ClientsController::class, 'index'])->name('admin.clients.index');
        Route::get('/clientsmatterslist', [ClientsController::class, 'clientsmatterslist'])->name('admin.clients.clientsmatterslist');
        Route::get('/clientsemaillist', [ClientsController::class, 'clientsemaillist'])->name('admin.clients.clientsemaillist');
		Route::post('/clients/store', [ClientsController::class, 'store'])->name('admin.clients.store');
		Route::get('/clients/edit/{id}', [ClientsController::class, 'edit'])->name('admin.clients.edit');
		Route::post('/clients/edit', [ClientsController::class, 'edit'])->name('admin.clients.update');
		Route::post('/clients/save-section', [ClientPersonalDetailsController::class, 'saveSection'])->name('admin.clients.saveSection');
		Route::get('/clients/partner-eoi-data/{partnerId}', [ClientPersonalDetailsController::class, 'getPartnerEoiData'])->name('admin.clients.partnerEoiData');

        // Phone Verification Routes
        Route::prefix('clients/phone')->name('clients.phone.')->group(function () {
            Route::post('/send-otp', [PhoneVerificationController::class, 'sendOTP'])->name('sendOTP');
            Route::post('/verify-otp', [PhoneVerificationController::class, 'verifyOTP'])->name('verifyOTP');
            Route::post('/resend-otp', [PhoneVerificationController::class, 'resendOTP'])->name('resendOTP');
            Route::get('/status/{contactId}', [PhoneVerificationController::class, 'getStatus'])->name('status');
        });

        // Email Verification Routes
        Route::prefix('clients/email')->name('clients.email.')->group(function () {
            Route::post('/send-verification', [EmailVerificationController::class, 'sendVerificationEmail'])->name('sendVerification');
            Route::post('/resend-verification', [EmailVerificationController::class, 'resendVerificationEmail'])->name('resendVerification');
            Route::get('/status/{emailId}', [EmailVerificationController::class, 'getStatus'])->name('status');
        });

        Route::post('/clients/followup/store', [ClientsController::class, 'followupstore']);


		Route::post('/clients/followup/retagfollowup', [ClientsController::class, 'retagfollowup']);
		Route::get('/clients/changetype/{id}/{type}', [ClientsController::class, 'changetype']);
		Route::get('/document/download/pdf/{id}', [ClientsController::class, 'downloadpdf']);
		Route::get('/clients/removetag', [ClientsController::class, 'removetag']);
		Route::get('/clients/detail/{client_id}/{client_unique_matter_ref_no?}/{tab?}', [ClientsController::class, 'detail'])->name('admin.clients.detail');
		
        Route::get('/clients/get-recipients', [ClientsController::class, 'getrecipients'])->name('admin.clients.getrecipients');
		Route::get('/clients/get-onlyclientrecipients', [ClientsController::class, 'getonlyclientrecipients'])->name('admin.clients.getonlyclientrecipients');
		Route::get('/clients/get-allclients', [ClientsController::class, 'getallclients'])->name('admin.clients.getallclients');
		Route::get('/clients/change_assignee', [ClientsController::class, 'change_assignee']);
		Route::get('/get-templates', 'Admin\AdminController@gettemplates')->name('admin.clients.gettemplates');
		Route::post('/sendmail', 'Admin\AdminController@sendmail')->name('admin.clients.sendmail');
		Route::post('/create-note', [ClientNotesController::class, 'createnote'])->name('admin.clients.createnote');
		Route::post('/update-note-datetime', [ClientNotesController::class, 'updateNoteDatetime'])->name('admin.clients.updateNoteDatetime');
		Route::get('/getnotedetail', [ClientNotesController::class, 'getnotedetail'])->name('admin.clients.getnotedetail');
		Route::get('/deletenote', [ClientNotesController::class, 'deletenote'])->name('admin.clients.deletenote');
		Route::get('/deletecostagreement', [ClientsController::class, 'deletecostagreement'])->name('admin.clients.deletecostagreement');
        Route::get('/deleteactivitylog', [ClientsController::class, 'deleteactivitylog'])->name('admin.clients.deleteactivitylog');
        Route::post('/not-picked-call', [ClientsController::class, 'notpickedcall'])->name('admin.clients.notpickedcall');
		Route::get('/viewnotedetail', [ClientNotesController::class, 'viewnotedetail']);
		Route::get('/viewapplicationnote', [ClientNotesController::class, 'viewapplicationnote']);
		Route::post('/saveprevvisa', [ClientNotesController::class, 'saveprevvisa']);
		Route::post('/saveonlineprimaryform', [ClientNotesController::class, 'saveonlineform']);
		Route::post('/saveonlinesecform', [ClientNotesController::class, 'saveonlineform']);
		Route::post('/saveonlinechildform', [ClientNotesController::class, 'saveonlineform']);
		//archived Start
		Route::get('/archived', 'Admin\ClientsController@archived')->name('admin.clients.archived');
		Route::get('/change-client-status', 'Admin\ClientsController@updateclientstatus')->name('admin.clients.updateclientstatus');
		Route::get('/get-activities', 'Admin\ClientsController@activities')->name('admin.clients.activities');
		Route::get('/get-application-lists', 'Admin\ClientsController@getapplicationlists')->name('admin.clients.getapplicationlists');
		Route::post('/saveapplication', 'Admin\ClientsController@saveapplication')->name('admin.clients.saveapplication');
		Route::get('/get-notes', [ClientNotesController::class, 'getnotes'])->name('admin.clients.getnotes');
		Route::get('/convertapplication', 'Admin\ClientsController@convertapplication')->name('admin.clients.convertapplication');
		Route::get('/deleteservices', 'Admin\ClientsController@deleteservices')->name('admin.clients.deleteservices');
        
        /*---------- Client Documents Management (NEW CONTROLLER - Laravel 12 Syntax) ----------*/
        Route::post('/documents/add-edu-checklist', [\App\Http\Controllers\Admin\Clients\ClientDocumentsController::class, 'addedudocchecklist'])->name('admin.clients.documents.addedudocchecklist');
        Route::post('/documents/upload-edu-document', [\App\Http\Controllers\Admin\Clients\ClientDocumentsController::class, 'uploadedudocument'])->name('admin.clients.documents.uploadedudocument');
        Route::post('/documents/add-visa-checklist', [\App\Http\Controllers\Admin\Clients\ClientDocumentsController::class, 'addvisadocchecklist'])->name('admin.clients.documents.addvisadocchecklist');
        Route::post('/documents/upload-visa-document', [\App\Http\Controllers\Admin\Clients\ClientDocumentsController::class, 'uploadvisadocument'])->name('admin.clients.documents.uploadvisadocument');
        Route::post('/documents/rename', [\App\Http\Controllers\Admin\Clients\ClientDocumentsController::class, 'renamedoc'])->name('admin.clients.documents.renamedoc');
        Route::get('/documents/delete', [\App\Http\Controllers\Admin\Clients\ClientDocumentsController::class, 'deletedocs'])->name('admin.clients.documents.deletedocs');
        Route::get('/documents/get-visa-checklist', [\App\Http\Controllers\Admin\Clients\ClientDocumentsController::class, 'getvisachecklist'])->name('admin.clients.documents.getvisachecklist');
        Route::post('/documents/not-used', [\App\Http\Controllers\Admin\Clients\ClientDocumentsController::class, 'notuseddoc'])->name('admin.clients.documents.notuseddoc');
        Route::post('/documents/rename-checklist', [\App\Http\Controllers\Admin\Clients\ClientDocumentsController::class, 'renamechecklistdoc'])->name('admin.clients.documents.renamechecklistdoc');
        Route::post('/documents/back-to-doc', [\App\Http\Controllers\Admin\Clients\ClientDocumentsController::class, 'backtodoc'])->name('admin.clients.documents.backtodoc');
        Route::post('/documents/download', [\App\Http\Controllers\Admin\Clients\ClientDocumentsController::class, 'download_document'])->name('admin.clients.documents.download');
        Route::post('/documents/add-personal-category', [\App\Http\Controllers\Admin\Clients\ClientDocumentsController::class, 'addPersonalDocCategory'])->name('admin.clients.documents.addPersonalDocCategory');
        Route::post('/documents/update-personal-category', [\App\Http\Controllers\Admin\Clients\ClientDocumentsController::class, 'updatePersonalDocCategory'])->name('admin.clients.documents.updatePersonalDocCategory');
        Route::post('/documents/add-visa-category', [\App\Http\Controllers\Admin\Clients\ClientDocumentsController::class, 'addVisaDocCategory'])->name('admin.clients.documents.addVisaDocCategory');
        Route::post('/documents/update-visa-category', [\App\Http\Controllers\Admin\Clients\ClientDocumentsController::class, 'updateVisaDocCategory'])->name('admin.clients.documents.updateVisaDocCategory');
        
        /*---------- Client EOI/ROI Management (Laravel 12 Syntax) ----------*/
        Route::prefix('clients/{admin}/eoi-roi')->name('admin.clients.eoi-roi.')->group(function () {
            Route::get('/', [ClientEoiRoiController::class, 'index'])->name('index');
            Route::get('/calculate-points', [ClientEoiRoiController::class, 'calculatePoints'])->name('calculatePoints');
            Route::post('/', [ClientEoiRoiController::class, 'upsert'])->name('upsert');
            Route::get('/{eoiReference}', [ClientEoiRoiController::class, 'show'])->name('show');
            Route::delete('/{eoiReference}', [ClientEoiRoiController::class, 'destroy'])->name('destroy');
            Route::get('/{eoiReference}/reveal-password', [ClientEoiRoiController::class, 'revealPassword'])->name('revealPassword');
        });
        
		Route::post('/savetoapplication', 'Admin\ClientsController@savetoapplication');

		/*---------- Branch Management ----------*/
		// Branch routes moved to routes/adminconsole.php

		/*---------- Applications Management ----------*/
		Route::get('/applications/detail/{id}', 'Admin\ApplicationsController@detail')->name('admin.applications.detail');
		Route::post('/interested-service', 'Admin\ClientsController@interestedService');
		Route::post('/edit-interested-service', 'Admin\ClientsController@editinterestedService');
		Route::get('/get-services', 'Admin\ClientsController@getServices');
		Route::post('/servicesavefee', 'Admin\ClientsController@servicesavefee');

		

		Route::post('/upload-mail', 'Admin\ClientsController@uploadmail');
        Route::post('/upload-fetch-mail', 'Admin\ClientsController@uploadfetchmail'); //upload inbox email
        Route::post('/upload-sent-fetch-mail', 'Admin\ClientsController@uploadsentfetchmail'); //upload sent email


        Route::get('/deleteappointment', 'Admin\AppointmentsController@deleteappointment');
		Route::post('/add-appointment', 'Admin\AppointmentsController@addAppointment');
        Route::post('/add-appointment-book', 'Admin\AppointmentsController@addAppointmentBook');
		Route::post('/editappointment', 'Admin\AppointmentsController@editappointment');
        
        Route::post('/updatefollowupschedule', 'Admin\AppointmentsController@updatefollowupschedule');
		Route::get('/updateappointmentstatus/{status}/{id}', 'Admin\AppointmentsController@updateappointmentstatus');
		Route::get('/get-appointments', 'Admin\AppointmentsController@getAppointments');
        Route::get('/getAppointmentdetail', 'Admin\AppointmentsController@getAppointmentdetail');



        Route::get('/pinnote', [ClientNotesController::class, 'pinnote']);
        Route::get('/pinactivitylog', 'Admin\ClientsController@pinactivitylog');

		Route::get('/getintrestedservice', 'Admin\ClientsController@getintrestedservice');
		Route::get('/getintrestedserviceedit', 'Admin\ClientsController@getintrestedserviceedit');
		
		Route::get('/getapplicationdetail', 'Admin\ApplicationsController@getapplicationdetail');
		Route::post('/load-application-insert-update-data', 'Admin\ApplicationsController@loadApplicationInsertUpdateData');
		Route::get('/updatestage', 'Admin\ApplicationsController@updatestage');
		Route::get('/completestage', 'Admin\ApplicationsController@completestage');
		Route::get('/updatebackstage', 'Admin\ApplicationsController@updatebackstage');
		Route::get('/get-applications-logs', 'Admin\ApplicationsController@getapplicationslogs');
		Route::get('/get-applications', 'Admin\ApplicationsController@getapplications');
		Route::post('/create-app-note', 'Admin\ApplicationsController@addNote');
		Route::get('/getapplicationnotes', 'Admin\ApplicationsController@getapplicationnotes');
		Route::post('/application-sendmail', 'Admin\ApplicationsController@applicationsendmail');
		Route::get('/application/updateintake', 'Admin\ApplicationsController@updateintake');
		Route::get('/application/updatedates', 'Admin\ApplicationsController@updatedates');
		Route::get('/application/updateexpectwin', 'Admin\ApplicationsController@updateexpectwin');
		Route::get('/application/getapplicationbycid', 'Admin\ApplicationsController@getapplicationbycid');
		Route::post('/application/spagent_application', 'Admin\ApplicationsController@spagent_application');
		Route::post('/application/sbagent_application', 'Admin\ApplicationsController@sbagent_application');
		Route::post('/application/application_ownership', 'Admin\ApplicationsController@application_ownership');
		Route::get('/superagent', 'Admin\ApplicationsController@superagent');
		Route::get('/subagent', 'Admin\ApplicationsController@subagent');
		Route::post('/applicationsavefee', 'Admin\ApplicationsController@applicationsavefee');
		Route::get('/application/export/pdf/{id}', 'Admin\ApplicationsController@exportapplicationpdf');
		Route::post('/add-checklists', 'Admin\ApplicationsController@addchecklists');
		Route::post('/application/checklistupload', 'Admin\ApplicationsController@checklistupload');
		Route::get('/deleteapplicationdocs', 'Admin\ApplicationsController@deleteapplicationdocs');
		Route::get('/application/publishdoc', 'Admin\ApplicationsController@publishdoc');

		/*---------- Checklist Management ----------*/
		Route::get('/checklist', 'Admin\ChecklistController@index')->name('admin.checklist.index');
		Route::get('/checklist/create', 'Admin\ChecklistController@create')->name('admin.checklist.create');
		Route::post('checklist/store', 'Admin\ChecklistController@store')->name('admin.checklist.store');
		Route::get('/checklist/edit/{id}', 'Admin\ChecklistController@edit')->name('admin.checklist.edit');
		Route::post('/checklist/edit', 'Admin\ChecklistController@edit')->name('admin.checklist.edit');

		/*---------- Applications & Office Visits ----------*/
		Route::get('/applications', 'Admin\ApplicationsController@index')->name('admin.applications.index');
		Route::get('/applications/create', 'Admin\ApplicationsController@create')->name('admin.applications.create');
		Route::post('/discontinue_application', 'Admin\ApplicationsController@discontinue_application');
		Route::post('/revert_application', 'Admin\ApplicationsController@revert_application');
		Route::post('/applications-import', 'Admin\ApplicationsController@import')->name('admin.applications.import');
		Route::get('/migration', 'Admin\ApplicationsController@migrationindex')->name('admin.migration.index');
		Route::get('/office-visits', 'Admin\OfficeVisitController@index')->name('admin.officevisits.index');
		Route::get('/office-visits/waiting', 'Admin\OfficeVisitController@waiting')->name('admin.officevisits.waiting');
		Route::get('/office-visits/attending', 'Admin\OfficeVisitController@attending')->name('admin.officevisits.attending');
		Route::get('/office-visits/completed', 'Admin\OfficeVisitController@completed')->name('admin.officevisits.completed');
		Route::get('/office-visits/archived', 'Admin\OfficeVisitController@archived')->name('admin.officevisits.archived');
		Route::get('/office-visits/create', 'Admin\OfficeVisitController@create')->name('admin.officevisits.create');
		Route::post('/checkin', 'Admin\OfficeVisitController@checkin');
		Route::get('/get-checkin-detail', 'Admin\OfficeVisitController@getcheckin');
		Route::post('/update_visit_purpose', 'Admin\OfficeVisitController@update_visit_purpose');
		Route::post('/update_visit_comment', 'Admin\OfficeVisitController@update_visit_comment');
		Route::post('/attend_session', 'Admin\OfficeVisitController@attend_session');
		Route::post('/complete_session', 'Admin\OfficeVisitController@complete_session');
		Route::get('/office-visits/change_assignee', 'Admin\OfficeVisitController@change_assignee');


		//Audit Logs Start
		Route::get('/audit-logs', 'Admin\AuditLogController@index')->name('admin.auditlogs.index');



		Route::post('/save_tag', 'Admin\ClientsController@save_tag');


		// Email routes moved to routes/adminconsole.php
		



		// Crm Email Template routes moved to routes/adminconsole.php


		Route::get('/fetch-notification', 'Admin\AdminController@fetchnotification');
		Route::get('/fetch-messages', 'Admin\AdminController@fetchmessages');

		//In-Person Notification related routes start
		Route::get('/fetch-office-visit-notifications', 'Admin\AdminController@fetchOfficeVisitNotifications');
		Route::post('/mark-notification-seen', 'Admin\AdminController@markNotificationSeen');
		Route::get('/check-checkin-status', 'Admin\AdminController@checkCheckinStatus');
		Route::post('/update-checkin-status', 'Admin\AdminController@updateCheckinStatus');
		//In-Person Notification related routes end

		// Teams routes moved to routes/adminconsole.php
		
		Route::get('/all-notifications', 'Admin\AdminController@allnotification');

		// Appointment modulle
		Route::resource('appointments', Admin\AppointmentsController::class);
		Route::get('/get-assigne-detail', 'Admin\AppointmentsController@assignedetail');
		Route::post('/update_appointment_status', 'Admin\AppointmentsController@update_appointment_status');
		Route::post('/update_appointment_priority', 'Admin\AppointmentsController@update_appointment_priority');
		Route::get('/change_assignee', 'Admin\AppointmentsController@change_assignee');
		Route::post('/update_apppointment_comment', 'Admin\AppointmentsController@update_apppointment_comment');
		Route::post('/update_apppointment_description', 'Admin\AppointmentsController@update_apppointment_description');

		// Assignee modulle
		Route::resource('/assignee', Admin\AssigneeController::class);
        Route::get('/assignee-completed', 'Admin\AssigneeController@completed'); //completed list only

        Route::post('/update-task-completed', 'Admin\AssigneeController@updatetaskcompleted'); //update task to be completed
        Route::post('/update-task-not-completed', 'Admin\AssigneeController@updatetasknotcompleted'); //update task to be not completed

        Route::get('/assigned_by_me', 'Admin\AssigneeController@assigned_by_me')->name('assignee.assigned_by_me'); //assigned by me
        Route::get('/assigned_to_me', 'Admin\AssigneeController@assigned_to_me')->name('assignee.assigned_to_me'); //assigned to me

        Route::delete('/destroy_by_me/{note_id}', 'Admin\AssigneeController@destroy_by_me')->name('assignee.destroy_by_me'); //assigned by me
        Route::delete('/destroy_to_me/{note_id}', 'Admin\AssigneeController@destroy_to_me')->name('assignee.destroy_to_me'); //assigned to me
        Route::get('/activities_completed', 'Admin\AssigneeController@activities_completed')->name('assignee.activities_completed'); //activities completed


        Route::delete('/destroy_activity/{note_id}', 'Admin\AssigneeController@destroy_activity')->name('assignee.destroy_activity'); //delete activity
        Route::delete('/destroy_complete_activity/{note_id}', 'Admin\AssigneeController@destroy_complete_activity')->name('assignee.destroy_complete_activity'); //delete completed activity

        //Save Personal Task
        Route::post('/clients/personalfollowup/store', 'Admin\ClientsController@personalfollowup');
        Route::post('/clients/updatefollowup/store', 'Admin\ClientsController@updatefollowup');
        Route::post('/clients/reassignfollowup/store', 'Admin\ClientsController@reassignfollowupstore');

        //update attending session to be completed
        Route::post('/clients/update-session-completed', 'Admin\ClientsController@updatesessioncompleted')->name('admin.clients.updatesessioncompleted');

        //Total person waiting and total activity counter
        Route::get('/fetch-InPersonWaitingCount', 'Admin\AdminController@fetchInPersonWaitingCount');
        Route::get('/fetch-TotalActivityCount', 'Admin\AdminController@fetchTotalActivityCount');

        Route::post('/clients/getAllUser', 'Admin\ClientsController@getAllUser')->name('admin.clients.getAllUser');


        //for datatble
        Route::get('/activities', 'Admin\AssigneeController@activities')->name('assignee.activities');;
        Route::get('/activities/list','Admin\AssigneeController@getActivities')->name('activities.list');


        Route::post('/get_assignee_list', 'Admin\AssigneeController@get_assignee_list');

        //For email and contact number uniqueness
        Route::post('/is_email_unique', 'Admin\Leads\LeadController@is_email_unique');
        Route::post('/is_contactno_unique', 'Admin\Leads\LeadController@is_contactno_unique');

        //merge records
        Route::post('/merge_records','Admin\ClientsController@merge_records')->name('client.merge_records');



        // Appointment Dates Not Available routes moved to routes/adminconsole.php



        // Personal Document Category routes moved to routes/adminconsole.php


        // Visa Document Category routes moved to routes/adminconsole.php



        // Matter routes moved to routes/adminconsole.php

        Route::post('/address_auto_populate', 'Admin\ClientsController@address_auto_populate');

        Route::post('/client/createservicetaken', 'Admin\ClientsController@createservicetaken');
        Route::post('/client/removeservicetaken', 'Admin\ClientsController@removeservicetaken');
        Route::post('/client/getservicetaken', 'Admin\ClientsController@getservicetaken');


        //Account Receipts section
        Route::get('/clients/saveaccountreport/{id}', 'Admin\ClientsController@saveaccountreport')->name('admin.clients.saveaccountreport');
		Route::post('/clients/saveaccountreport', 'Admin\ClientsController@saveaccountreport')->name('admin.clients.saveaccountreport');

        Route::get('/clients/saveinvoicereport/{id}', 'Admin\ClientsController@saveinvoicereport')->name('admin.clients.saveinvoicereport');
		Route::post('/clients/saveinvoicereport', 'Admin\ClientsController@saveinvoicereport')->name('admin.clients.saveinvoicereport');

        Route::get('/clients/saveadjustinvoicereport/{id}', 'Admin\ClientsController@saveadjustinvoicereport')->name('admin.clients.saveadjustinvoicereport');
		Route::post('/clients/saveadjustinvoicereport', 'Admin\ClientsController@saveadjustinvoicereport')->name('admin.clients.saveadjustinvoicereport');

        Route::get('/clients/saveofficereport/{id}', 'Admin\ClientsController@saveofficereport')->name('admin.clients.saveofficereport');
		Route::post('/clients/saveofficereport', 'Admin\ClientsController@saveofficereport')->name('admin.clients.saveofficereport');

        Route::get('/clients/savejournalreport/{id}', 'Admin\ClientsController@savejournalreport')->name('admin.clients.savejournalreport');
		Route::post('/clients/savejournalreport', 'Admin\ClientsController@savejournalreport')->name('admin.clients.savejournalreport');


        Route::post('/clients/isAnyInvoiceNoExistInDB', 'Admin\ClientsController@isAnyInvoiceNoExistInDB')->name('admin.clients.isAnyInvoiceNoExistInDB');
        Route::post('/clients/listOfInvoice', 'Admin\ClientsController@listOfInvoice')->name('admin.clients.listOfInvoice');
        Route::post('/clients/getTopReceiptValInDB', 'Admin\ClientsController@getTopReceiptValInDB')->name('admin.clients.getTopReceiptValInDB');
        Route::post('/clients/getInfoByReceiptId', 'Admin\ClientsController@getInfoByReceiptId')->name('admin.clients.getInfoByReceiptId');
        Route::get('/clients/genInvoice/{id}', 'Admin\ClientsController@genInvoice');
        Route::post('/clients/sendToHubdoc/{id}', 'Admin\ClientsController@sendToHubdoc')->name('admin.clients.sendToHubdoc');
        Route::get('/clients/checkHubdocStatus/{id}', 'Admin\ClientsController@checkHubdocStatus')->name('admin.clients.checkHubdocStatus');
		Route::get('/clients/printPreview/{id}', 'Admin\ClientsController@printPreview'); //Client receipt print preview
		Route::post('/clients/getTopInvoiceNoFromDB', 'Admin\ClientsController@getTopInvoiceNoFromDB')->name('admin.clients.getTopInvoiceNoFromDB');
        Route::post('/clients/clientLedgerBalanceAmount', 'Admin\ClientsController@clientLedgerBalanceAmount')->name('admin.clients.clientLedgerBalanceAmount');

        Route::get('/clients/invoicelist', 'Admin\ClientsController@invoicelist')->name('admin.clients.invoicelist');
        Route::post('/void_invoice','Admin\ClientsController@void_invoice')->name('client.void_invoice');
        Route::get('/clients/clientreceiptlist', 'Admin\ClientsController@clientreceiptlist')->name('admin.clients.clientreceiptlist');
        Route::get('/clients/officereceiptlist', 'Admin\ClientsController@officereceiptlist')->name('admin.clients.officereceiptlist');
        Route::get('/clients/journalreceiptlist', 'Admin\ClientsController@journalreceiptlist')->name('admin.clients.journalreceiptlist');
        Route::post('/validate_receipt','Admin\ClientsController@validate_receipt')->name('client.validate_receipt');

        Route::post('/clients/update-address', [ClientPersonalDetailsController::class, 'updateAddress'])->name('admin.clients.updateAddress');
        Route::post('/clients/search-address-full', [ClientPersonalDetailsController::class, 'searchAddressFull'])->name('admin.clients.searchAddressFull');
        Route::post('/clients/get-place-details', [ClientPersonalDetailsController::class, 'getPlaceDetails'])->name('admin.clients.getPlaceDetails');

        //Fetch all contact list of any client at create note popup
        Route::post('/clients/fetchClientContactNo', [ClientPersonalDetailsController::class, 'fetchClientContactNo']);

        Route::post('/clients/clientdetailsinfo/{id}', [ClientPersonalDetailsController::class, 'clientdetailsinfo'])->name('admin.clients.clientdetailsinfo');
        Route::post('/clients/clientdetailsinfo', [ClientPersonalDetailsController::class, 'clientdetailsinfo'])->name('admin.clients.clientdetailsinfo');


        Route::post('/reassiginboxemail', 'Admin\ClientsController@reassiginboxemail')->name('admin.clients.reassiginboxemail');
        Route::post('/reassigsentemail', 'Admin\ClientsController@reassigsentemail')->name('admin.clients.reassigsentemail');

        //Fetch selected client all matters at assign email to user popup
        Route::post('/listAllMattersWRTSelClient', 'Admin\ClientsController@listAllMattersWRTSelClient')->name('admin.clients.listAllMattersWRTSelClient');

        //Get visa checklist - route moved to /documents/ prefix (line 225)




        Route::post('/extenddeadlinedate', 'Admin\AdminController@extenddeadlinedate');

        Route::post('/leads/updateOccupation', [ClientPersonalDetailsController::class, 'updateOccupation'])->name('admin.leads.updateOccupation');

        /*---------- ANZSCO Occupation Database ----------*/
        // ANZSCO routes moved to routes/adminconsole.php
        // Search route for client forms
        Route::get('/anzsco/search', [AnzscoOccupationController::class, 'search'])->name('admin.anzsco.search');

        //Document Checklist Start
		// Document checklist routes moved to routes/adminconsole.php

        //Personal and Visa Document routes moved to /documents/ prefix (lines 219-222)

        Route::post('/check-email', 'Admin\ClientsController@checkEmail')->name('check.email');
        Route::post('/check.phone', 'Admin\ClientsController@checkContact')->name('check.phone');

        //Document Not Use Tab - routes moved to /documents/ prefix (lines 226-227)
        //inbox preview click update mail_is_read bit
        Route::post('/updatemailreadbit', 'Admin\ClientsController@updatemailreadbit')->name('admin.clients.updatemailreadbit');

        //Back To Document - route moved to /documents/ prefix (line 228)

        //Ajax change on workflow status change
        Route::post('/update-stage', 'Admin\AdminController@updateStage');

        Route::post('/mail/enhance', 'Admin\ClientsController@enhanceMessage')->name('admin.mail.enhance');

		Route::post('/clients/filter-emails', 'Admin\ClientsController@filterEmails')->name('admin.clients.filter.emails');
		Route::post('/clients/filter-sentemails', 'Admin\ClientsController@filterSentEmails')->name('admin.clients.filter.sentmails');

        Route::get('/admin/get-visa-types', [ClientPersonalDetailsController::class, 'getVisaTypes'])->name('admin.getVisaTypes');
        Route::get('/admin/get-countries', [ClientPersonalDetailsController::class, 'getCountries'])->name('admin.getCountries');
        Route::post('/updateOccupation', [ClientPersonalDetailsController::class, 'updateOccupation'])->name('admin.clients.updateOccupation');


        Route::get('/clients/genClientFundLedgerInvoice/{id}', 'Admin\ClientsController@genClientFundLedgerInvoice');
        Route::get('/clients/genofficereceiptInvoice/{id}', 'Admin\ClientsController@genofficereceiptInvoice');


        Route::post('/update-client-funds-ledger', 'Admin\ClientsController@updateClientFundsLedger')->name('admin.clients.update-client-funds-ledger');

        Route::post('/update-task', 'Admin\AssigneeController@updateTask');
        Route::get('/activities/counts','Admin\AssigneeController@getActivityCounts' )->name('activities.counts');


        Route::post('/clients/invoiceamount', 'Admin\ClientsController@getInvoiceAmount')->name('admin.clients.invoiceamount');



        Route::post('/admin/clients/search-partner', [ClientPersonalDetailsController::class, 'searchPartner'])->name('admin.clients.searchPartner');
        Route::get('/admin/clients/search-partner-test', [ClientPersonalDetailsController::class, 'searchPartnerTest'])->name('admin.clients.searchPartnerTest');
        Route::get('/admin/clients/test-bidirectional', [ClientPersonalDetailsController::class, 'testBidirectionalRemoval'])->name('admin.clients.testBidirectional');
        Route::post('/admin/clients/save-relationship', [ClientPersonalDetailsController::class, 'saveRelationship'])->name('admin.clients.saveRelationship');


        //Client receipt delete by Celesty
        Route::post('/delete_receipt','Admin\ClientsController@delete_receipt');
        //Download Document - route moved to /documents/ prefix (line 229)

        //Form 965
        Route::post('/admin/forms', 'Admin\Form956Controller@store')->name('forms.store');
		Route::get('/admin/forms/{form}', 'Admin\Form956Controller@show')->name('forms.show');
        Route::get('/forms/{form}/preview', 'Admin\Form956Controller@previewPdf')->name('forms.preview');
        Route::get('/forms/{form}/pdf', 'Admin\Form956Controller@generatePdf')->name('forms.pdf');

        //Show visa expiry message
        Route::get('/fetch-visa_expiry_messages', 'Admin\AdminController@fetchvisaexpirymessages');
		//Create agreement
		Route::post('/clients/generateagreement', 'Admin\ClientsController@generateagreement')->name('clients.generateagreement');
        Route::post('/clients/getMigrationAgentDetail', 'Admin\ClientsController@getMigrationAgentDetail')->name('admin.clients.getMigrationAgentDetail');
		Route::post('/clients/getVisaAggreementMigrationAgentDetail', 'Admin\ClientsController@getVisaAggreementMigrationAgentDetail')->name('admin.clients.getVisaAggreementMigrationAgentDetail');
        Route::post('/clients/getCostAssignmentMigrationAgentDetail', 'Admin\ClientsController@getCostAssignmentMigrationAgentDetail')->name('admin.clients.getCostAssignmentMigrationAgentDetail');
        //Save cost assignment
        Route::post('/clients/savecostassignment', 'Admin\ClientsController@savecostassignment')->name('clients.savecostassignment');

        //save reference
        Route::post('/save-references', 'Admin\ClientsController@savereferences')->name('references.store');
        //check star client
        Route::post('/check-star-client', 'Admin\ClientsController@checkStarClient')->name('check.star.client');
        //Fetch client matter assignee
        Route::post('/clients/fetchClientMatterAssignee', [ClientPersonalDetailsController::class, 'fetchClientMatterAssignee']);
        //Save client matter assignee
        Route::post('/clients/updateClientMatterAssignee', [ClientPersonalDetailsController::class, 'updateClientMatterAssignee']);

        //Document Category Management - routes moved to /documents/ prefix (lines 230-233)

        //Send summary page code to webhook
        Route::post('/send-webhook', 'Admin\ClientsController@sendToWebhook')->name('admin.send-webhook');
        //Check cost assignment is exist or not
        Route::post('/clients/check-cost-assignment', 'Admin\ClientsController@checkCostAssignment');


       

        Route::get('/sign/{id}/{token}', 'Admin\DocumentController@sign')->name('documents.sign');
		Route::get('/documents/{id?}', 'Admin\DocumentController@index')->name('documents.index');
  
        //Lead Save cost assignment
        Route::post('/clients/savecostassignmentlead', 'Admin\ClientsController@savecostassignmentlead')->name('clients.savecostassignmentlead');
		Route::post('/clients/getCostAssignmentMigrationAgentDetailLead', 'Admin\ClientsController@getCostAssignmentMigrationAgentDetailLead')->name('clients.getCostAssignmentMigrationAgentDetailLead');
        

		Route::post('/clients/{admin}/upload-agreement', 'Admin\ClientsController@uploadAgreement')->name('clients.uploadAgreement');
		//Get matter template
		Route::get('/get-matter-templates', 'Admin\AdminController@getmattertemplates')->name('admin.clients.getmattertemplates');
    
		// Matter email template routes moved to routes/adminconsole.php
  
		//matter checklist
		Route::get('/upload-checklists', 'Admin\UploadChecklistController@index')->name('admin.upload_checklists.index');
		Route::get('/upload-checklists/matter/{matterId}', 'Admin\UploadChecklistController@showByMatter')->name('admin.upload_checklists.matter');
		Route::post('/upload-checklists/store', 'Admin\UploadChecklistController@store')->name('admin.upload_checklistsupload');
		
  
       //apointment related routes
		Route::get('/appointments-education', 'Admin\AppointmentsController@appointmentsEducation')->name('appointments-education');
		Route::get('/appointments-jrp', 'Admin\AppointmentsController@appointmentsJrp')->name('appointments-jrp');
		Route::get('/appointments-tourist', 'Admin\AppointmentsController@appointmentsTourist')->name('appointments-tourist');
		Route::get('/appointments-others', 'Admin\AppointmentsController@appointmentsOthers')->name('appointments-others');
        Route::get('/appointments-adelaide', 'Admin\AppointmentsController@appointmentsAdelaide')->name('appointments-adelaide');
	
		//Appointment modules
        Route::resource('appointments', Admin\AppointmentsController::class);

		/*---------- Document Signature Management ----------*/
		Route::get('/documents', 'Admin\DocumentController@index')->name('documents.index');
		Route::get('/documents/create', 'Admin\DocumentController@create')->name('documents.create');
		Route::post('/documents', 'Admin\DocumentController@store')->name('documents.store');
		Route::get('/documents/{id}/edit', 'Admin\DocumentController@edit')->name('documents.edit');
		Route::patch('/documents/{id}', 'Admin\DocumentController@update')->name('documents.update');

		Route::post('/documents/{document}/sign', 'Admin\DocumentController@submitSignatures')->name('documents.submitSignatures');
		Route::post('/documents/{document}/send-reminder', 'Admin\DocumentController@sendReminder')->name('documents.sendReminder');
		Route::get('/documents/{id}/download-signed', 'Admin\DocumentController@downloadSigned')->name('download.signed');
		Route::get('/documents/{id}/download-signed-and-thankyou', 'Admin\DocumentController@downloadSignedAndThankyou')->name('documents.signed.download_and_thankyou');
		Route::get('/documents/thankyou/{id?}', 'Admin\DocumentController@thankyou')->name('documents.thankyou');
		Route::post('/documents/{document}/send-signing-link', 'Admin\DocumentController@sendSigningLink')->name('documents.sendSigningLink');
		Route::get('/documents/{id}/page/{page}', 'Admin\DocumentController@getPage')->name('documents.page');
		Route::get('/documents/{document}/sign', 'Admin\DocumentController@showSignForm')->name('documents.showSignForm');

		Route::get('/download-signed/{id}', 'Admin\DocumentController@downloadSigned')->name('download.signed');
		  
		// Test signature route
		Route::get('/test-signature', function () {
			return view('test-signature');
		})->name('test.signature');

        // DOC/DOCX to PDF Converter Routes
        Route::get('/doc-to-pdf', 'Admin\DocToPdfController@showForm')->name('admin.doc-to-pdf.form');
        Route::post('/doc-to-pdf/convert', 'Admin\DocToPdfController@convertLocal')->name('admin.doc-to-pdf.convert');
        Route::get('/doc-to-pdf/test', 'Admin\DocToPdfController@testLocalConversion')->name('admin.doc-to-pdf.test');
        Route::get('/doc-to-pdf/test-python', 'Admin\DocToPdfController@testPythonConversion')->name('admin.doc-to-pdf.test-python');
        Route::get('/doc-to-pdf/debug', 'Admin\DocToPdfController@debugConfig')->name('admin.doc-to-pdf.debug');




		Route::post('/convert-activity-to-note', 'Admin\ClientsController@convertActivityToNote')->name('admin.clients.convertActivityToNote');
		Route::get('/get-client-matters/{clientId}', 'Admin\ClientsController@getClientMatters')->name('admin.clients.getClientMatters');
        
		//Toggle client portal status
		Route::post('/clients/toggle-client-portal', 'Admin\ClientsController@toggleClientPortal')->name('admin.clients.toggleClientPortal');

		// DateTime backend for appointment scheduling
		Route::post('/getdatetimebackend', [App\Http\Controllers\HomeController::class, 'getdatetimebackend'])->name('getdatetimebackend');
		Route::post('/getdisableddatetime', [App\Http\Controllers\HomeController::class, 'getdisableddatetime'])->name('getdisableddatetime');


	});

// Test routes moved to routes/test.php (loaded only in debug mode)
// Frontend dynamic routing removed - no frontend website

//Frontend Document Controller
Route::get('/sign/{id}/{token}', [App\Http\Controllers\DocumentController::class, 'sign'])->name('documents.sign');
Route::get('/documents/{id}/page/{page}', [App\Http\Controllers\DocumentController::class, 'getPage'])->name('documents.page');
Route::post('/documents/{document}/sign', [App\Http\Controllers\DocumentController::class, 'submitSignatures'])->name('documents.submitSignatures');

Route::get('/documents/thankyou/{id?}', [App\Http\Controllers\DocumentController::class, 'thankyou'])->name('documents.thankyou');

Route::post('/documents/{document}/send-reminder', [App\Http\Controllers\DocumentController::class, 'sendReminder'])->name('documents.sendReminder');
Route::get('/documents/{id}/download-signed', [App\Http\Controllers\DocumentController::class, 'downloadSigned'])->name('download.signed');
Route::get('/documents/{id}/download-signed-and-thankyou', [App\Http\Controllers\DocumentController::class, 'downloadSignedAndThankyou'])->name('documents.signed.download_and_thankyou');
Route::get('/documents/thankyou/{id?}', [App\Http\Controllers\DocumentController::class, 'thankyou'])->name('documents.thankyou');
Route::get('/documents/{id?}', [App\Http\Controllers\DocumentController::class, 'index'])->name('documents.index');

// Public email verification route (no auth required)
Route::get('/verify-email/{token}', [EmailVerificationController::class, 'verifyEmail'])->name('admin.clients.email.verify');

