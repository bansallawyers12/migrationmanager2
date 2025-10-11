<!-- Form 956 -->
<div class="modal fade custom_modal" id="form956CreateFormModel" tabindex="-1" role="dialog" aria-labelledby="form956ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="form956ModalLabel">Create Form 956</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('forms.store') }}" name="createForm956" id="createForm956" autocomplete="off">
                    @csrf
                    <!-- Hidden Fields for Client and Client Matter ID -->
                    <input type="hidden" name="client_id" id="form956_client_id">
                    <input type="hidden" name="client_matter_id" id="form956_client_matter_id">

                    <!-- Error Message Container -->
                    <div class="custom-error-msg"></div>

                    <!-- Agent Details (Read-only, assuming agent is pre-fetched) -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="font-medium text-gray-900">Agent Details</h6>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="text-sm font-medium text-gray-700">Agent Name - <span id="agent_name_label"></span></label>
                                        <input type="hidden" name="agent_id" id="agent_id">
                                        <input type="hidden" name="agent_name" id="agent_name">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="text-sm font-medium text-gray-700">Business Name - <span id="business_name_label"></span></label>
                                        <input type="hidden" name="business_name" id="business_name" class="form-control bg-gray-100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

					<!-- Application Details -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="font-medium text-gray-900">Application Details</h6>
                            <div class="row mt-2">
                                <!-- Application Type -->
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="text-sm font-medium text-gray-700">Type of Application</label>
                                        <br/><span id="application_type_label"></span>
                                        <input type="hidden" name="application_type" id="application_type">
                                    </div>
                                </div>
                                <!-- Date Lodged -->
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="text-sm font-medium text-gray-700">Date Lodged</label>
                                        <input type="date" name="date_lodged" id="date_lodged" class="form-control">
                                    </div>
                                </div>
                                <!-- Not Lodged Checkbox -->
                                <div class="col-12">
                                    <div class="form-group" style="margin-left: 20px;">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="not_lodged" value="1" class="form-check-input">
                                            <span class="ml-2 text-sm text-gray-700">Application not yet lodged</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Type (Hidden - Always Appointment) -->
                    <input type="hidden" name="form_type" value="appointment">

                    <!-- Part A: New Appointment -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="font-medium text-gray-900">Part A: New Appointment</h6>
                            <div class="row mt-2">
                                <!-- Agent Type -->
                                <div class="col-12">
                                    <label class="text-sm font-medium text-gray-700">Agent Type</label>
                                    <div class="mt-2">
                                        <div class="form-group" style="margin-left: 20px;">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="is_registered_migration_agent" value="1" checked class="form-check-input">
                                                <span class="ml-2 text-sm text-gray-700">Registered Migration Agent</span>
                                            </label>
                                        </div>
                                        <div class="form-group" style="margin-left: 20px;">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="is_legal_practitioner" value="1" class="form-check-input">
                                                <span class="ml-2 text-sm text-gray-700">Legal Practitioner</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Type of Assistance -->
                                <div class="col-12 mt-3">
                                    <label class="text-sm font-medium text-gray-700">Type of Assistance</label>
                                    <div class="mt-2">
                                        <div class="form-group" style="margin-left: 20px;">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="assistance_visa_application" value="1" checked class="form-check-input">
                                                <span class="ml-2 text-sm text-gray-700">Visa Application</span>
                                            </label>
                                        </div>
                                        <div class="form-group" style="margin-left: 20px;">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="assistance_sponsorship" value="1" class="form-check-input">
                                                <span class="ml-2 text-sm text-gray-700">Sponsorship</span>
                                            </label>
                                        </div>
                                        <div class="form-group" style="margin-left: 20px;">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="assistance_nomination" value="1" class="form-check-input">
                                                <span class="ml-2 text-sm text-gray-700">Nomination</span>
                                            </label>
                                        </div>
                                        <div class="form-group" style="margin-left: 20px;">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="assistance_cancellation" value="1" class="form-check-input">
                                                <span class="ml-2 text-sm text-gray-700">Cancellation</span>
                                            </label>
                                        </div>
                                        <div class="form-group" style="margin-left: 20px;">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="assistance_other" value="1" class="form-check-input">
                                                <span class="ml-2 text-sm text-gray-700">Other</span>
                                            </label>
                                            <input type="text" name="assistance_other_details" placeholder="Specify other assistance" class="form-control mt-1">
                                        </div>
                                    </div>
                                </div>
                                <!-- Question 5 - Business Address -->
                                <div class="col-12 mt-3">
                                    <div class="form-group" style="margin-left: 20px;">
                                        <label class="text-sm font-medium text-gray-700">Question 5 - Business Address</label>
                                        <input type="text" name="business_address" value="As Above" readonly class="form-control bg-gray-100">
                                    </div>
                                </div>
                                <!-- Question 7 -->
                                <div class="col-12">
                                    <div class="form-group" style="margin-left: 20px;">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="question_7" value="1" checked class="form-check-input">
                                            <span class="ml-2 text-sm text-gray-700">Question 7 - Registered Migration Agent</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Question 17 -->
                                <div class="col-12">
                                    <div class="form-group" style="margin-left: 20px;">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="is_authorized_recipient" value="1" checked class="form-check-input">
                                            <span class="ml-2 text-sm text-gray-700">Authorized Recipient (Question 17)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
							<div class="row mt-2">
                                <div class="col-12 col-md-6">
                                    <input type="date" name="agent_declaration_date" value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
                                <div class="col-12 col-md-6">
                                    <input type="date" name="client_declaration_date" value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
							</div>
							<!-- Submit Button -->
							<div class="row mt-4">
								<div class="col-12">
									<button type="submit" class="btn btn-primary">Create Form</button>
								</div>
							</div>
						</div>
					</div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Visa agreement Form -->
