<?php

use App\Http\Controllers\Admin\ClientsController;

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

/*********************General Function for Both (Front-end & Back-end) ***********************/
/* Route::post('/get_states', 'HomeController@getStates');
Route::post('/get_product_views', 'HomeController@getProductViews');
Route::post('/get_product_other_info', 'HomeController@getProductOtherInformation');
Route::post('/delete_action', 'HomeController@deleteAction')->middleware('auth');
 */

// Root route - redirect to admin login
Route::get('/', function() {
    return redirect()->route('admin.login');
});

Route::get('/clear-cache', function() {

    Artisan::call('config:clear');
	Artisan::call('view:clear');
   $exitCode = Artisan::call('route:clear');
   $exitCode = Artisan::call('route:cache');
     /* $exitCode = \Artisan::call('BirthDate:birthdate');
        $output = \Artisan::output();
        return $output;  */
    // return what you want
});

/*********************Exception Handling ***********************/
Route::get('/exception', 'ExceptionController@index')->name('exception');
Route::post('/exception', 'ExceptionController@index')->name('exception');

/*********************Front Panel Start ***********************/


//Login and Register
Auth::routes();


// Frontend Route

Route::get('/book-an-appointment', 'HomeController@bookappointment')->name('bookappointment');
Route::post('/book-an-appointment/store', 'AppointmentBookController@store');
Route::post('/book-an-appointment/storepaid', 'AppointmentBookController@storepaid')->name('stripe.post');
Route::post('/getdatetime', 'HomeController@getdatetime');

//Route::post('/contact', 'HomeController@contact');
Route::post('/getdisableddatetime', 'HomeController@getdisableddatetime');
Route::get('/refresh-captcha', 'HomeController@refresh_captcha');

//Route::get('page/{slug}', 'HomeController@Page')->name('page.slug');
Route::get('sicaptcha', 'HomeController@sicaptcha')->name('sicaptcha');
Route::get('invoice/secure/{slug}', 'InvoiceController@invoice')->name('invoice');
Route::get('/invoice/download/{id}', 'InvoiceController@customer_invoice_download')->name('invoice.customer_invoice_download');
Route::get('/invoice/print/{id}', 'InvoiceController@customer_invoice_print')->name('invoice.customer_invoice_print');
Route::get('/profile', 'HomeController@myprofile')->name('profile');

Route::post('/getdatetime', 'HomeController@getdatetime');
Route::post('/getdatetimebackend', 'HomeController@getdatetimebackend');
Route::post('/getdisableddatetime', 'HomeController@getdisableddatetime');


Route::get('stripe/{appointmentId}', 'HomeController@stripe');
Route::post('stripe', 'HomeController@stripePost')->name('stripe.post1');

/*---------------Email manager Route-------------------*/
include_once 'emailUser.php';

