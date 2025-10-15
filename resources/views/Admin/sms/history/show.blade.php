@extends('layouts.admin')
@section('title', 'SMS Details')

@section('styles')
<style>
    .sms-detail-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .sms-detail-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 12px 12px 0 0;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .sms-detail-header h2 {
        margin: 0 0 10px 0;
        font-size: 1.8rem;
        font-weight: 600;
    }
    
    .sms-detail-header p {
        margin: 0;
        opacity: 0.9;
    }
    
    .sms-detail-content {
        background: white;
        border-radius: 0 0 12px 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    
    .sms-info-section {
        padding: 25px;
        border-bottom: 1px solid #f1f3f4;
    }
    
    .sms-info-section:last-child {
        border-bottom: none;
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f8f9fa;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .info-item {
        display: flex;
        flex-direction: column;
    }
    
    .info-label {
        font-size: 0.85rem;
        font-weight: 500;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }
    
    .info-value {
        font-size: 1rem;
        color: #495057;
        font-weight: 500;
    }
    
    .phone-display {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 1.1rem;
        color: #495057;
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 6px;
        border-left: 3px solid #667eea;
    }
    
    .message-content {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid #667eea;
        font-size: 1rem;
        line-height: 1.6;
        color: #495057;
        margin-top: 10px;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
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
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 0.85rem;
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
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 500;
        text-transform: capitalize;
        border: 2px solid;
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
    
    .client-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
        padding: 8px 0;
        display: inline-block;
    }
    
    .client-link:hover {
        color: #5a6fd8;
        text-decoration: underline;
    }
    
    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #dc3545;
        margin-top: 10px;
    }
    
    .back-button {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    
    .back-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        color: white;
        text-decoration: none;
    }
    
    .timeline-item {
        display: flex;
        align-items: start;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #f1f3f4;
    }
    
    .timeline-item:last-child {
        border-bottom: none;
    }
    
    .timeline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    
    .timeline-icon.sent {
        background: #d4edda;
        color: #155724;
    }
    
    .timeline-icon.delivered {
        background: #cce5ff;
        color: #004085;
    }
    
    .timeline-icon.failed {
        background: #f8d7da;
        color: #721c24;
    }
    
    .timeline-content {
        flex: 1;
    }
    
    .timeline-time {
        font-size: 0.8rem;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="sms-detail-container">
    <a href="{{ route('admin.sms.history') }}" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Back to SMS History
    </a>

    <div class="sms-detail-content">
        <div class="sms-detail-header">
            <h2><i class="fas fa-sms"></i> SMS Message Details</h2>
            <p>Sent {{ $smsLog->created_at->format('M j, Y \a\t g:i A') }}</p>
        </div>

        {{-- Basic Information --}}
        <div class="sms-info-section">
            <h4 class="section-title">
                <i class="fas fa-info-circle"></i>
                Message Information
            </h4>
            
            <div class="info-grid">
                <div class="info-item">
                    <label class="info-label">Recipient Phone</label>
                    <div class="phone-display">{{ $smsLog->formatted_phone ?? $smsLog->recipient_phone }}</div>
                </div>
                
                <div class="info-item">
                    <label class="info-label">Status</label>
                    <div class="info-value">
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
                    </div>
                </div>
                
                <div class="info-item">
                    <label class="info-label">Provider</label>
                    <div class="info-value">
                        <span class="provider-badge {{ $smsLog->provider }}">
                            @if($smsLog->provider === 'cellcast')
                                <i class="fas fa-flag"></i>
                            @else
                                <i class="fas fa-globe"></i>
                            @endif
                            {{ strtoupper($smsLog->provider) }}
                        </span>
                    </div>
                </div>
                
                <div class="info-item">
                    <label class="info-label">Message Type</label>
                    <div class="info-value">
                        <span class="message-type-badge {{ $smsLog->message_type }}">
                            {{ ucfirst($smsLog->message_type) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Client Information --}}
        @if($smsLog->client)
        <div class="sms-info-section">
            <h4 class="section-title">
                <i class="fas fa-user"></i>
                Client Information
            </h4>
            
            <div class="info-grid">
                <div class="info-item">
                    <label class="info-label">Client Name</label>
                    <div class="info-value">
                        <a href="{{ route('admin.clients.detail', $smsLog->client->id) }}" class="client-link">
                            <i class="fas fa-external-link-alt"></i>
                            {{ $smsLog->client->name }}
                        </a>
                    </div>
                </div>
                
                @if($smsLog->contact)
                <div class="info-item">
                    <label class="info-label">Contact Name</label>
                    <div class="info-value">{{ $smsLog->contact->name ?? 'N/A' }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Message Content --}}
        <div class="sms-info-section">
            <h4 class="section-title">
                <i class="fas fa-comment"></i>
                Message Content
            </h4>
            
            <div class="message-content">
                {{ $smsLog->message_content }}
            </div>
            
            <div class="info-grid" style="margin-top: 20px;">
                <div class="info-item">
                    <label class="info-label">Character Count</label>
                    <div class="info-value">{{ strlen($smsLog->message_content) }} characters</div>
                </div>
                
                <div class="info-item">
                    <label class="info-label">SMS Parts</label>
                    <div class="info-value">{{ ceil(strlen($smsLog->message_content) / 160) }} part(s)</div>
                </div>
            </div>
        </div>

        {{-- Technical Details --}}
        <div class="sms-info-section">
            <h4 class="section-title">
                <i class="fas fa-cog"></i>
                Technical Details
            </h4>
            
            <div class="info-grid">
                <div class="info-item">
                    <label class="info-label">Provider Message ID</label>
                    <div class="info-value">
                        <code>{{ $smsLog->provider_message_id ?? 'N/A' }}</code>
                    </div>
                </div>
                
                <div class="info-item">
                    <label class="info-label">Sent By</label>
                    <div class="info-value">{{ $smsLog->sender->name ?? 'System' }}</div>
                </div>
                
                <div class="info-item">
                    <label class="info-label">Sent At</label>
                    <div class="info-value">{{ $smsLog->sent_at ? $smsLog->sent_at->format('M j, Y g:i:s A') : 'N/A' }}</div>
                </div>
                
                <div class="info-item">
                    <label class="info-label">Delivered At</label>
                    <div class="info-value">{{ $smsLog->delivered_at ? $smsLog->delivered_at->format('M j, Y g:i:s A') : 'N/A' }}</div>
                </div>
            </div>
            
            @if($smsLog->error_message)
            <div class="error-message">
                <strong>Error Details:</strong><br>
                {{ $smsLog->error_message }}
            </div>
            @endif
        </div>

        {{-- Delivery Timeline --}}
        <div class="sms-info-section">
            <h4 class="section-title">
                <i class="fas fa-route"></i>
                Delivery Timeline
            </h4>
            
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-icon sent">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="timeline-content">
                        <strong>Message Queued</strong>
                        <div class="timeline-time">{{ $smsLog->created_at->format('M j, Y g:i:s A') }}</div>
                    </div>
                </div>
                
                @if($smsLog->sent_at)
                <div class="timeline-item">
                    <div class="timeline-icon sent">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="timeline-content">
                        <strong>Sent to Provider ({{ strtoupper($smsLog->provider) }})</strong>
                        <div class="timeline-time">{{ $smsLog->sent_at->format('M j, Y g:i:s A') }}</div>
                    </div>
                </div>
                @endif
                
                @if($smsLog->delivered_at)
                <div class="timeline-item">
                    <div class="timeline-icon delivered">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="timeline-content">
                        <strong>Delivered to Recipient</strong>
                        <div class="timeline-time">{{ $smsLog->delivered_at->format('M j, Y g:i:s A') }}</div>
                    </div>
                </div>
                @endif
                
                @if($smsLog->status === 'failed')
                <div class="timeline-item">
                    <div class="timeline-icon failed">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="timeline-content">
                        <strong>Delivery Failed</strong>
                        <div class="timeline-time">{{ $smsLog->updated_at->format('M j, Y g:i:s A') }}</div>
                        @if($smsLog->error_message)
                            <div class="text-danger">{{ $smsLog->error_message }}</div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Future: Real-time status updates
    // Future: Resend functionality
    // Future: Status refresh button
});
</script>
@endsection
