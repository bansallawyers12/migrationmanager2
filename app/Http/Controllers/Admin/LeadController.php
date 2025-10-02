<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Lead;
use App\Models\FollowupType;
use App\Package;
use App\Models\Followup;

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
     * All Vendors.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		$roles = \App\Models\UserRole::find(Auth::user()->role);
		$newarray = json_decode($roles->module_access);
		$module_access = (array) $newarray;
		if(array_key_exists('20',  $module_access)) {
		    $query 		= Admin::where('is_archived', '=', '0')->where('role', '=', '7')->where('type', '=', 'lead')->whereNull('is_deleted');

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

            if ($request->has('name'))
            {
                $name 		= 	$request->input('name');
                if(trim($name) != '')
                {
                    $query->where('first_name', 'LIKE', '%'.$name.'%');
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

            if ($request->has('phone'))
            {
                $phone 		= 	$request->input('phone');
                if(trim($phone) != '')
                {
                    //$query->where('phone', $phone);
                    $query->where('phone', 'LIKE','%'.$phone.'%')->orwhere('att_phone', 'LIKE','%'.$phone.'%');
                }
            }
            //$lists		= $query->toSql(); //dd($lists);
            $lists		= $query->sortable(['id' => 'desc'])->paginate(20);

		} else {
		    $query 		= Admin::where('id', '=', '')->where('role', '=', '7')->whereNull('is_deleted');
		    $lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		    $totalData = 0;
		}
			return view('Admin.leads.index', compact(['lists', 'totalData']));
		//return view('Admin.leads.index',compact(['lists', 'totalData', 'not_contacted', 'create_porposal', 'followup', 'undecided', 'lost', 'won', 'ready_to_pay', 'cur_url', 'todaycall']));

	}

	public function create(Request $request)
	{
		return view('Admin.leads.create');
	}

	public function assign(Request $request) {
		$requestData 		= 	$request->all();
		$id = $this->decodeString($requestData['mlead_id']);
		if(Lead::where('id', '=', $id)->where('user_id', '=', Auth::user()->id)->exists())
		{
			$leads = Lead::where('id', '=', $id)->where('user_id', '=', Auth::user()->id)->first();
			if($leads->assign_to != ''){
				if($leads->assign_to == $requestData['assignto']){
					return redirect()->back()->with('error', 'Already Assigned to this user');
				}else{
					$assignfrom = Admin::where('id',$leads->assign_to)->first();
					$assignto = Admin::where('id',$requestData['assignto'])->first();
					$ld = Lead::find($id);
					$ld->assign_to = $requestData['assignto'];
					$ld->save();
					$followup 					= new Followup;
					$followup->lead_id			= @$id;
					$followup->user_id			= Auth::user()->id;
					$followup->note				= 'changed from '.$assignfrom->first_name.' '.$assignfrom->last_name.' to '.$assignto->first_name.' '.$assignto->last_name;
					$followup->followup_type	= 'assigned_to';
					$saved				=	$followup->save();
					if(!$saved)
					{
						return redirect()->back()->with('error', 'Please try again');
					}else{
						return redirect()->back()->with('success', 'Lead transfer successfully');
					}
				}
			}else{
				$ld = Lead::find($id);
				$ld->assign_to = $requestData['assignto'];
				$saved		= $ld->save();
				if(!$saved)
					{
						return redirect()->back()->with('error', 'Please try again');
					}else{
						return redirect()->back()->with('success', 'Lead Assigned successfully');
					}
			}
		}else{
			return redirect()->back()->with('error', 'Not Found');
		}
	}
	public function history(Request $request, $id = NULL)
	{
		//check authorization start

		//check authorization end
		if(isset($id) && !empty($id))
			{
				$id = $this->decodeString($id);
				if(Lead::where('id', '=', $id)->exists())
				{
					$fetchedData = Lead::where('id', '=', $id)->with(['staffuser'])->first();
					return view('Admin.leads.history', compact(['fetchedData']));
				}
				else
				{
					return Redirect::to('/admin/leads')->with('error', 'Lead Not Exist');
				}
			}
			else
			{
				return Redirect::to('/admin/leads')->with('error', Config::get('constants.unauthorized'));
			}
	}

	 public function store(Request $request)
	{
		//check authorization start
			  $check = $this->checkAuthorizationAction('add_lead', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}
		//check authorization end
		if ($request->isMethod('post'))
		{
			$this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'gender' => 'required|max:255',
                'contact_type' => 'required|max:255',
                'phone' => 'required|max:255|unique:admins,phone|unique:leads,phone',
                'email_type' => 'required|max:255',
                'email' => 'required|max:255|unique:admins,email|unique:leads,email',
                'service' => 'required',
                'assign_to' => 'required',
                'lead_quality' => 'required',
                'lead_source' => 'required',
            ]);

			$requestData 		= 	$request->all();

			$related_files = '';
	        if(isset($requestData['related_files'])){
	            for($i=0; $i<count($requestData['related_files']); $i++){
	                $related_files .= $requestData['related_files'][$i].',';
	            }

	        }
			  $dob = '';
	        if($requestData['dob'] != ''){
	           $dobs = explode('/', $requestData['dob']);
	          $dob = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
	        }
	         $visa_expiry_date = '';
	        if($requestData['visa_expiry_date'] != ''){
	           $visa_expiry_dates = explode('/', $requestData['visa_expiry_date']);
	          $visa_expiry_date = $visa_expiry_dates[2].'-'.$visa_expiry_dates[1].'-'. $visa_expiry_dates[0];
	        }
			$obj				= 	new Lead;
			$obj->user_id	=	Auth::user()->id;
			$obj->first_name		=	@$requestData['first_name'];
			$obj->last_name		=	@$requestData['last_name'];
			$obj->gender		=	@$requestData['gender'];
			$obj->dob		=	@$dob;
			$obj->age		=	@$requestData['$age'];
			$obj->martial_status		=	@$requestData['martial_status'];
			$obj->passport_no		=	@$requestData['passport_no'];
			$obj->visa_type			=	@$requestData['visa_type'];
			$obj->visa_expiry_date		=	@$visa_expiry_date;
			$obj->tags_label		=	@$requestData['tags_label'];
			$obj->contact_type		=	@$requestData['contact_type'];
			$obj->country_code		=	@$requestData['country_code'];
			$obj->phone		=	@$requestData['phone'];
			$obj->email_type		=	@$requestData['email_type'];
			$obj->email		=	@$requestData['email'];
		    //$obj->social_type		=	@$requestData['social_type'];
			//$obj->social_link		=	@$requestData['social_link'];
			$obj->service		=	@$requestData['service'];
			$obj->assign_to		=	@$requestData['assign_to'];
			$obj->status		=	@$requestData['status'];
			$obj->lead_quality		=	@$requestData['lead_quality'];
			$obj->att_country_code		=	@$requestData['att_country_code'];
			$obj->att_phone		=	@$requestData['att_phone'];
				$obj->att_email		=	@$requestData['att_email'];
			$obj->lead_source		=	@$requestData['lead_source'];
			$obj->related_files	=	rtrim($related_files,',');
		//	$obj->advertisements_name		=	@$requestData['advertisements_name'];
			$obj->comments_note		=	@$requestData['comments_note'];
    		/* Profile Image Upload Function Start */
            if($request->hasfile('profile_img'))
            {
                $profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
            }
            else
            {
                $profile_img = NULL;
            }
    		$obj->profile_img			=	@$profile_img;
    		$obj->preferredIntake			=	@$requestData['preferredIntake'];
    		$obj->country_passport			=	@$requestData['country_passport'];
    		$obj->address			=	@$requestData['address'];
    		$obj->city			=	@$requestData['city'];
    		$obj->state			=	@$requestData['state'];
    		$obj->zip			=	@$requestData['zip'];
    		$obj->country			=	@$requestData['country'];
    		$obj->nomi_occupation			=	@$requestData['nomi_occupation'];
    		$obj->skill_assessment			=	@$requestData['skill_assessment'];
    		$obj->high_quali_aus			=	@$requestData['high_quali_aus'];
    		$obj->high_quali_overseas			=	@$requestData['high_quali_overseas'];
    		$obj->relevant_work_exp_aus			=	@$requestData['relevant_work_exp_aus'];
    		$obj->relevant_work_exp_over			=	@$requestData['relevant_work_exp_over'];
    		$obj->naati_py			=	@$requestData['naati_py'];
    		$obj->married_partner			=	@$requestData['married_partner'];
    		$obj->total_points			=	@$requestData['total_points'];
    		$obj->start_process			=	@$requestData['start_process'];
    		/* Profile Image Upload Function End */
			$saved				=	$obj->save();

			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/leads')->with('success', 'Lead added Successfully');
			}
		}
	}

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

            // Find the lead by ID
            $obj = Lead::find($requestData['id']);

            // Check if the lead exists
            if (!$obj) {
                return redirect()->back()->with('error', 'Lead not found.');
            }

            // Continue with the update if the lead is found
            $obj->first_name = $requestData['first_name'];
            $obj->last_name = $requestData['last_name'];
            $obj->gender = $requestData['gender'];

            // Process other fields and save
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

            $obj->dob = $dob;
            $obj->age = $requestData['age'];
            $obj->martial_status = $requestData['martial_status'];
            $obj->passport_no = $requestData['passport_no'];
            $obj->visa_type = $requestData['visa_type'];
            $obj->visa_expiry_date = $visa_expiry_date;
            $obj->tags_label = $requestData['tags_label'];
            $obj->contact_type = $requestData['contact_type'];
            $obj->country_code = $requestData['country_code'];
            $obj->phone = $requestData['phone'];
            $obj->email_type = $requestData['email_type'];
            $obj->email = $requestData['email'];
            $obj->service = $requestData['service'];
            $obj->assign_to = $requestData['assign_to'];
            $obj->status = $requestData['status'];
            $obj->lead_quality = $requestData['lead_quality'];
            $obj->att_country_code = $requestData['att_country_code'];
            $obj->att_phone = $requestData['att_phone'];
            $obj->att_email = $requestData['att_email'];
            $obj->lead_source = $requestData['lead_source'];
            $obj->related_files = rtrim($related_files, ',');

            if ($request->hasfile('profile_img')) {
                if ($requestData['old_profile_img'] != '') {
                    $this->unlinkFile($requestData['old_profile_img'], Config::get('constants.profile_imgs'));
                }
                $profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
            } else {
                $profile_img = $requestData['old_profile_img'];
            }

            $obj->profile_img = $profile_img;
            $obj->preferredIntake = $requestData['preferredIntake'];
            $obj->country_passport = $requestData['country_passport'];
            $obj->address = $requestData['address'];
            $obj->city = $requestData['city'];
            $obj->state = $requestData['state'];
            $obj->zip = $requestData['zip'];
            $obj->country = $requestData['country'];
            $obj->nomi_occupation = $requestData['nomi_occupation'];
            $obj->skill_assessment = $requestData['skill_assessment'];
            $obj->high_quali_aus = $requestData['high_quali_aus'];
            $obj->high_quali_overseas = $requestData['high_quali_overseas'];
            $obj->relevant_work_exp_aus = $requestData['relevant_work_exp_aus'];
            $obj->relevant_work_exp_over = $requestData['relevant_work_exp_over'];
            $obj->naati_py = $requestData['naati_py'];
            $obj->married_partner = $requestData['married_partner'];
            $obj->total_points = $requestData['total_points'];
            $obj->start_process = $requestData['start_process'];

            if (!$obj->save()) {
                return redirect()->back()->with('error', Config::get('constants.server_error'));
            } else {
                return Redirect::to('/admin/leads/edit/' . base64_encode(convert_uuencode($requestData['id'])))->with('success', 'Lead updated successfully.');
            }
        } else {
            if (isset($id) && !empty($id)) {
                $id = $this->decodeString($id);
                if (Admin::where('id', $id)->where('role', '7')->exists()) {
                    $fetchedData = Admin::find($id);
                    return view('Admin.leads.edit', compact('fetchedData'));
                } else {
                    return Redirect::to('/admin/leads')->with('error', 'leads not found.');
                }
            } else {
                return Redirect::to('/admin/leads')->with('error', Config::get('constants.unauthorized'));
            }
        }
    }

    public function leadPin(Request $request, $id)
	{
	    if(Followup::where('id', $id)->exists()){
	        $a = Followup::find($id);
	        if($a->pin == 1){
	           $a->pin =  0;
	        }else{
	           $a->pin =  1;
	        }
	        $save = $a->save();
	        if($save){
	            return Redirect::to('/admin/leads/history/'.base64_encode(convert_uuencode(@$a->lead_id)))->with('success', 'Record Updated successfully');
	        }else{
	            return Redirect::to('/admin/leads/history/'.base64_encode(convert_uuencode(@$a->lead_id)))->with('error', 'Please try again');
	        }
	    }
	}
	public function convertoClient(Request $request)
	{
		$requestData 		= 	$request->all();
		$enqdatas = Lead::where('id', '!=','')->paginate(500);
	//	if(Lead::where('id', $id)->exists()){
		foreach($enqdatas as $lead){
		    $id = $lead->id;
			$enqdata = Admin::where('lead_id', $id)->first();
			if($enqdata){
			$obj = Admin::find($enqdata->id);
			$obj->created_at = $lead->created_at;
			$obj->updated_at = $lead->updated_at;
			$obj->save();
			}
			/*if(!Admin::where('email', $enqdata->email)->exists()){
				$first_name = substr(@$enqdata->first_name, 0, 4);
				$obj				= 	new Admin;
				$obj->role	=	7;
					$obj->lead_id	=	$id;
			$obj->first_name	=	@$enqdata->first_name;
			$obj->last_name	=	@$enqdata->last_name;
			$obj->age	=	@$enqdata->first_name;
			$obj->dob	=		@$enqdata->dob;
			$obj->gender = @$enqdata->gender;
			$obj->martial_status	=	@$enqdata->martial_status;
			$obj->contact_type	=	@$enqdata->contact_type;
			$obj->email_type	=	@$enqdata->email_type;
			$obj->service	=	@$enqdata->service;
			$obj->related_files	=	@$enqdata->related_files;
			$obj->email	=	@$enqdata->email;
			$obj->phone	=	@$enqdata->phone;
			$obj->address	=	@$enqdata->address;
			$obj->city	=	@$enqdata->city;
			$obj->state	=	@$enqdata->state;
			$obj->zip	=	@$enqdata->zip;
			$obj->country	=	@$enqdata->country;
			$obj->preferredIntake	=	@$enqdata->preferredIntake;
			$obj->country_passport			=	@$enqdata->country_passport;
			$obj->passport_number			=	@$enqdata->passport_no;
			$obj->visa_type			=	@$enqdata->visa_type;
			$obj->visaExpiry			=	@$enqdata->visa_expiry_date;
			//$obj->applications	=@$enqdata->first_name;
			$obj->assignee	=	@$enqdata->assign_to;

			$obj->att_phone	=	@$enqdata->att_phone;
			$obj->att_country_code	=@$enqdata->att_country_code;
			$obj->att_email	=	@$enqdata->att_email;
			$obj->nomi_occupation	=@$enqdata->nomi_occupation;
			$obj->skill_assessment	=@$enqdata->skill_assessment;
			$obj->high_quali_aus	=@$enqdata->high_quali_aus;
			$obj->high_quali_overseas	=	@$enqdata->high_quali_overseas;
			$obj->relevant_work_exp_aus	=	@$enqdata->relevant_work_exp_aus;
			$obj->relevant_work_exp_over	=	@$enqdata->relevant_work_exp_over;
			$obj->naati_py	=	@$enqdata->naati_py;
			$obj->married_partner	=@$enqdata->married_partner;
			$obj->total_points	=@$enqdata->total_points;
			$obj->start_process	=@$enqdata->start_process;
			$obj->source	=	@$enqdata->lead_source;
			$obj->comments_note	=	@$enqdata->comments_note;
			$obj->type	=	'lead';
			$obj->profile_img			=@$enqdata->profile_img;

				$saved				=	$obj->save();
			$objs							= 	Admin::find($obj->id);
		    	$objs->client_id	=	strtoupper($first_name).date('ym').$obj->id;
		    	$saveds				=	$objs->save();

				if(!$saved)
				{
					$response['status'] 	= 	false;
					$response['message']	=	'Please try again';
					return Redirect::to('/admin/leads')->with('error', 'Please try again');
				}
				else
				{
				    $o = Lead::find($id);
				    $o->converted = 1;
				    $o->converted_date = date('Y-m-d');
				    $o->save();
				    $Followups = Followup::where('lead_id', $id)->get();
				    foreach($Followups as $Followup){
	                	$Followupstype = FollowupType::where('type', $Followup->followup_type)->first();
	                	$r = '';
	                	if(@$Followup->subject != ''){
	                	    $r .= @$Followup->subject.'<br>';
	                	}
	                	if(@$Followup->followup_date != ''){
	                	    $r .= @$Followup->followup_date.'<br>';
	                	}
	                	if(@$Followup->note != ''){
	                	    $r .= @$Followup->note;
	                	}
				        $objn = new \App\Models\Note;
				        $objn->client_id = $obj->id;
		            	$objn->user_id = Auth::user()->id;
		        	    $objn->title = @$Followupstype->name;
		        	    $objn->description = $r;
		        	    $objn->mail_id = 0;
		        	    $objn->type = 'client';
		        	      $objn->save();
				    }

    				$enq = new Followup;
    				$enq->lead_id = $id;
    				$enq->user_id = @Auth::user()->id;
    				$enq->note = 'Lead converted to client';
    				$enq->followup_type = 'converted';
    				$enq->save();
					$response['status'] 	= 	true;
					$response['message']	=	'Client saved successfully';
				//	return Redirect::to('/admin/leads')->with('success', 'Client saved successfully');
				}
			}*/
			echo $id.'<br>';
		}
		//	echo json_encode($response);
		//}
	}

	public function leaddeleteNotes(Request $request, $id = Null){
	    if(isset($id) && !empty($id))
			{

				if(Followup::where('id', '=', $id)->exists())
				{
				    $leadid = Followup::where('id', '=', $id)->first()->lead_id;
				    $res = Followup::where('id', '=', $id)->delete();
					if($res){
					    return Redirect::to('/admin/leads/history/'.base64_encode(convert_uuencode(@$leadid)))->with('success', 'Record deleted successfully');
					}else{
					    return Redirect::to('/admin/leads/history/'.base64_encode(convert_uuencode(@$leadid)))->with('error', 'Lead Not Exist');
					}
				}
				else
				{
					return Redirect::to('/admin/leads/')->with('error', 'Lead Not Exist');
				}
			}
			else
			{
				return Redirect::to('/admin/leads/')->with('error', Config::get('constants.unauthorized'));
			}
	}

	public function getnotedetail(Request $request){
	    $id = $request->id;
	    if(Followup::where('id', '=', $id)->exists())
		{
		    $fetchedData = Followup::where('id', '=', $id)->first();
		    	return view('Admin.leads.editnotemodal', compact(['fetchedData']));
		}else{
		    echo 'No Found';
		}
	}

    //Check Email is unique or not
    public function is_email_unique(Request $request){
        $email = $request->email;
        $email_count = \App\Models\Admin::where('email',$email)->count();//dd($email_count); //->where('status',1)
        //$email_count = \App\Models\Admin::where('email','LIKE','%'.$email.'%')->count();//dd($email_count);
        if($email_count >0){
            $response['status'] 	= 	1;
            $response['message']	=	"The email has already been taken.";
        }else{
            $response['status'] 	= 	0;
            $response['message']	=	"";
        }
        echo json_encode($response);
    }

    //Check Contact no is unique or not
    public function is_contactno_unique(Request $request){
        $contact = $request->contact;
        //$phone_count = \App\Models\Admin::where('phone',$contact)->count();//dd($phone_count); //->where('status',1)
        $phone_count = \App\Models\Admin::where('phone','LIKE','%'.$contact.'%')->count();//dd($phone_count);
        if($phone_count >0){
            $response['status'] 	= 	1;
            $response['message']	=	"The phone has already been taken.";
        }else{
            $response['status'] 	= 	0;
            $response['message']	=	"";
        }
        echo json_encode($response);
    }


}
