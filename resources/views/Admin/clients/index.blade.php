@extends('layouts.admin_client_detail')
@section('title', 'Clients')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/listing-pagination.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-datepicker.css') }}">
<style>
    /* Page-specific styles for clients index page */
    /* Fix dropdown menu display for action buttons */
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
    
    .listing-container .table td .dropdown {
        position: relative;
        display: inline-block;
    }
    
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
    }
    
    /* Ensure dropdown container doesn't clip content */
    .listing-container .dropdown {
        overflow: visible !important;
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
                    <h4>All Clients</h4>

                    <div class="d-flex align-items-center">
                        <a href="javascript:;" class="btn btn-theme btn-theme-sm filter_btn mr-2"><i class="fas fa-filter"></i> Filter</a>
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
                            <a class="nav-link active" id="clients-tab"  href="{{URL::to('/admin/clients')}}" >Clients</a>
                        </li>
                        <li class="nav-item is_checked_clientn">
                            <a class="nav-link" id="archived-tab"  href="{{URL::to('/admin/archived')}}" >Archived</a>
                        </li>

                        <li class="nav-item is_checked_clientn">
                            <a class="nav-link" id="lead-tab"  href="{{URL::to('/admin/leads')}}" >Leads</a>
                        </li>
                    </ul>

                    <div class="filter_panel"><h4>Search By Details</h4>
                        <form action="{{URL::to('/admin/clients')}}" method="get">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="client_id" class="col-form-label" style="color:#4a5568 !important;">Client ID</label>
                                        <input type="text" name="client_id" value="{{ old('client_id', Request::get('client_id')) }}" class="form-control" data-valid="" autocomplete="off" placeholder="Client ID" id="client_id">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name" class="col-form-label" style="color:#4a5568 !important;">Name</label>
                                        <input type="text" name="name" value="{{ old('name', Request::get('name')) }}" class="form-control agent_company_name" data-valid="" autocomplete="off" placeholder="Name" id="name">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email" class="col-form-label" style="color:#4a5568 !important;">Email</label>
                                        <input type="text" name="email" value="{{ old('email', Request::get('email')) }}" class="form-control" data-valid="" autocomplete="off" placeholder="Email" id="email">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="phone" class="col-form-label" style="color:#000;">Phone</label>
                                        <input type="text" name="phone" value="{{ old('phone', Request::get('phone')) }}" class="form-control" data-valid="" autocomplete="off" placeholder="Phone" id="phone">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="type" class="col-form-label" style="color:#000;">Type</label>
                                        <select class="form-control" name="type">
                                            <option value="">Select</option>
                                            <option value="client" {{ request('type') == 'client' ? 'selected' : '' }}>Client</option>
                                            <option value="lead" {{ request('type') == 'lead' ? 'selected' : '' }}>Lead</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="filter-buttons-container">
                                        <button type="submit" class="btn btn-primary btn-theme-lg mr-3">Search</button>
                                        <a class="btn btn-info" href="{{URL::to('/admin/clients')}}">Reset</a>
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
                                    <th>Name</th>
                                    <th>Rating</th>
                                    <th>Client ID</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="tdata">
                                @if(@$totalData !== 0)
                                <?php $i=0; ?>
                                    @foreach (@$lists as $list)
                                    <tr id="id_{{@$list->id}}">
                                            <td style="white-space: initial;" class="text-center">
                                                <div class="custom-checkbox custom-control">
                                                    <input data-id="{{@$list->id}}" data-email="{{@$list->email}}" data-name="{{@$list->first_name}} {{@$list->last_name}}" data-clientid="{{@$list->client_id}}" type="checkbox" data-checkboxes="mygroup" class="cb-element custom-control-input  your-checkbox" id="checkbox-{{$i}}">
                                                    <label for="checkbox-{{$i}}" class="custom-control-label">&nbsp;</label>
                                                </div>
                                            </td>
                                            <?php
                                            // Check if active matter exists
                                            $latestMatter = \DB::table('client_matters')
                                                ->where('client_id', $list->id)
                                                ->where('matter_status', 1)
                                                ->orderByDesc('id') // or use created_at if preferred
                                                ->first();
                                            $encodedId = base64_encode(convert_uuencode(@$list->id));
                                            $clientDetailUrl = $latestMatter
                                                ? URL::to('/admin/clients/detail/'.$encodedId.'/'.$latestMatter->client_unique_matter_no )
                                                : URL::to('/admin/clients/detail/'.$encodedId);
                                            ?>
                                            <td style="white-space: initial;"><a href="{{ $clientDetailUrl }}">{{ @$list->first_name == "" ? config('constants.empty') : str_limit(@$list->first_name, '50', '...') }} {{ @$list->last_name == "" ? config('constants.empty') : str_limit(@$list->last_name, '50', '...') }} </a><br/></td>
                                            <td style="white-space: initial;"><?php echo @$list->rating; ?></td>
                                            <td style="white-space: initial;">{{ @$list->client_id == "" ? config('constants.empty') : str_limit(@$list->client_id, '50', '...') }}</td>
                                            <td><span class="ag-label--circular" style="color: #6777ef" >In Progress</span></td>
                                            <td style="white-space: initial;">{{date('d/m/Y', strtotime($list->created_at))}}</td>
                                            <td style="white-space: initial;">
                                                <div class="dropdown d-inline">
                                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Action
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item has-icon clientemail" data-id="{{@$list->id}}" data-email="{{@$list->email}}" data-name="{{@$list->first_name}} {{@$list->last_name}}" href="javascript:;">
                                                            <i class="far fa-envelope"></i> Email
                                                        </a>
                                                        <a class="dropdown-item has-icon" href="{{URL::to('/admin/clients/edit/'.base64_encode(convert_uuencode(@$list->id)))}}">
                                                            <i class="far fa-edit"></i> Edit
                                                        </a>
                                                        <a class="dropdown-item has-icon" href="javascript:;" onclick="deleteAction({{$list->id}}, 'admins')">
                                                            <i class="fas fa-trash"></i> Archived
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
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">From <span class="span_req">*</span></label>
								<select class="form-control" name="email_from">
									<?php
									$emails = \App\Models\Email::select('email')->where('status', 1)->get();
									foreach($emails as $email){
										?>
											<option value="<?php echo $email->email; ?>"><?php echo $email->email; ?></option>
										<?php
									}

									?>
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
								<select data-valid="" class="js-data-example-ajaxcc" name="email_cc[]"></select>

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
								<select data-valid="" class="form-control select2 selecttemplate" name="template">
									<option value="">Select</option>
									@foreach(\App\Models\CrmEmailTemplate::all() as $list)
										<option value="{{$list->id}}">{{$list->name}}</option>
									@endforeach
								</select>

							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="subject">Subject <span class="span_req">*</span></label>
								<input type="text" name="subject" value="{{ old('subject', '') }}" class="form-control selectedsubject" data-valid="required" autocomplete="off" placeholder="Enter Subject">
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
								<textarea class="summernote-simple selectedmessage" name="message"></textarea>
								@if ($errors->has('message'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('message') }}</strong>
									</span>
								@endif
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
@endsection
@push('scripts')
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
            //alert('total='+total);
            //alert('checked_length='+checked_length);
            //alert('role='+role);
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
            //alert(checked_length);
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
        var finalStr = nameStr+'('+clientidStr+')'; //console.log('finalStr='+finalStr);
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
                        //console.log(obj.message);
                        location.reload(true);
                    }
                });
                //return false;
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
@endpush

