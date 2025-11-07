<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CRM\ClientsController;
use App\Http\Controllers\CRM\ClientAccountsController;
use App\Http\Controllers\CRM\ClientEoiRoiController;
use App\Http\Controllers\CRM\Clients\ClientNotesController;
use App\Http\Controllers\CRM\Clients\ClientDocumentsController;
use App\Http\Controllers\CRM\ClientPersonalDetailsController;
use App\Http\Controllers\CRM\PhoneVerificationController;
use App\Http\Controllers\CRM\EmailVerificationController;
use App\Http\Controllers\AdminConsole\AnzscoOccupationController;

/*
|--------------------------------------------------------------------------
| Client Management Routes
|--------------------------------------------------------------------------
|
| All routes for client CRUD operations, documents, verification, invoices,
| EOI/ROI management, notes, agreements, and related functionality.
|
| Prefix: None (routes at root level)
| Middleware: auth:admin (inherited from web.php)
|
*/

/*---------- Client CRUD Operations ----------*/
Route::get('/clients', [ClientsController::class, 'index'])->name('clients.index');
Route::get('/clientsmatterslist', [ClientsController::class, 'clientsmatterslist'])->name('clients.clientsmatterslist');
Route::get('/clientsemaillist', [ClientsController::class, 'clientsemaillist'])->name('clients.clientsemaillist');
Route::post('/clients/store', [ClientsController::class, 'store'])->name('clients.store');
Route::get('/clients/edit/{id}', [ClientsController::class, 'edit'])->name('clients.edit');
Route::post('/clients/edit', [ClientsController::class, 'edit'])->name('clients.update');
Route::post('/clients/save-section', [ClientPersonalDetailsController::class, 'saveSection'])->name('clients.saveSection');
Route::get('/clients/partner-eoi-data/{partnerId}', [ClientPersonalDetailsController::class, 'getPartnerEoiData'])->name('clients.partnerEoiData');

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
Route::get('/clients/detail/{client_id}/{client_unique_matter_ref_no?}/{tab?}', [ClientsController::class, 'detail'])->name('clients.detail');

/*---------- Client Communication ----------*/
Route::get('/clients/get-recipients', [ClientsController::class, 'getrecipients'])->name('clients.getrecipients');
Route::get('/clients/get-onlyclientrecipients', [ClientsController::class, 'getonlyclientrecipients'])->name('clients.getonlyclientrecipients');
Route::get('/clients/get-allclients', [ClientsController::class, 'getallclients'])->name('clients.getallclients');
Route::get('/clients/change_assignee', [ClientsController::class, 'change_assignee']);
Route::get('/get-templates', 'CRM\CRMUtilityController@gettemplates')->name('clients.gettemplates');
Route::post('/sendmail', 'CRM\CRMUtilityController@sendmail')->name('clients.sendmail');

Route::post('/upload-mail', 'CRM\ClientsController@uploadmail');

// LEGACY ROUTES (using PEAR - deprecated)
// Route::post('/upload-fetch-mail', 'CRM\ClientsController@uploadfetchmail'); //upload inbox email
// Route::post('/upload-sent-fetch-mail', 'CRM\ClientsController@uploadsentfetchmail'); //upload sent email

// MODERN ROUTES (using Python microservice - recommended)
Route::post('/upload-fetch-mail', 'CRM\EmailUploadController@uploadInboxEmails')->name('email.upload.inbox');
Route::post('/upload-sent-fetch-mail', 'CRM\EmailUploadController@uploadSentEmails')->name('email.upload.sent');
Route::get('/email/check-service', 'CRM\EmailUploadController@checkPythonService')->name('email.check.service');

Route::post('/reassiginboxemail', 'CRM\ClientsController@reassiginboxemail')->name('clients.reassiginboxemail');
Route::post('/reassigsentemail', 'CRM\ClientsController@reassigsentemail')->name('clients.reassigsentemail');
Route::post('/listAllMattersWRTSelClient', 'CRM\ClientsController@listAllMattersWRTSelClient')->name('clients.listAllMattersWRTSelClient');
Route::post('/updatemailreadbit', 'CRM\ClientsController@updatemailreadbit')->name('clients.updatemailreadbit');

