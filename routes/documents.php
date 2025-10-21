<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\PublicDocumentController;
use App\Http\Controllers\Admin\SignatureDashboardController;

/*
|--------------------------------------------------------------------------
| Document Signature Routes
|--------------------------------------------------------------------------
|
| WORKFLOW:
| 1. Admin prepares document for signing (CRUD operations)
| 2. Admin sends signing link via email to client
| 3. Client receives email with link: /sign/{id}/{token}
| 4. Client signs document (no login - token validated)
| 5. Client sees thank you page & downloads signed document
| 6. Admin views completed document in admin panel
|
| ROUTE ORGANIZATION:
| - Admin routes: /admin/documents/* (auth:admin required)
| - Public routes: /sign/* and /documents/* (token-based validation)
|
*/

/*
|--------------------------------------------------------------------------
| ADMIN DOCUMENT MANAGEMENT ROUTES
|--------------------------------------------------------------------------
| Prefix: /admin
| Middleware: auth:admin
| Route Names: admin.documents.*
*/

Route::prefix('admin')->middleware('auth:admin')->group(function () {

/*---------- Document CRUD Operations ----------*/
Route::get('/documents', [AdminDocumentController::class, 'index'])
    ->name('admin.documents.index');

Route::get('/documents/create', [AdminDocumentController::class, 'create'])
    ->name('admin.documents.create');

Route::post('/documents', [AdminDocumentController::class, 'store'])
    ->name('admin.documents.store');

Route::get('/documents/{id}/edit', [AdminDocumentController::class, 'edit'])
    ->name('admin.documents.edit');

Route::patch('/documents/{id}', [AdminDocumentController::class, 'update'])
    ->name('admin.documents.update');

/*---------- Admin Signing & Reminder Operations ----------*/
Route::post('/documents/{document}/sign', [AdminDocumentController::class, 'submitSignatures'])
    ->name('admin.documents.submitSignatures');

Route::post('/documents/{document}/send-reminder', [AdminDocumentController::class, 'sendReminder'])
    ->name('admin.documents.sendReminder');

Route::post('/documents/{document}/send-signing-link', [AdminDocumentController::class, 'sendSigningLink'])
    ->name('admin.documents.sendSigningLink');

Route::get('/documents/{document}/sign', [AdminDocumentController::class, 'showSignForm'])
    ->name('admin.documents.showSignForm');

Route::get('/sign/{id}/{token}', [AdminDocumentController::class, 'sign'])
    ->name('admin.documents.sign');

/*---------- Admin Document Viewing & Download ----------*/
Route::get('/documents/{id}/page/{page}', [AdminDocumentController::class, 'getPage'])
    ->name('admin.documents.page');

Route::get('/documents/{id}/download-signed', [AdminDocumentController::class, 'downloadSigned'])
    ->name('admin.documents.download.signed');

Route::get('/documents/{id}/download-signed-and-thankyou', [AdminDocumentController::class, 'downloadSignedAndThankyou'])
    ->name('admin.documents.download_and_thankyou');

Route::get('/documents/thankyou/{id?}', [AdminDocumentController::class, 'thankyou'])
    ->name('admin.documents.thankyou');

/*---------- Admin Utilities ----------*/
Route::get('/test-signature', function () {
    return view('test-signature');
})->name('test.signature');

// DOC/DOCX to PDF Converter Routes
Route::get('/doc-to-pdf', 'Admin\DocToPdfController@showForm')->name('admin.doc-to-pdf.form');
Route::post('/doc-to-pdf/convert', 'Admin\DocToPdfController@convertLocal')->name('admin.doc-to-pdf.convert');
Route::get('/doc-to-pdf/test', 'Admin\DocToPdfController@testLocalConversion')->name('admin.doc-to-pdf.test');
Route::get('/doc-to-pdf/test-python', 'Admin\DocToPdfController@testPythonConversion')->name('admin.doc-to-pdf.test-python');
Route::get('/doc-to-pdf/debug', 'Admin\DocToPdfController@debugConfig')->name('admin.doc-to-pdf.debug');

/*---------- Signature Dashboard Routes ----------*/
Route::prefix('signatures')->group(function () {
    Route::get('/', [SignatureDashboardController::class, 'index'])->name('admin.signatures.index');
    Route::get('/analytics', [SignatureDashboardController::class, 'analytics'])->name('admin.signatures.analytics');
    Route::get('/export', [SignatureDashboardController::class, 'exportAudit'])->name('admin.signatures.export');
    Route::get('/create', [SignatureDashboardController::class, 'create'])->name('admin.signatures.create');
    Route::post('/', [SignatureDashboardController::class, 'store'])->name('admin.signatures.store');
    Route::post('/suggest-association', [SignatureDashboardController::class, 'suggestAssociation'])->name('admin.signatures.suggest-association');
    Route::post('/preview-email', [SignatureDashboardController::class, 'previewEmail'])->name('admin.signatures.preview-email');
    
    // Bulk actions (Phase 6)
    Route::post('/bulk-archive', [SignatureDashboardController::class, 'bulkArchive'])->name('admin.signatures.bulk-archive');
    Route::post('/bulk-void', [SignatureDashboardController::class, 'bulkVoid'])->name('admin.signatures.bulk-void');
    Route::post('/bulk-resend', [SignatureDashboardController::class, 'bulkResend'])->name('admin.signatures.bulk-resend');
    
    Route::get('/{id}', [SignatureDashboardController::class, 'show'])->name('admin.signatures.show');
    Route::post('/{id}/reminder', [SignatureDashboardController::class, 'sendReminder'])->name('admin.signatures.reminder');
    Route::get('/{id}/copy-link', [SignatureDashboardController::class, 'copyLink'])->name('admin.signatures.copy-link');
    
    // Association management (Phase 3)
    Route::post('/{id}/associate', [SignatureDashboardController::class, 'associate'])->name('admin.signatures.associate');
    Route::post('/{id}/detach', [SignatureDashboardController::class, 'detach'])->name('admin.signatures.detach');
});

/*---------- Client Matters API ----------*/
Route::get('/clients/{id}/matters', [SignatureDashboardController::class, 'getClientMatters'])->name('admin.clients.matters');

}); // End of admin routes group

