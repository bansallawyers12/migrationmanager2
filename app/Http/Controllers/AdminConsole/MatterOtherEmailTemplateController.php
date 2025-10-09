<?php
namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\MatterOtherEmailTemplate; 
use App\Models\Matter;
  
use Auth; 
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Exception;

class MatterOtherEmailTemplateController extends Controller
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
     * All Matter Email Templates for a specific matter.
     *
     * @return \Illuminate\Http\Response 
     */
	public function index(Request $request, $matterId = NULL)
	{
		//check authorization start	
			
			/* if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			} */	
		//check authorization end 
	
		$query = MatterOtherEmailTemplate::where('matter_id', $matterId); 
		 
		$totalData 	= $query->count();	//for all data
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		$matter = Matter::find($matterId);
		
		return view('AdminConsole\.features\.matterotheremailtemplate.index',compact(['lists', 'totalData', 'matter', 'matterId'])); 	
		
	}
	
	public function create(Request $request, $matterId = NULL)
	{	
		$matter = Matter::find($matterId);
		return view('AdminConsole\.features\.matterotheremailtemplate.create', compact(['matterId', 'matter']));	
	}
	
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$requestData = $request->all();
			
			// Validation
			$request->validate([
				'matter_id' => 'required|exists:matters,id',
				'name' => 'required|string|max:255',
				'subject' => 'required|string|max:255',
				'description' => 'required|string',
			]);
			
			$obj				= 	new MatterOtherEmailTemplate; 
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
				return Redirect::to('/admin/matter_other_email_template/'.$obj->matter_id)->with('success', 'Matter Email Template Added Successfully');
			}				
		}	
		return view('AdminConsole\.features\.matterotheremailtemplate.create');	
	}	
	
	public function edit(Request $request, $templateId = NULL, $matterId = NULL)
	{
		
		if ($request->isMethod('post')) 
		{
			$requestData = $request->all();
			
			// Validation
			$request->validate([
				'id' => 'required|exists:matter_other_email_templates,id',
				'matter_id' => 'required|exists:matters,id',
				'name' => 'required|string|max:255',
				'subject' => 'required|string|max:255',
				'description' => 'required|string',
			]);
			
			$obj			    = 	MatterOtherEmailTemplate::find(@$requestData['id']);
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
				return Redirect::to('/admin/matter_other_email_template/'.$obj->matter_id)->with('success', 'Matter Email Template Edited Successfully');
			}				
		}
		else
		{		
			if(isset($templateId) && !empty($templateId))
			{
				try {
					// Use templateId directly without decoding
					$template = MatterOtherEmailTemplate::find($templateId);
					if(!$template) {
						return Redirect::to('/admin/matter')->with('error', 'Template not found with ID: ' . $templateId);
					}
					
					// Verify matterId matches the template's matter_id
					if($matterId && $template->matter_id != $matterId) {
						return Redirect::to('/admin/matter')->with('error', 'Matter ID mismatch');
					}
					
					$matter = Matter::find($template->matter_id);
					if(!$matter) {
						return Redirect::to('/admin/matter')->with('error', 'Matter not found with ID: ' . $template->matter_id);
					}
					
					$matterId = $template->matter_id;
					$fetchedData = $template;
					return view('AdminConsole\.features\.matterotheremailtemplate.edit', compact('fetchedData', 'matterId', 'matter'));
				} catch(Exception $e) {
					return Redirect::to('/admin/matter')->with('error', 'Error: ' . $e->getMessage());
				}
			} 
			else
			{
				return Redirect::to('/admin/matter')->with('error', Config::get('constants.unauthorized'));
			}		
		} 	
	}
	
	public function destroy($id)
	{
		$template = MatterOtherEmailTemplate::find($id);
		if($template) {
			$matterId = $template->matter_id;
			$template->delete();
			return Redirect::to('/admin/matter_other_email_template/'.$matterId)->with('success', 'Matter Email Template Deleted Successfully');
		}
		return Redirect::to('/admin/matter')->with('error', 'Template not found');
	}
}