Route::post('/clients/filter-emails', 'CRM\ClientsController@filterEmails')->name('clients.filter.emails');
Route::post('/clients/filter-sentemails', 'CRM\ClientsController@filterSentEmails')->name('clients.filter.sentmails');
Route::post('/mail/enhance', 'CRM\ClientsController@enhanceMessage')->name('mail.enhance');

/*---------- Email Labels Management ----------*/
Route::prefix('email-labels')->name('email-labels.')->group(function() {
    Route::get('/', 'CRM\EmailLabelController@index')->name('index');
    Route::post('/', 'CRM\EmailLabelController@store')->name('store');
    Route::post('/apply', 'CRM\EmailLabelController@apply')->name('apply');
    Route::delete('/remove', 'CRM\EmailLabelController@remove')->name('remove');
});

/*---------- Email Attachments ----------*/
Route::prefix('mail-attachments')->name('mail-attachments.')->group(function() {
    Route::get('/{id}/download', 'CRM\MailReportAttachmentController@download')->name('download');
    Route::get('/{id}/preview', 'CRM\MailReportAttachmentController@preview')->name('preview');
    Route::get('/email/{mailReportId}/download-all', 'CRM\MailReportAttachmentController@downloadAll')->name('download-all');
});

/*---------- Client Notes ----------*/
Route::post('/create-note', [ClientNotesController::class, 'createnote'])->name('clients.createnote');
Route::post('/update-note-datetime', [ClientNotesController::class, 'updateNoteDatetime'])->name('clients.updateNoteDatetime');
Route::get('/getnotedetail', [ClientNotesController::class, 'getnotedetail'])->name('clients.getnotedetail');
Route::get('/deletenote', [ClientNotesController::class, 'deletenote'])->name('clients.deletenote');
Route::get('/viewnotedetail', [ClientNotesController::class, 'viewnotedetail']);
Route::get('/viewapplicationnote', [ClientNotesController::class, 'viewapplicationnote']);
Route::post('/saveprevvisa', [ClientNotesController::class, 'saveprevvisa']);
Route::post('/saveonlineprimaryform', [ClientNotesController::class, 'saveonlineform']);
Route::post('/saveonlinesecform', [ClientNotesController::class, 'saveonlineform']);
Route::post('/saveonlinechildform', [ClientNotesController::class, 'saveonlineform']);
Route::get('/get-notes', [ClientNotesController::class, 'getnotes'])->name('clients.getnotes');
Route::get('/pinnote', [ClientNotesController::class, 'pinnote']);

Route::post('/convert-activity-to-note', 'CRM\ClientsController@convertActivityToNote')->name('clients.convertActivityToNote');

/*---------- Client Status & Archive ----------*/
Route::get('/archived', 'CRM\ClientsController@archived')->name('clients.archived');
Route::get('/change-client-status', 'CRM\ClientsController@updateclientstatus')->name('clients.updateclientstatus');
Route::get('/get-activities', 'CRM\ClientsController@activities')->name('clients.activities');
Route::get('/deletecostagreement', [ClientsController::class, 'deletecostagreement'])->name('clients.deletecostagreement');
Route::get('/deleteactivitylog', [ClientsController::class, 'deleteactivitylog'])->name('clients.deleteactivitylog');
Route::post('/not-picked-call', [ClientsController::class, 'notpickedcall'])->name('clients.notpickedcall');
Route::get('/pinactivitylog', 'CRM\ClientsController@pinactivitylog');

/*---------- Client Services ----------*/
Route::post('/interested-service', 'CRM\ClientsController@interestedService');
Route::post('/edit-interested-service', 'CRM\ClientsController@editinterestedService');
Route::get('/get-services', 'CRM\ClientsController@getServices');
Route::post('/servicesavefee', 'CRM\ClientsController@servicesavefee');
Route::get('/getintrestedservice', 'CRM\ClientsController@getintrestedservice');
Route::get('/getintrestedserviceedit', 'CRM\ClientsController@getintrestedserviceedit');

