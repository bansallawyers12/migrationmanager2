<?php

namespace App\Http\Controllers\CRM\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Models\Admin;
use App\Models\Note;
use App\Models\ActivitiesLog;
use App\Models\ApplicationActivitiesLog;
use App\Models\OnlineForm;
use App\Models\ClientMatter;
use App\Traits\LogsClientActivity;
use Auth;
use Carbon\Carbon;

/**
| * ClientNotesController
| * 
| * Handles all note-related operations including creating, updating,
| * viewing, deleting, and pinning notes.
| * 
| * Maps to: resources/views/Admin/clients/tabs/notes.blade.php
| */
class ClientNotesController extends Controller
{
    use LogsClientActivity;

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
     * Create or update a note
     * 
     * @param Request $request
     * @return json
     */
    public function createnote(Request $request)
    { 
        //dd($request->all());
        $isUpdate = isset($request->noteid) && $request->noteid != '';
        $changedFields = [];
        $oldNote = null;
        
        if($isUpdate){
            $obj = Note::find($request->noteid);
            $oldNote = $obj->replicate(); // Keep a copy of old values for tracking changes
        }else{
            $obj = new Note;
            $obj->title = $request->title;
            $obj->matter_id = $request->matter_id;
        }

        // Track changes for updates
        if($isUpdate && $oldNote) {
            if($oldNote->title !== $request->title) {
                $changedFields['Title'] = [
                    'old' => $oldNote->title,
                    'new' => $request->title
                ];
            }
            if($oldNote->description !== $request->description) {
                $changedFields['Description'] = [
                    'old' => $oldNote->description ? substr(strip_tags($oldNote->description), 0, 50) . '...' : '(empty)',
                    'new' => $request->description ? substr(strip_tags($request->description), 0, 50) . '...' : '(empty)'
                ];
            }
            if($oldNote->task_group !== $request->task_group) {
                $changedFields['Note Type'] = [
                    'old' => $oldNote->task_group ?? 'Uncategorized',
                    'new' => $request->task_group ?? 'Uncategorized'
                ];
            }
        }

        $obj->client_id = $request->client_id;
        $obj->user_id = Auth::user()->id;
        $obj->description = $request->description;
        $obj->mail_id = $request->mailid;
        $obj->type = $request->vtype;
        /*if(isset($request->note_deadline_checkbox) && $request->note_deadline_checkbox != ''){
            if($request->note_deadline_checkbox == 1){
                $obj->note_deadline = $request->note_deadline;
            } else {
                $obj->note_deadline = NULL;
            }
        } else {
            $obj->note_deadline = NULL;
        }*/
        $obj->mobile_number = $request->mobileNumber; // Add this line
        $obj->task_group = $request->task_group;
        $saved = $obj->save();
		if($saved){
            if($request->vtype == 'client'){
                // Get note type for enhanced subject line with proper formatting
                $taskGroup = $request->task_group ?? 'General';
                $noteTypeFormatted = ucfirst(strtolower($taskGroup));
                
                // Get matter reference (like TGV_1)
                $matterReference = '';
                if(isset($request->matter_id) && $request->matter_id != "") {
                    $matter = ClientMatter::find($request->matter_id);
                    if($matter && $matter->client_unique_matter_no) {
                        $matterReference = $matter->client_unique_matter_no;
                    }
                }
                
                // If no matter reference found, try to get the latest active matter for this client
                if(empty($matterReference)) {
                    $latestMatter = ClientMatter::where('client_id', $request->client_id)
                        ->where('matter_status', 1)
                        ->orderBy('id', 'desc')
                        ->first();
                    if($latestMatter && $latestMatter->client_unique_matter_no) {
                        $matterReference = $latestMatter->client_unique_matter_no;
                    }
                }
                
                // Format subject line with action word
                if($isUpdate) {
                    // "updated Call Notes - TGV_1"
                    $subjectLine = !empty($matterReference) 
                        ? "updated {$noteTypeFormatted} Notes - {$matterReference}"
                        : "updated {$noteTypeFormatted} Notes";
                        
                    // Enhanced update logging with change tracking
                    if(!empty($changedFields)) {
                        $this->logClientActivityWithChanges(
                            $request->client_id,
                            $subjectLine,
                            $changedFields,
                            'note'
                        );
                    } else {
                        // Remove redundant note type from description
                        $description = '<p>'.substr(strip_tags($request->description), 0, 150).'...</p>';
                        $this->logClientActivity(
                            $request->client_id,
                            $subjectLine,
                            $description,
                            'note'
                        );
                    }
                } else {
                    // "added Call Notes - TGV_1"
                    $subjectLine = !empty($matterReference) 
                        ? "added {$noteTypeFormatted} Notes - {$matterReference}"
                        : "added {$noteTypeFormatted} Notes";
                        
                    // Enhanced create logging - Remove redundant note type from description
                    $description = '<p>'.substr(strip_tags($request->description), 0, 150).'...</p>';
                    $this->logClientActivity(
                        $request->client_id,
                        $subjectLine,
                        $description,
                        'note'
                    );
                }

                //Update date in client matter table
                if( isset($request->matter_id) && $request->matter_id != ""){
                    $obj1 = ClientMatter::find($request->matter_id);
                    $obj1->updated_at = date('Y-m-d H:i:s');
                    $obj1->save();
                }
            }
            $response['status'] 	= 	true;
            if($isUpdate){
                $response['message']	=	'You have successfully updated Note';
            }else{
                $response['message']	=	'You have successfully added Note';
            }
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
        echo json_encode($response);
	}

    /**
     * Update note datetime
     * 
     * @param Request $request
     * @return json
     */
    public function updateNoteDatetime(Request $request)
    {
        $note_id = $request->note_id;
        $datetime = $request->datetime;
        
        try {
            $carbonDateTime = Carbon::parse($datetime);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid date and time format'
            ]);
        }
        
