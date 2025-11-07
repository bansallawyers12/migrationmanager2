@extends('layouts.crm_client_detail')
@section('title', 'Client Detail')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ URL::asset('css/client-detail.css') }}">

<?php
use App\Http\Controllers\Controller;
?>
<div class="crm-container" data-client-id="{{ $fetchedData->id }}">
    <!-- Collapsed Toggle Button (shown when sidebar is collapsed) -->
    <button id="collapsed-toggle" class="collapsed-toggle-btn" title="Show Sidebar">
        ☰
    </button>
    
    <!-- Client Navigation Sidebar -->
    <aside class="client-navigation-sidebar" id="client-sidebar">
        <div class="sidebar-header">
            <!-- Sidebar Toggle Button -->
            <button id="sidebar-toggle" class="sidebar-toggle-btn" title="Hide Sidebar">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="client-info">
                <h3 class="client-id">
                    <?php
                    if($id1) { //if client unique reference id is present in url
                        $matter_info_arr = \App\Models\ClientMatter::select('client_unique_matter_no')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                    ?>
                        {{$fetchedData->client_id}}-{{$matter_info_arr ? $matter_info_arr->client_unique_matter_no : 'N/A'}}
                    <?php
                    } else {
                        $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                        if($matter_cnt >0){
                            $matter_info_arr = \App\Models\ClientMatter::select('client_unique_matter_no')->where('client_id',$fetchedData->id)->where('matter_status',1)->orderBy('id', 'desc')->first();
                        ?>
                            {{$fetchedData->client_id}}-{{$matter_info_arr ? $matter_info_arr->client_unique_matter_no : 'N/A'}}
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
                    <a href="{{route('clients.edit', base64_encode(convert_uuencode(@$fetchedData->id)))}}" title="Edit" class="client-name-edit">
                        <i class="fa fa-edit"></i>
                    </a>
                </p>
                
                <!-- Action Icons (left) and Client Portal Toggle (right) -->
                <div class="sidebar-actions-row">
                    <!-- Action Icons -->
                    <div class="client-actions">
                        <a href="javascript:;" class="create_note_d" datatype="note" title="Add Notes"><i class="fas fa-plus"></i></a>
                        <a href="javascript:;" data-id="{{@$fetchedData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="clientemail" title="Compose Mail"><i class="fa fa-envelope"></i></a>
                        <a href="javascript:;" class="send-sms-btn" data-client-id="{{@$fetchedData->id}}" data-client-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" title="Send SMS"><i class="fas fa-sms"></i></a>
                        <a href="javascript:;" datatype="not_picked_call" class="not_picked_call" title="Not Picked Call"><i class="fas fa-mobile-alt"></i></a>
                        <a href="javascript:;" data-toggle="modal" data-target="#create_appoint" title="Add Appointment"><i class="fas fa-calendar-plus"></i></a>
                    </div>
                    
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
                            ->select('client_matters.id','client_matters.client_unique_matter_no','matters.title','client_matters.sel_matter_id')
                            ->where('client_matters.client_id',@$fetchedData->id)
                            ->where('client_matters.matter_status',1)
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
                                @php
                                    // If sel_matter_id is 1 or title is null, use "General Matter"
                                    $matterName = 'General Matter';
                                    if ($matterlist->sel_matter_id != 1 && !empty($matterlist->title)) {
                                        $matterName = $matterlist->title;
                                    }
                                @endphp
                                <option value="{{$matterlist->id}}" {{ $matterlist->id == $latestClientMatterId ? 'selected' : '' }} data-clientuniquematterno="{{@$matterlist->client_unique_matter_no}}">{{$matterName}}({{@$matterlist->client_unique_matter_no}})</option>
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
                            ->select('client_matters.id','client_matters.client_unique_matter_no','matters.title','client_matters.sel_matter_id')
                            ->where('client_matters.client_id',@$fetchedData->id)
                            ->where('client_matters.matter_status',1)
                            ->orderBy('client_matters.created_at', 'desc')
                            ->get();
                            $latestClientMatter = \App\Models\ClientMatter::where('client_id',$fetchedData->id)->where('matter_status',1)->latest()->first();
                            $latestClientMatterId = $latestClientMatter ? $latestClientMatter->id : null;
                            ?>
                        <select name="matter_id" id="sel_matter_id_client_detail" class="form-control select2 visa-dropdown" data-valid="required">
                            <option value="">Select Matters</option>
                            @foreach($matter_list_arr as $matterlist)
                                @php
                                    // If sel_matter_id is 1 or title is null, use "General Matter"
                                    $matterName = 'General Matter';
                                    if ($matterlist->sel_matter_id != 1 && !empty($matterlist->title)) {
                                        $matterName = $matterlist->title;
                                    }
                                @endphp
                                <option value="{{$matterlist->id}}" {{ $matterlist->id == $latestClientMatterId ? 'selected' : '' }} data-clientuniquematterno="{{@$matterlist->client_unique_matter_no}}">{{$matterName}}({{@$matterlist->client_unique_matter_no}})</option>
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
            
            <!-- Matter References Section -->
            <div class="sidebar-references">
                <?php
                // Load reference values - SAME LOGIC AS ACCOUNTS TAB
                $matter__ref_info_arr = [];
                if($id1) {
                    // If client unique reference id is present in url
                    $matter__ref_info_arr = \App\Models\ClientMatter::select('department_reference','other_reference')
                        ->where('client_id', $fetchedData->id)
                        ->where('client_unique_matter_no', $id1)
                        ->first();
                } else {
                    $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id', $fetchedData->id)->where('matter_status', 1)->count();
                    if($matter_cnt > 0) {
                        $matter__ref_info_arr = \App\Models\ClientMatter::select('department_reference','other_reference')
                            ->where('client_id', $fetchedData->id)
                            ->where('matter_status', 1)
                            ->orderBy('id', 'desc')
                            ->first();
                    }
                }
                ?>
                
                <!-- Hidden inputs - SAME IDs AS ORIGINAL -->
                <input type="hidden" 
                       id="department_reference" 
                       name="department_reference" 
                       value="<?php if(isset($matter__ref_info_arr) && !empty($matter__ref_info_arr) && $matter__ref_info_arr->department_reference != ''){ echo $matter__ref_info_arr->department_reference; } ?>">
                
                <input type="hidden" 
                       id="other_reference" 
                       name="other_reference" 
                       value="<?php if(isset($matter__ref_info_arr) && !empty($matter__ref_info_arr) && $matter__ref_info_arr->other_reference != ''){ echo $matter__ref_info_arr->other_reference; } ?>">
                
                <!-- Reference Chips Container -->
                <div id="references-container" class="references-chips-container">
                    <!-- Dynamically generated chips -->
                </div>
                
                <!-- Input Container (hidden by default) -->
                <div id="reference-input-container" class="reference-input-wrapper" style="display: none;">
                    <input type="text" 
                           id="reference-input" 
                           class="form-control form-control-sm reference-input" 
                           placeholder="Type and press Enter..."
                           maxlength="50"
                           autocomplete="off">
                    <button class="btn-cancel-input" type="button" title="Cancel (Esc)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Add Button -->
                <button id="btn-add-reference" class="btn-add-reference-chip" type="button">
                    <i class="fas fa-plus"></i> Add Reference
                </button>
            </div>
        </div>
        <nav class="client-sidebar-nav">
            <?php
            $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
            
            // Valid tab names that should NOT be treated as matter IDs
            $validTabNames = ['personaldetails', 'noteterm', 'personaldocuments', 'visadocuments', 
                              'eoiroi', 'accounts', 'conversations', 'emailhandling', 
                              'formgenerations', 'formgenerationsL', 'application','appointments'];
            
            // Check if $id1 is a valid matter ID (not a tab name)
            $isMatterIdInUrl = isset($id1) && $id1 != "" && !in_array($id1, $validTabNames);
            
            // Show client menu if: valid matter ID in URL OR client has any matters
            if( $isMatterIdInUrl || $matter_cnt > 0 )
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
                @if(isset($isEoiMatter) && $isEoiMatter)
                <button class="client-nav-button" data-tab="eoiroi">
                    <i class="fas fa-passport"></i>
                    <span>EOI / ROI</span>
                </button>
                @endif
                <button class="client-nav-button" data-tab="accounts">
                    <i class="fas fa-calculator"></i>
                    <span>Accounts</span>
                </button>
                <button class="client-nav-button" data-tab="accounts-test">
                    <i class="fas fa-flask"></i>
                    <span>Accounts-Test</span>
                </button>
                <button class="client-nav-button" data-tab="conversations">
                    <i class="fas fa-envelope"></i>
                    <span>Emails</span>
                </button>
                <button class="client-nav-button" data-tab="emailhandling">
                    <i class="fas fa-inbox"></i>
                    <span>Email Handling</span>
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
            @include('crm.clients.tabs.personal_details')
            
            @include('crm.clients.tabs.notes')
            
            @include('crm.clients.tabs.personal_documents')
            
            <?php
            // Mirror the same condition used to render sidebar buttons so that
            // only panes for visible tabs are included (prevents duplicates)
            $matter_cnt = \App\Models\ClientMatter::select('id')
                ->where('client_id',$fetchedData->id)
                ->where('matter_status',1)
                ->count();
            ?>
            @if((isset($id1) && $id1 != "") || $matter_cnt > 0)
                @include('crm.clients.tabs.visa_documents')
                
                @if(isset($isEoiMatter) && $isEoiMatter)
                    @include('crm.clients.tabs.eoi_roi')
                @endif
                
                @include('crm.clients.tabs.accounts')
                @include('crm.clients.tabs.accounts_test')
                @include('crm.clients.tabs.conversations')
                @include('crm.clients.tabs.email_handling')
                @include('crm.clients.tabs.form_generation_client')
                @include('crm.clients.tabs.appointments')
                @include('crm.clients.tabs.client_portal')
            @else
                @include('crm.clients.tabs.form_generation_lead')
                @include('crm.clients.tabs.appointments')
            @endif
            
            @include('crm.clients.tabs.not_used_documents')
            
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
        
        <!-- Activity Type Filters -->
        <div class="activity-filters">
            <button class="activity-filter-btn active" data-filter="all">
                <i class="fas fa-list"></i> All
            </button>
            <button class="activity-filter-btn" data-filter="sms">
                <i class="fas fa-sms"></i> SMS
            </button>
            <button class="activity-filter-btn" data-filter="note">
                <i class="fas fa-sticky-note"></i> Notes
            </button>
            <button class="activity-filter-btn" data-filter="document">
                <i class="fas fa-file-alt"></i> Documents
            </button>
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
                <li class="feed-item feed-item--email activity {{ $activit->activity_type ? 'activity-type-' . $activit->activity_type : '' }}" id="activity_{{$activit->id}}">
                    <span class="feed-icon {{ $activit->activity_type === 'sms' ? 'feed-icon-sms' : '' }}">
                        <?php
                        // Determine icon based on activity type
                        if ($activit->activity_type === 'sms') {
                            echo '<i class="fas fa-sms"></i>';
                        } elseif (str_contains($activit->subject, "document")) {
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

@include('crm.clients.modals.appointment')
@include('crm.clients.addclientmodal')
@include('crm.clients.editclientmodal')






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
				<form method="post" name="sendmail" action="{{route('clients.sendmail')}}" autocomplete="off" enctype="multipart/form-data">
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
				<form method="post" name="sendmsg" id="sendmsg" action="{{route('clients.sendmail')}}" autocomplete="off" enctype="multipart/form-data">
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

<!-- Send SMS Modal -->
<div id="sendSmsModal" data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="smsModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="smsModalLabel">
					<i class="fas fa-sms"></i> Send SMS
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="sendSmsForm">
				    @csrf
                    <input type="hidden" name="client_id" id="sms_client_id" value="">
                    
					<div class="row">
						<!-- Phone Number Selection -->
						<div class="col-12">
							<div class="form-group">
								<label for="sms_phone">Send To <span class="span_req">*</span></label>
								<select class="form-control" id="sms_phone" name="phone" required>
									<option value="">Select phone number...</option>
								</select>
								<small class="form-text text-muted">
									<i class="fas fa-info-circle"></i> 
									Australian numbers will use Cellcast, international numbers will use Twilio
								</small>
							</div>
						</div>
						
						<!-- Template Selection -->
						<div class="col-12">
							<div class="form-group">
								<label for="sms_template">Quick Template (Optional)</label>
								<select class="form-control" id="sms_template">
									<option value="">Type your own message or select a template...</option>
								</select>
							</div>
						</div>
						
						<!-- Message -->
						<div class="col-12">
							<div class="form-group">
								<label for="sms_message">Message <span class="span_req">*</span></label>
								<textarea class="form-control" id="sms_message" name="message" rows="5" maxlength="1600" required></textarea>
								<div class="d-flex justify-content-between">
									<small class="form-text text-muted">
										<span id="sms_char_count">0</span> / 1600 characters
									</small>
									<small class="form-text text-muted">
										<span id="sms_parts_count">1</span> SMS part(s)
									</small>
								</div>
							</div>
						</div>
						
						<!-- Buttons -->
                        <div class="col-12">
							<button type="submit" class="btn btn-primary" id="sendSmsBtn">
								<i class="fas fa-paper-plane"></i> Send SMS
							</button>
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
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">&times;</span></button>
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
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">&times;</span></button>
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
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">&times;</span></button>
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
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">&times;</span></button>
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
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">&times;</span></button>
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
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">&times;</span></button>
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
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">&times;</span></button>
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
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">&times;</span></button>
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
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">&times;</span></button>
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
				<form method="post" action="{{url('/application/application_ownership')}}" name="xapplication_ownership" id="xapplication_ownership" autocomplete="off" enctype="multipart/form-data">
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
				<form method="post" action="{{url('/application/spagent_application')}}" name="spagent_application" id="spagent_application" autocomplete="off" enctype="multipart/form-data">
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
				<form method="post" action="{{url('/application/sbagent_application')}}" name="sbagent_application" id="sbagent_application" autocomplete="off" enctype="multipart/form-data">
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
                <form method="post" action="{{url('/save_tag')}}" name="stags_application" id="stags_application" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" id="client_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="super_agent">Tags <span class="span_req">*</span></label>
								<select data-valid="required" multiple class="tagsselec form-control super_tag" id="tag" name="tag[]" data-tags="true">
								<?php $r = [];
								if($fetchedData->tagname != ''){
									$r = explode(',', $fetchedData->tagname);
								}
								?>
									<option value="">Please Select</option>
									<?php $stagd = \App\Models\Tag::where('id','!=','')->get(); ?>
									@foreach($stagd as $sa)
										<option <?php if(in_array($sa->id, $r)){ echo 'selected'; } ?> value="{{$sa->name}}">{{$sa->name}}</option>
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
                <form method="post" action="{{url('/client/createservicetaken')}}" name="createservicetaken" id="createservicetaken" autocomplete="off" enctype="multipart/form-data">
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
			<form method="POST" action="{{ url('/reassiginboxemail') }}" name="inbox-email-reassign-to-client-matter" autocomplete="off" enctype="multipart/form-data" id="inbox-email-reassign-to-client-matter">
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
			<form method="POST" action="{{ url('/reassigsentemail') }}" name="sent-email-reassign-to-client-matter" autocomplete="off" enctype="multipart/form-data" id="sent-email-reassign-to-client-matter">
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
@push('scripts')
<script src="{{URL::to('/')}}/js/popover.js"></script>
<script src="{{URL::asset('js/bootstrap-datepicker.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>

{{-- Activity Feed Filters --}}
<script>
$(document).ready(function() {
    // Activity type filter functionality
    $('.activity-filter-btn').on('click', function() {
        // Remove active class from all buttons
        $('.activity-filter-btn').removeClass('active');
        
        // Add active class to clicked button
        $(this).addClass('active');
        
        // Get filter type
        var filterType = $(this).data('filter');
        
        // Show/hide activities based on filter
        if (filterType === 'all') {
            $('.feed-item.activity').show();
        } else if (filterType === 'note') {
            // Show activities that don't have specific types (default notes) or have note type
            $('.feed-item.activity').each(function() {
                var $item = $(this);
                if (!$item.hasClass('activity-type-sms') && !$item.hasClass('activity-type-document')) {
                    $item.show();
                } else {
                    $item.hide();
                }
            });
        } else if (filterType === 'document') {
            $('.feed-item.activity').hide();
            // Show document activities
            $('.feed-item.activity').each(function() {
                var $item = $(this);
                var subject = $item.find('.feed-content strong').text().toLowerCase();
                if (subject.includes('document')) {
                    $item.show();
                }
            });
        } else {
            // Show only activities with specific type
            $('.feed-item.activity').hide();
            $('.feed-item.activity-type-' + filterType).show();
        }
    });
});
</script>

{{-- Sidebar Tabs Management - Dedicated file for sidebar navigation --}}
<script src="{{URL::asset('js/crm/clients/sidebar-tabs.js')}}"></script>

{{-- Pass Blade variables to JavaScript --}}
<script>
    // Configuration object with all Blade variables needed for JavaScript
    window.ClientDetailConfig = {
        clientId: '{{ $fetchedData->id }}',
        encodeId: '{{ $encodeId }}',
        matterId: '{{ $id1 ?? "" }}',
        activeTab: '{{ $activeTab ?? "personaldetails" }}',
        matterRefNo: '{{ $id1 ?? "" }}',
        clientFirstName: '{{ $fetchedData->first_name ?? "user" }}',
        // SMS Template Variables
        staffName: '{{ $staffName ?? "" }}',
        matterNumber: '{{ $matterNumber ?? "" }}',
        officePhone: '{{ $officePhone ?? "" }}',
        officeCountryCode: '{{ $officeCountryCode ?? "+61" }}',
        csrfToken: '{{ csrf_token() }}',
        currentDate: '{{ date("Y-m-d") }}',
        appId: '{{ $_GET["appid"] ?? "" }}',
        urls: {
            base: '{{ URL::to("/") }}',
            admin: '{{ URL::to("/") }}',
            fetchVisaExpiryMessages: '{{ URL::to("/fetch-visa_expiry_messages") }}',
            downloadDocument: '{{ url("/documents/download") }}',
            getTopInvoiceNo: '{{ URL::to("/clients/getTopInvoiceNoFromDB") }}',
            getTopReceiptVal: '{{ URL::to("/clients/getTopReceiptValInDB") }}',
            listOfInvoice: '{{ URL::to("/clients/listOfInvoice") }}',
            clientLedgerBalance: '{{ URL::to("/clients/clientLedgerBalanceAmount") }}',
            getInvoicesByMatter: '{{ URL::to("/get-invoices-by-matter") }}',
            loadApplicationInsertUpdate: '{{ URL::to("/load-application-insert-update-data") }}',
            getApplicationDetail: '{{ URL::to("/getapplicationdetail") }}',
            updateIntake: '{{ URL::to("/application/updateintake") }}',
            updateExpectWin: '{{ URL::to("/application/updateexpectwin") }}',
            updateDates: '{{ URL::to("/application/updatedates") }}',
            updateNoteDatetime: '{{ URL::to("/update-note-datetime") }}',
            referencesStore: '{{ route("references.store") }}',
            updateClientFundsLedger: '{{ route("clients.update-client-funds-ledger") }}',
            getMigrationAgentDetail: '{{ URL::to("/clients/getMigrationAgentDetail") }}',
            createIntakeUrl: '{{ url("/clients/store-application-doc-via-form") }}',
            toggleClientPortal: '{{ route("clients.toggleClientPortal") }}',
            enhanceMail: '{{ route("mail.enhance") }}',
            composeEmail: '{{ URL::to("/sendmail") }}',
            createNote: '{{ URL::to("/create-note") }}',
            getNoteDetail: '{{ URL::to("/getnotedetail") }}',
            deleteNote: '{{ URL::to("/deletenote") }}',
            filterEmails: '{{ URL::to("/clients/filter/emails") }}',
            filterSentMails: '{{ URL::to("/clients/filter/sentmails") }}',
            checkStarClient: '{{ route("check.star.client") }}',
            getInfoByReceiptId: '{{ URL::to("/clients/getInfoByReceiptId") }}',
            notPickedCall: '{{ URL::to("/not-picked-call") }}',
            getDateTimeBackend: '{{ URL::to("/getdatetimebackend") }}',
            getDisabledDateTime: '{{ URL::to("/getdisableddatetime") }}',
            checkCostAssignment: '{{ URL::to("/clients/check-cost-assignment") }}',
            getVisaAgreementAgent: '{{ URL::to("/clients/getVisaAggreementMigrationAgentDetail") }}',
            generateAgreement: '{{ route("clients.generateagreement") }}',
            getCostAssignmentAgent: '{{ URL::to("/clients/getCostAssignmentMigrationAgentDetail") }}',
            getCostAssignmentAgentLead: '{{ URL::to("/clients/getCostAssignmentMigrationAgentDetailLead") }}',
            uploadAgreement: '{{ route("clients.uploadAgreement", $fetchedData->id) }}',
            fetchClientContactNo: '{{ URL::to("/clients/fetchClientContactNo") }}',
            followupStore: '{{ URL::to("/clients/followup/store") }}',
            publishDoc: '{{ URL::to("/application/publishdoc") }}',
            deleteCostagreement: '{{ URL::to("/deletecostagreement") }}',
            deleteAction: '{{ URL::to("/delete_action") }}',
            pinNote: '{{ URL::to("/pinnote") }}',
            pinActivityLog: '{{ URL::to("/pinactivitylog") }}',
            getRecipients: '{{ URL::to("/clients/get-recipients") }}',
            updateSessionCompleted: '{{ URL::to("/clients/update-session-completed") }}',
            viewNoteDetail: '{{ URL::to("/viewnotedetail") }}',
            viewApplicationNote: '{{ URL::to("/viewapplicationnote") }}',
            getPartnerBranch: '{{ URL::to("/getpartnerbranch") }}',
            changeClientStatus: '{{ URL::to("/change-client-status") }}',
            getTemplates: '{{ URL::to("/get-templates") }}',
            getPartner: '{{ URL::to("/getpartner") }}',
            getProduct: '{{ URL::to("/getproduct") }}',
            getBranch: '{{ URL::to("/getbranch") }}',
            convertApplication: '{{ URL::to("/convertapplication") }}',
            renameDoc: '{{ URL::to("/documents/rename") }}',
            renameChecklistDoc: '{{ URL::to("/documents/rename-checklist") }}',
            deleteEducation: '{{ URL::to("/delete-education") }}',
            getSubjects: '{{ URL::to("/getsubjects") }}',
            getAppointmentDetail: '{{ URL::to("/getAppointmentdetail") }}',
            getEducationDetail: '{{ URL::to("/getEducationdetail") }}',
            getInterestedService: '{{ URL::to("/getintrestedservice") }}',
            getInterestedServiceEdit: '{{ URL::to("/getintrestedserviceedit") }}',
            fetchClientMatterAssignee: '{{ URL::to("/clients/fetchClientMatterAssignee") }}',
            addScheduleInvoiceDetail: '{{ URL::to("/addscheduleinvoicedetail") }}',
            updateStage: '{{ URL::to("/updatestage") }}',
            completeStage: '{{ URL::to("/completestage") }}',
            updateBackStage: '{{ URL::to("/updatebackstage") }}',
            getApplicationNotes: '{{ URL::to("/getapplicationnotes") }}',
            scheduleInvoiceDetail: '{{ URL::to("/scheduleinvoicedetail") }}',
            checklistUpload: '{{ URL::to("/application/checklistupload") }}',
            sendToHubdoc: '{{ url("/clients/sendToHubdoc") }}',
            checkHubdocStatus: '{{ url("/clients/checkHubdocStatus") }}',
            updateMailReadBit: '{{ URL::to("/clients/updatemailreadbit") }}',
            listAllMatters: '{{ URL::to("/clients/listAllMattersWRTSelClient") }}',
            getActivities: '{{ route("clients.activities") }}',
        }
    };
    
    // Appointment data for the appointments tab
    @php
    $appointmentdata = [];
    $appointmentlists = \App\Models\Appointment::where('client_id', $fetchedData->id)
        ->where('related_to', 'client')
        ->orderby('created_at', 'DESC')
        ->get();
    
    foreach($appointmentlists as $appointmentlist){
        $admin = \App\Models\Admin::select('id', 'first_name','email')
            ->where('id', $appointmentlist->user_id)
            ->first();
        $first_name= $admin->first_name ?? 'N/A';
        
        $appointmentdata[$appointmentlist->id] = [
            'title' => $appointmentlist->title,
            'time' => date('H:i A', strtotime($appointmentlist->time)),
            'date' => date('d D, M Y', strtotime($appointmentlist->date)),
            'description' => htmlspecialchars($appointmentlist->description, ENT_QUOTES, 'UTF-8'),
            'createdby' => substr($first_name, 0, 1),
            'createdname' => $first_name,
            'createdemail' => $admin->email ?? 'N/A',
        ];
    }
    @endphp
    window.appointmentData = {!! json_encode($appointmentdata, JSON_FORCE_OBJECT) !!};
    
    // Global function to load activities feed
    window.loadActivities = function() {
        $.ajax({
            url: window.ClientDetailConfig.urls.getActivities,
            type: 'GET',
            dataType: 'json',
            data: { id: window.ClientDetailConfig.clientId },
            success: function(response) {
                if (response.status && response.data) {
                    var html = '';
                    
                    $.each(response.data, function (k, v) {
                        // Determine icon based on activity type
                        var activityType = v.activity_type ?? 'note';
                        var subjectIcon;
                        var iconClass = '';
                        
                        if (activityType === 'sms') {
                            subjectIcon = '<i class="fas fa-sms"></i>';
                            iconClass = 'feed-icon-sms';
                        } else if (v.subject && v.subject.toLowerCase().includes("document")) {
                            subjectIcon = '<i class="fas fa-file-alt"></i>';
                        } else {
                            subjectIcon = '<i class="fas fa-sticky-note"></i>';
                        }

                        var subject = v.subject ?? '';
                        var description = v.message ?? '';
                        var taskGroup = v.task_group ?? '';
                        var followupDate = v.followup_date ?? '';
                        var date = v.date ?? '';
                        var fullName = v.name ?? '';
                        var activityTypeClass = activityType ? 'activity-type-' + activityType : '';

                        html += `
                            <li class="feed-item feed-item--email activity ${activityTypeClass}" id="activity_${v.activity_id}">
                                <span class="feed-icon ${iconClass}">
                                    ${subjectIcon}
                                </span>
                                <div class="feed-content">
                                    <p><strong>${fullName} ${subject}</strong></p>
                                    ${description !== '' ? `<p>${description}</p>` : ''}
                                    ${taskGroup !== '' ? `<p>${taskGroup}</p>` : ''}
                                    ${followupDate !== '' ? `<p>${followupDate}</p>` : ''}
                                    <span class="feed-timestamp">${date}</span>
                                </div>
                            </li>
                        `;
                    });

                    $('.feed-list').html(html);
                    
                    // Adjust Activity Feed height after content update
                    if (typeof adjustActivityFeedHeight === 'function') {
                        adjustActivityFeedHeight();
                    }
                } else {
                    console.error('Failed to load activities:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading activities:', error);
            }
        });
    };
</script>

{{-- Newly added external JS placeholders for progressive migration --}}
<script src="{{ URL::asset('js/crm/clients/shared.js') }}" defer></script>
<script src="{{ URL::asset('js/crm/clients/detail.js') }}" defer></script>
<script src="{{ URL::asset('js/crm/clients/tabs/application.js') }}" defer></script>

{{-- Main detail page JavaScript --}}
<script src="{{ URL::asset('js/crm/clients/detail-main.js') }}?v={{ time() }}"></script>

{{-- Sidebar Toggle JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const collapsedToggle = document.getElementById('collapsed-toggle');
    const sidebar = document.getElementById('client-sidebar');
    const container = document.querySelector('.crm-container');
    
    // Check if sidebar state is saved in localStorage
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    
    // Apply initial state
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
        container.classList.add('sidebar-collapsed');
    }
    
    // Hide sidebar functionality
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.add('collapsed');
        container.classList.add('sidebar-collapsed');
        localStorage.setItem('sidebarCollapsed', 'true');
    });
    
    // Show sidebar functionality
    collapsedToggle.addEventListener('click', function() {
        sidebar.classList.remove('collapsed');
        container.classList.remove('sidebar-collapsed');
        localStorage.setItem('sidebarCollapsed', 'false');
    });
});

// SMS Modal Functionality
// Declare global variables for SMS functionality
let smsClientId = null;
let smsClientName = null;

$('.send-sms-btn').on('click', function() {
    smsClientId = $(this).data('client-id');
    smsClientName = $(this).data('client-name');
    
    $('#sms_client_id').val(smsClientId);
    $('#smsModalLabel').text(`Send SMS to ${smsClientName}`);
    
    // Show loading state
    const phoneSelect = $('#sms_phone');
    phoneSelect.empty();
    phoneSelect.append('<option value="">Loading phone numbers...</option>');
    
    // Load client phone numbers
    $.ajax({
        url: '{{ URL::to("/clients/fetchClientContactNo") }}',
        type: 'POST',
        dataType: 'json',
        data: {
            _token: '{{ csrf_token() }}',
            client_id: smsClientId
        },
        success: function(response) {
            console.log('Phone numbers response:', response);
            phoneSelect.empty();
            phoneSelect.append('<option value="">Select phone number...</option>');
            
            // Parse response if it's a string (fallback for older jQuery versions)
            const data = (typeof response === 'string') ? $.parseJSON(response) : response;
            
            if (data && data.clientContacts && data.clientContacts.length > 0) {
                data.clientContacts.forEach(function(contact) {
                    console.log('Processing contact:', contact);
                    // Handle missing fields gracefully
                    const countryCode = contact.country_code || '';
                    const phone = contact.phone || '';
                    const contactType = contact.contact_type || 'Phone';
                    const fullPhone = countryCode + phone;
                    const label = contactType + ': ' + fullPhone;
                    phoneSelect.append(`<option value="${fullPhone}">${label}</option>`);
                });
            } else {
                phoneSelect.append('<option value="">No phone numbers found</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to fetch phone numbers:', error);
            phoneSelect.empty();
            phoneSelect.append('<option value="">Error loading phone numbers</option>');
            iziToast.error({
                title: 'Error',
                message: 'Failed to load phone numbers. Please try again.',
                position: 'topRight'
            });
        }
    });
    
    // Load SMS templates
    $.ajax({
        url: '{{ route("adminconsole.features.sms.templates.active") }}',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            const templateSelect = $('#sms_template');
            templateSelect.empty();
            templateSelect.append('<option value="">Type your own message or select a template...</option>');
            
            if (response.success && response.data && response.data.length > 0) {
                response.data.forEach(function(template) {
                    templateSelect.append(`<option value="${template.id}" data-message="${template.message}">${template.title}</option>`);
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to fetch SMS templates:', error);
            const templateSelect = $('#sms_template');
            templateSelect.empty();
            templateSelect.append('<option value="">Error loading templates</option>');
            iziToast.error({
                title: 'Error',
                message: 'Failed to load SMS templates. Please try again.',
                position: 'topRight'
            });
        }
    });
    
    // Reset form
    $('#sms_message').val('');
    $('#sms_char_count').text('0');
    $('#sms_parts_count').text('1');
    
    $('#sendSmsModal').modal('show');
});

// Template selection
$('#sms_template').on('change', function() {
    const selectedOption = $(this).find('option:selected');
    const message = selectedOption.data('message');
    if (message && smsClientName) {
        // Replace placeholders with actual client data
        let processedMessage = message;
        
        // Basic client variables
        processedMessage = processedMessage.replace(/\{first_name\}/g, smsClientName.split(' ')[0] || '');
        processedMessage = processedMessage.replace(/\{last_name\}/g, smsClientName.split(' ').slice(1).join(' ') || '');
        processedMessage = processedMessage.replace(/\{client_name\}/g, smsClientName);
        processedMessage = processedMessage.replace(/\{full_name\}/g, smsClientName);
        
        // New variables from ClientDetailConfig
        processedMessage = processedMessage.replace(/\{staff_name\}/g, window.ClientDetailConfig.staffName || '');
        processedMessage = processedMessage.replace(/\{matter_number\}/g, window.ClientDetailConfig.matterNumber || '');
        
        // Format office phone with country code
        const officePhone = window.ClientDetailConfig.officeCountryCode + window.ClientDetailConfig.officePhone;
        processedMessage = processedMessage.replace(/\{office_phone\}/g, officePhone || '');
        
        $('#sms_message').val(processedMessage).trigger('input');
    }
});

// Character counter
$('#sms_message').on('input', function() {
    const length = $(this).val().length;
    $('#sms_char_count').text(length);
    
    const parts = Math.ceil(length / 160) || 1;
    $('#sms_parts_count').text(parts);
});

// Form submission
$('#sendSmsForm').on('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = $('#sendSmsBtn');
    const originalText = submitBtn.html();
    
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
    
    const formData = {
        _token: '{{ csrf_token() }}',
        client_id: $('#sms_client_id').val(),
        phone: $('#sms_phone').val(),
        message: $('#sms_message').val()
    };
    
    $.ajax({
        url: '{{ route("adminconsole.features.sms.send") }}',
        type: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                iziToast.success({
                    title: 'Success',
                    message: 'SMS sent successfully!',
                    position: 'topRight'
                });
                $('#sendSmsModal').modal('hide');
                
                // Reload activity feed if exists
                if (typeof loadActivities === 'function') {
                    loadActivities();
                }
            } else {
                iziToast.error({
                    title: 'Error',
                    message: response.message || 'Failed to send SMS',
                    position: 'topRight'
                });
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while sending SMS';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            iziToast.error({
                title: 'Error',
                message: errorMessage,
                position: 'topRight'
            });
        },
        complete: function() {
            submitBtn.prop('disabled', false).html(originalText);
        }
    });
});
</script>

@endpush
