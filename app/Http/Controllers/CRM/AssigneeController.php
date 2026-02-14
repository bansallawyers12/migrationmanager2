<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder;

// WARNING: Appointment and AppointmentLog models have been removed - old appointment system deleted
// use App\Models\Appointment;
use App\Models\Note;
// use App\Models\AppointmentLog;
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

    //All action lists except completed = Closed
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Note::class)
            ->allowedSorts(['first_name', 'followup_date', 'task_group', 'created_at'])
            ->with(['noteUser','noteClient','lead.service','assigned_user'])
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

    //All completed action lists
    public function completed(Request $request)
    {
        if(Auth::user()->role == 1){
            $assignees = \App\Models\Note::with(['noteUser','noteClient','lead.service','assigned_user'])->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status','1')->orderBy('created_at', 'desc')->latest()->paginate(20); //where('status','like','Closed')
        }else{
            $assignees = \App\Models\Note::with(['noteUser','noteClient','lead.service','assigned_user'])->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status','1')->orderBy('created_at', 'desc')->latest()->paginate(20);
        }  //dd( $assignees);
        return view('crm.assignee.completed',compact('assignees'))
         ->with('i', (request()->input('page', 1) - 1) * 20);
    }

    //Update action to be complete
    public function updateActionCompleted(Request $request)
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
                
                // Prepare description with completion notes (completion notes appear first)
                $description = '';
                if (!empty($data['completion_notes'])) {
                    $description .= '<p>';
                    $description .= '<i class="fas fa-ellipsis-v convert-activity-to-note" ';
                    $description .= 'style="cursor: pointer; color: #6c757d;" ';
                    $description .= 'title="Convert to Note" ';
                    $description .= 'data-activity-id="" ';
                    $description .= 'data-activity-subject="Completion Notes" ';
                    $description .= 'data-activity-description="'.htmlspecialchars($data['completion_notes'], ENT_QUOTES).'" ';
                    $description .= 'data-activity-created-by="'.Auth::user()->id.'" ';
                    $description .= 'data-activity-created-at="'.now().'" ';
                    $description .= 'data-client-id="'.$note_data['client_id'].'"></i></p>';
                    $description .= '<p>'.nl2br(htmlspecialchars($data['completion_notes'])).'</p>';
                    $description .= '<hr>';
                }
                $description .= '<p>'.@$note_data['description'].'</p>';
                
                $objs = new ActivitiesLog;
                $objs->client_id = $note_data['client_id'];
                $objs->created_by = Auth::user()->id;
                $objs->subject = 'completed action for '.@$assignee_name;
                $objs->description = $description;
                if(Auth::user()->id != @$note_data['assigned_to']){
                    $objs->use_for = @$note_data['assigned_to'];
                } else {
                    $objs->use_for = null;
                }
                $objs->followup_date = @$note_data['updated_at'];
                $objs->task_group = @$note_data['task_group'];
                $objs->task_status = 1; //marked completed
                $objs->pin = 0;
                $objs->save();
            }
            $response['status'] 	= 	true;
            $response['message']	=	'Action completed successfully';
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
        }
        echo json_encode($response);
    }

    //Update action to be not complete
    public function updateActionNotCompleted(Request $request)
    {
        $data = $request->all(); //dd($data['id']);
        $note = Note::where('unique_group_id',$data['unique_group_id'])
                    ->whereNotNull('assigned_to')
                    ->whereNotNull('unique_group_id')
                    ->update(['status'=>'0']);
        if($note){
            $response['status'] 	= 	true;
            $response['message']	=	'Action updated successfully';
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
        }
        echo json_encode($response);
    }

     //All assigned by me action list which r incomplete
     public function assigned_by_me(Request $request)
     {
        if(Auth::user()->role == 1){
             $assignees_notCompleted = \App\Models\Note::with(['noteUser','noteClient','assigned_user'])->where('status','<>',1)->where('type','client')->whereNotNull('client_id')->where('folloup',1)->orderBy('created_at', 'desc')->latest()->paginate(20);
        } else {
             $assignees_notCompleted = \App\Models\Note::with(['noteUser','noteClient','assigned_user'])->where('status','<>',1)->where('user_id',Auth::user()->id)->where('type','client')->where('folloup',1)->orderBy('created_at', 'desc')->latest()->paginate(20);
        }
         #dd($assignees_notCompleted);
         return view('crm.assignee.assign_by_me',compact('assignees_notCompleted'))
          ->with('i', (request()->input('page', 1) - 1) * 20);
     }

    //All assigned to me action list
    public function assigned_to_me(Request $request)
    {
        if(Auth::user()->role == 1){
            $assignees_notCompleted = \App\Models\Note::with(['noteUser','noteClient','lead.service','assigned_user'])->where('status','<>','1')->where('assigned_to',Auth::user()->id)->where('type','client')->whereNotNull('client_id')->where('folloup',1)->orderBy('created_at', 'desc')->latest()->paginate(20);//where('status','not like','Closed')

            $assignees_completed = \App\Models\Note::with(['noteUser','noteClient','lead.service','assigned_user'])->where('status','1')->where('assigned_to',Auth::user()->id)->where('type','client')->whereNotNull('client_id')->where('folloup',1)->orderBy('created_at', 'desc')->latest()->paginate(20);
        }else{
            $assignees_notCompleted = \App\Models\Note::with(['noteUser','noteClient','lead.service','assigned_user'])->where('status','<>','1')->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->orderBy('created_at', 'desc')->latest()->paginate(20);

            $assignees_completed = \App\Models\Note::with(['noteUser','noteClient','lead.service','assigned_user'])->where('status','1')->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->orderBy('created_at', 'desc')->latest()->paginate(20);
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
                'assigned_user'
            ])
            ->where('status', 1)
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

        // Get action group counts with single optimized query
        $taskGroupCounts = $this->getCompletedActionGroupCounts($user);
        
        //dd(count($assignees_completed));
        return view('crm.assignee.action_completed',compact('assignees_completed','task_group','taskGroupCounts'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    /**
     * Get completed action counts grouped by task_group (field name kept for database compatibility)
     * Uses a single query with GROUP BY for better performance
     * 
     * @param \App\Models\Admin $user
     * @return array
     */
    private function getCompletedActionGroupCounts($user)
    {
        $query = \App\Models\Note::where('status', 1)
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

        // Initialize all action groups with default count of 0
        $counts = [
            'All' => array_sum($groupedCounts),
            'Call' => $groupedCounts['Call'] ?? 0,
            'Checklist' => $groupedCounts['Checklist'] ?? 0,
            'Review' => $groupedCounts['Review'] ?? 0,
            'Query' => $groupedCounts['Query'] ?? 0,
            'Urgent' => $groupedCounts['Urgent'] ?? 0,
            'Personal Action' => $groupedCounts['Personal Action'] ?? 0,
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
                        // Handle special case for personal_action to convert underscore to space
                        $actionGroup = $request->filter;
                        if ($actionGroup == 'personal_action') {
                            $actionGroup = 'Personal Action';
                        } else {
                            $actionGroup = ucfirst($actionGroup);
                        }
                        $query->where('notes.task_group', $actionGroup);
                    }
                }

                // Note: Search functionality is now handled by Yajra DataTables filterColumn() definitions
                // The custom 'd.search' parameter from frontend is handled by DataTables' built-in search

                // Apply sorting
                $orderDirection = in_array($request->input('order.0.dir'), ['asc', 'desc']) 
                    ? $request->input('order.0.dir') 
                    : 'desc';

                if ($request->has('order')) {
                    $orderColumnIndex = (int) $request->order[0]['column'];
                    $columns = $request->columns;

                    $columnName = $columns[$orderColumnIndex]['name'] ?? '';

                    // Map DataTables column names to database columns
                    switch ($columnName) {
                        case 'assigner_name':
                            $query->leftJoin('admins as assigner_admins', 'notes.user_id', '=', 'assigner_admins.id')
                                ->orderByRaw("COALESCE(assigner_admins.first_name, '') " . $orderDirection . ", COALESCE(assigner_admins.last_name, '') " . $orderDirection);
                            break;
                        case 'client_reference':
                            $query->leftJoin('admins as client_admins', 'notes.client_id', '=', 'client_admins.id')
                                ->orderByRaw("COALESCE(client_admins.first_name, 'zzz') " . $orderDirection . ", COALESCE(client_admins.last_name, '') " . $orderDirection);
                            break;
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
                    ->addColumn('done_action', function($data) {
                        $done_action = '<input type="radio" class="complete_task" data-toggle="tooltip" title="Mark Complete!" data-id="'.$data->id.'" data-unique_group_id="'.$data->unique_group_id.'">';
                        return $done_action;
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
                            if ($data->noteClient && $data->client_id) {
                                $firstName = Utf8Helper::safeSanitize($data->noteClient->first_name ?? '');
                                $lastName = Utf8Helper::safeSanitize($data->noteClient->last_name ?? '');
                                $clientId = Utf8Helper::safeSanitize($data->noteClient->client_id ?? '');
                                
                                $user_name = $firstName . ' ' . $lastName;
                                $user_name .= "<br>";
                                $client_encoded_id = base64_encode(convert_uuencode(@$data->client_id));
                                $user_name .= '<a href="'.url('/clients/detail/'.$client_encoded_id).'" target="_blank">'.$clientId.'</a>';
                            } else {
                                // Personal Action - no client assigned
                                $user_name = '<span class="badge badge-info">Personal Action</span>';
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

                            // Update Action button - available for all actions including Personal Actions
                            // Use direct htmlspecialchars instead of Utf8Helper wrapper to avoid redundant sanitization
                            $safe_description = htmlspecialchars(Utf8Helper::safeSanitize($list->description ?? ''), ENT_QUOTES, 'UTF-8');
                            $safe_task_group = htmlspecialchars(Utf8Helper::safeSanitize($list->task_group ?? ''), ENT_QUOTES, 'UTF-8');
                            
                            // For personal actions, client_id will be null, so use empty string for encoded value
                            $encoded_client_id = $list->client_id ? base64_encode(convert_uuencode($list->client_id)) : '';
                            
                            $actionBtn .= '<button type="button" data-assignedto="'.$list->assigned_to.'" data-noteid="'.$safe_description.'" data-taskid="'.$list->id.'" data-taskgroupid="'.$safe_task_group.'" data-followupdate="'.$current_date1.'" data-clientid="'.$encoded_client_id.'" class="btn btn-primary btn-block update_task" data-role="popover" style="width: 40px;display: inline;margin-top:0px;"><i class="fa fa-edit" aria-hidden="true"></i></button>';

                            // Delete button removed from action tab

                            return $actionBtn;
                        } catch (\Exception $e) {
                            return '';
                        }
                    })
                    ->rawColumns(['done_action', 'client_reference', 'note_description', 'action'])
                    // Define how to filter computed columns
                    ->filterColumn('assigner_name', function($query, $keyword) {
                        $keywordLower = strtolower($keyword);
                        $query->whereHas('noteUser', function($q) use ($keywordLower) {
                            $q->whereRaw('LOWER(first_name) LIKE ?', ['%' . $keywordLower . '%'])
                              ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . $keywordLower . '%'])
                              ->orWhereRaw("LOWER(COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) LIKE ?", ['%' . $keywordLower . '%']);
                        });
                    })
                    ->filterColumn('client_reference', function($query, $keyword) {
                        $keywordLower = strtolower($keyword);
                        $query->whereHas('noteClient', function($q) use ($keywordLower) {
                            $q->whereRaw('LOWER(client_id) LIKE ?', ['%' . $keywordLower . '%'])
                              ->orWhereRaw('LOWER(first_name) LIKE ?', ['%' . $keywordLower . '%'])
                              ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . $keywordLower . '%']);
                        });
                    });

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
            'personal_action' => 0
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
        $counts['personal_action'] = (clone $query)->where('task_group', 'Personal Action')->count();

        return response()->json($counts);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  Note  $Note
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

            $assign_user = \App\Models\Staff::find($appointment->assigned_to);
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
                $objs->use_for = null;
            }
            $objs->followup_date = @$appointment->followup_datetime;
            $objs->task_group = @$appointment->task_group;
            $objs->task_status = 0;
            $objs->pin = 0;
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

            $assign_user = \App\Models\Staff::find($appointment->assigned_to);
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
                $objs->use_for = null;
            }
            $objs->followup_date = @$appointment->followup_datetime;
            $objs->task_group = @$appointment->task_group;
            $objs->task_status = 0;
            $objs->pin = 0;
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

            $assign_user = \App\Models\Staff::find($appointment->assigned_to);
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
                $objs->use_for = null;
            }
            $objs->followup_date = @$appointment->followup_datetime;
            $objs->task_group = @$appointment->task_group;
            $objs->task_status = 0;
            $objs->pin = 0;
            $objs->save();
            return redirect()->route('assignee.action_completed')->with('success','Action deleted successfully');
        }
    }


    //Get All assignee list dropdown
    public function get_assignee_list(Request $request){
        $assignedto = $request->assignedto;

        $content1 = array();
        foreach(\App\Models\Staff::where('status',1)->orderby('first_name','ASC')->get() as $admin)
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
        $staff = \App\Models\Staff::find($assigneeId);
        return $staff ? $staff->first_name . ' ' . $staff->last_name : 'Unknown Assignee';
    }

    /**
     * Update an action (Note) based on the provided data.
     * This function marks the current action as complete and creates a new action with the provided information.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAction(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'id' => 'required|exists:notes,id', // Ensure the action ID exists in the notes table
            'client_id' => 'nullable|string', // Client ID is optional for Personal Actions
            'assigned_to' => 'required|exists:staff,id',
            'description' => 'required|string',
            'task_group' => 'required|string|in:Call,Checklist,Review,Query,Urgent,Personal Action', // Include Personal Action
        ]);

        try {
            // Log the incoming assigned_to value for debugging
            Log::info('Updating action with assigned_to: ' . $validated['assigned_to']);

            // Decode client_id if it was encoded and not empty
            $clientId = null;
            if (!empty($validated['client_id'])) {
                $clientId = convert_uudecode(base64_decode($validated['client_id']));
            }

            // Find the current action (Note) by ID
            $currentAction = Note::findOrFail($validated['id']);

            // Get assignee information for activity logs
            $admin_data_old = Admin::where('id', $currentAction->assigned_to)->first();
            $assignee_name_old = $admin_data_old ? $admin_data_old->first_name . " " . $admin_data_old->last_name : 'N/A';

            // Step 1: Mark the current action as complete
            $currentAction->update(['status' => '1']);

            // Step 2: Create activity log for action completion (only if there's a client_id)
            if ($currentAction->client_id) {
                $completionLog = new ActivitiesLog;
                $completionLog->client_id = $currentAction->client_id;
                $completionLog->created_by = Auth::user()->id;
                $completionLog->subject = 'Action completed for ' . $assignee_name_old;
                $completionLog->description = '<p>' . $currentAction->description . '</p>';
                if (Auth::user()->id != $currentAction->assigned_to) {
                    $completionLog->use_for = $currentAction->assigned_to;
                } else {
                    $completionLog->use_for = null;
                }
                $completionLog->followup_date = $currentAction->updated_at;
                $completionLog->task_group = $currentAction->task_group;
                $completionLog->task_status = 1; // Marked as completed
                $completionLog->pin = 0;
                $completionLog->save();
            }

            $admin_data = Admin::where('id', $validated['assigned_to'])->first();
            $assignee_name = $admin_data ? $admin_data->first_name . " " . $admin_data->last_name : 'N/A';

            // Use the original action's followup_date or today's date if not available
            $followupDate = $currentAction->followup_date ?: date('Y-m-d');

            // Step 3: Create a new action with the provided information
            $newAction = new Note;
            $newAction->user_id = Auth::user()->id;
            $newAction->client_id = $clientId;
            $newAction->assigned_to = $validated['assigned_to'];
            $newAction->description = $validated['description'];
            $newAction->followup_date = $followupDate;
            $newAction->task_group = $validated['task_group'];
            $newAction->type = 'client';
            $newAction->folloup = 1;
            $newAction->status = '0'; // New action is incomplete
            $newAction->pin = 0; // Set pin to 0 (required field)
            $actionUniqueId = 'group_' . uniqid('', true);
            $newAction->unique_group_id = $actionUniqueId; // Generate unique group ID for the new action
            $newAction->save();

            // Step 4: Create activity log for new action creation (only if there's a client_id)
            if ($clientId) {
                $newActionLog = new ActivitiesLog;
                $newActionLog->client_id = $clientId;
                $newActionLog->created_by = Auth::user()->id;
                $newActionLog->subject = 'New action assigned for ' . $assignee_name;
                $newActionLog->description = '<p>' . $validated['description'] . '</p>';
                if (Auth::user()->id != $validated['assigned_to']) {
                    $newActionLog->use_for = $validated['assigned_to'];
                } else {
                    $newActionLog->use_for = null;
                }
                $newActionLog->followup_date = $followupDate;
                $newActionLog->task_group = $validated['task_group'];
                $newActionLog->task_status = 0; // New action is incomplete
                $newActionLog->pin = 0;
                $newActionLog->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Action completed and new action created successfully.'
            ], 200);

        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error updating action: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the action: ' . $e->getMessage()
            ], 500);
        }
    }

}