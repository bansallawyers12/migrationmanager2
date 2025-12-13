@extends('layouts.crm_client_detail_dashboard')

@section('content')
    <main class="main-content">
        <header class="header">
            <h1>Dashboard</h1>
        </header>

        {{-- KPI Cards Section --}}
        <section class="kpi-cards">
            <x-dashboard.kpi-card 
                :title="'Active Matters'" 
                :count="$count_active_matter" 
                :route="route('clients.clientsmatterslist')"
                icon="fas fa-briefcase"
                icon-class="icon-active" 
            />
            
            <x-dashboard.kpi-card 
                :title="'Urgent Notes Deadlines'" 
                :count="$count_note_deadline"
                icon="fas fa-hourglass-half"
                icon-class="icon-pending" 
            />
            
            <x-dashboard.kpi-card 
                :title="'Cases Requiring Attention'" 
                :count="$count_cases_requiring_attention_data"
                icon="fas fa-check-circle"
                icon-class="icon-success" 
            />
        </section>

        <section class="priority-focus">
            <div class="focus-container">
                <h3><i class="fas fa-calendar-times" style="color: var(--danger-color);"></i> Urgent Notes & Deadlines</h3>
                <div class="task-list-container">
                    <ul class="task-list">
                        @foreach($notesData as $note)
                        <?php
                        $note_client = \App\Models\Admin::select('id','first_name','last_name','client_id')->where('id', $note->client_id)->first();
                        ?>
                        <li>
                            <div class="task-details">
                                <span class="client-name">
                                    {{ $note_client && $note_client->first_name ? Str::limit($note_client->first_name, '50', '...') : config('constants.empty') }} {{ $note_client && $note_client->last_name ? Str::limit($note_client->last_name, '50', '...') : config('constants.empty') }}
                                    (<a href="{{ $note_client ? URL::to('/clients/detail/'.base64_encode(convert_uuencode($note_client->id))) : '#' }}">{{ $note_client && $note_client->client_id ? Str::limit($note_client->client_id, '50', '...') : config('constants.empty') }}</a>)
                                </span>
                                <span class="task-desc">
                                    <?php echo preg_replace('/<\/?p>/', '', $note->description ); ?>
                                </span>
                            </div>
                            <div class="task-deadline">
                                <span class="date">{{ date('d/m/Y',strtotime($note->note_deadline)) }}</span>
                                <?php
                                    $deadline = new DateTime($note->note_deadline);
                                    $today = new DateTime();
                                    $interval = $today->diff($deadline);
                                    $daysLeft = $interval->days;
                                    $daysLeftText = $daysLeft . ' day' . ($daysLeft != 1 ? 's' : '') . ' left';
                                    $daysLeftClass = $daysLeft <= 3 ? 'text-danger' : ($daysLeft <= 7 ? 'text-warning' : 'text-success');
                                ?>
                                <span class="days-left {{ $daysLeftClass }}">({{ $daysLeftText }})</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="focus-container">
                <h3><i class="fas fa-exclamation-circle" style="color: var(--warning-color);"></i> Cases Requiring Attention</h3>
                <div class="case-list-container">
                    <ul class="case-list">
                        @foreach($cases_requiring_attention_data as $attention)
                        <?php
                        $client_attention = \App\Models\Admin::select('id','first_name','last_name','client_id')->where('id', $attention->client_id)->first();
                        //dd(base64_encode(convert_uuencode($client_attention->id)));
                        ?>
                        <li>
                            <div class="case-details">
                                <span class="client-name">
                                    {{ $client_attention && $client_attention->first_name ? Str::limit($client_attention->first_name, '50', '...') : config('constants.empty') }} {{ $client_attention && $client_attention->last_name ? Str::limit($client_attention->last_name, '50', '...') : config('constants.empty') }}
                                    (<a href="{{ $client_attention ? URL::to('/clients/detail/'.base64_encode(convert_uuencode($client_attention->id)).'/'.$attention->client_unique_matter_no) : '#' }}">{{ $client_attention && $client_attention->client_id ? Str::limit($client_attention->client_id, '50', '...') : config('constants.empty') }}</a>)
                                </span>
                                <span class="case-info">
                                <?php
                                if($attention->sel_matter_id == 1) {
                                    $matter_name = 'General matter';
                                } else {
                                    if($attention->sel_matter_id != ''){
                                        $matter = \App\Models\Matter::select('title')->where('id', $attention->sel_matter_id)->first();
                                        if($matter){
                                            $matter_name = $matter->title;
                                        } else {
                                            $matter_name = 'NA';
                                        }
                                    } else {
                                        $matter_name = 'NA';
                                    }
                                }

                                // Calculate days since last update
                                $lastUpdated = new DateTime($attention->updated_at);
                                $today = new DateTime();
                                $interval = $today->diff($lastUpdated);
                                $daysStalled = $interval->days;
                                //dd($daysStalled);
                                if($daysStalled <1){
                                    $daysStalledText = 'Today';
                                } else {
                                    $daysStalledText = $daysStalled .' days ago';
                                }
                                ?>
                                <a href="{{URL::to('/clients/detail/'.base64_encode(convert_uuencode(@$client_attention->id)).'/'.$attention->client_unique_matter_no )}}">{{ $matter_name}} ({{$attention->client_unique_matter_no }}) </a>
                                <span style="display: inline-block;" class="stalled-days {{ $daysStalled > 14 ? 'text-danger' : ($daysStalled > 7 ? 'text-warning' : 'text-info') }}">({{ $daysStalledText }})</span>
                                </span>
                            </div>
                            <span class="case-attention-reason reason-stalled">{{ $attention->updated_at_type ?: 'NA' }}</span>
                        </li>
                        @endforeach

                    </ul>
                </div>
            </div>
        </section>

        <section class="cases-overview">
            <div class="cases-overview-header">
                <div class="header-left">
                    <h3>Client Matters <span class="total-count">({{ $data->total() }} total)</span></h3>
                </div>
                <div class="header-right" style="margin-bottom: 24px;margin-right: 5px;">
                    <div class="column-toggle-container">
                        <button class="column-toggle-btn" type="button" id="columnToggleBtn">
                            <i class="fas fa-columns"></i>
                            <span class="visible-count">{{ count($visibleColumns) }}</span>
                        </button>
                        <div class="column-dropdown" id="columnDropdown">
                            <div class="column-dropdown-header">
                                <label class="column-toggle-all">
                                    <input type="checkbox" id="toggleAllColumns" {{ count($visibleColumns) == 8 ? 'checked' : '' }}>
                                    <span>Display All</span>
                                </label>
                            </div>
                            <div class="column-dropdown-body">
                                <label class="column-option">
                                    <input type="checkbox" name="column" value="matter" {{ in_array('matter', $visibleColumns) ? 'checked' : '' }}>
                                    <span>Matter</span>
                                </label>
                                <label class="column-option">
                                    <input type="checkbox" name="column" value="client_id" {{ in_array('client_id', $visibleColumns) ? 'checked' : '' }}>
                                    <span>Client ID</span>
                                </label>
                                <label class="column-option">
                                    <input type="checkbox" name="column" value="client_name" {{ in_array('client_name', $visibleColumns) ? 'checked' : '' }}>
                                    <span>Client Name</span>
                                </label>
                                <label class="column-option">
                                    <input type="checkbox" name="column" value="dob" {{ in_array('dob', $visibleColumns) ? 'checked' : '' }}>
                                    <span>DOB</span>
                                </label>
                                <label class="column-option">
                                    <input type="checkbox" name="column" value="migration_agent" {{ in_array('migration_agent', $visibleColumns) ? 'checked' : '' }}>
                                    <span>Migration Agent</span>
                                </label>
                                <label class="column-option">
                                    <input type="checkbox" name="column" value="person_responsible" {{ in_array('person_responsible', $visibleColumns) ? 'checked' : '' }}>
                                    <span>Person Responsible</span>
                                </label>
                                <label class="column-option">
                                    <input type="checkbox" name="column" value="person_assisting" {{ in_array('person_assisting', $visibleColumns) ? 'checked' : '' }}>
                                    <span>Person Assisting</span>
                                </label>
                                <label class="column-option">
                                    <input type="checkbox" name="column" value="stage" {{ in_array('stage', $visibleColumns) ? 'checked' : '' }}>
                                    <span>Stage</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="filter-controls">
                    <form id="filterForm" method="GET" action="{{ route('dashboard') }}">
                        <div class="search-box">
                            <input type="text" name="client_name" placeholder="Search Client Name..." value="{{ $filters['client_name'] ?? '' }}">
                            <i class="fas fa-search"></i>
                        </div>

                        <select name="client_stage" class="stage-select">
                            <option value="">All Stages</option>
                            @foreach(\App\Models\WorkflowStage::where('id','!=','')->orderby('id','ASC')->get() as $stage)
                                <option value="{{ $stage->id }}" {{ (isset($filters['client_stage']) && $filters['client_stage'] == $stage->id) ? 'selected' : '' }}>
                                    {{ $stage->name }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="filter-button">
                            <i class="fas fa-filter"></i> Filter
                        </button>

                        @if(isset($filters['client_name']) || isset($filters['client_stage']))
                            <a href="{{ route('dashboard') }}" class="clear-filters" onclick="clearFiltersAndReset()">
                                <i class="fas fa-times"></i> Clear Filters
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="table-responsive" style="overflow-x: hidden; max-width: 100%;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="col-matter" style="min-width: 120px;">Matter</th>
                            <th class="col-client_id" style="min-width: 80px;">Client ID</th>
                            <th class="col-client_name" style="min-width: 100px;">Client Name</th>
                            <th class="col-dob" style="min-width: 100px;">DOB</th>
                            <th class="col-migration_agent" style="min-width: 100px;">Migration Agent</th>
                            <th class="col-person_responsible" style="min-width: 100px;">Person Responsible</th>
                            <th class="col-person_assisting" style="min-width: 100px;">Person Assisting</th>
                            <th class="col-stage" style="min-width: 80px;">Stage</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php //dd($data);?>
                    @if(@count($data) !== 0)
                    @foreach($data as $index => $item)
                        <?php
                        $admin = \App\Models\Admin::select('first_name')->where('id', $item->client_id)->first();
                        if($item->sel_matter_id == 1) {
                            $matter_name = 'Genral matter';
                        } else {
                            if($item->sel_matter_id != ''){
                                $matter = \App\Models\Matter::select('title')->where('id', $item->sel_matter_id)->first();
                                if($matter){
                                    $matter_name = $matter->title;
                                } else {
                                    $matter_name = 'NA';
                                }
                            } else {
                                $matter_name = 'NA';
                            }
                        }
                        $client_info = \App\Models\Admin::select('client_id','first_name','last_name')->where('id', $item->client_id)->first();
                        $mig_agent_info = \App\Models\Admin::select('first_name','last_name')->where('id', $item->sel_migration_agent)->first();
                        $person_responsible = \App\Models\Admin::select('first_name','last_name')->where('id', $item->sel_person_responsible)->first();
                        $person_assisting = \App\Models\Admin::select('first_name','last_name')->where('id', $item->sel_person_assisting)->first();

                        //Get Total mail assign to any specific client matter
                        $total_email_assign_cnt = \App\Models\MailReport::where('client_matter_id', $item->id)
                        ->where('client_id', $item->client_id)
                        ->where('conversion_type', 'conversion_email_fetch')
                        ->whereNull('mail_is_read')
                        ->where(function($query) {
                            $query->orWhere('mail_body_type','inbox')
                            ->orWhere('mail_body_type','sent');
                        })->count();
                    ?>
                    <tr>
                        <td class="col-matter" style="white-space: initial;">
                            <a href="{{URL::to('/clients/detail/'.base64_encode(convert_uuencode(@$item->client_id)).'/'.$item->client_unique_matter_no )}}">{{ $matter_name}} ({{$item->client_unique_matter_no }}) </a>
                            <span class="totalEmailCntToClientMatter">{{$total_email_assign_cnt}}</span>
                        </td>
                        <td class="col-client_id">
                            @php
                                $clientDetailUrl = '/clients/detail/'.base64_encode(convert_uuencode(@$item->client_id));
                                if(!empty($item->client_unique_matter_no)) {
                                    $clientDetailUrl .= '/'.$item->client_unique_matter_no;
                                }
                            @endphp
                            <a href="{{ URL::to($clientDetailUrl) }}">{{ $client_info && $client_info->client_id ? Str::limit($client_info->client_id, '50', '...') : config('constants.empty') }}</a>
                        </td>
                        <td class="col-client_name">{{ $client_info && $client_info->first_name ? Str::limit($client_info->first_name, '50', '...') : config('constants.empty') }} {{ $client_info && $client_info->last_name ? Str::limit($client_info->last_name, '50', '...') : config('constants.empty') }}</td>
                        <td class="col-dob">{{ @$item->dob == "" ? config('constants.empty') : (strtotime(@$item->dob) ? date('d/m/Y', strtotime(@$item->dob)) : Str::limit(@$item->dob, '50', '...')) }}</td>
                        <td class="col-migration_agent">{{ $mig_agent_info && $mig_agent_info->first_name ? Str::limit($mig_agent_info->first_name, '50', '...') : config('constants.empty') }} {{ $mig_agent_info && $mig_agent_info->last_name ? Str::limit($mig_agent_info->last_name, '50', '...') : config('constants.empty') }}</td>
                        <td class="col-person_responsible">{{ $person_responsible && $person_responsible->first_name ? Str::limit($person_responsible->first_name, '50', '...') : config('constants.empty') }} {{ $person_responsible && $person_responsible->last_name ? Str::limit($person_responsible->last_name, '50', '...') : config('constants.empty') }}</td>
                        <td class="col-person_assisting">{{ $person_assisting && $person_assisting->first_name ? Str::limit($person_assisting->first_name, '50', '...') : config('constants.empty') }} {{ $person_assisting && $person_assisting->last_name ? Str::limit($person_assisting->last_name, '50', '...') : config('constants.empty') }}</td>
                        <td class="col-stage">
                            <select class="form-select stageCls" id="stage_<?php echo $item->id;?>" style="height: 30px;border-color: #e0e0e0;">
                                @foreach(\App\Models\WorkflowStage::where('id','!=','')->orderby('id','ASC')->get() as $stage)
                                <option value="<?php echo $stage->id; ?>" <?php echo $item->workflow_stage_id == $stage->id ? 'selected' : ''; ?>><?php echo $stage->name; ?></option>
                                @endforeach
                            </select>
                        </td>

                    </tr>
                    @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="empty-state">
                                <div>
                                    <i class="fas fa-inbox fa-3x mb-3" style="color: #cbd5e0;"></i>
                                    <p>No records found</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($data->hasPages())
                <div class="pagination-container" style="margin-top: 20px; display: flex; justify-content: center; align-items: center;">
                    <div class="pagination-info" style="margin-right: 20px; color: var(--text-muted-color); font-size: 0.9em;">
                        Showing {{ $data->firstItem() ?? 0 }} to {{ $data->lastItem() ?? 0 }} of {{ $data->total() }} results
                    </div>
                    <div class="pagination-links">
                        {{ $data->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </section>
    </main>

    <div class="modal fade custom_modal" id="create_task_modal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalLabel">Create New Task</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="#" name="newtaskform" autocomplete="off" id="tasktermform" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="is_ajax" value="0">
                    <input type="hidden" name="is_dashboard" value="true">
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="title">Title <span class="span_req">*</span></label>
                                    <input type="text" name="title" class="form-control" required autocomplete="off" placeholder="Enter Title">
                                    <span class="custom-error title_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="category">Category <span class="span_req">*</span></label>
                                    <select data-valid="required" class="form-control cleintselect2 select2" name="category">
                                        <option value="">Choose Category</option>
                                        <option value="Reminder">Reminder</option>
                                        <option value="Call">Call</option>
                                        <option value="Follow Up">Follow Up</option>
                                        <option value="Email">Email</option>
                                        <option value="Meeting">Meeting</option>
                                        <option value="Support">Support</option>
                                        <option value="Others">Others</option>
                                    </select>
                                    <span class="custom-error category_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="assignee">Assignee</label>
                                    <select data-valid="" class="form-control cleintselect2 select2" name="assignee">
                                        <option value="">Select</option>
                                        @foreach($assignee as $assigne)
                                            <option value="{{$assigne->id}}">{{$assigne->first_name}} ({{$assigne->email}})</option>
                                        @endforeach
                                    </select>
                                    <span class="custom-error assignee_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="priority">Priority</label>
                                    <select data-valid="" class="form-control cleintselect2 select2" name="priority">
                                        <option value="">Choose Priority</option>
                                        <option value="Low">Low</option>
                                        <option value="Normal">Normal</option>
                                        <option value="High">High</option>
                                        <option value="Urgent">Urgent</option>
                                    </select>
                                    <span class="custom-error priority_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="due_date">Due Date</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                        </div>
                                        <input type="text" name="due_date" class="form-control datepicker" autocomplete="off" placeholder="Select Date">
                                    </div>
                                    <span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
                                    <span class="custom-error due_date_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="due_time">Due Time</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                        </div>
                                        <input type="time" name="due_time" class="form-control" autocomplete="off" placeholder="Select Time">
                                    </div>
                                    <span class="custom-error due_time_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 col-md-12 col-lg-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" name="description"></textarea>
                                    <span class="custom-error description_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 col-md-12 col-lg-12">
                                <div class="form-group">
                                    <label class="d-block" for="related_to">Related To</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="contact" value="Contact" name="related_to" checked>
                                        <label class="form-check-label" for="contact">Contact</label>
                                    </div>
                                    {{--<div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="application" value="Application" name="related_to">
                                        <label class="form-check-label" for="application">Application</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="internal" value="Internal" name="related_to">
                                        <label class="form-check-label" for="internal">Internal</label>
                                    </div>--}}
                                    @if ($errors->has('related_to'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('related_to') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-6 is_contact">
                                <div class="form-group">
                                    <label for="contact_name">Contact Name <span class="span_req">*</span></label>
                                    <select data-valid="required" class="form-control cleintselect2 select2" name="contact_name[]">
                                        <option value="">Choose Contact</option>
                                        <?php
                                        $clients = \App\Models\Admin::select('id','first_name','email')->where('is_archived', '=', '0')->where('role', '=', '7')->get();
                                        foreach($clients as $client){
                                        ?>
                                        <option value="{{$client->id}} ">{{$client->first_name}} ({{$client->email}})</option>
                                        <?php } ?>
                                    </select>
                                    <span class="custom-error contact_name_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-6 is_application">
                                <div class="form-group">
                                    <label for="client_name">Client Name <span class="span_req">*</span></label>
                                    <select data-valid="" id="getapplications" class="form-control client_name cleintselect2" name="client_name">
                                        <option value="">Choose Client</option>
                                        <?php
                                    //$clientsss = \App\Models\Admin::where('is_archived', '0')->where('role', '7')->get();
                                    /*	foreach($clientsss as $clientsssss){
                                        ?>
                                        <option value="{{@$clientsssss->id}}">{{@$clientsssss->first_name}} ({{@$clientsssss->email}})</option>
                                        <?php }*/ ?>
                                    </select>
                                    <span class="custom-error client_name_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-6 is_application">
                                <div class="form-group">
                                    <label for="application">Application <span class="span_req">*</span></label>
                                    <select data-valid="" id="allaplication" class="form-control cleintselect2 select2" name="application">
                                        <option value="">Choose Application</option>

                                    </select>
                                    <span class="custom-error application_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6 is_application">
                                <div class="form-group">
                                    <label for="stage">Stage <span class="span_req">*</span></label>
                                    <select data-valid="" class="form-control cleintselect2 select2" name="stage">
                                        <option value="">Choose Stage</option>
                                        <option value="Application">Application</option>
                                        <option value="Acceptance">Acceptance</option>
                                        <option value="Payment">Payment</option>
                                        <option value="Form | 20">Form | 20</option>
                                        <option value="Visa Application">Visa Application</option>
                                        <option value="Interview">Interview</option>
                                        <option value="Enrolment">Enrolment</option>
                                        <option value="Course Ongoing">Course Ongoing</option>

                                    </select>
                                    <span class="custom-error stage_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="followers">Followers <span class="span_req">*</span></label>
                                    <select data-valid="" class="form-control cleintselect2 select2" name="followers">
                                        <option value="">Choose Followers</option>
                                        <?php
                                        $followers = \App\Models\Admin::select('id','first_name','email')->where('role', '!=', '7')->get();
                                        foreach($followers as $follower){
                                        ?>
                                        <option value="{{$follower->id}} ">{{$follower->first_name}} ({{$follower->email}})</option>
                                        <?php } ?>
                                    </select>
                                    <span class="custom-error followers_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>

                            <div class="col-12 col-md-12 col-lg-12">
                                <div class="form-group">
                                    <label for="attachments">Attachments</label>
                                    <div class="custom-file">
                                        <input type="file" class="form-control" name="attachments">

                                    </div>
                                    <span class="custom-error attachments_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 col-md-12 col-lg-12">
                                <button onclick="customValidate('newtaskform')" type="button" class="btn btn-primary">Create</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Popup Modal -->
    <div class="modal fade custom_modal" id="extend_note_popup" tabindex="-1" role="dialog" aria-labelledby="create_action_popupLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="padding: 20px;">
                <div class="modal-header" style="padding-bottom: 11px;">
                    <h5 class="modal-title assignnn" id="create_action_popupLabel" style="margin: 0 -24px;">Extend Notes Deadline</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input id="note_id" type="hidden" value="">
                <input id="unique_group_id" type="hidden" value="">
                <div id="popover-content">
                    <div class="box-header with-border">
                        <div class="form-group row" style="margin-bottom:12px;">
                            <label for="inputEmail3" class="col-sm-3 control-label c6 f13" style="margin-top:8px;">Note</label>
                            <div class="col-sm-9">
                                <textarea id="assignnote" class="form-control" placeholder="Enter a note..."></textarea>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <div class="form-group row note_deadline">
                        <label for="inputSub3" class="col-sm-3 control-label c6 f13" style="margin-top:8px;">
                            Note Deadline
                        </label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control f13" placeholder="yyyy-mm-dd" id="note_deadline" value="<?php echo date('Y-m-d');?>" name="note_deadline">
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="box-footer" style="padding:10px 0;">
                        <div class="row text-center">
                            <div class="col-md-12 text-center">
                                <button class="btn btn-danger" id="extend_deadline">Extend Deadline</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
@once
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<style>
        :root {
            --primary-color: #005792; /* Deeper Blue */
            --secondary-color: #00BBF0; /* Lighter Blue Accent */
            --background-color: #f4f7fc; /* Light grey background */
            --card-bg-color: #ffffff; /* White cards */
            --text-color: #333333;
            --text-muted-color: #777777;
            --border-color: #e0e0e0;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8; /* Added info color */
        }

        /* Override layout styles for main-content to ensure full width */
        .main-content {
            flex-grow: 1;
            margin-left: 60px; /* Default for collapsed sidebar */
            padding: 25px;
            overflow-y: auto;
            height: calc(100vh - 70px - 50px); /* Adjust for header and footer */
            width: calc(100% - 60px); /* Full width minus sidebar */
            box-sizing: border-box;
            background-color: var(--background-color);
            color: var(--text-color);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .sidebar-expanded + .main-content {
            margin-left: 220px; /* Adjust for expanded sidebar */
            width: calc(100% - 220px);
        }

        /* Ensure main-content takes full width and overrides layout constraints */
        .main-content {
            min-width: 0 !important; /* Override layout's min-width */
            max-width: 100% !important; /* Ensure it takes full width */
        }

        /* --- Header --- */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 1.8em;
            font-weight: 600;
        }
        .user-profile {
            display: flex;
            align-items: center;
        }
        .user-profile .fa-bell {
            font-size: 1.2em;
            margin-right: 20px;
            color: var(--text-muted-color);
            cursor: pointer;
        }
        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .user-profile span {
            font-weight: 500;
        }

        /* --- KPI Cards --- */
        .kpi-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
            width: 100%; /* Ensure it takes full width */
        }
        .card {
            background-color: var(--card-bg-color);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            transition: transform 0.2s ease-in-out;
        }
        .card:hover {
            transform: translateY(-3px);
        }
        .card i {
            font-size: 1.8em;
            margin-right: 15px;
            padding: 10px;
            border-radius: 50%;
            color: #fff;
        }
        .card .icon-active {
            background-color: var(--primary-color);
        }
        .card .icon-pending {
            background-color: var(--warning-color);
        }
        .card .icon-success {
            background-color: var(--success-color);
        }
        .card .icon-tasks {
            background-color: var(--danger-color);
        }
        .card-content h3 {
            margin: 0 0 5px 0;
            font-size: 0.9em;
            color: var(--text-muted-color);
            font-weight: 500;
        }
        .card-content p {
            margin: 0;
            font-size: 1.5em;
            font-weight: 600;
        }

        /* --- Priority Focus Section --- */
        .priority-focus {
            display: grid;
            grid-template-columns: 1fr 1fr; /* Two columns for tasks and cases */
            gap: 20px;
            margin-bottom: 30px;
            width: 100%;
        }

        .focus-container {
            background-color: var(--card-bg-color);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            min-height: 250px; /* Ensure containers have some height */
        }

        .focus-container h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.1em;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        .focus-container h3 i {
            margin-right: 8px;
        }

        .task-list, .case-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .task-list li, .case-list li {
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9em;
        }
        .task-list li:last-child, .case-list li:last-child {
            border-bottom: none;
        }

        .task-details span, .case-details span {
            display: block; /* Stack details vertically */
        }
        .task-details .client-name, .case-details .client-name {
            font-weight: 500;
        }
        .task-details .task-desc, .case-details .case-info {
            color: var(--text-muted-color);
            font-size: 0.9em;
        }

        .task-deadline {
            text-align: right;
        }
        .task-deadline .date {
            font-weight: 500;
            color: var(--danger-color); /* Highlight deadline */
        }
        .task-deadline .days-left {
            font-size: 0.85em;
            color: var(--text-muted-color);
        }

        .case-attention-reason {
            font-weight: 500;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.85em;
        }
        .reason-rfi {
            background-color: var(--warning-color);
            color: #333;
        }
        .reason-stalled {
            background-color: var(--info-color);
            color: #fff;
        }

        /* --- Active Cases Overview Section --- */
        .cases-overview {
            background-color: var(--card-bg-color);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            width: 100%;
        }

        .cases-overview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap; /* Allow wrapping on smaller screens */
        }

        .header-left {
            flex: 1;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Column Toggle Styles */
        .column-toggle-container {
            position: relative;
        }

        .column-toggle-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.2s;
        }

        .column-toggle-btn:hover {
            background-color: #00416a;
        }

        .column-toggle-btn i {
            font-size: 1em;
        }

        .visible-count {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 600;
        }

        .column-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            min-width: 200px;
            max-height: 400px;
            overflow-y: auto;
            display: none;
        }

        .column-dropdown.show {
            display: block;
        }

        .column-dropdown-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
            background-color: #f8f9fa;
        }

        .column-dropdown-body {
            padding: 8px 0;
            max-height: 300px;
            overflow-y: auto;
        }

        .column-toggle-all,
        .column-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 16px;
            cursor: pointer;
            transition: background-color 0.2s;
            font-size: 0.9em;
        }

        .column-toggle-all:hover,
        .column-option:hover {
            background-color: #f1f5f9;
        }

        .column-toggle-all input,
        .column-option input {
            margin: 0;
            cursor: pointer;
        }

        .column-toggle-all span,
        .column-option span {
            user-select: none;
            color: var(--text-color);
        }

        /* Column Hide/Show Styles */
        .col-hidden {
            display: none !important;
        }

        .cases-overview-header h3 {
            margin: 0;
            font-size: 1.2em;
            font-weight: 600;
        }

        .total-count {
            font-size: 0.8em;
            color: var(--text-muted-color);
            font-weight: 400;
        }

        .filter-controls {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        .filter-controls form {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
            width: 100%;
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 200px;
        }

        .search-box input {
            width: 100%;
            padding: 0.5rem 2rem 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }

        .search-box i {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted-color);
        }

        .stage-select {
            padding: 0.5rem 2rem 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 0.875rem;
            background-color: white;
            min-width: 200px;
        }

        .filter-button {
            padding: 0.5rem 1rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background-color 0.2s;
        }

        /*.filter-button:hover {
            background-color: var(--primary-dark-color);
        }*/

        .clear-filters {
            padding: 0.5rem 1rem;
            background-color: var(--danger-color);
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        /*.clear-filters:hover {
            background-color: var(--danger-dark-color);
        }*/

        .data-table {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
            font-size: 0.8em;
            table-layout: fixed;
        }

        .table-responsive {
            overflow-x: hidden !important;
            max-width: 100% !important;
            width: 100% !important;
        }

        .data-table th, .data-table td {
            padding: 8px 4px; /* Reduced padding */
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle; /* Align content vertically */
            white-space: normal;
            word-wrap: break-word;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .data-table th {
            font-weight: 600;
            color: var(--text-muted-color);
            background-color: #f8f9fa; /* Light header background */
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }
        .data-table tbody tr:hover {
            background-color: #f1f5f9; /* Slightly different hover */
        }

        .status-badge {
            padding: 4px 10px; /* Slightly larger badges */
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 500;
            color: #fff;
            white-space: nowrap; /* Prevent wrapping */
        }

        .status-lead {
            background-color: var(--info-color);
        }
        .status-lodged {
            background-color: var(--warning-color);
            color: #333;
        }
        .status-pending {
            background-color: var(--secondary-color);
        }
        .status-granted {
            background-color: var(--success-color);
        }
        .status-rfi {
            background-color: var(--danger-color);
        }

        .action-button {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            background-color: var(--primary-color);
            color: #fff;
            cursor: pointer;
            font-size: 0.85em;
            transition: background-color 0.2s;
        }
        .action-button:hover {
            background-color: #00416a; /* Darker shade of primary */
        }

        .last-activity {
            font-size: 0.85em;
            color: var(--text-muted-color);
        }

        .main-content {
            min-height: 812px !important;
        }

        /* Responsive Adjustments */
        @media (max-width: 1200px) {
            .priority-focus {
                grid-template-columns: 1fr; /* Stack focus sections */
            }
        }

        /* Force prevent horizontal scroll */
        body, html {
            overflow-x: hidden !important;
            max-width: 100% !important;
        }

        .main-content, .cases-overview, .focus-container, .card {
            overflow-x: hidden !important;
            max-width: 100% !important;
        }

        /* Make table columns more compact */
        .data-table th:nth-child(1) { width: 16%; } /* Matter */
        .data-table th:nth-child(2) { width: 11%; } /* Client ID */
        .data-table th:nth-child(3) { width: 13%; } /* Client Name */
        .data-table th:nth-child(4) { width: 11%; } /* DOB */
        .data-table th:nth-child(5) { width: 13%; } /* Migration Agent */
        .data-table th:nth-child(6) { width: 13%; } /* Person Responsible */
        .data-table th:nth-child(7) { width: 13%; } /* Person Assisting */
        .data-table th:nth-child(8) { width: 10%; } /* Stage */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 60px;
            }
            .header h1 {
                font-size: 1.5em;
            }
            .kpi-cards {
                grid-template-columns: 1fr 1fr;
            }
            .filter-controls form {
                flex-direction: column;
                align-items: stretch;
            }
            .search-box,
            .stage-select {
                width: 100%;
            }
            .filter-button,
            .clear-filters {
                width: 100%;
                justify-content: center;
            }
            .cases-overview-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .cases-overview-header h3 {
                margin-bottom: 15px;
            }
        }
        @media (max-width: 576px) {
            .kpi-cards {
                grid-template-columns: 1fr;
            }
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            .user-profile {
                margin-top: 10px;
            }
        }

        /* Alert Styles */
        .alert-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1000;
            max-width: 400px;
            width: 100%;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            animation: slideIn 0.3s ease-out;
        }

        .alert-success {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .alert-warning {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            color: #854d0e;
        }

        .alert-danger {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-info {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
        }

        .alert-content {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .alert-content i {
            font-size: 1.25rem;
            margin-top: 0.125rem;
        }

        .alert-message h4 {
            margin: 0 0 0.25rem 0;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .alert-message p {
            margin: 0;
            font-size: 0.875rem;
            color: inherit;
            opacity: 0.9;
        }

        .alert-close {
            background: none;
            border: none;
            padding: 0.25rem;
            cursor: pointer;
            color: inherit;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .alert-close:hover {
            opacity: 1;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .alert.hide {
            animation: slideOut 0.3s ease-in forwards;
        }

        /* Days Left Styling */
        .days-left {
            font-size: 0.85em;
            font-weight: 500;
            margin-left: 0.5rem;
        }

        .text-danger {
            color: var(--danger-color);
        }

        .text-warning {
            color: var(--warning-color);
        }

        .text-success {
            color: var(--success-color);
        }

        /* Stalled Days Styling */
        .stalled-days {
            font-size: 0.85em;
            font-weight: 500;
            margin-left: 0.5rem;
        }

        .text-danger {
            color: var(--danger-color);
        }

        .text-warning {
            color: var(--warning-color);
        }

        .text-info {
            color: var(--info-color);
        }

        /* Case List Container Styling */
        .case-list-container {
            max-height: 300px; /* Adjust this value based on your needs */
            overflow-y: auto;
            padding-right: 10px; /* Add some padding for the scrollbar */
        }

        .case-list-container::-webkit-scrollbar {
            width: 6px;
        }

        .case-list-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .case-list-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .case-list-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Case List Item Styling */
        .case-list li {
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 8px;
        }

        .case-list li:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        /* Task List Container Styling */
        .task-list-container {
            max-height: 300px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .task-list-container::-webkit-scrollbar {
            width: 6px;
        }

        .task-list-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .task-list-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .task-list-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Task List Item Styling */
        .task-list li {
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 8px;
        }

        .task-list li:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        /* Pagination Styling */
        .pagination-container {
            background-color: var(--card-bg-color);
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
        }

        .pagination-links {
            display: flex;
            align-items: center;
        }

        .pagination-links .pagination {
            margin: 0;
            display: flex;
            list-style: none;
            padding: 0;
            gap: 5px;
        }

        .pagination-links .page-item {
            margin: 0;
        }

        .pagination-links .page-link {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            background-color: var(--card-bg-color);
            color: var(--text-color);
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.2s ease;
            font-size: 0.9em;
        }

        .pagination-links .page-link:hover {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination-links .page-item.active .page-link {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination-links .page-item.disabled .page-link {
            background-color: #f8f9fa;
            color: var(--text-muted-color);
            border-color: var(--border-color);
            cursor: not-allowed;
        }

        .pagination-info {
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .pagination-container {
                flex-direction: column;
                gap: 15px;
            }
            
            .pagination-info {
                margin-right: 0;
                text-align: center;
            }
        }
    </style>
@endonce
@endpush

@push('scripts')
@once
<script>
    // Dashboard routes for JavaScript
    window.dashboardRoutes = {
        dashboard: "{{ route('dashboard') }}",
        updateStage: "{{ route('dashboard.update-stage') }}",
        extendDeadline: "{{ route('dashboard.extend-deadline') }}",
        updateTaskCompleted: "{{ route('dashboard.update-task-completed') }}",
        columnPreferences: "{{ route('dashboard.column-preferences') }}"
    };
    
    // Dashboard data for JavaScript
    window.dashboardData = {
        visibleColumns: @json($visibleColumns)
    };
</script>
<script src="{{URL::to('/')}}/js/popover.js"></script>
<script src="{{ asset('js/dashboard-optimized.js') }}"></script>
<script>
$(document).ready(function() {
    //Ajax change on workflow status change
    $(document).on('change', '.stageCls', function () {
        let stageId = $(this).val();
        let itemId = $(this).attr('id').split('_')[1];
        if (stageId) {
            $.ajax({
                url: "{{URL::to('/')}}/update-stage",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                method: 'POST',
                data: { item_id: itemId, stage_id: stageId },
                success: function (response) {
                    if (response.success) {
                        alert('Client matter stage updated successfully!');
                    } else {
                        alert('Failed to update client matter stage.');
                    }
                    location.reload();
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('An error occurred while updating status.');
                }
            });
        }
    });



    // Listen for changes on the status dropdown
    $('.status-dropdown').change(function() {
        var status = $(this).val(); // Get the selected status
        var rowNumber = $(this).closest('tr').find('td:first').text(); // Get the row number (first cell value)
        // Display an alert or perform an action
        alert('Status for row ' + rowNumber + ' changed to ' + status);
        // Here, you could send an AJAX request to save the status change, for example
    });

    // Handle filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();

        // Get form data
        const formData = $(this).serialize();

        // Update URL with filter parameters and reset to page 1
        const url = new URL(window.location.href);
        const params = new URLSearchParams(formData);
        params.delete('page'); // Remove page parameter to reset to page 1
        url.search = params.toString();

        // Reload page with new filters
        window.location.href = url.toString();
    });

    // Auto-submit form when stage is changed
    $('.stage-select').on('change', function() {
        $('#filterForm').submit();
    });

    // Function to clear filters and reset pagination
    window.clearFiltersAndReset = function() {
        // Redirect to dashboard without any parameters
        window.location.href = "{{ route('dashboard') }}";
    }

    // Column Toggle Functionality
    $(document).ready(function() {
        // Toggle dropdown visibility
        $('#columnToggleBtn').on('click', function(e) {
            e.stopPropagation();
            $('#columnDropdown').toggleClass('show');
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.column-toggle-container').length) {
                $('#columnDropdown').removeClass('show');
            }
        });

        // Handle individual column toggle
        $('input[name="column"]').on('change', function() {
            const columnValue = $(this).val();
            const isChecked = $(this).is(':checked');
            
            // Show/hide columns
            if (isChecked) {
                $('.col-' + columnValue).removeClass('col-hidden');
            } else {
                $('.col-' + columnValue).addClass('col-hidden');
            }
            
            // Update visible count
            updateVisibleCount();
            
            // Update toggle all checkbox
            updateToggleAllState();
            
            // Save preferences
            saveColumnPreferences();
        });

        // Handle toggle all functionality
        $('#toggleAllColumns').on('change', function() {
            const isChecked = $(this).is(':checked');
            
            // Update all individual checkboxes
            $('input[name="column"]').prop('checked', isChecked);
            
            // Show/hide all columns
            if (isChecked) {
                $('[class*="col-"]').removeClass('col-hidden');
            } else {
                $('[class*="col-"]').addClass('col-hidden');
            }
            
            // Update visible count
            updateVisibleCount();
            
            // Save preferences
            saveColumnPreferences();
        });

        // Function to update visible count
        function updateVisibleCount() {
            const visibleCount = $('input[name="column"]:checked').length;
            $('.visible-count').text(visibleCount);
        }

        // Function to update toggle all state
        function updateToggleAllState() {
            const totalColumns = $('input[name="column"]').length;
            const checkedColumns = $('input[name="column"]:checked').length;
            
            if (checkedColumns === 0) {
                $('#toggleAllColumns').prop('indeterminate', false).prop('checked', false);
            } else if (checkedColumns === totalColumns) {
                $('#toggleAllColumns').prop('indeterminate', false).prop('checked', true);
            } else {
                $('#toggleAllColumns').prop('indeterminate', true);
            }
        }

        // Function to save column preferences
        function saveColumnPreferences() {
            const visibleColumns = [];
            $('input[name="column"]:checked').each(function() {
                visibleColumns.push($(this).val());
            });

            $.ajax({
                url: "{{ route('dashboard.column-preferences') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    visible_columns: visibleColumns
                },
                success: function(response) {
                    // Optional: Show success message
                    console.log('Column preferences saved');
                },
                error: function(xhr, status, error) {
                    console.error('Failed to save column preferences:', error);
                }
            });
        }

        // Apply initial column visibility based on server preferences
        function applyInitialColumnVisibility() {
            const visibleColumns = @json($visibleColumns);
            const allColumns = ['matter', 'client_id', 'client_name', 'dob', 'migration_agent', 'person_responsible', 'person_assisting', 'stage'];
            
            // Hide columns that are not in visibleColumns
            allColumns.forEach(function(column) {
                if (!visibleColumns.includes(column)) {
                    $('.col-' + column).addClass('col-hidden');
                    $('input[name="column"][value="' + column + '"]').prop('checked', false);
                }
            });
            
            // Update UI state
            updateVisibleCount();
            updateToggleAllState();
        }

        // Initialize column visibility
        applyInitialColumnVisibility();
    });

    // Debounce search input
    /*let searchTimeout;
    $('input[name="client_name"]').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            $('#filterForm').submit();
        }, 500);
    });*/

    const $input = $('input[name="client_name"]');

    $input.on('blur', function() {
        if ($(this).val().trim() !== '') {
            $('#filterForm').submit();
        }
    });

    $input.on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault(); // Prevent form from submitting twice if it's in a form
            $('#filterForm').submit();
        }
    });

});

</script>

<style>
.custom-select {
    border-radius: 10px; /* Adjust the value for the desired curvature */
    padding: 8px; /* Optional: Add padding for better visual appearance */
    border: 1px solid #ced4da; /* Optional: Adjust the border color if needed */
}

</style>

<script>
jQuery(document).ready(function($){
    $(".invitesselects2").select2({
        dropdownParent: $("#create_appoint")
    });

	$(document).delegate('#create_task', 'click', function(){
		$('#create_task_modal').modal('show');
		$('.cleintselect2').select2({
			dropdownParent: $('#create_task_modal .modal-content'),
		});
	});

	$(document).delegate('.opentaskview', 'click', function(){
		$('#opentaskview').modal('show');
		var v = $(this).attr('id');
		$.ajax({
			url: site_url+'/get-task-detail',
			type:'GET',
			data:{task_id:v},
			success: function(responses){

				$('.taskview').html(responses);
			}
		});
	});

});
</script>
@endonce
@endpush