<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Followup;
use App\Models\FollowupType;
use App\Models\Lead;
use App\Models\Attachment;
 
use Auth;
use Config;
class FollowupController extends Controller
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
		$id = $this->decodeString($request->leadid);
	//	Lead::where('id', '=', $id)->where('user_id', '=', Auth::user()->id)->orwhere('assign_to', '=', Auth::user()->id)->exists()
		if(Lead::where('id', '=', $id)->exists()) 
		{
			$query = Followup::where('lead_id','=',	$id)->with(['user']);
			$totalData 	= $query->count();	//for all data

			$lists		= $query->orderby('pin', 'DESC')->orderby('created_at', 'DESC')->paginate(config('constants.limit')); 
			return view('Admin.leads.list',compact(['lists', 'totalData'])); 
		}
	} 
	
	public function compose(Request $request){
		$requestData 		= 	$request->all();
		$ledID = $this->decodeString(@$requestData['lead_id']);
		 $LEADS = Lead::where('id', $ledID)->first();
		 $assi = \App\Models\Admin::where('id', @$LEADS->assign_to)->first();
			 $message = @$requestData['message'];
			    			 	 $subject = @$requestData['subject'];
			    	$subject = str_replace('{Client First Name}',$LEADS->first_name, $subject);
			$message = str_replace('{Client First Name}',$LEADS->first_name, $message);	
			$message = str_replace('{Company Name}','Test', $message);
			$message = str_replace('{Client Assignee Name}',@$assi->first_name, $message);
		$followup 					= new Followup;
		$followup->lead_id			= $this->decodeString(@$requestData['lead_id']);
		$followup->user_id			= Auth::user()->id;
		
		$followup->note				= $message;
		$followup->subject			= @$subject;
		$followup->followup_type	= 'mail_compose';
		//$followup->rem_cat	= @$requestData['rem_cat'];
		$saved				=	$followup->save();  
			
		if(!$saved) 
		{
			return redirect()->back()->with('error', Config::get('constants.server_error'));
		}
		else
		{ 
		   
			    	$ccarray = array();
			    		$array = array();
			    			
			
				$issuccess = $this->send_compose_template($message, 'Test', $requestData['email_to'], $subject, 'test@gmail.com', $array, @$ccarray);
		
			return Redirect::to('/admin/leads/history/'.@$requestData['lead_id'])->with('success', 'Mail sent successpully Successfully');
		}
	}
	public function store(Request $request)
	{ 
		$requestData 		= 	$request->all();
		
	
		$followup 					= new Followup;
		$followup->lead_id			= $this->decodeString(@$requestData['lead_id']);
		$followup->user_id			= Auth::user()->id;
		$followup->note				= @$requestData['description'];
		$followup->subject				= @$requestData['remindersubject'];
		$followup->followup_type	= @$requestData['note_type'];
		$followup->rem_cat	= @$requestData['rem_cat'];
		if(isset($requestData['followup_date']) && $requestData['followup_date'] != ''){
		//	$followup->followup_date	= @$requestData['followup_date'].date('H:i', strtotime($requestData['followup_time']));
				$followup->followup_date	=  @$requestData['followup_date'].' '.date('H:i', strtotime($requestData['followup_time']));
		}
		
		$saved				=	$followup->save();  
			
		if(!$saved) 
		{
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'leadid' => $requestData['lead_id']));
		}
		else
		{ 
			$note_type = $this->followuptype($requestData['note_type'],'id' );
			$Lead = Lead::find($this->decodeString($requestData['lead_id']));
			$Lead->status = $note_type;
			$Lead->save();
			echo json_encode(array('success' => true, 'message' => 'successfully saved', 'leadid' => $requestData['lead_id']));
		}
	}
	public function followupupdate(Request $request)
	{ 
		$requestData 		= 	$request->all();
		$lead_id = $this->decodeString(@$requestData['lead_id']);
	
		$followup 					= Followup::find($lead_id);
		$followup->note				= @$requestData['description'];
		$followup->subject				= @$requestData['remindersubject'];
		$followup->followup_type	= @$requestData['note_type'];
		$followup->rem_cat	= @$requestData['rem_cat'];
		if(isset($requestData['followup_date']) && $requestData['followup_date'] != ''){
		//	$followup->followup_date	= @$requestData['followup_date'].date('H:i', strtotime($requestData['followup_time']));
				$followup->followup_date	=  @$requestData['followup_date'].' '.date('H:i', strtotime($requestData['followup_time']));
		}
		
		$saved				=	$followup->save();  
			
		if(!$saved) 
		{
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'leadid' => $requestData['lead_id']));
		}
		else
		{ 
			$note_type = $this->followuptype($requestData['note_type'],'id' );
			$Lead = Lead::find($followup->lead_id);
			$Lead->status = $note_type;
			$Lead->save();
			echo json_encode(array('success' => true, 'message' => 'successfully saved', 'leadid' => base64_encode(convert_uuencode($followup->lead_id))));
		}
	}
	
		public static function followuptype($type, $field) { 
			$FollowupType = FollowupType::where('type','=',	$type)->first();
			
			return @$FollowupType->$field;
		}
		public static function time_Ago($time) { 
			$diff     = time() - $time; 
			// Time difference in seconds 
			$sec     = $diff; 
			// Convert time difference in minutes 
			$min     = round($diff / 60 ); 
			// Convert time difference in hours 
			$hrs     = round($diff / 3600); 
		  
		// Convert time difference in days 
			$days     = round($diff / 86400 ); 
		  
		// Convert time difference in weeks 
			$weeks     = round($diff / 604800); 
		  
		// Convert time difference in months 
		$mnths     = round($diff / 2600640 ); 
		  
		// Convert time difference in years 
		$yrs     = round($diff / 31207680 ); 
		  
		// Check for seconds 
		if($sec <= 60) { 
			echo "$sec seconds ago"; 
		} 
		  
		// Check for minutes 
		else if($min <= 60) { 
			if($min==1) { 
				echo "one minute ago"; 
			} 
			else { 
				echo "$min minutes ago"; 
			} 
		} 
		  
		// Check for hours 
		else if($hrs <= 24) { 
			if($hrs == 1) {  
				echo " an hour ago"; 
			} 
			else { 
				echo "$hrs hours ago"; 
			} 
		}else{
			echo date("d M, Y", $time);
		} 
	} 
}
?>