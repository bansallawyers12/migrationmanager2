@extends('layouts.crm_client_detail')
@section('title', 'TR Sheet - ' . ($config['title'] ?? 'Ongoing'))

@section('styles')
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
<link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
<style>
    .tr-sheet-page.listing-container { margin-top: 0 !important; padding-top: 0 !important; }
    .tr-sheet-page .art-sheet-sticky-header {
        position: sticky; top: 70px; z-index: 100;
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
        border-bottom: 1px solid #e2e8f0;
    }
    .tr-sheet-page .tr-tabs-row {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 20px; background: #f1f5f9; border-bottom: 1px solid #e2e8f0;
    }
    .tr-sheet-page .sheet-tabs {
        background: #e2e8f0; padding: 4px; border-radius: 8px;
        display: flex; gap: 2px; flex-wrap: wrap;
    }
    .tr-sheet-page .sheet-tab {
        padding: 10px 18px; color: #64748b; text-decoration: none;
        font-weight: 600; font-size: 14px; border-radius: 6px;
        white-space: nowrap; transition: all 0.2s ease;
    }
    .tr-sheet-page .sheet-tab:hover { color: #334155; background: #cbd5e1; text-decoration: none; }
    .tr-sheet-page .sheet-tab.active {
        color: #fff; background: linear-gradient(135deg, #475569 0%, #334155 100%);
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
    }
    .tr-sheet-page .art-sheet-title { font-size: 1.2rem; font-weight: 600; color: #1e293b; margin: 0; }
    .tr-sheet-page .comment-cell .sheet-comment-text { max-height: 3.6em; overflow: hidden; text-overflow: ellipsis; white-space: pre-wrap; word-break: break-word; }
    .tr-sheet-page .checklist-status-cell .checklist-status-select { min-width: 140px; max-width: 180px; }
    .tr-sheet-page .reminder-cell { min-width: 120px; }
    .tr-sheet-page .checklist-sent-cell { min-width: 120px; }
</style>
@endsection

@section('content')
<div class="listing-container tr-sheet-page art-sheet-page">
    <section class="listing-section">
        <div class="listing-section-body">
            <div class="card art-sheet-card">
                <div class="art-sheet-sticky-header">
                    <div class="art-sheet-top-bar">
                        <h4 class="art-sheet-title"><i class="fas fa-clipboard-list"></i> TR Sheet</h4>
                        <a href="{{ route('clients.index') }}" class="btn btn-theme btn-theme-sm">
                            <i class="fas fa-arrow-left"></i> Back to Clients
                        </a>
                    </div>
                    <div class="tr-tabs-row">
                        <span class="text-muted font-weight-bold" style="font-size: 13px;">Tabs:</span>
                        <div class="sheet-tabs">
                            @foreach(['ongoing' => 'Ongoing', 'lodged' => 'Lodged', 'checklist' => 'Checklist', 'discontinue' => 'Discontinue'] as $t => $label)
                                <a href="{{ route('clients.sheets.tr', array_merge(request()->except('tab'), ['tab' => $t])) }}"
                                   class="sheet-tab {{ $tab === $t ? 'active' : '' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="art-sheet-filter-bar">
                        <button type="button" class="btn btn-theme btn-theme-sm filter_btn">
                            <i class="fas fa-filter"></i> Filters
                            @if($activeFilterCount > 0)
                                <span class="active-filters-badge">{{ $activeFilterCount }}</span>
                            @endif
                        </button>
                        @if($activeFilterCount > 0)
                            <a href="{{ route('clients.sheets.tr', ['tab' => $tab, 'clear_filters' => 1]) }}" class="clear-filter-btn">
                                <i class="fas fa-undo"></i> Clear Filters
                            </a>
                        @endif
                        <label class="mb-0 mr-2" style="font-size: 13px;">Assignee:</label>
                        <select name="assignee" id="tr_assignee" class="form-control" style="max-width: 180px;">
                            <option value="all" {{ request('assignee') === 'all' ? 'selected' : '' }}>All</option>
                            @foreach($assignees as $a)
                                <option value="{{ $a->id }}" {{ request('assignee') == $a->id ? 'selected' : '' }}>
                                    {{ trim(($a->first_name ?? '') . ' ' . ($a->last_name ?? '')) ?: '—' }}
                                </option>
                            @endforeach
                        </select>
                        <label class="mb-0 ml-2 mr-1" style="font-size: 13px;">Show:</label>
                        <select name="per_page" id="tr_per_page" class="form-control per-page-select" style="max-width: 100px;">
                            @foreach([10, 25, 50, 100, 200] as $opt)
                                <option value="{{ $opt }}" {{ $perPage == $opt ? 'selected' : '' }}>{{ $opt }}/page</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter_panel {{ $activeFilterCount > 0 ? 'show' : '' }}">
                        <form action="{{ route('clients.sheets.tr') }}" method="get" id="trFilterForm">
                            <input type="hidden" name="tab" value="{{ $tab }}">
                            <input type="hidden" name="per_page" value="{{ $perPage }}">
                            <input type="hidden" name="assignee" value="{{ request('assignee') }}">
                            <div class="row">
                                <div class="col-md-2">
                                    <label>Branch</label>
                                    <select name="branch[]" class="form-control" multiple>
                                        @foreach($branches as $b)
                                            <option value="{{ $b->id }}" {{ in_array($b->id, (array)request('branch', [])) ? 'selected' : '' }}>{{ $b->office_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Current Stage</label>
                                    <select name="current_stage" class="form-control">
                                        <option value="">All stages</option>
                                        @foreach($currentStages as $val => $lbl)
                                            <option value="{{ $val }}" {{ request('current_stage') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Visa From</label>
                                    <input type="text" name="visa_expiry_from" class="form-control datepicker" placeholder="dd/mm/yyyy" value="{{ request('visa_expiry_from') }}" autocomplete="off">
                                </div>
                                <div class="col-md-2">
                                    <label>Visa To</label>
                                    <input type="text" name="visa_expiry_to" class="form-control datepicker" placeholder="dd/mm/yyyy" value="{{ request('visa_expiry_to') }}" autocomplete="off">
                                </div>
                                <div class="col-md-2">
                                    <label>Search</label>
                                    <input type="text" name="search" class="form-control" placeholder="Name, CRM Ref..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Apply</button>
                                </div>
                            </div>
                            <a href="{{ route('clients.sheets.tr', ['tab' => $tab]) }}" class="btn btn-secondary mt-2">Reset</a>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    @if($setupRequired ?? false)
                        <div class="alert alert-warning mb-3" role="alert">
                            <i class="fas fa-tools mr-1"></i>
                            <strong>Setup required:</strong> Run <code>php artisan migrate</code> to create <code>client_tr_references</code>, <code>tr_checklist_status</code>, and <code>tr_matter_reminders</code>. Until then, the 4 tabs and filters are visible for structure review but no data will load.
                        </div>
                    @endif
                    <div class="tr-sheet-scroll-hint px-3 pt-2 mb-2" style="font-size: 13px; color: #64748b;">
                        <i class="fas fa-arrows-alt-h"></i> Scroll horizontally to see all columns.
                    </div>
                    <div class="table-container">
                        <div class="scroll-indicator scroll-indicator-left"></div>
                        <div class="scroll-indicator scroll-indicator-right visible"></div>
                        <div class="table-responsive" id="tr-table-scroll">
                            <table class="table table-bordered table-hover art-table" id="tr-sheet-table">
                                <thead>
                                    <tr>
                                        <th>Matter / Course</th>
                                        @if($tab !== 'checklist')
                                        <th>CRM Ref</th>
                                        @endif
                                        <th>Client Name</th>
                                        <th>DOB</th>
                                        <th>Payment Received</th>
                                        <th>Institute</th>
                                        @if($tab !== 'checklist')
                                        <th>Branch</th>
                                        @endif
                                        <th>Assignee</th>
                                        <th>Visa Expiry</th>
                                        @if($tab !== 'checklist')
                                        <th>Current Stage</th>
                                        @endif
                                        <th>Comment</th>
                                        @if($tab === 'checklist')
                                        <th>Status</th>
                                        <th>Checklist sent</th>
                                        <th>Email reminder</th>
                                        <th>SMS reminder</th>
                                        <th>Phone reminder</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($rows->isEmpty())
                                        <tr>
                                            <td colspan="{{ $tab === 'checklist' ? 14 : 12 }}" class="text-center text-muted py-4">
                                                @if($setupRequired ?? false)
                                                    <i class="fas fa-info-circle"></i> Run migrations to enable data. Add a TR matter type (nick_name='tr' or title contains 'TR') and assign matters to clients.
                                                @else
                                                    <i class="fas fa-info-circle"></i> No TR records found for this tab. Add a TR matter type (nick_name='tr' or title contains 'TR') and assign matters to clients.
                                                @endif
                                            </td>
                                        </tr>
                                    @else
                                        @foreach($rows as $row)
                                            @php
                                                $encodedId = base64_encode(convert_uuencode($row->client_id));
                                                $detailUrl = route('clients.detail', ['client_id' => $encodedId, 'client_unique_matter_ref_no' => $row->client_unique_matter_no ?? '']);
                                                $matterId = $row->matter_internal_id ?? '';
                                                $checklistUrl = $detailUrl . ($matterId ? '?matterId=' . $matterId . '&open_checklist=1' : '');
                                                $emailReminderUrl = $detailUrl . ($matterId ? '?matterId=' . $matterId . '&open_email_reminder=1' : '');
                                                $smsReminderUrl = $detailUrl . ($matterId ? '?matterId=' . $matterId . '&open_sms_reminder=1' : '');
                                            @endphp
                                            <tr style="cursor: pointer;" onclick="window.location.href='{{ $detailUrl }}'">
                                                <td onclick="event.stopPropagation();"><a href="{{ $detailUrl }}" class="art-link">{{ $row->matter_title ?? $row->client_unique_matter_no ?? $row->other_reference ?? '—' }}</a></td>
                                                @if($tab !== 'checklist')
                                                <td onclick="event.stopPropagation();"><a href="{{ $detailUrl }}" class="art-link">{{ $row->crm_ref ?? '—' }}</a></td>
                                                @endif
                                                <td onclick="event.stopPropagation();"><a href="{{ $detailUrl }}" class="art-link">{{ trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')) ?: '—' }}</a></td>
                                                <td>{{ $row->dob ? \Carbon\Carbon::parse($row->dob)->format('d/m/Y') : '—' }}</td>
                                                <td>
                                                    @if($row->payment_display_note ?? null)
                                                        {{ $row->payment_display_note }}
                                                    @elseif(($row->total_payment ?? 0) > 0)
                                                        ${{ number_format((float)($row->total_payment ?? 0), 2) }}
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>{{ $row->institute_override ?? $row->department_reference ?? '—' }}</td>
                                                @if($tab !== 'checklist')
                                                <td>{{ $row->branch_name ?? '—' }}</td>
                                                @endif
                                                <td>{{ trim($row->assignee_name ?? '') ?: '—' }}</td>
                                                <td>{{ isset($row->visa_expiry) && $row->visa_expiry && $row->visa_expiry != '0000-00-00' ? \Carbon\Carbon::parse($row->visa_expiry)->format('d/m/Y') : '—' }}</td>
                                                @if($tab !== 'checklist')
                                                <td>{{ $row->application_stage ?? '—' }}</td>
                                                @endif
                                                <td class="art-comments-cell comment-cell" onclick="event.stopPropagation();" title="{{ $row->sheet_comment_text ?? '' }}">
                                                    <span class="sheet-comment-text">{{ Str::limit($row->sheet_comment_text ?? '—', 60) }}</span>
                                                </td>
                                                @if($tab === 'checklist')
                                                <td onclick="event.stopPropagation();" class="checklist-status-cell">
                                                    @php
                                                        $currentStatus = $row->tr_checklist_status ?? 'active';
                                                        $statusLabels = ['active' => 'Active', 'convert_to_client' => 'Convert to client', 'discontinue' => 'Discontinue', 'hold' => 'Hold'];
                                                    @endphp
                                                    <select class="form-control form-control-sm checklist-status-select" data-matter-id="{{ $matterId }}" title="Status">
                                                        @foreach($statusLabels as $val => $label)
                                                        <option value="{{ $val }}" {{ $currentStatus === $val ? 'selected' : '' }}>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td onclick="event.stopPropagation();" class="checklist-sent-cell">
                                                    @if(!empty($row->checklist_sent_at))
                                                        {{ \Carbon\Carbon::parse($row->checklist_sent_at)->format('d/m/Y') }}
                                                        <br><a href="{{ $checklistUrl }}" class="btn btn-sm btn-outline-secondary mt-1" onclick="event.stopPropagation();" title="Resend checklist">Resend checklist</a>
                                                    @else
                                                        Not sent
                                                        <br><a href="{{ $checklistUrl }}" class="btn btn-sm btn-outline-primary mt-1" onclick="event.stopPropagation();" title="Send checklist">Send checklist</a>
                                                    @endif
                                                </td>
                                                <td onclick="event.stopPropagation();" class="reminder-cell">
                                                    @if(!empty($row->email_reminder_latest ?? null))
                                                        {{ \Carbon\Carbon::parse($row->email_reminder_latest)->format('d/m/Y') }}@if(($row->email_reminder_count ?? 0) > 0) ({{ $row->email_reminder_count }})@endif
                                                    @else
                                                        —
                                                    @endif
                                                    <br><a href="{{ $emailReminderUrl }}" class="btn btn-sm btn-outline-secondary mt-1 checklist-reminder-link" onclick="event.stopPropagation();" title="Email reminder">Email reminder</a>
                                                </td>
                                                <td onclick="event.stopPropagation();" class="reminder-cell">
                                                    @if(!empty($row->sms_reminder_latest ?? null))
                                                        {{ \Carbon\Carbon::parse($row->sms_reminder_latest)->format('d/m/Y') }}@if(($row->sms_reminder_count ?? 0) > 0) ({{ $row->sms_reminder_count }})@endif
                                                    @else
                                                        —
                                                    @endif
                                                    <br><a href="{{ $smsReminderUrl }}" class="btn btn-sm btn-outline-secondary mt-1 checklist-reminder-link" onclick="event.stopPropagation();" title="SMS reminder">SMS reminder</a>
                                                </td>
                                                <td onclick="event.stopPropagation();" class="reminder-cell">
                                                    @if(!empty($row->phone_reminder_latest ?? null))
                                                        {{ \Carbon\Carbon::parse($row->phone_reminder_latest)->format('d/m/Y') }}@if(($row->phone_reminder_count ?? 0) > 0) ({{ $row->phone_reminder_count }})@endif
                                                    @else
                                                        —
                                                    @endif
                                                    <br><button type="button" class="btn btn-sm btn-outline-secondary mt-1 checklist-phone-reminder-btn" data-matter-id="{{ $matterId }}" onclick="event.stopPropagation();" title="Phone reminder">Phone reminder</button>
                                                </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($rows->hasPages())
                        <div class="card-footer">
                            {!! $rows->appends(array_merge(request()->except('page'), ['tab' => $tab]))->render() !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
<script>
jQuery(document).ready(function($) {
    var $scroll = $('#tr-table-scroll');
    var $left = $('.scroll-indicator-left');
    var $right = $('.scroll-indicator-right');
    function updateScroll() {
        if (!$scroll.length || !$scroll[0]) return;
        var sl = $scroll.scrollLeft();
        var sw = $scroll[0].scrollWidth;
        var cw = $scroll[0].clientWidth;
        $left.toggleClass('visible', sl > 10);
        $right.toggleClass('visible', sl < sw - cw - 10);
    }
    $scroll.on('scroll resize', updateScroll);
    setTimeout(updateScroll, 100);
    $scroll.on('wheel', function(e) {
        if (e.originalEvent.deltaY && !e.shiftKey && this.scrollWidth > this.clientWidth) {
            e.preventDefault();
            this.scrollLeft += e.originalEvent.deltaY;
        }
    });
    $('#tr_per_page').on('change', function() {
        var u = new URL(window.location.href);
        u.searchParams.set('per_page', $(this).val());
        u.searchParams.delete('page');
        window.location.href = u.toString();
    });
    $('#tr_assignee').on('change', function() {
        var u = new URL(window.location.href);
        u.searchParams.set('assignee', $(this).val());
        u.searchParams.delete('page');
        window.location.href = u.toString();
    });
    $('.filter_btn').on('click', function() {
        $('.filter_panel').toggleClass('show');
    });
    $('.datepicker').datepicker({ format: 'dd/mm/yyyy', autoclose: true, todayHighlight: true });
});
</script>
@endpush
