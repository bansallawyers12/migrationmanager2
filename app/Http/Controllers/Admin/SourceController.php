<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Source; 
  
use Auth; 
use Config;

class SourceController extends Controller
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
	
		$query 		= Source::where('id', '!=', ''); 
		 
		$totalData 	= $query->count();	//for all data
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('Admin.feature.source.index',compact(['lists', 'totalData'])); 	
		
	}
	
	public function create(Request $request)
	{
		//check authorization end
		
		return view('Admin.feature.source.create');	
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
			
			$obj				= 	new Source; 
			$obj->name	=	@$requestData['name'];
			
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/source')->with('success', 'Source Added Successfully');
			}				
		}	

		return view('Admin.feature.source.create');	
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
								  					  
			$obj			= 	Source::find(@$requestData['id']);
						
			$obj->name	=	@$requestData['name'];
			
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/source')->with('success', 'Source Edited Successfully');
			}				
		}

		else
		{		
			if(isset($id) && !empty($id))
			{
				$id = $this->decodeString($id);	
				if(Source::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Source::find($id);
					return view('Admin.feature.source.edit', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/admin/source')->with('error', 'Source Not Exist');
				}	
			} 
			else
			{
				return Redirect::to('/admin/source')->with('error', Config::get('constants.unauthorized'));
			}		 
		} 	
		
	}
}
