<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\UserType;

use Auth;
use Config;

class UsertypeController extends Controller
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
			$check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		$query 		= UserType::where('id', '!=', '');
		
		$totalData 	= $query->count();	//for all data

		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('Admin.usertype.index',compact(['lists', 'totalData']));	

		//return view('Admin.usertype.index');	
	}
	
	public function create(Request $request)
	{
			//check authorization start	
			$check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end

		return view('Admin.usertype.create');	
	}
	
	public function store(Request $request)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'name' => 'required|max:255|unique:user_types'
									  ]);
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new UserType;
			$obj->name	=	@$requestData['name'];
			
			$saved				=	$obj->save(); 
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/usertype')->with('success', 'User Type added Successfully');
			}				
		}	

		return view('Admin.usertype.create');	
	}
	
	public function edit(Request $request, $id = NULL)
	{			
		//check authorization start	
			$check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
	
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [
										'name' => 'required|max:255|unique:user_types,name,'.$requestData['id']
									  ]);
									  
									  
			$obj				= 	UserType::find($requestData['id']);
			$obj->name	=	@$requestData['name'];
			
			$saved				=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/usertype')->with('success', 'User Type Edited Successfully');
			}				
		}
		else
		{	
			if(isset($id) && !empty($id))
			{
				$id = $this->decodeString($id);	
				if(UserType::where('id', '=', $id)->exists()) 
				{
					$fetchedData = UserType::find($id);
					return view('Admin.usertype.edit', compact(['fetchedData']));
				}
				else
				{
					return Redirect::to('/admin/usertype')->with('error', 'User Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/usertype')->with('error', Config::get('constants.unauthorized'));
			}		
		}				
	}
}
