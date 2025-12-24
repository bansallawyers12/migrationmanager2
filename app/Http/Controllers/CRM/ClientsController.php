<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use App\Models\Admin;
use App\Models\Lead;
use App\Models\ActivitiesLog;
use App\Models\OnlineForm;
use Auth;
use PDF;
use App\Models\CheckinLog;
use App\Models\Note;
use App\Models\BookingAppointment;
use App\Models\clientServiceTaken;
use App\Models\AccountClientReceipt;

use App\Models\Matter;
use App\Models\ClientMatter;
use App\Models\Branch;

use App\Models\FileStatus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Services\ClientReferenceService;

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

use Illuminate\Support\Facades\Http;

use App\Models\Form956;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\CostAssignmentForm;
use App\Models\PersonalDocumentType;
use App\Models\VisaDocumentType;
use App\Models\ClientEoiReference;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use App\Mail\HubdocInvoiceMail;
use App\Services\Sms\UnifiedSmsManager;
use App\Traits\ClientAuthorization;
use App\Traits\ClientHelpers;
use App\Traits\ClientQueries;
use App\Traits\LogsClientActivity;

class ClientsController extends Controller
{
    use ClientAuthorization, ClientHelpers, ClientQueries, LogsClientActivity;
    
    protected $openAiClient;
    protected $smsManager;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UnifiedSmsManager $smsManager)
    {
        $this->middleware('auth:admin');
        $this->smsManager = $smsManager;

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
	{
		// Check authorization using trait
		if ($this->hasModuleAccess('20')) {
		    $query = $this->getBaseClientQuery();
            $totalData = $query->count();
            
            // Apply filters using trait
            $query = $this->applyClientFilters($query, $request);

            $allowedPerPage = [10, 20, 50, 100, 200];
            $perPage = (int) $request->get('per_page', 20);
            if (!in_array($perPage, $allowedPerPage, true)) {
                $perPage = 20;
            }
            
            $lists = $query->sortable(['id' => 'desc'])
                ->paginate($perPage)
                ->appends($request->except('page'));
		} else {
		    $query = $this->getEmptyClientQuery();
            $allowedPerPage = [10, 20, 50, 100, 200];
            $perPage = (int) $request->get('per_page', 20);
            if (!in_array($perPage, $allowedPerPage, true)) {
                $perPage = 20;
            }
		    $lists = $query->sortable(['id' => 'desc'])->paginate($perPage);
		    $totalData = 0;
		}
		
		return view('crm.clients.index', compact(['lists', 'totalData', 'perPage']));
    }

    public function clientsmatterslist(Request $request)
    {
        // Check authorization using trait
        $teamMembers = collect();
        if ($this->hasModuleAccess('20')) {
            $sortField = $request->get('sort', 'cm.id');
            $sortDirection = $request->get('direction', 'desc');

            $query = DB::table('client_matters as cm')
            ->join('admins as ad', 'cm.client_id', '=', 'ad.id')
            ->join('matters as ma', 'ma.id', '=', 'cm.sel_matter_id')
            ->select('cm.*', 'ad.client_id as client_unique_id','ad.first_name','ad.last_name','ad.email','ma.title','ma.nick_name','ad.dob')
            ->where('cm.matter_status', '=', '1')
            ->where('ad.is_archived', '=', '0')
            ->where('ad.role', '=', '7')
            ->whereNull('ad.is_deleted');

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
                    $nameLower = strtolower($name);
                    $query->where(function ($q) use ($nameLower) {
                        $q->whereRaw('LOWER(ad.first_name) LIKE ?', ['%' . $nameLower . '%'])
                          ->orWhereRaw('LOWER(ad.last_name) LIKE ?', ['%' . $nameLower . '%'])
                          ->orWhereRaw("LOWER(COALESCE(ad.first_name, '') || ' ' || COALESCE(ad.last_name, '')) LIKE ?", ['%' . $nameLower . '%']);
                    });
                }
            }

            if ($request->filled('sel_migration_agent')) {
                $query->where('cm.sel_migration_agent', '=', $request->input('sel_migration_agent'));
            }

            if ($request->filled('sel_person_responsible')) {
                $query->where('cm.sel_person_responsible', '=', $request->input('sel_person_responsible'));
            }

            if ($request->filled('sel_person_assisting')) {
                $query->where('cm.sel_person_assisting', '=', $request->input('sel_person_assisting'));
            }

            if (
                $request->filled('quick_date_range') ||
                $request->filled('from_date') ||
                $request->filled('to_date')
            ) {
                [$startDate, $endDate] = $this->resolveClientDateRange($request);
                $dateField = $request->input('date_filter_field', 'created_at') === 'updated_at'
                    ? 'cm.updated_at'
                    : 'cm.created_at';

                if ($startDate && $endDate) {
                    $query->whereBetween($dateField, [$startDate, $endDate]);
                }
            }

            // Count AFTER all filters are applied, BEFORE orderBy
            $totalData = $query->count();

            // Apply orderBy AFTER count for pagination
            $query->orderBy($sortField, $sortDirection);

            $allowedPerPage = [10, 20, 50, 100, 200];
            $perPage = (int) $request->get('per_page', 20);
            if (!in_array($perPage, $allowedPerPage, true)) {
                $perPage = 20;
            }

            $teamMembers = Admin::where('role', '!=', '7')
                ->whereNull('is_deleted')
                ->orderBy('first_name', 'asc')
                ->select('id', 'first_name', 'last_name')
                ->get();

            $lists = $query->paginate($perPage)->appends($request->except('page'));
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
            $allowedPerPage = [10, 20, 50, 100, 200];
            $perPage = (int) $request->get('per_page', 20);
            if (!in_array($perPage, $allowedPerPage, true)) {
                $perPage = 20;
            }
            $totalData = 0;
            $lists = $query->paginate($perPage);
        }
        //dd( $lists);
        return view('crm.clients.clientsmatterslist', compact(['lists', 'totalData', 'teamMembers', 'perPage']));
    }

    public function insights(Request $request)
    {
        $section = $request->input('section', 'clients');
        $now = Carbon::now();

        // Client metrics
        $clientBaseQuery = $this->getBaseClientQuery();
        $clientStats = [
            'total' => (clone $clientBaseQuery)->count(),
            'new30' => (clone $clientBaseQuery)->where('created_at', '>=', $now->copy()->subDays(30))->count(),
            'inactive' => (clone $clientBaseQuery)->where('status', 0)->count(),
            'archived' => Admin::where('is_archived', 1)
                ->where('role', 7)
                ->where('type', 'client')
                ->whereNull('is_deleted')
                ->count(),
        ];

        $clientStatusBreakdown = (clone $clientBaseQuery)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(function ($row) {
                $row->label = ((int) $row->status === 1) ? 'Active' : 'Inactive';
                return $row;
            });

        $clientMonthlyGrowth = (clone $clientBaseQuery)
            ->select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as sort_key"),
                DB::raw("TO_CHAR(created_at, 'Mon YYYY') as label"),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', $now->copy()->subMonths(5)->startOfMonth())
            ->groupBy('sort_key', 'label')
            ->orderBy('sort_key')
            ->get();

        $recentClients = (clone $clientBaseQuery)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'first_name', 'last_name', 'client_id', 'created_at', 'status']);

        // Matter metrics
        $matterBase = DB::table('client_matters as cm')->where('cm.matter_status', 1);
        $matterStats = [
            'total' => (clone $matterBase)->count(),
            'new30' => (clone $matterBase)->where('cm.created_at', '>=', $now->copy()->subDays(30))->count(),
            'assigned' => (clone $matterBase)->whereNotNull('cm.sel_migration_agent')->count(),
        ];

        $mattersByAgent = DB::table('client_matters as cm')
            ->leftJoin('admins as agent', 'agent.id', '=', 'cm.sel_migration_agent')
            ->select(
                DB::raw("COALESCE(agent.first_name || ' ' || agent.last_name, 'Unassigned') as agent_name"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('agent_name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $recentMatters = DB::table('client_matters as cm')
            ->join('admins as client', 'client.id', '=', 'cm.client_id')
            ->leftJoin('admins as agent', 'agent.id', '=', 'cm.sel_migration_agent')
            ->select(
                'cm.client_unique_matter_no',
                'cm.created_at',
                'client.first_name as client_first_name',
                'client.last_name as client_last_name',
                'agent.first_name as agent_first_name',
                'agent.last_name as agent_last_name'
            )
            ->orderByDesc('cm.created_at')
            ->limit(5)
            ->get();

        // Lead metrics
        $leadBase = Lead::query();
        $leadStats = [
            'total' => (clone $leadBase)->count(),
            'new30' => (clone $leadBase)->where('created_at', '>=', $now->copy()->subDays(30))->count(),
            'assigned' => (clone $leadBase)->whereNotNull('assignee')->count(),
        ];

        $leadsByStatus = (clone $leadBase)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        $leadsByQuality = (clone $leadBase)
            ->select('lead_quality', DB::raw('COUNT(*) as total'))
            ->groupBy('lead_quality')
            ->orderByDesc('total')
            ->get();

        $leadMonthlyGrowth = (clone $leadBase)
            ->select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as sort_key"),
                DB::raw("TO_CHAR(created_at, 'Mon YYYY') as label"),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', $now->copy()->subMonths(5)->startOfMonth())
            ->groupBy('sort_key', 'label')
            ->orderBy('sort_key')
            ->get();

        $recentLeads = (clone $leadBase)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['first_name', 'last_name', 'service', 'status', 'lead_quality', 'created_at']);

        return view('crm.clients.insights', [
            'section' => $section,
            'clientStats' => $clientStats,
            'clientStatusBreakdown' => $clientStatusBreakdown,
            'clientMonthlyGrowth' => $clientMonthlyGrowth,
            'recentClients' => $recentClients,
            'matterStats' => $matterStats,
            'mattersByAgent' => $mattersByAgent,
            'recentMatters' => $recentMatters,
            'leadStats' => $leadStats,
            'leadsByStatus' => $leadsByStatus,
            'leadsByQuality' => $leadsByQuality,
            'leadMonthlyGrowth' => $leadMonthlyGrowth,
            'recentLeads' => $recentLeads,
        ]);
    }

    public function clientsemaillist(Request $request)
    {
        // Check authorization using trait
        if ($this->hasModuleAccess('20')) {
            $sortField = $request->get('sort', 'id');
            $sortDirection = $request->get('direction', 'desc');

            $query = Admin::where('is_archived', '=', '0')
                ->where('role', '=', 7)
                ->where('type', '=', 'client')
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->whereNull('is_deleted')
                ->orderBy($sortField, $sortDirection);

            $totalData = $query->count();

            if ($request->has('client_id')) {
                $client_id = $request->input('client_id');
                if(trim($client_id) != '') {
                    $query->where('client_id', '=', $client_id);
                }
            }

            if ($request->has('name')) {
                $name = trim($request->input('name'));
                if ($name != '') {
                    $nameLower = strtolower($name);
                    $query->where(function ($q) use ($nameLower) {
                        $q->whereRaw('LOWER(first_name) LIKE ?', ['%' . $nameLower . '%'])
                          ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . $nameLower . '%'])
                          ->orWhereRaw("LOWER(COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) LIKE ?", ['%' . $nameLower . '%']);
                    });
                }
            }

            if ($request->has('email')) {
                $email = $request->input('email');
                if(trim($email) != '') {
                    $query->where('email', 'LIKE', '%' . $email . '%');
                }
            }

            $lists = $query->paginate(20);
        } else {
            $query = Admin::where('id', '=', '')->where('role', '=', 7)->whereNull('is_deleted');
            $lists = $query->sortable(['id' => 'desc'])->paginate(20);
            $totalData = 0;
        }
        
        return view('crm.clients.clientsemaillist', compact(['lists', 'totalData']));
    }

    public function archived(Request $request)
	{
		$query 		= Admin::where('is_archived', '=', '1')->where('role', '=', 7);
        $totalData 	= $query->count();	//for all data
        $lists		= $query->sortable(['id' => 'desc'])->paginate(20);
        return view('crm.archived.index', compact(['lists', 'totalData']));
    }

	// REMOVED - prospects method
	// public function prospects(Request $request)
	// {
    //     return view('crm.prospects.index');
    // }

	public function create(Request $request)
	{
		return view('crm.clients.create');
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
                'marital_status' => 'nullable|in:Single,Married,De Facto,Divorced,Widowed,Separated',

                'phone_verified' => 'nullable|in:1',
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
                'partner_relationship_type.*' => 'nullable|in:Husband,Wife,Ex-Husband,Ex-Wife,Defacto',
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
                'parent_relationship_type.*' => 'nullable|in:Father,Mother,Step Father,Step Mother,Mother-in-law,Father-in-law',
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
            // Generate client_counter and client_id using centralized service
            // This prevents race conditions and duplicate references
            $referenceService = app(ClientReferenceService::class);
            $reference = $referenceService->generateClientReference($validated['first_name']);
            $client_id = $reference['client_id'];
            $client_current_counter = $reference['client_counter'];

            // Create the main client/lead record in the admins table
            // Use Lead model if type is 'lead', otherwise use Admin model for clients
            $client = ($validated['type'] === 'lead') ? new \App\Models\Lead() : new Admin();
            $client->first_name = $validated['first_name'];
            $client->last_name = $validated['last_name'] ?? null;
            $client->dob = $validated['dob'] ? date('Y-m-d', strtotime(str_replace('/', '-', $validated['dob']))) : null;

            $currentDateTime = \Carbon\Carbon::now();
            $currentUserId = Auth::user()->id;

            // DOB verification
            if (isset($validated['dob_verified']) && $validated['dob_verified'] === '1') {
                $client->dob_verified_date = $currentDateTime;
                $client->dob_verified_by = $currentUserId;
                
                // Recalculate age when DOB is verified (ensures age is current)
                if ($client->dob && $client->dob !== null) {
                    try {
                        $dobDate = \Carbon\Carbon::parse($client->dob);
                        $client->age = $dobDate->diff(\Carbon\Carbon::now())->format('%y years %m months');
                    } catch (\Exception $e) {
                        // If calculation fails, use provided age or keep existing
                        $client->age = $validated['age'] ?? null;
                    }
                } else {
                    $client->age = $validated['age'] ?? null;
                }
            } else {
                $client->dob_verified_date = null;
                $client->dob_verified_by = null;
                $client->age = $validated['age'] ?? null;
            }
            $client->gender = $validated['gender'] ?? null;
            $client->marital_status = $validated['marital_status'] ?? null;
            $client->country_passport = $validated['visa_country'][0] ?? null;
            $client->client_counter = $client_current_counter;
            $client->client_id = $client_id;
            $client->role = 7;
            $client->email = $modifiedEmail;
            $client->email_type = $lastEmailType ?? null;


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
                            'is_verified' => false,
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
                            'is_verified' => false,
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
                
                // Log activity for travel history creation
                $newTravels = ClientTravelInformation::where('client_id', $client->id)->get();
                $travelDisplay = [];
                foreach ($newTravels as $travel) {
                    $display = [];
                    if ($travel->travel_country_visited) {
                        $display[] = 'Country: ' . $travel->travel_country_visited;
                    }
                    if ($travel->travel_arrival_date) {
                        $display[] = 'Arrival: ' . date('d/m/Y', strtotime($travel->travel_arrival_date));
                    }
                    if ($travel->travel_departure_date) {
                        $display[] = 'Departure: ' . date('d/m/Y', strtotime($travel->travel_departure_date));
                    }
                    if ($travel->travel_purpose) {
                        $display[] = 'Purpose: ' . $travel->travel_purpose;
                    }
                    $travelDisplay[] = !empty($display) ? implode(', ', $display) : 'Travel record';
                }
                $travelDisplayStr = !empty($travelDisplay) ? implode(' | ', $travelDisplay) : '(empty)';
                
                $this->logClientActivityWithChanges(
                    $client->id,
                    'added travel information',
                    ['Travel Information' => [
                        'old' => '(empty)',
                        'new' => $travelDisplayStr
                    ]],
                    'activity'
                );
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
                            'specialist_education' => 0,
                            'stem_qualification' => 0,
                            'regional_study' => 0,
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
                            'fte_multiplier' => 1.00,
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
            if (isset($validated['marital_status']) && $validated['marital_status'] === 'Married') {
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
                'parent' => ['Father', 'Mother', 'Step Father', 'Step Mother', 'Mother-in-law', 'Father-in-law'],
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
                    case 'Mother-in-law':
                        return $relatedGender === 'Female' ? 'Daughter' : 'Son';
                    case 'Father-in-law':
                        return $relatedGender === 'Female' ? 'Daughter' : 'Son';
                    
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
                return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
            } else {
                return redirect()->route('clients.index')->with('success', 'Client created successfully.');
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

    // getNextCounter method moved to ClientHelpers trait

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

    public function edit($id)
    {
        // Check authorization (assumed to be handled elsewhere)
        if (isset($id) && !empty($id)) {
            $id = $this->decodeString($id);
            if (Admin::where('id', '=', $id)->where('role', '=', 7)->exists()) {
                
                // Use service to get all data with optimized queries (prevents N+1)
                $data = app(\App\Services\ClientEditService::class)->getClientEditData($id);
                
                return view('crm.clients.edit', $data);
            } else {
                return Redirect::to('/clients')->with('error', 'Client does not exist.');
            }
        } else {
            return Redirect::to('/clients')->with('error', config('constants.unauthorized'));
        }
    }

    public function update(Request $request)
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
                'marital_status' => 'nullable|in:Single,Married,De Facto,Divorced,Widowed,Separated',

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
                        // Use centralized validation
                        $validation = \App\Helpers\PhoneValidationHelper::validatePhoneNumber($value);
                        if (!$validation['valid']) {
                            $fail($validation['message']);
                            return;
                        }
                        
                        // Allow placeholder numbers to be duplicated
                        if ($validation['is_placeholder']) {
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
                'relationship_type.*' => 'nullable|in:Husband,Wife,Ex-Husband,Ex-Wife,Defacto',
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
                'parent_relationship_type.*' => 'nullable|in:Father,Mother,Step Father,Step Mother,Mother-in-law,Father-in-law',
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
            if ($requestData['marital_status'] === 'Married') {
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
                'parent_relationship_type.*.in' => 'Parent Relationship Type must be one of: Father, Mother, Step Father, Step Mother, Mother-in-law, Father-in-law.',
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
            $obj->marital_status = $requestData['marital_status'] ?? null;
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
                
                // Recalculate age when DOB is verified (ensures age is current)
                // This happens even if DOB hasn't changed, just verification status
                if ($obj->dob && $obj->dob !== null) {
                    try {
                        $dobDate = \Carbon\Carbon::parse($obj->dob);
                        $obj->age = $dobDate->diff(\Carbon\Carbon::now())->format('%y years %m months');
                    } catch (\Exception $e) {
                        // If calculation fails, keep existing age
                        \Log::warning("Failed to recalculate age for client {$obj->id} during DOB verification: " . $e->getMessage());
                    }
                }
            } else {
                $obj->dob_verified_date = null;
                $obj->dob_verified_by = null;
                // When DOB is not verified, clear the document field
                $obj->dob_verify_document = null;
            }

            // Email verification

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
                                    'country_code' => $country_code,
                                    'is_verified' => false
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

            // Initialize variables for emails
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
                                    'email' => $emailToSave,
                                    'is_verified' => false
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
                
                // Check if all passport data is being deleted
                $remainingPassports = ClientPassportInformation::where('client_id', $obj->id)->count();
                $newPassports = isset($requestData['passports']) ? count($requestData['passports']) : 0;
                
                // If no remaining passports and no new passports, clear the country_passport field
                if ($remainingPassports == 0 && $newPassports == 0) {
                    $obj->country_passport = null;
                    $obj->save();
                }
            }

            // Handle case when all passport information is deleted
            if (isset($requestData['clear_all_passports']) && $requestData['clear_all_passports'] === '1') {
                // Clear the country_passport field in admins table
                $obj->country_passport = null;
                $obj->save();
                
                // Delete all passport records for this client
                ClientPassportInformation::where('client_id', $obj->id)->delete();
            }

            // Passport Information Handling
            if (
                (isset($requestData['visa_country']) && !empty($requestData['visa_country'])) ||
                (isset($requestData['passports']) && is_array($requestData['passports'])) ||
                (isset($requestData['clear_all_passports']) && $requestData['clear_all_passports'] === '1')
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

            // Get existing travel records BEFORE any changes for activity logging
            $existingTravels = ClientTravelInformation::where('client_id', $obj->id)->get();
            $oldTravelDisplay = [];
            foreach ($existingTravels as $existing) {
                $display = [];
                if ($existing->travel_country_visited) {
                    $display[] = 'Country: ' . $existing->travel_country_visited;
                }
                if ($existing->travel_arrival_date) {
                    $display[] = 'Arrival: ' . date('d/m/Y', strtotime($existing->travel_arrival_date));
                }
                if ($existing->travel_departure_date) {
                    $display[] = 'Departure: ' . date('d/m/Y', strtotime($existing->travel_departure_date));
                }
                if ($existing->travel_purpose) {
                    $display[] = 'Purpose: ' . $existing->travel_purpose;
                }
                $oldTravelDisplay[] = !empty($display) ? implode(', ', $display) : 'Travel record';
            }
            $oldTravelDisplayStr = !empty($oldTravelDisplay) ? implode(' | ', $oldTravelDisplay) : '(empty)';

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

            // Get new travel records after changes for activity logging
            $newTravels = ClientTravelInformation::where('client_id', $obj->id)->get();
            $newTravelDisplay = [];
            foreach ($newTravels as $newTravel) {
                $display = [];
                if ($newTravel->travel_country_visited) {
                    $display[] = 'Country: ' . $newTravel->travel_country_visited;
                }
                if ($newTravel->travel_arrival_date) {
                    $display[] = 'Arrival: ' . date('d/m/Y', strtotime($newTravel->travel_arrival_date));
                }
                if ($newTravel->travel_departure_date) {
                    $display[] = 'Departure: ' . date('d/m/Y', strtotime($newTravel->travel_departure_date));
                }
                if ($newTravel->travel_purpose) {
                    $display[] = 'Purpose: ' . $newTravel->travel_purpose;
                }
                $newTravelDisplay[] = !empty($display) ? implode(', ', $display) : 'Travel record';
            }
            $newTravelDisplayStr = !empty($newTravelDisplay) ? implode(' | ', $newTravelDisplay) : '(empty)';

            // Log activity if travel information changed
            if ($oldTravelDisplayStr !== $newTravelDisplayStr) {
                $this->logClientActivityWithChanges(
                    $obj->id,
                    'updated travel information',
                    ['Travel Information' => [
                        'old' => $oldTravelDisplayStr,
                        'new' => $newTravelDisplayStr
                    ]],
                    'activity'
                );
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
                                'relevant_qualification' => $relevant_qualification,
                                'specialist_education' => 0,
                                'stem_qualification' => 0,
                                'regional_study' => 0
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
                                'job_type' => $job_type,
                                'fte_multiplier' => 1.00
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
            $testScoresChanged = false;
            if (isset($requestData['test_type_hidden']) && is_array($requestData['test_type_hidden'])) {
                $testScoresChanged = true;
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
                        // Calculate proficiency level using the service
                        $proficiencyService = new \App\Services\EnglishProficiencyService();
                        $scores = [
                            'listening' => $listening,
                            'reading' => $reading,
                            'writing' => $writing,
                            'speaking' => $speaking,
                            'overall' => $overallScore
                        ];
                        $proficiencyResult = $proficiencyService->calculateProficiency($testType, $scores, $formatted_test_date);

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
                            'proficiency_level' => $proficiencyResult['level'],
                            'proficiency_points' => $proficiencyResult['points'],
                            'relevant_test' => $relevant_test,
                            'test_reference_no' => $test_reference_no
                        ]);
                    }
                }
            }

            if ($testScoresChanged) {
                try {
                    (new \App\Services\PointsService())->clearCache($obj->id);
                } catch (\Throwable $th) {
                    \Log::warning('Failed to clear points cache after test score update', [
                        'client_id' => $obj->id,
                        'error' => $th->getMessage(),
                    ]);
                }
            }

            // Spouse Detail Handling
            if ($requestData['marital_status'] === 'Married') {
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
                'parent' => ['Father', 'Mother', 'Step Father', 'Step Mother', 'Mother-in-law', 'Father-in-law'],
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
                    case 'Mother-in-law':
                        return $relatedGender === 'Female' ? 'Daughter' : 'Son';
                    case 'Father-in-law':
                        return $relatedGender === 'Female' ? 'Daughter' : 'Son';
                    
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
                return redirect()->back()->with('error', config('constants.server_error'));
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
                ? '/clients/detail/'.$encodedId.'/'.$latestMatter->client_unique_matter_no
                : '/clients/detail/'.$encodedId;

            return Redirect::to($redirectUrl)->with('success',  ($requestData['type'] ?? 'Client') . ' edited successfully');
        }
    }




    public function saveSection(Request $request)
    {
        try {
            $section = $request->input('section');
            $clientId = $request->input('client_id');
            
            // Validate client exists
            $client = Admin::where('id', $clientId)->where('role', '7')->first();
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found'
                ], 404);
            }

            switch ($section) {
                case 'basicInfo':
                    return $this->saveBasicInfoSection($request, $client);
                case 'phoneNumbers':
                    return $this->savePhoneNumbersSection($request, $client);
                case 'emailAddresses':
                    return $this->saveEmailAddressesSection($request, $client);
                case 'passportInfo':
                    return $this->savePassportInfoSection($request, $client);
                case 'visaInfo':
                    return $this->saveVisaInfoSection($request, $client);
                case 'addressInfo':
                    return $this->saveAddressInfoSection($request, $client);
                case 'travelInfo':
                    return $this->saveTravelInfoSection($request, $client);
                case 'qualificationsInfo':
                    return $this->saveQualificationsInfoSection($request, $client);
                case 'experienceInfo':
                    return $this->saveExperienceInfoSection($request, $client);
                case 'additionalInfo':
                    return $this->saveAdditionalInfoSection($request, $client);
                case 'characterInfo':
                    return $this->saveCharacterInfoSection($request, $client);
                case 'partnerInfo':
                    return $this->savePartnerInfoSection($request, $client);
                case 'childrenInfo':
                    return $this->saveChildrenInfoSection($request, $client);
                case 'eoiInfo':
                    return $this->saveEoiInfoSection($request, $client);
                case 'test_scores':
                    return $this->saveTestScoreInfoSection($request, $client);
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid section specified'
                    ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveBasicInfoSection($request, $client)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|max:255',
                'last_name' => 'nullable|max:255',
                'client_id' => 'required|max:255|unique:admins,client_id,' . $client->id,
                'dob' => 'nullable|date_format:d/m/Y',
                'age' => 'nullable|string',
                'gender' => 'nullable|in:Male,Female,Other',
                'marital_status' => 'nullable|in:Single,Married,De Facto,Defacto,Divorced,Widowed,Separated'
            ]);

            // Convert DOB format and calculate age (like the working methods)
            $dob = null;
            $age = null;
            if (!empty($validated['dob'])) {
                try {
                    $dobDate = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['dob']);
                    $dob = $dobDate->format('Y-m-d');
                    $age = $dobDate->diff(\Carbon\Carbon::now())->format('%y years %m months');
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid date format. Must be dd/mm/yyyy.'
                    ], 422);
                }
            }

            // Map marital status values for backward compatibility
            $maritalStatus = $validated['marital_status'] ?? null;
            if ($maritalStatus === 'Defacto') {
                $maritalStatus = 'De Facto';
            }

            // Use direct assignment pattern (like the working old methods)
            $client->first_name = $validated['first_name'];
            $client->last_name = $validated['last_name'] ?? null;
            $client->client_id = $validated['client_id'];
            $client->dob = $dob;
            $client->age = $age;
            $client->gender = $validated['gender'] ?? null;
            $client->marital_status = $maritalStatus;
            $client->save();

            // Log activity for basic information update
            $this->logClientActivity(
                $client->id,
                'updated basic information',
                'Updated basic client information',
                'activity'
            );

            return response()->json([
                'success' => true,
                'message' => 'Basic information updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    private function savePhoneNumbersSection($request, $client)
    {
        try {
            $phoneNumbers = json_decode($request->input('phone_numbers'), true);
            
            if (!is_array($phoneNumbers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid phone numbers data'
                ], 400);
            }

            // Delete existing phone numbers for this client
            ClientContact::where('client_id', $client->id)->delete();

            // Insert new phone numbers
            foreach ($phoneNumbers as $phoneData) {
                if (!empty($phoneData['phone'])) {
                    ClientContact::create([
                        'client_id' => $client->id,
                        'contact_type' => $phoneData['contact_type'],
                        'country_code' => $phoneData['country_code'] ?? '',
                        'phone' => $phoneData['phone'],
                        'is_verified' => false
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Phone numbers updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving phone numbers: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveEmailAddressesSection($request, $client)
    {
        try {
            $emails = json_decode($request->input('emails'), true);
            
            if (!is_array($emails)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email addresses data'
                ], 400);
            }

            // Delete existing emails for this client
            ClientEmail::where('client_id', $client->id)->delete();

            // Insert new emails and update admins table
            $primaryEmail = null;
            $primaryEmailType = 'Personal';
            
            foreach ($emails as $emailData) {
                if (!empty($emailData['email'])) {
                    ClientEmail::create([
                        'client_id' => $client->id,
                        'email_type' => $emailData['email_type'],
                        'email' => $emailData['email'],
                        'is_verified' => false
                    ]);
                    
                    // Set primary email for admins table update
                    if ($emailData['email_type'] === 'Personal' || empty($primaryEmail)) {
                        $primaryEmail = $emailData['email'];
                        $primaryEmailType = $emailData['email_type'];
                    }
                }
            }
            
            // Update admins table with primary email
            if (!empty($primaryEmail)) {
                $client->email = $primaryEmail;
                $client->email_type = $primaryEmailType;
                $client->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Email addresses updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving email addresses: ' . $e->getMessage()
            ], 500);
        }
    }

    private function savePassportInfoSection($request, $client)
    {
        try {
            $passportCountry = $request->input('passport_country');
            $passports = json_decode($request->input('passports'), true);
            
            if (!is_array($passports)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid passport data'
                ], 400);
            }

            // Update client's passport country (column name is country_passport)
            $client->country_passport = $passportCountry;
            $client->save();

            // Delete existing passport records for this client
            ClientPassportInformation::where('client_id', $client->id)->delete();

            // Insert new passport records
            foreach ($passports as $passportData) {
                if (!empty($passportData['passport_number'])) {
                    // Convert date format from d/m/Y to Y-m-d if needed
                    $issueDate = null;
                    $expiryDate = null;
                    
                    if (!empty($passportData['issue_date'])) {
                        $issueDate = \DateTime::createFromFormat('d/m/Y', $passportData['issue_date']);
                        $issueDate = $issueDate ? $issueDate->format('Y-m-d') : null;
                    }
                    
                    if (!empty($passportData['expiry_date'])) {
                        $expiryDate = \DateTime::createFromFormat('d/m/Y', $passportData['expiry_date']);
                        $expiryDate = $expiryDate ? $expiryDate->format('Y-m-d') : null;
                    }
                    
                    ClientPassportInformation::create([
                        'client_id' => $client->id,
                        'admin_id' => \Auth::user()->id,
                        'passport_country' => $passportCountry,
                        'passport' => $passportData['passport_number'],
                        'passport_issue_date' => $issueDate,
                        'passport_expiry_date' => $expiryDate
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Passport information updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving passport information: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveVisaInfoSection($request, $client)
    {
        try {
            $visaExpiryVerified = $request->input('visa_expiry_verified');
            $visas = json_decode($request->input('visas'), true);
            
            if (!is_array($visas)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid visa data'
                ], 400);
            }

            // Update client's visa expiry verified status using existing system
            if ($visaExpiryVerified === '1') {
                $client->visa_expiry_verified_at = now();
                $client->visa_expiry_verified_by = \Auth::user()->id;
            } else {
                $client->visa_expiry_verified_at = null;
                $client->visa_expiry_verified_by = null;
            }
            $client->save();

            // Delete existing visa records for this client
            ClientVisaCountry::where('client_id', $client->id)->delete();

            // Insert new visa records
            foreach ($visas as $visaData) {
                if (!empty($visaData['visa_type_hidden'])) {
                    // Convert date format from d/m/Y to Y-m-d if needed
                    $expiryDate = null;
                    $grantDate = null;
                    
                    if (!empty($visaData['visa_expiry_date'])) {
                        $expiryDate = \DateTime::createFromFormat('d/m/Y', $visaData['visa_expiry_date']);
                        $expiryDate = $expiryDate ? $expiryDate->format('Y-m-d') : null;
                    }
                    
                    if (!empty($visaData['visa_grant_date'])) {
                        $grantDate = \DateTime::createFromFormat('d/m/Y', $visaData['visa_grant_date']);
                        $grantDate = $grantDate ? $grantDate->format('Y-m-d') : null;
                    }
                    
                    ClientVisaCountry::create([
                        'client_id' => $client->id,
                        'admin_id' => \Auth::user()->id,
                        'visa_country' => $client->country_passport ?? '',
                        'visa_type' => $visaData['visa_type_hidden'],
                        'visa_expiry_date' => $expiryDate,
                        'visa_grant_date' => $grantDate,
                        'visa_description' => $visaData['visa_description'] ?? null
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Visa information updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving visa information: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveAddressInfoSection($request, $client)
    {
        // For now, return success - implement address saving logic here
        return response()->json([
            'success' => true,
            'message' => 'Address information updated successfully'
        ]);
    }

    private function saveTravelInfoSection($request, $client)
    {
        // For now, return success - implement travel saving logic here
        return response()->json([
            'success' => true,
            'message' => 'Travel information updated successfully'
        ]);
    }

    private function saveQualificationsInfoSection($request, $client)
    {
        try {
            $requestData = $request->all();
            
            // Handle qualification deletion
            if (isset($requestData['delete_qualification_ids']) && is_array($requestData['delete_qualification_ids'])) {
                foreach ($requestData['delete_qualification_ids'] as $qualificationId) {
                    $qualification = ClientQualification::find($qualificationId);
                    if ($qualification && $qualification->client_id == $client->id) {
                        $qualification->delete();
                    }
                }
            }

            // Handle qualification data
            if (isset($requestData['level']) && is_array($requestData['level'])) {
                foreach ($requestData['level'] as $key => $level) {
                    $name = $requestData['name'][$key] ?? null;
                    $qual_college_name = $requestData['qual_college_name'][$key] ?? null;
                    $qual_campus = $requestData['qual_campus'][$key] ?? null;
                    $country = $requestData['qual_country'][$key] ?? null;
                    $qual_state = $requestData['qual_state'][$key] ?? null;
                    $start = $requestData['start_date'][$key] ?? null;
                    $finish = $requestData['finish_date'][$key] ?? null;
                    $relevant_qualification = isset($requestData['relevant_qualification'][$key]) && $requestData['relevant_qualification'][$key] == 1 ? 1 : 0;
                    $qualificationId = $requestData['qualification_id'][$key] ?? null;

                    // Convert start_date from dd/mm/yyyy to Y-m-d for database storage
                    $formatted_start_date = null;
                    if (!empty($start)) {
                        try {
                            $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $start);
                            $formatted_start_date = $startDate->format('Y-m-d');
                        } catch (\Exception $e) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Invalid Start Date format: ' . $start . '. Must be dd/mm/yyyy.'
                            ], 422);
                        }
                    }

                    // Convert finish_date from dd/mm/yyyy to Y-m-d for database storage
                    $formatted_finish_date = null;
                    if (!empty($finish)) {
                        try {
                            $finishDate = \Carbon\Carbon::createFromFormat('d/m/Y', $finish);
                            $formatted_finish_date = $finishDate->format('Y-m-d');
                        } catch (\Exception $e) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Invalid Finish Date format: ' . $finish . '. Must be dd/mm/yyyy.'
                            ], 422);
                        }
                    }

                    // Only save if there's at least level or name
                    if (!empty($level) || !empty($name)) {
                        if ($qualificationId) {
                            // Update existing qualification
                            $existingQualification = ClientQualification::find($qualificationId);
                            if ($existingQualification && $existingQualification->client_id == $client->id) {
                                $existingQualification->update([
                                    'admin_id' => Auth::user()->id,
                                    'level' => $level,
                                    'name' => $name,
                                    'qual_college_name' => $qual_college_name,
                                    'qual_campus' => $qual_campus,
                                    'country' => $country,
                                    'qual_state' => $qual_state,
                                    'start_date' => $formatted_start_date,
                                    'finish_date' => $formatted_finish_date,
                                    'relevant_qualification' => $relevant_qualification
                                ]);
                            }
                        } else {
                            // Create new qualification
                            ClientQualification::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $client->id,
                                'level' => $level,
                                'name' => $name,
                                'qual_college_name' => $qual_college_name,
                                'qual_campus' => $qual_campus,
                                'country' => $country,
                                'qual_state' => $qual_state,
                                'start_date' => $formatted_start_date,
                                'finish_date' => $formatted_finish_date,
                                'relevant_qualification' => $relevant_qualification,
                                'specialist_education' => 0,
                                'stem_qualification' => 0,
                                'regional_study' => 0
                            ]);
                        }
                    }
                }
            }

            // Update client's qualification_level and qualification_name with the most recent qualification
            if (isset($requestData['level']) && is_array($requestData['level'])) {
                $qualificationCount = count($requestData['level']);
                if ($qualificationCount > 0) {
                    $levelArray = array_values($requestData['level']);
                    $nameArray = array_values($requestData['name']);
                    
                    $lastLevel = $levelArray[$qualificationCount - 1] ?? null;
                    $lastName = $nameArray[$qualificationCount - 1] ?? null;

                    if (!empty($lastLevel) || !empty($lastName)) {
                        $client->qualification_level = $lastLevel;
                        $client->qualification_name = $lastName;
                        $client->save();
                    }
                }
            }

        return response()->json([
            'success' => true,
            'message' => 'Qualifications information updated successfully'
        ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save qualifications: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveExperienceInfoSection($request, $client)
    {
        // For now, return success - implement experience saving logic here
        return response()->json([
            'success' => true,
            'message' => 'Experience information updated successfully'
        ]);
    }

    private function saveAdditionalInfoSection($request, $client)
    {
        // For now, return success - implement additional info saving logic here
        return response()->json([
            'success' => true,
            'message' => 'Additional information updated successfully'
        ]);
    }

    private function saveCharacterInfoSection($request, $client)
    {
        // For now, return success - implement character saving logic here
        return response()->json([
            'success' => true,
            'message' => 'Character information updated successfully'
        ]);
    }

    private function savePartnerInfoSection($request, $client)
    {
        // For now, return success - implement partner saving logic here
        return response()->json([
            'success' => true,
            'message' => 'Partner information updated successfully'
        ]);
    }

    private function saveChildrenInfoSection($request, $client)
    {
        // For now, return success - implement children saving logic here
        return response()->json([
            'success' => true,
            'message' => 'Children information updated successfully'
        ]);
    }

    private function saveEoiInfoSection($request, $client)
    {
        // For now, return success - implement EOI saving logic here
        return response()->json([
            'success' => true,
            'message' => 'EOI reference information updated successfully'
        ]);
    }


    private function saveTestScoreInfoSection($request, $client)
    {
        try {
            $requestData = $request->all();
            
            // Handle test score deletion
            if (isset($requestData['delete_test_score_ids']) && is_array($requestData['delete_test_score_ids'])) {
                foreach ($requestData['delete_test_score_ids'] as $testScoreId) {
                    $testScore = \App\Models\ClientTestScore::find($testScoreId);
                    if ($testScore && $testScore->client_id == $client->id) {
                        $testScore->delete();
                    }
                }
            }

            // Handle test score data
            if (isset($requestData['test_type_hidden']) && is_array($requestData['test_type_hidden'])) {
                foreach ($requestData['test_type_hidden'] as $key => $testType) {
                    if (!empty($testType)) {
                        $testScoreId = $requestData['test_score_id'][$key] ?? null;
                        $listening = $requestData['listening'][$key] ?? null;
                        $reading = $requestData['reading'][$key] ?? null;
                        $writing = $requestData['writing'][$key] ?? null;
                        $speaking = $requestData['speaking'][$key] ?? null;
                        $overallScore = $requestData['overall_score'][$key] ?? null;
                        $testDate = $requestData['test_date'][$key] ?? null;
                        $testReferenceNo = $requestData['test_reference_no'][$key] ?? null;
                        $relevantTest = isset($requestData['relevant_test_hidden'][$key]) && $requestData['relevant_test_hidden'][$key] === '1' ? 1 : 0;

                        // Convert test_date from dd/mm/yyyy to Y-m-d for database storage
                        $formattedTestDate = null;
                        if (!empty($testDate)) {
                            try {
                                $dateObj = \Carbon\Carbon::createFromFormat('d/m/Y', $testDate);
                                $formattedTestDate = $dateObj->format('Y-m-d');
                            } catch (\Exception $e) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Invalid Test Date format: ' . $testDate
                                ], 422);
                            }
                        }

                        if ($testScoreId) {
                            // Update existing record
                            $existingTestScore = \App\Models\ClientTestScore::find($testScoreId);
                            if ($existingTestScore && $existingTestScore->client_id == $client->id) {
                                $existingTestScore->update([
                                    'admin_id' => Auth::user()->id,
                                    'test_type' => $testType,
                                    'listening' => $listening,
                                    'reading' => $reading,
                                    'writing' => $writing,
                                    'speaking' => $speaking,
                                    'overall_score' => $overallScore,
                                    'test_date' => $formattedTestDate,
                                    'test_reference_no' => $testReferenceNo,
                                    'relevant_test' => $relevantTest
                                ]);
                            }
                        } else {
                            // Create new record
                            \App\Models\ClientTestScore::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $client->id,
                                'test_type' => $testType,
                                'listening' => $listening,
                                'reading' => $reading,
                                'writing' => $writing,
                                'speaking' => $speaking,
                                'overall_score' => $overallScore,
                                'test_date' => $formattedTestDate,
                                'test_reference_no' => $testReferenceNo,
                                'relevant_test' => $relevantTest
                            ]);
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Test score information saved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving test score information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle legacy test scores form submission (old format with band_score fields)
     * Converts legacy format to new ClientTestScore structure
     */
    public function editTestScores(Request $request)
    {
        try {
            $requestData = $request->all();
            $clientId = $requestData['client_id'] ?? null;
            
            if (!$clientId) {
                return redirect()->back()->withErrors(['error' => 'Client ID is required'])->withInput();
            }

            // Verify client exists
            $client = Admin::find($clientId);
            if (!$client || $client->role != 7) {
                return redirect()->back()->withErrors(['error' => 'Client not found'])->withInput();
            }

            // Delete existing TOEFL, IELTS, and PTE test scores for this client (only the ones handled by this legacy form)
            ClientTestScore::where('client_id', $clientId)
                ->whereIn('test_type', ['TOEFL', 'IELTS', 'PTE'])
                ->delete();

            // Process TOEFL scores
            if (!empty($requestData['band_score_1_1']) || !empty($requestData['band_score_2_1']) || 
                !empty($requestData['band_score_3_1']) || !empty($requestData['band_score_4_1']) || 
                !empty($requestData['score_1'])) {
                
                $testDate = $requestData['band_score_5_1'] ?? null;
                $formattedDate = null;
                if (!empty($testDate)) {
                    try {
                        $dateObj = \Carbon\Carbon::createFromFormat('d/m/Y', $testDate);
                        $formattedDate = $dateObj->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Invalid date format, skip
                    }
                }

                if (!empty($requestData['band_score_1_1']) || !empty($requestData['band_score_2_1']) || 
                    !empty($requestData['band_score_3_1']) || !empty($requestData['band_score_4_1']) || 
                    !empty($requestData['score_1'])) {
                    ClientTestScore::create([
                        'admin_id' => Auth::user()->id,
                        'client_id' => $clientId,
                        'test_type' => 'TOEFL',
                        'listening' => $requestData['band_score_1_1'] ?? null,
                        'reading' => $requestData['band_score_2_1'] ?? null,
                        'writing' => $requestData['band_score_3_1'] ?? null,
                        'speaking' => $requestData['band_score_4_1'] ?? null,
                        'overall_score' => $requestData['score_1'] ?? null,
                        'test_date' => $formattedDate,
                        'relevant_test' => 1
                    ]);
                }
            }

            // Process IELTS scores
            if (!empty($requestData['band_score_5_2']) || !empty($requestData['band_score_6_2']) || 
                !empty($requestData['band_score_7_2']) || !empty($requestData['band_score_8_2']) || 
                !empty($requestData['score_2'])) {
                
                $testDate = $requestData['band_score_6_1'] ?? null;
                $formattedDate = null;
                if (!empty($testDate)) {
                    try {
                        $dateObj = \Carbon\Carbon::createFromFormat('d/m/Y', $testDate);
                        $formattedDate = $dateObj->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Invalid date format, skip
                    }
                }

                if (!empty($requestData['band_score_5_2']) || !empty($requestData['band_score_6_2']) || 
                    !empty($requestData['band_score_7_2']) || !empty($requestData['band_score_8_2']) || 
                    !empty($requestData['score_2'])) {
                    ClientTestScore::create([
                        'admin_id' => Auth::user()->id,
                        'client_id' => $clientId,
                        'test_type' => 'IELTS',
                        'listening' => $requestData['band_score_5_2'] ?? null,
                        'reading' => $requestData['band_score_6_2'] ?? null,
                        'writing' => $requestData['band_score_7_2'] ?? null,
                        'speaking' => $requestData['band_score_8_2'] ?? null,
                        'overall_score' => $requestData['score_2'] ?? null,
                        'test_date' => $formattedDate,
                        'relevant_test' => 1
                    ]);
                }
            }

            // Process PTE scores
            if (!empty($requestData['band_score_9_3']) || !empty($requestData['band_score_10_3']) || 
                !empty($requestData['band_score_11_3']) || !empty($requestData['band_score_12_3']) || 
                !empty($requestData['score_3'])) {
                
                $testDate = $requestData['band_score_7_1'] ?? null;
                $formattedDate = null;
                if (!empty($testDate)) {
                    try {
                        $dateObj = \Carbon\Carbon::createFromFormat('d/m/Y', $testDate);
                        $formattedDate = $dateObj->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Invalid date format, skip
                    }
                }

                if (!empty($requestData['band_score_9_3']) || !empty($requestData['band_score_10_3']) || 
                    !empty($requestData['band_score_11_3']) || !empty($requestData['band_score_12_3']) || 
                    !empty($requestData['score_3'])) {
                    ClientTestScore::create([
                        'admin_id' => Auth::user()->id,
                        'client_id' => $clientId,
                        'test_type' => 'PTE',
                        'listening' => $requestData['band_score_9_3'] ?? null,
                        'reading' => $requestData['band_score_10_3'] ?? null,
                        'writing' => $requestData['band_score_11_3'] ?? null,
                        'speaking' => $requestData['band_score_12_3'] ?? null,
                        'overall_score' => $requestData['score_3'] ?? null,
                        'test_date' => $formattedDate,
                        'relevant_test' => 1
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Test scores updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error updating test scores: ' . $e->getMessage()])->withInput();
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

            if (Admin::where('id', '=', $id)->where('role', '=', 7)->exists()) {
                $fetchedData = Admin::find($id); //dd($fetchedData);


                //Fetch other client-related data
                $clientAddresses = ClientAddress::where('client_id', $id)->orderBy('created_at', 'desc')->get();
                $clientContacts = ClientContact::where('client_id', $id)->get();
                $emails = ClientEmail::where('client_id', $id)->get() ?? [];
                $qualifications = ClientQualification::where('client_id', $id)->orderByRaw('finish_date DESC NULLS LAST')->get() ?? [];
                $experiences = ClientExperience::where('client_id', $id)->orderByRaw('job_finish_date DESC NULLS LAST')->get() ?? [];
                $testScores = ClientTestScore::where('client_id', $id)->get() ?? [];
                $visaCountries = ClientVisaCountry::where('client_id', $id)->get() ?? [];
                $clientSpouseDetail = ClientSpouseDetail::where('client_id', $id)->get();
                $clientOccupations = ClientOccupation::where('client_id', $id)->get();
                $ClientPoints = ClientPoint::where('client_id', $id)->get();

                // Fetch client family details with optimized query
                // Eager load related client to prevent N+1 queries in the view
                $clientFamilyDetails = ClientRelationship::where('client_id', $id)
                    ->with(['relatedClient:id,first_name,last_name,client_id'])
                    ->get() ?? [];
                
                // Detect if current matter is EOI-related
                $isEoiMatter = false;
                if ($id1) {
                    // Check if the current matter is EOI
                    $currentMatter = DB::table('client_matters as cm')
                        ->join('matters as m', 'cm.sel_matter_id', '=', 'm.id')
                        ->where('cm.client_id', $id)
                        ->where('cm.client_unique_matter_no', $id1)
                        ->where('cm.matter_status', 1)
                        ->select('m.nick_name', 'm.title')
                        ->first();
                    
                    if ($currentMatter) {
                        $isEoiMatter = (
                            strtolower($currentMatter->nick_name) === 'eoi' ||
                            stripos($currentMatter->title, 'eoi') !== false ||
                            stripos($currentMatter->title, 'expression of interest') !== false ||
                            stripos($currentMatter->title, 'expression') !== false ||
                            stripos($currentMatter->title, 'interest') !== false
                        );
                    }
                } else {
                    // If no specific matter is selected, check if client has any EOI matter
                    $eoiMatterExists = DB::table('client_matters as cm')
                        ->join('matters as m', 'cm.sel_matter_id', '=', 'm.id')
                        ->where('cm.client_id', $id)
                        ->where('cm.matter_status', 1)
                        ->where(function($query) {
                            $query->where('m.nick_name', 'ILIKE', 'eoi')
                                  ->orWhere('m.title', 'LIKE', '%eoi%')
                                  ->orWhere('m.title', 'LIKE', '%expression of interest%')
                                  ->orWhere('m.title', 'LIKE', '%expression%');
                        })
                        ->exists();
                    
                    $isEoiMatter = $eoiMatterExists;
                }
                
                //dd($clientFamilyDetails);
                
                // Check and insert/update application record when Client Portal tab is accessed
                if ($tab === 'application' && $id1) {
                    // Get client_matter_id from client_unique_matter_ref_no (id1)
                    // Check all matters regardless of status
                    $clientMatter = DB::table('client_matters')
                        ->where('client_id', $id)
                        ->where('client_unique_matter_no', $id1)
                        ->first();
                    
                    if ($clientMatter) {
                        $clientMatterId = $clientMatter->id;
                        
                        // Get workflow and stage from client_matters table
                        $workflowStageInfo = DB::table('client_matters')
                            ->join('workflow_stages', 'client_matters.workflow_stage_id', '=', 'workflow_stages.id')
                            ->where('client_matters.id', $clientMatterId)
                            ->select('workflow_stages.w_id as workflow_id', 'workflow_stages.name as stage_name')
                            ->first();
                        
                        if ($workflowStageInfo) {
                            // Map matter_status to status (inverse mapping)
                            // If matter_status = 1 (active), then status = 0 (InProgress)
                            // If matter_status = 0 (inactive), then status = 1 (Completed)
                            $applicationStatus = ($clientMatter->matter_status == 1) ? 0 : 1;
                            
                            // Check if record exists in applications table
                            $existingApplication = DB::table('applications')
                                ->where('client_matter_id', $clientMatterId)
                                ->where('client_id', $id)
                                ->first();
                            
                            if (!$existingApplication) {
                                // Insert new record
                                DB::table('applications')->insert([
                                    'client_matter_id' => $clientMatterId,
                                    'client_id' => $id,
                                    'user_id' => Auth::user()->id,
                                    'workflow' => $workflowStageInfo->workflow_id,
                                    'stage' => $workflowStageInfo->stage_name,
                                    'status' => $applicationStatus,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                            } else {
                                // Update workflow, stage, and status columns
                                DB::table('applications')
                                    ->where('client_matter_id', $clientMatterId)
                                    ->where('client_id', $id)
                                    ->update([
                                        'workflow' => $workflowStageInfo->workflow_id,
                                        'stage' => $workflowStageInfo->stage_name,
                                        'status' => $applicationStatus,
                                        'updated_at' => now()
                                    ]);
                            }
                        }
                    }
                }
                
                // Get current admin user data for SMS templates
                $currentAdmin = Auth::user();
                $staffName = $currentAdmin->first_name . ' ' . $currentAdmin->last_name;
                $matterNumber = $id1 ?? '';
                $officePhone = $currentAdmin->phone ?? $currentAdmin->att_phone ?? '';
                $officeCountryCode = $currentAdmin->att_country_code ?? '+61';
                
                //Return the view with all data
                return view('crm.clients.detail', compact(
                    'fetchedData', 'clientAddresses', 'clientContacts', 'emails', 'qualifications',
                    'experiences', 'testScores', 'visaCountries', 'clientOccupations','ClientPoints', 'clientSpouseDetail',
                    'encodeId', 'id1','clientFamilyDetails', 'activeTab', 'isEoiMatter',
                    'staffName', 'matterNumber', 'officePhone', 'officeCountryCode'
                ));
            } else {
                return redirect()->route('clients.index')->with('error', 'Clients Not Exist');
            }
        } else {
            return redirect()->route('clients.index')->with('error', config('constants.unauthorized'));
        }
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
			 $squeryLower = strtolower($squery);
			 $clients = \App\Models\Admin::where('is_archived', '=', 0)
       ->where('role', '=', 7)
       ->where(
           function($query) use ($squeryLower) {
             return $query
                    ->whereRaw('LOWER(email) LIKE ?', ['%'.$squeryLower.'%'])
                    ->orWhereRaw('LOWER(first_name) LIKE ?', ['%'.$squeryLower.'%'])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', ['%'.$squeryLower.'%'])
                    ->orWhereRaw('LOWER(client_id) LIKE ?', ['%'.$squeryLower.'%'])
                    ->orWhereRaw('LOWER(phone) LIKE ?', ['%'.$squeryLower.'%'])
                    ->orWhereRaw("LOWER(COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) LIKE ?", ['%'.$squeryLower.'%']);
            })
            ->get();

            /* $leads = \App\Models\Lead::where('converted', '=', 0)
			->where(
			function($query) use ($squery,$d) {
				return $query
					->where('email', 'LIKE', '%'.$squery.'%')
					->orwhere('first_name', 'LIKE','%'.$squery.'%')->orwhere('last_name', 'LIKE','%'.$squery.'%')->orwhere('phone', 'LIKE','%'.$squery.'%')  ->orWhere(DB::raw("(COALESCE(first_name, '') || ' ' || COALESCE(last_name, ''))"), 'LIKE', "%".$squery."%");
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
             	$squeryLower = strtolower($squery);
             	return $query
                    ->whereRaw('LOWER(email) LIKE ?', ['%'.$squeryLower.'%'])
                    ->orWhereRaw('LOWER(first_name) LIKE ?', ['%'.$squeryLower.'%'])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', ['%'.$squeryLower.'%'])
                    ->orWhereRaw('LOWER(client_id) LIKE ?', ['%'.$squeryLower.'%'])
                    ->orWhereRaw('LOWER(phone) LIKE ?', ['%'.$squeryLower.'%'])
                    ->orWhereRaw("LOWER(COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) LIKE ?", ['%'.$squeryLower.'%']);
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

            $squeryLower = strtolower($squery);
            $clients = \App\Models\Admin::where('role', '=', 7)
                ->whereNull('is_deleted')
                ->where(function ($query) use ($squery, $squeryLower, $d) {
                    $query->orWhereRaw('LOWER(email) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(first_name) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(last_name) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(client_id) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(att_email) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(att_phone) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(phone) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw("LOWER(COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) LIKE ?", ["%$squeryLower%"]);
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
                    $squeryLower = strtolower($squery);
                    $query->orWhereRaw('LOWER(admins.email) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(admins.first_name) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(admins.last_name) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(admins.client_id) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(admins.att_email) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(admins.att_phone) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(admins.phone) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(admins.emergency_contact_no) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw("LOWER(COALESCE(admins.first_name, '') || ' ' || COALESCE(admins.last_name, '')) LIKE ?", ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(client_contacts.phone) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(client_emails.email) LIKE ?', ["%$squeryLower%"]);
                    if ($d != "") {
                        $query->orWhere('admins.dob', '=', $d);
                    }
                })
                ->select(
                    'admins.*',
                    DB::raw('STRING_AGG(DISTINCT client_contacts.phone, \', \' ORDER BY client_contacts.contact_type) as all_phones'),
                    DB::raw('STRING_AGG(DISTINCT client_emails.email, \', \' ORDER BY client_emails.email_type) as all_emails')
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
            
            // Log the search query for debugging
            \Log::info('Header search query: ' . $squery);

            /**
             * 1. Search for composite references (client_id + matter_no format like "SHAL2500295-JRP_1")
             * Optimized: Use JOIN instead of N+1 queries
             */
            if (strpos($squery, '-') !== false) {
                $parts = explode('-', $squery, 2);
                if (count($parts) == 2) {
                    $clientIdPart = $parts[0];
                    $matterNoPart = $parts[1];
                    
                    // Optimized: Single query with JOIN instead of nested loops
                    $clientIdPartLower = strtolower($clientIdPart);
                    $matterNoPartLower = strtolower($matterNoPart);
                    $matterResults = DB::table('admins')
                        ->join('client_matters', 'admins.id', '=', 'client_matters.client_id')
                        ->where('admins.role', 7)
                        ->whereNull('admins.is_deleted')
                        ->whereRaw('LOWER(admins.client_id) LIKE ?', ["%{$clientIdPartLower}%"])
                        ->whereRaw('LOWER(client_matters.client_unique_matter_no) LIKE ?', ["%{$matterNoPartLower}%"])
                        ->select(
                            'admins.id as client_id',
                            'admins.first_name',
                            'admins.last_name',
                            'admins.email',
                            'admins.is_archived',
                            'admins.type',
                            'client_matters.client_unique_matter_no'
                        )
                        ->get();
                    
                    foreach ($matterResults as $result) {
                        $results[] = [
                            'id' => base64_encode(convert_uuencode($result->client_id)) . '/Matter/' . $result->client_unique_matter_no,
                            'name' => $result->first_name . ' ' . $result->last_name,
                            'email' => $result->email,
                            'status' => $result->is_archived ? 'Archived' : $result->type,
                            'cid' => $result->client_id,
                        ];
                    }
                }
            }
            
            /**
             * 2. Search in client_matters by department_reference / other_reference / client_unique_matter_no
             * Optimized: Use JOIN to fetch client data in single query
             */
            $matterMatches = DB::table('client_matters')
                ->join('admins', 'client_matters.client_id', '=', 'admins.id')
                ->where('admins.role', 7)
                ->whereNull('admins.is_deleted')
                ->where(function($query) use ($squery) {
                    $query->where('client_matters.department_reference', 'LIKE', "%{$squery}%")
                          ->orWhere('client_matters.other_reference', 'LIKE', "%{$squery}%")
                          ->orWhere('client_matters.client_unique_matter_no', 'LIKE', "%{$squery}%");
                })
                ->select(
                    'admins.id as client_id',
                    'admins.first_name',
                    'admins.last_name',
                    'admins.email',
                    'admins.is_archived',
                    'admins.type',
                    'client_matters.client_unique_matter_no'
                )
                ->get();
            
            // Log matter matches for debugging
            \Log::info('Matter matches found: ' . count($matterMatches) . ' for query: ' . $squery);

            foreach ($matterMatches as $matter) {
                $results[] = [
                    'id' => base64_encode(convert_uuencode($matter->client_id)) . '/Matter/' . $matter->client_unique_matter_no,
                    'name' => $matter->first_name . ' ' . $matter->last_name,
                    'email' => $matter->email,
                    'status' => $matter->is_archived ? 'Archived' : $matter->type,
                    'cid' => $matter->client_id,
                ];
            }

            /**
             * 3. Search in admins (clients) - OPTIMIZED VERSION
             * Replaced correlated subqueries with LEFT JOINs
             * Replaced IN subqueries with EXISTS or LEFT JOINs
             */
            $d = '';
            if (strstr($squery, '/')) {
                $dob = explode('/', $squery);
                if (!empty($dob) && is_array($dob)) {
                    $d = $dob[2] . '/' . $dob[1] . '/' . $dob[0];
                }
            }

            // Optimized: Use LEFT JOINs instead of correlated subqueries
            // Use EXISTS or LEFT JOINs instead of IN subqueries
            $clientsQuery = \App\Models\Admin::query()
                ->where('admins.role', 7)
                ->whereNull('admins.is_deleted')
                ->leftJoin('client_contacts', function($join) use ($squery) {
                    $squeryLower = strtolower($squery);
                    $join->on('client_contacts.client_id', '=', 'admins.id')
                         ->whereRaw('LOWER(client_contacts.phone) LIKE ?', ["%{$squeryLower}%"]);
                })
                ->leftJoin('client_emails', function($join) use ($squery) {
                    $squeryLower = strtolower($squery);
                    $join->on('client_emails.client_id', '=', 'admins.id')
                         ->whereRaw('LOWER(client_emails.email) LIKE ?', ["%{$squeryLower}%"]);
                })
                ->where(function ($query) use ($squery, $d) {
                    $squeryLower = strtolower($squery);
                    $query->whereRaw('LOWER(admins.email) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(admins.first_name) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(admins.last_name) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(admins.client_id) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw('LOWER(admins.phone) LIKE ?', ["%$squeryLower%"])
                        ->orWhereRaw("LOWER(COALESCE(admins.first_name, '') || ' ' || COALESCE(admins.last_name, '')) LIKE ?", ["%$squeryLower%"])
                        ->orWhereNotNull('client_contacts.client_id')  // Matches phone search
                        ->orWhereNotNull('client_emails.client_id');    // Matches email search

                    if ($d != "") {
                        $query->orWhere('admins.dob', '=', $d);
                    }
                })
                ->select(
                    'admins.*'
                )
                ->distinct()
                ->get();

            // Get client IDs for batch loading of related data
            $clientIds = $clientsQuery->pluck('id')->toArray();
            
            if (!empty($clientIds)) {
                // Optimized: Batch load all phones, emails, and latest matters in separate queries
                // This is much faster than correlated subqueries
                
                // Get all phones grouped by client_id
                $phonesData = DB::table('client_contacts')
                    ->whereIn('client_id', $clientIds)
                    ->select('client_id', 'phone', 'contact_type')
                    ->orderBy('client_id')
                    ->orderBy('contact_type')
                    ->get()
                    ->groupBy('client_id');
                
                // Get all emails grouped by client_id
                $emailsData = DB::table('client_emails')
                    ->whereIn('client_id', $clientIds)
                    ->select('client_id', 'email', 'email_type')
                    ->orderBy('client_id')
                    ->orderBy('email_type')
                    ->get()
                    ->groupBy('client_id');
                
                // Get latest matter for each client (optimized: get max IDs first, then fetch details)
                $maxMatterIds = DB::table('client_matters')
                    ->whereIn('client_id', $clientIds)
                    ->where('matter_status', 1)
                    ->select('client_id', DB::raw('MAX(id) as max_id'))
                    ->groupBy('client_id')
                    ->pluck('max_id', 'client_id')
                    ->toArray();
                
                $latestMatters = [];
                if (!empty($maxMatterIds)) {
                    $latestMatters = DB::table('client_matters')
                        ->whereIn('id', array_values($maxMatterIds))
                        ->select('client_id', 'client_unique_matter_no')
                        ->get()
                        ->keyBy('client_id');
                }

                // Process results
                foreach ($clientsQuery as $client) {
                    // Aggregate phones (ordered by contact_type as in original query)
                    $allPhones = '';
                    if (isset($phonesData[$client->id])) {
                        $phones = $phonesData[$client->id]
                            ->sortBy('contact_type')
                            ->pluck('phone')
                            ->unique()
                            ->values()
                            ->toArray();
                        $allPhones = implode(', ', $phones);
                    }
                    
                    // Aggregate emails (ordered by email_type as in original query)
                    $allEmails = '';
                    if (isset($emailsData[$client->id])) {
                        $emails = $emailsData[$client->id]
                            ->sortBy('email_type')
                            ->pluck('email')
                            ->unique()
                            ->values()
                            ->toArray();
                        $allEmails = implode(', ', $emails);
                    }
                    
                    // Get latest matter
                    $latestMatterNo = isset($latestMatters[$client->id]) 
                        ? $latestMatters[$client->id]->client_unique_matter_no 
                        : null;
                    
                    $resultFinalId = $latestMatterNo
                        ? base64_encode(convert_uuencode($client->id)) . '/Matter/' . $latestMatterNo
                        : base64_encode(convert_uuencode($client->id)) . '/Client';

                    $results[] = [
                        'id' => $resultFinalId,
                        'name' => $client->first_name . ' ' . $client->last_name,
                        'email' => $client->email,
                        'status' => $client->is_archived ? 'Archived' : $client->type,
                        'cid' => $client->id,
                        'phones' => $allPhones,
                        'emails' => $allEmails,
                    ];
                }
            }

            return response()->json(['items' => $results]);
        }
        
        // Return empty array when query is empty
        return response()->json(['items' => []]);
    }

    public function getAllUser(Request $request, Admin $product) {
            $products = $request->q
                ? Admin::select('id', 'first_name')->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($request->q) . '%'])
                : new Admin();

            return $products->paginate(10, ['*'], 'page', $request->page)->toArray();
    }

	public function activities(Request $request){ 
		// Bypass all output buffering
		while (ob_get_level()) {
			ob_end_clean();
		}
		
		// Start fresh output buffer
		ob_start();
		
		// Force error reporting off
		@ini_set('display_errors', '0');
		@error_reporting(0);
		
		// Initialize response with default error state
		$response = [
			'status' => false,
			'message' => 'An error occurred while fetching activities'
		];

		try {
			// Validate request has id parameter
			if (!$request->has('id') || empty($request->id)) {
				$response['message'] = 'Client ID is required';
				header('Content-Type: application/json');
				echo json_encode($response);
				ob_end_flush();
				exit;
			}

			// Check if client exists - role must be integer for PostgreSQL compatibility
			$clientExists = Admin::where('role', '=', 7)->where('id', $request->id)->exists();
			
			if($clientExists){
				$activities = ActivitiesLog::where('client_id', $request->id)
					->orderby('created_at', 'DESC')
					->get();
				
				$data = array();
				
				foreach($activities as $activit){
					$admin = Admin::where('id', $activit->created_by)->first();
					$data[] = array(
						'activity_id' => $activit->id,
						'subject' => $activit->subject ?? '',
						'createdname' => $admin ? substr($admin->first_name, 0, 1) : '?',
						'name' => $admin ? $admin->first_name : 'Unknown',
						'message' => $activit->description ?? '',
						'date' => date('d M Y, H:i A', strtotime($activit->created_at)),
					   'followup_date' => $activit->followup_date ?? '',
					   'task_group' => $activit->task_group ?? '',
					   'pin' => $activit->pin ?? 0,
					   'activity_type' => $activit->activity_type ?? 'note'
					);
				}

				$response['status'] 	= 	true;
				$response['data']	=	$data;
				unset($response['message']); // Remove error message on success
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Client not found';
			}
		} catch (\Exception $e) {
			\Log::error('Error fetching activities (Exception): ' . $e->getMessage(), [
				'client_id' => $request->id ?? 'N/A',
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'trace' => $e->getTraceAsString()
			]);
			$response['status'] = false;
			$response['message'] = 'Exception: ' . $e->getMessage();
		} catch (\Throwable $e) {
			// Catch fatal errors
			\Log::error('Fatal error fetching activities (Throwable): ' . $e->getMessage(), [
				'client_id' => $request->id ?? 'N/A',
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'trace' => $e->getTraceAsString()
			]);
			$response['status'] = false;
			$response['message'] = 'Fatal: ' . $e->getMessage();
		}

		// Ensure JSON response is always returned
		header('Content-Type: application/json');
		$jsonOutput = json_encode($response);
		echo $jsonOutput;
		ob_end_flush();
		exit;
	}

	public function updateclientstatus(Request $request){
		if(Admin::where('role', '=', 7)->where('id', $request->id)->exists()){
			$client = Admin::where('role', '=', 7)->where('id', $request->id)->first();

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
				$objs->task_status = 0;
				$objs->pin = 0;
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
		if(Admin::where('role', '=', 7)->where('id', $request->client_id)->exists()){
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
				$objs->task_status = 0;
				$objs->pin = 0;
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
		if(Admin::where('role', '=', 7)->where('id', $request->id)->exists()){
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
            return redirect()->back()->with('error', config('constants.server_error'));
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
                    $objs->task_status = 0;
                    $objs->pin = 0;
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
                    $objs->task_status = 0;
                    $objs->pin = 0;
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

            //accounts - DISABLED: invoices table has been deleted
            /* $accounts = DB::table('invoices')->where('client_id', $request->merge_from)->get(); //dd($accounts);
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
            } */

            // Email history (mail_reports)
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
        //Get user Phone no and send message via UnifiedSmsManager
        $userInfo = Admin::select('id','country_code','phone')->where('id', $data['id'])->first();//dd($userInfo);
        
        $smsResult = null;
        if ($userInfo) {
            $message = $data['message'];
            $userPhone = $userInfo->country_code."".$userInfo->phone;
            
            // Use UnifiedSmsManager with proper context (auto-creates activity log)
            $smsResult = $this->smsManager->sendSms($userPhone, $message, 'notification', [
                'client_id' => $data['id']
            ]);
        }
        
        $recExist = Admin::where('id', $data['id'])->update(['not_picked_call' => $data['not_picked_call']]);
        if($recExist){
            if($data['not_picked_call'] == 1){ //if checked true
                // Activity log is now automatically created by UnifiedSmsManager
                // No need to manually create it here
                
                $response['status'] 	= 	true;
                $response['message']	=	$smsResult && $smsResult['success'] 
                    ? 'Call not picked. SMS sent successfully!' 
                    : 'Call not picked. SMS failed to send.';
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

 
    // ============================================================
    // COMMENTED OUT: Original saveofficereport function
    // This method has been moved to ClientAccountsController
    // Kept here for reference only - DO NOT USE
    // Routes point to ClientAccountsController@saveofficereport
    // ============================================================
    /*
    public function saveofficereport(Request $request, $id = NULL)
    {
        // This is a duplicate method that was moved to ClientAccountsController
        // The active version is in ClientAccountsController.php
        // This commented version is kept for reference only
        
        // Original functionality:
        // - Handles document upload for office receipts
        // - Creates office receipt entries
        // - Links receipts to invoices
        // - Updates invoice status based on payment
        // - Groups deposits by invoice number
        // - Handles excess amounts
        // - Returns JSON response with receipt data
        
        // DO NOT UNCOMMENT OR USE THIS METHOD
        // Use ClientAccountsController@saveofficereport instead
    }
    */
    // ============================================================

    //Fetch all contact list of any client at create note popup



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
                    $objs->task_status = 0;
                    $objs->pin = 0;
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
                return redirect()->back()->with('error', config('constants.server_error'));
            } else {
                return redirect()->back()->with('success', 'Inbox email re-assigned successfully');
            }
        } else {
            return redirect()->back()->with('error', config('constants.server_error'));
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
                    $objs->task_status = 0;
                    $objs->pin = 0;
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
                return redirect()->back()->with('error', config('constants.server_error'));
            } else {
                return redirect()->back()->with('success', 'Sent email re-assigned successfully');
            }
        } else {
            return redirect()->back()->with('error', config('constants.server_error'));
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

    // Filter Inbox emails
    public function filterEmails(Request $request)
    {
        try {
            $client_id = $request->input('client_id');
            $client_matter_id = $request->input('client_matter_id');
            $status = $request->input('status');
            $search = $request->input('search');
            $label_id = $request->input('label_id');

            if (!$client_matter_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Matter ID is required'
                ], 400);
            }

            $query = \App\Models\MailReport::where('client_matter_id', $client_matter_id)
                ->where('type', 'client')
                ->where('mail_type', 1)
                ->where('conversion_type', 'conversion_email_fetch')
                ->where('mail_body_type', 'inbox')
                ->with(['labels', 'attachments'])
                ->orderBy('created_at', 'DESC');

            if ($status !== null && $status !== '') {
                if ($status == 1) {
                    $query->where('mail_is_read', 1);
                } elseif ($status == 2) {
                    $query->where(function ($q) {
                        $q->where('mail_is_read', 0)
                          ->orWhereNull('mail_is_read');
                    });
                }
            }

            if ($search !== null && $search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('subject', 'LIKE', "%{$search}%")
                      ->orWhere('message', 'LIKE', "%{$search}%")
                      ->orWhere('from_mail', 'LIKE', "%{$search}%")
                      ->orWhere('to_mail', 'LIKE', "%{$search}%");
                });
            }

            if (!empty($label_id)) {
                $query->whereHas('labels', function ($q) use ($label_id) {
                    $q->where('email_labels.id', $label_id);
                });
            }

            $emails = $query->get();
            $url = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/';

            $emails = $emails->map(function ($email) use ($url, $client_id) {
                $DocInfo = \App\Models\Document::select('id','doc_type','myfile','myfile_key','mail_type')
                    ->where('id', $email->uploaded_doc_id)
                    ->first();

                $AdminInfo = \App\Models\Admin::select('client_id')->where('id',$email->client_id)->first();

                $previewUrl = '';
                if ($DocInfo) {
                    if (!empty($DocInfo->myfile_key)) {
                        $previewUrl = $DocInfo->myfile;
                    } else {
                        $previewUrl = $url . $AdminInfo->client_id . '/' . ($DocInfo->doc_type ?? 'mail') . '/' . ($DocInfo->mail_type ?? 'inbox') . '/' . $DocInfo->myfile;
                    }
                }

                $email->preview_url = $previewUrl;
                return $email;
            });

            return response()->json($emails, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            \Log::error('Error in filterEmails: ' . $e->getMessage());

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
            $client_matter_id = $request->input('client_matter_id'); // NEW: Filter by matter
            $type = $request->input('type');
            $status = $request->input('status');
            $search = $request->input('search');

            // Validate input
            if (!$client_matter_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Matter ID is required'
                ], 400);
            }

            // Base query for sent mail - FILTER BY MATTER ID instead of client_id
            $query = \App\Models\MailReport::where('client_matter_id', $client_matter_id)
                ->where('type', 'client')
                ->where('mail_type', 1)
                ->where(function ($query) {
                    $query->whereNull('conversion_type')
                        ->orWhere(function ($subQuery) {
                            $subQuery->where('conversion_type', 'conversion_email_fetch')
                                ->where('mail_body_type', 'sent');
                        });
                })
                ->with(['labels', 'attachments']) // Load labels and attachments relationships
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

     //Seach Client Relationship

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

    //Generate agreemnt
    public function generateagreement(Request $request)
    {
        try { //dd($request->all());
            $id = $request->client_id;
            $client = Admin::findOrFail($request->client_id);
            $responsiblePerson = Admin::findOrFail($request->agent_id); //dd($responsiblePerson);
            if (!$responsiblePerson) {
                return response()->json([
                    'success' => false,
                    'error' => 'No responsible person found in the database.',
                    'message' => 'No responsible person found in the database.'
                ], 400);
            }

            // Ensure templates directory exists
            $templatesDir = storage_path('app/templates');
            if (!file_exists($templatesDir)) {
                mkdir($templatesDir, 0755, true);
                Log::info('Created templates directory: ' . $templatesDir);
            }
            
            $templatePath = storage_path('app/templates/agreement_template.docx'); //dd($templatePath);

            if (!file_exists($templatePath)) {
                Log::error('Agreement template file not found at: ' . $templatePath);
                return response()->json([
                    'success' => false,
                    'error' => 'Template file not found.',
                    'message' => 'The agreement template file (agreement_template.docx) is missing. Please ensure the template file is placed at: storage/app/templates/agreement_template.docx',
                    'template_path' => $templatePath,
                    'help' => 'Contact your system administrator to upload the agreement template file.'
                ], 404);
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
                'DOHABaseApplicationChargeIncCCSurcharge' => number_format($visa_application_charge, 2),

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
            $downloadUrl = null;
            $s3UploadSuccess = false;
            
            // Try to upload to S3
            try {
                $uploadResult = Storage::disk('s3')->put($s3Path, file_get_contents($outputPath));
                
                if ($uploadResult) {
                    // Get the S3 URL
                    $downloadUrl = Storage::disk('s3')->url($s3Path);
                    
                    // Verify the URL is not empty
                    if (!empty($downloadUrl)) {
                        $s3UploadSuccess = true;
                        Log::info('Document uploaded to S3 successfully. URL: ' . $downloadUrl);
                    } else {
                        Log::warning('S3 upload succeeded but URL is empty');
                    }
                } else {
                    Log::warning('S3 upload returned false');
                }
            } catch (\Exception $s3Exception) {
                Log::error('S3 upload failed: ' . $s3Exception->getMessage());
                Log::error($s3Exception->getTraceAsString());
            }
            
            // If S3 upload failed or URL is empty, use local file as fallback
            if (!$s3UploadSuccess || empty($downloadUrl)) {
                // File is already in public storage (storage/app/public/agreements)
                // Generate public URL using the storage path
                // The file is saved as: agreement_{client_id}.docx
                $localFileName = basename($outputPath);
                $relativePath = 'agreements/' . $localFileName;
                $downloadUrl = asset('storage/' . $relativePath);
                
                // Verify the file exists before returning the URL
                if (!file_exists($outputPath)) {
                    throw new \Exception('Document was generated but file not found at: ' . $outputPath);
                }
                
                Log::info('Using local file as fallback. URL: ' . $downloadUrl);
                // Keep the local file for download (don't delete it)
            } else {
                // Clean up local file only if S3 upload was successful
                if (file_exists($outputPath)) {
                    unlink($outputPath);
                }
            }
            
            // Verify download URL is set
            if (empty($downloadUrl)) {
                Log::error('Download URL is empty after all attempts. Output path: ' . $outputPath);
                throw new \Exception('Failed to generate download URL. Document was created but could not be made available for download.');
            }
            
            // Log the final response for debugging
            Log::info('Returning success response with download_url: ' . $downloadUrl);
            
            // Log activity
            $matter = \App\Models\ClientMatter::find($request->client_matter_id);
            $matterName = $matter ? $matter->title : 'N/A';
            
            $activity = new \App\Models\ActivitiesLog;
            $activity->client_id = $request->client_id;
            $activity->created_by = Auth::user()->id;
            $activity->subject = 'created visa agreement';
            $activity->description = '<p>Visa agreement has been created for matter: <strong>' . $matterName . '</strong></p>';
            $activity->task_status = 0;
            $activity->pin = 0;
            $activity->save();
            
            // Return the download URL as JSON
            $response = [
                'success' => true,
                'download_url' => $downloadUrl,
                'filename' => $fileName,
                'message' => 'Document generated successfully'
            ];
            
            // Double-check response structure before returning
            if (!isset($response['success']) || !isset($response['download_url'])) {
                Log::error('Response structure is invalid: ' . json_encode($response));
                throw new \Exception('Invalid response structure generated.');
            }
            
            return response()->json($response);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Model not found in generateagreement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Client or agent not found.',
                'message' => 'Client or agent not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error generating document: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error generating document: ' . $e->getMessage()
            ], 500);
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
            $agentInfo = DB::table('admins')->select(
                'id as agentId',
                'first_name',
                'last_name',
                'company_name',
                'is_migration_agent',
                'marn_number',
                'legal_practitioner_number',
                'exempt_person_reason',
                'business_address',
                'business_phone',
                'business_mobile',
                'business_email',
                'business_fax',
                'tax_number'
            )->where('id',$sel_migration_agent)->first();
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
            $agentInfo = DB::table('admins')->select(
                'id as agentId',
                'first_name',
                'last_name',
                'company_name',
                'is_migration_agent',
                'marn_number',
                'legal_practitioner_number',
                'exempt_person_reason',
                'business_address',
                'business_phone',
                'business_mobile',
                'business_email',
                'business_fax',
                'tax_number'
            )->where('id',$sel_migration_agent)->first();
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
            $agentInfo = DB::table('admins')->select(
                'id as agentId',
                'first_name',
                'last_name',
                'company_name',
                'is_migration_agent',
                'marn_number',
                'legal_practitioner_number',
                'exempt_person_reason',
                'business_address',
                'business_phone',
                'business_mobile',
                'business_email',
                'business_fax',
                'tax_number'
            )->where('id',$sel_migration_agent)->first();
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
                
                // Log activity
                $action = ($cost_assignment_cnt > 0) ? 'updated' : 'created';
                $matter = \App\Models\ClientMatter::find($requestData['client_matter_id']);
                $matterName = $matter ? $matter->title : 'N/A';
                
                $activity = new \App\Models\ActivitiesLog;
                $activity->client_id = $requestData['client_id'];
                $activity->created_by = Auth::user()->id;
                $activity->subject = $action . ' cost assignment form';
                $activity->description = '<p>Cost assignment form has been ' . $action . ' for matter: <strong>' . $matterName . '</strong></p>';
                $activity->task_status = 0;
                $activity->pin = 0;
                $activity->save();
            }
        }
        echo json_encode($response);
    }

    public function deletecostagreement(Request $request)
    {
        $cost_agreement_id = $request->input('cost_agreement_id');
        
        if (!$cost_agreement_id) {
            return response()->json([
                'status' => false,
                'message' => 'Cost agreement ID is required'
            ]);
        }

        $costAssignment = \App\Models\CostAssignmentForm::find($cost_agreement_id);
        
        if (!$costAssignment) {
            return response()->json([
                'status' => false,
                'message' => 'Cost agreement not found'
            ]);
        }

        $client_id = $costAssignment->client_id;
        $matter = \App\Models\ClientMatter::find($costAssignment->client_matter_id);
        $matterName = $matter ? $matter->title : 'N/A';

        // Delete the cost assignment
        $deleted = $costAssignment->delete();

        if ($deleted) {
            // Log activity
            $activity = new \App\Models\ActivitiesLog;
            $activity->client_id = $client_id;
            $activity->created_by = Auth::user()->id;
            $activity->subject = 'deleted cost assignment form';
            $activity->description = '<p>Cost assignment form has been deleted for matter: <strong>' . $matterName . '</strong></p>';
            $activity->task_status = 0;
            $activity->pin = 0;
            $activity->save();

            return response()->json([
                'status' => true,
                'message' => 'Cost agreement deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete cost agreement'
            ]);
        }
    }

    //save reference
    public function savereferences(Request $request)
    {  //dd($request->all());
        $validated = $request->validate([
            'department_reference' => 'nullable|string|max:255',
            'other_reference' => 'nullable|string|max:255',
        ]);

        // Step 2: Find the matter - use EXACT same logic as page load (detail.blade.php lines 252-267)
        // Priority: 1) Use client_unique_matter_no from URL (id1), 2) Use client_matter_id from dropdown, 3) Get latest active matter
        $matter = null;
        $lookupMethod = '';
        
        if ($request->has('client_unique_matter_no') && !empty($request->client_unique_matter_no)) {
            // Priority 1: Use client_unique_matter_no from URL (id1) - EXACT match to page load logic
            $matter = \App\Models\ClientMatter::where('client_id', $request->client_id)
                ->where('client_unique_matter_no', $request->client_unique_matter_no)
                ->first();
            $lookupMethod = 'client_unique_matter_no: ' . $request->client_unique_matter_no;
        } elseif ($request->has('client_matter_id') && !empty($request->client_matter_id)) {
            // Priority 2: Use the matter ID from dropdown
            $matter = \App\Models\ClientMatter::where('client_id', $request->client_id)
                ->where('id', $request->client_matter_id)
                ->first();
            $lookupMethod = 'client_matter_id: ' . $request->client_matter_id;
        } else {
            // Priority 3: Fallback - Get latest active matter (EXACT match to page load logic)
            $matter = \App\Models\ClientMatter::where('client_id', $request->client_id)
                ->where('matter_status', 1)
                ->orderBy('id', 'desc')
                ->first();
            $lookupMethod = 'latest active matter';
        }

        if (!$matter) {
            \Log::error('References save - Matter not found', [
                'client_id' => $request->client_id,
                'client_unique_matter_no' => $request->client_unique_matter_no ?? 'not provided',
                'client_matter_id' => $request->client_matter_id ?? 'not provided',
                'lookup_method' => $lookupMethod
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Record not found for given client_id and matter information.'
            ], 404);
        }
        
        \Log::info('References save - Matter found', [
            'matter_id' => $matter->id,
            'client_id' => $matter->client_id,
            'client_unique_matter_no' => $matter->client_unique_matter_no,
            'lookup_method' => $lookupMethod,
            'current_department_reference' => $matter->department_reference,
            'current_other_reference' => $matter->other_reference
        ]);

        // Step 3: Perform the update - convert empty strings to null
        $deptRefInput = $request->input('department_reference', '');
        $otherRefInput = $request->input('other_reference', '');
        $deptRef = !empty($deptRefInput) && trim($deptRefInput) !== '' ? trim($deptRefInput) : null;
        $otherRef = !empty($otherRefInput) && trim($otherRefInput) !== '' ? trim($otherRefInput) : null;
        
        // Direct assignment and save (fields are in fillable, so this is safe)
        $matter->department_reference = $deptRef;
        $matter->other_reference = $otherRef;
        $saved = $matter->save();
        
        if (!$saved) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save references.'
            ], 500);
        }
        
        // Refresh to get latest values from database
        $matter->refresh();

        // Log for debugging
        \Log::info('References saved', [
            'matter_id' => $matter->id,
            'client_id' => $request->client_id,
            'client_unique_matter_no' => $matter->client_unique_matter_no,
            'department_reference' => $matter->department_reference,
            'other_reference' => $matter->other_reference,
            'saved' => $saved
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'References updated successfully.',
            'data' => [
                'matter_id' => $matter->id,
                'client_unique_matter_no' => $matter->client_unique_matter_no,
                'department_reference' => $matter->department_reference,
                'other_reference' => $matter->other_reference
            ]
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
            $obj5->office_id = $requestData['office_id'] ?? Auth::user()->office_id ?? null;
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
            $obj5->matter_status = 1; // Active by default
            $saved5 = $obj5->save();
            $lastInsertedId = $obj5->id; // ← This gets the last inserted ID
            if($saved5) 
            {
                //update type client from lead in admins table - using Lead model
                $lead = \App\Models\Lead::withArchived()->find($requestData['client_id']);
                if($lead) {
                    $lead->convertToClient();
                }

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
    public function uploadAgreement(Request $request, Admin $admin)
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

        //3. Build S3 path using client ID (admin is the client record)
        $clientUniqueId = $admin->client_id ?? "";
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
        $doc->client_id = $admin->id;
        $doc->type = 'client';
        $doc->file_size = $size;
        $doc->doc_type = 'agreement';
        $doc->client_matter_id = $requestData['clientmatterid'];
        $saved = $doc->save();

        //6. Log activity if saved
        if ($saved) {
            $log = new \App\Models\ActivitiesLog;
            $log->client_id = $admin->id;
            $log->created_by = Auth::user()->id;
            $log->description = '';
            $log->subject = 'Finalized visa agreement uploaded as PDF';
            $log->task_status = 0;
            $log->pin = 0;
            $log->save();
        }

        //7. Return success response
        return response()->json([
            'status' => true,
            'message' => 'PDF agreement uploaded successfully!'
        ]);
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
				$activityLog->task_status = 0;
				$activityLog->pin = 0;
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
				->select('client_matters.id', 'client_matters.client_unique_matter_no', 'matters.title', 'client_matters.sel_matter_id')
				->where('client_matters.matter_status', 1)
				->where('client_matters.client_id', $clientId)
				->orderBy('client_matters.id', 'desc')
				->get();
			
			$matters = [];
			foreach($clientMatters as $matter){
				// If sel_matter_id is 1 or title is null, use "General Matter"
				$matterName = 'General Matter';
				if ($matter->sel_matter_id != 1 && !empty($matter->title)) {
					$matterName = $matter->title;
				}
				
				$displayName = $matterName . ' - ' . $matter->client_unique_matter_no;
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
     * Decode string helper method - consistent with other controllers
     * 
     * @param string|null $string
     * @return string|false
     */
    public function decodeString($string = null)
    {
        if (empty($string)) {
            return false;
        }
        
        if (base64_encode(base64_decode($string, true)) === $string) {
            return convert_uudecode(base64_decode($string));
        }
        
        return false;
    }

    public function createservicetaken(Request $request)
    {
        $id = $request->logged_client_id;
        if (\App\Models\Admin::where('id', $id)->exists()) {
         $entity_type = $request->entity_type;
         if ($entity_type == 'add') {
             $obj = new clientServiceTaken;
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
         } else if ($entity_type == 'edit') {
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
         if ($saved) {
             $response['status'] = true;
             $response['message'] = 'success';
             $user_rec = DB::table('client_service_takens')->where('client_id', $id)->where('is_saved_db', 0)->orderBy('id', 'desc')->get();
             $response['user_rec'] = $user_rec;
         } else {
             $response['status'] = true;
             $response['message'] = 'success';
             $response['user_rec'] = array();
         }
        } else {
         $response['status'] = false;
         $response['message'] = 'fail';
         $response['result_str'] = array();
        }
        return response()->json($response);
    }

    public function removeservicetaken(Request $request)
    {
        $sel_service_taken_id = $request->sel_service_taken_id;
        if (DB::table('client_service_takens')->where('id', $sel_service_taken_id)->exists()) {
         $res = DB::table('client_service_takens')->where('id', $sel_service_taken_id)->delete();
         if ($res) {
             $response['status'] = true;
             $response['record_id'] = $sel_service_taken_id;
             $response['message'] = 'Service removed successfully';
         } else {
             $response['status'] = false;
             $response['record_id'] = $sel_service_taken_id;
             $response['message'] = 'Service not removed';
         }
        } else {
         $response['status'] = false;
         $response['record_id'] = $sel_service_taken_id;
         $response['message'] = 'Please try again';
        }
        return response()->json($response);
    }

    public function getservicetaken(Request $request)
    {
        $sel_service_taken_id = $request->sel_service_taken_id;
        if (DB::table('client_service_takens')->where('id', $sel_service_taken_id)->exists()) {
         $res = DB::table('client_service_takens')->where('id', $sel_service_taken_id)->first();
         if ($res) {
             $response['status'] = true;
             $response['message'] = 'success';
             $response['user_rec'] = $res;
         } else {
             $response['status'] = true;
             $response['message'] = 'success';
             $response['user_rec'] = array();
         }
        } else {
         $response['status'] = false;
         $response['message'] = 'fail';
         $response['user_rec'] = array();
        }
        return response()->json($response);
    }
    public function previewMsgFile($filename)
    {
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
    /**
     * Change client type (lead to client conversion)
     */
    public function changetype(Request $request,$id = Null, $slug = Null){ 
        if(isset($id) && !empty($id)) {
            $id = $this->decodeString($id);
            if(Admin::where('id', '=', $id)->where('role', '=', 7)->exists()) {
                $obj = Admin::find($id);
                $user_type = $obj->type;
                if($slug == 'client') {
                    $obj->type = $slug;
                    $obj->user_id = $request['user_id'];
                    $saved = $obj->save();

                    $matter = new ClientMatter();
                    $matter->user_id = $request['user_id'];
                    $matter->client_id = $request['client_id'];
                    $matter->office_id = $request['office_id'] ?? Auth::user()->office_id ?? null;
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
                    $matter->matter_status = 1; // Active by default
                    $matter->save();
                    
                    if($user_type == 'lead'){
                        $activity = new \App\Models\ActivitiesLog;
                        $activity->client_id = $request['client_id'];
                        $activity->created_by = Auth::user()->id;
                        $activity->subject = 'Lead converted to client. Matter '.$matter->client_unique_matter_no. ' created';
                        $activity->description = 'Lead converted to client. Matter '.$matter->client_unique_matter_no. ' created';
                        $activity->task_status = 0;
                        $activity->pin = 0;
                        $activity->save();

                        $msg = 'Lead converted to client. Matter '.$matter->client_unique_matter_no. ' created';
                    }  else if($user_type == 'client'){
                        $activity = new \App\Models\ActivitiesLog;
                        $activity->client_id = $request['client_id'];
                        $activity->created_by = Auth::user()->id;
                        $activity->subject = 'Matter '.$matter->client_unique_matter_no. ' created';
                        $activity->description = 'Matter '.$matter->client_unique_matter_no. ' created';
                        $activity->task_status = 0;
                        $activity->pin = 0;
                        $activity->save();

                        $msg = 'Matter '.$matter->client_unique_matter_no. ' created';
                    }
                    // Redirect with matter number in URL
                    return Redirect::to('/clients/detail/'.base64_encode(convert_uuencode(@$id)).'/'.$matter->client_unique_matter_no)->with('success', $msg);
                } else if($slug == 'lead' ) {
                    $obj->type = $slug;
                    $obj->user_id = "";
                    $saved = $obj->save();
                }
                return Redirect::to('/clients/detail/'.base64_encode(convert_uuencode(@$id)))->with('success', 'Record Updated successfully');
            } else {
                return Redirect::to('/clients')->with('error', 'Clients Not Exist');
            }
        } else {
            return Redirect::to('/clients')->with('error', config('constants.unauthorized'));
        }
	}

    /**
     * Store follow-up note with assignee information
     * Handles the "Assign User" popup functionality
     * Supports both single and multiple assignees
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function followupstore(Request $request)
    {
        try {
            $requestData = $request->all();
            
            // Validate required fields
            if (empty($requestData['client_id'])) {
                echo json_encode(array('success' => false, 'message' => 'Client ID is required'));
                exit;
            }
            
            // Decode the client ID
            $clientId = $this->decodeString($requestData['client_id']);
            
            // Validate decoded client ID
            if ($clientId === false || empty($clientId)) {
                echo json_encode(array('success' => false, 'message' => 'Invalid client ID'));
                exit;
            }
            
            // Handle rem_cat - ensure it exists and is an array (PostgreSQL migration pattern)
            $remCat = $requestData['rem_cat'] ?? [];
            if (!is_array($remCat)) {
                // If it's a single value, convert to array
                $remCat = !empty($remCat) ? [$remCat] : [];
            }
            
            // Validate that at least one assignee is selected
            if (empty($remCat)) {
                echo json_encode(array('success' => false, 'message' => 'At least one assignee must be selected'));
                exit;
            }
            
            // Get the next unique ID for this task
            $taskUniqueId = 'group_' . uniqid('', true);

            // Loop through each assignee and create a follow-up note
            foreach ($remCat as $assigneeId) {
                // Create a new followup note for each assignee
                $followup = new \App\Models\Note;
                $followup->client_id = $clientId;
                $followup->user_id = Auth::user()->id;
                $followup->description = $requestData['description'] ?? '';
                $followup->unique_group_id = $taskUniqueId;

                // Set the title for the current assignee
                $assigneeName = $this->getAssigneeName($assigneeId);
                $followup->title = $requestData['remindersubject'] ?? 'Lead assigned to ' . $assigneeName;

                // PostgreSQL NOT NULL constraints - must set these fields (Notes Table pattern)
                $followup->folloup = 1; // This is a follow-up note
                $followup->pin = 0; // Default to not pinned
                $followup->status = '0'; // Default status (string '0' = active, '1' = completed)
                $followup->type = 'client';
                $followup->task_group = $requestData['task_group'] ?? null;
                $followup->assigned_to = $assigneeId;

                if (isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != '') {
                    $followup->followup_date = $requestData['followup_datetime'];
                }

                //add note deadline
                if(isset($requestData['note_deadline_checkbox']) && $requestData['note_deadline_checkbox'] != ''){
                    if($requestData['note_deadline_checkbox'] == 1){
                        $followup->note_deadline = $requestData['note_deadline'] ?? null;
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
                        if ($Lead) {
                            $Lead->followup_date = $requestData['followup_datetime'];
                            $Lead->save();
                        }
                    }

                    // Create a notification for the current assignee
                    $o = new \App\Models\Notification;
                    $o->sender_id = Auth::user()->id;
                    $o->receiver_id = $assigneeId;
                    $o->module_id = $clientId;
                    $o->url = \URL::to('/clients/detail/' . $requestData['client_id']);
                    $o->notification_type = 'client';
                    $o->receiver_status = 0; // Unread
                    $o->seen = 0; // Not seen
                    
                    $followupDateTime = $requestData['followup_datetime'] ?? now();
                    try {
                        if (is_numeric($followupDateTime)) {
                            $formattedDate = date('d/M/Y h:i A', $followupDateTime);
                        } else {
                            $timestamp = strtotime($followupDateTime);
                            $formattedDate = $timestamp !== false ? date('d/M/Y h:i A', $timestamp) : date('d/M/Y h:i A');
                        }
                    } catch (\Exception $dateEx) {
                        $formattedDate = date('d/M/Y h:i A');
                    }
                    
                    $o->message = 'Followup Assigned by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name . ' on ' . $formattedDate;
                    $o->save();

                    // Log the activity for the current assignee
                    $objs = new ActivitiesLog;
                    $objs->client_id = $clientId;
                    $objs->created_by = Auth::user()->id;
                    $objs->subject = 'Set action for ' . $assigneeName;
                    $objs->description = '<span class="text-semi-bold">' . ($requestData['remindersubject'] ?? '') . '</span><p>' . ($requestData['description'] ?? '') . '</p>';
                    $objs->task_status = 0;
                    $objs->pin = 0;

                    if (Auth::user()->id != $assigneeId) {
                        $objs->use_for = $assigneeId;
                    } else {
                        $objs->use_for = "";
                    }

                    $objs->followup_date = $requestData['followup_datetime'] ?? null;
                    $objs->task_group = $requestData['task_group'] ?? null;
                    $objs->save();
                }
            }
            
            echo json_encode(array('success' => true, 'message' => 'successfully saved', 'clientID' => $requestData['client_id']));
            exit;
            
        } catch (\Exception $e) {
            Log::error('Error in followupstore: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            echo json_encode(array('success' => false, 'message' => 'Error saving follow-up. Please try again.'));
            exit;
        }
    }

    // Helper function to get assignee name
    protected function getAssigneeName($assigneeId)
    {
        $admin = \App\Models\Admin::find($assigneeId);
        return $admin ? $admin->first_name . ' ' . $admin->last_name : 'Unknown Assignee';
    }

    /**
     * Save tags for a client
     * Handles the tag assignment functionality from the client detail modal
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_tag(Request $request)
    {
        try {
            // Validate required fields
            $request->validate([
                'client_id' => 'required|integer',
                'tag' => 'array'
            ]);

            $clientId = $request->input('client_id');
            $tags = $request->input('tag', []);

            // Find the client
            $client = \App\Models\Admin::where('id', $clientId)
                ->where('role', '7') // Client role
                ->first();

            if (!$client) {
                return redirect()->back()->with('error', 'Client not found');
            }

            // Process tags - create new ones if they don't exist, get IDs for existing ones
            $tagIds = [];
            if (!empty($tags) && is_array($tags)) {
                foreach ($tags as $tagValue) {
                    if (!empty($tagValue)) {
                        // Check if tag exists by name first
                        $existingTag = \App\Models\Tag::where('name', $tagValue)->first();
                        
                        if ($existingTag) {
                            // Tag exists, use its ID
                            $tagIds[] = $existingTag->id;
                        } else {
                            // Check if it's an ID (numeric)
                            if (is_numeric($tagValue)) {
                                $tagById = \App\Models\Tag::find($tagValue);
                                if ($tagById) {
                                    $tagIds[] = $tagById->id;
                                }
                            } else {
                                // Create new tag
                                $newTag = new \App\Models\Tag();
                                $newTag->name = $tagValue;
                                $newTag->created_by = auth()->id();
                                $newTag->save();
                                $tagIds[] = $newTag->id;
                            }
                        }
                    }
                }
            }

            // Update the client's tagname field with tag IDs
            $client->tagname = implode(',', $tagIds);
            $client->save();

            return redirect()->back()->with('success', 'Tags saved successfully');

        } catch (\Exception $e) {
            \Log::error('Error saving tags: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while saving tags');
        }
    }

    /**
     * Store personal followup/task (Add My Task functionality)
     * Used by: action.blade.php
     */
    public function personalfollowup(Request $request)
    {
        $requestData = $request->all();
        
        // Decode the client ID - handle empty/null for personal tasks
        $clientId = null;
        $encodedClientId = null;
        
        if (!empty($requestData['client_id'])) {
            // Extract just the encoded part (format: "ENCODED/Matter/NO" or "ENCODED/Client")
            $clientIdParts = explode('/', $requestData['client_id']);
            $encodedClientId = $clientIdParts[0];
            $clientId = $this->decodeString($encodedClientId);
        }

        // Generate unique task ID
        $taskUniqueId = 'group_' . uniqid('', true);

        // Handle single or multiple assignees
        $assignees = is_array($requestData['rem_cat']) ? $requestData['rem_cat'] : [$requestData['rem_cat']];

        // Loop through each assignee and create a task
        foreach ($assignees as $assigneeId) {
            // Create a new task note for each assignee
            $task = new \App\Models\Note;
            $task->client_id = $clientId;
            $task->user_id = Auth::user()->id;
            $task->description = @$requestData['description'];
            $task->unique_group_id = $taskUniqueId;
            $task->folloup = 1;
            $task->type = 'client';
            $task->task_group = @$requestData['task_group'];
            $task->assigned_to = $assigneeId;
            $task->status = '0'; // Not completed
            
            if (isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != '') {
                $task->followup_date = @$requestData['followup_datetime'];
            }

            $saved = $task->save();

            if ($saved) {
                // Create a notification for the assignee
                $notification = new \App\Models\Notification;
                $notification->sender_id = Auth::user()->id;
                $notification->receiver_id = $assigneeId;
                $notification->module_id = $clientId;
                
                // Set URL based on whether client exists
                if (!empty($requestData['client_id'])) {
                    $notification->url = \URL::to('/clients/detail/' . $requestData['client_id']);
                } else {
                    $notification->url = \URL::to('/action');
                }
                
                $notification->message = 'assigned you a task';
                $notification->seen = 0;
                $notification->save();
            }
        }

        return response()->json(['success' => true, 'message' => 'Task created successfully']);
    }

    /**
     * Update existing followup/task
     * Used by: assign_by_me.blade.php
     */
    public function updatefollowup(Request $request)
    {
        $requestData = $request->all();
        
        try {
            // Find the existing task
            $task = \App\Models\Note::findOrFail($requestData['note_id']);
            
            // Decode the client ID - handle empty/null for personal tasks
            $clientId = null;
            if (!empty($requestData['client_id'])) {
                // Extract just the encoded part (format: "ENCODED/Matter/NO" or "ENCODED/Client")
                $clientIdParts = explode('/', $requestData['client_id']);
                $encodedClientId = $clientIdParts[0];
                $clientId = $this->decodeString($encodedClientId);
            }
            
            // Update task fields
            $task->description = @$requestData['description'];
            $task->client_id = $clientId;
            $task->task_group = @$requestData['task_group'];
            $task->assigned_to = @$requestData['rem_cat'];
            
            if (isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != '') {
                $task->followup_date = @$requestData['followup_datetime'];
            }
            
            $task->save();

            // Create notification for the assignee if changed
            if ($task->assigned_to != $task->getOriginal('assigned_to')) {
                $notification = new \App\Models\Notification;
                $notification->sender_id = Auth::user()->id;
                $notification->receiver_id = $task->assigned_to;
                $notification->module_id = $clientId;
                
                // Set URL based on whether client exists
                if (!empty($requestData['client_id'])) {
                    $notification->url = \URL::to('/clients/detail/' . $requestData['client_id']);
                } else {
                    $notification->url = \URL::to('/action');
                }
                
                $notification->message = 'updated your task';
                $notification->seen = 0;
                $notification->save();
            }

            return response()->json(['success' => true, 'message' => 'Task updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating task: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Reassign followup/task (for completed tasks)
     * Used by: action_completed.blade.php
     */
    public function reassignfollowupstore(Request $request)
    {
        $requestData = $request->all();
        
        // Decode the client ID - handle empty/null for personal tasks
        $clientId = null;
        if (!empty($requestData['client_id'])) {
            // Extract just the encoded part (format: "ENCODED/Matter/NO" or "ENCODED/Client")
            $clientIdParts = explode('/', $requestData['client_id']);
            $encodedClientId = $clientIdParts[0];
            $clientId = $this->decodeString($encodedClientId);
        }

        // Generate unique task ID
        $taskUniqueId = 'group_' . uniqid('', true);

        // Create a new task
        $task = new \App\Models\Note;
        $task->client_id = $clientId;
        $task->user_id = Auth::user()->id;
        $task->description = @$requestData['description'];
        $task->unique_group_id = $taskUniqueId;
        $task->folloup = 1;
        $task->type = 'client';
        $task->task_group = @$requestData['task_group'];
        $task->assigned_to = @$requestData['rem_cat'];
        $task->status = '0'; // Not completed
        
        if (isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != '') {
            $task->followup_date = @$requestData['followup_datetime'];
        }

        $saved = $task->save();

        if ($saved) {
            // Create a notification for the assignee
            $notification = new \App\Models\Notification;
            $notification->sender_id = Auth::user()->id;
            $notification->receiver_id = $task->assigned_to;
            $notification->module_id = $clientId;
            
            // Set URL based on whether client exists
            if (!empty($requestData['client_id'])) {
                $notification->url = \URL::to('/clients/detail/' . $requestData['client_id']);
            } else {
                $notification->url = \URL::to('/action');
            }
            
            $notification->message = 'assigned you a task';
            $notification->seen = 0;
            $notification->save();
        }

        return response()->json(['success' => true, 'message' => 'Task created successfully']);
    }

    /**
     * Test Python Accounting Processing
     * 
     * This is a test endpoint to experiment with Python-based accounting processing
     * Can be used to test data export, analytics, report generation, etc.
     */
    public function testPythonAccounting(Request $request)
    {
        try {
            $clientId = $request->input('client_id');
            $matterId = $request->input('matter_id');
            $processingType = $request->input('processing_type', 'analytics'); // analytics, export, report
            
            // Get accounting data
            $clientReceipts = DB::table('account_client_receipts')
                ->where('client_id', $clientId)
                ->where('client_matter_id', $matterId)
                ->get();
            
            $startTime = microtime(true);
            
            // Prepare data for Python service
            $accountingData = [
                'client_id' => $clientId,
                'matter_id' => $matterId,
                'receipts' => $clientReceipts->toArray(),
                'processing_type' => $processingType
            ];
            
            // TODO: Call Python service for processing
            // Example:
            // $pythonService = app(\App\Services\PythonService::class);
            // $result = $pythonService->processAccountingData($accountingData);
            
            // For now, return mock response
            $endTime = microtime(true);
            $processingTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
            
            return response()->json([
                'success' => true,
                'message' => 'Test completed successfully',
                'data' => [
                    'processing_time_ms' => round($processingTime, 2),
                    'records_count' => $clientReceipts->count(),
                    'processing_type' => $processingType,
                    'php_processing' => true,
                    'python_service_available' => false, // Will be true when Python service is integrated
                ],
                'note' => 'This is a test endpoint. Integrate with Python service for actual processing.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Test Python Accounting Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error during test processing',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update office assignment for a matter
     * POST /matters/update-office
     */
    public function updateMatterOffice(Request $request)
    {
        try {
            $this->validate($request, [
                'matter_id' => 'required|exists:client_matters,id',
                'office_id' => 'required|exists:branches,id',
            ]);
            
            $matter = ClientMatter::findOrFail($request->matter_id);
            $oldOffice = $matter->office ? $matter->office->office_name : 'None';
            $newOffice = Branch::findOrFail($request->office_id);
            
            // Update matter
            $matter->office_id = $request->office_id;
            $matter->save();
            
            // Log activity
            $activitySubject = $oldOffice === 'None' 
                ? "assigned matter to {$newOffice->office_name} office"
                : "changed matter office from {$oldOffice} to {$newOffice->office_name}";
            
            if (!empty($request->notes)) {
                $activitySubject .= " - Notes: {$request->notes}";
            }
            
            $activityLog = new ActivitiesLog;
            $activityLog->client_id = $matter->client_id;
            $activityLog->created_by = Auth::id();
            $activityLog->subject = $activitySubject;
            $activityLog->task_status = 0;
            $activityLog->pin = 0;
            $activityLog->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Office assigned successfully',
                'office_name' => $newOffice->office_name,
                'office_id' => $newOffice->id
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating matter office: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign office: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add appointment (legacy appointment system using Note model)
     * POST /add-appointment
     */
    public function addAppointment(Request $request)
    {
        try {
            $requestData = $request->all();
            
            // Validate required fields
            $validator = Validator::make($requestData, [
                'client_id' => 'required|exists:admins,id',
                'title' => 'required|string|max:255',
                'appoint_date' => 'required|date',
                'appoint_time' => 'required',
                'timezone' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Combine date and time into datetime
            $appointmentDateTime = $requestData['appoint_date'] . ' ' . $requestData['appoint_time'];
            // Parse the datetime in the user's selected timezone, then convert to UTC for storage
            $followupDateTime = Carbon::createFromFormat('Y-m-d H:i', $appointmentDateTime, $requestData['timezone'])
                ->setTimezone(config('app.timezone', 'UTC'));
            
            // Create appointment as Note record
            $appointment = new Note();
            $appointment->client_id = $requestData['client_id'];
            $appointment->user_id = Auth::id();
            $appointment->title = $requestData['title'];
            $appointment->description = $requestData['description'] ?? '';
            $appointment->followup_date = $followupDateTime->toDateTimeString();
            $appointment->type = 'application'; // Legacy appointment type
            $appointment->folloup = 1; // Active followup
            $appointment->status = 0; // Incomplete
            $appointment->pin = 0;
            
            // Set assigned_to from invitees if provided
            if (!empty($requestData['invitees'])) {
                $appointment->assigned_to = $requestData['invitees'];
            }
            
            $appointment->save();

            // Log activity
            $activityLog = new ActivitiesLog();
            $activityLog->client_id = $requestData['client_id'];
            $activityLog->created_by = Auth::id();
            $activityLog->subject = 'Appointment created: ' . $requestData['title'];
            $activityLog->description = $requestData['description'] ?? '';
            $activityLog->followup_date = $followupDateTime->toDateTimeString();
            $activityLog->task_status = 0;
            $activityLog->pin = 0;
            $activityLog->save();

            // Return JSON response matching expected format (status instead of success)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => true,
                    'success' => true, // Also include for compatibility
                    'message' => 'Appointment created successfully'
                ]);
            }

            return redirect()->back()->with('success', 'Appointment created successfully');
            
        } catch (\Exception $e) {
            \Log::error('Error creating appointment: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => false,
                    'success' => false, // Also include for compatibility
                    'message' => 'Failed to create appointment: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to create appointment: ' . $e->getMessage());
        }
    }

    /**
     * Add booking appointment (new booking system using BookingAppointment model)
     * POST /add-appointment-book
     */
    public function addAppointmentBook(Request $request)
    {
        try {
            $requestData = $request->all();
            
            // Validate required fields
            $validator = Validator::make($requestData, [
                'client_id' => 'required|exists:admins,id',
                'noe_id' => 'required|integer|in:1,2,3,4,5,6,7,8',
                'service_id' => 'required|integer|in:1,2,3',
                'appoint_date' => 'required|date',
                'appoint_time' => 'required|string',
                'description' => 'required|string',
                'appointment_details' => 'required|in:phone,in_person,video_call',
                'preferred_language' => 'required|string',
                'inperson_address' => 'required|in:1,2',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed: ' . $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get client information
            $client = Admin::findOrFail($requestData['client_id']);
            
            // Map service_id from form to actual service_id
            // Form: 1=Free Consultation, 2=Comprehensive Migration Advice, 3=Overseas Applicant Enquiry
            // DB: 1=Paid, 2=Free, 3=Paid Overseas
            $serviceIdMap = [
                1 => 2, // Free Consultation -> Free
                2 => 1, // Comprehensive Migration Advice -> Paid
                3 => 3, // Overseas Applicant Enquiry -> Paid Overseas
            ];
            $serviceId = $serviceIdMap[$requestData['service_id']] ?? 2;

            // Map NOE ID to service_type/enquiry_type
            $noeToServiceType = [
                1 => ['service_type' => 'Permanent Residency', 'enquiry_type' => 'pr'],
                2 => ['service_type' => 'Temporary Residency', 'enquiry_type' => 'tr'],
                3 => ['service_type' => 'JRP/Skill Assessment', 'enquiry_type' => 'jrp'],
                4 => ['service_type' => 'Tourist Visa', 'enquiry_type' => 'tourist'],
                5 => ['service_type' => 'Education/Student Visa', 'enquiry_type' => 'education'],
                6 => ['service_type' => 'Complex Matters (AAT, Protection visa, Federal Case)', 'enquiry_type' => 'complex'],
                7 => ['service_type' => 'Visa Cancellation/NOICC/Refusals', 'enquiry_type' => 'cancellation'],
                8 => ['service_type' => 'INDIA/UK/CANADA/EUROPE TO AUSTRALIA', 'enquiry_type' => 'international'],
            ];
            $serviceTypeMapping = $noeToServiceType[$requestData['noe_id']] ?? ['service_type' => 'Other', 'enquiry_type' => 'other'];

            // Map location
            $locationMap = [1 => 'adelaide', 2 => 'melbourne'];
            $location = $locationMap[$requestData['inperson_address']] ?? 'melbourne';

            // Map meeting type
            $meetingTypeMap = [
                'phone' => 'phone',
                'in_person' => 'in_person',
                'video_call' => 'video',
            ];
            $meetingType = $meetingTypeMap[$requestData['appointment_details']] ?? 'in_person';

            // Parse appointment time - handle different formats
            // Time can be in format "10:00 AM - 10:15 AM" or "10:00 AM" or "10:00:00"
            $timeStr = trim($requestData['appoint_time']);
            
            // Extract start time if in range format (e.g., "10:00 AM - 10:15 AM")
            if (preg_match('/^([0-9]{1,2}:[0-9]{2}\s*(?:AM|PM)?)/i', $timeStr, $matches)) {
                $timeStr = trim($matches[1]);
            }
            
            // Parse time - handle 12-hour format with AM/PM
            try {
                if (preg_match('/(AM|PM)/i', $timeStr)) {
                    // 12-hour format with AM/PM
                    $parsedTime = Carbon::createFromFormat('g:i A', $timeStr);
                    $timeStr = $parsedTime->format('H:i');
                } else {
                    // 24-hour format - extract just HH:MM
                    if (preg_match('/^(\d{1,2}):(\d{2})/', $timeStr, $timeMatches)) {
                        $timeStr = $timeMatches[1] . ':' . $timeMatches[2];
                    }
                }
            } catch (\Exception $e) {
                // If parsing fails, try to extract HH:MM format
                if (preg_match('/^(\d{1,2}):(\d{2})/', $timeStr, $timeMatches)) {
                    $timeStr = $timeMatches[1] . ':' . $timeMatches[2];
                } else {
                    throw new \Exception('Invalid time format: ' . $requestData['appoint_time']);
                }
            }

            // Combine date and time
            $dateStr = $requestData['appoint_date'];
            $timezone = $requestData['timezone'] ?? 'Australia/Melbourne';
            
            try {
                $appointmentDateTime = Carbon::createFromFormat('Y-m-d H:i', $dateStr . ' ' . $timeStr, $timezone)
                    ->setTimezone(config('app.timezone', 'UTC'));
            } catch (\Exception $e) {
                // Try alternative date format
                try {
                    $appointmentDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $dateStr . ' ' . $timeStr . ':00', $timezone)
                        ->setTimezone(config('app.timezone', 'UTC'));
                } catch (\Exception $e2) {
                    throw new \Exception('Invalid date/time format. Date: ' . $dateStr . ', Time: ' . $timeStr);
                }
            }

            // Calculate duration based on service
            // Service 1 (Free Consultation) = 15 min, Service 2/3 (Paid) = 30 min
            $durationMinutes = $requestData['service_id'] == 1 ? 15 : 30;

            // Use ConsultantAssignmentService to assign consultant
            $consultantAssigner = app(\App\Services\BansalAppointmentSync\ConsultantAssignmentService::class);
            $appointmentDataForConsultant = [
                'noe_id' => $requestData['noe_id'],
                'service_id' => $serviceId,
                'location' => $location,
                'inperson_address' => $requestData['inperson_address'],
            ];
            $consultant = $consultantAssigner->assignConsultant($appointmentDataForConsultant);

            if (!$consultant) {
                return response()->json([
                    'status' => false,
                    'message' => 'Could not assign consultant. Please ensure consultants are set up for the selected service type.'
                ], 422);
            }

            // Generate unique bansal_appointment_id for manually created appointments
            // Use timestamp + random to ensure uniqueness (manual appointments start from 1000000)
            $bansalAppointmentId = 1000000 + (time() % 900000) + mt_rand(1, 99999);

            // Ensure uniqueness
            while (BookingAppointment::where('bansal_appointment_id', $bansalAppointmentId)->exists()) {
                $bansalAppointmentId = 1000000 + (time() % 900000) + mt_rand(1, 99999);
            }

            // Create booking appointment
            $appointment = BookingAppointment::create([
                'bansal_appointment_id' => $bansalAppointmentId,
                'order_hash' => null, // No payment for manually created appointments
                
                'client_id' => $client->id,
                'consultant_id' => $consultant->id,
                'assigned_by_admin_id' => Auth::id(),
                
                'client_name' => $client->first_name . ' ' . ($client->last_name ?? ''),
                'client_email' => $client->email ?? '',
                'client_phone' => $client->phone ?? null,
                'client_timezone' => $requestData['timezone'] ?? 'Australia/Melbourne',
                
                'appointment_datetime' => $appointmentDateTime,
                'timeslot_full' => $requestData['appoint_time'], // Store as provided
                'duration_minutes' => $durationMinutes,
                'location' => $location,
                'inperson_address' => $requestData['inperson_address'],
                'meeting_type' => $meetingType,
                'preferred_language' => $requestData['preferred_language'],
                
                'service_id' => $serviceId,
                'noe_id' => $requestData['noe_id'],
                'enquiry_type' => $serviceTypeMapping['enquiry_type'],
                'service_type' => $serviceTypeMapping['service_type'],
                'enquiry_details' => $requestData['description'],
                
                'status' => 'pending',
                'is_paid' => ($serviceId == 2) ? false : true, // Free service is not paid
                'amount' => ($serviceId == 2) ? 0 : 150, // Set appropriate amounts
                'final_amount' => ($serviceId == 2) ? 0 : 150,
                'payment_status' => ($serviceId == 2) ? null : 'pending',
                'user_id' => Auth::id(),
            ]);

            // Log activity
            $activityLog = new ActivitiesLog();
            $activityLog->client_id = $client->id;
            $activityLog->created_by = Auth::id();
            $activityLog->subject = 'Booking appointment created: ' . $serviceTypeMapping['service_type'];
            $activityLog->description = $requestData['description'];
            $activityLog->task_status = 0;
            $activityLog->pin = 0;
            $activityLog->save();

            // Return JSON response matching expected format
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => true,
                    'success' => true,
                    'message' => 'Appointment booked successfully'
                ]);
            }

            return redirect()->back()->with('success', 'Appointment booked successfully');
            
        } catch (\Exception $e) {
            \Log::error('Error creating booking appointment: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => false,
                    'success' => false,
                    'message' => 'Failed to create appointment: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to create appointment: ' . $e->getMessage());
        }
    }

}
