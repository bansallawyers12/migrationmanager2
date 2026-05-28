@extends('layouts.crm_client_detail')
@section('title', 'Sent Email — Detail')

@section('styles')
<style>
    .meta-grid dt { font-weight: 600; color: #666; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em; }
    .meta-grid dd { margin-bottom: 0.75rem; word-break: break-all; }
    .preview-frame { width: 100%; height: 600px; border: 1px solid #dee2e6; border-radius: 4px; background: #fff; }
    .preview-placeholder { background: #f8f9fa; border: 1px dashed #ced4da; border-radius: 4px;
                            padding: 2.5rem; text-align: center; color: #6c757d; }
    .attach-item { display: flex; align-items: center; gap: 0.5rem;
                   padding: 0.5rem 0.75rem; border: 1px solid #dee2e6; border-radius: 4px;
                   margin-bottom: 0.5rem; background: #fff; }
    .attach-item i { font-size: 1.1rem; }
    .type-badge-client  { background-color: #3498db; }
    .type-badge-lead    { background-color: #f39c12; }
    .type-badge-agent   { background-color: #8e44ad; }
    .section-heading    { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.06em;
                          color: #888; border-bottom: 1px solid #eee; padding-bottom: 0.4rem;
                          margin-bottom: 1rem; }
</style>
@endsection

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="server-error">@include('../Elements/flash-message')</div>

            <div class="row">
                <div class="col-3 col-md-3 col-lg-3">
                    @include('../Elements/CRM/setting')
                </div>

                <div class="col-9 col-md-9 col-lg-9">

                    {{-- Navigation --}}
                    <div class="mb-3">
                        <a href="{{ route('adminconsole.features.sent-emails.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Sent Emails
                        </a>
                    </div>

                    {{-- Metadata card --}}
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">
                                <i class="fas fa-envelope-open-text"></i>
                                {{ $email->subject ?: '(no subject)' }}
                            </h4>
                            <div class="card-header-action">
                                <span class="badge type-badge-{{ $email->type ?? 'client' }} text-white">
                                    {{ ucfirst($email->type ?? 'client') }}
                                </span>
                                <small class="text-muted ml-2">
                                    <i class="fas fa-info-circle" title="Status note"
                                       data-toggle="tooltip"
                                       title="Email was logged by the CRM. Delivery confirmation requires SendGrid webhook integration."></i>
                                    Logged
                                </small>
                            </div>
                        </div>

                        <div class="card-body">
                            <p class="section-heading">Email Details</p>
                            <dl class="row meta-grid">
                                <dt class="col-sm-3">Date &amp; Time</dt>
                                <dd class="col-sm-9">{{ $email->created_at->format('d M Y, H:i:s') }}</dd>

                                <dt class="col-sm-3">Sent By</dt>
                                <dd class="col-sm-9">
                                    @if($email->uploader)
                                        {{ $email->uploader->first_name }} {{ $email->uploader->last_name }}
                                        <small class="text-muted">&lt;{{ $email->uploader->email }}&gt;</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-3">From</dt>
                                <dd class="col-sm-9">{{ $email->from_mail ?: '—' }}</dd>

                                <dt class="col-sm-3">To</dt>
                                <dd class="col-sm-9">{{ $toDisplay ?: '—' }}</dd>

                                @if($ccDisplay)
                                <dt class="col-sm-3">CC</dt>
                                <dd class="col-sm-9">{{ $ccDisplay }}</dd>
                                @endif

                                <dt class="col-sm-3">Subject</dt>
                                <dd class="col-sm-9">{{ $email->subject ?: '—' }}</dd>

                                @if($email->template_id)
                                <dt class="col-sm-3">Template ID</dt>
                                <dd class="col-sm-9">{{ $email->template_id }}</dd>
                                @endif
                            </dl>

                            {{-- Client / Matter --}}
                            @if($email->client || $email->clientMatter)
                            <p class="section-heading mt-3">Client &amp; Matter</p>
                            <dl class="row meta-grid">
                                @if($email->client)
                                <dt class="col-sm-3">Client / Lead</dt>
                                <dd class="col-sm-9">
                                    {{ $email->client->first_name }} {{ $email->client->last_name }}
                                    @if($email->client->client_id)
                                        <small class="text-muted">({{ $email->client->client_id }})</small>
                                    @endif
                                    <a href="/crm/clients/{{ $email->client_id }}" target="_blank"
                                       class="btn btn-xs btn-outline-primary ml-2" style="font-size:0.75rem;padding:1px 6px;">
                                        <i class="fas fa-external-link-alt"></i> Open CRM
                                    </a>
                                </dd>
                                @endif

                                @if($email->clientMatter)
                                <dt class="col-sm-3">Matter</dt>
                                <dd class="col-sm-9">
                                    {{ $email->clientMatter->matter->title ?? $email->clientMatter->matter->nick_name ?? 'Matter #' . $email->client_matter_id }}
                                </dd>
                                @endif
                            </dl>
                            @endif
                        </div>
                    </div>

                    {{-- Attachments --}}
                    @if($attachments->isNotEmpty())
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">
                                <i class="fas fa-paperclip"></i> Attachments
                                <span class="badge badge-secondary ml-1">{{ $attachments->count() }}</span>
                            </h4>
                        </div>
                        <div class="card-body">
                            @foreach($attachments->where('is_inline', false) as $att)
                            <div class="attach-item">
                                @if(in_array($att->content_type, ['image/jpeg','image/png','image/gif','image/webp']))
                                    <i class="fas fa-file-image text-info"></i>
                                @elseif($att->content_type === 'application/pdf')
                                    <i class="fas fa-file-pdf text-danger"></i>
                                @else
                                    <i class="fas fa-paperclip text-secondary"></i>
                                @endif
                                <span class="flex-grow-1">{{ $att->display_name ?? $att->filename }}</span>
                                @if($att->file_size)
                                <small class="text-muted">{{ $att->formatted_file_size }}</small>
                                @endif
                                @if($att->s3_key)
                                    {{-- S3 attachment: link via the document's stored URL --}}
                                    <a href="{{ $att->file_path ?? '#' }}" target="_blank" class="btn btn-xs btn-outline-secondary"
                                       style="font-size:0.75rem;padding:1px 6px;">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Body preview --}}
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0"><i class="fas fa-envelope"></i> Email Preview</h4>
                            @if($previewUrl)
                            <div class="card-header-action">
                                <a href="{{ $previewUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-external-link-alt"></i> Open Full Preview
                                </a>
                            </div>
                            @endif
                        </div>
                        <div class="card-body">
                            @if($previewUrl)
                                <iframe src="{{ $previewUrl }}"
                                        class="preview-frame"
                                        sandbox="allow-same-origin"
                                        title="Email preview"></iframe>
                            @else
                                <div class="preview-placeholder">
                                    <i class="fas fa-file-alt fa-2x mb-2"></i>
                                    <p class="mb-0">No preview available.</p>
                                    <small>A full HTML archive is only stored for emails sent via the CRM compose window.</small>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection
