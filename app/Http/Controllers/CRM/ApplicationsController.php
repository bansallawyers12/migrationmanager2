<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Broadcast;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Models\Admin;
use App\Models\Application;
use App\Models\ApplicationFeeOptionType;
use App\Models\ApplicationFeeOption;
   use PDF;
use Illuminate\Support\Str;
use Auth;

class ApplicationsController extends Controller
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
            return Redirect::to('/dashboard')->with('error',config('constants.unauthorized'));
        } */
		//check authorization end
	    //$allstages = Application::select('stage')->groupBy('stage')->get();
		//$query 		= Application::where('id', '!=', '')->with(['application_assignee']);
        $allstages = Application::select('stage')->where('status', '!=', 2)->groupBy('stage')->get();
		$query 		= Application::where('status', '!=', 2)->with(['application_assignee']);

		$totalData 	= $query->count();	//for all data
        if ($request->has('partner'))
		{
			$partner 		= 	$request->input('partner');
			if(trim($partner) != '')
			{
				$query->where('partner_id', '=', $partner);
			}
		}
		if ($request->has('assignee'))
		{
			$assignee 		= 	$request->input('assignee');
			if(trim($assignee) != '')
			{
				$query->where('user_id', '=', $assignee);
			}
		}
		 if ($request->has('stage'))
		{
			$stage 		= 	$request->input('stage');
			if(trim($stage) != '')
			{
				$query->where('stage', '=', $stage);
			}
		}
		$lists		= $query->sortable(['id' => 'desc'])->paginate(10);

		return view('crm.applications.index', compact(['lists', 'totalData','allstages']));

		//return view('crm.applications.index');
	}

	// REMOVED - prospects method
	// public function prospects(Request $request)
	// {
	//     //return view('crm.prospects.index');
	// }

	public function create(Request $request)
	{
		//check authorization end
		//return view('crm.users.create',compact(['usertype']));

		//return view('crm.clients.create');
	}

	public function detail(){
		return view('crm.applications.detail');
	}

	public function getapplicationdetail(Request $request){
		$fetchData = Application::find($request->id); //dd($fetchData);
		return view('crm.clients.applicationdetail', compact(['fetchData']));
	}

	//Load Application Insert Update Data
	public function loadApplicationInsertUpdateData(Request $request){
		// Get client_id and client_matter_id from request
		$clientId = $request->client_id;
		$clientMatterId = $request->client_matter_id;

		// get workflow stage name from 
		$workflowStage = DB::table('client_matters')
			->where('client_matters.client_id', $clientId)
			->where('client_matters.id', $clientMatterId)
			->join('workflow_stages', 'client_matters.workflow_stage_id', '=', 'workflow_stages.id')
			->select('workflow_stages.name','workflow_stages.w_id')
			->first();
		
		// Check if record exists in applications table
		$existingApplication = DB::table('applications')
			->where('client_id', $clientId)
			->where('client_matter_id', $clientMatterId)
			->first();
			
		if($existingApplication) {
			// Update existing record
			$applicationId = DB::table('applications')
				->where('client_id', $clientId)
				->where('client_matter_id', $clientMatterId)
				->update([
					'user_id' => Auth::user()->id,
					'stage' => $workflowStage->name,
					'workflow' => $workflowStage->w_id,
					'updated_at' => now()
				]);
				
			$applicationId = $existingApplication->id;
		} else {
			// Insert new record
			$applicationId = DB::table('applications')->insertGetId([
				'client_matter_id' => $clientMatterId,
				'user_id' => Auth::user()->id,
				'client_id' => $clientId,
				'stage' => $workflowStage->name,
				'workflow' => $workflowStage->w_id,
				'created_at' => now(),
				'updated_at' => now()
			]);
		}
		
		return response()->json([
			'status' => true,
			'application_id' => $applicationId,
			'message' => $existingApplication ? 'Application updated successfully' : 'Application created successfully'
		]);
	}

	public function completestage(Request $request){
		$fetchData = Application::find($request->id);
		$fetchData->status = 1;

		$saved = $fetchData->save();
		if($saved){
			$response['status'] 	= 	true;
			$response['stage']	=	$fetchData->stage;
			$response['width']	=	100;
			$response['message']	=	'Application has been successfully completed.';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}

		echo json_encode($response);
	}
	public function updatestage(Request $request){
		$fetchData = Application::find($request->id);
		$workflowstagecount = \App\Models\WorkflowStage::where('w_id', $fetchData->workflow)->count();
		$widthcount = 0;
		if($workflowstagecount !== 0){
			$s = 100 / $workflowstagecount;
			$widthcount = round($s);
		}
		//$workflowstage = \App\Models\WorkflowStage::where('name', $fetchData->stage)->where('w_id', $fetchData->workflow)->first();
		$workflowstage = \App\Models\WorkflowStage::where('name', 'like', '%'.$fetchData->stage.'%')->where('w_id', $fetchData->workflow)->first();
		$nextid = \App\Models\WorkflowStage::where('id', '>', @$workflowstage->id)->where('w_id', $fetchData->workflow)->orderBy('id','asc')->first();

		$fetchData->stage = $nextid->name;
		$comments = 'moved the stage from  <b>'.$workflowstage->name.'</b> to <b>'.$nextid->name.'</b>';

		$width = $fetchData->progresswidth + $widthcount;
		$fetchData->progresswidth = $width;
		$saved = $fetchData->save();
		if($saved){
			$obj = new \App\Models\ApplicationActivitiesLog;
			$obj->stage = $workflowstage->name;
			$obj->comment = @$comments;
			$obj->app_id = $request->id;
			$obj->type = 'stage';
			$obj->user_id = Auth::user()->id;
			$saved = $obj->save();
			$displayback = false;
			$workflowstage = \App\Models\WorkflowStage::where('w_id', $fetchData->workflow)->orderBy('id','desc')->first();

			if($workflowstage->name == $fetchData->stage){
				$displayback = true;
			}
			$response['status'] 	= 	true;
			$response['stage']	=	$fetchData->stage;
			$response['width']	=	$width;
			$response['displaycomplete']	=	$displayback;
			$response['message']	=	'Application has been successfully moved to next stage.';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function updatebackstage(Request $request){
		$fetchData = Application::find($request->id);
		$workflowstage = \App\Models\WorkflowStage::where('name', $fetchData->stage)->where('w_id', $fetchData->workflow)->first();
		$nextid = \App\Models\WorkflowStage::where('id', '<', $workflowstage->id)->where('w_id', $fetchData->workflow)->orderBy('id','Desc')->first();
		if($nextid){
			$workflowstagecount = \App\Models\WorkflowStage::where('w_id', $fetchData->workflow)->count();
			$widthcount = 0;
			if($workflowstagecount !== 0){
				$s = 100 / $workflowstagecount;
				$widthcount = round($s);
			}
			$fetchData->stage = $nextid->name;
			$comments = 'moved the stage from  <b>'.$workflowstage->name.'</b> to <b>'.$nextid->name.'</b>';
			$width = $fetchData->progresswidth - $widthcount;
			if($width <= 0){
				$width = 0;
			}

			$fetchData->progresswidth = $width;

			$saved = $fetchData->save();
			if($saved){

				$obj = new \App\Models\ApplicationActivitiesLog;
				$obj->stage = $workflowstage->stage;
				$obj->type = 'stage';
				$obj->comment = $comments;
				$obj->app_id = $request->id;
				$obj->user_id = Auth::user()->id;
				$saved = $obj->save();

				$displayback = false;
				$workflowstage = \App\Models\WorkflowStage::where('w_id', $fetchData->workflow)->orderBy('id','desc')->first();

				if($workflowstage->name == $fetchData->stage){
					$displayback = true;
				}

				$response['status'] 	= 	true;
				$response['stage']	=	$fetchData->stage;
				$response['displaycomplete']	=	$displayback;

				$response['width']	=	$width;
				$response['message']	=	'Application has been successfully moved to previous stage.';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
	   }else{
		   $response['status'] 	= 	false;
				$response['message']	=	'';
	   }
		echo json_encode($response);
	}

	public function getapplicationslogs(Request $request){
		//$clientid = @$request->clientid;
		$id = $request->id;
		$fetchData = Application::find($id);

		$stagesquery = \App\Models\WorkflowStage::where('w_id', $fetchData->workflow)->get();
		foreach($stagesquery as $stages){
		$stage1 = '';

							$workflowstagess = \App\Models\WorkflowStage::where('name', $fetchData->stage)->where('w_id', $fetchData->workflow)->first();

					$prevdata = \App\Models\WorkflowStage::where('id', '<', $workflowstagess->id)->where('w_id', $fetchData->workflow)->orderBy('id','Desc')->get();
					$stagearray = array();
					foreach($prevdata as $pre){
						$stagearray[] = $pre->id;
					}

							if(in_array($stages->id, $stagearray)){
								$stage1 = 'app_green';
							}
							if($fetchData->status == 1){
								$stage1 = 'app_green';
							}
							$stagname = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $stages->name)));
							?>

						<div class="accordion cus_accrodian">

							<div class="accordion-header collapsed <?php echo $stage1; ?> <?php if($fetchData->stage == $stages->name && $fetchData->status != 1){ echo  'app_blue'; }  ?>"" role="button" data-toggle="collapse" data-target="#<?php echo $stagname; ?>_accor" aria-expanded="false">
								<h4><?php echo $stages->name; ?></h4>
								<div class="accord_hover">
									<a title="Add Note" class="openappnote" data-app-type="<?php echo $stages->name; ?>" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-file-alt"></i></a>
									<a title="Add Document" class="opendocnote" data-app-type="<?php echo $stagname; ?>" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-file-image"></i></a>
									<a data-app-type="<?php echo $stages->name; ?>" title="Add Appointments" class="openappappoint" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-calendar"></i></a>
									<a data-app-type="<?php echo $stages->name; ?>" title="Email" data-id="{{@$fetchData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="openclientemail" title="Compose Mail" href="javascript:;"><i class="fa fa-envelope"></i></a>
								</div>
							</div>
							<?php
							$applicationlists = \App\Models\ApplicationActivitiesLog::where('app_id', $fetchData->id)->where('stage',$stages->name)->orderby('created_at', 'DESC')->get();

							?>
							<div class="accordion-body collapse" id="<?php echo $stagname; ?>_accor" data-parent="#accordion" style="">
								<div class="activity_list">
								<?php foreach($applicationlists as $applicationlist){
								$admin = \App\Models\Admin::where('id',$applicationlist->user_id)->first();
								?>
									<div class="activity_col">
										<div class="activity_txt_time">
											<span class="span_txt"><b><?php echo $admin->first_name; ?></b> <?php echo $applicationlist->comment; ?></span>
											<span class="span_time"><?php echo date('d D, M Y h:i A', strtotime($applicationlist->created_at)); ?></span>
										</div>
										<?php if($applicationlist->title != ''){ ?>
										<div class="app_description">
											<div class="app_card">
												<div class="app_title"><?php echo $applicationlist->title; ?></div>
											</div>
											<?php if($applicationlist->description != ''){ ?>
											<div class="log_desc">
												<?php echo $applicationlist->description; ?>
											</div>
											<?php } ?>
										</div>
										<?php } ?>
									</div>
								<?php } ?>
								</div>
							</div>
						</div>
						<?php } ?>
		<?php
		}

	public function addNote(Request $request){
		$noteid =  $request->noteid;
		$type =  $request->type;

		$obj = new \App\Models\ApplicationActivitiesLog;
			$obj->stage = $type;
			$obj->type = 'note';
			$obj->comment = 'added a note';
			$obj->title = $request->title;
			$obj->description = $request->description;
			$obj->app_id = $noteid;
			$obj->user_id = Auth::user()->id;
			$saved = $obj->save();
		$saved = $obj->save();
		if($saved){
			$response['status'] 	= 	true;
			$response['message']	=	'Note successfully added';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function getapplicationnotes(Request $request){
		$noteid =  $request->id;

		$lists = \App\Models\ApplicationActivitiesLog::where('type','note')->where('app_id',$noteid)->orderby('created_at', 'DESC')->get();

		ob_start();
			?>
			<div class="note_term_list">
				<?php
				foreach($lists as $list){
					$admin = \App\Models\Admin::where('id', $list->user_id)->first();
				?>
					<div class="note_col" id="note_id_<?php echo $list->id; ?>">
						<div class="note_content">
						<h4><a class="viewapplicationnote" data-id="<?php echo $list->id; ?>" href="javascript:;"><?php echo @$list->title == "" ? config('constants.empty') : Str::limit(@$list->title, 19, '...'); ?></a></h4>
						<p><?php echo @$list->description == "" ? config('constants.empty') : Str::limit(@$list->description, 15, '...'); ?></p>
						</div>
						<div class="extra_content">
							<div class="left">
								<div class="author">
									<a href="#"><?php echo substr($admin->first_name, 0, 1); ?></a>
								</div>
								<div class="note_modify">
									<small>Last Modified <span><?php echo date('Y-m-d', strtotime($list->updated_at)); ?></span></small>
								</div>
							</div>
							<div class="right">

							</div>
						</div>
					</div>
				<?php } ?>
				</div>
				<div class="clearfix"></div>
			<?php
			echo ob_get_clean();

	}

	public function applicationsendmail(Request $request){
		$requestData = $request->all();
		//echo '<pre>'; print_r($requestData); die;
		$user_id = @Auth::user()->id;
		$subject = $requestData['subject'];
		$message = $requestData['message'];
		$to = $requestData['to'];

	$client = \App\Models\Admin::Where('email', $requestData['to'])->first();
			$subject = str_replace('{Client First Name}',$client->first_name, $subject);
			$message = str_replace('{Client First Name}',$client->first_name, $message);
			$message = str_replace('{Client Assignee Name}',$client->first_name, $message);
			$message = str_replace('{Company Name}',Auth::user()->company_name, $message);
			$array = array();
			$ccarray = array();
			if(isset($requestData['email_cc']) && !empty($requestData['email_cc'])){
				foreach($requestData['email_cc'] as $cc){
					$clientcc = \App\Models\Admin::Where('id', $cc)->first();
					$ccarray[] = $clientcc;
				}
			}
				$sent = $this->send_compose_template($message, 'digitrex', $to, $subject, 'support@digitrex.live', $array, @$ccarray);
			if($sent){
				$objs = new \App\Models\ApplicationActivitiesLog;
				$objs->stage = $request->type;
				$objs->type = 'appointment';
				$objs->comment = 'sent an email';
				$objs->title = '<b>Subject : '.$subject.'</b>';
				$objs->description = '<b>To: '.$to.'</b></br>'.$message;
				$objs->app_id = $request->noteid;
				$objs->user_id = Auth::user()->id;
				$saved = $objs->save();
				$response['status'] 	= 	true;
				$response['message']	=	'Email Sent Successfully';
			}else{
				$response['status'] 	= 	true;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}

	public function updateintake(Request $request){
		$requestData = $request->all();
		//echo '<pre>'; print_r($requestData); die;
		$user_id = @Auth::user()->id;
		$obj = Application::find($request->appid);
		$obj->intakedate = $request->from;
		$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Applied date successfully updated.';
			}else{
				$response['status'] 	= 	true;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}

	public function updateexpectwin(Request $request){
		$requestData = $request->all();
		//echo '<pre>'; print_r($requestData); die;
		$user_id = @Auth::user()->id;
		$obj = Application::find($request->appid);
		$obj->expect_win_date = $request->from;
		$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Date successfully updated.';
			}else{
				$response['status'] 	= 	true;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}

	public function updatedates(Request $request){
		$requestData = $request->all();
		//echo '<pre>'; print_r($requestData); die;
		$user_id = @Auth::user()->id;
		$obj = Application::find($request->appid);
		if($request->datetype == 'start'){
			$obj->start_date = $request->from;
		}else{
			$obj->end_date = $request->from;
		}
		$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Date successfully updated.';
				if($request->datetype == 'start'){
					$response['dates']	=	array(
						'date' => date('d',strtotime($obj->start_date)),
						'month' => date('M',strtotime($obj->start_date)),
						'year' => date('Y',strtotime($obj->start_date)),
					);
				}else{
					$response['dates']	=	array(
						'date' => date('d',strtotime($obj->end_date)),
						'month' => date('M',strtotime($obj->end_date)),
						'year' => date('Y',strtotime($obj->end_date)),
					);
				}

			}else{
				$response['status'] 	= 	true;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}

	public function discontinue_application(Request $request){
		$requestData = $request->all();
		//echo '<pre>'; print_r($requestData); die;
		$user_id = @Auth::user()->id;
		$obj = Application::find($request->diapp_id);
		$obj->status = 2;
		$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully discontinued.';
			}else{
				$response['status'] 	= 	true;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}

	public function revert_application(Request $request){
		$requestData = $request->all();

		//echo '<pre>'; print_r($requestData); die;
		$user_id = @Auth::user()->id;
		$obj = Application::find($request->revapp_id);
		$obj->status = 0;
		$workflowstagecount = \App\Models\WorkflowStage::where('w_id', $obj->workflow)->count();
			$widthcount = 0;
			if($workflowstagecount !== 0){
				$s = 100 / $workflowstagecount;
				$widthcount = round($s);
			}
		$progresswidth = $obj->progresswidth - $widthcount;
		$obj->progresswidth = $progresswidth;
		$saved = $obj->save();
			if($saved){
			$displayback = false;
				$workflowstage = \App\Models\WorkflowStage::where('w_id', $obj->workflow)->orderBy('id','desc')->first();

				if($workflowstage->name == $obj->stage){
					$displayback = true;
				}
				$response['status'] 	= 	true;
				$response['width'] 	= 	$progresswidth;
				$response['displaycomplete'] 	= 	$displayback;
				$response['message']	=	'Application successfully reverted.';
			}else{
				$response['status'] 	= 	true;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}

	public function spagent_application(Request $request){
		$requestData = $request->all();
		$flag = true;
		/* if(Application::where('super_agent',$request->super_agent)->exists()){
			$flag = false;
			$response['message']	=	'Agent is already exists';
		}
		if(Application::where('sub_agent',$request->super_agent)->exists()){
			$flag = false;
			$response['message']	=	'Agent is already exists in sub admin';
		} */
		if($flag){
			$user_id = @Auth::user()->id;
			$obj = Application::find($request->siapp_id);
			$obj->super_agent = $request->super_agent;
			$saved = $obj->save();
			if($saved){
				$agent = \App\Models\AgentDetails::where('id',$request->super_agent)->first();
				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully updated.';
				$response['data']	=	'<div class="client_info">
							<div class="cl_logo" style="display: inline-block;width: 30px;height: 30px; border-radius: 50%;background: #6777ef;text-align: center;color: #fff;font-size: 14px; line-height: 30px; vertical-align: top;">'.substr($agent->full_name, 0, 1).'</div>
							<div class="cl_name" style="display: inline-block;margin-left: 5px;width: calc(100% - 60px);">
								<span class="name">'.$agent->full_name.'</span>
								<span class="ui label zippyLabel alignMiddle yellow">
							  '.$agent->struture.'
							</span>
							</div>
							<div class="cl_del" style="display: inline-block;">
								<a href=""><i class="fa fa-times"></i></a>
							</div>
						</div>';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
		}

		echo json_encode($response);
	}

	public function sbagent_application(Request $request){
		$requestData = $request->all();
		$flag = true;
		/* if(Application::where('super_agent',$request->sub_agent)->exists()){
			$flag = false;
			$response['message']	=	'Agent is already exists in super admin';
		}
		if(Application::where('sub_agent',$request->sub_agent)->exists()){
			$flag = false;
			$response['message']	=	'Agent is already exists';
		} */
		if($flag){
			$user_id = @Auth::user()->id;
			$obj = Application::find($request->sbapp_id);
			$obj->sub_agent = $request->sub_agent;
			$saved = $obj->save();
			if($saved){
				$agent = \App\Models\AgentDetails::where('id',$request->sub_agent)->first();
				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully updated.';
				$response['data']	=	'<div class="client_info">
							<div class="cl_logo" style="display: inline-block;width: 30px;height: 30px; border-radius: 50%;background: #6777ef;text-align: center;color: #fff;font-size: 14px; line-height: 30px; vertical-align: top;">'.substr($agent->full_name, 0, 1).'</div>
							<div class="cl_name" style="display: inline-block;margin-left: 5px;width: calc(100% - 60px);">
								<span class="name">'.$agent->full_name.'</span>
								<span class="ui label zippyLabel alignMiddle yellow">
							  '.$agent->struture.'
							</span>
							</div>
							<div class="cl_del" style="display: inline-block;">
								<a href=""><i class="fa fa-times"></i></a>
							</div>
						</div>';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
		}

		echo json_encode($response);
	}

	public function superagent(Request $request){
		$requestData = $request->all();

			$user_id = @Auth::user()->id;
			$obj = Application::find($request->note_id);
			$obj->super_agent = '';
			$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully updated.';

			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}

	public function subagent(Request $request){
		$requestData = $request->all();

			$user_id = @Auth::user()->id;
			$obj = Application::find($request->note_id);
			$obj->sub_agent = '';
			$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully updated.';

			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}

	public function application_ownership(Request $request){
		$requestData = $request->all();

			$user_id = @Auth::user()->id;
			$obj = Application::find($request->mapp_id);
			$obj->ratio = $request->ratio;
			$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully updated.';
				$response['ratio']	=	$obj->ratio;

			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}

	// Removed legacy method: saleforcast

	public function getapplicationbycid(Request $request){
		$clientid = $request->clientid;
		//echo '<pre>'; print_r($requestData); die;
		$applications = Application::where('client_id', $clientid)->orderby('created_at', 'DESC')->get();
		ob_start();
		?>
		<option value="">Select Application</option>
		<?php
		foreach($applications as $application){

			$clientdetail = \App\Models\Admin::where('id', $application->client_id)->first();
			
			?>
			<option value="<?php echo $application->id; ?>"><?php echo @$productdetail->name.'('.@$partnerdetail->partner_name; ?> <?php echo @$PartnerBranch->name; ?>)</option>
			<?php
		}
		return ob_get_clean();
	}


	public function applicationsavefee(Request $request){
		$requestData = $request->all();
		if(ApplicationFeeOption::where('app_id', $request->id)->exists()){
			$o = ApplicationFeeOption::where('app_id', $request->id)->first();
			$obj = ApplicationFeeOption::find($o->id);
			$obj->user_id = Auth::user()->id;
			$obj->app_id = $request->id;
			$obj->name = $requestData['fee_option_name'];
			$obj->country = $requestData['country_residency'];
			$obj->installment_type = $requestData['degree_level'];
			$obj->discount_amount = $requestData['discount_amount'];
			$obj->discount_sem = $requestData['discount_sem'];
			$obj->total_discount = $requestData['total_discount'];
			$saved = $obj->save();
			if($saved){
				ApplicationFeeOptionType::where('fee_id', $obj->id)->delete();
				$course_fee_type = $requestData['course_fee_type'];
				$totl = 0;
				for($i = 0; $i< count($course_fee_type); $i++){
					$totl += $requestData['total_fee'][$i];
					$objs = new ApplicationFeeOptionType;
					$objs->fee_id = $obj->id;
					$objs->fee_type = $requestData['course_fee_type'][$i];
					$objs->inst_amt = $requestData['semester_amount'][$i];
					$objs->installment = $requestData['no_semester'][$i];
					$objs->total_fee = $requestData['total_fee'][$i];
					$objs->claim_term = $requestData['claimable_semester'][$i];
					$objs->commission = $requestData['commission'][$i];

					$saved = $objs->save();

				}
				$discount = 0.00;
				if(@$obj->total_discount != ''){
				$discount = @$obj->total_discount;
				}
				$response['status'] 	= 	true;
					$response['message']	=	'Fee Option added successfully';
					$response['totalfee']	=	$totl;
					$response['discount']	=	$discount;
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Record not found';
			}
		}else{
			$obj = new ApplicationFeeOption;
			$obj->user_id = Auth::user()->id;
			$obj->app_id = $request->id;
			$obj->name = $requestData['fee_option_name'];
			$obj->country = $requestData['country_residency'];
			$obj->installment_type = $requestData['degree_level'];
			$saved = $obj->save();
			if($saved){
				$course_fee_type = $requestData['course_fee_type'];
				$totl = 0;
				for($i = 0; $i< count($course_fee_type); $i++){
					$totl += $requestData['total_fee'][$i];
					$objs = new ApplicationFeeOptionType;
					$objs->fee_id = $obj->id;
					$objs->fee_type = $requestData['course_fee_type'][$i];
					$objs->inst_amt = $requestData['semester_amount'][$i];
					$objs->installment = $requestData['no_semester'][$i];
					$objs->total_fee = $requestData['total_fee'][$i];
					$objs->claim_term = $requestData['claimable_semester'][$i];
					$objs->commission = $requestData['commission'][$i];

					$saved = $objs->save();

				}
				$discount = 0.00;
				if(@$obj->total_discount != ''){
				$discount = @$obj->total_discount;
				}
				$response['status'] 	= 	true;
					$response['message']	=	'Fee Option added successfully';
					$response['totalfee']	=	$totl;
					$response['discount']	=	$discount;
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Record not found';
			}
		}
		echo json_encode($response);
	}

	public function exportapplicationpdf(Request $request, $id){
		$applications = \App\Models\Application::where('id', $id)->first();

		$cleintname = \App\Models\Admin::where('role',7)->where('id',@$applications->client_id)->first();
		
		$pdf = PDF::setOptions([
			'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
			'logOutputFile' => storage_path('logs/log.htm'),
			'tempDir' => storage_path('logs/')
			])->loadView('emails.application',compact(['cleintname','applications','productdetail','PartnerBranch','partnerdetail']));
			//
			return $pdf->stream('application.pdf');
	}

	public function addchecklists(Request $request){
		// Validate required fields
		$request->validate([
			'document_type' => 'required|string|max:255',
			'client_id' => 'required|integer',
			'app_id' => 'required|integer',
			'type' => 'required|string',
			'typename' => 'required|string'
		], [
			'document_type.required' => 'Checklist Name is required.',
			'document_type.string' => 'Checklist Name must be a valid text.',
			'document_type.max' => 'Checklist Name cannot exceed 255 characters.',
			'client_id.required' => 'Client ID is required.',
			'app_id.required' => 'Application ID is required.',
			'type.required' => 'Type is required.',
			'typename.required' => 'Type name is required.'
		]);
		
		$requestData = $request->all();
		$client_id = $requestData['client_id'];
		$app_id = $requestData['app_id'];
		$type = $requestData['type'];
		$typename = $requestData['typename'];
		$document_type = trim($request->document_type);
		
		// Double check document_type is not empty after trimming
		if (empty($document_type)) {
			$response['status'] = false;
			$response['message'] = 'Checklist Name is required.';
			echo json_encode($response);
			return;
		}
		
		$obj = new \App\Models\ApplicationDocumentList;
		$obj->type = $type;
		$obj->typename = $typename;
		$obj->client_id = $client_id;
		$obj->application_id = $app_id;
		$obj->document_type = $document_type;
		$obj->description = $request->description ?? null;
		$obj->allow_client = $request->allow_upload_docu ?? 0;
		$obj->make_mandatory = $request->proceed_next_stage ?? null;
		if(isset($requestData['due_date']) && $requestData['due_date'] == 1){
			$obj->date = $request->appoint_date ?? null;
			$obj->time = $request->appoint_time ?? null;
		}
		$obj->user_id = Auth::user()->id;

		$saved = $obj->save();
		if($saved){
			$applicationdocuments = \App\Models\ApplicationDocumentList::where('application_id', $app_id)->where('client_id', $client_id)->where('type', $type)->get();
			$checklistdata = '<table class="table"><tbody>';
			foreach($applicationdocuments as $applicationdocument){
				$appcount = \App\Models\ApplicationDocument::where('list_id', $applicationdocument->id)->count();
				$checklistdata .= '<tr>';
				if($appcount >0){
					$checklistdata .= '<td><span class="check"><i class="fa fa-check"></i></span></td>';
				}else{
					$checklistdata .= '<td><span class="round"></span></td>';
				}

					$checklistdata .= '<td>'.@$applicationdocument->document_type.'</td>';
					$checklistdata .= '<td><div class="circular-box cursor-pointer"><button class="transparent-button paddingNone">'.$appcount.'</button></div></td>';
					$checklistdata .= '<td><a data-aid="'.$app_id.'" data-type="'.$type.'" data-typename="'.$typename.'" data-id="'.$applicationdocument->id.'" class="openfileupload" href="javascript:;"><i class="fa fa-plus"></i></a></td>';
				$checklistdata .= '</tr>';
			}
			$checklistdata .= '</tbody></table>';
			$response['status'] 	= 	true;
			$response['message']	=	'CHecklist added successfully';
			$response['data']	=	$checklistdata;
			$countchecklist = \App\Models\ApplicationDocumentList::where('application_id', $app_id)->count();
			$response['countchecklist']	=	$countchecklist;
		}else{
			$response['status'] 	= 	false;
				$response['message']	=	'Record not found';
		}
		echo json_encode($response);
	}

	public function checklistupload(Request $request){
		 $imageData = '';
		if (isset($_FILES['file']['name'][0])) {
		  foreach ($_FILES['file']['name'] as $keys => $values) {
			$fileName = $_FILES['file']['name'][$keys];
			if (move_uploaded_file($_FILES['file']['tmp_name'][$keys], config('constants.documents').'/'. $fileName)) {
				$obj = new \App\Models\ApplicationDocument;
				$obj->type = $request->type;
				$obj->typename = $request->typename;
				$obj->list_id = $request->id;
				$obj->file_name = $fileName;
				$obj->user_id = Auth::user()->id;
				$obj->application_id = $request->application_id;
				$save = $obj->save();
			  $imageData .= '<li><i class="fa fa-file"></i> '.$fileName.'</li>';
			}
		  }
		}

		$doclists = \App\Models\ApplicationDocument::where('application_id',$request->application_id)->orderby('created_at','DESC')->get();
		$doclistdata = '';
		foreach($doclists as $doclist){
			$docdata = \App\Models\ApplicationDocumentList::where('id', $doclist->list_id)->first();
			$doclistdata .= '<tr id="">';
				$doclistdata .= '<td><i class="fa fa-file"></i> '. $doclist->file_name.'<br>'.@$docdata->document_type.'</td>';
				$doclistdata .= '<td>';
					$doclistdata .=  $doclist->typename;
				$doclistdata .= '</td>';
				$admin = \App\Models\Admin::where('id', @$doclist->user_id)->first();

			$doclistdata .= '<td><span style="    position: relative;background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;">'.substr(@$admin->first_name, 0, 1).'</span>'.@$admin->first_name.'</td>';
			$doclistdata .= '<td>'.date('Y-m-d',strtotime($doclist->created_at)).'</td>';
			$doclistdata .= '<td>';
			if($doclist->status == 1){
				$doclistdata .= '<span class="check"><i class="fa fa-eye"></i></span>';
			}
				$doclistdata .= '<div class="dropdown d-inline">
					<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
					<div class="dropdown-menu">
						<a target="_blank" class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Preview</a>
						<a data-id="'.$doclist->id.'" class="dropdown-item deletenote" data-href="deleteapplicationdocs" href="javascript:;">Delete</a>
						<a download class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Download</a>';
						if($doclist->status == 0){
							$doclistdata .= '<a data-id="'.$doclist->id.'" class="dropdown-item publishdoc" href="javascript:;">Publish Document</a>';
						}else{
							$doclistdata .= '<a data-id="'.$doclist->id.'"  class="dropdown-item unpublishdoc" href="javascript:;">Unpublish Document</a>';
						}

					$doclistdata .= '</div>
				</div>
			</td>';
			$doclistdata .= '</tr>';
		}
		$application_id = $request->application_id;
		$applicationuploadcount = DB::select("SELECT COUNT(DISTINCT list_id) AS cnt FROM application_documents where application_id = '$application_id'");
		$response['status'] 	= 	true;
		$response['imagedata']	=	$imageData;
		$response['doclistdata']	=	$doclistdata;
		$response['applicationuploadcount']	=	@$applicationuploadcount[0]->cnt;

		$applicationdocuments = \App\Models\ApplicationDocumentList::where('application_id', $application_id)->where('type', $request->type)->get();
			$checklistdata = '<table class="table"><tbody>';
			foreach($applicationdocuments as $applicationdocument){
				$appcount = \App\Models\ApplicationDocument::where('list_id', $applicationdocument->id)->count();
				$checklistdata .= '<tr>';
				if($appcount >0){
					$checklistdata .= '<td><span class="check"><i class="fa fa-check"></i></span></td>';
				}else{
					$checklistdata .= '<td><span class="round"></span></td>';
				}

					$checklistdata .= '<td>'.@$applicationdocument->document_type.'</td>';
					$checklistdata .= '<td><div class="circular-box cursor-pointer"><button class="transparent-button paddingNone">'.$appcount.'</button></div></td>';
					$checklistdata .= '<td><a data-aid="'.$application_id.'" data-type="'.$request->type.'" data-id="'.$applicationdocument->id.'" class="openfileupload" href="javascript:;"><i class="fa fa-plus"></i></a></td>';
				$checklistdata .= '</tr>';
			}
			$checklistdata .= '</tbody></table>';
		$response['checklistdata']	=	$checklistdata;
		$response['type']	=	$request->type;
		echo json_encode($response);
	}

	public function deleteapplicationdocs(Request $request){
		// Check if we're deleting by list_id (new method) or by id (old method for backward compatibility)
		if($request->has('list_id') && $request->list_id){
			// Delete all documents with the same list_id
			$listId = $request->list_id;
			
			// Get first document to get application_id for response
			$appdoc = \App\Models\ApplicationDocument::where('list_id', $listId)->first();
			
			if($appdoc){
				// Delete all documents with this list_id
				$res = \App\Models\ApplicationDocument::where('list_id', $listId)->delete();
				
				if($res){
				$response['status'] 	= 	true;
				$response['message'] 	= 	'Record removed successfully';

				$doclists = \App\Models\ApplicationDocument::where('application_id',$appdoc->application_id)->orderby('created_at','DESC')->get();
		$doclistdata = '';
		foreach($doclists as $doclist){
			$docdata = \App\Models\ApplicationDocumentList::where('id', $doclist->list_id)->first();
			$doclistdata .= '<tr id="">';
				$doclistdata .= '<td><i class="fa fa-file"></i> '. $doclist->file_name.'<br>'.@$docdata->document_type.'</td>';
				$doclistdata .= '<td>';
				if($doclist->type == 'application'){ $doclistdata .= 'Application'; }else if($doclist->type == 'acceptance'){ $doclistdata .=  'Acceptance'; }else if($doclist->type == 'payment'){ $doclistdata .=  'Payment'; }else if($doclist->type == 'formi20'){ $doclistdata .=  'Form I 20'; }else if($doclist->type == 'visaapplication'){ $doclistdata .=  'Visa Application'; }else if($doclist->type == 'interview'){ $doclistdata .=  'Interview'; }else if($doclist->type == 'enrolment'){ $doclistdata .=  'Enrolment'; }else if($doclist->type == 'courseongoing'){ $doclistdata .=  'Course Ongoing'; }
				$doclistdata .= '</td>';
				$admin = \App\Models\Admin::where('id', $doclist->user_id)->first();

			$doclistdata .= '<td><span style="    position: relative;background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;">'.substr($admin->first_name, 0, 1).'</span>'.$admin->first_name.'</td>';
			$doclistdata .= '<td>'.date('Y-m-d',strtotime($doclist->created_at)).'</td>';
			$doclistdata .= '<td>';
			if($doclist->status == 1){
				$doclistdata .= '<span class="check"><i class="fa fa-eye"></i></span>';
			}
				$doclistdata .= '<div class="dropdown d-inline">
					<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
					<div class="dropdown-menu">
						<a target="_blank" class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Preview</a>
						<a data-id="'.$doclist->id.'" class="dropdown-item deletenote" data-href="deleteapplicationdocs" href="javascript:;">Delete</a>
						<a download class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Download</a>';
						if($doclist->status == 0){
							$doclistdata .= '<a data-id="'.$doclist->id.'" class="dropdown-item publishdoc" href="javascript:;">Publish Document</a>';
						}else{
							$doclistdata .= '<a data-id="'.$doclist->id.'"  class="dropdown-item unpublishdoc" href="javascript:;">Unpublish Document</a>';
						}

					$doclistdata .= '</div>
				</div>
			</td>';
			$doclistdata .= '</tr>';
		}
		$application_id = $appdoc->application_id;
		$applicationuploadcount = DB::select("SELECT COUNT(DISTINCT list_id) AS cnt FROM application_documents where application_id = '$application_id'");
		$response['status'] 	= 	true;

		$response['doclistdata']	=	$doclistdata;
		$response['applicationuploadcount']	=	@$applicationuploadcount[0]->cnt;

		$applicationdocuments = \App\Models\ApplicationDocumentList::where('application_id', $application_id)->where('type', $appdoc->type)->get();
			$checklistdata = '<table class="table"><tbody>';
			foreach($applicationdocuments as $applicationdocument){
				$appcount = \App\Models\ApplicationDocument::where('list_id', $applicationdocument->id)->count();
				$checklistdata .= '<tr>';
				if($appcount >0){
					$checklistdata .= '<td><span class="check"><i class="fa fa-check"></i></span></td>';
				}else{
					$checklistdata .= '<td><span class="round"></span></td>';
				}

					$checklistdata .= '<td>'.@$applicationdocument->document_type.'</td>';
					$checklistdata .= '<td><div class="circular-box cursor-pointer"><button class="transparent-button paddingNone">'.$appcount.'</button></div></td>';
					$checklistdata .= '<td><a data-aid="'.$application_id.'" data-type="'.$appdoc->type.'"data-typename="'.$appdoc->typename.'" data-id="'.$applicationdocument->id.'" class="openfileupload" href="javascript:;"><i class="fa fa-plus"></i></a></td>';
				$checklistdata .= '</tr>';
			}
			$checklistdata .= '</tbody></table>';
		$response['checklistdata']	=	$checklistdata;
		$response['type']	=	$appdoc->type;
			}else{
				$response['status'] 	= 	false;
				$response['message'] 	= 	'Please try again';
			}
			}else{
				$response['status'] 	= 	false;
				$response['message'] 	= 	'No Record found with this list_id';
			}
			echo json_encode($response);
			return;
		}
		
		// Backward compatibility: Delete by document id (old method)
		if(\App\Models\ApplicationDocument::where('id', $request->note_id)->exists()){
			$appdoc = \App\Models\ApplicationDocument::where('id', $request->note_id)->first();
			$res = \App\Models\ApplicationDocument::where('id', $request->note_id)->delete();
			if($res){
				$response['status'] 	= 	true;
				$response['message'] 	= 	'Record removed successfully';

				$doclists = \App\Models\ApplicationDocument::where('application_id',$appdoc->application_id)->orderby('created_at','DESC')->get();
		$doclistdata = '';
		foreach($doclists as $doclist){
			$docdata = \App\Models\ApplicationDocumentList::where('id', $doclist->list_id)->first();
			$doclistdata .= '<tr id="">';
				$doclistdata .= '<td><i class="fa fa-file"></i> '. $doclist->file_name.'<br>'.@$docdata->document_type.'</td>';
				$doclistdata .= '<td>';
				if($doclist->type == 'application'){ $doclistdata .= 'Application'; }else if($doclist->type == 'acceptance'){ $doclistdata .=  'Acceptance'; }else if($doclist->type == 'payment'){ $doclistdata .=  'Payment'; }else if($doclist->type == 'formi20'){ $doclistdata .=  'Form I 20'; }else if($doclist->type == 'visaapplication'){ $doclistdata .=  'Visa Application'; }else if($doclist->type == 'interview'){ $doclistdata .=  'Interview'; }else if($doclist->type == 'enrolment'){ $doclistdata .=  'Enrolment'; }else if($doclist->type == 'courseongoing'){ $doclistdata .=  'Course Ongoing'; }
				$doclistdata .= '</td>';
				$admin = \App\Models\Admin::where('id', $doclist->user_id)->first();

			$doclistdata .= '<td><span style="    position: relative;background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;">'.substr($admin->first_name, 0, 1).'</span>'.$admin->first_name.'</td>';
			$doclistdata .= '<td>'.date('Y-m-d',strtotime($doclist->created_at)).'</td>';
			$doclistdata .= '<td>';
			if($doclist->status == 1){
				$doclistdata .= '<span class="check"><i class="fa fa-eye"></i></span>';
			}
				$doclistdata .= '<div class="dropdown d-inline">
					<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
					<div class="dropdown-menu">
						<a target="_blank" class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Preview</a>
						<a data-id="'.$doclist->id.'" class="dropdown-item deletenote" data-href="deleteapplicationdocs" href="javascript:;">Delete</a>
						<a download class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Download</a>';
						if($doclist->status == 0){
							$doclistdata .= '<a data-id="'.$doclist->id.'" class="dropdown-item publishdoc" href="javascript:;">Publish Document</a>';
						}else{
							$doclistdata .= '<a data-id="'.$doclist->id.'"  class="dropdown-item unpublishdoc" href="javascript:;">Unpublish Document</a>';
						}

					$doclistdata .= '</div>
				</div>
			</td>';
			$doclistdata .= '</tr>';
		}

		$response['status'] 	= 	true;

		$response['doclistdata']	=	$doclistdata;

			}else{
				$response['status'] 	= 	false;
				$response['message'] 	= 	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message'] 	= 	'No Record found';
		}
		echo json_encode($response);
	}

	public function publishdoc(Request $request){
		if(\App\Models\ApplicationDocument::where('id', $request->appid)->exists()){
			$appdoc = \App\Models\ApplicationDocument::where('id', $request->appid)->first();
			$obj = \App\Models\ApplicationDocument::find($request->appid);
			$obj->status = $request->status;
			$saved = $obj->save();
			if($saved){
				$response['status'] 	= 	true;
				$response['message'] 	= 	'Record updated successfully';
				$doclists = \App\Models\ApplicationDocument::where('application_id',$appdoc->application_id)->orderby('created_at','DESC')->get();
		$doclistdata = '';
		foreach($doclists as $doclist){
			$docdata = \App\Models\ApplicationDocumentList::where('id', $doclist->list_id)->first();
			$doclistdata .= '<tr id="">';
				$doclistdata .= '<td><i class="fa fa-file"></i> '. $doclist->file_name.'<br>'.@$docdata->document_type.'</td>';
				$doclistdata .= '<td>';
				if($doclist->type == 'application'){ $doclistdata .= 'Application'; }else if($doclist->type == 'acceptance'){ $doclistdata .=  'Acceptance'; }else if($doclist->type == 'payment'){ $doclistdata .=  'Payment'; }else if($doclist->type == 'formi20'){ $doclistdata .=  'Form I 20'; }else if($doclist->type == 'visaapplication'){ $doclistdata .=  'Visa Application'; }else if($doclist->type == 'interview'){ $doclistdata .=  'Interview'; }else if($doclist->type == 'enrolment'){ $doclistdata .=  'Enrolment'; }else if($doclist->type == 'courseongoing'){ $doclistdata .=  'Course Ongoing'; }
				$doclistdata .= '</td>';
				$admin = \App\Models\Admin::where('id', $doclist->user_id)->first();

			$doclistdata .= '<td><span style="    position: relative;background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;">'.substr($admin->first_name, 0, 1).'</span>'.$admin->first_name.'</td>';
			$doclistdata .= '<td>'.date('Y-m-d',strtotime($doclist->created_at)).'</td>';
			$doclistdata .= '<td>';
			if($doclist->status == 1){
				$doclistdata .= '<span class="check"><i class="fa fa-eye"></i></span>';
			}
				$doclistdata .= '<div class="dropdown d-inline">
					<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
					<div class="dropdown-menu">
						<a target="_blank" class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Preview</a>
						<a data-id="'.$doclist->id.'" class="dropdown-item deletenote" data-href="deleteapplicationdocs" href="javascript:;">Delete</a>
						<a download class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Download</a>';
						if($doclist->status == 0){
							$doclistdata .= '<a data-id="'.$doclist->id.'" class="dropdown-item publishdoc" href="javascript:;">Publish Document</a>';
						}else{
							$doclistdata .= '<a data-id="'.$doclist->id.'"  class="dropdown-item unpublishdoc" href="javascript:;">Unpublish Document</a>';
						}

					$doclistdata .= '</div>
				</div>
			</td>';
			$doclistdata .= '</tr>';
		}

		$response['status'] 	= 	true;

		$response['doclistdata']	=	$doclistdata;

			}else{
				$response['status'] 	= 	false;
				$response['message'] 	= 	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message'] 	= 	'No Record found';
		}
		echo json_encode($response);
	}

	public function getapplications(Request $request){
		$client_id = $request->client_id;
		$applications = Application::where('client_id', '=', $client_id)->get();
		ob_start();
		?>
		<option value="">Choose Application</option>
		<?php
		foreach($applications as $application){
			
			// Partner functionality removed
			?>
		<option value="<?php echo $application->id; ?>">(#<?php echo $application->id; ?>) Application (Partner)</option>
			<?php
		}
		return ob_get_clean();
	}

	public function migrationindex(Request $request)
	{
		//check authorization start

			/* if($check)
			{
				return Redirect::to('/dashboard')->with('error',config('constants.unauthorized'));
			} */
		//check authorization end
	    $allstages = Application::select('stage')->where('workflow', '=', 5)->groupBy('stage')->get();
		$query 		= Application::where('workflow', 5)->with(['application_assignee']);

		$totalData 	= $query->count();	//for all data
        if ($request->has('partner'))
		{
			$partner 		= 	$request->input('partner');
			if(trim($partner) != '')
			{
				$query->where('partner_id', '=', $partner);
			}
		}
		if ($request->has('assignee'))
		{
			$assignee 		= 	$request->input('assignee');
			if(trim($assignee) != '')
			{
				$query->where('user_id', '=', $assignee);
			}
		}
		 if ($request->has('stage'))
		{
			$stage 		= 	$request->input('stage');
			if(trim($stage) != '')
			{
				$query->where('stage', '=', $stage);
			}
		}
		$lists		= $query->sortable(['id' => 'desc'])->paginate(10);

		return view('crm.applications.migrationindex', compact(['lists', 'totalData','allstages']));

		//return view('crm.applications.index');
	}

	public function import(Request $request){
		$the_file = $request->file('uploaded_file');
		try{
			$spreadsheet = IOFactory::load($the_file->getRealPath());
			$sheet        = $spreadsheet->getActiveSheet();
			$row_limit    = $sheet->getHighestDataRow();
			$column_limit = $sheet->getHighestDataColumn();
			$row_range    = range( 2, $row_limit );
			$column_range = range( 'Z', $column_limit );
			$startcount = 2;
			$data = array();

			foreach ( $row_range as $row ) {
				$data[] = [
											   'user_id'=>$sheet->getCell( 'B' . $row )->getValue(),
											   'workflow'=>$sheet->getCell( 'C' . $row )->getValue(),
											   'partner_id'=>$sheet->getCell( 'D' . $row )->getValue(),
											   'product_id'=>$sheet->getCell( 'E' . $row )->getValue(),
											   'status'=>$sheet->getCell( 'F' . $row )->getValue(),
											   'stage'=>$sheet->getCell( 'G' . $row )->getValue(),
											   'sale_forcast'=>$sheet->getCell( 'H' . $row )->getValue(),
											   'created_at'=>$sheet->getCell( 'I' . $row )->getValue(),
											   'updated_at'=>$sheet->getCell( 'J' . $row )->getValue(),
											   'client_id'=>$sheet->getCell( 'K' . $row )->getValue(),
											   'branch'=>$sheet->getCell( 'L' . $row )->getValue(),
											   'intakedate'=>$sheet->getCell( 'M' . $row )->getValue(),
											   'start_date'=>$sheet->getCell( 'N' . $row )->getValue(),
											   'end_date'=>$sheet->getCell( 'O' . $row )->getValue(),
											   'expect_win_date'=>$sheet->getCell( 'P' . $row )->getValue(),
											   'super_agent'=>$sheet->getCell( 'Q' . $row )->getValue(),
											   'sub_agent'=>$sheet->getCell( 'R' . $row )->getValue(),
											   'ratio'=>$sheet->getCell( 'S' . $row )->getValue(),
											   'client_revenue'=>$sheet->getCell( 'T' . $row )->getValue(),
											   'partner_revenue'=>$sheet->getCell( 'U' . $row )->getValue(),
											   'discounts'=>$sheet->getCell( 'V' . $row )->getValue(),
											   'progresswidth'=>$sheet->getCell( 'W' . $row )->getValue()
				];
				$startcount++;
			}
			DB::connection()->getPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
			DB::table('check_applications')->insert($data);
			DB::connection()->getPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
		} catch (Exception $e) {
			$error_code = $e->errorInfo[1];
			return back()->withErrors('There was a problem uploading the data!');
		}
		return back()->withSuccess('Great! Data has been successfully uploaded.');
	}

	public function approveDocument(Request $request){
		$response = ['status' => false, 'message' => 'Error approving document.'];
		
		try {
			$documentId = $request->input('document_id');
			
			if (!$documentId) {
				$response['message'] = 'Document ID is required.';
				return response()->json($response);
			}
			
			// Update document status to 1 (Approved)
			$updated = DB::table('application_documents')
				->where('id', $documentId)
				->update([
					'status' => 1,
					'updated_at' => now()
				]);
			
			if ($updated) {
				$response['status'] = true;
				$response['message'] = 'Document approved successfully!';
			} else {
				$response['message'] = 'Document not found or could not be updated.';
			}
		} catch (\Exception $e) {
			$response['message'] = 'An error occurred: ' . $e->getMessage();
		}
		
		return response()->json($response);
	}

	public function rejectDocument(Request $request){
		$response = ['status' => false, 'message' => 'Error rejecting document.'];
		
		try {
			$documentId = $request->input('document_id');
			$rejectReason = $request->input('reject_reason');
			
			if (!$documentId) {
				$response['message'] = 'Document ID is required.';
				return response()->json($response);
			}
			
			if (!$rejectReason || trim($rejectReason) === '') {
				$response['message'] = 'Rejection reason is required.';
				return response()->json($response);
			}
			
			// Update data with status and rejection reason
			$updateData = [
				'status' => 2,
				'updated_at' => now()
			];
			
			// Update doc_rejection_reason if column exists
			if (Schema::hasColumn('application_documents', 'doc_rejection_reason')) {
				$updateData['doc_rejection_reason'] = trim($rejectReason);
			} elseif (Schema::hasColumn('application_documents', 'reject_reason')) {
				// Fallback to reject_reason if doc_rejection_reason doesn't exist
				$updateData['reject_reason'] = trim($rejectReason);
			}
			
			// Update document status to 2 (Rejected)
			$updated = DB::table('application_documents')
				->where('id', $documentId)
				->update($updateData);
			
			if ($updated) {
				$response['status'] = true;
				$response['message'] = 'Document rejected successfully!';
			} else {
				$response['message'] = 'Document not found or could not be updated.';
			}
		} catch (\Exception $e) {
			$response['message'] = 'An error occurred: ' . $e->getMessage();
		}
		
		return response()->json($response);
	}

	public function downloadDocument(Request $request){
		$response = ['status' => false, 'message' => 'Error downloading document.'];
		
		try {
			$documentId = $request->input('document_id');
			
			if (!$documentId) {
				$response['message'] = 'Document ID is required.';
				return response()->json($response);
			}
			
			// Get document from database
			$document = DB::table('application_documents')
				->where('id', $documentId)
				->first();
			
			if (!$document || !$document->myfile) {
				$response['message'] = 'Document not found.';
				return response()->json($response);
			}
			
			$fileUrl = $document->myfile;
			$fileName = $document->file_name ?: 'document.pdf';
			
			// Fetch file from S3/URL
			$fileContent = @file_get_contents($fileUrl);
			
			if ($fileContent === false) {
				// Try using cURL if file_get_contents fails
				$ch = curl_init($fileUrl);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				$fileContent = curl_exec($ch);
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
				
				if ($httpCode !== 200 || $fileContent === false) {
					$response['message'] = 'Failed to fetch file from URL.';
					return response()->json($response);
				}
			}
			
			// Determine content type based on file extension or file URL
			$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
			
			// If extension is empty, try to get it from URL
			if (empty($extension)) {
				$urlExtension = strtolower(pathinfo(parse_url($fileUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
				if (!empty($urlExtension)) {
					$extension = $urlExtension;
				}
			}
			
			// Default to PDF if extension still empty
			if (empty($extension)) {
				$extension = 'pdf';
			}
			
			$contentType = 'application/octet-stream';
			
			if ($extension === 'pdf') {
				$contentType = 'application/pdf';
			} elseif (in_array($extension, ['jpg', 'jpeg'])) {
				$contentType = 'image/jpeg';
			} elseif ($extension === 'png') {
				$contentType = 'image/png';
			} elseif ($extension === 'doc') {
				$contentType = 'application/msword';
			} elseif ($extension === 'docx') {
				$contentType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
			}
			
			// Ensure filename has proper extension
			if (empty(pathinfo($fileName, PATHINFO_EXTENSION))) {
				$fileName .= '.' . $extension;
			}
			
			// Ensure filename is properly encoded
			$encodedFileName = rawurlencode($fileName);
			
			// Return file as download with proper headers to force download
			return response($fileContent, 200)
				->header('Content-Type', $contentType)
				->header('Content-Disposition', 'attachment; filename="' . addslashes($fileName) . '"; filename*=UTF-8\'\'' . $encodedFileName)
				->header('Content-Length', strlen($fileContent))
				->header('Cache-Control', 'no-cache, no-store, must-revalidate')
				->header('Pragma', 'no-cache')
				->header('Expires', '0')
				->header('X-Content-Type-Options', 'nosniff');
				
		} catch (\Exception $e) {
			$response['message'] = 'An error occurred: ' . $e->getMessage();
			return response()->json($response);
		}
	}

	/**
     * Get Messages for a Client Matter
     * GET /clients/matter-messages
     * 
     * Retrieves all messages for a specific client matter for admin view
     * Used in the client portal application tab
     */
	public function getMatterMessages(Request $request)
	{
		try {
			$request->validate([
				'client_matter_id' => 'required|integer|min:1'
			]);

			$clientMatterId = $request->input('client_matter_id');
			$currentUserId = Auth::guard('admin')->id();

			if (!$currentUserId) {
				return response()->json([
					'success' => false,
					'message' => 'Unauthorized'
				], 401);
			}

			// Get all messages for this client matter, ordered by created_at ascending (oldest first)
			$messages = DB::table('messages')
				->where('client_matter_id', $clientMatterId)
				->orderBy('created_at', 'asc')
				->orderBy('id', 'asc')
				->get()
				->map(function ($message) use ($currentUserId) {
					// Get sender info
					$sender = null;
					if ($message->sender_id) {
						$sender = DB::table('admins')
							->where('id', $message->sender_id)
							->select('id', 'first_name', 'last_name', DB::raw("(COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) as full_name"))
							->first();
					}

					// Get all recipients for this message
					$recipients = DB::table('message_recipients')
						->where('message_id', $message->id)
						->get()
						->map(function ($recipient) {
							$recipientUser = DB::table('admins')
								->where('id', $recipient->recipient_id)
								->select('id', 'first_name', 'last_name', DB::raw("(COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) as full_name"))
								->first();
							
							return [
								'recipient_id' => $recipient->recipient_id,
								'recipient_name' => $recipient->recipient,
								'is_read' => $recipient->is_read,
								'read_at' => $recipient->read_at,
								'user' => $recipientUser
							];
						});

					// Determine if message is from current user (sent) or to current user (received)
					$isSent = ($message->sender_id == $currentUserId);
					
					// Generate sender initials
					$senderInitials = '';
					if ($sender) {
						$firstInitial = $sender->first_name ? strtoupper(substr($sender->first_name, 0, 1)) : '';
						$lastInitial = $sender->last_name ? strtoupper(substr($sender->last_name, 0, 1)) : '';
						$senderInitials = $firstInitial . $lastInitial;
					}

					// Safely handle attachments property (may not exist in all database schemas)
					$attachments = null;
					if (property_exists($message, 'attachments') && $message->attachments) {
						$attachments = json_decode($message->attachments, true);
					}

					return [
						'id' => $message->id,
						'message' => $message->message,
						'sender_id' => $message->sender_id,
						'sender_name' => $message->sender,
						'sender' => $sender,
						'sender_initials' => $senderInitials,
						'sent_at' => $message->sent_at ? $message->sent_at : $message->created_at,
						'created_at' => $message->created_at,
						'client_matter_id' => $message->client_matter_id,
						'recipients' => $recipients,
						'is_sent' => $isSent,
						'attachments' => $attachments
					];
				});

			return response()->json([
				'success' => true,
				'data' => [
					'messages' => $messages->values(), // Ensure it's a proper array
					'total' => $messages->count()
				]
			], 200);

		} catch (\Illuminate\Validation\ValidationException $e) {
			return response()->json([
				'success' => false,
				'message' => 'Validation failed',
				'errors' => $e->errors()
			], 422);
		} catch (\Exception $e) {
			Log::error('Get Matter Messages Error: ' . $e->getMessage(), [
				'client_matter_id' => $request->input('client_matter_id'),
				'trace' => $e->getTraceAsString()
			]);

			return response()->json([
				'success' => false,
				'message' => 'Failed to fetch messages',
				'error' => $e->getMessage()
			], 500);
		}
	}

	/**
     * Send Message to Client (Web Route)
     * POST /clients/send-message
     * 
     * Sends a message to the client associated with the client matter
     * Uses session-based authentication for web admin users
     */
	public function sendMessageToClient(Request $request)
	{
		try {
			$request->validate([
				'message' => 'required|string|max:5000',
				'client_matter_id' => 'required|integer|min:1'
			]);

			$admin = Auth::guard('admin')->user();
			if (!$admin) {
				return response()->json([
					'success' => false,
					'message' => 'Unauthorized'
				], 401);
			}

			$senderId = $admin->id;
			$message = $request->input('message');
			$clientMatterId = $request->input('client_matter_id');

			// Get client matter info to find the client_id
			$clientMatter = DB::table('client_matters')
				->where('id', $clientMatterId)
				->first();

			if (!$clientMatter) {
				return response()->json([
					'success' => false,
					'message' => 'Client matter not found'
				], 404);
			}

			$clientId = $clientMatter->client_id;
			
			if (!$clientId) {
				return response()->json([
					'success' => false,
					'message' => 'No client associated with this matter'
				], 422);
			}

			// Get sender information
			$sender = DB::table('admins')
				->where('id', $senderId)
				->select('id', 'first_name', 'last_name', DB::raw("(COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) as full_name"))
				->first();

			$senderName = $sender ? $sender->full_name : 'Admin';
			$senderInitials = '';
			if ($sender) {
				$firstInitial = $sender->first_name ? strtoupper(substr($sender->first_name, 0, 1)) : '';
				$lastInitial = $sender->last_name ? strtoupper(substr($sender->last_name, 0, 1)) : '';
				$senderInitials = $firstInitial . $lastInitial;
			}

			// Get recipient information
			$recipientUser = DB::table('admins')
				->where('id', $clientId)
				->select('id', 'first_name', 'last_name', DB::raw("(COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) as full_name"))
				->first();

			if (!$recipientUser) {
				return response()->json([
					'success' => false,
					'message' => 'Client user not found'
				], 404);
			}

			// Create message record
			$messageData = [
				'message' => $message,
				'sender' => $senderName,
				'sender_id' => $senderId,
				'sent_at' => now(),
				'client_matter_id' => $clientMatterId,
				'created_at' => now(),
				'updated_at' => now()
			];

			$messageId = DB::table('messages')->insertGetId($messageData);

			if ($messageId) {
				// Insert recipient into pivot table
				DB::table('message_recipients')->insert([
					'message_id' => $messageId,
					'recipient_id' => $clientId,
					'recipient' => $recipientUser->full_name,
					'is_read' => false,
					'read_at' => null,
					'created_at' => now(),
					'updated_at' => now()
				]);

				// Broadcast message via Pusher
				$messageForBroadcast = [
					'id' => $messageId,
					'message' => $message,
					'sender' => $senderName,
					'sender_id' => $senderId,
					'sender_initials' => $senderInitials,
					'sent_at' => now()->toISOString(),
					'created_at' => now()->toISOString(),
					'client_matter_id' => $clientMatterId,
					'recipients' => [[
						'recipient_id' => $clientId,
						'recipient' => $recipientUser->full_name
					]]
				];

				// Broadcast to client and sender
				if (class_exists('\App\Events\MessageSent')) {
					broadcast(new \App\Events\MessageSent($messageForBroadcast, $clientId));
					broadcast(new \App\Events\MessageSent($messageForBroadcast, $senderId));
				}

				return response()->json([
					'success' => true,
					'message' => 'Message sent successfully',
					'data' => [
						'message_id' => $messageId,
						'message' => $messageForBroadcast
					]
				], 201);
			} else {
				return response()->json([
					'success' => false,
					'message' => 'Failed to send message'
				], 500);
			}

		} catch (\Exception $e) {
			Log::error('Send Message Error: ' . $e->getMessage(), [
				'user_id' => Auth::guard('admin')->id(),
				'client_matter_id' => $request->input('client_matter_id'),
				'trace' => $e->getTraceAsString()
			]);

			return response()->json([
				'success' => false,
				'message' => 'Failed to send message',
				'error' => $e->getMessage()
			], 500);
		}
	}
}