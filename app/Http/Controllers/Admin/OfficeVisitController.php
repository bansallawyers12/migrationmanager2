<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\CheckinLog;
use App\Models\CheckinHistory;

use Auth;
use Config;

class OfficeVisitController extends Controller
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

	public function checkin(Request $request){
		$requestData 		= 	$request->all();
		$obj = new \App\Models\CheckinLog;
		$obj->client_id = $requestData['contact'];
		$obj->user_id = $requestData['assignee'];
		$obj->visit_purpose = $requestData['message'];
		$obj->office = $requestData['office'];
		$obj->contact_type = $requestData['utype'];
		$obj->status = 0;
		$obj->date = date('Y-m-d');
		$saved = $obj->save();
        if(!$saved)
		{
			return redirect()->back()->with('error', Config::get('constants.server_error'));
		}
		else
		{
		    $o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = $requestData['assignee'];
	    	$o->module_id = $obj->id;
	    	$o->url = \URL::to('/admin/office-visits/waiting');
	    	$o->notification_type = 'officevisit';
	    	$o->message = 'Office visit Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
	    	$o->save();
			$objs = new CheckinHistory;
			$objs->subject = 'has created check-in';
			$objs->created_by = Auth::user()->id;
			$objs->checkin_id = $obj->id;
			$objs->save();
			return redirect()->back()->with('success', 'Checkin updated successfully');
		}
	}

	public function index(Request $request)
	{
		$query 		= CheckinLog::where('id', '!=', '');

		$totalData 	= $query->count();	//for all data
		if($request->has('office')){
			$office 		= 	$request->input('office');
			if(trim($office) != '')
			{
				$query->where('office', '=', $office);
			}
		}
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));

		return view('Admin.officevisits.index',compact(['lists', 'totalData']));
	}

	public function getcheckin(Request $request)
	{

		$CheckinLog 		= CheckinLog::where('id', '=', $request->id)->first();

		if($CheckinLog){
			ob_start();
				if($CheckinLog->contact_type == 'Lead'){
				    	$client = \App\Models\Lead::where('id', '=', $CheckinLog->client_id)->first();
				}else{
				    	$client = \App\Models\Admin::where('role', '=', '7')->where('id', '=', $CheckinLog->client_id)->first();
				}

			?>
			<div class="row">
				<div class="col-md-12">
					<?php
					if($CheckinLog->status == 0){
						?>
						<h5 class="text-warning">Waiting</h5>
						<?php
					}else if($CheckinLog->status == 2){
						?>
						<h5 class="text-info">Attending</h5>
						<?php
					}else if($CheckinLog->status == 1){
						?>
						<h5 class="text-success">Completed</h5>
						<?php
					}
					?>
				</div>
			</div>
			<div class="row">
					<div class="col-md-6">
						<b>Contact</b>
						<div class="clientinfo">
							<a href="<?php echo \URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$client->id))); ?>"><?php echo $client->first_name.' '.$client->last_name; ?></a>
							<br>
							<?php echo $client->email; ?>
						</div>
					</div>
					<div class="col-md-6">
						<b><?php echo $CheckinLog->contact_type; ?></b>
						<br>
						<?php
						$checkin = \App\Models\Branch::where('id', $CheckinLog->office)->first();
						if($checkin){
						echo '<a target="_blank" href="'.\URL::to('/admin/branch/view/'.@$checkin->id).'">'.@$checkin->office_name.'</a>';
						}
						?>

					</div>

					<div class="col-md-12">
						<div class="form-group">
							<label>Visit Purpose</label>
								<textarea class="form-control visitpurpose" data-id="<?php echo $CheckinLog->id; ?>" ><?php echo $CheckinLog->visit_purpose; ?></textarea>
						</div>
					</div>

					<div class="col-md-7">
						<table class="table">
						<thead>
								<tr>
									<th>In Person Date</th>
									<th>Session Start</th>
									<th>Session End</th>
								</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php echo date('Y-m-d',strtotime($CheckinLog->created_at)); ?></td>
								<td><?php if($CheckinLog->sesion_start != '') { echo date('h:i A',strtotime($CheckinLog->sesion_start)); }else{ echo '-'; } ?></td>
								<td><?php if($CheckinLog->sesion_end != '') { echo date('h:i A',strtotime($CheckinLog->sesion_end)); }else{ echo '-'; } ?></td>
							</tr>

							</tbody>
						</table>
					</div>
					<div class="col-md-5">
						<div style="padding: 6px 8px; border-radius: 4px; background-color: rgb(84, 178, 75); margin-top: 14px;">
						<div class="row">
						<div class="col-md-6">
							<div class="ag-flex col-hr-3" style="flex-direction: column;"><p class="marginNone text-semi-bold text-white">Wait Time</p> <p class="marginNone small  text-white"><?php if($CheckinLog->status == 0){ ?><span id="waitcount"> 00h 0m 0s </span><?php }else if($CheckinLog->status == 2){ echo '<span>'.$CheckinLog->wait_time.'</span>'; }else if($CheckinLog->status == 1){ echo '<span>'.$CheckinLog->wait_time.'</span>'; }else{ echo '<span >-</span>'; } ?></p></div></div>
							<div class="col-md-6">
							<div class="ag-flex" style="flex-direction: column;"><p class="marginNone text-semi-bold  text-white">Attend Time</p> <p class="marginNone small  text-white"><?php if($CheckinLog->status == 2){ ?><span id="attendtime"> 00h 0m 0s </span><?php }else if($CheckinLog->status == 1){ echo '<span>'.$CheckinLog->attend_time.'</span>'; }else{ echo '<span >-</span>'; } ?>

							</p></div></div>
							</div>
						</div>
					</div>
					<div class="col-md-7">
						<b>In Person Assignee </b> <a class="openassignee" href="javascript:;"><i class="fa fa-edit"></i></a>
						<br>
						<?php
						$admin = \App\Models\Admin::where('role', '!=', '7')->where('id', '=', $CheckinLog->user_id)->first();
						?>
						<a href=""><?php echo @$admin->first_name.' '.@$admin->last_name; ?></a>
						<br>
						<span><?php echo @$admin->email; ?></span>
					</div>
						<div class="assignee" style="display:none;">
						    <div class="row">
						        <div class="col-md-8">
						            <select class="form-control select2" id="changeassignee" name="changeassignee">
						                 <?php
											foreach(\App\Models\Admin::where('role','!=',7)->orderby('first_name','ASC')->get() as $admin){
												$branchname = \App\Models\Branch::where('id',$admin->office_id)->first();
										?>
												<option value="<?php echo $admin->id; ?>"><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-md-2">
									<a class="saveassignee btn btn-success" data-id="<?php echo $CheckinLog->id; ?>" href="javascript:;">Save</a>
								</div>
								<div class="col-md-2">
									<a class="closeassignee" href="javascript:;"><i class="fa fa-times"></i></a>
								</div>
							</div>
						</div>

					<div class="col-md-5">
					<?php
					if($CheckinLog->status == 0){
					?>
						<a data-id="<?php echo $CheckinLog->id; ?>" href="javascript:;" class="btn btn-success attendsession">Attend Session</a>
					<?php }else if($CheckinLog->status == 2){ ?>
						<a data-id="<?php echo $CheckinLog->id; ?>" href="javascript:;" class="btn btn-success completesession">Complete Session</a>
					<?php } ?>
					</div>
					<input type="hidden" value="" id="waitcountdata">
					<input type="hidden" value="" id="attendcountdata">
					<div class="col-md-12">
						<div class="form-group">
							<label>Comment</label>
							<textarea class="form-control visit_comment" name="comment"></textarea>
						</div>
						<div class="form-group">
							<button data-id="<?php echo $CheckinLog->id; ?>" type="button" class="savevisitcomment btn btn-primary">Save</button>
						</div>
					</div>

					<div class="col-md-12">
						<h4>Logs</h4>
						<div class="logsdata">
						<?php
						$logslist = CheckinHistory::where('checkin_id',$CheckinLog->id)->orderby('created_at', 'DESC')->get();
						foreach($logslist as $llist){
							$admin = \App\Models\Admin::where('id', $llist->created_by)->first();
						?>
							<div class="logsitem">
								<div class="row">
									<div class="col-md-7">
										<span class="ag-avatar"><?php echo substr($admin->first_name, 0, 1); ?></span>
										<span class="text_info"><span><?php echo $admin->first_name; ?></span><?php echo $llist->subject; ?></span>
									</div>
									<div class="col-md-5">
										<span class="logs_date"><?php echo date('d M Y h:i A', strtotime($llist->created_at)); ?></span>
									</div>
									<?php if($llist->description != ''){ ?>
									<div class="col-md-12 logs_comment">
										<p><?php echo $llist->description; ?></p>
									</div>
									<?php } ?>
								</div>
							</div>
						<?php } ?>
						</div>
					</div>
				</div>
				<script>
				function pretty_time_stringd(num) {
					return ( num < 10 ? "0" : "" ) + num;
				}
				var start = new Date('<?php echo date('Y-m-d H:i:s',strtotime($CheckinLog->created_at)); ?>');
				setInterval(function() {
				  var total_seconds = (new Date - start) / 1000;

				  var hours = Math.floor(total_seconds / 3600);
				  total_seconds = total_seconds % 3600;

				  var minutes = Math.floor(total_seconds / 60);
				  total_seconds = total_seconds % 60;

				  var seconds = Math.floor(total_seconds);

				  hours = pretty_time_stringd(hours);
				  minutes = pretty_time_stringd(minutes);
				  seconds = pretty_time_stringd(seconds);

				  var currentTimeString = hours + "h:" + minutes + "m:" + seconds+'s';

				  $('#waitcount').text(currentTimeString);
				  $('#waitcountdata').val(currentTimeString);
				}, 1000);
				<?php
				if($CheckinLog->status == 2){
					?>
					var start = new Date('<?php echo date('Y-m-d H:i:s',strtotime($CheckinLog->sesion_start)); ?>');
				setInterval(function() {
				  var total_seconds = (new Date - start) / 1000;

				  var hours = Math.floor(total_seconds / 3600);
				  total_seconds = total_seconds % 3600;

				  var minutes = Math.floor(total_seconds / 60);
				  total_seconds = total_seconds % 60;

				  var seconds = Math.floor(total_seconds);

				  hours = pretty_time_stringd(hours);
				  minutes = pretty_time_stringd(minutes);
				  seconds = pretty_time_stringd(seconds);

				  var currentTimeString = hours + "h:" + minutes + "m:" + seconds+'s';

				  $('#attendtime').text(currentTimeString);
				  $('#attendcountdata').val(currentTimeString);
				}, 1000);
					<?php
				}
				?>
				</script>
			<?php
			return ob_get_clean();
		}

	}
	public function update_visit_purpose(Request $request){
		$obj = CheckinLog::find($request->id);
		$obj->visit_purpose = $request->visit_purpose;
		$saved = $obj->save();
		if($saved){
			$response['status'] 	= 	true;
			$response['message']	=	'saved successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function update_visit_comment(Request $request){
		$objs = new CheckinHistory;
		$objs->subject = 'has commented';
		$objs->created_by = Auth::user()->id;
		$objs->checkin_id = $request->id;
		$objs->description = $request->visit_comment;
		$saved = $objs->save();
		if($saved){
			$response['status'] 	= 	true;
			$response['message']	=	'saved successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function change_assignee(Request $request){
		$objs = CheckinLog::find($request->id);
		$objs->user_id = $request->assinee;

		$saved = $objs->save();
		if($objs->status == 2){
		    $t = 'attending';
		}else if($objs->status == 1){
		    $t = 'completed';
		}else if($objs->status == 0){
		    $t = 'waiting';
		}
		if($saved){
		    $o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = $request->assinee;
	    	$o->module_id = $request->id;
	    	$o->url = \URL::to('/admin/office-visits/'.$t);
	    	$o->notification_type = 'officevisit';
	    	$o->message = 'Office Visit Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
	    	$o->save();
			$response['status'] 	= 	true;
			$response['message']	=	'Updated successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}


    public function attend_session(Request $request){ 
		$obj = CheckinLog::find($request->id);
		$obj->sesion_start = date('Y-m-d H:i');
		$obj->wait_time = $request->waitcountdata;

		if($request->waitingtype == 1){ //waiting type = Pls send
            $obj->status = 2; //attending session
			$t = 'attending';
        } else {  //waiting type = waiting
            $obj->status = 0; //waiting session
            $obj->wait_type = 1; //waiting type = Pls send
			$t = 'waiting';
        }

        $saved = $obj->save();

        if($saved){
		    $o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = 36608; // to receptionist id  //info@bansaleducation.com.au 36730 (for live)
	    	$o->module_id = $request->id;
	    	$o->url = \URL::to('/admin/office-visits/'.$t);
	    	$o->notification_type = 'officevisit';
	    	$o->message = 'Office Visit Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
	    	$o->save();
		}

		$objs = new CheckinHistory;
		$objs->subject = 'has started session';
		$objs->created_by = Auth::user()->id;
		$objs->checkin_id = $request->id;
		$saved = $objs->save();
		if($saved){
			$response['status'] 	= 	true;
			$response['message']	=	'saved successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function complete_session(Request $request){
		$obj = CheckinLog::find($request->id);
		$obj->sesion_end = date('Y-m-d H:i');
		$obj->attend_time = $request->attendcountdata;
		$obj->status = 1;
		$saved = $obj->save();

		$objs = new CheckinHistory;
		$objs->subject = 'has completed session';
		$objs->created_by = Auth::user()->id;
		$objs->checkin_id = $request->id;
		$saved = $objs->save();
		if($saved){
			$response['status'] 	= 	true;
			$response['message']	=	'saved successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
	public function waiting(Request $request)
	{
	      if(isset($request->t)){
    	    if(\App\Models\Notification::where('id', $request->t)->exists()){
    	       $ovv =  \App\Models\Notification::find($request->t);
    	       $ovv->receiver_status = 1;
    	       $ovv->save();
    	    }
	    }
		$query 		= CheckinLog::where('status', '=', 0);

		$totalData 	= $query->count();	//for all data
		if($request->has('office')){
			$office 		= 	$request->input('office');
			if(trim($office) != '')
			{
				$query->where('office', '=', $office);
			}
		}
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));

		return view('Admin.officevisits.waiting',compact(['lists', 'totalData']));
	}
	public function attending(Request $request)
	{
	      if(isset($request->t)){
    	    if(\App\Models\Notification::where('id', $request->t)->exists()){
    	       $ovv =  \App\Models\Notification::find($request->t);
    	       $ovv->receiver_status = 1;
    	       $ovv->save();
    	    }
	    }
		$query 		= CheckinLog::where('status', '=', '2');

		$totalData 	= $query->count();	//for all data
		if($request->has('office')){
			$office 		= 	$request->input('office');
			if(trim($office) != '')
			{
				$query->where('office', '=', $office);
			}
		}
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));

		return view('Admin.officevisits.attending',compact(['lists', 'totalData']));
	}
	public function completed(Request $request)
	{
	      if(isset($request->t)){
    	    if(\App\Models\Notification::where('id', $request->t)->exists()){
    	       $ovv =  \App\Models\Notification::find($request->t);
    	       $ovv->receiver_status = 1;
    	       $ovv->save();
    	    }
	    }
		$query 		= CheckinLog::where('status', '=', '1');

		$totalData 	= $query->count();	//for all data
		if($request->has('office')){
			$office 		= 	$request->input('office');
			if(trim($office) != '')
			{
				$query->where('office', '=', $office);
			}
		}
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
        return view('Admin.officevisits.completed',compact(['lists', 'totalData']));
	}

	public function archived(Request $request)
	{
		$query 		= CheckinLog::where('is_archived', '=', '1');

		$totalData 	= $query->count();	//for all data
		if($request->has('office')){
			$office 		= 	$request->input('office');
			if(trim($office) != '')
			{
				$query->where('office', '=', $office);
			}
		}
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));

		return view('Admin.officevisits.archived',compact(['lists', 'totalData']));
	}
	public function create(Request $request){
		return view('Admin.officevisits.create');
	}

}
