<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Team; 
  
use Auth; 
use Config;

class TeamController extends Controller
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
	
		$query 		= Team::where('id', '!=', ''); 
		 
		$totalData 	= $query->count();	//for all data
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('Admin.teams.index',compact(['lists', 'totalData'])); 	
		
		//return view('Admin.feature.producttype.index');	 
	}
	
	public function edit(Request $request, $id = Null){
	    if ($request->isMethod('post')) 
		{
		    $this->validate($request, [
										'name' => 'required|max:255'
									  ]);
			
			$requestData 		= 	$request->all();
		
			$obj				= 	Team::find($requestData['id']); 
			$obj->name	        =	@$requestData['name'];
			$obj->color			=	@$requestData['color'];
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/teams')->with('success', 'Record update Successfully');
			}	
		}else{
	         if(isset($id) && !empty($id))
		    	{
				    if(Team::where('id', '=', $id)->exists()) 
				    {
				    	$fetchedData = Team::find($id);
						$query 		= Team::where('id', '!=', ''); 
		                $totalData 	= $query->count();	//for all data
		                $lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
				    	return view('Admin.teams.index', compact(['fetchedData','lists','totalData']));
				    }
				else 
				{
					return Redirect::to('/admin/teams')->with('error', 'Team Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/teams')->with('error', Config::get('constants.unauthorized'));
			}
		}
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
		
			$obj				= 	new Team; 
			$obj->name	        =	@$requestData['name'];
			$obj->color			=	@$requestData['color'];
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/teams')->with('success', 'Record Added Successfully');
			}				
		}	

		
	}
	

}