Route::get('/get-application-lists', 'CRM\ClientsController@getapplicationlists')->name('clients.getapplicationlists');
Route::post('/saveapplication', 'CRM\ClientsController@saveapplication')->name('clients.saveapplication');
Route::get('/convertapplication', 'CRM\ClientsController@convertapplication')->name('clients.convertapplication');
Route::get('/deleteservices', 'CRM\ClientsController@deleteservices')->name('clients.deleteservices');
Route::post('/savetoapplication', 'CRM\ClientsController@savetoapplication');

Route::post('/client/createservicetaken', 'CRM\ClientsController@createservicetaken');
Route::post('/client/removeservicetaken', 'CRM\ClientsController@removeservicetaken');
Route::post('/client/getservicetaken', 'CRM\ClientsController@getservicetaken');

/*---------- Client Documents Management ----------*/
Route::post('/documents/add-edu-checklist', [ClientDocumentsController::class, 'addedudocchecklist'])->name('clients.documents.addedudocchecklist');
Route::post('/documents/upload-edu-document', [ClientDocumentsController::class, 'uploadedudocument'])->name('clients.documents.uploadedudocument');
Route::post('/documents/add-visa-checklist', [ClientDocumentsController::class, 'addvisadocchecklist'])->name('clients.documents.addvisadocchecklist');
Route::post('/documents/upload-visa-document', [ClientDocumentsController::class, 'uploadvisadocument'])->name('clients.documents.uploadvisadocument');
Route::post('/documents/rename', [ClientDocumentsController::class, 'renamedoc'])->name('clients.documents.renamedoc');
Route::get('/documents/delete', [ClientDocumentsController::class, 'deletedocs'])->name('clients.documents.deletedocs');
Route::get('/documents/get-visa-checklist', [ClientDocumentsController::class, 'getvisachecklist'])->name('clients.documents.getvisachecklist');
Route::post('/documents/not-used', [ClientDocumentsController::class, 'notuseddoc'])->name('clients.documents.notuseddoc');
Route::post('/documents/rename-checklist', [ClientDocumentsController::class, 'renamechecklistdoc'])->name('clients.documents.renamechecklistdoc');
Route::post('/documents/back-to-doc', [ClientDocumentsController::class, 'backtodoc'])->name('clients.documents.backtodoc');
Route::post('/documents/download', [ClientDocumentsController::class, 'download_document'])->name('clients.documents.download');
Route::post('/documents/add-personal-category', [ClientDocumentsController::class, 'addPersonalDocCategory'])->name('clients.documents.addPersonalDocCategory');
Route::post('/documents/update-personal-category', [ClientDocumentsController::class, 'updatePersonalDocCategory'])->name('clients.documents.updatePersonalDocCategory');
Route::post('/documents/add-visa-category', [ClientDocumentsController::class, 'addVisaDocCategory'])->name('clients.documents.addVisaDocCategory');
Route::post('/documents/update-visa-category', [ClientDocumentsController::class, 'updateVisaDocCategory'])->name('clients.documents.updateVisaDocCategory');

/*---------- Client EOI/ROI Management ----------*/
Route::prefix('clients/{client}/eoi-roi')->name('clients.eoi-roi.')->group(function () {
    Route::get('/', [ClientEoiRoiController::class, 'index'])->name('index');
    Route::get('/calculate-points', [ClientEoiRoiController::class, 'calculatePoints'])->name('calculatePoints');
    Route::post('/', [ClientEoiRoiController::class, 'upsert'])->name('upsert');
    Route::get('/{eoiReference}', [ClientEoiRoiController::class, 'show'])->name('show');
    Route::delete('/{eoiReference}', [ClientEoiRoiController::class, 'destroy'])->name('destroy');
    Route::get('/{eoiReference}/reveal-password', [ClientEoiRoiController::class, 'revealPassword'])->name('revealPassword');
});

/*---------- Client Invoices & Receipts ----------*/
Route::get('/clients/saveaccountreport/{id}', 'CRM\ClientAccountsController@saveaccountreport')->name('clients.saveaccountreport');
Route::post('/clients/saveaccountreport', 'CRM\ClientAccountsController@saveaccountreport')->name('clients.saveaccountreport.update');

/* Test Route for Python Processing */
Route::post('/clients/test-python-accounting', 'CRM\ClientsController@testPythonAccounting')->name('clients.test-python-accounting');

