<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\PartnerType; 
  
use Auth; 
use Config;

class PartnerTypeController extends Controller
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
	
		$query 		= PartnerType::where('id', '!=', '')->with(['categorydata']); 
		 
		$totalData 	= $query->count();	//for all data
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('Admin.feature.partnertype.index',compact(['lists', 'totalData'])); 	
		
		//return view('Admin.feature.partnertype.index');	 
	}
	
	public function create(Request $request)
	{
		//check authorization end
		//return view('Admin.users.create',compact(['usertype']));	
		
		return view('Admin.feature.partnertype.create');	
	}
	
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'name' => 'required|max:255'
									  ]);
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new PartnerType; 
			$obj->name	=	@$requestData['name'];
			$obj->category_id	=	@$requestData['category'];
			
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/partner-type')->with('success', 'Partner Type Added Successfully');
			}				
		}	

		return view('Admin.feature.partnertype.create');	
	}
	
	public function edit(Request $request, $id = NULL)
	{
	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [										
										'name' => 'required|max:255'
									  ]);
								  					  
			$obj			= 	PartnerType::find(@$requestData['id']);
						
			$obj->name	=	@$requestData['name'];
				$obj->category_id	=	@$requestData['category'];
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/partner-type')->with('success', 'Partner Type Edited Successfully');
			}				
		}

		else
		{		
			if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(PartnerType::where('id', '=', $id)->exists()) 
				{
					$fetchedData = PartnerType::find($id);
					return view('Admin.feature.partnertype.edit', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/admin/partner-type')->with('error', 'Partner Type Not Exist');
				}	
			} 
			else
			{
				return Redirect::to('/admin/partner-type')->with('error', Config::get('constants.unauthorized'));
			}		
		} 	
		
	}
}
