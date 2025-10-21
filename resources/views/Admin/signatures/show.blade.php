@extends('layouts.admin_client_detail')
@section('title', 'Document Details')

@section('styles')
<style>
    .document-detail-container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 20px;
    }
    
    .page-header {
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    
    .page-header h1 {
        font-size: 24px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
        font-size: 14px;
    }
    
    .header-actions {
        display: flex;
        gap: 10px;
    }
    
    .detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }
    
    .main-content-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 30px;
    }
    
    .sidebar-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
        height: fit-content;
    }
    
    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }
    
    .section-title i {
        color: #667eea;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f8f9fa;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 14px;
    }
    
    .info-value {
        color: #2c3e50;
        font-size: 14px;
        text-align: right;
    }
    
    .status-badge {
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        display: inline-block;
    }
    
    .status-badge.draft {
        background: #6c757d;
        color: white;
    }
    
    .status-badge.sent {
        background: #ffc107;
        color: #000;
    }
    
    .status-badge.signed {
        background: #28a745;
        color: white;
    }
    
    .priority-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .priority-badge.low {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .priority-badge.normal {
        background: #d4edda;
        color: #155724;
    }
    
    .priority-badge.high {
        background: #f8d7da;
        color: #721c24;
    }
    
    .signer-list {
        margin-top: 20px;
    }
    
    .signer-item {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 15px;
        border-left: 4px solid #667eea;
    }
    
    .signer-item.signed {
        border-left-color: #28a745;
    }
    
    .signer-item.pending {
        border-left-color: #ffc107;
    }
    
    .signer-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .signer-name {
        font-weight: 600;
        font-size: 16px;
        color: #2c3e50;
    }
    
    .signer-email {
        color: #6c757d;
        font-size: 14px;
    }
    
    .signer-status {
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .signer-status.signed {
        background: #28a745;
        color: white;
    }
    
    .signer-status.opened {
        background: #ffc107;
        color: #000;
    }
    
    .signer-status.pending {
        background: #6c757d;
        color: white;
    }
    
    .signer-actions {
        margin-top: 15px;
        display: flex;
        gap: 10px;
    }
    
    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-primary {
        background: #667eea;
        color: white;
    }
    
    .btn-primary:hover {
        background: #5568d3;
    }
    
    .btn-warning {
        background: #ffc107;
        color: #000;
    }
    
    .btn-warning:hover {
        background: #e0a800;
    }
    
    .btn-success {
        background: #28a745;
        color: white;
    }
    
    .btn-success:hover {
        background: #218838;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
    }
    
    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .association-info {
        background: #e7f3ff;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #0066cc;
        margin-bottom: 20px;
    }
    
    .association-info a {
        color: #0066cc;
        font-weight: 600;
        text-decoration: none;
    }
    
    .association-info a:hover {
        text-decoration: underline;
    }
    
    .timeline-item {
        padding: 12px 0;
        border-left: 2px solid #e9ecef;
        padding-left: 20px;
        position: relative;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 18px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #667eea;
    }
    
    .timeline-date {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 5px;
    }
    
    .timeline-text {
        font-size: 14px;
        color: #2c3e50;
    }
    
    .overdue-warning {
        background: #fff3cd;
        border: 1px solid #ffc107;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .overdue-warning i {
        color: #ff6b6b;
        font-size: 24px;
    }
    
    @media (max-width: 768px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
        
        .page-header {
            flex-direction: column;
            gap: 15px;
        }
    }
</style>
@endsection

@section('content')
<div class="document-detail-container">
    <div class="page-header">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.signatures.index') }}">Signature Dashboard</a></li>
                    <li class="breadcrumb-item active">Document Details</li>
                </ol>
            </nav>
            <h1>{{ $document->display_title }}</h1>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.signatures.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
    @endif

    <!-- Overdue Warning -->
    @if($document->is_overdue)
    <div class="overdue-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <div>
            <strong>This document is overdue!</strong><br>
            <small>Due date was {{ $document->due_at->format('M d, Y g:i A') }}</small>
        </div>
    </div>
    @endif

    <!-- Association Info -->
    @if($document->documentable)
    <div class="association-info">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <strong><i class="fas fa-link"></i> Associated with:</strong>
                @if($document->documentable_type === 'App\Models\Admin')
                <a href="{{ route('admin.client.detail', $document->documentable_id) }}">
                    Client: {{ $document->documentable->first_name }} {{ $document->documentable->last_name }}
                </a>
                @elseif($document->documentable_type === 'App\Models\Lead')
                <a href="{{ route('admin.lead.detail', $document->documentable_id) }}">
                    Lead: {{ $document->documentable->first_name }} {{ $document->documentable->last_name }}
                </a>
                @endif
            </div>
            @if(auth('admin')->user()->role === 1)
            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDetach()">
                <i class="fas fa-unlink"></i> Detach
            </button>
            @endif
        </div>
    </div>
    @endif

    <div class="detail-grid">
        <!-- Main Content -->
        <div>
            <!-- Document Info Card -->
            <div class="main-content-card">
                <h2 class="section-title">
                    <i class="fas fa-file-alt"></i>
                    Document Information
                </h2>

                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value">
                        <span class="status-badge {{ $document->status }}">
                            {{ ucfirst($document->status) }}
                        </span>
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Document Type</span>
                    <span class="info-value">{{ ucfirst($document->document_type) }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Priority</span>
                    <span class="info-value">
                        <span class="priority-badge {{ $document->priority }}">
                            {{ ucfirst($document->priority) }}
                        </span>
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">File Name</span>
                    <span class="info-value">{{ $document->file_name }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Created By</span>
                    <span class="info-value">
                        {{ $document->creator ? $document->creator->first_name . ' ' . $document->creator->last_name : 'N/A' }}
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Created At</span>
                    <span class="info-value">
                        {{ $document->created_at->format('M d, Y g:i A') }}<br>
                        <small style="color: #6c757d;">{{ $document->created_at->diffForHumans() }}</small>
                    </span>
                </div>

                @if($document->due_at)
                <div class="info-row">
                    <span class="info-label">Due Date</span>
                    <span class="info-value">
                        {{ $document->due_at->format('M d, Y g:i A') }}<br>
                        <small style="color: #6c757d;">{{ $document->due_at->diffForHumans() }}</small>
                    </span>
                </div>
                @endif

                <div class="info-row">
                    <span class="info-label">Last Activity</span>
                    <span class="info-value">
                        {{ $document->last_activity_at ? $document->last_activity_at->format('M d, Y g:i A') : 'N/A' }}
                    </span>
                </div>

                @if($document->status === 'signed' && $document->signed_doc_link)
                <div style="margin-top: 20px; text-align: center;">
                    <a href="{{ route('admin.documents.download.signed', $document->id) }}" class="btn btn-success btn-lg">
                        <i class="fas fa-download"></i> Download Signed Document
                    </a>
                </div>
                @endif
            </div>

            <!-- Signers Card -->
            <div class="main-content-card" style="margin-top: 20px;">
                <h2 class="section-title">
                    <i class="fas fa-users"></i>
                    Signers ({{ $document->signers->count() }})
                </h2>

                <div class="signer-list">
                    @forelse($document->signers as $signer)
                    <div class="signer-item {{ $signer->status }}">
                        <div class="signer-header">
                            <div>
                                <div class="signer-name">{{ $signer->name }}</div>
                                <div class="signer-email">{{ $signer->email }}</div>
                            </div>
                            <span class="signer-status {{ $signer->opened_at && $signer->status === 'pending' ? 'opened' : $signer->status }}">
                                @if($signer->status === 'signed')
                                    <i class="fas fa-check-circle"></i> Signed
                                @elseif($signer->opened_at && $signer->status === 'pending')
                                    <i class="fas fa-eye"></i> Opened - Not Signed
                                @else
                                    <i class="fas fa-clock"></i> Pending
                                @endif
                            </span>
                        </div>

                        @if($signer->opened_at)
                        <div style="margin-top: 10px; font-size: 13px; color: #6c757d;">
                            <i class="fas fa-eye"></i> Opened: {{ $signer->opened_at->format('M d, Y g:i A') }}
                        </div>
                        @endif

                        @if($signer->signed_at)
                        <div style="margin-top: 5px; font-size: 13px; color: #6c757d;">
                            <i class="fas fa-check"></i> Signed: {{ $signer->signed_at->format('M d, Y g:i A') }}
                        </div>
                        @endif

                        @if($signer->status === 'pending')
                        <div class="signer-actions">
                            <form action="{{ route('admin.signatures.reminder', $document->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <input type="hidden" name="signer_id" value="{{ $signer->id }}">
                                <button type="submit" class="btn btn-warning" 
                                        {{ ($signer->reminder_count >= 3 || ($signer->last_reminder_sent_at && $signer->last_reminder_sent_at->diffInHours(now()) < 24)) ? 'disabled' : '' }}>
                                    <i class="fas fa-bell"></i> 
                                    Send Reminder ({{ $signer->reminder_count }}/3)
                                </button>
                            </form>
                            
                            <button type="button" class="btn btn-secondary" onclick="copySigningLink('{{ url("/sign/{$document->id}/{$signer->token}") }}')">
                                <i class="fas fa-copy"></i> Copy Link
                            </button>
                        </div>
                        
                        @if($signer->last_reminder_sent_at)
                        <div style="margin-top: 10px; font-size: 12px; color: #6c757d;">
                            Last reminder sent: {{ $signer->last_reminder_sent_at->diffForHumans() }}
                        </div>
                        @endif
                        @endif
                    </div>
                    @empty
                    <div style="text-align: center; padding: 40px; color: #6c757d;">
                        <i class="fas fa-user-slash" style="font-size: 32px; margin-bottom: 10px;"></i>
                        <p>No signers added yet</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Quick Actions Card -->
            <div class="sidebar-card">
                <h3 class="section-title" style="font-size: 16px;">
                    <i class="fas fa-bolt"></i>
                    Quick Actions
                </h3>

                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @if($document->status === 'signed')
                    <a href="{{ route('admin.documents.download.signed', $document->id) }}" class="btn btn-success btn-block">
                        <i class="fas fa-download"></i> Download
                    </a>
                    @endif
                    
                    <button type="button" class="btn btn-primary btn-block" onclick="viewDocument()">
                        <i class="fas fa-eye"></i> View Document
                    </button>
                </div>
            </div>

            <!-- Association History Card -->
            @if($document->notes->count() > 0)
            <div class="sidebar-card" style="margin-top: 20px;">
                <h3 class="section-title" style="font-size: 16px;">
                    <i class="fas fa-clipboard-list"></i>
                    Association History
                </h3>

                <div style="padding-left: 10px;">
                    @foreach($document->notes as $note)
                    <div class="timeline-item">
                        <div class="timeline-date">{{ $note->created_at->format('M d, Y g:i A') }}</div>
                        <div class="timeline-text">
                            <strong>{{ $note->action_text }}</strong>
                            @if($note->creator)
                            <br><small>by {{ $note->creator->first_name }} {{ $note->creator->last_name }}</small>
                            @endif
                        </div>
                        @if($note->note)
                        <div style="margin-top: 5px; font-size: 13px; color: #6c757d; font-style: italic;">
                            "{{ $note->note }}"
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Activity Timeline Card -->
            <div class="sidebar-card" style="margin-top: 20px;">
                <h3 class="section-title" style="font-size: 16px;">
                    <i class="fas fa-history"></i>
                    Activity Timeline
                </h3>

                <div style="padding-left: 10px;">
                    <div class="timeline-item">
                        <div class="timeline-date">{{ $document->created_at->format('M d, Y g:i A') }}</div>
                        <div class="timeline-text">Document created</div>
                    </div>

                    @if($document->status !== 'draft')
                    <div class="timeline-item">
                        <div class="timeline-date">{{ $document->last_activity_at->format('M d, Y g:i A') }}</div>
                        <div class="timeline-text">Document sent for signature</div>
                    </div>
                    @endif

                    @foreach($document->signers->where('opened_at', '!=', null) as $signer)
                    <div class="timeline-item">
                        <div class="timeline-date">{{ $signer->opened_at->format('M d, Y g:i A') }}</div>
                        <div class="timeline-text">Opened by {{ $signer->name }}</div>
                    </div>
                    @endforeach

                    @foreach($document->signers->where('signed_at', '!=', null) as $signer)
                    <div class="timeline-item">
                        <div class="timeline-date">{{ $signer->signed_at->format('M d, Y g:i A') }}</div>
                        <div class="timeline-text">Signed by {{ $signer->name }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detach Confirmation Modal -->
<div class="modal fade" id="detachModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.signatures.detach', $document->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-unlink"></i> Detach Document
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Are you sure?</strong>
                        <p style="margin: 10px 0 0 0;">This will remove the association between this document and the client/lead.</p>
                    </div>
                    
                    <div class="form-group">
                        <label>Reason for detachment (Optional)</label>
                        <textarea class="form-control" name="reason" rows="3" placeholder="Why is this document being detached?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-unlink"></i> Detach Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function copySigningLink(url) {
    navigator.clipboard.writeText(url).then(function() {
        alert('Signing link copied to clipboard!');
    }, function(err) {
        console.error('Failed to copy: ', err);
        prompt('Copy this link:', url);
    });
}

function viewDocument() {
    alert('Document viewer feature coming soon!');
}

function confirmDetach() {
    $('#detachModal').modal('show');
}
</script>
@endsection

