<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Enquiry;
use PDF;
use Auth;
use Config;
 
class EnquireController extends Controller
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
	
	
	public function index(Request $request)
	{ 
		return view('Admin.enquire.index'); 
	}
	
	public function archived(Request $request)
	{ 
		return view('Admin.enquire.archived'); 
	}
	
	public function archivedenquiry(Request $request, $id){
		if(Enquiry::where('id', $id)->exists()){
			$enq = Enquiry::find($id);
			$enq->is_archived = 1;
			$enq->archived_by = Auth::user()->id;
			$enq->archived_date = date('Y-m-d');
			$saved = $enq->save(); 
			if(!$saved)
			{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
			else
			{
				
				$response['status'] 	= 	true;
				$response['message']	=	'Enquiry archived successfully';
			}
			
			echo json_encode($response);
		}
	}
	public function covertenquiry(Request $request, $id)
	{ 
		$requestData 		= 	$request->all();
		if(Enquiry::where('id', $id)->exists()){
			
			$enqdata = Enquiry::where('id', $id)->first();
			if(!Admin::where('email', $enqdata->email)->exists()){
				$first_name = substr(@$enqdata->first_name, 0, 4);
				$obj				= 	new Admin;
				$obj->first_name	=	@$enqdata->first_name;
				$obj->last_name	=	@$enqdata->last_name;
				
				$obj->email	=	@$enqdata->email;
				$obj->phone	=	@$enqdata->phone;
				$obj->address	=	@$enqdata->address;
				$obj->city	=	@$enqdata->city;
				$obj->country	=	@$enqdata->country;
				$obj->cur_visa	=	@$enqdata->cur_visa;
				$obj->cur_visa_expiry	=	@$enqdata->visa_expiry;
				$obj->role	=	7;
				$saved				=	$obj->save(); 
				$objs							= 	Admin::find($obj->id);
				$objs->client_id	=	strtoupper($first_name).date('ym').$objs->id;
				$saveds				=	$objs->save(); 
				
				$enq = Enquiry::find($id);
				$enq->status = 1;
				$enq->save(); 
				if(!$saved)
				{
					$response['status'] 	= 	false;
					$response['message']	=	'Please try again';
				}
				else
				{
					$enq = Enquiry::find($id);
					$enq->status = 1;
					$enq->save(); 
					$response['status'] 	= 	true;
					$response['message']	=	'Client saved successfully';
				}
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Email is already exist in out record';
			}
			echo json_encode($response);
		}
	}
}
?>