/*********************Admin Panel Start ***********************/
Route::prefix('admin')->group(function() {

    //Login and Logout
    Route::get('/', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::get('/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login', 'Auth\AdminLoginController@login')->name('admin.login');
    Route::post('/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');



    //Route::post('/captcha-validation', [CaptchaServiceController::class, 'capthcaFormValidate']);
    //Route::get('/reload-captcha', 'Auth\AdminLoginController@reloadCaptcha');

	//General
    Route::get('/dashboard', 'Admin\AdminController@dashboard')->name('admin.dashboard');
    Route::post('/dashboard/column-preferences', 'Admin\AdminController@saveColumnPreferences')->name('admin.dashboard.column-preferences');
    //Route::get('/dashboard', 'Admin\NewWordController@dashboard')->name('new_word.dashboard');

    Route::get('/get_customer_detail', 'Admin\AdminController@CustomerDetail')->name('admin.get_customer_detail');
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
    Route::get('/website_setting', 'Admin\AdminController@websiteSetting')->name('admin.website_setting');
    Route::post('/website_setting', 'Admin\AdminController@websiteSetting')->name('admin.website_setting');
    Route::post('/get_states', 'Admin\AdminController@getStates');
    Route::get('/settings/taxes/returnsetting', 'Admin\AdminController@returnsetting')->name('admin.returnsetting');
    Route::get('/settings/taxes/taxrates', 'Admin\AdminController@taxrates')->name('admin.taxrates');
    Route::get('/settings/taxes/taxrates/create', 'Admin\AdminController@taxratescreate')->name('admin.taxrates.create');
    Route::post('/settings/taxes/taxrates/store', 'Admin\AdminController@savetaxrate')->name('admin.taxrates.store');
    Route::get('/settings/taxes/taxrates/edit/{id}', 'Admin\AdminController@edittaxrates')->name('admin.edittaxrates');
    Route::post('/settings/taxes/taxrates/edit', 'Admin\AdminController@edittaxrates')->name('admin.edittaxrates');
    Route::post('/settings/taxes/savereturnsetting', 'Admin\AdminController@returnsetting')->name('admin.savereturnsetting');
    Route::get('/getsubcategories', 'Admin\AdminController@getsubcategories');
    Route::get('/getproductbranch', 'Admin\AdminController@getproductbranch');
    Route::get('/getservicemodal', 'Admin\ServicesController@servicemodal');
    Route::get('/getassigneeajax', 'Admin\AdminController@getassigneeajax');
    Route::get('/getpartnerajax', 'Admin\AdminController@getpartnerajax');
    Route::get('/settings/currencies', 'Admin\CurrencyController@index')->name('admin.currency.index');
    Route::get('/settings/currencies/edit/{id}', 'Admin\CurrencyController@edit')->name('admin.currency.edit');
    Route::post('/settings/currencies/edit', 'Admin\CurrencyController@edit')->name('admin.currency.edit');
    Route::get('/settings/currencies/create', 'Admin\CurrencyController@create')->name('admin.currency.create');
    Route::post('/settings/currencies/store', 'Admin\CurrencyController@store')->name('admin.currency.store');
    Route::get('/checkclientexist', 'Admin\AdminController@checkclientexist');
/*CRM route start*/
    Route::post('/uploadfile/store', 'Admin\MediaController@store')->name('admin.media.store');
    Route::get('/uploadfile/index', 'Admin\MediaController@index')->name('admin.media.index');
    Route::get('/uploadfile/delete', 'Admin\MediaController@deleteAction')->name('admin.media.delete');

    Route::get('/users', 'Admin\UserController@index')->name('admin.users.index');
    Route::get('/users/create', 'Admin\UserController@create')->name('admin.users.create');
    Route::post('/users/store', 'Admin\UserController@store')->name('admin.users.store');
    Route::get('/users/edit/{id}', 'Admin\UserController@edit')->name('admin.users.edit');
    Route::get('/users/view/{id}', 'Admin\UserController@view')->name('admin.users.view');
    Route::post('/users/edit', 'Admin\UserController@edit')->name('admin.users.edit');
    Route::post('/users/savezone', 'Admin\UserController@savezone');

    Route::get('/users/active', 'Admin\UserController@active')->name('admin.users.active');
    Route::get('/users/inactive', 'Admin\UserController@inactive')->name('admin.users.inactive');
    Route::get('/users/invited', 'Admin\UserController@invited')->name('admin.users.invited');

    Route::get('/staff', 'Admin\StaffController@index')->name('admin.staff.index');
    Route::get('/staff/create', 'Admin\StaffController@create')->name('admin.staff.create');
    Route::post('/staff/store', 'Admin\StaffController@store')->name('admin.staff.store');
    Route::get('/staff/edit/{id}', 'Admin\StaffController@edit')->name('admin.staff.edit');
    Route::post('/staff/edit', 'Admin\StaffController@edit')->name('admin.staff.edit');

    Route::get('/customer', 'Admin\CustomerController@index')->name('admin.customer.index');
    Route::get('/customer/create', 'Admin\CustomerController@create')->name('admin.customer.create');
    Route::post('/customer/store', 'Admin\CustomerController@store')->name('admin.customer.store');
    Route::get('/customer/edit/{id}', 'Admin\CustomerController@edit')->name('admin.customer.edit');
    Route::post('/customer/edit', 'Admin\CustomerController@edit')->name('admin.customer.edit');
    Route::post('/customer/upload', 'Admin\CustomerController@uploadcsv')->name('admin.customer.upload');

    Route::get('/users/clientlist', 'Admin\UserController@clientlist')->name('admin.users.clientlist');
    Route::get('/users/createclient', 'Admin\UserController@createclient')->name('admin.users.createclient');
    Route::post('/users/storeclient', 'Admin\UserController@storeclient')->name('admin.users.storeclient');
    Route::get('/users/editclient/{id}', 'Admin\UserController@editclient')->name('admin.users.editclient');
    Route::post('/users/editclient', 'Admin\UserController@editclient')->name('admin.users.editclient');

    Route::post('/followup/store', 'Admin\FollowupController@store')->name('admin.followup.store');
    Route::get('/followup/list', 'Admin\FollowupController@index')->name('admin.followup.index');
    Route::post('/followup/compose', 'Admin\FollowupController@compose')->name('admin.followup.compose');

    Route::get('/usertype', 'Admin\UsertypeController@index')->name('admin.usertype.index');
    Route::get('/usertype/create', 'Admin\UsertypeController@create')->name('admin.usertype.create');
    Route::post('/usertype/store', 'Admin\UsertypeController@store')->name('admin.usertype.store');
    Route::get('/usertype/edit/{id}', 'Admin\UsertypeController@edit')->name('admin.usertype.edit');
    Route::post('/usertype/edit', 'Admin\UsertypeController@edit')->name('admin.usertype.edit');

    Route::get('/userrole', 'Admin\UserroleController@index')->name('admin.userrole.index');
    Route::get('/userrole/create', 'Admin\UserroleController@create')->name('admin.userrole.create');
    Route::post('/userrole/store', 'Admin\UserroleController@store')->name('admin.userrole.store');
    Route::get('/userrole/edit/{id}', 'Admin\UserroleController@edit')->name('admin.userrole.edit');
    Route::post('/userrole/edit', 'Admin\UserroleController@edit')->name('admin.userrole.edit');

//Services Start
    Route::get('/services', 'Admin\ServicesController@index')->name('admin.services.index');
    Route::get('/services/create', 'Admin\ServicesController@create')->name('admin.services.create');
    Route::post('/services/store', 'Admin\ServicesController@store')->name('admin.services.store');
    Route::get('/services/edit/{id}', 'Admin\ServicesController@edit')->name('admin.services.edit');
    Route::post('/services/edit', 'Admin\ServicesController@edit')->name('admin.services.edit');

    //Manage Contacts Start
    Route::get('/contact', 'Admin\ContactController@index')->name('admin.managecontact.index');
    Route::get('/contact/create', 'Admin\ContactController@create')->name('admin.managecontact.create');
    Route::post('/managecontact/store', 'Admin\ContactController@store')->name('admin.managecontact.store');
    Route::post('/contact/add', 'Admin\ContactController@add')->name('admin.managecontact.add');
    Route::get('/contact/edit/{id}', 'Admin\ContactController@edit')->name('admin.managecontact.edit');
    Route::post('/contact/edit', 'Admin\ContactController@edit')->name('admin.managecontact.edit');
    Route::post('/contact/storeaddress', 'Admin\ContactController@storeaddress')->name('admin.managecontact.edit');

    //Leads Start
    Route::get('/leads', 'Admin\LeadController@index')->name('admin.leads.index');
    Route::get('/leads/history/{id}', 'Admin\LeadController@history')->name('admin.leads.history');
    Route::get('/leads/create', 'Admin\LeadController@create')->name('admin.leads.create');
    Route::post('/leads/assign', 'Admin\LeadController@assign')->name('admin.leads.assign');
    Route::get('/leads/edit/{id}', 'Admin\LeadController@edit')->name('admin.leads.edit');
    Route::post('/leads/edit', 'Admin\LeadController@edit')->name('admin.leads.edit');
    Route::get('/leads/notes/delete/{id}', 'Admin\LeadController@leaddeleteNotes');
    Route::get('/get-notedetail', 'Admin\LeadController@getnotedetail');
    Route::post('/followup/update', 'Admin\FollowupController@followupupdate');

    Route::post('/leads/store', 'Admin\LeadController@store')->name('admin.leads.store');
    Route::get('/leads/convert', 'Admin\LeadController@convertoClient');
    Route::get('/leads/pin/{id}', 'Admin\LeadController@leadPin');
    //Invoices Start

    Route::get('/invoice/lists/{id}', 'Admin\InvoiceController@lists')->name('admin.invoice.lists');
    Route::get('/invoice/edit/{id}', 'Admin\InvoiceController@edit')->name('admin.invoice.edit');
    Route::post('/invoice/edit', 'Admin\InvoiceController@edit')->name('admin.invoice.edit');
    Route::get('/invoice/create', 'Admin\InvoiceController@create')->name('admin.invoice.create');
    Route::post('/invoice/store', 'Admin\InvoiceController@store')->name('admin.invoice.store');
    Route::get('/invoice/detail', 'Admin\InvoiceController@detail')->name('admin.invoice.detail');
    Route::get('/invoice/email/{id}', 'Admin\InvoiceController@email')->name('admin.invoice.email');
    Route::post('/invoice/email', 'Admin\InvoiceController@email')->name('admin.invoice.email');
    Route::get('/invoice/editpayment', 'Admin\InvoiceController@editpayment')->name('admin.invoice.editpayment');
    Route::get('/invoice/invoicebyid', 'Admin\InvoiceController@invoicebyid')->name('admin.invoice.invoicebyid');
    Route::get('/invoice/history', 'Admin\InvoiceController@history')->name('admin.invoice.history');
    Route::post('/invoice/paymentsave', 'Admin\InvoiceController@paymentsave')->name('admin.invoice.paymentsave');
    Route::post('/invoice/editpaymentsave', 'Admin\InvoiceController@editpaymentsave')->name('admin.invoice.editpaymentsave');
    Route::post('/invoice/addcomment', 'Admin\InvoiceController@addcomment')->name('admin.invoice.addcomment');
    Route::post('/invoice/sharelink', 'Admin\InvoiceController@sharelink')->name('admin.invoice.sharelink');
    Route::post('/invoice/disablelink', 'Admin\InvoiceController@disablelink')->name('admin.invoice.disablelink');
    Route::get('/invoice/download/{id}', 'Admin\InvoiceController@customer_invoice_download')->name('admin.invoice.customer_invoice_download');
    Route::get('/invoice/exportall', 'Admin\InvoiceController@exportall')->name('admin.invoice.exportall');
    Route::get('/invoice/printall', 'Admin\InvoiceController@customer_invoice_printall')->name('admin.invoice.customer_invoice_printall');
    Route::get('/invoice/print/{id}', 'Admin\InvoiceController@customer_invoice_print')->name('admin.invoice.customer_invoice_print');
    Route::get('/invoice/reminder/{id}', 'Admin\InvoiceController@reminder')->name('admin.invoice.reminder');
    Route::post('/invoice/reminder', 'Admin\InvoiceController@reminder')->name('admin.invoice.reminder');
    Route::post('/invoice/attachfile', 'Admin\InvoiceController@attachfile')->name('admin.invoice.attachfile');
    Route::get('/invoice/getattachfile', 'Admin\InvoiceController@getattachfile')->name('admin.invoice.getattachfile');
    Route::get('/invoice/removeattachfile', 'Admin\InvoiceController@removeattachfile')->name('admin.invoice.removeattachfile');
    Route::get('/invoice/attachfileemail', 'Admin\InvoiceController@attachfileemail')->name('admin.invoice.attachfileemail');
    //Manage Api key
    // Route::get('/api-key', 'Admin\ApiController@index')->name('admin.apikey.index');
    //Manage Api key


	//Email Templates Pages
    Route::get('/email_templates', 'Admin\EmailTemplateController@index')->name('admin.email.index');
    Route::get('/email_templates/create', 'Admin\EmailTemplateController@create')->name('admin.email.create');
    Route::post('/email_templates/store', 'Admin\EmailTemplateController@store')->name('admin.email.store');
    Route::get('/edit_email_template/{id}', 'Admin\EmailTemplateController@editEmailTemplate')->name('admin.edit_email_template');
    Route::post('/edit_email_template', 'Admin\EmailTemplateController@editEmailTemplate')->name('admin.edit_email_template');

	//SEO Tool
    Route::get('/edit_seo/{id}', 'Admin\AdminController@editSeo')->name('admin.edit_seo');
    Route::post('/edit_seo', 'Admin\AdminController@editSeo')->name('admin.edit_seo');

    Route::get('/api-key', 'Admin\AdminController@editapi')->name('admin.edit_api');
    Route::post('/api-key', 'Admin\AdminController@editapi')->name('admin.edit_api');


    Route::get('/photo-gallery/getlist', 'Admin\MediaController@getlist')->name('admin.photo.getlist');
    Route::post('/photo-gallery/uploadlist', 'Admin\MediaController@uploadlist')->name('admin.photo.uploadlist');
    Route::post('/photo-gallery/update_action', 'Admin\MediaController@update_action')->name('admin.photo.update_action');

		//clients Start
		Route::get('/clients', 'Admin\ClientsController@index')->name('admin.clients.index');
        Route::get('/clientsmatterslist', 'Admin\ClientsController@clientsmatterslist')->name('admin.clients.clientsmatterslist');
        Route::get('/clientsemaillist', 'Admin\ClientsController@clientsemaillist')->name('admin.clients.clientsemaillist');

		//Route::get('/clients/create', 'Admin\ClientsController@create')->name('admin.clients.create');
		Route::post('/clients/store', 'Admin\ClientsController@store')->name('admin.clients.store');
		Route::get('/clients/edit/{id}', 'Admin\ClientsController@edit')->name('admin.clients.edit');
		Route::post('/clients/edit', 'Admin\ClientsController@edit')->name('admin.clients.edit');

        Route::post('/clients/followup/store', 'Admin\ClientsController@followupstore');


		Route::post('/clients/followup/retagfollowup', 'Admin\ClientsController@retagfollowup');
		Route::get('/clients/changetype/{id}/{type}', 'Admin\ClientsController@changetype');
		Route::get('/document/download/pdf/{id}', 'Admin\ClientsController@downloadpdf');
		Route::get('/clients/removetag', 'Admin\ClientsController@removetag');

        //Route::get('/clients/detail/{id}', 'Admin\ClientsController@detail')->name('admin.clients.detail');
		Route::get('/clients/detail/{client_id}/{client_unique_matter_ref_no?}', 'Admin\ClientsController@detail')->name('admin.clients.detail');
		Route::get('/clients/summary/{client_id}', 'Admin\ClientsController@summary')->name('admin.clients.summary');
		
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

		//prospects Start
		Route::get('/prospects', 'Admin\ClientsController@prospects')->name('admin.clients.prospects');
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
       // Route::get('/get-note-matters', 'Admin\ClientsController@getnotematters')->name('admin.clients.getnotematters');

		Route::get('/convertapplication', 'Admin\ClientsController@convertapplication')->name('admin.clients.convertapplication');
		Route::get('/deleteservices', 'Admin\ClientsController@deleteservices')->name('admin.clients.deleteservices');

        //Route::post('/upload-document', 'Admin\ClientsController@uploaddocument')->name('admin.clients.uploaddocument');
        //Route::post('/upload-migration-document', 'Admin\ClientsController@uploadmigrationdocument')->name('admin.clients.uploadmigrationdocument');



        Route::get('/deletedocs', 'Admin\ClientsController@deletedocs')->name('admin.clients.deletedocs');
		Route::post('/renamedoc', 'Admin\ClientsController@renamedoc')->name('admin.clients.renamedoc');


		Route::post('/savetoapplication', 'Admin\ClientsController@savetoapplication');


		//products Start
		Route::get('/products', 'Admin\ProductsController@index')->name('admin.products.index');
		Route::get('/products/create', 'Admin\ProductsController@create')->name('admin.products.create');
		Route::post('/products/store', 'Admin\ProductsController@store')->name('admin.products.store');
		Route::get('/products/edit/{id}', 'Admin\ProductsController@edit')->name('admin.products.edit');
		Route::post('/products/edit', 'Admin\ProductsController@edit')->name('admin.products.edit');
		Route::post('/products-import', 'Admin\ProductsController@import')->name('admin.products.import');


		Route::get('/products/detail/{id}', 'Admin\ProductsController@detail')->name('admin.products.detail');
		 Route::get('/products/get-recipients', 'Admin\ProductsController@getrecipients')->name('admin.products.getrecipients');
		Route::get('/products/get-allclients', 'Admin\ProductsController@getallclients')->name('admin.products.getallclients');

		//Partner Start
		Route::get('/partners', 'Admin\PartnersController@index')->name('admin.partners.index');
		Route::get('/partners/create', 'Admin\PartnersController@create')->name('admin.partners.create');
		Route::post('/partners/store', 'Admin\PartnersController@store')->name('admin.partners.store');
		Route::get('/partners/edit/{id}', 'Admin\PartnersController@edit')->name('admin.partners.edit');
		Route::post('/partners/edit', 'Admin\PartnersController@edit')->name('admin.partners.edit');
		Route::get('/getpaymenttype', 'Admin\PartnersController@getpaymenttype')->name('admin.partners.getpaymenttype');

		Route::get('/partners/detail/{id}', 'Admin\PartnersController@detail')->name('admin.partners.detail');
		 Route::get('/partners/get-recipients', 'Admin\PartnersController@getrecipients')->name('admin.partners.getrecipients');
		Route::get('/partners/get-allclients', 'Admin\PartnersController@getallclients')->name('admin.partners.getallclients');

		//Branch Start
		Route::get('/branch', 'Admin\BranchesController@index')->name('admin.branch.index');
		Route::get('/branch/create', 'Admin\BranchesController@create')->name('admin.branch.create');
		Route::post('/branch/store', 'Admin\BranchesController@store')->name('admin.branch.store');
		Route::get('/branch/edit/{id}', 'Admin\BranchesController@edit')->name('admin.branch.edit');
		Route::get('/branch/view/{id}', 'Admin\BranchesController@view')->name('admin.branch.userview');
		Route::get('/branch/view/client/{id}', 'Admin\BranchesController@viewclient')->name('admin.branch.clientview');
		Route::post('/branch/edit', 'Admin\BranchesController@edit')->name('admin.branch.edit');





		//General Invoice Start
		Route::get('/invoice/general-invoice', 'Admin\InvoiceController@general_invoice')->name('admin.invoice.general-invoice');

		Route::get('/applications/detail/{id}', 'Admin\ApplicationsController@detail')->name('admin.applications.detail');
		Route::post('/interested-service', 'Admin\ClientsController@interestedService');
		Route::post('/edit-interested-service', 'Admin\ClientsController@editinterestedService');
		Route::get('/get-services', 'Admin\ClientsController@getServices');
		Route::get('/showproductfeeserv', 'Admin\ClientsController@showproductfeeserv');Route::post('/servicesavefee', 'Admin\ClientsController@servicesavefee');
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
		Route::post('/application/saleforcastservice', 'Admin\ClientsController@saleforcastservice');
		Route::get('/getintrestedserviceedit', 'Admin\ClientsController@getintrestedserviceedit');
		Route::get('/getAppointmentdetail', 'Admin\ClientsController@getAppointmentdetail');
		Route::post('/create-invoice', 'Admin\InvoiceController@createInvoice');
		Route::get('/application/invoice/{client_id}/{application}/{invoice_type}', 'Admin\InvoiceController@getInvoice');
		Route::get('/invoice/view/{id}', 'Admin\InvoiceController@show');
		Route::get('/invoice/preview/{id}', 'Admin\InvoiceController@getinvoicespdf');
		Route::get('/invoice/edit/{id}', 'Admin\InvoiceController@edit');
		Route::post('/invoice/general-store', 'Admin\InvoiceController@generalStore');
		Route::get('/invoice/delete-payment', 'Admin\InvoiceController@deletepayment');
		Route::post('/invoice/payment-store', 'Admin\InvoiceController@invoicepaymentstore');
		Route::get('/get-invoices', 'Admin\InvoiceController@getinvoices');
		Route::get('/get-invoices-pdf', 'Admin\InvoiceController@getinvoicespdf');
		Route::get('/delete-invoice', 'Admin\InvoiceController@deleteinvoice');
		Route::post('/invoice/general-edit', 'Admin\InvoiceController@updategeninvoices');
		Route::post('/invoice/com-store', 'Admin\InvoiceController@updatecominvoices');
		Route::get('/invoice/paid', 'Admin\InvoiceController@paid')->name('admin.invoice.paid');
		Route::get('/invoice/unpaid', 'Admin\InvoiceController@unpaid')->name('admin.invoice.unpaid');
		//Route::get('/invoice/unpaid', 'Admin\InvoiceController@unpaid')->name('admin.invoice.unpaid');
		Route::get('/invoice/', 'Admin\InvoiceController@index')->name('admin.invoice.index');
		Route::get('/payment/view/{id}', 'Admin\AccountController@preview');


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
		Route::post('/application/saleforcast', 'Admin\ApplicationsController@saleforcast');
		Route::get('/superagent', 'Admin\ApplicationsController@superagent');
		Route::get('/subagent', 'Admin\ApplicationsController@subagent');
		Route::get('/showproductfee', 'Admin\ApplicationsController@showproductfee');
		Route::post('/applicationsavefee', 'Admin\ApplicationsController@applicationsavefee');
		Route::get('/application/export/pdf/{id}', 'Admin\ApplicationsController@exportapplicationpdf');
		Route::post('/add-checklists', 'Admin\ApplicationsController@addchecklists');
		Route::post('/application/checklistupload', 'Admin\ApplicationsController@checklistupload');
		Route::get('/deleteapplicationdocs', 'Admin\ApplicationsController@deleteapplicationdocs');
		Route::get('/application/publishdoc', 'Admin\ApplicationsController@publishdoc');


		//Account Start
		Route::get('/payment', 'Admin\AccountController@payment')->name('admin.account.payment');
		Route::get('/income-sharing/payables/unpaid', 'Admin\AccountController@payableunpaid')->name('admin.account.payableunpaid');
		Route::get('/income-sharing/payables/paid', 'Admin\AccountController@payablepaid')->name('admin.account.payablepaid');
		Route::post('/income-payment-store', 'Admin\AccountController@incomepaymentstore');
		Route::get('/revert-payment', 'Admin\AccountController@revertpayment');
		Route::get('/income-sharing/receivables/unpaid', 'Admin\AccountController@receivableunpaid')->name('admin.account.receivableunpaid');
		Route::get('/income-sharing/receivables/paid', 'Admin\AccountController@receivablepaid')->name('admin.account.receivablepaid');
		Route::get('/group-invoice/unpaid', 'Admin\InvoiceController@unpaidgroupinvoice')->name('admin.invoice.unpaidgroupinvoice');
		Route::get('/group-invoice/paid', 'Admin\InvoiceController@paidgroupinvoice')->name('admin.invoice.paidgroupinvoice');
		Route::post('/group-invoice/create', 'Admin\InvoiceController@creategroupinvoice')->name('admin.invoice.creategroupinvoice');
        //Route::post('/group-invoice/save', 'Admin\InvoiceController@savegroupinvoice')->name('admin.invoice.savegroupinvoice');

		Route::get('/invoice-schedules', 'Admin\InvoiceController@invoiceschedules')->name('admin.invoice.invoiceschedules');
		Route::post('/paymentschedule', 'Admin\InvoiceController@paymentschedule')->name('admin.invoice.paymentschedule');
		Route::post('/setup-paymentschedule', 'Admin\InvoiceController@setuppaymentschedule');
		Route::post('/editpaymentschedule', 'Admin\InvoiceController@editpaymentschedule')->name('admin.invoice.editpaymentschedule');
		Route::get('/scheduleinvoicedetail', 'Admin\InvoiceController@scheduleinvoicedetail');
		Route::get('/addscheduleinvoicedetail', 'Admin\InvoiceController@addscheduleinvoicedetail');
		Route::get('/get-all-paymentschedules', 'Admin\InvoiceController@getallpaymentschedules');
		Route::get('/deletepaymentschedule', 'Admin\InvoiceController@deletepaymentschedule');
		Route::get('/applications/preview-schedules/{id}', 'Admin\InvoiceController@apppreviewschedules');


		//Product Type Start
		Route::get('/product-type', 'Admin\ProductTypeController@index')->name('admin.feature.producttype.index');
		Route::get('/product-type/create', 'Admin\ProductTypeController@create')->name('admin.feature.producttype.create');
		Route::post('/product-type/store', 'Admin\ProductTypeController@store')->name('admin.feature.producttype.store');
		Route::get('/product-type/edit/{id}', 'Admin\ProductTypeController@edit')->name('admin.feature.producttype.edit');
		Route::post('/product-type/edit', 'Admin\ProductTypeController@edit')->name('admin.feature.producttype.edit');

		Route::get('/profiles', 'Admin\ProfileController@index')->name('admin.feature.profiles.index');
		Route::get('/profiles/create', 'Admin\ProfileController@create')->name('admin.feature.profiles.create');
		Route::post('/profiles/store', 'Admin\ProfileController@store')->name('admin.feature.profiles.store');
		Route::get('/profiles/edit/{id}', 'Admin\ProfileController@edit')->name('admin.feature.profiles.edit');
		Route::post('/profiles/edit', 'Admin\ProfileController@edit')->name('admin.feature.profiles.edit');
		//Partner Type Start
		Route::get('/partner-type', 'Admin\PartnerTypeController@index')->name('admin.feature.partnertype.index');
		Route::get('/partner-type/create', 'Admin\PartnerTypeController@create')->name('admin.feature.partnertype.create');
		Route::post('/partner-type/store', 'Admin\PartnerTypeController@store')->name('admin.feature.partnertype.store');
		Route::get('/partner-type/edit/{id}', 'Admin\PartnerTypeController@edit')->name('admin.feature.partnertype.edit');
	Route::post('/partner-type/edit', 'Admin\PartnerTypeController@edit')->name('admin.feature.partnertype.edit');

	//Lead Service Start
		Route::get('/lead-service', 'Admin\LeadServiceController@index')->name('admin.feature.leadservice.index');
		Route::get('/lead-service/create', 'Admin\LeadServiceController@create')->name('admin.feature.leadservice.create');
		Route::post('/lead-service/store', 'Admin\LeadServiceController@store')->name('admin.feature.leadservice.store');
		Route::get('/lead-service/edit/{id}', 'Admin\LeadServiceController@edit')->name('admin.feature.leadservice.edit');
		Route::post('/lead-service/edit', 'Admin\LeadServiceController@edit')->name('admin.feature.leadservice.edit');



		//Subject Start
		Route::get('/subject', 'Admin\SubjectController@index')->name('admin.feature.subject.index');
		Route::get('/subject/create', 'Admin\SubjectController@create')->name('admin.feature.subject.create');
		Route::post('/subject/store', 'Admin\SubjectController@store')->name('admin.feature.subject.store');
		Route::get('/subject/edit/{id}', 'Admin\SubjectController@edit')->name('admin.feature.subject.edit');
		Route::post('/subject/edit', 'Admin\SubjectController@edit')->name('admin.feature.subject.edit');

		//Source Start
		Route::get('/source', 'Admin\SourceController@index')->name('admin.feature.source.index');
		Route::get('/source/create', 'Admin\SourceController@create')->name('admin.feature.source.create');
		Route::post('source/store', 'Admin\SourceController@store')->name('admin.feature.source.store');
		Route::get('/source/edit/{id}', 'Admin\SourceController@edit')->name('admin.feature.source.edit');
		Route::post('/source/edit', 'Admin\SourceController@edit')->name('admin.feature.source.edit');

		//Tags Start
		Route::get('/tags', 'Admin\TagController@index')->name('admin.feature.tags.index');
		Route::get('/tags/create', 'Admin\TagController@create')->name('admin.feature.tags.create');
		Route::post('tags/store', 'Admin\TagController@store')->name('admin.feature.tags.store');
		Route::get('/tags/edit/{id}', 'Admin\TagController@edit')->name('admin.feature.tags.edit');
		Route::post('/tags/edit', 'Admin\TagController@edit')->name('admin.feature.tags.edit');

		//Checklist Start
		Route::get('/checklist', 'Admin\ChecklistController@index')->name('admin.checklist.index');
		Route::get('/checklist/create', 'Admin\ChecklistController@create')->name('admin.checklist.create');
		Route::post('checklist/store', 'Admin\ChecklistController@store')->name('admin.checklist.store');
		Route::get('/checklist/edit/{id}', 'Admin\ChecklistController@edit')->name('admin.checklist.edit');
		Route::post('/checklist/edit', 'Admin\ChecklistController@edit')->name('admin.checklist.edit');

		//Enquiry Source Start
		Route::get('/enquirysource', 'Admin\EnquirySourceController@index')->name('admin.enquirysource.index');
		Route::get('/enquirysource/create', 'Admin\EnquirySourceController@create')->name('admin.enquirysource.create');
		Route::post('enquirysource/store', 'Admin\EnquirySourceController@store')->name('admin.enquirysource.store');
		Route::get('/enquirysource/edit/{id}', 'Admin\EnquirySourceController@edit')->name('admin.enquirysource.edit');
		Route::post('/enquirysource/edit', 'Admin\EnquirySourceController@edit')->name('admin.enquirysource.edit');

		//FeeType Start
		Route::get('/feetype', 'Admin\FeeTypeController@index')->name('admin.feetype.index');
		Route::get('/feetype/create', 'Admin\FeeTypeController@create')->name('admin.feetype.create');
		Route::post('feetype/store', 'Admin\FeeTypeController@store')->name('admin.feetype.store');
		Route::get('/feetype/edit/{id}', 'Admin\FeeTypeController@edit')->name('admin.enquirysource.edit');
		Route::post('/feetype/edit', 'Admin\FeeTypeController@edit')->name('admin.feetype.edit');


		//workflow Start
		Route::get('/workflow', 'Admin\WorkflowController@index')->name('admin.workflow.index');
		Route::get('/workflow/create', 'Admin\WorkflowController@create')->name('admin.workflow.create');
		Route::post('workflow/store', 'Admin\WorkflowController@store')->name('admin.workflow.store');
		Route::get('/workflow/edit/{id}', 'Admin\WorkflowController@edit')->name('admin.workflow.edit');
		Route::get('/workflow/deactivate-workflow/{id}', 'Admin\WorkflowController@deactivateWorkflow')->name('admin.workflow.deactivate');
		Route::get('/workflow/activate-workflow/{id}', 'Admin\WorkflowController@activateWorkflow')->name('admin.workflow.activate');
		Route::post('/workflow/edit', 'Admin\WorkflowController@edit')->name('admin.workflow.edit');

		Route::post('/partner/saveagreement', 'Admin\PartnersController@saveagreement');
		Route::post('/partner/create-contact', 'Admin\PartnersController@createcontact');
		Route::get('/get-contacts', 'Admin\PartnersController@getcontacts');
		Route::get('/deletecontact', 'Admin\PartnersController@deletecontact');
		Route::get('/getcontactdetail', 'Admin\PartnersController@getcontactdetail');
		Route::post('/partners-import', 'Admin\PartnersController@import')->name('admin.partners.import');

		Route::post('/partner/create-branch', 'Admin\PartnersController@createbranch');
		Route::get('/get-branches', 'Admin\PartnersController@getbranch');
		Route::get('/getbranchdetail', 'Admin\PartnersController@getbranchdetail');
		Route::get('/deletebranch', 'Admin\PartnersController@deletebranch');

		Route::post('/saveacademic', 'Admin\ProductsController@saveacademic');
		Route::post('/saveotherinfo', 'Admin\ProductsController@saveotherinfo');
		Route::get('/product/getotherinfo', 'Admin\ProductsController@getotherinfo');
		Route::get('/get-all-fees', 'Admin\ProductsController@getallfees');
		Route::post('/savefee', 'Admin\ProductsController@savefee');

		Route::get('/getfeeoptionedit', 'Admin\ProductsController@editfee');
		Route::post('/editfee', 'Admin\ProductsController@editfeeform');
		Route::get('/deletefee', 'Admin\ProductsController@deletefee');

		Route::post('/add-partner-appointment', 'Admin\PartnersController@addappointment');
		Route::get('/partner/get-appointments', 'Admin\PartnersController@getappointment');
		Route::get('/partner/getAppointmentdetail', 'Admin\PartnersController@getAppointmentdetail');

		Route::post('/partner/addtask', 'Admin\PartnersController@addTask');
		Route::get('/partner/get-tasks', 'Admin\PartnersController@gettasks');
		Route::get('/partner/get-task-detail', 'Admin\PartnersController@taskdetail');
		Route::post('/partner/savecomment', 'Admin\PartnersController@savecomment');
		Route::get('/change-task-status', 'Admin\PartnersController@changetaskstatus');
		Route::get('/change-task-priority', 'Admin\PartnersController@changetaskpriority');

		Route::post('/promotion/store', 'Admin\PromotionController@store');
		Route::post('/promotion/edit', 'Admin\PromotionController@edit');
		Route::get('/get-promotions', 'Admin\PromotionController@getpromotions');
		Route::get('/getpromotioneditform', 'Admin\PromotionController@getpromotioneditform');
		Route::get('/change-promotion-status', 'Admin\PromotionController@changepromotionstatus');


		//Applications Start
		Route::get('/applications', 'Admin\ApplicationsController@index')->name('admin.applications.index');
		Route::get('/applications/create', 'Admin\ApplicationsController@create')->name('admin.applications.create');
		Route::post('/discontinue_application', 'Admin\ApplicationsController@discontinue_application');
		Route::post('/revert_application', 'Admin\ApplicationsController@revert_application');
		Route::post('/applications-import', 'Admin\ApplicationsController@import')->name('admin.applications.import');
		//Route::post('/product-type/store', 'Admin\ProductTypeController@store')->name('admin.feature.producttype.store');
		//Route::get('/product-type/edit/{id}', 'Admin\ProductTypeController@edit')->name('admin.feature.producttype.edit');
		//Route::post('/product-type/edit', 'Admin\ProductTypeController@edit')->name('admin.feature.producttype.edit');
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

		Route::get('/enquiries', 'Admin\EnquireController@index')->name('admin.enquiries.index');
		Route::get('/enquiries/archived-enquiries', 'Admin\EnquireController@Archived')->name('admin.enquiries.archived');
		Route::get('/enquiries/covertenquiry/{id}', 'Admin\EnquireController@covertenquiry');
		Route::get('/enquiries/archived/{id}', 'Admin\EnquireController@archivedenquiry');

		//Audit Logs Start
		Route::get('/audit-logs', 'Admin\AuditLogController@index')->name('admin.auditlogs.index');



		Route::post('/save_tag', 'Admin\ClientsController@save_tag');


		Route::get('/emails', 'Admin\EmailController@index')->name('admin.emails.index');
		Route::get('/emails/create', 'Admin\EmailController@create')->name('admin.emails.create');
		Route::post('emails/store', 'Admin\EmailController@store')->name('admin.emails.store');
		Route::get('/emails/edit/{id}', 'Admin\EmailController@edit')->name('admin.emails.edit');
		Route::post('/emails/edit', 'Admin\EmailController@edit')->name('admin.emails.edit');
		



		//Crm Email Template Start
		Route::get('/crm_email_template', 'Admin\CrmEmailTemplateController@index')->name('admin.crmemailtemplate.index');
		Route::get('/crm_email_template/create', 'Admin\CrmEmailTemplateController@create')->name('admin.crmemailtemplate.create');
		Route::post('crm_email_template/store', 'Admin\CrmEmailTemplateController@store')->name('admin.crmemailtemplate.store');
		Route::get('/crm_email_template/edit/{id}', 'Admin\CrmEmailTemplateController@edit')->name('admin.crmemailtemplate.edit');
		Route::post('/crm_email_template/edit', 'Admin\CrmEmailTemplateController@edit')->name('admin.crmemailtemplate.edit');

		Route::get('/gen-settings', 'Admin\AdminController@gensettings')->name('admin.gensettings.index');
		Route::post('/gen-settings/update', 'Admin\AdminController@gensettingsupdate')->name('admin.gensettings.update');

		Route::get('/fetch-notification', 'Admin\AdminController@fetchnotification');
		Route::get('/fetch-messages', 'Admin\AdminController@fetchmessages');

		//In-Person Notification related routes start
		Route::get('/fetch-office-visit-notifications', 'Admin\AdminController@fetchOfficeVisitNotifications');
		Route::post('/mark-notification-seen', 'Admin\AdminController@markNotificationSeen');
		Route::get('/check-checkin-status', 'Admin\AdminController@checkCheckinStatus');
		Route::post('/update-checkin-status', 'Admin\AdminController@updateCheckinStatus');
		//In-Person Notification related routes end

	    //Route::get('/upload-checklists', 'Admin\UploadChecklistController@index')->name('admin.upload_checklists.index');
		//Route::post('/upload-checklists/store', 'Admin\UploadChecklistController@store')->name('admin.upload_checklistsupload');
		Route::get('/teams', 'Admin\TeamController@index')->name('admin.teams.index');
		Route::get('/teams/edit/{id}', 'Admin\TeamController@edit')->name('admin.teams.edit');
		Route::post('/teams/edit', 'Admin\TeamController@edit');
		Route::post('/teams/store', 'Admin\TeamController@store')->name('admin.teamsupload');
		Route::get('/all-notifications', 'Admin\AdminController@allnotification');

		// Appointment modulle
		//Route::resource('appointments', Admin\AppointmentsController::class);
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

        //Route::get('/activities', 'Admin\AssigneeController@activities')->name('assignee.activities'); //activities
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

        //update email verified at client detail page
        Route::post('/clients/update-email-verified', 'Admin\ClientsController@updateemailverified');


        //Appointment Dates Not Available
		Route::get('/appointment-dates-disable', 'Admin\AppointmentDisableDateController@index')->name('admin.feature.appointmentdisabledate.index');
		Route::get('/appointment-dates-disable/create', 'Admin\AppointmentDisableDateController@create')->name('admin.feature.appointmentdisabledate.create');
		Route::post('/appointment-dates-disable/store', 'Admin\AppointmentDisableDateController@store')->name('admin.feature.appointmentdisabledate.store');
		Route::get('/appointment-dates-disable/edit/{id}', 'Admin\AppointmentDisableDateController@edit')->name('admin.feature.appointmentdisabledate.edit');
		Route::post('/appointment-dates-disable/edit', 'Admin\AppointmentDisableDateController@edit')->name('admin.feature.appointmentdisabledate.edit');

        //Promo code
		Route::get('/promo-code', 'Admin\PromoCodeController@index')->name('admin.feature.promocode.index');
		Route::get('/promo-code/create', 'Admin\PromoCodeController@create')->name('admin.feature.promocode.create');
		Route::post('/promo-code/store', 'Admin\PromoCodeController@store')->name('admin.feature.promocode.store');
		Route::get('/promo-code/edit/{id}', 'Admin\PromoCodeController@edit')->name('admin.feature.promocode.edit');
		Route::post('/promo-code/edit', 'Admin\PromoCodeController@edit')->name('admin.feature.promocode.edit');
        Route::post('/promo-code/checkpromocode', 'Admin\PromoCodeController@checkpromocode');


        //Personal Document Category
		Route::get('/personal-document-type', 'Admin\PersonalDocumentTypeController@index')->name('admin.feature.personaldocumenttype.index');
		Route::get('/personal-document-type/create', 'Admin\PersonalDocumentTypeController@create')->name('admin.feature.personaldocumenttype.create');
		Route::post('/personal-document-type/store', 'Admin\PersonalDocumentTypeController@store')->name('admin.feature.personaldocumenttype.store');
		Route::get('/personal-document-type/edit/{id}', 'Admin\PersonalDocumentTypeController@edit')->name('admin.feature.personaldocumenttype.edit');
		Route::post('/personal-document-type/edit', 'Admin\PersonalDocumentTypeController@edit')->name('admin.feature.personaldocumenttype.edit');
        Route::post('/personal-document-type/checkcreatefolder', 'Admin\PersonalDocumentTypeController@checkcreatefolder');


        //Visa Document Category
		Route::get('/visa-document-type', 'Admin\VisaDocumentTypeController@index')->name('admin.feature.visadocumenttype.index');
		Route::get('/visa-document-type/create', 'Admin\VisaDocumentTypeController@create')->name('admin.feature.visadocumenttype.create');
		Route::post('/visa-document-type/store', 'Admin\VisaDocumentTypeController@store')->name('admin.feature.visadocumenttype.store');
		Route::get('/visa-document-type/edit/{id}', 'Admin\VisaDocumentTypeController@edit')->name('admin.feature.visadocumenttype.edit');
		Route::post('/visa-document-type/edit', 'Admin\VisaDocumentTypeController@edit')->name('admin.feature.visadocumenttype.edit');
        Route::post('/visa-document-type/checkcreatefolder', 'Admin\VisaDocumentTypeController@checkcreatefolder');



        //Matter Start
		Route::get('/matter', 'Admin\MatterController@index')->name('admin.feature.matter.index');
		Route::get('/matter/create', 'Admin\MatterController@create')->name('admin.feature.matter.create');
		Route::post('/matter/store', 'Admin\MatterController@store')->name('admin.feature.matter.store');
		Route::get('/matter/edit/{id}', 'Admin\MatterController@edit')->name('admin.feature.matter.edit');
		Route::post('/matter/edit', 'Admin\MatterController@edit')->name('admin.feature.matter.edit');

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


        //Route::get('/clients/preview-msg/{filename}', 'Admin\ClientsController@previewMsgFile');
        //Route::get('/clients/detail/{client_id}/{client_unique_matter_ref_no?}', 'Admin\ClientsController@detail')->name('admin.clients.detail');

        Route::post('/clients/update-address', 'Admin\ClientsController@updateAddress')->name('admin.clients.updateAddress');

        //Fetch all contact list of any client at create note popup
        Route::post('/clients/fetchClientContactNo', 'Admin\ClientsController@fetchClientContactNo');

        Route::post('/clients/clientdetailsinfo/{id}', 'Admin\ClientsController@clientdetailsinfo')->name('admin.clients.clientdetailsinfo');
        Route::post('/clients/clientdetailsinfo', 'Admin\ClientsController@clientdetailsinfo')->name('admin.clients.clientdetailsinfo');

        Route::post('/client/saveRating', 'Admin\ClientsController@saveRating')->name('admin.clients.saveRating');
        Route::get('/ratings', [ClientsController::class, 'showRatings'])->name('client.ratings');

        Route::post('/reassiginboxemail', 'Admin\ClientsController@reassiginboxemail')->name('admin.clients.reassiginboxemail');
        Route::post('/reassigsentemail', 'Admin\ClientsController@reassigsentemail')->name('admin.clients.reassigsentemail');

        //Fetch selected client all matters at assign email to user popup
        Route::post('/listAllMattersWRTSelClient', 'Admin\ClientsController@listAllMattersWRTSelClient')->name('admin.clients.listAllMattersWRTSelClient');

        Route::post('/verifydoc', 'Admin\ClientsController@verifydoc')->name('admin.clients.verifydoc');
        Route::post('/getvisachecklist', 'Admin\ClientsController@getvisachecklist')->name('admin.clients.getvisachecklist');




        Route::post('/extenddeadlinedate', 'Admin\AdminController@extenddeadlinedate');

        Route::post('/leads/updateOccupation', 'Admin\ClientsController@updateOccupation')->name('admin.leads.updateOccupation');

        //Document Checklist Start
		Route::get('/documentchecklist', 'Admin\DocumentChecklistController@index')->name('admin.feature.documentchecklist.index');
		Route::get('/documentchecklist/create', 'Admin\DocumentChecklistController@create')->name('admin.feature.documentchecklist.create');
		Route::post('/documentchecklist/store', 'Admin\DocumentChecklistController@store')->name('admin.feature.documentchecklist.store');
		Route::get('/documentchecklist/edit/{id}', 'Admin\DocumentChecklistController@edit')->name('admin.feature.documentchecklist.edit');
		Route::post('/documentchecklist/edit', 'Admin\DocumentChecklistController@edit')->name('admin.feature.documentchecklist.edit');

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

        Route::get('/admin/get-visa-types', 'Admin\ClientsController@getVisaTypes')->name('admin.getVisaTypes');
        Route::get('/admin/get-countries', 'Admin\ClientsController@getCountries')->name('admin.getCountries');
        Route::post('/updateOccupation', 'Admin\ClientsController@updateOccupation')->name('admin.clients.updateOccupation');


        Route::get('/clients/genClientFundLedgerInvoice/{id}', 'Admin\ClientsController@genClientFundLedgerInvoice');
        Route::get('/clients/genofficereceiptInvoice/{id}', 'Admin\ClientsController@genofficereceiptInvoice');


        Route::post('/update-client-funds-ledger', 'Admin\ClientsController@updateClientFundsLedger')->name('admin.clients.update-client-funds-ledger');

        Route::post('/update-task', 'Admin\AssigneeController@updateTask');
        Route::get('/activities/counts','Admin\AssigneeController@getActivityCounts' )->name('activities.counts');


        Route::post('/clients/invoiceamount', 'Admin\ClientsController@getInvoiceAmount')->name('admin.clients.invoiceamount');


        Route::post('/clients/get_client_au_pr_point_details', 'Admin\ClientsController@get_client_au_pr_point_details');
        Route::post('/clients/CalculatePoints', 'Admin\ClientsController@CalculatePoints');
        Route::post('/clients/prpoints_add_to_notes', 'Admin\ClientsController@prpoints_add_to_notes');

        Route::post('/admin/clients/search-partner', 'Admin\ClientsController@searchPartner')->name('admin.clients.searchPartner');

        //Matter AI Tab
        Route::post('/admin/load-matter-ai-data','Admin\ClientsController@loadMatterAiData');
        Route::post('/admin/get-chat-history', 'Admin\ClientsController@getChatHistory');
        Route::post('/admin/get-chat-messages', 'Admin\ClientsController@getChatMessages');
        Route::post('/admin/send-ai-message', 'Admin\ClientsController@sendAiMessage');

        //Client receipt delete by Celesty
        Route::post('/delete_receipt','Admin\ClientsController@delete_receipt');
        //Download Document
        Route::post('/download-document', 'Admin\ClientsController@download_document');
        //Route::get('/download-document', 'Admin\ClientsController@download_document');

        //Form 965
        Route::post('/admin/forms', 'Admin\Form956Controller@store')->name('forms.store');
		Route::get('/admin/forms/{form}', 'Admin\Form956Controller@show')->name('forms.show');
        Route::get('/forms/{form}/preview', 'Admin\Form956Controller@previewPdf')->name('forms.preview');
        Route::get('/forms/{form}/pdf', 'Admin\Form956Controller@generatePdf')->name('forms.pdf');

        //Show visa expiry message
        Route::get('/fetch-visa_expiry_messages', 'Admin\AdminController@fetchvisaexpirymessages');
		//Create agreement
		Route::post('/clients/generateagreement', 'Admin\ClientsController@generateagreement')->name('clients.generateagreement');
        //Route::get('/download-agreement/{client_id}', 'Admin\ClientsController@downloadAgreement')->name('agreement.download');

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
        Route::post('/clients/fetchClientMatterAssignee', 'Admin\ClientsController@fetchClientMatterAssignee');
        //Save client matter assignee
        Route::post('/clients/updateClientMatterAssignee', 'Admin\ClientsController@updateClientMatterAssignee');

        //Add Personal Doucment Category
        Route::post('/add-personaldoccategory', 'Admin\ClientsController@addPersonalDocCategory');

        //Update Personal Doucment Category
        Route::post('/update-personal-doc-category', 'Admin\ClientsController@updatePersonalDocCategory' )->name('update.personal.doc.category');
        //Route::post('/delete-personal-doc-category', 'Admin\ClientsController@deletePersonalDocCategory')->name('delete.personal.doc.category');

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
    
		//Matter First Email
		Route::get('/matter_email_template', 'Admin\MatterEmailTemplateController@index')->name('admin.matteremailtemplate.index');
		Route::get('/matter_email_template/create/{matterId}', 'Admin\MatterEmailTemplateController@create')->name('admin.matteremailtemplate.create');
		Route::post('matter_email_template/store', 'Admin\MatterEmailTemplateController@store')->name('admin.matteremailtemplate.store');
		Route::get('/matter_email_template/edit/{templateId}/{matterId}', 'Admin\MatterEmailTemplateController@edit')->name('admin.matteremailtemplate.edit');
		Route::post('/matter_email_template/edit', 'Admin\MatterEmailTemplateController@edit')->name('admin.matteremailtemplate.edit');
  
		//Matter Other Email Template Start
		Route::get('/matter_other_email_template/edit/{templateId}/{matterId}', 'Admin\MatterOtherEmailTemplateController@edit')->name('admin.matterotheremailtemplate.edit');
		Route::post('/matter_other_email_template/edit', 'Admin\MatterOtherEmailTemplateController@edit')->name('admin.matterotheremailtemplate.update');
		Route::get('/matter_other_email_template/create/{matterId}', 'Admin\MatterOtherEmailTemplateController@create')->name('admin.matterotheremailtemplate.create');
		Route::post('/matter_other_email_template/store', 'Admin\MatterOtherEmailTemplateController@store')->name('admin.matterotheremailtemplate.store');
		Route::delete('/matter_other_email_template/{id}', 'Admin\MatterOtherEmailTemplateController@destroy')->name('admin.matterotheremailtemplate.destroy');
		Route::get('/matter_other_email_template/{matterId}', 'Admin\MatterOtherEmailTemplateController@index')->name('admin.matterotheremailtemplate.index');
  
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


		//document signature
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

Route::get('/{slug}', 'HomeController@Page')->name('page.slug');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

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
