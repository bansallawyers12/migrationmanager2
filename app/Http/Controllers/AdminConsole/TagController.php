<?php
namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Tag; 
  
use Auth; 
use Config;

class TagController extends Controller
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
	
		$query 		= Tag::where('id', '!=', '')->with(['createddetail', 'updateddetail']); 
		 
		$totalData 	= $query->count();	//for all data
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('AdminConsole.features.tags.index',compact(['lists', 'totalData'])); 	
		
		//return view('AdminConsole\.features\.producttype.index');	 
	}
	
	public function create(Request $request)
	{
		//check authorization end
		//return view('AdminConsole\.system\.users\.create',compact(['usertype']));	
		
		return view('AdminConsole.features.tags.create');	
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
			
			$obj				= 	new Tag; 
			$obj->name	=	@$requestData['name'];
			$obj->created_by	=	Auth::user()->id;
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/tags')->with('success', 'Record Added Successfully');
			}				
		}	

		return view('AdminConsole.features.tags.create');	
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
								  					  
			$obj			= 	Tag::find(@$requestData['id']);	
			$obj->updated_by	=	Auth::user()->id;			
			$obj->name	=	@$requestData['name'];
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/tags')->with('success', 'Record updated Successfully');
			}				
		}

		else
		{		
			if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(Tag::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Tag::find($id);
					return view('AdminConsole.features.tags.edit', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/admin/tags')->with('error', 'Record Not Exist');
				}	
			} 
			else
			{
				return Redirect::to('/admin/tags')->with('error', Config::get('constants.unauthorized'));
			}		 
		} 	
		
	}
}


