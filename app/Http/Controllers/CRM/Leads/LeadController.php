<?php
namespace App\Http\Controllers\CRM\Leads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;
use App\Models\Lead;
use App\Models\ClientContact;
use App\Models\ClientEmail;
use App\Models\ClientVisaCountry;
use App\Models\ClientPassportInformation;
use App\Models\Matter;
use Carbon\Carbon;
use App\Traits\ClientHelpers;
use App\Services\ClientReferenceService;

class LeadController extends Controller
{
    use ClientHelpers;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of leads
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = \App\Models\UserRole::find(Auth::user()->role);
        $module_access = (array) json_decode($roles->module_access ?? '[]');
        
        $statusOptions = collect();
        $qualityOptions = collect();
        $perPage = 20;
        if (array_key_exists('20', $module_access)) {
            // Using Lead model - automatically filters by role=7, type='lead', and is_deleted=null
            $query = Lead::where('is_archived', 0);

            $totalData = $query->count();
            
            // Apply filters using modern syntax
            $query->when($request->filled('client_id'), function ($q) use ($request) {
                return $q->where('client_id', $request->input('client_id'));
            });

            $query->when($request->filled('type'), function ($q) use ($request) {
                return $q->where('type', $request->input('type'));
            });

            $query->when($request->filled('name'), function ($q) use ($request) {
                return $q->where('first_name', 'LIKE', '%' . $request->input('name') . '%');
            });

            $query->when($request->filled('email'), function ($q) use ($request) {
                return $q->where('email', $request->input('email'));
            });

            $query->when($request->filled('phone'), function ($q) use ($request) {
                return $q->where(function ($subQuery) use ($request) {
                    $subQuery->where('phone', 'LIKE', '%' . $request->input('phone') . '%')
                             ->orWhere('att_phone', 'LIKE', '%' . $request->input('phone') . '%');
                });
            });

            $query->when($request->filled('service'), function ($q) use ($request) {
                return $q->where('service', 'LIKE', '%' . $request->input('service') . '%');
            });

            $query->when($request->filled('lead_quality'), function ($q) use ($request) {
                return $q->where('lead_quality', $request->input('lead_quality'));
            });

            $query->when($request->filled('status_filter'), function ($q) use ($request) {
                return $q->where('status', $request->input('status_filter'));
            });

            if ($request->filled('quick_date_range') || $request->filled('from_date') || $request->filled('to_date')) {
                [$startDate, $endDate] = $this->resolveLeadDateRange($request);
                $dateColumn = $request->input('date_filter_field', 'created_at');

                if ($startDate && $endDate && in_array($dateColumn, ['created_at', 'updated_at'], true)) {
                    $query->whereBetween($dateColumn, [$startDate, $endDate]);
                }
            }

            $allowedPerPage = [10, 20, 50, 100, 200];
            $perPage = (int) $request->get('per_page', 20);
            if (!in_array($perPage, $allowedPerPage, true)) {
                $perPage = 20;
            }

            $statusOptions = Lead::select('status')
                ->distinct()
                ->whereNotNull('status')
                ->orderBy('status')
                ->pluck('status');

            $qualityOptions = Lead::select('lead_quality')
                ->distinct()
                ->whereNotNull('lead_quality')
                ->orderBy('lead_quality')
                ->pluck('lead_quality');

            $lists = $query->sortable(['id' => 'desc'])
                ->paginate($perPage)
                ->appends($request->except('page'));
        } else {
            $lists = Lead::whereRaw('1 = 0')->sortable(['id' => 'desc'])->paginate($perPage);
            $totalData = 0;
        }
        