/*
|--------------------------------------------------------------------------
| PUBLIC DOCUMENT SIGNING ROUTES
|--------------------------------------------------------------------------
| No authentication required - access controlled by token validation
| Route Names: public.documents.*
|
| These routes allow clients to sign documents via email links without
| requiring login. Security is handled through unique tokens sent via email.
*/

/*---------- Public Signing Interface ----------*/
Route::get('/sign/{id}/{token}', [PublicDocumentController::class, 'sign'])
    ->name('public.documents.sign');

Route::post('/documents/{document}/sign', [PublicDocumentController::class, 'submitSignatures'])
    ->name('public.documents.submitSignatures');

/*---------- Public Document Viewing ----------*/
Route::get('/documents/{id}/page/{page}', [PublicDocumentController::class, 'getPage'])
    ->name('public.documents.page');

Route::get('/documents/{id?}', [PublicDocumentController::class, 'index'])
    ->name('public.documents.index');

/*---------- Public Download & Thank You ----------*/
Route::get('/documents/{id}/download-signed', [PublicDocumentController::class, 'downloadSigned'])
    ->name('public.documents.download.signed');

Route::get('/documents/{id}/download-signed-and-thankyou', [PublicDocumentController::class, 'downloadSignedAndThankyou'])
    ->name('public.documents.download_and_thankyou');

Route::get('/documents/thankyou/{id?}', [PublicDocumentController::class, 'thankyou'])
    ->name('public.documents.thankyou');

/*---------- Public Reminder ----------*/
Route::post('/documents/{document}/send-reminder', [PublicDocumentController::class, 'sendReminder'])
    ->name('public.documents.sendReminder');

