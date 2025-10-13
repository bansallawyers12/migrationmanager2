@extends('layouts.admin_client_detail')
@section('title', 'Edit Workflow Stage')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<form action="{{ url('admin/workflow/edit') }}" name="add-visatype" autocomplete="off" enctype="multipart/form-data" method="POST">
				@csrf
				<input type="hidden" name="id" value="{{ old('id', @$fetchedData->id) }}">
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Edit Workflow Stage</h4>
								<div class="card-header-action">
									<a href="{{route('adminconsole.features.workflow.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
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
											<h4>Edit Workflow Stage</h4>
										</div>
										<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
											<div class="row">
												<!--<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group">
														<label for="name">Name <span class="span_req">*</span></label>
														{{-- <input type="text" name="name" value="{{ @$fetchedData->name }}" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Name"> --}}
														{{--@if ($errors->has('name'))--}}
															<span class="custom-error" role="alert">
																<strong>{{--@$errors->first('name')--}}</strong>
															</span>
														{{--@endif--}}
													</div>
												</div>-->
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group">
														<label for="stages">Workflow Stages <span class="span_req">*</span></label>
														<div class="workflow_stges">
															<table class="table">
															<?php
															$stagesquery = \App\Models\WorkflowStage::where('id', @$fetchedData->id);
															$stagescount = $stagesquery->count();
															$stages = $stagesquery->get();
															$i = 0;
															if($stagescount !== 0){
																foreach($stages as $stage){
															    ?>
																<tr>
																	<td>
																		<input data-valid="required" type="text" name="stage_name[]" placeholder="Stage Name" class="form-control" value="{{@$stage->name}}" >
																	</td>
																	<td>
                                                                        <?php
                                                                        if($i == 0 || $i == 1){
                                                                        } else { ?>
                                                                            <a href="javascript:;" class="remove_stage"><i class="fa fa-trash"></i></a>
                                                                        <?php } ?>
																	</td>
																	<td></td>
																</tr>
																<?php $i++;
                                                                }
															} else {
                                                                ?>
                                                                <tr>
                                                                    <td>
                                                                        <input data-valid="required" type="text" name="stage_name[]" placeholder="Stage Name" class="form-control">
                                                                    </td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <input data-valid="required" type="text" name="stage_name[]" placeholder="Stage Name" class="form-control">
                                                                    </td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>
                                                                <?php
                                                            } ?>
															</table>
														</div>
														<!--<div class="">
															<a href="javascript:;" class="add_stage btn btn-info">Add Stage</a>
														</div>-->
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
@push('scripts')
<script>
jQuery(document).ready(function($){
	$('.add_stage').on('click', function(){
		var html = '<tr>'+
				'<td><input type="text" name="stage_name[]" placeholder="Stage Name" class="form-control"></td>'+
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
@endpush