        // Find note with specific conditions
        $note = Note::where('id', $note_id)
            ->whereNull('assigned_to')
            ->whereNull('unique_group_id')
            ->first();
        
        if($note){
            $note->updated_at = $carbonDateTime; // Carbon instance
            $saved = $note->save();
            
            if($saved){
                $response['status'] = true;
                $response['message'] = 'Date and time updated successfully';
            } else {
                $response['status'] = false;
                $response['message'] = 'Failed to update date and time';
            }
        } else {
            $response['status'] = false;
            $response['message'] = 'Note not found or does not meet the criteria';
        }
        
        return response()->json($response);
    }

    /**
     * Get note details for editing
     * 
     * @param Request $request
     * @return json
     */
    public function getnotedetail(Request $request)
    {
		$note_id = $request->note_id; //dd($note_id);
		if(Note::where('id',$note_id)->exists()){
			$data = Note::select('title','description','task_group')->where('id',$note_id)->first();
			$response['status'] 	= 	true;
			$response['data']	=	$data;
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

    /**
     * View note details
     * 
     * @param Request $request
     * @return json
     */
    public function viewnotedetail(Request $request)
    {
		$note_id = $request->note_id;
		if(Note::where('id',$note_id)->exists()){
			$data = Note::select('title','description','user_id','updated_at')->where('id',$note_id)->first();
			$admin = Admin::where('id', $data->user_id)->first();
			$s = substr(@$admin->first_name, 0, 1);
			$data->admin = $s;
			$response['status'] 	= 	true;
			$response['data']	=	$data;
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

    /**
     * View application note details
     * 
     * @param Request $request
     * @return json
     */
    public function viewapplicationnote(Request $request)
    {
		$note_id = $request->note_id;
		if(ApplicationActivitiesLog::where('type','note')->where('id',$note_id)->exists()){
			$data = ApplicationActivitiesLog::select('title','description','user_id','updated_at')->where('type','note')->where('id',$note_id)->first();
			$admin = Admin::where('id', $data->user_id)->first();
			$s = substr(@$admin->first_name, 0, 1);
			$data->admin = $s;
			$response['status'] 	= 	true;
			$response['data']	=	$data;
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

    /**
     * Get notes list for Notes Tab (redesigned)
     * 
     * @param Request $request
     * @return html
     */
    public function getnotes(Request $request)
    {
        $client_id = $request->clientid;
        $type = $request->type;
        $task_group = $request->task_group;
        //if($task_group == ''){
            $notelist = Note::where('client_id',$client_id)->whereNull('assigned_to')->where('type',$type)->orderby('pin', 'DESC')->orderBy('created_at', 'DESC')->get();
        /*}else{
            $notelist = Note::where('client_id',$client_id)->whereNull('assigned_to')->where('type',$type)->where('task_group',$task_group)->orderby('pin', 'DESC')->orderBy('created_at', 'DESC')->get();
        }*/
        ob_start();
        foreach($notelist as $list){
            $admin = Admin::where('id', $list->user_id)->first();

            // Determine type label and color
            if($list->task_group === null || $list->task_group === '') {
                // Handle NULL or empty task_group - assign to "Uncategorized"
                $typeLabel = 'Uncategorized';
                $typeClass = 'note-type-uncategorized';
            } else {
                $type11 = strtolower($list->task_group);
                $typeLabel = 'Others';
                $typeClass = 'note-type-others';

                if(strpos($type11, 'call') !== false) { $typeLabel = 'Call'; $typeClass = 'note-type-call'; }
                else if(strpos($type11, 'email') !== false) { $typeLabel = 'Email'; $typeClass = 'note-type-email'; }
                else if(strpos($type11, 'in-person') !== false) { $typeLabel = 'In-Person'; $typeClass = 'note-type-inperson'; }
                else if(strpos($type11, 'others') !== false) { $typeLabel = 'Others'; $typeClass = 'note-type-others'; }
                else if(strpos($type11, 'attention') !== false) { $typeLabel = 'Attention'; $typeClass = 'note-type-attention'; }
            }
            //$desc = strip_tags($list->description);
            ?>
            <div class="note-card-redesign <?php if($list->pin == 1) echo 'pinned'; ?>" data-matterid="<?php echo $list->matter_id; ?>" id="note_id_<?php echo $list->id; ?>" data-id="<?php echo $list->id;?>" data-type="<?php echo $typeLabel;?>">
                <?php if($list->pin == 1) { ?>
                    <div class="pined_note">
                        <i class="fa fa-thumb-tack" aria-hidden="true"></i>
                    </div>
                <?php } ?>    
            <div class="note-card-info">
                    <span class="note-type-label <?php echo $typeClass;?>"><?php echo $typeLabel; ?></span>
                    <span class="author-name-created"><?php echo $admin->first_name ?? 'NA' ;?> <?php echo $admin->last_name ?? 'NA' ;?></span>
                    <span class="author-updated-date-time"><?php echo date('d/m/Y h:i A', strtotime($list->updated_at));?></span>
                </div>

                <!--<div class="note-content-redesign"><?php //echo nl2br(htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'));?></div>-->
                <div class="note-content-redesign">
                    <?php 
                    if (!empty($list->description)) {
                        $description = $list->description;

                        // Check for unwanted Word/Office XML markup
                        if (strpos($description, '<xml>') !== false || strpos($description, '<o:OfficeDocumentSettings>') !== false) {
                            $finalDescription = htmlentities($description);
                        } else {
                            $finalDescription = $description;
                        }
                    } else {
                        $finalDescription = '';
                    }
                    ?>
                    <?php echo $finalDescription; ?>
                </div>
                <div class="note-toggle-btn-div">
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle note-toggle-btn-div-type" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item opennoteform" data-id="<?php echo $list->id;?>" href="javascript:;">Edit</a>
                            <?php if( Auth::user()->role == 1 || Auth::user()->role == 16 ) { ?>
                                <a class="dropdown-item editdatetime" data-id="<?php echo $list->id;?>" href="javascript:;">Edit Date Time</a>
                            <?php }?>

                            <a data-id="<?php echo $list->id;?>"  data-href="deletenote" class="dropdown-item deletenote" href="javascript:;">Delete</a>
                            <?php if($list->pin == 1) { ?>
                                <a data-id="<?php echo $list->id;?>" class="dropdown-item pinnote" href="javascript:;">Unpin</a>
                            <?php } else { ?>
                                <a data-id="<?php echo $list->id;?>" class="dropdown-item pinnote" href="javascript:;">Pin</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        return ob_get_clean();
    }

    /**
     * Delete a note
     * 
     * @param Request $request
     * @return json
     */
    public function deletenote(Request $request)
    {
		$note_id = $request->note_id;
		if(Note::where('id',$note_id)->exists()){
			$data = Note::select('client_id','title','description','task_group','type')->where('id',$note_id)->first();
			$res = DB::table('notes')->where('id', @$note_id)->delete();
			if($res){
				if($data->type == 'client'){
                    // Enhanced delete logging with note type
                    $taskGroup = $data->task_group ?? 'General';
                    $noteTypeFormatted = ucfirst(strtolower($taskGroup));
                    
                    // Remove redundant note type from description
                    $description = '<p>'.substr(strip_tags($data->description), 0, 150).'...</p>';
                    
                    // Format as "deleted Call Notes"
                    $this->logClientActivity(
                        $data->client_id,
                        "deleted {$noteTypeFormatted} Notes",
                        $description,
                        'note'
                    );
				}
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

    /**
     * Pin or unpin a note
     * 
     * @param Request $request
     * @return json
     */
    public function pinnote(Request $request)
    {
		$requestData = $request->all();

		if(Note::where('id',$requestData['note_id'])->exists()){
			$note = Note::where('id',$requestData['note_id'])->first();
			if($note->pin == 0){
				$obj = Note::find($note->id);
				$obj->pin = 1;
				$saved = $obj->save();
			}else{
				$obj = Note::find($note->id);
				$obj->pin = 0;
				$saved = $obj->save();
			}
			$response['status'] 				= 	true;
			$response['message']			=	'Pin Option added successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Record not found';
		}
		echo json_encode($response);
	}

    /**
     * Save previous visa information
     * 
     * @param Request $request
     * @return redirect
     */
    public function saveprevvisa(Request $request)
    {
    	    $requestData 		= 	$request->all();
    	     $obj = Admin::find($requestData['client_id']);
    	    $pr = array();
    	    $i = 0;
    	  $start_date =  $requestData['prev_visa']['start_date'];
    	   $end_date =  $requestData['prev_visa']['end_date'];
    	    $place =  $requestData['prev_visa']['place'];
    	     $person =  $requestData['prev_visa']['person'];

    	    foreach($requestData['prev_visa']['name'] as  $prev_visa){

    	       $pr[] = array(
    	                'name' => $prev_visa,
    	                'start_date' => $start_date[$i],
    	                'end_date' =>$end_date[$i],
    	                'place' =>$place[$i],
    	                'person' =>$person[$i],
    	            );
    	            $i++;
    	    }

    	     $obj->prev_visa = json_encode($pr);

    	     $save = $obj->save();
    	     if($save){
    	         return Redirect::to('/clients/detail/'.base64_encode(convert_uuencode(@$requestData['client_id'])))->with('success', 'Previous Visa Updated Successfully');
    	     }else{
    	         return redirect()->back()->with('error', config('constants.server_error'));
    	     }
    	}

    /**
     * Save online form data
     * 
     * @param Request $request
     * @return redirect
     */
    public function saveonlineform(Request $request)
    {
    	   $requestData 		= 	$request->all();
    	   if(OnlineForm::where('client_id', $requestData['client_id'])->where('type', $requestData['type'])->exists()){
    	     $OnlineForm =  OnlineForm::where('client_id', $requestData['client_id'])->where('type', $requestData['type'])->first();
    	     $obj = OnlineForm::find($OnlineForm->id);
    	   }else{
    	       $obj = New OnlineForm;
    	   }

		   $parent_dob = '';
	        if($requestData['parent_dob'] != ''){
	           $dobs = explode('/', $requestData['parent_dob']);
	          $parent_dob = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
	        }

			 $parent_dob_2 = '';
	        if($requestData['parent_dob_2'] != ''){
	           $dobs = explode('/', $requestData['parent_dob_2']);
	          $parent_dob_2 = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
	        }
			$sibling_dob = '';
	        if($requestData['sibling_dob'] != ''){
	           $dobs = explode('/', $requestData['sibling_dob']);
	          $sibling_dob = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
	        }
			$sibling_dob_2 = '';
	        if($requestData['sibling_dob_2'] != ''){
	           $dobs = explode('/', $requestData['sibling_dob_2']);
	          $sibling_dob_2 = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
	        }

                $obj->client_id = $requestData['client_id'];
                $obj->type = $requestData['type'];
                $obj->info_name = $requestData['info_name'];
                $obj->main_lang = implode(',', $requestData['main_lang']);
                $obj->marital_status = $requestData['marital_status'];
                $obj->mobile = $requestData['mobile'];
                $obj->curr_address = $requestData['curr_address'];
                $obj->email = $requestData['email'];
                $obj->parent_name = $requestData['parent_name'];
                $obj->parent_dob = $parent_dob;
                $obj->parent_occ = $requestData['parent_occ'];
                $obj->parent_country = $requestData['parent_country'];
                $obj->parent_name_2 = $requestData['parent_name_2'];
                $obj->parent_dob_2 = $parent_dob_2;
                $obj->parent_occ_2 = $requestData['parent_occ_2'];
                $obj->parent_country_2 = $requestData['parent_country_2'];
                $obj->sibling_name = $requestData['sibling_name'];
                $obj->sibling_dob = $sibling_dob;
                $obj->sibling_occ = $requestData['sibling_occ'];
                $obj->sibling_gender = $requestData['sibling_gender'];
                $obj->sibling_country = $requestData['sibling_country'];
                $obj->sibling_marital = $requestData['sibling_marital'];
                $obj->sibling_name_2 = $requestData['sibling_name_2'];
                $obj->sibling_dob_2 = $sibling_dob_2;
                $obj->sibling_occ_2 = $requestData['sibling_occ_2'];
                $obj->sibling_gender_2 = $requestData['sibling_gender_2'];
                $obj->sibling_country_2 = $requestData['sibling_country_2'];
                $obj->sibling_marital_2 = $requestData['sibling_marital_2'];
                $obj->held_visa = $requestData['held_visa'];
                $obj->visa_refused = $requestData['visa_refused'];
                $obj->traveled = $requestData['traveled'];

    	     $save = $obj->save();
    	     if($save){
    	         return Redirect::to('/clients/detail/'.base64_encode(convert_uuencode(@$requestData['client_id'])))->with('success', 'Record Updated Successfully');
    	     }else{
    	         return redirect()->back()->with('error', config('constants.server_error'));
    	     }
    	}
}

