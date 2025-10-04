@extends('layouts.admin_client_detail')
@section('title', 'Client Detail')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{URL::asset('css/bootstrap-datepicker.min.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">
<link rel="stylesheet" href="{{ URL::asset('css/client-detail.css') }}">

<?php
use App\Http\Controllers\Controller;
?>
<div class="crm-container">
    <!-- Client Navigation Sidebar -->
    <aside class="client-navigation-sidebar">
        <div class="sidebar-header">
            <div class="client-info">
                <h3 class="client-id">
                    <?php
                    if($id1) { //if client unique reference id is present in url
                        $matter_info_arr = \App\Models\ClientMatter::select('client_unique_matter_no')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                    ?>
                        {{$fetchedData->client_id}}-{{$matter_info_arr->client_unique_matter_no}}
                    <?php
                    } else {
                        $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                        if($matter_cnt >0){
                            $matter_info_arr = \App\Models\ClientMatter::select('client_unique_matter_no')->where('client_id',$fetchedData->id)->where('matter_status',1)->orderBy('id', 'desc')->first();
                        ?>
                            {{$fetchedData->client_id}}-{{$matter_info_arr->client_unique_matter_no}}
                        <?php
                        } else {
                        ?>
                            {{$fetchedData->client_id}}
                        <?php
                        }
                    } ?>
                </h3>
                <p class="client-name">
                    {{$fetchedData->first_name}} {{$fetchedData->last_name}} 
                    <a href="{{URL::to('/admin/clients/edit/'.base64_encode(convert_uuencode(@$fetchedData->id)))}}" title="Edit" class="client-name-edit">
                        <i class="fa fa-edit"></i>
                    </a>
                </p>
                
                <!-- Action Icons (left) and Client Portal Toggle (right) -->
                <div class="sidebar-actions-row">
                    <!-- Action Icons -->
                    <div class="client-actions">
                        <a href="javascript:;" class="create_note_d" datatype="note" title="Add Notes"><i class="fas fa-plus"></i></a>
                        <a href="javascript:;" data-id="{{@$fetchedData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="clientemail" title="Compose Mail"><i class="fa fa-envelope"></i></a>
                        <a href="javascript:;" datatype="not_picked_call" class="not_picked_call" title="Not Picked Call"><i class="fas fa-mobile-alt"></i></a>
                        @if($fetchedData->is_archived == 0)
                            <a class="arcivedval" href="javascript:;" onclick="arcivedAction({{$fetchedData->id}}, 'admins')" title="Archive"><i class="fas fa-archive"></i></a>
                        @else
                            <a class="arcivedval archived-active" href="javascript:;" onclick="arcivedAction({{$fetchedData->id}}, 'admins')" title="UnArchive"><i class="fas fa-archive"></i></a>
                        @endif
                    </div>
                    
                    <!-- Client Portal Toggle -->
                    <?php
                    // Check if client has any records in client_matters table
                    $client_matters_exist = DB::table('client_matters')
                        ->where('client_id', $fetchedData->id)
                        ->exists();
                    ?>
                    @if($client_matters_exist)
                    <div class="sidebar-portal-toggle" title="Client Portal">
                        <label class="toggle-switch">
                            <input type="checkbox" id="client-portal-toggle" 
                                   data-client-id="{{ $fetchedData->id}}" 
                                   {{ isset($fetchedData->cp_status) && $fetchedData->cp_status == 1 ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Client/Lead Toggle Buttons -->
            <div class="sidebar-client-lead-buttons">
                <a class="status-btn status-btn-client convertLeadToClient <?php if($fetchedData->type == 'client'){ echo 'active'; }?>" href="javascript:;" role="button">Client</a>
                <a href="javascript:;" class="status-btn status-btn-lead <?php if($fetchedData->type == 'lead'){ echo 'active'; } ?>">Lead</a>
            </div>
            
            <!-- Matter Selection Dropdown in Sidebar -->
            <div class="sidebar-matter-selection">
                <?php
                $assign_info_arr = \App\Models\Admin::select('type')->where('id',@$fetchedData->id)->first();
                ?>
                @if($assign_info_arr->type == 'client')
                    <?php
                    if($id1)
                    {
                        //if client_unique_matter_no is present in url
                        $matter_cnt = DB::table('client_matters')
                        ->select('client_matters.id')
                        ->where('client_matters.client_id',@$fetchedData->id)
                        ->where('client_matters.client_unique_matter_no',$id1)
                        ->where('client_matters.matter_status',1)
                        ->whereNotNull('client_matters.sel_matter_id')
                        ->count();
                        if( $matter_cnt >0 )
                        {
                            // Fetch all matters, but we'll sort them in Blade to prioritize the URL matter
                            $matter_list_arr = DB::table('client_matters')
                            ->leftJoin('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
                            ->select('client_matters.id','client_matters.client_unique_matter_no','matters.title')
                            ->where('client_matters.client_id',@$fetchedData->id)
                            ->where('client_matters.matter_status',1)
                            ->where('client_matters.sel_matter_id','!=',1)
                            ->get();
                            $clientmatter_info_arr = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                            $latestClientMatterId = $clientmatter_info_arr ? $clientmatter_info_arr->id : null;

                            // Convert matter_list_arr to an array for sorting
                            $matter_list_arr = $matter_list_arr->toArray();
                            // Sort matters: URL matter ($id1) comes first, others follow
                            usort($matter_list_arr, function($a, $b) use ($id1) {
                                if ($a->client_unique_matter_no == $id1 && $b->client_unique_matter_no != $id1) {
                                    return -1; // $a (URL matter) comes first
                                } elseif ($a->client_unique_matter_no != $id1 && $b->client_unique_matter_no == $id1) {
                                    return 1; // $b (URL matter) comes first
                                }
                                return 0; // Maintain original order for other matters
                            });
                            ?>
                        <select name="matter_id" id="sel_matter_id_client_detail" class="form-control select2 visa-dropdown" data-valid="required">
                            <option value="">Select Matters</option>
                            @foreach($matter_list_arr as $matterlist)
                                <option value="{{$matterlist->id}}" {{ $matterlist->id == $latestClientMatterId ? 'selected' : '' }} data-clientuniquematterno="{{@$matterlist->client_unique_matter_no}}">{{@$matterlist->title}}({{@$matterlist->client_unique_matter_no}})</option>
                            @endforeach
                        </select>
                    <?php
                        }
                    }
                    else
                    {
                        $matter_cnt = DB::table('client_matters')
                        ->select('client_matters.id')
                        ->where('client_matters.client_id',@$fetchedData->id)
                        ->where('client_matters.matter_status',1)
                        ->whereNotNull('client_matters.sel_matter_id')
                        ->count();
                        if( $matter_cnt >0 )
                        {
                            $matter_list_arr = DB::table('client_matters')
                            ->leftJoin('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
                            ->select('client_matters.id','client_matters.client_unique_matter_no','matters.title')
                            ->where('client_matters.client_id',@$fetchedData->id)
                            ->where('client_matters.matter_status',1)
                            ->where('client_matters.sel_matter_id','!=',1)
                            ->orderBy('client_matters.created_at', 'desc')
                            ->get();
                            $latestClientMatter = \App\Models\ClientMatter::where('client_id',$fetchedData->id)->where('matter_status',1)->latest()->first();
                            $latestClientMatterId = $latestClientMatter ? $latestClientMatter->id : null;
                            ?>
                        <select name="matter_id" id="sel_matter_id_client_detail" class="form-control select2 visa-dropdown" data-valid="required">
                            <option value="">Select Matters</option>
                            @foreach($matter_list_arr as $matterlist)
                                <option value="{{$matterlist->id}}" {{ $matterlist->id == $latestClientMatterId ? 'selected' : '' }} data-clientuniquematterno="{{@$matterlist->client_unique_matter_no}}">{{@$matterlist->title}}({{@$matterlist->client_unique_matter_no}})</option>
                            @endforeach
                        </select>
                    <?php
                        }
                    }
                    ?>
                @endif
            </div>
            
            <div class="application-status-badge">
                <?php
                // Get the current workflow stage for this client matter
                $workflow_stage_arr = null;
                
                if ($id1) {
                    // If client unique reference id is present in url
                    $workflow_stage_arr = DB::table('client_matters')
                        ->join('workflow_stages', 'client_matters.workflow_stage_id', '=', 'workflow_stages.id')
                        ->select('workflow_stages.name')
                        ->where('client_id', $fetchedData->id)
                        ->where('client_unique_matter_no', $id1)
                        ->first();
                } else {
                    // Get the most recent active matter
                    $clientMatterInfo = DB::table('client_matters')
                        ->select('client_unique_matter_no')
                        ->where('client_id', $fetchedData->id)
                        ->where('matter_status', 1)
                        ->orderBy('id', 'desc')
                        ->first();

                    if ($clientMatterInfo) {
                        $workflow_stage_arr = DB::table('client_matters')
                            ->join('workflow_stages', 'client_matters.workflow_stage_id', '=', 'workflow_stages.id')
                            ->select('workflow_stages.name')
                            ->where('client_id', $fetchedData->id)
                            ->where('client_unique_matter_no', $clientMatterInfo->client_unique_matter_no)
                            ->first();
                    }
                }

                // Display the workflow stage name or default to "Initial Consultation"
                if ($workflow_stage_arr && $workflow_stage_arr->name) {
                    echo $workflow_stage_arr->name;
                } else {
                    echo "Initial Consultation";
                }
                ?>
            </div>
        </div>
        <nav class="client-sidebar-nav">
            <?php
            $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
            if( isset($id1) && $id1 != "" || $matter_cnt >0 )
            {  //if client unique reference id is present in url
            ?>
                <button class="client-nav-button active" data-tab="personaldetails">
                    <i class="fas fa-user"></i>
                    <span>Personal Details</span>
                </button>
                <button class="client-nav-button" data-tab="noteterm">
                    <i class="fas fa-sticky-note"></i>
                    <span>Notes</span>
                </button>
                <button class="client-nav-button" data-tab="personaldocuments">
                    <i class="fas fa-folder-open"></i>
                    <span>Personal Documents</span>
                </button>
                <button class="client-nav-button" data-tab="visadocuments">
                    <i class="fas fa-file-contract"></i>
                    <span>Visa Documents</span>
                </button>
                <button class="client-nav-button" data-tab="accounts">
                    <i class="fas fa-calculator"></i>
                    <span>Accounts</span>
                </button>
                <button class="client-nav-button" data-tab="conversations">
                    <i class="fas fa-envelope"></i>
                    <span>Emails</span>
                </button>
                <button class="client-nav-button" data-tab="formgenerations">
                    <i class="fas fa-file-alt"></i>
                    <span>Form Generation</span>
                </button>
                <button class="client-nav-button" data-tab="appointments">
                    <i class="fas fa-calendar"></i>
                    <span>Appointments</span>
                </button>
                <button class="client-nav-button" data-tab="application">
                    <i class="fas fa-globe"></i>
                    <span>Client Portal</span>
                </button>
            <?php
            }
            else
            {  //If no matter is exist
            ?>
                <button class="client-nav-button active" data-tab="personaldetails">
                    <i class="fas fa-user"></i>
                    <span>Personal Details</span>
                </button>
                <button class="client-nav-button" data-tab="noteterm">
                    <i class="fas fa-sticky-note"></i>
                    <span>Notes</span>
                </button>
                <button class="client-nav-button" data-tab="personaldocuments">
                    <i class="fas fa-folder-open"></i>
                    <span>Personal Documents</span>
                </button>
                <button class="client-nav-button" data-tab="formgenerationsL">
                    <i class="fas fa-file-alt"></i>
                    <span>Form Generation</span>
                </button>
                <button class="client-nav-button" data-tab="appointments">
                    <i class="fas fa-calendar"></i>
                    <span>Appointments</span>
                </button>
            <?php
            }
            ?>
        </nav>
    </aside>

    <main class="main-content" id="main-content">
        <div class="server-error">
            @include('../Elements/flash-message')
        </div>
        <div class="custom-error-msg">
        </div>
        <!-- Main Content Container with Vertical Tabs -->
        <div class="main-content-with-tabs">
            <!-- Tab Contents -->
            <div class="tab-content" id="tab-content">
            @include('Admin.clients.tabs.personal_details')
            
            @include('Admin.clients.tabs.notes')
            
            @include('Admin.clients.tabs.personal_documents')
            
            <?php
            // Mirror the same condition used to render sidebar buttons so that
            // only panes for visible tabs are included (prevents duplicates)
            $matter_cnt = \App\Models\ClientMatter::select('id')
                ->where('client_id',$fetchedData->id)
                ->where('matter_status',1)
                ->count();
            ?>
            @if((isset($id1) && $id1 != "") || $matter_cnt > 0)
                @include('Admin.clients.tabs.visa_documents')
                @include('Admin.clients.tabs.accounts')
                @include('Admin.clients.tabs.conversations')
                @include('Admin.clients.tabs.form_generation_client')
                @include('Admin.clients.tabs.appointments')
                @include('Admin.clients.tabs.client_portal')
            @else
                @include('Admin.clients.tabs.form_generation_lead')
                @include('Admin.clients.tabs.appointments')
            @endif
            
            @include('Admin.clients.tabs.not_used_documents')
            
            </div>
        </div>
    </main>

    <!-- Activity Feed (Only visible with Personal Details) -->
    <aside class="activity-feed" id="activity-feed">
        <div class="activity-feed-header">
            <h2><i class="fas fa-history"></i> Activity Feed</h2>
            <label for="increase-activity-feed-width">
               <input type="checkbox" id="increase-activity-feed-width" title="Expand Width">
            </label>
        </div>
        <ul class="feed-list">
            <?php
            if(
                ( isset($_REQUEST['user']) && $_REQUEST['user'] != "" )
                ||
                ( isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != "" )
            ){
                $user_search = $_REQUEST['user'];
                $keyword_search = $_REQUEST['keyword'];

                if($user_search != "" && $keyword_search != "") {
                    $activities = \App\Models\ActivitiesLog::select('activities_logs.*')
                    ->leftJoin('admins', 'activities_logs.created_by', '=', 'admins.id')
                    ->where('activities_logs.client_id', $fetchedData->id)
                    ->where(function($query) use ($user_search) {
                        $query->where('admins.first_name', 'like', '%'.$user_search.'%');
                    })
                    ->where(function($query) use ($keyword_search) {
                        $query->where('activities_logs.description', 'like', '%'.$keyword_search.'%');
                        $query->orWhere('activities_logs.subject', 'like', '%'.$keyword_search.'%');
                    })
                    ->orderby('activities_logs.created_at', 'DESC')
                    ->get();
                }
                else if($user_search == "" && $keyword_search != "") {
                    $activities = \App\Models\ActivitiesLog::select('activities_logs.*')
                    ->where('activities_logs.client_id', $fetchedData->id)
                    ->where(function($query) use ($keyword_search) {
                        $query->where('activities_logs.description', 'like', '%'.$keyword_search.'%');
                        $query->orWhere('activities_logs.subject', 'like', '%'.$keyword_search.'%');
                    })
                    ->orderby('activities_logs.created_at', 'DESC')
                    ->get();
                }
                else if($user_search != "" && $keyword_search == "") {
                    $activities = \App\Models\ActivitiesLog::select('activities_logs.*','admins.first_name','admins.last_name','admins.email')
                    ->leftJoin('admins', 'activities_logs.created_by', '=', 'admins.id')
                    ->where('activities_logs.client_id', $fetchedData->id)
                    ->where(function($query) use ($user_search) {
                        $query->where('admins.first_name', 'like', '%'.$user_search.'%');
                    })
                    ->orderby('activities_logs.created_at', 'DESC')
                    ->get();
                }
            } else {
                $activities = \App\Models\ActivitiesLog::where('client_id', $fetchedData->id)
                ->orderby('created_at', 'DESC')
                ->get();
            }
            //dd($activities);
            foreach($activities as $activit)
            {
                $admin = \App\Models\Admin::where('id', $activit->created_by)->first();
                ?>
                <li class="feed-item feed-item--email activity" id="activity_{{$activit->id}}">
                    <span class="feed-icon">
                        <?php
                        if (str_contains($activit->subject, "document")) {
                            echo '<i class="fas fa-file-alt"></i>';
                        } else {
                            echo '<i class="fas fa-sticky-note"></i>';
                        }?>
                    </span>
                    <div class="feed-content">
                        <p><strong>{{ $admin->first_name ?? 'NA' }}  <?php echo @$activit->subject; ?></strong>
                            @if(str_contains($activit->subject, 'added a note') || str_contains($activit->subject, 'updated a note'))
                                <i class="fas fa-ellipsis-v convert-activity-to-note" 
                                   style="margin-left: 5px; cursor: pointer;" 
                                   title="Convert to Note"
                                   data-activity-id="{{ $activit->id }}"
                                   data-activity-subject="{{ $activit->subject }}"
                                   data-activity-description="{{ $activit->description }}"
                                   data-activity-created-by="{{ $activit->created_by }}"
                                   data-activity-created-at="{{ $activit->created_at }}"
                                   data-client-id="{{ $fetchedData->id }}"></i>
                            @endif
                            -
                            @if($activit->description != '')
                                <p>{!!$activit->description!!}</p>
                            @endif
                        </p>
                        <span class="feed-timestamp">{{date('d M Y, H:i A', strtotime($activit->created_at))}}</span>
                    </div>
                </li>
            <?php
			}
			?>
        </ul>
        <!--<button class="btn btn-secondary btn-block">View Full History</button>-->
    </aside>
</div>

@include('Admin/clients/addclientmodal')
@include('Admin/clients/editclientmodal')






<div id="emailmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Compose Email</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="sendmail" action="{{URL::to('/admin/sendmail')}}" autocomplete="off" enctype="multipart/form-data">
				@csrf
                    <input type="hidden" name="client_id" value="{{$fetchedData->id}}">
                    <input type="hidden" name="mail_type" value="1">
                    <input type="hidden" name="mail_body_type" value="sent">
                    <input type="hidden" name="compose_client_matter_id" id="compose_client_matter_id" value="">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">From <span class="span_req">*</span></label>
								<select class="form-control" name="email_from" data-valid="required">
                                    <option value="">Select From</option>
									<?php
									$emails = \App\Models\Email::select('email')->where('status', 1)->get();
									foreach($emails as $nemail){
										?>
											<option value="<?php echo $nemail->email; ?>"><?php echo $nemail->email; ?></option>
										<?php
									}?>
								</select>
								@if ($errors->has('email_from'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_from') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_to">To <span class="span_req">*</span></label>
								<select data-valid="required" class="js-data-example-ajax" name="email_to[]"></select>

								@if ($errors->has('email_to'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_to') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_cc">CC </label>
								<select data-valid="" class="js-data-example-ajaxccd" name="email_cc[]"></select>

								@if ($errors->has('email_cc'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_cc') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<!--<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="template">Templates </label>
								<select data-valid="" class="form-control select2 selecttemplate" name="template">
									<option value="">Select</option>
									{{--@foreach(\App\Models\CrmEmailTemplate::all() as $list)--}}
										<option value="{{--$list->id--}}">{{--$list->name--}}</option>
									{{--@endforeach--}}
								</select>
                            </div>
						</div>-->

                        <div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="template">Templates </label>
                                <?php
                                $assignee = \App\Models\Admin::select('first_name')->where('id',@$fetchedData->assignee)->first();
                                if($assignee){
                                    $clientAssigneeName = $assignee->first_name;
                                } else {
                                    $clientAssigneeName = 'NA';
                                }
                                ?>
								<select data-valid="" class="form-control select2 selecttemplate" name="template" data-clientid="{{@$fetchedData->id}}" data-clientfirstname="{{@$fetchedData->first_name}}" data-clientvisaExpiry="{{@$fetchedData->visaExpiry}}" data-clientreference_number="{{@$fetchedData->client_id}}" data-clientassignee_name="{{@$clientAssigneeName}}">
									<option value="">Select</option>
									@foreach( \App\Models\CrmEmailTemplate::orderBy('id', 'desc')->get() as $list)
										<option value="{{$list->id}}">{{$list->name}}</option>
									@endforeach
								</select>
                            </div>
						</div>


						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="subject">Subject <span class="span_req">*</span></label>
								<input type="text" name="subject" id="compose_email_subject" class="form-control selectedsubject" data-valid="required" autocomplete="off" placeholder="Enter Subject" value="" />
								@if ($errors->has('subject'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('subject') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Message <span class="span_req">*</span></label>
								<textarea class="summernote-simple selectedmessage" id="compose_email_message" name="message"></textarea>
								@if ($errors->has('message'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('message') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
						     <div class="form-group">
						        <label>Attachment</label>
						        <input type="file" name="attach[]" class="form-control" multiple>
						     </div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
						    <div class="table-responsive uploadchecklists">
							<table id="mychecklist-datatable" class="table text_wrap table-2">
							    <thead>
							        <tr>
							            <th></th>
							            <th>File Name</th>
							            <th>File</th>
							        </tr>
							    </thead>
							    <tbody>
							        @foreach(\App\Models\UploadChecklist::all() as $uclist)
							        <tr>
							            <td><input type="checkbox" name="checklistfile[]" value="<?php echo $uclist->id; ?>"></td>
							            <td><?php echo $uclist->name; ?></td>
							             <td><a target="_blank" href="<?php echo URL::to('/checklists/'.$uclist->file); ?>"><?php echo $uclist->name; ?></a></td>
							        </tr>
							        @endforeach
							    </tbody>
							</table>
						</div>
							</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('sendmail')" type="button" class="btn btn-primary">Send</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- Send Message-->
<div id="sendmsgmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="messageModalLabel">Send Message</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="sendmsg" id="sendmsg" action="{{URL::to('/admin/sendmsg')}}" autocomplete="off" enctype="multipart/form-data">
				    @csrf
                    <input type="hidden" name="client_id" id="sendmsg_client_id" value="">
                    <input type="hidden" name="vtype" value="client">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Message <span class="span_req">*</span></label>
								<textarea class="summernote-simple selectedmessage" name="message" data-valid="required"></textarea>
								@if ($errors->has('message'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('message') }}</strong>
									</span>
								@endif
							</div>
						</div>
                        <div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('sendmsg')" type="button" class="btn btn-primary">Send</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>



<div class="modal fade  custom_modal" id="interest_service_view" tabindex="-1" role="dialog" aria-labelledby="interest_serviceModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content showinterestedservice">

		</div>
	</div>
</div>

<div id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">Ã—</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to delete this note?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Delete</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmNotUseDocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">Ã—</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to send this document in Not Use Tab?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Send</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmBackToDocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">Ã—</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to send this in related document Tab again?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Send</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmDocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">Ã—</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to verify this doc?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Verify</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>


<div id="confirmLogModal" tabindex="-1" role="dialog" aria-labelledby="confirmLogModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">Ã—</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to delete this log?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Delete</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmEducationModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">Ã—</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to delete this note?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accepteducation">Delete</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmcompleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">Ã—</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to complete the Application?</h4>
				<button  data-id="" type="submit" style="margin-top: 40px;" class="button btn btn-danger acceptapplication">Complete</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmCostAgreementModal" tabindex="-1" role="dialog" aria-labelledby="confirmCostAgreementModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">Ã—</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to delete this Cost Agreement?</h4>
				<button data-id="" type="submit" style="margin-top: 40px;" class="button btn btn-danger acceptCostAgreementDelete">Yes, Delete</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmpublishdocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">Ã—</span></button>
				<h4 class="modal-title text-center message col-v-5">Publish Document?</h4>
				<h5 class="">Publishing documents will allow client to access from client portal , Are you sure you want to continue ?</h5>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger acceptpublishdoc">Publish Anyway</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

 
    
 

<div class="modal fade custom_modal" id="application_ownership" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Application Ownership Ratio</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/application/application_ownership')}}" name="xapplication_ownership" id="xapplication_ownership" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="mapp_id" id="mapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="sus_agent"> </label>
								<input type="number" max="100" min="0" step="0.01" class="form-control ration" name="ratio">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('xapplication_ownership')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="superagent_application" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Select Super Agent</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/application/spagent_application')}}" name="spagent_application" id="spagent_application" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="siapp_id" id="siapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="super_agent">Super Agent <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control super_agent" id="super_agent" name="super_agent">
									<option value="">Please Select</option>
									<?php $sagents = \App\Models\Agent::whereRaw('FIND_IN_SET("Super Agent", agent_type)')->get(); ?>
									@foreach($sagents as $sa)
										<option value="{{$sa->id}}">{{$sa->full_name}} {{$sa->email}}</option>
									@endforeach
								</select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('spagent_application')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="subagent_application" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Select Sub Agent</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/application/sbagent_application')}}" name="sbagent_application" id="sbagent_application" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="sbapp_id" id="sbapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="sub_agent">Sub Agent <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control sub_agent" id="sub_agent" name="sub_agent">
									<option value="">Please Select</option>
									<?php $sagents = \App\Models\Agent::whereRaw('FIND_IN_SET("Sub Agent", agent_type)')->where('is_acrchived',0)->get(); ?>
									@foreach($sagents as $sa)
										<option value="{{$sa->id}}">{{$sa->full_name}} {{$sa->email}}</option>
									@endforeach
								</select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('sbagent_application')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="tags_clients" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Tags</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/save_tag')}}" name="stags_application" id="stags_application" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" id="client_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="super_agent">Tags <span class="span_req">*</span></label>
								<select data-valid="required" multiple class="tagsselec form-control super_tag" id="tag" name="tag[]">
								<?php $r = array();
								if($fetchedData->tagname != ''){
									$r = explode(',', $fetchedData->tagname);
								}
								?>
									<option value="">Please Select</option>
									<?php $stagd = \App\Models\Tag::where('id','!=','')->get(); ?>
									@foreach($stagd as $sa)
										<option <?php if(in_array($sa->id, $r)){ echo 'selected'; } ?> value="{{$sa->id}}">{{$sa->name}}</option>
									@endforeach
								</select>

							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('stags_application')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


 
    
 

 
    
 


<div class="modal fade custom_modal" id="serviceTaken" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Service Taken</h5>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <form method="post" action="{{URL::to('/admin/client/createservicetaken')}}" name="createservicetaken" id="createservicetaken" autocomplete="off" enctype="multipart/form-data">
				@csrf
                    <input id="logged_client_id" name="logged_client_id"  type="hidden" value="<?php echo $fetchedData->id;?>">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">

							<div class="form-group">
								<label style="display:block;" for="service_type">Select Service Type:</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="Migration_inv" value="Migration" name="service_type" checked>
									<label class="form-check-label" for="Migration_inv">Migration</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="Eductaion_inv" value="Eductaion" name="service_type">
									<label class="form-check-label" for="Eductaion_inv">Eductaion</label>
								</div>
								<span class="custom-error service_type_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12 is_Migration_inv">
                            <div class="form-group">
								<label for="mig_ref_no">Reference No: <span class="span_req">*</span></label>
                                <input type="text" name="mig_ref_no" id="mig_ref_no" value="" class="form-control" data-valid="required">
                            </div>

                            <div class="form-group">
								<label for="mig_service">Service: <span class="span_req">*</span></label>
                                <input type="text" name="mig_service" id="mig_service" value="" class="form-control" data-valid="required">
                            </div>

                            <div class="form-group">
								<label for="mig_notes">Notes: <span class="span_req">*</span></label>
                                <input type="text" name="mig_notes" id="mig_notes" value="" class="form-control" data-valid="required">
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12 is_Eductaion_inv" style="display:none;">
                            <div class="form-group">
								<label for="edu_course">Course: <span class="span_req">*</span></label>
                                <input type="text" name="edu_course" id="edu_course" value="" class="form-control">
                            </div>

                            <div class="form-group">
								<label for="edu_college">College: <span class="span_req">*</span></label>
                                <input type="text" name="edu_college" id="edu_college" value="" class="form-control">
                            </div>

                            <div class="form-group">
								<label for="edu_service_start_date">Service Start Date: <span class="span_req">*</span></label>
                                <input type="text" name="edu_service_start_date" id="edu_service_start_date" value="" class="form-control">
                            </div>

                            <div class="form-group">
								<label for="edu_notes">Notes: <span class="span_req">*</span></label>
                                <input type="text" name="edu_notes" id="edu_notes" value="" class="form-control">
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('createservicetaken')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>



<div class="modal fade" id="inbox_reassignemail_modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				  <h4 class="modal-title">Re-assign Inbox Email</h4>
				  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
			</div>
			<form method="POST" action="{{ url('/admin/reassiginboxemail') }}" name="inbox-email-reassign-to-client-matter" autocomplete="off" enctype="multipart/form-data" id="inbox-email-reassign-to-client-matter">
			@csrf
			<div class="modal-body">
				<div class="form-group row">
					<div class="col-sm-12">
						<input id="memail_id" name="memail_id" type="hidden" value="">
                        <input id="mail_type" name="mail_type" type="hidden" value="inbox">
                        <input id="user_mail" name="user_mail" type="hidden" value="">
                        <input id="uploaded_doc_id" name="uploaded_doc_id" type="hidden" value="">
						<select id="reassign_client_id" name="reassign_client_id" class="form-control select2" style="width: 100%;" data-select2-id="1" tabindex="-1" aria-hidden="true" data-valid="required">
							<option value="">Select Client</option>
							@foreach(\App\Models\Admin::Where('role','7')->Where('type','client')->get() as $ulist)
							<option value="{{@$ulist->id}}">{{@$ulist->first_name}} {{@$ulist->last_name}}({{@$ulist->client_id}})</option>
							@endforeach
						</select>
					</div>
				</div>

                <div class="form-group row">
					<div class="col-sm-12">
						<select id="reassign_client_matter_id" name="reassign_client_matter_id" class="form-control select2 " style="width: 100%;" data-select2-id="1" tabindex="-1" aria-hidden="true" disabled>
						</select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="customValidate('inbox-email-reassign-to-client-matter')">
					<i class="fa fa-save"></i> Re-assign Inbox Email
				</button>
			</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="sent_reassignemail_modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				  <h4 class="modal-title">Re-assign Sent Email</h4>
				  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
			</div>
			<form method="POST" action="{{ url('/admin/reassigsentemail') }}" name="sent-email-reassign-to-client-matter" autocomplete="off" enctype="multipart/form-data" id="sent-email-reassign-to-client-matter">
			@csrf
			<div class="modal-body">
				<div class="form-group row">
					<div class="col-sm-12">
						<input id="memail_id" name="memail_id" type="hidden" value="">
                        <input id="mail_type" name="mail_type" type="hidden" value="sent">
                        <input id="user_mail" name="user_mail" type="hidden" value="">
                        <input id="uploaded_doc_id" name="uploaded_doc_id" type="hidden" value="">
						<select id="reassign_sent_client_id" name="reassign_sent_client_id" class="form-control select2" style="width: 100%;" data-select2-id="1" tabindex="-1" aria-hidden="true" data-valid="required">
							<option value="">Select Client</option>
							@foreach(\App\Models\Admin::Where('role','7')->Where('type','client')->get() as $ulist)
							<option value="{{@$ulist->id}}">{{@$ulist->first_name}} {{@$ulist->last_name}}({{@$ulist->client_id}})</option>
							@endforeach
						</select>
					</div>
				</div>

                <div class="form-group row">
					<div class="col-sm-12">
						<select id="reassign_sent_client_matter_id" name="reassign_sent_client_matter_id" class="form-control select2 " style="width: 100%;" data-select2-id="1" tabindex="-1" aria-hidden="true" disabled>
						</select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="customValidate('sent-email-reassign-to-client-matter')">
					<i class="fa fa-save"></i> Re-assign Sent Email
				</button>
			</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="sent_mail_preview_modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				  <h4 class="modal-title" id="memail_subject"></h4>
				  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
			</div>
			<div class="modal-body">
				<div class="form-group row">
					<div class="col-sm-12" id="memail_message">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{URL::to('/')}}/js/popover.js"></script>
<script src="{{URL::asset('js/bootstrap-datepicker.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>

{{-- Sidebar Tabs Management - Dedicated file for sidebar navigation --}}
<script src="{{URL::asset('js/admin/clients/sidebar-tabs.js')}}"></script>

{{-- Pass Blade variables to JavaScript --}}
<script>
    // Configuration object with all Blade variables needed for JavaScript
    window.ClientDetailConfig = {
        clientId: '{{ $fetchedData->id }}',
        encodeId: '{{ $encodeId }}',
        matterId: '{{ $id1 ?? "" }}',
        activeTab: '{{ $activeTab ?? "personaldetails" }}',
        matterRefNo: '{{ $id1 ?? ($matter_info_arr->client_unique_matter_no ?? null) }}',
        clientFirstName: '{{ $fetchedData->first_name ?? "user" }}',
        csrfToken: '{{ csrf_token() }}',
        currentDate: '{{ date("Y-m-d") }}',
        appId: '{{ $_GET["appid"] ?? "" }}',
        urls: {
            base: '{{ URL::to("/") }}',
            admin: '{{ URL::to("/admin") }}',
            fetchVisaExpiryMessages: '{{ URL::to("/admin/fetch-visa_expiry_messages") }}',
            downloadDocument: '{{ url("/admin/download-document") }}',
            getTopInvoiceNo: '{{ URL::to("/admin/clients/getTopInvoiceNoFromDB") }}',
            getTopReceiptVal: '{{ URL::to("/admin/clients/getTopReceiptValInDB") }}',
            listOfInvoice: '{{ URL::to("/admin/clients/listOfInvoice") }}',
            clientLedgerBalance: '{{ URL::to("/admin/clients/clientLedgerBalanceAmount") }}',
            getClientAuPrPoints: '{{ URL::to("/admin/clients/get_client_au_pr_point_details") }}',
            calculatePoints: '{{ URL::to("/admin/clients/CalculatePoints") }}',
            prPointsAddToNotes: '{{ URL::to("/admin/clients/prpoints_add_to_notes") }}',
            loadApplicationInsertUpdate: '{{ URL::to("/admin/load-application-insert-update-data") }}',
            getApplicationDetail: '{{ URL::to("/admin/getapplicationdetail") }}',
            updateIntake: '{{ URL::to("/admin/application/updateintake") }}',
            updateExpectWin: '{{ URL::to("/admin/application/updateexpectwin") }}',
            updateDates: '{{ URL::to("/admin/application/updatedates") }}',
            updateNoteDatetime: '{{ URL::to("/admin/update-note-datetime") }}',
            referencesStore: '{{ route("references.store") }}',
            updateClientFundsLedger: '{{ route("admin.clients.update-client-funds-ledger") }}',
            getMigrationAgentDetail: '{{ URL::to("/admin/clients/getMigrationAgentDetail") }}',
            createIntakeUrl: '{{ url("/admin/clients/store-application-doc-via-form") }}',
            toggleClientPortal: '{{ route("admin.clients.toggleClientPortal") }}',
            enhanceMail: '{{ route("admin.mail.enhance") }}',
            composeEmail: '{{ URL::to("/admin/sendmail") }}',
            createNote: '{{ URL::to("/admin/create-note") }}',
            getNoteDetail: '{{ URL::to("/admin/getnotedetail") }}',
            deleteNote: '{{ URL::to("/admin/deletenote") }}',
            filterEmails: '{{ URL::to("/admin/clients/filter/emails") }}',
            filterSentMails: '{{ URL::to("/admin/clients/filter/sentmails") }}',
            checkStarClient: '{{ route("check.star.client") }}',
            getInfoByReceiptId: '{{ URL::to("/admin/clients/getInfoByReceiptId") }}',
            notPickedCall: '{{ URL::to("/admin/not-picked-call") }}',
            getDateTimeBackend: '{{ URL::to("/getdatetimebackend") }}',
            getDisabledDateTime: '{{ URL::to("/getdisableddatetime") }}',
            checkPromoCode: '{{ URL::to("/admin/promo-code/checkpromocode") }}',
            checkCostAssignment: '{{ URL::to("/admin/clients/check-cost-assignment") }}',
            getVisaAgreementAgent: '{{ URL::to("/admin/clients/getVisaAggreementMigrationAgentDetail") }}',
            generateAgreement: '{{ route("clients.generateagreement") }}',
            getCostAssignmentAgent: '{{ URL::to("/admin/clients/getCostAssignmentMigrationAgentDetail") }}',
            getCostAssignmentAgentLead: '{{ URL::to("/admin/clients/getCostAssignmentMigrationAgentDetailLead") }}',
            uploadAgreement: '{{ route("clients.uploadAgreement", $fetchedData->id) }}',
            fetchClientContactNo: '{{ URL::to("/admin/clients/fetchClientContactNo") }}',
            followupStore: '{{ URL::to("/admin/clients/followup/store") }}',
            publishDoc: '{{ URL::to("/admin/application/publishdoc") }}',
            deleteCostagreement: '{{ URL::to("/admin/deletecostagreement") }}',
            deleteAction: '{{ URL::to("/admin/delete_action") }}',
            pinNote: '{{ URL::to("/admin/pinnote") }}',
            pinActivityLog: '{{ URL::to("/admin/pinactivitylog") }}',
            getRecipients: '{{ URL::to("/admin/clients/get-recipients") }}',
            updateSessionCompleted: '{{ URL::to("/admin/clients/update-session-completed") }}',
            viewNoteDetail: '{{ URL::to("/admin/viewnotedetail") }}',
            viewApplicationNote: '{{ URL::to("/admin/viewapplicationnote") }}',
            getPartnerBranch: '{{ URL::to("/admin/getpartnerbranch") }}',
            changeClientStatus: '{{ URL::to("/admin/change-client-status") }}',
            getTemplates: '{{ URL::to("/admin/get-templates") }}',
            getPartner: '{{ URL::to("/admin/getpartner") }}',
            getProduct: '{{ URL::to("/admin/getproduct") }}',
            getBranch: '{{ URL::to("/admin/getbranch") }}',
            convertApplication: '{{ URL::to("/admin/convertapplication") }}',
            renameDoc: '{{ URL::to("/admin/renamedoc") }}',
            renameChecklistDoc: '{{ URL::to("/admin/renamechecklistdoc") }}',
            deleteEducation: '{{ URL::to("/admin/delete-education") }}',
            getSubjects: '{{ URL::to("/admin/getsubjects") }}',
            getAppointmentDetail: '{{ URL::to("/admin/getAppointmentdetail") }}',
            getEducationDetail: '{{ URL::to("/admin/getEducationdetail") }}',
            getInterestedService: '{{ URL::to("/admin/getintrestedservice") }}',
            getInterestedServiceEdit: '{{ URL::to("/admin/getintrestedserviceedit") }}',
            fetchClientMatterAssignee: '{{ URL::to("/admin/clients/fetchClientMatterAssignee") }}',
            addScheduleInvoiceDetail: '{{ URL::to("/admin/addscheduleinvoicedetail") }}',
            updateStage: '{{ URL::to("/admin/updatestage") }}',
            completeStage: '{{ URL::to("/admin/completestage") }}',
            updateBackStage: '{{ URL::to("/admin/updatebackstage") }}',
            getApplicationNotes: '{{ URL::to("/admin/getapplicationnotes") }}',
            scheduleInvoiceDetail: '{{ URL::to("/admin/scheduleinvoicedetail") }}',
            checklistUpload: '{{ URL::to("/admin/application/checklistupload") }}',
            sendToHubdoc: '{{ url("/admin/clients/sendToHubdoc") }}',
            checkHubdocStatus: '{{ url("/admin/clients/checkHubdocStatus") }}',
            updateMailReadBit: '{{ URL::to("/admin/clients/updatemailreadbit") }}',
            listAllMatters: '{{ URL::to("/admin/clients/listAllMattersWRTSelClient") }}',
        }
    };
    
    // Appointment data for the appointments tab
    @php
    $appointmentdata = array();
    $appointmentlists = \App\Models\Appointment::where('client_id', $fetchedData->id)
        ->where('related_to', 'client')
        ->orderby('created_at', 'DESC')
        ->get();
    
    foreach($appointmentlists as $appointmentlist){
        $admin = \App\Models\Admin::select('id', 'first_name','email')
            ->where('id', $appointmentlist->user_id)
            ->first();
        $first_name= $admin->first_name ?? 'N/A';
        
        $appointmentdata[$appointmentlist->id] = array(
            'title' => $appointmentlist->title,
            'time' => date('H:i A', strtotime($appointmentlist->time)),
            'date' => date('d D, M Y', strtotime($appointmentlist->date)),
            'description' => htmlspecialchars($appointmentlist->description, ENT_QUOTES, 'UTF-8'),
            'createdby' => substr($first_name, 0, 1),
            'createdname' => $first_name,
            'createdemail' => $admin->email ?? 'N/A',
        );
    }
    @endphp
    window.appointmentData = {!! json_encode($appointmentdata, JSON_FORCE_OBJECT) !!};
</script>

{{-- Newly added external JS placeholders for progressive migration --}}
<script src="{{ URL::asset('js/admin/clients/shared.js') }}" defer></script>
<script src="{{ URL::asset('js/admin/clients/detail.js') }}" defer></script>
<script src="{{ URL::asset('js/admin/clients/tabs/application.js') }}" defer></script>

{{-- Main detail page JavaScript --}}
<script src="{{ URL::asset('js/admin/clients/detail-main.js') }}?v={{ time() }}"></script>

@endsection
