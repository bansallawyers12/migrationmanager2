<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

use App\Classes\FileDurationClass;

use App\Models\FreeDownload;

use Auth;
use Config;

class FreeDownloadController extends Controller
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
     * All Free Downloads.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('FreeDownload', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		
		$query 		= FreeDownload::where('id', '!=', '');
		
		$totalData 	= $query->count();	//for all data

		//searching start	
			if ($request->has('search_term')) 
			{
				$search_term 		= 	$request->input('search_term');

				if(trim($search_term) != '')	
				{
					$query->where('professor_name', 'LIKE', '%' . $search_term . '%');
				}
			}
			if ($request->has('search_term_subject')) 
			{
				$search_term_subject 		= 	$request->input('search_term_subject');
				if(trim($search_term_subject) != '')
				{
					$query->where('subject', 'LIKE', '%' . $search_term_subject . '%');
				}
			}
			if ($request->has('search_term_type')) 
			{
				$search_term_type 		= 	$request->input('search_term_type');
				if(trim($search_term_type) != '')
				{
					$query->where('type', '=', $search_term_type);
				}
			}
			if ($request->has('search_term_from')) 
			{
				$search_term_from 		= 	$request->input('search_term_from');
				if(trim($search_term_from) != '')
				{
					$query->whereDate('created_at', '>=', $search_term_from);
				}
			}
			if ($request->has('search_term_to')) 
			{	
				$search_term_to 		= 	$request->input('search_term_to');
				if(trim($search_term_to) != '')
				{
					$query->whereDate('created_at', '<=', $search_term_to);
				}	
			}
			
			if ($request->has('search_term') || $request->has('search_term_subject') || $request->has('search_term_type') || $request->has('search_term_from') || $request->has('search_term_to')) 
			{
				$totalData 	= $query->count();//after search
			}
		//searching end	

		$lists		= $query->sortable(['id'=>'desc'])->paginate(config('constants.limit'));
	
		return view('Admin.free_downloads.index',compact(['lists', 'totalData']));	
	}
	/**
     * Add Free Downloads.
     *
     * @return \Illuminate\Http\Response
     */
	public function addFreeDownload(Request $request)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('FreeDownload', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 					= 	$request->all();
	
			$this->validate($request, [
										'resource' => 'required',
										'professor_name' => 'required|max:255',
										'subject' => 'required|max:255',
										'type' => 'required',
										'content' => 'required',
										'duration' => 'required'
									  ]);
									  
			/* Content Upload Function Start */						  
				if($request->file('content')) 
				{
					if(@$requestData['type'] == 1)//Audio
					{						
						$content = $this->uploadFile($request->file('content'), Config::get('constants.free_audio'));
					}
					else if(@$requestData['type'] == 2) //Video
					{
						$content = $this->uploadFile($request->file('content'), Config::get('constants.free_video'));
					}	
					else if(@$requestData['type'] == 3) //PDF
					{
						$content = $this->uploadFile($request->file('content'), Config::get('constants.free_pdf'));
					}	
				}	
			/* Content Upload Function End */

			/* Get Duration Start */
				/* if(@$requestData['type'] == 1)//Audio
				{
					$file = new FileDurationClass(Config::get('constants.free_audio').'/'.$content);
					$duration = $file->getDuration();//(slower) for VBR (or CBR)	
					$duration = FileDurationClass::formatTime($duration);	
				}
				else if(@$requestData['type'] == 2)//Video
				{
					$file = new FileDurationClass(Config::get('constants.free_video').'/'.$content);
					$duration = $file->getDurationEstimate();//(slower) for VBR (or CBR)	
					$duration = FileDurationClass::formatTime($duration);
				}
				else
				{
					$duration = NULL;
				}		 */
			/* Get Duration End */
			
			/* Free Image Upload Function Start */						  
					if($request->hasfile('free_img')) 
					{	
						$free_img = $this->uploadFile($request->file('free_img'), Config::get('constants.free_imgs'));
					}
					else
					{
						$free_img = NULL;
					}		
			/* Free Image Upload Function End */
					

			$obj						= 	new FreeDownload;
			$obj->resource				=	@$requestData['resource'];
			$obj->professor_name		=	@$requestData['professor_name'];
			$obj->subject				=	@$requestData['subject'];
			$obj->type					=	@$requestData['type'];
			$obj->content				=	@$content;
			$obj->free_img				=	@$free_img;
			$obj->duration				=	@$requestData['duration'];

			$saved						=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/free_downloads')->with('success', 'Free Download '.Config::get('constants.added'));	
			}	
		}
		return view('Admin.free_downloads.add_free_download');		
	}
	
	public function editFreeDownload(Request $request, $id = NULL)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('FreeDownload', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [
										'resource' => 'required',
										'professor_name' => 'required|max:255',
										'subject' => 'required|max:255',
										'type' => 'required',
										'old_content' => 'required',
										'duration' => 'required'
									  ]);
									  
			/* File Upload Function Start */						  
				if($request->file('content')) 
				{
					/* Unlink File Function Start */ 
						if(@$requestData['old_content'] != '')
							{
								if(@$requestData['type'] == 1)//Audio
								{
									$this->unlinkFile(@$requestData['old_content'], Config::get('constants.free_audio'));
								}
								else if(@$requestData['type'] == 2) //Video
								{
									$this->unlinkFile(@$requestData['old_content'], Config::get('constants.free_video'));
								}	
								else if(@$requestData['type'] == 3) //PDF
								{
									$this->unlinkFile(@$requestData['old_content'], Config::get('constants.free_pdf'));
								}
							}
					/* Unlink File Function End */
					
					if(@$requestData['type'] == 1)//Audio
					{						
						$content = $this->uploadFile($request->file('content'), Config::get('constants.free_audio'));
					}
					else if(@$requestData['type'] == 2) //Video
					{
						$content = $this->uploadFile($request->file('content'), Config::get('constants.free_video'));
					}	
					else if(@$requestData['type'] == 3) //PDF
					{
						$content = $this->uploadFile($request->file('content'), Config::get('constants.free_pdf'));
					}
				}
			else
				{
					$content = @$requestData['old_content'];
				}		
			/* File Upload Function End */
			
			/* Image Upload Function Start */						  
					if($request->hasfile('free_img')) 
					{	
						/* Unlink File Function Start */ 
							if($requestData['free_img'] != '')
								{
									$this->unlinkFile($requestData['old_free_img'], Config::get('constants.free_imgs'));
								}
						/* Unlink File Function End */
						
						$free_img = $this->uploadFile($request->file('free_img'), Config::get('constants.free_imgs'));
					}
					else
					{
						$free_img = @$requestData['old_free_img'];
					}		
				/* Image Upload Function End */
						  
			$obj						= 	FreeDownload::find($requestData['id']);
			$obj->resource				=	@$requestData['resource'];
			$obj->professor_name		=	@$requestData['professor_name'];
			$obj->subject				=	@$requestData['subject'];
			$obj->type					=	@$requestData['type'];
			$obj->content				=	@$content;
			$obj->free_img				=	@$free_img;
			$obj->duration				=	@$duration;

			$saved						=	$obj->save();

			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/free_downloads')->with('success', 'Free Download'.Config::get('constants.edited'));
			}		
		}
		else
		{	
			if(isset($id) && !empty($id))
			{
				$id = $this->decodeString($id);	
				if(FreeDownload::where('id', '=', $id)->exists())
				{
					$fetchedData = FreeDownload::find($id);
					return view('Admin.free_downloads.edit_free_download', compact(['fetchedData', 'subjects']));
				}
				else
				{
					return Redirect::to('/admin/free_downloads')->with('error', 'Free Download'.Config::get('constants.not_exist'));
				}	
			}
			else
			{
				return Redirect::to('/admin/free_downloads')->with('error', Config::get('constants.unauthorized'));
			}		
		}				
	}
}
