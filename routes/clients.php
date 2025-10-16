<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ClientsController;
use App\Http\Controllers\Admin\ClientEoiRoiController;
use App\Http\Controllers\Admin\Clients\ClientNotesController;
use App\Http\Controllers\Admin\Clients\ClientDocumentsController;
use App\Http\Controllers\Admin\ClientPersonalDetailsController;
use App\Http\Controllers\Admin\PhoneVerificationController;
use App\Http\Controllers\Admin\EmailVerificationController;
use App\Http\Controllers\AdminConsole\AnzscoOccupationController;

/*
|--------------------------------------------------------------------------
| Client Management Routes
|--------------------------------------------------------------------------
|
| All routes for client CRUD operations, documents, verification, invoices,
| EOI/ROI management, notes, agreements, and related functionality.
|
| Prefix: /admin (inherited from web.php)
| Middleware: auth:admin (inherited from web.php)
|
*/

/*---------- Client CRUD Operations ----------*/
Route::get('/clients', [ClientsController::class, 'index'])->name('admin.clients.index');
Route::get('/clientsmatterslist', [ClientsController::class, 'clientsmatterslist'])->name('admin.clients.clientsmatterslist');
Route::get('/clientsemaillist', [ClientsController::class, 'clientsemaillist'])->name('admin.clients.clientsemaillist');
Route::post('/clients/store', [ClientsController::class, 'store'])->name('admin.clients.store');
Route::get('/clients/edit/{id}', [ClientsController::class, 'edit'])->name('admin.clients.edit');
Route::post('/clients/edit', [ClientsController::class, 'edit'])->name('admin.clients.update');
Route::post('/clients/save-section', [ClientPersonalDetailsController::class, 'saveSection'])->name('admin.clients.saveSection');
Route::get('/clients/partner-eoi-data/{partnerId}', [ClientPersonalDetailsController::class, 'getPartnerEoiData'])->name('admin.clients.partnerEoiData');

/*---------- Phone & Email Verification ----------*/
Route::prefix('clients/phone')->name('clients.phone.')->group(function () {
    Route::post('/send-otp', [PhoneVerificationController::class, 'sendOTP'])->name('sendOTP');
    Route::post('/verify-otp', [PhoneVerificationController::class, 'verifyOTP'])->name('verifyOTP');
    Route::post('/resend-otp', [PhoneVerificationController::class, 'resendOTP'])->name('resendOTP');
    Route::get('/status/{contactId}', [PhoneVerificationController::class, 'getStatus'])->name('status');
});

Route::prefix('clients/email')->name('clients.email.')->group(function () {
    Route::post('/send-verification', [EmailVerificationController::class, 'sendVerificationEmail'])->name('sendVerification');
    Route::post('/resend-verification', [EmailVerificationController::class, 'resendVerificationEmail'])->name('resendVerification');
    Route::get('/status/{emailId}', [EmailVerificationController::class, 'getStatus'])->name('status');
});

/*---------- Client Follow-ups & Activities ----------*/
Route::post('/clients/followup/store', [ClientsController::class, 'followupstore']);
Route::post('/clients/followup/retagfollowup', [ClientsController::class, 'retagfollowup']);
Route::get('/clients/changetype/{id}/{type}', [ClientsController::class, 'changetype']);
Route::get('/document/download/pdf/{id}', [ClientsController::class, 'downloadpdf']);
Route::get('/clients/removetag', [ClientsController::class, 'removetag']);
Route::get('/clients/detail/{client_id}/{client_unique_matter_ref_no?}/{tab?}', [ClientsController::class, 'detail'])->name('admin.clients.detail');

/*---------- Client Communication ----------*/
Route::get('/clients/get-recipients', [ClientsController::class, 'getrecipients'])->name('admin.clients.getrecipients');
Route::get('/clients/get-onlyclientrecipients', [ClientsController::class, 'getonlyclientrecipients'])->name('admin.clients.getonlyclientrecipients');
Route::get('/clients/get-allclients', [ClientsController::class, 'getallclients'])->name('admin.clients.getallclients');
Route::get('/clients/change_assignee', [ClientsController::class, 'change_assignee']);
Route::get('/get-templates', 'Admin\AdminController@gettemplates')->name('admin.clients.gettemplates');
Route::post('/sendmail', 'Admin\AdminController@sendmail')->name('admin.clients.sendmail');

