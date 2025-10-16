@extends('layouts.admin')
@section('title', 'SMS History')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/listing-pagination.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
<style>
    .sms-history-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .sms-history-header h2 {
        margin: 0 0 10px 0;
        font-size: 1.8rem;
        font-weight: 600;
    }
    
    .sms-history-header p {
        margin: 0;
        opacity: 0.9;
        font-size: 1rem;
    }
    
    .sms-filters {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .sms-table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .sms-table {
        width: 100%;
        margin: 0;
    }
    
    .sms-table th {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        color: #495057;
        font-weight: 600;
        border: none;
        padding: 15px 12px;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .sms-table td {
        padding: 12px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f4;
    }
    
    .sms-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-badge.sent {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-badge.pending {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .status-badge.delivered {
        background-color: #cce5ff;
        color: #004085;
    }
    
    .status-badge.failed {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .provider-badge {
        padding: 3px 8px;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .provider-badge.cellcast {
        background-color: #ffe6e6;
        color: #d63031;
    }
    
    .provider-badge.twilio {
        background-color: #e6f7ff;
        color: #0066cc;
    }
    
    .message-type-badge {
        padding: 2px 6px;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 400;
        border: 1px solid;
    }
    
    .message-type-badge.verification {
        background-color: #fff9c4;
        color: #b7791f;
        border-color: #f4c842;
    }
    
    .message-type-badge.notification {
        background-color: #e7f3ff;
        color: #2c5aa0;
        border-color: #4dabf7;
    }
    
    .message-type-badge.manual {
        background-color: #f0f0f0;
        color: #495057;
        border-color: #ced4da;
    }
    
    .message-type-badge.reminder {
        background-color: #ffe0e6;
        color: #c92a2a;
        border-color: #ffa8a8;
    }
    
    .phone-display {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 0.9rem;
        color: #495057;
    }
    
    .message-preview {
        max-width: 250px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .client-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
    }
    
    .client-link:hover {
        color: #5a6fd8;
        text-decoration: underline;
    }
    
    .action-buttons {
        display: flex;
        gap: 5px;
    }
    
    .btn-view {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .btn-view:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .no-records {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    
    .no-records i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #dee2e6;
    }
</style>
@endsection

@section('content')
<div class="listing-container">
    <div class="sms-history-header">
        <h2><i class="fas fa-history"></i> SMS History</h2>
        <p>View and manage all SMS messages sent through the system</p>
    </div>

    {{-- Filters Section (Sprint 4) --}}
    <div class="sms-filters">
        <h5><i class="fas fa-filter"></i> Filters <small class="text-muted">(Coming in Sprint 4)</small></h5>
        <div class="row">
            <div class="col-md-3">
                <label>Date Range</label>
                <input type="text" class="form-control" placeholder="Select date range" disabled>
            </div>
            <div class="col-md-2">
                <label>Status</label>
                <select class="form-control" disabled>
                    <option>All Statuses</option>
                    <option>Sent</option>
                    <option>Delivered</option>
                    <option>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Provider</label>
                <select class="form-control" disabled>
                    <option>All Providers</option>
                    <option>Cellcast</option>
                    <option>Twilio</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Search</label>
                <input type="text" class="form-control" placeholder="Phone number or message..." disabled>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button class="btn btn-primary form-control" disabled>
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </div>
    </div>

    {{-- SMS History Table --}}
    <div class="sms-table-container">
        <table class="sms-table">
            <thead>
                <tr>
                    <th>Date/Time</th>
                    <th>To</th>
                    <th>Client</th>
                    <th>Message</th>
                    <th>Type</th>
                    <th>Provider</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($smsLogs as $smsLog)
                    <tr>
                        <td>
                            <div style="font-size: 0.85rem;">
                                <strong>{{ $smsLog->created_at->format('M j, Y') }}</strong><br>
                                <span class="text-muted">{{ $smsLog->created_at->format('g:i A') }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="phone-display">{{ $smsLog->formatted_phone ?? $smsLog->recipient_phone }}</span>
                        </td>
                        <td>
                            @if($smsLog->client)
                                <a href="{{ route('admin.clients.detail', $smsLog->client->id) }}" class="client-link">
                                    {{ $smsLog->client->name }}
                                </a>
                            @else
                                <span class="text-muted">No client</span>
                            @endif
                        </td>
                        <td>
                            <div class="message-preview" title="{{ $smsLog->message_content }}">
                                {{ $smsLog->message_content }}
                            </div>
                        </td>
                        <td>
                            <span class="message-type-badge {{ $smsLog->message_type }}">
                                {{ ucfirst($smsLog->message_type) }}
                            </span>
                        </td>
                        <td>
                            <span class="provider-badge {{ $smsLog->provider }}">
                                {{ strtoupper($smsLog->provider) }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge {{ $smsLog->status }}">
                                @if($smsLog->status === 'sent')
                                    <i class="fas fa-check"></i>
                                @elseif($smsLog->status === 'delivered')
                                    <i class="fas fa-check-double"></i>
                                @elseif($smsLog->status === 'failed')
                                    <i class="fas fa-times"></i>
                                @else
                                    <i class="fas fa-clock"></i>
                                @endif
                                {{ ucfirst($smsLog->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.sms.history.show', $smsLog->id) }}" class="btn-view">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="no-records">
                                <i class="fas fa-inbox"></i>
                                <h5>No SMS Messages Found</h5>
                                <p>No SMS messages have been sent yet, or none match your current filters.</p>
                                <a href="{{ route('admin.sms.send.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Send First SMS
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($smsLogs->hasPages())
            <div class="listing-pagination">
                {{ $smsLogs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Future: Real-time status updates
    // Future: Advanced filtering with AJAX
    // Future: Bulk actions (resend, export)
    
    // Add tooltip for long messages
    $('[title]').tooltip();
});
</script>
@endsection
