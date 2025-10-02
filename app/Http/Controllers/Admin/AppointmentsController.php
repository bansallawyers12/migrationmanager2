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
use Auth;
use Illuminate\Support\Facades\DB;

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
     * @param  \App\Appointment  $appointment
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
     * @param  \App\Appointment  $appointment
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
     * @param  \App\Appointment  $appointment
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
        $obj = \App\Appointment::find($requestData['id']);
        $obj->user_id = @Auth::user()->id;
        if( isset($request->date) && $request->date != "") {
            $date = explode('/', $request->date);
            $datey = $date[2].'-'.$date[1].'-'.$date[0];
            $obj->date = $date[2].'-'.$date[1].'-'.$date[0];
        }


        //Adelaide
        if( isset($obj->inperson_address) && $obj->inperson_address == 1 )
        {
            $appointExist = \App\Appointment::where('id','!=',$requestData['id'])
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

                $appointExist = \App\Appointment::where('id','!=',$requestData['id'])
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
                    $appointExist = \App\Appointment::where('id','!=',$requestData['id'])
                    ->where('status', '!=', 7)
                    ->whereDate('date', $datey)
                    ->where('time', $request->time)
                    ->where(function ($query) {
                        $query->whereIn('noe_id', [2,3])
                        ->Where('service_id', 2);
                    })->count();
                }
                else if( isset($obj->noe_id) && ( $obj->noe_id == 4 ) ) { //Tourist Visa
                    $appointExist = \App\Appointment::where('id','!=',$requestData['id'])
                    ->where('status', '!=', 7)
                    ->whereDate('date', $datey)
                    ->where('time', $request->time)
                    ->where(function ($query) {
                        $query->whereIn('noe_id', [4])
                        ->Where('service_id', 2);
                    })->count();
                }
                else if( isset($obj->noe_id) && ( $obj->noe_id == 5 ) ) { //Education/Course Change
                    $appointExist = \App\Appointment::where('id','!=',$requestData['id'])
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
            //return redirect()->route('appointments.index')->with('error','This appointment time slot is already booked.Please select other time slot.' );
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
        //return redirect()->route('appointments.index')->with('success','Appointment updated successfully');
    }
     

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Appointment  $appointment
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
    // $admin = \App\Admin::where('id', $notedetail->assignee)->first();
    // $noe = \App\NatureOfEnquiry::where('id', @$appointmentdetail->noeid)->first();
    // $addedby = \App\Admin::where('id', $appointmentdetail->user_id)->first();
    // $client = \App\Admin::where('id', $appointmentdetail->client_id)->first();
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
                                foreach(\App\Admin::where('role','!=',7)->orderby('first_name','ASC')->get() as $admin){
                                    $branchname = \App\Branch::where('id',$admin->office_id)->first();
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
                       $admin = \App\Admin::where('id', $llist->created_by)->first();
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
        $o = new \App\Notification;
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

}
