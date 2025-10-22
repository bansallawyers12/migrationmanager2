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
    
    .match-alert {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        border-left: 4px solid #4caf50;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: none;
        animation: slideDown 0.3s ease;
    }
    
    .match-alert.show {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .match-alert-icon {
        font-size: 32px;
        color: #4caf50;
    }
    
    .match-alert-content {
        flex: 1;
    }
    
    .match-alert-title {
        font-weight: 600;
        color: #2e7d32;
        margin-bottom: 5px;
    }
    
    .match-alert-text {
        color: #558b2f;
        font-size: 14px;
    }
    
    .match-alert-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn-match-accept {
        background: #4caf50;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-weight: 500;
    }
    
    .btn-match-dismiss {
        background: #757575;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-weight: 500;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-step {
        transition: all 0.3s ease;
    }

    .search-results {
        margin-top: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .search-results h6 {
        margin-bottom: 10px;
        color: #495057;
        font-weight: 600;
    }

    .match-item {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 12px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .match-item:hover {
        border-color: #667eea;
        box-shadow: 0 2px 4px rgba(102, 126, 234, 0.1);
    }

    .match-item.selected {
        border-color: #667eea;
        background: #e8ecff;
    }

    .match-item:last-child {
        margin-bottom: 0;
    }

    .match-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .match-name {
        font-weight: 600;
        color: #2c3e50;
        font-size: 14px;
    }

    .match-type {
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .match-type.client {
        background: #d1ecf1;
        color: #0c5460;
    }

    .match-type.lead {
        background: #fff3cd;
        color: #856404;
    }

    .match-email {
        color: #6c757d;
        font-size: 13px;
        margin-bottom: 5px;
    }

    .match-matters {
        margin-top: 8px;
    }

    .match-matters-title {
        font-size: 12px;
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
    }

    .matter-item {
        background: #f8f9fa;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        color: #6c757d;
        margin-bottom: 3px;
        display: inline-block;
        margin-right: 5px;
    }
    
    .matter-item.clickable {
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }
    
    .matter-item.clickable:hover {
        background: #e9ecef;
        border-color: #667eea;
        color: #667eea;
    }
    
    .matter-item.selected {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .no-matches {
        margin-top: 15px;
    }

    .no-matches .alert {
        margin-bottom: 0;
    }

    .step-indicator {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .step-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #e9ecef;
        margin: 0 5px;
        transition: all 0.3s ease;
    }

    .step-dot.active {
        background: #667eea;
    }

    .step-dot.completed {
        background: #28a745;
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
            <small>Due date was {{ $document->due_at ? $document->due_at->format('M d, Y g:i A') : 'N/A' }}</small>
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
                <a href="{{ route('admin.clients.detail', $document->documentable_id) }}">
                    Client: {{ $document->documentable->first_name }} {{ $document->documentable->last_name }}
                </a>
                @elseif($document->documentable_type === 'App\Models\Lead')
                <a href="{{ route('detail', $document->documentable_id) }}">
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
                        {{ $document->due_at ? $document->due_at->format('M d, Y g:i A') : 'N/A' }}<br>
                        @if($document->due_at)
                        <small style="color: #6c757d;">{{ $document->due_at->diffForHumans() }}</small>
                        @endif
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
                        <div style="margin-top: 20px;">
                            <button type="button" class="btn btn-primary" onclick="openAddSignerModal()">
                                <i class="fas fa-user-plus"></i> Add Signer
                            </button>
                        </div>
                    </div>
                    @endforelse
                </div>
                
                <!-- Add Another Signer Button -->
                @if($document->signers->count() > 0)
                <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                    <button type="button" class="btn btn-outline-primary" onclick="openAddSignerModal()">
                        <i class="fas fa-user-plus"></i> Add Another Signer
                    </button>
                </div>
                @endif
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

                    @if($document->status !== 'draft' && $document->last_activity_at)
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

<!-- Add Signer Modal -->
<div class="modal fade" id="addSignerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="addSignerForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus"></i> Add Signer
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="step-indicator">
                    <div class="step-dot active" id="step1"></div>
                    <div class="step-dot" id="step2"></div>
                    <div class="step-dot" id="step3"></div>
                </div>
                <div class="modal-body">
                    <!-- Step 1: Email Input -->
                    <div id="emailStep" class="modal-step">
                        <div class="form-group">
                            <label for="modal_signer_email">Signer Email <span style="color: #dc3545;">*</span></label>
                            <input type="email" class="form-control" id="modal_signer_email" name="signer_email" 
                                   placeholder="john@example.com" required>
                            <small class="form-help-text">Enter the email address to find existing clients/leads</small>
                        </div>
                        
                        <div id="emailSearchResults" class="search-results" style="display: none;">
                            <h6>Found Matches:</h6>
                            <div id="matchesList"></div>
                            <div id="matterSelectionConfirmation"></div>
                        </div>
                        
                        <div id="noMatchesMessage" class="no-matches" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No existing clients or leads found with this email address.
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Client/Lead Selection -->
                    <div id="selectionStep" class="modal-step" style="display: none;">
                        <div class="form-group">
                            <label>Select Client/Lead:</label>
                            <div id="clientLeadSelection"></div>
                        </div>
                    </div>

                    <!-- Step 3: Manual Name Input (when no matches) -->
                    <div id="nameStep" class="modal-step" style="display: none;">
                        <div class="form-group">
                            <label for="modal_signer_name">Signer Name <span style="color: #dc3545;">*</span></label>
                            <input type="text" class="form-control" id="modal_signer_name" name="signer_name" 
                                   placeholder="John Doe">
                            <small class="form-help-text">Enter the full name of the signer</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="modal_from_email">Send From Email Account</label>
                        <select class="form-control" id="modal_from_email" name="from_email">
                            <option value="">-- Use Default --</option>
                            @foreach($emailAccounts as $account)
                                <option value="{{ $account->email }}">{{ $account->display_name }} ({{ $account->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="modal_email_template">Email Template</label>
                        <select class="form-control" id="modal_email_template" name="email_template">
                            <option value="emails.signature.send">Standard - Professional signature request</option>
                            <option value="emails.signature.send_agreement">Agreement - With legal notices and attachments</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="modal_email_subject">Email Subject (Optional)</label>
                        <input type="text" class="form-control" id="modal_email_subject" name="email_subject" 
                               placeholder="Leave blank for default subject">
                    </div>

                    <div class="form-group">
                        <label for="modal_email_message">Custom Message (Optional)</label>
                        <textarea class="form-control" id="modal_email_message" name="email_message" rows="3"
                                  placeholder="Add a personal message..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-secondary" id="backBtn" onclick="goBackStep()" style="display: none;">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                    <button type="button" class="btn btn-primary" id="nextBtn" onclick="goNextStep()" style="display: none;">
                        Next <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn" style="display: none;">
                        <i class="fas fa-user-plus"></i> Add Signer
                    </button>
                </div>
            </form>
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

// ===== ADD SIGNER MODAL FUNCTIONALITY =====
let currentStep = 1;
let emailMatches = [];
let selectedMatch = null;
let selectedMatter = null;
let selectedClient = null;

function openAddSignerModal() {
    // Reset everything
    currentStep = 1;
    emailMatches = [];
    selectedMatch = null;
    selectedMatter = null;
    selectedClient = null;
    
    // Reset form
    document.getElementById('addSignerForm').reset();
    
    // Hide all steps
    document.querySelectorAll('.modal-step').forEach(step => step.style.display = 'none');
    
    // Show only email step
    document.getElementById('emailStep').style.display = 'block';
    
    // Reset step indicators
    document.querySelectorAll('.step-dot').forEach((dot, index) => {
        dot.classList.remove('active', 'completed');
        if (index === 0) dot.classList.add('active');
    });
    
    // Reset buttons
    document.getElementById('backBtn').style.display = 'none';
    document.getElementById('nextBtn').style.display = 'none';
    document.getElementById('submitBtn').style.display = 'none';
    
    // Set form action
    document.getElementById('addSignerForm').action = '{{ route("admin.signatures.store") }}';
    
    // Add hidden document_id field
    let existingInput = document.getElementById('modal_document_id');
    if (!existingInput) {
        let hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'document_id';
        hiddenInput.id = 'modal_document_id';
        hiddenInput.value = '{{ $document->id }}';
        document.getElementById('addSignerForm').appendChild(hiddenInput);
    } else {
        existingInput.value = '{{ $document->id }}';
    }
    
    // Show modal
    $('#addSignerModal').modal('show');
}

// ===== EMAIL SEARCH FUNCTIONALITY =====
let emailCheckTimeout = null;

// Email field change detection with debounce
document.addEventListener('DOMContentLoaded', function() {
    const emailField = document.getElementById('modal_signer_email');
    if (emailField) {
        emailField.addEventListener('input', function() {
            clearTimeout(emailCheckTimeout);
            const email = this.value.trim();
            
            // Hide previous results
            document.getElementById('emailSearchResults').style.display = 'none';
            document.getElementById('noMatchesMessage').style.display = 'none';
            
            // Only check if it's a valid-looking email
            if (email && email.includes('@') && email.includes('.')) {
                emailCheckTimeout = setTimeout(() => {
                    searchEmailMatches(email);
                }, 800); // Wait 800ms after user stops typing
            }
        });
    }
});

// Search for email matches via API
function searchEmailMatches(email) {
    fetch('{{ route('admin.signatures.suggest-association') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.matches && data.matches.length > 0) {
            // Matches found
            emailMatches = data.matches;
            
            if (data.matches.length === 1) {
                // Auto-select single match and go to step 3
                selectedMatch = data.matches[0];
                selectedClient = data.matches[0];
                document.getElementById('modal_signer_name').value = selectedMatch.name;
                currentStep = 3;
                showStep(3);
            } else {
                // Multiple matches, show selection
                displayEmailMatches();
            }
        } else {
            // No matches found
            emailMatches = [];
            document.getElementById('noMatchesMessage').style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error checking email match:', error);
        emailMatches = [];
        document.getElementById('noMatchesMessage').style.display = 'block';
    });
}

// Display email matches
function displayEmailMatches() {
    const resultsDiv = document.getElementById('emailSearchResults');
    const matchesList = document.getElementById('matchesList');
    
    matchesList.innerHTML = '';
    
    emailMatches.forEach((match, index) => {
        const matchDiv = document.createElement('div');
        matchDiv.className = 'match-item';
        matchDiv.onclick = () => selectMatch(match, index);
        
        let mattersHtml = '';
        if (match.has_matters && match.matters.length > 0) {
            mattersHtml = `
                <div class="match-matters">
                    <div class="match-matters-title">Matters (click to select):</div>
                    ${match.matters.map(matter => `<span class="matter-item clickable" onclick="selectMatter('${match.id}', '${matter.id}', '${matter.label}')">${matter.label}</span>`).join('')}
                </div>
            `;
        }
        
        matchDiv.innerHTML = `
            <div class="match-header">
                <div class="match-name">${match.name}</div>
                <span class="match-type ${match.type}">${match.type}</span>
            </div>
            <div class="match-email">${match.email}</div>
            ${mattersHtml}
        `;
        
        matchesList.appendChild(matchDiv);
    });
    
    resultsDiv.style.display = 'block';
}

// Select a match
function selectMatch(match, index) {
    selectedMatch = match;
    selectedClient = match; // Store the selected client/lead
    
    // Update visual selection
    document.querySelectorAll('.match-item').forEach((item, i) => {
        item.classList.toggle('selected', i === index);
    });
    
    // Auto-fill name field
    document.getElementById('modal_signer_name').value = match.name;
    
    // Auto-advance to step 3 when a match is selected
    if (currentStep === 1 || currentStep === 2) {
        currentStep = 3;
        showStep(3);
    }
}

// Select a matter
function selectMatter(clientId, matterId, matterLabel) {
    selectedMatter = {
        client_id: clientId,
        matter_id: matterId,
        label: matterLabel
    };
    
    // Update visual selection
    document.querySelectorAll('.matter-item.clickable').forEach(item => {
        item.classList.remove('selected');
    });
    
    // Find and highlight the selected matter
    const selectedMatterElement = document.querySelector(`[onclick="selectMatter('${clientId}', '${matterId}', '${matterLabel}')"]`);
    if (selectedMatterElement) {
        selectedMatterElement.classList.add('selected');
    }
    
    // Show a confirmation message
    const confirmationDiv = document.getElementById('matterSelectionConfirmation');
    if (confirmationDiv) {
        confirmationDiv.innerHTML = `
            <div class="alert alert-success" style="margin-top: 10px;">
                <i class="fas fa-check-circle"></i> Selected matter: <strong>${matterLabel}</strong>
            </div>
        `;
    }
}

// ===== STEP NAVIGATION =====
function goNextStep() {
    if (currentStep === 1) {
        // From email step
        if (emailMatches.length > 1 && !selectedMatch) {
            // Multiple matches, need to show selection step
            currentStep = 2;
            showStep(2);
        } else if (emailMatches.length === 1 && !selectedMatch) {
            // Single match, auto-select it
            selectedMatch = emailMatches[0];
            document.getElementById('modal_signer_name').value = selectedMatch.name;
            currentStep = 3; // Skip to final step
            showStep(3);
        } else if (emailMatches.length > 0 && selectedMatch) {
            // Match already selected, go to final step
            currentStep = 3;
            showStep(3);
        } else if (emailMatches.length === 0) {
            // No matches, go to name input step
            currentStep = 2;
            showStep(2);
        }
    } else if (currentStep === 2) {
        // From selection/name step to final step
        currentStep = 3;
        showStep(3);
    }
}

function goBackStep() {
    if (currentStep === 2) {
        currentStep = 1;
        showStep(1);
    } else if (currentStep === 3) {
        if (emailMatches.length > 0) {
            currentStep = 1;
            showStep(1);
        } else {
            currentStep = 2;
            showStep(2);
        }
    }
}

function showStep(step) {
    // Hide all steps
    document.querySelectorAll('.modal-step').forEach(stepEl => stepEl.style.display = 'none');
    
    // Show current step
    if (step === 1) {
        document.getElementById('emailStep').style.display = 'block';
    } else if (step === 2) {
        // Determine if this is selection step or name step
        if (emailMatches.length > 1 && !selectedMatch) {
            // Show selection step
            document.getElementById('selectionStep').style.display = 'block';
            populateSelectionStep();
        } else {
            // Show name input step
            document.getElementById('nameStep').style.display = 'block';
        }
    } else if (step === 3) {
        // Show email settings (the existing form fields)
        // These are already visible, we just need to show the submit button
        // Also ensure the name field is visible and filled
        document.getElementById('nameStep').style.display = 'block';
    }
    
    // Update step indicators
    document.querySelectorAll('.step-dot').forEach((dot, index) => {
        dot.classList.remove('active', 'completed');
        if (index < step - 1) {
            dot.classList.add('completed');
        } else if (index === step - 1) {
            dot.classList.add('active');
        }
    });
    
    // Update buttons
    document.getElementById('backBtn').style.display = step > 1 ? 'inline-block' : 'none';
    document.getElementById('nextBtn').style.display = step < 3 ? 'inline-block' : 'none';
    document.getElementById('submitBtn').style.display = (step === 3 || selectedMatch) ? 'inline-block' : 'none';
}

// Populate the selection step with matches
function populateSelectionStep() {
    const selectionDiv = document.getElementById('clientLeadSelection');
    selectionDiv.innerHTML = '';
    
    emailMatches.forEach((match, index) => {
        const matchDiv = document.createElement('div');
        matchDiv.className = 'match-item';
        matchDiv.onclick = () => selectMatch(match, index);
        
        let mattersHtml = '';
        if (match.has_matters && match.matters.length > 0) {
            mattersHtml = `
                <div class="match-matters">
                    <div class="match-matters-title">Matters (click to select):</div>
                    ${match.matters.map(matter => `<span class="matter-item clickable" onclick="selectMatter('${match.id}', '${matter.id}', '${matter.label}')">${matter.label}</span>`).join('')}
                </div>
            `;
        }
        
        matchDiv.innerHTML = `
            <div class="match-header">
                <div class="match-name">${match.name}</div>
                <span class="match-type ${match.type}">${match.type}</span>
            </div>
            <div class="match-email">${match.email}</div>
            ${mattersHtml}
        `;
        
        selectionDiv.appendChild(matchDiv);
    });
}

// ===== FORM SUBMISSION =====
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addSignerForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate required fields
            const email = document.getElementById('modal_signer_email').value;
            const name = document.getElementById('modal_signer_name').value;
            
            if (!email || !name) {
                alert('Please fill in all required fields.');
                return;
            }
            
            // Add selected matter data to form if available
            if (selectedMatter) {
                const matterInput = document.createElement('input');
                matterInput.type = 'hidden';
                matterInput.name = 'client_matter_id';
                matterInput.value = selectedMatter.matter_id;
                form.appendChild(matterInput);
            } else if (selectedClient) {
                // If no matter selected but client/lead is selected, send client ID
                const clientInput = document.createElement('input');
                clientInput.type = 'hidden';
                clientInput.name = 'selected_client_id';
                clientInput.value = selectedClient.id;
                form.appendChild(clientInput);
            }
            
            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding Signer...';
            submitBtn.disabled = true;
            
            // Submit form
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(response => {
                if (response.ok) {
                    // Close modal and reload page
                    $('#addSignerModal').modal('hide');
                    location.reload();
                } else {
                    return response.text().then(text => {
                        throw new Error('Server error: ' + text);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding signer: ' + error.message);
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});
</script>
@endsection

