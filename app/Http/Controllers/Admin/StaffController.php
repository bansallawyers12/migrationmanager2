<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Routing\Route;

use App\Models\Admin;

use Auth;
use Config;

class StaffController extends Controller
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
     * All Courses.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('staff', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
	
			
			$query 		= Admin::where('user_id', '=', Auth::user()->id)->Where('role', '=', '63')->with(['usertype']);
		  
		$totalData 	= $query->count();	//for all data

		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('Admin.staff.index',compact(['lists', 'totalData']));	

		//return view('Admin.users.index');	 
	}
	
	public function create(Request $request)
	{
			//check authorization start	
			$check = $this->checkAuthorizationAction('staff', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		return view('Admin.staff.create');	
	}
	
	public function store(Request $request)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('staff', $request->route()->getActionMethod(), Auth::user()->role);
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
										'email' => 'required|max:255|unique:admins',
										'staff_id' => 'required|max:255|unique:admins',
										'password' => 'required|max:255',
										'phone' => 'required|max:255',
										'profile_img' => 'required'
									  ]);
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new Admin;
			$obj->first_name	=	@$requestData['first_name'];
			$obj->last_name		=	@$requestData['last_name'];
			$obj->email			=	@$requestData['email'];
			$obj->staff_id			=	@$requestData['staff_id'];
			$obj->user_id		=	 @Auth::user()->id;
			$obj->password	=	Hash::make(@$requestData['password']);
			$obj->role	=	63;
			$obj->phone	=	@$requestData['phone'];
			/* Profile Image Upload Function Start */						  
					if($request->hasfile('profile_img')) 
					{	
						$profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
					}
					else
					{
						$profile_img = NULL;
					}		
				/* Profile Image Upload Function End */	
			$obj->profile_img			=	@$profile_img;
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/staff')->with('success', 'User added Successfully');
			}				
		}	

		return view('Admin.users.create');	
	}
	
	public function edit(Request $request, $id = NULL)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('staff', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [										
										'first_name' => 'required|max:255',
										'last_name' => 'required|max:255',
							
										'email' => 'required|max:255|unique:admins,email,'.$requestData['id'],
										'staff_id' => 'required|max:255|unique:admins,staff_id,'.$requestData['id'],
										'phone' => 'required|max:255',
										
										
									  ]);
								  					  
			$obj							= 	Admin::find(@$requestData['id']);
						
			$obj->first_name				=	@$requestData['first_name'];
			$obj->last_name					=	@$requestData['last_name'];
			$obj->email						=	@$requestData['email'];
			$obj->staff_id					=	@$requestData['staff_id'];
			if(!empty(@$requestData['password']))
				{		
					$obj->password				=	Hash::make(@$requestData['password']);
					//$objAdmin->decrypt_password		=	@$requestData['password'];
				}
			$obj->phone						=	@$requestData['phone'];
			
			/* Profile Image Upload Function Start */						  
			if($request->hasfile('profile_img')) 
			{	
				/* Unlink File Function Start */ 
					if($requestData['profile_img'] != '')
						{
							$this->unlinkFile($requestData['old_profile_img'], Config::get('constants.profile_imgs'));
						}
				/* Unlink File Function End */
				
				$profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
			}
			else
			{
				$profile_img = @$requestData['old_profile_img'];
			}		
		/* Profile Image Upload Function End */
			$obj->profile_img			=	@$profile_img;
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/staff')->with('success', 'User Edited Successfully');
			}				
		}

		else
		{	
			if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(Admin::where('id', '=', $id)->where('user_id', '=', Auth::user()->id)->exists()) 
				{
					$fetchedData = Admin::find($id);
					return view('Admin.staff.edit', compact(['fetchedData']));
				}
				else
				{
					return Redirect::to('/admin/staff')->with('error', 'User Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/staff')->with('error', Config::get('constants.unauthorized'));
			}		
		}	
		
	}
}
