@extends('layouts.crm_client_detail')
@section('title', 'ART Submission and Hearing Files')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-pagination.css') }}">
<link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
<style>
    .sheet-tabs {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 0;
        margin: 0 -20px 20px -20px;
        display: flex;
        gap: 0;
        border-radius: 8px 8px 0 0;
    }
    .sheet-tab {
        flex: 1;
        padding: 15px 20px;
        text-align: center;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s ease;
        border-bottom: 3px solid transparent;
    }
    .sheet-tab:hover { color: #fff; background: rgba(255,255,255,0.1); text-decoration: none; }
    .sheet-tab.active { color: #fff; background: rgba(255,255,255,0.15); border-bottom-color: #fff; }
    .sheet-tab i { margin-right: 8px; }
    .art-table { font-size: 13px; }
    .art-table th {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        font-weight: 600;
        white-space: nowrap;
        padding: 12px 8px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #495057;
        border-bottom: 2px solid #667eea;
    }
    .art-table td { padding: 10px 8px; vertical-align: middle; }
    .art-table tbody tr:hover { background-color: #f8f9ff; }
    .art-link { color: #667eea; font-weight: 600; text-decoration: none; }
    .art-link:hover { color: #764ba2; text-decoration: underline; }
    .art-comments-cell { max-width: 280px; font-size: 12px; line-height: 1.4; word-wrap: break-word; white-space: normal; }
    .filter_panel { display: none; background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
    .filter_panel.show { display: block; }
    .active-filters-badge { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; margin-left: 10px; }
    .clear-filter-btn { background: #6c757d; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-size: 14px; cursor: pointer; }
    .clear-filter-btn:hover { background: #5a6268; }
    .per-page-select { width: auto; display: inline-block; margin-left: 10px; }
    .sortable { cursor: pointer; user-select: none; position: relative; padding-right: 20px !important; }
    .sortable:hover { background: rgba(102, 126, 234, 0.1); }
    .sortable::after { content: '\f0dc'; font-family: 'Font Awesome 5 Free'; font-weight: 900; position: absolute; right: 8px; opacity: 0.3; }
    .sortable.asc::after { content: '\f0de'; opacity: 1; color: #667eea; }
    .sortable.desc::after { content: '\f0dd'; opacity: 1; color: #667eea; }
    .table-responsive { position: relative; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-container { position: relative; }
    .scroll-indicator { position: absolute; top: 0; bottom: 20px; width: 40px; pointer-events: none; z-index: 10; transition: opacity 0.3s; }
    .scroll-indicator-left { left: 0; background: linear-gradient(to right, rgba(255,255,255,0.95), transparent); opacity: 0; }
    .scroll-indicator-right { right: 0; background: linear-gradient(to left, rgba(255,255,255,0.95), transparent); }
    .scroll-indicator-left.visible, .scroll-indicator-right.visible { opacity: 1; }
    .scroll-hint { text-align: center; padding: 10px; background: #e7f3ff; border-radius: 5px; margin-bottom: 10px; font-size: 13px; color: #0c5460; }
</style>
@endsection

@section('content')
<div class="listing-container">
    <section class="listing-section" style="padding-top: 40px;">
        <div class="listing-section-body">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h4><i class="fas fa-gavel"></i> ART Submission and Hearing Files</h4>
                    <div class="card-header-actions">
                        <a href="{{ route('clients.index') }}" class="btn btn-theme btn-theme-sm" title="Back to Clients">
                            <i class="fas fa-arrow-left"></i> Back to Clients
                        </a>
                    </div>
                </div>

                <div class="sheet-tabs">
                    <a href="{{ route('clients.sheets.art') }}" class="sheet-tab active">
                        <i class="fas fa-list"></i> List
                    </a>
                    <a href="{{ route('clients.sheets.art.insights') }}" class="sheet-tab">
                        <i class="fas fa-chart-bar"></i> Insights
                    </a>
                </div>

                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <button type="button" class="btn btn-theme btn-theme-sm filter_btn">
                                <i class="fas fa-filter"></i> Filters
                                @if($activeFilterCount > 0)
                                    <span class="active-filters-badge">{{ $activeFilterCount }}</span>
                                @endif
                            </button>
                            @if($activeFilterCount > 0)
                                <a href="{{ route('clients.sheets.art') }}" class="clear-filter-btn ml-2">
                                    <i class="fas fa-undo"></i> Clear Filters
                                </a>
                            @endif
                        </div>
                        <div>
                            <label for="per_page" style="display: inline; margin-right: 5px;">Show:</label>
                            <select name="per_page" id="per_page" class="form-control per-page-select">
                                @foreach([10, 25, 50, 100, 200] as $option)
                                    <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>{{ $option }} / page</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="filter_panel {{ $activeFilterCount > 0 ? 'show' : '' }}">
                        <form action="{{ route('clients.sheets.art') }}" method="get" id="filterForm">
                            <input type="hidden" name="per_page" value="{{ $perPage }}">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="search">Search</label>
                                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name, CRM Ref, Other Ref" id="search">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="">All Statuses</option>
                                            @foreach($statusOptions as $value => $label)
                                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="agent">Agent</label>
                                        <select name="agent" id="agent" class="form-control">
                                            <option value="">All Agents</option>
                                            @foreach($agents as $agent)
                                                <option value="{{ $agent->id }}" {{ request('agent') == $agent->id ? 'selected' : '' }}>{{ $agent->first_name }} {{ $agent->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="from_date">From Date</label>
                                        <input type="text" name="from_date" value="{{ request('from_date') }}" class="form-control datepicker" placeholder="dd/mm/yyyy" id="from_date" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="to_date">To Date</label>
                                        <input type="text" name="to_date" value="{{ request('to_date') }}" class="form-control datepicker" placeholder="dd/mm/yyyy" id="to_date" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">Apply</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <a href="{{ route('clients.sheets.art') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="scroll-hint">
                        <i class="fas fa-arrows-alt-h"></i> Scroll horizontally to see all columns.
                    </div>

                    <div class="table-container">
                        <div class="scroll-indicator scroll-indicator-left"></div>
                        <div class="scroll-indicator scroll-indicator-right visible"></div>
                        <div class="table-responsive" id="table-scroll-container">
                            <table class="table table-bordered table-hover art-table" id="art-sheet-table">
                                <thead>
                                    <tr>
                                        <th class="sortable {{ request('sort') == 'crm_ref' ? (request('direction') == 'asc' ? 'asc' : 'desc') : '' }}" data-sort="crm_ref">CRM Ref</th>
                                        <th class="sortable {{ request('sort') == 'other_reference' ? (request('direction') == 'asc' ? 'asc' : 'desc') : '' }}" data-sort="other_reference">Other Reference</th>
                                        <th class="sortable {{ request('sort') == 'client_name' ? (request('direction') == 'asc' ? 'asc' : 'desc') : '' }}" data-sort="client_name">Name</th>
                                        <th>Total Payment</th>
                                        <th>Pending Payment</th>
                                        <th class="sortable {{ request('sort') == 'submission_date' ? (request('direction') == 'asc' ? 'asc' : 'desc') : '' }}" data-sort="submission_date">Submission Last Date</th>
                                        <th class="sortable {{ request('sort') == 'agent_name' ? (request('direction') == 'asc' ? 'asc' : 'desc') : '' }}" data-sort="agent_name">Agent Name</th>
                                        <th class="sortable {{ request('sort') == 'status' ? (request('direction') == 'asc' ? 'asc' : 'desc') : '' }}" data-sort="status">Status of the File</th>
                                        <th>Time</th>
                                        <th>Member Name</th>
                                        <th>Outcome</th>
                                        <th>Comments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($rows->isEmpty())
                                        <tr>
                                            <td colspan="13" class="text-center text-muted py-4">
                                                <i class="fas fa-info-circle"></i> No ART records found. Add an ART matter type and assign matters to clients to see data here.
                                            </td>
                                        </tr>
                                    @else
                                        @foreach($rows as $row)
                                            @php
                                                $encodedClientId = base64_encode(convert_uuencode($row->client_id));
                                                $clientDetailUrl = route('clients.detail', [
                                                    'client_id' => $encodedClientId,
                                                    'client_unique_matter_ref_no' => $row->matter_id ?? '',
                                                ]);
                                                $otherRef = $row->other_reference ?? $row->department_reference ?? '—';
                                            @endphp
                                            <tr>
                                                <td><a href="{{ $clientDetailUrl }}" class="art-link">{{ $row->crm_ref ?? '—' }}</a></td>
                                                <td>{{ $otherRef }}</td>
                                                <td><a href="{{ $clientDetailUrl }}" class="art-link">{{ trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')) ?: '—' }}</a></td>
                                                <td>${{ $row->total_payment ?? '0.00' }}</td>
                                                <td>${{ $row->pending_payment ?? '0.00' }}</td>
                                                <td>{{ $row->submission_last_date ? \Carbon\Carbon::parse($row->submission_last_date)->format('d/m/Y') : '—' }}</td>
                                                <td>{{ trim($row->agent_name ?? '') ?: '—' }}</td>
                                                <td>
                                                    @if($row->status_of_file)
                                                        @php
                                                            $statusLabels = [
                                                                'submission_pending' => 'Submission Pending',
                                                                'submission_done' => 'Submission Done',
                                                                'hearing_invitation_sent' => 'Hearing Invitation Sent',
                                                                'waiting_for_hearing' => 'Waiting for Hearing',
                                                                'hearing' => 'Hearing',
                                                                'decided' => 'Decided',
                                                                'withdrawn' => 'Withdrawn',
                                                            ];
                                                            $label = $statusLabels[$row->status_of_file] ?? ucfirst(str_replace('_', ' ', $row->status_of_file));
                                                        @endphp
                                                        <span class="badge badge-{{ $row->status_of_file === 'submission_done' ? 'success' : ($row->status_of_file === 'hearing' ? 'primary' : 'secondary') }}">{{ $label }}</span>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>{{ $row->hearing_time ?? '—' }}</td>
                                                <td>{{ $row->member_name ?? '—' }}</td>
                                                <td>{{ $row->outcome ?? '—' }}</td>
                                                <td class="art-comments-cell" title="{{ $row->comments ?? '' }}">{{ Str::limit($row->comments ?? '—', 80) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($rows->hasPages())
                        <div class="card-footer">
                            {!! $rows->appends(Request::except('page'))->render() !!}
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
    var $scrollContainer = $('#table-scroll-container');
    var $leftIndicator = $('.scroll-indicator-left');
    var $rightIndicator = $('.scroll-indicator-right');
    function updateScrollIndicators() {
        var scrollLeft = $scrollContainer.scrollLeft();
        var scrollWidth = $scrollContainer[0].scrollWidth;
        var clientWidth = $scrollContainer[0].clientWidth;
        var maxScroll = scrollWidth - clientWidth;
        $leftIndicator.toggleClass('visible', scrollLeft > 10);
        $rightIndicator.toggleClass('visible', scrollLeft < maxScroll - 10);
    }
    $scrollContainer.on('scroll', updateScrollIndicators);
    $(window).on('resize', updateScrollIndicators);
    setTimeout(updateScrollIndicators, 100);
    $scrollContainer.on('wheel', function(e) {
        if (e.originalEvent.deltaY !== 0 && !e.shiftKey) {
            e.preventDefault();
            this.scrollLeft += e.originalEvent.deltaY;
        }
    });
    $('#per_page').on('change', function() {
        var url = new URL(window.location.href);
        url.searchParams.set('per_page', $(this).val());
        url.searchParams.delete('page');
        window.location.href = url.toString();
    });
    $('.filter_btn').on('click', function() {
        $('.filter_panel').toggleClass('show');
    });
    $('.datepicker').datepicker({ format: 'dd/mm/yyyy', autoclose: true, todayHighlight: true });
    $('.sortable').on('click', function() {
        var sortField = $(this).data('sort');
        var currentSort = '{{ request("sort") }}';
        var currentDirection = '{{ request("direction", "desc") }}';
        var newDirection = (currentSort === sortField && currentDirection === 'desc') ? 'asc' : 'desc';
        var url = new URL(window.location.href);
        url.searchParams.set('sort', sortField);
        url.searchParams.set('direction', newDirection);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    });
});
</script>
@endpush
