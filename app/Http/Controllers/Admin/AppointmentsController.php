<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;

use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\Notification;
use Carbon\Carbon;

use App\Models\ActivitiesLog;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\ApplicationActivitiesLog;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class AppointmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct()
     {
         $this->middleware('auth:admin');
     }

    public function index(Request $request)
    {  //dd($request->all());
        /*$appointments = Appointment::query()
        ->when($request->q,function($query) use($request){
            $query->where('description','like',"%{$request->q}%")
            ->orWhere('client_unique_id','like',"%{$request->q}%");
        })
        ->with(['user','clients','service','natureOfEnquiry'])
        ->orderBy('created_at', 'desc')->latest('created_at')->paginate(20); //dd($appointments);
        return view('Admin.appointments.index',compact('appointments'))
        ->with('i', (request()->input('page', 1) - 1) * 20);*/

      $appointments = Appointment::query()
        // Handle the appointment date (r)
        ->when($request->r, function ($query) use ($request) {
            $searchTerm = $request->r;

            // Attempt to parse the input as a date
            $formattedDate = null;
            if (preg_match('/\d{2}\/\d{2}\/\d{4}/', $searchTerm)) {
                try {
                    $formattedDate = Carbon::createFromFormat('d/m/Y', $searchTerm)->format('Y-m-d');
                } catch (\Exception $e) {
                    // Handle invalid date format gracefully
                }
            }

            if ($formattedDate) {
                // Filter by the formatted date
                $query->where('date', $formattedDate);
            }
        })

        // Handle the Client reference/description (q)
        ->when($request->q, function ($query) use ($request) {
            $searchTerm = $request->q;

            // Filter by description or client_unique_id
            $query->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('description', 'like', "%{$searchTerm}%")
                    ->orWhere('client_unique_id', 'like', "%{$searchTerm}%");
            });
        })
        // Include related models
        ->with(['user', 'clients', 'service', 'natureOfEnquiry'])
        // Order results
        ->orderBy('created_at', 'desc')
        ->paginate(20); //dd($appointments);

        // Return the view with appointments and pagination index
        return view('Admin.appointments.index', compact('appointments'))->with('i', (request()->input('page', 1) - 1) * 20);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('appointment.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required','detail' => 'required',]);
        Product::create($request->all());
        return redirect()->route('appointment.index')->with('success','Product created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function show(Appointment $appointment)
    {
        $appointment = Appointment::with(['user','clients','service','natureOfEnquiry'])->where('id',$appointment->id)->first();
        //dd($appointment);
        return view('Admin.appointments.show',compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Appointment $appointment)
    {
        $appointment = Appointment::with(['user','clients','service','natureOfEnquiry'])->where('id',$appointment->id)->first();
        return view('Admin.appointments.edit',compact('appointment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
     public function update(Request $request, Appointment $appointment)
    {
        //dd($request->all());
        $request->validate([
            // 'user_id' => 'required|exists:admins,id',
            //'client_id' => 'required|exists:admins,id',
            'date' => 'required',
            'time' => 'required',
            //'title' => 'required',
            'description' => 'required',
            //'invites' => 'required',
            'status' => 'required',
            'appointment_details' => 'required',
            //'noe_id' => 'required'
            'preferred_language' => 'required'
        ]);

        $requestData = $request->all();
        $obj = Appointment::find($requestData['id']);
        $obj->user_id = @Auth::user()->id;
        if( isset($request->date) && $request->date != "") {
            $date = explode('/', $request->date);
            $datey = $date[2].'-'.$date[1].'-'.$date[0];
            $obj->date = $date[2].'-'.$date[1].'-'.$date[0];
        }


        //Adelaide
        if( isset($obj->inperson_address) && $obj->inperson_address == 1 )
        {
            $appointExist = Appointment::where('id','!=',$requestData['id'])
            ->where('inperson_address', '=', 1)
            ->where('status', '!=', 7)
            ->whereDate('date', $datey)
            ->where('time', $requestData['time'])
            ->count();
        }
        //Melbourne
        else
        {

            if
            (
                ( isset($obj->service_id) && $obj->service_id == 1  )
                ||
                (
                    ( isset($obj->service_id) && $obj->service_id == 2 )
                    &&
                    ( isset($obj->noe_id) && ( $obj->noe_id == 1 || $obj->noe_id == 6 || $obj->noe_id == 7) )
                )
            ) { //Paid

                $appointExist = Appointment::where('id','!=',$requestData['id'])
                ->where('status', '!=', 7)
                ->whereDate('date', $datey)
                ->where('time', $request->time)
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->whereIn('noe_id', [1, 2, 3, 4, 5, 6, 7, 8])
                        ->where('service_id', 1);
                    })
                    ->orWhere(function ($q) {
                        $q->whereIn('noe_id', [1, 6, 7])
                        ->where('service_id', 2);
                    });
                })->count();
            }
            else if( isset($obj->service_id) && $obj->service_id == 2) { //Free
                if( isset($obj->noe_id) && ( $obj->noe_id == 2 || $obj->noe_id == 3 ) ) { //Temporary and JRP
                    $appointExist = Appointment::where('id','!=',$requestData['id'])
                    ->where('status', '!=', 7)
                    ->whereDate('date', $datey)
                    ->where('time', $request->time)
                    ->where(function ($query) {
                        $query->whereIn('noe_id', [2,3])
                        ->Where('service_id', 2);
                    })->count();
                }
                else if( isset($obj->noe_id) && ( $obj->noe_id == 4 ) ) { //Tourist Visa
                    $appointExist = Appointment::where('id','!=',$requestData['id'])
                    ->where('status', '!=', 7)
                    ->whereDate('date', $datey)
                    ->where('time', $request->time)
                    ->where(function ($query) {
                        $query->whereIn('noe_id', [4])
                        ->Where('service_id', 2);
                    })->count();
                }
                else if( isset($obj->noe_id) && ( $obj->noe_id == 5 ) ) { //Education/Course Change
                    $appointExist = Appointment::where('id','!=',$requestData['id'])
                    ->where('status', '!=', 7)
                    ->whereDate('date', $datey)
                    ->where('time', $request->time)
                    ->where(function ($query) {
                        $query->whereIn('noe_id', [5])
                        ->Where('service_id', 2);
                    })->count();
                }
            }
        }
        //dd($appointExist);
        if( $appointExist > 0 ){
            return redirect()->route('appointments.edit', ['appointment' => $requestData['id']])->with('error','This appointment time slot is already booked.Please select other time slot.' );
        }

		$obj->time = @$request->time;
        if( isset($request->time) && $request->time != "" ){
			$time = explode('-', $request->time);
			//echo "@@".date("H:i", strtotime($time[0])); die;
            $timeslot_full_start_time = date("g:i A", strtotime($request->time));
            // Add 15 minutes to the start time
            $timeslot_full_end_time = date("g:i A", strtotime($request->time . ' +15 minutes'));
			$obj->timeslot_full = $timeslot_full_start_time.' - '.$timeslot_full_end_time;
		}
		//$obj->title = @$request->title;
		$obj->description = @$request->description;
        $obj->status = @$request->status;
        $obj->appointment_details = @$request->appointment_details;
		//$obj->invites = @$request->invites
        $obj->preferred_language = @$request->preferred_language;
		$saved = $obj->save();
		if($saved){
            //$subject = 'updated an appointment';
			$objs = new ActivitiesLog;
			$objs->client_id = $obj->client_id;
			$objs->created_by = Auth::user()->id;


            //Get Nature of Enquiry
            $nature_of_enquiry_data = DB::table('nature_of_enquiry')->where('id', $obj->noe_id)->first();
            if($nature_of_enquiry_data){
                $nature_of_enquiry_title = $nature_of_enquiry_data->title;
            } else {
                $nature_of_enquiry_title = "";
            }

            //Get book_services
            $service_data = DB::table('book_services')->where('id', $obj->service_id)->first();
            if($service_data){
                $service_title = $service_data->title;
                if( $request->service_id == 1) { //Paid
                    $service_type = 'Paid';
                } else {
                    $service_type = 'Free';
                }
                $service_title_text = $service_title.'-'.$service_type;
            } else {
                $service_title = "";
                $service_title_text = "";
            }

            $objs->description = '<div style="display: -webkit-inline-box;">
            <span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
                <span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
                    '.date('d M', strtotime($obj->date)).'
                </span>
                <span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
                    '.date('Y', strtotime($obj->date)).'
                </span>
            </span>
            </div>
            <div style="display:inline-grid;"><span class="text-semi-bold">'.$nature_of_enquiry_title.'</span> <span class="text-semi-bold">'.$service_title_text.'</span>  <span class="text-semi-bold">'.$obj->appointment_details.'</span> <span class="text-semi-bold">'.$obj->description.'</span> <p class="text-semi-light-grey col-v-1">@ '.$obj->timeslot_full.'</p></div>';

            if( isset($obj->service_id) && $obj->service_id == 1 ){ //1=>Paid
                $subject = 'updated an paid appointment without payment';
            } else if( isset($obj->service_id) && $obj->service_id == 2 ){ //2=>Free
                $subject = 'updated an appointment';
            }
            $objs->subject = $subject;
            $obj->appointment_details = @$request->appointment_details;
			$objs->save();
            return redirect()->route('appointments.index')->with('success','Appointment updated successfully');
		} else {
			return redirect()->route('appointments.index')->with('error',Config::get('constants.server_error') );
		}

        //$data['time']= Carbon::parse($request->time)->format('H:i:s');
       // $appointment->update($data);
        /*if($request->route == url('/admin/assignee')){
            return redirect()->route('assignee.index')->with('success','Assignee updated successfully');
        }*/
    }
     

    /**
     * Remove the specified resource from storage.
     *
     * @param  Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success','Appointment deleted successfully');
    }


    public function assignedetail(Request $request){
        $appointmentdetail = Appointment::with(['user','clients','service','assignee_user','natureOfEnquiry'])->where('id',$request->id)->first();
        // dd($appointmentdetail->assignee_user->id);
    // $admin = \App\Models\Admin::where('id', $notedetail->assignee)->first();
    // $noe = \App\Models\NatureOfEnquiry::where('id', @$appointmentdetail->noeid)->first();
    // $addedby = \App\Models\Admin::where('id', $appointmentdetail->user_id)->first();
    // $client = \App\Models\Admin::where('id', $appointmentdetail->client_id)->first();
    // ?>
    <div class="modal-header">
            <h5 class="modal-title" id="taskModalLabel"><i class="fa fa-bag"></i> <?php echo $appointmentdetail->title ?? $appointmentdetail->service->title; ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="title">Status:</label>
                    <?php

                    if($appointmentdetail->status == 0){
                        $status = '<span style="color: rgb(255, 173, 0);" class="">Pending</span>';
                    }else if($appointmentdetail->status == 2){
                        $status = '<span style="color: rgb(255, 173, 0); " class="">Completed</span>';
                    }else if($appointmentdetail->status == 3){
                        $status = '<span style="color: rgb(156, 156, 156);" class="">Rejected</span>';
                    }else if($appointmentdetail->status == 1){
                        $status = '<span style="color: rgb(113, 204, 83);" class="">Approved</span>';
                    }else{
                        $status = '<span style="color: rgb(113, 204, 83);" class="">N/P</span>';
                    }
                    ?>


                    <ul class="navbar-nav navbar-right">
                        <li class="dropdown dropdown-list-toggle">
                            <a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle updatedstatus"><?php echo $status ?? 'Pending'; ?> <i class="fa fa-angle-down"></i></a>
                            <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
                                <a data-status="0" data-id="<?php echo $appointmentdetail->id; ?>" data-status-name="Pending" href="javascript:;" class="dropdown-item changestatus">
                                    Pending
                                </a>
                                <a data-status="2" data-status-name="Completed" data-id="<?php echo $appointmentdetail->id; ?>" href="javascript:;" class="dropdown-item changestatus">
                                    Completed
                                </a>
                                <a data-status="3" data-status-name="Rejected" data-id="<?php echo $appointmentdetail->id; ?>" href="javascript:;" class="dropdown-item changestatus">
                                    Rejected
                                </a>
                                <a data-status="1" data-status-name="Approved" data-id="<?php echo $appointmentdetail->id; ?>" href="javascript:;" class="dropdown-item changestatus">
                                    Approved
                                </a>
                                <a data-status="4" data-status-name="N/P" data-id="<?php echo $appointmentdetail->id; ?>" href="javascript:;" class="dropdown-item changestatus">
                                     N/P
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="title">Priority:</label>
                    <ul class="navbar-nav navbar-right">
                        <li class="dropdown dropdown-list-toggle">
                            <a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle updatedpriority"><?php echo $appointmentdetail->priority ?? 'Low'; ?><i class="fa fa-angle-down"></i></a>
                             <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
                                <a data-status="Low" data-id="<?php echo $appointmentdetail->id; ?>" href="javascript:;" class="dropdown-item changepriority">
                                    Low
                                </a>
                                <a data-status="Normal" data-id="<?php echo $appointmentdetail->id; ?>" href="javascript:;" class="dropdown-item changepriority">
                                    Normal
                                </a>
                                <a data-status="High" data-id="<?php echo $appointmentdetail->id; ?>" href="javascript:;" class="dropdown-item changepriority">
                                    High
                                </a>
                                <a data-status="Urgent" data-id="<?php echo $appointmentdetail->id; ?>" href="javascript:;" class="dropdown-item changepriority">
                                    Urgent
                                </a>
                             </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="title">Assignee: <a class="openassignee"  href="javascript:;">Change</a></label>
                    <br>
                    <?php if($appointmentdetail){ ?>
                        <div style="display: flex;">
                            <span class="author-avtar" style="margin-left: unset;margin-right: unset;font-size: .8rem;height: 24px;line-height: 24px;width: 24px;min-width: 24px;background: rgb(3, 169, 244);"><?php echo substr($appointmentdetail->user->first_name, 0, 1); ?></span>
                            <span style="margin-left:5px;"><?php echo $appointmentdetail->assignee_user->first_name ?? ''; ?></span>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="title">Added By:</label>
                    <br>
                    <?php if($appointmentdetail){ ?>
                        <div style="display: flex;">
                            <span class="author-avtar" style="margin-left: unset;margin-right: unset;font-size: .8rem;height: 24px;line-height: 24px;width: 24px;min-width: 24px;background: rgb(3, 169, 244);"><?php echo substr($appointmentdetail->user->first_name, 0, 1); ?></span>
                            <span style="margin-left:5px;"><?php echo $appointmentdetail->user->first_name; ?></span>
                        </div>
                    <?php } ?>
                </div>
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
                        <a class="saveassignee btn btn-success" data-id="<?php echo $appointmentdetail->id; ?>" href="javascript:;">Save</a>
                    </div>
                    <div class="col-md-2">
                        <a class="closeassignee" href="javascript:;"><i class="fa fa-times"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-12">
                <div class="form-group">
                    <label for="title">Description:</label>
                    <br>
                    <?php if($appointmentdetail->description != ''){ echo '<span class="desc_click">'.$appointmentdetail->description.'</span>'; }else{ ?><textarea data-id="<?php echo $appointmentdetail->id; ?>" class="form-control tasknewdesc"  placeholder="Enter Description"><?php echo $appointmentdetail->description; ?></textarea><?php } ?>
                    <textarea data-id="<?php echo $appointmentdetail->id; ?>" class="form-control taskdesc" style="display:none;"  placeholder="Enter Description"><?php echo $appointmentdetail->description; ?></textarea>
                </div>
                <p><strong>Note:</strong> <span class="badge badge-warning">Please,click on the above description text to enable the input field.</span></p>
            </div>
            <div class="col-12 col-md-12 col-lg-12">
                <div class="form-group">
                    <label for="title">Comments:</label>
                    <textarea class="form-control taskcomment" name="comment" placeholder="Enter comment here"></textarea>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-12">
                <div class="form-group">
                    <button data-id="<?php echo $appointmentdetail->id; ?>" class="btn btn-primary savecomment" >Save</button>
                </div>
            </div>

            <div class="col-md-12">
                    <h4>Appointment Logs</h4>
                    <div class="logsdata">

  <?php
                    $logslist = AppointmentLog::where('appointment_id',$appointmentdetail->id)->orderby('created_at', 'DESC')->get();
                    foreach($logslist as $llist){
                       $admin = \App\Models\Admin::where('id', $llist->created_by)->first();
                    ?>
                        <div class="logsitem">
                            <div class="row">
                                <div class="col-md-7">
                                    <span class="ag-avatar"><?php echo substr($admin->first_name, 0, 1); ?></span>
                                    <span class="text_info"><span><?php echo $admin->first_name; ?></span><?php echo $llist->title; ?></span>
                                </div>
                                <div class="col-md-5">
                                    <span class="logs_date"><?php echo date('d M Y h:i A', strtotime($llist->created_at)); ?></span>
                                </div>
                                <?php if($llist->message != ''){ ?>
                                <div class="col-md-12 logs_comment">
                                    <p><?php echo $llist->message; ?></p>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                </div>
        </div>
    </div>
    <?php
}

public function update_appointment_status(Request $request){

    $objs = Appointment::find($request->id);

    if($objs->status == 0){
        $status = 'Pending';
    }else if($objs->status == 2){
        $status = 'Completed';
    }else if($objs->status == 3){
        $status = 'Rejected';
    }else if($objs->status == 1){
        $status = 'Approved';
    }else if($objs->status == 4){
        $status = 'N/P';
    }
    $objs->status = $request->status;
    $saved = $objs->save();
    if($saved){
        $objs = new AppointmentLog;
        $objs->title = 'changed status from '.$status.' to '.$request->statusname;
        $objs->created_by = \Auth::user()->id;
        $objs->appointment_id = $request->id;

        $saved = $objs->save();
        $alist = Appointment::find($request->id);
        $status = '';
        if($alist->status == 1 ){
                $status = '<span style="color: rgb(113, 204, 83); width: 84px;">Approved</span>';
            }else if($alist->status == 0){
                $status = '<span style="color: rgb(255, 173, 0); width: 84px;">Pending</span>';
            }else if($alist->status == 2){
                $status = '<span style="color: rgb(255, 173, 0); width: 84px;">Completed</span>';
            }else if($alist->status == 3){
                $status = '<span style="color: rgb(156, 156, 156); width: 84px;">Rejected</span>';
            }else if($alist->status == 4){
                $status = '<span style="color: rgb(156, 156, 156); width: 84px;">N/P</span>';
            }else {
                $status = '<span style="color: rgb(255, 173, 0); width: 84px;">N/P</span>';
            }
        $response['status'] 	= 	true;
        $response['viewstatus'] 	= 	$status;
        $response['message']	=	'saved successfully';
    }else{
        $response['status'] 	= 	false;
        $response['message']	=	'Please try again';
    }
    echo json_encode($response);
}

public function update_appointment_priority(Request $request){
    $objs = Appointment::findOrFail($request->id);
    $status = $objs->priority;
    if($request->status == 'Low'){
        $objs->priority_no = 1;
    }else if($request->status == 'Normal'){
        $objs->priority_no = 2;
    }if($request->status == 'High'){
        $objs->priority_no = 3;
    }if($request->status == 'Urgent'){
        $objs->priority_no = 4;
    }
    $objs->priority = $request->status;
    $saved = $objs->save();

    if($saved){
        $objs = new AppointmentLog;
        $objs->title = 'changed priority from '.$status.' to '.$request->status;
        $objs->created_by = \Auth::user()->id;
        $objs->appointment_id = $request->id;

        $saved = $objs->save();
        $response['status'] 	= 	true;
        $response['message']	=	'saved successfully';
    }else{
        $response['status'] 	= 	false;
        $response['message']	=	'Please try again';
    }
    echo json_encode($response);
}

public function change_assignee(Request $request){
    $objs = Appointment::find($request->id);

    $objs->assignee = $request->assinee;

    $saved = $objs->save();
    if($saved){
        $o = new \App\Models\Notification;
        $o->sender_id = \Auth::user()->id;
        $o->receiver_id = $request->assinee;
        $o->module_id = $request->id;
        $o->url = \URL::to('/admin/appointments');
        $o->notification_type = 'appointment';
        $o->message = $objs->title.' Appointments Assigned by '.\Auth::user()->first_name.' '.\Auth::user()->last_name;
        $o->save();
        $response['status'] 	= 	true;
        $response['message']	=	'Updated successfully';
    }else{
        $response['status'] 	= 	false;
        $response['message']	=	'Please try again';
    }
    echo json_encode($response);
}

public function update_apppointment_comment(Request $request){
    $objs = new AppointmentLog;
    $objs->title = 'has commented';
    $objs->created_by = \Auth::user()->id;
    $objs->appointment_id = $request->id;
    $objs->message = $request->visit_comment;
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

    public function update_apppointment_description(Request $request){
        $objs = Appointment::find($request->id);
        $objs->description = $request->visit_purpose;
        $saved = $objs->save();
        if($saved){
            $objs = new AppointmentLog;
            $objs->title = 'changed description';
            $objs->created_by = \Auth::user()->id;
            $objs->appointment_id = $request->id;
            $objs->message = $request->visit_purpose;
            $saved = $objs->save();
            $response['status'] 	= 	true;
            $response['message']	=	'saved successfully';
        }else{
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
        }
        echo json_encode($response);
    }

    public function appointmentsEducation(Request $request){
		$type='Education';
		return view('Admin.appointments.calender', compact('type'));
	}

	public function appointmentsJrp(Request $request){
		$type='Jrp';
		return view('Admin.appointments.calender', compact('type'));
	}

	public function appointmentsTourist(Request $request){
		$type='Tourist';
		return view('Admin.appointments.calender', compact('type'));
	}

	public function appointmentsOthers(Request $request){
		$type='Others';
		return view('Admin.appointments.calender', compact('type'));
	}

    public function appointmentsAdelaide(Request $request){
		$type = 'Adelaide';
		return view('Admin.appointments.calender', compact('type'));
	}


    public function addAppointmentBook(Request $request){
		$requestData = $request->all(); //dd($requestData);
        $obj = new Appointment;
		$obj->user_id = @Auth::user()->id;
		$obj->client_id = @$request->client_id;
		$obj->timezone = @$request->timezone;
        $obj->service_id = @$request->service_id;
        $obj->noe_id = @$request->noe_id;
        $obj->inperson_address = $request->inperson_address;
        $obj->appointment_details = @$request->appointment_details;
        $obj->preferred_language = @$request->preferred_language;

        if( isset($request->appoint_date) && $request->appoint_date != "") {
            $obj->client_unique_id = @$request->client_unique_id;
        }

        //$obj->full_name = @$request->fullname;
        //$obj->email = @$request->email;
        //$obj->phone = @$request->phone;
        //$obj->date = @$request->appoint_date;
		//$obj->time = @$request->appoint_time;
        if( isset($request->appoint_date) && $request->appoint_date != "") {
            $date = explode('/', $request->appoint_date);
            $obj->date = $date[2].'-'.$date[1].'-'.$date[0];
        }

        $obj->timeslot_full = @$request->appoint_time;
        if( isset($request->appoint_time) && $request->appoint_time != "" ){
			$time = explode('-', $request->appoint_time);
			//echo "@@".date("H:i", strtotime($time[0])); die;
			$obj->time = date("H:i", strtotime($time[0]));
		}

        if( isset($request->slot_overwrite_hidden) && $request->slot_overwrite_hidden != "" ){
			$obj->slot_overwrite_hidden = $request->slot_overwrite_hidden;
		}

        //$obj->title = @$request->title;
		$obj->description = @$request->description;
        //$obj->invites = @$request->invites;
        if( isset($request->promocode_used) && $request->promocode_used != "" ){
			$obj->promocode_used = $request->promocode_used;
        }

        if( isset($request->service_id) && $request->service_id == 1 ){ //1=>Paid,2=>Free
            if( isset($request->promocode_used) && $request->promocode_used != "" ){
                $obj->status = 0; //Due to promocode no payment is needed
            } else {
                $obj->status = 9; //9=>Pending Appointment With Payment Pending
            }
        } else if( isset($request->service_id) && $request->service_id == 2 ){
            $obj->status = 0; //0=>Pending Appointment With Free Type
        }

        $obj->related_to = 'client';
		$saved = $obj->save();
        //Get Nature of Enquiry
        $nature_of_enquiry_data = DB::table('nature_of_enquiry')->where('id', $request->noe_id)->first();
        if($nature_of_enquiry_data){
            $nature_of_enquiry_title = $nature_of_enquiry_data->title;
        } else {
            $nature_of_enquiry_title = "";
        }

        //Get book_services
        $service_data = DB::table('book_services')->where('id', $request->service_id)->first();
        if($service_data){
            $service_title = $service_data->title;
            if( $request->service_id == 1) { //Paid
                $service_type = 'Paid';
            } else {
                $service_type = 'Free';
            }
            $service_title_text = $service_title.'-'.$service_type;
        } else {
            $service_title = "";
            $service_title_text = "";
        }

		if($saved){
            if( isset($request->promocode_used) && $request->promocode_used != "" ){
                DB::table('promocode_uses')->insert([
                    'client_id' => $request->client_id,
                    'promocode_id' => $request->promocode_id,
                    'promocode' => $request->promocode_used
                ]);
            }

		if(isset($request->type) && $request->atype == 'application'){
			$objs = new \App\Models\ApplicationActivitiesLog;
			$objs->stage = $request->type;
				$objs->type = 'appointment';
				$objs->comment = 'created appointment '.@$request->appoint_date;
				$objs->title = '';
				$objs->description = '';
				$objs->app_id = $request->noteid;
				$objs->user_id = Auth::user()->id;
				$saved = $objs->save();
            } else {
                $objs = new ActivitiesLog;
                $objs->client_id = $request->client_id;
                $objs->created_by = Auth::user()->id;

                $appoint_date_val = explode('/', $request->appoint_date);
                $appoint_date_val_formated = $appoint_date_val[0].'/'.$appoint_date_val[1].'/'.$appoint_date_val[2];
                /*if( isset($request->service_id) && $request->service_id == 1 ){ //1=>Paid
                    $objs->description = '<p><span class="text-semi-bold">scheduled an paid appointment without payment on '.$appoint_date_val_formated.' at '.$request->appoint_time.'</span></p>';
                } else if( isset($request->service_id) && $request->service_id == 2 ){ //2=>Free
                    $objs->description = '<p><span class="text-semi-bold">scheduled an appointment on '.$appoint_date_val_formated.' at '.$request->appoint_time.'</span></p>';
                }*/


                $objs->description = '<div style="display: -webkit-inline-box;">
						<span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
							<span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
							  '.date('d M', strtotime($obj->date)).'
							</span>
							<span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
							   '.date('Y', strtotime($obj->date)).'
							</span>
						</span>
					</div>
					<div style="display:inline-grid;"><span class="text-semi-bold">'.$nature_of_enquiry_title.'</span> <span class="text-semi-bold">'.$service_title_text.'</span>  <span class="text-semi-bold">'.$request->appointment_details.'</span> <span class="text-semi-bold">'.$request->description.'</span> <p class="text-semi-light-grey col-v-1">@ '.$request->appoint_time.'</p></div>';

                if( isset($request->service_id) && $request->service_id == 1 ){ //1=>Paid
                    if( isset($request->promocode_used) && $request->promocode_used != "" ){
                        $subject = 'scheduled an appointment';
                    } else {
                        $subject = 'scheduled an paid appointment without payment';
                    }
                } else if( isset($request->service_id) && $request->service_id == 2 ){ //2=>Free
                    $subject = 'scheduled an appointment';
                }
                $objs->subject = $subject;
                $objs->save();
			}

            //if( isset($request->service_id) && $request->service_id == 1 )
            //{ //1=>Paid

                $adminInfo = \App\Models\Admin::select('id','phone','first_name','last_name','email')->where('id','=',$request->client_id)->first();
                if($adminInfo){
                    $clientFullname = $adminInfo->first_name.' '.$adminInfo->last_name;
                } else {
                    $clientFullname = '';
                }
                //Email To customer
                //$host = request()->getHost(); dd($host);
                $host = request()->getHttpHost(); //dd($host);

                if( isset($requestData['appointment_details']) && $requestData['appointment_details'] != ""){
                    if( $requestData['appointment_details'] == "in_person" ){
                        $appointment_details = "In Person";
                    } else if( $requestData['appointment_details'] == "phone" ){
                        $appointment_details = "Phone";
                    } else if( $requestData['appointment_details'] == "zoom_google_meeting" ){
                        $appointment_details = "Zoom / Google Meeting";
                    }
                } else {
                    $appointment_details = "";
                }

                if(isset($requestData['inperson_address']) && $requestData['inperson_address'] != ""){
                    if($requestData['inperson_address'] == 1){
                        $inperson_address = "ADELAIDE (Unit 5 5/55 Gawler Pl, Adelaide SA 5000)";
                    } else if($requestData['inperson_address'] == 2){
                        $inperson_address = "MELBOURNE (Next to flight Center, Level 8/278 Collins St, Melbourne VIC 3000, Australia)";
                    }
                } else {
                    $inperson_address = "";
                }

                $details = [
                    'title' => 'Your Payment is pending. You have booked an appointment on '.$request->appoint_date.'  at '.$request->appoint_time,
                    'body' => 'This is for testing email using smtp',
                    'fullname' => $clientFullname,
                    'date' => $request->appoint_date,
                    'time' => $request->appoint_time,
                    'email'=> $request->client_email,
                    'phone' => $adminInfo->phone,
                    'description' => $request->description,
                    'service'=> $service_title,
                    'host'=> $host,
                    'appointment_id'=> $obj->id,  //payment id
                    'appointment_details'=> $appointment_details,
                    'inperson_address'=> $inperson_address,
                    'service_type'=> $request->service_id,
                    'client_id'=> $request->client_id,
                    'preferred_language'=> $request->preferred_language,
                    'promocode_used'=> $request->promocode_used
                ];

                if( \Mail::to($adminInfo->email)->send(new \App\Mail\AppointmentStripeMail($details)) ) {
                    //send sms message
                    $message = 'Your appointment booked successfully on '.$request->appoint_date.' '.$request->appoint_time;
                    /*$verifiedNumber = VerifiedNumber::where('phone_number',$adminInfo->phone)->where('is_verified', true)->first();
                    if ( $verifiedNumber) {
                        //$this->twilioService->sendSMS($adminInfo->phone,$message);
                        $this->smsService->sendSms($adminInfo->phone,$message);
                    }*/
                }
            //}

            $response['status'] = 	true;
			$response['data']	=	'Appointment saved successfully';
            if(isset($requestData['is_ajax']) && $requestData['is_ajax'] == 1){
                $response['reloadpage'] = true;
            }else{
                $response['reloadpage'] = true; //false;
            }
            $response['client_id']  =    $request->client_id;
            $response['message']	=	'Appointment is booked successfully';
		} else {
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
            $response['client_id']  =    $request->client_id;
            $response['message']	=	'Appointment is not booked.Pls try again';
		}
        echo json_encode($response);
    }

    public function addAppointment(Request $request){
		$requestData = $request->all();

		$obj = new Appointment;
		$obj->user_id = @Auth::user()->id;
		$obj->client_id = @$request->client_id;
		$obj->timezone = @$request->timezone;
		$obj->date = @$request->appoint_date;
		$obj->time = @$request->appoint_time;
		$obj->title = @$request->title;
		$obj->description = @$request->description;
		$obj->invites = @$request->invites;

		$obj->status = 0;
		$obj->related_to = 'client';
		$saved = $obj->save();
		if($saved){

			if(isset($request->type) && $request->atype == 'application'){
				$objs = new \App\Models\ApplicationActivitiesLog;
				$objs->stage = $request->type;
				$objs->type = 'appointment';
				$objs->comment = 'created appointment '.@$request->appoint_date;
				$objs->title = '';
				$objs->description = '';
				$objs->app_id = $request->noteid;
				$objs->user_id = Auth::user()->id;
				$saved = $objs->save();

			}else{
				$subject = 'scheduled an appointment';
			$objs = new ActivitiesLog;
			$objs->client_id = $request->client_id;
			$objs->created_by = Auth::user()->id;
			$objs->description = '<div  style="margin-right: 1rem;float:left;">
						<span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
							<span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
							  '.date('d M', strtotime($obj->date)).'
							</span>
							<span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
							   '.date('Y', strtotime($obj->date)).'
							</span>
						</span>
					</div>
					<div style="float:right;"><span  class="text-semi-bold">'.$obj->title.'</span> <p  class="text-semi-light-grey col-v-1">
				@ '.date('H:i A', strtotime($obj->time)).'
				</p></div>';
			$objs->subject = $subject;
			$objs->save();
			}


			$response['status'] 	= 	true;
			$response['data']	=	'Appointment saved successfully';
				if(isset($requestData['is_ajax']) && $requestData['is_ajax'] == 1){
		            $response['reloadpage'] 	= 	true;
	        	}else{
		        $response['reloadpage'] 	= 	false;
	        	}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}

		 echo json_encode($response);

	}

    public function editappointment(Request $request){
		$requestData = $request->all();

		$obj = Appointment::find($requestData['id']);
		$obj->user_id = @Auth::user()->id;
		$obj->timezone = @$request->timezone;
		$obj->date = @$request->appoint_date;
		$obj->time = @$request->appoint_time;
		$obj->title = @$request->title;
		$obj->description = @$request->description;
		$obj->invites = @$request->invites;
		$obj->status = 0;
		$saved = $obj->save();
		if($saved){
			$subject = 'rescheduled an appointment';
			$objs = new ActivitiesLog;
			$objs->client_id = $request->client_id;
			$objs->created_by = Auth::user()->id;
			$objs->description = '<div  style="margin-right: 1rem;float:left;">
						<span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
							<span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
							  '.date('d M', strtotime($obj->date)).'
							</span>
							<span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
							   '.date('Y', strtotime($obj->date)).'
							</span>
						</span>
					</div>
					<div style="float:right;"><span  class="text-semi-bold">'.$obj->title.'</span> <p  class="text-semi-light-grey col-v-1">
				@ '.date('H:i A', strtotime($obj->time)).'
				</p></div>';
			$objs->subject = $subject;
			$objs->save();
			$response['status'] 	= 	true;
			$response['data']	=	'Appointment updated successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}


    public function updateappointmentstatus(Request $request, $status = Null, $id = Null){
		if(isset($id) && !empty($id))
		{
			$requestData = $request->all();
			if(Appointment::where('id', '=', $id)->exists())
			{
				$obj = Appointment::find($id);
				$obj->status = @$status;
				$saved = $obj->save();

				//$subject = 'Appointment Completed';
                if( $status == 0){
                    $subject = 'Appointment is pending';
                } else if( $status == 1){
                    $subject = 'Appointment is approved';
                } else if( $status == 2){
                    $subject = 'Appointment is completed';
                } else if( $status == 3){
                    $subject = 'Appointment is rejected';
                } else if( $status == 4){
                    $subject = 'Appointment is N/P';
                } else if( $status == 5){
                    $subject = 'Appointment is inrogress';
                } else if( $status == 6){
                    $subject = 'Appointment is pending due to did not come';
                } else if( $status == 7){
                    $subject = 'Appointment is cancelled';
                } else if( $status == 8){
                    $subject = 'Appointment is missed';
                } else if( $status == 9){
                    $subject = 'Appointment is pending with payment pending';
                } else if( $status == 10){
                    $subject = 'Appointment is pending with payment success';
                } else if( $status == 11){
                    $subject = 'Appointment is pending with payment failed';
                }
                $objs = new ActivitiesLog;
                $objs->client_id = $obj->client_id;
                $objs->created_by = Auth::user()->id;
                $objs->description = '<div  style="margin-right: 1rem;float:left;">
						<span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
							<span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
							  '.date('d M', strtotime($obj->date)).'
							</span>
							<span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
							   '.date('Y', strtotime($obj->date)).'
							</span>
						</span>
					</div>
					<div style="float:right;"><span  class="text-semi-bold">'.$obj->title.'</span> <p  class="text-semi-light-grey col-v-1">
				@ '.date('H:i A', strtotime($obj->time)).'
				</p></div>';
				$objs->subject = $subject;
				$objs->save();
				//return Redirect::to('/admin/appointments-cal')->with('success', 'Appointment updated successfully.');
                return redirect()->back()->withInput()->with('success', 'Appointment updated successfully.');
			}else{
				return redirect()->back()->with('error', 'Record Not Found');
			}
		}else{
			return redirect()->back()->with('error', 'Record Not Found');
		}
	}

    public function updatefollowupschedule(Request $request)
    {
        $requestData = $request->all(); //dd($requestData);

        $obj = Appointment::find($requestData['appointment_id']);
        $obj->user_id = @Auth::user()->id;
        //$obj->timezone = @$request->timezone;
        //$obj->date = @$request->followup_date;

        if( isset($request->followup_date) && $request->followup_date != "") {
            $date = explode('/', $request->followup_date);
            $datey = $date[2].'-'.$date[1].'-'.$date[0];
            $obj->date = $date[2].'-'.$date[1].'-'.$date[0];
        }

        //Adelaide
        if( isset($obj->inperson_address) && $obj->inperson_address == 1 )
        {
            $appointExist = Appointment::where('id','!=',$requestData['appointment_id'])
            ->where('inperson_address', '=', 1)
            ->where('status', '!=', 7)
            ->whereDate('date', $datey)
            ->where('time', $request->followup_time)
            ->count();
        }

        //Melbourne
        else
        {

            if
            (
                ( isset($obj->service_id) && $obj->service_id == 1  )
                ||
                (
                    ( isset($obj->service_id) && $obj->service_id == 2 )
                    &&
                    ( isset($obj->noe_id) && ( $obj->noe_id == 1 || $obj->noe_id == 6 || $obj->noe_id == 7) )
                )
            ) { //Paid

                $appointExist = Appointment::where('id','!=',$requestData['appointment_id'])
                ->where('inperson_address', '=', 2)
                ->where('status', '!=', 7)
                ->whereDate('date', $datey)
                ->where('time', $request->followup_time)
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->whereIn('noe_id', [1, 2, 3, 4, 5, 6, 7, 8])
                        ->where('service_id', 1);
                    })
                    ->orWhere(function ($q) {
                        $q->whereIn('noe_id', [1, 6, 7])
                        ->where('service_id', 2);
                    });
                })->count();
            }
            else if( isset($obj->service_id) && $obj->service_id == 2) { //Free
                if( isset($obj->noe_id) && ( $obj->noe_id == 2 || $obj->noe_id == 3 ) ) { //Temporary and JRP
                    $appointExist = Appointment::where('id','!=',$requestData['appointment_id'])
                    ->where('inperson_address', '=', 2)
                    ->where('status', '!=', 7)
                    ->whereDate('date', $datey)
                    ->where('time', $request->followup_time)
                    ->where(function ($query) {
                        $query->whereIn('noe_id', [2,3])
                        ->Where('service_id', 2);
                    })->count();
                }
                else if( isset($obj->noe_id) && ( $obj->noe_id == 4 ) ) { //Tourist Visa
                    $appointExist = Appointment::where('id','!=',$requestData['appointment_id'])
                    ->where('inperson_address', '=', 2)
                    ->where('status', '!=', 7)
                    ->whereDate('date', $datey)
                    ->where('time', $request->followup_time)
                    ->where(function ($query) {
                        $query->whereIn('noe_id', [4])
                        ->Where('service_id', 2);
                    })->count();
                }
                else if( isset($obj->noe_id) && ( $obj->noe_id == 5 ) ) { //Education/Course Change
                    $appointExist = Appointment::where('id','!=',$requestData['appointment_id'])
                    ->where('inperson_address', '=', 2)
                    ->where('status', '!=', 7)
                    ->whereDate('date', $datey)
                    ->where('time', $request->followup_time)
                    ->where(function ($query) {
                        $query->whereIn('noe_id', [5])
                        ->Where('service_id', 2);
                    })->count();
                }
            }
        }
        //dd($appointExist);
        if( $appointExist > 0 ){
            return redirect()->back()->with('error', 'This appointment time slot is already booked.Please select other time slot.');
        }

        $obj->time = @$request->followup_time;
        if( isset($request->followup_time) && $request->followup_time != "" ){
            $time = explode('-', $request->followup_time);
            //echo "@@".date("H:i", strtotime($time[0])); die;
            $timeslot_full_start_time = date("g:i A", strtotime($request->followup_time));
            // Add 15 minutes to the start time
            $timeslot_full_end_time = date("g:i A", strtotime($request->followup_time . ' +15 minutes'));
            $obj->timeslot_full = $timeslot_full_start_time.' - '.$timeslot_full_end_time;
        }
        //$obj->title = @$request->title;
        $obj->description = @$request->edit_description;
        //$obj->invites = @$request->invites
        $saved = $obj->save();
        if($saved){
            //$subject = 'updated an appointment';
            $objs = new ActivitiesLog;
            $objs->client_id = $obj->client_id;
            $objs->created_by = Auth::user()->id;
            /*$objs->description = '<div  style="margin-right: 1rem;float:left;">
                    <span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
                        <span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
                            '.date('d M', strtotime($obj->date)).'
                        </span>
                        <span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
                            '.date('Y', strtotime($obj->date)).'
                        </span>
                    </span>
                </div>
                <div style="float:right;"><span  class="text-semi-bold">'.$obj->title.'</span> <p  class="text-semi-light-grey col-v-1">
            @ '.date('H:i A', strtotime($obj->time)).'
            </p></div>';*/

            //Get Nature of Enquiry
            $nature_of_enquiry_data = DB::table('nature_of_enquiry')->where('id', $obj->noe_id)->first();
            if($nature_of_enquiry_data){
                $nature_of_enquiry_title = $nature_of_enquiry_data->title;
            } else {
                $nature_of_enquiry_title = "";
            }

            //Get book_services
            $service_data = DB::table('book_services')->where('id', $obj->service_id)->first();
            if($service_data){
                $service_title = $service_data->title;
                if( $request->service_id == 1) { //Paid
                    $service_type = 'Paid';
                } else {
                    $service_type = 'Free';
                }
                $service_title_text = $service_title.'-'.$service_type;
            } else {
                $service_title = "";
                $service_title_text = "";
            }

            $objs->description = '<div style="display: -webkit-inline-box;">
            <span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
                <span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
                    '.date('d M', strtotime($obj->date)).'
                </span>
                <span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
                    '.date('Y', strtotime($obj->date)).'
                </span>
            </span>
            </div>
            <div style="display:inline-grid;"><span class="text-semi-bold">'.$nature_of_enquiry_title.'</span> <span class="text-semi-bold">'.$service_title_text.'</span>  <span class="text-semi-bold">'.$obj->appointment_details.'</span> <span class="text-semi-bold">'.$obj->description.'</span> <p class="text-semi-light-grey col-v-1">@ '.$obj->timeslot_full.'</p></div>';

            if( isset($obj->service_id) && $obj->service_id == 1 ){ //1=>Paid
                $subject = 'updated an paid appointment without payment';
            } else if( isset($obj->service_id) && $obj->service_id == 2 ){ //2=>Free
                $subject = 'updated an appointment';
            }
            $objs->subject = $subject;
            $objs->save();
            //return Redirect::to('/admin/appointments-cal')->with('success', 'Appointment updated successfully.');
            return Redirect()->back()->with('success', 'Appointment updated successfully.');
        } else {
            return redirect()->back()->with('error', Config::get('constants.server_error'));
        }

    }


    public function getAppointments(Request $request){
		ob_start();
		?>
		<div class="row">
			<div class="col-md-5 appointment_grid_list">
				<?php
				$rr=0;
				$appointmentdata = array();
				$appointmentlists = Appointment::where('client_id', $request->clientid)->where('related_to', 'client')->orderby('created_at', 'DESC')->get();
				$appointmentlistslast = Appointment::where('client_id', $request->clientid)->where('related_to', 'client')->orderby('created_at', 'DESC')->first();
				foreach($appointmentlists as $appointmentlist){
					$admin = \App\Models\Admin::where('id', $appointmentlist->user_id)->first();
					$datetime = $appointmentlist->created_at;
					$timeago = Controller::time_elapsed_string($datetime);

					$appointmentdata[$appointmentlist->id] = array(
						'title' => $appointmentlist->title,
						'time' => date('H:i A', strtotime($appointmentlist->time)),
						'date' => date('d D, M Y', strtotime($appointmentlist->date)),
						'description' => $appointmentlist->description,
						'createdby' => substr($admin->first_name, 0, 1),
						'createdname' => $admin->first_name,
						'createdemail' => $admin->email,
					);
				?>
				<div class="appointmentdata <?php if($rr == 0){ echo 'active'; } ?>" data-id="<?php echo $appointmentlist->id; ?>">
					<div class="appointment_col">
						<div class="appointdate">
							<h5><?php echo date('d D', strtotime($appointmentlist->date)); ?></h5>
							<p><?php echo date('H:i A', strtotime($appointmentlist->time)); ?><br>
							<i><small><?php echo $timeago ?></small></i></p>
						</div>
						<div class="title_desc">
							<h5><?php echo $appointmentlist->title; ?></h5>
							<p><?php echo $appointmentlist->description; ?></p>
						</div>
						<div class="appoint_created">
							<span class="span_label">Created By:
							<span><?php echo substr($admin->first_name, 0, 1); ?></span></span>
							<div class="dropdown d-inline dropdown_ellipsis_icon">
								<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
								<div class="dropdown-menu">
									<a class="dropdown-item edit_appointment" data-id="<?php echo $appointmentlist->id; ?>" href="javascript:;">Edit</a>
									<a data-id="<?php echo $appointmentlist->id; ?>" data-href="deleteappointment" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php $rr++; } ?>
			</div>
			<div class="col-md-7">
				<div class="editappointment">
					<a class="edit_link edit_appointment" href="javascript:;" data-id="<?php echo $appointmentlistslast->id; ?>"><i class="fa fa-edit"></i></a>
					<?php
					$adminfirst = \App\Models\Admin::where('id', $appointmentlistslast->user_id)->first();
					?>
					<div class="content">
						<h4 class="appointmentname"><?php echo $appointmentlistslast->title; ?></h4>
						<div class="appitem">
							<i class="fa fa-clock"></i>
							<span class="appcontent appointmenttime"><?php echo date('H:i A', strtotime($appointmentlistslast->time)); ?></span>
						</div>
						<div class="appitem">
							<i class="fa fa-calendar"></i>
							<span class="appcontent appointmentdate"><?php echo date('d D, M Y', strtotime($appointmentlistslast->date)); ?></span>
						</div>
						<div class="description appointmentdescription">
							<p><?php echo $appointmentlistslast->description; ?></p>
						</div>
						<div class="created_by">
							<span class="label">Created By:</span>
							<div class="createdby">
								<span class="appointmentcreatedby"><?php echo substr($adminfirst->first_name, 0, 1); ?></span>
							</div>
							<div class="createdinfo">
								<a href="" class="appointmentcreatedname"><?php echo $adminfirst->first_name ?></a>
								<p class="appointmentcreatedemail"><?php echo $adminfirst->primary_email; ?></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		echo ob_get_clean();
		die;
	}

    public function getAppointmentdetail(Request $request){
		$obj = Appointment::find($request->id);
		if($obj){
			?>
			<form method="post" action="<?php echo \URL::to('/admin/editappointment'); ?>" name="editappointment" id="editappointment" autocomplete="off" enctype="multipart/form-data">

				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<input type="hidden" name="client_id" value="<?php echo $obj->client_id; ?>">
				<input type="hidden" name="id" value="<?php echo $obj->id; ?>">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label style="display:block;" for="related_to">Related to:</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="client" value="Client" name="related_to" checked>
									<label class="form-check-label" for="client">Client</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="partner" value="Partner" name="related_to">
									<label class="form-check-label" for="partner">Partner</label>
								</div>
								<span class="custom-error related_to_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label style="display:block;" for="related_to">Added by:</label>
								<span><?php echo @Auth::user()->first_name; ?></span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="client_name">Client Name <span class="span_req">*</span></label>
								<input type="text" name="client_name" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Client Name" readonly value="<?php echo $obj->clients->first_name.' '.@$obj->clients->last_name; ?>">

							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="timezone">Timezone <span class="span_req">*</span></label>
								<select class="form-control timezoneselects2" name="timezone" data-valid="required">
									<option value="">Select Timezone</option>
									<?php
									$timelist = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
									foreach($timelist as $tlist){
										?>
										<option value="<?php echo $tlist; ?>" <?php if($obj->timezone == $tlist){ echo 'selected'; } ?>><?php echo $tlist; ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-7 col-lg-7">
							<div class="form-group">
								<label for="appoint_date">Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input type="text" name="appoint_date" class="form-control datepicker" data-valid="required" autocomplete="off" placeholder="Select Date" readonly value="<?php echo $obj->date; ?>">

								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error appoint_date_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-5 col-lg-5">
							<div class="form-group">
								<label for="appoint_time">Time</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-clock"></i>
										</div>
									</div>
									<input type="time" name="appoint_time" class="form-control" data-valid="required" autocomplete="off" placeholder="Select Date" value="<?php echo $obj->time; ?>">

								</div>
								<span class="custom-error appoint_time_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								<input type="text" name="title" class="form-control " data-valid="required" autocomplete="off" placeholder="Enter Title"  value="<?php echo $obj->title; ?>">

								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="description">Description</label>
								<textarea class="form-control" name="description" placeholder="Description"><?php echo $obj->description; ?></textarea>
								<span class="custom-error description_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="invites">Invitees</label>
								<select class="form-control invitesselects2" name="invites">
									<option value="">Select Invitees</option>
								 <?php
										$headoffice = \App\Models\Admin::where('role','!=',7)->get();
									foreach($headoffice as $holist){
										?>
										<option value="<?php echo $holist->id; ?>" <?php if($obj->invites == $holist->id){ echo 'selected'; } ?>><?php echo $holist->first_name.' '. $holist->last_name.' ('.$holist->email.')'; ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('editappointment')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			<?php
		}else{
			?>
			Record Not Found
			<?php
		}
	}

	public function deleteappointment(Request $request){
		$note_id = $request->note_id;
		if(Appointment::where('id',$note_id)->exists()){
			$data = Appointment::where('id',$note_id)->first();
			$res = DB::table('appointments')->where('id', @$note_id)->delete();
			if($res){

				$subject = 'deleted an appointment';

				$objs = new ActivitiesLog;
				$objs->client_id = $data->client_id;
				$objs->created_by = Auth::user()->id;
			$objs->description = '<div  style="margin-right: 1rem;float:left;">
						<span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
							<span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
							  '.date('d M', strtotime($data->date)).'
							</span>
							<span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
							   '.date('Y', strtotime($data->date)).'
							</span>
						</span>
					</div>
					<div style="float:right;"><span  class="text-semi-bold">'.$data->title.'</span> <p  class="text-semi-light-grey col-v-1">
				@ '.date('H:i A', strtotime($data->time)).'
				</p></div>';
				$objs->subject = $subject;
				$objs->save();
			$response['status'] 	= 	true;
			$response['data']	=	$data;
			}else{
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}


}
