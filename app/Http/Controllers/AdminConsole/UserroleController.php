<?php
namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\UserRole;
use App\Models\UserType;

use Auth;
use Config;

class UserroleController extends Controller
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
			$check = $this->checkAuthorizationAction('user_role', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		$query 		= UserRole::where('id', '!=', '')->with(['usertypedata']);
		 
		$totalData 	= $query->count();	//for all data

		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('AdminConsole\.system\.roles\.index',compact(['lists', 'totalData']));	

		//return view('Admin.usertype.index');	
	}
	
	public function create(Request $request) 
	{
			//check authorization start	
			$check = $this->checkAuthorizationAction('user_role', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		$usertype 		= UserType::all();
		return view('AdminConsole\.system\.roles\.create',compact(['usertype']));	
	} 
	
	public function store(Request $request)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('user_role', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										//'usertype' => 'required|max:255|unique:user_roles',
										
									  ]);
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new UserRole;
			$obj->name	=	@$requestData['name'];
			$obj->description	=	@$requestData['description'];
			$obj->module_access	=	json_encode(@$requestData['module_access']);
			
			$saved				=	$obj->save(); 
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/userrole')->with('success', 'User Role added Successfully');
			}				
		}	

		return view('AdminConsole\.system\.roles\.create');	
	}
	
	public function edit(Request $request, $id = NULL)
	{			
		//check authorization start	
			$check = $this->checkAuthorizationAction('user_role', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		$usertype 		= UserType::all();
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			/* $this->validate($request, [
										'usertype' => 'required|max:255|unique:user_roles,usertype,'.$requestData['id']
									  ]); */									  
									  
			$obj				= 	UserRole::find($requestData['id']);
			$obj->name	=	@$requestData['name'];
			$obj->description	=	@$requestData['description'];
			$obj->module_access	=	json_encode(@$requestData['module_access']);
			
			$saved				=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/userrole')->with('success', 'User Role Edited Successfully');
			}				
		}
		else
		{	
			if(isset($id) && !empty($id))
			{
				$id = $this->decodeString($id);	
				if(UserRole::where('id', '=', $id)->exists()) 
				{
					$fetchedData = UserRole::find($id);
					return view('AdminConsole\.system\.roles\.edit', compact(['fetchedData', 'usertype']));
				}
				else
				{
					return Redirect::to('/admin/userrole')->with('error', 'User Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/userrole')->with('error', Config::get('constants.unauthorized'));
			}		
		}				
	}
}


