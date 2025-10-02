<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Contact;
 
use Auth;  
use Config;

class ContactController extends Controller
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
		//check authorization start	
			/*  $check = $this->checkAuthorizationAction('holiday_package', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	 */
		//check authorization end
		
		  if(Auth::user()->role == 1){
			$query 		= Contact::where('id','!=','' ); 
		 }else{	
			$query 		= Contact::where('user_id', '=', Auth::user()->id);
		 }	
		if ($request->has('company_name')) 
		{
			$company_name 		= 	$request->input('company_name'); 
			if(trim($company_name) != '')
			{
				$query->where('company_name', '=', @$company_name);
			}
		}
		if ($request->has('email')) 
		{
			$email 		= 	$request->input('email'); 
			if(trim($email) != '')
			{
				$query->where('contact_email', '=', @$email);
			}
		}
		if ($request->has('first_name')) 
				{
					$first_name 		= 	$request->input('first_name'); 
					if(trim($first_name) != '')
					{
						$query->where('first_name', '=', @$first_name);
					}
				}	
		if ($request->has('last_name')) 
				{
					$last_name 		= 	$request->input('last_name'); 
					if(trim($last_name) != '')
					{
						$query->where('last_name', '=', @$last_name);
					}
				}
				
				if ($request->has('phone')) 
				{
					$phone 		= 	$request->input('phone'); 
					if(trim($phone) != '')
					{
						$query->where('contact_phone', '=', @$phone);
					}
				}			
		
		//$query 		= Contact::where('id','!=','' );
		
		$totalData 	= $query->count();	//for all data

		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit')); 
		
		return view('Admin.managecontact.index',compact(['lists', 'totalData'])); 
		
		//return view('Admin.managecontact.index'); 	
		
	}
	
	public function create(Request $request) 
	{
		//check authorization start	
			/* $check = $this->checkAuthorizationAction('holiday_package', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	 
		//check authorization end
		
		$managecontact 		=  Managecontact::all();	 */	
		return view('Admin.managecontact.create');
	}
	
	 public function add(Request $request){
		 $this->validate($request, [
			'first_name' => 'required|max:255',
			'last_name' => 'required|max:255',
			'company_name' => 'required|unique:contacts',
			'contact_display_name' => 'required',
			'contact_email' => 'required|unique:contacts',
			'contact_phone' => 'required'
		  ]);
		  $requestData 		= 	$request->all();
			 
			$obj						= 	new Contact; 
			$obj->user_id				=	Auth::user()->id;   
			$obj->srname				=	@$requestData['srname'];
			$obj->first_name			=	@$requestData['first_name'];
			$obj->middle_name			=	@$requestData['middle_name'];			
			$obj->last_name				=	@$requestData['last_name'];			
			$obj->company_name			=	@$requestData['company_name'];
			$obj->contact_display_name	=	@$requestData['contact_display_name'];
			$obj->contact_email			=	@$requestData['contact_email'];
			$obj->contact_phone			=	@$requestData['contact_phone'];
			$obj->currency			=	@$requestData['currency'];
			
			$saved				=	$obj->save();  
			
			if(!$saved) 
			{
				return json_encode(array('success' => false, 'message' => Config::get('constants.server_error')));
			}
			else
			{ 
				return json_encode(array('success' => true, 'contactdetail' => $obj));
			} 				
	 }
	 public function store(Request $request)
	{
		//check authorization start	
			/* $check = $this->checkAuthorizationAction('holiday_package', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	 */
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'first_name' => 'required|max:255',
										'last_name' => 'required|max:255',
										'company_name' => 'required|unique:contacts',
										'contact_display_name' => 'required',
										'contact_email' => 'required|unique:contacts',
										'contact_phone' => 'required'
									  ]);
			
			$requestData 		= 	$request->all();
			 
			$obj						= 	new Contact; 
			$obj->user_id				=	Auth::user()->id;   
			$obj->srname				=	@$requestData['srname'];
			$obj->first_name			=	@$requestData['first_name'];
			$obj->middle_name			=	@$requestData['middle_name'];			
			$obj->last_name				=	@$requestData['last_name'];			
			$obj->company_name			=	@$requestData['company_name'];
			$obj->contact_display_name	=	@$requestData['contact_display_name'];
			$obj->contact_email			=	@$requestData['contact_email'];
			$obj->contact_phone			=	@$requestData['contact_phone'];
			$obj->work_phone			=	@$requestData['work_phone'];
			$obj->website				=	@$requestData['website'];
			$obj->birth_date				=	@$requestData['birth_date'];
			$obj->anniversary_date				=	@$requestData['anniversary_date'];
			$obj->designation			=	@$requestData['designation'];
			$obj->department			=	@$requestData['department'];
			$obj->skype_name			=	@$requestData['skype_name'];
			$obj->facebook_name			=	@$requestData['facebook_name'];
			$obj->twitter_name			=	@$requestData['twitter_name'];
			$obj->linkedin_name			=	@$requestData['linkedin_name'];
			$obj->instagram_name		=	@$requestData['instagram_name'];
			$obj->youtube_name			=	@$requestData['youtube_name'];
			$obj->country				=	@$requestData['country'];
			$obj->address				=	@$requestData['address'];
			$obj->city					=	@$requestData['city'];
			$obj->zipcode				=	@$requestData['zipcode'];
			$obj->phone					=	@$requestData['phone'];
			$obj->currency					=	@$requestData['currency'];
			
			$saved				=	$obj->save();  
			
			if(!$saved) 
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{ 
				return Redirect::to('/admin/contact')->with('success', 'Contacts added Successfully');
			} 				
		}	 
	} 
	
	 public function storeaddress(Request $request){
		 if ($request->isMethod('post')) 
		 {
			$requestData 		= 	$request->all();
			$obj				= 	Contact::find($requestData['customer_id']);
			$obj->country	=	@$requestData['country'];
			$obj->address	=	@$requestData['address'];
			$obj->city	=	@$requestData['city'];
			$obj->zipcode	=	@$requestData['zipcode'];
			$obj->phone	=	@$requestData['phone'];
			$saved				=	$obj->save();
			
			if(!$saved)
			{
				return json_encode(array('success' => false, 'message' => 'Please try again'));
			}
			else
			{
				return json_encode(array('success' => true, 'contactdetail' => $obj));
			}
		 }
	 }
	 public function edit(Request $request, $id = NULL)
	{			
		//check authorization end
	//check authorization start	
			/* $check = $this->checkAuthorizationAction('holiday_package', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			} */	
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [
										'first_name' => 'required|max:255',
										'last_name' => 'required|max:255',
										'company_name' => 'required',
										'contact_display_name' => 'required',
										'contact_email' => 'required',
										'contact_phone' => 'required'
									  ]);
									  
									  
			$obj				= 	Contact::find($requestData['id']);
			$obj->srname		=	@$requestData['srname'];
			$obj->first_name	=	@$requestData['first_name'];
			$obj->middle_name	=	@$requestData['middle_name'];			
			$obj->last_name		=	@$requestData['last_name'];			
			$obj->company_name	=	@$requestData['company_name'];
			$obj->contact_display_name	=	@$requestData['contact_display_name'];
			$obj->contact_email	=	@$requestData['contact_email'];
			$obj->contact_phone	=	@$requestData['contact_phone'];
			$obj->work_phone	=	@$requestData['work_phone'];
			$obj->website	=	@$requestData['website'];
			$obj->birth_date				=	@$requestData['birth_date'];
			$obj->anniversary_date				=	@$requestData['anniversary_date'];
			$obj->designation	=	@$requestData['designation'];
			$obj->department	=	@$requestData['department'];
			$obj->skype_name	=	@$requestData['skype_name'];
			$obj->facebook_name	=	@$requestData['facebook_name'];
			$obj->twitter_name	=	@$requestData['twitter_name'];
			$obj->linkedin_name	=	@$requestData['linkedin_name'];
			$obj->instagram_name	=	@$requestData['instagram_name'];
			$obj->youtube_name	=	@$requestData['youtube_name'];
			$obj->country	=	@$requestData['country'];
			$obj->address	=	@$requestData['address'];
			$obj->city	=	@$requestData['city'];
			$obj->zipcode	=	@$requestData['zipcode'];
			$obj->phone	=	@$requestData['phone'];
			$obj->currency	=	@$requestData['currency'];
				
			//$obj->slug	=	$this->createSlug(Auth::user()->id,'destinations',@$requestData['dest_name'], $requestData['id']);			
			
			$saved				=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/contact')->with('success', 'Contact Edited Successfully');
			}				
		}
		else
		{	 
			if(isset($id) && !empty($id)) 
			{
				$id = $this->decodeString($id);	 
				if(Contact::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Contact::find($id);
					return view('Admin.managecontact.edit', compact(['fetchedData']));
				}
				else
				{
					return Redirect::to('/admin/contact')->with('error', 'Contact Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/contact')->with('error', Config::get('constants.unauthorized'));
			}		
		}				
	} 
	
	
}
