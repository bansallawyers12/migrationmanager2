<!-- Appointment Modal -->
<div class="modal fade custom_modal" id="edit_appointment" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Edit Appointment</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button> 
			</div>
			<div class="modal-body showappointmentdetail">
				<h4>Please Wait ......</h4>
			</div>
			
		</div>
	</div>
</div>

<!-- Note & Terms Modal -->
<div class="modal fade custom_modal" id="edit_note" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Create Note</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/create-note')}}" name="editnotetermform" autocomplete="off" id="editnotetermform" enctype="multipart/form-data">
				@csrf 
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
				<input type="hidden" name="noteid" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								{{ Form::text('title', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Title' )) }}
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Description <span class="span_req">*</span></label>
								<textarea class="summernote-simple" name="description" data-valid="required"></textarea>
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<!--<div class="col-12 col-md-12 col-lg-12 is_not_note" style="display:none;">
							<div class="form-group"> 
								<label class="d-block" for="related_to">Related To</label> 
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="contact" value="Contact" name="related_to" checked>
									<label class="form-check-label" for="contact">Contact</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="partner" value="Partner" name="related_to">
									<label class="form-check-label" for="partner">Partner</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="application" value="Application" name="related_to">
									<label class="form-check-label" for="application">Application</label>
								</div>
							
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12 is_not_note" style="display:none;">
							<div class="form-group">
								<label for="contact_name">Contact Name <span class="span_req">*</span></label> 	
								<select data-valid="" class="form-control contact_name select2" name="contact_name">
									<option value="">Choose Contact</option>
									<option value="Amit">Amit</option>
								</select>
								<span class="custom-error contact_name_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>-->
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('editnotetermform')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form> 
			</div>
		</div> 
	</div>
</div>

<!-- English Test Modal -->
<div class="modal fade edit_english_test custom_modal" tabindex="-1" role="dialog" aria-labelledby="editenglishModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content"> 
			<div class="modal-header">
				<h5 class="modal-title" id="editenglishModalLabel">Edit English Test Scores</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			<?php
				$testscores = \App\Models\TestScore::where('client_id', $fetchedData->id)->first();
				?>
				<form method="post" action="{{URL::to('/admin/edit-test-scores')}}" name="testscoreform" autocomplete="off" id="testscoreform" enctype="multipart/form-data">
				@csrf 
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
					<div class="edu_test_score edu_english_score" style="margin-bottom:15px;">
						<div class="edu_test_row" style="text-align:center;">
							<div class="edu_test_col">&nbsp;</div>
							<div class="edu_test_col"><span>Listening</span></div>
							<div class="edu_test_col"><span>Reading</span></div>
							<div class="edu_test_col"><span>Writing</span></div>
							<div class="edu_test_col"><span>Speaking</span></div>
							<div class="edu_test_col"><span style="color:#71cc53;">Overall Scores</span></div>
							<div class="edu_test_col"><span>Date</span></div>
						</div> 
						<div class="edu_test_row flex_row">
							<div class="edu_test_col"><span>TOEFL</span></div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="number" class="form-control" name="band_score_1_1" value="<?php if(@$testscores->toefl_Listening != ''){ echo @$testscores->toefl_Listening; }else{ echo ''; } ?>" step="0.01"/>
								</div>
							</div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="number" class="form-control" name="band_score_2_1" value="<?php if(@$testscores->toefl_Reading != ''){ echo @$testscores->toefl_Reading; }else{ echo ''; } ?>" step="0.01"/>
								</div>
							</div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="number" class="form-control" name="band_score_3_1" value="<?php if(@$testscores->toefl_Writing != ''){ echo @$testscores->toefl_Writing; }else{ echo ''; } ?>" step="0.01"/>
								</div>
							</div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="number" class="form-control" name="band_score_4_1" value="<?php if(@$testscores->toefl_Speaking != ''){ echo @$testscores->toefl_Speaking; }else{ echo ''; } ?>" step="0.01"/>
								</div>
							</div>
							<div class="edu_test_col overal_block">
								<div class="edu_field">
									<input type="number" class="form-control" name="score_1" value="<?php if(@$testscores->score_1 != ''){ echo @$testscores->score_1; }else{ echo ''; } ?>" step="0.01"/>
								</div>
							</div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="text" class="form-control datepicker" name="band_score_5_1" value="<?php if(@$testscores->toefl_Date != ''){ echo @$testscores->toefl_Date; }else{ echo ''; } ?>"/>
								</div>
							</div>
						</div>
						<div class="edu_test_row flex_row">
							<div class="edu_test_col"><span>IELTS</span></div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="number" class="form-control" name="band_score_5_2"  value="<?php if(@$testscores->ilets_Listening != ''){ echo @$testscores->ilets_Listening; }else{ echo ''; } ?>" step="0.01"/>
								</div>
							</div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="number" class="form-control" name="band_score_6_2" value="<?php if(@$testscores->ilets_Reading != ''){ echo @$testscores->ilets_Reading; }else{ echo ''; } ?>"  step="0.01"/>
								</div>
							</div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="number" class="form-control" name="band_score_7_2" value="<?php if(@$testscores->ilets_Writing != ''){ echo $testscores->ilets_Writing; }else{ echo ''; } ?>" step="0.01"/>
								</div>
							</div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="number" class="form-control" name="band_score_8_2" value="<?php if(@$testscores->ilets_Speaking != ''){ echo @$testscores->ilets_Speaking; }else{ echo ''; } ?>" step="0.01"/>
								</div>
							</div>
							<div class="edu_test_col overal_block">
								<div class="edu_field">
									<input type="number" class="form-control" name="score_2"  value="<?php if(@$testscores->score_2 != ''){ echo @$testscores->score_2; }else{ echo ''; } ?>" step="0.01"/>
								</div>
							</div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="text" class="form-control datepicker" name="band_score_6_1" value="<?php if(@$testscores->ilets_Date != ''){ echo @$testscores->ilets_Date; }else{ echo ''; } ?>"/>
								</div>
							</div>
						</div>
						<div class="edu_test_row flex_row">
							<div class="edu_test_col"><span>PTE</span></div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="number" class="form-control" name="band_score_9_3" value="<?php if(@$testscores->pte_Listening != ''){ echo @$testscores->pte_Listening; }else{ echo ''; } ?>" step="0.01"/>
								</div>
							</div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="number" class="form-control" name="band_score_10_3" value="<?php if(@$testscores->pte_Reading != ''){ echo @$testscores->pte_Reading; }else{ echo ''; } ?>" step="0.01"/>
								</div>
							</div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="number" class="form-control" name="band_score_11_3" value="<?php if(@$testscores->pte_Writing != ''){ echo @$testscores->pte_Writing; }else{ echo ''; } ?>" step="0.01"/>
								</div>
							</div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="number" class="form-control" name="band_score_12_3" value="<?php if(@$testscores->pte_Speaking != ''){ echo @$testscores->pte_Speaking; }else{ echo ''; } ?>" step="0.01"/>
								</div>
							</div>
							<div class="edu_test_col overal_block">
								<div class="edu_field"> 
									<input type="number" class="form-control" name="score_3" value="<?php if(@$testscores->score_3 != ''){ echo @$testscores->score_3; }else{ echo ''; } ?>" step="0.01"/>
								</div>
							</div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="text" class="form-control datepicker" name="band_score_7_1" value="<?php if(@$testscores->pte_Date != ''){ echo @$testscores->pte_Date; }else{ echo ''; } ?>"/>
								</div>
							</div>
						</div> 
						<div class="clearfix"></div>
					</div>
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('testscoreform')" type="button" class="btn btn-primary">Update</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						</div>
					</div>
				</form> 
			</div>
		</div> 
	</div>
</div>

<!-- Other Test Modal -->
<div class="modal fade edit_other_test custom_modal" tabindex="-1" role="dialog" aria-labelledby="editotherModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"> 
			<div class="modal-header">
				<h5 class="modal-title" id="editotherModalLabel">Edit Other Test Scores</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/other-test-scores')}}" name="othertestform" autocomplete="off" id="othertestform" enctype="multipart/form-data">
				@csrf 
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="sat_i">SAT I</label>
							
								<input type="number" class="form-control" name="sat_i" value="<?php if(@$testscores->sat_i != ''){ echo @$testscores->sat_i; }else{ echo ''; } ?>" step="0.01"/>
								
								<span class="custom-error sat_i_error" role="alert">
									<strong></strong>
								</span> 
							</div>
							<div class="form-group"> 
								<label for="sat_ii">SAT II</label>
								<input type="number" class="form-control" name="sat_ii" value="<?php if(@$testscores->sat_ii != ''){ echo @$testscores->sat_ii; }else{ echo ''; } ?>" step="0.01"/>
							
								<span class="custom-error sat_ii_error" role="alert">
									<strong></strong>
								</span> 
							</div>
							<div class="form-group">
								<label for="gre">GRE</label>
								<input type="number" class="form-control" name="gre" value="<?php if(@$testscores->gre != ''){ echo $testscores->gre; }else{ echo ''; } ?>" step="0.01"/>
								
								<span class="custom-error gre_error" role="alert">
									<strong></strong>
								</span> 
							</div>
							<div class="form-group">
								<label for="gmat">GMAT</label>
								<input type="number" class="form-control" name="gmat" value="<?php if(@$testscores->gmat != ''){ echo @$testscores->gmat; }else{ echo ''; } ?>" step="0.01"/>
								
								<span class="custom-error gmat_error" role="alert">
									<strong></strong>
								</span> 
							</div> 
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('othertestform')" type="button" class="btn btn-primary">Update</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						</div>
					</div>
				</form> 
			</div>
		</div> 
	</div>
</div>


<!-- Education Modal -->
<div class="modal fade  custom_modal" id="edit_education" tabindex="-1" role="dialog" aria-labelledby="create_educationModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Edit Education</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showeducationdetail">
				<h4>Please wait ...</h4>
			</div>
		</div>
	</div>
</div> 

<!-- Interested Service Modal -->
<div class="modal fade  custom_modal" id="eidt_interested_service" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Edit Interested Services</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showinterestedserviceedit">
				 
			</div>
		</div>
	</div>
</div>


<!-- Interested Service Modal -->
<div class="modal fade  custom_modal" id="edit_promotion" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Edit Promotion</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showpromotionedit">
				 
			</div>
		</div>
	</div>
</div>