Route::post('/upload-mail', 'Admin\ClientsController@uploadmail');
Route::post('/upload-fetch-mail', 'Admin\ClientsController@uploadfetchmail'); //upload inbox email
Route::post('/upload-sent-fetch-mail', 'Admin\ClientsController@uploadsentfetchmail'); //upload sent email

Route::post('/reassiginboxemail', 'Admin\ClientsController@reassiginboxemail')->name('admin.clients.reassiginboxemail');
Route::post('/reassigsentemail', 'Admin\ClientsController@reassigsentemail')->name('admin.clients.reassigsentemail');
Route::post('/listAllMattersWRTSelClient', 'Admin\ClientsController@listAllMattersWRTSelClient')->name('admin.clients.listAllMattersWRTSelClient');
Route::post('/updatemailreadbit', 'Admin\ClientsController@updatemailreadbit')->name('admin.clients.updatemailreadbit');

Route::post('/clients/filter-emails', 'Admin\ClientsController@filterEmails')->name('admin.clients.filter.emails');
Route::post('/clients/filter-sentemails', 'Admin\ClientsController@filterSentEmails')->name('admin.clients.filter.sentmails');
Route::post('/mail/enhance', 'Admin\ClientsController@enhanceMessage')->name('admin.mail.enhance');

/*---------- Client Notes ----------*/
Route::post('/create-note', [ClientNotesController::class, 'createnote'])->name('admin.clients.createnote');
Route::post('/update-note-datetime', [ClientNotesController::class, 'updateNoteDatetime'])->name('admin.clients.updateNoteDatetime');
Route::get('/getnotedetail', [ClientNotesController::class, 'getnotedetail'])->name('admin.clients.getnotedetail');
Route::get('/deletenote', [ClientNotesController::class, 'deletenote'])->name('admin.clients.deletenote');
Route::get('/viewnotedetail', [ClientNotesController::class, 'viewnotedetail']);
Route::get('/viewapplicationnote', [ClientNotesController::class, 'viewapplicationnote']);
Route::post('/saveprevvisa', [ClientNotesController::class, 'saveprevvisa']);
Route::post('/saveonlineprimaryform', [ClientNotesController::class, 'saveonlineform']);
Route::post('/saveonlinesecform', [ClientNotesController::class, 'saveonlineform']);
Route::post('/saveonlinechildform', [ClientNotesController::class, 'saveonlineform']);
Route::get('/get-notes', [ClientNotesController::class, 'getnotes'])->name('admin.clients.getnotes');
Route::get('/pinnote', [ClientNotesController::class, 'pinnote']);

Route::post('/convert-activity-to-note', 'Admin\ClientsController@convertActivityToNote')->name('admin.clients.convertActivityToNote');

/*---------- Client Status & Archive ----------*/
Route::get('/archived', 'Admin\ClientsController@archived')->name('admin.clients.archived');
Route::get('/change-client-status', 'Admin\ClientsController@updateclientstatus')->name('admin.clients.updateclientstatus');
Route::get('/get-activities', 'Admin\ClientsController@activities')->name('admin.clients.activities');
Route::get('/deletecostagreement', [ClientsController::class, 'deletecostagreement'])->name('admin.clients.deletecostagreement');
Route::get('/deleteactivitylog', [ClientsController::class, 'deleteactivitylog'])->name('admin.clients.deleteactivitylog');
Route::post('/not-picked-call', [ClientsController::class, 'notpickedcall'])->name('admin.clients.notpickedcall');
Route::get('/pinactivitylog', 'Admin\ClientsController@pinactivitylog');

