<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Routing\Route;

use App\Models\User;

use Auth;
use Config;

class CustomerController extends Controller
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
     * All Courses.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		$query 		= User::where('client_id', '=', Auth::user()->id);
		  
		$totalData 	= $query->count();	//for all data

		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('Admin.customer.index',compact(['lists', 'totalData']));	 
	}
	
	public function create(Request $request)
	{
		return view('Admin.customer.create');	
	}
	
	public function store(Request $request)
	{
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'name' => 'required|max:255',
										'email' => 'required|max:255|unique:users',
										'password' => 'required|max:255',
										'phone' => 'required|max:255',
									  ]);
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new User;
			$obj->name	=	@$requestData['name'];
			$obj->email			=	@$requestData['email'];
			$obj->city			=	@$requestData['city'];
			$obj->address			=	@$requestData['location'];
			$obj->dob			=	date('Y-m-d', strtotime(@$requestData['dob']));
			if(@$requestData['wedding_anniversary'] != ''){
				$obj->wedding_anniversary			=	date('Y-m-d', strtotime(@$requestData['wedding_anniversary']));
			}
			$obj->client_id		=	 @Auth::user()->id;
			$obj->password	=	Hash::make(@$requestData['password']);
			$obj->phone	=	@$requestData['phone'];
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/customer')->with('success', 'User added Successfully');
			}				
		}	

		return view('Admin.customer.create');	
	}
	
	public function edit(Request $request, $id = NULL)
	{
		
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [										
										'name' => 'required|max:255',
										
										'email' => 'required|max:255|unique:users,email,'.$requestData['id'],
										'phone' => 'required|max:255',
										
										
									  ]);
								  					  
			$obj							= 	User::find(@$requestData['id']);
						
			$obj->name	=	@$requestData['name'];
			$obj->email			=	@$requestData['email'];
			$obj->city			=	@$requestData['city'];
			$obj->address			=	@$requestData['location'];
			$obj->dob			=	date('Y-m-d', strtotime(@$requestData['dob']));
			if(@$requestData['wedding_anniversary'] != ''){
				$obj->wedding_anniversary			=	date('Y-m-d', strtotime(@$requestData['wedding_anniversary']));
			}
			$obj->client_id		=	 @Auth::user()->id;
			$obj->phone	=	@$requestData['phone'];
			if(!empty(@$requestData['password']))
				{		
					$obj->password				=	Hash::make(@$requestData['password']);
					//$objAdmin->decrypt_password		=	@$requestData['password'];
				}
			$obj->phone						=	@$requestData['phone'];
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/customer')->with('success', 'User Edited Successfully');
			}				
		}

		else
		{	
			if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(User::where('id', '=', $id)->where('client_id', '=', Auth::user()->id)->exists()) 
				{
					$fetchedData = User::find($id);
					return view('Admin.customer.edit', compact(['fetchedData']));
				}
				else
				{
					return Redirect::to('/admin/customer')->with('error', 'User Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/customer')->with('error', Config::get('constants.unauthorized'));
			}		
		}	
		
	}
	
	public function uploadcsv(Request $request){
		 $file = $request->file('file');
		$filename = $file->getClientOriginalName();
		$extension = $file->getClientOriginalExtension();
		$tempPath = $file->getRealPath();
		$fileSize = $file->getSize();
		$mimeType = $file->getMimeType();
	  
	   $valid_extension = array("csv");
	   $maxFileSize = 2097152; 
	   
	    if(in_array(strtolower($extension),$valid_extension)){
			if($fileSize <= $maxFileSize){
				$location = public_path().'/csvs';
				$locations = 'csvs';
				 $file->move($location,$filename);
				 $filepath = public_path($locations."/".$filename);
				 
				 $file = fopen($filepath,"r");
				 
				 $importData_arr = array();
				  $i = 0;

				  while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
					 $num = count($filedata );
					 
					 // Skip first row (Remove below comment if you want to skip the first row)
					 if($i == 0){
						$i++;
						continue; 
					 }
					 for ($c=0; $c < $num; $c++) {
						$importData_arr[$i][] = $filedata [$c];
					 }
					 $i++;
				  }
				  fclose($file);
				  $meg = '';
				  foreach($importData_arr as $importData){
					 if(!User::where('email', '=', $importData[1])->where('client_id', '=', Auth::user()->id)->exists()) 
						{
							$obj				= 	new User;
							$obj->name	=	$importData[0];
							$obj->email			=	$importData[1];
							$obj->city			=	$importData[2];
							$obj->address			=	$importData[3];
							$obj->dob			=	date('Y-m-d', strtotime($importData[4]));
							if($importData[5] != ''){
								$obj->wedding_anniversary			=	date('Y-m-d', strtotime(@$importData[5]));
							}
							$obj->client_id		=	 @Auth::user()->id;
							$obj->password	=	Hash::make(@$importData[6]);
							$obj->phone	=	@$importData[6];
							$saved				=	$obj->save(); 
						}else{
							$meg .= $importData[2].' is already exists.';
						}
				  }
				  
				  unlink($filepath);
				return Redirect::to('/admin/customer')->with('success', 'Import Successfully. '.$meg);
			}else{
				return Redirect::to('/admin/customer')->with('error', 'File too large. File must be less than 2MB.');
			}
		}else{
			return Redirect::to('/admin/customer')->with('error', 'Invalid File Extension.');
		}
	}
}