        return view('crm.leads.index', compact('lists', 'totalData', 'perPage', 'statusOptions', 'qualityOptions'));
    }

    /**
     * Resolve quick or manual date range for filtering leads.
     */
    protected function resolveLeadDateRange(Request $request): array
    {
        $quickRange = $request->input('quick_date_range');
        if (!empty($quickRange)) {
            $range = $this->getLeadQuickDateRangeBounds($quickRange);
            if ($range[0] && $range[1]) {
                return $range;
            }
        }

        $from = $this->parseLeadDate($request->input('from_date'));
        $to = $this->parseLeadDate($request->input('to_date'), true);

        if ($from || $to) {
            $start = $from ?? Carbon::now()->subYears(20)->startOfDay();
            $end = $to ?? Carbon::now()->endOfDay();

            return [$start, $end];
        }

        return [null, null];
    }

    /**
     * Map quick filter keys to Carbon ranges.
     */
    protected function getLeadQuickDateRangeBounds(string $range): array
    {
        $now = Carbon::now();

        switch ($range) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'this_week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'this_month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'last_month':
                $start = $now->copy()->subMonth()->startOfMonth();
                $end = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'last_30_days':
                $start = $now->copy()->subDays(30)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'last_90_days':
                $start = $now->copy()->subDays(90)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'this_year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
            case 'last_year':
                $start = $now->copy()->subYear()->startOfYear();
                $end = $now->copy()->subYear()->endOfYear();
                break;
            default:
                return [null, null];
        }

        return [$start, $end];
    }

    /**
     * Parse incoming date strings supporting multiple formats.
     */
    protected function parseLeadDate(?string $value, bool $endOfDay = false): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        $formats = ['d/m/Y', 'Y-m-d'];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);
                return $endOfDay ? $date->endOfDay() : $date->startOfDay();
            } catch (\Throwable $th) {
                continue;
            }
        }

        return null;
    }

    /**
     * Display the specified lead's details
     * Shows comprehensive view of a single lead
     */
    public function detail(Request $request, $id = null)
    {
        if (isset($id) && !empty($id)) {
            $id = $this->decodeString($id);
            
            if (!$id) {
                return Redirect::to('/leads')->with('error', config('constants.decode_string'));
            }
            
            // Using Lead model with withArchived scope to include archived leads
            $fetchedData = Lead::withArchived()->where('id', $id)->first();
            
            if ($fetchedData) {
                return view('crm.leads.detail', compact('fetchedData'));
            } else {
                return Redirect::to('/leads')->with('error', 'Lead does not exist');
            }
        } else {
            return Redirect::to('/leads')->with('error', config('constants.unauthorized'));
        }
    }

    /**
     * Show the form for creating a new lead
     */
    public function create(Request $request)
    {
        // Get countries for dropdowns
        $countries = \App\Models\Country::orderBy('name', 'asc')->get();
        
        return view('crm.leads.create', compact('countries'));
    }

    /**
     * Store a newly created lead
     */
    public function store(Request $request)
    {
        // Debug logging
        Log::info('Lead store method called');
        Log::info('Request method: ' . $request->method());
        Log::info('Request data: ' . json_encode($request->all()));
        
        if ($request->isMethod('post')) {
            $requestData = $request->all();
            
            // Extract phone and email (now only one of each)
            $primaryPhone = $requestData['phone'][0] ?? null;
            $primaryEmail = $requestData['email'][0] ?? null;
            
            Log::info('Primary phone: ' . $primaryPhone);
            Log::info('Primary email: ' . $primaryEmail);

            // Validate required fields
            try {
                $this->validate($request, [
                    'first_name' => 'required|max:255',
                    'last_name' => 'required|max:255',
                    'gender' => 'required|max:255',
                    'dob' => 'required',
                    'phone.0' => 'required|max:255',
                    'email.0' => 'required|email|max:255',
                ], [
                    'phone.0.required' => 'Phone number is required.',
                    'email.0.required' => 'Email address is required.',
                    'email.0.email' => 'Please enter a valid email address.',
                ]);
                Log::info('Validation passed');
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Validation failed: ' . json_encode($e->errors()));
                throw $e; // Re-throw to maintain normal flow
            }
            
           

            // Custom validation for uniqueness of phone and email fields
            $errors = [];

            // Validate uniqueness for phone number (check both admins table and client_contacts table)
            if ($primaryPhone) {
                // Check in admins table (primary phone) - check all records regardless of role/type
                $existingPhone = Admin::where('phone', $primaryPhone)->first();
                if ($existingPhone) {
                    $errors["phone.0"] = "This phone number is already registered.";
                }
                
                // Check in client_contacts table (all phone numbers)
                $existingContact = ClientContact::where('phone', $primaryPhone)->first();
                if ($existingContact) {
                    $errors["phone.0"] = "This phone number is already registered.";
                }
            }

            // Validate uniqueness for email address (check both admins table and client_emails table)
            if ($primaryEmail) {
                // Check in admins table (primary email) - check all records regardless of role/type
                $existingEmail = Admin::where('email', $primaryEmail)->first();
                if ($existingEmail) {
                    $errors["email.0"] = "This email address is already registered.";
                }
                
                // Check in client_emails table (all email addresses)
                $existingClientEmail = ClientEmail::where('email', $primaryEmail)->first();
                if ($existingClientEmail) {
                    $errors["email.0"] = "This email address is already registered.";
                }
            }

            // If there are any custom errors, return them
            if (!empty($errors)) {
                Log::warning('Custom validation errors: ' . json_encode($errors));
                return redirect()->back()
                    ->withInput()
                    ->withErrors($errors);
            }
            
            Log::info('Custom validation passed - proceeding to insert');
            


            // Process dates with validation
            $dob = null;
            if (!empty($requestData['dob'])) {
                $dobs = explode('/', $requestData['dob']);
                if (count($dobs) === 3) {
                    $dob = $dobs[2] . '-' . $dobs[1] . '-' . $dobs[0];
                }
            }


            // Use database transaction for data integrity
            DB::beginTransaction();
            
            try {
                // Generate client_counter and client_id using centralized service
                // This prevents race conditions and duplicate references
                $referenceService = app(ClientReferenceService::class);
                $reference = $referenceService->generateClientReference($requestData['first_name']);
                $client_id = $reference['client_id'];
                $client_current_counter = $reference['client_counter'];


                // Create new lead using DB query builder - only fields from simplified form
                $adminData = [
                    // System fields
                    'user_id' => Auth::user()->id,
                    'password' => '', // Set empty password for leads (password field is NOT nullable)
                    'client_counter' => $client_current_counter,
                    'client_id' => $client_id,
                    'status' => '1', // Default status: 1 (Active)
                    'role' => 7, // Lead role
                    'type' => 'lead', // Lead type
                    'is_archived' => 0, // Not archived
                    'is_deleted' => null, // Not deleted
                    
                    // Form fields from simplified create form
                    'first_name' => $requestData['first_name'],
                    'last_name' => $requestData['last_name'],
                    'gender' => $requestData['gender'],
                    'dob' => $dob,
                    'age' => $requestData['age'] ?? null,
                    'marital_status' => $requestData['marital_status'] ?? null,
                    
                    // Contact information
                    'contact_type' => $requestData['contact_type_hidden'][0] ?? null,
                    'country_code' => $requestData['country_code'][0] ?? null,
                    'phone' => $primaryPhone,
                    'email_type' => $requestData['email_type_hidden'][0] ?? null,
                    'email' => $primaryEmail,
                    
                    // Timestamps
                    'created_at' => now(),
                    'updated_at' => now(),
                ];


                Log::info('Attempting to insert lead into database');
                Log::info('Admin data to insert: ' . json_encode($adminData));
                
                try {
                    // Insert into admins table and get the ID
                    $adminId = DB::table('admins')->insertGetId($adminData);
                    Log::info('Lead inserted successfully with ID: ' . $adminId);
                    
                    // Create an object to maintain compatibility with existing code
                    $admin = (object) array_merge($adminData, ['id' => $adminId]);
                    
                    // Validate insert was successful
                    if (!$admin->id) {
                        throw new \Exception('Failed to insert lead - no ID returned');
                    }
                } catch (\Illuminate\Database\QueryException $queryException) {
                    // Handle database-specific errors
                    Log::error('Database query failed: ' . $queryException->getMessage());
                    Log::error('SQL Error Code: ' . $queryException->getCode());
                    Log::error('Failed data: ' . json_encode($adminData));
                    throw $queryException; // Re-throw to be caught by outer try-catch
                } catch (\Exception $saveException) {
                    Log::error('Insert operation failed: ' . $saveException->getMessage());
                    Log::error('Insert exception details: ' . $saveException->getTraceAsString());
                    throw $saveException; // Re-throw to be caught by outer try-catch
                }
                
                // Save phone number to client_contacts table
                if ($primaryPhone) {
                    $contactType = $requestData['contact_type_hidden'][0] ?? 'Personal';
                    $countryCode = $requestData['country_code'][0] ?? '';
                    
                    ClientContact::create([
                        'admin_id' => Auth::user()->id,
                        'client_id' => $admin->id,
                        'contact_type' => $contactType,
                        'phone' => $primaryPhone,
                        'country_code' => $countryCode,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                // Save email to client_emails table
                if ($primaryEmail) {
                    $emailType = $requestData['email_type_hidden'][0] ?? 'Personal';
                    
                    ClientEmail::create([
                        'admin_id' => Auth::user()->id,
                        'client_id' => $admin->id,
                        'email_type' => $emailType,
                        'email' => $primaryEmail,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                DB::commit();
                Log::info('Transaction committed successfully');
                
                // Encode the client/lead ID for the URL
                $encodedId = base64_encode(convert_uuencode($admin->id));
                Log::info('Redirecting to edit page with encoded ID: ' . $encodedId);
                
                return redirect()->route('clients.edit', ['id' => $encodedId])
                    ->with('success', 'Lead added successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                
                Log::error('Lead creation failed: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                
                // Clean up uploaded file if exists
                // No profile image to clean up
                
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'Failed to create lead: ' . $e->getMessage()]);
            }
        }
        
        // If not POST, return error
        Log::error('Invalid request method - not POST. Method was: ' . $request->method());
        return redirect()->route('leads.create')
            ->with('error', 'Invalid request method');
    }

    /**
     * Show the form for editing the specified lead
     */
    public function edit(Request $request, $id)
    {
        // Check authorization
        $check = $this->checkAuthorizationAction('edit_lead', $request->route()->getActionMethod(), Auth::user()->role);
        if ($check) {
            return Redirect::to('/dashboard')->with('error', config('constants.unauthorized'));
        }

        $id = $this->decodeString($id);
        
        if (!$id) {
            return Redirect::to('/leads')->with('error', config('constants.decode_string'));
        }
        
        // Using Lead model - automatically handles filtering
        $fetchedData = Lead::find($id);
        
        if (!$fetchedData) {
            return Redirect::to('/leads')->with('error', 'Lead not found');
        }

        // Get countries for dropdown
        $countries = \App\Models\Country::orderBy('name', 'asc')->get();
        
        // Load contact data (required by edit form)
        $clientContacts = ClientContact::where('client_id', $id)->get() ?? collect();
        $emails = ClientEmail::where('client_id', $id)->get() ?? collect();
        
        // Load other related data for the edit form
        $visaCountries = \App\Models\ClientVisaCountry::where('client_id', $id)
            ->with('matter:id,title,nick_name')
            ->get() ?? collect();
        $clientPassports = \App\Models\ClientPassportInformation::where('client_id', $id)->get() ?? collect();
        $clientAddresses = \App\Models\ClientAddress::where('client_id', $id)
            ->orderBy('created_at', 'desc')
            ->get() ?? collect();
        $clientTravels = \App\Models\ClientTravelInformation::where('client_id', $id)
            ->orderByRaw('travel_arrival_date ASC NULLS LAST')
            ->get() ?? collect();
        $visaTypes = \App\Models\Matter::where('title', 'not like', '%skill assessment%')
            ->where('status', 1)
            ->orderBy('title', 'ASC')
            ->get();
        
        return view('crm.leads.edit', compact(
            'fetchedData', 'countries', 'clientContacts', 'emails', 
            'visaCountries', 'clientPassports', 'clientAddresses', 'clientTravels', 'visaTypes'
        ));
    }

    /**
     * Update the specified lead in storage
     */
    public function update(Request $request, $id)
    {
        // Check authorization
        $check = $this->checkAuthorizationAction('edit_lead', $request->route()->getActionMethod(), Auth::user()->role);
        if ($check) {
            return Redirect::to('/dashboard')->with('error', config('constants.unauthorized'));
        }

        $id = $this->decodeString($id);
        
        if (!$id) {
            return Redirect::to('/leads')->with('error', config('constants.decode_string'));
        }

        $requestData = $request->all();
        $requestData['id'] = $id; // Ensure ID is set for validation
        
        // Validate basic fields only (NOT phone/email as they are arrays)
        $this->validate($request, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'gender' => 'required|max:255',
            'dob' => 'required',
        ]);

        // Custom validation for phone array
        if (empty($requestData['phone']) || !array_filter($requestData['phone'])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['phone' => 'At least one phone number is required.']);
        }

        // Custom validation for email array
        if (empty($requestData['email']) || !array_filter($requestData['email'])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['email' => 'At least one email address is required.']);
        }

        // Check for duplicate phones (excluding current lead)
        foreach ($requestData['phone'] as $phone) {
            if (!empty($phone)) {
                $existingPhone = Lead::where('phone', $phone)
                    ->where('id', '!=', $id)
                    ->first();
                if ($existingPhone) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['phone' => "Phone number {$phone} is already registered."]);
                }
            }
        }

        // Check for duplicate emails (excluding current lead)
        foreach ($requestData['email'] as $email) {
            if (!empty($email)) {
                $existingEmail = Lead::where('email', $email)
                    ->where('id', '!=', $id)
                    ->first();
                if ($existingEmail) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['email' => "Email {$email} is already registered."]);
                }
            }
        }

        // Find the lead by ID using Lead model
        $lead = Lead::find($id);
        
        // Check if the lead exists
        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found.');
        }

        // Process related files with type validation
        $related_files = '';
        if (isset($requestData['related_files']) && is_array($requestData['related_files'])) {
            $related_files = implode(',', $requestData['related_files']);
        }

        // Process dates with validation
        $dob = null;
        if (!empty($requestData['dob'])) {
            $dobs = explode('/', $requestData['dob']);
            if (count($dobs) === 3) {
                $dob = $dobs[2] . '-' . $dobs[1] . '-' . $dobs[0];
            }
        }

        $visa_expiry_date = null;
        if (!empty($requestData['visa_expiry_date'])) {
            $visa_expiry_dates = explode('/', $requestData['visa_expiry_date']);
            if (count($visa_expiry_dates) === 3) {
                $visa_expiry_date = $visa_expiry_dates[2] . '-' . $visa_expiry_dates[1] . '-' . $visa_expiry_dates[0];
            }
        }

        // Use database transaction for data integrity
        DB::beginTransaction();
        
        try {
            // Update lead data
            $lead->first_name = $requestData['first_name'];
            $lead->last_name = $requestData['last_name'];
            $lead->gender = $requestData['gender'];
            $lead->dob = $dob;
            $lead->age = $requestData['age'] ?? null;
            $lead->marital_status = $requestData['marital_status'] ?? null;
            $lead->passport_number = $requestData['passport_no'] ?? null;
            $lead->visa_type = $requestData['visa_type'] ?? null;
            $lead->visaExpiry = $visa_expiry_date;
            $lead->tagname = $requestData['tags_label'] ?? null;
            
            // Extract LAST phone from array (following ClientPersonalDetailsController pattern)
            $lastPhone = null;
            $lastCountryCode = null;
            $lastContactType = null;
            
            if (isset($requestData['phone']) && is_array($requestData['phone'])) {
                $phoneCount = count($requestData['phone']);
                for ($i = $phoneCount - 1; $i >= 0; $i--) {
                    if (!empty($requestData['phone'][$i])) {
                        $lastPhone = $requestData['phone'][$i];
                        $lastCountryCode = $requestData['country_code'][$i] ?? null;
                        $lastContactType = $requestData['contact_type_hidden'][$i] ?? null;
                        break;
                    }
                }
            }
            
            // Extract LAST email from array (following ClientPersonalDetailsController pattern)
            $lastEmail = null;
            $lastEmailType = null;
            
            if (isset($requestData['email']) && is_array($requestData['email'])) {
                $emailCount = count($requestData['email']);
                for ($i = $emailCount - 1; $i >= 0; $i--) {
                    if (!empty($requestData['email'][$i])) {
                        $lastEmail = $requestData['email'][$i];
                        $lastEmailType = $requestData['email_type_hidden'][$i] ?? null;
                        break;
                    }
                }
            }
            
            $lead->contact_type = $lastContactType;
            $lead->country_code = $lastCountryCode;
            $lead->phone = $lastPhone;
            $lead->email_type = $lastEmailType;
            $lead->email = $lastEmail;
            $lead->service = $requestData['service'] ?? null;
            $lead->assignee = $requestData['assign_to'] ?? null;
            $lead->status = $requestData['status'] ?? null;
            $lead->lead_quality = $requestData['lead_quality'] ?? null;
            $lead->att_country_code = $requestData['att_country_code'] ?? null;
            $lead->att_phone = $requestData['att_phone'] ?? null;
            $lead->att_email = $requestData['att_email'] ?? null;
            $lead->source = $requestData['lead_source'] ?? null;
            $lead->related_files = rtrim($related_files, ',');

            // Handle profile image upload with error handling
            if ($request->hasfile('profile_img')) {
                $new_profile_img = $this->uploadFile($request->file('profile_img'), config('constants.profile_imgs'));
                
                if ($new_profile_img) {
                    // Only delete old image after successful upload
                    if (!empty($requestData['old_profile_img'])) {
                        $this->unlinkFile($requestData['old_profile_img'], config('constants.profile_imgs'));
                    }
                    $lead->profile_img = $new_profile_img;
                } else {
                    throw new \Exception('Profile image upload failed');
                }
            } else {
                $lead->profile_img = $requestData['old_profile_img'] ?? null;
            }

            // Additional fields with null coalescing
            $lead->preferredIntake = $requestData['preferredIntake'] ?? null;
            $lead->country_passport = $requestData['country_passport'] ?? null;
            $lead->address = $requestData['address'] ?? null;
            $lead->city = $requestData['city'] ?? null;
            $lead->state = $requestData['state'] ?? null;
            $lead->zip = $requestData['zip'] ?? null;
            $lead->country = $requestData['country'] ?? null;
            $lead->nomi_occupation = $requestData['nomi_occupation'] ?? null;
            $lead->skill_assessment = $requestData['skill_assessment'] ?? null;
            $lead->high_quali_aus = $requestData['high_quali_aus'] ?? null;
            $lead->high_quali_overseas = $requestData['high_quali_overseas'] ?? null;
            $lead->relevant_work_exp_aus = $requestData['relevant_work_exp_aus'] ?? null;
            $lead->relevant_work_exp_over = $requestData['relevant_work_exp_over'] ?? null;
            $lead->naati_py = $requestData['naati_py'] ?? null;
            $lead->married_partner = $requestData['married_partner'] ?? null;
            $lead->total_points = $requestData['total_points'] ?? null;
            $lead->start_process = $requestData['start_process'] ?? null;

            $lead->save();
            
            // Update phone numbers in client_contacts table (following ClientPersonalDetailsController pattern)
            if (isset($requestData['contact_type_hidden']) && is_array($requestData['contact_type_hidden'])) {
                $processedPhoneIds = [];
                
                foreach ($requestData['contact_type_hidden'] as $key => $contactType) {
                    $contactId = $requestData['contact_id'][$key] ?? null;
                    $phone = $requestData['phone'][$key] ?? null;
                    $countryCode = $requestData['country_code'][$key] ?? '';
                    
                    if (!empty($phone)) {
                        if ($contactId) {
                            // Update existing contact
                            $existingContact = ClientContact::find($contactId);
                            if ($existingContact && $existingContact->client_id == $lead->id) {
                                $existingContact->update([
                                    'admin_id' => Auth::user()->id,
                                    'contact_type' => $contactType,
                                    'phone' => $phone,
                                    'country_code' => $countryCode
                                ]);
                                $processedPhoneIds[] = $existingContact->id;
                            }
                        } else {
                            // Create new contact
                            $newContact = ClientContact::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $lead->id,
                                'contact_type' => $contactType,
                                'phone' => $phone,
                                'country_code' => $countryCode
                            ]);
                            $processedPhoneIds[] = $newContact->id;
                        }
                    }
                }
                
                // Delete contacts not in the processed list (user removed them)
                if (!empty($processedPhoneIds)) {
                    ClientContact::where('client_id', $lead->id)
                        ->whereNotIn('id', $processedPhoneIds)
                        ->delete();
                }
            }
            
            // Update emails in client_emails table (following ClientPersonalDetailsController pattern)
            if (isset($requestData['email_type_hidden']) && is_array($requestData['email_type_hidden'])) {
                $processedEmailIds = [];
                
                foreach ($requestData['email_type_hidden'] as $key => $emailType) {
                    $emailId = $requestData['email_id'][$key] ?? null;
                    $email = $requestData['email'][$key] ?? null;
                    
                    if (!empty($email)) {
                        if ($emailId) {
                            // Update existing email
                            $existingEmail = ClientEmail::find($emailId);
                            if ($existingEmail && $existingEmail->client_id == $lead->id) {
                                $existingEmail->update([
                                    'admin_id' => Auth::user()->id,
                                    'email_type' => $emailType,
                                    'email' => $email
                                ]);
                                $processedEmailIds[] = $existingEmail->id;
                            }
                        } else {
                            // Create new email
                            $newEmail = ClientEmail::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $lead->id,
                                'email_type' => $emailType,
                                'email' => $email
                            ]);
                            $processedEmailIds[] = $newEmail->id;
                        }
                    }
                }
                
                // Delete emails not in the processed list (user removed them)
                if (!empty($processedEmailIds)) {
                    ClientEmail::where('client_id', $lead->id)
                        ->whereNotIn('id', $processedEmailIds)
                        ->delete();
                }
            }
            
            DB::commit();
            
            return redirect()->route('leads.edit', base64_encode(convert_uuencode($id)))
                ->with('success', 'Lead updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', config('constants.server_error'));
        }
    }

    /**
     * Display the specified lead's history
     * Anyone can view lead history
     */
    public function history(Request $request, $id = null)
    {
        if (isset($id) && !empty($id)) {
            $id = $this->decodeString($id);
            
            if (!$id) {
                return Redirect::to('/leads')->with('error', config('constants.decode_string'));
            }
            
            // Using Lead model with withArchived scope to include archived leads
            $fetchedData = Lead::withArchived()->where('id', $id)->first();
            
            if ($fetchedData) {
                return view('crm.leads.history', compact('fetchedData'));
            } else {
                return Redirect::to('/leads')->with('error', 'Lead does not exist');
            }
        } else {
            return Redirect::to('/leads')->with('error', config('constants.unauthorized'));
        }
    }

    /**
     * Check if email is unique across leads AND clients
     * Prevents duplicate emails in the system
     */
    public function is_email_unique(Request $request)
    {
        $email = $request->input('email');
        $excludeId = $request->input('id'); // Optional - for edit operations
        
        // Check in leads (admins table where role=7, type='lead')
        $leadQuery = Lead::where('email', $email);
        if ($excludeId) {
            $leadQuery->where('id', '!=', $excludeId);
        }
        $lead_count = $leadQuery->count();
        
        // Check in clients (admins table where role=7, type='client')
        $client_count = Admin::where('role', 7)
            ->where('type', 'client')
            ->where('email', $email)
            ->when($excludeId, function($q) use ($excludeId) {
                return $q->where('id', '!=', $excludeId);
            })
            ->count();
        
        $total_count = $lead_count + $client_count;
        
        $response = [
            'status' => $total_count > 0 ? 1 : 0,
            'message' => $total_count > 0 ? 'The email has already been taken.' : '',
        ];
        
        return response()->json($response);
    }

    /**
     * Check if contact number is unique across leads AND clients
     * Prevents duplicate phone numbers in the system
     */
    public function is_contactno_unique(Request $request)
    {
        $contact = $request->input('contact');
        $excludeId = $request->input('id'); // Optional - for edit operations
        
        // Check in leads (admins table where role=7, type='lead')
        $leadQuery = Lead::where('phone', 'LIKE', '%' . $contact . '%');
        if ($excludeId) {
            $leadQuery->where('id', '!=', $excludeId);
        }
        $lead_count = $leadQuery->count();
        
        // Check in clients (admins table where role=7, type='client')
        $client_count = Admin::where('role', 7)
            ->where('type', 'client')
            ->where('phone', 'LIKE', '%' . $contact . '%')
            ->when($excludeId, function($q) use ($excludeId) {
                return $q->where('id', '!=', $excludeId);
            })
            ->count();
        
        $total_count = $lead_count + $client_count;
        
        $response = [
            'status' => $total_count > 0 ? 1 : 0,
            'message' => $total_count > 0 ? 'The phone has already been taken.' : '',
        ];
        
        return response()->json($response);
    }

    /**
     * Legacy method - Lead pin functionality (deprecated)
     */
    public function leadPin(Request $request, $id)
    {
        return redirect()->back()->with('error', 'Followup functionality has been removed');
    }

    /**
     * Legacy method - Delete lead notes (deprecated)
     */
    public function leaddeleteNotes(Request $request, $id = null)
    {
        return redirect()->back()->with('error', 'Followup functionality has been removed');
    }

    /**
     * Legacy method - Get note detail (deprecated)
     */
    public function getnotedetail(Request $request)
    {
        return response()->json([
            'status' => 0,
            'message' => 'Followup functionality has been removed'
        ]);
    }

    /**
     * Decode string helper method - consistent with parent behavior
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
}
