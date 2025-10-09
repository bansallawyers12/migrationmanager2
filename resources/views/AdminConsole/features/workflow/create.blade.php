@extends('layouts.admin')
@section('title', 'Add Workflow Stage')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<form action="{{ url('admin/workflow/store') }}" name="add-workflow" autocomplete="off" enctype="multipart/form-data" method="POST">
				@csrf
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Add Workflow Stage</h4>
								<div class="card-header-action">
									<a href="{{route('admin.workflow.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-3 col-md-3 col-lg-3">
			        	@include('../Elements/Admin/setting')
    		        </div>
    				<div class="col-9 col-md-9 col-lg-9">
						<div class="card">
							<div class="card-body">
								<div id="accordion">
									<div class="accordion">
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#primary_info" aria-expanded="true">
											<h4>Add Workflow Stage</h4>
										</div>
										<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
											<div class="row">
												<!--<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group">
														<label for="name">Name <span class="span_req">*</span></label>
														{{--Form::text('name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' ))--}}
														{{--@if ($errors->has('name'))--}}
															<span class="custom-error" role="alert">
																<strong>{{--@$errors->first('name')--}}</strong>
															</span>
														{{--@endif--}}
													</div>
												</div>-->
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group">
														<!--<label for="stages">Workflow Stages <span class="span_req">*</span></label>-->
														<div class="workflow_stges">
															<table class="table">
																<tr>
																	<td>
																		<input data-valid="required" type="text" name="stage_name[]" placeholder="Stage Name" class="form-control">
																	</td>
																	<td></td>
																	<td></td>
																</tr>
																<!--<tr>
																	<td>
																		<input data-valid="required" type="text" name="stage_name[]" placeholder="Stage Name" class="form-control">
																	</td>
																	<td></td>
																	<td></td>
																</tr>-->
															</table>
														</div>
														<div class="">
															<a href="javascript:;" class="add_stage btn btn-info">Add Stage</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group float-right">
									<button type="submit" class="btn btn-primary">Save Workflow Stage</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</section>
</div>

@endsection
@section('scripts')
<script>
jQuery(document).ready(function($){
	$('.add_stage').on('click', function(){
		var html = '<tr>'+
            '<td><input type="text" data-valid="required" name="stage_name[]" placeholder="Stage Name" class="form-control"></td>'+
            '<td><a href="javascript:;" class="remove_stage"><i class="fa fa-trash"></i></a></td>'+
            '<td></td>'+
        '</tr>';
        $('.workflow_stges table').append(html);
	});

	$(document).delegate('.remove_stage', 'click', function(){
		$(this).parent().parent().remove();
	});
});
</script>
@endsection
