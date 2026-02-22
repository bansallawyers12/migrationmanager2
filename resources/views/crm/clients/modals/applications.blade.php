{{-- ========================================
    ALL APPLICATION-RELATED MODALS
    This file contains all application modals for the client detail page
    ======================================== --}}

{{-- 1. Add Application Modal --}}
<!-- Application Modal -->
<div class="modal fade add_appliation custom_modal"  tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Add Application</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/saveapplication')}}" name="applicationform" id="addapplicationformform" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
                <input type="hidden" name="client_matter_id" id="hidden_client_matter_id_latest" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="workflow">Select Workflow <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control workflow applicationselect2" id="workflow" name="workflow">
									<option value="">Please Select a Workflow</option>
									@foreach(\App\Models\Workflow::all() as $wlist)
										<option value="{{$wlist->id}}">{{$wlist->name}}</option>
									@endforeach
								</select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="branch">Select Branch <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control" id="branch" name="branch">
									<option value="">Please Select a Branch</option>
									@foreach(\App\Models\Branch::orderBy('office_name')->get() as $office)
										<option value="{{$office->id}}" {{ Auth::user()->office_id == $office->id ? 'selected' : '' }}>{{$office->office_name}}</option>
									@endforeach
								</select>
								<span class="custom-error branch_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="office_id">Handling Office <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control" id="office_id" name="office_id">
									<option value="">Select Office</option>
									@foreach(\App\Models\Branch::orderBy('office_name')->get() as $office)
										<option value="{{$office->id}}" 
											{{ Auth::user()->office_id == $office->id ? 'selected' : '' }}>
											{{$office->office_name}}
										</option>
									@endforeach
								</select>
								<span class="custom-error office_id_error" role="alert">
									<strong></strong>
								</span>
								<small class="form-text text-muted">
									<i class="fas fa-building"></i> This matter will be handled by the selected office
								</small>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="product">Select Product</label>
								<select data-valid="required" class="form-control product approductselect2" id="product" name="product">
									<option value="">Please Select a Product</option>

								</select>
								<span class="custom-error product_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('applicationform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- 2. Discontinue Application Modal --}}
<!-- Discontinue Application Modal -->
<div class="modal fade custom_modal" id="discon_application" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Discontinue Application</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/discontinue_application')}}" name="discontinue_application" id="discontinue_application" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="diapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="workflow">Discontinue Reason <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control workflow" id="workflow" name="workflow">
									<option value="">Please Select</option>
									<option value="Change of Application">Change of Application</option>
									<option value="Error by Team Member">Error by Team Member</option>
									<option value="Financial Difficulties">Financial Difficulties</option>
									<option value="Loss of competitor">Loss of competitor</option>
									<option value="Other Reasons">Other Reasons</option>

								</select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label>Notes <span class="span_req">*</span></label>
								<textarea data-valid="required"  class="form-control" name="note"></textarea>

							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('discontinue_application')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- 2a-1. Verification: Payment, Service Agreement, Forms - Migration Agent must tick before proceeding --}}
<div class="modal fade custom_modal" id="verification-payment-forms-modal" tabindex="-1" role="dialog" aria-labelledby="verificationPaymentFormsModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="verificationPaymentFormsModalLabel">Verification: Payment, Service Agreement, Forms</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p class="mb-3" style="color: #374151;">As a Migration Agent, please confirm that you have verified the Payment, Service Agreement, and Forms before proceeding.</p>
				<form id="verification-payment-forms-form" name="verification-payment-forms-form" autocomplete="off">
					@csrf
					<input type="hidden" name="matter_id" id="verification-payment-forms-matter-id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="verification-confirm-checkbox" name="verification_confirm" required>
									<label class="custom-control-label" for="verification-confirm-checkbox">I have verified Payment, Service Agreement, and Forms for this matter <span class="span_req">*</span></label>
								</div>
								<span class="custom-error verification-confirm-error" role="alert"><strong></strong></span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="verification-note">Optional note</label>
								<textarea class="form-control" id="verification-note" name="verification_note" rows="2" placeholder="Add any optional notes..."></textarea>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button type="button" class="btn btn-primary" id="verification-payment-forms-submit">
								<i class="fas fa-check"></i> Verify and Proceed to Next Stage
							</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- 2a. Decision Received Modal (Granted/Refused/Withdrawn + note) --}}
