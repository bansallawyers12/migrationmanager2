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
				return Redirect::to('/dashboard')->with('error',config('constants.unauthorized'));
			} */	
		//check authorization end 
	
		$query 		= MatterEmailTemplate::query(); 
		 
		$totalData 	= $query->count();	//for all data
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('AdminConsole.features.matteremailtemplate.index',compact(['lists', 'totalData'])); 	
		
	}
	
	public function create(Request $request, $matterId = NULL)
	{	//dd($matterId);
		return view('AdminConsole.features.matteremailtemplate.create', compact(['matterId']));	
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
				return redirect()->back()->with('error', config('constants.server_error'));
			}
			else
			{
				return redirect()->route('adminconsole.features.matter.index')->with('success', 'Matter Email Template Added Successfully');
			}				
		}	
		return view('AdminConsole.features.matteremailtemplate.create');	
	}
	
	/**
	 * Show the form for editing the specified matter email template.
	 */
	public function edit($templateId, $matterId = NULL)
	{
		if(isset($templateId) && !empty($templateId))
		{
			//$id = $this->decodeString($id);	
			if(MatterEmailTemplate::where('id', '=', $templateId)->exists()) 
			{
				$fetchedData = MatterEmailTemplate::find($templateId);
				return view('AdminConsole.features.matteremailtemplate.edit', compact(['fetchedData','matterId']));
			}
			else 
			{
				return redirect()->route('adminconsole.features.matteremailtemplate.index')->with('error', 'Matter Email Template Not Exist');
			}	
		} 
		else
		{
			return redirect()->route('adminconsole.features.matteremailtemplate.index')->with('error', config('constants.unauthorized'));
		}
	}

	/**
	 * Update the specified matter email template in storage.
	 */
	public function update(Request $request, $templateId)
	{
		$requestData = $request->all();
		
		$obj = MatterEmailTemplate::find($templateId);
		if (!$obj) {
			return redirect()->route('adminconsole.features.matteremailtemplate.index')->with('error', 'Matter Email Template Not Found');
		}
		
		$obj->matter_id = @$requestData['matter_id'];
		$obj->name = @$requestData['name'];
		$obj->subject = @$requestData['subject'];
		$obj->description = @$requestData['description'];
		$saved = $obj->save();
		
		if(!$saved)
		{
			return redirect()->back()->with('error', config('constants.server_error'));
		}
		else
		{
			return redirect()->route('adminconsole.features.matter.index')->with('success', 'Matter Email Template Updated Successfully');
		}				
	}
}


