<?php

use App\Http\Controllers\AdminConsole\MatterController;
use App\Http\Controllers\AdminConsole\TagController;
use App\Http\Controllers\AdminConsole\WorkflowController;
use App\Http\Controllers\AdminConsole\EmailController;
use App\Http\Controllers\AdminConsole\CrmEmailTemplateController;
use App\Http\Controllers\AdminConsole\MatterEmailTemplateController;
use App\Http\Controllers\AdminConsole\MatterOtherEmailTemplateController;
use App\Http\Controllers\AdminConsole\PersonalDocumentTypeController;
use App\Http\Controllers\AdminConsole\VisaDocumentTypeController;
use App\Http\Controllers\AdminConsole\DocumentChecklistController;
use App\Http\Controllers\AdminConsole\AppointmentDisableDateController;
use App\Http\Controllers\AdminConsole\PromoCodeController;
use App\Http\Controllers\AdminConsole\ProfileController;
use App\Http\Controllers\AdminConsole\UserController;
use App\Http\Controllers\AdminConsole\UserroleController;
use App\Http\Controllers\AdminConsole\TeamController;
use App\Http\Controllers\AdminConsole\BranchesController;
use App\Http\Controllers\AdminConsole\AnzscoOccupationController;

/*
|--------------------------------------------------------------------------
| AdminConsole Routes
|--------------------------------------------------------------------------
|
| Here are the admin console routes for system configuration and settings.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('adminconsole')->name('adminconsole.')->middleware(['auth'])->group(function() {
    
    // Features routes - Feature management
    Route::prefix('features')->name('features.')->group(function() {
        
        // Matter routes
        Route::get('/matter', [MatterController::class, 'index'])->name('matter.index');
        Route::get('/matter/create', [MatterController::class, 'create'])->name('matter.create');
        Route::post('/matter/store', [MatterController::class, 'store'])->name('matter.store');
        Route::get('/matter/edit/{id}', [MatterController::class, 'edit'])->name('matter.edit');
        Route::post('/matter/edit', [MatterController::class, 'edit'])->name('matter.edit');
        
        // Tags routes
        Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
        Route::get('/tags/create', [TagController::class, 'create'])->name('tags.create');
        Route::post('/tags/store', [TagController::class, 'store'])->name('tags.store');
        Route::get('/tags/edit/{id}', [TagController::class, 'edit'])->name('tags.edit');
        Route::post('/tags/edit', [TagController::class, 'edit'])->name('tags.edit');
        
        // Workflow routes
        Route::get('/workflow', [WorkflowController::class, 'index'])->name('workflow.index');
        Route::get('/workflow/create', [WorkflowController::class, 'create'])->name('workflow.create');
        Route::post('/workflow/store', [WorkflowController::class, 'store'])->name('workflow.store');
        Route::get('/workflow/edit/{id}', [WorkflowController::class, 'edit'])->name('workflow.edit');
        Route::post('/workflow/edit', [WorkflowController::class, 'edit'])->name('workflow.edit');
        Route::get('/workflow/deactivate-workflow/{id}', [WorkflowController::class, 'deactivateWorkflow'])->name('workflow.deactivate');
        Route::get('/workflow/activate-workflow/{id}', [WorkflowController::class, 'activateWorkflow'])->name('workflow.activate');
        
        // Email routes
        Route::get('/emails', [EmailController::class, 'index'])->name('emails.index');
        Route::get('/emails/create', [EmailController::class, 'create'])->name('emails.create');
        Route::post('/emails/store', [EmailController::class, 'store'])->name('emails.store');
        Route::get('/emails/edit/{id}', [EmailController::class, 'edit'])->name('emails.edit');
        Route::post('/emails/edit', [EmailController::class, 'edit'])->name('emails.edit');
        
        // CRM Email Template routes
        Route::get('/crm-email-template', [CrmEmailTemplateController::class, 'index'])->name('crmemailtemplate.index');
        Route::get('/crm-email-template/create', [CrmEmailTemplateController::class, 'create'])->name('crmemailtemplate.create');
        Route::post('/crm-email-template/store', [CrmEmailTemplateController::class, 'store'])->name('crmemailtemplate.store');
        Route::get('/crm-email-template/edit/{id}', [CrmEmailTemplateController::class, 'edit'])->name('crmemailtemplate.edit');
        Route::post('/crm-email-template/edit', [CrmEmailTemplateController::class, 'edit'])->name('crmemailtemplate.edit');
        
        // Matter Email Template routes
        Route::get('/matter-email-template', [MatterEmailTemplateController::class, 'index'])->name('matteremailtemplate.index');
        Route::get('/matter-email-template/create', [MatterEmailTemplateController::class, 'create'])->name('matteremailtemplate.create');
        Route::post('/matter-email-template/store', [MatterEmailTemplateController::class, 'store'])->name('matteremailtemplate.store');
        Route::get('/matter-email-template/edit/{id}', [MatterEmailTemplateController::class, 'edit'])->name('matteremailtemplate.edit');
        Route::post('/matter-email-template/edit', [MatterEmailTemplateController::class, 'edit'])->name('matteremailtemplate.edit');
        
        // Matter Other Email Template routes
        Route::get('/matter-other-email-template', [MatterOtherEmailTemplateController::class, 'index'])->name('matterotheremailtemplate.index');
        Route::get('/matter-other-email-template/create', [MatterOtherEmailTemplateController::class, 'create'])->name('matterotheremailtemplate.create');
        Route::post('/matter-other-email-template/store', [MatterOtherEmailTemplateController::class, 'store'])->name('matterotheremailtemplate.store');
        Route::get('/matter-other-email-template/edit/{id}', [MatterOtherEmailTemplateController::class, 'edit'])->name('matterotheremailtemplate.edit');
        Route::post('/matter-other-email-template/edit', [MatterOtherEmailTemplateController::class, 'edit'])->name('matterotheremailtemplate.edit');
        
        // Personal Document Type routes
        Route::get('/personal-document-type', [PersonalDocumentTypeController::class, 'index'])->name('personaldocumenttype.index');
        Route::get('/personal-document-type/create', [PersonalDocumentTypeController::class, 'create'])->name('personaldocumenttype.create');
        Route::post('/personal-document-type/store', [PersonalDocumentTypeController::class, 'store'])->name('personaldocumenttype.store');
        Route::get('/personal-document-type/edit/{id}', [PersonalDocumentTypeController::class, 'edit'])->name('personaldocumenttype.edit');
        Route::post('/personal-document-type/edit', [PersonalDocumentTypeController::class, 'edit'])->name('personaldocumenttype.edit');
        Route::post('/personal-document-type/checkcreatefolder', [PersonalDocumentTypeController::class, 'checkcreatefolder']);
        
        // Visa Document Type routes
        Route::get('/visa-document-type', [VisaDocumentTypeController::class, 'index'])->name('visadocumenttype.index');
        Route::get('/visa-document-type/create', [VisaDocumentTypeController::class, 'create'])->name('visadocumenttype.create');
        Route::post('/visa-document-type/store', [VisaDocumentTypeController::class, 'store'])->name('visadocumenttype.store');
        Route::get('/visa-document-type/edit/{id}', [VisaDocumentTypeController::class, 'edit'])->name('visadocumenttype.edit');
        Route::post('/visa-document-type/edit', [VisaDocumentTypeController::class, 'edit'])->name('visadocumenttype.edit');
        Route::post('/visa-document-type/checkcreatefolder', [VisaDocumentTypeController::class, 'checkcreatefolder']);
        
        // Document Checklist routes
        Route::get('/document-checklist', [DocumentChecklistController::class, 'index'])->name('documentchecklist.index');
        Route::get('/document-checklist/create', [DocumentChecklistController::class, 'create'])->name('documentchecklist.create');
        Route::post('/document-checklist/store', [DocumentChecklistController::class, 'store'])->name('documentchecklist.store');
        Route::get('/document-checklist/edit/{id}', [DocumentChecklistController::class, 'edit'])->name('documentchecklist.edit');
        Route::post('/document-checklist/edit', [DocumentChecklistController::class, 'edit'])->name('documentchecklist.edit');
        
        // Appointment Disable Date routes
        Route::get('/appointment-dates-disable', [AppointmentDisableDateController::class, 'index'])->name('appointmentdisabledate.index');
        Route::get('/appointment-dates-disable/create', [AppointmentDisableDateController::class, 'create'])->name('appointmentdisabledate.create');
        Route::post('/appointment-dates-disable/store', [AppointmentDisableDateController::class, 'store'])->name('appointmentdisabledate.store');
        Route::get('/appointment-dates-disable/edit/{id}', [AppointmentDisableDateController::class, 'edit'])->name('appointmentdisabledate.edit');
        Route::post('/appointment-dates-disable/edit', [AppointmentDisableDateController::class, 'edit'])->name('appointmentdisabledate.edit');
        
        // Promo Code routes
        Route::get('/promo-code', [PromoCodeController::class, 'index'])->name('promocode.index');
        Route::get('/promo-code/create', [PromoCodeController::class, 'create'])->name('promocode.create');
        Route::post('/promo-code/store', [PromoCodeController::class, 'store'])->name('promocode.store');
        Route::get('/promo-code/edit/{id}', [PromoCodeController::class, 'edit'])->name('promocode.edit');
        Route::post('/promo-code/edit', [PromoCodeController::class, 'edit'])->name('promocode.edit');
        Route::post('/promo-code/checkpromocode', [PromoCodeController::class, 'checkpromocode']);
        
        // Profile routes
        Route::get('/profiles', [ProfileController::class, 'index'])->name('profiles.index');
        Route::get('/profiles/create', [ProfileController::class, 'create'])->name('profiles.create');
        Route::post('/profiles/store', [ProfileController::class, 'store'])->name('profiles.store');
        Route::get('/profiles/edit/{id}', [ProfileController::class, 'edit'])->name('profiles.edit');
        Route::post('/profiles/edit', [ProfileController::class, 'edit'])->name('profiles.edit');
    });
    
    // System routes - System management
    Route::prefix('system')->name('system.')->group(function() {
        
        // Users routes
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
        Route::get('/users/view/{id}', [UserController::class, 'view'])->name('users.view');
        Route::post('/users/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::post('/users/savezone', [UserController::class, 'savezone']);
        Route::get('/users/active', [UserController::class, 'active'])->name('users.active');
        Route::get('/users/inactive', [UserController::class, 'inactive'])->name('users.inactive');
        Route::get('/users/invited', [UserController::class, 'invited'])->name('users.invited');
        Route::get('/users/clientlist', [UserController::class, 'clientlist'])->name('users.clientlist');
        Route::get('/users/createclient', [UserController::class, 'createclient'])->name('users.createclient');
        Route::post('/users/storeclient', [UserController::class, 'storeclient'])->name('users.storeclient');
        Route::get('/users/editclient/{id}', [UserController::class, 'editclient'])->name('users.editclient');
        Route::post('/users/editclient', [UserController::class, 'editclient'])->name('users.editclient');
        
        // Roles routes
        Route::get('/roles', [UserroleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [UserroleController::class, 'create'])->name('roles.create');
        Route::post('/roles/store', [UserroleController::class, 'store'])->name('roles.store');
        Route::get('/roles/edit/{id}', [UserroleController::class, 'edit'])->name('roles.edit');
        Route::post('/roles/edit', [UserroleController::class, 'edit'])->name('roles.edit');
        
        // Teams routes
        Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
        Route::get('/teams/edit/{id}', [TeamController::class, 'edit'])->name('teams.edit');
        Route::post('/teams/store', [TeamController::class, 'store'])->name('teams.store');
        
        // Offices routes
        Route::get('/offices', [BranchesController::class, 'index'])->name('offices.index');
        Route::get('/offices/create', [BranchesController::class, 'create'])->name('offices.create');
        Route::post('/offices/store', [BranchesController::class, 'store'])->name('offices.store');
        Route::get('/offices/edit/{id}', [BranchesController::class, 'edit'])->name('offices.edit');
        Route::get('/offices/view/{id}', [BranchesController::class, 'view'])->name('offices.view');
        Route::get('/offices/view/client/{id}', [BranchesController::class, 'viewclient'])->name('offices.viewclient');
        Route::post('/offices/edit', [BranchesController::class, 'edit'])->name('offices.edit');
        
        // Settings routes
        Route::get('/settings', [\App\Http\Controllers\Admin\AdminController::class, 'gensettings'])->name('settings.index');
        Route::post('/settings/update', [\App\Http\Controllers\Admin\AdminController::class, 'gensettingsupdate'])->name('settings.update');
    });
    
    // Database routes - Database management
    Route::prefix('database')->name('database.')->group(function() {
        
        // ANZSCO routes
        Route::get('/anzsco', [AnzscoOccupationController::class, 'index'])->name('anzsco.index');
        Route::get('/anzsco/create', [AnzscoOccupationController::class, 'create'])->name('anzsco.create');
        Route::post('/anzsco/store', [AnzscoOccupationController::class, 'store'])->name('anzsco.store');
        Route::get('/anzsco/edit/{id}', [AnzscoOccupationController::class, 'edit'])->name('anzsco.edit');
        Route::post('/anzsco/edit', [AnzscoOccupationController::class, 'edit'])->name('anzsco.edit');
        Route::get('/anzsco/import', [AnzscoOccupationController::class, 'import'])->name('anzsco.import');
        Route::post('/anzsco/import', [AnzscoOccupationController::class, 'import'])->name('anzsco.import');
    });
});
