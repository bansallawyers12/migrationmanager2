<?php
namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\MatterEmailTemplate; 
  
use Auth; 
use Config;

class MatterEmailTemplateController extends Controller
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
	
		$query 		= MatterEmailTemplate::where('id', '!=', ''); 
		 
		$totalData 	= $query->count();	//for all data
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('AdminConsole\.features\.matteremailtemplate.index',compact(['lists', 'totalData'])); 	
		
	}
	
	public function create(Request $request, $matterId = NULL)
	{	//dd($matterId);
		return view('AdminConsole\.features\.matteremailtemplate.create', compact(['matterId']));	
	}
	
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			$obj				= 	new MatterEmailTemplate; 
			$obj->matter_id		=	@$requestData['matter_id'];
			$obj->name			=	@$requestData['name'];
			$obj->subject		=	@$requestData['subject'];
			$obj->description	=	@$requestData['description'];
			$saved				=	$obj->save();  
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				//return Redirect::to('/admin/matter_email_template')->with('success', 'Matter Email Template Added Successfully');
				return Redirect::to('/admin/matter')->with('success', 'Matter Email Template Added Successfully');
			}				
		}	
		return view('AdminConsole\.features\.matteremailtemplate.create');	
	}
	
	public function edit(Request $request, $templateId = NULL, $matterId = NULL)
	{
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all(); //dd($requestData);
			$obj			    = 	MatterEmailTemplate::find(@$requestData['id']);
			$obj->matter_id	    =	@$requestData['matter_id'];
			$obj->name	        =	@$requestData['name'];
			$obj->subject	    =	@$requestData['subject'];
			$obj->description	=	@$requestData['description'];
			$saved				=	$obj->save();
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/matter')->with('success', 'Matter Email Template Edited Successfully');
			}				
		}
		else
		{		
			if(isset($templateId) && !empty($templateId))
			{
				//$id = $this->decodeString($id);	
				if(MatterEmailTemplate::where('id', '=', $templateId)->exists()) 
				{
					$fetchedData = MatterEmailTemplate::find($templateId);
					return view('AdminConsole\.features\.matteremailtemplate.edit', compact(['fetchedData','matterId']));
				}
				else 
				{
					return Redirect::to('/admin/matter_email_template')->with('error', 'Matter Email Template Not Exist');
				}	
			} 
			else
			{
				return Redirect::to('/admin/matter_email_template')->with('error', Config::get('constants.unauthorized'));
			}		
		} 	
	}
}


