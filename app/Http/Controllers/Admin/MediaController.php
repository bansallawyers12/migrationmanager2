<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\MediaImage;
 
use Auth;
use Config;
use Image;
 
class MediaController extends Controller
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
		  if(Auth::user()->role == 1){
			$query 		= MediaImage::where('id','!=','' );
		}else{	
			$query 		= MediaImage::where('user_id', '=', Auth::user()->id);
		 }	
		$fieldname = $request->fieldname;
		$totalData 	= $query->count();	//for all data

		$lists		= $query->sortable(['id' => 'DESC'])->paginate(config('constants.limit'));
		
		return view('Admin.media.index',compact(['lists', 'totalData', 'fieldname']));   
	}
	public function getlist(Request $request)
	{
		  if(Auth::user()->role == 1){
			$query 		= MediaImage::where('id','!=','' );
		}else{	
			$query 		= MediaImage::where('user_id', '=', Auth::user()->id);
		 }	
		$fieldname = $request->fieldname;
		$totalData 	= $query->count();	//for all data

		$lists		= $query->sortable(['id' => 'DESC'])->paginate(config('constants.limit'));
		
		return view('Admin.media.index',compact(['lists', 'totalData', 'fieldname']));   
	}
	
	 public function uploadlist(Request $request)
		{
        
		$requestData 		= 	$request->all();
         if($files=$request->file('files')){
			foreach($files as $filename){
				$obj = new MediaImage();
				$gallery_image = $this->uploadFile($filename, Config::get('constants.media_gallery')); 

				 $obj->images			=	@$gallery_image;
				$obj->user_id	=	Auth::user()->id;
				$saved		 		=	$obj->save();
			}  
		}	
			if(Auth::user()->role == 1){
			$query 		= MediaImage::where('id','!=','' );
		}else{	
			$query 		= MediaImage::where('user_id', '=', Auth::user()->id);
		 }	
		$fieldname = $request->fieldname;
		$totalData 	= $query->count();	//for all data

		$lists		= $query->sortable(['id' => 'DESC'])->paginate(config('constants.limit'));
		
		return view('Admin.media.index',compact(['lists', 'totalData', 'fieldname'])); 
    }
	
	public function store(Request $request)
	{
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'file' => 'required|max:5128'
									  ]);
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new MediaImage;

				// Gallery Image Upload Function Start 						  
						if($request->hasfile('file')) 
						{	
							
							$gallery_image = $this->uploadFile($request->file('file'), Config::get('constants.media_gallery')); 
							$obj->images			=	@$gallery_image;
							$obj->user_id	=	Auth::user()->id;
							
							$saved		 		=	$obj->save();  
							
							if(!$saved)
							{
								echo json_encode(array('success' => false));
							}
							else
							{
								$query 		= MediaImage::where('id','=',$obj->id )->first();
								echo json_encode(array('success' => true, 'id' => $query->id, 'imagedata' => $query->images));
							}
						}
			
				
		
				exit();
						
		}	
		
	}
	
	public function deleteAction(Request $request, $id = NULL)
	{			
		$recordExist = DB::table('media_images')->where('id', $request->imageid)->exists();
		if($recordExist)
		{
			$recordimage = DB::table('media_images')->where('id', $request->imageid)->first();
			$gallery_image = $this->unlinkFile($recordimage->images, Config::get('constants.media_gallery')); 
			$response	=	DB::table('media_images')->where('id', @$request->imageid)->delete();
			if($response) 
			{
				
				echo json_encode(array('success'=> true));
			}else{
				echo json_encode(array('success'=> true));
			}
			die;
		}
	}
	 
	
}
