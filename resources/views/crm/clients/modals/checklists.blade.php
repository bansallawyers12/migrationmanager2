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
				<form method="post" action="{{URL::to('/documents/add-edu-checklist')}}" name="edu_upload_form" id="edu_upload_form" autocomplete="off"  enctype="multipart/form-data">
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
				<form method="post" action="{{URL::to('/documents/add-visa-checklist')}}" name="mig_upload_form" id="mig_upload_form" autocomplete="off"  enctype="multipart/form-data">
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

<!-- Add New Checklist Modal (Client Portal → Documents tab) - same DOM level as Add Personal/Visa for Select2 -->
<div class="modal fade custom_modal" id="create_checklist" tabindex="-1" role="dialog" aria-labelledby="createChecklistModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="createChecklistModalLabel">Add New Checklist</h5>
				<button type="button" class="close" id="create_checklist_close_btn" data-dismiss="modal" aria-label="Close" onclick="closeCreateChecklistModal(); return false;">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/add-checklists')}}" name="create_checklist_form" id="create_checklist_form" autocomplete="off" enctype="multipart/form-data">
					@csrf
					<input type="hidden" name="app_id" id="checklistapp_id" value="">
					<input type="hidden" name="client_id" value="{{ $fetchedData->id }}">
					<input type="hidden" name="type" id="checklist_type" value="">
					<input type="hidden" name="typename" id="checklist_typename" value="">

					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="checklist_name_input">Checklist Name <span class="span_req">*</span></label>
							<select data-valid="required" id="checklist_name_input" name="cp_checklist_name[]" class="form-control select2" multiple style="width: 100%;">
								<option value="">Select</option>
								@php
								$portalChecklists = \App\Models\DocumentChecklist::where('status', 1)->whereIn('doc_type', [1, 2])->orderBy('doc_type')->orderBy('name')->get();
								@endphp
								@foreach($portalChecklists as $chk)
									<option value="{{ $chk->name }}">{{ $chk->name }}</option>
								@endforeach
							</select>
							<small class="form-text text-muted">Select from the list or type a custom checklist name and press Enter to add. Multiple selections allowed.</small>
							<span class="custom-error cp_checklist_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Description</label>
								<textarea name="description" id="description" class="form-control" rows="3" placeholder="Enter description (optional)"></textarea>
							</div>
						</div>

						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label>
									<input type="checkbox" name="allow_upload_docu" value="1" checked> Allow clients to upload documents from client portal
								</label>
							</div>
						</div>

					<div class="col-12 col-md-12 col-lg-12">
							<button type="button" id="create_checklist_submit_btn" class="btn btn-primary">Add Checklist</button>
							<button type="button" class="btn btn-secondary" id="create_checklist_close_btn_footer" data-dismiss="modal" onclick="closeCreateChecklistModal(); return false;">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Inline Signature Placement Modal (Checklist Agreements) -->
<div class="modal fade" id="signaturePlacementModal" tabindex="-1" role="dialog" aria-labelledby="signaturePlacementModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-xl modal-dialog-scrollable" role="document" style="max-width: 95%; max-height: 90vh;">
		<div class="modal-content" style="max-height: 90vh;">
			<div class="modal-header bg-warning text-dark">
				<h5 class="modal-title" id="signaturePlacementModalLabel">
					<i class="fas fa-pen-nib mr-2"></i>Place Signature Fields
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body p-4" style="max-height: calc(90vh - 150px); overflow-y: auto;">
				<div id="signature-placement-loading" class="text-center py-5">
					<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>
					<p class="mt-2" style="color: #374151;">Loading document...</p>
				</div>
				<div id="signature-placement-content" style="display: none;">
					<div class="row">
						<div class="col-lg-8">
							<div class="mb-2">
								<small style="color: #4b5563;">Document Preview — Click on the document to add signature fields, then drag to position them.</small>
							</div>
							<div id="signature-page-nav" class="mb-3" style="display: none;">
								<div class="btn-group btn-group-sm">
									<button type="button" class="btn btn-outline-secondary" id="sig-prev-page">&larr; Prev</button>
									<span class="px-3 align-self-center" id="sig-page-info">Page 1</span>
									<button type="button" class="btn btn-outline-secondary" id="sig-next-page">Next &rarr;</button>
								</div>
							</div>
							<div class="position-relative d-inline-block border rounded overflow-hidden" id="sig-preview-container" style="min-height: 400px;">
								<img id="sig-preview-image" src="" alt="PDF Preview" style="max-width: 100%; height: auto; display: block;">
								<div id="sig-fields-preview"></div>
							</div>
						</div>
						<div class="col-lg-4">
							<h6 class="font-weight-bold mb-2">Signature Fields</h6>
							<div id="sig-fields-container" class="mb-3" style="max-height: 280px; overflow-y: auto;"></div>
							<button type="button" class="btn btn-outline-primary btn-sm btn-block mb-3" id="sig-add-field">
								<i class="fas fa-plus mr-1"></i>Add Signature Field
							</button>
						</div>
					</div>
				</div>
				<div id="signature-placement-error" class="alert alert-danger" style="display: none;"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-success" id="sig-save-btn">
					<i class="fas fa-save mr-1"></i>Save Signature Locations
				</button>
			</div>
		</div>
	</div>
</div>

