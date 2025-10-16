@extends('layouts.admin_client_detail')
@section('title', 'SMS Dashboard')

@section('content')
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="server-error">
                @include('../Elements/flash-message')
            </div>
            <div class="custom-error-msg">
            </div>
            <div class="row">
                <div class="col-3 col-md-3 col-lg-3">
                    @include('../Elements/Admin/setting')
                </div>
                <div class="col-9 col-md-9 col-lg-9">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-sms"></i> SMS Management Dashboard</h4>
                            <p class="text-muted">Monitor SMS activity, manage templates, and send messages</p>
                        </div>
                        <div class="card-body">

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
