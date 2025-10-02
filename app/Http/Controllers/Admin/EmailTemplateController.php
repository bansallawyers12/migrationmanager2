<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Routing\Route;

use App\Models\EmailTemplate;

use Auth;
use Config;

class EmailTemplateController extends Controller
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
     * All Email Template.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		//check authorization start	
			if(Auth::user()->role != 1)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		
		$query 		= EmailTemplate::where('id', '!=', '');
		
		$totalData 	= $query->count();	//for all data
		
		if ($request->has('search_term')) 
		{
			$search_term 		= 	$request->input('search_term');
			if(trim($search_term) != '')
			{		
				$query->where('title', 'LIKE', '%' . $search_term . '%');
			}
		}

		if ($request->has('search_term')) 
		{
			$totalData 	= $query->count();//after search
		}
		
		$lists		= $query->get();
		
		return view('Admin.email_template.index',compact(['lists', 'totalData']));	
	}
	
	public function create(Request $request){
		//check authorization start	
			 //$check = $this->checkAuthorizationAction('EmailTemplate', $request->route()->getActionMethod(), Auth::user()->role);
			if(Auth::user()->role != 1)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	 
		//check authorization end
		return view('Admin.email_template.create');	
	}
	public function store(Request $request){
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [
										'title' => 'required|max:255|unique:email_templates,title',
										'subject' => 'required|max:255|unique:email_templates,subject',
										'description' =>'required'
									  ]);
			$obj				= 	new EmailTemplate;
			$obj->title			=	@$requestData['title'];
			$obj->subject		=	@$requestData['subject'];
			$obj->alias	=	$this->createEmailSlug('email_templates',@$requestData['title']);
			$obj->description	=	@$requestData['description'];
			
			$saved				=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/email_templates')->with('success', 'Email Template'.Config::get('constants.edited'));
			}				
		}
	}
	public function editEmailTemplate(Request $request, $id = NULL)
	{	
		//check authorization start	
			 //$check = $this->checkAuthorizationAction('EmailTemplate', $request->route()->getActionMethod(), Auth::user()->role);
			if(Auth::user()->role != 1)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	 
		//check authorization end
	
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [
										'title' => 'required|max:255|unique:email_templates,title,'.@$requestData['id'],
										'subject' => 'required|max:255|unique:email_templates,subject,'.@$requestData['id'],
										'description' =>'required'
									  ]);
			$obj				= 	EmailTemplate::find(@$requestData['id']);
			$obj->title			=	@$requestData['title'];
			$obj->subject		=	@$requestData['subject'];
			$obj->alias	=	$this->createEmailSlug('email_templates',@$requestData['title'], $requestData['id']);
			$obj->description	=	@$requestData['description'];
			
			$saved				=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/email_templates')->with('success', 'Email Template'.Config::get('constants.edited'));
			}				
		}
		else
		{	
			if(isset($id) && !empty($id))
			{
				$id = $this->decodeString($id);	
				if(EmailTemplate::where('id', '=', $id)->exists()) 
				{
					$fetchedData = EmailTemplate::find($id);
					return view('Admin.email_template.edit', compact(['fetchedData']));
				}
				else
				{
					return Redirect::to('/admin/email_templates')->with('error', 'Email Template'.Config::get('constants.not_exist'));
				}	
			}
			else
			{
				return Redirect::to('/admin/email_templates')->with('error', Config::get('constants.unauthorized'));
			}		
		}				
	}
}