Route::get('/clients/saveinvoicereport/{id}', 'CRM\ClientAccountsController@saveinvoicereport')->name('clients.saveinvoicereport');
Route::post('/clients/saveinvoicereport', 'CRM\ClientAccountsController@saveinvoicereport')->name('clients.saveinvoicereport.update');

Route::get('/clients/saveadjustinvoicereport/{id}', 'CRM\ClientAccountsController@saveadjustinvoicereport')->name('clients.saveadjustinvoicereport');
Route::post('/clients/saveadjustinvoicereport', 'CRM\ClientAccountsController@saveadjustinvoicereport')->name('clients.saveadjustinvoicereport.update');

Route::get('/clients/saveofficereport/{id}', 'CRM\ClientAccountsController@saveofficereport')->name('clients.saveofficereport');
Route::post('/clients/saveofficereport', 'CRM\ClientAccountsController@saveofficereport')->name('clients.saveofficereport.update');

Route::get('/clients/savejournalreport/{id}', 'CRM\ClientAccountsController@savejournalreport')->name('clients.savejournalreport');
Route::post('/clients/savejournalreport', 'CRM\ClientAccountsController@savejournalreport')->name('clients.savejournalreport.update');

Route::post('/clients/isAnyInvoiceNoExistInDB', 'CRM\ClientAccountsController@isAnyInvoiceNoExistInDB')->name('clients.isAnyInvoiceNoExistInDB');
Route::post('/clients/listOfInvoice', 'CRM\ClientAccountsController@listOfInvoice')->name('clients.listOfInvoice');
Route::post('/clients/getTopReceiptValInDB', 'CRM\ClientAccountsController@getTopReceiptValInDB')->name('clients.getTopReceiptValInDB');
Route::post('/clients/getInfoByReceiptId', 'CRM\ClientAccountsController@getInfoByReceiptId')->name('clients.getInfoByReceiptId');
Route::get('/clients/genInvoice/{id}', 'CRM\ClientAccountsController@genInvoice');
Route::post('/clients/sendToHubdoc/{id}', 'CRM\ClientAccountsController@sendToHubdoc')->name('clients.sendToHubdoc');
Route::get('/clients/checkHubdocStatus/{id}', 'CRM\ClientAccountsController@checkHubdocStatus')->name('clients.checkHubdocStatus');
Route::get('/clients/printPreview/{id}', 'CRM\ClientAccountsController@printPreview');
Route::post('/clients/getTopInvoiceNoFromDB', 'CRM\ClientAccountsController@getTopInvoiceNoFromDB')->name('clients.getTopInvoiceNoFromDB');
Route::post('/clients/clientLedgerBalanceAmount', 'CRM\ClientAccountsController@clientLedgerBalanceAmount')->name('clients.clientLedgerBalanceAmount');

Route::get('/clients/analytics-dashboard', 'CRM\ClientAccountsController@analyticsDashboard')->name('clients.analytics-dashboard');
Route::get('/clients/invoicelist', 'CRM\ClientAccountsController@invoicelist')->name('clients.invoicelist');
Route::post('/void_invoice','CRM\ClientAccountsController@void_invoice')->name('client.void_invoice');
Route::get('/clients/clientreceiptlist', 'CRM\ClientAccountsController@clientreceiptlist')->name('clients.clientreceiptlist');
Route::get('/clients/officereceiptlist', 'CRM\ClientAccountsController@officereceiptlist')->name('clients.officereceiptlist');
Route::get('/clients/journalreceiptlist', 'CRM\ClientAccountsController@journalreceiptlist')->name('clients.journalreceiptlist');
Route::post('/validate_receipt','CRM\ClientAccountsController@validate_receipt')->name('client.validate_receipt');
Route::post('/delete_receipt','CRM\ClientAccountsController@delete_receipt');