/*---------- Client Services ----------*/
Route::post('/interested-service', 'Admin\ClientsController@interestedService');
Route::post('/edit-interested-service', 'Admin\ClientsController@editinterestedService');
Route::get('/get-services', 'Admin\ClientsController@getServices');
Route::post('/servicesavefee', 'Admin\ClientsController@servicesavefee');
Route::get('/getintrestedservice', 'Admin\ClientsController@getintrestedservice');
Route::get('/getintrestedserviceedit', 'Admin\ClientsController@getintrestedserviceedit');

Route::get('/get-application-lists', 'Admin\ClientsController@getapplicationlists')->name('admin.clients.getapplicationlists');
Route::post('/saveapplication', 'Admin\ClientsController@saveapplication')->name('admin.clients.saveapplication');
Route::get('/convertapplication', 'Admin\ClientsController@convertapplication')->name('admin.clients.convertapplication');
Route::get('/deleteservices', 'Admin\ClientsController@deleteservices')->name('admin.clients.deleteservices');
Route::post('/savetoapplication', 'Admin\ClientsController@savetoapplication');

Route::post('/client/createservicetaken', 'Admin\ClientsController@createservicetaken');
Route::post('/client/removeservicetaken', 'Admin\ClientsController@removeservicetaken');
Route::post('/client/getservicetaken', 'Admin\ClientsController@getservicetaken');

/*---------- Client Documents Management ----------*/
Route::post('/documents/add-edu-checklist', [ClientDocumentsController::class, 'addedudocchecklist'])->name('admin.clients.documents.addedudocchecklist');
Route::post('/documents/upload-edu-document', [ClientDocumentsController::class, 'uploadedudocument'])->name('admin.clients.documents.uploadedudocument');
Route::post('/documents/add-visa-checklist', [ClientDocumentsController::class, 'addvisadocchecklist'])->name('admin.clients.documents.addvisadocchecklist');
Route::post('/documents/upload-visa-document', [ClientDocumentsController::class, 'uploadvisadocument'])->name('admin.clients.documents.uploadvisadocument');
Route::post('/documents/rename', [ClientDocumentsController::class, 'renamedoc'])->name('admin.clients.documents.renamedoc');
Route::get('/documents/delete', [ClientDocumentsController::class, 'deletedocs'])->name('admin.clients.documents.deletedocs');
Route::get('/documents/get-visa-checklist', [ClientDocumentsController::class, 'getvisachecklist'])->name('admin.clients.documents.getvisachecklist');
Route::post('/documents/not-used', [ClientDocumentsController::class, 'notuseddoc'])->name('admin.clients.documents.notuseddoc');
Route::post('/documents/rename-checklist', [ClientDocumentsController::class, 'renamechecklistdoc'])->name('admin.clients.documents.renamechecklistdoc');
Route::post('/documents/back-to-doc', [ClientDocumentsController::class, 'backtodoc'])->name('admin.clients.documents.backtodoc');
Route::post('/documents/download', [ClientDocumentsController::class, 'download_document'])->name('admin.clients.documents.download');
Route::post('/documents/add-personal-category', [ClientDocumentsController::class, 'addPersonalDocCategory'])->name('admin.clients.documents.addPersonalDocCategory');
Route::post('/documents/update-personal-category', [ClientDocumentsController::class, 'updatePersonalDocCategory'])->name('admin.clients.documents.updatePersonalDocCategory');
Route::post('/documents/add-visa-category', [ClientDocumentsController::class, 'addVisaDocCategory'])->name('admin.clients.documents.addVisaDocCategory');
Route::post('/documents/update-visa-category', [ClientDocumentsController::class, 'updateVisaDocCategory'])->name('admin.clients.documents.updateVisaDocCategory');

/*---------- Client EOI/ROI Management ----------*/
Route::prefix('clients/{admin}/eoi-roi')->name('admin.clients.eoi-roi.')->group(function () {
    Route::get('/', [ClientEoiRoiController::class, 'index'])->name('index');
    Route::get('/calculate-points', [ClientEoiRoiController::class, 'calculatePoints'])->name('calculatePoints');
    Route::post('/', [ClientEoiRoiController::class, 'upsert'])->name('upsert');
    Route::get('/{eoiReference}', [ClientEoiRoiController::class, 'show'])->name('show');
    Route::delete('/{eoiReference}', [ClientEoiRoiController::class, 'destroy'])->name('destroy');
    Route::get('/{eoiReference}/reveal-password', [ClientEoiRoiController::class, 'revealPassword'])->name('revealPassword');
});

