<!-- Legacy create_checklist modal removed - functionality moved to adminconsole DocumentChecklist -->

<!-- Add Personal Checklist Modal -->
<div class="modal fade create_education_docs custom_modal" id="openeducationdocsmodal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="taskModalLabel">Add Personal Checklist</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/documents/add-edu-checklist')}}" name="edu_upload_form" id="edu_upload_form" autocomplete="off"  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="clientid" value="{{$fetchedData->id}}">
                    <input type="hidden" name="type" value="client">
                    <input type="hidden" name="doctype" value="personal">
                    <input type="hidden" name="doccategory" id="doccategory" value="">
                    <input type="hidden" name="folder_name" id="folder_name" value="">

                    <div class="row">
                        <div class="col-6 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="checklist">Select Checklist<span class="span_req">*</span></label>
								<select data-valid="required" class="form-control select2" name="checklist[]" id="checklist" multiple>
									<option value="">Select</option>
									<?php
									$eduChkList = \App\Models\DocumentChecklist::where('status',1)->where('doc_type',1)->get();
									foreach($eduChkList as $edulist){
									?>
										<option value="{{$edulist->name}}">{{$edulist->name}}</option>
									<?php
									}
									?>
								</select>
								<span class="custom-error checklist_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
                    </div>
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('edu_upload_form')" type="button" class="btn btn-primary" style="margin: 0px !important;">Create</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Add Visa Checklist Modal -->
<div class="modal fade create_migration_docs custom_modal" id="openmigrationdocsmodal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="taskModalLabel">Add Visa Checklist</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/documents/add-visa-checklist')}}" name="mig_upload_form" id="mig_upload_form" autocomplete="off"  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="clientid" value="{{$fetchedData->id}}">
                    <input type="hidden" name="type" value="client">
                    <input type="hidden" name="doctype" value="visa">
                    <input type="hidden" name="client_matter_id" id="hidden_client_matter_id" value="">
                    <input type="hidden" name="folder_name" id="visa_folder_name" value="">

					<div class="row">
                        <div class="col-6 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="visa_checklist">Select Checklist<span class="span_req">*</span></label>
								<select data-valid="required" class="form-control select2" name="visa_checklist[]" id="visa_checklist" multiple>
									<option value="">Select</option>
									<?php
									$visaChkList = \App\Models\DocumentChecklist::where('status',1)->where('doc_type',2)->get();
									foreach($visaChkList as $visalist){
									?>
										<option value="{{$visalist->name}}">{{$visalist->name}}</option>
									<?php
									}
									?>
								</select>
								<span class="custom-error visa_checklist_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
                    </div>

                    <div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('mig_upload_form')" type="button" class="btn btn-primary" style="margin: 0px !important;">Create</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