Route::get('/clients/genClientFundReceipt/{id}', 'CRM\ClientAccountsController@genClientFundReceipt');
Route::get('/clients/genOfficeReceipt/{id}', 'CRM\ClientAccountsController@genofficereceiptInvoice');
Route::post('/update-client-funds-ledger', 'CRM\ClientAccountsController@updateClientFundsLedger')->name('clients.update-client-funds-ledger');
Route::post('/update-office-receipt', 'CRM\ClientAccountsController@updateOfficeReceipt')->name('clients.updateOfficeReceipt');
Route::post('/get-invoices-by-matter', 'CRM\ClientAccountsController@getInvoicesByMatter')->name('clients.getInvoicesByMatter');
Route::post('/update-client-fund-ledger', 'CRM\ClientAccountsController@updateClientFundLedger')->name('clients.updateClientFundLedger');
Route::post('/clients/invoiceamount', 'CRM\ClientAccountsController@getInvoiceAmount')->name('clients.invoiceamount');

// Receipt document uploads
Route::post('/clients/upload-clientreceipt-document', 'CRM\ClientAccountsController@uploadclientreceiptdocument')->name('clients.uploadclientreceiptdocument');
Route::post('/clients/upload-officereceipt-document', 'CRM\ClientAccountsController@uploadofficereceiptdocument')->name('clients.uploadofficereceiptdocument');
Route::post('/clients/upload-journalreceipt-document', 'CRM\ClientAccountsController@uploadjournalreceiptdocument')->name('clients.uploadjournalreceiptdocument');

/*---------- Client Personal Details & Address ----------*/
Route::post('/clients/update-address', [ClientPersonalDetailsController::class, 'updateAddress'])->name('clients.updateAddress');
Route::post('/clients/search-address-full', [ClientPersonalDetailsController::class, 'searchAddressFull'])->name('clients.searchAddressFull');
Route::post('/clients/get-place-details', [ClientPersonalDetailsController::class, 'getPlaceDetails'])->name('clients.getPlaceDetails');
Route::post('/address_auto_populate', 'CRM\ClientsController@address_auto_populate');

Route::post('/clients/fetchClientContactNo', [ClientPersonalDetailsController::class, 'fetchClientContactNo']);
Route::post('/clients/clientdetailsinfo/{id}', [ClientPersonalDetailsController::class, 'clientdetailsinfo'])->name('clients.clientdetailsinfo');
Route::post('/clients/clientdetailsinfo', [ClientPersonalDetailsController::class, 'clientdetailsinfo'])->name('clients.clientdetailsinfo.update');

Route::get('/get-visa-types', [ClientPersonalDetailsController::class, 'getVisaTypes'])->name('getVisaTypes');
Route::get('/get-countries', [ClientPersonalDetailsController::class, 'getCountries'])->name('getCountries');
Route::post('/updateOccupation', [ClientPersonalDetailsController::class, 'updateOccupation'])->name('clients.updateOccupation');
Route::post('/leads/updateOccupation', [ClientPersonalDetailsController::class, 'updateOccupation'])->name('leads.updateOccupation');

/*---------- Client Relationships ----------*/
Route::post('/clients/search-partner', [ClientPersonalDetailsController::class, 'searchPartner'])->name('clients.searchPartner');
Route::get('/clients/search-partner-test', [ClientPersonalDetailsController::class, 'searchPartnerTest'])->name('clients.searchPartnerTest');
Route::get('/clients/test-bidirectional', [ClientPersonalDetailsController::class, 'testBidirectionalRemoval'])->name('clients.testBidirectional');
Route::post('/clients/save-relationship', [ClientPersonalDetailsController::class, 'saveRelationship'])->name('clients.saveRelationship');

/*---------- Client Agreements & Forms ----------*/
Route::post('/clients/generateagreement', 'CRM\ClientsController@generateagreement')->name('clients.generateagreement');
Route::post('/clients/getMigrationAgentDetail', 'CRM\ClientsController@getMigrationAgentDetail')->name('clients.getMigrationAgentDetail');
Route::post('/clients/getVisaAggreementMigrationAgentDetail', 'CRM\ClientsController@getVisaAggreementMigrationAgentDetail')->name('clients.getVisaAggreementMigrationAgentDetail');
Route::post('/clients/getCostAssignmentMigrationAgentDetail', 'CRM\ClientsController@getCostAssignmentMigrationAgentDetail')->name('clients.getCostAssignmentMigrationAgentDetail');
Route::post('/clients/savecostassignment', 'CRM\ClientsController@savecostassignment')->name('clients.savecostassignment');
Route::post('/clients/check-cost-assignment', 'CRM\ClientsController@checkCostAssignment');