/*---------- Client Invoices & Receipts ----------*/
Route::get('/clients/saveaccountreport/{id}', 'Admin\ClientsController@saveaccountreport')->name('admin.clients.saveaccountreport');
Route::post('/clients/saveaccountreport', 'Admin\ClientsController@saveaccountreport')->name('admin.clients.saveaccountreport.update');

Route::get('/clients/saveinvoicereport/{id}', 'Admin\ClientsController@saveinvoicereport')->name('admin.clients.saveinvoicereport');
Route::post('/clients/saveinvoicereport', 'Admin\ClientsController@saveinvoicereport')->name('admin.clients.saveinvoicereport.update');

Route::get('/clients/saveadjustinvoicereport/{id}', 'Admin\ClientsController@saveadjustinvoicereport')->name('admin.clients.saveadjustinvoicereport');
Route::post('/clients/saveadjustinvoicereport', 'Admin\ClientsController@saveadjustinvoicereport')->name('admin.clients.saveadjustinvoicereport.update');

Route::get('/clients/saveofficereport/{id}', 'Admin\ClientsController@saveofficereport')->name('admin.clients.saveofficereport');
Route::post('/clients/saveofficereport', 'Admin\ClientsController@saveofficereport')->name('admin.clients.saveofficereport.update');

Route::get('/clients/savejournalreport/{id}', 'Admin\ClientsController@savejournalreport')->name('admin.clients.savejournalreport');
Route::post('/clients/savejournalreport', 'Admin\ClientsController@savejournalreport')->name('admin.clients.savejournalreport.update');

Route::post('/clients/isAnyInvoiceNoExistInDB', 'Admin\ClientsController@isAnyInvoiceNoExistInDB')->name('admin.clients.isAnyInvoiceNoExistInDB');
Route::post('/clients/listOfInvoice', 'Admin\ClientsController@listOfInvoice')->name('admin.clients.listOfInvoice');
Route::post('/clients/getTopReceiptValInDB', 'Admin\ClientsController@getTopReceiptValInDB')->name('admin.clients.getTopReceiptValInDB');
Route::post('/clients/getInfoByReceiptId', 'Admin\ClientsController@getInfoByReceiptId')->name('admin.clients.getInfoByReceiptId');
Route::get('/clients/genInvoice/{id}', 'Admin\ClientsController@genInvoice');
Route::post('/clients/sendToHubdoc/{id}', 'Admin\ClientsController@sendToHubdoc')->name('admin.clients.sendToHubdoc');
Route::get('/clients/checkHubdocStatus/{id}', 'Admin\ClientsController@checkHubdocStatus')->name('admin.clients.checkHubdocStatus');
Route::get('/clients/printPreview/{id}', 'Admin\ClientsController@printPreview');
Route::post('/clients/getTopInvoiceNoFromDB', 'Admin\ClientsController@getTopInvoiceNoFromDB')->name('admin.clients.getTopInvoiceNoFromDB');
Route::post('/clients/clientLedgerBalanceAmount', 'Admin\ClientsController@clientLedgerBalanceAmount')->name('admin.clients.clientLedgerBalanceAmount');

Route::get('/clients/invoicelist', 'Admin\ClientsController@invoicelist')->name('admin.clients.invoicelist');
Route::post('/void_invoice','Admin\ClientsController@void_invoice')->name('client.void_invoice');
Route::get('/clients/clientreceiptlist', 'Admin\ClientsController@clientreceiptlist')->name('admin.clients.clientreceiptlist');
Route::get('/clients/officereceiptlist', 'Admin\ClientsController@officereceiptlist')->name('admin.clients.officereceiptlist');
Route::get('/clients/journalreceiptlist', 'Admin\ClientsController@journalreceiptlist')->name('admin.clients.journalreceiptlist');
Route::post('/validate_receipt','Admin\ClientsController@validate_receipt')->name('client.validate_receipt');
Route::post('/delete_receipt','Admin\ClientsController@delete_receipt');

