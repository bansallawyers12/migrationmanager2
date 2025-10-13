<?php
namespace App\Http\Controllers\Admin\Leads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Models\Admin;
use App\Models\Lead;
use Auth;
use Config;
use Carbon\Carbon;

class LeadController extends Controller
{
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
        $newarray = json_decode($roles->module_access);
        $module_access = (array) $newarray;
        
        if(array_key_exists('20', $module_access)) {
            // Using Lead model - automatically filters by role=7, type='lead', and is_deleted=null
            $query = Lead::where('is_archived', '=', '0');

            $totalData = $query->count();
            
            // Apply filters
            if ($request->has('client_id') && trim($request->input('client_id')) != '') {
                $query->where('client_id', '=', $request->input('client_id'));
            }

            if ($request->has('type') && trim($request->input('type')) != '') {
                $query->where('type', 'LIKE', $request->input('type'));
            }

            if ($request->has('name') && trim($request->input('name')) != '') {
                $query->where('first_name', 'LIKE', '%' . $request->input('name') . '%');
            }

            if ($request->has('email') && trim($request->input('email')) != '') {
                $query->where('email', $request->input('email'));
            }

            if ($request->has('phone') && trim($request->input('phone')) != '') {
                $query->where(function($q) use ($request) {
                    $q->where('phone', 'LIKE', '%' . $request->input('phone') . '%')
                      ->orWhere('att_phone', 'LIKE', '%' . $request->input('phone') . '%');
                });
            }

            $lists = $query->sortable(['id' => 'desc'])->paginate(20);
        } else {
            $query = Lead::where('id', '=', '');
            $lists = $query->sortable(['id' => 'desc'])->paginate(20);
            $totalData = 0;
        }
        
