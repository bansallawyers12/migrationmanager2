@extends('layouts.admin_client_detail')
@section('title', 'Clients Matters')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/listing-pagination.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-datepicker.css') }}">
<style>
    /* Page-specific styles for clientsmatterslist */
    .listing-container .table th:first-child,
    .listing-container .table td:first-child {
        min-width: 250px;
        max-width: 300px;
        width: 25%;
    }
    
    .listing-container .table th:first-child {
        width: 25%;
    }
    
    /* Professional Action Button Design */
    .listing-container .table td .dropdown-toggle {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border: 1px solid #667eea !important;
        min-width: 80px;
        max-width: 90px;
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 500;
        color: white !important;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(102, 126, 234, 0.2);
        transition: all 0.3s ease;
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }
    
    .listing-container .table td .dropdown-toggle:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%) !important;
        border-color: #5a6fd8 !important;
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
        transform: translateY(-1px);
    }
    
    .listing-container .table td .dropdown-toggle:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.25);
    }
    
    .listing-container .table td .dropdown-toggle::after {
        content: '';
        display: inline-block;
        margin-left: 4px;
        vertical-align: middle;
        border-top: 4px solid;
        border-right: 4px solid transparent;
        border-bottom: 0;
        border-left: 4px solid transparent;
        transition: transform 0.2s ease;
    }
    
    .listing-container .table td .dropdown-toggle.show::after {
        transform: rotate(180deg);
    }
    
    /* Enhanced Dropdown Menu */
    .listing-container .dropdown-menu {
        position: absolute !important;
        top: 100% !important;
        right: 0 !important;
        left: auto !important;
        float: none !important;
        min-width: 180px;
        padding: 8px 0;
        margin: 4px 0 0;
        font-size: 14px;
        text-align: left;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 1px solid rgba(102, 126, 234, 0.2);
        border-radius: 8px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        background-clip: padding-box;
        z-index: 9999 !important;
        transform: none !important;
        max-height: none !important;
        overflow: visible !important;
        backdrop-filter: blur(10px);
    }
    
    .listing-container .dropdown-menu.show {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
    
    /* Dropdown Items Styling */
    .listing-container .dropdown-item {
        display: block;
        width: 100%;
        padding: 10px 20px;
        clear: both;
        font-weight: 500;
        color: #495057;
        text-align: inherit;
        white-space: nowrap;
        background-color: transparent;
        border: 0;
        text-decoration: none;
        transition: all 0.2s ease;
        border-radius: 4px;
        margin: 2px 8px;
        width: calc(100% - 16px);
    }
    
    .listing-container .dropdown-item:hover {
        color: #667eea;
        text-decoration: none;
        background: linear-gradient(135deg, #f8f9ff 0%, #e8ecff 100%);
        transform: translateX(2px);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
    }
    
    .listing-container .dropdown-item:active {
        background: linear-gradient(135deg, #e8ecff 0%, #d8e0ff 100%);
        transform: translateX(1px);
    }
    
    .listing-container .dropdown-item.has-icon {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .listing-container .dropdown-item.has-icon i {
        width: 16px;
        text-align: center;
    }
    
    /* Ensure all dropdown items are visible */
    .listing-container .dropdown-menu .dropdown-item {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        height: auto !important;
        min-height: 32px !important;
        line-height: 1.5 !important;
    }
    
    /* Remove any potential overflow restrictions */
    .listing-container .table td {
        overflow: visible !important;
    }
    
    .listing-container .table td .dropdown {
        overflow: visible !important;
        position: relative;
        display: inline-block;
    }
    
    /* Ensure dropdown container doesn't clip content */
    .listing-container .dropdown {
        overflow: visible !important;
    }
    .thCls,.tdCls {
        white-space: initial !important;
    }
</style>
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
                    <h4>All Clients Matters</h4>
                    <a href="javascript:;" class="btn btn-theme btn-theme-sm filter_btn"><i class="fas fa-filter"></i> Filter</a>
                </div>
                
                <div class="card-body">
                    <div class="filter_panel">
                        <h4>Search By Details</h4>
                        <form action="{{URL::to('/admin/clientsmatterslist')}}" method="get">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="matter" class="col-form-label" style="color:#4a5568 !important;">Matter</label>
                                        <select class="form-control" name="sel_matter_id">
                                            <option value="">Select Matter</option>
                                            @foreach(\App\Models\Matter::orderBy('title', 'asc')->get() as $matter)
                                            <option value="{{ $matter->id }}" {{ request('sel_matter_id') == $matter->id ? 'selected' : '' }}>{{ $matter->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pnr" class="col-form-label" style="color:#4a5568 !important;">Client ID</label>
                                        <input type="text" name="client_id" value="{{ Request::get('client_id') }}" class="form-control" data-valid="" autocomplete="off" placeholder="Client ID" id="client_id">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="company_name" class="col-form-label" style="color:#4a5568 !important;">Client Name</label>
                                        <input type="text" name="name" value="{{ Request::get('name') }}" class="form-control agent_company_name" data-valid="" autocomplete="off" placeholder="Name" id="name">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="filter-buttons-container">
                                        <button type="submit" class="btn btn-primary btn-theme-lg mr-3">Search</button>
                                        <a class="btn btn-info" href="{{URL::to('/admin/clientsmatterslist')}}">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="thCls">Matter</th>
                                    <th class="thCls">Client ID</th>
                                    <th class="thCls">Client Name</th>
                                    <th class="thCls">DOB</th>
                                    <th class="thCls">Migration Agent</th>
                                    <th class="thCls">Person Responsible</th>
                                    <th class="thCls">Person Assisting</th>
                                    <th class="thCls">Created At</th>
                                    @if(Auth::user()->role == 1)
                                    <th class="thCls">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="tdata">
                                @if(@$totalData !== 0)
                                <?php $i=0; ?>
                                    @foreach (@$lists as $list)
                                        <?php
                                        $mig_agent_info = \App\Models\Admin::select('first_name','last_name')->where('id', $list->sel_migration_agent)->first();
                                        $person_responsible = \App\Models\Admin::select('first_name','last_name')->where('id', $list->sel_person_responsible)->first();
                                        $person_assisting = \App\Models\Admin::select('first_name','last_name')->where('id', $list->sel_person_assisting)->first();
                                        ?>
                                        <tr id="id_{{@$list->id}}">
                                            <td class="tdCls"><a href="{{URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$list->client_id)).'/'.$list->client_unique_matter_no )}}">{{ @$list->title == "" ? config('constants.empty') : str_limit(@$list->title, '50', '...') }} ({{ @$list->client_unique_matter_no == "" ? config('constants.empty') : str_limit(@$list->client_unique_matter_no, '50', '...') }}) </a></td>
                                            <td class="tdCls">{{ @$list->client_unique_id == "" ? config('constants.empty') : str_limit(@$list->client_unique_id, '50', '...') }}</td>
                                            <td class="tdCls"><a href="{{URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$list->client_id)) )}}">{{ @$list->first_name == "" ? config('constants.empty') : str_limit(@$list->first_name, '50', '...') }} {{ @$list->last_name == "" ? config('constants.empty') : str_limit(@$list->last_name, '50', '...') }}</a></td>
                                            <td class="tdCls">{{ @$list->dob == "" ? config('constants.empty') : (strtotime(@$list->dob) ? date('d/m/Y', strtotime(@$list->dob)) : str_limit(@$list->dob, '50', '...')) }}</td>
                                            <td class="tdCls">{{ @$mig_agent_info->first_name == "" ? config('constants.empty') : str_limit(@$mig_agent_info->first_name, '50', '...') }} {{ @$mig_agent_info->last_name == "" ? config('constants.empty') : str_limit(@$mig_agent_info->last_name, '50', '...') }}</td>
                                            <td class="tdCls">{{ @$person_responsible->first_name == "" ? config('constants.empty') : str_limit(@$person_responsible->first_name, '50', '...') }} {{ @$person_responsible->last_name == "" ? config('constants.empty') : str_limit(@$person_responsible->last_name, '50', '...') }}</td>
                                            <td class="tdCls">{{ @$person_assisting->first_name == "" ? config('constants.empty') : str_limit(@$person_assisting->first_name, '50', '...') }} {{ @$person_assisting->last_name == "" ? config('constants.empty') : str_limit(@$person_assisting->last_name, '50', '...') }}</td>
                                            <td class="tdCls">{{date('d/m/Y', strtotime($list->created_at))}}</td>
                                            @if(Auth::user()->role == 1)
                                            <td class="tdCls">
                                                <div class="dropdown d-inline">
                                                    <button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item has-icon" href="javascript:;" onclick="deleteAction({{$list->id}}, 'client_matters')"><i class="fas fa-trash"></i> Delete Matter</a>
                                                    </div>
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="{{ Auth::user()->role == 1 ? '9' : '8' }}" style="text-align: center; padding: 20px;">
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
@endsection
@section('scripts')
<script>
jQuery(document).ready(function($){
    $('.listing-container .filter_btn').on('click', function(){
        $('.listing-container .filter_panel').slideToggle();
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
                    $('.listing-container .is_checked_clientn').hide();
                } else {
                    all.prop('checked', false);
                    $('.listing-container .is_checked_clientn').show();
                }
            } else {
                if (checked_length >= total) {
                    dad.prop('checked', true);
                    $('.listing-container .is_checked_clientn').hide();
                } else {
                    dad.prop('checked', false);
                    $('.listing-container .is_checked_clientn').show();
                }
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

    $('.listing-container .cb-element').change(function () {
        if ($('.listing-container .cb-element:checked').length == $('.listing-container .cb-element').length){
            $('.listing-container #checkbox-all').prop('checked',true);
        } else {
            $('.listing-container #checkbox-all').prop('checked',false);
        }
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
            url: '{{URL::to('/admin/get-templates')}}',
            type:'GET',
            datatype:'json',
            data:{id:v},
            success: function(response){
                var res = JSON.parse(response);
                $('.selectedsubject').val(res.subject);
                $(".summernote-simple").summernote('reset');
                $(".summernote-simple").summernote('code', res.description);
                $(".summernote-simple").val(res.description);
            }
        });
    });

    $('.js-data-example-ajax').select2({
        multiple: true,
        closeOnSelect: false,
        dropdownParent: $('#emailmodal'),
        ajax: {
            url: '{{URL::to('/admin/clients/get-recipients')}}',
            dataType: 'json',
            processResults: function (data) {
                // Transforms the top-level key of the response object from 'items' to 'results'
                return { results: data.items };
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
            url: '{{URL::to('/admin/clients/get-recipients')}}',
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
@endsection