Route::get('/clients/genClientFundLedgerInvoice/{id}', 'Admin\ClientsController@genClientFundLedgerInvoice');
Route::get('/clients/genofficereceiptInvoice/{id}', 'Admin\ClientsController@genofficereceiptInvoice');
Route::post('/update-client-funds-ledger', 'Admin\ClientsController@updateClientFundsLedger')->name('admin.clients.update-client-funds-ledger');
Route::post('/clients/invoiceamount', 'Admin\ClientsController@getInvoiceAmount')->name('admin.clients.invoiceamount');

/*---------- Client Personal Details & Address ----------*/
Route::post('/clients/update-address', [ClientPersonalDetailsController::class, 'updateAddress'])->name('admin.clients.updateAddress');
Route::post('/clients/search-address-full', [ClientPersonalDetailsController::class, 'searchAddressFull'])->name('admin.clients.searchAddressFull');
Route::post('/clients/get-place-details', [ClientPersonalDetailsController::class, 'getPlaceDetails'])->name('admin.clients.getPlaceDetails');
Route::post('/address_auto_populate', 'Admin\ClientsController@address_auto_populate');

Route::post('/clients/fetchClientContactNo', [ClientPersonalDetailsController::class, 'fetchClientContactNo']);
Route::post('/clients/clientdetailsinfo/{id}', [ClientPersonalDetailsController::class, 'clientdetailsinfo'])->name('admin.clients.clientdetailsinfo');
Route::post('/clients/clientdetailsinfo', [ClientPersonalDetailsController::class, 'clientdetailsinfo'])->name('admin.clients.clientdetailsinfo.update');

Route::get('/admin/get-visa-types', [ClientPersonalDetailsController::class, 'getVisaTypes'])->name('admin.getVisaTypes');
Route::get('/admin/get-countries', [ClientPersonalDetailsController::class, 'getCountries'])->name('admin.getCountries');
Route::post('/updateOccupation', [ClientPersonalDetailsController::class, 'updateOccupation'])->name('admin.clients.updateOccupation');
Route::post('/leads/updateOccupation', [ClientPersonalDetailsController::class, 'updateOccupation'])->name('admin.leads.updateOccupation');

/*---------- Client Relationships ----------*/
Route::post('/admin/clients/search-partner', [ClientPersonalDetailsController::class, 'searchPartner'])->name('admin.clients.searchPartner');
Route::get('/admin/clients/search-partner-test', [ClientPersonalDetailsController::class, 'searchPartnerTest'])->name('admin.clients.searchPartnerTest');
Route::get('/admin/clients/test-bidirectional', [ClientPersonalDetailsController::class, 'testBidirectionalRemoval'])->name('admin.clients.testBidirectional');
Route::post('/admin/clients/save-relationship', [ClientPersonalDetailsController::class, 'saveRelationship'])->name('admin.clients.saveRelationship');

/*---------- Client Agreements & Forms ----------*/
Route::post('/clients/generateagreement', 'Admin\ClientsController@generateagreement')->name('clients.generateagreement');
Route::post('/clients/getMigrationAgentDetail', 'Admin\ClientsController@getMigrationAgentDetail')->name('admin.clients.getMigrationAgentDetail');
Route::post('/clients/getVisaAggreementMigrationAgentDetail', 'Admin\ClientsController@getVisaAggreementMigrationAgentDetail')->name('admin.clients.getVisaAggreementMigrationAgentDetail');
Route::post('/clients/getCostAssignmentMigrationAgentDetail', 'Admin\ClientsController@getCostAssignmentMigrationAgentDetail')->name('admin.clients.getCostAssignmentMigrationAgentDetail');
Route::post('/clients/savecostassignment', 'Admin\ClientsController@savecostassignment')->name('clients.savecostassignment');
Route::post('/clients/check-cost-assignment', 'Admin\ClientsController@checkCostAssignment');

