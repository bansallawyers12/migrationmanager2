{{-- Compose email popup for visa sheet checklist Email reminder --}}
<div id="emailmodal" data-backdrop="static" data-keyboard="false" class="modal fade custom_modal sheet-email-reminder-modal" tabindex="-1" role="dialog" aria-labelledby="sheetEmailReminderLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sheetEmailReminderLabel">Email Reminder</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" name="sendmail" action="{{ route('clients.sendmail') }}" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="client_id" id="sheet_reminder_client_id" value="">
                    <input type="hidden" name="type" value="client">
                    <input type="hidden" name="mail_type" value="1">
                    <input type="hidden" name="mail_body_type" value="sent">
                    <input type="hidden" name="compose_client_matter_id" id="compose_client_matter_id" value="">
                    <input type="hidden" name="checklist_reminder_type" id="compose_checklist_reminder_type" value="email">
                    <input type="hidden" name="email_to[]" id="sheet_reminder_email_to" value="">
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="email_from">From <span class="span_req">*</span></label>
                                @include('partials.email-from-sendgrid')
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="sheet_reminder_to_display">To <span class="span_req">*</span></label>
                                <div class="form-control bg-light" id="sheet_reminder_to_display" style="min-height: 38px;">—</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="sheet_reminder_template">Templates</label>
                                <select class="form-control" id="sheet_reminder_template" name="template">
                                    <option value="">Select</option>
                                    @foreach(\App\Models\EmailTemplate::crm()->orderBy('id', 'desc')->get() as $list)
                                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="compose_email_subject">Subject <span class="span_req">*</span></label>
                                <input type="text" name="subject" id="compose_email_subject" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Subject" value="">
                            </div>
                        </div>
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="compose_email_message">Message <span class="span_req">*</span></label>
                                <textarea id="compose_email_message" name="message" data-valid="required"></textarea>
                            </div>
                        </div>
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Attachment</label>
                                <input type="file" name="attach[]" class="form-control" multiple>
                            </div>
                        </div>
                        <div class="col-12 col-md-12 col-lg-12">
                            <button type="button" class="btn btn-primary" onclick="saveComposeEmail()">Send</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
