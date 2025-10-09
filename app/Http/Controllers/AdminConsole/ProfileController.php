<?php
namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Profile; 
  
use Auth; 
use Config;

class ProfileController extends Controller
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
	
		$query 		= Profile::where('id', '!=', ''); 
		 
		$totalData 	= $query->count();	//for all data
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('AdminConsole\.features\.profile.index',compact(['lists', 'totalData'])); 	
		
		//return view('AdminConsole\.features\.profile.index');	 
	}
	
	public function create(Request $request)
	{
		//check authorization end
		//return view('AdminConsole\.system\.users\.create',compact(['usertype']));	
		
		return view('AdminConsole\.features\.profile.create');	
	}
	
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'company_name' => 'required|max:255'
									  ]);
			
			$requestData 		= 	$request->all();
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
			$obj				= 	new Profile; 
			$obj->company_name	=	@$requestData['company_name'];
			$obj->address	=	@$requestData['address'];
			$obj->phone	=	@$requestData['phone'];
			$obj->other_phone	=	@$requestData['other_phone'];
			$obj->email	=	@$requestData['email'];
			$obj->website	=	@$requestData['website'];
			$obj->abn	=	@$requestData['abn'];
			$obj->note	=	@$requestData['note'];
			$obj->logo	=	@$profile_img;
			
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/profiles')->with('success', 'Profile Added Successfully');
			}				
		}	

		return view('AdminConsole\.features\.profile.create');	
	}
	
	public function edit(Request $request, $id = NULL)
	{
	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [										
										'company_name' => 'required|max:255'
									  ]);
				/* Profile Image Upload Function Start */						  
				if($request->hasfile('profile_img')) 
				{	
					$profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
				}
				else
				{
					$profile_img = @$requestData['old_profile_img'];
				}		 
			/* Profile Image Upload Function End */				  					  
			$obj			= 	Profile::find(@$requestData['id']);
						
			$obj->company_name	=	@$requestData['company_name'];
			$obj->address	=	@$requestData['address'];
			$obj->phone	=	@$requestData['phone'];
			$obj->other_phone	=	@$requestData['other_phone'];
			$obj->email	=	@$requestData['email'];
			$obj->website	=	@$requestData['website'];
			$obj->abn	=	@$requestData['abn'];
			$obj->note	=	@$requestData['note'];
			$obj->logo	=	@$profile_img;
			
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/profiles')->with('success', 'Profile updated Successfully');
			}				
		}

		else
		{		
			if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(Profile::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Profile::find($id);
					return view('AdminConsole\.features\.profile.edit', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/admin/profiles')->with('error', 'Product Type Not Exist');
				}	
			} 
			else
			{
				return Redirect::to('/admin/profiles')->with('error', Config::get('constants.unauthorized'));
			}		
		} 	
		
	}
}


