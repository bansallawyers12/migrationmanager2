@extends('layouts.admin_client_detail')
@section('title', 'Leads')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/listing-pagination.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-datepicker.css') }}">
<style>
    /* Page-specific styles for leads index page */
    /* Professional Action Button Design */
    .listing-container .action_toggle .dropdown-toggle {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border: 1px solid #667eea !important;
        min-width: 40px;
        max-width: 45px;
        height: 35px;
        padding: 6px 8px;
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
        text-decoration: none;
    }
    
    .listing-container .action_toggle .dropdown-toggle:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%) !important;
        border-color: #5a6fd8 !important;
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
        transform: translateY(-1px);
        color: white !important;
        text-decoration: none;
    }
    
    .listing-container .action_toggle .dropdown-toggle:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.25);
        color: white !important;
        text-decoration: none;
    }
    
    .listing-container .action_toggle .dropdown-toggle::after {
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
    
    .listing-container .action_toggle .dropdown-toggle.show::after {
        transform: rotate(180deg);
    }
    
    /* Enhanced Dropdown Menu */
    .listing-container .action_toggle .dropdown-menu {
        position: absolute !important;
        top: 100% !important;
        right: 0 !important;
        left: auto !important;
        float: none !important;
        min-width: 160px;
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
    
    .listing-container .action_toggle .dropdown-menu.show {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
    
    /* Dropdown Items Styling */
    .listing-container .action_toggle .dropdown-item {
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
    
    .listing-container .action_toggle .dropdown-item:hover {
        color: #667eea;
        text-decoration: none;
        background: linear-gradient(135deg, #f8f9ff 0%, #e8ecff 100%);
        transform: translateX(2px);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
    }
    
    .listing-container .action_toggle .dropdown-item:active {
        background: linear-gradient(135deg, #e8ecff 0%, #d8e0ff 100%);
        transform: translateX(1px);
    }
    
    .listing-container .action_toggle .dropdown-item.has-icon {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .listing-container .action_toggle .dropdown-item.has-icon i {
        width: 16px;
        text-align: center;
    }
    
    /* Ensure all dropdown items are visible */
    .listing-container .action_toggle .dropdown-menu .dropdown-item {
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
    
    .listing-container .action_toggle {
        overflow: visible !important;
        position: relative;
        display: inline-block;
    }
    
    /* Ensure dropdown container doesn't clip content */
    .listing-container .dropdown {
        overflow: visible !important;
    }
    
    /* Icon styling for the action button */
    .listing-container .action_toggle .dropdown-toggle i {
        font-size: 14px;
        color: white;
    }
    
    .listing-container .action_toggle .dropdown-toggle:hover i {
        color: white;
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
                    <h4>All Leads</h4>

                    <div class="d-flex align-items-center">
                        <a href="{{route('admin.leads.create')}}" class="btn btn-primary">Create Lead</a>
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
                            <a class="nav-link " id="clients-tab"  href="{{URL::to('/admin/clients')}}" >Clients</a>
                        </li>
                        <li class="nav-item is_checked_clientn">
                            <a class="nav-link" id="archived-tab"  href="{{URL::to('/admin/archived')}}" >Archived</a>
                        </li>
                        <li class="nav-item is_checked_clientn">
                            <a class="nav-link active" id="lead-tab"  href="{{URL::to('/admin/leads')}}" >Leads</a>
                        </li>
                    </ul>

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
                                    <th>Name</th>
                                    <th>Info</th>
                                    <th>Contact Date</th>
                                    <th>Level & Status</th>
                                    <th>Followup</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="tdata">
                                @if(@$totalData !== 0)
                                <?php $i = 0; ?>
                                @foreach (@$lists as $list)
                                    <?php
                                    $followpe = \App\Models\Followup::where('lead_id', '=', $list->id)
                                    ->where('followup_type', '!=', 'assigned_to')
                                    ->orderby('id', 'DESC')
                                    ->with(['followutype'])
                                    ->first();
                                    $followp = \App\Models\Followup::where('lead_id', '=', $list->id)
                                    ->where('followup_type', '=', 'follow_up')
                                    ->orderby('id', 'DESC')
                                    ->with(['followutype'])
                                    ->first();
                                    ?>
                                    <tr id="id_{{@$list->id}}">
                                        <td style="white-space: initial;" class="text-center">
                                            <div class="custom-checkbox custom-control">
                                                <input data-id="{{@$list->id}}" data-email="{{@$list->email}}" data-name="{{@$list->first_name}} {{@$list->last_name}}" data-clientid="{{@$list->client_id}}" type="checkbox" data-checkboxes="mygroup" class="cb-element custom-control-input  your-checkbox" id="checkbox-{{$i}}">
                                                <label for="checkbox-{{$i}}" class="custom-control-label">&nbsp;</label>
                                            </div>
                                        </td>
                                        <td style="white-space: initial;">
                                            <a href="{{ URL::to('/admin/clients/detail/' . base64_encode(convert_uuencode(@$list->id))) }}">
                                                {{ @$list->first_name == "" ? config('constants.empty') : str_limit(@$list->first_name, '50', '...') }}
                                                {{ @$list->last_name == "" ? config('constants.empty') : str_limit(@$list->last_name, '50', '...') }}
                                            </a>

                                        </td>
                                        <td><i class="fa fa-mobile"></i> {{@$list->phone}} <br/> <i class="fa fa-envelope"></i> {{@$list->email}}</td>
                                        <td>{{@$list->service}} <br/> {{date('d/m/Y h:i:s a', strtotime($list->created_at))}}</td>
                                        <td><div class="lead_stars"><i class="fa fa-star"></i><span>{{@$list->lead_quality}}</span> {{@$followpe->followutype->name}}</div></td>
                                        @if($followp)
                                            @if(@$followp->followutype->type == 'follow_up')
                                                <td>{{$followp->followutype->name}}<br> {{date('d/m/Y h:i:s a', strtotime($followp->followup_date))}}</td>
                                            @else
                                                <td>{{@$followp->followutype->name}}</td>
                                            @endif
                                        @else
                                            <td>Not Contacted</td>
                                        @endif
                                        <td>
                                            <div class="dropdown action_toggle">
                                                <a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="more-vertical"></i></a>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item has-icon" href="{{URL::to('/admin/clients/edit/'.base64_encode(convert_uuencode(@$list->id)))}}">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </a>
                                                </div>
                                            </div>
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
            <form action="{{ url('admin/leads/assign') }}" method="POST" name="add-assign" autocomplete="off" enctype="multipart/form-data" id="addnoteform">
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
@section('scripts')
<script>
    jQuery(document).ready(function($){
        $('.listing-container .filter_btn').on('click', function(){
            $('.listing-container .filter_panel').slideToggle();
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
                        url:"{{URL::to('/')}}/admin/merge_records",
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