        return view('Admin.leads.index', compact(['lists', 'totalData']));
    }

    /**
     * Show the form for creating a new lead
     */
    public function create(Request $request)
    {
        return view('Admin.leads.create');
    }

    /**
     * Store a newly created lead
     */
    public function store(Request $request)
    {
        // Check authorization
        $check = $this->checkAuthorizationAction('add_lead', $request->route()->getActionMethod(), Auth::user()->role);
        if($check) {
            return Redirect::to('/admin/dashboard')->with('error', config('constants.unauthorized'));
        }

        if ($request->isMethod('post')) {
            $this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'gender' => 'required|max:255',
                'contact_type' => 'required|max:255',
                'phone' => 'required|max:255|unique:admins,phone',
                'email_type' => 'required|max:255',
                'email' => 'required|max:255|unique:admins,email',
                'service' => 'required',
                'assign_to' => 'required',
                'lead_quality' => 'required',
                'lead_source' => 'required',
            ]);

            $requestData = $request->all();

            // Process related files
            $related_files = '';
            if(isset($requestData['related_files'])) {
                $related_files = implode(',', $requestData['related_files']);
            }

            // Process dates
            $dob = '';
            if($requestData['dob'] != '') {
                $dobs = explode('/', $requestData['dob']);
                $dob = $dobs[2] . '-' . $dobs[1] . '-' . $dobs[0];
            }

            $visa_expiry_date = '';
            if($requestData['visa_expiry_date'] != '') {
                $visa_expiry_dates = explode('/', $requestData['visa_expiry_date']);
                $visa_expiry_date = $visa_expiry_dates[2] . '-' . $visa_expiry_dates[1] . '-' . $visa_expiry_dates[0];
            }

            // Create new lead using Lead model
            $lead = new Lead;
            $lead->user_id = Auth::user()->id;
            $lead->first_name = $requestData['first_name'];
            $lead->last_name = $requestData['last_name'];
            $lead->gender = $requestData['gender'];
            $lead->dob = $dob;
            $lead->age = $requestData['age'] ?? null;
            $lead->marital_status = $requestData['marital_status'] ?? null;
            $lead->passport_no = $requestData['passport_no'] ?? null;
            $lead->visa_type = $requestData['visa_type'] ?? null;
            $lead->visa_expiry_date = $visa_expiry_date;
            $lead->tags_label = $requestData['tags_label'] ?? null;
            $lead->contact_type = $requestData['contact_type'];
            $lead->country_code = $requestData['country_code'] ?? null;
            $lead->phone = $requestData['phone'];
            $lead->email_type = $requestData['email_type'];
            $lead->email = $requestData['email'];
            $lead->service = $requestData['service'];
            $lead->assignee = $requestData['assign_to'];
            $lead->status = $requestData['status'] ?? null;
            $lead->lead_quality = $requestData['lead_quality'];
            $lead->att_country_code = $requestData['att_country_code'] ?? null;
            $lead->att_phone = $requestData['att_phone'] ?? null;
            $lead->att_email = $requestData['att_email'] ?? null;
            $lead->source = $requestData['lead_source'];
            $lead->related_files = rtrim($related_files, ',');
            $lead->comments_note = $requestData['comments_note'] ?? null;

            // Handle profile image upload
            if($request->hasfile('profile_img')) {
                $profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
                $lead->profile_img = $profile_img;
            }

            // Additional fields
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

            $saved = $lead->save();

            if(!$saved) {
                return redirect()->back()->with('error', Config::get('constants.server_error'));
            } else {
                return Redirect::to('/admin/leads')->with('success', 'Lead added Successfully');
            }
        }
    }

    /**
     * Show the form for editing the specified lead
     */
    public function edit(Request $request, $id = NULL)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'gender' => 'required|max:255',
                'contact_type' => 'required|max:255',
                'phone' => 'required',
                'email_type' => 'required|max:255',
                'email' => 'required|max:255',
                'service' => 'required',
                'assign_to' => 'required',
                'lead_quality' => 'required',
                'lead_source' => 'required',
            ]);

            $requestData = $request->all();

            // Find the lead by ID using Lead model
            $lead = Lead::find($requestData['id']);
            
            // Check if the lead exists
            if (!$lead) {
                return redirect()->back()->with('error', 'Lead not found.');
            }

            // Update lead data
            $lead->first_name = $requestData['first_name'];
            $lead->last_name = $requestData['last_name'];
            $lead->gender = $requestData['gender'];

            // Process dates
            $dob = '';
            if (!empty($requestData['dob'])) {
                $dobs = explode('/', $requestData['dob']);
                $dob = $dobs[2] . '-' . $dobs[1] . '-' . $dobs[0];
            }

            $visa_expiry_date = '';
            if (!empty($requestData['visa_expiry_date'])) {
                $visa_expiry_dates = explode('/', $requestData['visa_expiry_date']);
                $visa_expiry_date = $visa_expiry_dates[2] . '-' . $visa_expiry_dates[1] . '-' . $visa_expiry_dates[0];
            }

            $related_files = isset($requestData['related_files']) ? implode(',', $requestData['related_files']) : '';

            $lead->dob = $dob;
            $lead->age = $requestData['age'];
            $lead->marital_status = $requestData['marital_status'];
            $lead->passport_no = $requestData['passport_no'];
            $lead->visa_type = $requestData['visa_type'];
            $lead->visa_expiry_date = $visa_expiry_date;
            $lead->tags_label = $requestData['tags_label'];
            $lead->contact_type = $requestData['contact_type'];
            $lead->country_code = $requestData['country_code'];
            $lead->phone = $requestData['phone'];
            $lead->email_type = $requestData['email_type'];
            $lead->email = $requestData['email'];
            $lead->service = $requestData['service'];
            $lead->assignee = $requestData['assign_to'];
            $lead->status = $requestData['status'];
            $lead->lead_quality = $requestData['lead_quality'];
            $lead->att_country_code = $requestData['att_country_code'];
            $lead->att_phone = $requestData['att_phone'];
            $lead->att_email = $requestData['att_email'];
            $lead->source = $requestData['lead_source'];
            $lead->related_files = rtrim($related_files, ',');

            // Handle profile image upload
            if ($request->hasfile('profile_img')) {
                if ($requestData['old_profile_img'] != '') {
                    $this->unlinkFile($requestData['old_profile_img'], Config::get('constants.profile_imgs'));
                }
                $profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
            } else {
                $profile_img = $requestData['old_profile_img'];
            }

            $lead->profile_img = $profile_img;
            $lead->preferredIntake = $requestData['preferredIntake'];
            $lead->country_passport = $requestData['country_passport'];
            $lead->address = $requestData['address'];
            $lead->city = $requestData['city'];
            $lead->state = $requestData['state'];
            $lead->zip = $requestData['zip'];
            $lead->country = $requestData['country'];
            $lead->nomi_occupation = $requestData['nomi_occupation'];
            $lead->skill_assessment = $requestData['skill_assessment'];
            $lead->high_quali_aus = $requestData['high_quali_aus'];
            $lead->high_quali_overseas = $requestData['high_quali_overseas'];
            $lead->relevant_work_exp_aus = $requestData['relevant_work_exp_aus'];
            $lead->relevant_work_exp_over = $requestData['relevant_work_exp_over'];
            $lead->naati_py = $requestData['naati_py'];
            $lead->married_partner = $requestData['married_partner'];
            $lead->total_points = $requestData['total_points'];
            $lead->start_process = $requestData['start_process'];

            if (!$lead->save()) {
                return redirect()->back()->with('error', Config::get('constants.server_error'));
            } else {
                return Redirect::to('/admin/leads/edit/' . base64_encode(convert_uuencode($requestData['id'])))->with('success', 'Lead updated successfully.');
            }
        } else {
            if (isset($id) && !empty($id)) {
                $id = $this->decodeString($id);
                // Using Lead model - automatically handles filtering
                $fetchedData = Lead::find($id);
                
                if ($fetchedData) {
                    // Get countries for dropdown
                    $countries = \App\Models\Country::all();
                    return view('Admin.leads.edit', compact('fetchedData', 'countries'));
                } else {
                    return Redirect::to('/admin/leads')->with('error', 'leads not found.');
                }
            } else {
                return Redirect::to('/admin/leads')->with('error', Config::get('constants.unauthorized'));
            }
        }
    }

    /**
     * Display the specified lead's history
     */
    public function history(Request $request, $id = NULL)
    {
        if(isset($id) && !empty($id)) {
            $id = $this->decodeString($id);
            // Using Lead model with withArchived scope to include archived leads
            $fetchedData = Lead::withArchived()->where('id', '=', $id)->first();
            
            if($fetchedData) {
                return view('Admin.leads.history', compact(['fetchedData']));
            } else {
                return Redirect::to('/admin/leads')->with('error', 'Lead Not Exist');
            }
        } else {
            return Redirect::to('/admin/leads')->with('error', Config::get('constants.unauthorized'));
        }
    }

    /**
     * Check if email is unique
     */
    public function is_email_unique(Request $request)
    {
        $email = $request->email;
        $email_count = Lead::where('email', $email)->count();
        
        if($email_count > 0) {
            $response['status'] = 1;
            $response['message'] = "The email has already been taken.";
        } else {
            $response['status'] = 0;
            $response['message'] = "";
        }
        
        echo json_encode($response);
    }

    /**
     * Check if contact number is unique
     */
    public function is_contactno_unique(Request $request)
    {
        $contact = $request->contact;
        $phone_count = Lead::where('phone', 'LIKE', '%' . $contact . '%')->count();
        
        if($phone_count > 0) {
            $response['status'] = 1;
            $response['message'] = "The phone has already been taken.";
        } else {
            $response['status'] = 0;
            $response['message'] = "";
        }
        
        echo json_encode($response);
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
    public function leaddeleteNotes(Request $request, $id = Null)
    {
        return redirect()->back()->with('error', 'Followup functionality has been removed');
    }

    /**
     * Legacy method - Get note detail (deprecated)
     */
    public function getnotedetail(Request $request)
    {
        echo 'Followup functionality has been removed';
    }

    /**
     * Decode string helper method - overrides parent method
     */
    public function decodeString($string = NULL)
    {
        if (base64_encode(base64_decode($string, true)) === $string) {
            return convert_uudecode(base64_decode($string));
        }
        return $string;
    }
}
