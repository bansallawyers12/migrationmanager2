<!-- Upload Document Modal -->
<div class="modal fade custom_modal" id="openfileuploadmodal" tabindex="-1" role="dialog" aria-labelledby="paymentscheModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="paymentscheModalLabel">Upload Document</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
				<style>
                #ddArea {height: 200px;border: 2px dashed #ccc;line-height: 200px;font-size: 20px;background: #f9f9f9;margin-bottom: 15px;}
                .drag_over {color: #000;border-color: #000;}
                .thumbnail {width: 100px;height: 100px;padding: 2px;margin: 2px;border: 2px solid lightgray;border-radius: 3px;float: left;}
                .d-none {display: none;}
				</style>
					<div class="col-md-8">
					<input type="hidden" class="checklisttype" value="">
					<input type="hidden" class="checklisttypename" value="">
					<input type="hidden" class="checklistid" value="">
					<input type="hidden" class="application_id" value="">
						<div id="ddArea" style="text-align: center;">
							Click or drag to upload new file from your device

							<a style="display: none;" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent ">

							</a>
						</div>

						<input type="file" class="d-none" id="selectfile" multiple />
					</div>
					<div class="col-md-4">
						<div id="showThumb">
							<ul>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Add Personal Document Category Modal -->
<div class="modal fade addpersonaldoccatmodel custom_modal" id="addpersonaldoccatmodel" tabindex="-1" role="dialog" aria-labelledby="addPersDocCatModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addPersDocCatModalLabel">Add Personal Document Category</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/documents/add-personal-category')}}" name="add_pers_doc_cat_form" id="add_pers_doc_cat_form" autocomplete="off"  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="clientid" value="{{$fetchedData->id}}">

					<div class="row">
						<div class="col-6 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="personal_doc_category">Category<span class="span_req">*</span></label>
								<input type="text" class="form-control" name="personal_doc_category" id="personal_doc_category" data-valid="required">

								<span class="custom-error personal_doc_category_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('add_pers_doc_cat_form')" type="button" class="btn btn-primary" style="margin: 0px !important;">Create</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Add Visa Document Category Modal -->
<div class="modal fade addvisadoccatmodel custom_modal" id="addvisadoccatmodel" tabindex="-1" role="dialog" aria-labelledby="addVisaDocCatModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addVisaDocCatModalLabel">Add Visa Document Category</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/documents/add-visa-category')}}" name="add_visa_doc_cat_form" id="add_visa_doc_cat_form" autocomplete="off"  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="clientid" value="{{$fetchedData->id}}">
					<input type="hidden" name="clientmatterid" id="visaclientmatterid" value="">

					<div class="row">
						<div class="col-6 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="visa_doc_category">Category<span class="span_req">*</span></label>
								<input type="text" class="form-control" name="visa_doc_category" id="visa_doc_category" data-valid="required">

								<span class="custom-error visa_doc_category_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('add_visa_doc_cat_form')" type="button" class="btn btn-primary" style="margin: 0px !important;">Create</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