// Lead cost assignment
Route::post('/clients/savecostassignmentlead', 'Admin\ClientsController@savecostassignmentlead')->name('clients.savecostassignmentlead');
Route::post('/clients/getCostAssignmentMigrationAgentDetailLead', 'Admin\ClientsController@getCostAssignmentMigrationAgentDetailLead')->name('clients.getCostAssignmentMigrationAgentDetailLead');

Route::post('/clients/{admin}/upload-agreement', 'Admin\ClientsController@uploadAgreement')->name('clients.uploadAgreement');

// Form 956
Route::post('/admin/forms', 'Admin\Form956Controller@store')->name('forms.store');
Route::get('/admin/forms/{form}', 'Admin\Form956Controller@show')->name('forms.show');
Route::get('/forms/{form}/preview', 'Admin\Form956Controller@previewPdf')->name('forms.preview');
Route::get('/forms/{form}/pdf', 'Admin\Form956Controller@generatePdf')->name('forms.pdf');

/*---------- Client Matter Management ----------*/
Route::get('/get-matter-templates', 'Admin\AdminController@getmattertemplates')->name('admin.clients.getmattertemplates');
Route::get('/get-client-matters/{clientId}', 'Admin\ClientsController@getClientMatters')->name('admin.clients.getClientMatters');
Route::post('/clients/fetchClientMatterAssignee', [ClientPersonalDetailsController::class, 'fetchClientMatterAssignee']);
Route::post('/clients/updateClientMatterAssignee', [ClientPersonalDetailsController::class, 'updateClientMatterAssignee']);

//matter checklist
Route::get('/upload-checklists', 'Admin\UploadChecklistController@index')->name('admin.upload_checklists.index');
Route::get('/upload-checklists/matter/{matterId}', 'Admin\UploadChecklistController@showByMatter')->name('admin.upload_checklists.matter');
Route::post('/upload-checklists/store', 'Admin\UploadChecklistController@store')->name('admin.upload_checklistsupload');

/*---------- Client Sessions & Follow-ups ----------*/
Route::post('/clients/personalfollowup/store', 'Admin\ClientsController@personalfollowup');
Route::post('/clients/updatefollowup/store', 'Admin\ClientsController@updatefollowup');
Route::post('/clients/reassignfollowup/store', 'Admin\ClientsController@reassignfollowupstore');
Route::post('/clients/update-session-completed', 'Admin\ClientsController@updatesessioncompleted')->name('admin.clients.updatesessioncompleted');
Route::post('/clients/getAllUser', 'Admin\ClientsController@getAllUser')->name('admin.clients.getAllUser');

/*---------- Client Portal ----------*/
Route::post('/clients/toggle-client-portal', 'Admin\ClientsController@toggleClientPortal')->name('admin.clients.toggleClientPortal');

/*---------- ANZSCO Occupation Search ----------*/
Route::get('/anzsco/search', [AnzscoOccupationController::class, 'search'])->name('admin.anzsco.search');

/*---------- Client Validation & Utilities ----------*/
Route::post('/check-email', 'Admin\ClientsController@checkEmail')->name('check.email');
Route::post('/check.phone', 'Admin\ClientsController@checkContact')->name('check.phone');
Route::post('/save_tag', 'Admin\ClientsController@save_tag');
Route::post('/save-references', 'Admin\ClientsController@savereferences')->name('references.store');
Route::post('/check-star-client', 'Admin\ClientsController@checkStarClient')->name('check.star.client');
Route::post('/merge_records','Admin\ClientsController@merge_records')->name('client.merge_records');

/*---------- Webhook Integration ----------*/
Route::post('/send-webhook', 'Admin\ClientsController@sendToWebhook')->name('admin.send-webhook');

/*---------- Visa Expiry Messages ----------*/
Route::get('/fetch-visa_expiry_messages', 'Admin\AdminController@fetchvisaexpirymessages');

/*---------- Public Email Verification (No Auth Required) ----------*/
// This route is outside admin middleware for public access
Route::get('/verify-email/{token}', [EmailVerificationController::class, 'verifyEmail'])->name('admin.clients.email.verify');

