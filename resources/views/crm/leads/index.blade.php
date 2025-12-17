@extends('layouts.crm_client_detail')
@section('title', 'Leads')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/listing-pagination.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-datepicker.css') }}">
<style>
    /* Page-specific styles for leads index page */
    
    /* Edit Icon Button Styling */
    .btn-edit-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        color: white !important;
        text-decoration: none;
        box-shadow: 0 2px 4px rgba(102, 126, 234, 0.2);
    }

    .btn-edit-icon:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
        color: white !important;
        text-decoration: none;
    }

    .btn-edit-icon:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.25);
        color: white !important;
    }

    .btn-edit-icon i {
        font-size: 14px;
        color: white;
    }

    .listing-container .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .listing-container .card-header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .listing-container .per-page-select {
        border: 1px solid white !important;
        border-radius: 8px !important;
        background: white !important;
        color: #667eea !important;
        font-weight: 600 !important;
        padding: 8px 16px !important;
        min-width: 110px;
        width: auto !important;
        flex: 0 0 auto;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .listing-container .per-page-select:focus {
        outline: none;
        border-color: #667eea !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
    }

    .listing-container .per-page-select option {
        background: white;
        color: #667eea;
    }

    .listing-container .filter_panel {
        background: #f8fafc;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        display: none;
        border: 1px solid #e2e8f0;
    }

    .listing-container .filter_panel h4 {
        color: #1e293b;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .active-filters-badge {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border-radius: 12px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .clear-filter-btn {
        background: transparent;
        border: 2px solid #ef4444;
        color: #ef4444;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .clear-filter-btn:hover {
        background: #ef4444;
        color: white;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.open {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .status-badge.closed {
        background: linear-gradient(135deg, #f87171 0%, #dc2626 100%);
        color: white;
    }

    .status-badge.converted {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: white;
    }

    .sortable-header a {
        color: inherit;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .sortable-header i {
        color: #94a3b8;
    }
</style>
@include('crm.clients.partials.enhanced-date-filter-styles')
@endsection

@section('content')
<div class="listing-container">
    <section class="listing-section" style="padding-top: 40px;">
        <div class="listing-section-body">
            @include('../Elements/flash-message')

            <div class="card">
                <div class="custom-error-msg">
                </div>
                <div class="card-header">
                    <h4>All Leads</h4>

                    <div class="card-header-actions">
                        <a href="{{ route('clients.insights', ['section' => 'leads']) }}" class="btn btn-theme btn-theme-sm" title="Lead Insights">
                            <i class="fas fa-chart-line"></i> Insights
                        </a>
                        <a href="{{route('leads.create')}}" class="btn btn-primary">Create Lead</a>
                        <select name="per_page" id="per_page" class="form-control per-page-select">
                            @foreach([10, 20, 50, 100, 200] as $option)
                                <option value="{{ $option }}" {{ ($perPage ?? 20) == $option ? 'selected' : '' }}>
                                    {{ $option }} / page
                                </option>
                            @endforeach
                        </select>
                        <a href="javascript:;" class="btn btn-theme btn-theme-sm filter_btn">
                            <i class="fas fa-filter"></i> Filter
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <ul class="nav nav-pills" id="client_tabs" role="tablist">
                        <li class="nav-item is_checked_client" style="display:none;">
                            <a class="btn btn-primary emailmodal" id=""  href="javascript:;"  >Send Mail</a>
                        </li>
                        <li class="nav-item is_checked_client" style="display:none;">
                            <a class="btn btn-primary " id=""  href="javascript:;"  >Change Assignee</a>
                        </li>

                        <li class="nav-item is_checked_client_merge" style="display:none;">
                            <a class="btn btn-primary " id=""  href="javascript:;"  >Merge</a>
                        </li>

                        <li class="nav-item is_checked_clientn">
                            <a class="nav-link " id="clients-tab"  href="{{URL::to('/clients')}}" >Clients</a>
                        </li>
                        <li class="nav-item is_checked_clientn">
                            <a class="nav-link" id="archived-tab"  href="{{URL::to('/archived')}}" >Archived</a>
                        </li>
                        <li class="nav-item is_checked_clientn">
                            <a class="nav-link active" id="lead-tab"  href="{{URL::to('/leads')}}" >Leads</a>
                        </li>
                    </ul>

                    @php
                        $leadFilters = collect([
                            'client_id' => request('client_id'),
                            'name' => request('name'),
                            'email' => request('email'),
                            'phone' => request('phone'),
                            'service' => request('service'),
                            'status_filter' => request('status_filter'),
                            'lead_quality' => request('lead_quality'),
                            'quick_date_range' => request('quick_date_range'),
                            'from_date' => request('from_date'),
                            'to_date' => request('to_date'),
                            'date_filter_field' => request('date_filter_field') !== 'created_at' ? request('date_filter_field') : null,
                        ]);
                        $activeLeadFilters = $leadFilters->filter(function ($value) {
                            return $value !== null && $value !== '';
                        })->count();
                        $qualityList = ($qualityOptions ?? collect())->filter()->values();
                        if ($qualityList->isEmpty()) {
                            $qualityList = collect([5, 4, 3, 2, 1]);
                        }
                        $statusList = ($statusOptions ?? collect())->filter()->values();
                        $fallbackStatuses = collect(['New', 'In Progress', 'Converted', 'Closed', 'Lost']);
                    @endphp

                    <div class="filter_panel">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h4>
                                Search By Details
                                @if($activeLeadFilters > 0)
                                    <span class="active-filters-badge">
                                        <i class="fas fa-filter"></i> {{ $activeLeadFilters }} Active
                                    </span>
                                @endif
                            </h4>
                            @if($activeLeadFilters > 0)
                                <button type="button" class="clear-filter-btn" id="clearLeadFilters">
                                    <i class="fas fa-undo"></i> Clear Filters
                                </button>
                            @endif
                        </div>
                        <form action="{{URL::to('/leads')}}" method="get" id="leadFilterForm">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="client_id">Client ID</label>
                                        <input type="text" name="client_id" id="client_id" value="{{ request('client_id') }}" class="form-control" placeholder="Client ID">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" id="name" value="{{ request('name') }}" class="form-control" placeholder="Lead name">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="text" name="email" id="email" value="{{ request('email') }}" class="form-control" placeholder="Lead email">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="phone">Phone</label>
                                        <input type="text" name="phone" id="phone" value="{{ request('phone') }}" class="form-control" placeholder="Lead phone">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="service">Service</label>
                                        <input type="text" name="service" id="service" value="{{ request('service') }}" class="form-control" placeholder="Interested service">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status_filter">Status</label>
                                        <select name="status_filter" id="status_filter" class="form-control">
                                            <option value="">All Statuses</option>
                                            @if(($statusList ?? collect())->isNotEmpty())
                                                @foreach($statusList as $status)
                                                    <option value="{{ $status }}" {{ request('status_filter') == $status ? 'selected' : '' }}>
                                                        {{ ucfirst($status) }}
                                                    </option>
                                                @endforeach
                                            @else
                                                @foreach($fallbackStatuses as $status)
                                                    <option value="{{ \Illuminate\Support\Str::slug($status, '_') }}" {{ request('status_filter') == \Illuminate\Support\Str::slug($status, '_') ? 'selected' : '' }}>
                                                        {{ $status }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lead_quality">Lead Quality</label>
                                        <select name="lead_quality" id="lead_quality" class="form-control">
                                            <option value="">All Levels</option>
                                            @foreach($qualityList as $quality)
                                                <option value="{{ $quality }}" {{ request('lead_quality') == $quality ? 'selected' : '' }}>
                                                    {{ is_numeric($quality) ? $quality . ' Star' : ucfirst($quality) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_filter_field">Date Field</label>
                                        <select name="date_filter_field" id="date_filter_field" class="form-control">
                                            <option value="created_at" {{ request('date_filter_field', 'created_at') === 'created_at' ? 'selected' : '' }}>Created Date</option>
                                            <option value="updated_at" {{ request('date_filter_field') === 'updated_at' ? 'selected' : '' }}>Last Updated</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="date-filter-section mt-2">
                                <input type="hidden" name="quick_date_range" id="lead_quick_date_range" value="{{ request('quick_date_range') }}">
                                @php
                                    $quickFilters = [
                                        'today' => 'Today',
                                        'this_week' => 'This Week',
                                        'this_month' => 'This Month',
                                        'last_month' => 'Last Month',
                                        'last_30_days' => 'Last 30 Days',
                                        'last_90_days' => 'Last 90 Days',
                                        'this_year' => 'This Year',
                                        'last_year' => 'Last Year',
                                    ];
                                @endphp
                                <div class="quick-filters">
                                    @foreach($quickFilters as $key => $label)
                                        <span class="quick-filter-chip lead-quick-filter {{ request('quick_date_range') === $key ? 'active' : '' }}" data-filter="{{ $key }}">
                                            <i class="fas fa-calendar"></i> {{ $label }}
                                        </span>
                                    @endforeach
                                </div>
                                <div class="divider-text">Or Custom Range</div>
                                <div class="date-range-wrapper">
                                    <div class="form-group">
                                        <label for="lead_from_date">From Date</label>
                                        <input type="date" name="from_date" id="lead_from_date" value="{{ request('from_date') }}" class="form-control">
                                    </div>
                                    <span class="date-range-arrow">→</span>
                                    <div class="form-group">
                                        <label for="lead_to_date">To Date</label>
                                        <input type="date" name="to_date" id="lead_to_date" value="{{ request('to_date') }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12 text-center">
                                    <div class="filter-buttons-container">
                                        <button type="submit" class="btn btn-primary btn-theme-lg mr-3">Apply Filters</button>
                                        <a class="btn btn-info" href="{{URL::to('/leads')}}">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center">
                                        <div class="custom-checkbox custom-checkbox-table custom-control">
                                            <input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input" id="checkbox-all">
                                            <label for="checkbox-all" class="custom-control-label">&nbsp;</label>
                                        </div>
                                    </th>
                                    <th class="sortable-header">@sortablelink('first_name', 'Name')</th>
                                    <th>Info</th>
                                    <th class="sortable-header">@sortablelink('created_at', 'Contact Date')</th>
                                    <th class="sortable-header">@sortablelink('lead_quality', 'Level & Status')</th>
                                    <th class="sortable-header">@sortablelink('status', 'Status')</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="tdata">
                                @if(@$totalData !== 0)
                                <?php $i = 0; ?>
                                @foreach (@$lists as $list)
                                    <?php
                                    // Followup functionality removed
                                    ?>
                                    <tr id="id_{{@$list->id}}">
                                        <td style="white-space: initial;" class="text-center">
                                            <div class="custom-checkbox custom-control">
                                                <input data-id="{{@$list->id}}" data-email="{{@$list->email}}" data-name="{{@$list->first_name}} {{@$list->last_name}}" data-clientid="{{@$list->client_id}}" type="checkbox" data-checkboxes="mygroup" class="cb-element custom-control-input  your-checkbox" id="checkbox-{{$i}}">
                                                <label for="checkbox-{{$i}}" class="custom-control-label">&nbsp;</label>
                                            </div>
                                        </td>
                                        <td style="white-space: initial;">
                                            <a href="{{ route('clients.detail', base64_encode(convert_uuencode(@$list->id))) }}">
                                                {{ @$list->first_name == "" ? config('constants.empty') : Str::limit(@$list->first_name, '50', '...') }}
                                                {{ @$list->last_name == "" ? config('constants.empty') : Str::limit(@$list->last_name, '50', '...') }}
                                            </a>

                                        </td>
                                        <td><i class="fa fa-mobile"></i> {{@$list->phone}} <br/> <i class="fa fa-envelope"></i> {{@$list->email}}</td>
                                        <td>{{@$list->service}} <br/> {{date('d/m/Y h:i:s a', strtotime($list->created_at))}}</td>
                                        <td><div class="lead_stars"><i class="fa fa-star"></i><span>{{@$list->lead_quality}}</span></div></td>
                                        <td>
                                            @php
                                                $statusValue = @$list->status ?: config('constants.empty');
                                                $statusSlug = \Illuminate\Support\Str::slug(strtolower($statusValue), '_');
                                            @endphp
                                            <span class="status-badge {{ $statusSlug }}">
                                                <i class="fas fa-circle"></i> {{ $statusValue }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{route('clients.edit', base64_encode(convert_uuencode(@$list->id)))}}" class="btn-edit-icon" title="Edit Lead">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                @endforeach

                                @else
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 20px;">
                                            No Record Found
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="card-footer">
                    {!! $lists->appends(\Request::except('page'))->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="assignlead_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                  <h4 class="modal-title">Assign Lead</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
            </div>
            <form action="{{ url('leads/assign') }}" method="POST" name="add-assign" autocomplete="off" enctype="multipart/form-data" id="addnoteform">
    @csrf
    <div class="modal-body">
        <div class="form-group row">
            <div class="col-sm-12">
                <input id="mlead_id" name="mlead_id" type="hidden" value="">
                <select name="assignto" class="form-control select2 " style="width: 100%;" data-select2-id="1" tabindex="-1" aria-hidden="true">
                    <option value="">Select</option>
                    @foreach(\App\Models\Admin::Where('role', '!=', '7')->get() as $ulist)
                    <option value="{{@$ulist->id}}">{{@$ulist->first_name}} {{@$ulist->last_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary" onClick='customValidate("add-assign")'>
            <i class="fa fa-save"></i> Assign Lead
        </button>
    </div>
</form>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    jQuery(document).ready(function($){
        $('#per_page').on('change', function(){
            var currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('per_page', $(this).val());
            currentUrl.searchParams.delete('page');
            window.location.href = currentUrl.toString();
        });

        $('.lead-quick-filter').on('click', function(){
            var filter = $(this).data('filter');
            $('#lead_quick_date_range').val(filter);
            $('#lead_from_date, #lead_to_date').val('');
            $('#leadFilterForm').submit();
        });

        $('#lead_from_date, #lead_to_date').on('change', function(){
            $('#lead_quick_date_range').val('');
        });

        $('#clearLeadFilters').on('click', function(){
            window.location.href = "{{ URL::to('/leads') }}";
        });

        $('.listing-container .filter_btn').on('click', function(){
            $('.listing-container .filter_panel').toggle();
        });
        
        $('.listing-container .assignlead_modal').on('click', function(){
              var val = $(this).attr('mleadid');
              $('#assignlead_modal #mlead_id').val(val);
              $('#assignlead_modal').modal('show');
          });

        $('.listing-container [data-checkboxes]').each(function () {
            var me = $(this),
            group = me.data('checkboxes'),
            role = me.data('checkbox-role');

            me.change(function () {
                var all = $('.listing-container [data-checkboxes="' + group + '"]:not([data-checkbox-role="dad"])'),
                checked = $('.listing-container [data-checkboxes="' + group + '"]:not([data-checkbox-role="dad"]):checked'),
                dad = $('.listing-container [data-checkboxes="' + group + '"][data-checkbox-role="dad"]'),
                total = all.length,
                checked_length = checked.length;
                if (role == 'dad') {
                    if (me.is(':checked')) {
                        all.prop('checked', true);
                        $('.listing-container .is_checked_client').show();
                        $('.listing-container .is_checked_clientn').hide();
                    } else {
                        all.prop('checked', false);
                        $('.listing-container .is_checked_client').hide();
                        $('.listing-container .is_checked_clientn').show();
                    }
                } else {
                    if (checked_length >= total) {
                        dad.prop('checked', true);
                        $('.listing-container .is_checked_client').show();
                        $('.listing-container .is_checked_clientn').hide();
                    } else {
                        dad.prop('checked', false);
                        $('.listing-container .is_checked_client').hide();
                        $('.listing-container .is_checked_clientn').show();
                    }
                }
                if(checked_length == 2){
                    $('.listing-container .is_checked_client_merge').show();
                } else {
                    $('.listing-container .is_checked_client_merge').hide();
                }
            });
        });

        var clickedOrder = [];
        var clickedIds = [];
        $(document).delegate('.listing-container .your-checkbox', 'click', function(){
            var clicked_id = $(this).data('id');
            var nameStr = $(this).attr('data-name');
            var clientidStr = $(this).attr('data-clientid');
            var finalStr = nameStr+'('+clientidStr+')';
            if ($(this).is(':checked')) {
                clickedOrder.push(finalStr);
                clickedIds.push(clicked_id);
            } else {
                var index = clickedOrder.indexOf(finalStr);
                if (index !== -1) {
                    clickedOrder.splice(index, 1);
                }
                var index1 = clickedIds.indexOf(clicked_id);
                if (index1 !== -1) {
                    clickedIds.splice(index1, 1);
                }
            }
        });

        //merge task
        $(document).delegate('.listing-container .is_checked_client_merge', 'click', function(){
            if ( clickedOrder.length > 0 && clickedOrder.length == 2 )
            {
                var mergeStr = "Are you sure want to merge "+clickedOrder[0]+" record into this "+clickedOrder[1]+" record?";
                if (confirm(mergeStr)) {
                    $.ajax({
                        type:'post',
                        url:"{{URL::to('/')}}/merge_records",
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: {merge_from:clickedIds[0],merge_into:clickedIds[1]},
                        success: function(response){
                            var obj = $.parseJSON(response);
                            location.reload(true);
                        }
                    });
                }
            }
        });

        $('.listing-container .cb-element').change(function () {
            if ($('.listing-container .cb-element:checked').length == $('.listing-container .cb-element').length){
                $('.listing-container #checkbox-all').prop('checked',true);
            }
            else {
                $('.listing-container #checkbox-all').prop('checked',false);
            }

            if ($('.listing-container .cb-element:checked').length > 0){
                $('.listing-container .is_checked_client').show();
                $('.listing-container .is_checked_clientn').hide();
            }else{
                $('.listing-container .is_checked_client').hide();
                $('.listing-container .is_checked_clientn').show();
            }
        });

        $(document).delegate('.listing-container .emailmodal', 'click', function(){
            $('#emailmodal').modal('show');
            var array = [];
            var data = [];
            $('.listing-container .cb-element:checked').each(function(){
                var id = $(this).attr('data-id');
                array.push(id);
                var email = $(this).attr('data-email');
                var name = $(this).attr('data-name');
                var status = 'Client';

                data.push({
                    id: id,
                    text: name,
                    html:  "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

                        "<div  class='ag-flex ag-align-start'>" +
                            "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'>"+name+"</span>&nbsp;</div>" +
                            "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'>"+email+"</small ></div>" +

                        "</div>" +
                        "</div>" +
                        "<div class='ag-flex ag-flex-column ag-align-end'>" +

                            "<span class='ui label yellow select2-result-repository__statistics'>"+ status +

                            "</span>" +
                        "</div>" +
                        "</div>",
                    title: name
                });
            });

            $(".js-data-example-ajax").select2({
                data: data,
                escapeMarkup: function(markup) {
                    return markup;
                },
                templateResult: function(data) {
                    return data.html;
                },
                templateSelection: function(data) {
                    return data.text;
                }
            })

            $('.js-data-example-ajax').val(array);
            $('.js-data-example-ajax').trigger('change');

        });

        $(document).delegate('.listing-container .clientemail', 'click', function(){
            $('#emailmodal').modal('show');
            var array = [];
            var data = [];

            var id = $(this).attr('data-id');
            array.push(id);
            var email = $(this).attr('data-email');
            var name = $(this).attr('data-name');
            var status = 'Client';

            data.push({
                id: id,
                text: name,
                html:  "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

                    "<div  class='ag-flex ag-align-start'>" +
                        "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'>"+name+"</span>&nbsp;</div>" +
                        "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'>"+email+"</small ></div>" +

                    "</div>" +
                    "</div>" +
                    "<div class='ag-flex ag-flex-column ag-align-end'>" +

                        "<span class='ui label yellow select2-result-repository__statistics'>"+ status +

                        "</span>" +
                    "</div>" +
                    "</div>",
                title: name
            });

            $(".js-data-example-ajax").select2({
                data: data,
                escapeMarkup: function(markup) {
                    return markup;
                },
                templateResult: function(data) {
                    return data.html;
                },
                templateSelection: function(data) {
                    return data.text;
                }
            })

            $('.js-data-example-ajax').val(array);
            $('.js-data-example-ajax').trigger('change');

        });

        $(document).delegate('.listing-container .selecttemplate', 'change', function(){
            var v = $(this).val();
            $.ajax({
                url: '{{URL::to('/get-templates')}}',
                type:'GET',
                datatype:'json',
                data:{id:v},
                success: function(response){
                    var res = JSON.parse(response);
                    $('.selectedsubject').val(res.subject);
                    // Clear and set TinyMCE editor content
                    $(".summernote-simple").each(function() {
                        var editorId = $(this).attr('id');
                        if (editorId && typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
                            tinymce.get(editorId).setContent(res.description || '');
                        } else {
                            $(this).val(res.description || '');
                        }
                    });
                }
            });
        });

        $('.js-data-example-ajax').select2({
            multiple: true,
            closeOnSelect: false,
            dropdownParent: $('#emailmodal'),
            ajax: {
                url: '{{URL::to('/clients/get-recipients')}}',
                dataType: 'json',
                processResults: function (data) {
                  // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: data.items
                    };
                },
                cache: true
            },
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });

        $('.js-data-example-ajaxcc').select2({
            multiple: true,
            closeOnSelect: false,
            dropdownParent: $('#emailmodal'),
            ajax: {
                url: '{{URL::to('/clients/get-recipients')}}',
                dataType: 'json',
                processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: data.items
                    };
                },
                cache: true
            },
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });

        function formatRepo (repo) {
            if (repo.loading) {
                return repo.text;
            }

            var $container = $(
                "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

                "<div  class='ag-flex ag-align-start'>" +
                    "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
                    "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +

                "</div>" +
                "</div>" +
                "<div class='ag-flex ag-flex-column ag-align-end'>" +

                    "<span class='ui label yellow select2-result-repository__statistics'>" +

                    "</span>" +
                "</div>" +
                "</div>"
            );

            $container.find(".select2-result-repository__title").text(repo.name);
            $container.find(".select2-result-repository__description").text(repo.email);
            $container.find(".select2-result-repository__statistics").append(repo.status);

            return $container;
        }

        function formatRepoSelection (repo) {
            return repo.name || repo.text;
        }
    });
</script>
@endpush