// Lead cost assignment
Route::post('/clients/savecostassignmentlead', 'CRM\ClientsController@savecostassignmentlead')->name('clients.savecostassignmentlead');
Route::post('/clients/getCostAssignmentMigrationAgentDetailLead', 'CRM\ClientsController@getCostAssignmentMigrationAgentDetailLead')->name('clients.getCostAssignmentMigrationAgentDetailLead');

Route::post('/clients/{admin}/upload-agreement', 'CRM\ClientsController@uploadAgreement')->name('clients.uploadAgreement');

// Form 956
Route::post('/forms', 'CRM\Form956Controller@store')->name('forms.store');
Route::get('/forms/{form}', 'CRM\Form956Controller@show')->name('forms.show');
Route::get('/forms/{form}/preview', 'CRM\Form956Controller@previewPdf')->name('forms.preview');
Route::get('/forms/{form}/pdf', 'CRM\Form956Controller@generatePdf')->name('forms.pdf');

/*---------- Client Matter Management ----------*/
Route::get('/get-matter-templates', 'CRM\CRMUtilityController@getmattertemplates')->name('clients.getmattertemplates');
Route::get('/get-client-matters/{clientId}', 'CRM\ClientsController@getClientMatters')->name('clients.getClientMatters');
Route::post('/clients/fetchClientMatterAssignee', [ClientPersonalDetailsController::class, 'fetchClientMatterAssignee']);
Route::post('/clients/updateClientMatterAssignee', [ClientPersonalDetailsController::class, 'updateClientMatterAssignee']);

//matter checklist
Route::get('/upload-checklists', 'CRM\UploadChecklistController@index')->name('upload_checklists.index');
Route::get('/upload-checklists/matter/{matterId}', 'CRM\UploadChecklistController@showByMatter')->name('upload_checklists.matter');
Route::post('/upload-checklists/store', 'CRM\UploadChecklistController@store')->name('upload_checklistsupload');

/*---------- Client Sessions & Follow-ups ----------*/
Route::post('/clients/personalfollowup/store', 'CRM\ClientsController@personalfollowup');
Route::post('/clients/updatefollowup/store', 'CRM\ClientsController@updatefollowup');
Route::post('/clients/reassignfollowup/store', 'CRM\ClientsController@reassignfollowupstore');
Route::post('/clients/update-session-completed', 'CRM\ClientsController@updatesessioncompleted')->name('clients.updatesessioncompleted');
Route::post('/clients/getAllUser', 'CRM\ClientsController@getAllUser')->name('clients.getAllUser');

/*---------- Client Portal ----------*/
Route::post('/clients/toggle-client-portal', 'CRM\ClientPortalController@toggleClientPortal')->name('clients.toggleClientPortal');

/*---------- ANZSCO Occupation Search ----------*/
Route::get('/anzsco/search', [AnzscoOccupationController::class, 'search'])->name('anzsco.search');
Route::get('/anzsco/code/{code}', [AnzscoOccupationController::class, 'getByCode'])->name('anzsco.getByCode');

/*---------- Client Validation & Utilities ----------*/
Route::post('/check-email', 'CRM\ClientsController@checkEmail')->name('check.email');
Route::post('/check.phone', 'CRM\ClientsController@checkContact')->name('check.phone');
Route::post('/save_tag', 'CRM\ClientsController@save_tag');
Route::post('/save-references', 'CRM\ClientsController@savereferences')->name('references.store');
Route::post('/check-star-client', 'CRM\ClientsController@checkStarClient')->name('check.star.client');
Route::post('/merge_records','CRM\ClientsController@merge_records')->name('client.merge_records');

/*---------- Webhook Integration ----------*/
Route::post('/send-webhook', 'CRM\ClientsController@sendToWebhook')->name('send-webhook');

/*---------- Visa Expiry Messages ----------*/
Route::get('/fetch-visa_expiry_messages', 'CRM\CRMUtilityController@fetchvisaexpirymessages');

/*---------- Public Email Verification (No Auth Required) ----------*/
// This route is outside admin middleware for public access
Route::get('/verify-email/{token}', [EmailVerificationController::class, 'verifyEmail'])->name('clients.email.verify');

