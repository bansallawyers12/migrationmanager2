@extends('layouts.crm_client_detail')
@section('title', 'Sent Emails — Dashboard')

@section('styles')
<style>
    .stat-card { transition: box-shadow 0.2s; }
    .stat-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,0.10); }
    .top-senders-list { list-style: none; padding: 0; margin: 0; }
    .top-senders-list li { display: flex; align-items: center; justify-content: space-between;
                           padding: 0.5rem 0; border-bottom: 1px solid #f0f0f0; }
    .top-senders-list li:last-child { border-bottom: none; }
    .sender-bar { height: 6px; border-radius: 3px; background: #3498db; display: inline-block; min-width: 4px; }
    .recent-table th { white-space: nowrap; font-size: 0.82rem; }
    .truncate-cell { max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .coverage-notice { border-left: 4px solid #f39c12; }
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

                    {{-- Page header --}}
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="mb-0"><i class="fas fa-chart-bar"></i> Sent Emails — Dashboard</h4>
                        <a href="{{ route('adminconsole.features.sent-emails.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list"></i> Search All Emails
                        </a>
                    </div>

                    {{-- Coverage notice --}}
                    <div class="alert alert-warning coverage-notice mb-4" role="alert" style="font-size:0.875rem;">
                        <i class="fas fa-info-circle"></i>
                        Analytics cover CRM-logged outgoing emails only. System-generated emails (invoices, reminders, appointments, etc.) are not included.
                    </div>

                    {{-- Stat cards --}}
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                            <div class="card card-statistic-1 stat-card">
                                <div class="card-icon bg-primary">
                                    <i class="fas fa-paper-plane"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header"><h4>Sent Today</h4></div>
                                    <div class="card-body">{{ number_format($stats['totalToday']) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                            <div class="card card-statistic-1 stat-card">
                                <div class="card-icon bg-success">
                                    <i class="fas fa-calendar-week"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header"><h4>This Week</h4></div>
                                    <div class="card-body">{{ number_format($stats['totalWeek']) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                            <div class="card card-statistic-1 stat-card">
                                <div class="card-icon bg-info">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header"><h4>This Month</h4></div>
                                    <div class="card-body">{{ number_format($stats['totalMonth']) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                            <div class="card card-statistic-1 stat-card">
                                <div class="card-icon bg-warning">
                                    <i class="fas fa-paperclip"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header"><h4>With Attachments</h4></div>
                                    <div class="card-body">{{ number_format($stats['withAttachments']) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Top senders --}}
                        <div class="col-lg-5 col-md-12 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h4><i class="fas fa-users"></i> Top Senders This Month</h4>
                                </div>
                                <div class="card-body">
                                    @if($stats['topSenders']->isNotEmpty())
                                        @php
                                            $maxCount = $stats['topSenders']->max('send_count');
                                        @endphp
                                        <ul class="top-senders-list">
                                            @foreach($stats['topSenders'] as $sender)
                                            <li>
                                                <div class="d-flex flex-column" style="min-width:0;flex:1;">
                                                    <span style="font-weight:500;">{{ $sender['name'] }}</span>
                                                    <span class="sender-bar mt-1"
                                                          style="width:{{ $maxCount > 0 ? round(($sender['send_count'] / $maxCount) * 100) : 0 }}%;"></span>
                                                </div>
                                                <span class="badge badge-primary ml-3">{{ number_format($sender['send_count']) }}</span>
                                            </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted text-center mt-3">No emails sent this month yet.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Quick links / shortcuts --}}
                        <div class="col-lg-7 col-md-12 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h4><i class="fas fa-bolt"></i> Quick Searches</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6 mb-2">
                                            <a href="{{ route('adminconsole.features.sent-emails.index', ['filter'=>1,'date_from'=>now()->toDateString(),'date_to'=>now()->toDateString()]) }}"
                                               class="btn btn-outline-primary btn-block">
                                                <i class="fas fa-sun"></i> Today's Emails
                                            </a>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <a href="{{ route('adminconsole.features.sent-emails.index', ['filter'=>1,'has_attachments'=>'1']) }}"
                                               class="btn btn-outline-secondary btn-block">
                                                <i class="fas fa-paperclip"></i> With Attachments
                                            </a>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <a href="{{ route('adminconsole.features.sent-emails.index', ['filter'=>1,'type'=>'client']) }}"
                                               class="btn btn-outline-info btn-block">
                                                <i class="fas fa-user"></i> To Clients
                                            </a>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <a href="{{ route('adminconsole.features.sent-emails.index', ['filter'=>1,'type'=>'lead']) }}"
                                               class="btn btn-outline-warning btn-block">
                                                <i class="fas fa-user-plus"></i> To Leads
                                            </a>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <a href="{{ route('adminconsole.features.sent-emails.index', ['filter'=>1,'date_from'=>now()->startOfMonth()->toDateString(),'date_to'=>now()->toDateString()]) }}"
                                               class="btn btn-outline-success btn-block">
                                                <i class="fas fa-calendar-alt"></i> This Month
                                            </a>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <a href="{{ route('adminconsole.features.sent-emails.index') }}"
                                               class="btn btn-outline-dark btn-block">
                                                <i class="fas fa-search"></i> Full Search
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Recent 10 --}}
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-history"></i> Recent Sent Emails</h4>
                            <div class="card-header-action">
                                <a href="{{ route('adminconsole.features.sent-emails.index', ['filter'=>1]) }}" class="btn btn-sm btn-primary">
                                    View All
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if($recent->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 recent-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Sent By</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Subject</th>
                                            <th>Client</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recent as $row)
                                        <tr>
                                            <td style="white-space:nowrap;">
                                                <small>{{ \Carbon\Carbon::parse($row['created_at'])->format('d M Y') }}</small><br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($row['created_at'])->format('H:i') }}</small>
                                            </td>
                                            <td>{{ $row['sent_by'] }}</td>
                                            <td class="truncate-cell" title="{{ $row['from_mail'] }}">{{ $row['from_mail'] }}</td>
                                            <td class="truncate-cell" title="{{ $row['to_mail'] }}">{{ $row['to_mail'] }}</td>
                                            <td class="truncate-cell" title="{{ $row['subject'] }}">
                                                {{ $row['subject'] }}
                                                @if($row['attach_count'] > 0)
                                                    <span class="badge badge-secondary ml-1" style="font-size:0.7rem;">
                                                        <i class="fas fa-paperclip"></i> {{ $row['attach_count'] }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($row['client_id'])
                                                    <a href="/crm/clients/{{ $row['client_id'] }}" target="_blank">
                                                        {{ \Illuminate\Support\Str::limit($row['client_name'], 20) }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('adminconsole.features.sent-emails.show', $row['id']) }}"
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No sent emails recorded yet.
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
