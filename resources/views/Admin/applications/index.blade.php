@extends('layouts.admin')
@section('title', 'Applications')

@section('content')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
    .filter_panel {background: #f7f7f7;margin-bottom: 10px;border: 1pxsolid #eee;display: none;}
.card .card-body .filter_panel { padding: 20px;}
</style>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="custom-error-msg">
			</div>
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>All Applications</h4>
							<div class="card-header-action is_checked_clientn">
								<a href="#" class="btn btn-primary importmodal"> Import csv</a>
							</div>
							<div class="card-header-action">
							        <a href="javascript:;" class="btn btn-theme btn-theme-sm filter_btn"><i class="fas fa-filter"></i> Filter</a>
							 </div>
						</div>
						<div class="card-body">
						    <div class="filter_panel">
								<h4>Search By Details</h4>
								<form action="{{URL::to('/admin/applications')}}" method="get">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label for="ass_id" class="col-form-label">Assignee</label>
												{{ Form::text('ass_id', Request::get('ass_id'), array('class' => 'form-control assignee', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Assignee', 'id' => 'ass_id', 'onkeyup' => "suggestassignee(this.value)" )) }}
											</div>
											<input type="hidden" value="{{Request::get('assignee')}}" id="assigneeid" name="assignee">
										</div>
										<div class="col-md-4">
											<div class="form-group">
											    <?php
											    // Partner functionality removed
											    ?>
												<label for="partner" class="col-form-label">Partner</label>
												{{ Form::text('partner', @$par->partner_name, array('class' => 'form-control agent_company_name', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Partner', 'id' => 'partner', 'onkeyup' => "suggest(this.value)" )) }}

													<input type="hidden" value="{{Request::get('partner')}}" id="partnerid" name="partner">
											</div>
										</div>

										<div class="col-md-4">
											<div class="form-group">
												<label for="" class="col-form-label">Stage</label>
												<select class="form-control" name="stage">
												    <option value="">Select Stage</option>
												    @foreach($allstages as $allstage)
											<option @if(Request::get('stage') == $allstage->stage) selected @endif value="{{$allstage->stage}}">{{$allstage->stage}}</option>
											@endforeach
												</select>
											</div>
										</div>

									</div>
									<div class="row">
										<div class="col-md-12 text-center">

											{{ Form::submit('Search', ['class'=>'btn btn-primary btn-theme-lg' ]) }}
											<a class="btn btn-info" href="{{URL::to('/admin/applications')}}">Reset</a>
										</div>
									</div>
								</form>
							</div>
							<div class="table-responsive">
								<table class="table text_wrap">
									<thead>
										<tr>
											<th>Application ID</th>
											<th>Client Name</th>

											<!--<th>Client Phone</th>-->
											<th>Client Assignee</th>

											<th>Product</th>
											<th>Partner</th>
											<th>Partner Branch</th>

											<th>Workflow</th>
											<th>Stage</th>

											<th>Branch</th>
											<th>Status</th>

											<th>Created At</th>
											<th></th>
										</tr>
									</thead>
									@if(@$totalData !== 0)
									@foreach (@$lists as $list)
									<?php

									$clientdetail = \App\Models\Admin::where('id', $list->client_id)->first();
									
									$workflow = \App\Models\Workflow::where('id', $list->workflow)->first();
									?>
									<tbody class="tdata">
										<tr id="id_{{@$list->id}}">
											<td style="white-space: initial;"><a href="{{URL::to('admin/clients/detail/')}}/{{base64_encode(convert_uuencode(@$clientdetail->id))}}?tab=application&appid={{@$list->id}}">{{ @$list->id == "" ? config('constants.empty') : str_limit(@$list->id, '50', '...') }}</a></td>
											<td style="white-space: initial;"><a href="{{URL::to('admin/clients/detail/')}}/{{base64_encode(convert_uuencode(@$clientdetail->id))}}?tab=application">{{@$clientdetail->first_name}} {{@$clientdetail->last_name}}</a><!--<br/>-->{{--@$clientdetail->email--}}</td>

											{{--<td>@$clientdetail->phone }}</td> --}}
											<td style="white-space: initial;">{{@$list->application_assignee->first_name}}</td>

											<td style="white-space: initial;">Application #{{$aplist->id}}</td>
											<td style="white-space: initial;">Partner #{{$aplist->partner_id}}</td>
											<td style="white-space: initial;">{{$PartnerBranch->name ?? 'N/P'}}</td>

											<td style="white-space: initial;"><?php echo @$workflow->name; ?></td>
											<td style="white-space: initial;">{{ @$list->stage == "" ? config('constants.empty') : str_limit(@$list->stage, '50', '...') }}</td>

											<td style="white-space: initial;">{{$PartnerBranch->name ?? 'N/P'}}</td>
											<td><?php if($list->status == 0){ ?>
                                            <span class="ag-label--circular" style="color: #6777ef" >In Progress</span>
                                            <?php }else if($list->status == 1){ ?>
                                                <span class="ag-label--circular" style="color: #6777ef" >Completed</span>
                                            <?php } else if($list->status == 2){
                                            ?>
                                            <span class="ag-label--circular" style="color: red;" >Discontinued</span>
                                            <?php
                                            } ?></td>

											<!--<td>{{ @$list->created_at == "" ? config('constants.empty') : str_limit(@$list->created_at , '50', '...') }}</td>-->
											<td style="white-space: initial;">{{ @$list->created_at == "" ? config('constants.empty') : date('d/m/Y', strtotime($list->created_at))  }}</td>

                                            <td>
												<div class="dropdown d-inline">
													<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
													<div class="dropdown-menu">

													</div>
												</div>
											</td>
										</tr>
									@endforeach
									</tbody>
									@else
									<tbody>
										<tr>
											<td style="text-align:center;" colspan="12">
												No Record found
											</td>
										</tr>
									</tbody>
									@endif
								</table>
							</div>
						</div>
						<div class="card-footer">
						    {!! $lists->appends(\Request::except('page'))->render() !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<div id="importmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="importmodalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="importmodalLabel">Import CSV</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{URL::to('/admin/applications-import')}}"  enctype="multipart/form-data">
				@csrf
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="import">Select Import File<span class="span_req">*</span></label>
								<input type="file" required class="form-control" name="uploaded_file" id="uploaded_file">
								<small class="warning text-muted">Please upload only CSV file</small>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button  type="submit" class="btn btn-primary">Import</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script>
 function suggest(inputString) {
$( ".agent_company_name" ).autocomplete({
	autoFocus: true,
	minLength : 2,
	source : function(request, response) {
    $.ajax({
        type: "GET",
        url: "{{URL::to('/')}}/admin/getpartnerajax",
        dataType : "json",
        cache : false,
        data: {likewith : 'agent_company_name', likevalue: inputString},
        success:
            function(data){
                var all_l=[];
                for(var i=0;i<data.length;i++)
                {
                    var city_name=data[i].agent_company_name;
                     var y=data[i].id;
                    all_l.push({ "label": city_name, "value": city_name , "s": y } );
                }
                response(all_l);
            }
        });
    },
});

}

$('.agent_company_name').on('autocompleteselect', function (e, ui) {
      $('#partnerid').val(ui.item.s);
    });

 function suggestassignee(inputString) {
$( ".assignee" ).autocomplete({
	autoFocus: true,
	minLength : 2,
	source : function(request, response) {
    $.ajax({
        type: "GET",
        url: "{{URL::to('/')}}/admin/getassigneeajax",
        dataType : "json",
        cache : false,
        data: {likewith : 'assignee', likevalue: inputString},
        success:
            function(data){
                var all_l=[];
                for(var i=0;i<data.length;i++)
                {
                    var city_name=data[i].assignee;
                     var y=data[i].id;
                    all_l.push({ "label": city_name, "value": city_name , "s": y } );
                }
                response(all_l);
            }
        });
    },
});

}
$('.assignee').on('autocompleteselect', function (e, ui) {
      $('#assigneeid').val(ui.item.s);
    });
jQuery(document).ready(function($){

$('.filter_btn').on('click', function(){
		$('.filter_panel').slideToggle();
	});

});
$(document).delegate('.importmodal', 'click', function(){

$('#importmodal').modal('show');
});
</script>
@endsection