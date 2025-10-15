@extends('layouts.admin')
@section('title', 'SMS Dashboard')

@section('styles')
<style>
    .sms-dashboard {
        padding: 20px;
    }
    
    .sms-stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .sms-stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
    }
    
    .sms-stat-card:hover {
        transform: translateY(-5px);
    }
    
    .sms-stat-card.cellcast {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    }
    
    .sms-stat-card.twilio {
        background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
    }
    
    .sms-stat-card.failed {
        background: linear-gradient(135deg, #ff7979 0%, #d63031 100%);
    }
    
    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
        opacity: 0.9;
    }
    
    .stat-number {
        font-size: 2.2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .sms-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }
    
    .sms-action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px 20px;
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        color: #495057;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .sms-action-btn:hover {
        border-color: #667eea;
        color: #667eea;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .sms-action-btn i {
        margin-right: 10px;
        font-size: 1.2rem;
    }
    
    .sms-recent {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .sms-recent-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 20px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .sms-recent-header h3 {
        margin: 0;
        color: #495057;
        font-size: 1.2rem;
        font-weight: 600;
    }
    
    .coming-soon {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }
    
    .coming-soon i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #dee2e6;
    }
    
    .coming-soon h4 {
        margin-bottom: 10px;
        color: #495057;
    }
    
    .coming-soon p {
        margin: 0;
        font-size: 0.9rem;
    }
    
    .provider-status {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .provider-status.online {
        background-color: #d4edda;
        color: #155724;
    }
    
    .provider-status.offline {
        background-color: #f8d7da;
        color: #721c24;
    }
</style>
@endsection

@section('content')
<div class="sms-dashboard">
    <div class="main-content-header">
        <h2><i class="fas fa-sms"></i> SMS Management Dashboard</h2>
        <p class="text-muted">Monitor SMS activity, manage templates, and send messages</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="sms-stats-cards">
        <div class="sms-stat-card">
            <div class="stat-icon">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div class="stat-number">{{ $stats['total_today'] ?? 0 }}</div>
            <div class="stat-label">Total Sent Today</div>
        </div>

        <div class="sms-stat-card cellcast">
            <div class="stat-icon">
                <i class="fas fa-flag"></i>
            </div>
            <div class="stat-number">{{ $stats['cellcast_today'] ?? 0 }}</div>
            <div class="stat-label">Via Cellcast (AU)</div>
        </div>

        <div class="sms-stat-card twilio">
            <div class="stat-icon">
                <i class="fas fa-globe"></i>
            </div>
            <div class="stat-number">{{ $stats['twilio_today'] ?? 0 }}</div>
            <div class="stat-label">Via Twilio (Intl)</div>
        </div>

        <div class="sms-stat-card failed">
            <div class="stat-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-number">{{ $stats['failed_today'] ?? 0 }}</div>
            <div class="stat-label">Failed Messages</div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="sms-actions">
        <a href="{{ route('admin.sms.send.create') }}" class="sms-action-btn">
            <i class="fas fa-plus"></i>
            Send SMS
        </a>
        
        <a href="{{ route('admin.sms.history') }}" class="sms-action-btn">
            <i class="fas fa-history"></i>
            View History
        </a>
        
        <a href="{{ route('admin.sms.templates.index') }}" class="sms-action-btn">
            <i class="fas fa-file-alt"></i>
            Manage Templates
        </a>
        
        <a href="{{ route('admin.sms.statistics') }}" class="sms-action-btn">
            <i class="fas fa-chart-bar"></i>
            View Statistics
        </a>
    </div>

    {{-- Recent Activity --}}
    <div class="sms-recent">
        <div class="sms-recent-header">
            <h3>
                <i class="fas fa-clock"></i> 
                Recent SMS Activity
                <small class="provider-status online float-right">
                    <i class="fas fa-circle"></i> Services Online
                </small>
            </h3>
        </div>
        
        @if($recentSms->count() > 0)
        <div style="padding: 20px;">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>To</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Provider</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentSms as $sms)
                    <tr>
                        <td>
                            <small>{{ $sms->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <span style="font-family: monospace;">{{ $sms->formatted_phone ?? $sms->recipient_phone }}</span>
                        </td>
                        <td>
                            <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $sms->message_content }}
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $sms->status === 'sent' ? 'success' : ($sms->status === 'failed' ? 'danger' : 'warning') }}">
                                {{ ucfirst($sms->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $sms->provider === 'cellcast' ? 'danger' : 'info' }}">
                                {{ strtoupper($sms->provider) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="coming-soon">
            <i class="fas fa-inbox"></i>
            <h4>No SMS Activity Yet</h4>
            <p>Send your first SMS message to see activity here!</p>
            <br>
            <a href="{{ route('admin.sms.send.create') }}" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Send First SMS
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Future: Real-time updates via WebSocket
    // Future: SMS statistics API integration
    // Future: Provider health monitoring
    
    $(document).ready(function() {
        console.log('SMS Dashboard initialized - Sprint 4 will add live data');
    });
</script>
@endsection
