<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Branch;
 
use Auth; 
use Config;

class BranchesController extends Controller
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
			
			/* if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			} */	
		//check authorization end
	
		 $query 		= Branch::where('id', '!=', ''); 
		 
		$totalData 	= $query->count();	//for all data
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('Admin.branch.index',compact(['lists', 'totalData'])); 	
		
		//return view('Admin.branch.index');	 
	}
	
	public function create(Request $request)
	{
		//check authorization end
		//return view('Admin.users.create',compact(['usertype']));	
		
		return view('Admin.branch.create');	
	}
	
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'office_name' => 'required|max:255',
										'country' => 'required|max:255',
										'email' => 'required|max:255'
									  ]);
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new Branch; 
			$obj->office_name	=	@$requestData['office_name'];
			$obj->address	=	@$requestData['address'];
			$obj->city	=	@$requestData['city'];
			$obj->state	=	@$requestData['state'];
			$obj->zip	=	@$requestData['zip'];
			$obj->country	=	@$requestData['country'];
			$obj->email	=	@$requestData['email']; 
			$obj->phone	=	@$requestData['phone'];
			$obj->mobile	=	@$requestData['mobile'];
			$obj->contact_person	=	@$requestData['contact_person'];
			$obj->choose_admin	=	@$requestData['choose_admin'];
			
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/branch')->with('success', 'Branch Added Successfully');
			}				
		}	

		return view('Admin.branch.create');	
	}
	
	public function edit(Request $request, $id = NULL)
	{
	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [										
										'office_name' => 'required|max:255',
										'country' => 'required|max:255',
										'email' => 'required|max:255'
									  ]);
								  					  
			$obj			= 	Branch::find(@$requestData['id']);
						
			$obj->office_name	=	@$requestData['office_name'];
			$obj->address	=	@$requestData['address'];
			$obj->city	=	@$requestData['city'];
			$obj->state	=	@$requestData['state'];
			$obj->zip	=	@$requestData['zip'];
			$obj->country	=	@$requestData['country'];
			$obj->email	=	@$requestData['email'];
			$obj->phone	=	@$requestData['phone'];
			$obj->mobile	=	@$requestData['mobile'];
			$obj->contact_person	=	@$requestData['contact_person'];
			$obj->choose_admin	=	@$requestData['choose_admin'];
			
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/branch')->with('success', 'Branch Edited Successfully');
			}				
		}

		else
		{		
			if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(Branch::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Branch::find($id);
					return view('Admin.branch.edit', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/admin/branch')->with('error', 'Branch Not Exist');
				}	
			} 
			else
			{
				return Redirect::to('/admin/branch')->with('error', Config::get('constants.unauthorized'));
			}		
		} 	
		
	}
	
	public function view(Request $request, $id = NULL)
	{
			
			if(isset($id) && !empty($id))
			{
				
				
				if(Branch::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Branch::find($id);
					return view('Admin.branch.view', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/admin/branch')->with('error', 'Branch Not Exist');
				}	
			} 
			else
			{
				return Redirect::to('/admin/branch')->with('error', Config::get('constants.unauthorized'));
			}		
		 	
		
	}
	
	public function viewclient(Request $request, $id = NULL)
	{
			
			if(isset($id) && !empty($id))
			{
				
				
				if(Branch::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Branch::find($id);
					return view('Admin.branch.viewclient', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/admin/branch')->with('error', 'Branch Not Exist');
				}	
			} 
			else
			{
				return Redirect::to('/admin/branch')->with('error', Config::get('constants.unauthorized'));
			}		
		 	
		
	}
}
