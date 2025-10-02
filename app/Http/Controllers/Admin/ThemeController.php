<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\HolidayTheme;
 
use Auth;
use Config;
 
class ThemeController extends Controller
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
			//$check = $this->checkAuthorizationAction('holiday_package', $request->route()->getActionMethod(), Auth::user()->role);
			if(Auth::user()->role != 1)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
			
			$query 		= HolidayTheme::where('id','!=','' );
		$totalData 	= $query->count();	//for all data
		if ($request->has('type')) 
		{
			$inc_id 		= 	$request->input('type'); 
			if(trim($inc_id) != '')
			{
				$query->where('id', '=', @$inc_id);
			}
		} 
		
		if ($request->has('name')) 
		{
			$name 		= 	$request->input('name'); 
			if(trim($name) != '')
			{
				$query->where('name', 'LIKE', '%'.@$name.'%');
			} 
		}
		$lists		= $query->orderby('id','desc')->get();
		
		return view('Admin.holidaythemes.index',compact(['lists', 'totalData'])); 	

		//return view('Admin.manageholidaytype.index');	  
	}
	
	public function create(Request $request)
	{
		//check authorization start	
			if(Auth::user()->role != 1)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		return view('Admin.holidaythemes.create');
	} 
	
	public function store(Request $request)
	{
		//check authorization start	
			if(Auth::user()->role != 1)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'name' => 'required|max:255',
							
									  ]);
			
			$requestData 		= 	$request->all();
			if($request->hasfile('image')) 
					{	
						$topinclu_image = $this->uploadFile($request->file('image'), Config::get('constants.themes_img')); 
					}
					else
					{
						$topinclu_image = NULL;
					}	
			$obj				= 	new HolidayTheme;
			$obj->name			=	@$requestData['name'];
			//$obj->status		=	@$requestData['status'];
			$obj->image		=	@$topinclu_image;
			$obj->slug	=	$this->createlocSlug('holiday_themes',@$requestData['name']);
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/themes')->with('success', 'Holiday Theme added Successfully');
			}				
		}	

	 
	}
	
	public function edit(Request $request, $id = NULL)
	{			
		//check authorization start	
			if(Auth::user()->role != 1)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}
		//check authorization end
	
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [
										'name' => 'required|max:255',
										
									  ]);
				if($request->hasfile('image')) 
			{	
				/* Unlink File Function Start */ 
					if($requestData['image'] != '')
						{
							$this->unlinkFile($requestData['old_image'], Config::get('constants.themes_img'));
						}
				/* Unlink File Function End */
				
				$topinclu_image = $this->uploadFile($request->file('image'), Config::get('constants.themes_img'));
			}
			else
			{
				$topinclu_image = @$requestData['old_image'];
			}						  
									  
			$obj				= 	HolidayTheme::find($requestData['id']);
			$obj->name			=	@$requestData['name'];
			//$obj->status		=	@$requestData['status'];			
			$obj->image		=	@$topinclu_image;	
			$obj->slug	=	$this->createlocSlug('holiday_themes',@$requestData['name'], $requestData['id']);			
			$saved				=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/themes')->with('success', 'Holiday Theme Edited Successfully');
			}				
		}
		else
		{	
			if(isset($id) && !empty($id)) 
			{
				$id = $this->decodeString($id);	
				if(HolidayTheme::where('id', '=', $id)->exists()) 
				{
					$fetchedData = HolidayTheme::find($id);
					return view('Admin.holidaythemes.edit', compact(['fetchedData']));
				}
				else
				{
					return Redirect::to('/admin/themes')->with('error', 'Holiday Theme Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/themes')->with('error', Config::get('constants.unauthorized'));
			}		
		}				
	}
	 
	
}
