<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder;

use App\Models\Appointment;
use App\Models\Note;
use App\Models\AppointmentLog;
use App\Models\Notification;
use Carbon\Carbon;
use App\Models\Admin;
use App\Models\ActivitiesLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Helpers\Utf8Helper;
use Illuminate\Support\Facades\URL;

class AssigneeController extends Controller
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

    //All task lists except completed = Closed
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Note::class)
            ->allowedSorts(['first_name', 'followup_date', 'task_group', 'created_at'])
            ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])
            ->where('type','client')
            ->where('folloup',1)
            ->where('status','<>','1');

        if(Auth::user()->role == 1){
            $assignees = $query->whereNotNull('client_id')->paginate(20);
        }else{
            $assignees = $query->where('assigned_to',Auth::user()->id)->paginate(20);
        }

        return view('crm.assignee.index',compact('assignees'))
         ->with('i', (request()->input('page', 1) - 1) * 20);
    }

    //All completed task lists
    public function completed(Request $request)
    {
        if(Auth::user()->role == 1){
            $assignees = \App\Models\Note::with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status','1')->orderBy('created_at', 'desc')->latest()->paginate(20); //where('status','like','Closed')
        }else{
            $assignees = \App\Models\Note::with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status','1')->orderBy('created_at', 'desc')->latest()->paginate(20);
        }  //dd( $assignees);
        return view('crm.assignee.completed',compact('assignees'))
         ->with('i', (request()->input('page', 1) - 1) * 20);
    }

    //Update task to be complete
    public function updatetaskcompleted(Request $request,Note $note)
    {
        $data = $request->all(); //dd($data);
        $note = Note::where('unique_group_id',$data['unique_group_id'])
                ->whereNotNull('assigned_to')
                ->whereNotNull('unique_group_id')
                ->update(['status'=>'1']);
        if($note){
            $note_data = Note::where('id',$data['id'])->first(); //dd($note_data);
            if($note_data){
                $admin_data = Admin::where('id',$note_data['assigned_to'])->first(); //dd($admin_data);
                if($admin_data){
                    $assignee_name = $admin_data['first_name']." ".$admin_data['last_name'];
                } else {
                    $assignee_name = 'N/A';
                }
                $objs = new ActivitiesLog;
                $objs->client_id = $note_data['client_id'];
                $objs->created_by = Auth::user()->id;
                $objs->subject = 'assigned task for '.@$assignee_name;
                $objs->description = '<p>'.@$note_data['description'].'</p>';
                if(Auth::user()->id != @$note_data['assigned_to']){
                    $objs->use_for = @$note_data['assigned_to'];
                } else {
                    $objs->use_for = "";
                }
                $objs->followup_date = @$note_data['updated_at'];
                $objs->task_group = @$note_data['task_group'];
                $objs->task_status = 1; //maked completed
                $objs->save();
            }
            $response['status'] 	= 	true;
            $response['message']	=	'Task updated successfully';
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
        }
        echo json_encode($response);
    }

    //Update task to be not complete
    public function updatetasknotcompleted(Request $request,Note $note)
    {
        $data = $request->all(); //dd($data['id']);
        $note = Note::where('unique_group_id',$data['unique_group_id'])
                    ->whereNotNull('assigned_to')
                    ->whereNotNull('unique_group_id')
                    ->update(['status'=>'0']);
        if($note){
            $response['status'] 	= 	true;
            $response['message']	=	'Task updated successfully';
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
        }
        echo json_encode($response);
    }

     //All assigned by me task list which r incomplete
     public function assigned_by_me(Request $request)
     {
        if(Auth::user()->role == 1){
             $assignees_notCompleted = \App\Models\Note::with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])->where('status','<>','1')->where('type','client')->whereNotNull('client_id')->where('folloup',1)->orderBy('created_at', 'desc')->latest()->paginate(20);
        } else {
             $assignees_notCompleted = \App\Models\Note::with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])->where('status','<>','1')->where('user_id',Auth::user()->id)->where('type','client')->where('folloup',1)->orderBy('created_at', 'desc')->latest()->paginate(20);
        }
         #dd($assignees_notCompleted);
         return view('crm.assignee.assign_by_me',compact('assignees_notCompleted'))
          ->with('i', (request()->input('page', 1) - 1) * 20);
     }

    //All assigned to me task list
    public function assigned_to_me(Request $request)
    {
        if(Auth::user()->role == 1){
            $assignees_notCompleted = \App\Models\Note::with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])->where('status','<>','1')->where('assigned_to',Auth::user()->id)->where('type','client')->whereNotNull('client_id')->where('folloup',1)->orderBy('created_at', 'desc')->latest()->paginate(20);//where('status','not like','Closed')

            $assignees_completed = \App\Models\Note::with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])->where('status','1')->where('assigned_to',Auth::user()->id)->where('type','client')->whereNotNull('client_id')->where('folloup',1)->orderBy('created_at', 'desc')->latest()->paginate(20);
        }else{
            $assignees_notCompleted = \App\Models\Note::with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])->where('status','<>','1')->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->orderBy('created_at', 'desc')->latest()->paginate(20);

            $assignees_completed = \App\Models\Note::with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])->where('status','1')->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->orderBy('created_at', 'desc')->latest()->paginate(20);
        }
        return view('crm.assignee.assign_to_me',compact('assignees_notCompleted','assignees_completed'))
         ->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function action_completed(Request $request)
    {   //dd($request->all());
        $req_data = $request->all();
        if( isset($req_data['group_type'])  && $req_data['group_type'] != ""){
            $task_group = $req_data['group_type'];
        } else {
            $task_group = 'All';
        }
        $user = Auth::user();

        $assignees_completed = \App\Models\Note::with([
                'noteUser',
                'noteClient',
                'lead.natureOfEnquiry',
                'lead.service',
                'assigned_user'
            ])
            ->where('status', '1')
            ->where('type', 'client')
            ->whereNotNull('client_id')
            ->where('folloup', 1)
            ->when($user->role != 1, function ($query) use ($user) {
                return $query->where('assigned_to', $user->id);
            })
            ->when($task_group !== 'All', function ($query) use ($task_group) {
                return $query->where('task_group', 'like', $task_group);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get task group counts with single optimized query
        $taskGroupCounts = $this->getCompletedTaskGroupCounts($user);
        
        //dd(count($assignees_completed));
        return view('crm.assignee.action_completed',compact('assignees_completed','task_group','taskGroupCounts'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    /**
     * Get completed task counts grouped by task_group
     * Uses a single query with GROUP BY for better performance
     * 
     * @param \App\Models\Admin $user
     * @return array
     */
    private function getCompletedTaskGroupCounts($user)
    {
        $query = \App\Models\Note::where('status', '1')
            ->where('type', 'client')
            ->where('folloup', 1)
            ->when($user->role != 1, function ($query) use ($user) {
                return $query->where('assigned_to', $user->id);
            });

        // For admin role, add client_id filter
        if ($user->role == 1) {
            $query->whereNotNull('client_id');
        }

        // Get counts grouped by task_group
        $groupedCounts = $query->selectRaw('task_group, COUNT(*) as count')
            ->groupBy('task_group')
            ->pluck('count', 'task_group')
            ->toArray();

        // Initialize all task groups with default count of 0
        $counts = [
            'All' => array_sum($groupedCounts),
            'Call' => $groupedCounts['Call'] ?? 0,
            'Checklist' => $groupedCounts['Checklist'] ?? 0,
            'Review' => $groupedCounts['Review'] ?? 0,
            'Query' => $groupedCounts['Query'] ?? 0,
            'Urgent' => $groupedCounts['Urgent'] ?? 0,
            'Personal Task' => $groupedCounts['Personal Task'] ?? 0,
        ];

        return $counts;
    }

    public function action() {
        return view('crm.assignee.action');
    }

    public function getAction(Request $request)
    {
        try {
            if ($request->ajax()) {
                // Select specific columns from the notes table, using the correct column name 'user_id'
                $query = Note::select([
                        'notes.id',
                        'notes.user_id', // Changed from 'created_by' to 'user_id'
                        'notes.client_id',
                        'notes.assigned_to',
                        'notes.status',
                        'notes.type',
                        'notes.folloup',
                        'notes.followup_date',
                        'notes.task_group',
                        'notes.description',
                        'notes.unique_group_id',
                        'notes.created_at'
                    ])
                    ->with(['noteUser', 'noteClient', 'assigned_user'])
                    ->where('notes.status', '<>', '1')
                    ->where('notes.type', 'client')
                    ->where('notes.folloup', 1);

                // Check if user is authenticated and has proper role
                if (Auth::check() && Auth::user()->role != 1) {
                    $query->where('notes.assigned_to', Auth::user()->id);
                }

                // Apply filter based on tab
                if ($request->filter && $request->filter != 'all') {
                    if ($request->filter == 'assigned_by_me') {
                        $query->where('notes.user_id', Auth::user()->id);
                    } elseif ($request->filter == 'completed') {
                        $query->where('notes.status', '1');
                    } else {
                        // Handle special case for personal_task to convert underscore to space
                        $taskGroup = $request->filter;
                        if ($taskGroup == 'personal_task') {
                            $taskGroup = 'Personal Task';
                        } else {
                            $taskGroup = ucfirst($taskGroup);
                        }
                        $query->where('notes.task_group', $taskGroup);
                    }
                }

                // Apply search functionality
                if ($request->has('search') && !empty($request->search)) {
                    $searchTerm = $request->search;
                    $query->where(function ($q) use ($searchTerm) {
                        $q->where('notes.followup_date', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('notes.task_group', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('notes.description', 'LIKE', '%' . $searchTerm . '%');
                    });
                }

                // Apply sorting - simplified to avoid join issues
                if ($request->has('order')) {
                    $orderColumnIndex = $request->order[0]['column'];
                    $orderDirection = $request->order[0]['dir'];
                    $columns = $request->columns;

                    $columnName = $columns[$orderColumnIndex]['name'];

                    // Map DataTables column names to database columns
                    switch ($columnName) {
                        case 'assign_date':
                            $query->orderBy('notes.followup_date', $orderDirection);
                            break;
                        case 'task_group':
                            $query->orderBy('notes.task_group', $orderDirection);
                            break;
                        case 'note_description':
                            $query->orderBy('notes.description', $orderDirection);
                            break;
                        default:
                            $query->orderBy('notes.created_at', 'desc'); // Fallback sorting
                            break;
                    }
                } else {
                    $query->orderBy('notes.created_at', 'desc'); // Default sorting
                }

                $dataTable = DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('done_task', function($data) {
                        $done_task = '<input type="radio" class="complete_task" data-toggle="tooltip" title="Mark Complete!" data-id="'.$data->id.'" data-unique_group_id="'.$data->unique_group_id.'">';
                        return $done_task;
                    })
                    ->addColumn('assigner_name', function($data) {
                        try {
                            if ($data->noteUser) {
                                $firstName = Utf8Helper::safeSanitize($data->noteUser->first_name ?? '');
                                $lastName = Utf8Helper::safeSanitize($data->noteUser->last_name ?? '');
                                return $firstName . ' ' . $lastName;
                            }
                            return 'N/P';
                        } catch (\Exception $e) {
                            return 'N/P';
                        }
                    })
                    ->addColumn('client_reference', function($data) {
                        try {
                            if ($data->noteClient) {
                                $firstName = Utf8Helper::safeSanitize($data->noteClient->first_name ?? '');
                                $lastName = Utf8Helper::safeSanitize($data->noteClient->last_name ?? '');
                                $clientId = Utf8Helper::safeSanitize($data->noteClient->client_id ?? '');
                                
                                $user_name = $firstName . ' ' . $lastName;
                                $user_name .= "<br>";
                                $client_encoded_id = base64_encode(convert_uuencode(@$data->client_id));
                                $user_name .= '<a href="'.url('/clients/detail/'.$client_encoded_id).'" target="_blank">'.$clientId.'</a>';
                            } else {
                                $user_name = 'N/P';
                            }
                            return $user_name;
                        } catch (\Exception $e) {
                            return 'N/P';
                        }
                    })
                    ->addColumn('assign_date', function($data) {
                        try {
                            return $data->followup_date ? date('d/m/Y', strtotime($data->followup_date)) : 'N/P';
                        } catch (\Exception $e) {
                            return 'N/P';
                        }
                    })
                    ->addColumn('task_group', function($data) {
                        try {
                            return $data->task_group ? Utf8Helper::safeSanitize($data->task_group) : 'N/P';
                        } catch (\Exception $e) {
                            return 'N/P';
                        }
                    })
                    ->addColumn('note_description', function($data) {
                        try {
                            if (isset($data->description) && $data->description != "") {
                                // Use Utf8Helper for consistent UTF-8 handling
                                $sanitized_description = Utf8Helper::safeSanitize($data->description);
                                
                                if (mb_strlen($sanitized_description, 'UTF-8') > 190) {
                                    // For data attribute: use HTML encoding to prevent XSS
                                    $encoded_for_attr = htmlspecialchars($sanitized_description, ENT_QUOTES, 'UTF-8');
                                    // For display: use safe truncation without encoding (DataTables rawColumns handles this)
                                    $truncated_desc = Utf8Helper::safeTruncate($sanitized_description, 190, '');
                                    $final_desc = $truncated_desc . '<button type="button" class="btn btn-link btn_readmore" data-toggle="popover" data-trigger="click" data-html="true" data-full-content="'.$encoded_for_attr.'" data-placement="top">Read more</button>';
                                } else {
                                    $final_desc = $sanitized_description;
                                }
                            } else {
                                $final_desc = "N/P";
                            }
                            return $final_desc;
                        } catch (\Exception $e) {
                            return "N/P";
                        }
                    })
                    ->addColumn('action', function($list) {
                        try {
                            $actionBtn = '';
                            $current_date1 = $list->followup_date ?: date('Y-m-d');

                            if ($list->task_group != 'Personal Task') {
                                // Update Task button with data attributes but no inline data-content
                                // Use direct htmlspecialchars instead of Utf8Helper wrapper to avoid redundant sanitization
                                $safe_description = htmlspecialchars(Utf8Helper::safeSanitize($list->description ?? ''), ENT_QUOTES, 'UTF-8');
                                $safe_task_group = htmlspecialchars(Utf8Helper::safeSanitize($list->task_group ?? ''), ENT_QUOTES, 'UTF-8');
                                $actionBtn .= '<button type="button" data-assignedto="'.$list->assigned_to.'" data-noteid="'.$safe_description.'" data-taskid="'.$list->id.'" data-taskgroupid="'.$safe_task_group.'" data-followupdate="'.$current_date1.'" data-clientid="'.base64_encode(convert_uuencode(@$list->client_id)).'" class="btn btn-primary btn-block update_task" data-toggle="popover" data-role="popover" title="" data-placement="left" style="width: 40px;display: inline;margin-top:0px;"><i class="fa fa-edit" aria-hidden="true"></i></button>';
                            }

                            // Delete button
                            $actionBtn .= ' <button class="btn btn-danger deleteNote" data-remote="/destroy_activity/'.$list->id.'"><i class="fa fa-trash" aria-hidden="true"></i></button>';

                            return $actionBtn;
                        } catch (\Exception $e) {
                            return '';
                        }
                    })
                    ->rawColumns(['done_task', 'client_reference', 'note_description', 'action']);

                // Get the response and ensure UTF-8 encoding
                $response = $dataTable->make(true);
                
                // Set proper UTF-8 headers
                if ($response instanceof \Illuminate\Http\JsonResponse) {
                    $response->header('Content-Type', 'application/json; charset=utf-8');
                }
                
                return $response;
            }
        } catch (\Exception $e) {
            Log::error('Error in getAction: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'draw' => intval($request->get('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while processing the request'
            ], 500);
        }
    }

    public function getActionCounts(Request $request)
    {
        $counts = [
            'all' => 0,
            'call' => 0,
            'checklist' => 0,
            'review' => 0,
            'query' => 0,
            'urgent' => 0,
            'personal_task' => 0
        ];

        $query = Note::where('status', '<>', '1')
            ->where('type', 'client')
            ->where('folloup', 1);

        if (Auth::user()->role != 1) {
            $query->where('assigned_to', Auth::user()->id);
        }

        $counts['all'] = (clone $query)->count();
        $counts['call'] = (clone $query)->where('task_group', 'Call')->count();
        $counts['checklist'] = (clone $query)->where('task_group', 'Checklist')->count();
        $counts['review'] = (clone $query)->where('task_group', 'Review')->count();
        $counts['query'] = (clone $query)->where('task_group', 'Query')->count();
        $counts['urgent'] = (clone $query)->where('task_group', 'Urgent')->count();
        $counts['personal_task'] = (clone $query)->where('task_group', 'Personal Task')->count();

        return response()->json($counts);
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
     * Display the specified resource.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function show(Appointment $appointment)
    {
        $appointment=Appointment::with(['user','clients','service','natureOfEnquiry'])->where('id',$appointment->id)->first();
        return view('crm.appointments.show',compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Appointment $appointment)
    {
        $appointment=Appointment::with(['user','clients','service','natureOfEnquiry'])->where('id',$appointment->id)->first();
        return view('crm.appointments.edit',compact('appointment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            // 'user_id' => 'required|exists:admins,id',
            'client_id' => 'required|exists:admins,id',
            'date' => 'required',
            'time' => 'required',
            'title' => 'required',
            'description' => 'required',
            'invites' => 'required',
            'status' => 'required',
        ]);

        $data=$request->all();
        $data['time']= Carbon::parse($request->time)->format('H:i:s');
        $appointment->update($data);

        return redirect()->route('appointments.index')
                        ->with('success','Appointment updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id,Note $Note)
    {   // dd($id);
        $appointment =Note::find($id);
        $appointment->folloup = 0;
        $appointment->save();

        return redirect()->route('assignee.index')
        ->with('success','Assingee deleted successfully');
    }

    public function destroy_by_me( $id,Note $Note)
    {
        $appointment =Note::find($id);
        $appointment->folloup = 0;
        if( $appointment->save() ){
            $objs = new ActivitiesLog;
            $objs->client_id = $appointment->client_id;
            $objs->created_by = Auth::user()->id;

            $assign_user = Admin::find($appointment->assigned_to);
            if($assign_user){
                $assign_full_name = $assign_user->first_name." ".$assign_user->last_name;
                $objs->subject = 'deleted action for '.@$assign_full_name;
            } else {
                $objs->subject = 'deleted action ';
            }

            $objs->description = '<p>'.$appointment->description.'</p>';
            if(Auth::user()->id != @$appointment->assigned_to){
                $objs->use_for = @$appointment->assigned_to;
            } else {
                $objs->use_for = "";
            }
            $objs->followup_date = @$appointment->followup_datetime;
            $objs->task_group = @$appointment->task_group;
            $objs->save();
            return redirect()->route('assignee.assigned_by_me')->with('success','Activity deleted successfully');
        }
    }

    public function destroy_to_me( $id,Note $Note)
    {
        $appointment =Note::find($id);
        $appointment->folloup = 0;
        $appointment->save();
        return redirect()->route('assignee.assigned_to_me')->with('success','Assingee deleted successfully');
    }

    //incomplete activity remove
    public function destroy_activity($id,Note $Note)
    {
        $appointment = Note::find($id);//dd($appointment);
        $appointment->folloup = 0;
        if( $appointment->save() ){
            $objs = new ActivitiesLog;
            $objs->client_id = $appointment->client_id;
            $objs->created_by = Auth::user()->id;

            $assign_user = Admin::find($appointment->assigned_to);
            if($assign_user){
                $assign_full_name = $assign_user->first_name." ".$assign_user->last_name;
                $objs->subject = 'deleted action for '.@$assign_full_name;
            } else {
                $objs->subject = 'deleted action ';
            }

            $objs->description = '<p>'.$appointment->description.'</p>';
            if(Auth::user()->id != @$appointment->assigned_to){
                $objs->use_for = @$appointment->assigned_to;
            } else {
                $objs->use_for = "";
            }
            $objs->followup_date = @$appointment->followup_datetime;
            $objs->task_group = @$appointment->task_group;
            $objs->save();
            echo json_encode(array('success' => true, 'message' => 'Activity deleted successfully'));
            exit;
        }
    }

    //complete activity remove
    public function destroy_complete_activity( $id,Note $Note)
    {
        $appointment = Note::find($id);
        $appointment->folloup = 0;
        if( $appointment->save() ){
            $objs = new ActivitiesLog;
            $objs->client_id = $appointment->client_id;
            $objs->created_by = Auth::user()->id;

            $assign_user = Admin::find($appointment->assigned_to);
            if($assign_user){
                $assign_full_name = $assign_user->first_name." ".$assign_user->last_name;
                $objs->subject = 'deleted completed action for '.@$assign_full_name;
            } else {
                $objs->subject = 'deleted completed action ';
            }

            $objs->description = '<p>'.$appointment->description.'</p>';
            if(Auth::user()->id != @$appointment->assigned_to){
                $objs->use_for = @$appointment->assigned_to;
            } else {
                $objs->use_for = "";
            }
            $objs->followup_date = @$appointment->followup_datetime;
            $objs->task_group = @$appointment->task_group;
            $objs->save();
            return redirect()->route('assignee.action_completed')->with('success','Action deleted successfully');
        }
    }

    public function assignedetail(Request $request){
        $appointmentdetail = Appointment::with(['user','clients','service','assignee_user','natureOfEnquiry'])->where('id',$request->id)->first();
      ?>
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
                    <h4>Application Logs</h4>
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
        $objs->created_by = Auth::user()->id;
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
        $objs->created_by = Auth::user()->id;
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
        $o->sender_id = Auth::user()->id;
        $o->receiver_id = $request->assinee;
        $o->module_id = $request->id;
        $o->url = URL::to('/appointments');
        $o->notification_type = 'appointment';
        $o->message = $objs->title.' Appointments Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
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
    $objs->created_by = Auth::user()->id;
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
        $objs->created_by = Auth::user()->id;
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

    //Get All assignee list dropdown
    public function get_assignee_list(Request $request){
        $assignedto = $request->assignedto;

        $content1 = array();
        foreach(\App\Models\Admin::where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get() as $admin)
        {
            $branchname = \App\Models\Branch::where('id',$admin->office_id)->first();
            $option_value =  $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')';

            if($admin->id == $assignedto){
                $content1[] = '<option value="'.$admin->id.'" selected>'.$option_value.'</option>';
            } else {
                $content1[] = '<option value="'.$admin->id.'">'.$option_value.'</option>';
            }
        }
        $response['status'] 	= 	true;
        $response['message']	=	$content1;
       echo json_encode($response);
    }

    // Helper function to get assignee name
    protected function getAssigneeName($assigneeId)
    {
        $admin = \App\Models\Admin::find($assigneeId);
        return $admin ? $admin->first_name . ' ' . $admin->last_name : 'Unknown Assignee';
    }

    /**
     * Update a task (Note) based on the provided data.
     * This function marks the current task as complete and creates a new task with the provided information.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTask(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'id' => 'required|exists:notes,id', // Ensure the task ID exists in the notes table
            'client_id' => 'required|string', // Client ID (might need decoding if encoded)
            'assigned_to' => 'required|exists:admins,id', // Check against the admins table instead of users
            'description' => 'required|string',
            'followup_date' => 'required|date',
            'task_group' => 'required|string|in:Call,Checklist,Review,Query,Urgent', // Assuming task_group has specific values
        ]);

        try {
            // Log the incoming assigned_to value for debugging
            Log::info('Updating task with assigned_to: ' . $validated['assigned_to']);

            // Decode client_id if it was encoded
            $clientId = convert_uudecode(base64_decode($validated['client_id']));

            // Find the current task (Note) by ID
            $currentTask = Note::findOrFail($validated['id']);

            // Get assignee information for activity logs
            $admin_data_old = Admin::where('id', $currentTask->assigned_to)->first();
            $assignee_name_old = $admin_data_old ? $admin_data_old->first_name . " " . $admin_data_old->last_name : 'N/A';

            // Step 1: Mark the current task as complete
            $currentTask->update(['status' => '1']);

            // Step 2: Create activity log for task completion
            $completionLog = new ActivitiesLog;
            $completionLog->client_id = $currentTask->client_id;
            $completionLog->created_by = Auth::user()->id;
            $completionLog->subject = 'Task completed for ' . $assignee_name_old;
            $completionLog->description = '<p>' . $currentTask->description . '</p>';
            if (Auth::user()->id != $currentTask->assigned_to) {
                $completionLog->use_for = $currentTask->assigned_to;
            } else {
                $completionLog->use_for = "";
            }
            $completionLog->followup_date = $currentTask->updated_at;
            $completionLog->task_group = $currentTask->task_group;
            $completionLog->task_status = 1; // Marked as completed
            $completionLog->save();

            $admin_data = Admin::where('id', $validated['assigned_to'])->first();
            $assignee_name = $admin_data ? $admin_data->first_name . " " . $admin_data->last_name : 'N/A';

            // Step 3: Create a new task with the provided information
            $newTask = new Note;
            $newTask->user_id = Auth::user()->id;
            $newTask->client_id = $clientId;
            $newTask->assigned_to = $validated['assigned_to'];
            $newTask->description = $validated['description'];
            $newTask->followup_date = $validated['followup_date'];
            $newTask->task_group = $validated['task_group'];
            $newTask->type = 'client';
            $newTask->folloup = 1;
            $newTask->status = '0'; // New task is incomplete
            $taskUniqueId = 'group_' . uniqid('', true);
            $newTask->unique_group_id = $taskUniqueId; // Generate unique group ID for the new task
            $newTask->save();

            // Step 4: Create activity log for new task creation
            $newTaskLog = new ActivitiesLog;
            $newTaskLog->client_id = $clientId;
            $newTaskLog->created_by = Auth::user()->id;
            $newTaskLog->subject = 'New task assigned for ' . $assignee_name;
            $newTaskLog->description = '<p>' . $validated['description'] . '</p>';
            if (Auth::user()->id != $validated['assigned_to']) {
                $newTaskLog->use_for = $validated['assigned_to'];
            } else {
                $newTaskLog->use_for = "";
            }
            $newTaskLog->followup_date = $validated['followup_date'];
            $newTaskLog->task_group = $validated['task_group'];
            $newTaskLog->task_status = 0; // New task is incomplete
            $newTaskLog->save();

            return response()->json([
                'success' => true,
                'message' => 'Task completed and new task created successfully.'
            ], 200);

        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error updating task: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the task: ' . $e->getMessage()
            ], 500);
        }
    }

}