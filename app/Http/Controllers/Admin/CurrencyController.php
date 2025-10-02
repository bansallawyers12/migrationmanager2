<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Currency;
 
use Auth;  
use Config;

class CurrencyController extends Controller
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
		$query 		= Currency::where('is_base','=','1' )->orwhere('user_id',Auth::user()->id);
		
		$totalData 	= $query->count();	//for all data

		$lists		= $query->get(); 
		
		return view('Admin.currency.index',compact(['lists', 'totalData'])); 
	}
	
	public function create(Request $request) 
	{
		//check authorization start	
			/* $check = $this->checkAuthorizationAction('holiday_package', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	 
		//check authorization end
		
		$managecontact 		=  Managecontact::all();	 */	
		return view('Admin.currency.create');
	}
	
	 public function store(Request $request)
	{
		//check authorization start	
			/* $check = $this->checkAuthorizationAction('holiday_package', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	 */
		//check authorization end
		if ($request->isMethod('post')) 
		{
			
			$requestData 		= 	$request->all();
			$isexist =  Currency::where('user_id',Auth::user()->id)->where('currency_code',$requestData['currency_code'])->where('is_base',0)->exists();
			if($isexist){
				return Redirect::to('/admin/settings/currencies/')->with('error', 'currency is already exist');
			}
			$isexist_base =  Currency::where('currency_code',$requestData['currency_code'])->where('is_base',1)->exists();
			if($isexist_base){
				return Redirect::to('/admin/settings/currencies/')->with('error', 'currency is already exist');
			}
			$obj				= 	new Currency; 
			$obj->user_id	=	Auth::user()->id;   
			$obj->currency_code		=	@$requestData['currency_code'];
			$obj->currency_symbol		=	@$requestData['currency_symbol'];
			$obj->name		=	@$requestData['name'];
			$obj->decimal		=	@$requestData['decimal'];
			$obj->format			=	@$requestData['format'];
			$obj->is_base			=	0;
			$saved				=	$obj->save();  
			
			if(!$saved) 
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{ 
				return Redirect::to('/admin/settings/currencies/')->with('success', 'location added Successfully');
			} 				
		}	 
	} 
	
	 public function edit(Request $request, $id = NULL)
	{			
		//check authorization end
	//check authorization start	
			/* $check = $this->checkAuthorizationAction('holiday_package', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			} */	
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
									  
			$obj				= 	Currency::find($requestData['id']);
			$obj->currency_symbol		=	@$requestData['currency_symbol'];
			$obj->name		=	@$requestData['name'];
			$obj->decimal		=	@$requestData['decimal'];
			$obj->format			=	@$requestData['format'];
			$obj->is_base			=	0;
			$saved				=	$obj->save(); 
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/settings/currencies')->with('success', 'Currency Edited Successfully');
			}				
		}
		else
		{	 
			if(isset($id) && !empty($id)) 
			{
				$id = $this->decodeString($id);	 
				if(Currency::where('user_id',Auth::user()->id)->where('id',$id)->where('is_base',0)->exists()) 
				{
					$fetchedData = Currency::find($id);
					return view('Admin.currency.edit', compact(['fetchedData']));
				}
				else
				{
					return Redirect::to('/admin/settings/currencies')->with('error', 'Currency Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/settings/currencies')->with('error', Config::get('constants.unauthorized'));
			}		
		}				
	} 
	
	
}
