<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\CrmEmailTemplate; 
  
use Auth; 
use Config;

class CrmEmailTemplateController extends Controller
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
	
		$query 		= CrmEmailTemplate::where('id', '!=', ''); 
		 
		$totalData 	= $query->count();	//for all data
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('Admin.feature.crmemailtemplate.index',compact(['lists', 'totalData'])); 	
		
	}
	
	public function create(Request $request)
	{
		//check authorization end
		//return view('Admin.users.create',compact(['usertype']));	
		
		return view('Admin.feature.crmemailtemplate.create');	
	}
	
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new CrmEmailTemplate; 
			$obj->name	=	@$requestData['name'];
			$obj->subject	=	@$requestData['subject'];
			$obj->description	=	@$requestData['description'];
			
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/crm_email_template')->with('success', 'Crm Email Template Added Successfully');
			}				
		}	

		return view('Admin.feature.crmemailtemplate.create');	
	}
	
	public function edit(Request $request, $id = NULL)
	{
	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
								  					  
			$obj			= 	CrmEmailTemplate::find(@$requestData['id']);
			$obj->name	=	@$requestData['name'];
			$obj->subject	=	@$requestData['subject'];
			$obj->description	=	@$requestData['description'];
			
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/crm_email_template')->with('success', 'Crm Email Template Edited Successfully');
			}				
		}

		else
		{		
			if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(CrmEmailTemplate::where('id', '=', $id)->exists()) 
				{
					$fetchedData = CrmEmailTemplate::find($id);
					return view('Admin.feature.crmemailtemplate.edit', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/admin/crm_email_template')->with('error', 'Crm Email Template Not Exist');
				}	
			} 
			else
			{
				return Redirect::to('/admin/crm_email_template')->with('error', Config::get('constants.unauthorized'));
			}		
		} 	
		
	}
}