<div class="modal fade custom_modal" id="decision-received-modal" tabindex="-1" role="dialog" aria-labelledby="decisionReceivedModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="decisionReceivedModalLabel">Decision Received</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p class="mb-3" style="color: #374151;">Please select the outcome and add a note.</p>
				<form id="decision-received-form" name="decision-received-form" autocomplete="off">
					@csrf
					<input type="hidden" name="matter_id" id="decision-received-matter-id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="decision-outcome">Outcome <span class="span_req">*</span></label>
								<select class="form-control" id="decision-outcome" name="decision_outcome" data-valid="required" required>
									<option value="">Please Select</option>
									<option value="Granted">Granted</option>
									<option value="Refused">Refused</option>
									<option value="Withdrawn">Withdrawn</option>
								</select>
								<span class="custom-error decision-outcome-error" role="alert"><strong></strong></span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="decision-note">Note <span class="span_req">*</span></label>
								<textarea class="form-control" id="decision-note" name="decision_note" rows="3" placeholder="Enter note..." required></textarea>
								<span class="custom-error decision-note-error" role="alert"><strong></strong></span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button type="button" class="btn btn-primary" id="decision-received-submit">
								<i class="fas fa-check"></i> Proceed to Decision Received
							</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- 2b. Discontinue Matter Modal (for Workflow tab - client_matters) --}}
<div class="modal fade custom_modal" id="discontinue-matter-modal" tabindex="-1" role="dialog" aria-labelledby="discontinueMatterModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="discontinueMatterModalLabel">Discontinue Matter</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="discontinue-matter-form" name="discontinue-matter-form" autocomplete="off">
					@csrf
					<input type="hidden" name="matter_id" id="discontinue-matter-id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="discontinue-reason">Reason for Discontinue <span class="span_req">*</span></label>
								<select class="form-control" id="discontinue-reason" name="discontinue_reason" data-valid="required" required>
									<option value="">Please Select</option>
									<option value="Change of Application">Change of Application</option>
									<option value="Error by Team Member">Error by Team Member</option>
									<option value="Financial Difficulties">Financial Difficulties</option>
									<option value="Grant of Another visa">Grant of Another visa</option>
									<option value="Loss of Competitor">Loss of Competitor</option>
									<option value="Client Withdrew">Client Withdrew</option>
									<option value="Other Reasons">Other Reasons</option>
								</select>
								<span class="custom-error discontinue-reason-error" role="alert"><strong></strong></span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="discontinue-notes">Notes</label>
								<textarea class="form-control" id="discontinue-notes" name="discontinue_notes" rows="3" placeholder="Optional additional notes"></textarea>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button type="button" class="btn btn-danger" id="discontinue-matter-submit">
								<i class="fas fa-ban"></i> Discontinue
							</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- 2c. Change Workflow Modal (for existing matters) --}}
<div class="modal fade custom_modal" id="change-workflow-modal" tabindex="-1" role="dialog" aria-labelledby="changeWorkflowModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="changeWorkflowModalLabel">Change Workflow</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<input type="hidden" id="change-workflow-matter-id" value="">
				<div class="form-group">
					<label for="change-workflow-select">Select Workflow</label>
					<select class="form-control" id="change-workflow-select">
						@foreach(\App\Models\Workflow::orderBy('name')->get() as $wf)
						<option value="{{ $wf->id }}">{{ $wf->name }}{{ $wf->matter ? ' (' . $wf->matter->title . ')' : '' }}</option>
						@endforeach
					</select>
					<small class="form-text text-muted">Stage will be mapped by name; if no match, first stage is used.</small>
				</div>
				<button type="button" class="btn btn-primary" id="change-workflow-submit">
					<i class="fas fa-exchange-alt"></i> Change Workflow
				</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>

{{-- 3. Revert Discontinued Application Modal --}}
<div class="modal fade custom_modal" id="revert_application" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Revert Discontinued Application</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/revert_application')}}" name="revertapplication" id="revertapplication" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="revapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label>Notes <span class="span_req">*</span></label>
								<textarea data-valid="required"  class="form-control" name="note"></textarea>

							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('revertapplication')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- 4. Add Interested Service Modal - REMOVED --}}
{{-- Feature deprecated - no UI triggers exist --}}
{{-- Backend routes still exist (/interested-service, /get-services) but modal never opens --}}
{{-- Partner/Product/Branch dropdown population routes (getProduct, getBranch) were never implemented --}}