<div class="modal fade custom_modal" id="visaAgreementCreateFormModel" tabindex="-1" role="dialog" aria-labelledby="visaAgreementModalLabel11" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="visaAgreementModalLabel">Create Visa Agreement</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{route('clients.generateagreement')}}" name="visaagreementform11" id="visaagreementform11" autocomplete="off">
					@csrf
					<!-- Hidden Fields for Client and Client Matter ID -->
					<input type="hidden" name="client_id" id="visa_agreement_client_id">
					<input type="hidden" name="client_matter_id" id="visa_agreement_client_matter_id">

					<!-- Error Message Container -->
					<div class="custom-error-msg"></div>

					<!-- Agent Details (Read-only, assuming agent is pre-fetched) -->
					<div class="row">
						<div class="col-12">
							<h6 class="font-medium text-gray-900">Agent Details</h6>
							<div class="row mt-2">
								<div class="col-6">
									<div class="form-group">
										<label class="text-sm font-medium text-gray-700">Agent Name - <span id="visaagree_agent_name_label"></span></label>
										<input type="hidden" name="agent_id" id="visaagree_agent_id">
										<input type="hidden" name="agent_name" id="visaagree_agent_name">
									</div>
								</div>
								<div class="col-6">
									<div class="form-group">
										<label class="text-sm font-medium text-gray-700">Business Name - <span id="visaagree_business_name_label"></span></label>
										<input type="hidden" name="business_name" id="visaagree_business_name" class="form-control bg-gray-100">
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Submit Button -->
					<div class="row mt-4">
						<div class="col-12">
							<button type="submit" class="btn btn-primary">Generate Agreement</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Agreement Model Open -->
<div class="modal fade custom_modal" id="agreementModal" tabindex="-1" role="dialog" aria-labelledby="agreementModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form id="agreementUploadForm" enctype="multipart/form-data">
			<input type="hidden" name="clientmatterid" id="agreemnt_clientmatterid" value="">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="agreementModalLabel">Upload Agreement (PDF)</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<input type="file" name="agreement_doc" class="form-control" accept=".pdf" required>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Upload</button>
				</div>
			</div>
		</form>
	</div>
</div>
