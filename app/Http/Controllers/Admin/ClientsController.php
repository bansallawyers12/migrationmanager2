<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Models\ServiceFeeOption;
use App\Models\ServiceFeeOptionType;
use App\Models\OnlineForm;
use Auth;
use Config;
use PDF;
use App\Models\CheckinLog;
use App\Models\Note;
use App\Models\clientServiceTaken;
use App\Models\AccountClientReceipt;

use App\Models\Matter;
use App\Models\ClientMatter;

use App\Models\FileStatus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

use Hfig\MAPI;
use Hfig\MAPI\OLE\Pear;
use Hfig\MAPI\Message\Msg;
use Hfig\MAPI\MapiMessageFactory;

use DateTime;
use DateTimeZone;

use App\Models\ClientAddress; // Import the ClientAddress model
use App\Models\ClientContact; // Import the ClientAddress model
use App\Models\ClientEmail; // Import the ClientAddress model
use App\Models\ClientQualification; // Import the ClientAddress model
use App\Models\ClientExperience; // Import the ClientAddress model
use App\Models\ClientTestScore; // Import the ClientAddress model
use App\Models\ClientVisaCountry; // Import the ClientAddress model
use App\Models\ClientOccupation; // Import the ClientAddress model
use App\Models\ClientSpouseDetail; // Import the ClientAddress model

use App\Models\EmailRecord;
use App\Models\ClientPoint;
use App\Models\VisaDocChecklist;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;

use App\Models\ClientPassportInformation;
use App\Models\ClientTravelInformation;
use App\Models\ClientCharacter;
use App\Models\ClientRelationship;

use App\Models\AiChat;
use App\Models\AiChatMessage;
use Illuminate\Support\Facades\Http;

use App\Models\Form956;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Log;
use App\Models\CostAssignmentForm;
use App\Models\PersonalDocumentType;
use App\Models\VisaDocumentType;
use App\Models\ClientEoiReference;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use App\Mail\HubdocInvoiceMail;
use App\Services\SmsService;

class ClientsController extends Controller
{
    protected $openAiClient;
    protected $smsService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SmsService $smsService)
    {
        $this->middleware('auth:admin');
        $this->smsService = $smsService;

        $this->openAiClient = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

	/**
     * All Vendors.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
	{  //dd($request->all());
		//check authorization start
        /* if($check)
        {
            return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
        } */
		//check authorization end
	    $roles = \App\Models\UserRole::find(Auth::user()->role);
		$newarray = json_decode($roles->module_access);
		$module_access = (array) $newarray;
		if(array_key_exists('20',  $module_access)) {
		    $query 		= Admin::where('is_archived', '=', '0')->where('role', '=', '7')->where('type', '=', 'client') ->whereNull('is_deleted');

            $totalData 	= $query->count();	//for all data
            //dd($totalData);
            if ($request->has('client_id'))
            {
                $client_id 		= 	$request->input('client_id');
                if(trim($client_id) != '')
                {
                    $query->where('client_id', '=', $client_id);
                }
            }

            if ($request->has('type'))
            {
                $type 		= 	$request->input('type');
                if(trim($type) != '')
                {
                    $query->where('type', 'LIKE', $type);
                }
            }

            if ($request->has('name')) {
                $name = trim($request->input('name'));
                if ($name != '') {
                    $query->where(function ($q) use ($name) {
                        $q->where('first_name', 'LIKE', '%' . $name . '%')
                          ->orWhere('last_name', 'LIKE', '%' . $name . '%')
                          ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$name}%"]);
                    });
                }
            }

            if ($request->has('email'))
            {
                $email 		= 	$request->input('email');
                if(trim($email) != '')
                {
                    $query->where('email', $email);
                }
            }

            /*if ($request->has('phone'))
            {
                $phone 		= 	$request->input('phone');
                if(trim($phone) != '')
                {
                    //$query->where('phone', $phone);
                    $query->where('phone', 'LIKE','%'.$phone.'%')->orwhere('att_phone', 'LIKE','%'.$phone.'%');
                }
            }*/

            if ($request->has('phone')) {
                $phone = trim($request->input('phone'));
                if ($phone != '') {
                    $query->where(function ($q) use ($phone) {
                        $q->where('phone', 'LIKE', '%' . $phone . '%')
                          ->orWhere('country_code', 'LIKE', '%' . $phone . '%')
                          ->orWhereRaw("CONCAT(country_code, phone) LIKE ?", ["%{$phone}%"]);
                    });
                }
            }

            //$lists		= $query->toSql(); //dd($lists);
            $lists		= $query->sortable(['id' => 'desc'])->paginate(20);

		} else {
		    $query 		= Admin::where('id', '=', '')->where('role', '=', '7')->whereNull('is_deleted');
		    $lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		    $totalData = 0;
		}
		return view('Admin.clients.index', compact(['lists', 'totalData']));
    }

    public function clientsmatterslist(Request $request)
    {   //dd($request->all());
        $roles = \App\Models\UserRole::find(Auth::user()->role);
        $newarray = json_decode($roles->module_access);
        $module_access = (array) $newarray;
        if(array_key_exists('20',  $module_access)) {
            $sortField = $request->get('sort', 'cm.id');
            $sortDirection = $request->get('direction', 'desc');

            $query = DB::table('client_matters as cm')
            ->join('admins as ad', 'cm.client_id', '=', 'ad.id')
            ->join('matters as ma', 'ma.id', '=', 'cm.sel_matter_id')
            ->select('cm.*', 'ad.client_id as client_unique_id','ad.first_name','ad.last_name','ad.email','ma.title','ma.nick_name','ad.dob')
            ->where('cm.matter_status', '=', '1')
            ->where('ad.is_archived', '=', '0')
            ->where('ad.role', '=', '7')
            ->whereNull('ad.is_deleted')
            ->orderBy($sortField, $sortDirection);

            $totalData 	= $query->count(); //dd($totalData);

            if ($request->has('sel_matter_id')) {
                $sel_matter_id = $request->input('sel_matter_id');
                if(trim($sel_matter_id) != '') {
                    $query->where('cm.sel_matter_id', '=', $sel_matter_id);
                }
            }

            if ($request->has('client_id')) {
                $client_id = $request->input('client_id');
                if(trim($client_id) != '') {
                    $query->where('ad.client_id', '=', $client_id);
                }
            }

            if ($request->has('name')) {
                $name = trim($request->input('name'));
                if ($name != '') {
                    $query->where(function ($q) use ($name) {
                        $q->where('ad.first_name', 'LIKE', '%' . $name . '%')
                          ->orWhere('ad.last_name', 'LIKE', '%' . $name . '%')
                          ->orWhereRaw("CONCAT(ad.first_name, ' ', ad.last_name) LIKE ?", ["%{$name}%"]);
                    });
                }
            }
            //$lists = $query->toSql(); dd($lists);
            $lists = $query->paginate(20);
        } else {
            $sortField = $request->get('sort', 'cm.id');
            $sortDirection = $request->get('direction', 'desc');

            $query = DB::table('client_matters as cm')
            ->join('admins as ad', 'cm.client_id', '=', 'ad.id')
            ->join('matters as ma', 'ma.id', '=', 'cm.sel_matter_id')
            ->select('cm.*', 'ad.client_id as client_unique_id','ad.first_name','ad.last_name','ad.email','ma.title','ma.nick_name','ad.dob')
            ->where('cm.matter_status', '=', '1')
            ->where('ad.is_archived', '=', '0')
            ->where('ad.role', '=', '7')
            ->whereNull('ad.is_deleted')
            ->orderBy($sortField, $sortDirection);
            $totalData = 0;
            $lists = $query->paginate(20);
        }
        //dd( $lists);
        return view('Admin.clients.clientsmatterslist', compact(['lists', 'totalData']));
    }

    public function archived(Request $request)
	{
		$query 		= Admin::where('is_archived', '=', '1')->where('role', '=', '7');
        $totalData 	= $query->count();	//for all data
        $lists		= $query->sortable(['id' => 'desc'])->paginate(20);
        return view('Admin.archived.index', compact(['lists', 'totalData']));
    }

	// REMOVED - prospects method
	// public function prospects(Request $request)
	// {
    //     return view('Admin.prospects.index');
    // }

	public function create(Request $request)
	{
		return view('Admin.clients.create');
	}

    public function store(Request $request)
    {   //dd($request->all());
        $requestData = $request->all();
        
        try {
            // Validate the request data
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'dob' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'dob_verified' => 'nullable|in:1',
                'dob_verify_document' => 'nullable|string|max:255',
                'age' => 'nullable|string',
                'gender' => 'nullable|in:Male,Female,Other',
                'martial_status' => 'nullable|in:Single,Married,De Facto,Divorced,Widowed,Separated',

                'phone_verified' => 'nullable|in:1',
                'email_verified' => 'nullable|in:1',
                'contact_type_hidden.*' => 'nullable|in:Personal,Work,Mobile,Business,Secondary,Father,Mother,Brother,Sister,Uncle,Aunt,Cousin,Others,Partner,Not In Use',
                'country_code.*' => 'nullable|string|max:10',
                'phone.*' => 'nullable|string|max:20',
                'email_type_hidden.*' => 'nullable|in:Personal,Work,Business',
                'email.*' => 'nullable|email|max:255',
                'visa_country.*' => 'nullable|string|max:255',
                'passports.*.passport_number' => 'nullable|string|max:50',
                'passports.*.issue_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'passports.*.expiry_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'visas.*.visa_type' => 'nullable|exists:matters,id',
                'visas.*.expiry_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'visas.*.grant_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'visas.*.description' => 'nullable|string|max:255',
                'visa_expiry_verified' => 'nullable|in:1',
                'is_current_address' => 'nullable|in:1',
                'address.*' => 'nullable|string|max:1000',
                'zip.*' => 'nullable|string|max:20',
                'regional_code.*' => 'nullable|string|max:50',
                'address_start_date.*' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'address_end_date.*' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'travel_country_visited.*' => 'nullable|string|max:255',
                'travel_arrival_date.*' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'travel_departure_date.*' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'travel_purpose.*' => 'nullable|string|max:500',
                'level_hidden.*' => 'nullable|string|max:255',
                'name.*' => 'nullable|string|max:255',
                'country_hidden.*' => 'nullable|string|max:255',
                'start_date.*' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'finish_date.*' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'relevant_qualification_hidden.*' => 'nullable|in:1',
                'job_title.*' => 'nullable|string|max:255',
                'job_code.*' => 'nullable|string|max:50',
                'job_country_hidden.*' => 'nullable|string|max:255',
                'job_start_date.*' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'job_finish_date.*' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'relevant_experience_hidden.*' => 'nullable|in:1',
                'nomi_occupation.*' => 'nullable|string|max:500',
                'occupation_code.*' => 'nullable|string|max:500',
                'list.*' => 'nullable|string|max:500',
                'visa_subclass.*' => 'nullable|string|max:500',
                'dates.*' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'expiry_dates.*' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'relevant_occupation_hidden.*' => 'nullable|in:1',
                'test_type_hidden.*' => 'nullable|in:IELTS,IELTS_A,PTE,TOEFL,CAE,OET',
                'listening.*' => 'nullable|string|max:10',
                'reading.*' => 'nullable|string|max:10',
                'writing.*' => 'nullable|string|max:10',
                'speaking.*' => 'nullable|string|max:10',
                'overall_score.*' => 'nullable|string|max:10',
                'test_date.*' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'relevant_test_hidden.*' => 'nullable|in:1',
                'naati_test' => 'nullable|in:1',
                'naati_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'py_test' => 'nullable|in:1',
                'py_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'spouse_has_english_score' => 'nullable|in:Yes,No',
                'spouse_has_skill_assessment' => 'nullable|in:Yes,No',
                'spouse_test_type' => 'nullable|in:IELTS,IELTS_A,PTE,TOEFL,CAE',
                'spouse_listening_score' => 'nullable|string|max:10',
                'spouse_reading_score' => 'nullable|string|max:10',
                'spouse_writing_score' => 'nullable|string|max:10',
                'spouse_speaking_score' => 'nullable|string|max:10',
                'spouse_overall_score' => 'nullable|string|max:10',
                'spouse_test_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'spouse_skill_assessment_status' => 'nullable|string|max:255',
                'spouse_nomi_occupation' => 'nullable|string|max:255',
                'spouse_assessment_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'criminal_charges.*.details' => 'nullable|string|max:1000',
                'criminal_charges.*.date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'military_service.*.details' => 'nullable|string|max:1000',
                'military_service.*.date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'intelligence_work.*.details' => 'nullable|string|max:1000',
                'intelligence_work.*.date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'visa_refusals.*.details' => 'nullable|string|max:1000',
                'visa_refusals.*.date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'deportations.*.details' => 'nullable|string|max:1000',
                'deportations.*.date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'citizenship_refusals.*.details' => 'nullable|string|max:1000',
                'citizenship_refusals.*.date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'health_declarations.*.details' => 'nullable|string|max:1000',
                'health_declarations.*.date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'source' => 'nullable|in:SubAgent,Others',
                'partner_details.*' => 'nullable|string|max:255',
                'partner_relationship_type.*' => 'nullable|in:Husband,Wife,Ex-Wife,Defacto',
                'partner_company_type.*' => 'nullable|in:Accompany Member,Non-Accompany Member',
                'partner_email.*' => 'nullable|email|max:255',
                'partner_first_name.*' => 'nullable|string|max:255',
                'partner_last_name.*' => 'nullable|string|max:255',
                'partner_phone.*' => 'nullable|string|max:20',
                'children_details.*' => 'nullable|string|max:255',
                'children_relationship_type.*' => 'nullable|in:Son,Daughter,Step Son,Step Daughter',
                'children_company_type.*' => 'nullable|in:Accompany Member,Non-Accompany Member',
                'children_email.*' => 'nullable|email|max:255',
                'children_first_name.*' => 'nullable|string|max:255',
                'children_last_name.*' => 'nullable|string|max:255',
                'children_phone.*' => 'nullable|string|max:20',
                'parent_details.*' => 'nullable|string|max:255',
                'parent_relationship_type.*' => 'nullable|in:Father,Mother,Step Father,Step Mother',
                'parent_company_type.*' => 'nullable|in:Accompany Member,Non-Accompany Member',
                'parent_email.*' => 'nullable|email|max:255',
                'parent_first_name.*' => 'nullable|string|max:255',
                'parent_last_name.*' => 'nullable|string|max:255',
                'parent_phone.*' => 'nullable|string|max:20',
                'siblings_details.*' => 'nullable|string|max:255',
                'siblings_relationship_type.*' => 'nullable|in:Brother,Sister,Step Brother,Step Sister',
                'siblings_company_type.*' => 'nullable|in:Accompany Member,Non-Accompany Member',
                'siblings_email.*' => 'nullable|email|max:255',
                'siblings_first_name.*' => 'nullable|string|max:255',
                'siblings_last_name.*' => 'nullable|string|max:255',
                'siblings_phone.*' => 'nullable|string|max:20',
                'others_details.*' => 'nullable|string|max:255',
                'others_relationship_type.*' => 'nullable|in:Cousin,Friend,Uncle,Aunt,Grandchild,Granddaughter,Grandparent,Niece,Nephew,Grandfather',
                'others_company_type.*' => 'nullable|in:Accompany Member,Non-Accompany Member',
                'others_email.*' => 'nullable|email|max:255',
                'others_first_name.*' => 'nullable|string|max:255',
                'others_last_name.*' => 'nullable|string|max:255',
                'others_phone.*' => 'nullable|string|max:20',
                'type' => 'required|in:lead,client',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        }

        // Custom validation: Check if at least one unique email is provided
        if (empty($validated['email']) || !array_filter($validated['email'])) {
            return redirect()->back()
                ->withErrors(['email' => 'At least one email address is required.'])
                ->withInput();
        }

        // Check if at least one email is unique (not already in database)
        $hasUniqueEmail = false;
        foreach ($validated['email'] as $email) {
            if (!empty($email) && !Admin::where('email', $email)->exists()) {
                $hasUniqueEmail = true;
                break;
            }
        }
        
        if (!$hasUniqueEmail) {
            return redirect()->back()
                ->withErrors(['email' => 'At least one unique email address is required.'])
                ->withInput();
        }

        // Custom validation: Check if at least one phone is provided
        if (empty($validated['phone']) || !array_filter($validated['phone'])) {
            return redirect()->back()
                ->withErrors(['phone' => 'At least one phone number is required.'])
                ->withInput();
        }

        // Check if at least one phone is unique (not already in database)
        $hasUniquePhone = false;
        foreach ($validated['phone'] as $index => $phone) {
            if (!empty($phone)) {
                $countryCode = $validated['country_code'][$index] ?? '';
                $fullPhone = $countryCode . $phone;
                if (!Admin::where('phone', $fullPhone)->exists()) {
                    $hasUniquePhone = true;
                    break;
                }
            }
        }
        
        if (!$hasUniquePhone) {
            return redirect()->back()
                ->withErrors(['phone' => 'At least one unique phone number is required.'])
                ->withInput();
        }

        // Check for duplicate Personal phone types
        if (!empty($validated['contact_type_hidden'])) {
            $personalPhoneCount = array_count_values($validated['contact_type_hidden'])['Personal'] ?? 0;
            if ($personalPhoneCount > 1) {
                return redirect()->back()->withErrors(['phone' => 'Only one phone number can be marked as Personal.'])->withInput();
            }
        }

        // Check for duplicate Personal email types
        if (!empty($validated['email_type_hidden'])) {
            $personalEmailCount = array_count_values($validated['email_type_hidden'])['Personal'] ?? 0;
            
        // Custom validation: DOB Verify Document is required when DOB is verified
        if (isset($validated['dob_verified']) && $validated['dob_verified'] === '1' && empty($requestData['dob_verify_document'])) {
            return redirect()->back()
                ->withErrors(['dob_verify_document' => 'DOB Verify Document is required when DOB is verified.'])
                ->withInput();
        }
            if ($personalEmailCount > 1) {
                return redirect()->back()->withErrors(['email' => 'Only one email address can be marked as Personal.'])->withInput();
            }
        }

        // Get the last email and email type
        $lastEmail = null;
        $lastEmailType = null;
        if (!empty($validated['email_type_hidden']) && !empty($validated['email'])) {
            $emailCount = count($validated['email']);
            for ($i = $emailCount - 1; $i >= 0; $i--) {
                if (!empty($validated['email'][$i])) {
                    $lastEmail = $validated['email'][$i];
                    $lastEmailType = $validated['email_type_hidden'][$i];
                    break;
                }
            }
        }

        // Get the last contact type and phone number
        $lastContactType = null;
        $lastPhone = null;
        $lastCountryCode = null;
        if (!empty($validated['contact_type_hidden']) && !empty($validated['phone'])) {
            $phoneCount = count($validated['phone']);
            for ($i = $phoneCount - 1; $i >= 0; $i--) {
                if (!empty($validated['phone'][$i])) {
                    $lastContactType = $validated['contact_type_hidden'][$i];
                    $lastCountryCode = $validated['country_code'][$i] ?? '';
                    $lastPhone = $validated['phone'][$i];
                    break;
                }
            }
        }

        // Handle special cases for duplicate email and phone
        $timestamp = time();
        $modifiedEmail = $lastEmail;
        $modifiedPhone = $lastPhone;
        $emailModified = false;
        $phoneModified = false;

                        // Check for duplicate email and handle special case
                if ($lastEmail) {
                    if (Admin::where('email', $lastEmail)->exists()) {
                        // Special case: allow demo@gmail.com to be duplicated with timestamp
                        if ($lastEmail === 'demo@gmail.com') {
                            // Add timestamp to local part (before @ symbol)
                            $emailParts = explode('@', $lastEmail);
                            $localPart = $emailParts[0];
                            $domainPart = $emailParts[1];
                            $modifiedEmail = $localPart . '_' . $timestamp . '@' . $domainPart;
                            $emailModified = true;
                        } else {
                            return redirect()->back()->withErrors(['email' => 'The provided email is already in use.'])->withInput();
                        }
                    }
                }

        // Check for duplicate phone and handle special case
        if ($lastPhone) {
            if (Admin::where('phone', $lastPhone)->exists()) {
                // Special case: allow 4444444444 to be duplicated with timestamp
                if ($lastPhone === '4444444444') {
                    $modifiedPhone = $lastPhone . '_' . $timestamp;
                    $phoneModified = true;
                } else {
                    return redirect()->back()->withErrors(['phone' => 'The provided phone number is already in use.'])->withInput();
                }
            }
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Generate client_counter and client_id
            $clientCntExist = DB::table('admins')->select('id')->where('role', 7)->count();
            if ($clientCntExist > 0) {
                $clientLatestArr = DB::table('admins')->select('client_counter')->where('role', 7)->latest()->first();
                $client_latest_counter = $clientLatestArr ? $clientLatestArr->client_counter : "00000";
            } else {
                $client_latest_counter = "00000";
            }

            $client_current_counter = $this->getNextCounter($client_latest_counter);
            $firstFourLetters = strtoupper(strlen($validated['first_name']) >= 4
                ? substr($validated['first_name'], 0, 4)
                : $validated['first_name']);
            $client_id = $firstFourLetters . date('y') . $client_current_counter;

            // Create the main client/lead record in the admins table
            $client = new Admin();
            $client->first_name = $validated['first_name'];
            $client->last_name = $validated['last_name'] ?? null;
            $client->dob = $validated['dob'] ? date('Y-m-d', strtotime(str_replace('/', '-', $validated['dob']))) : null;

            $currentDateTime = \Carbon\Carbon::now();
            $currentUserId = Auth::user()->id;

            // DOB verification
            if (isset($validated['dob_verified']) && $validated['dob_verified'] === '1') {
                $client->dob_verified_date = $currentDateTime;
                $client->dob_verified_by = $currentUserId;
            } else {
                $client->dob_verified_date = null;
                $client->dob_verified_by = null;
            }

            $client->age = $validated['age'] ?? null;
            $client->gender = $validated['gender'] ?? null;
            $client->martial_status = $validated['martial_status'] ?? null;
            $client->country_passport = $validated['visa_country'][0] ?? null;
            $client->client_counter = $client_current_counter;
            $client->client_id = $client_id;
            $client->role = 7;
            $client->email = $modifiedEmail;
            $client->email_type = $lastEmailType ?? null;

            if (isset($validated['email_verified']) && $validated['email_verified'] === '1') {
                $client->email_verified_date = $currentDateTime;
                $client->email_verified_by = $currentUserId;
            } else {
                $client->email_verified_date = null;
                $client->email_verified_by = null;
            }

            $client->country_code = $lastCountryCode ?? null;
            $client->contact_type = $lastContactType ?? null;
            $client->phone = $modifiedPhone;

            if (isset($validated['phone_verified']) && $validated['phone_verified'] === '1') {
                $client->phone_verified_date = $currentDateTime;
                $client->phone_verified_by = $currentUserId;
            } else {
                $client->phone_verified_date = null;
                $client->phone_verified_by = null;
            }

            $client->naati_test = isset($validated['naati_test']) ? 1 : 0;
            $client->naati_date = $validated['naati_date'] ? date('Y-m-d', strtotime(str_replace('/', '-', $validated['naati_date']))) : null;
            $client->nati_language = $requestData['nati_language'] ?? null;
            $client->py_test = isset($validated['py_test']) ? 1 : 0;
            $client->py_date = $validated['py_date'] ? date('Y-m-d', strtotime(str_replace('/', '-', $validated['py_date']))) : null;
            $client->py_field = $requestData['py_field'] ?? null;
            $client->regional_points = $requestData['regional_points'] ?? null;
            $client->source = $validated['source'] ?? null;
            $client->type = $validated['type'];

            $client->dob_verify_document = $requestData['dob_verify_document'];

            $client->emergency_country_code = $requestData['emergency_country_code'];
            $client->emergency_contact_no = $requestData['emergency_contact_no'];
			 $client->emergency_contact_type = $requestData['emergency_contact_type'];
          
            $client->created_at = now();
            $client->updated_at = now();

            // Visa Expiry Verification
            if (isset($validated['visa_expiry_verified']) && $validated['visa_expiry_verified'] === '1') {
                if (isset($validated['visa_country'][0]) && $validated['visa_country'][0] === 'Australia') {
                    $client->visa_expiry_verified_at = null;
                    $client->visa_expiry_verified_by = null;
                } else {
                    $client->visa_expiry_verified_at = $currentDateTime;
                    $client->visa_expiry_verified_by = $currentUserId;
                }
            } else {
                $client->visa_expiry_verified_at = null;
                $client->visa_expiry_verified_by = null;
            }

            $client->save();

            // Save phone numbers
            if (!empty($validated['contact_type_hidden']) && !empty($validated['phone'])) {
                foreach ($validated['contact_type_hidden'] as $index => $contact_type) {
                    if (!empty($validated['phone'][$index])) {
                        $phoneToSave = $validated['phone'][$index];
                        
                        // If this is the last phone and it was modified, use the modified version
                        if ($index === array_key_last($validated['phone']) && $phoneModified) {
                            $phoneToSave = $modifiedPhone;
                        }
                        
                        ClientContact::create([
                            'client_id' => $client->id,
                            'admin_id' => Auth::user()->id,
                            'contact_type' => $contact_type,
                            'country_code' => $validated['country_code'][$index] ?? null,
                            'phone' => $phoneToSave,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Save email addresses
            if (!empty($validated['email_type_hidden']) && !empty($validated['email'])) {
                foreach ($validated['email_type_hidden'] as $index => $email_type) {
                    if (!empty($validated['email'][$index])) {
                        $emailToSave = $validated['email'][$index];
                        
                        // If this is the last email and it was modified, use the modified version
                        if ($index === array_key_last($validated['email']) && $emailModified) {
                            $emailToSave = $modifiedEmail;
                        }
                        
                        ClientEmail::create([
                            'client_id' => $client->id,
                            'admin_id' => Auth::user()->id,
                            'email_type' => $email_type,
                            'email' => $emailToSave,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Save passports
            if (!empty($validated['passports'])) {
                foreach ($validated['passports'] as $index => $passport) {
                    if (!empty($passport['passport_number'])) {
                        ClientPassportInformation::create([
                            'client_id' => $client->id,
                            'admin_id' => Auth::user()->id,
                            'passport' => $passport['passport_number'],
                            'passport_issue_date' => !empty($passport['issue_date'])
                                ? date('Y-m-d', strtotime(str_replace('/', '-', $passport['issue_date'])))
                                : null,
                            'passport_expiry_date' => !empty($passport['expiry_date'])
                                ? date('Y-m-d', strtotime(str_replace('/', '-', $passport['expiry_date'])))
                                : null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Save visa details
            if (!empty($validated['visas']) && isset($validated['visa_country'][0]) && $validated['visa_country'][0] !== 'Australia') {
                foreach ($validated['visas'] as $index => $visa) {
                    if (!empty($visa['visa_type'])) {
                        ClientVisaCountry::create([
                            'client_id' => $client->id,
                            'admin_id' => Auth::user()->id,
                            'visa_type' => $visa['visa_type'],
                            'visa_expiry_date' => !empty($visa['expiry_date'])
                                ? date('Y-m-d', strtotime(str_replace('/', '-', $visa['expiry_date'])))
                                : null,
                            'visa_grant_date' => !empty($visa['grant_date'])
                                ? date('Y-m-d', strtotime(str_replace('/', '-', $visa['grant_date'])))
                                : null,
                            'visa_description' => $visa['description'] ?? null,
                            'visa_expiry_verified_at' => isset($validated['visa_expiry_verified']) ? now() : null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Save addresses
            if (!empty($validated['address'])) {
                $count = count($validated['address']);
                if ($count > 0) {
                    $lastIndex = $count - 1;
                    $lastAddress = $validated['address'][$lastIndex];
                    $lastZip = $validated['zip'][$lastIndex];

                    if (!empty($lastAddress) || !empty($lastZip)) {
                        $client->address = $lastAddress;
                        $client->zip = $lastZip;
                        $client->save();
                    }

                    $isCurrentAddress = isset($validated['is_current_address']) && $validated['is_current_address'] === '1';
                    $reversedKeys = array_reverse(array_keys($validated['address']));
                    $lastIndexInLoop = count($reversedKeys) - 1;

                    foreach ($reversedKeys as $index => $key) {
                        $addr = $validated['address'][$key] ?? null;
                        $zip = $validated['zip'][$key] ?? null;
                        $regional_code = $validated['regional_code'][$key] ?? null;
                        $start_date = $validated['address_start_date'][$key] ?? null;
                        $end_date = $validated['address_end_date'][$key] ?? null;

                        $formatted_start_date = null;
                        if (!empty($start_date)) {
                            try {
                                $date = \Carbon\Carbon::createFromFormat('d/m/Y', $start_date);
                                $formatted_start_date = $date->format('Y-m-d');
                            } catch (\Exception $e) {
                                throw new \Exception('Invalid Address Start Date format: ' . $start_date);
                            }
                        }

                        $formatted_end_date = null;
                        if (!empty($end_date)) {
                            try {
                                $date = \Carbon\Carbon::createFromFormat('d/m/Y', $end_date);
                                $formatted_end_date = $date->format('Y-m-d');
                            } catch (\Exception $e) {
                                throw new \Exception('Invalid Address End Date format: ' . $end_date);
                            }
                        }

                        if (!empty($addr) || !empty($zip)) {
                            $isCurrent = ($index === $lastIndexInLoop && $isCurrentAddress) ? 1 : 0;
                            ClientAddress::create([
                                'client_id' => $client->id,
                                'admin_id' => Auth::user()->id,
                                'address' => $addr,
                                'zip' => $zip,
                                'regional_code' => $regional_code,
                                'start_date' => $formatted_start_date,
                                'end_date' => $formatted_end_date,
                                'is_current' => $isCurrent,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            // Save travel history
            if (!empty($validated['travel_country_visited'])) {
                foreach ($validated['travel_country_visited'] as $index => $country) {
                    if (!empty($country)) {
                        ClientTravelInformation::create([
                            'client_id' => $client->id,
                            'admin_id' => Auth::user()->id,
                            'travel_country_visited' => $country,
                            'travel_arrival_date' => !empty($validated['travel_arrival_date'][$index])
                                ? date('Y-m-d', strtotime(str_replace('/', '-', $validated['travel_arrival_date'][$index])))
                                : null,
                            'travel_departure_date' => !empty($validated['travel_departure_date'][$index])
                                ? date('Y-m-d', strtotime(str_replace('/', '-', $validated['travel_departure_date'][$index])))
                                : null,
                            'travel_purpose' => $validated['travel_purpose'][$index] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Save qualifications
            if (!empty($validated['level_hidden'])) {
                foreach ($validated['level_hidden'] as $index => $level) {
                    if (!empty($level)) {
                        ClientQualification::create([
                            'client_id' => $client->id,
                            'admin_id' => Auth::user()->id,
                            'level' => $level,
                            'name' => $validated['name'][$index] ?? null,
                            'qual_college_name' => $requestData['qual_college_name'][$index] ?? null,
                            'qual_campus' => $requestData['qual_campus'][$index] ?? null,
                            'country' => $validated['country_hidden'][$index] ?? null,
                            'qual_state' => $requestData['qual_state'][$index] ?? null,
                            'start_date' => !empty($validated['start_date'][$index])
                                ? date('Y-m-d', strtotime(str_replace('/', '-', $validated['start_date'][$index])))
                                : null,
                            'finish_date' => !empty($validated['finish_date'][$index])
                                ? date('Y-m-d', strtotime(str_replace('/', '-', $validated['finish_date'][$index])))
                                : null,
                            'relevant_qualification' => isset($validated['relevant_qualification_hidden'][$index]) ? 1 : 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Save work experiences
            if (!empty($validated['job_title'])) {
                foreach ($validated['job_title'] as $index => $job_title) {
                    if (!empty($job_title)) {
                        ClientExperience::create([
                            'client_id' => $client->id,
                            'admin_id' => Auth::user()->id,
                            'job_title' => $job_title,
                            'job_code' => $validated['job_code'][$index] ?? null,
                            'job_country' => $validated['job_country_hidden'][$index] ?? null,
                            'job_start_date' => !empty($validated['job_start_date'][$index])
                                ? date('Y-m-d', strtotime(str_replace('/', '-', $validated['job_start_date'][$index])))
                                : null,
                            'job_finish_date' => !empty($validated['job_finish_date'][$index])
                                ? date('Y-m-d', strtotime(str_replace('/', '-', $validated['job_finish_date'][$index])))
                                : null,
                            'relevant_experience' => isset($validated['relevant_experience_hidden'][$index]) ? 1 : 0,
                            'job_emp_name' => $requestData['job_emp_name'][$index] ?? null,
                            'job_state' => $requestData['job_state'][$index] ?? null,
                            'job_type' => $requestData['job_type'][$index] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Save occupations
            if (!empty($validated['nomi_occupation'])) {
                foreach ($validated['nomi_occupation'] as $index => $nomi_occupation) {
                    if (!empty($nomi_occupation)) {
                        ClientOccupation::create([
                            'client_id' => $client->id,
                            'admin_id' => Auth::user()->id,
                            'nomi_occupation' => $nomi_occupation,
                            'occupation_code' => $validated['occupation_code'][$index] ?? null,
                            'list' => $validated['list'][$index] ?? null,
                            //'visa_subclass' => $validated['visa_subclass'][$index] ?? null,
                            'occ_reference_no' => $requestData['occ_reference_no'][$index] ?? null,
                            'dates' => !empty($validated['dates'][$index])
                                ? date('Y-m-d', strtotime(str_replace('/', '-', $validated['dates'][$index])))
                                : null,
                            'expiry_dates' => !empty($validated['expiry_dates'][$index])
                                ? date('Y-m-d', strtotime(str_replace('/', '-', $validated['expiry_dates'][$index])))
                                : null,
                            //'relevant_occupation' => isset($validated['relevant_occupation_hidden'][$index]) ? 1 : 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Save test scores
            if (!empty($validated['test_type_hidden'])) {
                foreach ($validated['test_type_hidden'] as $index => $test_type) {
                    if (!empty($test_type)) {
                        ClientTestScore::create([
                            'client_id' => $client->id,
                            'admin_id' => Auth::user()->id,
                            'test_type' => $test_type,
                            'listening' => $validated['listening'][$index] ?? null,
                            'reading' => $validated['reading'][$index] ?? null,
                            'writing' => $validated['writing'][$index] ?? null,
                            'speaking' => $validated['speaking'][$index] ?? null,
                            'overall_score' => $validated['overall_score'][$index] ?? null,
                            'test_date' => !empty($validated['test_date'][$index])
                                ? date('Y-m-d', strtotime(str_replace('/', '-', $validated['test_date'][$index])))
                                : null,
                            'relevant_test' => isset($validated['relevant_test_hidden'][$index]) ? 1 : 0,
                            'test_reference_no' => $requestData['test_reference_no'][$index] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Save spouse details
            if (isset($validated['martial_status']) && $validated['martial_status'] === 'Married') {
                ClientSpouseDetail::create([
                    'client_id' => $client->id,
                    'admin_id' => Auth::user()->id,
                    'spouse_has_english_score' => $validated['spouse_has_english_score'] ?? 'No',
                    'spouse_has_skill_assessment' => $validated['spouse_has_skill_assessment'] ?? 'No',
                    'spouse_test_type' => $validated['spouse_has_english_score'] === 'Yes' ? ($validated['spouse_test_type'] ?? null) : null,
                    'spouse_listening_score' => $validated['spouse_has_english_score'] === 'Yes' ? ($validated['spouse_listening_score'] ?? null) : null,
                    'spouse_reading_score' => $validated['spouse_has_english_score'] === 'Yes' ? ($validated['spouse_reading_score'] ?? null) : null,
                    'spouse_writing_score' => $validated['spouse_has_english_score'] === 'Yes' ? ($validated['spouse_writing_score'] ?? null) : null,
                    'spouse_speaking_score' => $validated['spouse_has_english_score'] === 'Yes' ? ($validated['spouse_speaking_score'] ?? null) : null,
                    'spouse_overall_score' => $validated['spouse_has_english_score'] === 'Yes' ? ($validated['spouse_overall_score'] ?? null) : null,
                    'spouse_test_date' => $validated['spouse_has_english_score'] === 'Yes' && !empty($validated['spouse_test_date'])
                        ? date('Y-m-d', strtotime(str_replace('/', '-', $validated['spouse_test_date'])))
                        : null,
                    'spouse_skill_assessment_status' => $validated['spouse_has_skill_assessment'] === 'Yes' ? ($validated['spouse_skill_assessment_status'] ?? null) : null,
                    'spouse_nomi_occupation' => $validated['spouse_has_skill_assessment'] === 'Yes' ? ($validated['spouse_nomi_occupation'] ?? null) : null,
                    'spouse_assessment_date' => $validated['spouse_has_skill_assessment'] === 'Yes' && !empty($validated['spouse_assessment_date'])
                        ? date('Y-m-d', strtotime(str_replace('/', '-', $validated['spouse_assessment_date'])))
                        : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Save character and history details
            $characterSections = [
                'criminal_charges' => 1,
                'military_service' => 2,
                'intelligence_work' => 3,
                'visa_refusals' => 4,
                'deportations' => 5,
                'citizenship_refusals' => 6,
                'health_declarations' => 7,
            ];

            foreach ($characterSections as $field => $typeOfCharacter) {
                if (!empty($validated[$field])) {
                    foreach ($validated[$field] as $index => $record) {
                        if (!empty($record['details'])) {
                            ClientCharacter::create([
                                'client_id' => $client->id,
                                'admin_id' => Auth::user()->id,
                                'type_of_character' => $typeOfCharacter,
                                'character_detail' => $record['details'],
                                'character_date' => !empty($record['date'])
                                    ? date('Y-m-d', strtotime(str_replace('/', '-', $record['date'])))
                                    : null,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            // Update Partner Handling to include all family member types
            $familyTypes = [
                'partner' => ['Husband', 'Wife', 'Ex-Wife', 'Defacto'],
                'children' => ['Son', 'Daughter', 'Step Son', 'Step Daughter'],
                'parent' => ['Father', 'Mother', 'Step Father', 'Step Mother'],
                'siblings' => ['Brother', 'Sister', 'Step Brother', 'Step Sister'],
                'others' => ['Cousin', 'Friend', 'Uncle', 'Aunt', 'Grandchild', 'Granddaughter', 'Grandparent', 'Niece', 'Nephew', 'Grandfather'],
            ];

            // Function to get reciprocal relationship based on gender
            $getReciprocalRelationship = function($relationshipType, $currentGender, $relatedGender) {
                switch ($relationshipType) {
                    // Partner relationships
                    case 'Husband':
                        return 'Wife';
                    case 'Wife':
                        return 'Husband';
                    case 'Ex-Wife':
                        return 'Ex-Husband';
                    case 'Defacto':
                        return 'Defacto';
                    
                    // Parent-Child relationships
                    case 'Son':
                        return $relatedGender === 'Female' ? 'Mother' : 'Father';
                    case 'Daughter':
                        return $relatedGender === 'Female' ? 'Mother' : 'Father';
                    case 'Step Son':
                        return $relatedGender === 'Female' ? 'Step Mother' : 'Step Father';
                    case 'Step Daughter':
                        return $relatedGender === 'Female' ? 'Step Mother' : 'Step Father';
                    case 'Father':
                        return $relatedGender === 'Female' ? 'Daughter' : 'Son';
                    case 'Mother':
                        return $relatedGender === 'Female' ? 'Daughter' : 'Son';
                    case 'Step Father':
                        return $relatedGender === 'Female' ? 'Step Daughter' : 'Step Son';
                    case 'Step Mother':
                        return $relatedGender === 'Female' ? 'Step Daughter' : 'Step Son';
                    
                    // Sibling relationships
                    case 'Brother':
                        return $relatedGender === 'Female' ? 'Sister' : 'Brother';
                    case 'Sister':
                        return $relatedGender === 'Female' ? 'Sister' : 'Brother';
                    case 'Step Brother':
                        return $relatedGender === 'Female' ? 'Step Sister' : 'Step Brother';
                    case 'Step Sister':
                        return $relatedGender === 'Female' ? 'Step Sister' : 'Step Brother';
                    
                    // Other relationships
                    case 'Cousin':
                        return 'Cousin';
                    case 'Friend':
                        return 'Friend';
                    case 'Uncle':
                        return $relatedGender === 'Female' ? 'Niece' : 'Nephew';
                    case 'Aunt':
                        return $relatedGender === 'Female' ? 'Niece' : 'Nephew';
                    case 'Grandchild':
                        return $relatedGender === 'Female' ? 'Grandmother' : 'Grandfather';
                    case 'Granddaughter':
                        return $relatedGender === 'Female' ? 'Grandmother' : 'Grandfather';
                    case 'Grandparent':
                        return $relatedGender === 'Female' ? 'Granddaughter' : 'Grandson';
                    case 'Grandfather':
                        return $relatedGender === 'Female' ? 'Granddaughter' : 'Grandson';
                    case 'Grandmother':
                        return $relatedGender === 'Female' ? 'Granddaughter' : 'Grandson';
                    case 'Niece':
                        return $relatedGender === 'Female' ? 'Aunt' : 'Uncle';
                    case 'Nephew':
                        return $relatedGender === 'Female' ? 'Aunt' : 'Uncle';
                    
                    default:
                        return $relationshipType; // Fallback to same relationship type
                }
            };

            // Clear existing relationships for the client
            foreach ($familyTypes as $type => $relationships) {
                if (!empty($requestData["{$type}_details"]) || !empty($requestData["{$type}_relationship_type"])) {
                    $detailsArray = $requestData["{$type}_details"] ?? [];
                    $relationshipTypeArray = $requestData["{$type}_relationship_type"] ?? [];
                    $partnerIdArray = $requestData["{$type}_id"] ?? [];
                    $emailArray = $requestData["{$type}_email"] ?? [];
                    $firstNameArray = $requestData["{$type}_first_name"] ?? [];
                    $lastNameArray = $requestData["{$type}_last_name"] ?? [];
                    $phoneArray = $requestData["{$type}_phone"] ?? [];
                    $companyArray = $requestData["{$type}_company_type"] ?? [];
                    $genderArray = $requestData["{$type}_gender"] ?? [];
                    //$dobArray = $requestData["{$type}_dob"] ?? [];

                    $dobArray = [];
                    if (!empty($requestData["{$type}_dob"]) && is_array($requestData["{$type}_dob"])) {
                        foreach ($requestData["{$type}_dob"] as $dobIndex => $dobValue) {
                            if (!empty($dobValue)) {
                                try {
                                    $dobDate = \Carbon\Carbon::createFromFormat('d/m/Y', $dobValue);
                                    $dobArray[$dobIndex] = $dobDate->format('Y-m-d'); // Convert to Y-m-d for storage
                                } catch (\Exception $e) {
                                    return redirect()->back()->withErrors(['dob' => 'Invalid Date of Birth format: ' . $dobValue . '. Must be in dd/mm/yyyy format.'])->withInput();
                                }
                            }
                        }
                    }

                    foreach ($detailsArray as $key => $details) {
                        $relationshipType = $relationshipTypeArray[$key] ?? null;
                        $partnerId = $partnerIdArray[$key] ?? null;
                        $email = $emailArray[$key] ?? null;
                        $firstName = $firstNameArray[$key] ?? null;
                        $lastName = $lastNameArray[$key] ?? null;
                        $phone = $phoneArray[$key] ?? null;
                        $companyType = $companyArray[$key] ?? null;
                        $gender = $genderArray[$key] ?? null;
                        $dob = $dobArray[$key] ?? null;

                        // Skip if neither details nor relationship type is provided
                        if (empty($details) && empty($relationshipType)) {
                            continue;
                        }

                        // Ensure relationship type is provided
                        if (empty($relationshipType)) {
                            throw new \Exception("Relationship type is required for {$type} entry at index {$key}.");
                        }
                        //dd($partnerId);
                        // Determine if we need to save extra fields (when related_client_id is not set)
                        $relatedClientId = $partnerId && is_numeric($partnerId) ? $partnerId : null;
                        $saveExtraFields = !$relatedClientId;

                        // Prepare data for the primary relationship
                        $partnerData = [
                            'admin_id' => Auth::user()->id,
                            'client_id' => $client->id,
                            'related_client_id' => $relatedClientId ? $relatedClientId : null,
                            'details' => $saveExtraFields ? $details : ($relatedClientId ? $details : null),
                            'relationship_type' => $relationshipType,
                            'company_type' => $companyType,
                            'email' => $saveExtraFields ? $email : null,
                            'first_name' => $saveExtraFields ? $firstName : null,
                            'last_name' => $saveExtraFields ? $lastName : null,
                            'phone' => $saveExtraFields ? $phone : null,
                            'gender' => $gender, // Always save gender as it's now a main field
                            'dob' => $saveExtraFields ? $dob : null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        // Save the primary relationship
                        $newPartner = ClientRelationship::create($partnerData);

                        // Create reciprocal relationship if related_client_id is set
                        if ($relatedClientId) {
                            $relatedClient = Admin::find($relatedClientId);
                            if ($relatedClient) {
                                // Get the reciprocal relationship type based on gender
                                $reciprocalRelationshipType = $getReciprocalRelationship($relationshipType, $gender, $relatedClient->gender ?? 'Male');
                                
                                ClientRelationship::create([
                                    'admin_id' => Auth::user()->id,
                                    'client_id' => $relatedClientId,
                                    'related_client_id' => $client->id,
                                    //'details' => $details,
                                    'details' => "{$client->first_name} {$client->last_name} ({$client->email}, {$client->phone}, {$client->client_id})",
                                    'relationship_type' => $reciprocalRelationshipType,
                                    'company_type' => $companyType,
                                    'email' => null,
                                    'first_name' => null,
                                    'last_name' => null,
                                    'phone' => null,
                                    'gender' =>  $client->gender ? $client->gender : null, // Save gender for reciprocal relationship too
                                    'dob' => null,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                    }
                }
            }

            // Commit the transaction
            DB::commit();

            // Redirect with success message
            if ($validated['type'] === 'lead') {
                return redirect()->route('admin.leads.index')->with('success', 'Lead created successfully.');
            } else {
                return redirect()->route('admin.clients.index')->with('success', 'Client created successfully.');
            }
        } catch (\Exception $e) {
            // Roll back the transaction on error
            DB::rollBack();

            // Log the error for debugging
            \Log::error('Lead/Client creation failed: ' . $e->getMessage());

            // Redirect back with error message
            if ($validated['type'] === 'lead') {
                return redirect()->back()->withErrors(['error' => 'Failed to create lead. Please try again: ' . $e->getMessage()])->withInput();
            } else {
                return redirect()->back()->withErrors(['error' => 'Failed to create client. Please try again: ' . $e->getMessage()])->withInput();
            }
        }
    }

    public function getNextCounter($currentCounter) {
        // Convert current counter to an integer
        $counter = intval($currentCounter);

        // Increment the counter
        $counter++;

        // If the counter exceeds 99999, reset it to 1
        if ($counter > 99999) {
            $counter = 1;
        }

        // Format the counter as a 5-digit number with leading zeros
        return str_pad($counter, 5, '0', STR_PAD_LEFT);
    }

	/*public function downloadpdf(Request $request, $id = NULL){
	    $fetchd = \App\Models\Document::where('id',$id)->first();
	    $data = ['title' => 'Welcome to codeplaners.com','image' => $fetchd->myfile];
        $pdf = PDF::loadView('myPDF', $data);
        return $pdf->stream('codeplaners.pdf');
	}*/

	public function downloadpdf(Request $request, $id = NULL){
	    $fetchd = \App\Models\Document::where('id',$id)->first();
        $admin = DB::table('admins')->select('client_id')->where('id', @$fetchd->client_id)->first();
        if($fetchd->doc_type == 'migration'){
            $filePath = $admin->client_id.'/'.$fetchd->folder_name.'/'.$fetchd->myfile;
        } else {
            $filePath = $admin->client_id.'/'.$fetchd->doc_type.'/'.$fetchd->myfile;
        }
        // Get the image URL from S3
        $imageUrl = Storage::disk('s3')->url($filePath); //dd($imageUrl);

        $data = ['title' => 'Welcome to codeplaners.com','image' => $imageUrl];
        // Generate the PDF
        $pdf = PDF::loadView('myPDF', compact('imageUrl'));

        // Return the generated PDF
        return $pdf->stream('codeplaners.pdf');
    }

    public function edit(Request $request, $id = NULL)
    {
        // Check authorization (assumed to be handled elsewhere)

        if ($request->isMethod('post')) {
            $requestData = $request->all();  //dd($requestData);

            //Get Db values of related files
            $db_arr = Admin::select('related_files')->where('id', $requestData['id'])->get();

            // Base validation rules
            $validationRules = [
                'first_name' => 'required|max:255',
                'last_name' => 'nullable|max:255',
                'dob' => 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01', // Updated to expect dd/mm/yyyy
                'client_id' => 'required|max:255|unique:admins,client_id,' . $requestData['id'],
                'gender' => 'nullable|in:Male,Female,Other',
                'martial_status' => 'nullable|in:Single,Married,De Facto,Divorced,Widowed,Separated',

                'visa_country' => 'nullable|string|max:255',
                'passports.*.passport_number' => 'nullable|string|max:50',
                'passports.*.issue_date' => 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01',
                'passports.*.expiry_date' => 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01',
                'visa_expiry_verified' => 'nullable|in:1',
                'visas.*.visa_type' => 'nullable|string|max:255',
                'visas.*.expiry_date' => 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01',
                'visas.*.description' => 'nullable|string|max:255',

                'travel_country_visited.*' => 'nullable|string|max:255',
                'travel_arrival_date.*' => 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01',
                'travel_departure_date.*' => 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01',
                'travel_purpose.*' => 'nullable|string|max:1000',

                'email.*' => [
                    'required',
                    'email',
                    'distinct',
                    function ($attribute, $value, $fail) use ($requestData) {
                        // Allow demo@gmail.com to be duplicated (will be handled later with timestamp)
                        if ($value === 'demo@gmail.com') {
                            return;
                        }
                        
                        // Check if this email already exists in the system (excluding current client)
                        $clientId = $requestData['id'] ?? null;
                        if ($clientId) {
                            $emailExists = DB::table('admins')
                                ->where('email', $value)
                                ->where('id', '!=', $clientId)
                                ->exists();
                            
                            if ($emailExists) {
                                $fail('The email address "' . $value . '" already exists in the system.');
                            }
                        }
                    },
                ],
                'phone.*' => [
                    'required',
                    'distinct',
                    function ($attribute, $value, $fail) use ($requestData) {
                        // Allow 4444444444 to be duplicated (will be handled later with timestamp)
                        if ($value === '4444444444') {
                            return;
                        }
                        
                        // Check if this phone already exists in the system (excluding current client)
                        $clientId = $requestData['id'] ?? null;
                        if ($clientId) {
                            $phoneExists = DB::table('admins')
                                ->where('phone', $value)
                                ->where('id', '!=', $clientId)
                                ->exists();
                            
                            if ($phoneExists) {
                                $fail('The phone number "' . $value . '" already exists in the system.');
                            }
                        }
                    },
                ],

                //'town_city' => 'nullable|string|max:255',
                //'state_region' => 'nullable|string|max:255',
                //'country' => 'nullable|string|max:255',
                'dob_verified' => 'nullable|in:1',
                'dob_verify_document' => 'nullable|string|max:255',
                'email_verified' => 'nullable|in:1',
                'phone_verified' => 'nullable|in:1',

                'test_type_hidden.*' => 'nullable|in:IELTS,IELTS_A,PTE,TOEFL,CAE,OET',
                'test_date.*' => 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01',
                'listening.*' => [
                    'nullable',
                    function ($attribute, $value, $fail) use ($requestData) {
                        $index = explode('.', $attribute)[1];
                        $testType = $requestData['test_type_hidden'][$index] ?? null;

                        if ($value && $testType) {
                            if (in_array($testType, ['IELTS', 'IELTS_A'])) {
                                $num = floatval($value);
                                if ($num < 1 || $num > 9 || fmod($num * 2, 1) != 0) {
                                    $fail('IELTS scores must be between 1 and 9 in steps of 0.5');
                                }
                            } elseif ($testType === 'TOEFL') {
                                $num = intval($value);
                                if ($num < 0 || $num > 30) {
                                    $fail('TOEFL scores must be between 0 and 30');
                                }
                            } elseif ($testType === 'PTE') {
                                $num = intval($value);
                                if ($num < 0 || $num > 90) {
                                    $fail('PTE scores must be between 0 and 90');
                                }
                            } elseif ($testType === 'OET') {
                                if (!preg_match('/^(A|B|C|C\+\+|D)$/', $value)) {
                                    $fail('OET scores must be A, B, C, C++, or D');
                                }
                            }
                        }
                    },
                ],
                'reading.*' => [
                    'nullable',
                    function ($attribute, $value, $fail) use ($requestData) {
                        $index = explode('.', $attribute)[1];
                        $testType = $requestData['test_type_hidden'][$index] ?? null;

                        if ($value && $testType) {
                            if (in_array($testType, ['IELTS', 'IELTS_A'])) {
                                $num = floatval($value);
                                if ($num < 1 || $num > 9 || fmod($num * 2, 1) != 0) {
                                    $fail('IELTS scores must be between 1 and 9 in steps of 0.5');
                                }
                            } elseif ($testType === 'TOEFL') {
                                $num = intval($value);
                                if ($num < 0 || $num > 30) {
                                    $fail('TOEFL scores must be between 0 and 30');
                                }
                            } elseif ($testType === 'PTE') {
                                $num = intval($value);
                                if ($num < 0 || $num > 90) {
                                    $fail('PTE scores must be between 0 and 90');
                                }
                            } elseif ($testType === 'OET') {
                                if (!preg_match('/^(A|B|C|C\+\+|D)$/', $value)) {
                                    $fail('OET scores must be A, B, C, C++, or D');
                                }
                            }
                        }
                    },
                ],
                'writing.*' => [
                    'nullable',
                    function ($attribute, $value, $fail) use ($requestData) {
                        $index = explode('.', $attribute)[1];
                        $testType = $requestData['test_type_hidden'][$index] ?? null;

                        if ($value && $testType) {
                            if (in_array($testType, ['IELTS', 'IELTS_A'])) {
                                $num = floatval($value);
                                if ($num < 1 || $num > 9 || fmod($num * 2, 1) != 0) {
                                    $fail('IELTS scores must be between 1 and 9 in steps of 0.5');
                                }
                            } elseif ($testType === 'TOEFL') {
                                $num = intval($value);
                                if ($num < 0 || $num > 30) {
                                    $fail('TOEFL scores must be between 0 and 30');
                                }
                            } elseif ($testType === 'PTE') {
                                $num = intval($value);
                                if ($num < 0 || $num > 90) {
                                    $fail('PTE scores must be between 0 and 90');
                                }
                            } elseif ($testType === 'OET') {
                                if (!preg_match('/^(A|B|C|C\+\+|D)$/', $value)) {
                                    $fail('OET scores must be A, B, C, C++, or D');
                                }
                            }
                        }
                    },
                ],
                'speaking.*' => [
                    'nullable',
                    function ($attribute, $value, $fail) use ($requestData) {
                        $index = explode('.', $attribute)[1];
                        $testType = $requestData['test_type_hidden'][$index] ?? null;

                        if ($value && $testType) {
                            if (in_array($testType, ['IELTS', 'IELTS_A'])) {
                                $num = floatval($value);
                                if ($num < 1 || $num > 9 || fmod($num * 2, 1) != 0) {
                                    $fail('IELTS scores must be between 1 and 9 in steps of 0.5');
                                }
                            } elseif ($testType === 'TOEFL') {
                                $num = intval($value);
                                if ($num < 0 || $num > 30) {
                                    $fail('TOEFL scores must be between 0 and 30');
                                }
                            } elseif ($testType === 'PTE') {
                                $num = intval($value);
                                if ($num < 0 || $num > 90) {
                                    $fail('PTE scores must be between 0 and 90');
                                }
                            } elseif ($testType === 'OET') {
                                if (!preg_match('/^(A|B|C|C\+\+|D)$/', $value)) {
                                    $fail('OET scores must be A, B, C, C++, or D');
                                }
                            }
                        }
                    },
                ],
                'overall_score.*' => [
                    'nullable',
                    function ($attribute, $value, $fail) use ($requestData) {
                        $index = explode('.', $attribute)[1];
                        $testType = $requestData['test_type_hidden'][$index] ?? null;

                        if ($value && $testType) {
                            if (in_array($testType, ['IELTS', 'IELTS_A'])) {
                                $num = floatval($value);
                                if ($num < 1 || $num > 9 || fmod($num * 2, 1) != 0) {
                                    $fail('IELTS overall score must be between 1 and 9 in steps of 0.5');
                                }
                            } elseif ($testType === 'TOEFL') {
                                $num = intval($value);
                                if ($num < 0 || $num > 120) { // TOEFL overall is sum of sections (4 * 30)
                                    $fail('TOEFL overall score must be between 0 and 120');
                                }
                            } elseif ($testType === 'PTE') {
                                $num = intval($value);
                                if ($num < 0 || $num > 90) {
                                    $fail('PTE overall score must be between 0 and 90');
                                }
                            } elseif ($testType === 'OET') {
                                if (!preg_match('/^(A|B|C|C\+\+|D)$/', $value)) {
                                    $fail('OET overall score must be A, B, C, C++, or D');
                                }
                            }
                        }
                    },
                ],
                'naati_date' => 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01',
                'py_date' => 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01',

                // New validations for Character & History repeatable sections
                'criminal_charges.*.details' => 'nullable|string|max:1000',
                'criminal_charges.*.date' => 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01',
                'military_service.*.details' => 'nullable|string|max:1000',
                'military_service.*.date' => 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01',
                'intelligence_work.*.details' => 'nullable|string|max:1000',
                'intelligence_work.*.date' => 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01',
                'visa_refusals.*.details' => 'nullable|string|max:1000',
                'visa_refusals.*.date' => 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01',
                'deportations.*.details' => 'nullable|string|max:1000',
                'deportations.*.date' => 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01',
                'citizenship_refusals.*.details' => 'nullable|string|max:1000',
                'citizenship_refusals.*.date' => 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01',

                // New validations for Partner fields
                'partner_details.*' => 'nullable|string|max:1000',
                'relationship_type.*' => 'nullable|in:Husband,Wife,Ex-Wife,Defacto',
                'partner_email.*' => 'nullable|email|max:255',
                'partner_first_name.*' => 'nullable|string|max:255',
                'partner_last_name.*' => 'nullable|string|max:255',
                'partner_phone.*' => 'nullable|string|max:20',

            ];

            // Update validation rules for new subsections
            $validationRules = array_merge($validationRules, [
                // Children
                'children_details.*' => 'nullable|string|max:1000',
                'children_relationship_type.*' => 'nullable|in:Son,Daughter,Step Son,Step Daughter',
                'children_email.*' => 'nullable|email|max:255',
                'children_first_name.*' => 'nullable|string|max:255',
                'children_last_name.*' => 'nullable|string|max:255',
                'children_phone.*' => 'nullable|string|max:20',

                // Parent
                'parent_details.*' => 'nullable|string|max:1000',
                'parent_relationship_type.*' => 'nullable|in:Father,Mother,Step Father,Step Mother',
                'parent_email.*' => 'nullable|email|max:255',
                'parent_first_name.*' => 'nullable|string|max:255',
                'parent_last_name.*' => 'nullable|string|max:255',
                'parent_phone.*' => 'nullable|string|max:20',

                // Siblings
                'siblings_details.*' => 'nullable|string|max:1000',
                'siblings_relationship_type.*' => 'nullable|in:Brother,Sister,Step Brother,Step Sister',
                'siblings_email.*' => 'nullable|email|max:255',
                'siblings_first_name.*' => 'nullable|string|max:255',
                'siblings_last_name.*' => 'nullable|string|max:255',
                'siblings_phone.*' => 'nullable|string|max:20',

                // Others
                'others_details.*' => 'nullable|string|max:1000',
                'others_relationship_type.*' => 'nullable|in:Cousin,Friend,Uncle,Aunt,Grandchild,Granddaughter,Grandparent,Niece,Nephew,Grandfather',
                'others_email.*' => 'nullable|email|max:255',
                'others_first_name.*' => 'nullable|string|max:255',
                'others_last_name.*' => 'nullable|string|max:255',
                'others_phone.*' => 'nullable|string|max:20',
				
			]);

            // Add validation for visa fields only if the passport country is not Australia
            if (isset($requestData['visa_country']) && $requestData['visa_country'] !== 'Australia') {
                $validationRules['visa_type_hidden.*'] = 'required|string|max:255';
                $validationRules['visa_expiry_date.*'] = 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01';
                $validationRules['visa_grant_date.*'] = 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01';
                $validationRules['visa_description.*'] = 'nullable|string|max:255';
            }

            // Add validation for qualification dates
            if (isset($requestData['level_hidden']) && is_array($requestData['level_hidden'])) {
                $validationRules['start_date.*'] = 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01';
                $validationRules['finish_date.*'] = 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01';
            }

            // Add validation for work experience dates
            if (isset($requestData['job_title']) && is_array($requestData['job_title'])) {
                $validationRules['job_country_hidden.*'] = 'nullable|string|max:255';
                $validationRules['job_start_date.*'] = 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01';
                $validationRules['job_finish_date.*'] = 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01';
            }

            // Add validation for occupation fields
            if (isset($requestData['skill_assessment_hidden']) && is_array($requestData['skill_assessment_hidden'])) {
                $validationRules['nomi_occupation.*'] = 'nullable|string|max:500';
                $validationRules['occupation_code.*'] = 'nullable|string|max:500';
                $validationRules['list.*'] = 'nullable|string|max:500';
                $validationRules['visa_subclass.*'] = 'nullable|string|max:500';
                $validationRules['dates.*'] = 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01';
                $validationRules['expiry_dates.*'] = 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01';
            }

            // Add validation for address fields (new fields: start_date, end_date)
            if (isset($requestData['address']) && is_array($requestData['address'])) {
                $validationRules['address.*'] = 'nullable|string|max:1000';
                $validationRules['zip.*'] = 'nullable|string|max:20';
                $validationRules['regional_code.*'] = 'nullable|string|max:50';
                $validationRules['address_start_date.*'] = 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01';
                $validationRules['address_end_date.*'] = 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01';
            }

            // Add validation for spouse details if marital status is Married
            if ($requestData['martial_status'] === 'Married') {
                $validationRules['spouse_has_english_score'] = 'required|in:Yes,No';
                $validationRules['spouse_has_skill_assessment'] = 'required|in:Yes,No';

                // Add validation for English score fields if spouse_has_english_score is Yes
                if (isset($requestData['spouse_has_english_score']) && $requestData['spouse_has_english_score'] === 'Yes') {
                    $validationRules['spouse_test_type'] = 'nullable|in:IELTS,IELTS_A,PTE,TOEFL,CAE';
                    $validationRules['spouse_listening_score'] = [
                        'nullable',
                        function ($attribute, $value, $fail) use ($requestData) {
                            if ($value && $requestData['spouse_test_type']) {
                                $testType = $requestData['spouse_test_type'];
                                if (in_array($testType, ['IELTS', 'IELTS_A'])) {
                                    $num = floatval($value);
                                    if ($num < 1 || $num > 9 || fmod($num * 2, 1) != 0) {
                                        $fail('Spouse IELTS Listening score must be between 1 and 9 in steps of 0.5');
                                    }
                                } elseif ($testType === 'TOEFL') {
                                    $num = intval($value);
                                    if ($num < 0 || $num > 30) {
                                        $fail('Spouse TOEFL Listening score must be between 0 and 30');
                                    }
                                } elseif ($testType === 'PTE') {
                                    $num = intval($value);
                                    if ($num < 0 || $num > 90) {
                                        $fail('Spouse PTE Listening score must be between 0 and 90');
                                    }
                                }
                            }
                        },
                    ];
                    $validationRules['spouse_reading_score'] = [
                        'nullable',
                        function ($attribute, $value, $fail) use ($requestData) {
                            if ($value && $requestData['spouse_test_type']) {
                                $testType = $requestData['spouse_test_type'];
                                if (in_array($testType, ['IELTS', 'IELTS_A'])) {
                                    $num = floatval($value);
                                    if ($num < 1 || $num > 9 || fmod($num * 2, 1) != 0) {
                                        $fail('Spouse IELTS Reading score must be between 1 and 9 in steps of 0.5');
                                    }
                                } elseif ($testType === 'TOEFL') {
                                    $num = intval($value);
                                    if ($num < 0 || $num > 30) {
                                        $fail('Spouse TOEFL Reading score must be between 0 and 30');
                                    }
                                } elseif ($testType === 'PTE') {
                                    $num = intval($value);
                                    if ($num < 0 || $num > 90) {
                                        $fail('Spouse PTE Reading score must be between 0 and 90');
                                    }
                                }
                            }
                        },
                    ];
                    $validationRules['spouse_writing_score'] = [
                        'nullable',
                        function ($attribute, $value, $fail) use ($requestData) {
                            if ($value && $requestData['spouse_test_type']) {
                                $testType = $requestData['spouse_test_type'];
                                if (in_array($testType, ['IELTS', 'IELTS_A'])) {
                                    $num = floatval($value);
                                    if ($num < 1 || $num > 9 || fmod($num * 2, 1) != 0) {
                                        $fail('Spouse IELTS Writing score must be between 1 and 9 in steps of 0.5');
                                    }
                                } elseif ($testType === 'TOEFL') {
                                    $num = intval($value);
                                    if ($num < 0 || $num > 30) {
                                        $fail('Spouse TOEFL Writing score must be between 0 and 30');
                                    }
                                } elseif ($testType === 'PTE') {
                                    $num = intval($value);
                                    if ($num < 0 || $num > 90) {
                                        $fail('Spouse PTE Writing score must be between 0 and 90');
                                    }
                                }
                            }
                        },
                    ];
                    $validationRules['spouse_speaking_score'] = [
                        'nullable',
                        function ($attribute, $value, $fail) use ($requestData) {
                            if ($value && $requestData['spouse_test_type']) {
                                $testType = $requestData['spouse_test_type'];
                                if (in_array($testType, ['IELTS', 'IELTS_A'])) {
                                    $num = floatval($value);
                                    if ($num < 1 || $num > 9 || fmod($num * 2, 1) != 0) {
                                        $fail('Spouse IELTS Speaking score must be between 1 and 9 in steps of 0.5');
                                    }
                                } elseif ($testType === 'TOEFL') {
                                    $num = intval($value);
                                    if ($num < 0 || $num > 30) {
                                        $fail('Spouse TOEFL Speaking score must be between 0 and 30');
                                    }
                                } elseif ($testType === 'PTE') {
                                    $num = intval($value);
                                    if ($num < 0 || $num > 90) {
                                        $fail('Spouse PTE Speaking score must be between 0 and 90');
                                    }
                                }
                            }
                        },
                    ];
                    $validationRules['spouse_overall_score'] = [
                        'nullable',
                        function ($attribute, $value, $fail) use ($requestData) {
                            if ($value && $requestData['spouse_test_type']) {
                                $testType = $requestData['spouse_test_type'];
                                if (in_array($testType, ['IELTS', 'IELTS_A'])) {
                                    $num = floatval($value);
                                    if ($num < 1 || $num > 9 || fmod($num * 2, 1) != 0) {
                                        $fail('Spouse IELTS Overall score must be between 1 and 9 in steps of 0.5');
                                    }
                                } elseif ($testType === 'TOEFL') {
                                    $num = intval($value);
                                    if ($num < 0 || $num > 120) {
                                        $fail('Spouse TOEFL Overall score must be between 0 and 120');
                                    }
                                } elseif ($testType === 'PTE') {
                                    $num = intval($value);
                                    if ($num < 0 || $num > 90) {
                                        $fail('Spouse PTE Overall score must be between 0 and 90');
                                    }
                                }
                            }
                        },
                    ];
                    $validationRules['spouse_test_date'] = 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01';
                }

                // Add validation for Skill Assessment fields if spouse_has_skill_assessment is Yes
                if (isset($requestData['spouse_has_skill_assessment']) && $requestData['spouse_has_skill_assessment'] === 'Yes') {
                    $validationRules['spouse_skill_assessment_status'] = 'nullable|string|max:255';
                    $validationRules['spouse_nomi_occupation'] = 'nullable|string|max:255';
                    $validationRules['spouse_assessment_date'] = 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01';
                }
            }

            // Update validation for Source field
            $validationRules['source'] = 'required|in:SubAgent,Others';
			
			$validationRules['EOI_number.*'] = 'nullable|string|max:255';
			$validationRules['EOI_subclass.*'] = 'nullable|string|max:255';
			$validationRules['EOI_occupation.*'] = 'nullable|string|max:255';
			$validationRules['EOI_point.*'] = 'nullable|numeric';
			$validationRules['EOI_state.*'] = 'nullable|string|max:255';
            $validationRules['EOI_submission_date.*'] = 'nullable|date_format:d/m/Y|after_or_equal:1000-01-01';
			$validationRules['EOI_ROI.*'] = 'nullable|string|max:255';
			$validationRules['EOI_password.*'] = 'nullable|string|max:255';
			
			// Remove validation for Type field since it's read-only
            unset($validationRules['type']);

            // Validation messages
            $validationMessages = [
                'first_name.required' => 'First name is required.',
                'client_id.required' => 'Client ID is required.',
                'client_id.unique' => 'This Client ID already exists.',
                'dob.date_format' => 'Date of Birth must be in the format YYYY-MM-DD.',
                'email.*.required' => 'Each email address is required.',
                'email.*.email' => 'Please enter a valid email address.',
                'email.*.distinct' => 'Duplicate email addresses in the form are not allowed.',
                'phone.*.required' => 'Each phone number is required.',
                'phone.*.distinct' => 'Duplicate phone numbers in the form are not allowed.',

                //'town_city.string' => 'Town/City must be a valid string.',
                //'town_city.max' => 'Town/City must not exceed 255 characters.',
                //'state_region.string' => 'State/Region must be a valid string.',
                //'state_region.max' => 'State/Region must not exceed 255 characters.',
                //'country.string' => 'Country must be a valid string.',
                //'country.max' => 'Country must not exceed 255 characters.',

                'visa_type_hidden.*.required' => 'Visa Type / Subclass is required when the country is not Australia.',
                'visa_expiry_date.*.date_format' => 'Visa Expiry Date must be in the format dd/mm/yyyy.',
                'visa_expiry_date.*.after_or_equal' => 'Visa Expiry Date must be on or after 1000-01-01.',

                'visa_grant_date.*.date_format' => 'Visa Grant Date must be in the format dd/mm/yyyy.',
                'visa_grant_date.*.after_or_equal' => 'Visa Grant Date must be on or after 1000-01-01.',

                'start_date.*.date_format' => 'Start Date must be in the format dd/mm/yyyy.',
                'start_date.*.after_or_equal' => 'Start Date must be on or after 1000-01-01.',
                'finish_date.*.date_format' => 'Finish Date must be in the format dd/mm/yyyy.',
                'finish_date.*.after_or_equal' => 'Finish Date must be on or after 1000-01-01.',
                'test_date.*.date_format' => 'Test Date must be in the format dd/mm/yyyy.',
                'test_date.*.after_or_equal' => 'Test Date must be on or after 1000-01-01.',
                'naati_date.date_format' => 'NAATI CCL Test date must be in the format dd/mm/yyyy.',
                'naati_date.after_or_equal' => 'NAATI CCL Test date must be on or after 1000-01-01.',
                'py_date.date_format' => 'Professional Year (PY) date must be in the format dd/mm/yyyy.',
                'py_date.after_or_equal' => 'Professional Year (PY) date must be on or after 1000-01-01.',

                'address.*.string' => 'Address must be a valid string.',
                'address.*.max' => 'Address must not exceed 1000 characters.',
                'zip.*.string' => 'Post Code must be a valid string.',
                'zip.*.max' => 'Post Code must not exceed 20 characters.',
                'regional_code.*.string' => 'Regional Code must be a valid string.',
                'regional_code.*.max' => 'Regional Code must not exceed 50 characters.',
                'address_start_date.*.date_format' => 'Address Start Date must be in the format dd/mm/yyyy.',
                'address_start_date.*.after_or_equal' => 'Address Start Date must be on or after 1000-01-01.',
                'address_end_date.*.date_format' => 'Address End Date must be in the format dd/mm/yyyy.',
                'address_end_date.*.after_or_equal' => 'Address End Date must be on or after 1000-01-01.',

                'visa_country.string' => 'Country of Passport must be a valid string.',
                'visa_country.max' => 'Country of Passport must not exceed 255 characters.',
                'passports.*.passport_number.string' => 'Passport Number must be a valid string.',
                'passports.*.passport_number.max' => 'Passport Number must not exceed 50 characters.',
                'passports.*.issue_date.date_format' => 'Passport Issue Date must be in the format dd/mm/yyyy.',
                'passports.*.issue_date.after_or_equal' => 'Passport Issue Date must be on or after 1000-01-01.',
                'passports.*.expiry_date.date_format' => 'Passport Expiry Date must be in the format dd/mm/yyyy.',
                'passports.*.expiry_date.after_or_equal' => 'Passport Expiry Date must be on or after 1000-01-01.',
                'visas.*.visa_type.required' => 'Visa Type / Subclass is required when the passport country is not Australia.',
                'visas.*.visa_type.string' => 'Visa Type must be a valid string.',
                'visas.*.visa_type.max' => 'Visa Type must not exceed 255 characters.',
                'visas.*.expiry_date.date_format' => 'Visa Expiry Date must be in the format dd/mm/yyyy.',
                'visas.*.expiry_date.after_or_equal' => 'Visa Expiry Date must be on or after 1000-01-01.',
                'visas.*.description.string' => 'Visa Description must be a valid string.',
                'visas.*.description.max' => 'Visa Description must not exceed 255 characters.',

                'travel_country_visited.*.string' => 'Country Visited must be a valid string.',
                'travel_country_visited.*.max' => 'Country Visited must not exceed 255 characters.',
                'travel_arrival_date.*.date_format' => 'Travel Arrival Date must be in the format dd/mm/yyyy.',
                'travel_arrival_date.*.after_or_equal' => 'Travel Arrival Date must be on or after 1000-01-01.',
                'travel_departure_date.*.date_format' => 'Travel Departure Date must be in the format dd/mm/yyyy.',
                'travel_departure_date.*.after_or_equal' => 'Travel Departure Date must be on or after 1000-01-01.',
                'travel_purpose.*.string' => 'Travel Purpose must be a valid string.',
                'travel_purpose.*.max' => 'Travel Purpose must not exceed 1000 characters.',
            ];

            // Add validation messages for work experience dates
            $validationMessages['job_country_hidden.*.string'] = 'Country must be a valid string.';
            $validationMessages['job_country_hidden.*.max'] = 'Country must not exceed 255 characters.';
            $validationMessages['job_start_date.*.date_format'] = 'Work Experience Start Date must be in the format dd/mm/yyyy.';
            $validationMessages['job_start_date.*.after_or_equal'] = 'Work Experience Start Date must be on or after 1000-01-01.';
            $validationMessages['job_finish_date.*.date_format'] = 'Work Experience Finish Date must be in the format dd/mm/yyyy.';
            $validationMessages['job_finish_date.*.after_or_equal'] = 'Work Experience Finish Date must be on or after 1000-01-01.';

            // Add validation messages for occupation fields
            $validationMessages['nomi_occupation.*.string'] = 'Nominated Occupation must be a valid string.';
            $validationMessages['nomi_occupation.*.max'] = 'Nominated Occupation must not exceed 500 characters.';
            $validationMessages['occupation_code.*.string'] = 'Occupation Code must be a valid string.';
            $validationMessages['occupation_code.*.max'] = 'Occupation Code must not exceed 500 characters.';
            $validationMessages['list.*.string'] = 'Skill Assessment Body must be a valid string.';
            $validationMessages['list.*.max'] = 'Skill Assessment Body must not exceed 500 characters.';
            $validationMessages['visa_subclass.*.string'] = 'Target Visa Subclass must be a valid string.';
            $validationMessages['visa_subclass.*.max'] = 'Target Visa Subclass must not exceed 500 characters.';
            $validationMessages['dates.*.date_format'] = 'Assessment Date must be in the format dd/mm/yyyy.';
            $validationMessages['dates.*.after_or_equal'] = 'Assessment Date must be on or after 1000-01-01.';
            $validationMessages['expiry_dates.*.date_format'] = 'Expiry Date must be in the format dd/mm/yyyy.';
            $validationMessages['expiry_dates.*.after_or_equal'] = 'Expiry Date must be on or after 1000-01-01.';

            // Add validation messages for spouse details
            $validationMessages['spouse_has_english_score.required'] = 'Please specify if the spouse has an English score.';
            $validationMessages['spouse_has_english_score.in'] = 'Spouse English score selection must be either Yes or No.';
            $validationMessages['spouse_has_skill_assessment.required'] = 'Please specify if the spouse has a skill assessment.';
            $validationMessages['spouse_has_skill_assessment.in'] = 'Spouse skill assessment selection must be either Yes or No.';
            $validationMessages['spouse_test_type.in'] = 'Spouse Test Type must be one of: IELTS, IELTS_A, PTE, TOEFL, CAE.';
            $validationMessages['spouse_test_date.date_format'] = 'Spouse Test Date must be in the format dd/mm/yyyy.';
            $validationMessages['spouse_test_date.after_or_equal'] = 'Spouse Test Date must be on or after 1000-01-01.';
            $validationMessages['spouse_skill_assessment_status.string'] = 'Spouse Skill Assessment Status must be a valid string.';
            $validationMessages['spouse_skill_assessment_status.max'] = 'Spouse Skill Assessment Status must not exceed 255 characters.';
            $validationMessages['spouse_nomi_occupation.string'] = 'Spouse Nominated Occupation must be a valid string.';
            $validationMessages['spouse_nomi_occupation.max'] = 'Spouse Nominated Occupation must not exceed 255 characters.';
            $validationMessages['spouse_assessment_date.date_format'] = 'Spouse Assessment Date must be in the format dd/mm/yyyy.';
            $validationMessages['spouse_assessment_date.after_or_equal'] = 'Spouse Assessment Date must be on or after 1000-01-01.';

            $validationMessages = [
                'type.required' => 'Type is required.',
                'type.in' => 'Type must be either lead or client.',
                'criminal_charges.*.details.string' => 'Criminal Charges Details must be a valid string.',
                'criminal_charges.*.details.max' => 'Criminal Charges Details must not exceed 1000 characters.',
                'criminal_charges.*.date.date_format' => 'Criminal Charges Date must be in the format dd/mm/yyyy.',
                'criminal_charges.*.date.after_or_equal' => 'Criminal Charges Date must be on or after 1000-01-01.',
                'military_service.*.details.string' => 'Military Service Details must be a valid string.',
                'military_service.*.details.max' => 'Military Service Details must not exceed 1000 characters.',
                'military_service.*.date.date_format' => 'Military Service Date must be in the format dd/mm/yyyy.',
                'military_service.*.date.after_or_equal' => 'Military Service Date must be on or after 1000-01-01.',
                'intelligence_work.*.details.string' => 'Intelligence Work Details must be a valid string.',
                'intelligence_work.*.details.max' => 'Intelligence Work Details must not exceed 1000 characters.',
                'intelligence_work.*.date.date_format' => 'Intelligence Work Date must be in the format dd/mm/yyyy.',
                'intelligence_work.*.date.after_or_equal' => 'Intelligence Work Date must be on or after 1000-01-01.',
                'visa_refusals.*.details.string' => 'Visa Refusals Details must be a valid string.',
                'visa_refusals.*.details.max' => 'Visa Refusals Details must not exceed 1000 characters.',
                'visa_refusals.*.date.date_format' => 'Visa Refusals Date must be in the format dd/mm/yyyy.',
                'visa_refusals.*.date.after_or_equal' => 'Visa Refusals Date must be on or after 1000-01-01.',
                'deportations.*.details.string' => 'Deportations Details must be a valid string.',
                'deportations.*.details.max' => 'Deportations Details must not exceed 1000 characters.',
                'deportations.*.date.date_format' => 'Deportations Date must be in the format dd/mm/yyyy.',
                'deportations.*.date.after_or_equal' => 'Deportations Date must be on or after 1000-01-01.',
                'citizenship_refusals.*.details.string' => 'Citizenship Refusals Details must be a valid string.',
                'citizenship_refusals.*.details.max' => 'Citizenship Refusals Details must not exceed 1000 characters.',
                'citizenship_refusals.*.date.date_format' => 'Citizenship Refusals Date must be in the format dd/mm/yyyy.',
                'citizenship_refusals.*.date.after_or_equal' => 'Citizenship Refusals Date must be on or after 1000-01-01.',

                'health_declarations.*.details.string' => 'Health Declarations Details must be a valid string.',
                'health_declarations.*.details.max' => 'Health Declarations Details must not exceed 1000 characters.',
                'health_declarations.*.date.date_format' => 'Health Declarations Date must be in the format dd/mm/yyyy.',
                'health_declarations.*.date.after_or_equal' => 'Health Declarations Date must be on or after 1000-01-01.',

                // Validation messages for Partner fields
                'partner_details.*.string' => 'Partner Details must be a valid string.',
                'partner_details.*.max' => 'Partner Details must not exceed 1000 characters.',
                'relationship_type.*.in' => 'Relationship Type must be one of: Husband, Wife, Ex-Wife, Defacto.',
                'partner_email.*.email' => 'Partner Email must be a valid email address.',
                'partner_email.*.max' => 'Partner Email must not exceed 255 characters.',
                'partner_first_name.*.string' => 'Partner First Name must be a valid string.',
                'partner_first_name.*.max' => 'Partner First Name must not exceed 255 characters.',
                'partner_last_name.*.string' => 'Partner Last Name must be a valid string.',
                'partner_last_name.*.max' => 'Partner Last Name must not exceed 255 characters.',
                'partner_phone.*.string' => 'Partner Phone must be a valid string.',
                'partner_phone.*.max' => 'Partner Phone must not exceed 20 characters.',
            ];

            // Update validation messages for new subsections
            $validationMessages = array_merge($validationMessages, [
                // Children
                'children_details.*.string' => 'Child Details must be a valid string.',
                'children_details.*.max' => 'Child Details must not exceed 1000 characters.',
                'children_relationship_type.*.in' => 'Child Relationship Type must be one of: Son, Daughter, Step Son, Step Daughter.',
                'children_email.*.email' => 'Child Email must be a valid email address.',
                'children_email.*.max' => 'Child Email must not exceed 255 characters.',
                'children_first_name.*.string' => 'Child First Name must be a valid string.',
                'children_first_name.*.max' => 'Child First Name must not exceed 255 characters.',
                'children_last_name.*.string' => 'Child Last Name must be a valid string.',
                'children_last_name.*.max' => 'Child Last Name must not exceed 255 characters.',
                'children_phone.*.string' => 'Child Phone must be a valid string.',
                'children_phone.*.max' => 'Child Phone must not exceed 20 characters.',

                // Parent
                'parent_details.*.string' => 'Parent Details must be a valid string.',
                'parent_details.*.max' => 'Parent Details must not exceed 1000 characters.',
                'parent_relationship_type.*.in' => 'Parent Relationship Type must be one of: Father, Mother, Step Father, Step Mother.',
                'parent_email.*.email' => 'Parent Email must be a valid email address.',
                'parent_email.*.max' => 'Parent Email must not exceed 255 characters.',
                'parent_first_name.*.string' => 'Parent First Name must be a valid string.',
                'parent_first_name.*.max' => 'Parent First Name must not exceed 255 characters.',
                'parent_last_name.*.string' => 'Parent Last Name must be a valid string.',
                'parent_last_name.*.max' => 'Parent Last Name must not exceed 255 characters.',
                'parent_phone.*.string' => 'Parent Phone must be a valid string.',
                'parent_phone.*.max' => 'Parent Phone must not exceed 20 characters.',

                // Siblings
                'siblings_details.*.string' => 'Sibling Details must be a valid string.',
                'siblings_details.*.max' => 'Sibling Details must not exceed 1000 characters.',
                'siblings_relationship_type.*.in' => 'Sibling Relationship Type must be one of: Brother, Sister, Step Brother, Step Sister.',
                'siblings_email.*.email' => 'Sibling Email must be a valid email address.',
                'siblings_email.*.max' => 'Sibling Email must not exceed 255 characters.',
                'siblings_first_name.*.string' => 'Sibling First Name must be a valid string.',
                'siblings_first_name.*.max' => 'Sibling First Name must not exceed 255 characters.',
                'siblings_last_name.*.string' => 'Sibling Last Name must be a valid string.',
                'siblings_last_name.*.max' => 'Sibling Last Name must not exceed 255 characters.',
                'siblings_phone.*.string' => 'Sibling Phone must be a valid string.',
                'siblings_phone.*.max' => 'Sibling Phone must not exceed 20 characters.',

                // Others
                'others_details.*.string' => 'Other Details must be a valid string.',
                'others_details.*.max' => 'Other Details must not exceed 1000 characters.',
                'others_relationship_type.*.in' => 'Other Relationship Type must be one of: Cousin, Friend, Uncle, Aunt, Grandchild, Granddaughter, Grandparent, Niece, Nephew, Grandfather.',
                'others_email.*.email' => 'Other Email must be a valid email address.',
                'others_email.*.max' => 'Other Email must not exceed 255 characters.',
                'others_first_name.*.string' => 'Other First Name must be a valid string.',
                'others_first_name.*.max' => 'Other First Name must not exceed 255 characters.',
                'others_last_name.*.string' => 'Other Last Name must be a valid string.',
                'others_last_name.*.max' => 'Other Last Name must not exceed 255 characters.',
                'others_phone.*.string' => 'Other Phone must be a valid string.',
                'others_phone.*.max' => 'Other Phone must not exceed 20 characters.',
            ]);

            $validationMessages['source.required'] = 'Source is required.';
            $validationMessages['source.in'] = 'Source must be either SubAgent or Others.';
			
			// Add EOI Reference validation messages
			$validationMessages['EOI_number.*.string'] = 'EOI Number must be a valid string.';
			$validationMessages['EOI_number.*.max'] = 'EOI Number must not exceed 255 characters.';
			$validationMessages['EOI_subclass.*.string'] = 'Subclass must be a valid string.';
			$validationMessages['EOI_subclass.*.max'] = 'Subclass must not exceed 255 characters.';
			$validationMessages['EOI_occupation.*.string'] = 'Occupation must be a valid string.';
			$validationMessages['EOI_occupation.*.max'] = 'Occupation must not exceed 255 characters.';
			$validationMessages['EOI_point.*.numeric'] = 'Point must be a valid number.';
			$validationMessages['EOI_state.*.string'] = 'State must be a valid string.';
			$validationMessages['EOI_state.*.max'] = 'State must not exceed 255 characters.';
			$validationMessages['EOI_ROI.*.string'] = 'ROI must be a valid string.';
			$validationMessages['EOI_ROI.*.max'] = 'ROI must not exceed 255 characters.';
			$validationMessages['EOI_password.*.string'] = 'Password must be a valid string.';
			$validationMessages['EOI_password.*.max'] = 'Password must not exceed 255 characters.';

            // Perform validation
            $this->validate($request, $validationRules, $validationMessages);

            // Role-based validation for DOB verification
            $client = Admin::find($requestData['id']);
            $currentUserRole = Auth::user()->role;
            
            // Check if user is trying to verify DOB or update verified DOB information
            if (isset($requestData['dob_verified']) && $requestData['dob_verified'] === '1') {
                // User is trying to set DOB as verified - only Superadmin (role=1) can do this
                if ($currentUserRole != 1) {
                    return redirect()->back()
                        ->withErrors(['dob_verified' => 'Only Superadmin can verify DOB.'])
                        ->withInput();
                }
            }
            
            // Check if client already has verified DOB and user is trying to update DOB-related fields
            if ($client && $client->dob_verified_date) {
                // Client already has verified DOB - only Superadmin (role=1) can update DOB-related fields
                if ($currentUserRole != 1) {
                    // Check if user is trying to modify DOB-related fields
                    $dobFieldsChanged = (
                        (isset($requestData['dob']) && $requestData['dob'] != ($client->dob ? date('d/m/Y', strtotime($client->dob)) : '')) ||
                        (isset($requestData['dob_verify_document']) && $requestData['dob_verify_document'] != $client->dob_verify_document) ||
                        (isset($requestData['dob_verified']) && $requestData['dob_verified'] === '0') // Trying to unverify
                    );
                    
                    if ($dobFieldsChanged) {
                        return redirect()->back()
                            ->withErrors(['dob_verified' => 'Only Superadmin can update verified DOB information.'])
                            ->withInput();
                    }
                }
            }

            // Custom validation for DOB Verify Document
            if ($client && $client->dob_verified_date && !empty($client->dob_verify_document)) {
                // If client was previously verified with a document, prevent changes (unless user is Superadmin)
                if ($requestData['dob_verify_document'] !== $client->dob_verify_document && $currentUserRole != 1) {
                    return redirect()->back()
                        ->withErrors(['dob_verify_document' => 'DOB Verify Document cannot be changed once it has been set for a verified client.'])
                        ->withInput();
                }
            } elseif (isset($requestData['dob_verified']) && $requestData['dob_verified'] === '1' && empty($requestData['dob_verify_document'])) {
                // If setting to verified but document is empty, require it
                return redirect()->back()
                    ->withErrors(['dob_verify_document' => 'DOB Verify Document is required when DOB is verified.'])
                    ->withInput();
            }

            $related_files = '';
            if(isset($requestData['related_files'])){
                for($i=0; $i<count($requestData['related_files']); $i++){
                    $related_files .= $requestData['related_files'][$i].',';
                }
            }
            //dd(rtrim($related_files,','));

            // Process Date of Birth and calculate age
            $dob = null;
            $age = null;
            if (!empty($requestData['dob'])) {
                try {
                    $dobDate = \Carbon\Carbon::createFromFormat('d/m/Y', $requestData['dob']);
                    $dob = $dobDate->format('Y-m-d'); // Convert to Y-m-d for storage
                    $age = $dobDate->diff(\Carbon\Carbon::now())->format('%y years %m months');
                } catch (\Exception $e) {
                    return redirect()->back()->withErrors(['dob' => 'Invalid Date of Birth format: ' . $requestData['dob'] . '. Must be in dd/mm/yyyy format.'])->withInput();
                }
            }

            // Find and update the Admin record
            $obj = Admin::find($requestData['id']);
            if (!$obj) {
                return redirect()->back()->with('error', 'Client not found.');
            }

            // Update basic information
            $obj->first_name = $requestData['first_name'];
            $obj->last_name = $requestData['last_name'] ?? null;
            $obj->dob = $dob;
            $obj->age = $age;
            $obj->gender = $requestData['gender'] ?? null;
            $obj->martial_status = $requestData['martial_status'] ?? null;
            $obj->client_id = $requestData['client_id'];
            //$obj->city = $requestData['town_city'] ?? null;
            //$obj->state = $requestData['state_region'] ?? null;
            //$obj->country = $requestData['country'] ?? null;
            // Only update dob_verify_document if DOB is verified
            if (isset($requestData['dob_verified']) && $requestData['dob_verified'] === '1') {
                $obj->dob_verify_document = $requestData['dob_verify_document'] ?? null;
            }
            $obj->related_files	=	rtrim($related_files,',');

            $obj->emergency_country_code = $requestData['emergency_country_code'];
            $obj->emergency_contact_no = $requestData['emergency_contact_no'];
          	$obj->emergency_contact_type = $requestData['emergency_contact_type'];

            // Handle verification fields
            $currentDateTime = \Carbon\Carbon::now();
            $currentUserId = Auth::user()->id;

            // DOB verification
            if (isset($requestData['dob_verified']) && $requestData['dob_verified'] === '1') {
                $obj->dob_verified_date = $currentDateTime;
                $obj->dob_verified_by = $currentUserId;
            } else {
                $obj->dob_verified_date = null;
                $obj->dob_verified_by = null;
                // When DOB is not verified, clear the document field
                $obj->dob_verify_document = null;
            }

            // Email verification
            if (isset($requestData['email_verified']) && $requestData['email_verified'] === '1') {
                $obj->email_verified_date = $currentDateTime;
                $obj->email_verified_by = $currentUserId;
            } else {
                $obj->email_verified_date = null;
                $obj->email_verified_by = null;
            }

            // Phone verification
            if (isset($requestData['phone_verified']) && $requestData['phone_verified'] === '1') {
                $obj->phone_verified_date = $currentDateTime;
                $obj->phone_verified_by = $currentUserId;
            } else {
                $obj->phone_verified_date = null;
                $obj->phone_verified_by = null;
            }

            // Update passport information
            if (isset($requestData['visa_country']) && is_array($requestData['visa_country']) && !empty($requestData['visa_country'][0])) {
                $obj->country_passport = $requestData['visa_country'][0];
            }

            // Handle NAATI and Professional Year (PY) tests
            $naatiTest = isset($requestData['naati_test']) && $requestData['naati_test'] === '1' ? 1 : 0;
            $naatiDate = null;
            if ($naatiTest && !empty($requestData['naati_date'])) {
                try {
                    $date = \Carbon\Carbon::createFromFormat('d/m/Y', $requestData['naati_date']);
                    $naatiDate = $date->format('Y-m-d');
                } catch (\Exception $e) {
                    return redirect()->back()->withErrors(['naati_date' => 'Invalid NAATI CCL Test date format: ' . $requestData['naati_date'] . '. Must be in dd/mm/yyyy format.'])->withInput();
                }
            }
            $obj->naati_test = $naatiTest;
            $obj->naati_date = $naatiDate;

            $nati_language = null;
            if ($naatiTest && !empty($requestData['nati_language'])) {
                $nati_language = $requestData['nati_language'] ?? null;
            }
            $obj->nati_language = $nati_language;

            $pyTest = isset($requestData['py_test']) && $requestData['py_test'] === '1' ? 1 : 0;
            $pyDate = null;
            if ($pyTest && !empty($requestData['py_date'])) {
                try {
                    $date = \Carbon\Carbon::createFromFormat('d/m/Y', $requestData['py_date']);
                    $pyDate = $date->format('Y-m-d');
                } catch (\Exception $e) {
                    return redirect()->back()->withErrors(['py_date' => 'Invalid Professional Year (PY) date format: ' . $requestData['py_date'] . '. Must be in dd/mm/yyyy format.'])->withInput();
                }
            }
            $obj->py_test = $pyTest;
            $obj->py_date = $pyDate;

            $py_field = null;
            if ($pyTest && !empty($requestData['py_field'])) {
                $py_field = $requestData['py_field'] ?? null;
            }
            $obj->py_field = $py_field ?? null;

            $obj->regional_points = $requestData['regional_points'] ?? null;

            $obj->type = $requestData['type'] ?? null; // Update type field
            $obj->source = $requestData['source'] ?? null;
            if ($requestData['source'] == 'Sub Agent') {
                $obj->agent_id = $requestData['subagent'] ?? null;
            } else {
                $obj->agent_id = '';
            }
            // Save the Admin object
            $obj->save();

            // Helper function to process character entries
            $processCharacterEntries = function ($entries, $typeOfCharacter, $deleteIdsKey) use ($obj, $requestData) {
                // Handle deletions
                if (isset($requestData[$deleteIdsKey]) && is_array($requestData[$deleteIdsKey])) {
                    foreach ($requestData[$deleteIdsKey] as $characterId) {
                        $character = ClientCharacter::find($characterId);
                        if ($character && $character->client_id == $obj->id) {
                            $character->delete();
                        }
                    }
                }

                // Process entries
                if (isset($requestData[$entries]) && is_array($requestData[$entries])) {
                    foreach ($requestData[$entries] as $index => $entry) {
                        $details = $entry['details'] ?? null;
                        $date = $entry['date'] ?? null;
                        $characterId = $entry['id'] ?? null;

                        $formattedDate = null;
                        if (!empty($date)) {
                            try {
                                $dateObj = \Carbon\Carbon::createFromFormat('d/m/Y', $date);
                                $formattedDate = $dateObj->format('Y-m-d');
                            } catch (\Exception $e) {
                                return redirect()->back()->withErrors(["{$entries}.{$index}.date" => "Invalid date format: {$date}. Must be in dd/mm/yyyy format."])->withInput();
                            }
                        }

                        if (!empty($details) || !empty($formattedDate)) {
                            if ($characterId) {
                                $existingCharacter = ClientCharacter::find($characterId);
                                if ($existingCharacter && $existingCharacter->client_id == $obj->id) {
                                    $existingCharacter->update([
                                        'admin_id' => Auth::user()->id,
                                        'character_detail' => $details,
                                        'character_date' => $formattedDate,
                                    ]);
                                }
                            } else {
                                ClientCharacter::create([
                                    'admin_id' => Auth::user()->id,
                                    'client_id' => $obj->id,
                                    'type_of_character' => $typeOfCharacter,
                                    'character_detail' => $details,
                                    'character_date' => $formattedDate,
                                ]);
                            }
                        }
                    }
                }
            };

            // Process Character & History entries
            $processCharacterEntries('criminal_charges', 1, 'delete_criminal_charges_ids');
            $processCharacterEntries('military_service', 2, 'delete_military_service_ids');
            $processCharacterEntries('intelligence_work', 3, 'delete_intelligence_work_ids');
            $processCharacterEntries('visa_refusals', 4, 'delete_visa_refusals_ids');
            $processCharacterEntries('deportations', 5, 'delete_deportations_ids');
            $processCharacterEntries('citizenship_refusals', 6, 'delete_citizenship_refusals_ids');
            $processCharacterEntries('health_declarations', 7, 'delete_health_declarations_ids');

            // Handle Phone Number Deletion
            if (isset($requestData['delete_phone_ids']) && is_array($requestData['delete_phone_ids'])) {
                foreach ($requestData['delete_phone_ids'] as $contactId) {
                    $contact = ClientContact::find($contactId);
                    if ($contact && $contact->client_id == $obj->id) {
                        $contact->delete();
                    }
                }
            }

            // Initialize variables for phone handling
            $phoneModified = false;
            $modifiedPhone = null;
            $timestamp = time();

            if (
                (isset($requestData['contact_type_hidden']) && is_array($requestData['contact_type_hidden']))
                &&
                (isset($requestData['phone']) && is_array($requestData['phone']))
            ) {
                $count_contact = count($requestData['contact_type_hidden']);
                if ($count_contact > 0) {
                    // Check for multiple "Personal" types
                    $personalCount = 0;
                    foreach ($requestData['contact_type_hidden'] as $key => $contactType) {
                        if ($contactType === 'Personal') {
                            $personalCount++;
                            if ($personalCount > 1) {
                                return redirect()->back()->withErrors(['phone.' . $key => 'Only one phone number can be of type Personal.'])->withInput();
                            }
                        }
                    }

                    $lastContactType = end($requestData['contact_type_hidden']);
                    $lastPhone = end($requestData['phone']);
                    $lastCountryCode = end($requestData['country_code']);

                    if ($lastPhone != "") {
                        $phoneExistsInAdmins = DB::table('admins')
                            ->where('phone', $lastPhone)
                            ->where('country_code', $lastCountryCode)
                            ->where('id', '!=', $obj->id)
                            ->exists();

                        // Handle special case for duplicate phone
                        $modifiedPhone = $lastPhone;

                        if ($phoneExistsInAdmins) {
                            // Special case: allow 4444444444 to be duplicated with timestamp
                            if ($lastPhone === '4444444444') {
                                $modifiedPhone = $lastPhone . '_' . $timestamp;
                                $phoneModified = true;
                            } else {
                                return redirect()->back()->withErrors(['phone' => 'The phone number "' . $lastCountryCode . $lastPhone . '" already exists in the system.'])->withInput();
                            }
                        }

                        $obj->contact_type = $lastContactType;
                        $obj->phone = $modifiedPhone;
                        $obj->country_code = $lastCountryCode;
                        $obj->save();
                    }

                    foreach ($requestData['contact_type_hidden'] as $key => $contactType) {
                        $contactId = $requestData['contact_id'][$key] ?? null;
                        $phone = $requestData['phone'][$key] ?? null;
                        $country_code = $requestData['country_code'][$key] ?? null;

                        if (!empty($contactType) && !empty($phone)) {
                            // If this is the last phone and it was modified, use the modified version
                            $phoneToSave = $phone;
                            if ($key === array_key_last($requestData['contact_type_hidden']) && $phoneModified && $phone === $lastPhone) {
                                $phoneToSave = $modifiedPhone;
                            }

                            $duplicatePhone = ClientContact::where('phone', $phoneToSave)
                                ->where('country_code', $country_code)
                                ->where('client_id', $obj->id)
                                ->where('id', '!=', $contactId)
                                ->first();

                            if ($duplicatePhone) {
                                return redirect()->back()->withErrors(['phone.' . $key => 'This phone number is already taken for this client: ' . $country_code . $phoneToSave])->withInput();
                            }

                            if ($contactId) {
                                $existingContact = ClientContact::find($contactId);
                                if ($existingContact) {
                                    $existingContact->update([
                                        'admin_id' => Auth::user()->id,
                                        'contact_type' => $contactType,
                                        'phone' => $phoneToSave,
                                        'country_code' => $country_code
                                    ]);
                                }
                            } else {
                                ClientContact::create([
                                    'admin_id' => Auth::user()->id,
                                    'client_id' => $obj->id,
                                    'contact_type' => $contactType,
                                    'phone' => $phoneToSave,
                                    'country_code' => $country_code
                                ]);
                            }
                        }
                    }
                }
            }

            // Handle Email Deletion
            if (isset($requestData['delete_email_ids']) && is_array($requestData['delete_email_ids'])) {
                foreach ($requestData['delete_email_ids'] as $emailId) {
                    $email = ClientEmail::find($emailId);
                    if ($email && $email->client_id == $obj->id) {
                        $email->delete();
                    }
                }
            }

            // Initialize variables for email handling
            $emailModified = false;
            $modifiedEmail = null;

            // Email Type Handling
            if (
                (isset($requestData['email_type_hidden']) && is_array($requestData['email_type_hidden']))
                &&
                (isset($requestData['email']) && is_array($requestData['email']))
            ) {
                $count_email_type = count($requestData['email_type_hidden']);
                if ($count_email_type > 0) {
                    // Check for multiple "Personal" types
                    $personalCount = 0;
                    foreach ($requestData['email_type_hidden'] as $key => $emailType) {
                        if ($emailType === 'Personal') {
                            $personalCount++;
                            if ($personalCount > 1) {
                                return redirect()->back()->withErrors(['email.' . $key => 'Only one email address can be of type Personal.'])->withInput();
                            }
                        }
                    }

                    $lastEmailType = end($requestData['email_type_hidden']);
                    $lastEmail = end($requestData['email']);
                    if ($lastEmail != "") {
                        $emailExistsInAdmins = DB::table('admins')
                            ->where('email', $lastEmail)
                            ->where('id', '!=', $obj->id)
                            ->exists();

                        // Handle special case for duplicate email
                        $modifiedEmail = $lastEmail;

                        if ($emailExistsInAdmins) {
                            // Special case: allow demo@gmail.com to be duplicated with timestamp
                            if ($lastEmail === 'demo@gmail.com') {
                                // Add timestamp to local part (before @ symbol)
                                $emailParts = explode('@', $lastEmail);
                                $localPart = $emailParts[0];
                                $domainPart = $emailParts[1];
                                $modifiedEmail = $localPart . '_' . $timestamp . '@' . $domainPart;
                                $emailModified = true;
                            } else {
                                return redirect()->back()->withErrors(['email' => 'The email address "' . $lastEmail . '" already exists in the system.'])->withInput();
                            }
                        }

                        $obj->email_type = $lastEmailType;
                        $obj->email = $modifiedEmail;
                        $obj->save();
                    }

                    foreach ($requestData['email_type_hidden'] as $key => $emailType) {
                        $email = $requestData['email'][$key] ?? null;
                        $emailId = $requestData['email_id'][$key] ?? null;

                        if (!empty($emailType) && !empty($email)) {
                            // If this is the last email and it was modified, use the modified version
                            $emailToSave = $email;
                            if ($key === array_key_last($requestData['email_type_hidden']) && $emailModified && $email === $lastEmail) {
                                $emailToSave = $modifiedEmail;
                            }

                            $duplicateEmail = ClientEmail::where('email', $emailToSave)
                                ->where('client_id', $obj->id)
                                ->where('id', '!=', $emailId)
                                ->first();

                            if ($duplicateEmail) {
                                return redirect()->back()->withErrors(['email.' . $key => 'This email is already taken for this client: ' . $emailToSave])->withInput();
                            }

                            if ($emailId) {
                                $existingEmail = ClientEmail::find($emailId);
                                if ($existingEmail && $existingEmail->client_id == $obj->id) {
                                    $existingEmail->update([
                                        'email_type' => $emailType,
                                        'email' => $emailToSave,
                                        'admin_id' => Auth::user()->id
                                    ]);
                                }
                            } else {
                                ClientEmail::create([
                                    'admin_id' => Auth::user()->id,
                                    'client_id' => $obj->id,
                                    'email_type' => $emailType,
                                    'email' => $emailToSave
                                ]);
                            }
                        }
                    }
                }
            }

            // Handle Passport Deletion
            if (isset($requestData['delete_passport_ids']) && is_array($requestData['delete_passport_ids'])) {
                foreach ($requestData['delete_passport_ids'] as $passportId) {
                    $passport = ClientPassportInformation::find($passportId);
                    if ($passport && $passport->client_id == $obj->id) {
                        $passport->delete();
                    }
                }
            }

            // Passport Information Handling
            if (
                (isset($requestData['visa_country']) && !empty($requestData['visa_country'])) ||
                (isset($requestData['passports']) && is_array($requestData['passports']))
            ) {
                $passportCountry = $requestData['visa_country'] ?? null;

                // Update the main Admin record with the passport country
                $obj->country_passport = $passportCountry;
                $obj->save();

                // Delete existing passport records for the client to start fresh
                ClientPassportInformation::where('client_id', $obj->id)->delete();

                // Process each passport entry
                if (isset($requestData['passports'])) {
                    foreach ($requestData['passports'] as $key => $passportData) {
                        $passportNumber = $passportData['passport_number'] ?? null;
                        $issueDate = $passportData['issue_date'] ?? null;
                        $expiryDate = $passportData['expiry_date'] ?? null;
                        $passportId = $requestData['passport_id'][$key] ?? null;

                        // Convert dates from dd/mm/yyyy to Y-m-d for database storage
                        $formattedIssueDate = null;
                        if (!empty($issueDate)) {
                            try {
                                $date = \Carbon\Carbon::createFromFormat('d/m/Y', $issueDate);
                                $formattedIssueDate = $date->format('Y-m-d');
                            } catch (\Exception $e) {
                                return redirect()->back()->withErrors(['passports.' . $key . '.issue_date' => 'Invalid Passport Issue Date format: ' . $issueDate])->withInput();
                            }
                        }

                        $formattedExpiryDate = null;
                        if (!empty($expiryDate)) {
                            try {
                                $date = \Carbon\Carbon::createFromFormat('d/m/Y', $expiryDate);
                                $formattedExpiryDate = $date->format('Y-m-d');
                            } catch (\Exception $e) {
                                return redirect()->back()->withErrors(['passports.' . $key . '.expiry_date' => 'Invalid Passport Expiry Date format: ' . $expiryDate])->withInput();
                            }
                        }

                        if (!empty($passportNumber) && !empty($passportCountry)) {
                            ClientPassportInformation::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $obj->id,
                                'passport_country' => $passportCountry,
                                'passport' => $passportNumber,
                                'passport_issue_date' => $formattedIssueDate,
                                'passport_expiry_date' => $formattedExpiryDate,
                            ]);
                        }
                    }
                }
            }

            // Visa Expiry Verification
            if (isset($requestData['visa_expiry_verified']) && $requestData['visa_expiry_verified'] === '1') {
                if ( isset($requestData['visa_country']) && $requestData['visa_country'] === 'Australia' ) {
                    $obj->visa_expiry_verified_at = null;
                    $obj->visa_expiry_verified_by = null;
                } else {
                    $obj->visa_expiry_verified_at = $currentDateTime;
                    $obj->visa_expiry_verified_by = $currentUserId;
                }
            } else {
                $obj->visa_expiry_verified_at = null;
                $obj->visa_expiry_verified_by = null;
            }

            // Visa Details Handling
            if (
                (isset($requestData['visa_country']) && !empty($requestData['visa_country'])) ||
                (isset($requestData['visas']) && is_array($requestData['visas']))
            ) {
                // Delete existing visa records for the client
                ClientVisaCountry::where('client_id', $obj->id)->delete();

                $passportCountry = $requestData['visa_country'] ?? null;

                if ($passportCountry === 'Australia') {
                    // If passport country is Australia, save minimal visa details
                    if (!empty($passportCountry)) {
                        ClientVisaCountry::create([
                            'admin_id' => Auth::user()->id,
                            'client_id' => $obj->id,
                            'visa_country' => $passportCountry,
                            'visa_type' => "",
                            'visa_expiry_date' => null,
                            'visa_grant_date' => null,
                            'visa_description' => ""
                        ]);

                        $obj->visa_type = "";
                        $obj->visaExpiry = null;
                        $obj->visaGrant = null;
                        $obj->save();
                    }
                } else {
                    // If passport country is not Australia, save visa details if provided
                    if (isset($requestData['visas'])) {
                        foreach ($requestData['visas'] as $key => $visaData) {
                            $visaType = $visaData['visa_type'] ?? null;
                            $visaExpiryDate = $visaData['expiry_date'] ?? null;
                            $visaGrantDate = $visaData['grant_date'] ?? null;
                            $visaDescription = $visaData['description'] ?? null;
                            $visaId = $visaData['id'] ?? null;

                            // Convert visa_expiry_date from dd/mm/yyyy to Y-m-d
                            $formattedExpiryDate = null;
                            if (!empty($visaExpiryDate)) {
                                try {
                                    $date = \Carbon\Carbon::createFromFormat('d/m/Y', $visaExpiryDate);
                                    $formattedExpiryDate = $date->format('Y-m-d');
                                } catch (\Exception $e) {
                                    return redirect()->back()->withErrors(['visas.' . $key . '.expiry_date' => 'Invalid Visa Expiry Date format: ' . $visaExpiryDate])->withInput();
                                }
                            }

                            $formattedGrantDate = null;
                            if (!empty($visaGrantDate)) {
                                try {
                                    $date1 = \Carbon\Carbon::createFromFormat('d/m/Y', $visaGrantDate);
                                    $formattedGrantDate = $date1->format('Y-m-d');
                                } catch (\Exception $e) {
                                    return redirect()->back()->withErrors(['visas.' . $key . '.grant_date' => 'Invalid Visa Grant Date format: ' . $visaGrantDate])->withInput();
                                }
                            }

                            if (!empty($visaType) && !empty($passportCountry)) {
                                ClientVisaCountry::create([
                                    'admin_id' => Auth::user()->id,
                                    'client_id' => $obj->id,
                                    'visa_country' => $passportCountry,
                                    'visa_type' => $visaType,
                                    'visa_expiry_date' => $formattedExpiryDate,
                                    'visa_grant_date' => $formattedGrantDate,
                                    'visa_description' => $visaDescription
                                ]);
                            }
                        }

                        // Update Admin model with the last visa details
                        if (!empty($requestData['visas'])) {
                            $lastVisa = end($requestData['visas']);
                            $lastVisaType = $lastVisa['visa_type'] ?? null;
                            $lastVisaExpiryDate = $lastVisa['expiry_date'] ?? null;
                            $lastVisaGrantDate = $lastVisa['grant_date'] ?? null;

                            $lastFormattedExpiryDate = null;
                            if (!empty($lastVisaExpiryDate)) {
                                try {
                                    $date = \Carbon\Carbon::createFromFormat('d/m/Y', $lastVisaExpiryDate);
                                    $lastFormattedExpiryDate = $date->format('Y-m-d');
                                } catch (\Exception $e) {
                                    $lastFormattedExpiryDate = null;
                                }
                            }

                            $lastFormattedGrantDate = null;
                            if (!empty($lastVisaGrantDate)) {
                                try {
                                    $date1 = \Carbon\Carbon::createFromFormat('d/m/Y', $lastVisaGrantDate);
                                    $lastFormattedGrantDate = $date1->format('Y-m-d');
                                } catch (\Exception $e) {
                                    $lastFormattedGrantDate = null;
                                }
                            }

                            if (!empty($lastVisaType) && !empty($passportCountry)) {
                                $obj->visa_type = $lastVisaType;
                                $obj->visaExpiry = $lastFormattedExpiryDate;
                                $obj->visaGrant = $lastFormattedGrantDate;
                                $obj->save();
                            }
                        }
                    }
                }
            }

            // Handle Travel Deletion
            if (isset($requestData['delete_travel_ids']) && is_array($requestData['delete_travel_ids'])) {
                foreach ($requestData['delete_travel_ids'] as $travelId) {
                    $travel = ClientTravelInformation::find($travelId);
                    if ($travel && $travel->client_id == $obj->id) {
                        $travel->delete();
                    }
                }
            }

            // Travel Information Handling
            if (
                (isset($requestData['travel_country_visited']) && is_array($requestData['travel_country_visited'])) ||
                (isset($requestData['travel_purpose']) && is_array($requestData['travel_purpose']))
            ) {
                foreach ($requestData['travel_country_visited'] as $key => $countryVisited) {
                    $arrivalDate = $requestData['travel_arrival_date'][$key] ?? null;
                    $departureDate = $requestData['travel_departure_date'][$key] ?? null;
                    $travelPurpose = $requestData['travel_purpose'][$key] ?? null;
                    $travelId = $requestData['travel_id'][$key] ?? null;

                    // Convert dates from dd/mm/yyyy to Y-m-d
                    $formattedArrivalDate = null;
                    if (!empty($arrivalDate)) {
                        try {
                            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $arrivalDate);
                            $formattedArrivalDate = $date->format('Y-m-d');
                        } catch (\Exception $e) {
                            return redirect()->back()->withErrors(['travel_arrival_date.' . $key => 'Invalid Travel Arrival Date format: ' . $arrivalDate])->withInput();
                        }
                    }

                    $formattedDepartureDate = null;
                    if (!empty($departureDate)) {
                        try {
                            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $departureDate);
                            $formattedDepartureDate = $date->format('Y-m-d');
                        } catch (\Exception $e) {
                            return redirect()->back()->withErrors(['travel_departure_date.' . $key => 'Invalid Travel Departure Date format: ' . $departureDate])->withInput();
                        }
                    }

                    if (!empty($countryVisited) || !empty($travelPurpose)) {
                        if ($travelId) {
                            $existingTravel = ClientTravelInformation::find($travelId);
                            if ($existingTravel && $existingTravel->client_id == $obj->id) {
                                $existingTravel->update([
                                    'admin_id' => Auth::user()->id,
                                    'travel_country_visited' => $countryVisited,
                                    'travel_arrival_date' => $formattedArrivalDate,
                                    'travel_departure_date' => $formattedDepartureDate,
                                    'travel_purpose' => $travelPurpose,
                                ]);
                            }
                        } else {
                            ClientTravelInformation::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $obj->id,
                                'travel_country_visited' => $countryVisited,
                                'travel_arrival_date' => $formattedArrivalDate,
                                'travel_departure_date' => $formattedDepartureDate,
                                'travel_purpose' => $travelPurpose,
                            ]);
                        }
                    }
                }
            }

            // Updated Address Handling
            if (
                (isset($requestData['zip']) && is_array($requestData['zip']))
                ||
                (isset($requestData['address']) && is_array($requestData['address']))
            ) {
                $count = count($requestData['zip']);
                if ($count > 0) {
                    // Get the first address (most recent due to frontend ordering)
                    // Use array_values to reindex the array and safely access the first element
                    $addressArray = array_values($requestData['address']);
                    $zipArray = array_values($requestData['zip']);
                    
                    $firstAddress = !empty($addressArray) ? $addressArray[0] : null;
                    $firstZip = !empty($zipArray) ? $zipArray[0] : null;

                    if (!empty($firstAddress) || !empty($firstZip)) {
                        $obj->address = $firstAddress;
                        $obj->zip = $firstZip;
                        $obj->save();
                    }

                    // Delete existing addresses to start fresh
                    ClientAddress::where('client_id', $obj->id)->delete();

                    // Check if "Is this your current address?" is checked
                    $isCurrentAddress = isset($requestData['is_current_address']) && $requestData['is_current_address'] === '1';

                    // Reverse the array keys to save the last address first
                    $reversedKeys = array_reverse(array_keys($requestData['address']));
                    $lastIndexInLoop = count($reversedKeys) - 1; // The last index in the reversed loop
                    // Process each address in reverse order (so the first in the form is the most recent)
                    foreach ($reversedKeys as $index =>$key) {
                        $addr = $requestData['address'][$key] ?? null;
                        $zip = $requestData['zip'][$key] ?? null;
                        $regional_code = $requestData['regional_code'][$key] ?? null;
                        $start_date = $requestData['address_start_date'][$key] ?? null;
                        $end_date = $requestData['address_end_date'][$key] ?? null;
                        $addressId = $requestData['address_id'][$key] ?? null;

                        // Convert start_date from dd/mm/yyyy to Y-m-d for database storage
                        $formatted_start_date = null;
                        if (!empty($start_date)) {
                            try {
                                $date = \Carbon\Carbon::createFromFormat('d/m/Y', $start_date);
                                $formatted_start_date = $date->format('Y-m-d');
                            } catch (\Exception $e) {
                                return redirect()->back()->withErrors(['start_date.' . $key => 'Invalid Address Start Date format: ' . $start_date])->withInput();
                            }
                        }

                        // Convert end_date from dd/mm/yyyy to Y-m-d for database storage
                        $formatted_end_date = null;
                        if (!empty($end_date)) {
                            try {
                                $date = \Carbon\Carbon::createFromFormat('d/m/Y', $end_date);
                                $formatted_end_date = $date->format('Y-m-d');
                            } catch (\Exception $e) {
                                return redirect()->back()->withErrors(['end_date.' . $key => 'Invalid Address End Date format: ' . $end_date])->withInput();
                            }
                        }

                        if (!empty($addr) || !empty($zip)) {
                        // Set is_current to 1 for the last address saved (first in the form array) if the checkbox is checked, otherwise 0
                            $isCurrent = ($index === $lastIndexInLoop && $isCurrentAddress) ? 1 : 0;

                            ClientAddress::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $obj->id,
                                'address' => $addr,
                                'zip' => $zip,
                                'regional_code' => $regional_code,
                                'start_date' => $formatted_start_date,
                                'end_date' => $formatted_end_date,
                                'is_current' => $isCurrent,
                            ]);
                        }
                    }
                }
            }

            // Handle Qualification Deletion
            if (isset($requestData['delete_qualification_ids']) && is_array($requestData['delete_qualification_ids'])) {
                foreach ($requestData['delete_qualification_ids'] as $qualificationId) {
                    $qualification = ClientQualification::find($qualificationId);
                    if ($qualification && $qualification->client_id == $obj->id) {
                        $qualification->delete();
                    }
                }
            }

            // Handle Experience Deletion
            if (isset($requestData['delete_experience_ids']) && is_array($requestData['delete_experience_ids'])) {
                foreach ($requestData['delete_experience_ids'] as $experienceId) {
                    $experience = ClientExperience::find($experienceId);
                    if ($experience && $experience->client_id == $obj->id) {
                        $experience->delete();
                    }
                }
            }

            // Handle Occupation Deletion
            if (isset($requestData['delete_occupation_ids']) && is_array($requestData['delete_occupation_ids'])) {
                foreach ($requestData['delete_occupation_ids'] as $occupationId) {
                    $occupation = ClientOccupation::find($occupationId);
                    if ($occupation && $occupation->client_id == $obj->id) {
                        $occupation->delete();
                    }
                }
            }

            // Handle Test Score Deletion
            if (isset($requestData['delete_test_score_ids']) && is_array($requestData['delete_test_score_ids'])) {
                foreach ($requestData['delete_test_score_ids'] as $testScoreId) {
                    $testScore = ClientTestScore::find($testScoreId);
                    if ($testScore && $testScore->client_id == $obj->id) {
                        $testScore->delete();
                    }
                }
            }

            // Client Qualification Handling (updated to handle start_date and finish_date format conversion)
            if (
                (isset($requestData['level_hidden']) && is_array($requestData['level_hidden']))
                ||
                (isset($requestData['name']) && is_array($requestData['name']))
            ) {
                $qualificationCount = count($requestData['level_hidden']);
                if ($qualificationCount > 0) {
                    // Use array_values to reindex the arrays and safely access elements
                    $levelArray = array_values($requestData['level_hidden']);
                    $nameArray = array_values($requestData['name']);
                    
                    $secondLastLevel = $levelArray[$qualificationCount - 1] ?? null;
                    $secondLastName = $nameArray[$qualificationCount - 1] ?? null;

                    if (!empty($secondLastLevel) || !empty($secondLastName)) {
                        $obj->qualification_level = $secondLastLevel;
                        $obj->qualification_name = $secondLastName;
                        $obj->save();
                    }
                }

                foreach ($requestData['level_hidden'] as $key => $level) {
                    $name = $requestData['name'][$key] ?? null;
                    $qual_college_name = $requestData['qual_college_name'][$key] ?? null;
                    $qual_campus = $requestData['qual_campus'][$key] ?? null;
                    $country = $requestData['country_hidden'][$key] ?? null;
                    $qual_state = $requestData['qual_state'][$key] ?? null;
                    $start = $requestData['start_date'][$key] ?? null;
                    $finish = $requestData['finish_date'][$key] ?? null;
                    $relevant_qualification = $requestData['relevant_qualification_hidden'][$key] ?? null;
                    $qualificationId = $requestData['qualification_id'][$key] ?? null;

                    // Convert start_date from dd/mm/yyyy to Y-m-d for database storage
                    $formatted_start_date = null;
                    if (!empty($start)) {
                        try {
                            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $start);
                            $formatted_start_date = $date->format('Y-m-d');
                        } catch (\Exception $e) {
                            return redirect()->back()->withErrors(['start_date.' . $key => 'Invalid Start Date format: ' . $start])->withInput();
                        }
                    }

                    // Convert finish_date from dd/mm/yyyy to Y-m-d for database storage
                    $formatted_finish_date = null;
                    if (!empty($finish)) {
                        try {
                            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $finish);
                            $formatted_finish_date = $date->format('Y-m-d');
                        } catch (\Exception $e) {
                            return redirect()->back()->withErrors(['finish_date.' . $key => 'Invalid Finish Date format: ' . $finish])->withInput();
                        }
                    }

                    if (!empty($level) || !empty($name)) {
                        if ($qualificationId) {
                            $existingQualification = ClientQualification::find($qualificationId);
                            if ($existingQualification && $existingQualification->client_id == $obj->id) {
                                $existingQualification->update([
                                    'admin_id' => Auth::user()->id,
                                    'level' => $level,
                                    'name' => $name,
                                    'qual_college_name' => $qual_college_name,
                                    'qual_campus' => $qual_campus,
                                    'country' => $country,
                                    'qual_state' => $qual_state,
                                    'start_date' => $formatted_start_date, // Use the formatted date
                                    'finish_date' => $formatted_finish_date, // Use the formatted date
                                    'relevant_qualification' => $relevant_qualification
                                ]);
                            }
                        } else {
                            ClientQualification::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $obj->id,
                                'level' => $level,
                                'name' => $name,
                                'qual_college_name' => $qual_college_name,
                                'qual_campus' => $qual_campus,
                                'country' => $country,
                                'qual_state' => $qual_state,
                                'start_date' => $formatted_start_date, // Use the formatted date
                                'finish_date' => $formatted_finish_date, // Use the formatted date
                                'relevant_qualification' => $relevant_qualification
                            ]);
                        }
                    }
                }
            }

            // Client Experience Handling (updated to handle country, job_start_date, and job_finish_date format conversion)
            if (
                (isset($requestData['job_title']) && is_array($requestData['job_title']))
                ||
                (isset($requestData['job_code']) && is_array($requestData['job_code']))
            ) {
                foreach ($requestData['job_title'] as $key => $jobTitle) {
                    $jobCode = $requestData['job_code'][$key] ?? null;
                    $jobCountry = $requestData['job_country_hidden'][$key] ?? null;
                    $jobStartDate = $requestData['job_start_date'][$key] ?? null;
                    $jobFinishDate = $requestData['job_finish_date'][$key] ?? null;
                    $jobRelevantExp = $requestData['relevant_experience_hidden'][$key] ?? null;
                    $job_emp_name = $requestData['job_emp_name'][$key] ?? null;
                    $job_state = $requestData['job_state'][$key] ?? null;
                    $job_type = $requestData['job_type'][$key] ?? null;
                    $jobId = $requestData['job_id'][$key] ?? null;

                    // Convert job_start_date from dd/mm/yyyy to Y-m-d for database storage
                    $formatted_start_date = null;
                    if (!empty($jobStartDate)) {
                        try {
                            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $jobStartDate);
                            $formatted_start_date = $date->format('Y-m-d');
                        } catch (\Exception $e) {
                            return redirect()->back()->withErrors(['job_start_date.' . $key => 'Invalid Work Experience Start Date format: ' . $jobStartDate])->withInput();
                        }
                    }

                    // Convert job_finish_date from dd/mm/yyyy to Y-m-d for database storage
                    $formatted_finish_date = null;
                    if (!empty($jobFinishDate)) {
                        try {
                            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $jobFinishDate);
                            $formatted_finish_date = $date->format('Y-m-d');
                        } catch (\Exception $e) {
                            return redirect()->back()->withErrors(['job_finish_date.' . $key => 'Invalid Work Experience Finish Date format: ' . $jobFinishDate])->withInput();
                        }
                    }

                    if (!empty($jobTitle) || !empty($jobCode)) {
                        if ($jobId) {
                            $existingJob = ClientExperience::find($jobId);
                            if ($existingJob && $existingJob->client_id == $obj->id) {
                                $existingJob->update([
                                    'admin_id' => Auth::user()->id,
                                    'job_title' => $jobTitle,
                                    'job_code' => $jobCode,
                                    'job_country' => $jobCountry,
                                    'job_start_date' => $formatted_start_date, // Use the formatted date
                                    'job_finish_date' => $formatted_finish_date, // Use the formatted date
                                    'relevant_experience' => $jobRelevantExp,
                                    'job_emp_name' => $job_emp_name,
                                    'job_state' => $job_state,
                                    'job_type' => $job_type
                                ]);
                            }
                        } else {
                            ClientExperience::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $obj->id,
                                'job_title' => $jobTitle,
                                'job_code' => $jobCode,
                                'job_country' => $jobCountry,
                                'job_start_date' => $formatted_start_date, // Use the formatted date
                                'job_finish_date' => $formatted_finish_date, // Use the formatted date
                                'relevant_experience' => $jobRelevantExp,
                                'job_emp_name' => $job_emp_name,
                                'job_state' => $job_state,
                                'job_type' => $job_type
                            ]);
                        }
                    }
                }
            }

            if ( isset($requestData['nomi_occupation']) && is_array($requestData['nomi_occupation']))
            {
                // Delete existing occupation records for the client to start fresh
                if (ClientOccupation::where('client_id', $obj->id)->exists()) {
                    ClientOccupation::where('client_id', $obj->id)->delete();
                }

                // Debug: Log the incoming data to verify all entries are received
                \Log::info('Occupation Data Received:', [

                    'nomi_occupation' => $requestData['nomi_occupation'],
                    'occupation_code' => $requestData['occupation_code'] ?? [],
                    'list' => $requestData['list'] ?? [],
                    'occ_reference_no' => $requestData['occ_reference_no'] ?? [],
                    'dates' => $requestData['dates'] ?? [],
                    'expiry_dates' => $requestData['expiry_dates'] ?? [],
                    //'relevant_occupation_hidden' => $requestData['relevant_occupation_hidden'] ?? [],
                ]);

                // Ensure we're iterating over all entries
                $occupationCount = count($requestData['nomi_occupation']);
                for ($key = 0; $key < $occupationCount; $key++) {
                    //$skillAssessment = $requestData['skill_assessment_hidden'][$key] ?? null;
                    $nomiOccupation = $requestData['nomi_occupation'][$key] ?? null;
                    $occupationCode = $requestData['occupation_code'][$key] ?? null;
                    $list = $requestData['list'][$key] ?? null;
                    $occ_reference_no = $requestData['occ_reference_no'][$key] ?? null;
                    $date = $requestData['dates'][$key] ?? null;
                    $expiry_dates = $requestData['expiry_dates'][$key] ?? null;

                    // Convert dates from dd/mm/yyyy to Y-m-d for database storage
                    $formatted_date = null;
                    if (!empty($date)) {
                        try {
                            $dateObj = \Carbon\Carbon::createFromFormat('d/m/Y', $date);
                            $formatted_date = $dateObj->format('Y-m-d');
                        } catch (\Exception $e) {
                            return redirect()->back()->withErrors(['dates.' . $key => 'Invalid Assessment Date format: ' . $date])->withInput();
                        }
                    }

                    // Convert expiry dates from dd/mm/yyyy to Y-m-d for database storage
                    $formatted_date_expiry = null;
                    if (!empty($expiry_dates)) {
                        try {
                            $dateObj1 = \Carbon\Carbon::createFromFormat('d/m/Y', $expiry_dates);
                            $formatted_date_expiry = $dateObj1->format('Y-m-d');
                        } catch (\Exception $e) {
                            return redirect()->back()->withErrors(['expiry_dates.' . $key => 'Invalid Expiry Date format: ' . $expiry_dates])->withInput();
                        }
                    }

                    // Create a new occupation record if the required fields are present
                    if ( !empty($nomiOccupation)) {
                        ClientOccupation::create([
                            'admin_id' => Auth::user()->id,
                            'client_id' => $obj->id,

                            'nomi_occupation' => $nomiOccupation,
                            'occupation_code' => $occupationCode,
                            'list' => $list,
                            'occ_reference_no' => $occ_reference_no,
                            'dates' => $formatted_date,
                            'expiry_dates' => $formatted_date_expiry
                        ]);
                    }
                }

                // Debug: Log the number of occupations saved
                $savedOccupations = ClientOccupation::where('client_id', $obj->id)->count();
                \Log::info('Occupations Saved:', ['count' => $savedOccupations]);
            }

            // Test Score Handling
            if (isset($requestData['test_type_hidden']) && is_array($requestData['test_type_hidden'])) {
                // Delete existing test scores for the client to start fresh
                if (ClientTestScore::where('client_id', $obj->id)->exists()) {
                    ClientTestScore::where('client_id', $obj->id)->delete();
                }

                foreach ($requestData['test_type_hidden'] as $key => $testType) {
                    $listening = $requestData['listening'][$key] ?? null;
                    $reading = $requestData['reading'][$key] ?? null;
                    $writing = $requestData['writing'][$key] ?? null;
                    $speaking = $requestData['speaking'][$key] ?? null;
                    $overallScore = $requestData['overall_score'][$key] ?? null;
                    $testDate = $requestData['test_date'][$key] ?? null;
                    $testScoreId = $requestData['test_score_id'][$key] ?? null;
                    $relevant_test = isset($requestData['relevant_test_hidden'][$key]) && $requestData['relevant_test_hidden'][$key] === '1' ? 1 : 0;
                    $test_reference_no = $requestData['test_reference_no'][$key] ?? null;

                    // Convert test_date from dd/mm/yyyy to Y-m-d for database storage
                    $formatted_test_date = null;
                    if (!empty($testDate)) {
                        try {
                            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $testDate);
                            $formatted_test_date = $date->format('Y-m-d');
                        } catch (\Exception $e) {
                            return redirect()->back()->withErrors(['test_date.' . $key => 'Invalid Test Date format: ' . $testDate . '. Must be in dd/mm/yyyy format.'])->withInput();
                        }
                    }

                    // Only save if testType is provided and at least one score or date is present
                    if (!empty($testType) && (!empty($listening) || !empty($reading) || !empty($writing) || !empty($speaking) || !empty($overallScore) || !empty($formatted_test_date))) {
                        ClientTestScore::create([
                            'admin_id' => Auth::user()->id,
                            'client_id' => $obj->id,
                            'test_type' => $testType,
                            'listening' => $listening,
                            'reading' => $reading,
                            'writing' => $writing,
                            'speaking' => $speaking,
                            'test_date' => $formatted_test_date,
                            'overall_score' => $overallScore,
                            'relevant_test' => $relevant_test,
                            'test_reference_no' => $test_reference_no
                        ]);
                    }
                }
            }

            // Spouse Detail Handling
            if ($requestData['martial_status'] === 'Married') {
                // Only process spouse details if marital status is Married
                $hasEnglishScore = isset($requestData['spouse_has_english_score']) && $requestData['spouse_has_english_score'] === 'Yes';
                $hasSkillAssessment = isset($requestData['spouse_has_skill_assessment']) && $requestData['spouse_has_skill_assessment'] === 'Yes';

                // Delete existing spouse details for the client to start fresh
                if (ClientSpouseDetail::where('client_id', $obj->id)->exists()) {
                    ClientSpouseDetail::where('client_id', $obj->id)->delete();
                }

                // Initialize variables with null values
                $testType = null;
                $listeningScore = null;
                $readingScore = null;
                $writingScore = null;
                $speakingScore = null;
                $overallScore = null;
                $spouseTestDate = null;
                $skillAssessmentStatus = null;
                $nomiOccupation = null;
                $assessmentDate = null;

                // Handle English Score fields if "spouse_has_english_score" is Yes
                if ($hasEnglishScore) {
                    $testType = $requestData['spouse_test_type'] ?? null;
                    $listeningScore = $requestData['spouse_listening_score'] ?? null;
                    $readingScore = $requestData['spouse_reading_score'] ?? null;
                    $writingScore = $requestData['spouse_writing_score'] ?? null;
                    $speakingScore = $requestData['spouse_speaking_score'] ?? null;
                    $overallScore = $requestData['spouse_overall_score'] ?? null;
                    $spouseTestDate = $requestData['spouse_test_date'] ?? null;

                    // Convert spouse_test_date from dd/mm/yyyy to Y-m-d for database storage
                    if (!empty($spouseTestDate)) {
                        try {
                            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $spouseTestDate);
                            $spouseTestDate = $date->format('Y-m-d');
                        } catch (\Exception $e) {
                            return redirect()->back()->withErrors(['spouse_test_date' => 'Invalid Spouse Test Date format: ' . $spouseTestDate . '. Must be in dd/mm/yyyy format.'])->withInput();
                        }
                    }
                }

                // Handle Skill Assessment fields if "spouse_has_skill_assessment" is Yes
                if ($hasSkillAssessment) {
                    $skillAssessmentStatus = $requestData['spouse_skill_assessment_status'] ?? null;
                    $nomiOccupation = $requestData['spouse_nomi_occupation'] ?? null;
                    $assessmentDate = $requestData['spouse_assessment_date'] ?? null;

                    // Convert spouse_assessment_date from dd/mm/yyyy to Y-m-d for database storage
                    if (!empty($assessmentDate)) {
                        try {
                            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $assessmentDate);
                            $assessmentDate = $date->format('Y-m-d');
                        } catch (\Exception $e) {
                            return redirect()->back()->withErrors(['spouse_assessment_date' => 'Invalid Spouse Assessment Date format: ' . $assessmentDate . '. Must be in dd/mm/yyyy format.'])->withInput();
                        }
                    }
                }

                // Save spouse details only if at least one field is provided
                if ($hasEnglishScore || $hasSkillAssessment) {
                    ClientSpouseDetail::create([
                        'admin_id' => Auth::user()->id,
                        'client_id' => $obj->id,
                        'spouse_has_english_score' => $requestData['spouse_has_english_score'] ?? 'No',
                        'spouse_test_type' => $testType,
                        'spouse_listening_score' => $listeningScore,
                        'spouse_reading_score' => $readingScore,
                        'spouse_writing_score' => $writingScore,
                        'spouse_speaking_score' => $speakingScore,
                        'spouse_overall_score' => $overallScore,
                        'spouse_test_date' => $spouseTestDate,
                        'spouse_has_skill_assessment' => $requestData['spouse_has_skill_assessment'] ?? 'No',
                        'spouse_skill_assessment_status' => $skillAssessmentStatus,
                        'spouse_nomi_occupation' => $nomiOccupation,
                        'spouse_assessment_date' => $assessmentDate,
                    ]);
                }
            } else {
                // If marital status is not Married, delete any existing spouse details
                if (ClientSpouseDetail::where('client_id', $obj->id)->exists()) {
                    ClientSpouseDetail::where('client_id', $obj->id)->delete();
                }
            }

            // Update Partner Handling to include all family member types
            $familyTypes = [
                'partner' => ['Husband', 'Wife', 'Ex-Wife', 'Defacto'],
                'children' => ['Son', 'Daughter', 'Step Son', 'Step Daughter'],
                'parent' => ['Father', 'Mother', 'Step Father', 'Step Mother'],
                'siblings' => ['Brother', 'Sister', 'Step Brother', 'Step Sister'],
                'others' => ['Cousin', 'Friend', 'Uncle', 'Aunt', 'Grandchild', 'Granddaughter', 'Grandparent', 'Niece', 'Nephew', 'Grandfather'],
            ];

            // Function to get reciprocal relationship based on gender
            $getReciprocalRelationship = function($relationshipType, $currentGender, $relatedGender) {
                switch ($relationshipType) {
                    // Partner relationships
                    case 'Husband':
                        return 'Wife';
                    case 'Wife':
                        return 'Husband';
                    case 'Ex-Wife':
                        return 'Ex-Husband';
                    case 'Defacto':
                        return 'Defacto';
                    
                    // Parent-Child relationships
                    case 'Son':
                        return $relatedGender === 'Female' ? 'Mother' : 'Father';
                    case 'Daughter':
                        return $relatedGender === 'Female' ? 'Mother' : 'Father';
                    case 'Step Son':
                        return $relatedGender === 'Female' ? 'Step Mother' : 'Step Father';
                    case 'Step Daughter':
                        return $relatedGender === 'Female' ? 'Step Mother' : 'Step Father';
                    case 'Father':
                        return $relatedGender === 'Female' ? 'Daughter' : 'Son';
                    case 'Mother':
                        return $relatedGender === 'Female' ? 'Daughter' : 'Son';
                    case 'Step Father':
                        return $relatedGender === 'Female' ? 'Step Daughter' : 'Step Son';
                    case 'Step Mother':
                        return $relatedGender === 'Female' ? 'Step Daughter' : 'Step Son';
                    
                    // Sibling relationships
                    case 'Brother':
                        return $relatedGender === 'Female' ? 'Sister' : 'Brother';
                    case 'Sister':
                        return $relatedGender === 'Female' ? 'Sister' : 'Brother';
                    case 'Step Brother':
                        return $relatedGender === 'Female' ? 'Step Sister' : 'Step Brother';
                    case 'Step Sister':
                        return $relatedGender === 'Female' ? 'Step Sister' : 'Step Brother';
                    
                    // Other relationships
                    case 'Cousin':
                        return 'Cousin';
                    case 'Friend':
                        return 'Friend';
                    case 'Uncle':
                        return $relatedGender === 'Female' ? 'Niece' : 'Nephew';
                    case 'Aunt':
                        return $relatedGender === 'Female' ? 'Niece' : 'Nephew';
                    case 'Grandchild':
                        return $relatedGender === 'Female' ? 'Grandmother' : 'Grandfather';
                    case 'Granddaughter':
                        return $relatedGender === 'Female' ? 'Grandmother' : 'Grandfather';
                    case 'Grandparent':
                        return $relatedGender === 'Female' ? 'Granddaughter' : 'Grandson';
                    case 'Grandfather':
                        return $relatedGender === 'Female' ? 'Granddaughter' : 'Grandson';
                    case 'Grandmother':
                        return $relatedGender === 'Female' ? 'Granddaughter' : 'Grandson';
                    case 'Niece':
                        return $relatedGender === 'Female' ? 'Aunt' : 'Uncle';
                    case 'Nephew':
                        return $relatedGender === 'Female' ? 'Aunt' : 'Uncle';
                    
                    default:
                        return $relationshipType; // Fallback to same relationship type
                }
            };

            //First remove all record and then add
            $exists = ClientRelationship::where('client_id', $obj->id)->exists();
            if ($exists) {
                ClientRelationship::where('client_id', $obj->id)->delete();
            }
            $exists1 = ClientRelationship::where('related_client_id', $obj->id)->exists();
            if ($exists1) {
                ClientRelationship::where('related_client_id', $obj->id)->delete();
            }

            foreach ($familyTypes as $type => $relationships)
            {
                // Handle Deletion
                if (isset($requestData["delete_{$type}_ids"]) && is_array($requestData["delete_{$type}_ids"])) {
                    foreach ($requestData["delete_{$type}_ids"] as $partnerId) {
                        $partner = ClientRelationship::find($partnerId);
                        if ($partner && $partner->client_id == $obj->id) {
                            // Delete reciprocal relationship if exists
                            if ($partner->related_client_id) {
                                ClientRelationship::where('client_id', $partner->related_client_id)
                                    ->where('related_client_id', $obj->id)
                                    ->delete();
                            }
                            $partner->delete();
                        }
                    }
                }

                // Handle Creation/Update
                if (
                    (isset($requestData["{$type}_details"]) && is_array($requestData["{$type}_details"])) ||
                    (isset($requestData["{$type}_relationship_type"]) && is_array($requestData["{$type}_relationship_type"]))
                ) {
                    foreach ($requestData["{$type}_details"] as $key => $details) {
                        $relationshipType = $requestData["{$type}_relationship_type"][$key] ?? null;
                        $companyType = $requestData["{$type}_company_type"][$key] ?? null;
                        $partnerId = $requestData["{$type}_id"][$key] ?? null;
                        $email = $requestData["{$type}_email"][$key] ?? null;
                        $firstName = $requestData["{$type}_first_name"][$key] ?? null;
                        $lastName = $requestData["{$type}_last_name"][$key] ?? null;
                        $phone = $requestData["{$type}_phone"][$key] ?? null;
                        $gender = $requestData["{$type}_gender"][$key] ?? null;
                        
                        //$dob = $requestData["{$type}_dob"][$key] ?? null;
                        if (!empty($requestData["{$type}_dob"][$key])) {
                            try {
                                $dobDate = \Carbon\Carbon::createFromFormat('d/m/Y', $requestData["{$type}_dob"][$key]);
                                $dobFormated = $dobDate->format('Y-m-d'); // Convert to Y-m-d for storage
                            } catch (\Exception $e) {
                                return redirect()->back()->withErrors(['dob' => 'Invalid Date of Birth format: ' . $dob . '. Must be in dd/mm/yyyy format.'])->withInput();
                            }
                        } else {
                            $dobFormated = null;
                        }

                        // Skip if neither details nor relationship type is provided
                        if (empty($details) && empty($relationshipType)) {
                            continue;
                        }

                        $relatedClientId = $partnerId && is_numeric($partnerId) ? $partnerId : null;
                        $saveExtraFields = !$relatedClientId;

                        // Prepare partner data
                        $partnerData = [
                            'admin_id' => Auth::user()->id,
                            'client_id' => $obj->id,
                            'related_client_id' => $relatedClientId,
                            'details' => $relatedClientId ? $details : null,
                            'relationship_type' => $relationshipType,
                            'company_type' => $companyType,
                            'email' => $saveExtraFields ? $email : null,
                            'first_name' => $saveExtraFields ? $firstName : null,
                            'last_name' => $saveExtraFields ? $lastName : null,
                            'phone' => $saveExtraFields ? $phone : null,
                            'gender' => $gender, // Always save gender since it's in main section
                            'dob' => $saveExtraFields ? $dobFormated : null,
                        ]; //dd($partnerData);

                        // Create new partner
                        $newPartner = ClientRelationship::create($partnerData);

                        // Create reciprocal relationship if related_client_id is set
                        if ($relatedClientId && $relationshipType) {
                            $relatedClient = Admin::find($relatedClientId);
                            if ($relatedClient) {
                                // Get the gender of the related client (current user)
                                $relatedClientGender = $obj->gender ?? null;
                                // Get the gender of the family member being added
                                $familyMemberGender = $gender ?? null;
                                
                                // Get reciprocal relationship based on gender
                                $reciprocalRelationship = $getReciprocalRelationship($relationshipType, $familyMemberGender, $relatedClientGender);
                                
                                ClientRelationship::create([
                                    'admin_id' => Auth::user()->id,
                                    'client_id' => $relatedClientId,
                                    'related_client_id' => $obj->id,
                                    'details' => "{$obj->first_name} {$obj->last_name} ({$obj->email}, {$obj->phone}, {$obj->client_id})",
                                    'relationship_type' => $reciprocalRelationship,
                                    'company_type' => $companyType,
                                    'email' => null,
                                    'first_name' => null,
                                    'last_name' => null,
                                    'phone' => null,
                                    'gender' => $relatedClientGender,
                                    'dob' => null
                                ]);
                            }
                        }
                    } //End foreach inner
                }
            } //End foreach

			// Handle EOI Reference Deletion
			if (isset($requestData['delete_eoi_ids']) && is_array($requestData['delete_eoi_ids'])) {
				foreach ($requestData['delete_eoi_ids'] as $eoiId) {
					$eoi = ClientEoiReference::find($eoiId);
					if ($eoi && $eoi->client_id == $obj->id) {
						$eoi->delete();
					}
				}
			}

			// EOI Reference Handling
			if (isset($requestData['EOI_number']) && is_array($requestData['EOI_number'])) {
				foreach ($requestData['EOI_number'] as $key => $eoiNumber) {
					$EOI_subclass = $requestData['EOI_subclass'][$key] ?? null;
					$EOI_occupation = $requestData['EOI_occupation'][$key] ?? null;
					$EOI_point = $requestData['EOI_point'][$key] ?? null;
					$EOI_state = $requestData['EOI_state'][$key] ?? null;
                    $EOI_submission_date = $requestData['EOI_submission_date'][$key] ?? null;
					$EOI_ROI = $requestData['EOI_ROI'][$key] ?? null;
					$EOI_password = $requestData['EOI_password'][$key] ?? null;
					$eoiId = $requestData['eoi_id'][$key] ?? null;

					// Convert submission date from dd/mm/yyyy to Y-m-d for database storage
					$formatted_submission_date = null;
					if (!empty($EOI_submission_date)) {
						try {
							$date = \Carbon\Carbon::createFromFormat('d/m/Y', $EOI_submission_date);
							$formatted_submission_date = $date->format('Y-m-d');
						} catch (\Exception $e) {
							return redirect()->back()->withErrors(['EOI_submission_date.' . $key => 'Invalid Submission Date format: ' . $EOI_submission_date . '. Must be in dd/mm/yyyy format.'])->withInput();
						}
					}

					//if (!empty($eoiNumber) || !empty($EOI_subclass) || !empty($EOI_occupation) || !empty($EOI_point) || !empty($EOI_state)) {
						if ($eoiId) {
							$existingEoi = ClientEoiReference::find($eoiId);
							if ($existingEoi && $existingEoi->client_id == $obj->id) {
								$existingEoi->update([
									'admin_id' => Auth::user()->id,
									'EOI_number' => $eoiNumber,
									'EOI_subclass' => $EOI_subclass,
									'EOI_occupation' => $EOI_occupation,
									'EOI_point' => $EOI_point,
									'EOI_state' => $EOI_state,
                                    'EOI_submission_date' =>  $formatted_submission_date,
									'EOI_ROI' => $EOI_ROI,
									'EOI_password' => $EOI_password
								]);
							}
						} else {
							ClientEoiReference::create([
								'admin_id' => Auth::user()->id,
								'client_id' => $obj->id,
								'EOI_number' => $eoiNumber,
								'EOI_subclass' => $EOI_subclass,
								'EOI_occupation' => $EOI_occupation,
								'EOI_point' => $EOI_point,
								'EOI_state' => $EOI_state,
                                'EOI_submission_date' =>  $formatted_submission_date,
								'EOI_ROI' => $EOI_ROI,
								'EOI_password' => $EOI_password
							]);
						}
					//}
				}
			}

			$saved = $obj->save();
            if (!$saved) {
                return redirect()->back()->with('error', Config::get('constants.server_error'));
            }

            // Update service taken (unchanged)
            if (DB::table('client_service_takens')->where('client_id', $requestData['id'])->exists()) {
                DB::table('client_service_takens')->where('client_id', $requestData['id'])->update(['is_saved_db' => 1]);
            }

            //simiar related files
            if(isset($requestData['related_files']))
            {

                //Code for addition of simiar related files in added users account
                for($j=0; $j<count($requestData['related_files']); $j++)
                {
                    if(Admin::where('id', '=', $requestData['related_files'][$j])->exists())
                    {
                        $objsY = Admin::select('id', 'related_files')->where('id', $requestData['related_files'][$j])->get();
                        if(!empty($objsY)){
                            if($objsY[0]->related_files != ""){
                                $related_files_string = $objsY[0]->related_files;
                                $commaPosition = strpos($related_files_string, ',');
                                if ($commaPosition !== false) { //If comma is exist
                                    $related_files_string_Arr = explode(",",$related_files_string);
                                    array_push($related_files_string_Arr, $requestData['id']);
                                    // Remove duplicate elements
                                    $uniqueArray = array_unique($related_files_string_Arr);

                                    // Reindex the array
                                    $uniqueArray = array_values($uniqueArray);

                                    $related_files_latest = implode(",",$uniqueArray);
                                } else { //If comma is not exist
                                    $related_files_string_Arr = array($objsY[0]->related_files);
                                    array_push($related_files_string_Arr, $requestData['id']);

                                        // Remove duplicate elements
                                        $uniqueArray = array_unique($related_files_string_Arr);

                                        // Reindex the array
                                        $uniqueArray = array_values($uniqueArray);

                                    $related_files_latest = implode(",",$uniqueArray);
                                }
                            } else {
                                $related_files_latest = $requestData['id'];
                            }
                            Admin::where('id', $requestData['related_files'][$j])->update(['related_files' => $related_files_latest]);
                        }
                    }
                } //end foreach
            }

            if( isset($requestData['related_files'])  || !isset($requestData['related_files']) )
            {
                //Code for removal of simiar related files in added users account
                if( isset($requestData['related_files']) ) {
                    $req_arr11 = $requestData['related_files'];
                } else {
                    $req_arr11 = array();
                }

                if( !empty($db_arr)  ){

                    $commaPosition11 = strpos($db_arr[0]->related_files, ',');
                    if ($commaPosition11 !== false) { //If comma is exist
                        $db_arr11 = explode(",",$db_arr[0]->related_files);
                    } else { //If comma is not exist
                        $db_arr11 = array($db_arr[0]->related_files);
                    }

                    //echo "<pre>db_arr11=";print_r($db_arr11);
                    //echo "<pre>req_arr11=";print_r($req_arr11);
                    $diff_arr = array_diff( $db_arr11,$req_arr11 );
                    //echo "<pre>diff_arr=";print_r($diff_arr);
                    $diff_arr = array_values($diff_arr);
                    //echo "<pre>diff_arr=";print_r($diff_arr);die;
                }

                    if( isset($diff_arr) && !empty($diff_arr))
                    {
                        for($k=0; $k<count($diff_arr); $k++)
                        {
                            if(Admin::where('id', '=', $diff_arr[$k])->exists())
                            {
                                $rel_data_arr = Admin::select('related_files')->where('id', $diff_arr[$k])->get();
                                if( !empty($rel_data_arr) ){
                                    $commaPosition1 = strpos($rel_data_arr[0]->related_files, ',');
                                    if ($commaPosition1 !== false) { //If comma is exist
                                        $rel_data_exploded_arr = explode(",",$rel_data_arr[0]->related_files);
                                        $key_search = array_search($requestData['id'], $rel_data_exploded_arr);
                                        if ($key_search !== false) {
                                            unset($rel_data_exploded_arr[$key_search]);
                                        }
                                        $rel_data_exploded_arr = array_values($rel_data_exploded_arr);
                                        //print_r($rel_data_exploded_arr);
                                        $related_files_updated = implode(",",$rel_data_exploded_arr);

                                        Admin::where('id', $diff_arr[$k])->update(['related_files' => $related_files_updated]);

                                    } else { //If comma is not exist
                                        if ($rel_data_arr[0]->related_files == $requestData['id']) {
                                            $related_files_updated = "";
                                            Admin::where('id', $diff_arr[$k])->update(['related_files' => $related_files_updated]);
                                        }
                                    }
                            }
                        }
                    }
                }

            }

            $clientId = $requestData['id'];
            $encodedId = base64_encode(convert_uuencode($clientId));

            $latestMatter = DB::table('client_matters')
                ->where('client_id', $clientId)
                ->where('matter_status', 1)
                ->orderByDesc('id')
                ->first();

            $redirectUrl = $latestMatter
                ? '/admin/clients/detail/'.$encodedId.'/'.$latestMatter->client_unique_matter_no
                : '/admin/clients/detail/'.$encodedId;

            return Redirect::to($redirectUrl)->with('success',  ($requestData['type'] ?? 'Client') . ' edited successfully');
            //return Redirect::to('/admin/clients/detail/' . base64_encode(convert_uuencode($requestData['id'])))->with('success', ($requestData['type'] ?? 'Client') . ' edited successfully');
        }
        else {
            if (isset($id) && !empty($id)) {
                $id = $this->decodeString($id);
                if (Admin::where('id', '=', $id)->where('role', '=', '7')->exists()) {
                    $fetchedData = Admin::find($id);
                    $clientContacts = ClientContact::where('client_id', $id)->get() ?? [];
                    $emails = ClientEmail::where('client_id', $id)->get() ?? [];
                    $visaCountries = ClientVisaCountry::where('client_id', $id)->get() ?? [];
                    $clientAddresses = ClientAddress::where('client_id', $id)->get() ?? [];
                    $qualifications = ClientQualification::where('client_id', $id)->get() ?? [];
                    $experiences = ClientExperience::where('client_id', $id)->get() ?? [];
                    $clientOccupations = ClientOccupation::where('client_id', $id)->get() ?? [];
                    $testScores = ClientTestScore::where('client_id', $id)->get() ?? [];
                    $ClientSpouseDetail = ClientSpouseDetail::where('client_id', $id)->first() ?? [];
                    $clientPassports = ClientPassportInformation::where('client_id', $id)->get() ?? [];
                    $clientTravels = ClientTravelInformation::where('client_id', $id)->get() ?? [];
                    $clientCharacters = ClientCharacter::where('client_id', $id)->get() ?? [];

                    $clientPartners = ClientRelationship::where('client_id', $id)->get() ?? [];
                    //dd($clientPartners);
					$clientEoiReferences = ClientEoiReference::where('client_id', $id)->get() ?? [];

                    return view('Admin.clients.edit', compact('fetchedData', 'clientContacts', 'emails', 'visaCountries', 'clientAddresses', 'qualifications', 'experiences', 'clientOccupations', 'testScores', 'ClientSpouseDetail', 'clientPassports', 'clientTravels','clientCharacters','clientPartners','clientEoiReferences'));
                } else {
                    return Redirect::to('/admin/clients')->with('error', 'Client does not exist.');
                }
            } else {
                return Redirect::to('/admin/clients')->with('error', Config::get('constants.unauthorized'));
            }
        }
    }

    public function getVisaTypes()
    {
        $visaTypes = \App\Models\Matter::select('id', 'title', 'nick_name')
            ->where('title', 'not like', '%skill assessment%')
            ->where('status', 1)
            ->orderBy('title', 'ASC')
            ->get();

        return response()->json($visaTypes);
    }

    public function getCountries()
    {
        $countries = \App\Models\Country::all()->pluck('name')->toArray();

        // Ensure "India" and "Australia" are at the top of the list
        $priorityCountries = ['Australia','India'];
        $otherCountries = array_diff($countries, $priorityCountries);
        $sortedCountries = array_merge($priorityCountries, $otherCountries);

        return response()->json($sortedCountries);
    }

    public function clientdetailsinfo(Request $request, $id = NULL)
	{
        //check authorization end
        if ($request->isMethod('post'))
		{
			$requestData = $request->all(); //dd($requestData);
			$this->validate($request, [
                'first_name' => 'required|max:255',
                'email' => 'required|max:255|unique:admins,email,'.$requestData['id'],
                'phone' => 'required|max:255|unique:admins,phone,'.$requestData['id'],
                'client_id' => 'required|max:255|unique:admins,client_id,'.$requestData['id']
            ]);

            $related_files = '';
	        if(isset($requestData['related_files'])){
	            for($i=0; $i<count($requestData['related_files']); $i++){
	                $related_files .= $requestData['related_files'][$i].',';
	            }
            }

	        $dob = '';
	        if(array_key_exists("dob",$requestData) && $requestData['dob'] != ''){
	           $dobs = explode('/', $requestData['dob']);
	           $dob = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
	        }

	        $visaExpiry = '';
	        if(array_key_exists("visaExpiry",$requestData) && $requestData['visaExpiry'] != '' ){
	           $visaExpirys = explode('/', $requestData['visaExpiry']);
	            $visaExpiry = $visaExpirys[2].'-'.$visaExpirys[1].'-'. $visaExpirys[0];
	        }
			$obj = 	Admin::find(@$requestData['id']);
			$first_name = substr(@$requestData['first_name'], 0, 4);

			$obj->first_name	=	@$requestData['first_name'];
			$obj->last_name	=	@$requestData['last_name'];
            $obj->dob	=	@$dob;
			$obj->age	=	@$requestData['age'];
			$obj->gender	=	@$requestData['gender'];
			$obj->martial_status	=	@$requestData['martial_status'];

            $naatiTest = isset($requestData['naati_test']) && $requestData['naati_test'] === '1' ? 1 : 0;
            $obj->naati_test = $naatiTest;
            $obj->naati_date = $naatiTest ? ($requestData['naati_date'] ?? null) : null;

            $pyTest = isset($requestData['py_test']) && $requestData['py_test'] === '1' ? 1 : 0;
            $obj->py_test = $pyTest;
            $obj->py_date = $pyTest ? ($requestData['py_date'] ?? null) : null;
            $obj->related_files	=	rtrim($related_files,',');
		    $obj->save(); //Finally, save the object

            //Contact Type Start Code
            if(
                ( isset($requestData['contact_type_hidden']) && is_array($requestData['contact_type_hidden']) )
				&&
				( isset($requestData['phone']) && is_array($requestData['phone']) )
            )
            {
                // Get the count of the email array
				$count_contact = count($requestData['contact_type_hidden']);
				// Save the last values for email_type_hidden and email to the Admin object
				if ($count_contact > 0 ) {
                    // Get the last values for contact_type and phone
                    $lastContactType = end($requestData['contact_type_hidden']);
                    $lastPhone = end($requestData['phone']);
                    $lastcountry_code =  end($requestData['country_code']);

                    if($lastPhone != ""){
                        $lastPhone = $lastPhone;
                        $lastContactType = $lastContactType;
                        $lastcountry_code = $lastcountry_code;
                    } else {
                        if($count_contact >1){
                            $lastPhone = $requestData['phone'][$count_contact-2];
                            $lastContactType = $requestData['contact_type_hidden'][$count_contact-2];
                            $lastcountry_code = $requestData['country_code'][$count_contact-2];
                        } else {
                            $lastPhone = $requestData['phone'][0];
                            $lastContactType = $requestData['contact_type_hidden'][0];
                            $lastcountry_code = $requestData['country_code'][0];
                        }
                    }
                    $obj->contact_type = $lastContactType;
                    $obj->phone = $lastPhone;
                    $obj->country_code = $lastcountry_code;
                    $obj->save(); // Save the admin object with the last phone number
                }

                // Loop through each contact in the request
                foreach ($requestData['contact_type_hidden'] as $key => $contactType) {
                    $contactId = $requestData['contact_id'][$key] ?? null;
                    $phone = $requestData['phone'][$key] ?? null;
                    $country_code = $requestData['country_code'][$key] ?? null;
                    // Check if both contact_type and phone are not empty
                    if (!empty($contactType) && !empty($phone)) {
                        if ($contactId) {
                            // Update existing contact if ID is provided
                            $existingContact = ClientContact::find($contactId);
                            //if ($existingContact && $existingContact->admin_id == Auth::user()->id) {
                            if ($existingContact) {
                                $existingContact->update([
                                    'admin_id' => Auth::user()->id,
									'contact_type' => $contactType,
									'phone' => $phone,
									'country_code' => $country_code
                                ]);
                            }
                        } else {
                            // Insert new contact if no ID is provided
                            ClientContact::create([
                                'admin_id' => Auth::user()->id, // Assigning Auth user ID to admin_id
                                'client_id' => $obj->id,
                                'contact_type' => $contactType,
                                'phone' => $phone,
                                'country_code' => $country_code
                            ]);
                        }
                    }
                }
            }
            //Contact Type End Code

            //Email Type Start Code
            if (
				( isset($requestData['email_type_hidden']) && is_array($requestData['email_type_hidden']) )
				&&
				( isset($requestData['email']) && is_array($requestData['email']) )
			)
			{
				// Get the count of the email array
				$count_email_type = count($requestData['email_type_hidden']);
				// Save the last values for email_type_hidden and email to the Admin object
				if ($count_email_type > 0 ) {
					$lastEmailType = end($requestData['email_type_hidden']);
                    $lastEmail = end($requestData['email']);
                    if($lastEmail != ""){
                        $lastEmail = $lastEmail;
                        $lastEmailType = $lastEmailType;
                    } else {
                        if($count_email_type >1){
                            $lastEmail = $requestData['email'][$count_email_type-2];
                            $lastEmailType = $requestData['email_type_hidden'][$count_email_type-2];
                        } else {
                            $lastEmail = $requestData['email'][0];
                            $lastEmailType = $requestData['email_type_hidden'][0];
                        }
                    }
                    $obj->email_type = $lastEmailType;
                    $obj->email = $lastEmail;
                    $obj->save();
                }

				// Loop through each email in the request
				foreach ($requestData['email_type_hidden'] as $key => $emailType) {
					$email = $requestData['email'][$key] ?? null;
					$emailId = $requestData['email_id'][$key] ?? null;

					// Check if the current row is not blank
					if (!empty($emailType) && !empty($email)) {

                        // Check if the email already exists in the current client's email list
						$duplicateEmail = ClientEmail::where('email', $email)
						->where('client_id', $obj->id)
						->where('id', '!=', $emailId)
						->first();

						if ($duplicateEmail) {
							// If duplicate found, add error message to the session
							return response()->json([
								'status' => 'error',
								'message' => 'This email is already taken: ' . $email
							], 422); // Unprocessable Entity
						}

						if ($emailId) {
							// Update existing email if ID is provided
							$existingEmail = ClientEmail::find($emailId);
							if ($existingEmail && $existingEmail->client_id == $obj->id) {
								$existingEmail->update([
									'email_type' => $emailType,
									'email' => $email,
									'admin_id' => Auth::user()->id
								]);
							}
						} else {
							// Insert new email if no ID is provided
							ClientEmail::create([
								'admin_id' => Auth::user()->id,
								'client_id' => $obj->id, // Assigning the correct client ID
								'email_type' => $emailType,
								'email' => $email
							]);
						}
					}
				}
			}
            //Email Type End Code

            //Visa Country Start Code
            if (
                ( isset($requestData['visa_country']) && is_array($requestData['visa_country']) )
                ||
                ( isset($requestData['visa_type_hidden']) && is_array($requestData['visa_type_hidden']) )
            )
            {
                if( isset($requestData['visa_country']) &&  $requestData['visa_country'][0] == 'Australia')
                {

                    if (ClientVisaCountry::where('client_id', $obj->id)->exists()) {
                        if ( ClientVisaCountry::where('client_id', $obj->id)->delete() ) {
                            ClientVisaCountry::create([
                                'admin_id' => Auth::user()->id, // Assigning Auth user ID to admin_id
                                'client_id' => $obj->id,
                                'visa_country' => $requestData['visa_country'][0],
                                'visa_type' => "",
                                'visa_expiry_date' => "",
                                'visa_description' => ""
                            ]);

                            $obj->visa_type = "";
							$obj->country_passport = $requestData['visa_country'][0];
							$obj->visaExpiry = "";
							$obj->save();
                        }
                    }
                }
                else
                {
                    //If Visa Country is not Australia
                    if (ClientVisaCountry::where('client_id', $obj->id)->exists()) {
                        if ( ClientVisaCountry::where('client_id', $obj->id)->delete() ) {

                            foreach ($requestData['visa_type_hidden'] as $key => $visaType) {
                                $visa_country = $requestData['visa_country'][0] ?? null;
                                $visa_expiry_date = $requestData['visa_expiry_date'][$key] ?? null;
                                $visa_description = $requestData['visa_description'][$key] ?? null;
                                $visaId = $requestData['visa_id'][$key] ?? null;
                                // Check if the current row is not blank
                                if (!empty($visaType) || !empty($visa_country)) {
                                    ClientVisaCountry::create([
                                        'admin_id' => Auth::user()->id, // Assigning Auth user ID to admin_id
                                        'client_id' => $obj->id,
                                        'visa_country' => $visa_country,
                                        'visa_type' => $visaType,
                                        'visa_expiry_date' => $visa_expiry_date,
                                        'visa_description' => $visa_description
                                    ]);
                                }
                            }
                            $count_visa = count($requestData['visa_type_hidden']);
                            // Save the last values for visa_type, visa_country, and visa_expiry_date to the Admin object
                            if ($count_visa > 0 ) {
                                $lastVisaCountry = $requestData['visa_country'][0];
                                $lastVisaType = end($requestData['visa_type_hidden']);
                                $lastVisaExpiryDate = end($requestData['visa_expiry_date']);
                                // Check if the last visa details are not empty before assigning
                                if (!empty($lastVisaType)  &&  !empty($lastVisaCountry)) {
                                    $obj->visa_type = $lastVisaType;
                                    $obj->country_passport = $lastVisaCountry;
                                    $obj->visaExpiry = $lastVisaExpiryDate;
                                    $obj->save();
                                }
                            }
                        }
                    }
                }
            }

            //Address Start Code
            if (
				( isset($requestData['zip']) && is_array($requestData['zip']) )
				||
				( isset($requestData['address']) && is_array($requestData['address']) )
			)
            {
                // Get the count of the address array
				$count = count($requestData['zip']);
				// Save the last values for address, city, state, and zip code to the Admin object
				if ($count > 0 ) {
					$secondLastAddress = $requestData['address'][$count - 1];
					$secondLastZip = $requestData['zip'][$count - 1];

					// Check if the last address details are not empty before assigning
					if (!empty($secondLastAddress)  || !empty($secondLastZip)) {
						$obj->address = $secondLastAddress;
						$obj->zip = $secondLastZip;
						$obj->save();
					}
				}

                // Loop through each address in the request
                foreach ($requestData['address'] as $key => $addr) {
                    $zip = $requestData['zip'][$key] ?? null;
                    $addressId = $requestData['address_id'][$key] ?? null;
                    $regional_code = $requestData['regional_code'][$key] ?? null;

                    // Check if the current row is not blank
                    if (!empty($addr) || !empty($zip)) {
                        if ($addressId) {
                            // Update existing address if ID is provided
                            $existingAddress = ClientAddress::find($addressId);
                            if ($existingAddress && $existingAddress->client_id == $obj->id) {
                                $existingAddress->update([
                                    'admin_id' => Auth::user()->id,
                                    'address' => $addr,
                                    'zip' => $zip,
                                    'regional_code' => $regional_code
                                ]);
                            }
                        } else {
                            // Insert new address if no ID is provided
                            ClientAddress::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $obj->id,
                                'address' => $addr,
                                'zip' => $zip,
                                'regional_code' => $regional_code
                            ]);
                        }
                    }
                }
            }
            //Address End Code

            //Client Qualification Start Code
            if (
                ( isset($requestData['level_hidden']) && is_array($requestData['level_hidden']) )
                ||
                ( isset($requestData['name']) && is_array($requestData['name']) )
            )
            {
                // Get the count of qualification entries
				$qualificationCount = count($requestData['level_hidden']);

				// Ensure that there are at least two qualification entries to get the last one
				if ($qualificationCount > 0) {
					// Get the second last values for level and name
					$secondLastLevel = $requestData['level_hidden'][$qualificationCount - 1];
					$secondLastName = $requestData['name'][$qualificationCount - 1];

					// Save the second last qualification details to the Admin object if not empty
					if (!empty($secondLastLevel) || !empty($secondLastName)) {
						$obj->qualification_level = $secondLastLevel;
						$obj->qualification_name = $secondLastName;
						$obj->save(); // Save the admin object with the second last qualification details
					}
				}

                // Loop through each qualification in the request
                foreach ($requestData['level_hidden'] as $key => $level)
                {
                    $name = $requestData['name'][$key] ?? null;
                    $country = $requestData['country_hidden'][$key] ?? null;
                    $short = $requestData['start_date'][$key] ?? null;
                    $finish = $requestData['finish_date'][$key] ?? null;
                    $qualificationId = $requestData['qualification_id'][$key] ?? null;
                    $relevant_qualification = $requestData['relevant_qualification_hidden'][$key] ?? null;

                    // Check if the current row is not blank
                    if (!empty($level) || !empty($name) ) {
                        if ($qualificationId) {
                            // Update existing qualification if ID is provided
                            $existingQualification = ClientQualification::find($qualificationId);
                            if ($existingQualification && $existingQualification->client_id == $obj->id) {
                                $existingQualification->update([
                                    'admin_id' => Auth::user()->id,
                                    'level' => $level,
                                    'name' => $name,
                                    'country' => $country,
                                    'start_date' => $short,
                                    'finish_date' => $finish,
                                    'relevant_qualification' => $relevant_qualification
                                ]);
                            }
                        } else {
                            // Insert new qualification if no ID is provided
                            ClientQualification::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $obj->id, // Assigning the correct client ID
                                'level' => $level,
                                'name' => $name,
                                'country' => $country,
                                'start_date' => $short,
                                'finish_date' => $finish,
                                'relevant_qualification' => $relevant_qualification
                            ]);
                        }
                    }
                }
            }
            //Client Qualification End Code

            //Client Experience Start Code
            if (
				( isset($requestData['job_title']) && is_array($requestData['job_title']) )
				||
				( isset($requestData['job_code']) && is_array($requestData['job_code']) )
			)
            {
                // Loop through each job in the request
                foreach ($requestData['job_title'] as $key => $jobTitle) {
                    $jobCode = $requestData['job_code'][$key] ?? null;
                    $jobCountry = $requestData['job_country_hidden'][$key] ?? null;
                    $jobStartDate = $requestData['job_start_date'][$key] ?? null;
                    $jobFinishDate = $requestData['job_finish_date'][$key] ?? null;
                    $jobRelevantExp = $requestData['relevant_experience_hidden'][$key] ?? null;
                    $jobId = $requestData['job_id'][$key] ?? null;

                    // Check if the current row is not blank
                    //if (!empty($jobTitle) && !empty($jobCode) && !empty($jobCountry)) {
                    if (!empty($jobTitle) || !empty($jobCode) ) {
                        if ($jobId) {
                            // Update existing job if ID is provided
                            $existingJob = ClientExperience::find($jobId);
                            if ($existingJob && $existingJob->client_id == $obj->id) {
                                $existingJob->update([
                                    'admin_id' => Auth::user()->id,
                                    'job_title' => $jobTitle,
                                    'job_code' => $jobCode,
                                    'job_country' => $jobCountry,
                                    'job_start_date' => $jobStartDate,
                                    'job_finish_date' => $jobFinishDate,
                                    'relevant_experience' =>$jobRelevantExp
                                ]);
                            }
                        } else {
                            // Insert new job if no duplicate exists
                            ClientExperience::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $obj->id, // Assigning the correct client ID
                                'job_title' => $jobTitle,
                                'job_code' => $jobCode,
                                'job_country' => $jobCountry,
                                'job_start_date' => $jobStartDate,
                                'job_finish_date' => $jobFinishDate,
                                'relevant_experience' =>$jobRelevantExp
                            ]);
                        }
                    }
                }
            }
            //Client Experience End Code

            //Client Occupation Start Code
            if (
				( isset($requestData['skill_assessment_hidden']) && is_array($requestData['skill_assessment_hidden']) )
				||
				( isset($requestData['nomi_occupation']) && is_array($requestData['nomi_occupation']) )
				)
            {

                // Loop through each set of data
                foreach ($requestData['skill_assessment_hidden'] as $key => $skillAssessment) {
                    $nomiOccupation = $requestData['nomi_occupation'][$key] ?? null;
                    $occupationCode = $requestData['occupation_code'][$key] ?? null;
                    $list = $requestData['list'][$key] ?? null;
                    $visaSubclass = $requestData['visa_subclass'][$key] ?? null;
                    $date = $requestData['dates'][$key] ?? null;
                    $occupationId = $requestData['occupation_id'][$key] ?? null; // Assuming you have IDs for updating
                    $relevant_occupation = $requestData['relevant_occupation_hidden'][$key] ?? null;
                    // Check if both skill_assessment and nomi_occupation are not empty
                    if (!empty($skillAssessment) || !empty($nomiOccupation))
                    {
                        if ($occupationId)
                        {
                            // Update existing record if ID is provided
                            $existingOccupation = ClientOccupation::find($occupationId);
                            if ($existingOccupation ) {
                                $existingOccupation->update([
                                    'admin_id' => Auth::user()->id,
                                    'skill_assessment' => $skillAssessment,
                                    'nomi_occupation' => $nomiOccupation,
                                    'occupation_code' => $occupationCode,
                                    'list' => $list,
                                    'visa_subclass' => $visaSubclass,
                                    'dates' => $date,
                                    'relevant_occupation' => $relevant_occupation
                                ]);
                            }
                        }
                        else
                        {
                            // Insert new record if no ID is provided
                            ClientOccupation::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $obj->id,
                                'skill_assessment' => $skillAssessment,
                                'nomi_occupation' => $nomiOccupation,
                                'occupation_code' => $occupationCode,
                                'list' => $list,
                                'visa_subclass' => $visaSubclass,
                                'dates' => $date,
                                'relevant_occupation' => $relevant_occupation
                            ]);
                        }
                    }
                }
            }
            //Client Occupation End Code

            //Test Score Start Code
            if ( isset($requestData['test_type_hidden']) && is_array($requestData['test_type_hidden']) )
            {
                // Loop through each test score entry in the request
                foreach ($requestData['test_type_hidden'] as $key => $testType) {
                    $listening = $requestData['listening'][$key] ?? null;
                    $reading = $requestData['reading'][$key] ?? null;
                    $writing = $requestData['writing'][$key] ?? null;
                    $speaking = $requestData['speaking'][$key] ?? null;
                    $overallScore = $requestData['overall_score'][$key] ?? null;
                    $testDate = $requestData['test_date'][$key] ?? null;
                    $testScoreId = $requestData['test_score_id'][$key] ?? null;
                    $relevant_test = $requestData['relevant_test_hidden'][$key] ?? null;

                    // Check if the current row is not blank (i.e., test_type and test_date are not empty)
                    if (!empty($testType) ) {
                        if ($testScoreId) {
                            // Update existing test score if ID is provided
                            $existingTestScore = ClientTestScore::find($testScoreId);
                            if ($existingTestScore && $existingTestScore->client_id == $obj->id) {
                                $existingTestScore->update([
                                    'admin_id' => Auth::user()->id,
                                    'test_type' => $testType,
                                    'listening' => $listening, // Update with text value
                                    'reading' => $reading,     // Update with text value
                                    'writing' => $writing,     // Update with text value
                                    'speaking' => $speaking,   // Update with text value
                                    'test_date' => $testDate,
                                    'overall_score' => $overallScore, // Update overall_score
                                    'relevant_test' => $relevant_test
                                ]);
                            }
                        } else {
                            // Check if a test score with the same type and date already exists
                            /*$existingTestScore = ClientTestScore::where('client_id', $obj->id)
                                ->where('test_type', $testType)
                                ->where('test_date', $testDate)
                                ->first();

                            if (!$existingTestScore) {*/
                                // Insert new test score if no duplicate is found
                                ClientTestScore::create([
                                    'admin_id' => Auth::user()->id,
                                    'client_id' => $obj->id, // Assigning the correct client ID
                                    'test_type' => $testType,
                                    'listening' => $listening, // Set with text value
                                    'reading' => $reading,     // Set with text value
                                    'writing' => $writing,     // Set with text value
                                    'speaking' => $speaking,   // Set with text value
                                    'test_date' => $testDate,
                                    'overall_score' => $overallScore, // Set overall_score
                                    'relevant_test' => $relevant_test
                                ]);
                            //}
                        }
                    }
                }
            }
            //Test Score End Code

            //Spouse Detail Start Code
            if(
                (isset($requestData['spouse_english_score']) && !empty($requestData['spouse_english_score']))
                ||
                (isset($requestData['spouse_test_type']) && !empty($requestData['spouse_test_type']))
                ||
                (isset($requestData['spouse_listening_score']) && !empty($requestData['spouse_listening_score']))
                ||
                (isset($requestData['spouse_reading_score']) && !empty($requestData['spouse_reading_score']))
                ||
                (isset($requestData['spouse_writing_score']) && !empty($requestData['spouse_writing_score']))
                ||
                (isset($requestData['spouse_speaking_score']) && !empty($requestData['spouse_speaking_score']))
                ||
                (isset($requestData['spouse_overall_score']) && !empty($requestData['spouse_overall_score']))
                ||
                (isset($requestData['spouse_test_date']) && !empty($requestData['spouse_test_date']))
                ||
                (isset($requestData['spouse_skill_assessment']) && !empty($requestData['spouse_skill_assessment']))
                ||
                (isset($requestData['spouse_skill_assessment_status']) && !empty($requestData['spouse_skill_assessment_status']))
                ||
                (isset($requestData['spouse_nomi_occupation']) && !empty($requestData['spouse_nomi_occupation']))
                ||
                (isset($requestData['spouse_assessment_date']) && !empty($requestData['spouse_assessment_date']))
            )
            {

                // Extract single values from the request
                $englishScore = $requestData['spouse_english_score'];
                $testType = $requestData['spouse_test_type'];
                $listeningScore = $requestData['spouse_listening_score'];
                $readingScore = $requestData['spouse_reading_score'];
                $writingScore = $requestData['spouse_writing_score'];
                $speakingScore = $requestData['spouse_speaking_score'];
                $overallScore = $requestData['spouse_overall_score'];
                $spousetestdate = $requestData['spouse_test_date'];

                $skillAssessment = $requestData['spouse_skill_assessment'];
                $skillAssessmentStatus = $requestData['spouse_skill_assessment_status'];
                $nomiOccupation = $requestData['spouse_nomi_occupation'];
                $assessmentDate = $requestData['spouse_assessment_date'];

                if( ClientSpouseDetail::where('client_id', $obj->id)->delete() ) {
                    ClientSpouseDetail::create([
                        'admin_id' => Auth::user()->id,
                        'client_id' => $obj->id,
                        'spouse_english_score' => $englishScore,
                        'spouse_test_type' => $testType,
                        'spouse_listening_score' => $listeningScore,
                        'spouse_reading_score' => $readingScore,
                        'spouse_writing_score' => $writingScore,
                        'spouse_speaking_score' => $speakingScore,
                        'spouse_overall_score' => $overallScore,
                        'spouse_test_date' => $spousetestdate,
                        'spouse_skill_assessment' => $skillAssessment,
                        'spouse_skill_assessment_status' => $skillAssessmentStatus,
                        'spouse_nomi_occupation' => $nomiOccupation,
                        'spouse_assessment_date' => $assessmentDate
                    ]);
                }
            }
            //Spouse Detail End Code

            // Handle Partner Deletion
        if (isset($requestData['delete_partner_ids']) && is_array($requestData['delete_partner_ids'])) {
            \Log::info('Deleting partners:', ['delete_partner_ids' => $requestData['delete_partner_ids']]);
            foreach ($requestData['delete_partner_ids'] as $partnerId) {
                $partner = ClientPartner::find($partnerId);
                if ($partner && $partner->client_id == $obj->id) {
                    // Delete reciprocal relationship if exists
                    if ($partner->related_client_id) {
                        ClientPartner::where('client_id', $partner->related_client_id)
                            ->where('related_client_id', $obj->id)
                            ->delete();
                        \Log::info('Deleted reciprocal relationship for partner:', ['partner_id' => $partnerId, 'related_client_id' => $partner->related_client_id]);
                    }
                    $partner->delete();
                    \Log::info('Deleted partner:', ['partner_id' => $partnerId]);
                } else {
                    \Log::warning('Partner not found or does not belong to client:', ['partner_id' => $partnerId, 'client_id' => $obj->id]);
                }
            }
        }

        // Partner Handling for client_partners table
        if (isset($requestData['partner_details']) && is_array($requestData['partner_details'])) {
            \Log::info('Processing partner data:', [
                'partner_details' => $requestData['partner_details'],
                'relationship_type' => $requestData['relationship_type'] ?? [],
                'partner_id' => $requestData['partner_id'] ?? [],
                'partner_email' => $requestData['partner_email'] ?? [],
                'partner_first_name' => $requestData['partner_first_name'] ?? [],
                'partner_last_name' => $requestData['partner_last_name'] ?? [],
                'partner_phone' => $requestData['partner_phone'] ?? [],
            ]);

            $relationshipMap = [
                'Husband' => 'Wife',
                'Wife' => 'Husband',
                'Ex-Wife' => 'Ex-Wife',
                'Defacto' => 'Defacto',
            ];

            foreach ($requestData['partner_details'] as $key => $details) {
                $relationshipType = $requestData['relationship_type'][$key] ?? null;
                $partnerId = $requestData['partner_id'][$key] ?? null;
                $email = $requestData['partner_email'][$key] ?? null;
                $firstName = $requestData['partner_first_name'][$key] ?? null;
                $lastName = $requestData['partner_last_name'][$key] ?? null;
                $phone = $requestData['partner_phone'][$key] ?? null;

                // Skip if relationship_type is not provided (validation should catch this, but adding as a safety check)
                if (empty($relationshipType)) {
                    \Log::warning('Skipping partner entry due to missing relationship type:', ['key' => $key]);
                    continue;
                }

                $relatedClientId = $partnerId && is_numeric($partnerId) ? $partnerId : null;

                // Determine if extra fields should be saved (only if related_client_id is null)
                $saveExtraFields = !$relatedClientId;

                // Prepare partner data for client_partners table
                $partnerData = [
                    'admin_id' => Auth::user()->id,
                    'client_id' => $obj->id,
                    'related_client_id' => $relatedClientId,
                    'details' => $relatedClientId ? $details : null, // Save details only if a match is found
                    'relationship_type' => $relationshipType,
                    'email' => $saveExtraFields ? $email : null,
                    'first_name' => $saveExtraFields ? $firstName : null,
                    'last_name' => $saveExtraFields ? $lastName : null,
                    'phone' => $saveExtraFields ? $phone : null,
                ];

                \Log::info('Prepared partner data:', ['key' => $key, 'partnerData' => $partnerData]);

                if ($partnerId && is_numeric($partnerId)) {
                    // Update existing partner
                    $existingPartner = ClientPartner::find($partnerId);
                    if ($existingPartner && $existingPartner->client_id == $obj->id) {
                        $existingPartner->update($partnerData);
                        \Log::info('Updated existing partner:', ['partner_id' => $partnerId, 'data' => $partnerData]);

                        // Update reciprocal relationship if exists
                        if ($existingPartner->related_client_id && isset($relationshipMap[$relationshipType])) {
                            $reciprocalData = [
                                'admin_id' => Auth::user()->id,
                                'relationship_type' => $relationshipMap[$relationshipType],
                                'details' => "{$obj->first_name} {$obj->last_name} ({$obj->email}, {$obj->phone}, {$obj->client_id})",
                                'email' => null,
                                'first_name' => null,
                                'last_name' => null,
                                'phone' => null,
                            ];
                            ClientPartner::where('client_id', $existingPartner->related_client_id)
                                ->where('related_client_id', $obj->id)
                                ->update($reciprocalData);
                            \Log::info('Updated reciprocal relationship for partner:', ['partner_id' => $partnerId, 'reciprocal_data' => $reciprocalData]);
                        }
                    } else {
                        \Log::warning('Existing partner not found or does not belong to client:', ['partner_id' => $partnerId, 'client_id' => $obj->id]);
                    }
                } else {
                    // Create new partner
                    $newPartner = ClientPartner::create($partnerData);
                    \Log::info('Created new partner:', ['new_partner_id' => $newPartner->id, 'data' => $partnerData]);

                    // Create reciprocal relationship if related_client_id is set
                    if ($relatedClientId && isset($relationshipMap[$relationshipType])) {
                        $relatedClient = Admin::find($relatedClientId);
                        if ($relatedClient) {
                            $reciprocalData = [
                                'admin_id' => Auth::user()->id,
                                'client_id' => $relatedClientId,
                                'related_client_id' => $obj->id,
                                'details' => "{$obj->first_name} {$obj->last_name} ({$obj->email}, {$obj->phone}, {$obj->client_id})",
                                'relationship_type' => $relationshipMap[$relationshipType],
                                'email' => null,
                                'first_name' => null,
                                'last_name' => null,
                                'phone' => null,
                            ];
                            ClientPartner::create($reciprocalData);
                            \Log::info('Created reciprocal relationship for new partner:', ['new_partner_id' => $newPartner->id, 'reciprocal_data' => $reciprocalData]);
                        } else {
                            \Log::warning('Related client not found for reciprocal relationship:', ['related_client_id' => $relatedClientId]);
                        }
                    }
                }
            }

            // Debug: Log the number of partners saved
            $savedPartners = ClientPartner::where('client_id', $obj->id)->count();
            \Log::info('Total partners saved for client:', ['client_id' => $obj->id, 'count' => $savedPartners]);
        } else {
            \Log::info('No partner data provided to process.');
        }

			/*$obj->total_points	=	@$requestData['total_points'];
			$obj->type	=	@$requestData['type'];
			$obj->source	=	@$requestData['source'];
			if(@$requestData['source'] == 'Sub Agent' ){
			    $obj->agent_id	=	@$requestData['subagent'];
			} else {
			    $obj->agent_id	=	'';
			}*/
            $saved	=	$obj->save();
            if( $requestData['client_id'] != '') {
                $objs			   = 	Admin::find($obj->id);
                $objs->client_id	=	$requestData['client_id'];
                $saveds				=	$objs->save();
			}

			$route = $request->route;
			if(strpos($request->route,'?')){
			    $position=strpos($request->route,'?');
                if ($position !== false) {
                    $route = substr($request->route, 0, $position);
                }
			}
            //dd($route);
			if(!$saved) {
			    return redirect()->back()->with('error', Config::get('constants.server_error'));
			} else if( $route ==url('/admin/assignee')) {
                //$subject = 'Lead status has changed to '.@$requestData['status'].' from '. \Auth::user()->first_name;
                $subject = 'Lead status has changed from '. \Auth::user()->first_name;
                $objs = new ActivitiesLog;
                $objs->client_id = $request->id;
                $objs->created_by = \Auth::user()->id;
                $objs->subject = $subject;
                $objs->save();
                return redirect()->route('assignee.index')->with('success','Assignee updated successfully');
			} else {
                //If record exist then update service taken
                if (DB::table('client_service_takens')->where('client_id',  $requestData['id'])->exists()) {
                    DB::table('client_service_takens')->where('client_id', $requestData['id'])->update(['is_saved_db' => 1 ]);
                }

                $clientId = $requestData['id'];
                $encodedId = base64_encode(convert_uuencode($clientId));

                $latestMatter = DB::table('client_matters')
                    ->where('client_id', $clientId)
                    ->where('matter_status', 1)
                    ->orderByDesc('id')
                    ->first();

                $redirectUrl = $latestMatter
                    ? '/admin/clients/detail/'.$encodedId.'/'.$latestMatter->client_unique_matter_no
                    : '/admin/clients/detail/'.$encodedId;

                return Redirect::to($redirectUrl)->with('success', 'Details updated successfully');
                //return Redirect::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$requestData['id'])))->with('success', 'Details updated successfully');
			}
		}
        else
		{
			if(isset($id) && !empty($id))
			{
                $id = $this->decodeString($id); //dd($id);
                if(Admin::where('id', '=', $id)->where('role', '=', '7')->exists())
                {
                    $fetchedData = Admin::find($id); //dd($fetchedData);

                    $clientContacts = ClientContact::where('client_id', $id)->get() ?? [];
                    $emails = ClientEmail::where('client_id', $id)->get() ?? [];
                    $visaCountries = ClientVisaCountry::where('client_id', $id)->get() ?? [];
                    $clientAddresses = ClientAddress::where('client_id', $id)->get() ?? [];
                    $qualifications = ClientQualification::where('client_id', $id)->get() ?? [];
                    $experiences = ClientExperience::where('client_id', $id)->get() ?? [];
                    $clientOccupations = ClientOccupation::where('client_id', $id)->get();
                    $testScores = ClientTestScore::where('client_id', $id)->get() ?? [];
                    $ClientSpouseDetail = ClientSpouseDetail::where('client_id', $id)->first() ?? [];
                    //dd($ClientSpouseDetail->spouse_english_score);
                    return view('Admin.clients.edit', compact('fetchedData', 'clientContacts', 'emails', 'visaCountries','clientAddresses', 'qualifications', 'experiences','clientOccupations','testScores','ClientSpouseDetail'));
                } else {
                    return Redirect::to('/admin/clients')->with('error', 'Clients Not Exist');
                }
			} else {
			    return Redirect::to('/admin/clients')->with('error', Config::get('constants.unauthorized'));
			}
		}
    }

    public function detail(Request $request, $id = NULL, $id1 = NULL, $tab = NULL)
    {

        if (isset($request->t)) {
            if (\App\Models\Notification::where('id', $request->t)->exists()) {
                $ovv = \App\Models\Notification::find($request->t);
                $ovv->receiver_status = 1;
                $ovv->save();
            }
        }

        if (isset($id) && !empty($id)) {
            $encodeId = $id;
            $id = $this->decodeString($id);
            
            // Set default tab if not provided
            $activeTab = $tab ?? 'personaldetails';

            if (Admin::where('id', '=', $id)->where('role', '=', '7')->exists()) {
                $fetchedData = Admin::find($id); //dd($fetchedData);

                /* Points Calculation Code Start */

                //Age point calculation
                if ( isset($fetchedData) && $fetchedData->dob != '' && $fetchedData->dob != '0000-00-00') {
                    $dobDate = new DateTime($fetchedData->dob);
                    $today = new DateTime();
                    $age = $today->diff($dobDate)->y;
                    $agePoint = $this->calculateAgePoint($age);

                    $clientPoint = ClientPoint::updateOrCreate(
                        ['client_id' => $fetchedData->id, 'item_type' => 'Age'],
                        ['value' => $age, 'calculate_point' => $agePoint, 'admin_id' => auth()->user()->id ]
                    );
                }

                //English point calculation
                $clientTestScoreCnt = ClientTestScore::where('client_id', $id)->count();
                if ( isset($clientTestScoreCnt)  && $clientTestScoreCnt >0 ) {

                    $clientTestScoreLatest = ClientTestScore::where('client_id', $id)->orderBy('id','desc')->first();
                    // Calculate English proficiency points
                    list($englishLevel, $englishPoint) = $this->calculateEnglishPoint(
                        $clientTestScoreLatest->test_type,
                        $clientTestScoreLatest->listening,
                        $clientTestScoreLatest->reading,
                        $clientTestScoreLatest->writing,
                        $clientTestScoreLatest->speaking
                    );
                    // Update or create the record for the specific test type
                    $clientPoint = ClientPoint::updateOrCreate(
                        ['client_id' => $fetchedData->id, 'item_type' => 'English'], // Match by test type
                        ['test_name' => $clientTestScoreLatest->test_type,'value' => $englishLevel, 'calculate_point' => $englishPoint, 'admin_id' => auth()->user()->id ] // Update if found
                    );
                }
                /* Points Calculation Code End */

                //Fetch other client-related data
                $clientAddresses = ClientAddress::where('client_id', $id)->get();
                $clientContacts = ClientContact::where('client_id', $id)->get();
                $emails = ClientEmail::where('client_id', $id)->get() ?? [];
                $qualifications = ClientQualification::where('client_id', $id)->get() ?? [];
                $experiences = ClientExperience::where('client_id', $id)->get() ?? [];
                $testScores = ClientTestScore::where('client_id', $id)->get() ?? [];
                $visaCountries = ClientVisaCountry::where('client_id', $id)->get() ?? [];
                $clientSpouseDetail = ClientSpouseDetail::where('client_id', $id)->get();
                $clientOccupations = ClientOccupation::where('client_id', $id)->get();
                $ClientPoints = ClientPoint::where('client_id', $id)->get();

                // Fetch client family details with optimized query
                $clientFamilyDetails = ClientRelationship::Where('client_id', $id)->get()?? [];
                //dd($clientFamilyDetails);
                //Return the view with all data
                $viewName = 'Admin.clients.detail';
                
                // Check if this is the test route
                if (request()->route()->getName() === 'admin.clients.detail-test') {
                    $viewName = 'Admin.clients.detail_test';
                }
                
                return view($viewName, compact(
                    'fetchedData', 'clientAddresses', 'clientContacts', 'emails', 'qualifications',
                    'experiences', 'testScores', 'visaCountries', 'clientOccupations','ClientPoints', 'clientSpouseDetail',
                    'encodeId', 'id1','clientFamilyDetails', 'activeTab'
                ));
            } else {
                return redirect('/admin/clients')->with('error', 'Clients Not Exist');
            }
        } else {
            return redirect('/admin/clients')->with('error', Config::get('constants.unauthorized'));
        }
    }

    //Calculate Age Point
    public function calculateAgePoint($age) {
        $points = 0;
        if ($age >= 18 && $age <= 24) {
            $points = 25;
        } elseif ($age >= 25 && $age <= 32) {
            $points = 30;
        } elseif ($age >= 33 && $age <= 39) {
            $points = 25;
        } elseif ($age >= 40 && $age <= 44) {
            $points = 15;
        } /*elseif ($age >= 45 && $age <= 54) {
            $points = 15;
        } elseif ($age >= 55) {
            $points = 0;
        }*/
        else {
            $points = 0;
        }
        return $points;
    }

    //Calculate English Point
    public function calculateEnglishPoint($testType, $listening, $reading, $writing, $speaking) {
        $points = 'N/A';
        $numericPoints = 0; // New variable for numerical points

        if ( $testType == 'IELTS Academic' || $testType == 'IELTS General Training' ){
            if ($listening >= 8 && $reading >= 8 && $writing >= 8 && $speaking >= 8) {
                $points = 'Superior English';
                $numericPoints = 20; // Numeric value for Superior English
            } elseif ($listening >= 7 && $reading >= 7 && $writing >= 7 && $speaking >= 7) {
                $points = 'Proficient English';
                $numericPoints = 10; // Numeric value for Proficient English
            } elseif ($listening >= 6 && $reading >= 6 && $writing >= 6 && $speaking >= 6) {
                $points = 'Competent English';
                $numericPoints = 0; // Numeric value for Competent English
            } elseif ($listening >= 5 && $reading >= 5 && $writing >= 5 && $speaking >= 5) {
                $points = 'Vocational English';
                $numericPoints = 0; // Numeric value for Vocational English
            } elseif (($listening + $reading + $writing + $speaking) / 4 >= 4.5) {
                $points = 'Functional English';
                $numericPoints = 0; // Numeric value for Functional English
            }
        }
        else if ($testType == 'TOEFL iBT') {
            if ($listening >= 28 && $reading >= 29 && $writing >= 30 && $speaking >= 26) {
                $points = 'Superior English';
                $numericPoints = 20;
            } elseif ($listening >= 24 && $reading >= 24 && $writing >= 27 && $speaking >= 23) {
                $points = 'Proficient English';
                $numericPoints = 10;
            } elseif ($listening >= 12 && $reading >= 13 && $writing >= 21 && $speaking >= 18) {
                $points = 'Competent English';
                $numericPoints = 0;
            } elseif ($listening >= 4 && $reading >= 4 && $writing >= 14 && $speaking >= 14) {
                $points = 'Vocational English';
                $numericPoints = 0;
            } elseif (($listening + $reading + $writing + $speaking)  >= 32) {
                $points = 'Functional English';
                $numericPoints = 0;
            }
        }
        else if ($testType == 'PTE Academic') {
            if ($listening >= 79 && $reading >= 79 && $writing >= 79 && $speaking >= 79) {
                $points = 'Superior English';
                $numericPoints = 20;
            } elseif ($listening >= 65 && $reading >= 65 && $writing >= 65 && $speaking >= 65) {
                $points = 'Proficient English';
                $numericPoints = 10;
            } elseif ($listening >= 50 && $reading >= 50 && $writing >= 50 && $speaking >= 50) {
                $points = 'Competent English';
                $numericPoints = 0;
            } elseif ($listening >= 36 && $reading >= 36 && $writing >= 36 && $speaking >= 36) {
                $points = 'Vocational English';
                $numericPoints = 0;
            } elseif (($listening + $reading + $writing + $speaking)  >= 30) {
                $points = 'Functional English';
                $numericPoints = 0;
            }
        }
        else if ($testType == 'OET') {
            if ($listening == 'A' && $reading == 'A' && $writing == 'A' && $speaking == 'A') {
                //At least A for each of the 4 test components
                $points = 'Superior English';
                $numericPoints = 20;
            } elseif ($listening == 'B' && $reading == 'B' && $writing == 'B' && $speaking == 'B') {
                //At least B for each of the 4 test components
                $points = 'Proficient English';
                $numericPoints = 10;
            } elseif ($listening == 'B' && $reading == 'B' && $writing == 'B' && $speaking == 'B') {
                //At least B for each of the 4 test components
                $points = 'Competent English';
                $numericPoints = 0;
            } elseif ($listening == 'B' && $reading == 'B' && $writing == 'B' && $speaking == 'B') {
                //At least B for each of the 4 test components
                $points = 'Vocational English';
                $numericPoints = 0;
            } else {
                $points = 'Functional English';
                $numericPoints = 0;
            }
        }
        return [$points, $numericPoints]; // Return both variables
    }

    public function saveRelationship(Request $request)
    {
        $clientId = auth()->user()->id; // Assuming the logged-in user is the client

        // Loop through the relationship data to insert each relationship
        foreach ($request->relationship_type as $index => $relationshipType) {
            ClientRelationship::create([
                'client_id' => $clientId,
                'relationship_type' => $relationshipType,
                'name' => $request->name[$index],
                'phone_number' => $request->phone_number[$index],
                'email_address' => $request->email_address[$index],
                'crm_reference' => $request->crm_reference[$index] ?? null,
            ]);
        }

        return response()->json(['success' => 'Relationship data saved successfully!']);
    }

    //Update session to be complete
    public function updatesessioncompleted(Request $request,CheckinLog $checkinLog)
    {
        $data = $request->all(); //dd($data['client_id']);
        $sessionExist = CheckinLog::where('client_id', $data['client_id'])
        ->where('status', 2)
        ->update(['status' => 1]);
        if($sessionExist){
            $response['status'] 	= 	true;
            $response['message']	=	'Session completed successfully';
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
        }
        echo json_encode($response);
    }

	public function getrecipients(Request $request){
		$squery = $request->q;
		if($squery != ''){
				$d = '';
			 $clients = \App\Models\Admin::where('is_archived', '=', 0)
       ->where('role', '=', 7)
       ->where(
           function($query) use ($squery) {
             return $query
                    ->where('email', 'LIKE', '%'.$squery.'%')
                    ->orwhere('first_name', 'LIKE','%'.$squery.'%')->orwhere('last_name', 'LIKE','%'.$squery.'%')->orwhere('client_id', 'LIKE','%'.$squery.'%')->orwhere('phone', 'LIKE','%'.$squery.'%')  ->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$squery."%");
            })
            ->get();

            /* $leads = \App\Models\Lead::where('converted', '=', 0)
			->where(
			function($query) use ($squery,$d) {
				return $query
					->where('email', 'LIKE', '%'.$squery.'%')
					->orwhere('first_name', 'LIKE','%'.$squery.'%')->orwhere('last_name', 'LIKE','%'.$squery.'%')->orwhere('phone', 'LIKE','%'.$squery.'%')  ->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$squery."%");
				})
            ->get();*/

			$items = array();
			foreach($clients as $clint){
				$items[] = array('name' => $clint->first_name.' '.$clint->last_name,'email'=>$clint->email,'status'=>$clint->type,'id'=>$clint->id,'cid'=>base64_encode(convert_uuencode(@$clint->id)));
			}

			$litems = array();
			/*	foreach($leads as $lead){
				$litems[] = array('name' => $lead->first_name.' '.$lead->last_name,'email'=>$lead->email,'status'=>'Lead','id'=>$lead->id,'cid'=>base64_encode(convert_uuencode(@$lead->id)));
			}*/
			$m = array_merge($items, $litems);
			echo json_encode(array('items'=>$m));
		}
	}

	public function getonlyclientrecipients(Request $request){
		$squery = $request->q;
		if($squery != ''){
				$d = '';
			$clients = \App\Models\Admin::where('is_archived', '=', 0)
			->where('role', '=', 7)
			->where(
           function($query) use ($squery) {
             	return $query
                    ->where('email', 'LIKE', '%'.$squery.'%')
                    ->orwhere('first_name', 'LIKE','%'.$squery.'%')->orwhere('last_name', 'LIKE','%'.$squery.'%')->orwhere('client_id', 'LIKE','%'.$squery.'%')->orwhere('phone', 'LIKE','%'.$squery.'%')  ->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$squery."%");
            })
            ->get();

			$items = array();
			foreach($clients as $clint){
				$items[] = array('name' => $clint->first_name.' '.$clint->last_name,'email'=>$clint->email,'status'=>$clint->type,'id'=>$clint->id,'cid'=>base64_encode(convert_uuencode(@$clint->id)));
			}

			$litems = array();

			$m = array_merge($items, $litems);
			echo json_encode(array('items'=>$m));
		}
	}

    /*public function getallclients(Request $request)
    {
        $squery = $request->q;

        if ($squery != '') {
            $results = [];

            // First: search department_reference or other_reference in client_matters
            $matterMatches = DB::table('client_matters')
                ->where('department_reference', 'LIKE', "%{$squery}%")
                ->orWhere('other_reference', 'LIKE', "%{$squery}%")
                ->get();

            foreach ($matterMatches as $matter) {
                $clientM = \App\Models\Admin::where('id', $matter->client_id)->first();
                $results[] = [
                    'id' => base64_encode(convert_uuencode($matter->client_id)) . '/Matter/'.$matter->client_unique_matter_no,
                    'name' => $clientM->first_name . ' ' . $clientM->last_name,
                    'email' => $clientM->email,
                    'status' => $clientM->is_archived ? 'Archived' : $clientM->type,
                    'cid' => $clientM->id,
                ];
            }

            // Second: search client (admin)
            $d = '';
            if (strstr($squery, '/')) {
                $dob = explode('/', $squery);
                if (!empty($dob) && is_array($dob)) {
                    $d = $dob[2] . '/' . $dob[1] . '/' . $dob[0];
                }
            }

            $clients = \App\Models\Admin::where('role', '=', 7)
                ->whereNull('is_deleted')
                ->where(function ($query) use ($squery, $d) {
                    $query->orWhere('email', 'LIKE', "%$squery%")
                        ->orWhere('first_name', 'LIKE', "%$squery%")
                        ->orWhere('last_name', 'LIKE', "%$squery%")
                        ->orWhere('client_id', 'LIKE', "%$squery%")
                        ->orWhere('att_email', 'LIKE', "%$squery%")
                        ->orWhere('att_phone', 'LIKE', "%$squery%")
                        ->orWhere('phone', 'LIKE', "%$squery%")
                        ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%$squery%");
                    if ($d != "") {
                        $query->orWhere('dob', '=', $d);
                    }
                })
                ->get();

            foreach ($clients as $client) {
                // Check if active matter exists
                $latestMatter = DB::table('client_matters')
                    ->where('client_id', $client->id)
                    ->where('matter_status', 1)
                    ->orderByDesc('id') // or use created_at if preferred
                    ->first();

                if ($latestMatter) {
                    $resultFinalId = base64_encode(convert_uuencode($client->id)) . '/Matter/' . $latestMatter->client_unique_matter_no;
                } else {
                    $resultFinalId = base64_encode(convert_uuencode($client->id)) . '/Client';
                }
                $results[] = [
                    'id' => $resultFinalId,
                    'name' => $client->first_name . ' ' . $client->last_name,
                    'email' => $client->email,
                    'status' => $client->is_archived ? 'Archived' : $client->type,
                    'cid' => $client->id,
                ];
            }
            return response()->json(['items' => $results]);
        }
    } */

    //Chnage at 15aug2025 after slow issue
	/*public function getallclients(Request $request)
    {
        $squery = $request->q;
        if ($squery != '') {
            $results = [];
            // First: search department_reference or other_reference in client_matters
            $matterMatches = DB::table('client_matters')
                ->where('department_reference', 'LIKE', "%{$squery}%")
                ->orWhere('other_reference', 'LIKE', "%{$squery}%")
                ->get();

            foreach ($matterMatches as $matter) {
                $clientM = \App\Models\Admin::where('id', $matter->client_id)->first();
                $results[] = [
                    'id' => base64_encode(convert_uuencode($matter->client_id)) . '/Matter/'.$matter->client_unique_matter_no,
                    'name' => $clientM->first_name . ' ' . $clientM->last_name,
                    'email' => $clientM->email,
                    'status' => $clientM->is_archived ? 'Archived' : $clientM->type,
                    'cid' => $clientM->id,
                ];
            }

            // Second: search client (admin)
            $d = '';
            if (strstr($squery, '/')) {
                $dob = explode('/', $squery);
                if (!empty($dob) && is_array($dob)) {
                    $d = $dob[2] . '/' . $dob[1] . '/' . $dob[0];
                }
            }

            $clients = \App\Models\Admin::where('role', '=', 7)
                ->whereNull('is_deleted')
                ->leftJoin('client_contacts', 'admins.id', '=', 'client_contacts.client_id')
                ->leftJoin('client_emails', 'admins.id', '=', 'client_emails.client_id')
                ->where(function ($query) use ($squery, $d) {
                    $query->orWhere('admins.email', 'LIKE', "%$squery%")
                        ->orWhere('admins.first_name', 'LIKE', "%$squery%")
                        ->orWhere('admins.last_name', 'LIKE', "%$squery%")
                        ->orWhere('admins.client_id', 'LIKE', "%$squery%")
                        ->orWhere('admins.att_email', 'LIKE', "%$squery%")
                        ->orWhere('admins.att_phone', 'LIKE', "%$squery%")
                        ->orWhere('admins.phone', 'LIKE', "%$squery%")
                        ->orWhere('admins.emergency_contact_no', 'LIKE', "%$squery%")
                        ->orWhere(DB::raw("CONCAT(admins.first_name, ' ', admins.last_name)"), 'LIKE', "%$squery%")
                        ->orWhere('client_contacts.phone', 'LIKE', "%$squery%")
                        ->orWhere('client_emails.email', 'LIKE', "%$squery%");
                    if ($d != "") {
                        $query->orWhere('admins.dob', '=', $d);
                    }
                })
                ->select(
                    'admins.*',
                    DB::raw('GROUP_CONCAT(DISTINCT client_contacts.phone ORDER BY client_contacts.contact_type SEPARATOR ", ") as all_phones'),
                    DB::raw('GROUP_CONCAT(DISTINCT client_emails.email ORDER BY client_emails.email_type SEPARATOR ", ") as all_emails')
                )
                ->groupBy('admins.id')
                ->get();

            foreach ($clients as $client) {
                // Check if active matter exists
                $latestMatter = DB::table('client_matters')
                    ->where('client_id', $client->id)
                    ->where('matter_status', 1)
                    ->orderByDesc('id') // or use created_at if preferred
                    ->first();

                if ($latestMatter) {
                    $resultFinalId = base64_encode(convert_uuencode($client->id)) . '/Matter/' . $latestMatter->client_unique_matter_no;
                } else {
                    $resultFinalId = base64_encode(convert_uuencode($client->id)) . '/Client';
                }
                $results[] = [
                    'id' => $resultFinalId,
                    'name' => $client->first_name . ' ' . $client->last_name,
                    'email' => $client->email,
                    'status' => $client->is_archived ? 'Archived' : $client->type,
                    'cid' => $client->id,
                    'phones' => $client->all_phones, // all phone numbers combined
                    'emails' => $client->all_emails, // All emails combined
                ];
            }
            return response()->json(['items' => $results]);
        }
    }*/

    public function getallclients(Request $request)
    {
        $squery = $request->q;
        if ($squery != '') {
            $results = [];

            /**
             * 1. Search in client_matters by department_reference / other_reference
             */
            $matterMatches = DB::table('client_matters')
                ->where('department_reference', 'LIKE', "%{$squery}%")
                ->orWhere('other_reference', 'LIKE', "%{$squery}%")
                ->get();

            // Get all matching client IDs in one go
            $matterClientIds = $matterMatches->pluck('client_id')->unique()->toArray();

            if (!empty($matterClientIds)) {
                $matterClients = \App\Models\Admin::whereIn('id', $matterClientIds)->get();
                $matterClientsMap = $matterClients->keyBy('id');

                foreach ($matterMatches as $matter) {
                    if (isset($matterClientsMap[$matter->client_id])) {
                        $clientM = $matterClientsMap[$matter->client_id];
                        $results[] = [
                            'id' => base64_encode(convert_uuencode($matter->client_id)) . '/Matter/' . $matter->client_unique_matter_no,
                            'name' => $clientM->first_name . ' ' . $clientM->last_name,
                            'email' => $clientM->email,
                            'status' => $clientM->is_archived ? 'Archived' : $clientM->type,
                            'cid' => $clientM->id,
                        ];
                    }
                }
            }

            /**
             * 2. Search in admins (clients) with subqueries for phones/emails
             */
            $d = '';
            if (strstr($squery, '/')) {
                $dob = explode('/', $squery);
                if (!empty($dob) && is_array($dob)) {
                    $d = $dob[2] . '/' . $dob[1] . '/' . $dob[0];
                }
            }

            $clients = \App\Models\Admin::query()
                ->where('role', 7)
                ->whereNull('is_deleted')
                ->where(function ($query) use ($squery, $d) {
                    $query->where('admins.email', 'LIKE', "%$squery%")
                        ->orWhere('admins.first_name', 'LIKE', "%$squery%")
                        ->orWhere('admins.last_name', 'LIKE', "%$squery%")
                        ->orWhere('admins.client_id', 'LIKE', "%$squery%")
                        ->orWhere('admins.att_email', 'LIKE', "%$squery%")
                        ->orWhere('admins.att_phone', 'LIKE', "%$squery%")
                        ->orWhere('admins.phone', 'LIKE', "%$squery%")
                        ->orWhere('admins.emergency_contact_no', 'LIKE', "%$squery%")
                        ->orWhere(DB::raw("CONCAT(admins.first_name, ' ', admins.last_name)"), 'LIKE', "%$squery%")
                        ->orWhereIn('admins.id', function ($sub) use ($squery) {
                            $sub->select('client_id')
                                ->from('client_contacts')
                                ->where('phone', 'LIKE', "%$squery%");
                        })
                        ->orWhereIn('admins.id', function ($sub) use ($squery) {
                            $sub->select('client_id')
                                ->from('client_emails')
                                ->where('email', 'LIKE', "%$squery%");
                        });

                    if ($d != "") {
                        $query->orWhere('admins.dob', '=', $d);
                    }
                })
                ->select(
                    'admins.*',
                    DB::raw('(SELECT GROUP_CONCAT(DISTINCT phone ORDER BY contact_type SEPARATOR ", ") 
                            FROM client_contacts 
                            WHERE client_contacts.client_id = admins.id) as all_phones'),
                    DB::raw('(SELECT GROUP_CONCAT(DISTINCT email ORDER BY email_type SEPARATOR ", ") 
                            FROM client_emails 
                            WHERE client_emails.client_id = admins.id) as all_emails'),
                    DB::raw('(SELECT client_unique_matter_no 
                            FROM client_matters 
                            WHERE client_matters.client_id = admins.id 
                            AND client_matters.matter_status = 1 
                            ORDER BY id DESC LIMIT 1) as latest_matter_no')
                )
                ->get();

            foreach ($clients as $client) {
                $resultFinalId = $client->latest_matter_no
                    ? base64_encode(convert_uuencode($client->id)) . '/Matter/' . $client->latest_matter_no
                    : base64_encode(convert_uuencode($client->id)) . '/Client';

                $results[] = [
                    'id' => $resultFinalId,
                    'name' => $client->first_name . ' ' . $client->last_name,
                    'email' => $client->email,
                    'status' => $client->is_archived ? 'Archived' : $client->type,
                    'cid' => $client->id,
                    'phones' => $client->all_phones,
                    'emails' => $client->all_emails,
                ];
            }

            return response()->json(['items' => $results]);
        }
    }

    public function getAllUser(Request $request, Admin $product) {
            $products = $request->q
                ? Admin::select('id', 'first_name')->where('first_name', 'LIKE', "%$request->q%")
                : new Admin();

            return $products->paginate(10, ['*'], 'page', $request->page)->toArray();
    }

	public function activities(Request $request){ 
		if(Admin::where('role', '=', '7')->where('id', $request->id)->exists()){
			$activities = ActivitiesLog::where('client_id', $request->id)->orderby('created_at', 'DESC')->get(); //->where('subject', '<>','added a note')
			$data = array();
			foreach($activities as $activit){
				$admin = Admin::where('id', $activit->created_by)->first();
                $data[] = array(
                    'activity_id' => $activit->id,
					'subject' => $activit->subject,
					'createdname' => substr($admin->first_name, 0, 1),
					'name' => $admin->first_name,
					'message' => $activit->description,
					'date' => date('d M Y, H:i A', strtotime($activit->created_at)),
                   'followup_date' => $activit->followup_date,
                   'task_group' => $activit->task_group,
                   'pin' => $activit->pin
                );
			}

			$response['status'] 	= 	true;
			$response['data']	=	$data;
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function updateclientstatus(Request $request){
		if(Admin::where('role', '=', '7')->where('id', $request->id)->exists()){
			$client = Admin::where('role', '=', '7')->where('id', $request->id)->first();

			$obj = Admin::find($request->id);
			$obj->rating = $request->rating;
			$saved = $obj->save();
			if($saved){
				if($client->rating == ''){
					$subject = 'has rated Client as '.$request->rating;
				}else{
					$subject = 'has changed Client’s rating from '.$client->rating.' to '.$request->rating;
				}
				$objs = new ActivitiesLog;
				$objs->client_id = $request->id;
				$objs->created_by = Auth::user()->id;
				$objs->subject = $subject;
				$objs->save();
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully updated your client’s information.';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function saveapplication(Request $request){
		if(Admin::where('role', '=', '7')->where('id', $request->client_id)->exists()){
			$workflow = $request->workflow;
			$explode = explode('_', $request->partner_branch);
			$partner = $explode[1];
			$branch = $explode[0];
			$product = $request->product;
			$client_id = $request->client_id;
			$status = 0;
			$workflowstage = \App\Models\WorkflowStage::where('w_id', $workflow)->orderby('id','asc')->first();
			$stage = $workflowstage->name;
			$sale_forcast = 0.00;
			$obj = new \App\Models\Application;
			$obj->user_id = Auth::user()->id;
			$obj->workflow = $workflow;
			$obj->partner_id = $partner;
			$obj->branch = $branch;
			$obj->product_id = $product;
			$obj->status = $status;
			$obj->stage = $stage;
			$obj->sale_forcast = $sale_forcast;
			$obj->client_id = $client_id;
            $obj->client_matter_id = $request->client_matter_id;
			$saved = $obj->save();
			if($saved){

				$subject = 'has started an application';
				$objs = new ActivitiesLog;
				$objs->client_id = $request->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '<span class="text-semi-bold">'.@$productdetail->name.'</span><p>'.@$partnerdetail->partner_name.' ('.@$PartnerBranch->name.')</p>';
				$objs->subject = $subject;
				$objs->save();
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully updated your client’s information.';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function getapplicationlists(Request $request){
		if(Admin::where('role', '=', '7')->where('id', $request->id)->exists()){
			$applications = \App\Models\Application::where('client_id', $request->id)->orderby('created_at', 'DESC')->get();
            //dd($applications);
			$data = array();
			ob_start();
			foreach($applications as $alist){

				$workflow = \App\Models\Workflow::where('id', $alist->workflow)->first();
				?>
				<tr id="id_<?php echo $alist->id; ?>">
				<td><a class="openapplicationdetail" data-id="<?php echo $alist->id; ?>" href="javascript:;" style="display:block;"><?php echo @$productdetail->name; ?></a> <small><?php echo @$partnerdetail->partner_name; ?>(<?php echo @$PartnerBranch->name; ?>)</small></td>
				<td><?php echo @$workflow->name; ?></td>
				<td><?php echo @$alist->stage; ?></td>
				<td>
				<?php if($alist->status == 0){ ?>
				<span class="ag-label--circular" style="color: #6777ef" >In Progress</span>
				<?php }else if($alist->status == 1){ ?>
					<span class="ag-label--circular" style="color: #6777ef" >Completed</span>
				<?php } else if($alist->status == 2){
				?>
				<span class="ag-label--circular" style="color: red;" >Discontinued</span>
				<?php
				} ?>
			</td>

				<td><?php if(@$alist->start_date != ''){ echo date('d/m/Y', strtotime($alist->start_date)); } ?></td>
				<td><?php if(@$alist->end_date != ''){ echo date('d/m/Y', strtotime($alist->end_date)); } ?></td>
				<td>
					<div class="dropdown d-inline">
						<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
						<div class="dropdown-menu">
							<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction(<?php echo @$alist->id; ?>, 'applications')"><i class="fas fa-trash"></i> Delete</a>
						</div>
					</div>
				</td>
			</tr>
				<?php
			}

			return ob_get_clean();
		}else {
			return '';
		}
	}

    //Save create and update note
	public function createnote(Request $request){ //dd($request->all());
        if(isset($request->noteid) && $request->noteid != ''){
            $obj = \App\Models\Note::find($request->noteid);
        }else{
            $obj = new \App\Models\Note;
            $obj->title = $request->title;
            $obj->matter_id = $request->matter_id;
        }

        $obj->client_id = $request->client_id;
        $obj->user_id = Auth::user()->id;
        $obj->description = $request->description;
        $obj->mail_id = $request->mailid;
        $obj->type = $request->vtype;
        /*if(isset($request->note_deadline_checkbox) && $request->note_deadline_checkbox != ''){
            if($request->note_deadline_checkbox == 1){
                $obj->note_deadline = $request->note_deadline;
            } else {
                $obj->note_deadline = NULL;
            }
        } else {
            $obj->note_deadline = NULL;
        }*/
        $obj->mobile_number = $request->mobileNumber; // Add this line
        $obj->task_group = $request->task_group;
        $saved = $obj->save();
		if($saved){
            if($request->vtype == 'client'){
                $subject = 'added a note';
                if(isset($request->noteid) && $request->noteid != ''){
                $subject = 'updated a note';
                }
                $objs = new ActivitiesLog;
                $objs->client_id = $request->client_id;
                $objs->created_by = Auth::user()->id;
                //$objs->mobile_number = $request->mobile_number; // Add this line if needed in the log
                $objs->description = '<span class="text-semi-bold">'.$request->task_group.'</span><p>'.$request->description.'</p>';
                $objs->subject = $subject;
                $objs->save();

                //Update date in client matter table
                if( isset($request->matter_id) && $request->matter_id != ""){
                    $obj1 = \App\Models\ClientMatter::find($request->matter_id);
                    $obj1->updated_at = date('Y-m-d H:i:s');
                    $obj1->save();
                }
            }
            $response['status'] 	= 	true;
            if(isset($request->noteid) && $request->noteid != ''){
                $response['message']	=	'You’ve successfully updated Note';
            }else{
                $response['message']	=	'You’ve successfully added Note';
            }
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
        echo json_encode($response);
	}

	//Update note datetime
	public function updateNoteDatetime(Request $request){
        $note_id = $request->note_id;
        $datetime = $request->datetime;
        
        try {
            $carbonDateTime = Carbon::parse($datetime);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid date and time format'
            ]);
        }
        
        // Find note with specific conditions
        $note = \App\Models\Note::where('id', $note_id)
            ->whereNull('assigned_to')
            ->whereNull('unique_group_id')
            ->first();
        
        if($note){
            $note->updated_at = $carbonDateTime; // Carbon instance
            $saved = $note->save();
            
            if($saved){
                $response['status'] = true;
                $response['message'] = 'Date and time updated successfully';
            } else {
                $response['status'] = false;
                $response['message'] = 'Failed to update date and time';
            }
        } else {
            $response['status'] = false;
            $response['message'] = 'Note not found or does not meet the criteria';
        }
        
        return response()->json($response);
    }

	public function getnotedetail(Request $request){
		$note_id = $request->note_id; //dd($note_id);
		if(\App\Models\Note::where('id',$note_id)->exists()){
			$data = \App\Models\Note::select('title','description','task_group')->where('id',$note_id)->first();
			$response['status'] 	= 	true;
			$response['data']	=	$data;
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function viewnotedetail(Request $request){
		$note_id = $request->note_id;
		if(\App\Models\Note::where('id',$note_id)->exists()){
			$data = \App\Models\Note::select('title','description','user_id','updated_at')->where('id',$note_id)->first();
			$admin = \App\Models\Admin::where('id', $data->user_id)->first();
			$s = substr(@$admin->first_name, 0, 1);
			$data->admin = $s;
			$response['status'] 	= 	true;
			$response['data']	=	$data;
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function viewapplicationnote(Request $request){
		$note_id = $request->note_id;
		if(\App\Models\ApplicationActivitiesLog::where('type','note')->where('id',$note_id)->exists()){
			$data = \App\Models\ApplicationActivitiesLog::select('title','description','user_id','updated_at')->where('type','note')->where('id',$note_id)->first();
			$admin = \App\Models\Admin::where('id', $data->user_id)->first();
			$s = substr(@$admin->first_name, 0, 1);
			$data->admin = $s;
			$response['status'] 	= 	true;
			$response['data']	=	$data;
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

    //Notes Tab redesign
    public function getnotes(Request $request){
        $client_id = $request->clientid;
        $type = $request->type;
        $task_group = $request->task_group;
        //if($task_group == ''){
            $notelist = \App\Models\Note::where('client_id',$client_id)->whereNull('assigned_to')->where('type',$type)->orderby('pin', 'DESC')->orderBy('created_at', 'DESC')->get();
        /*}else{
            $notelist = \App\Models\Note::where('client_id',$client_id)->whereNull('assigned_to')->where('type',$type)->where('task_group',$task_group)->orderby('pin', 'DESC')->orderBy('created_at', 'DESC')->get();
        }*/
        ob_start();
        foreach($notelist as $list){
            $admin = \App\Models\Admin::where('id', $list->user_id)->first();

            // Determine type label and color
            $type11 = strtolower($list->task_group ?? $list->task_group ?? 'others');
            $typeLabel = 'Others';
            $typeClass = 'note-type-others';

            if(strpos($type11, 'call') !== false) { $typeLabel = 'Call'; $typeClass = 'note-type-call'; }
            else if(strpos($type11, 'email') !== false) { $typeLabel = 'Email'; $typeClass = 'note-type-email'; }
            else if(strpos($type11, 'in-person') !== false) { $typeLabel = 'In-Person'; $typeClass = 'note-type-inperson'; }
            else if(strpos($type11, 'others') !== false) { $typeLabel = 'Others'; $typeClass = 'note-type-others'; }
            else if(strpos($type11, 'attention') !== false) { $typeLabel = 'Attention'; $typeClass = 'note-type-attention'; }
            //$desc = strip_tags($list->description);
            ?>
            <div class="note-card-redesign <?php if($list->pin == 1) echo 'pinned'; ?>" data-matterid="<?php echo $list->matter_id; ?>" id="note_id_<?php echo $list->id; ?>" data-id="<?php echo $list->id;?>" data-type="<?php echo $typeLabel;?>">
                <?php if($list->pin == 1) { ?>
                    <div class="pined_note">
                        <i class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </div>
                <?php } ?>    
            <div class="note-card-info">
                    <span class="note-type-label <?php echo $typeClass;?>"><?php echo $typeLabel; ?></span>
                    <span class="author-name-created"><?php echo $admin->first_name ?? 'NA' ;?> <?php echo $admin->last_name ?? 'NA' ;?></span>
                    <span class="author-updated-date-time"><?php echo date('d/m/Y h:i A', strtotime($list->updated_at));?></span>
                </div>

                <!--<div class="note-content-redesign"><?php //echo nl2br(htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'));?></div>-->
                <div class="note-content-redesign">
                    <?php 
                    if (!empty($list->description)) {
                        $description = $list->description;

                        // Check for unwanted Word/Office XML markup
                        if (strpos($description, '<xml>') !== false || strpos($description, '<o:OfficeDocumentSettings>') !== false) {
                            $finalDescription = htmlentities($description);
                        } else {
                            $finalDescription = $description;
                        }
                    } else {
                        $finalDescription = '';
                    }
                    ?>
                    <?php echo $finalDescription; ?>
                </div>
                <div class="note-toggle-btn-div">
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle note-toggle-btn-div-type" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item opennoteform" data-id="<?php echo $list->id;?>" href="javascript:;">Edit</a>
                            <?php if( Auth::user()->role == 1 || Auth::user()->role == 16 ) { ?>
                                <a class="dropdown-item editdatetime" data-id="<?php echo $list->id;?>" href="javascript:;">Edit Date Time</a>
                            <?php }?>

                            <a data-id="<?php echo $list->id;?>"  data-href="deletenote" class="dropdown-item deletenote" href="javascript:;">Delete</a>
                            <?php if($list->pin == 1) { ?>
                                <a data-id="<?php echo $list->id;?>" class="dropdown-item pinnote" href="javascript:;">Unpin</a>
                            <?php } else { ?>
                                <a data-id="<?php echo $list->id;?>" class="dropdown-item pinnote" href="javascript:;">Pin</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        return ob_get_clean();
    }

	public function deletenote(Request $request){
		$note_id = $request->note_id;
		if(\App\Models\Note::where('id',$note_id)->exists()){
			$data = \App\Models\Note::select('client_id','title','description')->where('id',$note_id)->first();
			$res = DB::table('notes')->where('id', @$note_id)->delete();
			if($res){
				if($data == 'client'){
                    $subject = 'deleted a note';

                    $objs = new ActivitiesLog;
                    $objs->client_id = $data->client_id;
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '<span class="text-semi-bold">'.$data->title.'</span><p>'.$data->description.'</p>';
                    $objs->subject = $subject;
                    $objs->save();
				}
			    $response['status'] 	= 	true;
			    $response['data']	=	$data;
			}else{
				$response['status'] 	= 	false;
			    $response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function deletecostagreement(Request $request){
		$cost_agreement_id = $request->cost_agreement_id;
		
		// Check if the cost agreement exists
		if(\App\Models\CostAssignmentForm::where('id', $cost_agreement_id)->exists()){
			$costAgreement = \App\Models\CostAssignmentForm::select('id', 'client_id', 'agent_id')->where('id', $cost_agreement_id)->first();
			
			// Check if user has permission to delete (roles: 1, 12, 16)
			$userRole = Auth::user()->role;
			if(!in_array($userRole, [1, 12, 16])){
				$response['status'] = false;
				$response['message'] = 'You do not have permission to delete this Cost Agreement.';
				echo json_encode($response);
				return;
			}
			
			// Delete the cost agreement
			$res = \App\Models\CostAssignmentForm::where('id', $cost_agreement_id)->delete();
			
			if($res){
				// Log the activity
				$objs = new \App\Models\ActivitiesLog;
				$objs->client_id = $costAgreement->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = 'Deleted Cost Assignment Form (ID: ' . $cost_agreement_id . ')';
				$objs->subject = 'deleted a cost assignment form';
				$objs->save();
				
				$response['status'] = true;
				$response['message'] = 'Cost Agreement deleted successfully.';
			} else {
				$response['status'] = false;
				$response['message'] = 'Failed to delete Cost Agreement. Please try again.';
			}
		} else {
			$response['status'] = false;
			$response['message'] = 'Cost Agreement not found.';
		}
		
		echo json_encode($response);
	}



    //Add personal Doc checklist
    public function addedudocchecklist(Request $request){
        $clientid = $request->clientid;
        $admin_info1 = \App\Models\Admin::select('client_id')->where('id', $clientid)->first();
        if(!empty($admin_info1)){
            $client_unique_id = $admin_info1->client_id;
        } else {
            $client_unique_id = "";
        }
        $doctype = isset($request->doctype)? $request->doctype : '';

        if ($request->has('checklist'))
        {
            $checklistArray = $request->input('checklist');
            if (is_array($checklistArray))
            {
                foreach ($checklistArray as $item)
                {
                    $obj = new \App\Models\Document;
                    $obj->user_id = Auth::user()->id;
                    $obj->client_id = $clientid;
                    $obj->type = $request->type;
                    $obj->doc_type = $doctype;
                    $obj->folder_name = $request->folder_name;
                    $obj->checklist = $item;
                    $saved = $obj->save();
                } //end foreach

                if($saved)
                {
                    if($request->type == 'client'){
                        $subject = 'added personal checklist';
                        $objs = new ActivitiesLog;
                        $objs->client_id = $clientid;
                        $objs->created_by = Auth::user()->id;
                        $objs->description = '';
                        $objs->subject = $subject;
                        $objs->save();
                    }

                    $response['status'] 	= 	true;
                    $response['message']	=	'You’ve successfully added your personal checklist';

                    $fetchd = \App\Models\Document::where('client_id',$clientid)->whereNull('not_used_doc')->where('doc_type',$doctype)->where('type',$request->type)->where('folder_name',$request->folder_name)->orderby('updated_at', 'DESC')->get();
                    ob_start();
                    foreach($fetchd as $docKey=>$fetch)
                    {
                        $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                        ?>
                        <tr class="drow" id="id_<?php echo $fetch->id; ?>">
                            <td><?php echo $docKey+1;?></td>
                            <td style="white-space: initial;">
                                <div data-id="<?php echo $fetch->id;?>" data-personalchecklistname="<?php echo $fetch->checklist; ?>" class="personalchecklist-row">
                                    <span><?php echo $fetch->checklist; ?></span>
                                </div>
                            </td>
                            <td style="white-space: initial;">
                                <?php
                                echo $admin->first_name. "<br>";
                                echo date('d/m/Y', strtotime($fetch->created_at));
                                ?>
                            </td>
                            <td style="white-space: initial;">
                                <?php
                                if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
                                    <div data-id="<?php echo $fetch->id; ?>" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
                                        <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-<?php echo $request->folder_name;?>')">
                                            <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                        </a>
                                    </div>
                                <?php
                                }
                                else
                                {?>
                                    <div class="upload_document" style="display:inline-block;">
                                        <form method="POST" enctype="multipart/form-data" id="upload_form_<?php echo $fetch->id;?>">
                                            <input type="hidden" name="_token" value="<?php echo csrf_token();?>" />
                                            <input type="hidden" name="clientid" value="<?php echo $clientid;?>">
                                            <input type="hidden" name="fileid" value="<?php echo $fetch->id;?>">
                                            <input type="hidden" name="type" value="client">
                                            <input type="hidden" name="doctype" value="personal">
                                            <input type="hidden" name="doccategory" value="<?php echo $request->doccategory;?>">
                                            <a href="javascript:;" class="btn btn-primary add-document" data-fileid="<?php echo $fetch->id;?>"><i class="fa fa-plus"></i> Add Document</a>
                                            <input class="docupload" data-fileid="<?php echo $fetch->id;?>" data-doccategory="<?php echo $request->doccategory;?>" type="file" name="document_upload" style="display: none;">
                                        </form>
                                    </div>
                                <?php
                                }?>
                            </td>
                            <td style="white-space: initial;">
                                <?php
                                if( isset($fetch->file_name) && $fetch->file_name !="")
                                { ?>
                                    <div class="dropdown d-inline">
                                        <button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">Action</button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item renamechecklist" href="javascript:;">Rename Checklist</a>
                                            <a class="dropdown-item renamedoc" href="javascript:;">Rename File Name</a>
                                            <?php
                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                            ?>
                                            <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                            <?php
                                            $explodeimg = explode('.',$fetch->myfile);
                                            if($explodeimg[1] == 'jpg'|| $explodeimg[1] == 'png'|| $explodeimg[1] == 'jpeg'){
                                            ?>
                                                <a target="_blank" class="dropdown-item" href="<?php echo \URL::to('/admin/document/download/pdf'); ?>/<?php echo $fetch->id; ?>">PDF</a>
                                            <?php } ?>
                                            <a href="#" class="dropdown-item download-file" data-filelink="<?php echo $fetch->myfile; ?>" data-filename="<?php echo $fetch->myfile_key; ?>">Download</a>

                                            <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item notuseddoc" data-doctype="personal" data-doccategory="<?php echo $request->doccategory;?>" data-href="notuseddoc" href="javascript:;">Not Used</a>

                                            <?php
                                            if (strtolower($fetch->filetype) === 'pdf')
                                            {
                                                if ($fetch->status === 'draft'){
                                                    $url1 = route('documents.edit', $fetch->id);
                                                ?>
                                                    <a target="_blank" href="<?php echo $url1;?>" class="dropdown-item">Send To Signature</a>
                                                <?php
                                                }

                                                if($fetch->status === 'sent') {

                                                    $url2 = route('documents.index', $fetch->id);
                                                ?>
                                                    <a target="_blank" href="<?php echo $url2;?>" class="dropdown-item">Check To Signature</a>
                                                <?php
                                                }

                                                if($fetch->status === 'signed') {
                                                    $url3 = route('download.signed', $fetch->id);
                                                ?>
                                                    <a target="_blank" href="<?php echo $url3;?>" class="dropdown-item">Download Signed</a>
                                                <?php
                                                }
                                            }?>

                                        </div>
                                    </div>
                                <?php
                                }?>
                            </td>
                        </tr>
			        <?php
			        } //end foreach

                    $data = ob_get_clean();
                    ob_start();
                    foreach($fetchd as $fetch)
                    {
                        $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                        ?>
                        <div class="grid_list">
                            <div class="grid_col">
                                <div class="grid_icon">
                                    <i class="fas fa-file-image"></i>
                                </div>
                                <div class="grid_content">
                                    <span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
                                    <div class="dropdown d-inline dropdown_ellipsis_icon">
                                        <a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu">
                                            <?php
                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                            ?>
                                            <?php if( isset($fetch->myfile) && $fetch->myfile != ""){?>
                                            <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                            <a href="#" class="dropdown-item download-file" data-filelink="<?php echo $fetch->myfile; ?>" data-filename="<?php echo $fetch->myfile_key; ?>">Download</a>

                                            <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item notuseddoc" data-doctype="personal" data-doccategory="<?php echo $request->folder_name;?>" data-href="notuseddoc" href="javascript:;">Not Used</a>
                                            <?php
                                            if (strtolower($fetch->filetype) === 'pdf')
                                            {
                                                if ($fetch->status === 'draft'){
                                                    $url1 = route('documents.edit', $fetch->id);
                                                ?>
                                                    <a target="_blank" href="<?php echo $url1;?>" class="dropdown-item">Send To Signature</a>
                                                <?php
                                                }

                                                if($fetch->status === 'sent') {

                                                    $url2 = route('documents.index', $fetch->id);
                                                ?>
                                                    <a target="_blank" href="<?php echo $url2;?>" class="dropdown-item">Check To Signature</a>
                                                <?php
                                                }

                                                if($fetch->status === 'signed') {
                                                    $url3 = route('download.signed', $fetch->id);
                                                ?>
                                                    <a target="_blank" href="<?php echo $url3;?>" class="dropdown-item">Download Signed</a>
                                                <?php
                                                }
                                            }?>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    } //end foreach
                    $griddata = ob_get_clean();
                    $response['data']	= $data;
                    $response['griddata'] = $griddata;
                } //end if
                else
                {
                    $response['status'] = false;
                    $response['message'] = 'Please try again';
                } //end else
            } //end if
            else
            {
                $response['status'] = false;
                $response['message'] = 'Please try again';
            } //end else
        }
        else
        {
            $response['status'] = false;
            $response['message'] = 'Please try again';
        } //end else
        echo json_encode($response);
	}
    
    //Update personal Document upload
    /*public function  uploadedudocument(Request $request) {
        // Start output buffering to catch any accidental output
        ob_start();

        $response = ['status' => false, 'message' => 'Please try again', 'data' => '', 'griddata' => ''];
        $clientid = $request->clientid;
        $admin_info1 = \App\Models\Admin::select('client_id')->where('id', $clientid)->first();
        $client_unique_id = !empty($admin_info1) ? $admin_info1->client_id : "";
        $doctype = $request->doctype ?? '';

        try {
            if ($request->hasfile('document_upload')) {
                $file = $request->file('document_upload');
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();

                if (!preg_match('/^[a-zA-Z0-9_\-\.\s\$]+$/', $fileName)) {
                    $response['message'] = 'File name can only contain letters, numbers, dashes (-), underscores (_), spaces, dots (.), and dollar signs ($). Please rename the file and try again.';
                } else {
                    $originalName = $file->getClientOriginalName();
                    $name = time() . '_' . $originalName;
                    $filePath = $client_unique_id . '/' . $doctype . '/' . $name;
                    Storage::disk('s3')->put($filePath, file_get_contents($file));

                    // Use pathinfo
                    $originalInfo = pathinfo($originalName);
                    $savedInfo = pathinfo($name);

                    $req_file_id = $request->fileid;
                    $obj = \App\Models\Document::find($req_file_id);
                    if ($obj) {
                        $obj->file_name = $originalInfo['filename']; // e.g., "passport" (without extension)
                        $obj->filetype = $savedInfo['extension'];    // e.g., "pdf"
                        $obj->user_id = Auth::user()->id;
                        $fileUrl = Storage::disk('s3')->url($filePath);
                        $obj->myfile = $fileUrl;
                        $obj->myfile_key = $name;
                        $obj->type = $request->type;
                        $obj->file_size = $size;
                        $obj->doc_type = $doctype;
                        $saved = $obj->save();

                        if ($saved && $request->type == 'client') {
                            $subject = 'updated personal document';
                            $objs = new ActivitiesLog;
                            $objs->client_id = $clientid;
                            $objs->created_by = Auth::user()->id;
                            $objs->description = '';
                            $objs->subject = $subject;
                            $objs->save();
                        }

                        if ($saved) {
                            $response['status'] = true;
                            $response['message'] = 'File uploaded successfully';
                            $response['filename'] = $fileName;
                            $response['filetype'] = $savedInfo['extension'];
                            $response['fileurl'] = $fileUrl;
                            $response['filekey'] = $name;
                            $PersonalDocumentTypeInfo = \App\Models\PersonalDocumentType::find($request->doccategory);
                            $response['doccategory'] = $PersonalDocumentTypeInfo ? $PersonalDocumentTypeInfo->title : '';
                        }
                    } else {
                        $response['message'] = 'Document record not found.';
                    }
                }
            }
        } catch (\Exception $e) {
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }

        // Clear any output buffer and send only the JSON response
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }*/

    public function uploadedudocument(Request $request) {  //dd($request->all());
        ob_start();
    
        $response = ['status' => false, 'message' => 'Please try again', 'data' => '', 'griddata' => ''];
        $clientid = $request->clientid;
        $admin_info1 = \App\Models\Admin::select('client_id', 'first_name')->where('id', $clientid)->first();
        $client_unique_id = !empty($admin_info1) ? $admin_info1->client_id : "";
        $client_first_name = !empty($admin_info1) ? preg_replace('/[^a-zA-Z0-9_\-]/', '_', $admin_info1->first_name) : "client";
    
        $doctype = $request->doctype ?? '';
    
        try {
            if ($request->hasfile('document_upload')) {
                $file = $request->file('document_upload');
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
    
                if (!preg_match('/^[a-zA-Z0-9_\-\.\s\$]+$/', $fileName)) {
                    $response['message'] = 'File name can only contain letters, numbers, dashes (-), underscores (_), spaces, dots (.), and dollar signs ($). Please rename the file and try again.';
                } else {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
    
                    // Get checklist (doc category)
                    $req_file_id = $request->fileid;
                    $obj = \App\Models\Document::find($req_file_id);
                    $checklistName = $obj->checklist;
                    // Build new file name: firstname_checklist_currentdate.ext
                    //$name = $client_first_name . "_" . $checklistName . "_" . date('d/m/Y') . "." . $extension;
                    $name = $client_first_name . "_" . $checklistName . "_" . time() . "." . $extension;
    
                    $filePath = $client_unique_id . '/' . $doctype . '/' . $name;
                    Storage::disk('s3')->put($filePath, file_get_contents($file));
    
                    $req_file_id = $request->fileid;
                    $obj = \App\Models\Document::find($req_file_id);
                    if ($obj) {
                        //$obj->file_name = pathinfo($name, PATHINFO_FILENAME);
                        $obj->file_name = $client_first_name . "_" . $checklistName . "_" . time();
                        $obj->filetype = $extension;
                        $obj->user_id = Auth::user()->id;
                        $fileUrl = Storage::disk('s3')->url($filePath);
                        $obj->myfile = $fileUrl;
                        $obj->myfile_key = $name;
                        $obj->type = $request->type;
                        $obj->file_size = $size;
                        $obj->doc_type = $doctype;
                        $saved = $obj->save();
    
                        if ($saved && $request->type == 'client') {
                            $subject = 'updated personal document';
                            $objs = new ActivitiesLog;
                            $objs->client_id = $clientid;
                            $objs->created_by = Auth::user()->id;
                            $objs->description = '';
                            $objs->subject = $subject;
                            $objs->save();
                        }
    
                        if ($saved) {
                            $response['status'] = true;
                            $response['message'] = 'File uploaded successfully';
                            $response['filename'] = $name;//$fileName;
                            $response['filetype'] = $extension;
                            $response['fileurl'] = $fileUrl;
                            $response['filekey'] = $name;
                            $response['doccategory'] = $checklistName;
                        }
                    } else {
                        $response['message'] = 'Document record not found.';
                    }
                }
            }
        } catch (\Exception $e) {
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }
    
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function addvisadocchecklist(Request $request){ //dd($request->all());
        $clientid = $request->clientid;
        $admin_info1 = \App\Models\Admin::select('client_id')->where('id', $clientid)->first();
        if(!empty($admin_info1)){
            $client_unique_id = $admin_info1->client_id;
        } else {
            $client_unique_id = "";
        } //dd($client_unique_id);

        $doctype = isset($request->doctype)? $request->doctype : '';
        if ($request->has('visa_checklist'))
        {
            $checklistArray = $request->input('visa_checklist'); //dd($checklistArray);
            if (is_array($checklistArray))
            {
                foreach ($checklistArray as $item)
                {
                    $obj = new \App\Models\Document;
                    $obj->user_id = Auth::user()->id;
                    $obj->client_id = $clientid;
                    $obj->type = $request->type;
                    $obj->doc_type = $doctype;
                    $obj->client_matter_id = $request->client_matter_id;
                    $obj->checklist = $item;
                    $obj->folder_name = $request->folder_name;
                    $saved = $obj->save();
                }  //end foreach

                if($saved)
                {
                    if($request->type == 'client'){
                        $subject = 'added visa checklist';
                        $objs = new ActivitiesLog;
                        $objs->client_id = $clientid;
                        $objs->created_by = Auth::user()->id;
                        $objs->description = '';
                        $objs->subject = $subject;
                        $objs->save();
                    }

                    //Update date in client matter table
                    if( isset($request->client_matter_id) && $request->client_matter_id != ""){
                        $obj1 = \App\Models\ClientMatter::find($request->client_matter_id);
                        $obj1->updated_at = date('Y-m-d H:i:s');
                        $obj1->save();
                    }
                    $response['status'] 	= 	true;
                    $response['message']	=	'You have added uploaded your visa checklist';

                    $fetchd = \App\Models\Document::where('client_id',$clientid)->whereNull('not_used_doc')->where('doc_type',$doctype)->where('type',$request->type)->orderby('updated_at', 'DESC')->get();
                    ob_start();
                    foreach($fetchd as $visaKey=>$fetch)
                    {
                        $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                        $VisaDocumentType = \App\Models\VisaDocumentType::where('id', $fetch->folder_name)->first();
                        if (
                            $request->client_matter_id != $fetch->client_matter_id ||
                            $request->folder_name != $fetch->folder_name
                        ) {
                            $showCls = "style='display: none;'";
                        } else {
                            $showCls = "";
                        }
                        ?>
                        <tr class="drow" data-matterid="<?php echo $fetch->client_matter_id;?>" data-catid="<?php echo $fetch->folder_name;?>" id="id_<?php echo $fetch->id; ?>" <?php echo $showCls;?>>
                            <td><?php echo $visaKey+1;?></td>
                            <td style="white-space: initial;">
                                <div data-id="<?php echo $fetch->id;?>" data-visachecklistname="<?php echo $fetch->checklist; ?>" class="visachecklist-row">
                                    <span><?php echo $fetch->checklist; ?></span>
                                </div>
                            </td>
                            <td style="white-space: initial;">
                                <?php
                                echo $admin->first_name. "<br>";
                                echo date('d/m/Y', strtotime($fetch->created_at));
                                ?>
                            </td>
                            <td style="white-space: initial;">
                                <?php
                                if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
                                    <div data-id="<?php echo $fetch->id; ?>" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
                                        <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-migdocumnetlist')">
                                            <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                        </a>
                                    </div>
                                <?php
                                }
                                else
                                {?>
                                    <div class="migration_upload_document" style="display:inline-block;">
                                        <form method="POST" enctype="multipart/form-data" id="mig_upload_form_<?php echo $fetch->id;?>">
                                            <input type="hidden" name="_token" value="<?php echo csrf_token();?>" />
                                            <input type="hidden" name="clientid" value="<?php echo $fetch->client_id;?>">
                                            <input type="hidden" name="client_matter_id" value="<?php echo $fetch->client_matter_id;?>">
                                            <input type="hidden" name="fileid" value="<?php echo $fetch->id;?>">
                                            <input type="hidden" name="type" value="client">
                                            <input type="hidden" name="doctype" value="visa">
                                            <input type="hidden" name="doccategory" value="<?php echo $VisaDocumentType->title; ?>">
                                            
                                            <a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
                                            <input class="migdocupload" data-fileid="<?php echo $fetch->id;?>" data-doccategory="<?php echo $fetch->folder_name;?>" type="file" name="document_upload"/>
                                        </form>
                                    </div>
                                <?php
                                }?>
                            </td>

                            <td style="white-space: initial;">
                                <?php
                                if( isset($fetch->file_name) && $fetch->file_name !="")
                                { ?>
                                <div class="dropdown d-inline">
                                    <button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">Action</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item renamechecklist" href="javascript:;">Rename Checklist</a>
                                        <a class="dropdown-item renamedoc" href="javascript:;">Rename File Name</a>

                                        <?php
                                        $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                        ?>
                                        <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                        <?php
                                        $explodeimg = explode('.',$fetch->myfile);
                                        if($explodeimg[1] == 'jpg'|| $explodeimg[1] == 'png'|| $explodeimg[1] == 'jpeg')
                                        {
                                        ?>
                                            <a target="_blank" class="dropdown-item" href="<?php echo \URL::to('/admin/document/download/pdf'); ?>/<?php echo $fetch->id; ?>">PDF</a>
                                        <?php
                                        } ?>
                                        <a href="#" class="dropdown-item download-file" data-filelink="<?php echo $fetch->myfile; ?>" data-filename="<?php echo $fetch->myfile_key; ?>">Download</a>
                                        <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item notuseddoc" data-doctype="visa" data-href="notuseddoc" href="javascript:;">Not Used</a>

                                        <?php
                                        if (strtolower($fetch->filetype) === 'pdf')
                                        {
                                            if ($fetch->status === 'draft'){
                                                $url1 = route('documents.edit', $fetch->id);
                                            ?>
                                                <a target="_blank" href="<?php echo $url1;?>" class="dropdown-item">Send To Signature</a>
                                            <?php
                                            }

                                            if($fetch->status === 'sent') {

                                                $url2 = route('documents.index', $fetch->id);
                                            ?>
                                                <a target="_blank" href="<?php echo $url2;?>" class="dropdown-item">Check To Signature</a>
                                            <?php
                                            }

                                            if($fetch->status === 'signed') {
                                                $url3 = route('download.signed', $fetch->id);
                                            ?>
                                                <a target="_blank" href="<?php echo $url3;?>" class="dropdown-item">Download Signed</a>
                                            <?php
                                            }
                                        }?>
                                    </div>
                                </div>
                                <?php
                                }?>
                            </td>
                        </tr>
                    <?php
                    } //end foreach

                    $data = ob_get_clean();
                    ob_start();
                    foreach($fetchd as $fetch)
                    {
                        $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                        ?>
                        <div class="grid_list">
                            <div class="grid_col">
                                <div class="grid_icon">
                                    <i class="fas fa-file-image"></i>
                                </div>
                                <div class="grid_content">
                                    <span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
                                    <div class="dropdown d-inline dropdown_ellipsis_icon">
                                        <a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu">
                                            <?php
                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                            ?>
                                            <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                            <a href="#" class="dropdown-item download-file" data-filelink="<?php echo $fetch->myfile; ?>" data-filename="<?php echo $fetch->myfile_key; ?>">Download</a>

                                            <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item notuseddoc" data-doctype="visa" data-href="notuseddoc" href="javascript:;">Not Used</a>
                                            <?php
                                            if (strtolower($fetch->filetype) === 'pdf')
                                            {
                                                if ($fetch->status === 'draft'){
                                                    $url1 = route('documents.edit', $fetch->id);
                                                ?>
                                                    <a target="_blank" href="<?php echo $url1;?>" class="dropdown-item">Send To Signature</a>
                                                <?php
                                                }

                                                if($fetch->status === 'sent') {

                                                    $url2 = route('documents.index', $fetch->id);
                                                ?>
                                                    <a target="_blank" href="<?php echo $url2;?>" class="dropdown-item">Check To Signature</a>
                                                <?php
                                                }

                                                if($fetch->status === 'signed') {
                                                    $url3 = route('download.signed', $fetch->id);
                                                ?>
                                                    <a target="_blank" href="<?php echo $url3;?>" class="dropdown-item">Download Signed</a>
                                                <?php
                                                }
                                            }?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    } //end foreach
                    $griddata = ob_get_clean();
                    $response['data']	= $data;
                    $response['griddata'] = $griddata;
                } //end if
                else
                {
                    $response['status'] = false;
                    $response['message'] = 'Please try again';
                } //end else
            } //end if
            else
            {
                $response['status'] = false;
                $response['message'] = 'Please try again';
            } //end else
        }
        else
        {
            $response['status'] = false;
            $response['message'] = 'Please try again';
        } //end else
        echo json_encode($response);
	}

    /*public function uploadvisadocument(Request $request){  //dd($request->all());
        $clientid = $request->clientid;
        $admin_info1 = \App\Models\Admin::select('client_id', 'first_name')->where('id', $clientid)->first();
        $client_unique_id = !empty($admin_info1) ? $admin_info1->client_id : "";
        $client_first_name = !empty($admin_info1) ? preg_replace('/[^a-zA-Z0-9_\-]/', '_', $admin_info1->first_name) : "client";
    
        $doctype = isset($request->doctype)? $request->doctype : '';
        if ($request->hasfile('document_upload')) {
            $file = $request->file('document_upload');
            $size = $file->getSize();
            $fileName = $file->getClientOriginalName();
            // Allow only letters, numbers, underscores, dashes, spaces, dots, and dollar signs
            if (!preg_match('/^[a-zA-Z0-9_\-\.\s\$]+$/', $fileName)) {
                $response['status'] = false;
                $response['message'] = 'File name can only contain letters, numbers, dashes (-), underscores (_), spaces, dots (.), and dollar signs ($). Please rename the file and try again.';
                echo json_encode($response);
                exit;
            }

            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            // Get checklist (doc category)
            $req_file_id = $request->fileid;
            $obj = \App\Models\Document::find($req_file_id);
            $checklistName = $obj->checklist;
            // Build new file name: firstname_checklist_timestamp.ext
            $name = $client_first_name . "_" . $checklistName . "_" . time() . "." . $extension;

            $filePath = $client_unique_id . '/' . $doctype . '/' . $name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));

            $obj->file_name = pathinfo($name, PATHINFO_FILENAME);
            $obj->filetype = $extension;
            $obj->user_id = Auth::user()->id;
            $fileUrl = Storage::disk('s3')->url($filePath);
            $obj->myfile = $fileUrl;
            $obj->myfile_key = $name;
            $obj->type = $request->type;
            $obj->file_size = $size;
            $obj->doc_type = $doctype;
            $saved = $obj->save();
            if($saved){
                if($request->type == 'client'){
                    $subject = 'updated visa document';
                    $objs = new ActivitiesLog;
                    $objs->client_id = $clientid;
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();
                }

                //Update date in client matter table
                if( isset($request->client_matter_id) && $request->client_matter_id != ""){
                    $obj1 = \App\Models\ClientMatter::find($request->client_matter_id);
                    $obj1->updated_at = date('Y-m-d H:i:s');
                    $obj1->save();
                }
                $response['status'] 	= 	true;
                $response['message']	=	'You have successfully uploaded your visa document';

                $fetchd = \App\Models\Document::where('client_id',$clientid)->whereNull('not_used_doc')->where('doc_type',$doctype)->where('type',$request->type)->orderby('updated_at', 'DESC')->get();
                ob_start();
                foreach($fetchd as $visaKey=>$fetch)
                {
                    $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                    $VisaDocumentType = \App\Models\VisaDocumentType::where('id', $fetch->folder_name)->first();
                    if (
                        $request->client_matter_id != $fetch->client_matter_id ||
                        $request->visa_doc_cat != $fetch->folder_name
                    ) {
                        $showCls = "style='display: none;'";
                    } else {
                        $showCls = "";
                    }
                    ?>
                    <tr class="drow" data-matterid="<?php echo $fetch->client_matter_id;?>" data-catid="<?php echo $fetch->folder_name;?>" id="id_<?php echo $fetch->id; ?>" <?php echo $showCls;?>>
                        <td><?php echo $visaKey+1;?></td>
                        <td style="white-space: initial;">
                            <div data-id="<?php echo $fetch->id;?>" data-visachecklistname="<?php echo $fetch->checklist; ?>" class="visachecklist-row">
                                <span><?php echo $fetch->checklist; ?></span>
                            </div>
                        </td>
                        <td style="white-space: initial;">
                            <?php
                            echo $admin->first_name. "<br>";
                            echo date('d/m/Y', strtotime($fetch->created_at));
                            ?>
                        </td>
                        <td style="white-space: initial;">
                            <?php
                            if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
                                <div data-id="<?php echo $fetch->id; ?>" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
                                    <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-migdocumnetlist')">
                                        <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                    </a>
                                </div>
                            <?php
                            }
                            else
                            {?>
                                <div class="migration_upload_document" style="display:inline-block;">
                                    <form method="POST" enctype="multipart/form-data" id="mig_upload_form_<?php echo $fetch->id;?>">
                                        <input type="hidden" name="_token" value="<?php echo csrf_token();?>" />
                                        <input type="hidden" name="clientid" value="<?php echo $fetch->client_id;?>">
                                        <input type="hidden" name="client_matter_id" value="<?php echo $fetch->client_matter_id;?>">
                                        <input type="hidden" name="fileid" value="<?php echo $fetch->id;?>">
                                        <input type="hidden" name="type" value="client">
                                        <input type="hidden" name="doctype" value="visa">
                                        <input type="hidden" name="doccategory" value="<?php echo $VisaDocumentType->title; ?>">
                                        <a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
                                        <input class="migdocupload" data-fileid="<?php echo $fetch->id;?>" data-doccategory="<?php echo $fetch->folder_name;?>" type="file" name="document_upload"/>
                                    </form>
                                </div>
                            <?php
                            }?>
                        </td>

                        <td style="white-space: initial;">
                            <?php
                            if( isset($fetch->file_name) && $fetch->file_name !="")
                            { ?>
                            <div class="dropdown d-inline">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">Action</button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item renamechecklist" href="javascript:;">Rename Checklist</a>
                                    <a class="dropdown-item renamedoc" href="javascript:;">Rename File Name</a>

                                    <?php
                                    $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                    ?>
                                    <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                    <?php
                                    $explodeimg = explode('.',$fetch->myfile);
                                    if($explodeimg[1] == 'jpg'|| $explodeimg[1] == 'png'|| $explodeimg[1] == 'jpeg')
                                    {
                                    ?>
                                        <a target="_blank" class="dropdown-item" href="<?php echo \URL::to('/admin/document/download/pdf'); ?>/<?php echo $fetch->id; ?>">PDF</a>
                                    <?php
                                    } ?>
                                    <a href="#" class="dropdown-item download-file" data-filelink="<?php echo $fetch->myfile; ?>" data-filename="<?php echo $fetch->myfile_key; ?>">Download</a>
                                    <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item notuseddoc" data-doctype="visa" data-href="notuseddoc" href="javascript:;">Not Used</a>

                                    <?php
                                    if (strtolower($fetch->filetype) === 'pdf')
                                    {
                                        if ($fetch->status === 'draft'){
                                            $url1 = route('documents.edit', $fetch->id);
                                        ?>
                                            <a target="_blank" href="<?php echo $url1;?>" class="dropdown-item">Send To Signature</a>
                                        <?php
                                        }

                                        if($fetch->status === 'sent') {

                                            $url2 = route('documents.index', $fetch->id);
                                        ?>
                                            <a target="_blank" href="<?php echo $url2;?>" class="dropdown-item">Check To Signature</a>
                                        <?php
                                        }

                                        if($fetch->status === 'signed') {
                                            $url3 = route('download.signed', $fetch->id);
                                        ?>
                                            <a target="_blank" href="<?php echo $url3;?>" class="dropdown-item">Download Signed</a>
                                        <?php
                                        }
                                    }?>
                                </div>
                            </div>
                            <?php
                            }?>
                        </td>
                    </tr>
                    <?php
                }
                $data = ob_get_clean();
                ob_start();
                foreach($fetchd as $fetch){
                    $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                    ?>
                    <div class="grid_list">
                        <div class="grid_col">
                            <div class="grid_icon">
                                <i class="fas fa-file-image"></i>
                            </div>
                            <div class="grid_content">
                                <span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
                                <div class="dropdown d-inline dropdown_ellipsis_icon">
                                    <a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                    <div class="dropdown-menu">
                                        <?php
                                        $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                        ?>
                                        <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                        <a href="#" class="dropdown-item download-file" data-filelink="<?php echo $fetch->myfile; ?>" data-filename="<?php echo $fetch->myfile_key; ?>">Download</a>
                                        <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item notuseddoc" data-doctype="visa" data-href="notuseddoc" href="javascript:;">Not Used</a>

                                        <?php
                                        if (strtolower($fetch->filetype) === 'pdf')
                                        {
                                            if ($fetch->status === 'draft'){
                                                $url1 = route('documents.edit', $fetch->id);
                                            ?>
                                                <a target="_blank" href="<?php echo $url1;?>" class="dropdown-item">Send To Signature</a>
                                            <?php
                                            }

                                            if($fetch->status === 'sent') {

                                                $url2 = route('documents.index', $fetch->id);
                                            ?>
                                                <a target="_blank" href="<?php echo $url2;?>" class="dropdown-item">Check To Signature</a>
                                            <?php
                                            }

                                            if($fetch->status === 'signed') {
                                                $url3 = route('download.signed', $fetch->id);
                                            ?>
                                                <a target="_blank" href="<?php echo $url3;?>" class="dropdown-item">Download Signed</a>
                                            <?php
                                            }
                                        }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
                $griddata = ob_get_clean();
                $response['data']	= $data;
                $response['griddata'] = $griddata;
                $response['visa_doc_cat'] = $request->visa_doc_cat;
            }else{
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
            }
        }else{
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
        }
        echo json_encode($response);
    }*/

    public function uploadvisadocument(Request $request){  //dd($request->all());
        ob_start();
        
        $response = ['status' => false, 'message' => 'Please try again', 'data' => '', 'griddata' => ''];
        $clientid = $request->clientid;
        $admin_info1 = \App\Models\Admin::select('client_id', 'first_name')->where('id', $clientid)->first();
        $client_unique_id = !empty($admin_info1) ? $admin_info1->client_id : "";
        $client_first_name = !empty($admin_info1) ? preg_replace('/[^a-zA-Z0-9_\-]/', '_', $admin_info1->first_name) : "client";
    
        $doctype = isset($request->doctype)? $request->doctype : '';
        
        try {
            if ($request->hasfile('document_upload')) {
                $file = $request->file('document_upload');
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
                
                // Allow only letters, numbers, underscores, dashes, spaces, dots, and dollar signs
                if (!preg_match('/^[a-zA-Z0-9_\-\.\s\$]+$/', $fileName)) {
                    $response['message'] = 'File name can only contain letters, numbers, dashes (-), underscores (_), spaces, dots (.), and dollar signs ($). Please rename the file and try again.';
                } else {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();

                    // Get checklist (doc category)
                    $req_file_id = $request->fileid;
                    $obj = \App\Models\Document::find($req_file_id);
                    $checklistName = $obj->checklist;
                    // Build new file name: firstname_checklist_timestamp.ext
                    $name = $client_first_name . "_" . $checklistName . "_" . time() . "." . $extension;
                    //$name = $client_first_name . "_" . $checklistName . "_" . date('d/m/Y') . "." . $extension;

                    $filePath = $client_unique_id . '/' . $doctype . '/' . $name;
                    Storage::disk('s3')->put($filePath, file_get_contents($file));

                    //$obj->file_name = pathinfo($name, PATHINFO_FILENAME);
                    $obj->file_name = $client_first_name . "_" . $checklistName . "_" . time();
                    $obj->filetype = $extension;
                    $obj->user_id = Auth::user()->id;
                    $fileUrl = Storage::disk('s3')->url($filePath);
                    $obj->myfile = $fileUrl;
                    $obj->myfile_key = $name;
                    $obj->type = $request->type;
                    $obj->file_size = $size;
                    $obj->doc_type = $doctype;
                    $saved = $obj->save();
                    
                    if($saved){
                        if($request->type == 'client'){
                            $subject = 'updated visa document';
                            $objs = new ActivitiesLog;
                            $objs->client_id = $clientid;
                            $objs->created_by = Auth::user()->id;
                            $objs->description = '';
                            $objs->subject = $subject;
                            $objs->save();
                        }

                        //Update date in client matter table
                        if( isset($request->client_matter_id) && $request->client_matter_id != ""){
                            $obj1 = \App\Models\ClientMatter::find($request->client_matter_id);
                            $obj1->updated_at = date('Y-m-d H:i:s');
                            $obj1->save();
                        }
                        
                        $response['status'] = true;
                        $response['message'] = 'You have successfully uploaded your visa document';
                        $response['filename'] = $name;
                        $response['filetype'] = $extension;
                        $response['fileurl'] = $fileUrl;
                        $response['filekey'] = $name;
                        $response['doccategory'] = $checklistName;
                        $response['doctype'] = $doctype;
                        $response['visa_doc_cat'] = $request->visa_doc_cat;
                    } else {
                        $response['message'] = 'Failed to save document record.';
                    }
                }
            }
        } catch (\Exception $e) {
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }
        
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
  


	/*public function renamedoc(Request $request){
		$id = $request->id;
		$filename = $request->filename;
		if(\App\Models\Document::where('id',$id)->exists()){
			$doc = \App\Models\Document::where('id',$id)->first();
			$res = DB::table('documents')->where('id', @$id)->update(['file_name' => $filename]);
			if($res){
				$response['status'] 	= 	true;
				$response['data']	=	'Document saved successfully';
				$response['Id']	=	$id;
				$response['filename']	=	$filename;
				$response['filetype']	=	$doc->filetype;
                $response['fileurl']	=	$doc->myfile;

                if($doc->doc_type == 'personal') {
                    $response['folder_name']	=	'preview-container-'.$doc->folder_name;
                } else if($doc->doc_type == 'visa') {
                     $response['folder_name']	=  'preview-container-migdocumnetlist';
                }

            }else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}*/

    public function renamedoc(Request $request){ //dd($request->all());
        $id = $request->id;
        $filename = $request->filename; // new file name without extension
    
        if(\App\Models\Document::where('id',$id)->exists()){
            $doc = \App\Models\Document::where('id',$id)->first(); //dd($doc);
    
            // Get old S3 key and extension
            $oldKey = $doc->myfile_key;
            $extension = $doc->filetype;
            $client_id = $doc->client_id;
            $doc_type = $doc->doc_type;
    
            // Get client unique id for S3 path
            $admin = \App\Models\Admin::select('client_id')->where('id', $client_id)->first();
            $client_unique_id = $admin ? $admin->client_id : "";
    
            // Build new key and S3 path
            $newKey = time() . $filename . '.' . $extension;
            $newS3Path = $client_unique_id . '/' . $doc_type . '/' . $newKey;
            $oldS3Path = $client_unique_id . '/' . $doc_type . '/' . $oldKey;
    
            // Copy and delete in S3
            if (\Storage::disk('s3')->exists($oldS3Path)) {
                try {
                    // Attempt to copy first
                    $copySuccess = \Storage::disk('s3')->copy($oldS3Path, $newS3Path);
                    
                    if ($copySuccess) {
                        // Only delete original if copy was successful
                        \Storage::disk('s3')->delete($oldS3Path);
                    } else {
                        // Copy failed, don't proceed with database update
                        $response['status'] = false;
                        $response['message'] = 'Failed to copy file. Please try again.';
                        echo json_encode($response);
                        return;
                    }
                } catch (\Exception $e) {
                    // Log the error for debugging
                    \Log::error('S3 copy failed: ' . $e->getMessage(), [
                        'oldPath' => $oldS3Path,
                        'newPath' => $newS3Path,
                        'document_id' => $id
                    ]);
                    
                    $response['status'] = false;
                    $response['message'] = 'File operation failed. Please try again.';
                    echo json_encode($response);
                    return;
                }
            } else {
                // Original file not found - handle gracefully
                \Log::warning('Document rename failed: Original file not found', [
                    'document_id' => $id,
                    'old_s3_path' => $oldS3Path,
                    'new_filename' => $filename,
                    'user_id' => Auth::user()->id ?? 'unknown'
                ]);
                
                // Return error response
                $response['status'] = false;
                $response['message'] = 'Original document not found. Please re-upload the document.';
                $response['error_type'] = 'file_not_found';
                echo json_encode($response);
                return;
            }
    
            // Build new S3 URL
            $newS3Url = \Storage::disk('s3')->url($newS3Path); //dd($newS3Url);
    
            // Update DB
            $res = \DB::table('documents')->where('id', $id)->update([
                'file_name' => $filename,
                'myfile' => $newS3Url,
                'myfile_key' => $newKey
            ]);
    
            if($res){
                $response['status'] = true;
                $response['data'] = 'Document saved successfully';
                $response['Id'] = $id;
                $response['filename'] = $filename;
                $response['filetype'] = $doc->filetype;
                $response['fileurl'] = $newS3Url;
    
                if($doc->doc_type == 'personal') {
                    $response['folder_name'] = 'preview-container-'.$doc->folder_name;
                } else if($doc->doc_type == 'visa') {
                    $response['folder_name'] = 'preview-container-migdocumnetlist';
                }
            } else {
                $response['status'] = false;
                $response['message'] = 'Please try again';
            }
        } else {
            $response['status'] = false;
            $response['message'] = 'Please try again';
        }
        echo json_encode($response);
    }

    public function save_tag(Request $request){
		 $id = $request->client_id;

		if(\App\Models\Admin::where('id',$id)->exists()){
		    $tagg = $request->tag;
		    $tag = array();
		    foreach($tagg as $tg){
		        $stagd = \App\Models\Tag::where('name','=',$tg)->first();
		        if($stagd) {
		            $tag[] = $stagd->id;
		        } else {
		            $stagds = \App\Models\Tag::where('id','=',$tg)->first();
		            if($stagds){
		                $tag[] = $stagds->id;
		            }else{
		                $o = new \App\Models\Tag;
		                $o->name = $tg;
		                $o->save();
		                $tag[] = $o->id;
		            }

		        }
		    }
			$obj = \App\Models\Admin::find($id);
			$obj->tagname = implode(',', $tag);
			$saved = $obj->save();
			if($saved){
				return Redirect::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$id)))->with('success', 'Tags addes successfully');
			}else{
				return Redirect::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$id)))->with('error', 'Please try again');
			}
		}else{
			return Redirect::to('/admin/clients')->with('error', Config::get('constants.unauthorized'));
		}

	}

	public function deletedocs(Request $request){ //dd('11');
		$note_id = $request->note_id;
        if(\App\Models\Document::where('id',$note_id)->exists()){
            $data = DB::table('documents')->where('id', @$note_id)->first();
            $admin = DB::table('admins')->select('client_id')->where('id', @$data->client_id)->first();
            $res = DB::table('documents')->where('id', @$note_id)->delete();
            //Storage::disk('s3')->delete('documents/' . $data->myfile);
            if($data->doc_type == 'migration') {
                Storage::disk('s3')->delete($admin->client_id.'/'.$data->doc_type.'/'.$data->myfile_key);
            } else {
                Storage::disk('s3')->delete($admin->client_id.'/'.$data->doc_type.'/'.$data->myfile_key);
            }
            if($res){
                $subject = 'deleted a document';

				$objs = new ActivitiesLog;
				$objs->client_id = $data->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->save();
				$response['status'] 	= 	true;
				$response['data']	=	'Document removed successfully';
                if(isset($data->doc_type) && $data->doc_type == 'personal'){
                    $response['doc_categry']	= $data->folder_name;
                } else {
                    $response['doc_categry']	= "";
                }
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
                if(isset($data->doc_type) && $data->doc_type == 'personal'){
                    $response['doc_categry']	= $data->folder_name;
                } else {
                    $response['doc_categry']	= "";
                }
			}
		} else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
            if(isset($data->doc_type) && $data->doc_type == 'personal'){
                $response['doc_categry']	= $data->folder_name;
            } else {
                $response['doc_categry']	= "";
            }
		}
		echo json_encode($response);
	}

	public function addAppointmentBook(Request $request){
		$requestData = $request->all(); //dd($requestData);
        $obj = new \App\Models\Appointment;
		$obj->user_id = @Auth::user()->id;
		$obj->client_id = @$request->client_id;
		$obj->timezone = @$request->timezone;
        $obj->service_id = @$request->service_id;
        $obj->noe_id = @$request->noe_id;
        $obj->inperson_address = $request->inperson_address;
        $obj->appointment_details = @$request->appointment_details;
        $obj->preferred_language = @$request->preferred_language;

        if( isset($request->appoint_date) && $request->appoint_date != "") {
            $obj->client_unique_id = @$request->client_unique_id;
        }

        //$obj->full_name = @$request->fullname;
        //$obj->email = @$request->email;
        //$obj->phone = @$request->phone;
        //$obj->date = @$request->appoint_date;
		//$obj->time = @$request->appoint_time;
        if( isset($request->appoint_date) && $request->appoint_date != "") {
            $date = explode('/', $request->appoint_date);
            $obj->date = $date[2].'-'.$date[1].'-'.$date[0];
        }

        $obj->timeslot_full = @$request->appoint_time;
        if( isset($request->appoint_time) && $request->appoint_time != "" ){
			$time = explode('-', $request->appoint_time);
			//echo "@@".date("H:i", strtotime($time[0])); die;
			$obj->time = date("H:i", strtotime($time[0]));
		}

        if( isset($request->slot_overwrite_hidden) && $request->slot_overwrite_hidden != "" ){
			$obj->slot_overwrite_hidden = $request->slot_overwrite_hidden;
		}

        //$obj->title = @$request->title;
		$obj->description = @$request->description;
        //$obj->invites = @$request->invites;
        if( isset($request->promocode_used) && $request->promocode_used != "" ){
			$obj->promocode_used = $request->promocode_used;
        }

        if( isset($request->service_id) && $request->service_id == 1 ){ //1=>Paid,2=>Free
            if( isset($request->promocode_used) && $request->promocode_used != "" ){
                $obj->status = 0; //Due to promocode no payment is needed
            } else {
                $obj->status = 9; //9=>Pending Appointment With Payment Pending
            }
        } else if( isset($request->service_id) && $request->service_id == 2 ){
            $obj->status = 0; //0=>Pending Appointment With Free Type
        }

        $obj->related_to = 'client';
		$saved = $obj->save();
        //Get Nature of Enquiry
        $nature_of_enquiry_data = DB::table('nature_of_enquiry')->where('id', $request->noe_id)->first();
        if($nature_of_enquiry_data){
            $nature_of_enquiry_title = $nature_of_enquiry_data->title;
        } else {
            $nature_of_enquiry_title = "";
        }

        //Get book_services
        $service_data = DB::table('book_services')->where('id', $request->service_id)->first();
        if($service_data){
            $service_title = $service_data->title;
            if( $request->service_id == 1) { //Paid
                $service_type = 'Paid';
            } else {
                $service_type = 'Free';
            }
            $service_title_text = $service_title.'-'.$service_type;
        } else {
            $service_title = "";
            $service_title_text = "";
        }

		if($saved){
            if( isset($request->promocode_used) && $request->promocode_used != "" ){
                DB::table('promocode_uses')->insert([
                    'client_id' => $request->client_id,
                    'promocode_id' => $request->promocode_id,
                    'promocode' => $request->promocode_used
                ]);
            }

			if(isset($request->type) && $request->atype == 'application'){
				$objs = new \App\Models\ApplicationActivitiesLog;
				$objs->stage = $request->type;
				$objs->type = 'appointment';
				$objs->comment = 'created appointment '.@$request->appoint_date;
				$objs->title = '';
				$objs->description = '';
				$objs->app_id = $request->noteid;
				$objs->user_id = Auth::user()->id;
				$saved = $objs->save();
            } else {
                $objs = new ActivitiesLog;
                $objs->client_id = $request->client_id;
                $objs->created_by = Auth::user()->id;

                $appoint_date_val = explode('/', $request->appoint_date);
                $appoint_date_val_formated = $appoint_date_val[0].'/'.$appoint_date_val[1].'/'.$appoint_date_val[2];
                /*if( isset($request->service_id) && $request->service_id == 1 ){ //1=>Paid
                    $objs->description = '<p><span class="text-semi-bold">scheduled an paid appointment without payment on '.$appoint_date_val_formated.' at '.$request->appoint_time.'</span></p>';
                } else if( isset($request->service_id) && $request->service_id == 2 ){ //2=>Free
                    $objs->description = '<p><span class="text-semi-bold">scheduled an appointment on '.$appoint_date_val_formated.' at '.$request->appoint_time.'</span></p>';
                }*/

                $objs->description = '<div style="display: -webkit-inline-box;">
						<span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
							<span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
							  '.date('d M', strtotime($obj->date)).'
							</span>
							<span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
							   '.date('Y', strtotime($obj->date)).'
							</span>
						</span>
					</div>
					<div style="display:inline-grid;"><span class="text-semi-bold">'.$nature_of_enquiry_title.'</span> <span class="text-semi-bold">'.$service_title_text.'</span>  <span class="text-semi-bold">'.$request->appointment_details.'</span> <span class="text-semi-bold">'.$request->description.'</span> <p class="text-semi-light-grey col-v-1">@ '.$request->appoint_time.'</p></div>';

                if( isset($request->service_id) && $request->service_id == 1 ){ //1=>Paid
                    if( isset($request->promocode_used) && $request->promocode_used != "" ){
                        $subject = 'scheduled an appointment';
                    } else {
                        $subject = 'scheduled an paid appointment without payment';
                    }
                } else if( isset($request->service_id) && $request->service_id == 2 ){ //2=>Free
                    $subject = 'scheduled an appointment';
                }
                $objs->subject = $subject;
                $objs->save();
			}

            //if( isset($request->service_id) && $request->service_id == 1 )
            //{ //1=>Paid

                $adminInfo = \App\Models\Admin::select('id','phone','first_name','last_name','email')->where('id','=',$request->client_id)->first();
                if($adminInfo){
                    $clientFullname = $adminInfo->first_name.' '.$adminInfo->last_name;
                } else {
                    $clientFullname = '';
                }
                //Email To customer
                //$host = request()->getHost(); dd($host);
                $host = request()->getHttpHost(); //dd($host);

                if( isset($requestData['appointment_details']) && $requestData['appointment_details'] != ""){
                    if( $requestData['appointment_details'] == "in_person" ){
                        $appointment_details = "In Person";
                    } else if( $requestData['appointment_details'] == "phone" ){
                        $appointment_details = "Phone";
                    } else if( $requestData['appointment_details'] == "zoom_google_meeting" ){
                        $appointment_details = "Zoom / Google Meeting";
                    }
                } else {
                    $appointment_details = "";
                }

                if(isset($requestData['inperson_address']) && $requestData['inperson_address'] != ""){
                    if($requestData['inperson_address'] == 1){
                        $inperson_address = "ADELAIDE (Unit 5 5/55 Gawler Pl, Adelaide SA 5000)";
                    } else if($requestData['inperson_address'] == 2){
                        $inperson_address = "MELBOURNE (Next to flight Center, Level 8/278 Collins St, Melbourne VIC 3000, Australia)";
                    }
                } else {
                    $inperson_address = "";
                }

                $details = [
                    'title' => 'Your Payment is pending. You have booked an appointment on '.$request->appoint_date.'  at '.$request->appoint_time,
                    'body' => 'This is for testing email using smtp',
                    'fullname' => $clientFullname,
                    'date' => $request->appoint_date,
                    'time' => $request->appoint_time,
                    'email'=> $request->client_email,
                    'phone' => $adminInfo->phone,
                    'description' => $request->description,
                    'service'=> $service_title,
                    'host'=> $host,
                    'appointment_id'=> $obj->id,  //payment id
                    'appointment_details'=> $appointment_details,
                    'inperson_address'=> $inperson_address,
                    'service_type'=> $request->service_id,
                    'client_id'=> $request->client_id,
                    'preferred_language'=> $request->preferred_language,
                    'promocode_used'=> $request->promocode_used
                ];

                if( \Mail::to($adminInfo->email)->send(new \App\Mail\AppointmentStripeMail($details)) ) {
                    //send sms message
                    $message = 'Your appointment booked successfully on '.$request->appoint_date.' '.$request->appoint_time;
                    /*$verifiedNumber = VerifiedNumber::where('phone_number',$adminInfo->phone)->where('is_verified', true)->first();
                    if ( $verifiedNumber) {
                        //$this->twilioService->sendSMS($adminInfo->phone,$message);
                        $this->smsService->sendSms($adminInfo->phone,$message);
                    }*/
                }
            //}

            $response['status'] = 	true;
			$response['data']	=	'Appointment saved successfully';
            if(isset($requestData['is_ajax']) && $requestData['is_ajax'] == 1){
                $response['reloadpage'] = true;
            }else{
                $response['reloadpage'] = true; //false;
            }
            $response['client_id']  =    $request->client_id;
            $response['message']	=	'Appointment is booked successfully';
		} else {
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
            $response['client_id']  =    $request->client_id;
            $response['message']	=	'Appointment is not booked.Pls try again';
		}
        echo json_encode($response);
    }

    public function addAppointment(Request $request){
		$requestData = $request->all();

		$obj = new \App\Models\Appointment;
		$obj->user_id = @Auth::user()->id;
		$obj->client_id = @$request->client_id;
		$obj->timezone = @$request->timezone;
		$obj->date = @$request->appoint_date;
		$obj->time = @$request->appoint_time;
		$obj->title = @$request->title;
		$obj->description = @$request->description;
		$obj->invites = @$request->invites;

		$obj->status = 0;
		$obj->related_to = 'client';
		$saved = $obj->save();
		if($saved){

			if(isset($request->type) && $request->atype == 'application'){
				$objs = new \App\Models\ApplicationActivitiesLog;
				$objs->stage = $request->type;
				$objs->type = 'appointment';
				$objs->comment = 'created appointment '.@$request->appoint_date;
				$objs->title = '';
				$objs->description = '';
				$objs->app_id = $request->noteid;
				$objs->user_id = Auth::user()->id;
				$saved = $objs->save();

			}else{
				$subject = 'scheduled an appointment';
			$objs = new ActivitiesLog;
			$objs->client_id = $request->client_id;
			$objs->created_by = Auth::user()->id;
			$objs->description = '<div  style="margin-right: 1rem;float:left;">
						<span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
							<span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
							  '.date('d M', strtotime($obj->date)).'
							</span>
							<span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
							   '.date('Y', strtotime($obj->date)).'
							</span>
						</span>
					</div>
					<div style="float:right;"><span  class="text-semi-bold">'.$obj->title.'</span> <p  class="text-semi-light-grey col-v-1">
				@ '.date('H:i A', strtotime($obj->time)).'
				</p></div>';
			$objs->subject = $subject;
			$objs->save();
			}

			$response['status'] 	= 	true;
			$response['data']	=	'Appointment saved successfully';
				if(isset($requestData['is_ajax']) && $requestData['is_ajax'] == 1){
		            $response['reloadpage'] 	= 	true;
	        	}else{
		        $response['reloadpage'] 	= 	false;
	        	}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}

		 echo json_encode($response);

	}

	public function editappointment(Request $request){
		$requestData = $request->all();

		$obj = \App\Models\Appointment::find($requestData['id']);
		$obj->user_id = @Auth::user()->id;
		$obj->timezone = @$request->timezone;
		$obj->date = @$request->appoint_date;
		$obj->time = @$request->appoint_time;
		$obj->title = @$request->title;
		$obj->description = @$request->description;
		$obj->invites = @$request->invites;
		$obj->status = 0;
		$saved = $obj->save();
		if($saved){
			$subject = 'rescheduled an appointment';
			$objs = new ActivitiesLog;
			$objs->client_id = $request->client_id;
			$objs->created_by = Auth::user()->id;
			$objs->description = '<div  style="margin-right: 1rem;float:left;">
						<span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
							<span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
							  '.date('d M', strtotime($obj->date)).'
							</span>
							<span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
							   '.date('Y', strtotime($obj->date)).'
							</span>
						</span>
					</div>
					<div style="float:right;"><span  class="text-semi-bold">'.$obj->title.'</span> <p  class="text-semi-light-grey col-v-1">
				@ '.date('H:i A', strtotime($obj->time)).'
				</p></div>';
			$objs->subject = $subject;
			$objs->save();
			$response['status'] 	= 	true;
			$response['data']	=	'Appointment updated successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function updateappointmentstatus(Request $request, $status = Null, $id = Null){
		if(isset($id) && !empty($id))
		{
			$requestData = $request->all();
			if(\App\Models\Appointment::where('id', '=', $id)->exists())
			{
				$obj = \App\Models\Appointment::find($id);
				$obj->status = @$status;
				$saved = $obj->save();

				//$subject = 'Appointment Completed';
                if( $status == 0){
                    $subject = 'Appointment is pending';
                } else if( $status == 1){
                    $subject = 'Appointment is approved';
                } else if( $status == 2){
                    $subject = 'Appointment is completed';
                } else if( $status == 3){
                    $subject = 'Appointment is rejected';
                } else if( $status == 4){
                    $subject = 'Appointment is N/P';
                } else if( $status == 5){
                    $subject = 'Appointment is inrogress';
                } else if( $status == 6){
                    $subject = 'Appointment is pending due to did not come';
                } else if( $status == 7){
                    $subject = 'Appointment is cancelled';
                } else if( $status == 8){
                    $subject = 'Appointment is missed';
                } else if( $status == 9){
                    $subject = 'Appointment is pending with payment pending';
                } else if( $status == 10){
                    $subject = 'Appointment is pending with payment success';
                } else if( $status == 11){
                    $subject = 'Appointment is pending with payment failed';
                }
                $objs = new ActivitiesLog;
                $objs->client_id = $obj->client_id;
                $objs->created_by = Auth::user()->id;
                $objs->description = '<div  style="margin-right: 1rem;float:left;">
						<span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
							<span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
							  '.date('d M', strtotime($obj->date)).'
							</span>
							<span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
							   '.date('Y', strtotime($obj->date)).'
							</span>
						</span>
					</div>
					<div style="float:right;"><span  class="text-semi-bold">'.$obj->title.'</span> <p  class="text-semi-light-grey col-v-1">
				@ '.date('H:i A', strtotime($obj->time)).'
				</p></div>';
				$objs->subject = $subject;
				$objs->save();
				//return Redirect::to('/admin/appointments-cal')->with('success', 'Appointment updated successfully.');
                return redirect()->back()->withInput()->with('success', 'Appointment updated successfully.');
			}else{
				return redirect()->back()->with('error', 'Record Not Found');
			}
		}else{
			return redirect()->back()->with('error', 'Record Not Found');
		}
	}

	 public function updatefollowupschedule(Request $request)
    {
        $requestData = $request->all(); //dd($requestData);

        $obj = \App\Models\Appointment::find($requestData['appointment_id']);
        $obj->user_id = @Auth::user()->id;
        //$obj->timezone = @$request->timezone;
        //$obj->date = @$request->followup_date;

        if( isset($request->followup_date) && $request->followup_date != "") {
            $date = explode('/', $request->followup_date);
            $datey = $date[2].'-'.$date[1].'-'.$date[0];
            $obj->date = $date[2].'-'.$date[1].'-'.$date[0];
        }

        //Adelaide
        if( isset($obj->inperson_address) && $obj->inperson_address == 1 )
        {
            $appointExist = \App\Models\Appointment::where('id','!=',$requestData['appointment_id'])
            ->where('inperson_address', '=', 1)
            ->where('status', '!=', 7)
            ->whereDate('date', $datey)
            ->where('time', $request->followup_time)
            ->count();
        }

        //Melbourne
        else
        {

            if
            (
                ( isset($obj->service_id) && $obj->service_id == 1  )
                ||
                (
                    ( isset($obj->service_id) && $obj->service_id == 2 )
                    &&
                    ( isset($obj->noe_id) && ( $obj->noe_id == 1 || $obj->noe_id == 6 || $obj->noe_id == 7) )
                )
            ) { //Paid

                $appointExist = \App\Models\Appointment::where('id','!=',$requestData['appointment_id'])
                ->where('inperson_address', '=', 2)
                ->where('status', '!=', 7)
                ->whereDate('date', $datey)
                ->where('time', $request->followup_time)
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->whereIn('noe_id', [1, 2, 3, 4, 5, 6, 7, 8])
                        ->where('service_id', 1);
                    })
                    ->orWhere(function ($q) {
                        $q->whereIn('noe_id', [1, 6, 7])
                        ->where('service_id', 2);
                    });
                })->count();
            }
            else if( isset($obj->service_id) && $obj->service_id == 2) { //Free
                if( isset($obj->noe_id) && ( $obj->noe_id == 2 || $obj->noe_id == 3 ) ) { //Temporary and JRP
                    $appointExist = \App\Models\Appointment::where('id','!=',$requestData['appointment_id'])
                    ->where('inperson_address', '=', 2)
                    ->where('status', '!=', 7)
                    ->whereDate('date', $datey)
                    ->where('time', $request->followup_time)
                    ->where(function ($query) {
                        $query->whereIn('noe_id', [2,3])
                        ->Where('service_id', 2);
                    })->count();
                }
                else if( isset($obj->noe_id) && ( $obj->noe_id == 4 ) ) { //Tourist Visa
                    $appointExist = \App\Models\Appointment::where('id','!=',$requestData['appointment_id'])
                    ->where('inperson_address', '=', 2)
                    ->where('status', '!=', 7)
                    ->whereDate('date', $datey)
                    ->where('time', $request->followup_time)
                    ->where(function ($query) {
                        $query->whereIn('noe_id', [4])
                        ->Where('service_id', 2);
                    })->count();
                }
                else if( isset($obj->noe_id) && ( $obj->noe_id == 5 ) ) { //Education/Course Change
                    $appointExist = \App\Models\Appointment::where('id','!=',$requestData['appointment_id'])
                    ->where('inperson_address', '=', 2)
                    ->where('status', '!=', 7)
                    ->whereDate('date', $datey)
                    ->where('time', $request->followup_time)
                    ->where(function ($query) {
                        $query->whereIn('noe_id', [5])
                        ->Where('service_id', 2);
                    })->count();
                }
            }
        }
        //dd($appointExist);
        if( $appointExist > 0 ){
            return redirect()->back()->with('error', 'This appointment time slot is already booked.Please select other time slot.');
        }

        $obj->time = @$request->followup_time;
        if( isset($request->followup_time) && $request->followup_time != "" ){
            $time = explode('-', $request->followup_time);
            //echo "@@".date("H:i", strtotime($time[0])); die;
            $timeslot_full_start_time = date("g:i A", strtotime($request->followup_time));
            // Add 15 minutes to the start time
            $timeslot_full_end_time = date("g:i A", strtotime($request->followup_time . ' +15 minutes'));
            $obj->timeslot_full = $timeslot_full_start_time.' - '.$timeslot_full_end_time;
        }
        //$obj->title = @$request->title;
        $obj->description = @$request->edit_description;
        //$obj->invites = @$request->invites
        $saved = $obj->save();
        if($saved){
            //$subject = 'updated an appointment';
            $objs = new ActivitiesLog;
            $objs->client_id = $obj->client_id;
            $objs->created_by = Auth::user()->id;
            /*$objs->description = '<div  style="margin-right: 1rem;float:left;">
                    <span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
                        <span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
                            '.date('d M', strtotime($obj->date)).'
                        </span>
                        <span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
                            '.date('Y', strtotime($obj->date)).'
                        </span>
                    </span>
                </div>
                <div style="float:right;"><span  class="text-semi-bold">'.$obj->title.'</span> <p  class="text-semi-light-grey col-v-1">
            @ '.date('H:i A', strtotime($obj->time)).'
            </p></div>';*/

            //Get Nature of Enquiry
            $nature_of_enquiry_data = DB::table('nature_of_enquiry')->where('id', $obj->noe_id)->first();
            if($nature_of_enquiry_data){
                $nature_of_enquiry_title = $nature_of_enquiry_data->title;
            } else {
                $nature_of_enquiry_title = "";
            }

            //Get book_services
            $service_data = DB::table('book_services')->where('id', $obj->service_id)->first();
            if($service_data){
                $service_title = $service_data->title;
                if( $request->service_id == 1) { //Paid
                    $service_type = 'Paid';
                } else {
                    $service_type = 'Free';
                }
                $service_title_text = $service_title.'-'.$service_type;
            } else {
                $service_title = "";
                $service_title_text = "";
            }

            $objs->description = '<div style="display: -webkit-inline-box;">
            <span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
                <span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
                    '.date('d M', strtotime($obj->date)).'
                </span>
                <span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
                    '.date('Y', strtotime($obj->date)).'
                </span>
            </span>
            </div>
            <div style="display:inline-grid;"><span class="text-semi-bold">'.$nature_of_enquiry_title.'</span> <span class="text-semi-bold">'.$service_title_text.'</span>  <span class="text-semi-bold">'.$obj->appointment_details.'</span> <span class="text-semi-bold">'.$obj->description.'</span> <p class="text-semi-light-grey col-v-1">@ '.$obj->timeslot_full.'</p></div>';

            if( isset($obj->service_id) && $obj->service_id == 1 ){ //1=>Paid
                $subject = 'updated an paid appointment without payment';
            } else if( isset($obj->service_id) && $obj->service_id == 2 ){ //2=>Free
                $subject = 'updated an appointment';
            }
            $objs->subject = $subject;
            $objs->save();
            //return Redirect::to('/admin/appointments-cal')->with('success', 'Appointment updated successfully.');
            return Redirect()->back()->with('success', 'Appointment updated successfully.');
        } else {
            return redirect()->back()->with('error', Config::get('constants.server_error'));
        }

    }

	public function getAppointments(Request $request){
		ob_start();
		?>
		<div class="row">
			<div class="col-md-5 appointment_grid_list">
				<?php
				$rr=0;
				$appointmentdata = array();
				$appointmentlists = \App\Models\Appointment::where('client_id', $request->clientid)->where('related_to', 'client')->orderby('created_at', 'DESC')->get();
				$appointmentlistslast = \App\Models\Appointment::where('client_id', $request->clientid)->where('related_to', 'client')->orderby('created_at', 'DESC')->first();
				foreach($appointmentlists as $appointmentlist){
					$admin = \App\Models\Admin::where('id', $appointmentlist->user_id)->first();
					$datetime = $appointmentlist->created_at;
					$timeago = \App\Http\Controllers\Controller::time_elapsed_string($datetime);

					$appointmentdata[$appointmentlist->id] = array(
						'title' => $appointmentlist->title,
						'time' => date('H:i A', strtotime($appointmentlist->time)),
						'date' => date('d D, M Y', strtotime($appointmentlist->date)),
						'description' => $appointmentlist->description,
						'createdby' => substr($admin->first_name, 0, 1),
						'createdname' => $admin->first_name,
						'createdemail' => $admin->email,
					);
				?>
				<div class="appointmentdata <?php if($rr == 0){ echo 'active'; } ?>" data-id="<?php echo $appointmentlist->id; ?>">
					<div class="appointment_col">
						<div class="appointdate">
							<h5><?php echo date('d D', strtotime($appointmentlist->date)); ?></h5>
							<p><?php echo date('H:i A', strtotime($appointmentlist->time)); ?><br>
							<i><small><?php echo $timeago ?></small></i></p>
						</div>
						<div class="title_desc">
							<h5><?php echo $appointmentlist->title; ?></h5>
							<p><?php echo $appointmentlist->description; ?></p>
						</div>
						<div class="appoint_created">
							<span class="span_label">Created By:
							<span><?php echo substr($admin->first_name, 0, 1); ?></span></span>
							<div class="dropdown d-inline dropdown_ellipsis_icon">
								<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
								<div class="dropdown-menu">
									<a class="dropdown-item edit_appointment" data-id="<?php echo $appointmentlist->id; ?>" href="javascript:;">Edit</a>
									<a data-id="<?php echo $appointmentlist->id; ?>" data-href="deleteappointment" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php $rr++; } ?>
			</div>
			<div class="col-md-7">
				<div class="editappointment">
					<a class="edit_link edit_appointment" href="javascript:;" data-id="<?php echo $appointmentlistslast->id; ?>"><i class="fa fa-edit"></i></a>
					<?php
					$adminfirst = \App\Models\Admin::where('id', $appointmentlistslast->user_id)->first();
					?>
					<div class="content">
						<h4 class="appointmentname"><?php echo $appointmentlistslast->title; ?></h4>
						<div class="appitem">
							<i class="fa fa-clock"></i>
							<span class="appcontent appointmenttime"><?php echo date('H:i A', strtotime($appointmentlistslast->time)); ?></span>
						</div>
						<div class="appitem">
							<i class="fa fa-calendar"></i>
							<span class="appcontent appointmentdate"><?php echo date('d D, M Y', strtotime($appointmentlistslast->date)); ?></span>
						</div>
						<div class="description appointmentdescription">
							<p><?php echo $appointmentlistslast->description; ?></p>
						</div>
						<div class="created_by">
							<span class="label">Created By:</span>
							<div class="createdby">
								<span class="appointmentcreatedby"><?php echo substr($adminfirst->first_name, 0, 1); ?></span>
							</div>
							<div class="createdinfo">
								<a href="" class="appointmentcreatedname"><?php echo $adminfirst->first_name ?></a>
								<p class="appointmentcreatedemail"><?php echo $adminfirst->primary_email; ?></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		echo ob_get_clean();
		die;
	}

	public function getAppointmentdetail(Request $request){
		$obj = \App\Models\Appointment::find($request->id);
		if($obj){
			?>
			<form method="post" action="<?php echo \URL::to('/admin/editappointment'); ?>" name="editappointment" id="editappointment" autocomplete="off" enctype="multipart/form-data">

				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<input type="hidden" name="client_id" value="<?php echo $obj->client_id; ?>">
				<input type="hidden" name="id" value="<?php echo $obj->id; ?>">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label style="display:block;" for="related_to">Related to:</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="client" value="Client" name="related_to" checked>
									<label class="form-check-label" for="client">Client</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="partner" value="Partner" name="related_to">
									<label class="form-check-label" for="partner">Partner</label>
								</div>
								<span class="custom-error related_to_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label style="display:block;" for="related_to">Added by:</label>
								<span><?php echo @Auth::user()->first_name; ?></span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="client_name">Client Name <span class="span_req">*</span></label>
								<input type="text" name="client_name" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Client Name" readonly value="<?php echo $obj->clients->first_name.' '.@$obj->clients->last_name; ?>">

							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="timezone">Timezone <span class="span_req">*</span></label>
								<select class="form-control timezoneselects2" name="timezone" data-valid="required">
									<option value="">Select Timezone</option>
									<?php
									$timelist = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
									foreach($timelist as $tlist){
										?>
										<option value="<?php echo $tlist; ?>" <?php if($obj->timezone == $tlist){ echo 'selected'; } ?>><?php echo $tlist; ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-7 col-lg-7">
							<div class="form-group">
								<label for="appoint_date">Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input type="text" name="appoint_date" class="form-control datepicker" data-valid="required" autocomplete="off" placeholder="Select Date" readonly value="<?php echo $obj->date; ?>">

								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error appoint_date_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-5 col-lg-5">
							<div class="form-group">
								<label for="appoint_time">Time</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-clock"></i>
										</div>
									</div>
									<input type="time" name="appoint_time" class="form-control" data-valid="required" autocomplete="off" placeholder="Select Date" value="<?php echo $obj->time; ?>">

								</div>
								<span class="custom-error appoint_time_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								<input type="text" name="title" class="form-control " data-valid="required" autocomplete="off" placeholder="Enter Title"  value="<?php echo $obj->title; ?>">

								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="description">Description</label>
								<textarea class="form-control" name="description" placeholder="Description"><?php echo $obj->description; ?></textarea>
								<span class="custom-error description_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="invites">Invitees</label>
								<select class="form-control invitesselects2" name="invites">
									<option value="">Select Invitees</option>
								 <?php
										$headoffice = \App\Models\Admin::where('role','!=',7)->get();
									foreach($headoffice as $holist){
										?>
										<option value="<?php echo $holist->id; ?>" <?php if($obj->invites == $holist->id){ echo 'selected'; } ?>><?php echo $holist->first_name.' '. $holist->last_name.' ('.$holist->email.')'; ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('editappointment')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			<?php
		}else{
			?>
			Record Not Found
			<?php
		}
	}

	public function deleteappointment(Request $request){
		$note_id = $request->note_id;
		if(\App\Models\Appointment::where('id',$note_id)->exists()){
			$data = \App\Models\Appointment::where('id',$note_id)->first();
			$res = DB::table('appointments')->where('id', @$note_id)->delete();
			if($res){

				$subject = 'deleted an appointment';

				$objs = new ActivitiesLog;
				$objs->client_id = $data->client_id;
				$objs->created_by = Auth::user()->id;
			$objs->description = '<div  style="margin-right: 1rem;float:left;">
						<span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
							<span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
							  '.date('d M', strtotime($data->date)).'
							</span>
							<span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
							   '.date('Y', strtotime($data->date)).'
							</span>
						</span>
					</div>
					<div style="float:right;"><span  class="text-semi-bold">'.$data->title.'</span> <p  class="text-semi-light-grey col-v-1">
				@ '.date('H:i A', strtotime($data->time)).'
				</p></div>';
				$objs->subject = $subject;
				$objs->save();
			$response['status'] 	= 	true;
			$response['data']	=	$data;
			}else{
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}




	public function savetoapplication(Request $request){
		if(Admin::where('role', '=', '7')->where('id', $request->contact)->exists()){
			$workflow = $request->workflow;

			$partner = $request->partner_id;
			$branch = $request->branch;
			$product = $request->product_id;
			$client_id = $request->contact;
			$status = 0;
			$stage = 'Application';
			$sale_forcast = 0.00;
			if(\App\Models\Application::where('client_id', $client_id)->where('product_id', $product)->where('partner_id', $partner)->exists()){
				$response['status'] 	= 	false;
				$response['message']	=	'Application to the product already exists for this client.';
			}else{
				$obj = new \App\Models\Application;
				$obj->user_id = Auth::user()->id;
				$obj->workflow = $workflow;
				$obj->partner_id = $partner;
				$obj->branch = $branch;
				$obj->product_id = $product;
				$obj->status = $status;
				$obj->stage = $stage;
				$obj->sale_forcast = $sale_forcast;
				$obj->client_id = $client_id;
				$saved = $obj->save();
				if($saved){

					$subject = 'has started an application';
					$objs = new ActivitiesLog;
					$objs->client_id = $request->client_id;
					$objs->created_by = Auth::user()->id;
					$objs->description = '<span class="text-semi-bold">'.@$productdetail->name.'</span><p>'.@$partnerdetail->partner_name.' ('.@$PartnerBranch->name.')</p>';
					$objs->subject = $subject;
					$objs->save();
					$response['status'] 	= 	true;
					$response['message']	=	'You’ve successfully updated your client’s information.';
				}else{
					$response['status'] 	= 	false;
					$response['message']	=	'Please try again';
				}
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}



	public function pinnote(Request $request){
		$requestData = $request->all();

		if(\App\Models\Note::where('id',$requestData['note_id'])->exists()){
			$note = \App\Models\Note::where('id',$requestData['note_id'])->first();
			if($note->pin == 0){
				$obj = \App\Models\Note::find($note->id);
				$obj->pin = 1;
				$saved = $obj->save();
			}else{
				$obj = \App\Models\Note::find($note->id);
				$obj->pin = 0;
				$saved = $obj->save();
			}
			$response['status'] 				= 	true;
			$response['message']			=	'Pin Option added successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Record not found';
		}
		echo json_encode($response);
	}

	public function changetype(Request $request,$id = Null, $slug = Null){
        //dd($request->all());
        if(isset($id) && !empty($id)) {
            $id = $this->decodeString($id);
            if(Admin::where('id', '=', $id)->where('role', '=', '7')->exists()) {
                $obj = Admin::find($id);
                if($slug == 'client') {
                    $obj->type = $slug;
                    $obj->user_id = $request['user_id'];
                    $saved = $obj->save();

                    $matter = new ClientMatter();
                    $matter->user_id = $request['user_id'];
                    $matter->client_id = $request['client_id'];
                    $matter->sel_migration_agent = $request['migration_agent'];
                    $matter->sel_person_responsible = $request['person_responsible'];
                    $matter->sel_person_assisting = $request['person_assisting'];
                    $matter->sel_matter_id = $request['matter_id'];

                    $client_matters_cnt_per_client = DB::table('client_matters')->select('id')->where('sel_matter_id',$request['matter_id'])->where('client_id',$request['client_id'])->count();
                    $client_matters_current_no = $client_matters_cnt_per_client+1;
                    if($request['matter_id'] == 1) {
                        $matter->client_unique_matter_no = 'GN_'.$client_matters_current_no;
                    } else {
                        $matterInfo = Matter::select('nick_name')->where('id', '=', $request['matter_id'])->first();
                        $matter->client_unique_matter_no = $matterInfo->nick_name."_".$client_matters_current_no;
                    }

                    $matter->workflow_stage_id = 1;
                    $matter->save();

                    // Get the last inserted ID
                    /*$lastInsertedId = $matter->id;
                    $existingPost = ClientMatter::find($lastInsertedId);
                    if ($existingPost) { // Update the record
                        $existingPost->client_unique_matter_id = 'Matter_'.$lastInsertedId;
                        $existingPost->save();
                    }*/
                } else if($slug == 'lead' ) {
                    $obj->type = $slug;
                    $obj->user_id = "";
                    $saved = $obj->save();
                }
                return Redirect::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$id)))->with('success', 'Record Updated successfully');
            } else {
                return Redirect::to('/admin/clients')->with('error', 'Clients Not Exist');
            }
        } else {
            return Redirect::to('/admin/clients')->with('error', Config::get('constants.unauthorized'));
        }
	}

public function followupstore(Request $request)
{
    $requestData = $request->all(); //dd($requestData);
    // Decode the client ID
    $clientId = $this->decodeString(@$requestData['client_id']);

    // Get the next unique ID for this task
    //$lastUniqueId = \App\Models\Note::max('unique_group_id'); // Get the last unique_id
    //$taskUniqueId = $lastUniqueId ? $lastUniqueId + 1 : 1; // Increment by 1 or start from 1

    $taskUniqueId = 'group_' . uniqid('', true);

    // Loop through each assignee and create a follow-up note
    foreach ($requestData['rem_cat'] as $assigneeId) {
        // Create a new followup note for each assignee
        $followup = new \App\Models\Note;
        $followup->client_id = $clientId;
        $followup->user_id = Auth::user()->id;
        $followup->description = @$requestData['description'];
        $followup->unique_group_id = $taskUniqueId;

        // Set the title for the current assignee
        $assigneeName = $this->getAssigneeName($assigneeId);
        $followup->title = @$requestData['remindersubject'] ?? 'Lead assigned to ' . $assigneeName;

        $followup->folloup = 1;
        $followup->task_group = @$requestData['task_group'];
        $followup->assigned_to = $assigneeId;

        if (isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != '') {
            $followup->followup_date = @$requestData['followup_datetime'];
        }

        //add note deadline
        if(isset($requestData['note_deadline_checkbox']) && $requestData['note_deadline_checkbox'] != ''){
            if($requestData['note_deadline_checkbox'] == 1){
                $followup->note_deadline = $requestData['note_deadline'];
            } else {
                $followup->note_deadline = NULL;
            }
        } else {
            $followup->note_deadline = NULL;
        }

        $saved = $followup->save();

        if ($saved) {
            // Update lead follow-up date
            if (isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != '') {
                $Lead = Admin::find($clientId);
                $Lead->followup_date = @$requestData['followup_datetime'];
                $Lead->save();
            }

            // Create a notification for the current assignee
            $o = new \App\Models\Notification;
            $o->sender_id = Auth::user()->id;
            $o->receiver_id = $assigneeId;
            $o->module_id = $clientId;
            $o->url = \URL::to('/admin/clients/detail/' . @$requestData['client_id']);
            $o->notification_type = 'client';
            $o->message = 'Followup Assigned by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name . ' on ' . date('d/M/Y h:i A', strtotime(@$requestData['followup_datetime']));
            $o->save();

            // Log the activity for the current assignee
            $objs = new ActivitiesLog;
            $objs->client_id = $clientId;
            $objs->created_by = Auth::user()->id;
            $objs->subject = 'Set action for ' . $assigneeName;
            $objs->description = '<span class="text-semi-bold">' . @$requestData['remindersubject'] . '</span><p>' . @$requestData['description'] . '</p>';

            if (Auth::user()->id != $assigneeId) {
                $objs->use_for = $assigneeId;
            } else {
                $objs->use_for = "";
            }

            $objs->followup_date = @$requestData['followup_datetime'];
            $objs->task_group = @$requestData['task_group'];
            $objs->save();
        }
    }
    echo json_encode(array('success' => true, 'message' => 'successfully saved', 'clientID' => $requestData['client_id']));
    exit;
}

// Helper function to get assignee name
protected function getAssigneeName($assigneeId)
{
    $admin = \App\Models\Admin::find($assigneeId);
    return $admin ? $admin->first_name . ' ' . $admin->last_name : 'Unknown Assignee';
}

    //task reassign and update exist followup
	public function reassignfollowupstore(Request $request){
	    $requestData 		= 	$request->all();
        //echo '<pre>'; print_r($requestData); die;
        /*if(\App\Models\Note::where('client_id',$requestData['client_id'])->where('assigned_to',$requestData['rem_cat'])->exists())
        {
            // return redirect()->back()->with('error', 'Lead already assigned');
            // return Redirect::to('/admin/assignee')->with('error', 'Lead already assigned');
            echo json_encode(array('success' => false, 'message' => 'Lead already assigned to '.@$requestData['assignee_name'], 'clientID' => $requestData['client_id']));
            exit;
        }*/

        $followup = \App\Models\Note::where('id', '=', $requestData['note_id'])->first();
        $followup->id               = $followup ->id;
		$followup->client_id		= $this->decodeString(@$requestData['client_id']);
		$followup->user_id			= Auth::user()->id;
		$followup->description		= @$requestData['description'];
		$followup->title		    = @$requestData['remindersubject'] ?? 'Lead assign to '.@$requestData['assignee_name'];
		$followup->folloup	= 1;
        $followup->task_group = @$requestData['task_group'];
		$followup->assigned_to	= @$requestData['rem_cat'];
		if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
		    //	$followup->followup_date	= @$requestData['followup_date'].date('H:i', strtotime($requestData['followup_time']));
			$followup->followup_date	=  @$requestData['followup_datetime'];
		}
        $saved				=	$followup->save();
        if(!$saved)
		{
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'clientID' => $requestData['client_id']));
		}
		else
		{
			if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
                $Lead = Admin::find($this->decodeString($requestData['client_id']));
                $Lead->followup_date = @$requestData['followup_datetime'];
                $Lead->save();
			}

			$o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['rem_cat'];
	    	$o->module_id = $this->decodeString(@$requestData['client_id']);
	    	$o->url = \URL::to('/admin/clients/detail/'.@$requestData['client_id']);
	    	$o->notification_type = 'client';
	    	$o->message = 'Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name.' '.date('d/M/Y h:i A',strtotime($Lead->followup_date));
	    	$o->save();

			$objs = new ActivitiesLog;
            $objs->client_id = $this->decodeString(@$requestData['client_id']);
            $objs->created_by = Auth::user()->id;
            //$objs->subject = 'Followup set for '.date('d/M/Y h:i A',strtotime($Lead->followup_date));
            $objs->subject = 'set action for '.@$requestData['assignee_name'];
            $objs->description = '<span class="text-semi-bold">'.@$requestData['remindersubject'].'</span><p>'.@$requestData['description'].'</p>';
            if(Auth::user()->id != @$requestData['rem_cat']){
                $objs->use_for = @$requestData['rem_cat'];
            } else {
                $objs->use_for = "";
            }
            $objs->followup_date = @$requestData['followup_datetime'];
            $objs->task_group = @$requestData['task_group'];
            $objs->save();
			echo json_encode(array('success' => true, 'message' => 'successfully saved', 'clientID' => $requestData['client_id']));
			exit;
		}
	}

    //update task follow up and save
	public function updatefollowup(Request $request){
	    $requestData 		= 	$request->all();

        //echo '<pre>'; print_r($requestData); die;
        /*if(\App\Models\Note::where('client_id',$requestData['client_id'])->where('assigned_to',$requestData['rem_cat'])->exists())
        {
            echo json_encode(array('success' => false, 'message' => 'Lead already assigned to '.@$requestData['assignee_name'], 'clientID' => $requestData['client_id']));
            exit;
        }*/

        $followup = \App\Models\Note::where('id', '=', $requestData['note_id'])->first();
        //$followup 				= new \App\Models\Note;
        $followup->id               = $followup ->id;
		$followup->client_id		= $this->decodeString(@$requestData['client_id']);
		$followup->user_id			= Auth::user()->id;
		$followup->description		= @$requestData['description'];
		$followup->title		    = @$requestData['remindersubject'] ?? 'Update Task and lead assign to '.@$requestData['assignee_name'];
		$followup->folloup	= 1;
        $followup->task_group = @$requestData['task_group'];
		$followup->assigned_to	= @$requestData['rem_cat'];
		if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
		    //	$followup->followup_date	= @$requestData['followup_date'].date('H:i', strtotime($requestData['followup_time']));
			$followup->followup_date	=  @$requestData['followup_datetime'];
		}
        $saved	=	$followup->save();

		if(!$saved)
		{
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'clientID' => $requestData['client_id']));
		}
		else
		{
			if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
                $Lead = Admin::find($this->decodeString($requestData['client_id']));
                $Lead->followup_date = @$requestData['followup_datetime'];
                $Lead->save();
			}

			$o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['rem_cat'];
	    	$o->module_id = $this->decodeString(@$requestData['client_id']);
	    	$o->url = \URL::to('/admin/clients/detail/'.@$requestData['client_id']);
	    	$o->notification_type = 'client';
	    	$o->message = 'Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name.' '.date('d/M/Y h:i A',strtotime($Lead->followup_date));
	    	$o->save();

			$objs = new ActivitiesLog;
            $objs->client_id = $this->decodeString(@$requestData['client_id']);
            $objs->created_by = Auth::user()->id;
            //$objs->subject = 'Followup set for '.date('d/M/Y h:i A',strtotime($Lead->followup_date));
            $objs->subject = 'Update task for '.@$requestData['assignee_name'];
            $objs->description = '<span class="text-semi-bold">'.@$requestData['remindersubject'].'</span><p>'.@$requestData['description'].'</p>';
            if(Auth::user()->id != @$requestData['rem_cat']){
                $objs->use_for = @$requestData['rem_cat'];
            } else {
                $objs->use_for = "";
            }

            $objs->followup_date = @$requestData['followup_datetime'];
            $objs->task_group = @$requestData['task_group'];
            $objs->save();
			echo json_encode(array('success' => true, 'message' => 'successfully saved', 'clientID' => $requestData['client_id']));
			exit;
		}
	}

    //personal followup
    /*public function personalfollowup(Request $request){
	    $requestData 		= 	$request->all();
        //echo '<pre>'; print_r($requestData); die;

        if( isset($requestData['client_id']) && strpos($requestData['client_id'],"/")){
            $req_client_arr = explode("/",$requestData['client_id']);
            if(!empty($req_client_arr)){
                $req_clientID = $req_client_arr[0];
                $client_id = $this->decodeString($req_clientID );
                //echo $req_clientID."====".$client_id;die;
            }
        } else {
            $req_clientID = "";
            $client_id = "";
        }
        //echo "####".$this->decodeString(@$requestData['client_id']);die;

        /*if(\App\Models\Note::where('client_id',$requestData['client_id'])->where('assigned_to',$requestData['rem_cat'])->exists())
        {
            echo json_encode(array('success' => false, 'message' => 'Lead already assigned to '.@$requestData['assignee_name'], 'clientID' => $req_clientID));
            exit;
        }*/
		/*$followup 					= new \App\Models\Note;
		$followup->client_id		= $client_id;//$this->decodeString(@$requestData['client_id']);
		$followup->user_id			= Auth::user()->id;
		$followup->description		= @$requestData['description'];
		$followup->title		    = @$requestData['remindersubject'] ?? 'Personal Task assign to '.@$requestData['assignee_name'];
		$followup->folloup	= 1;
        $followup->task_group = @$requestData['task_group'];
		$followup->assigned_to	= @$requestData['rem_cat'];
		if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
		    $followup->followup_date	=  @$requestData['followup_datetime'];
		}
        $saved	=	$followup->save();
        if(!$saved)
		{
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'clientID' => $client_id)); //$requestData['client_id']
		}
		else
		{
			$o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['rem_cat'];
	    	$o->module_id = '';//$this->decodeString(@$requestData['client_id']);
	    	$o->url = '';//\URL::to('/admin/clients/detail/'.@$requestData['client_id']);
	    	$o->notification_type = 'client';
	    	$o->message = 'Personal Task Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name.' '.date('d/M/Y h:i A',strtotime($requestData['followup_datetime']));
	    	$o->save();

			$objs = new ActivitiesLog;
            $objs->client_id = $client_id;//$this->decodeString(@$requestData['client_id']);
            $objs->created_by = Auth::user()->id;
            $objs->subject = 'set action for '.@$requestData['assignee_name'];
            $objs->description = '<span class="text-semi-bold">'.@$requestData['remindersubject'].'</span><p>'.@$requestData['description'].'</p>';
            if(Auth::user()->id != @$requestData['rem_cat']){
                $objs->use_for = @$requestData['rem_cat'];
            } else {
                $objs->use_for = "";
            }
            $objs->followup_date = @$requestData['followup_datetime'];
            $objs->task_group = @$requestData['task_group'];
            $objs->save();
			echo json_encode(array('success' => true, 'message' => 'successfully saved', 'clientID' => $client_id)); //$requestData['client_id']
			exit;
		}
	}*/

    //personal followup
public function personalfollowup(Request $request){
    $requestData = $request->all();

    if (isset($requestData['client_id']) && strpos($requestData['client_id'], "/")) {
        $req_client_arr = explode("/", $requestData['client_id']);
        if (!empty($req_client_arr)) {
            $req_clientID = $req_client_arr[0];
            $client_id = $this->decodeString($req_clientID);
        }
    } else {
        $req_clientID = "";
        $client_id = "";
    }
    $taskUniqueId = 'group_' . uniqid('', true);
    // Loop through each selected rem_cat value and insert records
    $rem_cats = $requestData['rem_cat']; // This should be an array
    if (is_array($rem_cats) && count($rem_cats) > 0) {
        foreach ($rem_cats as $rem_cat) {
            $followup = new \App\Models\Note;
            $followup->client_id = $client_id;
            $followup->user_id = Auth::user()->id;
            $followup->description = @$requestData['description'];
            $followup->title = 'Personal Task assigned to ' . $this->getUserName($rem_cat); // Use the name of each assignee
            $followup->folloup = 1;
            $followup->task_group = @$requestData['task_group'];
            $followup->assigned_to = $rem_cat; // Assign this specific rem_cat value
            $followup->unique_group_id = $taskUniqueId;

            if (isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != '') {
                $followup->followup_date = @$requestData['followup_datetime'];
            }

            $saved = $followup->save();

            if ($saved) {
                $o = new \App\Models\Notification;
                $o->sender_id = Auth::user()->id;
                $o->receiver_id = $rem_cat;
                $o->module_id = '';
                $o->url = '';
                $o->notification_type = 'client';
                $o->message = 'Personal Task Followup Assigned by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name . ' on ' . date('d/M/Y h:i A', strtotime($requestData['followup_datetime']));
                $o->save();

                $objs = new ActivitiesLog;
                $objs->client_id = $client_id;
                $objs->created_by = Auth::user()->id;
                $objs->subject = 'Set action for ' . $this->getUserName($rem_cat); // Use the name of each assignee
                $objs->description = '<span class="text-semi-bold">Personal Task</span><p>' . @$requestData['description'] . '</p>';

                if (Auth::user()->id != $rem_cat) {
                    $objs->use_for = $rem_cat;
                } else {
                    $objs->use_for = "";
                }

                $objs->followup_date = @$requestData['followup_datetime'];
                $objs->task_group = @$requestData['task_group'];
                $objs->save();
            } else {
                echo json_encode(array('success' => false, 'message' => 'Please try again', 'clientID' => $client_id));
                exit;
            }
        }

        echo json_encode(array('success' => true, 'message' => 'Successfully saved', 'clientID' => $client_id));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Invalid Assignee data', 'clientID' => $client_id));
    }

    exit;
}

private function getUserName($userId) {
    $user = \App\Models\Admin::find($userId);
    return $user ? $user->first_name . ' ' . $user->last_name : 'Unknown User';
}

	public function retagfollowup(Request $request){
	    $requestData 		= 	$request->all();

//	echo '<pre>'; print_r($requestData); die;
		$followup 					= new \App\Models\Note;
		$followup->client_id			= @$requestData['client_id'];
		$followup->user_id			= Auth::user()->id;
		$followup->description		= @$requestData['message'];
		$followup->title			= '';
		$followup->folloup	        = 1;
		$followup->assigned_to	    = @$requestData['changeassignee'];
		if(isset($requestData['followup_date']) && $requestData['followup_date'] != ''){

				$followup->followup_date	=  $requestData['followup_date'].' '.date('H:i', strtotime($requestData['followup_time']));
		}

		$saved				=	$followup->save();

		if(!$saved)
		{
		return Redirect::to('/admin/followup-dates')->with('error', 'Please try again');
		}
		else
		{
		   /*$objnote =  \App\Models\Note::find();
		   $objnote->status = 1;
		   $objnote->save();*/
		    $newassignee = Admin::find($requestData['changeassignee']);
			$o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['changeassignee'];
	    	$o->module_id = @$requestData['client_id'];
	    	$o->url = \URL::to('/admin/clients/detail/'.@$requestData['client_id']);
	    	$o->notification_type = 'client';
	    	$o->message = 'Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
	    	$o->save();

			$objs = new ActivitiesLog;
				$objs->client_id = @$requestData['client_id'];
				$objs->created_by = Auth::user()->id;
				$objs->subject = Auth::user()->first_name.' '.Auth::user()->last_name.' tags work to '.$newassignee->first_name.' '.$newassignee->last_name;
				$objs->description = @$requestData['message'];
				$objs->save();
		return Redirect::to('/admin/followup-dates')->with('success', 'Record Updated successfully');
		}
	}

		public function change_assignee(Request $request){
            //dd( $request->all() );
    		$objs = Admin::find($request->id);
            if ( is_array($request->assinee) ) {
                if( count($request->assinee) < 1){
                    $objs->assignee = "";
                } else if( count($request->assinee) == 1){
                    $objs->assignee = $request->assinee[0];
                } else if( count($request->assinee) > 1){
                    $objs->assignee = implode(",",$request->assinee);
                }
            }
    		//$objs->assignee = $request->assinee;
            $saved = $objs->save();
            if($saved){
                if ( is_array($request->assinee) && count($request->assinee) >=1) {
                    $assigneeArr = $request->assinee;
                    foreach($assigneeArr as $key=>$val) {
                        $o = new \App\Models\Notification;
                        $o->sender_id = Auth::user()->id;
                        $o->receiver_id = $val; //$request->assinee;
                        $o->module_id = $request->id;
                        $o->url = \URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$request->id)));
                        $o->notification_type = 'client';
                        $o->message = 'Client Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
                        $o->save();
                    }
                }
                $response['status'] 	= 	true;
    			$response['message']	=	'Updated successfully';
    		}else{
    			$response['status'] 	= 	false;
    			$response['message']	=	'Please try again';
    		}
    		echo json_encode($response);
    	}

    	public function saveprevvisa(Request $request){
    	    $requestData 		= 	$request->all();
    	     $obj = Admin::find($requestData['client_id']);
    	    $pr = array();
    	    $i = 0;
    	  $start_date =  $requestData['prev_visa']['start_date'];
    	   $end_date =  $requestData['prev_visa']['end_date'];
    	    $place =  $requestData['prev_visa']['place'];
    	     $person =  $requestData['prev_visa']['person'];

    	    foreach($requestData['prev_visa']['name'] as  $prev_visa){

    	       $pr[] = array(
    	                'name' => $prev_visa,
    	                'start_date' => $start_date[$i],
    	                'end_date' =>$end_date[$i],
    	                'place' =>$place[$i],
    	                'person' =>$person[$i],
    	            );
    	            $i++;
    	    }

    	     $obj->prev_visa = json_encode($pr);

    	     $save = $obj->save();
    	     if($save){
    	         return Redirect::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$requestData['client_id'])))->with('success', 'Previous Visa Updated Successfully');
    	     }else{
    	         return redirect()->back()->with('error', Config::get('constants.server_error'));
    	     }
    	}

    	public function removetag(Request $request){
    	    $objs = Admin::find($request->c);
    	    $itag = $request->rem_id;

    	    	if($objs->tagname != ''){
					$rs = explode(',', $objs->tagname);
					unset($rs[$itag]);

			    $objs->tagname = 	implode(',',@$rs);
			    $objs->save();
    	    	}
    	   return Redirect::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$objs->id)))->with('success', 'Record Updated successfully');
    	}

    	public function saveonlineform(Request $request){
    	   $requestData 		= 	$request->all();
    	   if(OnlineForm::where('client_id', $requestData['client_id'])->where('type', $requestData['type'])->exists()){
    	     $OnlineForm =  OnlineForm::where('client_id', $requestData['client_id'])->where('type', $requestData['type'])->first();
    	     $obj = OnlineForm::find($OnlineForm->id);
    	   }else{
    	       $obj = New OnlineForm;
    	   }

		   $parent_dob = '';
	        if($requestData['parent_dob'] != ''){
	           $dobs = explode('/', $requestData['parent_dob']);
	          $parent_dob = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
	        }

			 $parent_dob_2 = '';
	        if($requestData['parent_dob_2'] != ''){
	           $dobs = explode('/', $requestData['parent_dob_2']);
	          $parent_dob_2 = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
	        }
			$sibling_dob = '';
	        if($requestData['sibling_dob'] != ''){
	           $dobs = explode('/', $requestData['sibling_dob']);
	          $sibling_dob = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
	        }
			$sibling_dob_2 = '';
	        if($requestData['sibling_dob_2'] != ''){
	           $dobs = explode('/', $requestData['sibling_dob_2']);
	          $sibling_dob_2 = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
	        }

                $obj->client_id = $requestData['client_id'];
                $obj->type = $requestData['type'];
                $obj->info_name = $requestData['info_name'];
                $obj->main_lang = implode(',', $requestData['main_lang']);
                $obj->marital_status = $requestData['marital_status'];
                $obj->mobile = $requestData['mobile'];
                $obj->curr_address = $requestData['curr_address'];
                $obj->email = $requestData['email'];
                $obj->parent_name = $requestData['parent_name'];
                $obj->parent_dob = $parent_dob;
                $obj->parent_occ = $requestData['parent_occ'];
                $obj->parent_country = $requestData['parent_country'];
                $obj->parent_name_2 = $requestData['parent_name_2'];
                $obj->parent_dob_2 = $parent_dob_2;
                $obj->parent_occ_2 = $requestData['parent_occ_2'];
                $obj->parent_country_2 = $requestData['parent_country_2'];
                $obj->sibling_name = $requestData['sibling_name'];
                $obj->sibling_dob = $sibling_dob;
                $obj->sibling_occ = $requestData['sibling_occ'];
                $obj->sibling_gender = $requestData['sibling_gender'];
                $obj->sibling_country = $requestData['sibling_country'];
                $obj->sibling_marital = $requestData['sibling_marital'];
                $obj->sibling_name_2 = $requestData['sibling_name_2'];
                $obj->sibling_dob_2 = $sibling_dob_2;
                $obj->sibling_occ_2 = $requestData['sibling_occ_2'];
                $obj->sibling_gender_2 = $requestData['sibling_gender_2'];
                $obj->sibling_country_2 = $requestData['sibling_country_2'];
                $obj->sibling_marital_2 = $requestData['sibling_marital_2'];
                $obj->held_visa = $requestData['held_visa'];
                $obj->visa_refused = $requestData['visa_refused'];
                $obj->traveled = $requestData['traveled'];

    	     $save = $obj->save();
    	     if($save){
    	         return Redirect::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$requestData['client_id'])))->with('success', 'Record Updated Successfully');
    	     }else{
    	         return redirect()->back()->with('error', Config::get('constants.server_error'));
    	     }
    	}

    public function uploadmail(Request $request){
		$requestData 		= 	$request->all();
        $obj				= 	new \App\Models\MailReport;
		$obj->user_id		=	Auth::user()->id;
		$obj->from_mail 	=  $requestData['from'];
		$obj->to_mail 		=  $requestData['to'];
		$obj->subject		=  $requestData['subject'];
		$obj->message		=  $requestData['message'];
		$obj->mail_type		=  1;
		$obj->client_id		=  @$requestData['client_id'];
		$saved				=	$obj->save();
		if(!$saved) {
            return redirect()->back()->with('error', Config::get('constants.server_error'));
        } else {
            return redirect()->back()->with('success', 'Email uploaded Successfully');
        }
	}

    //upload inbox email
    public function uploadfetchmail(Request $request){ //dd($request->all());
         // ✅ Validate file input
        $validator = Validator::make($request->all(), [
            'email_files' => 'required',
            'email_files.*' => 'mimes:msg|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422); // Return validation errors with 422 status code
        }
        $id = $request->client_id;
        $client_info = \App\Models\Admin::select('client_id')->where('id', $id)->first();
        $client_id = !empty($client_info) ? $client_info->client_id : "";

        $doc_type = 'conversion_email_fetch';

        if ($request->hasfile('email_files')) {
            foreach ($request->file('email_files') as $file) {
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
                $name = time() . '-' . $fileName;
                $filePath = $client_id . '/' . $doc_type . '/inbox/' . $name;

                // Upload to S3
                Storage::disk('s3')->put($filePath, file_get_contents($file));

                // Save file details to database
                $obj = new \App\Models\Document;
                $obj->file_name = pathinfo($fileName, PATHINFO_FILENAME);
                $obj->filetype = pathinfo($fileName, PATHINFO_EXTENSION);
                $obj->user_id = Auth::user()->id;

                //$obj->myfile = $name;
                // Get the full URL of the uploaded file
                $fileUrl = Storage::disk('s3')->url($filePath);
                $obj->myfile = $fileUrl;
                $obj->myfile_key = $name;

                $obj->client_id = $id;
                $obj->type = $request->type;
                $obj->mail_type = "inbox";
                $obj->file_size = $size;
                $obj->doc_type = $doc_type;
                $obj->client_matter_id = $request->upload_inbox_mail_client_matter_id;
                $obj->save();

                // Fetch email content and save it to mail report
                $fileUploadedPath = $file->getPathname();
                $messageFactory = new MAPI\MapiMessageFactory();
                $documentFactory = new Pear\DocumentFactory();
                $ole = $documentFactory->createFromFile($fileUploadedPath);
                $message = $messageFactory->parseMessage($ole);

                $mail_subject = $message->properties['subject'];
                $mail_sender = $message->getSender();
                $mail_body = $message->getBody();
                $mail_to = array_map(fn($recipient) => (string)$recipient, $message->getRecipients());
                $mail_to_arr = implode(",", $mail_to);

                // Get mail sent time
                $sentTimeFinal = "";
                $sentTime = $message->getSendTime();
                if ($sentTime instanceof DateTime) {
                    $sentTime->modify('+5 hours 30 minutes');
                    $sentTimeFinal = $sentTime->format('d/m/Y h:i a');
                }

                // Save to MailReport
                $mailReport = new \App\Models\MailReport;
                $mailReport->user_id = Auth::user()->id;
                $mailReport->from_mail = $mail_sender;
                $mailReport->to_mail = $mail_to_arr;
                $mailReport->subject = $mail_subject;
                $mailReport->message = $mail_body;
                $mailReport->mail_type = 1;
                $mailReport->client_id = $id;
                $mailReport->conversion_type = $doc_type;
                $mailReport->mail_body_type = "inbox";
                $mailReport->uploaded_doc_id = $obj->id;
                $mailReport->client_matter_id = $request->upload_inbox_mail_client_matter_id;
                $mailReport->fetch_mail_sent_time = $sentTimeFinal;
                $mailReport->save();

                //Update date in client matter table
                if( isset($request->upload_inbox_mail_client_matter_id) && $request->upload_inbox_mail_client_matter_id != ""){
                    $obj1 = \App\Models\ClientMatter::find($request->upload_inbox_mail_client_matter_id);
                    $obj1->updated_at = date('Y-m-d H:i:s');
                    $obj1->save();
                }

                if($request->type == 'client'){
                    $subject = 'uploaded email document';
                    $objs = new ActivitiesLog;
                    $objs->client_id = $id;
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();
                }

            } //end foreach
            // ✅ Return success response
            return response()->json([
                'status' => true,
                'message' => 'Inbox email uploaded successfully',
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'File upload failed. Please try again.',
        ], 500);
    }

    //upload sent email
    public function uploadsentfetchmail(Request $request){ //dd($request->all());
         // ✅ Validate file input
         $validator = Validator::make($request->all(), [
            'email_files' => 'required',
            'email_files.*' => 'mimes:msg|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422); // Return validation errors with 422 status code
        }
        $id = $request->client_id;
        $client_info = \App\Models\Admin::select('client_id')->where('id', $id)->first();
        $client_id = !empty($client_info) ? $client_info->client_id : "";

        $doc_type = 'conversion_email_fetch';

        if ($request->hasfile('email_files')) {
            foreach ($request->file('email_files') as $file) {
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
                $name = time() . '-' . $fileName;
                $filePath = $client_id . '/' . $doc_type . '/sent/' . $name;

                // Upload to S3
                Storage::disk('s3')->put($filePath, file_get_contents($file));

                // Save file details to database
                $obj = new \App\Models\Document;
                $obj->file_name = pathinfo($fileName, PATHINFO_FILENAME);
                $obj->filetype = pathinfo($fileName, PATHINFO_EXTENSION);
                $obj->user_id = Auth::user()->id;

                //$obj->myfile = $name;
                // Get the full URL of the uploaded file
                $fileUrl = Storage::disk('s3')->url($filePath);
                $obj->myfile = $fileUrl;
                $obj->myfile_key = $name;

                $obj->client_id = $id;
                $obj->type = $request->type;
                $obj->mail_type = "sent";
                $obj->file_size = $size;
                $obj->doc_type = $doc_type;
                $obj->client_matter_id = $request->upload_sent_mail_client_matter_id;
                $obj->save();

                // Fetch email content and save it to mail report
                $fileUploadedPath = $file->getPathname();
                $messageFactory = new MAPI\MapiMessageFactory();
                $documentFactory = new Pear\DocumentFactory();
                $ole = $documentFactory->createFromFile($fileUploadedPath);
                $message = $messageFactory->parseMessage($ole);

                $mail_subject = $message->properties['subject'];
                $mail_sender = $message->getSender();
                $mail_body = $message->getBody();
                $mail_to = array_map(fn($recipient) => (string)$recipient, $message->getRecipients());
                $mail_to_arr = implode(",", $mail_to);

                // Get mail sent time
                $sentTimeFinal = "";
                $sentTime = $message->getSendTime();
                if ($sentTime instanceof DateTime) {
                    $sentTime->modify('+5 hours 30 minutes');
                    $sentTimeFinal = $sentTime->format('d/m/Y h:i a');
                }

                // Save to MailReport
                $mailReport = new \App\Models\MailReport;
                $mailReport->user_id = Auth::user()->id;
                $mailReport->from_mail = $mail_sender;
                $mailReport->to_mail = $mail_to_arr;
                $mailReport->subject = $mail_subject;
                $mailReport->message = $mail_body;
                $mailReport->mail_type = 1;
                $mailReport->client_id = $id;
                $mailReport->conversion_type = $doc_type;
                $mailReport->mail_body_type = "sent";
                $mailReport->uploaded_doc_id = $obj->id;
                $mailReport->client_matter_id = $request->upload_sent_mail_client_matter_id;
                $mailReport->fetch_mail_sent_time = $sentTimeFinal;
                $mailReport->save();

                //Update date in client matter table
                if( isset($request->upload_sent_mail_client_matter_id) && $request->upload_sent_mail_client_matter_id != ""){
                    $obj1 = \App\Models\ClientMatter::find($request->upload_sent_mail_client_matter_id);
                    $obj1->updated_at = date('Y-m-d H:i:s');
                    $obj1->save();
                }

                if($request->type == 'client'){
                    $subject = 'uploaded email document';
                    $objs = new ActivitiesLog;
                    $objs->client_id = $id;
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();
                }
            } //end foreach

            // ✅ Return success response
            return response()->json([
                'status' => true,
                'message' => 'Sent email uploaded successfully',
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'File upload failed. Please try again.',
        ], 500);
    }

    /*public function merge_records(Request $request){
        if(isset($request->merge_record_ids) && $request->merge_record_ids != ""){
            if( strpos($request->merge_record_ids, ',') !== false ) {
                $merge_record_ids_arr = explode(",",$request->merge_record_ids);
                //echo "<pre>";print_r($merge_record_ids_arr);

                //check 1st and 2nd record
                $first_record = Admin::where('id', $merge_record_ids_arr[0])->select('id','phone','att_phone','email','att_email')->first();
                //echo "<pre>";print_r($first_record);
                if(!empty($first_record)){
                    $first_phone = $first_record['phone'];
                    $first_att_phone = $first_record['att_phone'];
                    $first_email = $first_record['email'];
                    $first_att_email = $first_record['att_email'];
                }

                $second_record = Admin::where('id', $merge_record_ids_arr[1])->select('id','phone','att_phone','email','att_email')->first();
                //echo "<pre>";print_r($second_record);
                if(!empty($second_record)){
                    $second_phone = $second_record['phone'];
                    $second_att_phone = $second_record['att_phone'];
                    $second_email = $second_record['email'];
                    $second_att_email = $second_record['att_email'];
                }

               DB::table('admins')
                ->where('id', $merge_record_ids_arr[0])
                ->update(['att_phone' => $second_phone,'att_email' => $second_email]);

                DB::table('admins')
                ->where('id', $merge_record_ids_arr[1])
                ->update(['att_phone' => $first_phone,'att_email' => $first_email]);

                $notelist1 = Note::where('client_id', $merge_record_ids_arr[0])->whereNull('assigned_to')->where('type', 'client')->orderby('pin', 'DESC')->orderBy('created_at', 'DESC')->get();
                //dd($notelist1);

                $notelist2 = Note::where('client_id', $merge_record_ids_arr[1])->whereNull('assigned_to')->where('type', 'client')->orderby('pin', 'DESC')->orderBy('created_at', 'DESC')->get();
                //dd($notelist2);

                if(!empty($notelist2)){
                    foreach($notelist2 as $key2=>$list2){
                        $obj1 = new \App\Models\Note;
                        $obj1->user_id = $list2->user_id;
                        $obj1->client_id = $merge_record_ids_arr[0];
                        $obj1->lead_id = $list2->lead_id;
                        $obj1->title = $list2->title;
                        $obj1->description = $list2->description;
                        $obj1->mail_id = $list2->mail_id;
                        $obj1->type = $list2->type;
                        $obj1->pin = $list2->pin;
                        $obj1->followup_date = $list2->followup_date;
                        $obj1->folloup = $list2->folloup;
                        $obj1->assigned_to = $list2->assigned_to;
                        $obj1->status = $list2->status;
                        $obj1->task_group = $list2->task_group;
                        $saved1 = $obj1->save();
                    }
                }

                if(!empty($notelist1)){
                    foreach($notelist1 as $key1=>$list1){
                        $obj2 = new \App\Models\Note;
                        $obj2->user_id = $list1->user_id;
                        $obj2->client_id = $merge_record_ids_arr[1];
                        $obj2->lead_id = $list1->lead_id;
                        $obj2->title = $list1->title;
                        $obj2->description = $list1->description;
                        $obj2->mail_id = $list1->mail_id;
                        $obj2->type = $list1->type;
                        $obj2->pin = $list1->pin;
                        $obj2->followup_date = $list1->followup_date;
                        $obj2->folloup = $list1->folloup;
                        $obj2->assigned_to = $list1->assigned_to;
                        $obj2->status = $list1->status;
                        $obj2->task_group = $list1->task_group;
                        $saved2 = $obj2->save();
                    }
                }

                if($saved2){
                    $response['status'] 	= 	true;
				    $response['message']	=	'You have successfully merged records.';
                }else{
                    $response['status'] 	= 	false;
                    $response['message']	=	'Please try again';
                }
                echo json_encode($response);
            }
        }
    }*/

    public function merge_records(Request $request){
        $response = array();
        if(
            ( isset($request->merge_from) && $request->merge_from != "" )
            && ( isset($request->merge_into) && $request->merge_into != "" )
        ){
            //Update merge_into to be deleted
            DB::table('admins')->where('id',$request->merge_into)->update( array('is_deleted'=>1) );

            //activities_logs
            $activitiesLogs = DB::table('activities_logs')->where('client_id', $request->merge_from)->get(); //dd($activitiesLogs);
            if(!empty($activitiesLogs)){
                foreach($activitiesLogs as $actkey=>$actval){
                    DB::table('activities_logs')->insert(
                        [
                            'client_id' => $request->merge_into,
                            'created_by' => $actval->created_by,
                            'description' => $actval->description,
                            'created_at' => $actval->created_at,
                            'updated_at' => $actval->updated_at,
                            'subject' => $actval->subject,
                            'use_for' => $actval->use_for,
                            'followup_date' => $actval->followup_date,
                            'task_group' => $actval->task_group,
                            'task_status' => $actval->task_status
                        ]
                    );
                }
            }

            //notes
            $notes = DB::table('notes')->where('client_id', $request->merge_from)->get(); //dd($notes);
            if(!empty($notes)){
                foreach($notes as $notekey=>$noteval){
                    DB::table('notes')->insert(
                        [
                            'user_id'=> $noteval->user_id,
                            'client_id' => $request->merge_into,
                            'lead_id' => $noteval->lead_id,
                            'title' => $noteval->title,
                            'description' => $noteval->description,
                            'created_at' => $noteval->created_at,
                            'updated_at' => $noteval->updated_at,
                            'mail_id' => $noteval->mail_id,
                            'type' => $noteval->type,
                            'pin' => $noteval->pin,
                            'followup_date' => $noteval->followup_date,
                            'folloup' => $noteval->folloup,
                            'assigned_to' => $noteval->assigned_to,
                            'status' => $noteval->status,
                            'task_group' => $noteval->task_group,
                        ]
                    );
                }
            }

            //applications
            $applications = DB::table('applications')->where('client_id', $request->merge_from)->get(); //dd($applications);
            if(!empty($applications)){
                foreach($applications as $appkey=>$appval){
                    DB::table('applications')->insert(
                        [
                            'user_id'=> $appval->user_id,
                            'workflow' => $appval->workflow,
                            'partner_id' => $appval->partner_id,
                            'product_id' => $appval->product_id,
                            'status' => $appval->status,
                            'stage' => $appval->stage,
                            'sale_forcast' => $appval->sale_forcast,
                            'created_at' => $appval->created_at,
                            'updated_at' => $appval->updated_at,
                            'client_id' => $request->merge_into,
                            'branch' => $appval->branch,
                            'intakedate' => $appval->intakedate,
                            'start_date' => $appval->start_date,
                            'end_date' => $appval->end_date,
                            'expect_win_date' => $appval->expect_win_date,
                            'super_agent' => $appval->super_agent,
                            'sub_agent' => $appval->sub_agent,
                            'ratio' => $appval->ratio,
                            'client_revenue' => $appval->client_revenue,
                            'partner_revenue' => $appval->partner_revenue,
                            'discounts' => $appval->discounts,
                            'progresswidth' => $appval->progresswidth
                        ]
                    );
                }
            }

            //interested_services
            $interested_services = DB::table('interested_services')->where('client_id', $request->merge_from)->get(); //dd($interested_services);
            if(!empty($interested_services)){
                foreach($interested_services as $intkey=>$intval){
                    DB::table('interested_services')->insert(
                        [
                            'user_id'=> $intval->user_id,
                            'client_id' => $request->merge_into,
                            'workflow' => $intval->workflow,
                            'partner' => $intval->partner,
                            'product' => $intval->product,
                            'branch' => $intval->branch,
                            'start_date' => $intval->start_date,
                            'exp_date' => $intval->exp_date,
                            'status' => $intval->status,
                            'created_at' => $intval->created_at,
                            'updated_at' => $intval->updated_at,
                            'client_revenue' => $intval->client_revenue,
                            'partner_revenue' => $intval->partner_revenue,
                            'discounts' => $intval->discounts
                        ]
                    );
                }
            }

            //education documents and migration documents
            $documents = DB::table('documents')->where('client_id', $request->merge_from)->get(); //dd($documents);
            if(!empty($documents)){
                foreach($documents as $dockey=>$docval){
                    DB::table('documents')->insert(
                        [
                            'document'=> $docval->document,
                            'filetype' => $docval->filetype,
                            'myfile' => $docval->myfile,
                            'user_id' => $docval->user_id,
                            'client_id' => $request->merge_into,
                            'file_size' => $docval->file_size,
                            'type' => $docval->type,
                            'doc_type' => $docval->doc_type,
                            'created_at' => $docval->created_at,
                            'updated_at' => $docval->updated_at
                        ]
                    );
                }
            }

            //appointments
            $appointments = DB::table('appointments')->where('client_id', $request->merge_from)->get(); //dd($appointments);
            if(!empty($appointments)){
                foreach($appointments as $appkey=>$appval){
                    DB::table('appointments')->insert(
                        [
                            'user_id'=> $appval->user_id,
                            'client_id' => $request->merge_into,
                            'service_id' => $appval->service_id,
                            'noe_id' => $appval->noe_id,
                            'full_name' => $appval->full_name,
                            'email' => $appval->email,
                            'phone' => $appval->phone,
                            'timezone' => $appval->timezone,
                            'date' => $appval->date,
                            'time' => $appval->time,
                            'timeslot_full' => $appval->timeslot_full,
                            'title' => $appval->title,
                            'description' => $appval->description,
                            'invites' => $appval->invites,
                            'appointment_details' => $appval->appointment_details,
                            'status' => $appval->status,
                            'assignee' => $appval->assignee,
                            'priority' => $appval->priority,
                            'priority_no' => $appval->priority_no,
                            'created_at' => $appval->created_at,
                            'updated_at' => $appval->updated_at,
                            'related_to' => $appval->related_to,
                            'order_hash' => $appval->order_hash
                        ]
                    );
                }
            }

            //quotations
            $quotations = DB::table('quotations')->where('client_id', $request->merge_from)->get(); //dd($quotations);
            if(!empty($quotations)){
                foreach($quotations as $quotekey=>$quoteval){
                    DB::table('quotations')->insert(
                        [
                            'client_id' => $request->merge_into,
                            'user_id'=> $quoteval->user_id,
                            'total_fee' => $quoteval->total_fee,
                            'status' => $quoteval->status,
                            'due_date' => $quoteval->due_date,
                            'created_by' => $quoteval->created_by,
                            'created_at' => $quoteval->created_at,
                            'updated_at' => $quoteval->updated_at,
                            'currency' => $quoteval->currency,
                            'is_archive' => $quoteval->is_archive
                        ]
                    );
                }
            }

            //accounts
            $accounts = DB::table('invoices')->where('client_id', $request->merge_from)->get(); //dd($accounts);
            if(!empty($accounts)){
                foreach($accounts as $acckey=>$accval){
                    DB::table('invoices')->insert(
                        [
                            'invoice_no'=> $accval->invoice_no,
                            'user_id' => $accval->user_id,
                            'client_id' => $request->merge_into,
                            'application_id' => $accval->application_id,
                            'type' => $accval->type,
                            'invoice_date' => $accval->invoice_date,
                            'due_date' => $accval->due_date,
                            'discount' => $accval->discount,
                            'discount_date' => $accval->discount_date,
                            'net_fee_rec' => $accval->net_fee_rec,
                            'notes' => $accval->notes,
                            'payment_option' => $accval->payment_option,
                            'attachments' => $accval->attachments,
                            'status' => $accval->status,
                            'currency' => $accval->currency,
                            'created_at' => $accval->created_at,
                            'updated_at' => $accval->updated_at,
                            'profile' => $accval->profile
                        ]
                    );
                }
            }

            //Conversations
            $conversations = DB::table('mail_reports')->where('client_id', $request->merge_from)->get(); //dd($conversations);
            if(!empty($conversations)){
                foreach($conversations as $mailkey=>$mailval){
                    DB::table('mail_reports')->insert(
                        [
                            'user_id' => $mailval->user_id,
                            'from_mail' => $mailval->from_mail,
                            'to_mail' => $mailval->to_mail,
                            'cc' => $mailval->cc,
                            'template_id' => $mailval->template_id,
                            'subject' => $mailval->subject,
                            'message' => $mailval->message,
                            'created_at' => $mailval->created_at,
                            'updated_at' => $mailval->updated_at,
                            'type' => $mailval->type,
                            'reciept_id' => $mailval->reciept_id,
                            'attachments' => $mailval->attachments,
                            'mail_type' => $mailval->mail_type,
                            'client_id' => $request->merge_into
                        ]
                    );
                }
            }

            //Tasks
            $tasks = DB::table('tasks')->where('client_id', $request->merge_from)->get(); //dd($tasks);
            if(!empty($tasks)){
                foreach($tasks as $taskkey=>$taskval){
                    DB::table('tasks')->insert(
                        [
                            'title' => $taskval->user_id,
                            'category' => $taskval->from_mail,
                            'assignee' => $taskval->to_mail,
                            'priority' => $taskval->cc,
                            'due_date' => $taskval->template_id,
                            'due_time' => $taskval->subject,
                            'description' => $taskval->message,
                            'related_to' => $taskval->created_at,
                            'contact_name' => $taskval->updated_at,
                            'partner_name' => $taskval->type,
                            'client_name' => $taskval->reciept_id,
                            'application' => $taskval->attachments,
                            'stage' => $taskval->mail_type,
                            'followers' => $taskval->mail_type,
                            'attachments' => $taskval->mail_type,
                            'created_at' => $taskval->mail_type,
                            'updated_at' => $taskval->mail_type,
                            'mailid' => $taskval->mail_type,
                            'user_id' => $taskval->mail_type,
                            'client_id' => $request->merge_into,
                            'status' => $taskval->mail_type,
                            'type' => $taskval->mail_type,
                            'priority_no' => $taskval->mail_type,
                            'is_archived' => $taskval->mail_type,
                            'group_id' => $taskval->mail_type
                        ]
                    );
                }
            }

            //Education
            $educations = DB::table('education')->where('client_id', $request->merge_from)->get(); //dd($educations);
            if(!empty($educations)){
                foreach($educations as $edukey=>$eduval){
                    DB::table('education')->insert(
                        [
                             'user_id' => $eduval->user_id,
                             'client_id' => $request->merge_into,
                             'degree_title' => $eduval->degree_title,
                             'degree_level' => $eduval->degree_level,
                             'institution' => $eduval->institution,
                             'course_start' => $eduval->course_start,
                             'course_end' => $eduval->course_end,
                             'subject_area' => $eduval->subject_area,
                             'subject' => $eduval->subject,
                             'ac_score' => $eduval->ac_score,
                             'score' => $eduval->score,
                             'created_at' => $eduval->created_at,
                             'updated_at' => $eduval->updated_at
                        ]
                    );
                }
            }

            //CheckinLogs
            $checkinLogs = DB::table('checkin_logs')->where('client_id', $request->merge_from)->get(); //dd($checkinLogs);
            if(!empty($checkinLogs)){
                foreach($checkinLogs as $checkkey=>$checkval){
                    DB::table('checkin_logs')->insert(
                        [
                             'client_id' => $request->merge_into,
                             'contact_type' => $checkval->contact_type,
                             'user_id' => $checkval->user_id,
                             'visit_purpose' => $checkval->visit_purpose,
                             'status' => $checkval->status,
                             'date' => $checkval->date,
                             'sesion_start' => $checkval->sesion_start,
                             'sesion_end' => $checkval->sesion_end,
                             'created_at' => $checkval->created_at,
                             'updated_at' => $checkval->updated_at,
                             'wait_time' => $checkval->wait_time,
                             'attend_time' => $checkval->attend_time,
                             'is_archived' => $checkval->is_archived,
                             'office' => $checkval->office,
                             'wait_type' => $checkval->wait_type
                        ]
                    );
                }
            }

            //Previous History
            $prevHis = DB::table('admins')->where('id', $request->merge_from)->select('id','prev_visa')->get(); //dd($prevHis);
            if(!empty($prevHis)){
               DB::table('admins')->where('id',$request->merge_into)->update( array('prev_visa'=>$prevHis[0]->prev_visa) );
            }

            //Client Info Form
            $clientInfo = DB::table('online_forms')->where('client_id', $request->merge_from)->get(); //dd($clientInfo);
            if(!empty($clientInfo)){
                foreach($clientInfo as $clientkey=>$clientval){
                    DB::table('online_forms')->insert(
                        [
                             'client_id' => $request->merge_into,
                             'type' => $clientval->type,
                             'info_name' => $clientval->info_name,
                             'main_lang' => $clientval->main_lang,
                             'marital_status' => $clientval->marital_status,
                             'mobile' => $clientval->mobile,
                             'curr_address' => $clientval->curr_address,
                             'email' => $clientval->email,
                             'parent_name' => $clientval->parent_name,
                             'parent_dob' => $clientval->parent_dob,
                             'parent_occ' => $clientval->parent_occ,
                             'parent_country' => $clientval->parent_country,
                             'parent_name_2' => $clientval->parent_name_2,
                             'parent_dob_2' => $clientval->parent_dob_2,
                             'parent_occ_2' => $clientval->parent_occ_2,
                             'parent_country_2' => $clientval->parent_country_2,
                             'sibling_name' => $clientval->sibling_name,
                             'sibling_dob' => $clientval->sibling_dob,
                             'sibling_occ' => $clientval->sibling_occ,
                             'sibling_gender' => $clientval->sibling_gender,
                             'sibling_country' => $clientval->sibling_country,
                             'sibling_marital' => $clientval->sibling_marital,
                             'sibling_name_2' => $clientval->sibling_name_2,
                             'sibling_dob_2' => $clientval->sibling_dob_2,
                             'sibling_occ_2' => $clientval->sibling_occ_2,
                             'sibling_gender_2' => $clientval->sibling_gender_2,
                             'sibling_country_2' => $clientval->sibling_country_2,
                             'sibling_marital_2' => $clientval->sibling_marital_2,
                             'held_visa' => $clientval->held_visa,
                             'visa_refused' => $clientval->visa_refused,
                             'traveled' => $clientval->traveled,
                             'created_at' => $clientval->created_at,
                             'updated_at' => $clientval->updated_at
                        ]
                    );
                }
            }
        }
        $response['status'] 	= 	true;
        $response['message']	=	'You have successfully merged records.';
        echo json_encode($response);
    }

    //Update email to be verified wrt client id
    public function updateemailverified(Request $request)
    {
        $data = $request->all(); //dd($data);
        $recExist = Admin::where('id', $data['client_id'])
        ->update(['manual_email_phone_verified' => $data['manual_email_phone_verified']]);
         if($recExist){

            // Log the activity
            $activity = new \App\Models\ActivitiesLog();
            $activity->client_id = $data['client_id'];
            $activity->created_by = auth()->user()->id; // Current admin's ID
            $activity->subject = 'Email verification status updated';
            $activity->description = auth()->user()->first_name . ' updated the email verification status to ' . ($data['manual_email_phone_verified'] == 1 ? 'verified' : 'unverified');
            $activity->save();

             $response['status'] 	= 	true;
             $response['message']	=	'Record updated successfully';
         } else {
             $response['status'] 	= 	false;
             $response['message']	=	'Please try again';
         }
         echo json_encode($response);
    }

    //address_auto_populate
    public function address_auto_populate(Request $request){
        $address = $request->address;
        if( isset($address) && $address != ""){
            $result = app('geocoder')->geocode($address)->get(); //dd($result[0]);
            $postalCode = $result[0]->getPostalCode();
            $locality = $result[0]->getLocality();
            if( !empty($result) ){
                $response['status'] 	= 	1;
                $response['postal_code'] = 	$postalCode;
                $response['locality'] 	= 	$locality;
                $response['message']	=	"address is success.";
            } else {
                $response['status'] 	= 	0;
                $response['postal_code'] = 	"";
                $response['locality']    = 	"";
                $response['message']	=	"address is wrong.";
            }
            echo json_encode($response);
        }
    }

    //not picked call button click
    public function notpickedcall(Request $request){
        $data = $request->all(); //dd($data);
        //Get user Phone no and send message via cellcast
        $userInfo = Admin::select('id','country_code','phone')->where('id', $data['id'])->first();//dd($userInfo);
        if ( $userInfo) {
            //$message = 'Call not picked.SMS sent successfully!';
            $message = $data['message'];
            $userPhone = $userInfo->country_code."".$userInfo->phone;
            $this->smsService->sendSms($userPhone,$message);
        }
        $recExist = Admin::where('id', $data['id'])->update(['not_picked_call' => $data['not_picked_call']]);
        if($recExist){
            if($data['not_picked_call'] == 1){ //if checked true
                $objs = new ActivitiesLog;
                $objs->client_id = $data['id'];
                $objs->created_by = Auth::user()->id;
                $objs->description = '<span class="text-semi-bold">Call not picked.SMS sent successfully!</span>';
                //$objs->subject = "Call not picked";
                $objs->save();

                $response['status'] 	= 	true;
                $response['message']	=	'Call not picked.SMS sent successfully!';
                $response['not_picked_call'] 	= 	$data['not_picked_call'];
            }
            else if($data['not_picked_call'] == 0){
                $response['status'] 	= 	true;
                $response['message']	=	'You have updated call not picked bit. Please try again';
                $response['not_picked_call'] 	= 	$data['not_picked_call'];
            }
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
            $response['not_picked_call'] 	= 	$data['not_picked_call'];
        }
        echo json_encode($response);
    }

    public function deleteactivitylog(Request $request){
		$activitylogid = $request->activitylogid; //dd($activitylogid);
		if(\App\Models\ActivitiesLog::where('id',$activitylogid)->exists()){
			$data = \App\Models\ActivitiesLog::select('client_id','subject','description')->where('id',$activitylogid)->first();
			$res = DB::table('activities_logs')->where('id', @$activitylogid)->delete();
			if($res){
				$response['status'] 	= 	true;
			    $response['data']	=	$data;
			}else{
				$response['status'] 	= 	false;
			    $response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

    public function pinactivitylog(Request $request){
		$requestData = $request->all();
        if(\App\Models\ActivitiesLog::where('id',$requestData['activity_id'])->exists()){
			$activity = \App\Models\ActivitiesLog::where('id',$requestData['activity_id'])->first();
			if($activity->pin == 0){
				$obj = \App\Models\ActivitiesLog::find($activity->id);
				$obj->pin = 1;
				$saved = $obj->save();
			}else{
				$obj = \App\Models\ActivitiesLog::find($activity->id);
				$obj->pin = 0;
				$saved = $obj->save();
			}
			$response['status'] 	= 	true;
			$response['message']	=	'Pin Option added successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Record not found';
		}
		echo json_encode($response);
	}

    public function createservicetaken(Request $request){ //dd( $request->all() );
        $id = $request->logged_client_id;
        if( \App\Models\Admin::where('id',$id)->exists() ) {
            $entity_type = $request->entity_type;
            if($entity_type == 'add') {
                $obj	= 	new clientServiceTaken;
                $obj->client_id = $id;
                $obj->service_type = $request->service_type;
                $obj->mig_ref_no = $request->mig_ref_no;
                $obj->mig_service = $request->mig_service;
                $obj->mig_notes = $request->mig_notes;
                $obj->edu_course = $request->edu_course;
                $obj->edu_college = $request->edu_college;
                $obj->edu_service_start_date = $request->edu_service_start_date;
                $obj->edu_notes = $request->edu_notes;
                $obj->is_saved_db = 0;
                $saved = $obj->save();
            }
            else if($entity_type == 'edit') {
                $saved = DB::table('client_service_takens')
                ->where('id', $request->entity_id)
                ->update([
                    'service_type' => $request->service_type,

                    'mig_ref_no' => $request->mig_ref_no,
                    'mig_service' => $request->mig_service,
                    'mig_notes' => $request->mig_notes,

                    'edu_course' => $request->edu_course,
                    'edu_college' => $request->edu_college,
                    'edu_service_start_date' => $request->edu_service_start_date,
                    'edu_notes' => $request->edu_notes
                ]);
            }
            if($saved){
               $response['status'] 	= 	true;
               $response['message']	=	'success';
               $user_rec = DB::table('client_service_takens')->where('client_id', $id)->where('is_saved_db', 0)->orderBy('id', 'desc')->get();
               $response['user_rec'] = 	$user_rec;
            } else {
                $response['status'] 	= 	true;
                $response['message']	=	'success';
                $response['user_rec'] = 	array();
            }
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'fail';
            $response['result_str'] = 	array();
        }
        echo json_encode($response);
    }

    public function removeservicetaken(Request $request){ //dd( $request->all() );
        $sel_service_taken_id = $request->sel_service_taken_id;
		if( DB::table('client_service_takens')->where('id', $sel_service_taken_id)->exists() ){
			$res = DB::table('client_service_takens')->where('id', @$sel_service_taken_id)->delete();
			if($res){
				$response['status'] 	= 	true;
			    $response['record_id']	=	$sel_service_taken_id;
                $response['message']	=	'Service removed successfully';
			} else {
				$response['status'] 	= 	false;
			    $response['record_id']	=	$sel_service_taken_id;
                $response['message']	=	'Service not removed';
			}
		}else{
			$response['status'] 	= 	false;
            $response['record_id']	=	$sel_service_taken_id;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
    }

    public function getservicetaken(Request $request){ //dd( $request->all() );
        $sel_service_taken_id = $request->sel_service_taken_id;
        if( DB::table('client_service_takens')->where('id', $sel_service_taken_id)->exists() ){
			$res = DB::table('client_service_takens')->where('id', @$sel_service_taken_id)->first();//dd($res);
            if($res){
               $response['status'] 	= 	true;
               $response['message']	=	'success';
               $response['user_rec'] = 	$res;
            } else {
                $response['status'] 	= 	true;
                $response['message']	=   'success';
                $response['user_rec']   = 	array();
            }
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'fail';
            $response['user_rec'] = 	array();
        }
        echo json_encode($response);
    }

    //Save client account reports
    public function saveaccountreport(Request $request, $id = NULL)
    {
        $requestData = $request->all(); //dd($requestData);
        $response = [];

        // Handle document upload
        $insertedDocId = "";
        $doc_saved = false;
        $client_unique_id = "";
        $awsUrl = "";
        $doctype = isset($request->doctype) ? $request->doctype : '';

        if ($request->hasfile('document_upload')) {
            $files = is_array($request->file('document_upload')) ? $request->file('document_upload') : [$request->file('document_upload')];

            $client_info = \App\Models\Admin::select('client_id')->where('id', $requestData['client_id'])->first();
            $client_unique_id = !empty($client_info) ? $client_info->client_id : "";

            foreach ($files as $file) {
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
                $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
                $fileExtension = $file->getClientOriginalExtension();
                $name = time() . $file->getClientOriginalName();
                $filePath = $client_unique_id . '/' . $doctype . '/' . $name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));

                $obj = new \App\Models\Document;
                $obj->file_name = $nameWithoutExtension;
                $obj->filetype = $fileExtension;
                $obj->user_id = Auth::user()->id;
                $obj->myfile = Storage::disk('s3')->url($filePath);
                $obj->myfile_key = $name;
                $obj->client_id = $requestData['client_id'];
                $obj->type = $request->type;
                $obj->file_size = $size;
                $obj->doc_type = $doctype;
                $doc_saved = $obj->save();
                $insertedDocId = $obj->id;
            }
        } else {
            $insertedDocId = "";
            $doc_saved = "";
        }

        if (isset($requestData['trans_date'])) {
            // Generate unique receipt id
            $is_record_exist = DB::table('account_client_receipts')->select('receipt_id')->where('receipt_type', 1)->orderBy('receipt_id', 'desc')->first();
            $receipt_id = !$is_record_exist ? 1 : $is_record_exist->receipt_id + 1;

            $finalArr = [];
            $running_balance = floatval($requestData['client_ledger_balance_amount']);
            $saved = false;

            // Group entries by invoice_no for Fee Transfer
            $feeTransferByInvoice = [];
            for ($i = 0; $i < count($requestData['trans_date']); $i++) {
                $clientFundLedgerType = $requestData['client_fund_ledger_type'][$i];
                $invoiceNo = isset($requestData['invoice_no'][$i]) && $requestData['invoice_no'][$i] != "" ? $requestData['invoice_no'][$i] : null;

                if ($clientFundLedgerType === 'Fee Transfer' && $invoiceNo) {
                    if (!isset($feeTransferByInvoice[$invoiceNo])) {
                        $feeTransferByInvoice[$invoiceNo] = [];
                    }
                    $feeTransferByInvoice[$invoiceNo][] = [
                        'index' => $i,
                        'withdraw_amount' => floatval($requestData['withdraw_amount'][$i] ?? 0),
                        'trans_date' => $requestData['trans_date'][$i],
                        'entry_date' => $requestData['entry_date'][$i],
                        'description' => $requestData['description'][$i],
                    ];
                }
            }

            // Process Fee Transfers with invoice numbers
            foreach ($feeTransferByInvoice as $invoiceNo => $feeTransfers) {
                $totalWithdrawAmount = array_sum(array_column($feeTransfers, 'withdraw_amount'));

                // Validate Fee Transfer amount against Current Funds Held
                // Get the current funds held using the same logic as displayed in UI
                $latest_balance = DB::table('account_client_receipts')
                    ->where('client_id', $requestData['client_id'])
                    ->where('client_matter_id', $requestData['client_matter_id'])
                    ->where('receipt_type', 1)
                    ->orderBy('id', 'desc')
                    ->value('balance_amount');
                
                $currentFundsHeld = is_numeric($latest_balance) ? floatval($latest_balance) : 0;
                
                if ($totalWithdrawAmount > $currentFundsHeld) {
                    $response['status'] = false;
                    $response['message'] = 'You cannot transfer the amount greater than of Current Funds Held amount';
                    $response['requestData'] = [];
                    $response['awsUrl'] = "";
                    $response['invoices'] = [];
                    return response()->json($response, 200);
                }

                // Get invoice details
                $invoiceInfo = DB::table('account_client_receipts')
                    ->select('withdraw_amount', 'partial_paid_amount', 'balance_amount')
                    ->where('client_id', $requestData['client_id'])
                    ->where('receipt_type', 3)
                    ->where('invoice_no', $invoiceNo)
                    ->first();

                if ($invoiceInfo) {
                    $invoiceWithdrawAmount = floatval($invoiceInfo->withdraw_amount);
                    $currentPartialPaid = floatval($invoiceInfo->partial_paid_amount ?? 0);
                    $currentBalance = floatval($invoiceInfo->balance_amount ?? $invoiceWithdrawAmount);

                    // Process Fee Transfers
                    $remainingWithdraw = $totalWithdrawAmount;
                    $newPartialPaid = $currentPartialPaid;

                    foreach ($feeTransfers as $feeTransfer) {
                        $i = $feeTransfer['index'];
                        if ($remainingWithdraw <= 0) break;

                        $amountToUse = min($remainingWithdraw, $feeTransfer['withdraw_amount']);
                        if ($amountToUse <= 0) continue;

                        // Adjust amount if it exceeds the invoice's withdraw amount
                        if ($newPartialPaid + $amountToUse > $invoiceWithdrawAmount) {
                            $amountToUse = $invoiceWithdrawAmount - $newPartialPaid;
                        }

                        if ($amountToUse <= 0) continue;

                        $trans_no = $this->createTransactionNumber('Fee Transfer');
                        $deposit = 0;
                        $withdraw = $amountToUse;

                        $running_balance += $deposit - $withdraw;

                        $saved = DB::table('account_client_receipts')->insert([
                            'user_id' => $requestData['loggedin_userid'],
                            'client_id' => $requestData['client_id'],
                            'client_matter_id' => $requestData['client_matter_id'],
                            'receipt_id' => $receipt_id,
                            'receipt_type' => $requestData['receipt_type'],
                            'trans_date' => $feeTransfer['trans_date'],
                            'entry_date' => $feeTransfer['entry_date'],
                            'invoice_no' => $invoiceNo,
                            'trans_no' => $trans_no,
                            'client_fund_ledger_type' => 'Fee Transfer',
                            'description' => $feeTransfer['description'],
                            'deposit_amount' => $deposit,
                            'withdraw_amount' => $amountToUse,
                            'balance_amount' => $running_balance,
                            'uploaded_doc_id' => $insertedDocId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $finalArr[] = [
                            'trans_date' => $feeTransfer['trans_date'],
                            'entry_date' => $feeTransfer['entry_date'],
                            'client_fund_ledger_type' => 'Fee Transfer',
                            'trans_no' => $trans_no,
                            'invoice_no' => $invoiceNo,
                            'description' => $feeTransfer['description'],
                            'deposit_amount' => $deposit,
                            'withdraw_amount' => $amountToUse,
                            'balance_amount' => $running_balance,
                        ];

                        $newPartialPaid += $amountToUse;
                        $remainingWithdraw -= $amountToUse;
                    }

                    // Handle excess amount by creating additional Fee Transfer linked to the same invoice
                    if ($remainingWithdraw > 0) {
                        $trans_no = $this->createTransactionNumber('Fee Transfer');
                        $deposit = 0;
                        $withdraw = $remainingWithdraw;

                        $running_balance += $deposit - $withdraw;

                        $saved = DB::table('account_client_receipts')->insert([
                            'user_id' => $requestData['loggedin_userid'],
                            'client_id' => $requestData['client_id'],
                            'client_matter_id' => $requestData['client_matter_id'],
                            'receipt_id' => $receipt_id,
                            'receipt_type' => $requestData['receipt_type'],
                            'trans_date' => $feeTransfers[0]['trans_date'],
                            'entry_date' => $feeTransfers[0]['entry_date'],
                            'invoice_no' => $invoiceNo,
                            'trans_no' => $trans_no,
                            'client_fund_ledger_type' => 'Fee Transfer',
                            'description' => $feeTransfers[0]['description'],
                            'deposit_amount' => $deposit,
                            'withdraw_amount' => $withdraw,
                            'balance_amount' => $running_balance,
                            'uploaded_doc_id' => $insertedDocId,
                            'extra_amount_receipt' => 'exceed',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $finalArr[] = [
                            'trans_date' => $feeTransfers[0]['trans_date'],
                            'entry_date' => $feeTransfers[0]['entry_date'],
                            'client_fund_ledger_type' => 'Fee Transfer',
                            'trans_no' => $trans_no,
                            'invoice_no' => $invoiceNo,
                            'description' => $feeTransfers[0]['description'],
                            'deposit_amount' => $deposit,
                            'withdraw_amount' => $withdraw,
                            'balance_amount' => $running_balance,
                            'extra_amount_receipt' => 'exceed'
                        ];
                    }

                    // Update invoice status
                    $newBalance = $invoiceWithdrawAmount - $newPartialPaid;
                    $status = ($newBalance <= 0) ? 1 : 2; // Paid or Partial

                    DB::table('account_client_receipts')
                        ->where('client_id', $requestData['client_id'])
                        ->where('receipt_type', 3)
                        ->where('invoice_no', $invoiceNo)
                        ->update([
                            'invoice_status' => $status,
                            'partial_paid_amount' => $newPartialPaid,
                            'balance_amount' => $newBalance,
                            'updated_at' => now(),
                        ]);

                    DB::table('account_all_invoice_receipts')
                        ->where('client_id', $requestData['client_id'])
                        ->where('receipt_type', 3)
                        ->where('invoice_no', $invoiceNo)
                        ->update([
                            'invoice_status' => $status,
                            'updated_at' => now(),
                        ]);

                    $response['invoices'][] = [
                        'invoice_no' => $invoiceNo,
                        'invoice_status' => $status,
                        'invoice_balance' => $newBalance,
                        'outstanding_balance' => $newBalance,
                    ];
                }
            }

            // Process remaining entries (non-Fee Transfer or Fee Transfer without invoice)
            for ($i = 0; $i < count($requestData['trans_date']); $i++) {
                $clientFundLedgerType = $requestData['client_fund_ledger_type'][$i];
                $invoiceNo = isset($requestData['invoice_no'][$i]) && $requestData['invoice_no'][$i] != "" ? $requestData['invoice_no'][$i] : null;

                // Skip Fee Transfers with invoice numbers as they are already processed
                if ($clientFundLedgerType === 'Fee Transfer' && $invoiceNo) {
                    continue;
                }

                $trans_no = $this->createTransactionNumber($clientFundLedgerType);
                $deposit = floatval($requestData['deposit_amount'][$i] ?? 0);
                $withdraw = floatval($requestData['withdraw_amount'][$i] ?? 0);

                // Validate Fee Transfer amount against Current Funds Held (for Fee Transfer without invoice)
                if ($clientFundLedgerType === 'Fee Transfer' && !$invoiceNo) {
                    // Get the current funds held using the same logic as displayed in UI
                    $latest_balance = DB::table('account_client_receipts')
                        ->where('client_id', $requestData['client_id'])
                        ->where('client_matter_id', $requestData['client_matter_id'])
                        ->where('receipt_type', 1)
                        ->orderBy('id', 'desc')
                        ->value('balance_amount');
                    
                    $currentFundsHeld = is_numeric($latest_balance) ? floatval($latest_balance) : 0;
                    
                    if ($withdraw > $currentFundsHeld) {
                        $response['status'] = false;
                        $response['message'] = 'You cannot transfer the amount greater than of Current Funds Held amount';
                        $response['requestData'] = [];
                        $response['awsUrl'] = "";
                        $response['invoices'] = [];
                        return response()->json($response, 200);
                    }
                }

                $running_balance += $deposit - $withdraw;

                $saved = DB::table('account_client_receipts')->insert([
                    'user_id' => $requestData['loggedin_userid'],
                    'client_id' => $requestData['client_id'],
                     'client_matter_id' => $requestData['client_matter_id'],
                    'receipt_id' => $receipt_id,
                    'receipt_type' => $requestData['receipt_type'],
                    'trans_date' => $requestData['trans_date'][$i],
                    'entry_date' => $requestData['entry_date'][$i],
                    'invoice_no' => $invoiceNo ?? '',
                    'trans_no' => $trans_no,
                    'client_fund_ledger_type' => $clientFundLedgerType,
                    'description' => $requestData['description'][$i],
                    'deposit_amount' => $deposit,
                    'withdraw_amount' => $withdraw,
                    'balance_amount' => $running_balance,
                    'uploaded_doc_id' => $insertedDocId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $finalArr[] = [
                    'trans_date' => $requestData['trans_date'][$i],
                    'entry_date' => $requestData['entry_date'][$i],
                    'client_fund_ledger_type' => $clientFundLedgerType,
                    'trans_no' => $trans_no,
                    'invoice_no' => $invoiceNo ?? '',
                    'description' => $requestData['description'][$i],
                    'deposit_amount' => $deposit,
                    'withdraw_amount' => $withdraw,
                    'balance_amount' => $running_balance,
                ];
            }

            // Log activity
            if ($saved) {
                $subject = $doc_saved ? 'added client funds ledger with its document. Reference no- ' . $trans_no : 'added client funds ledger. Reference no- ' . $trans_no;
                if ($request->type == 'client') {
                    $objs = new \App\Models\ActivitiesLog;
                    $objs->client_id = $requestData['client_id'];
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();
                }
            }
        }

        // Prepare response
        if ($saved) {
            $response['status'] = true;
            $response['requestData'] = $finalArr;
            $response['db_total_balance_amount'] = $running_balance;
            $response['message'] = $doc_saved ? 'Client receipt with document added successfully' : 'Client receipt added successfully';
            if ($doc_saved) {
                $url = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                $awsUrl = $url . $client_unique_id . '/' . $doctype . '/' . $name;
                $response['awsUrl'] = $awsUrl;
            } else {
                $response['awsUrl'] = "";
            }
        } else {
            $response['status'] = false;
            $response['requestData'] = [];
            $response['awsUrl'] = "";
            $response['message'] = 'Please try again';
            $response['invoices'] = [];
        }

        return response()->json($response, 200);
    }

    private function createTransactionNumber($clientFundLedgerType)
    {
        switch ($clientFundLedgerType) {
            case 'Deposit':
                $prefix = 'CFL';
                break;
            case 'Fee Transfer':
                $prefix = 'FEE';
                break;
            case 'Disbursement':
                $prefix = 'DIS';
                break;
            case 'Refund':
                $prefix = 'REF';
                break;
            default:
                $prefix = 'CFL';
        }

        $latestTrans = DB::table('account_client_receipts')
            ->select('trans_no')
            ->where('receipt_type', 1)
            ->where('client_fund_ledger_type', $clientFundLedgerType)
            ->where('trans_no', 'LIKE', "$prefix-%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$latestTrans) {
            $nextNumber = 1;
        } else {
            $lastTransNo = explode('-', $latestTrans->trans_no);
            $lastNumber = isset($lastTransNo[1]) ? (int)$lastTransNo[1] : 0;
            $nextNumber = $lastNumber + 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    //Save adjust invoice reports
    public function saveadjustinvoicereport(Request $request, $id = NULL)
    {
        $requestData = $request->all();
        //echo '<pre>'; print_r($requestData); die;
        if( $requestData['function_type'] == 'add')
        {
            if(isset($requestData['trans_date'])){
                //Generate unique receipt id
                $is_record_exist = DB::table('account_client_receipts')->select('receipt_id')->where('receipt_type',3)->orderBy('receipt_id', 'desc')->first();
                //dd($is_record_exist);
                if(!$is_record_exist){
                    $receipt_id = 1;
                } else {
                    $receipt_id = $is_record_exist->receipt_id +1;
                }
                $finalArr = array();
                $totalWithdrawAmount = 0;
                for($i=0; $i<count($requestData['trans_date']); $i++){
                    $finalArr[$i]['trans_date'] = $requestData['trans_date'][$i];
                    $finalArr[$i]['entry_date'] = $requestData['entry_date'][$i];
                    $finalArr[$i]['trans_no'] = $requestData['invoice_no'];
                    $finalArr[$i]['gst_included'] = $requestData['gst_included'][$i];
                    $finalArr[$i]['payment_type'] = $requestData['payment_type'][$i];
                    $finalArr[$i]['description'] = $requestData['description'][$i];
                    $finalArr[$i]['withdraw_amount'] = $requestData['withdraw_amount'][$i];
                    $finalArr[$i]['balance_amount'] = $requestData['withdraw_amount'][$i];
                    $finalArr[$i]['invoice_no'] = $requestData['invoice_no'];
                    $finalArr[$i]['save_type'] = $requestData['save_type'];
                    $finalArr[$i]['receipt_id'] = $receipt_id;

                    $invoice_status = 1; //paid
                    $finalArr[$i]['invoice_status'] = $invoice_status; //unpaid

                    $lastInsertId	= DB::table('account_all_invoice_receipts')->insertGetId([
                        'user_id' => $requestData['loggedin_userid'],
                        'client_id' =>  $requestData['client_id'],
                        'receipt_id'=>  $receipt_id,
                        'receipt_type' => $requestData['receipt_type'],
                        'trans_date' => $requestData['trans_date'][$i],
                        'entry_date' => $requestData['entry_date'][$i],
                        'gst_included' => $requestData['gst_included'][$i],
                        'payment_type' => $requestData['payment_type'][$i],
                        'trans_no' => $requestData['invoice_no'],
                        'description' => $requestData['description'][$i],
                        'withdraw_amount' => $requestData['withdraw_amount'][$i],
                        'invoice_no' => $requestData['invoice_no'],
                        'save_type' => $requestData['save_type'],
                        'invoice_status' => $invoice_status,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $finalArr[$i]['id'] = $lastInsertId;

                    //Save to activity log
                    $subject = 'added invoice.Reference no- '.$requestData['invoice_no'];
                    $objs = new ActivitiesLog;
                    $objs->client_id = $requestData['client_id'];
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();

                    $amount11 = floatval($requestData['withdraw_amount'][$i]);
                    $totalWithdrawAmount += $amount11;
                } //end for loop

                //main table 'account_client_receipts' entry
                $lastInsertId	= DB::table('account_client_receipts')->insertGetId([
                    'user_id' => $requestData['loggedin_userid'],
                    'client_id' =>  $requestData['client_id'],
                    'receipt_id'=>  $receipt_id,
                    'receipt_type' => $requestData['receipt_type'],
                    'trans_date' => $requestData['trans_date'][0],
                    'entry_date' => $requestData['entry_date'][0],
                    'gst_included' => $requestData['gst_included'][0],
                    'payment_type' => $requestData['payment_type'][0],
                    'trans_no' => $requestData['invoice_no'],
                    'description' => $requestData['description'][0],
                    'withdraw_amount' => $totalWithdrawAmount,
                    'balance_amount' => $totalWithdrawAmount,
                    'invoice_no' => $requestData['invoice_no'],
                    'save_type' => $requestData['save_type'],
                    'invoice_status' => $invoice_status,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            //echo '<pre>'; print_r($finalArr); die;
            if($lastInsertId) {
                $response['requestData'] 	= $finalArr;
                $response['status'] 	= 	true;
                $response['message']	=	'Invoice added successfully';
                $response['function_type'] = $requestData['function_type'];
                $response['total_balance_amount'] = $totalWithdrawAmount;
            }else{
                $response['requestData'] = "";
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['function_type'] = $requestData['function_type'];
                $response['total_balance_amount'] = 0;
            }
        }
        echo json_encode($response);
    }

    //Generate unique invoice no
    private function createInvoiceNumber($invoiceType)
    {
        $prefix = 'INV';

        $latestInv = DB::table('account_client_receipts')
            ->select('trans_no')
            ->where('receipt_type', 3)
            ->where('trans_no', 'LIKE', "$prefix-%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$latestInv) {
            $nextNumber = 1;
        } else {
            $lastTransNo = explode('-', $latestInv->trans_no);
            $lastNumber = isset($lastTransNo[1]) ? (int)$lastTransNo[1] : 0;
            $nextNumber = $lastNumber + 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    //Save invoice reports
    public function saveinvoicereport(Request $request, $id = NULL)
    {
        $requestData = $request->all();
        //echo '<pre>'; print_r($requestData); die;
        if( $requestData['function_type'] == 'add')
        {
            if(isset($requestData['trans_date'])){
                //Generate unique receipt id
                $is_record_exist = DB::table('account_client_receipts')->select('receipt_id')->where('receipt_type',3)->orderBy('receipt_id', 'desc')->first();
                //dd($is_record_exist);
                if(!$is_record_exist){
                    $receipt_id = 1;
                } else {
                    $receipt_id = $is_record_exist->receipt_id +1;
                }
                $finalArr = array();
                $totalWithdrawAmount = 0;
                for($i=0; $i<count($requestData['trans_date']); $i++){
                    // Calculate unit price and withdraw amount based on GST
                    /*$unitPrice = floatval($requestData['withdraw_amount'][$i]);
                    $withdrawAmount = $unitPrice;
                    if ($requestData['gst_included'][$i] == 'Yes') {
                        $withdrawAmount = $unitPrice * 1.10; // Add 10% GST
                    }*/
                    $withdrawAmount = floatval($requestData['withdraw_amount'][$i]);

                    $invoiceType = 'INV';
                    $invoice_no = $this->createInvoiceNumber($invoiceType);

                    $finalArr[$i]['trans_date'] = $requestData['trans_date'][$i];
                    $finalArr[$i]['entry_date'] = $requestData['entry_date'][$i];
                    $finalArr[$i]['trans_no'] = $invoice_no; //$requestData['invoice_no'];
                    $finalArr[$i]['gst_included'] = $requestData['gst_included'][$i];
                    $finalArr[$i]['payment_type'] = $requestData['payment_type'][$i];
                    $finalArr[$i]['description'] = $requestData['description'][$i];

                    $finalArr[$i]['withdraw_amount'] = $withdrawAmount; //$requestData['withdraw_amount'][$i];
                    //$finalArr[$i]['unit_price'] = $unitPrice;
                    $finalArr[$i]['balance_amount'] = $withdrawAmount;

                    $finalArr[$i]['invoice_no'] = $invoice_no; //$requestData['invoice_no'];
                    $finalArr[$i]['save_type'] = $requestData['save_type'];
                    $finalArr[$i]['receipt_id'] = $receipt_id;

                    $finalArr[$i]['client_matter_id'] = $requestData['client_matter_id'];

                    $invoice_status = 0;
                    $finalArr[$i]['invoice_status'] = $invoice_status; //unpaid

                    $lastInsertId	= DB::table('account_all_invoice_receipts')->insertGetId([
                        'user_id' => $requestData['loggedin_userid'],
                        'client_id' =>  $requestData['client_id'],
                        'client_matter_id' =>  $requestData['client_matter_id'],
                        'receipt_id'=>  $receipt_id,
                        'receipt_type' => $requestData['receipt_type'],
                        'trans_date' => $requestData['trans_date'][$i],
                        'entry_date' => $requestData['entry_date'][$i],
                        'gst_included' => $requestData['gst_included'][$i],
                        'payment_type' => $requestData['payment_type'][$i],
                        'trans_no' => $invoice_no, //$requestData['invoice_no'],
                        'description' => $requestData['description'][$i],
                        'withdraw_amount' => $withdrawAmount, //$requestData['withdraw_amount'][$i],
                        //'unit_price' => $unitPrice,
                        'invoice_no' => $invoice_no, //$requestData['invoice_no'],
                        'save_type' => $requestData['save_type'],
                        'invoice_status' => $invoice_status,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $finalArr[$i]['id'] = $lastInsertId;

                    //Save to activity log
                    $subject = 'added invoice.Reference no- '.$invoice_no; //$requestData['invoice_no'];
                    $objs = new ActivitiesLog;
                    $objs->client_id = $requestData['client_id'];
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();

                    $amount11 = $withdrawAmount;
                    if ($requestData['payment_type'][$i] == 'Discount') {
                        $totalWithdrawAmount -= $amount11;
                    } else {
                        $totalWithdrawAmount += $amount11;
                    }
                } //end for loop

                //main table 'account_client_receipts' entry
                $lastInsertId	= DB::table('account_client_receipts')->insertGetId([
                    'user_id' => $requestData['loggedin_userid'],
                    'client_id' =>  $requestData['client_id'],
                    'client_matter_id' =>  $requestData['client_matter_id'],
                    'receipt_id'=>  $receipt_id,
                    'receipt_type' => $requestData['receipt_type'],
                    'trans_date' => $requestData['trans_date'][0],
                    'entry_date' => $requestData['entry_date'][0],
                    'gst_included' => $requestData['gst_included'][0],
                    'payment_type' => $requestData['payment_type'][0],
                    'trans_no' => $invoice_no,//$requestData['invoice_no'],
                    'description' => $requestData['description'][0],
                    'withdraw_amount' => $totalWithdrawAmount,
                    //'unit_price' => $totalWithdrawAmount,
                    'balance_amount' => $totalWithdrawAmount,
                    'invoice_no' => $invoice_no,//$requestData['invoice_no'],
                    'save_type' => $requestData['save_type'],
                    'invoice_status' => $invoice_status,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            //echo '<pre>'; print_r($finalArr); die;
            if($lastInsertId) {
                $response['requestData'] 	= $finalArr;
                $response['status'] 	= 	true;
                $response['message']	=	'Invoice added successfully';
                $response['function_type'] = $requestData['function_type'];
                $response['total_balance_amount'] = $totalWithdrawAmount;
                $response['invoice_no'] = $invoice_no;
            }else{
                $response['requestData'] = "";
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['function_type'] = $requestData['function_type'];
                $response['total_balance_amount'] = 0;
                $response['invoice_no'] = "";
            }
        }
        else if ($requestData['function_type'] == 'edit') {
            DB::beginTransaction();
            try {
                // Step 1: Check for deleted entries and remove them
                $existingRecords = DB::table('account_all_invoice_receipts')
                    ->select('id')
                    ->where('receipt_type', 3)
                    ->where('receipt_id', $requestData['receipt_id'])
                    ->pluck('id')
                    ->toArray();

                $requestIds = array_filter($requestData['id'], fn($id) => !empty($id));
                $deletedIds = array_diff($existingRecords, $requestIds);

                if (!empty($deletedIds)) {
                    DB::table('account_all_invoice_receipts')->whereIn('id', $deletedIds)->delete();
                }

                // Step 2: Process all entries (update existing and add new ones)
                $totalWithdrawAmount = 0;
                $lastEntryData = null;
                $processedEntries = []; // To store entries for response
                $currentTimestamp = now(); // Get current timestamp for created_at and updated_at

                foreach ($requestData['trans_date'] as $index => $transDate) {
                    // Calculate unit price and withdraw amount based on GST
                    /*$unitPrice = floatval($requestData['withdraw_amount'][$index]);
                    $withdrawAmount = $unitPrice;
                    if ($requestData['gst_included'][$index] == 'Yes') {
                        $withdrawAmount = $unitPrice * 1.10; // Add 10% GST
                    }*/
                    $withdrawAmount = floatval($requestData['withdraw_amount'][$index]);
                    $invoiceType = 'INV';
                    $invoice_no = $this->createInvoiceNumber($invoiceType);

                    $entryData = [
                        'user_id' => $requestData['loggedin_userid'],
                        'client_id' => $requestData['client_id'],
                        'client_matter_id' =>  $requestData['client_matter_id'],
                        'receipt_type' => $requestData['receipt_type'],
                        'receipt_id' => $requestData['receipt_id'],
                        'trans_date' => $transDate,
                        'entry_date' => $requestData['entry_date'][$index],
                        'gst_included' => $requestData['gst_included'][$index],
                        'payment_type' => $requestData['payment_type'][$index],
                        'trans_no' => $invoice_no,//$requestData['invoice_no'],
                        'description' => $requestData['description'][$index],
                        'withdraw_amount' => $withdrawAmount,
                        //'unit_price' => $unitPrice,
                        'invoice_no' => $invoice_no, //$requestData['invoice_no'],
                        'save_type' => $requestData['save_type'],
                        'updated_at' => $currentTimestamp, // Add updated_at timestamp
                    ];
                    // Adjust total based on payment type using the GST-adjusted withdraw amount
                    if ($requestData['payment_type'][$index] == 'Discount') {
                        $totalWithdrawAmount -= $withdrawAmount;
                    } else {
                        $totalWithdrawAmount += $withdrawAmount;
                    }

                    // Store the last entry data for account_client_receipts
                    $lastEntryData = $entryData;

                    // Update or Insert into account_all_invoice_receipts
                    if (!empty($requestData['id'][$index])) {
                        // Update existing entry
                        $entryData['id'] = $requestData['id'][$index];
                        DB::table('account_all_invoice_receipts')
                            ->where('id', $requestData['id'][$index])
                            ->update($entryData);
                    } else {
                        // Add new entry with created_at and updated_at
                        $entryData['created_at'] = $currentTimestamp;
                        $entryData['id'] = DB::table('account_all_invoice_receipts')->insertGetId($entryData);
                    }

                    // Add to processed entries for response
                    $processedEntries[] = $entryData;
                }

                // Step 3: Update or Insert into account_client_receipts with total withdraw_amount and last entry data
                if ($lastEntryData) {
                    $lastEntryData['withdraw_amount'] = $totalWithdrawAmount;
                    $lastEntryData['balance_amount'] = $totalWithdrawAmount;
                    //$lastEntryData['unit_price'] = $totalWithdrawAmount; // Total unit price not applicable here, using total withdraw amount
                    $lastEntryData['updated_at'] = $currentTimestamp; // Add updated_at timestamp

                    // Check if a record exists in account_client_receipts for this receipt_id
                    $existingClientReceipt = DB::table('account_client_receipts')
                        ->where('receipt_id', $requestData['receipt_id'])
                        ->where('receipt_type', $requestData['receipt_type'])
                        ->first();

                    if ($existingClientReceipt) {
                        // Update existing record
                        DB::table('account_client_receipts')
                            ->where('receipt_id', $requestData['receipt_id'])
                            ->where('receipt_type', $requestData['receipt_type'])
                            ->update($lastEntryData);
                    } else {
                        // Insert new record with created_at and updated_at
                        $lastEntryData['created_at'] = $currentTimestamp;
                        DB::table('account_client_receipts')->insert($lastEntryData);
                    }
                }

                DB::commit();

                $response = [
                    'requestData' => $processedEntries,
                    'status' => true,
                    'message' => 'Invoice updated successfully',
                    'function_type' => $requestData['function_type'],
                    'invoice_no' => $invoice_no,
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                $response = [
                    'requestData' => [],
                    'status' => false,
                    'message' => 'Please try again: ' . $e->getMessage(),
                    'function_type' => $requestData['function_type'],
                    'invoice_no' => "",
                ];
            }
        }
        echo json_encode($response);
    }

    public function isAnyInvoiceNoExistInDB(Request $request)
	{
        $requestData 		= 	$request->all();
        $record_count = DB::table('account_client_receipts')->where('client_id',$requestData['client_id'])->where('invoice_no','!=' ,'')->count();
        //dd($record_count);
        if($record_count) {
            $response['record_count'] 	= $record_count;
            $response['status'] 	= 	true;
            $response['message']	=	'Record is exist';
        }else{
            $response['record_count'] 	= $record_count;
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
        }
        echo json_encode($response);
    }

    public function listOfInvoice(Request $request)
	{
        $requestData 		= 	$request->all();
        $record_get = DB::table('account_client_receipts')->select('invoice_no')->where('client_matter_id',$requestData['selectedMatter'])->where('client_id',$requestData['client_id'])->where('receipt_type',3)->where('save_type','final')->distinct()->get();
        //dd($record_get);
        if(!empty($record_get)) {
            $str = '<option value="">Select</option>';
            foreach($record_get as $key=>$val) {
                $str .=  '<option value="'.$val->invoice_no.'">'.$val->invoice_no.'</option>';
            }
            $response['record_get'] 	= $str;
            $response['status'] 	= 	true;
            $response['message']	=	'Record is exist';
        }else{
            $response['record_count'] 	= array();
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
        }
        echo json_encode($response);
    }

    public function clientLedgerBalanceAmount(Request $request)
	{
        $requestData 		= 	$request->all();
        $latest_balance = DB::table('account_client_receipts')
            ->where('client_matter_id', $requestData['selectedMatter'])
            ->where('client_id', $requestData['client_id'])
            ->where('receipt_type', 1)
            ->orderBy('id', 'desc')
            ->value('balance_amount');
        if( is_numeric($latest_balance) ) {
            $response['record_get'] = $latest_balance;
            $response['status'] 	= 	true;
            $response['message']	=	'Record is exist';
        } else {
            $latest_balance = 0;
            $response['record_get'] = 0;
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
        }

        echo json_encode($response);
    }

    public function getInfoByReceiptId(Request $request)
    {
        $requestData = $request->all();
        $receiptid = $requestData['receiptid'];
        $record_get = array();
        $record_get_parent = array();

        $record_get_parent = DB::table('account_client_receipts')
            ->where('receipt_type', 3)
            ->where('receipt_id', $receiptid)
            ->get();

        $record_get  = DB::table('account_all_invoice_receipts')
            ->where('receipt_type', 3)
            ->where('receipt_id', $receiptid)
            ->get();

        if (!empty($record_get)) {
            $response['record_get'] = $record_get;
            $response['record_get_parent'] = $record_get_parent;
            $response['status'] = true;
            $response['message'] = 'Record is exist';

            $last_record_id = DB::table('account_client_receipts')
                ->where('receipt_type', 3)
                ->max('id');
            $response['last_record_id'] = $last_record_id;
        } else {
            $response['record_get'] = $record_get;
            $response['record_get_parent'] = $record_get_parent;
            $response['status'] = false;
            $response['message'] = 'Record is not exist.Please try again';
            $response['last_record_id'] = 0;
        }

        echo json_encode($response);
    }

    public function getTopReceiptValInDB(Request $request)
	{
        $requestData = 	$request->all();
        $receipt_type = $requestData['type'];
        $record_count = DB::table('account_client_receipts')->where('receipt_type',$receipt_type)->max('id');
        //dd($record_count);
        if($record_count) {
            if($receipt_type == 3){ //type = invoice
                $max_receipt_id = DB::table('account_client_receipts')->where('receipt_type',$receipt_type)->max('receipt_id');
                $response['max_receipt_id'] 	= $max_receipt_id;
            } else {
                $response['max_receipt_id'] 	= "";
            }
            $response['receipt_type'] 	= $receipt_type;
            $response['record_count'] 	= $record_count;
            $response['status'] 	= 	true;
            $response['message']	=	'Record is exist';
        }else{
            $response['receipt_type'] 	= $receipt_type;
            $response['record_count'] 	= $record_count;
            $response['max_receipt_id'] 	= "";
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
        }
        echo json_encode($response);
    }

    public function getTopInvoiceNoFromDB(Request $request)
	{
        $requestData = 	$request->all();
        $receipt_type = $requestData['type'];

        //Start Logic For Invoice no
        // Get the last invoice number with this type
        $prefix = "INV";
        $latestInv = DB::table('account_client_receipts')
            ->select('invoice_no')
            ->where('receipt_type', $receipt_type)
            ->where('invoice_no', 'LIKE', "$prefix-%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$latestInv) {
            $nextNumber = 1;
        } else {
            // Extract numeric part and increment
            $lastInvNo = explode('-', $latestInv->invoice_no);
            $lastNumber = isset($lastInvNo[1]) ? (int)$lastInvNo[1] : 0;
            $nextNumber = $lastNumber + 1;
        }

        // Format with leading zeros
        $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $invoice_no = $prefix . '-' . $formattedNumber;

        $response['max_receipt_id'] = $invoice_no;
        $response['status'] 	= 	true;
        $response['message']	=	'Record is exist';
        echo json_encode($response);
    }

    //Save office reports
    /*
    // COMMENTED OUT - Original saveofficereport function with invoice logic
    public function saveofficereport(Request $request, $id = NULL)
    {
        $requestData = $request->all();
        $response = [];

        // Handle document upload
        $insertedDocId = "";
        $doc_saved = false;
        $client_unique_id = "";
        $awsUrl = "";
        $doctype = isset($request->doctype) ? $request->doctype : '';

        if ($request->hasfile('document_upload')) {
            $files = is_array($request->file('document_upload')) ? $request->file('document_upload') : [$request->file('document_upload')];

            $client_info = \App\Models\Admin::select('client_id')->where('id', $requestData['client_id'])->first();
            $client_unique_id = !empty($client_info) ? $client_info->client_id : "";

            foreach ($files as $file) {
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
                $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
                $fileExtension = $file->getClientOriginalExtension();
                $name = time() . $file->getClientOriginalName();
                $filePath = $client_unique_id . '/' . $doctype . '/' . $name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));

                $obj = new \App\Models\Document;
                $obj->file_name = $nameWithoutExtension;
                $obj->filetype = $fileExtension;
                $obj->user_id = Auth::user()->id;
                $obj->myfile = Storage::disk('s3')->url($filePath);
                $obj->myfile_key = $name;
                $obj->client_id = $requestData['client_id'];
                $obj->type = $request->type;
                $obj->file_size = $size;
                $obj->doc_type = $doctype;
                $doc_saved = $obj->save();
                $insertedDocId = $obj->id;
            }
        }

        // Handle receipt processing
        if (isset($requestData['trans_date'])) {
            $is_record_exist = DB::table('account_client_receipts')->select('receipt_id')->where('receipt_type', 2)->orderBy('receipt_id', 'desc')->first();
            $receipt_id = !$is_record_exist ? 1 : $is_record_exist->receipt_id + 1;

            $finalArr = [];
            $saved = false;

            // Group deposit amounts by invoice_no
            $depositByInvoice = [];
            for ($i = 0; $i < count($requestData['trans_date']); $i++) {
                $invoiceNo = $requestData['invoice_no'][$i];
                if (!isset($depositByInvoice[$invoiceNo])) {
                    $depositByInvoice[$invoiceNo] = [];
                }
                $depositByInvoice[$invoiceNo][] = [
                    'index' => $i,
                    'amount' => $requestData['deposit_amount'][$i],
                    'trans_date' => $requestData['trans_date'][$i],
                    'entry_date' => $requestData['entry_date'][$i],
                    'payment_method' => $requestData['payment_method'][$i],
                    'description' => $requestData['description'][$i],
                ];
            }

            foreach ($depositByInvoice as $invoiceNo => $deposits) {
                // Get total deposit amount for this invoice
                $totalDepositAmount = array_sum(array_column($deposits, 'amount'));

                // Get invoice withdraw amount
                $invoiceInfo = DB::table('account_client_receipts')
                    ->select('withdraw_amount', 'partial_paid_amount', 'balance_amount')
                    ->where('client_id', $requestData['client_id'])
                    ->where('receipt_type', 3)
                    ->where('invoice_no', $invoiceNo)
                    ->first();

                if ($invoiceInfo) {
                    $withdrawAmount = $invoiceInfo->withdraw_amount;
                    $currentPartialPaid = $invoiceInfo->partial_paid_amount ?? 0;
                    $currentBalance = $invoiceInfo->balance_amount ?? $withdrawAmount;

                    // Process receipts based on total deposit amount
                    $remainingDeposit = $totalDepositAmount;
                    $newPartialPaid = $currentPartialPaid;
                    $newBalance = $currentBalance;
                    $status = 0;

                    foreach ($deposits as $deposit) {
                        if ($remainingDeposit <= 0) break;

                        $amountToUse = $deposit['amount'];
                        $trans_no = $this->generateTransNo();

                        // Adjust amount if it exceeds the invoice's withdraw amount
                        if ($newPartialPaid + $amountToUse > $withdrawAmount) {
                            $amountToUse = $withdrawAmount - $newPartialPaid;
                        }

                        // Skip if no amount to use
                        if ($amountToUse <= 0) continue;

                        $saved = DB::table('account_client_receipts')->insertGetId([
                            'user_id' => $requestData['loggedin_userid'],
                            'client_id' => $requestData['client_id'],
                             'client_matter_id' => $requestData['client_matter_id'],
                            'receipt_id' => $receipt_id,
                            'receipt_type' => $requestData['receipt_type'],
                            'trans_date' => $deposit['trans_date'],
                            'entry_date' => $deposit['entry_date'],
                            'trans_no' => $trans_no,
                            'invoice_no' => $invoiceNo,
                            'payment_method' => $deposit['payment_method'],
                            'description' => $deposit['description'],
                            'deposit_amount' => $amountToUse,
                            'uploaded_doc_id' => $insertedDocId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $finalArr[] = [
                            'trans_date' => $deposit['trans_date'],
                            'entry_date' => $deposit['entry_date'],
                            'trans_no' => $trans_no,
                            'invoice_no' => $invoiceNo,
                            'payment_method' => $deposit['payment_method'],
                            'description' => $deposit['description'],
                            'deposit_amount' => $amountToUse,
                        ];

                        $newPartialPaid += $amountToUse;
                        $remainingDeposit -= $amountToUse;
                    }

                    // Handle excess amount by creating additional receipt linked to the same invoice
                    if ($remainingDeposit > 0) {
                        $trans_no = $this->generateTransNo();
                        $saved = DB::table('account_client_receipts')->insertGetId([
                            'user_id' => $requestData['loggedin_userid'],
                            'client_id' => $requestData['client_id'],
                            'client_matter_id' => $requestData['client_matter_id'],
                            'receipt_id' => $receipt_id,
                            'receipt_type' => $requestData['receipt_type'],
                            'trans_date' => $deposits[0]['trans_date'],
                            'entry_date' => $deposits[0]['entry_date'],
                            'trans_no' => $trans_no,
                            'invoice_no' => $invoiceNo,
                            'payment_method' => $deposits[0]['payment_method'],
                            'description' => $deposits[0]['description'],
                            'deposit_amount' => $remainingDeposit,
                            'uploaded_doc_id' => $insertedDocId,
                            'extra_amount_receipt' => 'exceed',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $finalArr[] = [
                            'trans_date' => $deposits[0]['trans_date'],
                            'entry_date' => $deposits[0]['entry_date'],
                            'trans_no' => $trans_no,
                            'invoice_no' => $invoiceNo,
                            'payment_method' => $deposits[0]['payment_method'],
                            'description' => $deposits[0]['description'],
                            'deposit_amount' => $remainingDeposit,
                            'extra_amount_receipt' => 'exceed'
                        ];
                    }

                    // Update invoice status
                    $newBalance = $withdrawAmount - $newPartialPaid;
                    $status = ($newBalance <= 0) ? 1 : 2; // Paid or Partial

                    DB::table('account_client_receipts')
                        ->where('client_id', $requestData['client_id'])
                        ->where('receipt_type', 3)
                        ->where('invoice_no', $invoiceNo)
                        ->update([
                            'invoice_status' => $status,
                            'partial_paid_amount' => $newPartialPaid,
                            'balance_amount' => $newBalance,
                            'updated_at' => now(),
                        ]);

                    DB::table('account_all_invoice_receipts')
                        ->where('client_id', $requestData['client_id'])
                        ->where('receipt_type', 3)
                        ->where('invoice_no', $invoiceNo)
                        ->update([
                            'invoice_status' => $status,
                            'updated_at' => now(),
                        ]);

                    $response['invoices'][] = [
                        'invoice_no' => $invoiceNo,
                        'invoice_status' => $status,
                        'invoice_balance' => $newBalance,
                        'outstanding_balance' => $newBalance,
                    ];
                } else {
                    // Handle case when invoice doesn't exist
                    $newInvoiceNo = $this->generateInvoiceNo();
                    $receipt_id11 = $this->getNextReceiptId(3);

                    DB::table('account_client_receipts')->insert([
                        'user_id' => $requestData['loggedin_userid'],
                        'client_id' => $requestData['client_id'],
                        'client_matter_id' => $requestData['client_matter_id'],
                        'receipt_id' => $receipt_id11,
                        'receipt_type' => 3,
                        'trans_date' => $deposits[0]['trans_date'],
                        'entry_date' => $deposits[0]['entry_date'],
                        'gst_included' => 'Yes',
                        'payment_type' => 'Professional Fee',
                        'trans_no' => $newInvoiceNo,
                        'description' => $deposits[0]['description'],
                        'withdraw_amount' => $totalDepositAmount,
                        'balance_amount' => $totalDepositAmount,
                        'invoice_no' => $newInvoiceNo,
                        'save_type' => 'final',
                        'invoice_status' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('account_all_invoice_receipts')->insert([
                        'user_id' => $requestData['loggedin_userid'],
                        'client_id' => $requestData['client_id'],
                        'client_matter_id' => $requestData['client_matter_id'],
                        'receipt_id' => $receipt_id11,
                        'receipt_type' => 3,
                        'trans_date' => $deposits[0]['trans_date'],
                        'entry_date' => $deposits[0]['entry_date'],
                        'gst_included' => 'Yes',
                        'payment_type' => 'Professional Fee',
                        'trans_no' => $newInvoiceNo,
                        'description' => $deposits[0]['description'],
                        'withdraw_amount' => $totalDepositAmount,
                        'invoice_no' => $newInvoiceNo,
                        'save_type' => 'final',
                        'invoice_status' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    foreach ($deposits as $deposit) {
                        $trans_no = $this->generateTransNo();
                        $saved = DB::table('account_client_receipts')->insertGetId([
                            'user_id' => $requestData['loggedin_userid'],
                            'client_id' => $requestData['client_id'],
                            'client_matter_id' => $requestData['client_matter_id'],
                            'receipt_id' => $receipt_id,
                            'receipt_type' => $requestData['receipt_type'],
                            'trans_date' => $deposit['trans_date'],
                            'entry_date' => $deposit['entry_date'],
                            'trans_no' => $trans_no,
                            'invoice_no' => $newInvoiceNo,
                            'payment_method' => $deposit['payment_method'],
                            'description' => $deposit['description'],
                            'deposit_amount' => $deposit['amount'],
                            'uploaded_doc_id' => $insertedDocId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $finalArr[] = [
                            'trans_date' => $deposit['trans_date'],
                            'entry_date' => $deposit['entry_date'],
                            'trans_no' => $trans_no,
                            'invoice_no' => $newInvoiceNo,
                            'payment_method' => $deposit['payment_method'],
                            'description' => $deposit['description'],
                            'deposit_amount' => $deposit['amount'],
                        ];
                    }

                    $response['invoices'][] = [
                        'invoice_no' => $newInvoiceNo,
                        'invoice_status' => 0,
                        'invoice_balance' => $totalDepositAmount,
                        'outstanding_balance' => $totalDepositAmount,
                    ];
                }
            }

            // Log activity
            if ($saved) {
                $subject = $doc_saved ? 'added office receipt with its document. Reference no- ' . $trans_no : 'added office receipt. Reference no- ' . $trans_no;
                if ($request->type == 'client') {
                    $objs = new \App\Models\ActivitiesLog;
                    $objs->client_id = $requestData['client_id'];
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();
                }
            }
        }

        // Prepare response
        if ($saved) {
            $response['status'] = true;
            $response['requestData'] = $finalArr;
            $response['message'] = $doc_saved ? 'Office receipt with document added successfully' : 'Office receipt added successfully';
            if ($doc_saved) {
                $url = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                $awsUrl = $url . $client_unique_id . '/' . $doctype . '/' . $name;
                $response['awsUrl'] = $awsUrl;
            } else {
                $response['awsUrl'] = "";
            }
        } else {
            $response['status'] = false;
            $response['requestData'] = [];
            $response['awsUrl'] = "";
            $response['message'] = 'Please try again';
            $response['invoices'] = [];
        }

        return response()->json($response, 200);
    }
    */

    // NEW SIMPLIFIED saveofficereport function - Only handles office receipts (receipt_type=2)
    public function saveofficereport(Request $request, $id = NULL)
    {
        $requestData = $request->all();
        $response = [];

        // Handle document upload
        $insertedDocId = "";
        $doc_saved = false;
        $client_unique_id = "";
        $awsUrl = "";
        $doctype = isset($request->doctype) ? $request->doctype : '';

        if ($request->hasfile('document_upload')) {
            $files = is_array($request->file('document_upload')) ? $request->file('document_upload') : [$request->file('document_upload')];

            $client_info = \App\Models\Admin::select('client_id')->where('id', $requestData['client_id'])->first();
            $client_unique_id = !empty($client_info) ? $client_info->client_id : "";

            foreach ($files as $file) {
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
                $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
                $fileExtension = $file->getClientOriginalExtension();
                $name = time() . $file->getClientOriginalName();
                $filePath = $client_unique_id . '/' . $doctype . '/' . $name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));

                $obj = new \App\Models\Document;
                $obj->file_name = $nameWithoutExtension;
                $obj->filetype = $fileExtension;
                $obj->user_id = Auth::user()->id;
                $obj->myfile = Storage::disk('s3')->url($filePath);
                $obj->myfile_key = $name;
                $obj->client_id = $requestData['client_id'];
                $obj->type = $request->type;
                $obj->file_size = $size;
                $obj->doc_type = $doctype;
                $doc_saved = $obj->save();
                $insertedDocId = $obj->id;
            }
        }

        // Handle office receipt processing (receipt_type=2 only)
        if (isset($requestData['trans_date'])) {
            $is_record_exist = DB::table('account_client_receipts')->select('receipt_id')->where('receipt_type', 2)->orderBy('receipt_id', 'desc')->first();
            $receipt_id = !$is_record_exist ? 1 : $is_record_exist->receipt_id + 1;

            $finalArr = [];
            $saved = false;

            // Process each transaction individually (no invoice grouping)
            for ($i = 0; $i < count($requestData['trans_date']); $i++) {
                $trans_no = $this->generateTransNo();

                $saved = DB::table('account_client_receipts')->insertGetId([
                    'user_id' => $requestData['loggedin_userid'],
                    'client_id' => $requestData['client_id'],
                    'client_matter_id' => $requestData['client_matter_id'],
                    'receipt_id' => $receipt_id,
                    'receipt_type' => 2, // Only office receipts
                    'trans_date' => $requestData['trans_date'][$i],
                    'entry_date' => $requestData['entry_date'][$i],
                    'trans_no' => $trans_no,
                    'invoice_no' => isset($requestData['invoice_no'][$i]) ? $requestData['invoice_no'][$i] : '',
                    'payment_method' => $requestData['payment_method'][$i],
                    'description' => $requestData['description'][$i],
                    'deposit_amount' => $requestData['deposit_amount'][$i],
                    'uploaded_doc_id' => $insertedDocId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $finalArr[] = [
                    'trans_date' => $requestData['trans_date'][$i],
                    'entry_date' => $requestData['entry_date'][$i],
                    'trans_no' => $trans_no,
                    'invoice_no' => isset($requestData['invoice_no'][$i]) ? $requestData['invoice_no'][$i] : '',
                    'payment_method' => $requestData['payment_method'][$i],
                    'description' => $requestData['description'][$i],
                    'deposit_amount' => $requestData['deposit_amount'][$i],
                ];
            }

            // Log activity
            if ($saved) {
                $subject = $doc_saved ? 'added office receipt with its document. Reference no- ' . $trans_no : 'added office receipt. Reference no- ' . $trans_no;
                if ($request->type == 'client') {
                    $objs = new \App\Models\ActivitiesLog;
                    $objs->client_id = $requestData['client_id'];
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();
                }
            }
        }

        // Prepare response
        if ($saved) {
            $response['status'] = true;
            $response['requestData'] = $finalArr;
            $response['message'] = $doc_saved ? 'Office receipt with document added successfully' : 'Office receipt added successfully';
            if ($doc_saved) {
                $url = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                $awsUrl = $url . $client_unique_id . '/' . $doctype . '/' . $name;
                $response['awsUrl'] = $awsUrl;
            } else {
                $response['awsUrl'] = "";
            }
        } else {
            $response['status'] = false;
            $response['requestData'] = [];
            $response['awsUrl'] = "";
            $response['message'] = 'Please try again';
        }

        return response()->json($response, 200);
    }

    // Helper methods
    private function generateTransNo()
    {
        $latestTrans = DB::table('account_client_receipts')
            ->select('trans_no')
            ->where('receipt_type', 2)
            ->orderBy('id', 'desc')
            ->first();

        if (!$latestTrans) {
            $nextNumber = 1;
        } else {
            $lastTransNo = explode('-', $latestTrans->trans_no);
            $lastNumber = isset($lastTransNo[1]) ? (int)$lastTransNo[1] : 0;
            $nextNumber = $lastNumber + 1;
        }

        return 'REC-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    private function generateInvoiceNo()
    {
        $prefix = 'INV';
        $latestInv = DB::table('account_client_receipts')
            ->select('invoice_no')
            ->where('receipt_type', 3)
            ->where('invoice_no', 'LIKE', "$prefix-%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$latestInv) {
            $nextNumber = 1;
        } else {
            $lastInvNo = explode('-', $latestInv->invoice_no);
            $lastNumber = isset($lastInvNo[1]) ? (int)$lastInvNo[1] : 0;
            $nextNumber = $lastNumber + 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    private function getNextReceiptId($receipt_type)
    {
        $is_record_exist = DB::table('account_client_receipts')->select('receipt_id')->where('receipt_type', $receipt_type)->orderBy('receipt_id', 'desc')->first();
        return !$is_record_exist ? 1 : $is_record_exist->receipt_id + 1;
    }

    //Save Journal reports
    public function savejournalreport(Request $request, $id = NULL)
	{
		$requestData 		= 	$request->all();
        //echo '<pre>'; print_r($requestData); die;
        if ($request->hasfile('document_upload'))
        {
            if(!is_array($request->file('document_upload'))){
                $files[] = $request->file('document_upload');
            }else{
                $files = $request->file('document_upload');
            }

            $client_info = \App\Models\Admin::select('client_id')->where('id', $requestData['client_id'])->first();
            if(!empty($client_info)){
                $client_unique_id = $client_info->client_id;
            } else {
                $client_unique_id = "";
            }

            $doctype = isset($request->doctype)? $request->doctype : '';

            foreach ($files as $file) {
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
                $explodeFileName = explode('.', $fileName);
                $name = time() . $file->getClientOriginalName();
                $filePath = $client_unique_id.'/'.$doctype.'/'. $name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));
                $exploadename = explode('.', $name);

                $obj = new \App\Models\Document;
                $obj->file_name = $explodeFileName[0];
                $obj->filetype = $exploadename[1];
                $obj->user_id = Auth::user()->id;
                $obj->myfile = $name;

                $obj->client_id = $requestData['client_id'];
                $obj->type = $request->type;
                $obj->file_size = $size;
                $obj->doc_type = $doctype;
                $doc_saved = $obj->save();

                $insertedDocId = $obj->id;
            } //end foreach

            if($doc_saved){
                if($request->type == 'client'){
                    $subject = 'added 1 journal receipt document';
                    $objs = new ActivitiesLog;
                    $objs->client_id = $requestData['client_id'];
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();
                }
            }
        } else {
            $insertedDocId = "";
            $doc_saved = "";
        }

        if(isset($requestData['trans_date'])){
            $is_record_exist = DB::table('account_client_receipts')->select('receipt_id')->where('receipt_type',4)->orderBy('receipt_id', 'desc')->first();
            //dd($is_record_exist);
            if(!$is_record_exist){
                $receipt_id = 1;
            } else {
                $receipt_id = $is_record_exist->receipt_id +1;
            }

            $finalArr = array();
            for($i=0; $i<count($requestData['trans_date']); $i++){
                $finalArr[$i]['trans_date'] = $requestData['trans_date'][$i];
                $finalArr[$i]['entry_date'] = $requestData['entry_date'][$i];
                $finalArr[$i]['trans_no'] = $requestData['trans_no'][$i];
                $finalArr[$i]['invoice_no'] = $requestData['invoice_no'][$i];
                $finalArr[$i]['description'] = $requestData['description'][$i];
                $finalArr[$i]['withdrawal_amount'] = $requestData['withdrawal_amount'][$i];

                $saved	= DB::table('account_client_receipts')->insert([
                    'user_id' => $requestData['loggedin_userid'],
                    'client_id' =>  $requestData['client_id'],
                    'agent_id' =>  $requestData['agent_id'],
                    'receipt_id'=>  $receipt_id,
                    'receipt_type' => $requestData['receipt_type'],
                    'trans_date' => $requestData['trans_date'][$i],
                    'entry_date' => $requestData['entry_date'][$i],
                    'trans_no' => $requestData['trans_no'][$i],
                    'invoice_no' => $requestData['invoice_no'][$i],
                    'description' => $requestData['description'][$i],
                    'withdrawal_amount' => $requestData['withdrawal_amount'][$i],
                    'uploaded_doc_id'=> $insertedDocId
                ]);
            }
        }
        //echo '<pre>'; print_r($finalArr); die;
        if($saved) {
            $response['status'] 	= 	true;
            $response['requestData'] 	= $finalArr;
            //Get total withdrawl amount
            $db_total_withdrawal_amount = DB::table('account_client_receipts')->where('client_id',$requestData['client_id'])->where('receipt_type',4)->sum('withdrawal_amount');
            $response['db_total_withdrawal_amount'] 	= $db_total_withdrawal_amount;

            if($doc_saved){
                //Get AWS Url link
                $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                $awsUrl = $url.$client_unique_id.'/'.$doctype.'/'.$name; //dd($awsUrl);
                $response['awsUrl'] = $awsUrl;

                $response['message'] = 'Journal receipt with document added successfully';
            } else {
                $response['message'] = 'Journal receipt added successfully';
                $response['awsUrl'] =  "";
            }
        }else{
            $response['awsUrl'] =  "";
            $response['requestData'] 	= "";
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
        }
        echo json_encode($response);
    }

    public function genInvoice(Request $request, $id){
        $record_get = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->get();
        //dd($record_get);

        $record_get_Professional_Fee_cnt = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Professional Fee')->count();
        //dd($record_get_Professional_Fee_cnt);

        $record_get_Department_Charges_cnt = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Department Charges')->count();
        //dd($record_get_Department_Charges_cnt);

        $record_get_Surcharge_cnt = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Surcharge')->count();
        //dd($record_get_Surcharge_cnt);

        $record_get_Disbursements_cnt = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Disbursements')->count();
        //dd($record_get_Disbursements_cnt);

        $record_get_Other_Cost_cnt = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Other Cost')->count();
        //dd($record_get_Other_Cost_cnt);

        $record_get_Discount_cnt = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Discount')->count();
        //dd($record_get_Discount_cnt);

        $record_get_Professional_Fee = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Professional Fee')->get();
        //dd($record_get_Professional_Fee);

        $record_get_Department_Charges = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Department Charges')->get();
        //dd($record_get_Department_Charges);

        $record_get_Surcharge = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Surcharge')->get();
        //dd($record_get_Surcharge);

        $record_get_Disbursements = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Disbursements')->get();
        //dd($record_get_Disbursements);

        $record_get_Other_Cost = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Other Cost')->get();
        //dd($record_get_Other_Cost);

        $record_get_Discount = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Discount')->get();
        //dd($record_get_Discount);

        //Calculate Gross Amount
        $total_Gross_Amount = DB::table('account_all_invoice_receipts')
            ->where('receipt_type', 3)
            ->where('receipt_id', $id)
            ->sum(DB::raw("
                CASE
                    WHEN payment_type = 'Discount' AND gst_included = 'Yes' THEN -(withdraw_amount - (withdraw_amount / 11))
                    WHEN payment_type = 'Discount' AND gst_included = 'No' THEN -withdraw_amount
                    WHEN gst_included = 'Yes' THEN withdraw_amount - (withdraw_amount / 11)
                    ELSE withdraw_amount
                END
            "));

        //Total Invoice Amount
        $total_Invoice_Amount = DB::table('account_all_invoice_receipts')
            ->where('receipt_type', 3)
            ->where('receipt_id', $id)
            ->sum(DB::raw("CASE
                WHEN payment_type = 'Discount' THEN -withdraw_amount
                ELSE withdraw_amount
            END"));

        //Calculate GST
        $total_GST_amount =  $total_Invoice_Amount - $total_Gross_Amount;

        //Total Pending Amount
        $total_Pending_amount  = DB::table('account_client_receipts')
        ->where('receipt_type', 3) // Invoice
        ->where('receipt_id', $id)
        ->where(function ($query) {
            $query->whereIn('invoice_status', [0, 2])
                ->orWhere(function ($q) {
                    $q->where('invoice_status', 1)
                        ->where('balance_amount', '!=', 0);
                });
        })
        ->sum('balance_amount');

        $clientname = DB::table('admins')->where('id',$record_get[0]->client_id)->first();

        //Get payment method
        if( !empty($record_get) && $record_get[0]->invoice_no != '') {
            $invoice_payment_method = '';
            $office_receipt = DB::table('account_client_receipts')->select('payment_method')->where('receipt_type',2)->where('invoice_no',$record_get[0]->invoice_no)->first();
            if($office_receipt){
                $invoice_payment_method = $office_receipt->payment_method; //dd($payment_method);
                if($invoice_payment_method != "" ) {
                    $invoice_payment_method = $invoice_payment_method;
                } else {
                    $invoice_payment_method = '';
                }
            } else {
                $invoice_payment_method = '';
            }
        } else {
            $invoice_payment_method = '';
        }

        //Get client matter
        if( !empty($record_get) && $record_get[0]->client_matter_id != '') {
            $client_matter_no = '';
            $client_info = DB::table('admins')->select('client_id')->where('id',$record_get[0]->client_id)->first();
            if($client_info){
                $client_unique_id = $client_info->client_id; //dd($client_unique_id);
            } else {
                $client_unique_id = '';
            }

            $matter_info = DB::table('client_matters')->select('client_unique_matter_no')->where('id',$record_get[0]->client_matter_id)->first();
            if($matter_info){
                $client_unique_matter_no = $matter_info->client_unique_matter_no;
                $client_matter_no = $client_unique_id.'-'.$client_unique_matter_no;
            } else {
                $client_unique_matter_no = '';
                $client_matter_no = '';
            }
        } else {
            $client_unique_matter_no = '';
            $client_matter_no = '';
        }

        $pdf = PDF::setOptions([
			'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
			'logOutputFile' => storage_path('logs/log.htm'),
			'tempDir' => storage_path('logs/')
		])->loadView('emails.geninvoice',compact(
            ['record_get',
            'record_get_Professional_Fee_cnt',
            'record_get_Department_Charges_cnt',
            'record_get_Surcharge_cnt',
            'record_get_Disbursements_cnt',
            'record_get_Other_Cost_cnt',
            'record_get_Discount_cnt',

            'record_get_Professional_Fee',
            'record_get_Department_Charges',
            'record_get_Surcharge',
            'record_get_Disbursements',
            'record_get_Other_Cost',
            'record_get_Discount',

            'total_Gross_Amount',
            'total_Invoice_Amount',
            'total_GST_amount',
            'total_Pending_amount',

            'clientname',
            'invoice_payment_method',
            'client_matter_no'
        ]));
		//
		return $pdf->stream('Invoice.pdf');
	}

    public function uploadclientreceiptdocument(Request $request){ // dd($request->all());
        $id = $request->clientid;
        $client_info = \App\Models\Admin::select('client_id')->where('id', $id)->first(); //dd($admin);
        if(!empty($client_info)){
            $client_id = $client_info->client_id;
        } else {
            $client_id = "";
        }  //dd($client_id);
        $doctype = isset($request->doctype)? $request->doctype : '';
        if ($request->hasfile('document_upload')) {
            if(!is_array($request->file('document_upload'))){
				$files[] = $request->file('document_upload');
			}else{
				$files = $request->file('document_upload');
			}

		    foreach ($files as $file) {
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
                $explodeFileName = explode('.', $fileName);

                //$document_upload = $this->uploadrenameFile($file, Config::get('constants.documents'));

                //$file = $request->file('document_upload');
                $name = time() . $file->getClientOriginalName();
                //$explodeFileName1 = explode('.', $name);
                //$filePath = 'documents/' . $name;
                $filePath = $client_id.'/'.$doctype.'/'. $name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));

                //$exploadename = explode('.', $document_upload);
                $exploadename = explode('.', $name);

                $obj = new \App\Models\Document;
                $obj->file_name = $explodeFileName[0];
                $obj->filetype = $exploadename[1];
                $obj->user_id = Auth::user()->id;
                //$obj->myfile = $document_upload;
                $obj->myfile = $name;

                $obj->client_id = $id;
                $obj->type = $request->type;
                $obj->file_size = $size;
                $obj->doc_type = $doctype;
                $saved = $obj->save();
            }

			if($saved){
				if($request->type == 'client'){
                    $subject = 'added 1 client receipt document';
                    $objs = new ActivitiesLog;
                    $objs->client_id = $id;
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();

				}
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully uploaded your client receipt document';
				$fetchd = \App\Models\Document::where('client_id',$id)->where('doc_type',$doctype)->where('type',$request->type)->orderby('created_at', 'DESC')->get();
				ob_start();
				foreach($fetchd as $fetch){
					$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
					?>
					<tr class="drow" id="id_<?php echo $fetch->id; ?>">
						<td><div data-id="<?php echo $fetch->id; ?>" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
							<i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name; ?><?php echo '.'.$fetch->filetype; ?></span>
						</div></td>
						<td><?php echo $admin->first_name; ?></td>

						<td><?php echo date('Y-m-d', strtotime($fetch->created_at)); ?></td>
						<td>
							<div class="dropdown d-inline">
								<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
								<div class="dropdown-menu">
									<a class="dropdown-item renamedoc" href="javascript:;">Rename</a>
                                    <?php
                                    $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                    ?>
									<a target="_blank" class="dropdown-item" href="<?php echo $url.$client_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Preview</a>

                                    <?php
									$explodeimg = explode('.',$fetch->myfile);
									if($explodeimg[1] == 'jpg'|| $explodeimg[1] == 'png'|| $explodeimg[1] == 'jpeg'){
									?>
										<a target="_blank" class="dropdown-item" href="<?php echo \URL::to('/admin/document/download/pdf'); ?>/<?php echo $fetch->id; ?>">PDF</a>
									<?php } ?>

									<a download class="dropdown-item" href="<?php echo $url.$client_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Download</a>

									<a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletedocs" href="javascript:;" >Delete</a>
								</div>
							</div>
						</td>
					</tr>
					<?php
				}
				$data = ob_get_clean();
				ob_start();
				foreach($fetchd as $fetch){
					$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
					?>
					<div class="grid_list">
						<div class="grid_col">
							<div class="grid_icon">
								<i class="fas fa-file-image"></i>
							</div>
							<div class="grid_content">
								<span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
								<div class="dropdown d-inline dropdown_ellipsis_icon">
									<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
									<div class="dropdown-menu">
										<!--<a class="dropdown-item" href="<?php //echo \URL::to('/public/img/documents'); ?>/<?php //echo $fetch->myfile; ?>">Preview</a>
										<a download class="dropdown-item" href="<?php //echo \URL::to('/public/img/documents'); ?>/<?php //echo $fetch->myfile; ?>">Download</a>-->

                                        <?php
                                        $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                        ?>
										<!--<a class="dropdown-item" href="<?php //echo $url.'documents/'.$fetch->myfile; ?>">Preview</a>
										<a download class="dropdown-item" href="<?php //echo $url.'documents/'.$fetch->myfile; ?>">Download</a>-->

                                        <a class="dropdown-item" href="<?php echo $url.$client_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Preview</a>
										<a download class="dropdown-item" href="<?php echo $url.$client_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Download</a>

										<a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletedocs" href="javascript:;" >Delete</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
				$griddata = ob_get_clean();
				$response['data']	=$data;
				$response['griddata']	=$griddata;
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		 }else{
			 $response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		 }
		 echo json_encode($response);
	}

    public function uploadofficereceiptdocument(Request $request){ // dd($request->all());
        $id = $request->clientid;
        $client_info = \App\Models\Admin::select('client_id')->where('id', $id)->first(); //dd($admin);
        if(!empty($client_info)){
            $client_id = $client_info->client_id;
        } else {
            $client_id = "";
        }  //dd($client_id);
        $doctype = isset($request->doctype)? $request->doctype : '';
        if ($request->hasfile('document_upload')) {
            if(!is_array($request->file('document_upload'))){
				$files[] = $request->file('document_upload');
			}else{
				$files = $request->file('document_upload');
			}

		    foreach ($files as $file) {
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
                $explodeFileName = explode('.', $fileName);

                //$document_upload = $this->uploadrenameFile($file, Config::get('constants.documents'));

                //$file = $request->file('document_upload');
                $name = time() . $file->getClientOriginalName();
                //$explodeFileName1 = explode('.', $name);
                //$filePath = 'documents/' . $name;
                $filePath = $client_id.'/'.$doctype.'/'. $name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));

                //$exploadename = explode('.', $document_upload);
                $exploadename = explode('.', $name);

                $obj = new \App\Models\Document;
                $obj->file_name = $explodeFileName[0];
                $obj->filetype = $exploadename[1];
                $obj->user_id = Auth::user()->id;
                //$obj->myfile = $document_upload;
                $obj->myfile = $name;

                $obj->client_id = $id;
                $obj->type = $request->type;
                $obj->file_size = $size;
                $obj->doc_type = $doctype;
                $saved = $obj->save();
            }

			if($saved){
				if($request->type == 'client'){
                    $subject = 'added 1 office receipt document';
                    $objs = new ActivitiesLog;
                    $objs->client_id = $id;
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();

				}
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully uploaded your office receipt document';
				$fetchd = \App\Models\Document::where('client_id',$id)->where('doc_type',$doctype)->where('type',$request->type)->orderby('created_at', 'DESC')->get();
				ob_start();
				foreach($fetchd as $fetch){
					$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
					?>
					<tr class="drow" id="id_<?php echo $fetch->id; ?>">
						<td><div data-id="<?php echo $fetch->id; ?>" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
							<i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name; ?><?php echo '.'.$fetch->filetype; ?></span>
						</div></td>
						<td><?php echo $admin->first_name; ?></td>

						<td><?php echo date('Y-m-d', strtotime($fetch->created_at)); ?></td>
						<td>
							<div class="dropdown d-inline">
								<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
								<div class="dropdown-menu">
									<a class="dropdown-item renamedoc" href="javascript:;">Rename</a>
                                    <?php
                                    $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                    ?>
									<a target="_blank" class="dropdown-item" href="<?php echo $url.$client_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Preview</a>

                                    <?php
									$explodeimg = explode('.',$fetch->myfile);
									if($explodeimg[1] == 'jpg'|| $explodeimg[1] == 'png'|| $explodeimg[1] == 'jpeg'){
									?>
										<a target="_blank" class="dropdown-item" href="<?php echo \URL::to('/admin/document/download/pdf'); ?>/<?php echo $fetch->id; ?>">PDF</a>
									<?php } ?>

									<a download class="dropdown-item" href="<?php echo $url.$client_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Download</a>

									<a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletedocs" href="javascript:;" >Delete</a>
								</div>
							</div>
						</td>
					</tr>
					<?php
				}
				$data = ob_get_clean();
				ob_start();
				foreach($fetchd as $fetch){
					$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
					?>
					<div class="grid_list">
						<div class="grid_col">
							<div class="grid_icon">
								<i class="fas fa-file-image"></i>
							</div>
							<div class="grid_content">
								<span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
								<div class="dropdown d-inline dropdown_ellipsis_icon">
									<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
									<div class="dropdown-menu">
										<!--<a class="dropdown-item" href="<?php //echo \URL::to('/public/img/documents'); ?>/<?php //echo $fetch->myfile; ?>">Preview</a>
										<a download class="dropdown-item" href="<?php //echo \URL::to('/public/img/documents'); ?>/<?php //echo $fetch->myfile; ?>">Download</a>-->

                                        <?php
                                        $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                        ?>
										<!--<a class="dropdown-item" href="<?php //echo $url.'documents/'.$fetch->myfile; ?>">Preview</a>
										<a download class="dropdown-item" href="<?php //echo $url.'documents/'.$fetch->myfile; ?>">Download</a>-->

                                        <a class="dropdown-item" href="<?php echo $url.$client_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Preview</a>
										<a download class="dropdown-item" href="<?php echo $url.$client_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Download</a>

										<a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletedocs" href="javascript:;" >Delete</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
				$griddata = ob_get_clean();
				$response['data']	=$data;
				$response['griddata']	=$griddata;
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		 }else{
			 $response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		 }
		 echo json_encode($response);
	}

    public function uploadjournalreceiptdocument(Request $request){ // dd($request->all());
        $id = $request->clientid;
        $client_info = \App\Models\Admin::select('client_id')->where('id', $id)->first(); //dd($admin);
        if(!empty($client_info)){
            $client_id = $client_info->client_id;
        } else {
            $client_id = "";
        }  //dd($client_id);
        $doctype = isset($request->doctype)? $request->doctype : '';
        if ($request->hasfile('document_upload')) {
            if(!is_array($request->file('document_upload'))){
				$files[] = $request->file('document_upload');
			}else{
				$files = $request->file('document_upload');
			}

		    foreach ($files as $file) {
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
                $explodeFileName = explode('.', $fileName);

                //$document_upload = $this->uploadrenameFile($file, Config::get('constants.documents'));

                //$file = $request->file('document_upload');
                $name = time() . $file->getClientOriginalName();
                //$explodeFileName1 = explode('.', $name);
                //$filePath = 'documents/' . $name;
                $filePath = $client_id.'/'.$doctype.'/'. $name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));

                //$exploadename = explode('.', $document_upload);
                $exploadename = explode('.', $name);

                $obj = new \App\Models\Document;
                $obj->file_name = $explodeFileName[0];
                $obj->filetype = $exploadename[1];
                $obj->user_id = Auth::user()->id;
                //$obj->myfile = $document_upload;
                $obj->myfile = $name;

                $obj->client_id = $id;
                $obj->type = $request->type;
                $obj->file_size = $size;
                $obj->doc_type = $doctype;
                $saved = $obj->save();
            }

			if($saved){
				if($request->type == 'client'){
                    $subject = 'added 1 journal receipt document';
                    $objs = new ActivitiesLog;
                    $objs->client_id = $id;
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();
                }
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully uploaded your journal receipt document';
				$fetchd = \App\Models\Document::where('client_id',$id)->where('doc_type',$doctype)->where('type',$request->type)->orderby('created_at', 'DESC')->get();
				ob_start();
				foreach($fetchd as $fetch){
					$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
					?>
					<tr class="drow" id="id_<?php echo $fetch->id; ?>">
						<td><div data-id="<?php echo $fetch->id; ?>" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
							<i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name; ?><?php echo '.'.$fetch->filetype; ?></span>
						</div></td>
						<td><?php echo $admin->first_name; ?></td>

						<td><?php echo date('Y-m-d', strtotime($fetch->created_at)); ?></td>
						<td>
							<div class="dropdown d-inline">
								<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
								<div class="dropdown-menu">
									<a class="dropdown-item renamedoc" href="javascript:;">Rename</a>
                                    <?php
                                    $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                    ?>
									<a target="_blank" class="dropdown-item" href="<?php echo $url.$client_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Preview</a>

                                    <?php
									$explodeimg = explode('.',$fetch->myfile);
									if($explodeimg[1] == 'jpg'|| $explodeimg[1] == 'png'|| $explodeimg[1] == 'jpeg'){
									?>
										<a target="_blank" class="dropdown-item" href="<?php echo \URL::to('/admin/document/download/pdf'); ?>/<?php echo $fetch->id; ?>">PDF</a>
									<?php } ?>

									<a download class="dropdown-item" href="<?php echo $url.$client_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Download</a>

									<a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletedocs" href="javascript:;" >Delete</a>
								</div>
							</div>
						</td>
					</tr>
					<?php
				}
				$data = ob_get_clean();
				ob_start();
				foreach($fetchd as $fetch){
					$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
					?>
					<div class="grid_list">
						<div class="grid_col">
							<div class="grid_icon">
								<i class="fas fa-file-image"></i>
							</div>
							<div class="grid_content">
								<span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
								<div class="dropdown d-inline dropdown_ellipsis_icon">
									<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
									<div class="dropdown-menu">
										<!--<a class="dropdown-item" href="<?php //echo \URL::to('/public/img/documents'); ?>/<?php //echo $fetch->myfile; ?>">Preview</a>
										<a download class="dropdown-item" href="<?php //echo \URL::to('/public/img/documents'); ?>/<?php //echo $fetch->myfile; ?>">Download</a>-->

                                        <?php
                                        $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                        ?>
										<!--<a class="dropdown-item" href="<?php //echo $url.'documents/'.$fetch->myfile; ?>">Preview</a>
										<a download class="dropdown-item" href="<?php //echo $url.'documents/'.$fetch->myfile; ?>">Download</a>-->

                                        <a class="dropdown-item" href="<?php echo $url.$client_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Preview</a>
										<a download class="dropdown-item" href="<?php echo $url.$client_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Download</a>

										<a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletedocs" href="javascript:;" >Delete</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
				$griddata = ob_get_clean();
				$response['data']	=$data;
				$response['griddata']	=$griddata;
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		 }else{
			 $response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		 }
		 echo json_encode($response);
	}

    public function invoicelist(Request $request)
    {
        $query 	= AccountClientReceipt::where('receipt_type',3)->groupBy('receipt_id');
        // Filter: Client ID
        if ($request->has('client_id') && trim($request->input('client_id')) != '') {
            $query->where('client_id', '=', $request->input('client_id'));
        }

        // Filter: Client Matter ID
        if ($request->has('client_matter_id') && trim($request->input('client_matter_id')) != '') {
            $query->where('client_matter_id', '=', $request->input('client_matter_id'));
        }

        // Filter: Transaction Date
        if ($request->has('trans_date')) {
            $transDate = trim($request->input('trans_date'));
            if ($transDate != '') {
                $query->where('trans_date', 'LIKE', '%' . $transDate . '%');
            }
        }

        // Filter: Amount
        if ($request->has('amount') && trim($request->input('amount')) != '') {
            $amount = trim($request->input('amount'));
            $query->where(function($q) use ($amount) {
                $q->where('balance_amount', '=', $amount)
                  ->orWhere('partial_paid_amount', '=', $amount);
            });
        }
        $totalData 	= $query->count();
        $lists = $query->sortable(['id' => 'desc'])->paginate(2);

        // Dropdown: Client list with receipts
        $clientIds = DB::table('account_client_receipts as acr')
            ->join('admins', 'admins.id', '=', 'acr.client_id')
            ->select('acr.client_id', 'admins.first_name', 'admins.last_name', 'admins.client_id as client_unique_id')
            ->distinct()
            ->orderBy('admins.first_name', 'asc')
            ->get();

        // Dropdown: Matter list with receipts
        $matterIds = DB::table('account_client_receipts as acr')
            ->join('client_matters', 'client_matters.id', '=', 'acr.client_matter_id')
            ->join('admins', 'admins.id', '=', 'acr.client_id')
            ->select('acr.client_matter_id', 'client_matters.client_unique_matter_no', 'admins.client_id as client_unique_id')
            ->distinct()
            ->orderBy('admins.client_id', 'asc')
            ->get();
        return view('Admin.clients.invoicelist', compact(['lists', 'totalData', 'clientIds', 'matterIds']));
    }

    public function void_invoice(Request $request){
        $response = array(); //dd($request->all());
        if( isset($request->clickedReceiptIds) && !empty($request->clickedReceiptIds) ){
            //Update all selected invoice bit to be 1
            $affectedRows = DB::table('account_client_receipts')
            ->where('receipt_type', 3)
            ->whereIn('receipt_id', $request->clickedReceiptIds)
            ->update(['void_invoice' => 1,'voided_or_validated_by' => Auth::user()->id,'invoice_status' => 3]); //invoice_status =3 voided
            if ($affectedRows > 0) {

                //update all invoices deposit amount to be zero
                foreach($request->clickedReceiptIds as $clickedKey=>$clickedVal){

                    //Save in activity log
                    $invoice_info = AccountClientReceipt::select('user_id','client_id')->where('receipt_id', $clickedVal)->first();
                    $client_info = \App\Models\Admin::select('client_id')->where('id', $invoice_info->client_id)->first();
                    $subject = 'voided invoice Sno -'.$clickedVal.' of client-'.$client_info->client_id;
                    $objs = new ActivitiesLog;
                    $objs->client_id = $invoice_info->client_id;
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();

                    $record_info = DB::table('account_client_receipts')
                    ->select('id','withdraw_amount','receipt_id','balance_amount')
                    ->where('receipt_type', 3)
                    ->where('receipt_id', $clickedVal)
                    ->where('void_invoice', 1)
                    ->get();
                    if(!empty($record_info)){
                        foreach($record_info as $infoVal){
                            DB::table('account_client_receipts')
                            ->where('id',$infoVal->id)
                            ->update(['withdraw_amount_before_void' => $infoVal->balance_amount,'withdraw_amount'=>'0.00','balance_amount'=>'0.00','partial_paid_amount'=>'0.00']);
                        }
                    }

                    //update account_all_invoice_receipts entries also
                    $record_info1 = DB::table('account_all_invoice_receipts')
                    ->select('id','withdraw_amount','receipt_id')
                    ->where('receipt_id', $clickedVal)
                    ->get();
                    if(!empty($record_info1)){
                        foreach($record_info1 as $infoVal1){
                           DB::table('account_all_invoice_receipts')
                            ->where('receipt_id',$infoVal1->receipt_id)
                            ->update(['withdraw_amount_before_void' => $infoVal1->withdraw_amount,'withdraw_amount'=>'0.00','invoice_status'=>'3']); //void
                        }
                    }
                }

                //Get record For strike line through
                $record_data = DB::table('account_client_receipts')
                ->leftJoin('admins', 'admins.id', '=', 'account_client_receipts.voided_or_validated_by')
                ->select('account_client_receipts.id','account_client_receipts.voided_or_validated_by','admins.first_name','admins.last_name')
                ->where('account_client_receipts.receipt_type', 3)
                ->whereIn('account_client_receipts.receipt_id', $request->clickedReceiptIds)
                ->where('account_client_receipts.void_invoice', 1)
                ->get();
                //dd($record_data);
                $response['record_data'] = 	$record_data;
                $response['status'] 	= 	true;
                $response['message']	=	'Record voided successfully.';
            } else {
                $response['status'] 	= 	true;
                $response['message']	=	'No record was updated.';
                $response['clickedIds'] = 	array();
            }
        }
        echo json_encode($response);
    }

    public function clientreceiptlist(Request $request)
    {
        $query = AccountClientReceipt::where('receipt_type', 1);

        // Filter: Client ID
        if ($request->has('client_id') && trim($request->input('client_id')) != '') {
            $query->where('client_id', '=', $request->input('client_id'));
        }

        // Filter: Client Matter ID
        if ($request->has('client_matter_id') && trim($request->input('client_matter_id')) != '') {
            $query->where('client_matter_id', '=', $request->input('client_matter_id'));
        }

        // Filter: Transaction Date
        if ($request->has('trans_date')) {
            $transDate = trim($request->input('trans_date'));
            if ($transDate != '') {
                $query->where('trans_date', 'LIKE', '%' . $transDate . '%');
            }
        }

        // Filter: Type
        if ($request->has('client_fund_ledger_type') && trim($request->input('client_fund_ledger_type')) != '') {
            $query->where('client_fund_ledger_type', 'LIKE', $request->input('client_fund_ledger_type'));
        }

        // Filter: Amount (search in both deposit_amount and withdraw_amount columns)
        if ($request->has('amount') && trim($request->input('amount')) != '') {
            $amount = trim($request->input('amount'));
            $query->where(function($q) use ($amount) {
                $q->where('deposit_amount', 'LIKE', '%' . $amount . '%')
                  ->orWhere('withdraw_amount', 'LIKE', '%' . $amount . '%');
            });
        }

        // Filter: Receipt Validate
        if ($request->has('receipt_validate') && trim($request->input('receipt_validate')) != '') {
            $receiptValidate = trim($request->input('receipt_validate'));
            $query->where('validate_receipt', '=', $receiptValidate);
        }

        // Total count for pagination/meta
        $totalData = $query->count();

        // Get records per page from request, default to 20
        $perPage = $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100, 200, 500]) ? $perPage : 20;

        // Fetch paginated list
        $lists = $query->sortable(['id' => 'desc'])->paginate($perPage);

        // Dropdown: Client list with receipts
        $clientIds = DB::table('account_client_receipts as acr')
            ->join('admins', 'admins.id', '=', 'acr.client_id')
            ->select('acr.client_id', 'admins.first_name', 'admins.last_name', 'admins.client_id as client_unique_id')
            ->distinct()
            ->orderBy('admins.first_name', 'asc')
            ->get();

        // Dropdown: Matter list with receipts
        $matterIds = DB::table('account_client_receipts as acr')
            ->join('client_matters', 'client_matters.id', '=', 'acr.client_matter_id')
            ->join('admins', 'admins.id', '=', 'acr.client_id')
            ->select('acr.client_matter_id', 'client_matters.client_unique_matter_no', 'admins.client_id as client_unique_id')
            ->distinct()
            ->orderBy('admins.client_id', 'asc')
            ->get();

        return view('Admin.clients.clientreceiptlist', compact(['lists', 'totalData', 'clientIds', 'matterIds', 'perPage']));
    }

    public function officereceiptlist(Request $request)
    {
        $query 	= AccountClientReceipt::where('receipt_type',2);
        // Filter: Client ID
        if ($request->has('client_id') && trim($request->input('client_id')) != '') {
            $query->where('client_id', '=', $request->input('client_id'));
        }

        // Filter: Client Matter ID
        if ($request->has('client_matter_id') && trim($request->input('client_matter_id')) != '') {
            $query->where('client_matter_id', '=', $request->input('client_matter_id'));
        }

        // Filter: Transaction Date
        if ($request->has('trans_date')) {
            $transDate = trim($request->input('trans_date'));
            if ($transDate != '') {
                $query->where('trans_date', 'LIKE', '%' . $transDate . '%');
            }
        }

        // Filter: Amount
        if ($request->has('amount') && trim($request->input('amount')) != '') {
            $amount = trim($request->input('amount'));
            $query->where('deposit_amount', 'LIKE', '%' . $amount . '%');
        }

        // Filter: Validate Receipt
        if ($request->has('validate_receipt') && trim($request->input('validate_receipt')) != '') {
            $validateReceipt = trim($request->input('validate_receipt'));
            $query->where('validate_receipt', '=', $validateReceipt);
        }
        
        $totalData 	= $query->count();
        $lists = $query->sortable(['id' => 'desc'])->paginate(20);

        // Dropdown: Client list with receipts
        $clientIds = DB::table('account_client_receipts as acr')
            ->join('admins', 'admins.id', '=', 'acr.client_id')
            ->select('acr.client_id', 'admins.first_name', 'admins.last_name', 'admins.client_id as client_unique_id')
            ->distinct()
            ->orderBy('admins.first_name', 'asc')
            ->get();

        // Dropdown: Matter list with receipts
        $matterIds = DB::table('account_client_receipts as acr')
            ->join('client_matters', 'client_matters.id', '=', 'acr.client_matter_id')
            ->join('admins', 'admins.id', '=', 'acr.client_id')
            ->select('acr.client_matter_id', 'client_matters.client_unique_matter_no', 'admins.client_id as client_unique_id')
            ->distinct()
            ->orderBy('admins.client_id', 'asc')
            ->get();

        return view('Admin.clients.officereceiptlist', compact(['lists', 'totalData', 'clientIds', 'matterIds']));
    }

    public function journalreceiptlist(Request $request)
	{
		$query 	= AccountClientReceipt::select('id','receipt_id','client_id','user_id','trans_date','entry_date','trans_no', 'invoice_no','payment_method','validate_receipt','voided_or_validated_by', DB::raw('sum(withdrawal_amount) as total_withdrawal_amount'))->where('receipt_type',4)->groupBy('receipt_id');
        $totalData 	= $query->count();
        $lists = $query->sortable(['id' => 'desc'])->paginate(20);
		return view('Admin.clients.journalreceiptlist', compact(['lists', 'totalData']));
    }

    public function validate_receipt(Request $request){
        $response = array(); //dd($request->all());
        if( isset($request->clickedReceiptIds) && !empty($request->clickedReceiptIds) ){
            //Update all selected receipt bit to be 1
            $affectedRows = DB::table('account_client_receipts')
            ->where('receipt_type', $request->receipt_type)
            ->whereIn('id', $request->clickedReceiptIds)
            ->update(['validate_receipt' => 1,'voided_or_validated_by' => Auth::user()->id]);
            if ($affectedRows > 0) {

                foreach($request->clickedReceiptIds as $ReceiptVal){
                    $receipt_info = AccountClientReceipt::select('user_id','client_id','trans_date')->where('id', $ReceiptVal)->first();
                    $client_info = \App\Models\Admin::select('client_id')->where('id', $receipt_info->client_id)->first();

                    if($request->receipt_type == 1){
                        $subject = 'validated client receipt no -'.$ReceiptVal.' of client-'.$client_info->client_id;
                    } else if($request->receipt_type == 2){
                        $subject = 'validated office receipt no -'.$ReceiptVal.' of client-'.$client_info->client_id;
                    } else if($request->receipt_type == 4){
                        $subject = 'validated journal receipt no -'.$ReceiptVal.' of client-'.$client_info->client_id;
                    }
                    $objs = new ActivitiesLog;
                    $objs->client_id = $receipt_info->client_id;
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();
                }

                //Get record validate_receipt =1
                $record_data = DB::table('account_client_receipts')
                ->leftJoin('admins', 'admins.id', '=', 'account_client_receipts.voided_or_validated_by')
                ->select('account_client_receipts.id','account_client_receipts.voided_or_validated_by','account_client_receipts.trans_date','admins.first_name','admins.last_name')
                ->where('account_client_receipts.receipt_type', $request->receipt_type)
                ->whereIn('account_client_receipts.id', $request->clickedReceiptIds)
                ->where('account_client_receipts.validate_receipt', 1)
                ->get();
                $response['record_data'] = 	$record_data;
                $response['status'] 	= 	true;
                $response['message']	=	'Receipt validated successfully.';
            } else {
                $response['status'] 	= 	true;
                $response['message']	=	'No record was updated.';
                $response['clickedIds'] = 	array();
            }
        }
        echo json_encode($response);
    }

    //Delete Receipt by Super admin - Celesty
    public function delete_receipt(Request $request)
    {  //dd($request->all());
        $response = array();
        if (isset($request->receiptId) && !empty($request->receiptId)) {
            // Ensure the user is a Super Admin with the correct email
            if (Auth::user()->role != '1' || Auth::user()->email != 'celestyparmar.62@gmail.com') {
                $response['status'] = false;
                $response['message'] = 'Unauthorized access.';
                echo json_encode($response);
                return;
            }

            // Fetch the receipt to be deleted
            $receipt = AccountClientReceipt::where('id', $request->receiptId)
                ->where('receipt_type', $request->receipt_type)
                ->first();

            if (!$receipt) {
                $response['status'] = false;
                $response['message'] = 'Receipt not found.';
                echo json_encode($response);
                return;
            }

            // Check if the client_fund_ledger_type is 'Fee Transfer'
            if ($receipt->client_fund_ledger_type == 'Fee Transfer') {
                $response['status'] = false;
                $response['message'] = 'This entry is already associated with an Invoice, so it cannot be deleted. Please try another.';
                echo json_encode($response);
                return;
            }

            // Store receipt details for balance adjustment and logging
            $client_id = $receipt->client_id;
            $deposit_amount = $receipt->deposit_amount ?? 0;
            $withdraw_amount = $receipt->withdraw_amount ?? 0;
            $receipt_id = $receipt->id;

            // Delete the receipt
            $affectedRows = AccountClientReceipt::where('id', $request->receiptId)
                ->where('receipt_type', $request->receipt_type)
                ->delete();

            if ($affectedRows > 0) {
                // Adjust balance (assuming a balance table or logic exists)
                // Example: Update client balance by reversing the transaction
                $client_info = \App\Models\Admin::select('id')->where('id', $client_id)->first();
                if ($client_info) {
                    // This is a placeholder for balance adjustment logic
                    // You may need to adjust this based on your actual balance management system
                    // For example, if you have a ClientBalance model:
                    // ClientBalance::where('client_id', $client_id)
                    //     ->decrement('balance', $deposit_amount - $withdraw_amount);
                }

                // Log the activity
                $client_info = \App\Models\Admin::select('client_id')->where('id', $client_id)->first();
                $subject = 'Deleted client receipt no -' . $receipt_id . ' of client-' . ($client_info->client_id ?? 'N/A');
                $objs = new ActivitiesLog;
                $objs->client_id = $client_id;
                $objs->created_by = Auth::user()->id;
                $objs->description = '';
                $objs->subject = $subject;
                $objs->save();

                $response['status'] = true;
                $response['message'] = 'Receipt deleted successfully.';
            } else {
                $response['status'] = false;
                $response['message'] = 'Failed to delete receipt.';
            }
        } else {
            $response['status'] = false;
            $response['message'] = 'No receipt selected.';
        }
        echo json_encode($response);
    }

	public function printPreview(Request $request, $id){
        $record_get = DB::table('account_client_receipts')->where('receipt_type',1)->where('id',$id)->get();
        //dd($record_get);
        if($record_get){
            $clientname = DB::table('admins')->select('first_name','last_name','address','state','city','zip','country')->where('id',$record_get[0]->client_id)->first();
            $agentname = DB::table('agents')->where('id',$record_get[0]->agent_id)->first();
            $admin = DB::table('admins')->select('company_name','address','state','city','zip','primary_email','phone')->where('id',$record_get[0]->user_id)->first();
        }
        //dd(storage_path('logs/log.htm'));
        $pdf = PDF::setOptions([
			'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
			'logOutputFile' => storage_path('logs/log.htm'),
			'tempDir' => storage_path('logs/')
		])->loadView('emails.printpreview',compact(['record_get','clientname','agentname','admin']));
		return $pdf->stream('ClientReceipt.pdf');
	}

    public function previewMsgFile($filename)
    {
        //dd($filename);
        //$filePath = storage_path('app/public/msgfiles/' . $filename);

        //$url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
        //$filePath = $url.$AdminInfo->client_id.'/'.$DocInfo->doc_type.'/'.$filename;

        //$filePath = 'https://bansalcrmdemo.s3.ap-southeast-2.amazonaws.com/ARTI2400003/conversion_email_fetch/14004.pdf';
        $filePath = 'https://bansalcrmdemo.s3.ap-southeast-2.amazonaws.com/ARTI2400003/conversion_email_fetch/1724409625172329274417231216441723035319Request received – Reference Number NPRS-1773829 (1).msg';
        try {
            // Parse the .msg file
            $message = Msg::fromFile($filePath); dd($message);
            $htmlContent = $this->convertMsgToHtml($message);

            return view('preview', ['content' => $htmlContent]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not parse MSG file: ' . $e->getMessage()], 500);
        }
    }

    private function convertMsgToHtml($message)
    {
        $html = "<h1>{$message->getHeaders()->getSubject()}</h1>";
        $html .= "<p>From: {$message->getHeaders()->getFrom()}</p>";
        $html .= "<p>To: {$message->getHeaders()->getTo()}</p>";
        $html .= "<p>Date: {$message->getHeaders()->getDate()}</p>";
        $html .= "<div>{$message->getBodyText()}</div>";

        return $html;
    }

    //Fetch all contact list of any client at create note popup
    public function fetchClientContactNo(Request $request){ //dd($request->all());
        if( ClientContact::where('client_id', $request->client_id)->exists()){
            //Fetch All client contacts
            $clientContacts = ClientContact::select('phone')->where('client_id', $request->client_id)->get();
            //dd($clientContacts);
            if( !empty($clientContacts) && count($clientContacts)>0 ){
                $response['status'] 	= 	true;
                $response['message']	=	'Client contact is successfully fetched.';
                $response['clientContacts']	=	$clientContacts;
            } else {
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['clientContacts']	=	array();
            }
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
            $response['clientContacts']	=	array();
        }
        echo json_encode($response);
	}

    public function updateAddress(Request $request)
    {
        $postcode = $request->input('postcode');
        // Fetch data based on the postcode
        // Replace this with your actual API call to get address details
        $apiKey = 'acb06506-edb3-4965-856e-db81ade1b45b';
        $urlPrefix = 'digitalapi.auspost.com.au';
        $url = 'https://' . $urlPrefix . '/postcode/search.json?q=' . $postcode;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['AUTH-KEY: ' . $apiKey]);
        $response = curl_exec($ch);  //dd($response);
        curl_close($ch);
        if (!$response) {
            return response()->json(['localities' => []]);
        }
        $data = json_decode($response, true); //dd($data);
        return response()->json($data);
    }

    public function showRatings()
    {
        // Get the authenticated user's ID
        $clientId = auth()->user()->id;

        // Fetch the latest rating for the user
        $latestRating = DB::table('client_ratings')
            ->where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->first(['education_rating', 'migration_rating']);

        // Calculate average ratings
        $averageEducationRating = DB::table('client_ratings')->avg('education_rating');
        $averageMigrationRating = DB::table('client_ratings')->avg('migration_rating');

        // Return the data to the view
        return view('Admin.clients.detail', [
            'latest_rating' => $latestRating,
            'average_education_rating' => $averageEducationRating,
            'average_migration_rating' => $averageMigrationRating,
        ]);
    }

    public function saveRating(Request $request)
	{
		// Validate the incoming request
		$validated = $request->validate([
		'education_rating' => 'required|integer|min:1|max:5',
		'migration_rating' => 'required|integer|min:1|max:5',
		]);

		// Get the authenticated user's ID
		$clientId = auth()->user()->id;

		// Check if the record exists
		$existingRating = DB::table('client_ratings')->where('client_id', $clientId)->first();

		if ($existingRating) {
		// If record exists, update it
		DB::table('client_ratings')
		->where('client_id', $clientId)
		->update([
		'education_rating' => $request->input('education_rating'),
		'migration_rating' => $request->input('migration_rating'),
		'updated_at' => now(), // Update the timestamp
		]);
		} else {
		// If no record exists, insert a new one
		DB::table('client_ratings')->insert([
		'client_id' => $clientId,
		'education_rating' => $request->input('education_rating'),
		'migration_rating' => $request->input('migration_rating'),
		'created_at' => now(), // Set the creation timestamp
		'updated_at' => now(), // Set the updated timestamp
		]);
		}

		// Calculate the sum of all ratings and the count of ratings
		$totalEducationRating = DB::table('client_ratings')->sum('education_rating');
		$totalMigrationRating = DB::table('client_ratings')->sum('migration_rating');

		$countEducationRating = DB::table('client_ratings')->count('education_rating');
		$countMigrationRating = DB::table('client_ratings')->count('migration_rating');

		// Calculate the average by dividing the total sum by the count
		$averageEducationRating = $countEducationRating > 0 ? $totalEducationRating / $countEducationRating : 0;
		$averageMigrationRating = $countMigrationRating > 0 ? $totalMigrationRating / $countMigrationRating : 0;

		// Return a JSON response
		return response()->json([
		'status' => true,
		'message' => 'Ratings saved or updated successfully.',
		'average_education_rating' => $averageEducationRating,
		'average_migration_rating' => $averageMigrationRating
		]);
	}

    //Re-assign inbox email
    public function reassiginboxemail(Request $request) {
		$requestData = $request->all(); //dd($requestData);
		$uploaded_doc_id = $requestData['uploaded_doc_id'];
        if( \App\Models\Document::where('id', '=', $uploaded_doc_id)->exists() )
		{
            //Get existing document info
            $document_info = \App\Models\Document::select('id','file_name','filetype','myfile','client_id')->where('id', '=', $uploaded_doc_id)->first();
            $source_doc_client_id = $document_info['client_id'];
            $source_doc_myfile = $document_info['myfile'];

            $source_doc_admin_info = \App\Models\Admin::select('client_id')->where('id', '=', $source_doc_client_id)->first();
            $source_doc_client_unique_id = $source_doc_admin_info['client_id'];

            $dest_assign_client_id = $requestData['reassign_client_id'];
            $dest_doc_admin_info = \App\Models\Admin::select('client_id')->where('id', '=', $dest_assign_client_id)->first();
            $dest_doc_client_unique_id = $dest_doc_admin_info['client_id'];

            // Define the source and destination paths
            $sourcePath = $source_doc_client_unique_id.'/conversion_email_fetch/'.$requestData['mail_type'].'/'.$source_doc_myfile; // Replace with your source file path
            $destinationPath = $dest_doc_client_unique_id.'/conversion_email_fetch/'.$requestData['mail_type'].'/'.$source_doc_myfile; // Replace with your destination file path

            try {
                // Check if the file exists before copying
                if (Storage::disk('s3')->exists($sourcePath)) {
                    // Use the copy method to copy the file within S3
                    Storage::disk('s3')->copy($sourcePath, $destinationPath);
                    Storage::disk('s3')->delete($sourcePath);
                    //echo "File copied successfully.";
                } else {
                    //echo "Source file does not exist.";
                }
            } catch (\Exception $e) {
                // Handle errors here
                echo "Error: " . $e->getMessage();
            }

            //Update document with client id and matter id
            $upd_doc_info = \App\Models\Document::find($uploaded_doc_id);
            $upd_doc_info->client_id = $requestData['reassign_client_id'];
            $upd_doc_info->user_id = Auth::user()->id;
            $upd_doc_info->client_matter_id = $requestData['reassign_client_matter_id'];
            $saved_doc_info = $upd_doc_info->save();
            if($saved_doc_info){
                //Update mail_reports table with client id and matter id
                $id = $requestData['memail_id'];
                $mail_report_info = \App\Models\MailReport::find($id);
                $mail_report_info->client_id = $requestData['reassign_client_id'];
                $mail_report_info->user_id = Auth::user()->id;
                $mail_report_info->client_matter_id = $requestData['reassign_client_matter_id'];
                $saved_mail_report_info = $mail_report_info->save();
                if($saved_mail_report_info){
                    $client_matter_info = \App\Models\ClientMatter::select('client_unique_matter_no')->where('id', '=', $requestData['reassign_client_matter_id'])->first();
                    $subject = 'Inbox Email Re-assign';
                    $objs = new \App\Models\ActivitiesLog;
                    $objs->client_id = $requestData['reassign_client_id'];
                    $objs->created_by = Auth::user()->id;
                    $objs->description = $dest_doc_client_unique_id.'-'.$client_matter_info['client_unique_matter_no'];
                    $objs->subject = $subject;
                    $objs->save();
                }

                //Update date in client matter table
                if( isset( $requestData['reassign_client_matter_id'] ) && $requestData['reassign_client_matter_id'] != ""){
                    $obj1 = \App\Models\ClientMatter::find($requestData['reassign_client_matter_id']);
                    $obj1->updated_at = date('Y-m-d H:i:s');
                    $obj1->save();
                }
            }
            if(!$saved_mail_report_info) {
                return redirect()->back()->with('error', Config::get('constants.server_error'));
            } else {
                return redirect()->back()->with('success', 'Inbox email re-assigned successfully');
            }
        } else {
            return redirect()->back()->with('error', Config::get('constants.server_error'));
		}
    }

    //Re-assign sent email
    public function reassigsentemail(Request $request) {
		$requestData = $request->all(); //dd($requestData);
		$uploaded_doc_id = $requestData['uploaded_doc_id'];
        if( \App\Models\Document::where('id', '=', $uploaded_doc_id)->exists() )
		{
            //Get existing document info
            $document_info = \App\Models\Document::select('id','file_name','filetype','myfile','client_id')->where('id', '=', $uploaded_doc_id)->first();
            $source_doc_client_id = $document_info['client_id'];
            $source_doc_myfile = $document_info['myfile'];

            $source_doc_admin_info = \App\Models\Admin::select('client_id')->where('id', '=', $source_doc_client_id)->first();
            $source_doc_client_unique_id = $source_doc_admin_info['client_id'];

            $dest_assign_client_id = $requestData['reassign_sent_client_id'];
            $dest_doc_admin_info = \App\Models\Admin::select('client_id')->where('id', '=', $dest_assign_client_id)->first();
            $dest_doc_client_unique_id = $dest_doc_admin_info['client_id'];

            // Define the source and destination paths
            $sourcePath = $source_doc_client_unique_id.'/conversion_email_fetch/'.$requestData['mail_type'].'/'.$source_doc_myfile; // Replace with your source file path
            $destinationPath = $dest_doc_client_unique_id.'/conversion_email_fetch/'.$requestData['mail_type'].'/'.$source_doc_myfile; // Replace with your destination file path

            try {
                // Check if the file exists before copying
                if (Storage::disk('s3')->exists($sourcePath)) {
                    // Use the copy method to copy the file within S3
                    Storage::disk('s3')->copy($sourcePath, $destinationPath);
                    Storage::disk('s3')->delete($sourcePath);
                    //echo "File copied successfully.";
                } else {
                    //echo "Source file does not exist.";
                }
            } catch (\Exception $e) {
                // Handle errors here
                echo "Error: " . $e->getMessage();
            }

            //Update document with client id and matter id
            $upd_doc_info = \App\Models\Document::find($uploaded_doc_id);
            $upd_doc_info->client_id = $requestData['reassign_sent_client_id'];
            $upd_doc_info->user_id = Auth::user()->id;
            $upd_doc_info->client_matter_id = $requestData['reassign_sent_client_matter_id'];
            $saved_doc_info = $upd_doc_info->save();
            if($saved_doc_info){
                //Update mail_reports table with client id and matter id
                $id = $requestData['memail_id'];
                $mail_report_info = \App\Models\MailReport::find($id);
                $mail_report_info->client_id = $requestData['reassign_sent_client_id'];
                $mail_report_info->user_id = Auth::user()->id;
                $mail_report_info->client_matter_id = $requestData['reassign_sent_client_matter_id'];
                $saved_mail_report_info = $mail_report_info->save();
                if($saved_mail_report_info){
                    $client_matter_info = \App\Models\ClientMatter::select('client_unique_matter_no')->where('id', '=', $requestData['reassign_sent_client_matter_id'])->first();
                    $subject = 'Sent Email Re-assign';
                    $objs = new \App\Models\ActivitiesLog;
                    $objs->client_id = $requestData['reassign_sent_client_id'];
                    $objs->created_by = Auth::user()->id;
                    $objs->description = $dest_doc_client_unique_id.'-'.$client_matter_info['client_unique_matter_no'];
                    $objs->subject = $subject;
                    $objs->save();
                }

                //Update date in client matter table
                if( isset($requestData['reassign_sent_client_matter_id']) && $requestData['reassign_sent_client_matter_id'] != ""){
                    $obj1 = \App\Models\ClientMatter::find($requestData['reassign_sent_client_matter_id']);
                    $obj1->updated_at = date('Y-m-d H:i:s');
                    $obj1->save();
                }
            }
            if(!$saved_mail_report_info) {
                return redirect()->back()->with('error', Config::get('constants.server_error'));
            } else {
                return redirect()->back()->with('success', 'Sent email re-assigned successfully');
            }
        } else {
            return redirect()->back()->with('error', Config::get('constants.server_error'));
		}
    }

    //Fetch selected client all matters at assign email to user popup
    public function listAllMattersWRTSelClient(Request $request){ //dd($request->all());
        if( ClientMatter::where('client_id', $request->client_id)->exists()){
            //Fetch All client matters
            $clientMatetrs = ClientMatter::join('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
            ->select('client_matters.id', 'matters.title','client_matters.client_unique_matter_no')
            ->where('client_id', $request->client_id)
            ->get(); //dd($clientMatetrs);
            if( !empty($clientMatetrs) && count($clientMatetrs)>0 ){
                $response['status'] 	= 	true;
                $response['message']	=	'Client matter is successfully fetched.';
                $response['clientMatetrs']	=	$clientMatetrs;
            } else {
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['clientMatetrs']	=	array();
            }
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
            $response['clientMatetrs']	=	array();
        }
        echo json_encode($response);
	}

    public function verifydoc(Request $request){ //dd($request->all());
		$doc_id = $request->doc_id;
        $doc_type = $request->doc_type;
        if(\App\Models\Document::where('id',$doc_id)->exists()){
            $upd = DB::table('documents')->where('id', $doc_id)->update(array(
                'checklist_verified_by' => Auth::user()->id,
                'checklist_verified_at' => date('Y-m-d H:i:s')
            ));
            if($upd){
                $docInfo = \App\Models\Document::select('client_id','doc_type','folder_name')->where('id',$doc_id)->first();
                $subject = 'verified '.$doc_type.' document';
                $objs = new ActivitiesLog;
				$objs->client_id = $docInfo->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->save();
                //Get verified at and verified by
                $admin_info = DB::table('admins')->select('first_name')->where('id', '=',Auth::user()->id)->first();
                if($admin_info){
                    $response['verified_by'] = 	$admin_info->first_name;
                    $response['verified_at'] = 	date('d/m/Y');
                } else {
                    $response['verified_by'] = "";
                    $response['verified_at'] = "";
                }
                $response['doc_type'] = $doc_type;
				$response['status'] = 	true;
				$response['data']	=	$doc_type.' Document verified successfully';
                if(isset($docInfo->doc_type) && $docInfo->doc_type == 'personal'){
                    $response['doc_category'] = $docInfo->folder_name;
                } else {
                    $response['doc_category'] = "";
                }

			} else {
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
                $response['verified_by'] = "";
                $response['verified_at'] = "";
                $response['doc_type'] = "";
                $response['doc_category'] = "";
			}
		} else {
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
            $response['verified_by'] = "";
            $response['verified_at'] = "";
            $response['doc_type'] = "";
            $response['doc_category'] = "";
		}
		echo json_encode($response);
	}

    //Get Visa check list wrt matter id
    public function getvisachecklist(Request $request){ //dd($request->all());
        if( ClientMatter::where('id', $request->client_matter_id)->exists()){
            $clientMatterInfo = ClientMatter::select('sel_matter_id')->where('id',$request->client_matter_id)->first();
            //dd($clientMatterInfo->sel_matter_id);
            if( isset($clientMatterInfo) ){
                $visaCheckListInfo = VisaDocChecklist::select('id','name')->whereRaw("FIND_IN_SET($clientMatterInfo->sel_matter_id, matter_id)")->get();
                //dd($visaCheckListInfo);
                if( !empty($visaCheckListInfo) && count($visaCheckListInfo)>0 ){
                    $response['status'] 	= 	true;
                    $response['message']	=	'Visa checklist is successfully fetched.';
                    $response['visaCheckListInfo']	=	$visaCheckListInfo;
                } else {
                    $response['status'] 	= 	false;
                    $response['message']	=	'Please try again';
                    $response['visaCheckListInfo'] = array();
                }
            } else {
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['visaCheckListInfo']	=	array();
            }
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
            $response['visaCheckListInfo']	=	array();
        }
        echo json_encode($response);
	}

    public function updateOccupation(Request $request)
    {
        $occupation = $request->input('occupation');

        // Example: Replace this with actual search logic based on your database schema
        $occupations = \DB::table('client_occupation_lists')
            ->where('occupation', 'like', "%{$occupation}%")
            ->get(['occupation', 'occupation_code', 'list', 'visa_subclass','access_authority']);

        return response()->json(['occupations' => $occupations]);
    }

    public function checkEmail(Request $request)
    {
        $email = $request->input('email');

        // Check if email exists in the database
        $exists = DB::table('client_emails')->where('email', $email)->exists();

        $exists_admin = DB::table('admins')->where('email', $email)->exists();

        if ($exists || $exists_admin) {
            return response()->json(['status' => 'exists']);
        } else {
            return response()->json(['status' => 'available']);
        }
    }

    public function checkContact(Request $request)
    {
        $contact = $request->input('phone');

        // Check if the contact number exists in the client_contacts table
        $exists = DB::table('client_contacts')->where('phone', $contact)->exists();
        $exists_admin = DB::table('admins')->where('phone', $contact)->exists();

        if ($exists || $exists_admin) {
            return response()->json(['status' => 'exists']);
        } else {
            return response()->json(['status' => 'available']);
        }
    }

    //Not Used Document
    public function notuseddoc(Request $request){ //dd($request->all());
		$doc_id = $request->doc_id;
        $doc_type = $request->doc_type;
        if(\App\Models\Document::where('id',$doc_id)->exists()){
            $upd = DB::table('documents')->where('id', $doc_id)->update(array('not_used_doc' => 1));
            if($upd){
                $docInfo = \App\Models\Document::where('id',$doc_id)->first();
                $subject = $doc_type.' document moved to Not Used Tab';
                $objs = new ActivitiesLog;
				$objs->client_id = $docInfo->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->save();

                if($docInfo){
                    if( isset($docInfo->user_id) && $docInfo->user_id!= "" ){
                        $adminInfo = \App\Models\Admin::select('first_name')->where('id',$docInfo->user_id)->first();
                        $response['Added_By'] = $adminInfo->first_name;
                        $response['Added_date'] = date('d/m/Y',strtotime($docInfo->created_at));
                    } else {
                        $response['Added_By'] = "N/A";
                        $response['Added_date'] = "N/A";
                    }

                    if( isset($docInfo->checklist_verified_by) && $docInfo->checklist_verified_by!= "" ){
                        $verifyInfo = \App\Models\Admin::select('first_name')->where('id',$docInfo->checklist_verified_by)->first();
                        $response['Verified_By'] = $verifyInfo->first_name;
                        $response['Verified_At'] = date('d/m/Y',strtotime($docInfo->checklist_verified_at));
                    } else {
                        $response['Verified_By'] = "N/A";
                        $response['Verified_At'] = "N/A";
                    }

                }

                $response['docInfo'] = $docInfo;
                $response['doc_type'] = $doc_type;
                $response['doc_id'] = $doc_id;

                if(isset($docInfo->doc_type) && $docInfo->doc_type == 'personal'){
                    $response['doc_category'] = $docInfo->folder_name;
                } else {
                    $response['doc_category'] = "";
                }
				$response['status'] = 	true;
				$response['data']	=	$doc_type.' document moved to Not Used Tab';
			} else {
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
                $response['doc_type'] = "";
                $response['doc_id'] = "";
                $response['docInfo'] = "";
                $response['doc_category'] = "";
                $response['Added_By'] = "";
                $response['Added_date'] = "";
                $response['Verified_By'] = "";
                $response['Verified_At'] = "";
			}
		} else {
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
            $response['doc_type'] = "";
            $response['doc_id'] = "";
            $response['docInfo'] = "";
            $response['doc_category'] = "";

            $response['Added_By'] = "";
            $response['Added_date'] = "";
            $response['Verified_By'] = "";
            $response['Verified_At'] = "";
		}
		echo json_encode($response);
	}

    //Rename checklist in Document
    public function renamechecklistdoc(Request $request){
		$id = $request->id;
		$checklist = $request->checklist;
		if(\App\Models\Document::where('id',$id)->exists()){
			$doc = \App\Models\Document::where('id',$id)->first();
			$res = DB::table('documents')->where('id', @$id)->update(['checklist' => $checklist]);
			if($res){
				$response['status'] 	= 	true;
				$response['data']	=	'Checklist saved successfully';
				$response['Id']	=	$id;
				$response['checklist']	=	$checklist;
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

    //mail preview click update mail_is_read bit
    public function updatemailreadbit(Request $request){ //dd($request->all());
        if( \App\Models\MailReport::where('id', $request->mail_report_id)->exists()){
            $mailReportInfo = \App\Models\MailReport::select('mail_is_read')->where('id', $request->mail_report_id)->first();
            //dd($mailReportInfo);
            if( $mailReportInfo ){
                $mail_report_info = \App\Models\MailReport::find($request->mail_report_id);
                $mail_report_info->mail_is_read = 1;
                $mail_report_info->save();

                $response['status'] 	= 	true;
                $response['message']	=	'Mail is successfully updated';
            } else {
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
            }
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
        }
        echo json_encode($response);
	}

    //Back To their respective Document List From Not Used Tab
    public function backtodoc(Request $request){ //dd($request->all());
		$doc_id = $request->doc_id;
        $doc_type = $request->doc_type;
        if(\App\Models\Document::where('id',$doc_id)->exists()){
            $upd = DB::table('documents')->where('id', $doc_id)->update(array('not_used_doc' => null));
            if($upd){
                $docInfo = \App\Models\Document::where('id',$doc_id)->first();
                $subject = $doc_type.' document moved to '.$doc_type.' document tab';
                $objs = new ActivitiesLog;
				$objs->client_id = $docInfo->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->save();

                if($docInfo){
                    if( isset($docInfo->user_id) && $docInfo->user_id!= "" ){
                        $adminInfo = \App\Models\Admin::select('first_name')->where('id',$docInfo->user_id)->first();
                        $response['Added_By'] = $adminInfo->first_name;
                        $response['Added_date'] = date('d/m/Y',strtotime($docInfo->created_at));
                    } else {
                        $response['Added_By'] = "N/A";
                        $response['Added_date'] = "N/A";
                    }

                    if( isset($docInfo->checklist_verified_by) && $docInfo->checklist_verified_by!= "" ){
                        $verifyInfo = \App\Models\Admin::select('first_name')->where('id',$docInfo->checklist_verified_by)->first();
                        $response['Verified_By'] = $verifyInfo->first_name;
                        $response['Verified_At'] = date('d/m/Y',strtotime($docInfo->checklist_verified_at));
                    } else {
                        $response['Verified_By'] = "N/A";
                        $response['Verified_At'] = "N/A";
                    }

                }

                $response['docInfo'] = $docInfo;
                $response['doc_type'] = $doc_type;
                $response['doc_id'] = $doc_id;
				$response['status'] = 	true;
				$response['data']	=	$doc_type.' document moved to '.$doc_type.' document tab';
			} else {
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
                $response['doc_type'] = "";
                $response['doc_id'] = "";
                $response['docInfo'] = "";

                $response['Added_By'] = "";
                $response['Added_date'] = "";
                $response['Verified_By'] = "";
                $response['Verified_At'] = "";
			}
		} else {
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
            $response['doc_type'] = "";
            $response['doc_id'] = "";
            $response['docInfo'] = "";

            $response['Added_By'] = "";
            $response['Added_date'] = "";
            $response['Verified_By'] = "";
            $response['Verified_At'] = "";
		}
		echo json_encode($response);
	}

    //chatgpt enhance message
    public function enhanceMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        try {
            $response = $this->openAiClient->post('chat/completions', [
                'json' => [
                    'model' => 'gpt-3.5-turbo', // or 'gpt-4' if you have access
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a professional email writer. Rewrite the following content in a more professional and polished manner:'
                        ],
                        [
                            'role' => 'user',
                            'content' => $request->message
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 500,
                ],
            ]);

            $result = json_decode($response->getBody(), true);
            $enhancedMessage = $result['choices'][0]['message']['content'];

            return response()->json(['enhanced_message' => $enhancedMessage]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to enhance message: ' . $e->getMessage()], 500);
        }
    }

    //Filter Inbox emails
    public function filterEmails(Request $request)
    {
        try {
            $client_id = $request->input('client_id');
            $status = $request->input('status');
            $search = $request->input('search');

            // Base query for inbox mail
            $query = \App\Models\MailReport::where('client_id', $client_id)
                ->where('type', 'client')
                ->where('mail_type', 1)
                ->where('conversion_type', 'conversion_email_fetch')
                ->where('mail_body_type', 'inbox')
                ->orderBy('created_at', 'DESC');

            // Filter by status (mail_is_read)
            if ($status !== '') {
                if($status == 1) {
                    // Status = 1: Only fetch emails where mail_is_read = 1
                    $query->where('mail_is_read', $status);
                } else if ($status == 2) {
                    // Status = 2: Fetch emails where mail_is_read is either 0 or NULL
                    $query->where(function ($q) {
                        $q->where('mail_is_read', 0)
                          ->orWhereNull('mail_is_read');
                    });
                }
                // If $status is neither '1' nor '2' (e.g., empty or invalid), do nothing
            }

            // Search in subject, message, from_mail, or to_email
            if ($search !== '') {
                $query->where(function($q) use ($search) {
                    $q->where('subject', 'LIKE', "%{$search}%")
                      ->orWhere('message', 'LIKE', "%{$search}%")
                      ->orWhere('from_mail', 'LIKE', "%{$search}%")
                      ->orWhere('to_mail', 'LIKE', "%{$search}%");
                });
            }

            // Fetch the emails
            $emails = $query->get();

            // Base URL for AWS S3
            $url = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/';

            // Map the emails to include the preview URL
            $emails = $emails->map(function ($email) use ($url, $client_id) {
                // Fetch the associated document (DocInfo) for this email
                // Assuming there's a Document model with a relationship or query to fetch DocInfo
                $DocInfo = \App\Models\Document::select('id','doc_type','myfile','myfile_key','mail_type')
                    ->where('id', $email->uploaded_doc_id) // Adjust this based on your actual relationship or query
                    ->first();

                $AdminInfo = \App\Models\Admin::select('client_id')->where('id',$email->client_id)->first();
                // Compute the preview URL based on the PHP logic
                $previewUrl = '';
                if ($DocInfo) {
                    if (isset($DocInfo->myfile_key) && $DocInfo->myfile_key != "") {
                        // New file upload
                        $previewUrl = $DocInfo->myfile;
                    } else {
                        // Old file upload
                        $previewUrl = $url . $AdminInfo->client_id . '/' . ($DocInfo->doc_type ?? 'mail') . '/' . ($DocInfo->mail_type ?? 'inbox') . '/' . $DocInfo->myfile;
                    }
                }

                // Add the preview URL to the email object
                $email->preview_url = $previewUrl;
                return $email;
            });

            // Ensure the response is valid JSON
            return response()->json($emails, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error in filterEmails: ' . $e->getMessage());

            // Return an error response
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching emails: ' . $e->getMessage(),
            ], 500);
        }
    }

    //Filter Sent emails
    public function filterSentEmails(Request $request)
    {
        try
		{
            $client_id = $request->input('client_id');
            $type = $request->input('type');
            $status = $request->input('status');
            $search = $request->input('search');

            // Validate input
            if (!$client_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Client ID is required'
                ], 400);
            }

            // Base query for sent mail
            $query = \App\Models\MailReport::where('client_id', $client_id)
                ->where('type', 'client')
                ->where('mail_type', 1)
                ->where(function ($query) {
                    $query->whereNull('conversion_type')
                        ->orWhere(function ($subQuery) {
                            $subQuery->where('conversion_type', 'conversion_email_fetch')
                                ->where('mail_body_type', 'sent');
                        });
                })
                ->orderBy('created_at', 'DESC');

            // Filter by type
            if ($type !== '') {
                if ($type == 1) {
                    $query->whereNotNull('conversion_type');
                } elseif ($type == 2) {
                    $query->whereNull('conversion_type');
                }
            }

            // Filter by status
            if ($status !== '') {
                if ($status == 1) {
                    $query->where('mail_is_read', 1);
                } elseif ($status == 2) {
                    $query->where(function ($q) {
                        $q->where('mail_is_read', 0)
                          ->orWhereNull('mail_is_read');
                    });
                }
            }

            // Search filter
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('subject', 'LIKE', "%{$search}%")
                      ->orWhere('message', 'LIKE', "%{$search}%")
                      ->orWhere('from_mail', 'LIKE', "%{$search}%")
                      ->orWhere('to_mail', 'LIKE', "%{$search}%");
                });
            }

            // Fetch emails
            $emails = $query->get();

            // Base URL for AWS S3
            $url = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/';

            // Map emails with additional data
            $emails = $emails->map(function ($email) use ($url, $client_id) {
                $previewUrl = '';

                if (!empty($email->uploaded_doc_id)) {
                    $docInfo = \App\Models\Document::select('id', 'doc_type', 'myfile', 'myfile_key', 'mail_type')
                        ->where('id', $email->uploaded_doc_id)
                        ->first();
					if ($docInfo) {
                        if ($docInfo->myfile_key) {
							$previewUrl = $docInfo->myfile;
						} else {
							$previewUrl = $url . $client_id . '/' . ($docInfo->doc_type ?? 'mail') . '/' . ($docInfo->mail_type ?? 'sent') . '/' . $docInfo->myfile;
						}
					}
				} else {
					$previewUrl = '';
				}

				$email->preview_url = $previewUrl;
				$email->from_mail = $email->from_mail ?? '';
				$email->to_mail = $email->to_mail ?? '';
				$email->subject = $email->subject ?? '';
				$email->message = $email->message ?? '';
				return $email;
			});

			return response()->json($emails, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		} catch (\Exception $e) {
			Log::error('Error in filterSentEmails: ' . $e->getMessage(), [
				'request' => $request->all(),
				'trace' => $e->getTraceAsString()
			]);

			return response()->json([
				'status' => 'error',
				'message' => 'An error occurred while fetching emails'
			], 500);
		}
	}

    public function genClientFundLedgerInvoice(Request $request, $id){
        $record_get = DB::table('account_client_receipts')->where('receipt_type',1)->where('id',$id)->first();
        //dd($record_get);
        $clientname = DB::table('admins')->where('id',$record_get->client_id)->first();

        //Get client matter
        if( !empty($record_get) && $record_get->client_id != '') {
            $client_matter_no = '';
            $client_info = DB::table('admins')->select('client_id')->where('id',$record_get->client_id)->first();
            if($client_info){
                $client_unique_id = $client_info->client_id; //dd($client_unique_id);
            } else {
                $client_unique_id = '';
            }

            $matter_info = DB::table('client_matters')->select('client_unique_matter_no')->where('client_id',$record_get->client_id)->first();
            if($matter_info){
                $client_unique_matter_no = $matter_info->client_unique_matter_no;
                $client_matter_no = $client_unique_id.'-'.$client_unique_matter_no;
            } else {
                $client_unique_matter_no = '';
                $client_matter_no = '';
            }
        } else {
            $client_matter_no = '';
        }

        $pdf = PDF::setOptions([
			'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
			'logOutputFile' => storage_path('logs/log.htm'),
			'tempDir' => storage_path('logs/')
		])->loadView('emails.genclientfundledgerinvoice',compact(['record_get','clientname','client_matter_no']));
		//
		return $pdf->stream('Invoice.pdf');
	}

    public function genofficereceiptInvoice(Request $request, $id){
        $record_get = DB::table('account_client_receipts')->where('receipt_type',2)->where('id',$id)->first();
        //dd($record_get);
        $clientname = DB::table('admins')->where('id',$record_get->client_id)->first();

        //Get client matter
        if( !empty($record_get) && $record_get->client_id != '') {
            $client_matter_no = '';
            $client_info = DB::table('admins')->select('client_id')->where('id',$record_get->client_id)->first();
            if($client_info){
                $client_unique_id = $client_info->client_id; //dd($client_unique_id);
            } else {
                $client_unique_id = '';
            }

            $matter_info = DB::table('client_matters')->select('client_unique_matter_no')->where('client_id',$record_get->client_id)->first();
            if($matter_info){
                $client_unique_matter_no = $matter_info->client_unique_matter_no;
                $client_matter_no = $client_unique_id.'-'.$client_unique_matter_no;
            } else {
                $client_unique_matter_no = '';
                $client_matter_no = '';
            }
        } else {
            $client_matter_no = '';
        }

        $pdf = PDF::setOptions([
			'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
			'logOutputFile' => storage_path('logs/log.htm'),
			'tempDir' => storage_path('logs/')
		])->loadView('emails.genofficereceiptinvoice',compact(['record_get','clientname','client_matter_no']));
		//
		return $pdf->stream('Invoice.pdf');
	}

    /*public function updateClientFundsLedger(Request $request)
    {   //dd($request->all());
        $requestData = $request->all();
        $id = $request->input('id');
        $trans_date = $request->input('trans_date');
        $entry_date = $request->input('entry_date');
        $client_fund_ledger_type = $request->input('client_fund_ledger_type');
        $description = $request->input('description');
        $deposit_amount = floatval($request->input('deposit_amount', 0));
        $withdraw_amount = floatval($request->input('withdraw_amount', 0));

        // Handle document upload
        $insertedDocId = "";
        $doc_saved = false;
        $client_unique_id = "";
        $awsUrl = "";
        $doctype = isset($request->doctype) ? $request->doctype : '';

        if ($request->hasfile('document_upload')) {
            $files = is_array($request->file('document_upload')) ? $request->file('document_upload') : [$request->file('document_upload')];

            $client_info = \App\Models\Admin::select('client_id')->where('id', $requestData['client_id'])->first();
            $client_unique_id = !empty($client_info) ? $client_info->client_id : "";

            foreach ($files as $file) {
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
                $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
                $fileExtension = $file->getClientOriginalExtension();
                $name = time() . $file->getClientOriginalName();
                $filePath = $client_unique_id . '/' . $doctype . '/' . $name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));

                $obj = new \App\Models\Document;
                $obj->file_name = $nameWithoutExtension;
                $obj->filetype = $fileExtension;
                $obj->user_id = Auth::user()->id;
                $obj->myfile = Storage::disk('s3')->url($filePath);
                $obj->myfile_key = $name;
                $obj->client_id = $requestData['client_id'];
                $obj->type = $request->type;
                $obj->file_size = $size;
                $obj->doc_type = $doctype;
                $doc_saved = $obj->save();
                $insertedDocId = $obj->id;
            }
        } else {
            $insertedDocId = "";
            $doc_saved = "";
        }

        // Validate that the entry is not a Fee Transfer
        $entry = DB::table('account_client_receipts')
            ->where('id', $id)
            ->where('receipt_type', 1)
            ->first();

        if (!$entry) {
            return response()->json([
                'status' => false,
                'message' => 'Entry not found.',
            ], 404);
        }

        if ($entry->client_fund_ledger_type === 'Fee Transfer') {
            return response()->json([
                'status' => false,
                'message' => 'Fee Transfer entries cannot be edited.',
            ], 403);
        }

        // Update the entry (excluding trans_no)
        $updated = DB::table('account_client_receipts')
            ->where('id', $id)
            ->update([
                'trans_date' => $trans_date,
                'entry_date' => $entry_date,
                'client_fund_ledger_type' => $client_fund_ledger_type,
                'description' => $description,
                'deposit_amount' => $deposit_amount,
                'withdraw_amount' => $withdraw_amount,
                'updated_at' => now(),
            ]);

        if ($updated) {
            // Recalculate balances for all entries
            $entries = DB::table('account_client_receipts')
                ->where('client_id', $entry->client_id)
                ->where('receipt_type', 1)
                ->orderBy('id', 'asc')
                ->get();

            $running_balance = 0;
            $updatedEntries = [];

            foreach ($entries as $entry) {
                $running_balance += floatval($entry->deposit_amount) - floatval($entry->withdraw_amount);
                DB::table('account_client_receipts')
                    ->where('id', $entry->id)
                    ->update(['balance_amount' => $running_balance]);

                $entry->balance_amount = $running_balance;
                $updatedEntries[] = $entry;
            }

            // Log activity
            $subject = "updated client funds ledger entry. Reference no- {$entry->trans_no}";
            $activity = new \App\Models\ActivitiesLog;
            $activity->client_id = $entry->client_id;
            $activity->created_by = auth()->user()->id;
            $activity->description = '';
            $activity->subject = $subject;
            $activity->save();

            return response()->json([
                'status' => true,
                'message' => 'Entry updated successfully.',
                'updatedEntries' => $updatedEntries,
                'currentFundsHeld' => $running_balance,
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Failed to update entry.',
        ], 500);
    }*/

    public function updateClientFundsLedger(Request $request)
    {
        $requestData = $request->all();
        $id = $request->input('id');
        $trans_date = $request->input('trans_date');
        $entry_date = $request->input('entry_date');
        $client_fund_ledger_type = $request->input('client_fund_ledger_type');
        $description = $request->input('description');
        $deposit_amount = floatval($request->input('deposit_amount', 0));
        $withdraw_amount = floatval($request->input('withdraw_amount', 0));

        // Handle document upload
        $insertedDocId = null; // Use null to indicate no document uploaded
        $client_unique_id = "";
        $doctype = isset($request->doctype) ? $request->doctype : '';

        if ($request->hasFile('document_upload')) {
            $files = is_array($request->file('document_upload')) ? $request->file('document_upload') : [$request->file('document_upload')];

            $client_info = \App\Models\Admin::select('client_id')->where('id', $requestData['client_id'])->first();
            $client_unique_id = !empty($client_info) ? $client_info->client_id : "";

            foreach ($files as $file) {
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
                $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
                $fileExtension = $file->getClientOriginalExtension();
                $name = time() . $file->getClientOriginalName();
                $filePath = $client_unique_id . '/' . $doctype . '/' . $name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));

                $obj = new \App\Models\Document;
                $obj->file_name = $nameWithoutExtension;
                $obj->filetype = $fileExtension;
                $obj->user_id = Auth::user()->id;
                $obj->myfile = Storage::disk('s3')->url($filePath);
                $obj->myfile_key = $name;
                $obj->client_id = $requestData['client_id'];
                $obj->type = $request->type;
                $obj->file_size = $size;
                $obj->doc_type = $doctype;
                $obj->save();

                $insertedDocId = $obj->id; // Store the last inserted ID
            }
        }

        // Validate that the entry is not a Fee Transfer
        $entry = DB::table('account_client_receipts')
            ->where('id', $id)
            ->where('receipt_type', 1)
            ->first();

        if (!$entry) {
            return response()->json([
                'status' => false,
                'message' => 'Entry not found.',
            ], 404);
        }

        if ($entry->client_fund_ledger_type === 'Fee Transfer') {
            return response()->json([
                'status' => false,
                'message' => 'Fee Transfer entries cannot be edited.',
            ], 403);
        }

        // Prepare the update data for account_client_receipts
        $updateData = [
            'trans_date' => $trans_date,
            'entry_date' => $entry_date,
            'client_fund_ledger_type' => $client_fund_ledger_type,
            'description' => $description,
            'deposit_amount' => $deposit_amount,
            'withdraw_amount' => $withdraw_amount,
            'updated_at' => now(),
        ];

        // If a document was uploaded, add the uploaded_doc_id to the update data
        if ($insertedDocId !== null) {
            $updateData['uploaded_doc_id'] = $insertedDocId;
        }

        // Update the entry in account_client_receipts (excluding trans_no)
        $updated = DB::table('account_client_receipts')
            ->where('id', $id)
            ->update($updateData);

        if ($updated) {
            // Recalculate balances for all entries
            $entries = DB::table('account_client_receipts')
                ->where('client_id', $entry->client_id)
                ->where('receipt_type', 1)
                ->orderBy('id', 'asc')
                ->get();

            $running_balance = 0;
            $updatedEntries = [];

            foreach ($entries as $entry) {
                $running_balance += floatval($entry->deposit_amount) - floatval($entry->withdraw_amount);
                DB::table('account_client_receipts')
                    ->where('id', $entry->id)
                    ->update(['balance_amount' => $running_balance]);

                $entry->balance_amount = $running_balance;
                $updatedEntries[] = $entry;
            }

            // Log activity
            $subject = "updated client funds ledger entry. Reference no- {$entry->trans_no}";
            $activity = new \App\Models\ActivitiesLog;
            $activity->client_id = $entry->client_id;
            $activity->created_by = auth()->user()->id;
            $activity->description = '';
            $activity->subject = $subject;
            $activity->save();

            return response()->json([
                'status' => true,
                'message' => 'Entry updated successfully.',
                'updatedEntries' => $updatedEntries,
                'currentFundsHeld' => $running_balance,
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Failed to update entry.',
        ], 500);
    }

    public function getInvoiceAmount(Request $request)
    {
        // Validate the request
        $request->validate([
            'invoice_no' => 'required|string',
        ]);

        // Fetch the balance_amount from account_client_receipts where receipt_type = 3
        $invoice = AccountClientReceipt::select('balance_amount')->where('invoice_no', $request->invoice_no)
            ->where('receipt_type', 3)
            ->first();
        if ($invoice) {
            return response()->json([
                'success' => true,
                'balance_amount' => $invoice->balance_amount,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invoice not found',
            'balance_amount' => 0,
        ]);
    }

    public function get_client_au_pr_point_details(Request $request)
	{
        $final_arr = array();
        $requestData = 	$request->all();
        $client_id = $requestData['client_id'];
        if($client_id) {
            //Age
            if( \App\Models\Admin::where('id', $client_id)->exists()){
                $clientInfo = \App\Models\Admin::select('age')->where('id', $client_id)->first();
                if($clientInfo){
                    $clientAge = $clientInfo->age;
                } else {
                    $clientAge = 'NA';
                }
            } else {
                $clientAge = 'NA';
            }
            $final_arr['clientAge'] = $clientAge;

            //Get Test Type and Score
            $today = Carbon::today();
            $latestTestInfo = DB::table('client_testscore')
                ->where('client_id', $client_id)
                ->whereDate(DB::raw('DATE_ADD(test_date, INTERVAL 3 YEAR)'), '>=', $today)
                ->orderByDesc('id')
                ->select('test_type', 'overall_score')
                ->first();
            // Map to full test name
            $testTypeLabels = [
                'IELTS'   => 'IELTS General',
                'IELTS_A' => 'IELTS Academic',
                'PTE'     => 'PTE Academic',
                'TOEFL'   => 'TOEFL iBT',
                'CAE'     => 'CAE',
                'OET'     => 'OET',
            ];
            if($latestTestInfo){
                $test_type_full = $testTypeLabels[$latestTestInfo->test_type] ?? $latestTestInfo->test_type;
                $overall_score = $latestTestInfo->overall_score ?? $latestTestInfo->overall_score;
            } else {
                $test_type_full = 'NA';
                $overall_score = 'NA';
            }
            $final_arr['test_info'] = $test_type_full.' Score= '.$overall_score;

            // Get latest experience based on job_finish_date
            $latestExperience = DB::table('client_experiences')
            ->where('client_id', $client_id)
            ->orderByDesc('id')
            ->select('job_title', 'job_start_date', 'job_finish_date')
            ->first();

            // Calculate total experience duration in years (with months as fraction)
            $totalYears = null;
            if ($latestExperience) {
                $start = Carbon::parse($latestExperience->job_start_date);
                $end = Carbon::parse($latestExperience->job_finish_date);

                $totalYears = $start->diffInMonths($end) / 12;
                $totalYears = round($totalYears); // Optional: round to 2 decimal places
                $exp_job_title = $latestExperience->job_title;
                $exp_job_total = $totalYears." Years";
            } else {
                $exp_job_title = 'NA';
                $exp_job_total = 'NA';
            }
            $final_arr['exp_info'] = $exp_job_title.' '.$exp_job_total;

            // Get latest qualification
            $latestQualification = DB::table('client_qualifications')
            ->where('client_id', $client_id)
            ->orderByDesc('id')
            ->select('level', 'name')
            ->first();
            if ($latestQualification) {
                $qualification_level = $latestQualification->level;
                $qualification_name = $latestQualification->name;
            } else {
                $qualification_level = 'NA';
                $qualification_name = 'NA';
            }
            $final_arr['qualification_info'] = $qualification_level.' '.$qualification_name;

            $response['final_arr'] 	= $final_arr;
            $response['status'] 	= 	true;
            $response['message']	=	'Record is exist';
        }
        else  {
            $response['final_arr'] 	= array();
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist';
        }
        echo json_encode($response);
    }

    public function CalculatePoints(Request $request)
    {
        $request->validate([
            'pr_details_info' => 'required|string|max:1000',
        ]);

        $input = $request->input('pr_details_info'); //dd( $input);
        $prompt = <<<EOT
            You are an AI assistant tasked with calculating the points score for an applicant applying for the Australian Skilled Independent Visa (Subclass 189) based on the points table from the Australian Department of Home Affairs. Follow the instructions below to collect user inputs, validate them, and compute the total points score. Provide a detailed breakdown of points awarded for each factor and the total score. Ensure all calculations align with the official points table and handle edge cases appropriately.

            **Instructions for Calculating Points**

            **Collect User Inputs:** Prompt the user to provide the following information, ensuring clarity in what is being asked:

            - **Age:** The applicant's age at the time of visa application invitation.
            - **English Language Ability:** The applicant's English test results (e.g., IELTS, PTE, TOEFL iBT, OET, or Cambridge C1 Advanced) or evidence of functional English (e.g., passport from certain countries).
            - **Skilled Employment Experience:**
            - Years of skilled employment in the nominated occupation or closely related occupation outside Australia in the last 10 years.
            - Years of skilled employment in the nominated occupation or closely related occupation in Australia in the last 10 years.
            - Employment must be at least 20 hours per week and paid.
            - **Educational Qualifications:** The highest recognized qualification (e.g., Doctorate, Bachelor’s degree, diploma, or trade qualification).
            - **Australian Study Requirement:** Whether the applicant has completed a degree, diploma, or trade qualification in Australia requiring at least 2 years of full-time study.
            - **Professional Year in Australia:** Whether the applicant completed a professional year program in Australia (in their nominated occupation) for at least 12 months in the last 48 months.
            - **Credentialled Community Language:** Whether the applicant has a recognized community language qualification accredited by NAATI.
            - **Partner Skills:**
            - Whether the partner has a positive skills assessment in a nominated skilled occupation (not for a Subclass 485 visa) and competent English.
            - Alternatively, whether the applicant is single or the partner is an Australian citizen/permanent resident.
            - **Nomination/Sponsorship:** Note that Subclass 189 does not require state/territory nomination or sponsorship, so no points are awarded for this.

            **Points Allocation:** Use the following points table to assign points based on user inputs. Ensure calculations are precise and mutually exclusive where applicable (e.g., only one level of English proficiency applies).

            - **Age:**
            - 18–24 years: 25 points
            - 25–32 years: 30 points
            - 33–39 years: 25 points
            - 40–44 years: 15 points
            - Under 18 or 45 and over: 0 points

            - **English Language Ability:**
            - Superior English (e.g., IELTS 8 or equivalent in each component): 20 points
            - Proficient English (e.g., IELTS 7 or equivalent in each component): 10 points
            - Competent English (e.g., IELTS 6 or equivalent in each component, or passport from UK, USA, Canada, Ireland, or New Zealand): 0 points
            - Functional English or below: 0 points

            - **Skilled Employment Experience (Outside Australia, last 10 years):**
            - 8–10 years: 15 points
            - 5–7 years: 10 points
            - 3–4 years: 5 points
            - Less than 3 years: 0 points

            - **Skilled Employment Experience (In Australia, last 10 years):**
            - 8–10 years: 20 points
            - 5–7 years: 15 points
            - 3–4 years: 10 points
            - 1–2 years: 5 points
            - Less than 1 year: 0 points
            - **Note:** The maximum combined points for overseas and Australian work experience is 20 points. If the sum exceeds 20, cap at 20 points.

            - **Educational Qualifications:**
            - Doctorate from an Australian institution or recognized overseas institution: 20 points
            - Bachelor’s degree (or higher) from an Australian institution or recognized overseas institution: 15 points
            - Diploma or trade qualification completed in Australia: 10 points
            - Qualification recognized by the skills assessing authority for the nominated occupation: 10 points
            - **Note:** Award points for the highest qualification only.

            - **Australian Study Requirement:**
            - Completed a degree, diploma, or trade qualification in Australia requiring at least 2 years of full-time study: 5 points
            - Otherwise: 0 points

            - **Professional Year in Australia:**
            - Completed a professional year program in Australia in the nominated occupation for at least 12 months in the last 48 months: 5 points
            - Otherwise: 0 points

            - **Credentialled Community Language:**
            - Holds a recognized NAATI-accredited community language qualification: 5 points
            - Otherwise: 0 points

            - **Partner Skills:**
            - Partner has a positive skills assessment in a nominated skilled occupation (not Subclass 485) and competent English: 10 points
            - Applicant is single, or partner is an Australian citizen/permanent resident: 10 points
            - Partner has competent English only (no skills assessment): 5 points
            - Otherwise: 0 points

            **Validation and Edge Cases:**

            - **Validate age:** Ensure the applicant is between 18 and 44 years to be eligible for points. If under 18 or 45 and over, assign 0 points and note ineligibility for the visa unless invited.
            - **Validate English proficiency:** Ensure only one level (superior, proficient, competent, or functional) is selected based on test scores or other evidence.
            - **Cap combined work experience points:** If overseas + Australian work experience points exceed 20, assign only 20 points.
            - **Ensure mutual exclusivity for qualifications:** Award points for the highest qualification only, not multiple qualifications.
            - **Handle partner skills:** Confirm whether the partner’s skills assessment and English proficiency are valid and award points accordingly.
            - **Check for missing inputs:** If any required input is missing or invalid, prompt the user to provide it before calculating.

            **Output Format:** Provide the response in a clear, structured format with:

            - A breakdown of points awarded for each factor (e.g., Age: X points, English: Y points, etc.).
            - The total points score.
            - A note indicating the minimum pass mark is 65 points, but higher scores increase invitation chances.
            - If the applicant is ineligible (e.g., age 45 or over), include a warning.

            **User Input:** $input

            Calculate the points based on the provided user input, ensuring all validations are applied, and return the detailed breakdown, total score, and any relevant notes or warnings.
            EOT;

        try
        {
            $apiResponse = $this->openAiClient->post('chat/completions', [
                'json' => [
                    'model' => 'gpt-3.5-turbo', // or 'gpt-3.5-turbo'
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.7,
                ],
            ]);

            $data = json_decode($apiResponse->getBody(), true);
            $result = $data['choices'][0]['message']['content'] ?? 'No response from AI.';

            $responseArr = [
                'result_arr' => ['result' => $result, 'openai_response' => $data],
                'status' => true,
                'message' => 'Record exists',
            ];

            echo json_encode($responseArr);
        } catch (\Exception $e) {
            echo json_encode([
                'result_arr' => [],
                'status' => false,
                'message' => 'Record does not exist',
            ]);
        }
    }

    //pr points add to note
    public function prpoints_add_to_notes(Request $request)
	{
        $requestData = 	$request->all(); //dd($requestData);
        $client_id = $requestData['client_id'];
        $pr_details_info = $requestData['pr_details_info'];
        if($client_id != "" && $pr_details_info != "") {
            $obj = new \App\Models\Note;
            $obj->client_id = $request->client_id;
            $obj->user_id = Auth::user()->id;
            $obj->title = 'PR Point Added to Note';
            $obj->description = $request->pr_details_info;
            $obj->type = 'client';
            $saved = $obj->save();
            if($saved){
                //Added to Activity log
                $subject = 'added a note';
                $objs = new ActivitiesLog;
                $objs->client_id = $request->client_id;
                $objs->created_by = Auth::user()->id;
                $objs->description = '<span class="text-semi-bold">PR Point Added to Note</span><p>'.$request->pr_details_info.'</p>';
                $objs->subject = $subject;
                $objs->save();

                $response['status'] 	= 	true;
                $response['message']	=	'You have successfully added Note';
            } else {
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
            }
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
        }
        echo json_encode($response);
    }

    //Seach Client Relationship
    public function searchPartner(Request $request)
    {
        // Validate the incoming query
        $request->validate([
            'query' => 'required|string|min:3|max:255',
        ]);

        $query = $request->input('query');

        // Search the admins table for matching records
        $partners = Admin::where('role', '=', '7') // Assuming role 7 is for clients
            ->where(function ($q) use ($query) {
                $q->where('email', 'like', '%' . $query . '%')
                    ->orWhere('first_name', 'like', '%' . $query . '%')
                    ->orWhere('last_name', 'like', '%' . $query . '%')
                    ->orWhere('phone', 'like', '%' . $query . '%')
                    ->orWhere('client_id', 'like', '%' . $query . '%');
            })
            ->where('id', '!=', Auth::user()->id) // Exclude the current user
            ->select('id', 'email', 'first_name', 'last_name', 'phone', 'client_id')
            ->limit(10) // Limit results to prevent overload
            ->get();

        // Return JSON response with consistent structure
        return response()->json([
            'partners' => $partners->toArray(),
        ], 200);
    }

    public function loadMatterAiData(Request $request)
    {
        $clientId = $request->client_id;
        $clientUniqueMatterNo = $request->client_unique_matter_no;

        // Fetch client details
        $client = Admin::find($clientId);
        if (!$client) {
            return response()->json(['status' => false, 'message' => 'Client not found']);
        }

        // Fetch personal details
        $personalDetails = [
            'dob' => $client->dob,
            'age' => $client->age,
            'gender' => $client->gender,
            'marital_status' => $client->martial_status,
            'email' => ClientEmail::where('client_id', $clientId)->get(),
            'phone' => ClientContact::where('client_id', $clientId)->get(),
            'address' => ClientAddress::where('client_id', $clientId)->latest()->first(),
            'visa' => ClientVisaCountry::where('client_id', $clientId)->latest()->first(),
            'qualification' => ClientQualification::where('client_id', $clientId)->latest()->first(),
            'experience' => ClientExperience::where('client_id', $clientId)->latest()->first(),
            'test_score' => ClientTestScore::where('client_id', $clientId)->latest()->first(),
        ];

        // Fetch notes
        $notes = ClientNote::where('client_id', $clientId)->get();

        // Fetch edit page details (similar to personal details but including all tabs)
        $editDetails = [
            'personal' => $personalDetails,
            'notes' => $notes,
            // Add other tabs' data as needed
        ];

        // Store data in session or temporary storage for AI processing
        session(['matter_ai_data_' . $clientId => [
            'personal_details' => $personalDetails,
            'notes' => $notes,
            'edit_details' => $editDetails,
        ]]);

        return response()->json(['status' => true, 'message' => 'Data loaded successfully']);
    }

    public function getChatHistory(Request $request)
    {
        $clientId = $request->client_id;
        $chats = AiChat::where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'title' => $chat->title ?? 'Chat on ' . Carbon::parse($chat->created_at)->format('d/m/Y'),
                    'created_at' => Carbon::parse($chat->created_at)->format('d/m/Y H:i'),
                ];
            });

        return response()->json(['status' => true, 'chats' => $chats]);
    }

    public function getChatMessages(Request $request)
    {
        $chatId = $request->chat_id;
        $messages = AiChatMessage::where('chat_id', $chatId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'sender' => $message->sender,
                    'message' => $message->message,
                    'created_at' => Carbon::parse($message->created_at)->format('d/m/Y H:i'),
                ];
            });

        return response()->json(['status' => true, 'messages' => $messages]);
    }

    public function sendAiMessage(Request $request)
    {
        $clientId = $request->client_id;
        $message = $request->message;

        // Fetch stored data
        $aiData = session('matter_ai_data_' . $clientId);
        if (!$aiData) {
            return response()->json(['status' => false, 'message' => 'No data available for AI processing']);
        }

        // Create or find a chat session
        $chat = AiChat::where('client_id', $clientId)
            ->whereDate('created_at', Carbon::today())
            ->first();

        if (!$chat) {
            $chat = AiChat::create([
                'client_id' => $clientId,
                'title' => 'Chat on ' . Carbon::today()->format('d/m/Y'),
            ]);
        }

        // Save user message
        AiChatMessage::create([
            'chat_id' => $chat->id,
            'sender' => 'user',
            'message' => $message,
        ]);

        // Process the message with AI (mock implementation)
        $response = $this->processAiQuery($message, $aiData);

        // Save AI response
        AiChatMessage::create([
            'chat_id' => $chat->id,
            'sender' => 'ai',
            'message' => $response,
        ]);

        return response()->json(['status' => true, 'response' => $response]);
    }

    private function processAiQuery($message, $aiData)
    {
        // This is a mock implementation. In a real scenario, you'd integrate with an AI service like OpenAI, Grok, etc.
        // For now, we'll return a simple response based on the query.

        $message = strtolower($message);

        // Example: Check for keywords and respond accordingly
        if (str_contains($message, 'age')) {
            $age = $aiData['personal_details']['age'] ?? 'N/A';
            return "The client's age is $age.";
        } elseif (str_contains($message, 'visa')) {
            $visa = $aiData['personal_details']['visa'];
            if ($visa) {
                return "The client's visa type is {$visa->visa_type}, expiring on " . Carbon::parse($visa->visa_expiry_date)->format('d/m/Y') . ".";
            }
            return "No visa information available.";
        } elseif (str_contains($message, 'notes')) {
            $notes = $aiData['notes'];
            if ($notes->isNotEmpty()) {
                $latestNote = $notes->first();
                return "The latest note for the client: {$latestNote->note} (Added on " . Carbon::parse($latestNote->created_at)->format('d/m/Y') . ")";
            }
            return "No notes available for this client.";
        } else {
            return "I'm sorry, I couldn't understand your query. Please ask something related to the client's personal details, notes, or edit page data.";
        }
    }

    // OLD HTTP DOWNLOAD METHOD - COMMENTED OUT
    // public function download_document(Request $request)
    // {
    //     $fileUrl = $request->input('filelink');
    //     $filename = $request->input('filename', 'downloaded.pdf');

    //     if (!$fileUrl) {
    //         return abort(400, 'Missing file URL');
    //     }
      
    //     // Increase execution time for large files
    //     set_time_limit(900);

    //     // Increase HTTP client timeout
    //     $response = Http::timeout(120)->get($fileUrl);

    //     if (!$response->successful()) {
    //         return abort(404, 'File not found');
    //     }

    //     return response($response->body())
    //         ->header('Content-Type', 'application/pdf')
    //         ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    // }

    // NEW S3 TEMPORARY URL METHOD
    public function download_document(Request $request)
    {
        $fileUrl = $request->input('filelink');
        $filename = $request->input('filename', 'downloaded.pdf');

        if (!$fileUrl) {
            return abort(400, 'Missing file URL');
        }

        try {
            // Extract S3 key from the URL
            $parsed = parse_url($fileUrl);
            if (!isset($parsed['path'])) {
                return abort(400, 'Invalid S3 URL format');
            }
            
            $s3Key = ltrim(urldecode($parsed['path']), '/');
            
            // Check if file exists in S3
            if (!Storage::disk('s3')->exists($s3Key)) {
                return abort(404, 'File not found in S3');
            }
            
            // Generate temporary URL with proper headers
            $tempUrl = Storage::disk('s3')->temporaryUrl(
                $s3Key,
                now()->addMinutes(5), // 5 minutes expiration
                [
                    'ResponseContentDisposition' => 'attachment; filename="' . $filename . '"',
                    'ResponseContentType' => 'application/pdf'
                ]
            );
            
            // Redirect to S3 temporary URL
            return redirect($tempUrl);
            
        } catch (\Exception $e) {
            \Log::error('S3 download error: ' . $e->getMessage());
            return abort(500, 'Error generating download link');
        }
    }

    //Generate agreemnt
    public function generateagreement(Request $request)
    {
        try { //dd($request->all());
            $id = $request->client_id;
            $client = Admin::findOrFail($request->client_id);
            $responsiblePerson = Admin::findOrFail($request->agent_id); //dd($responsiblePerson);
            if (!$responsiblePerson) {
                return redirect()->back()->with('error', 'No responsible person found in the database.');
            }

            $templatePath = storage_path('app/templates/agreement_template.docx'); //dd($templatePath);

            if (!file_exists($templatePath)) {
                return redirect()->back()->with('error', 'Template file not found at: ' . $templatePath);
            }

            $templateProcessor = new TemplateProcessor($templatePath);

            // Log the values we're trying to set
            Log::info('Generating document for client: ' . $client->client_id);
            Log::info('Template path: ' . $templatePath);

            $dobFormated = 'NA';
            if($client->dob != ''){
                $dobArr = explode('-',$client->dob);
                if(!empty($dobArr)){
                    $dobFormated = $dobArr[2].'/'.$dobArr[1].'/'.$dobArr[0];
                } else{
                    $dobFormated = 'NA';
                }
            }

            // Try to find client address
            $address_record_cnt = DB::table('client_addresses')->where('client_id', $id)->count();
            if( $address_record_cnt > 0 ){
                // If a record with is_current = 1 is found, return its address
                $addressArr = DB::table('client_addresses')->where('client_id', $id)->where('is_current', 1)->first();
                if ($addressArr) {
                    $client_address = $addressArr->address;
                    $client_zip = $addressArr->zip;
                } else {
                    // If no record with is_current = 1 is found, get the latest record by created_at
                    $latestAddressRecord = DB::table('client_addresses')->where('client_id', $id)->orderBy('created_at', 'desc')->first();
                    $client_address = $latestAddressRecord->address;
                    $client_zip = $latestAddressRecord->zip;
                }
            } else {
                $client_address = null;
                $client_zip = null;
            }

            //Get client matter info
            $visa_subclass = '';
            $visa_stream = '';
            $professional_fee = 0;
            $gst_fee = 0;
            $visa_application_charge = 0;

            $Block_1_Description = '';
            $Block_1_Ex_Tax = 0;
            $Block_2_Description = '';
            $Block_2_Ex_Tax = 0;
            $Block_3_Description = '';
            $Block_3_Ex_Tax = 0;

            $Blocktotalfeesincltax = 0;

            $DoHAMainApplicantChargePersonCount = 0;
            $DoHAMainApplicantCharge = 0;
            $DoHAMainApplicantSurcharge = 0;

            $DoHAAdditionalApplicantCharge18PlusPersonCount = 0;
            $DoHAAdditionalApplicantCharge18Plus = 0;
            $DoHAAdditional18PlusSurcharge = 0;

            $DoHAAdditionalApplicantChargeUnder18PersonCount = 0;
            $DoHAAdditionalApplicantChargeUnder18 = 0;
            $DoHAAdditionalUnder18Surcharge = 0;

            $DoHASecondInstalmentMainPersonCount = 0;
            $DoHASecondInstalmentMain = 0;
            $DoHASecondInstalmentMainSurcharge = 0;

            $DoHASubsequentApplicantCharge18PlusPersonCount = 0;
            $DoHASubsequentApplicantCharge18Plus = 0;
            $DoHASubsequentApplicantCharge18PlusSurcharge = 0;

            $DoHASubsequentApplicantChargeUnder18PersonCount = 0;
            $DoHASubsequentTempAppCharge = 0;
            $DoHASubsequentTempAppSurcharge = 0;

            $DoHANonInternetChargePersonCount = 0;
            $DoHANonInternetCharge = 0;
            $DoHANonInternetSurcharge = 0;

            $TotalDoHACharges = 0;
            $TotalDoHASurcharges = 0;
            $TotalEstimatedOtherCosts = 0;
            $GrandTotalFeesAndCosts = 0;

            if( isset($request->client_matter_id) && $request->client_matter_id != '' )
            {  //dd($request->client_matter_id);
                //First check cost is assigned for this matter wrt client or not
                $cost_assignment_cnt = \App\Models\CostAssignmentForm::where('client_id',$request->client_id)->where('client_matter_id',$request->client_matter_id)->count();
	            if($cost_assignment_cnt >0)
                { //dd('iff');
                    // Get cost assignment form fee info
                    $matter_info = DB::table('cost_assignment_forms')->where('client_id', $request->client_id)->where('client_matter_id', $request->client_matter_id)->first();

                    $client_matter_info = DB::table('client_matters')->select('sel_matter_id')->where('id', $request->client_matter_id)->first();
                    // Get matter info
                    if( $client_matter_info ){ //dd($client_matter_info);
                        $matter_info_arr = DB::table('matters')->select('title','nick_name','Block_1_Description','Block_2_Description','Block_3_Description')->where('id', $client_matter_info->sel_matter_id )->first();
                    }
                    $matter_info->title = $matter_info_arr->title;
                    $matter_info->nick_name = $matter_info_arr->nick_name;
                    $matter_info->Block_1_Description = $matter_info_arr->Block_1_Description;
                    $matter_info->Block_2_Description = $matter_info_arr->Block_2_Description;
                    $matter_info->Block_3_Description = $matter_info_arr->Block_3_Description;

                }
                else
                { //dd('elsee');
                    $client_matter_info = DB::table('client_matters')->select('sel_matter_id')->where('id', $request->client_matter_id)->first();
                    // Get matter info
                    if( $client_matter_info ){ //dd($client_matter_info);
                        $matter_info = DB::table('matters')->where('id', $client_matter_info->sel_matter_id )->first();
                    }
                }

                if ($matter_info)
                { //dd($matter_info);

                    $visa_subclass = $matter_info->title;
                    $visa_stream = $matter_info->nick_name;

                    //$professional_fee = $matter_info->our_fee;
                    //$gst_fee = 0;
                    //$visa_application_charge = $matter_info->main_applicant_fee;

                    $Block_1_Description = $matter_info->Block_1_Description;
                    $Block_1_Ex_Tax = $matter_info->Block_1_Ex_Tax;

                    $Block_2_Description = $matter_info->Block_2_Description;
                    $Block_2_Ex_Tax = $matter_info->Block_2_Ex_Tax;

                    $Block_3_Description = $matter_info->Block_3_Description;
                    $Block_3_Ex_Tax = $matter_info->Block_3_Ex_Tax;

                    $Blocktotalfeesincltax = floatval($Block_1_Ex_Tax) + floatval($Block_2_Ex_Tax) + floatval($Block_3_Ex_Tax);
                    $BlocktotalfeesincltaxFormated = number_format($Blocktotalfeesincltax, 2, '.', '');
                    //dd($BlocktotalfeesincltaxFormated);

                    $DoHAMainApplicantChargePersonCount = $matter_info->Dept_Base_Application_Charge_no_of_person ."Person" ;
                    $DoHAMainApplicantCharge = $matter_info->Dept_Base_Application_Charge_after_person;
                    $DoHAMainApplicantSurcharge = $matter_info->Dept_Base_Application_Charge_after_person_surcharge;

                    $DoHAAdditionalApplicantCharge18PlusPersonCount = $matter_info->Dept_Additional_Applicant_Charge_18_Plus_no_of_person ."Person" ;
                    $DoHAAdditionalApplicantCharge18Plus = $matter_info->Dept_Additional_Applicant_Charge_18_Plus_after_person;
                    $DoHAAdditional18PlusSurcharge = $matter_info->Dept_Additional_Applicant_Charge_18_Plus_after_person_surcharge;

                    $DoHAAdditionalApplicantChargeUnder18PersonCount = $matter_info->Dept_Additional_Applicant_Charge_Under_18_no_of_person ."Person" ;
                    $DoHAAdditionalApplicantChargeUnder18 = $matter_info->Dept_Additional_Applicant_Charge_Under_18_after_person;
                    $DoHAAdditionalUnder18Surcharge = $matter_info->Dept_Additional_Applicant_Charge_Under_18_after_person_surcharge;

                    $DoHASecondInstalmentMainPersonCount = $matter_info->Dept_Subsequent_Temp_Application_Charge_no_of_person ."Person" ;
                    $DoHASecondInstalmentMain = $matter_info->Dept_Subsequent_Temp_Application_Charge_after_person;
                    $DoHASecondInstalmentMainSurcharge = $matter_info->Dept_Subsequent_Temp_Application_Charge_after_person_surcharge;

                    $DoHASubsequentApplicantCharge18PlusPersonCount = $matter_info->Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person ."Person" ;
                    $DoHASubsequentApplicantCharge18Plus = $matter_info->Dept_Second_VAC_Instalment_Charge_18_Plus_after_person;
                    $DoHASubsequentApplicantCharge18PlusSurcharge = $matter_info->Dept_Second_VAC_Instalment_Charge_18_Plus_after_person_surcharge;

                    $DoHASubsequentApplicantChargeUnder18PersonCount = $matter_info->Dept_Second_VAC_Instalment_Under_18_no_of_person ."Person" ;
                    $DoHASubsequentTempAppCharge = $matter_info->Dept_Second_VAC_Instalment_Under_18_after_person;
                    $DoHASubsequentTempAppSurcharge = $matter_info->Dept_Second_VAC_Instalment_Under_18_after_person_surcharge;

                    $DoHANonInternetChargePersonCount = $matter_info->Dept_Non_Internet_Application_Charge_no_of_person ."Person" ;
                    $DoHANonInternetCharge = $matter_info->Dept_Non_Internet_Application_Charge_after_person;
                    $DoHANonInternetSurcharge = $matter_info->Dept_Non_Internet_Application_Charge_after_person_surcharge;

                    $TotalDoHACharges = $matter_info->TotalDoHACharges;
                    $TotalDoHASurcharges = $matter_info->TotalDoHASurcharges;

                    $TotalEstimatedOtherCosts = $matter_info->additional_fee_1;
                    $GrandTotalFeesAndCosts = floatval($Blocktotalfeesincltax) + floatval($TotalDoHASurcharges) + floatval($TotalEstimatedOtherCosts);
                    $GrandTotalFeesAndCostsFormated = number_format($GrandTotalFeesAndCosts, 2, '.', '');
                }
            }

            // Replace placeholders
            $replacements = [
                'ClientID' => $client->client_id,
                'ApplicantGivenNames' => $client->first_name,
                'ApplicantSurname' => $client->last_name,
                'ApplicantDOB' => $dobFormated,
                'ApplicantResidentialAddressStreet1and2' => $client_address,
                'ApplicantResidentialAddressPostcode' => $client_zip,
                //'ApplicantResidentialAddressSuburbAndTown' => '',
                //'ApplicantResidentialAddressState' => '',
                //'ApplicantResidentialAddressCountry' => '',
                'Contact_ContactEmail' => $client->email,
                'Contact_ContactMobile' => $client->phone ?? '',
                'ApplicantHomePhone_Number' => $client->phone ?? '',

                'VisaApplyingFor' => $visa_subclass,
                'VisaApplyingForStream' => $visa_stream,

                'Block1IncTax' => number_format($professional_fee, 2),
                'TotalAgentFeeGST' => number_format($gst_fee ?? 0, 2),
                'TotalAgentFeeIncTax' => number_format($professional_fee + ($gst_fee ?? 0), 2),
                'BaseApplicationCharge' => number_format($visa_application_charge, 2),
                'DIBPBaseApplicationChargeIncCCSurcharge' => number_format($visa_application_charge, 2),

                'AgentName' => $responsiblePerson->first_name,
                'AgentSurName' => $responsiblePerson->last_name,
                'AgentTitle' => $responsiblePerson->company_name,
                'MARN' => $responsiblePerson->marn_number,

                'visa_apply'=>$visa_subclass,

                'Block1description'=>$Block_1_Description,
                'Block1feesincltax'=>$Block_1_Ex_Tax,
                'Block2description'=>$Block_2_Description,
                'Block2feesincltax'=>$Block_2_Ex_Tax,
                'Block3description'=>$Block_3_Description,
                'Block3feesincltax'=>$Block_3_Ex_Tax,
                'Blocktotalfeesincltax'=>$BlocktotalfeesincltaxFormated,

                'DoHAMainApplicantChargePersonCount'=>$DoHAMainApplicantChargePersonCount,
                'DoHAMainApplicantCharge'=>$DoHAMainApplicantCharge,
                'DoHAMainApplicantSurcharge'=>$DoHAMainApplicantSurcharge,

                'DoHAAdditionalApplicantCharge18PlusPersonCount'=>$DoHAAdditionalApplicantCharge18PlusPersonCount,
                'DoHAAdditionalApplicantCharge18Plus'=>$DoHAAdditionalApplicantCharge18Plus,
                'DoHAAdditional18PlusSurcharge'=>$DoHAAdditional18PlusSurcharge,

                'DoHAAdditionalApplicantChargeUnder18PersonCount'=>$DoHAAdditionalApplicantChargeUnder18PersonCount,
                'DoHAAdditionalApplicantChargeUnder18'=>$DoHAAdditionalApplicantChargeUnder18,
                'DoHAAdditionalUnder18Surcharge'=>$DoHAAdditionalUnder18Surcharge,

                'DoHASecondInstalmentMainPersonCount'=>$DoHASecondInstalmentMainPersonCount,
                'DoHASecondInstalmentMain'=>$DoHASecondInstalmentMain,
                'DoHASecondInstalmentMainSurcharge'=>$DoHASecondInstalmentMainSurcharge,

                'DoHASubsequentApplicantCharge18PlusPersonCount'=>$DoHASubsequentApplicantCharge18PlusPersonCount,
                'DoHASubsequentApplicantCharge18Plus'=>$DoHASubsequentApplicantCharge18Plus,
                'DoHASubsequentApplicantCharge18PlusSurcharge'=>$DoHASubsequentApplicantCharge18PlusSurcharge,

                'DoHASubsequentApplicantChargeUnder18PersonCount'=>$DoHASubsequentApplicantChargeUnder18PersonCount,
                'DoHASubsequentTempAppCharge'=>$DoHASubsequentTempAppCharge,
                'DoHASubsequentTempAppSurcharge'=>$DoHASubsequentTempAppSurcharge,

                'DoHANonInternetChargePersonCount'=>$DoHANonInternetChargePersonCount,
                'DoHANonInternetCharge'=>$DoHANonInternetCharge,
                'DoHANonInternetSurcharge'=>$DoHANonInternetSurcharge,

                'TotalDoHACharges'=>$TotalDoHACharges,
                'TotalDoHASurcharges'=>$TotalDoHASurcharges,

                'TotalEstimatedOthCosts'=>$TotalEstimatedOtherCosts,
                'GrandTotalFeesAndCosts'=>$GrandTotalFeesAndCostsFormated
            ];

            // Log each replacement
            foreach ($replacements as $key => $value) {
                Log::info("Setting {$key} to: {$value}");
                $templateProcessor->setValue($key, $value);
            }

            // Create the output directory if it doesn't exist - use public directory for web access
            $outputDir = storage_path('app/public/agreements');
            //  $outputDir = public_path('agreements');
            if (!file_exists($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            $outputPath = $outputDir . '/agreement_' . $client->client_id . '.docx'; //dd($outputPath);
            $templateProcessor->saveAs($outputPath);

            Log::info('Document generated successfully at: ' . $outputPath);

            // Upload to S3 and get download URL
            $fileName = 'agreement_' . $client->client_id . '_' . time() . '.docx';
            $s3Path = $client->client_id . '/cost_assignment_form/' . $fileName;
            
            // Upload to S3
            Storage::disk('s3')->put($s3Path, file_get_contents($outputPath));
            
            // Get the S3 URL
            $downloadUrl = Storage::disk('s3')->url($s3Path);
            
            // Clean up local file
            if (file_exists($outputPath)) {
                unlink($outputPath);
            }
            
            // Return the download URL as JSON
            return response()->json([
                'success' => true,
                'download_url' => $downloadUrl,
                'filename' => $fileName,
                'message' => 'Document generated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating document: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            //return redirect()->back()->with('error', 'Error generating document: ' . $e->getMessage());
             return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    //Get Migration Agent Detail
    public function getMigrationAgentDetail(Request $request)
    {
        $requestData = 	$request->all();
        $client_matter_id = $requestData['client_matter_id'];
        $clientMatterInfo = DB::table('client_matters')->select('sel_migration_agent','sel_matter_id')->where('id',$client_matter_id)->first();
        //dd($clientMatterInfo);
        if($clientMatterInfo) {
            //get matter name
            $matterInfo = DB::table('matters')->select('title','nick_name')->where('id',$clientMatterInfo->sel_matter_id)->first();
            //dd($matterInfo);
            if($matterInfo){
                $response['matterInfo'] = $matterInfo;
            } else {
                $response['matterInfo'] = "";
            }

            $sel_migration_agent = $clientMatterInfo->sel_migration_agent;
            $agentInfo = DB::table('admins')->select('id as agentId','first_name','last_name','company_name')->where('id',$sel_migration_agent)->first();
            //dd($agentInfo);
            if($agentInfo){
                $response['agentInfo'] 	= $agentInfo;
                $response['status'] 	= 	true;
                $response['message']	=	'Record is exist';
            } else {
                $response['agentInfo'] 	= "";
                $response['status'] 	= 	false;
                $response['message']	=	'Record is not exist.Please try again';
            }
        }
        echo json_encode($response);
    }

    //Get Visa agreemnt Migration Agent Detail
    public function getVisaAggreementMigrationAgentDetail(Request $request)
    {
        $requestData = 	$request->all();
        $client_matter_id = $requestData['client_matter_id'];
        $clientMatterInfo = DB::table('client_matters')->select('sel_migration_agent','sel_matter_id')->where('id',$client_matter_id)->first();
        //dd($clientMatterInfo);
        if($clientMatterInfo) {
            //get matter name
            $matterInfo = DB::table('matters')->select('title','nick_name')->where('id',$clientMatterInfo->sel_matter_id)->first();
            //dd($matterInfo);
            if($matterInfo){
                $response['matterInfo'] = $matterInfo;
            } else {
                $response['matterInfo'] = "";
            }

            $sel_migration_agent = $clientMatterInfo->sel_migration_agent;
            $agentInfo = DB::table('admins')->select('id as agentId','first_name','last_name','company_name')->where('id',$sel_migration_agent)->first();
            //dd($agentInfo);
            if($agentInfo){
                $response['agentInfo'] 	= $agentInfo;
                $response['status'] 	= 	true;
                $response['message']	=	'Record is exist';
            } else {
                $response['agentInfo'] 	= "";
                $response['status'] 	= 	false;
                $response['message']	=	'Record is not exist.Please try again';
            }
        }
        echo json_encode($response);
    }

    //Get Cost assignment Migration Agent Detail
    public function getCostAssignmentMigrationAgentDetail(Request $request)
    {
        $requestData = 	$request->all(); //dd($requestData);
        $client_matter_id = $requestData['client_matter_id'];
        $clientMatterInfo = DB::table('client_matters')->select('sel_migration_agent','sel_matter_id')->where('id',$client_matter_id)->first();
        //dd($clientMatterInfo);
        if($clientMatterInfo) {
            //get matter name
            $matterInfo = DB::table('matters')->where('id',$clientMatterInfo->sel_matter_id)->first();
            //dd($matterInfo);
            if($matterInfo){
                $response['matterInfo'] = $matterInfo;
            } else {
                $response['matterInfo'] = "";
            }

            //get cost assignment matter fee
            $costassignmentmatterInfo = DB::table('cost_assignment_forms')->where('client_id',$requestData['client_id'])->where('client_matter_id',$requestData['client_matter_id'])->first();
            //dd($costassignmentmatterInfo);
            if($matterInfo){
                $response['cost_assignment_matterInfo'] = $costassignmentmatterInfo;
            } else {
                $response['cost_assignment_matterInfo'] = "";
            }

            $sel_migration_agent = $clientMatterInfo->sel_migration_agent;
            $agentInfo = DB::table('admins')->select('id as agentId','first_name','last_name','company_name')->where('id',$sel_migration_agent)->first();
            //dd($agentInfo);
            if($agentInfo){
                $response['agentInfo'] 	= $agentInfo;
                $response['status'] 	= 	true;
                $response['message']	=	'Record is exist';
            } else {
                $response['agentInfo'] 	= "";
                $response['status'] 	= 	false;
                $response['message']	=	'Record is not exist.Please try again';
            }
        }
        echo json_encode($response);
    }

    //Store Cost Assignment Form Values
    public function savecostassignment(Request $request)
    {   //dd( $request->all());
        if ($request->isMethod('post'))
        {
            $requestData = $request->all(); //dd($requestData);

            if( isset($requestData['surcharge']) && $requestData['surcharge'] != '') {
                $surcharge = $requestData['surcharge'];
            } else {
                $surcharge = 'Yes';
            }

            $Dept_Base_Application_Charge = floatval($requestData['Dept_Base_Application_Charge'] ?? 0); //dd($Dept_Base_Application_Charge);
            $Dept_Base_Application_Charge_no_of_person = intval($requestData['Dept_Base_Application_Charge_no_of_person'] ?? 1); //dd($Dept_Base_Application_Charge_no_of_person);
            $Dept_Base_Application_Charge_after_person = $Dept_Base_Application_Charge * $Dept_Base_Application_Charge_no_of_person;
            $Dept_Base_Application_Charge_after_person = floatval($Dept_Base_Application_Charge_after_person); //dd($Dept_Base_Application_Charge_after_person);

            if( $surcharge == 'Yes'){
                // Step 2: Calculate 1.4% surcharge
                $Dept_Base_Application_Surcharge = round($Dept_Base_Application_Charge_after_person * 0.014, 2);
            } else {
                $Dept_Base_Application_Surcharge = 0;
            }
            
            // Step 3: Final total after surcharge
            $Dept_Base_Application_Charge_after_person_surcharge = $Dept_Base_Application_Charge_after_person + $Dept_Base_Application_Surcharge; //dd($Dept_Additional_Applicant_Charge_18_Plus_after_person_surcharge);

            $Dept_Non_Internet_Application_Charge = floatval($requestData['Dept_Non_Internet_Application_Charge'] ?? 0); //dd($Dept_Non_Internet_Application_Charge);
            $Dept_Non_Internet_Application_Charge_no_of_person = intval($requestData['Dept_Non_Internet_Application_Charge_no_of_person'] ?? 1); //dd($Dept_Non_Internet_Application_Charge_no_of_person);
            $Dept_Non_Internet_Application_Charge_after_person = $Dept_Non_Internet_Application_Charge * $Dept_Non_Internet_Application_Charge_no_of_person;
            $Dept_Non_Internet_Application_Charge_after_person = floatval($Dept_Non_Internet_Application_Charge_after_person); //dd($Dept_Non_Internet_Application_Charge_after_person);

            if( $surcharge == 'Yes'){
                // Step 2: Calculate 1.4% surcharge
                $Dept_Non_Internet_Application_Surcharge = round($Dept_Non_Internet_Application_Charge_after_person * 0.014, 2);
            } else {
                $Dept_Non_Internet_Application_Surcharge = 0;
            }
            // Step 3: Final total after surcharge
            $Dept_Non_Internet_Application_Charge_after_person_surcharge = $Dept_Non_Internet_Application_Surcharge + $Dept_Non_Internet_Application_Charge_after_person; //dd($Dept_Additional_Applicant_Charge_18_Plus_after_person_surcharge);

            $Dept_Additional_Applicant_Charge_18_Plus = floatval($requestData['Dept_Additional_Applicant_Charge_18_Plus'] ?? 0);
            $Dept_Additional_Applicant_Charge_18_Plus_no_of_person = intval($requestData['Dept_Additional_Applicant_Charge_18_Plus_no_of_person'] ?? 1);
            $Dept_Additional_Applicant_Charge_18_Plus_after_person = $Dept_Additional_Applicant_Charge_18_Plus * $Dept_Additional_Applicant_Charge_18_Plus_no_of_person;
            $Dept_Additional_Applicant_Charge_18_Plus_after_person = floatval($Dept_Additional_Applicant_Charge_18_Plus_after_person);

            if( $surcharge == 'Yes'){
                // Step 2: Calculate 1.4% surcharge
                $Dept_Additional_Applicant_Charge_18_Surcharge = round($Dept_Additional_Applicant_Charge_18_Plus_after_person * 0.014, 2);
            } else {
                $Dept_Additional_Applicant_Charge_18_Surcharge = 0;
            }
            // Step 3: Final total after surcharge
            $Dept_Additional_Applicant_Charge_18_Plus_after_person_surcharge = $Dept_Additional_Applicant_Charge_18_Surcharge + $Dept_Additional_Applicant_Charge_18_Plus_after_person;

            $Dept_Additional_Applicant_Charge_Under_18 = floatval($requestData['Dept_Additional_Applicant_Charge_Under_18'] ?? 0);
            $Dept_Additional_Applicant_Charge_Under_18_no_of_person = intval($requestData['Dept_Additional_Applicant_Charge_Under_18_no_of_person'] ?? 1);
            $Dept_Additional_Applicant_Charge_Under_18_after_person = $Dept_Additional_Applicant_Charge_Under_18 * $Dept_Additional_Applicant_Charge_Under_18_no_of_person;
            $Dept_Additional_Applicant_Charge_Under_18_after_person = floatval($Dept_Additional_Applicant_Charge_Under_18_after_person);

            if( $surcharge == 'Yes'){
                // Step 2: Calculate 1.4% surcharge
                $Dept_Additional_Applicant_Charge_Under_18_Surcharge = round($Dept_Additional_Applicant_Charge_Under_18_after_person * 0.014, 2);
            } else {
                $Dept_Additional_Applicant_Charge_Under_18_Surcharge = 0;
            }
            // Step 3: Final total after surcharge
            $Dept_Additional_Applicant_Charge_Under_18_after_person_surcharge = $Dept_Additional_Applicant_Charge_Under_18_Surcharge + $Dept_Additional_Applicant_Charge_Under_18_after_person;

            $Dept_Subsequent_Temp_Application_Charge = floatval($requestData['Dept_Subsequent_Temp_Application_Charge'] ?? 0);
            $Dept_Subsequent_Temp_Application_Charge_no_of_person = intval($requestData['Dept_Subsequent_Temp_Application_Charge_no_of_person'] ?? 1);
            $Dept_Subsequent_Temp_Application_Charge_after_person = $Dept_Subsequent_Temp_Application_Charge * $Dept_Subsequent_Temp_Application_Charge_no_of_person;
            $Dept_Subsequent_Temp_Application_Charge_after_person = floatval($Dept_Subsequent_Temp_Application_Charge_after_person);

            if( $surcharge == 'Yes'){
                // Step 2: Calculate 1.4% surcharge
                $Dept_Subsequent_Temp_Application_Surcharge = round($Dept_Subsequent_Temp_Application_Charge_after_person * 0.014, 2);
            } else {
                $Dept_Subsequent_Temp_Application_Surcharge = 0;
            }
            // Step 3: Final total after surcharge
            $Dept_Subsequent_Temp_Application_Charge_after_person_surcharge = $Dept_Subsequent_Temp_Application_Surcharge + $Dept_Subsequent_Temp_Application_Charge_after_person;

            $Dept_Second_VAC_Instalment_Charge_18_Plus = floatval($requestData['Dept_Second_VAC_Instalment_Charge_18_Plus'] ?? 0);
            $Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person = intval($requestData['Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person'] ?? 1);
            $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person = $Dept_Second_VAC_Instalment_Charge_18_Plus * $Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person;
            $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person = floatval($Dept_Second_VAC_Instalment_Charge_18_Plus_after_person);

            if( $surcharge == 'Yes'){
                // Step 2: Calculate 1.4% surcharge
                $Dept_Second_VAC_Instalment_18_Plus_Surcharge = round($Dept_Second_VAC_Instalment_Charge_18_Plus_after_person * 0.014, 2);
            } else {
                $Dept_Second_VAC_Instalment_18_Plus_Surcharge = 0;
            }
            // Step 3: Final total after surcharge
            $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person_surcharge = $Dept_Second_VAC_Instalment_18_Plus_Surcharge + $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person;

            $Dept_Second_VAC_Instalment_Under_18 = floatval($requestData['Dept_Second_VAC_Instalment_Under_18'] ?? 0);
            $Dept_Second_VAC_Instalment_Under_18_no_of_person = intval($requestData['Dept_Second_VAC_Instalment_Under_18_no_of_person'] ?? 1);
            $Dept_Second_VAC_Instalment_Under_18_after_person = $Dept_Second_VAC_Instalment_Under_18 * $Dept_Second_VAC_Instalment_Under_18_no_of_person;
            $Dept_Second_VAC_Instalment_Under_18_after_person = floatval($Dept_Second_VAC_Instalment_Under_18_after_person);

            if( $surcharge == 'Yes'){
                // Step 2: Calculate 1.4% surcharge
                $Dept_Second_VAC_Instalment_Under_18_Surcharge = round($Dept_Second_VAC_Instalment_Under_18_after_person * 0.014, 2);
            } else {
                $Dept_Second_VAC_Instalment_Under_18_Surcharge = 0;
            }
            // Step 3: Final total after surcharge
            $Dept_Second_VAC_Instalment_Under_18_after_person_surcharge = $Dept_Second_VAC_Instalment_Under_18_Surcharge + $Dept_Second_VAC_Instalment_Under_18_after_person;

            $TotalDoHACharges = $Dept_Base_Application_Charge_after_person
                                + $Dept_Additional_Applicant_Charge_18_Plus_after_person
                                + $Dept_Additional_Applicant_Charge_Under_18_after_person
                                + $Dept_Subsequent_Temp_Application_Charge_after_person
                                + $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person
                                + $Dept_Second_VAC_Instalment_Under_18_after_person
                                + $Dept_Non_Internet_Application_Charge_after_person;

            $TotalDoHASurcharges = $Dept_Base_Application_Charge_after_person_surcharge
                                    + $Dept_Additional_Applicant_Charge_18_Plus_after_person_surcharge
                                    + $Dept_Additional_Applicant_Charge_Under_18_after_person_surcharge
                                    + $Dept_Subsequent_Temp_Application_Charge_after_person_surcharge
                                    + $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person_surcharge
                                    + $Dept_Second_VAC_Instalment_Under_18_after_person_surcharge
                                    + $Dept_Non_Internet_Application_Charge_after_person_surcharge;

            $TotalBLOCKFEE = $requestData['Block_1_Ex_Tax'] + $requestData['Block_2_Ex_Tax'] +  $requestData['Block_3_Ex_Tax'];

            $cost_assignment_cnt = \App\Models\CostAssignmentForm::where('client_id',$requestData['client_id'])->where('client_matter_id',$requestData['client_matter_id'])->count();
            //dd($surcharge);
            if($cost_assignment_cnt >0){
                //update
                $costAssignment = \App\Models\CostAssignmentForm::where('client_id', $requestData['client_id'])
                ->where('client_matter_id', $requestData['client_matter_id'])
                ->first();
                if ($costAssignment) {
                    $saved = $costAssignment->update([
                        'agent_id' => $requestData['agent_id'],
                        'surcharge' => $surcharge,
                        
                        'Dept_Base_Application_Charge' => $requestData['Dept_Base_Application_Charge'],
                        'Dept_Base_Application_Charge_no_of_person' => $requestData['Dept_Base_Application_Charge_no_of_person'],
                        'Dept_Base_Application_Charge_after_person' => $Dept_Base_Application_Charge_after_person,
                        'Dept_Base_Application_Charge_after_person_surcharge' => $Dept_Base_Application_Charge_after_person_surcharge,

                        'Dept_Non_Internet_Application_Charge' => $requestData['Dept_Non_Internet_Application_Charge'],
                        'Dept_Non_Internet_Application_Charge_no_of_person' => $requestData['Dept_Non_Internet_Application_Charge_no_of_person'],
                        'Dept_Non_Internet_Application_Charge_after_person' => $Dept_Non_Internet_Application_Charge_after_person,
                        'Dept_Non_Internet_Application_Charge_after_person_surcharge' => $Dept_Non_Internet_Application_Charge_after_person_surcharge,

                        'Dept_Additional_Applicant_Charge_18_Plus' => $requestData['Dept_Additional_Applicant_Charge_18_Plus'],
                        'Dept_Additional_Applicant_Charge_18_Plus_no_of_person' => $requestData['Dept_Additional_Applicant_Charge_18_Plus_no_of_person'],
                        'Dept_Additional_Applicant_Charge_18_Plus_after_person' => $Dept_Additional_Applicant_Charge_18_Plus_after_person,
                        'Dept_Additional_Applicant_Charge_18_Plus_after_person_surcharge' => $Dept_Additional_Applicant_Charge_18_Plus_after_person_surcharge,

                        'Dept_Additional_Applicant_Charge_Under_18' => $requestData['Dept_Additional_Applicant_Charge_Under_18'],
                        'Dept_Additional_Applicant_Charge_Under_18_no_of_person' => $requestData['Dept_Additional_Applicant_Charge_Under_18_no_of_person'],
                        'Dept_Additional_Applicant_Charge_Under_18_after_person' => $Dept_Additional_Applicant_Charge_Under_18_after_person,
                        'Dept_Additional_Applicant_Charge_Under_18_after_person_surcharge' => $Dept_Additional_Applicant_Charge_Under_18_after_person_surcharge,

                        'Dept_Subsequent_Temp_Application_Charge' => $requestData['Dept_Subsequent_Temp_Application_Charge'],
                        'Dept_Subsequent_Temp_Application_Charge_no_of_person' => $requestData['Dept_Subsequent_Temp_Application_Charge_no_of_person'],
                        'Dept_Subsequent_Temp_Application_Charge_after_person' => $Dept_Subsequent_Temp_Application_Charge_after_person,
                        'Dept_Subsequent_Temp_Application_Charge_after_person_surcharge' => $Dept_Subsequent_Temp_Application_Charge_after_person_surcharge,

                        'Dept_Second_VAC_Instalment_Charge_18_Plus' => $requestData['Dept_Second_VAC_Instalment_Charge_18_Plus'],
                        'Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person' => $requestData['Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person'],
                        'Dept_Second_VAC_Instalment_Charge_18_Plus_after_person' => $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person,
                        'Dept_Second_VAC_Instalment_Charge_18_Plus_after_person_surcharge' => $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person_surcharge,

                        'Dept_Second_VAC_Instalment_Under_18' => $requestData['Dept_Second_VAC_Instalment_Under_18'],
                        'Dept_Second_VAC_Instalment_Under_18_no_of_person' => $requestData['Dept_Second_VAC_Instalment_Under_18_no_of_person'],
                        'Dept_Second_VAC_Instalment_Under_18_after_person' => $Dept_Second_VAC_Instalment_Under_18_after_person,
                        'Dept_Second_VAC_Instalment_Under_18_after_person_surcharge' => $Dept_Second_VAC_Instalment_Under_18_after_person_surcharge,

                        'Dept_Nomination_Application_Charge' => $requestData['Dept_Nomination_Application_Charge'],
                        'Dept_Sponsorship_Application_Charge' => $requestData['Dept_Sponsorship_Application_Charge'],
                        'Block_1_Ex_Tax' => $requestData['Block_1_Ex_Tax'],
                        'Block_2_Ex_Tax' => $requestData['Block_2_Ex_Tax'],
                        'Block_3_Ex_Tax' => $requestData['Block_3_Ex_Tax'],
                        'additional_fee_1' => $requestData['additional_fee_1'],
                        'TotalDoHACharges' => $TotalDoHACharges,
                        'TotalDoHASurcharges' => $TotalDoHASurcharges,
                        'TotalBLOCKFEE' => $TotalBLOCKFEE
                    ]);
                }
            }
            else
            {
                //insert
                $obj = new CostAssignmentForm;

                $obj->client_id = $requestData['client_id'];
                $obj->client_matter_id = $requestData['client_matter_id'];
                $obj->agent_id = $requestData['agent_id'];
                $obj->surcharge = $surcharge;
                
                $obj->Dept_Base_Application_Charge = $requestData['Dept_Base_Application_Charge'];
                $obj->Dept_Base_Application_Charge_no_of_person = $requestData['Dept_Base_Application_Charge_no_of_person'];
                $obj->Dept_Base_Application_Charge_after_person = $Dept_Base_Application_Charge_after_person;
                $obj->Dept_Base_Application_Charge_after_person_surcharge = $Dept_Base_Application_Charge_after_person_surcharge;

                $obj->Dept_Non_Internet_Application_Charge = $requestData['Dept_Non_Internet_Application_Charge'];
                 $obj->Dept_Non_Internet_Application_Charge_no_of_person = $requestData['Dept_Non_Internet_Application_Charge_no_of_person'];
                $obj->Dept_Non_Internet_Application_Charge_after_person = $Dept_Non_Internet_Application_Charge_after_person;
                $obj->Dept_Non_Internet_Application_Charge_after_person_surcharge = $Dept_Non_Internet_Application_Charge_after_person_surcharge;

                $obj->Dept_Additional_Applicant_Charge_18_Plus = $requestData['Dept_Additional_Applicant_Charge_18_Plus'];
                $obj->Dept_Additional_Applicant_Charge_18_Plus_no_of_person = $requestData['Dept_Additional_Applicant_Charge_18_Plus_no_of_person'];
                $obj->Dept_Additional_Applicant_Charge_18_Plus_after_person = $Dept_Additional_Applicant_Charge_18_Plus_after_person;
                $obj->Dept_Additional_Applicant_Charge_18_Plus_after_person_surcharge = $Dept_Additional_Applicant_Charge_18_Plus_after_person_surcharge;

                $obj->Dept_Additional_Applicant_Charge_Under_18 = $requestData['Dept_Additional_Applicant_Charge_Under_18'];
                $obj->Dept_Additional_Applicant_Charge_Under_18_no_of_person = $requestData['Dept_Additional_Applicant_Charge_Under_18_no_of_person'];
                $obj->Dept_Additional_Applicant_Charge_Under_18_after_person = $Dept_Additional_Applicant_Charge_Under_18_after_person;
                $obj->Dept_Additional_Applicant_Charge_Under_18_after_person_surcharge = $Dept_Additional_Applicant_Charge_Under_18_after_person_surcharge;

                $obj->Dept_Subsequent_Temp_Application_Charge = $requestData['Dept_Subsequent_Temp_Application_Charge'];
                $obj->Dept_Subsequent_Temp_Application_Charge_no_of_person = $requestData['Dept_Subsequent_Temp_Application_Charge_no_of_person'];
                $obj->Dept_Subsequent_Temp_Application_Charge_after_person = $Dept_Subsequent_Temp_Application_Charge_after_person;
                $obj->Dept_Subsequent_Temp_Application_Charge_after_person_surcharge = $Dept_Subsequent_Temp_Application_Charge_after_person_surcharge;

                $obj->Dept_Second_VAC_Instalment_Charge_18_Plus = $requestData['Dept_Second_VAC_Instalment_Charge_18_Plus'];
                $obj->Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person = $requestData['Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person'];
                $obj->Dept_Second_VAC_Instalment_Charge_18_Plus_after_person = $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person;
                $obj->Dept_Second_VAC_Instalment_Charge_18_Plus_after_person_surcharge = $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person_surcharge;

                $obj->Dept_Second_VAC_Instalment_Under_18 = $requestData['Dept_Second_VAC_Instalment_Under_18'];
                $obj->Dept_Second_VAC_Instalment_Under_18_no_of_person = $requestData['Dept_Second_VAC_Instalment_Under_18_no_of_person'];
                $obj->Dept_Second_VAC_Instalment_Under_18_after_person = $Dept_Second_VAC_Instalment_Under_18_after_person;
                $obj->Dept_Second_VAC_Instalment_Under_18_after_person_surcharge = $Dept_Second_VAC_Instalment_Under_18_after_person_surcharge;

                $obj->Dept_Nomination_Application_Charge = $requestData['Dept_Nomination_Application_Charge'];
                $obj->Dept_Sponsorship_Application_Charge = $requestData['Dept_Sponsorship_Application_Charge'];

                $obj->Block_1_Ex_Tax = $requestData['Block_1_Ex_Tax'];
                $obj->Block_2_Ex_Tax = $requestData['Block_2_Ex_Tax'];
                $obj->Block_3_Ex_Tax = $requestData['Block_3_Ex_Tax'];
                $obj->additional_fee_1 = $requestData['additional_fee_1'];
                $obj->TotalDoHACharges = $TotalDoHACharges;
                $obj->TotalDoHASurcharges = $TotalDoHASurcharges;
                $obj->TotalBLOCKFEE = $TotalBLOCKFEE;
                $saved = $obj->save();
            }
            if (!$saved) {
                $response['status'] 	= 	false;
                $response['message']	=	'Cost assignment not added successfully.Please try again';
            } else {
                $response['status'] 	= 	true;
                $response['message']	=	'Cost assignment added successfully';
            }
        }
        echo json_encode($response);
    }

    //save reference
    public function savereferences(Request $request)
    {  //dd($request->all());
        $validated = $request->validate([
            'department_reference' => 'nullable|string|max:255',
            'other_reference' => 'nullable|string|max:255',
        ]);

        // Step 2: Check if the record exists
        $matter = \App\Models\ClientMatter::where('client_id', $request->client_id)
        ->where('id', $request->client_matter_id)
        ->first();

        if (!$matter) {
            return response()->json([
                'status' => 'error',
                'message' => 'Record not found for given client_id and client_matter_id.'
            ], 404);
        }

        // Step 3: Perform the update
        $matter->department_reference = $request->department_reference;
        $matter->other_reference = $request->other_reference;
        $matter->save();

        return response()->json([
            'status' => 'success',
            'message' => 'References updated successfully.'
        ]);
    }

    //Check star client
    public function checkStarClient(Request $request)
    {
        $admin = \App\Models\Admin::find($request->admin_id);

        if (!$admin) {
            return response()->json(['status' => 'error', 'message' => 'Client not found']);
        }

        if ($admin->is_star_client == 1) {
            return response()->json(['status' => 'exists']);
        }

        // Update only if requested to do so
        if ($request->update == true) {
            $admin->is_star_client = 1;
            $admin->save();
            return response()->json(['status' => 'updated']);
        }

        return response()->json(['status' => 'not_star']);
    }

    //Fetch client matter assignee
    public function fetchClientMatterAssignee(Request $request)
    {
        $requestData = $request->all();
        $matter_info = DB::table('client_matters')->where('id',$requestData['client_matter_id'])->first();
        //dd($matter_info);
        if(!empty($matter_info)) {
            $response['matter_info'] = $matter_info;
            $response['status'] 	= 	true;
            $response['message']	=	'Record is exist';
        }else{
            $response['matter_info'] 	= array();
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
        }
        echo json_encode($response);
    }

    //update client matter assignee
    public function updateClientMatterAssignee(Request $request){
        //dd($request->all());
        $requstData = $request->all();
        if(ClientMatter::where('id', '=', $requstData['selectedMatterLM'])->exists()) {
            $obj = ClientMatter::find($requstData['selectedMatterLM']);
            $obj->sel_migration_agent = $requstData['migration_agent'];
            $obj->sel_person_responsible = $requstData['person_responsible'];
            $obj->sel_person_assisting = $requstData['person_assisting'];
            $obj->user_id = $requstData['user_id'];
            $saved = $obj->save();
            if($saved) {

                $objs = new \App\Models\ActivitiesLog;
                $objs->client_id = $requstData['client_id'];
                $objs->created_by = Auth::user()->id;
                $objs->description = '';
                $objs->subject = 'updated client matter assignee';
                $objs->save();

                $response['status'] 	= 	true;
                $response['message']	=	'Record is exist';
            }else{
                $response['status'] 	= 	false;
                $response['message']	=	'Record is not exist.Please try again';
            }
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
        }
        echo json_encode($response);
    }

    //Add Personal Doucment Category
    public function addPersonalDocCategory(Request $request)
    {
        $categoryTitle = trim($request->input('personal_doc_category'));
        $clientId = $request->input('clientid');

        $request->merge(['personal_doc_category' => $categoryTitle]);

        // Basic validation
        $validator = Validator::make($request->all(), [
            'personal_doc_category' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first('personal_doc_category')
            ]);
        }

        // RULE 1: If status=1 and client_id is NULL, title must be unique globally (only one)
        $existsForNullClient = \App\Models\PersonalDocumentType::where('title', $categoryTitle)
            ->where('status', 1)
            ->whereNull('client_id')
            ->exists();

        if ($existsForNullClient) {
            return response()->json([
                'status' => false,
                'message' => 'This category already exists globally (for NULL client).'
            ]);
        }

        // RULE 2: Same title with status=1 for same client_id is not allowed
        $existsForSameClient = \App\Models\PersonalDocumentType::where('title', $categoryTitle)
            ->where('status', 1)
            ->where('client_id', $clientId)
            ->exists();

        if ($existsForSameClient) {
            return response()->json([
                'status' => false,
                'message' => 'This category already exists for this client.'
            ]);
        }

        try {
            $category = new \App\Models\PersonalDocumentType();
            $category->title = $categoryTitle;
            $category->status = 1;
            $category->client_id = $clientId ?? null;
            $category->save();

            return response()->json([
                'status' => true,
                'message' => 'Personal Document Category added successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error adding category: ' . $e->getMessage()
            ]);
        }
    }

    //Update Personal Doucment Category
    public function updatePersonalDocCategory(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:personal_document_types,id',
            'title' => 'required|string|max:255',
        ]);

        $category = PersonalDocumentType::findOrFail($request->id);
        $clientId = $category->client_id; // Get the client_id of the category being updated

        // Check if the category is client-generated
        if ($category->client_id === null) {
            return response()->json(['success' => false, 'message' => 'Only client-generated categories can be updated.']);
        }

        $categoryTitle = trim($request->input('title'));

        // RULE 1: If status=1 and client_id is NULL, title must be unique globally (only one)
        $existsForNullClient = \App\Models\PersonalDocumentType::where('title', $categoryTitle)
            ->where('status', 1)
            ->whereNull('client_id')
            ->exists();

        if ($existsForNullClient) {
            return response()->json([
                'status' => false,
                'message' => 'This category already exists globally for all client.Pls try other.'
            ]);
        }

        // RULE 2: Same title with status=1 for same client_id is not allowed (excluding the current category)
        $existsForSameClient = \App\Models\PersonalDocumentType::where('title', $categoryTitle)
            ->where('status', 1)
            ->where('client_id', $clientId)
            ->where('id', '!=', $category->id) // Exclude the current category
            ->exists();

        if ($existsForSameClient) {
            return response()->json([
                'status' => false,
                'message' => 'This category already exists for this client.Pls try other.'
            ]);
        }

        try {
            $category->title = $categoryTitle;
            $category->save();

            return response()->json(['status' => true,'message' => 'This category is updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error updating category: ' . $e->getMessage()]);
        }
    }

    //Delete Personal Doucment Category
    /*public function deletePersonalDocCategory(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:personal_document_types,id',
        ]);

        $category = PersonalDocumentType::findOrFail($request->id);

        // Check if the category is client-generated
        if ($category->client_id !== null) {
            $category->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Only client-generated categories can be deleted.']);
    }*/

    //Add Visa Doucment Category
    public function addVisaDocCategory(Request $request)
    {
        $categoryTitle = trim($request->input('visa_doc_category'));
        $clientId = $request->input('clientid');
        $clientMatterId = $request->input('clientmatterid');

        $request->merge(['visa_doc_category' => $categoryTitle]);

        // Basic validation
        $validator = Validator::make($request->all(), [
            'visa_doc_category' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first('visa_doc_category')
            ]);
        }

        // RULE 1: If status=1 and client_id is NULL, title must be unique globally (only one)
        $existsForNullClient = \App\Models\VisaDocumentType::where('title', $categoryTitle)
            ->where('status', 1)
            ->whereNull('client_matter_id')
            ->exists();

        if ($existsForNullClient) {
            return response()->json([
                'status' => false,
                'message' => 'This category already exists globally.'
            ]);
        }

        // RULE 2: Same title with status=1 for same client_id is not allowed
        $existsForSameClient = \App\Models\VisaDocumentType::where('title', $categoryTitle)
            ->where('status', 1)
            ->where('client_matter_id', $clientMatterId)
            ->exists();

        if ($existsForSameClient) {
            return response()->json([
                'status' => false,
                'message' => 'This category already exists for this client matter.'
            ]);
        }

        try {
            $category = new \App\Models\VisaDocumentType();
            $category->title = $categoryTitle;
            $category->status = 1;
            $category->client_id = $clientId ?? null;
            $category->client_matter_id = $clientMatterId ?? null;
            $category->save();

            return response()->json([
                'status' => true,
                'message' => 'Visa Document Category added successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error adding category: ' . $e->getMessage()
            ]);
        }
    }

    //Update Visa Doucment Category
    public function updateVisaDocCategory(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:visa_document_types,id',
            'title' => 'required|string|max:255',
        ]);

        $category = VisaDocumentType::findOrFail($request->id);
        $clientMatterId = $category->client_matter_id; // Get the client_id of the category being updated

        // Check if the category is client-generated
        if ($category->client_matter_id === null) {
            return response()->json(['success' => false, 'message' => 'Only client-matter-generated categories can be updated.']);
        }

        $categoryTitle = trim($request->input('title'));

        // RULE 1: If status=1 and client_id is NULL, title must be unique globally (only one)
        $existsForNullClient = \App\Models\VisaDocumentType::where('title', $categoryTitle)
            ->where('status', 1)
            ->whereNull('client_matter_id')
            ->exists();

        if ($existsForNullClient) {
            return response()->json([
                'status' => false,
                'message' => 'This category already exists globally for all client matters.Pls try other.'
            ]);
        }

        // RULE 2: Same title with status=1 for same client_id is not allowed (excluding the current category)
        $existsForSameClient = \App\Models\VisaDocumentType::where('title', $categoryTitle)
            ->where('status', 1)
            ->where('client_matter_id', $clientMatterId)
            ->where('id', '!=', $category->id) // Exclude the current category
            ->exists();

        if ($existsForSameClient) {
            return response()->json([
                'status' => false,
                'message' => 'This category already exists for this client matter.Pls try other.'
            ]);
        }

        try {
            $category->title = $categoryTitle;
            $category->save();

            return response()->json(['status' => true,'message' => 'This category is updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error updating category: ' . $e->getMessage()]);
        }
    }

    //send to n8n webhook
    public function sendToWebhook(Request $request)
    {
        try {
            $data = $request->all();
            $webhookUrl = env('N8N_GTE_WEBHOOK');
            
            // Check if webhook URL is configured
            if (empty($webhookUrl)) {
                return response()->json([
                    'message' => 'N8N webhook URL is not configured. Please check your environment settings.',
                    'error' => 'Missing N8N_GTE_WEBHOOK environment variable'
                ], 500);
            }

            // Add timeout and retry configuration
            $response = Http::timeout(30)
                           ->retry(3, 1000)
                           ->post($webhookUrl, $data);

            if ($response->successful()) {
                return response()->json([
                    'message' => 'Data sent to n8n successfully', 
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'message' => 'Error sending data to n8n', 
                    'error' => $response->json(),
                    'status_code' => $response->status()
                ], 500);
            }
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'message' => 'Cannot connect to n8n server. Please check if the server is running and accessible.',
                'error' => $e->getMessage(),
                'webhook_url' => $webhookUrl ?? 'Not configured'
            ], 500);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unexpected error occurred while sending data to n8n',
                'error' => $e->getMessage(),
                'webhook_url' => $webhookUrl ?? 'Not configured'
            ], 500);
        }
    }

    //Check same client_id and same client matter is already exist in db or not
    public function checkCostAssignment(Request $request)
    {
        $exists = \App\Models\CostAssignmentForm::where('client_id', $request->client_id)
                    ->where('client_matter_id', $request->client_matter_id)
                    ->exists();

        return response()->json(['exists' => $exists]);
    }

    //Store Cost Assignment Form Values of Lead
    public function savecostassignmentlead(Request $request)
    {   
        if ($request->isMethod('post'))
        {
            $requestData = $request->all(); //dd($requestData);
            //insert into client matter table
            $obj5 = new ClientMatter();
            $obj5->user_id = Auth::user()->id;
            $obj5->client_id = $requestData['client_id'];
            $obj5->sel_migration_agent = $requestData['migration_agent'];
            $obj5->sel_person_responsible = $requestData['person_responsible'];
            $obj5->sel_person_assisting = $requestData['person_assisting'];
            $obj5->sel_matter_id = $requestData['matter_id'];
            
            $client_matters_cnt_per_client = DB::table('client_matters')->select('id')->where('sel_matter_id',$requestData['matter_id'])->where('client_id',$requestData['client_id'])->count();
            $client_matters_current_no = $client_matters_cnt_per_client+1;
            if($requestData['matter_id'] == 1) {
                $obj5->client_unique_matter_no = 'GN_'.$client_matters_current_no;
            } else {
                $matterInfo = Matter::select('nick_name')->where('id', '=', $requestData['matter_id'])->first();
                $obj5->client_unique_matter_no = $matterInfo->nick_name."_".$client_matters_current_no;
            }
            $obj5->workflow_stage_id = 1;
            $saved5 = $obj5->save();
            $lastInsertedId = $obj5->id; // ← This gets the last inserted ID
            if($saved5) 
            {
                //update type client from lead in admins table
                \App\Models\Admin::where('id', $requestData['client_id'])->update(['type' => 'client']);

                if( isset($requestData['surcharge']) && $requestData['surcharge'] != '') {
                    $surcharge = $requestData['surcharge'];
                } else {
                    $surcharge = 'Yes';
                }

                $Dept_Base_Application_Charge = floatval($requestData['Dept_Base_Application_Charge'] ?? 0); //dd($Dept_Base_Application_Charge);
                $Dept_Base_Application_Charge_no_of_person = intval($requestData['Dept_Base_Application_Charge_no_of_person'] ?? 1); //dd($Dept_Base_Application_Charge_no_of_person);
                $Dept_Base_Application_Charge_after_person = $Dept_Base_Application_Charge * $Dept_Base_Application_Charge_no_of_person;
                $Dept_Base_Application_Charge_after_person = floatval($Dept_Base_Application_Charge_after_person); //dd($Dept_Base_Application_Charge_after_person);

                if( $surcharge == 'Yes'){
                    // Step 2: Calculate 1.4% surcharge
                    $Dept_Base_Application_Surcharge = round($Dept_Base_Application_Charge_after_person * 0.014, 2);
                } else {
                    $Dept_Base_Application_Surcharge = 0;
                }
            
                // Step 3: Final total after surcharge
                $Dept_Base_Application_Charge_after_person_surcharge = $Dept_Base_Application_Charge_after_person + $Dept_Base_Application_Surcharge; //dd($Dept_Additional_Applicant_Charge_18_Plus_after_person_surcharge);

                $Dept_Non_Internet_Application_Charge = floatval($requestData['Dept_Non_Internet_Application_Charge'] ?? 0); //dd($Dept_Non_Internet_Application_Charge);
                $Dept_Non_Internet_Application_Charge_no_of_person = intval($requestData['Dept_Non_Internet_Application_Charge_no_of_person'] ?? 1); //dd($Dept_Non_Internet_Application_Charge_no_of_person);
                $Dept_Non_Internet_Application_Charge_after_person = $Dept_Non_Internet_Application_Charge * $Dept_Non_Internet_Application_Charge_no_of_person;
                $Dept_Non_Internet_Application_Charge_after_person = floatval($Dept_Non_Internet_Application_Charge_after_person); //dd($Dept_Non_Internet_Application_Charge_after_person);

                if( $surcharge == 'Yes'){
                    // Step 2: Calculate 1.4% surcharge
                    $Dept_Non_Internet_Application_Surcharge = round($Dept_Non_Internet_Application_Charge_after_person * 0.014, 2);
                } else {
                    $Dept_Non_Internet_Application_Surcharge = 0;
                }
                // Step 3: Final total after surcharge
                $Dept_Non_Internet_Application_Charge_after_person_surcharge = $Dept_Non_Internet_Application_Surcharge + $Dept_Non_Internet_Application_Charge_after_person; //dd($Dept_Additional_Applicant_Charge_18_Plus_after_person_surcharge);

                $Dept_Additional_Applicant_Charge_18_Plus = floatval($requestData['Dept_Additional_Applicant_Charge_18_Plus'] ?? 0);
                $Dept_Additional_Applicant_Charge_18_Plus_no_of_person = intval($requestData['Dept_Additional_Applicant_Charge_18_Plus_no_of_person'] ?? 1);
                $Dept_Additional_Applicant_Charge_18_Plus_after_person = $Dept_Additional_Applicant_Charge_18_Plus * $Dept_Additional_Applicant_Charge_18_Plus_no_of_person;
                $Dept_Additional_Applicant_Charge_18_Plus_after_person = floatval($Dept_Additional_Applicant_Charge_18_Plus_after_person);

                if( $surcharge == 'Yes'){
                    // Step 2: Calculate 1.4% surcharge
                    $Dept_Additional_Applicant_Charge_18_Surcharge = round($Dept_Additional_Applicant_Charge_18_Plus_after_person * 0.014, 2);
                } else {
                    $Dept_Additional_Applicant_Charge_18_Surcharge = 0;
                }
                // Step 3: Final total after surcharge
                $Dept_Additional_Applicant_Charge_18_Plus_after_person_surcharge = $Dept_Additional_Applicant_Charge_18_Surcharge + $Dept_Additional_Applicant_Charge_18_Plus_after_person;

                $Dept_Additional_Applicant_Charge_Under_18 = floatval($requestData['Dept_Additional_Applicant_Charge_Under_18'] ?? 0);
                $Dept_Additional_Applicant_Charge_Under_18_no_of_person = intval($requestData['Dept_Additional_Applicant_Charge_Under_18_no_of_person'] ?? 1);
                $Dept_Additional_Applicant_Charge_Under_18_after_person = $Dept_Additional_Applicant_Charge_Under_18 * $Dept_Additional_Applicant_Charge_Under_18_no_of_person;
                $Dept_Additional_Applicant_Charge_Under_18_after_person = floatval($Dept_Additional_Applicant_Charge_Under_18_after_person);

                if( $surcharge == 'Yes'){
                    // Step 2: Calculate 1.4% surcharge
                    $Dept_Additional_Applicant_Charge_Under_18_Surcharge = round($Dept_Additional_Applicant_Charge_Under_18_after_person * 0.014, 2);
                } else {
                    $Dept_Additional_Applicant_Charge_Under_18_Surcharge = 0;
                }
                // Step 3: Final total after surcharge
                $Dept_Additional_Applicant_Charge_Under_18_after_person_surcharge = $Dept_Additional_Applicant_Charge_Under_18_Surcharge + $Dept_Additional_Applicant_Charge_Under_18_after_person;

                $Dept_Subsequent_Temp_Application_Charge = floatval($requestData['Dept_Subsequent_Temp_Application_Charge'] ?? 0);
                $Dept_Subsequent_Temp_Application_Charge_no_of_person = intval($requestData['Dept_Subsequent_Temp_Application_Charge_no_of_person'] ?? 1);
                $Dept_Subsequent_Temp_Application_Charge_after_person = $Dept_Subsequent_Temp_Application_Charge * $Dept_Subsequent_Temp_Application_Charge_no_of_person;
                $Dept_Subsequent_Temp_Application_Charge_after_person = floatval($Dept_Subsequent_Temp_Application_Charge_after_person);

                if( $surcharge == 'Yes'){
                    // Step 2: Calculate 1.4% surcharge
                    $Dept_Subsequent_Temp_Application_Surcharge = round($Dept_Subsequent_Temp_Application_Charge_after_person * 0.014, 2);
                } else {
                    $Dept_Subsequent_Temp_Application_Surcharge = 0;
                }
                // Step 3: Final total after surcharge
                $Dept_Subsequent_Temp_Application_Charge_after_person_surcharge = $Dept_Subsequent_Temp_Application_Surcharge + $Dept_Subsequent_Temp_Application_Charge_after_person;

                $Dept_Second_VAC_Instalment_Charge_18_Plus = floatval($requestData['Dept_Second_VAC_Instalment_Charge_18_Plus'] ?? 0);
                $Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person = intval($requestData['Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person'] ?? 1);
                $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person = $Dept_Second_VAC_Instalment_Charge_18_Plus * $Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person;
                $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person = floatval($Dept_Second_VAC_Instalment_Charge_18_Plus_after_person);

                if( $surcharge == 'Yes'){
                    // Step 2: Calculate 1.4% surcharge
                    $Dept_Second_VAC_Instalment_18_Plus_Surcharge = round($Dept_Second_VAC_Instalment_Charge_18_Plus_after_person * 0.014, 2);
                } else {
                    $Dept_Second_VAC_Instalment_18_Plus_Surcharge = 0;
                }
                // Step 3: Final total after surcharge
                $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person_surcharge = $Dept_Second_VAC_Instalment_18_Plus_Surcharge + $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person;

                $Dept_Second_VAC_Instalment_Under_18 = floatval($requestData['Dept_Second_VAC_Instalment_Under_18'] ?? 0);
                $Dept_Second_VAC_Instalment_Under_18_no_of_person = intval($requestData['Dept_Second_VAC_Instalment_Under_18_no_of_person'] ?? 1);
                $Dept_Second_VAC_Instalment_Under_18_after_person = $Dept_Second_VAC_Instalment_Under_18 * $Dept_Second_VAC_Instalment_Under_18_no_of_person;
                $Dept_Second_VAC_Instalment_Under_18_after_person = floatval($Dept_Second_VAC_Instalment_Under_18_after_person);

                if( $surcharge == 'Yes'){
                    // Step 2: Calculate 1.4% surcharge
                    $Dept_Second_VAC_Instalment_Under_18_Surcharge = round($Dept_Second_VAC_Instalment_Under_18_after_person * 0.014, 2);
                } else {
                    $Dept_Second_VAC_Instalment_Under_18_Surcharge = 0;
                }
                // Step 3: Final total after surcharge
                $Dept_Second_VAC_Instalment_Under_18_after_person_surcharge = $Dept_Second_VAC_Instalment_Under_18_Surcharge + $Dept_Second_VAC_Instalment_Under_18_after_person;

                $TotalDoHACharges = $Dept_Base_Application_Charge_after_person
                                    + $Dept_Additional_Applicant_Charge_18_Plus_after_person
                                    + $Dept_Additional_Applicant_Charge_Under_18_after_person
                                    + $Dept_Subsequent_Temp_Application_Charge_after_person
                                    + $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person
                                    + $Dept_Second_VAC_Instalment_Under_18_after_person
                                    + $Dept_Non_Internet_Application_Charge_after_person;

                $TotalDoHASurcharges = $Dept_Base_Application_Charge_after_person_surcharge
                                        + $Dept_Additional_Applicant_Charge_18_Plus_after_person_surcharge
                                        + $Dept_Additional_Applicant_Charge_Under_18_after_person_surcharge
                                        + $Dept_Subsequent_Temp_Application_Charge_after_person_surcharge
                                        + $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person_surcharge
                                        + $Dept_Second_VAC_Instalment_Under_18_after_person_surcharge
                                        + $Dept_Non_Internet_Application_Charge_after_person_surcharge;

                $TotalBLOCKFEE = $requestData['Block_1_Ex_Tax'] + $requestData['Block_2_Ex_Tax'] +  $requestData['Block_3_Ex_Tax'];

                $cost_assignment_cnt = \App\Models\CostAssignmentForm::where('client_id',$requestData['client_id'])->where('client_matter_id',$lastInsertedId)->count();
                //dd($surcharge);
                if($cost_assignment_cnt >0)
                {
                    //update
                    $costAssignment = \App\Models\CostAssignmentForm::where('client_id', $requestData['client_id'])
                    ->where('client_matter_id', $lastInsertedId)
                    ->first();
                    if ($costAssignment) 
                    {
                        $saved = $costAssignment->update([
                            'agent_id' => $requestData['agent_id'],
                            'surcharge' => $surcharge,
                            
                            'Dept_Base_Application_Charge' => $requestData['Dept_Base_Application_Charge'],
                            'Dept_Base_Application_Charge_no_of_person' => $requestData['Dept_Base_Application_Charge_no_of_person'],
                            'Dept_Base_Application_Charge_after_person' => $Dept_Base_Application_Charge_after_person,
                            'Dept_Base_Application_Charge_after_person_surcharge' => $Dept_Base_Application_Charge_after_person_surcharge,

                            'Dept_Non_Internet_Application_Charge' => $requestData['Dept_Non_Internet_Application_Charge'],
                            'Dept_Non_Internet_Application_Charge_no_of_person' => $requestData['Dept_Non_Internet_Application_Charge_no_of_person'],
                            'Dept_Non_Internet_Application_Charge_after_person' => $Dept_Non_Internet_Application_Charge_after_person,
                            'Dept_Non_Internet_Application_Charge_after_person_surcharge' => $Dept_Non_Internet_Application_Charge_after_person_surcharge,

                            'Dept_Additional_Applicant_Charge_18_Plus' => $requestData['Dept_Additional_Applicant_Charge_18_Plus'],
                            'Dept_Additional_Applicant_Charge_18_Plus_no_of_person' => $requestData['Dept_Additional_Applicant_Charge_18_Plus_no_of_person'],
                            'Dept_Additional_Applicant_Charge_18_Plus_after_person' => $Dept_Additional_Applicant_Charge_18_Plus_after_person,
                            'Dept_Additional_Applicant_Charge_18_Plus_after_person_surcharge' => $Dept_Additional_Applicant_Charge_18_Plus_after_person_surcharge,

                            'Dept_Additional_Applicant_Charge_Under_18' => $requestData['Dept_Additional_Applicant_Charge_Under_18'],
                            'Dept_Additional_Applicant_Charge_Under_18_no_of_person' => $requestData['Dept_Additional_Applicant_Charge_Under_18_no_of_person'],
                            'Dept_Additional_Applicant_Charge_Under_18_after_person' => $Dept_Additional_Applicant_Charge_Under_18_after_person,
                            'Dept_Additional_Applicant_Charge_Under_18_after_person_surcharge' => $Dept_Additional_Applicant_Charge_Under_18_after_person_surcharge,

                            'Dept_Subsequent_Temp_Application_Charge' => $requestData['Dept_Subsequent_Temp_Application_Charge'],
                            'Dept_Subsequent_Temp_Application_Charge_no_of_person' => $requestData['Dept_Subsequent_Temp_Application_Charge_no_of_person'],
                            'Dept_Subsequent_Temp_Application_Charge_after_person' => $Dept_Subsequent_Temp_Application_Charge_after_person,
                            'Dept_Subsequent_Temp_Application_Charge_after_person_surcharge' => $Dept_Subsequent_Temp_Application_Charge_after_person_surcharge,

                            'Dept_Second_VAC_Instalment_Charge_18_Plus' => $requestData['Dept_Second_VAC_Instalment_Charge_18_Plus'],
                            'Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person' => $requestData['Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person'],
                            'Dept_Second_VAC_Instalment_Charge_18_Plus_after_person' => $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person,
                            'Dept_Second_VAC_Instalment_Charge_18_Plus_after_person_surcharge' => $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person_surcharge,

                            'Dept_Second_VAC_Instalment_Under_18' => $requestData['Dept_Second_VAC_Instalment_Under_18'],
                            'Dept_Second_VAC_Instalment_Under_18_no_of_person' => $requestData['Dept_Second_VAC_Instalment_Under_18_no_of_person'],
                            'Dept_Second_VAC_Instalment_Under_18_after_person' => $Dept_Second_VAC_Instalment_Under_18_after_person,
                            'Dept_Second_VAC_Instalment_Under_18_after_person_surcharge' => $Dept_Second_VAC_Instalment_Under_18_after_person_surcharge,

                            'Dept_Nomination_Application_Charge' => $requestData['Dept_Nomination_Application_Charge'],
                            'Dept_Sponsorship_Application_Charge' => $requestData['Dept_Sponsorship_Application_Charge'],
                            'Block_1_Ex_Tax' => $requestData['Block_1_Ex_Tax'],
                            'Block_2_Ex_Tax' => $requestData['Block_2_Ex_Tax'],
                            'Block_3_Ex_Tax' => $requestData['Block_3_Ex_Tax'],
                            'additional_fee_1' => $requestData['additional_fee_1'],
                            'TotalDoHACharges' => $TotalDoHACharges,
                            'TotalDoHASurcharges' => $TotalDoHASurcharges,
                            'TotalBLOCKFEE' => $TotalBLOCKFEE
                        ]);
                    }
                }
                else
                {
                    //insert
                    $obj = new CostAssignmentForm;
                    $obj->client_id = $requestData['client_id'];
                    $obj->client_matter_id = $lastInsertedId;
                    $obj->agent_id = $requestData['migration_agent'];
                    $obj->surcharge = $surcharge;
                    
                    $obj->Dept_Base_Application_Charge = $requestData['Dept_Base_Application_Charge'];
                    $obj->Dept_Base_Application_Charge_no_of_person = $requestData['Dept_Base_Application_Charge_no_of_person'];
                    $obj->Dept_Base_Application_Charge_after_person = $Dept_Base_Application_Charge_after_person;
                    $obj->Dept_Base_Application_Charge_after_person_surcharge = $Dept_Base_Application_Charge_after_person_surcharge;

                    $obj->Dept_Non_Internet_Application_Charge = $requestData['Dept_Non_Internet_Application_Charge'];
                    $obj->Dept_Non_Internet_Application_Charge_no_of_person = $requestData['Dept_Non_Internet_Application_Charge_no_of_person'];
                    $obj->Dept_Non_Internet_Application_Charge_after_person = $Dept_Non_Internet_Application_Charge_after_person;
                    $obj->Dept_Non_Internet_Application_Charge_after_person_surcharge = $Dept_Non_Internet_Application_Charge_after_person_surcharge;

                    $obj->Dept_Additional_Applicant_Charge_18_Plus = $requestData['Dept_Additional_Applicant_Charge_18_Plus'];
                    $obj->Dept_Additional_Applicant_Charge_18_Plus_no_of_person = $requestData['Dept_Additional_Applicant_Charge_18_Plus_no_of_person'];
                    $obj->Dept_Additional_Applicant_Charge_18_Plus_after_person = $Dept_Additional_Applicant_Charge_18_Plus_after_person;
                    $obj->Dept_Additional_Applicant_Charge_18_Plus_after_person_surcharge = $Dept_Additional_Applicant_Charge_18_Plus_after_person_surcharge;

                    $obj->Dept_Additional_Applicant_Charge_Under_18 = $requestData['Dept_Additional_Applicant_Charge_Under_18'];
                    $obj->Dept_Additional_Applicant_Charge_Under_18_no_of_person = $requestData['Dept_Additional_Applicant_Charge_Under_18_no_of_person'];
                    $obj->Dept_Additional_Applicant_Charge_Under_18_after_person = $Dept_Additional_Applicant_Charge_Under_18_after_person;
                    $obj->Dept_Additional_Applicant_Charge_Under_18_after_person_surcharge = $Dept_Additional_Applicant_Charge_Under_18_after_person_surcharge;

                    $obj->Dept_Subsequent_Temp_Application_Charge = $requestData['Dept_Subsequent_Temp_Application_Charge'];
                    $obj->Dept_Subsequent_Temp_Application_Charge_no_of_person = $requestData['Dept_Subsequent_Temp_Application_Charge_no_of_person'];
                    $obj->Dept_Subsequent_Temp_Application_Charge_after_person = $Dept_Subsequent_Temp_Application_Charge_after_person;
                    $obj->Dept_Subsequent_Temp_Application_Charge_after_person_surcharge = $Dept_Subsequent_Temp_Application_Charge_after_person_surcharge;

                    $obj->Dept_Second_VAC_Instalment_Charge_18_Plus = $requestData['Dept_Second_VAC_Instalment_Charge_18_Plus'];
                    $obj->Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person = $requestData['Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person'];
                    $obj->Dept_Second_VAC_Instalment_Charge_18_Plus_after_person = $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person;
                    $obj->Dept_Second_VAC_Instalment_Charge_18_Plus_after_person_surcharge = $Dept_Second_VAC_Instalment_Charge_18_Plus_after_person_surcharge;

                    $obj->Dept_Second_VAC_Instalment_Under_18 = $requestData['Dept_Second_VAC_Instalment_Under_18'];
                    $obj->Dept_Second_VAC_Instalment_Under_18_no_of_person = $requestData['Dept_Second_VAC_Instalment_Under_18_no_of_person'];
                    $obj->Dept_Second_VAC_Instalment_Under_18_after_person = $Dept_Second_VAC_Instalment_Under_18_after_person;
                    $obj->Dept_Second_VAC_Instalment_Under_18_after_person_surcharge = $Dept_Second_VAC_Instalment_Under_18_after_person_surcharge;

                    $obj->Dept_Nomination_Application_Charge = $requestData['Dept_Nomination_Application_Charge'];
                    $obj->Dept_Sponsorship_Application_Charge = $requestData['Dept_Sponsorship_Application_Charge'];

                    $obj->Block_1_Ex_Tax = $requestData['Block_1_Ex_Tax'];
                    $obj->Block_2_Ex_Tax = $requestData['Block_2_Ex_Tax'];
                    $obj->Block_3_Ex_Tax = $requestData['Block_3_Ex_Tax'];
                    $obj->additional_fee_1 = $requestData['additional_fee_1'];
                    $obj->TotalDoHACharges = $TotalDoHACharges;
                    $obj->TotalDoHASurcharges = $TotalDoHASurcharges;
                    $obj->TotalBLOCKFEE = $TotalBLOCKFEE;
                    $saved = $obj->save();
                }
                if (!$saved) 
                {
                    $response['status'] 	= 	false;
                    $response['message']	=	'Cost assignment not added successfully.Please try again';
                } 
                else 
                {
                    $response['status'] 	= 	true;
                    $response['message']	=	'Cost assignment added successfully';
                }
            }
        }
        echo json_encode($response);
    }

    //Get Cost assignment Migration Agent Detail Lead
    public function getCostAssignmentMigrationAgentDetailLead(Request $request)
    {
        $requestData = 	$request->all(); //dd($requestData);
        //get matter info
		$matterInfo = DB::table('matters')->where('id',$requestData['client_matter_id'])->first();
		//dd($matterInfo);
		if($matterInfo){
			$response['matterInfo'] = $matterInfo;
			$response['status'] 	= 	true;
			$response['message']	=	'Record is exist';
		} else {
			$response['matterInfo'] = "";
			$response['status'] 	= 	false;
			$response['message']	=	'Record is not exist.Please try again';
		}

		//get cost assignment matter fee
		$costassignmentmatterInfo = DB::table('cost_assignment_forms')->where('client_id',$requestData['client_id'])->where('client_matter_id',$requestData['client_matter_id'])->first();
		//dd($costassignmentmatterInfo);
		if($costassignmentmatterInfo){
			$response['cost_assignment_matterInfo'] = $costassignmentmatterInfo;
		} else {
			$response['cost_assignment_matterInfo'] = "";
		}
		echo json_encode($response);
    }

    //Upload agreement in PDF
    public function uploadAgreement(Request $request, $clientId)
    {
        //1. Validate only PDF files (max 10MB)
        $request->validate([
            'agreement_doc' => 'required|mimes:pdf|max:10240', // 10MB max
        ]);

        $requestData = $request->all();
        $pdfFile = $request->file('agreement_doc');

        //2. Get file details
        $originalName = $pdfFile->getClientOriginalName();
        $size = $pdfFile->getSize();
        $timestampedName = time() . '_' . $originalName;

        //3. Build S3 path using client ID
        $adminInfo = \App\Models\Admin::select('client_id')->where('id', $clientId)->first();
        $clientUniqueId = !empty($adminInfo) ? $adminInfo->client_id : "";
        $s3Path = $clientUniqueId . '/agreement/' . $timestampedName;

        //4. Upload directly to S3
        \Storage::disk('s3')->put($s3Path, file_get_contents($pdfFile));

        //5. Save document details in DB
        $originalInfo = pathinfo($originalName);
        $doc = new \App\Models\Document;
        $doc->file_name = $originalInfo['filename']; // e.g., "passport" (without extension)
        $doc->filetype = 'pdf';
        $doc->myfile = Storage::disk('s3')->url($s3Path);
        $doc->myfile_key = $timestampedName;
        $doc->user_id = Auth::user()->id;
        $doc->client_id = $clientId;
        $doc->type = 'client';
        $doc->file_size = $size;
        $doc->doc_type = 'agreement';
        $doc->client_matter_id = $requestData['clientmatterid'];
        $saved = $doc->save();

        //6. Log activity if saved
        if ($saved) {
            $log = new \App\Models\ActivitiesLog;
            $log->client_id = $clientId;
            $log->created_by = Auth::user()->id;
            $log->description = '';
            $log->subject = 'Finalized visa agreement uploaded as PDF';
            $log->save();
        }

        //7. Return success response
        return response()->json([
            'status' => true,
            'message' => 'PDF agreement uploaded successfully!'
        ]);
    }
    
    /**
     * Send invoice to Hubdoc
     */
    public function sendToHubdoc(Request $request, $id)
    {
        try {
            // Increase execution time limit for PDF generation
            set_time_limit(300); // 5 minutes
            
            // Check if invoice exists
            $record_get = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->get();
            
            if(empty($record_get) || count($record_get) == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invoice not found'
                ]);
            }

            // Get client info
            $clientname = DB::table('admins')->where('id',$record_get[0]->client_id)->first();
            
            // Calculate amounts
            $total_Invoice_Amount = DB::table('account_all_invoice_receipts')
                ->where('receipt_type', 3)
                ->where('receipt_id', $id)
                ->sum(DB::raw("CASE
                    WHEN payment_type = 'Discount' THEN -withdraw_amount
                    ELSE withdraw_amount
                END"));

            // Get all required data for PDF generation
            $record_get_Professional_Fee_cnt = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Professional Fee')->count();
            $record_get_Department_Charges_cnt = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Department Charges')->count();
            $record_get_Surcharge_cnt = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Surcharge')->count();
            $record_get_Disbursements_cnt = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Disbursements')->count();
            $record_get_Other_Cost_cnt = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Other Cost')->count();
            $record_get_Discount_cnt = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Discount')->count();

            $record_get_Professional_Fee = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Professional Fee')->get();
            $record_get_Department_Charges = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Department Charges')->get();
            $record_get_Surcharge = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Surcharge')->get();
            $record_get_Disbursements = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Disbursements')->get();
            $record_get_Other_Cost = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Other Cost')->get();
            $record_get_Discount = DB::table('account_all_invoice_receipts')->where('receipt_type',3)->where('receipt_id',$id)->where('payment_type','Discount')->get();

            $total_Gross_Amount = DB::table('account_all_invoice_receipts')
                ->where('receipt_type', 3)
                ->where('receipt_id', $id)
                ->sum(DB::raw("
                    CASE
                        WHEN payment_type = 'Discount' AND gst_included = 'Yes' THEN -(withdraw_amount - (withdraw_amount / 11))
                        WHEN payment_type = 'Discount' AND gst_included = 'No' THEN -withdraw_amount
                        WHEN gst_included = 'Yes' THEN withdraw_amount - (withdraw_amount / 11)
                        ELSE withdraw_amount
                    END
                "));

            $total_GST_amount =  $total_Invoice_Amount - $total_Gross_Amount;

            $total_Pending_amount  = DB::table('account_client_receipts')
            ->where('receipt_type', 3)
            ->where('receipt_id', $id)
            ->where(function ($query) {
                $query->whereIn('invoice_status', [0, 2])
                    ->orWhere(function ($q) {
                        $q->where('invoice_status', 1)
                            ->where('balance_amount', '!=', 0);
                    });
            })
            ->sum('balance_amount');

            // Get payment method
            $invoice_payment_method = '';
            if( !empty($record_get) && $record_get[0]->invoice_no != '') {
                $office_receipt = DB::table('account_client_receipts')->select('payment_method')->where('receipt_type',2)->where('invoice_no',$record_get[0]->invoice_no)->first();
                if($office_receipt){
                    $invoice_payment_method = $office_receipt->payment_method;
                }
            }

            // Get client matter
            $client_matter_no = '';
            if( !empty($record_get) && $record_get[0]->client_matter_id != '') {
                $client_info = DB::table('admins')->select('client_id')->where('id',$record_get[0]->client_id)->first();
                if($client_info){
                    $client_unique_id = $client_info->client_id;
                } else {
                    $client_unique_id = '';
                }

                $matter_info = DB::table('client_matters')->select('client_unique_matter_no')->where('id',$record_get[0]->client_matter_id)->first();
                if($matter_info){
                    $client_unique_matter_no = $matter_info->client_unique_matter_no;
                    $client_matter_no = $client_unique_id.'-'.$client_unique_matter_no;
                } else {
                    $client_unique_matter_no = '';
                    $client_matter_no = '';
                }
            }

            // Generate PDF with optimized settings
            $pdf = \PDF::setOptions([
                'isHtml5ParserEnabled' => true, 
                'isRemoteEnabled' => true,
                'logOutputFile' => storage_path('logs/log.htm'),
                'tempDir' => storage_path('logs/'),
                'chroot' => storage_path('logs/'),
                'enable_php' => false,
                'enable_javascript' => false,
                'enable_smart_shrinking' => true,
                'dpi' => 96,
                'default_font' => 'Arial'
            ])->loadView('emails.geninvoice',compact(
                ['record_get',
                'record_get_Professional_Fee_cnt',
                'record_get_Department_Charges_cnt',
                'record_get_Surcharge_cnt',
                'record_get_Disbursements_cnt',
                'record_get_Other_Cost_cnt',
                'record_get_Discount_cnt',

                'record_get_Professional_Fee',
                'record_get_Department_Charges',
                'record_get_Surcharge',
                'record_get_Disbursements',
                'record_get_Other_Cost',
                'record_get_Discount',

                'total_Gross_Amount',
                'total_Invoice_Amount',
                'total_GST_amount',
                'total_Pending_amount',

                'clientname',
                'invoice_payment_method',
                'client_matter_no'
            ]));

            // Save PDF to temporary file
            $pdfFileName = 'Invoice_' . $record_get[0]->invoice_no . '_' . time() . '.pdf';
            $pdfPath = storage_path('app/temp/' . $pdfFileName);
            
            // Create temp directory if it doesn't exist
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            
            $pdf->save($pdfPath);

            // Prepare email data
            $invoiceData = [
                'invoice_no' => $record_get[0]->invoice_no ?? 'N/A',
                'client_name' => $clientname->name ?? 'N/A',
                'invoice_date' => $record_get[0]->trans_date ?? 'N/A',
                'amount' => $total_Invoice_Amount,
                'pdf_path' => $pdfPath,
                'file_name' => $pdfFileName
            ];

            // Send email to Hubdoc
            Mail::to(env('HUBDOC_EMAIL', 'easyvisa.1ae4@app.hubdoc.com'))->send(new HubdocInvoiceMail($invoiceData));

            // Mark invoice as sent to Hubdoc
            $updateResult = DB::table('account_client_receipts')
                ->where('receipt_type', 3)
                ->where('receipt_id', $id)
                ->update([
                    'hubdoc_sent' => true,
                    'hubdoc_sent_at' => now()
                ]);

            // Clean up temporary file
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }

            return response()->json([
                'status' => true,
                'message' => 'Invoice sent to Hubdoc successfully!'
            ]);

        } catch (\Exception $e) {
            // Clean up any temporary files in case of error
            if (isset($pdfPath) && file_exists($pdfPath)) {
                unlink($pdfPath);
            }
            
            return response()->json([
                'status' => false,
                'message' => 'Error sending invoice to Hubdoc: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Check Hubdoc status for an invoice
     */
    public function checkHubdocStatus(Request $request, $id)
    {
        try {
            $hubdoc_status = DB::table('account_client_receipts')
                ->where('receipt_type', 3)
                ->where('receipt_id', $id)
                ->select('hubdoc_sent', 'hubdoc_sent_at')
                ->first();

            if ($hubdoc_status) {
                return response()->json([
                    'hubdoc_sent' => (bool) $hubdoc_status->hubdoc_sent,
                    'hubdoc_sent_at' => $hubdoc_status->hubdoc_sent_at
                ]);
            } else {
                return response()->json([
                    'hubdoc_sent' => false,
                    'hubdoc_sent_at' => null
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'hubdoc_sent' => false,
                'hubdoc_sent_at' => null,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display client summary page
     */
    public function summary(Request $request, $client_id)
    {
        if (isset($client_id) && !empty($client_id)) {
            $encodeId = $client_id;
            $id = $this->decodeString($client_id);

            if (Admin::where('id', '=', $id)->where('role', '=', '7')->exists()) {
                $fetchedData = Admin::find($id);

                // Fetch all client-related data for summary
                $clientAddresses = ClientAddress::where('client_id', $id)->get();
                $clientContacts = ClientContact::where('client_id', $id)->get();
                $emails = ClientEmail::where('client_id', $id)->get() ?? [];
                $qualifications = ClientQualification::where('client_id', $id)->get() ?? [];
                $experiences = ClientExperience::where('client_id', $id)->get() ?? [];
                $testScores = ClientTestScore::where('client_id', $id)->get() ?? [];
                $visaCountries = ClientVisaCountry::where('client_id', $id)->get() ?? [];
                $clientSpouseDetail = ClientSpouseDetail::where('client_id', $id)->get();
                $clientOccupations = ClientOccupation::where('client_id', $id)->get();
                $ClientPoints = ClientPoint::where('client_id', $id)->get();
                $clientFamilyDetails = ClientRelationship::where('client_id', $id)->get() ?? [];

                // Return the summary view with all data
                return view('Admin.clients.summary', compact(
                    'fetchedData', 'clientAddresses', 'clientContacts', 'emails', 'qualifications',
                    'experiences', 'testScores', 'visaCountries', 'clientOccupations', 'ClientPoints', 
                    'clientSpouseDetail', 'encodeId', 'clientFamilyDetails'
                ));
            } else {
                return redirect('/admin/clients')->with('error', 'Client does not exist');
            }
        } else {
            return redirect('/admin/clients')->with('error', 'Invalid client ID');
        }
    }

    //Convert activity to note
	public function convertActivityToNote(Request $request){
		try {
			// Validate request
			$request->validate([
				'activity_id' => 'required|integer',
				'client_id' => 'required|integer',
				'client_matter_id' => 'required|integer',
				'note_type' => 'required|string'
			]);

			// Get the activity details
			$activity = \App\Models\ActivitiesLog::find($request->activity_id);
			if (!$activity) {
				return response()->json([
					'success' => false,
					'message' => 'Activity not found'
				]);
			}

			// Check if client matter exists
			$clientMatter = \App\Models\ClientMatter::find($request->client_matter_id);
			if (!$clientMatter) {
				return response()->json([
					'success' => false,
					'message' => 'Client matter not found'
				]);
			}

			// Create new note
            $note = new \App\Models\Note;
            $note->client_id = $request->client_id;
            $note->user_id = Auth::user()->id;
            $note->title = 'Matter Discussion';
            $note->description = $request->note_description; // Use processed description
            $note->matter_id = $request->client_matter_id;
            $note->type = 'client';
            $note->task_group = $request->note_type;
            $note->status = 1;
			
			$saved = $note->save();

			if ($saved) {
				// Create activity log for the conversion
				$activityLog = new \App\Models\ActivitiesLog;
				$activityLog->client_id = $request->client_id;
				$activityLog->created_by = Auth::user()->id;
				$activityLog->description = '<span class="text-semi-bold">Activity Converted to Note</span><p>Activity "' . $activity->subject . '" has been converted to a note.</p>';
				$activityLog->subject = 'converted activity to note';
				$activityLog->save();

				// Update client matter timestamp
				$clientMatter->updated_at = date('Y-m-d H:i:s');
				$clientMatter->save();

				return response()->json([
					'success' => true,
					'message' => 'Activity successfully converted to note'
				]);
			} else {
				return response()->json([
					'success' => false,
					'message' => 'Failed to save note'
				]);
			}

		} catch (\Exception $e) {
			\Log::error('Error converting activity to note: ' . $e->getMessage());
			return response()->json([
				'success' => false,
				'message' => 'An error occurred while converting activity to note'
			]);
		}
	}
	
	//Get client matters for activity conversion
	public function getClientMatters($clientId){
		try {
			$clientMatters = DB::table('client_matters')
				->leftJoin('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
				->select('client_matters.id', 'client_matters.client_unique_matter_no', 'matters.title')
				->where('client_matters.matter_status', 1)
				->where('client_matters.client_id', $clientId)
				->where(function($query) {
					$query->where('client_matters.sel_matter_id', '!=', 1)
						->orWhere('client_matters.client_unique_matter_no', 'like', 'GN_%');
				})
				->orderBy('client_matters.id', 'desc')
				->get();
			
			$matters = [];
			foreach($clientMatters as $matter){
				$displayName = $matter->title ? $matter->title . ' - ' . $matter->client_unique_matter_no : $matter->client_unique_matter_no;
				$matters[] = [
					'id' => $matter->id,
					'display_name' => $displayName,
					'client_unique_matter_no' => $matter->client_unique_matter_no
				];
			}
			
			return response()->json([
				'success' => true,
				'matters' => $matters
			]);
			
		} catch (\Exception $e) {
			\Log::error('Error fetching client matters: ' . $e->getMessage());
			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching client matters'
			]);
		}
	}

     /**
     * Toggle Client Portal Status and Send Email
     */
    public function toggleClientPortal(Request $request)
    {
        try {
            $request->validate([
                'client_id' => 'required|integer',
                'status' => 'required|in:true,false,1,0'
            ]);

            $clientId = $request->client_id;
            $status = ($request->status === true || $request->status === 'true' || $request->status === '1' || $request->status === 1) ? 1 : 0;

            // Update the client's cp_status
            $client = \App\Models\Admin::where('id', $clientId)->where('role', '7')->first();

            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found'
                ], 404);
            }

            $client->cp_status = $status;
            
            // Handle password based on status
            if ($status == 1) {
                // Generate and save password when activating client portal
                $randomPassword = Str::random(12);
                $hashedPassword = Hash::make($randomPassword);
                $client->password = $hashedPassword;
            } else {
                // Clear password when deactivating client portal for security
                $client->password = null;
            }
            
            $client->save();

            // Send appropriate email based on status change
            if ($status == 1) {
                // Status is being turned ON - send activation email with password
                $this->sendClientPortalActivationEmail($client, $randomPassword);
            } else {
                // Status is being turned OFF - send deactivation email
                $this->sendClientPortalDeactivationEmail($client);
            }

            return response()->json([
                'success' => true,
                'message' => $status ? 'Client Portal activated and email sent successfully' : 'Client Portal deactivated and email sent successfully',
                'status' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating client portal status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send Client Portal activation email
     */
    private function sendClientPortalActivationEmail($client, $password)
    {
        try {
            // Get client's email directly from admins table
            $emailAddress = $client->email;

            if (!$emailAddress) {
                throw new \Exception('No email address found for client');
            }

            // Email content
            $subject = 'Client Portal Access Activated - Bansal Immigration';
            $message = "
                <html>
                <body>
                    <h2>Client Portal Access Activated</h2>
                    <p>Dear {$client->first_name} {$client->last_name},</p>
                    <p>Your client portal has been activated successfully. Below are your login credentials:</p>
                    <p><strong>Email:</strong> {$client->email}</p>
                    <p><strong>Password:</strong> {$password}</p>
                    <p>You can now access your client portal using the mobile app with these credentials to view your case details.</p>
                    <p>Download the mobile app from the following link: <a href='https://play.google.com/store/apps/details?id=com.bansalimmigration.clientportal'>https://play.google.com/store/apps/details?id=com.bansalimmigration.clientportal</a></p>
                    <p><strong>Important:</strong> Please keep your login credentials secure and do not share them with anyone. After Login in mboile App you can chnage your password.</p>
                    <p>Please contact us if you have any questions.</p>
                    <br>
                    <p>Best regards,<br>Bansal Immigration Team</p>
                </body>
                </html>
            ";

            // Send email using Mail facade
            Mail::send('emails.client_portal_active_email', ['content' => $message], function($mail) use ($emailAddress, $subject) {
                $mail->to($emailAddress)
                     ->subject($subject)
                     ->from(config('mail.from.address'), config('mail.from.name'));
            });

        } catch (\Exception $e) {
            \Log::error('Failed to send client portal activation email: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send Client Portal deactivation email
     */
    private function sendClientPortalDeactivationEmail($client)
    {
        try {
            // Get client's email directly from admins table
            $emailAddress = $client->email;

            if (!$emailAddress) {
                throw new \Exception('No email address found for client');
            }

            // Email content for deactivation
            $subject = 'Client Portal Access Deactivated - Bansal Immigration';
            $message = "
                <html>
                <body>
                    <h2>Client Portal Access Deactivated</h2>
                    <p>Dear {$client->first_name} {$client->last_name},</p>
                    <p>Your client portal access has been deactivated. Now you cannot access the client portal from mobile app.</p>
                    <p>Please contact the administrator for further assistance.</p>
                    <br>
                    <p>Best regards,<br>Bansal Immigration Team</p>
                </body>
                </html>
            ";

            // Send email using Mail facade
            Mail::send('emails.client_portal_active_email', ['content' => $message], function($mail) use ($emailAddress, $subject) {
                $mail->to($emailAddress)
                     ->subject($subject)
                     ->from(config('mail.from.address'), config('mail.from.name'));
            });

        } catch (\Exception $e) {
            \Log::error('Failed to send client portal deactivation email: ' . $e->getMessage());
            throw $e;
        }
    }

}