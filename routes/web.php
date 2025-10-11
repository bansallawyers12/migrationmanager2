<?php

use App\Http\Controllers\Admin\ClientsController;
use App\Http\Controllers\AdminConsole\AnzscoOccupationController;

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
Route::get('/exception', 'ExceptionController@index')->name('exception');
Route::post('/exception', 'ExceptionController@index')->name('exception');

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
    Route::get('/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login', 'Auth\AdminLoginController@login')->name('admin.login');
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
    Route::post('/my_profile', 'Admin\AdminController@myProfile')->name('admin.my_profile');
    Route::get('/change_password', 'Admin\AdminController@change_password')->name('admin.change_password');
    Route::post('/change_password', 'Admin\AdminController@change_password')->name('admin.change_password');
    Route::get('/sessions', 'Admin\AdminController@sessions')->name('admin.sessions');
    Route::post('/sessions', 'Admin\AdminController@sessions')->name('admin.sessions');
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

    /*---------- Leads Management ----------*/
    Route::get('/leads', 'Admin\LeadController@index')->name('admin.leads.index');
    Route::get('/leads/history/{id}', 'Admin\LeadController@history')->name('admin.leads.history');
    Route::get('/leads/create', 'Admin\LeadController@create')->name('admin.leads.create');
    Route::post('/leads/assign', 'Admin\LeadController@assign')->name('admin.leads.assign');
    Route::get('/leads/edit/{id}', 'Admin\LeadController@edit')->name('admin.leads.edit');
    Route::post('/leads/edit', 'Admin\LeadController@edit')->name('admin.leads.edit');
    Route::get('/leads/notes/delete/{id}', 'Admin\LeadController@leaddeleteNotes');
    Route::get('/get-notedetail', 'Admin\LeadController@getnotedetail');

    Route::post('/leads/store', 'Admin\LeadController@store')->name('admin.leads.store');
    Route::get('/leads/convert', 'Admin\LeadController@convertoClient');
    Route::get('/leads/pin/{id}', 'Admin\LeadController@leadPin');

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
		Route::get('/clients', 'Admin\ClientsController@index')->name('admin.clients.index');
        Route::get('/clientsmatterslist', 'Admin\ClientsController@clientsmatterslist')->name('admin.clients.clientsmatterslist');
		Route::post('/clients/store', 'Admin\ClientsController@store')->name('admin.clients.store');
		Route::get('/clients/edit/{id}', 'Admin\ClientsController@edit')->name('admin.clients.edit');
		Route::post('/clients/edit', 'Admin\ClientsController@edit')->name('admin.clients.update');
		Route::post('/clients/save-section', 'Admin\ClientPersonalDetailsController@saveSection')->name('admin.clients.saveSection');

        // Phone Verification Routes
        Route::prefix('clients/phone')->name('clients.phone.')->group(function () {
            Route::post('/send-otp', 'Admin\PhoneVerificationController@sendOTP')->name('sendOTP');
            Route::post('/verify-otp', 'Admin\PhoneVerificationController@verifyOTP')->name('verifyOTP');
            Route::post('/resend-otp', 'Admin\PhoneVerificationController@resendOTP')->name('resendOTP');
            Route::get('/status/{contactId}', 'Admin\PhoneVerificationController@getStatus')->name('status');
        });

        // Email Verification Routes
        Route::prefix('clients/email')->name('clients.email.')->group(function () {
            Route::post('/send-verification', 'Admin\EmailVerificationController@sendVerificationEmail')->name('sendVerification');
            Route::post('/resend-verification', 'Admin\EmailVerificationController@resendVerificationEmail')->name('resendVerification');
            Route::get('/status/{emailId}', 'Admin\EmailVerificationController@getStatus')->name('status');
        });

        Route::post('/clients/followup/store', 'Admin\ClientsController@followupstore');


		Route::post('/clients/followup/retagfollowup', 'Admin\ClientsController@retagfollowup');
		Route::get('/clients/changetype/{id}/{type}', 'Admin\ClientsController@changetype');
		Route::get('/document/download/pdf/{id}', 'Admin\ClientsController@downloadpdf');
		Route::get('/clients/removetag', 'Admin\ClientsController@removetag');
		Route::get('/clients/detail/{client_id}/{client_unique_matter_ref_no?}/{tab?}', 'Admin\ClientsController@detail')->name('admin.clients.detail');
		
        Route::get('/clients/get-recipients', 'Admin\ClientsController@getrecipients')->name('admin.clients.getrecipients');
		Route::get('/clients/get-onlyclientrecipients', 'Admin\ClientsController@getonlyclientrecipients')->name('admin.clients.getonlyclientrecipients');
		Route::get('/clients/get-allclients', 'Admin\ClientsController@getallclients')->name('admin.clients.getallclients');
		Route::get('/clients/change_assignee', 'Admin\ClientsController@change_assignee');
		Route::get('/get-templates', 'Admin\AdminController@gettemplates')->name('admin.clients.gettemplates');
		Route::post('/sendmail', 'Admin\AdminController@sendmail')->name('admin.clients.sendmail');
		Route::post('/create-note', 'Admin\ClientsController@createnote')->name('admin.clients.createnote');
		Route::post('/update-note-datetime', 'Admin\ClientsController@updateNoteDatetime')->name('admin.clients.updateNoteDatetime');
		Route::get('/getnotedetail', 'Admin\ClientsController@getnotedetail')->name('admin.clients.getnotedetail');
		Route::get('/deletenote', 'Admin\ClientsController@deletenote')->name('admin.clients.deletenote');
		Route::get('/deletecostagreement', 'Admin\ClientsController@deletecostagreement')->name('admin.clients.deletecostagreement');
        Route::get('/deleteactivitylog', 'Admin\ClientsController@deleteactivitylog')->name('admin.clients.deleteactivitylog');
        Route::post('/not-picked-call', 'Admin\ClientsController@notpickedcall')->name('admin.clients.notpickedcall');
		Route::get('/viewnotedetail', 'Admin\ClientsController@viewnotedetail');
		Route::get('/viewapplicationnote', 'Admin\ClientsController@viewapplicationnote');
		Route::post('/saveprevvisa', 'Admin\ClientsController@saveprevvisa');
		Route::post('/saveonlineprimaryform', 'Admin\ClientsController@saveonlineform');
		Route::post('/saveonlinesecform', 'Admin\ClientsController@saveonlineform');
		Route::post('/saveonlinechildform', 'Admin\ClientsController@saveonlineform');
		//archived Start
		Route::get('/archived', 'Admin\ClientsController@archived')->name('admin.clients.archived');
		Route::get('/change-client-status', 'Admin\ClientsController@updateclientstatus')->name('admin.clients.updateclientstatus');
		Route::get('/get-activities', 'Admin\ClientsController@activities')->name('admin.clients.activities');
		Route::get('/get-application-lists', 'Admin\ClientsController@getapplicationlists')->name('admin.clients.getapplicationlists');
		Route::post('/saveapplication', 'Admin\ClientsController@saveapplication')->name('admin.clients.saveapplication');
		Route::get('/get-notes', 'Admin\ClientsController@getnotes')->name('admin.clients.getnotes');
		Route::get('/convertapplication', 'Admin\ClientsController@convertapplication')->name('admin.clients.convertapplication');
		Route::get('/deleteservices', 'Admin\ClientsController@deleteservices')->name('admin.clients.deleteservices');
        Route::get('/deletedocs', 'Admin\ClientsController@deletedocs')->name('admin.clients.deletedocs');
		Route::post('/renamedoc', 'Admin\ClientsController@renamedoc')->name('admin.clients.renamedoc');
		Route::post('/savetoapplication', 'Admin\ClientsController@savetoapplication');

		/*---------- Branch Management ----------*/
		// Branch routes moved to routes/adminconsole.php

		/*---------- Applications Management ----------*/
		Route::get('/applications/detail/{id}', 'Admin\ApplicationsController@detail')->name('admin.applications.detail');
		Route::post('/interested-service', 'Admin\ClientsController@interestedService');
		Route::post('/edit-interested-service', 'Admin\ClientsController@editinterestedService');
		Route::get('/get-services', 'Admin\ClientsController@getServices');
		Route::post('/servicesavefee', 'Admin\ClientsController@servicesavefee');
		Route::get('/deleteappointment', 'Admin\ClientsController@deleteappointment');
		Route::post('/add-appointment', 'Admin\ClientsController@addAppointment');
        Route::post('/add-appointment-book', 'Admin\ClientsController@addAppointmentBook');
		Route::post('/editappointment', 'Admin\ClientsController@editappointment');
		Route::post('/upload-mail', 'Admin\ClientsController@uploadmail');
        Route::post('/upload-fetch-mail', 'Admin\ClientsController@uploadfetchmail'); //upload inbox email
        Route::post('/upload-sent-fetch-mail', 'Admin\ClientsController@uploadsentfetchmail'); //upload sent email


		Route::post('/updatefollowupschedule', 'Admin\ClientsController@updatefollowupschedule');
		Route::get('/updateappointmentstatus/{status}/{id}', 'Admin\ClientsController@updateappointmentstatus');
		Route::get('/get-appointments', 'Admin\ClientsController@getAppointments');

        Route::get('/pinnote', 'Admin\ClientsController@pinnote');
        Route::get('/pinactivitylog', 'Admin\ClientsController@pinactivitylog');

		Route::get('/getintrestedservice', 'Admin\ClientsController@getintrestedservice');
		Route::get('/getintrestedserviceedit', 'Admin\ClientsController@getintrestedserviceedit');
		Route::get('/getAppointmentdetail', 'Admin\ClientsController@getAppointmentdetail');
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
        Route::post('/is_email_unique', 'Admin\LeadController@is_email_unique');
        Route::post('/is_contactno_unique', 'Admin\LeadController@is_contactno_unique');

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

        Route::post('/clients/update-address', 'Admin\ClientPersonalDetailsController@updateAddress')->name('admin.clients.updateAddress');
        Route::post('/clients/search-address-full', 'Admin\ClientPersonalDetailsController@searchAddressFull')->name('admin.clients.searchAddressFull');
        Route::post('/clients/get-place-details', 'Admin\ClientPersonalDetailsController@getPlaceDetails')->name('admin.clients.getPlaceDetails');

        //Fetch all contact list of any client at create note popup
        Route::post('/clients/fetchClientContactNo', 'Admin\ClientPersonalDetailsController@fetchClientContactNo');

        Route::post('/clients/clientdetailsinfo/{id}', 'Admin\ClientPersonalDetailsController@clientdetailsinfo')->name('admin.clients.clientdetailsinfo');
        Route::post('/clients/clientdetailsinfo', 'Admin\ClientPersonalDetailsController@clientdetailsinfo')->name('admin.clients.clientdetailsinfo');


        Route::post('/reassiginboxemail', 'Admin\ClientsController@reassiginboxemail')->name('admin.clients.reassiginboxemail');
        Route::post('/reassigsentemail', 'Admin\ClientsController@reassigsentemail')->name('admin.clients.reassigsentemail');

        //Fetch selected client all matters at assign email to user popup
        Route::post('/listAllMattersWRTSelClient', 'Admin\ClientsController@listAllMattersWRTSelClient')->name('admin.clients.listAllMattersWRTSelClient');

        Route::post('/verifydoc', 'Admin\ClientsController@verifydoc')->name('admin.clients.verifydoc');
        Route::post('/getvisachecklist', 'Admin\ClientsController@getvisachecklist')->name('admin.clients.getvisachecklist');




        Route::post('/extenddeadlinedate', 'Admin\AdminController@extenddeadlinedate');

        Route::post('/leads/updateOccupation', 'Admin\ClientPersonalDetailsController@updateOccupation')->name('admin.leads.updateOccupation');

        /*---------- ANZSCO Occupation Database ----------*/
        // ANZSCO routes moved to routes/adminconsole.php
        // Search route for client forms
        Route::get('/anzsco/search', [AnzscoOccupationController::class, 'search'])->name('admin.anzsco.search');

        //Document Checklist Start
		// Document checklist routes moved to routes/adminconsole.php

        //Personal and Visa Document
        Route::post('/add-edudocchecklist', 'Admin\ClientsController@addedudocchecklist')->name('admin.clients.addedudocchecklist');
        Route::post('/upload-edudocument', 'Admin\ClientsController@uploadedudocument')->name('admin.clients.uploadedudocument');
        Route::post('/add-visadocchecklist', 'Admin\ClientsController@addvisadocchecklist')->name('admin.clients.addvisadocchecklist');
        Route::post('/upload-visadocument', 'Admin\ClientsController@uploadvisadocument')->name('admin.clients.uploadvisadocument');

        Route::post('/check-email', 'Admin\ClientsController@checkEmail')->name('check.email');
        Route::post('/check.phone', 'Admin\ClientsController@checkContact')->name('check.phone');

        //Document Not Use Tab
        Route::post('/notuseddoc', 'Admin\ClientsController@notuseddoc')->name('admin.clients.notuseddoc');
        Route::post('/renamechecklistdoc', 'Admin\ClientsController@renamechecklistdoc')->name('admin.clients.renamechecklistdoc');
        //inbox preview click update mail_is_read bit
        Route::post('/updatemailreadbit', 'Admin\ClientsController@updatemailreadbit')->name('admin.clients.updatemailreadbit');

        //Back To Document
        Route::post('/backtodoc', 'Admin\ClientsController@backtodoc')->name('admin.clients.backtodoc');

        //Ajax change on workflow status change
        Route::post('/update-stage', 'Admin\AdminController@updateStage');

        Route::post('/mail/enhance', 'Admin\ClientsController@enhanceMessage')->name('admin.mail.enhance');

		Route::post('/clients/filter-emails', 'Admin\ClientsController@filterEmails')->name('admin.clients.filter.emails');
		Route::post('/clients/filter-sentemails', 'Admin\ClientsController@filterSentEmails')->name('admin.clients.filter.sentmails');

        Route::get('/admin/get-visa-types', 'Admin\ClientPersonalDetailsController@getVisaTypes')->name('admin.getVisaTypes');
        Route::get('/admin/get-countries', 'Admin\ClientPersonalDetailsController@getCountries')->name('admin.getCountries');
        Route::post('/updateOccupation', 'Admin\ClientPersonalDetailsController@updateOccupation')->name('admin.clients.updateOccupation');


        Route::get('/clients/genClientFundLedgerInvoice/{id}', 'Admin\ClientsController@genClientFundLedgerInvoice');
        Route::get('/clients/genofficereceiptInvoice/{id}', 'Admin\ClientsController@genofficereceiptInvoice');


        Route::post('/update-client-funds-ledger', 'Admin\ClientsController@updateClientFundsLedger')->name('admin.clients.update-client-funds-ledger');

        Route::post('/update-task', 'Admin\AssigneeController@updateTask');
        Route::get('/activities/counts','Admin\AssigneeController@getActivityCounts' )->name('activities.counts');


        Route::post('/clients/invoiceamount', 'Admin\ClientsController@getInvoiceAmount')->name('admin.clients.invoiceamount');



        Route::post('/admin/clients/search-partner', 'Admin\ClientPersonalDetailsController@searchPartner')->name('admin.clients.searchPartner');
        Route::post('/admin/clients/save-relationship', 'Admin\ClientPersonalDetailsController@saveRelationship')->name('admin.clients.saveRelationship');


        //Client receipt delete by Celesty
        Route::post('/delete_receipt','Admin\ClientsController@delete_receipt');
        //Download Document
        Route::post('/download-document', 'Admin\ClientsController@download_document');

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
        Route::post('/clients/fetchClientMatterAssignee', 'Admin\ClientPersonalDetailsController@fetchClientMatterAssignee');
        //Save client matter assignee
        Route::post('/clients/updateClientMatterAssignee', 'Admin\ClientPersonalDetailsController@updateClientMatterAssignee');

        //Add Personal Doucment Category
        Route::post('/add-personaldoccategory', 'Admin\ClientsController@addPersonalDocCategory');

        //Update Personal Doucment Category
        Route::post('/update-personal-doc-category', 'Admin\ClientsController@updatePersonalDocCategory' )->name('update.personal.doc.category');

         //Add Visa Doucment Category
        Route::post('/add-visadoccategory', 'Admin\ClientsController@addVisaDocCategory');

        //Update Visa Doucment Category
        Route::post('/update-visa-doc-category', 'Admin\ClientsController@updateVisaDocCategory' )->name('update.visa.doc.category');

        //Send summary page code to webhook
        Route::post('/send-webhook', 'Admin\ClientsController@sendToWebhook')->name('admin.send-webhook');
        //Check cost assignment is exist or not
        Route::post('/clients/check-cost-assignment', 'Admin\ClientsController@checkCostAssignment');


       

        Route::get('/sign/{id}/{token}', 'Admin\DocumentController@sign')->name('documents.sign');
		Route::get('/documents/{id?}', 'Admin\DocumentController@index')->name('documents.index');
  
        //Lead Save cost assignment
        Route::post('/clients/savecostassignmentlead', 'Admin\ClientsController@savecostassignmentlead')->name('clients.savecostassignmentlead');
		Route::post('/clients/getCostAssignmentMigrationAgentDetailLead', 'Admin\ClientsController@getCostAssignmentMigrationAgentDetailLead')->name('clients.getCostAssignmentMigrationAgentDetailLead');
        

		Route::post('/clients/{client}/upload-agreement', 'Admin\ClientsController@uploadAgreement')->name('clients.uploadAgreement');
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
Route::get('/verify-email/{token}', 'Admin\EmailVerificationController@verifyEmail')->name('admin.clients.email.verify');

