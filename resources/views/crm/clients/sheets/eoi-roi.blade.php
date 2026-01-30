@extends('layouts.crm_client_detail')
@section('title', 'EOI/ROI Sheet')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-pagination.css') }}">
<link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
<style>
    /* Sheet tabs styling */
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
        position: relative;
    }
    
    .sheet-tab:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.1);
        text-decoration: none;
    }
    
    .sheet-tab.active {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.15);
        border-bottom-color: #ffffff;
    }
    
    .sheet-tab i {
        margin-right: 8px;
    }

    /* Table styling */
    .eoi-roi-table {
        font-size: 13px;
    }
    
    .eoi-roi-table th {
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

    /* First column (Matter ID) – wider so header and IDs are fully visible */
    .eoi-roi-table th:first-child,
    .eoi-roi-table td:first-child {
        min-width: 220px;
    }
    
    .eoi-roi-table td {
        padding: 10px 8px;
        vertical-align: middle;
    }
    
    .eoi-roi-table tbody tr:hover {
        background-color: #f8f9ff;
    }
    
    .eoi-link {
        color: #667eea;
        font-weight: 600;
        text-decoration: none;
    }
    
.eoi-link:hover {
    color: #764ba2;
    text-decoration: underline;
}

/* Comments cell – warnings text */
.eoi-comments-cell {
    max-width: 280px;
    font-size: 12px;
    line-height: 1.4;
    word-wrap: break-word;
    white-space: normal;
}

/* Filters panel */
    .filter_panel {
        display: none;
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .filter_panel.show {
        display: block;
    }
    
    .active-filters-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 10px;
    }
    
    .clear-filter-btn {
        background: #6c757d;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .clear-filter-btn:hover {
        background: #5a6268;
    }

    /* Per page select */
    .per-page-select {
        width: auto;
        display: inline-block;
        margin-left: 10px;
    }
    
    /* Sort icons */
    .sortable {
        cursor: pointer;
        user-select: none;
        position: relative;
        padding-right: 20px !important;
    }
    
    .sortable:hover {
        background: rgba(102, 126, 234, 0.1);
    }
    
    .sortable::after {
        content: '\f0dc';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: 8px;
        opacity: 0.3;
    }
    
    .sortable.asc::after {
        content: '\f0de';
        opacity: 1;
        color: #667eea;
    }
    
    .sortable.desc::after {
        content: '\f0dd';
        opacity: 1;
        color: #667eea;
    }

    /* Action buttons */
    .btn-group-vertical .btn {
        margin-bottom: 5px;
    }

    .verify-btn, .send-email-btn, .view-notes-btn {
        white-space: nowrap;
    }

    /* Improved horizontal scroll */
    .table-responsive {
        position: relative;
        overflow-x: auto;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
    }

    /* Custom scrollbar styling */
    .table-responsive::-webkit-scrollbar {
        height: 12px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }

    /* Scroll shadows to indicate more content */
    .table-container {
        position: relative;
    }

    .scroll-indicator {
        position: absolute;
        top: 0;
        bottom: 20px;
        width: 40px;
        pointer-events: none;
        z-index: 10;
        transition: opacity 0.3s ease;
    }

    .scroll-indicator-left {
        left: 0;
        background: linear-gradient(to right, rgba(255,255,255,0.95), transparent);
        opacity: 0;
    }

    .scroll-indicator-right {
        right: 0;
        background: linear-gradient(to left, rgba(255,255,255,0.95), transparent);
    }

    .scroll-indicator-left.visible,
    .scroll-indicator-right.visible {
        opacity: 1;
    }

    /* Scroll hint */
    .scroll-hint {
        text-align: center;
        padding: 10px;
        background: #e7f3ff;
        border-radius: 5px;
        margin-bottom: 10px;
        font-size: 13px;
        color: #0c5460;
    }

    .scroll-hint i {
        animation: slideHint 1.5s ease-in-out infinite;
    }

    @keyframes slideHint {
        0%, 100% { transform: translateX(-5px); }
        50% { transform: translateX(5px); }
    }
</style>
@endsection

@section('content')
<div class="listing-container">
    <section class="listing-section" style="padding-top: 40px;">
        <div class="listing-section-body">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h4><i class="fas fa-passport"></i> EOI/ROI Sheet</h4>
                    <div class="card-header-actions">
                        <a href="{{ route('clients.index') }}" class="btn btn-theme btn-theme-sm" title="Back to Clients">
                            <i class="fas fa-arrow-left"></i> Back to Clients
                        </a>
                    </div>
                </div>

                {{-- Tabs --}}
                <div class="sheet-tabs">
                    <a href="{{ route('clients.sheets.eoi-roi') }}" class="sheet-tab active">
                        <i class="fas fa-list"></i> List
                    </a>
                    <a href="{{ route('clients.sheets.eoi-roi.insights') }}" class="sheet-tab">
                        <i class="fas fa-chart-bar"></i> Insights
                    </a>
                </div>

                <div class="card-body">
                    {{-- Filter section --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <button type="button" class="btn btn-theme btn-theme-sm filter_btn">
                                <i class="fas fa-filter"></i> Filters
                                @if($activeFilterCount > 0)
                                    <span class="active-filters-badge">{{ $activeFilterCount }}</span>
                                @endif
                            </button>
                            @if($activeFilterCount > 0)
                                <a href="{{ route('clients.sheets.eoi-roi') }}" class="clear-filter-btn">
                                    <i class="fas fa-undo"></i> Clear Filters
                                </a>
                            @endif
                        </div>
                        <div>
                            <label for="per_page" style="display: inline; margin-right: 5px;">Show:</label>
                            <select name="per_page" id="per_page" class="form-control per-page-select">
                                @foreach([10, 25, 50, 100, 200] as $option)
                                    <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>
                                        {{ $option }} / page
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Filters panel --}}
                    <div class="filter_panel {{ $activeFilterCount > 0 ? 'show' : '' }}">
                        <form action="{{ route('clients.sheets.eoi-roi') }}" method="get" id="filterForm">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="search">Search</label>
                                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Client name or EOI number" id="search">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="occupation">Occupation</label>
                                        <input type="text" name="occupation" value="{{ request('occupation') }}" class="form-control" placeholder="Nominated occupation" id="occupation">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="eoi_status">EOI Status</label>
                                        <select name="eoi_status" id="eoi_status" class="form-control">
                                            <option value="">All Statuses</option>
                                            <option value="draft" {{ request('eoi_status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="submitted" {{ request('eoi_status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                            <option value="invited" {{ request('eoi_status') == 'invited' ? 'selected' : '' }}>Invited</option>
                                            <option value="nominated" {{ request('eoi_status') == 'nominated' ? 'selected' : '' }}>Nominated</option>
                                            <option value="rejected" {{ request('eoi_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            <option value="withdrawn" {{ request('eoi_status') == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
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
                                        <label>Subclass</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="subclass[]" value="189" id="subclass_189" {{ is_array(request('subclass')) && in_array('189', request('subclass')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="subclass_189">189</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="subclass[]" value="190" id="subclass_190" {{ is_array(request('subclass')) && in_array('190', request('subclass')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="subclass_190">190</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="subclass[]" value="491" id="subclass_491" {{ is_array(request('subclass')) && in_array('491', request('subclass')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="subclass_491">491</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>State</label>
                                        <div class="d-flex gap-3 flex-wrap">
                                            @foreach(['ACT', 'NSW', 'NT', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'FED'] as $state)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="state[]" value="{{ $state }}" id="state_{{ $state }}" {{ is_array(request('state')) && in_array($state, request('state')) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="state_{{ $state }}">{{ $state }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary mr-2">Apply Filters</button>
                                    <a href="{{ route('clients.sheets.eoi-roi') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Scroll hint --}}
                    <div class="scroll-hint">
                        <i class="fas fa-arrows-alt-h"></i> Scroll horizontally to see all columns.
                    </div>

                    {{-- Table --}}
                    <div class="table-container">
                        <div class="scroll-indicator scroll-indicator-left"></div>
                        <div class="scroll-indicator scroll-indicator-right visible"></div>
                        <div class="table-responsive" id="table-scroll-container">
                            <table class="table table-bordered table-hover eoi-roi-table" id="eoi-roi-sheet-table">
                            <thead>
                                <tr>
                                    <th class="sortable {{ request('sort') == 'matter_id' ? (request('direction') == 'asc' ? 'asc' : 'desc') : '' }}" data-sort="matter_id">Matter ID</th>
                                    <th class="sortable {{ request('sort') == 'eoi_number' ? (request('direction') == 'asc' ? 'asc' : 'desc') : '' }}" data-sort="eoi_number">EOI ID</th>
                                    <th class="sortable {{ request('sort') == 'client_name' ? (request('direction') == 'asc' ? 'asc' : 'desc') : '' }}" data-sort="client_name">Client Name</th>
                                    <th class="sortable {{ request('sort') == 'occupation' ? (request('direction') == 'asc' ? 'asc' : 'desc') : '' }}" data-sort="occupation">Nominated Occupation</th>
                                    <th>Current Job</th>
                                    <th class="sortable {{ request('sort') == 'individual_points' ? (request('direction') == 'asc' ? 'asc' : 'desc') : '' }}" data-sort="individual_points">Individual Points</th>
                                    <th class="sortable {{ request('sort') == 'marital_status' ? (request('direction') == 'asc' ? 'asc' : 'desc') : '' }}" data-sort="marital_status">Marital Status</th>
                                    <th>Partner Points</th>
                                    <th>State</th>
                                    <th>ROI Status</th>
                                    <th>Comments</th>
                                    <th class="sortable {{ request('sort') == 'submission_date' ? (request('direction') == 'asc' ? 'asc' : 'desc') : '' }}" data-sort="submission_date">Last EOI/ROI Sent</th>
                                    <th>Client Last Confirmation</th>
                                    <th>Confirmation Date</th>
                                    <th>Checked By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($rows->isEmpty())
                                    <tr>
                                        <td colspan="16" class="text-center text-muted py-4">
                                            <i class="fas fa-info-circle"></i> No EOI/ROI records found matching your criteria.
                                        </td>
                                    </tr>
                                @else
                                    @foreach($rows as $row)
                                        @php
                                            $encodedClientId = base64_encode(convert_uuencode($row->client_id));
                                            $eoiPageUrl = route('clients.detail', [
                                                'client_id' => $encodedClientId,
                                                'client_unique_matter_ref_no' => $row->matter_id,
                                                'tab' => 'eoiroi'
                                            ]);
                                            $subclasses = json_decode($row->eoi_subclasses, true) ?? [];
                                            // eoi_states: may be JSON string (from query) or already array; fallback to legacy EOI_state
                                            $states = is_array($row->eoi_states ?? null) ? $row->eoi_states : (json_decode($row->eoi_states ?? '[]', true) ?: []);
                                            if (empty($states) && !empty($row->EOI_state ?? null)) {
                                                $states = [ $row->EOI_state ];
                                            }
                                            // Fetch full EOI record for confirmation workflow data
                                            $eoiRecord = \App\Models\ClientEoiReference::find($row->eoi_id);
                                        @endphp
                                        <tr>
                                            <td><a href="{{ $eoiPageUrl }}" class="eoi-link">{{ $row->matter_id ?? '—' }}</a></td>
                                            <td><a href="{{ $eoiPageUrl }}" class="eoi-link">{{ $row->EOI_number ?? '—' }}</a></td>
                                            <td><a href="{{ $eoiPageUrl }}" class="eoi-link">{{ $row->first_name }} {{ $row->last_name }}</a></td>
                                            <td>{{ $row->EOI_occupation ?? '—' }}</td>
                                            <td class="text-muted">—</td>
                                            <td>{{ $row->individual_points ?? '—' }}</td>
                                            <td>{{ $row->marital_status ?? '—' }}</td>
                                            <td>{{ $row->partner_points ?? '—' }}</td>
                                            <td>{{ !empty($states) ? implode(', ', $states) : '—' }}</td>
                                            <td>
                                                @if($row->EOI_ROI)
                                                    {{ $row->EOI_ROI }}
                                                @endif
                                                @if($row->eoi_status)
                                                    <span class="badge badge-{{ $row->eoi_status == 'invited' ? 'success' : ($row->eoi_status == 'submitted' ? 'primary' : 'secondary') }}">
                                                        {{ ucfirst($row->eoi_status) }}
                                                    </span>
                                                @endif
                                                @if(!$row->EOI_ROI && !$row->eoi_status)
                                                    —
                                                @endif
                                            </td>
                                            <td class="eoi-comments-cell" title="{{ $row->warnings_text ?? '' }}">
                                                @if(!empty($row->warnings_text))
                                                    <span class="text-muted small">{{ $row->warnings_text }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($eoiRecord && $eoiRecord->confirmation_email_sent_at)
                                                    {{ $eoiRecord->confirmation_email_sent_at->format('d/m/Y H:i') }}
                                                    <br><small class="text-muted">Email sent</small>
                                                @elseif($row->EOI_submission_date)
                                                    {{ \Carbon\Carbon::parse($row->EOI_submission_date)->format('d/m/Y') }}
                                                    <br><small class="text-muted">EOI submitted</small>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($eoiRecord && $eoiRecord->client_last_confirmation)
                                                    <span class="client-confirmation-cell" data-eoi-id="{{ $row->eoi_id }}">
                                                        @if($eoiRecord->client_confirmation_status === 'confirmed')
                                                            <span class="badge badge-success" title="Confirmed on {{ $eoiRecord->client_last_confirmation->format('d/m/Y H:i') }}">
                                                                <i class="fas fa-check-circle"></i> Confirmed
                                                            </span>
                                                            <br><small class="text-muted">{{ $eoiRecord->client_last_confirmation->format('d/m/Y') }}</small>
                                                        @elseif($eoiRecord->client_confirmation_status === 'amendment_requested')
                                                            <span class="badge badge-warning" title="{{ $eoiRecord->client_confirmation_notes }}">
                                                                <i class="fas fa-edit"></i> Amendment
                                                            </span>
                                                            <br><small class="text-muted">{{ $eoiRecord->client_last_confirmation->format('d/m/Y') }}</small>
                                                        @elseif($eoiRecord->client_confirmation_status === 'pending' && $eoiRecord->confirmation_email_sent_at)
                                                            <span class="badge badge-secondary">
                                                                <i class="fas fa-clock"></i> Pending
                                                            </span>
                                                            <br><small class="text-muted">Sent {{ $eoiRecord->confirmation_email_sent_at->format('d/m/Y') }}</small>
                                                        @else
                                                            —
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="confirmation-date-cell" data-eoi-id="{{ $row->eoi_id }}">
                                                @if($eoiRecord && $eoiRecord->confirmation_date)
                                                    {{ $eoiRecord->confirmation_date->format('d/m/Y H:i') }}
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="checked-by-cell" data-eoi-id="{{ $row->eoi_id }}">
                                                @if($eoiRecord && $eoiRecord->verifier)
                                                    {{ $eoiRecord->verifier->first_name }} {{ $eoiRecord->verifier->last_name }}
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group-vertical" role="group">
                                                    @if($eoiRecord && !$eoiRecord->staff_verified)
                                                        <button type="button" class="btn btn-sm btn-success verify-btn" 
                                                                data-eoi-id="{{ $row->eoi_id }}"
                                                                title="Verify EOI details">
                                                            <i class="fas fa-check"></i> Verify
                                                        </button>
                                                    @elseif($eoiRecord && $eoiRecord->staff_verified && !$eoiRecord->confirmation_email_sent_at)
                                                        <button type="button" class="btn btn-sm btn-primary send-email-btn" 
                                                                data-eoi-id="{{ $row->eoi_id }}"
                                                                title="Send confirmation email to client">
                                                            <i class="fas fa-envelope"></i> Send Email
                                                        </button>
                                                    @elseif($eoiRecord && $eoiRecord->confirmation_email_sent_at && $eoiRecord->client_confirmation_status === 'pending')
                                                        <span class="badge badge-info">
                                                            <i class="fas fa-clock"></i> Awaiting Client
                                                        </span>
                                                    @elseif($eoiRecord && $eoiRecord->client_confirmation_status === 'confirmed')
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check-circle"></i> Completed
                                                        </span>
                                                    @elseif($eoiRecord && $eoiRecord->client_confirmation_status === 'amendment_requested')
                                                        <button type="button" class="btn btn-sm btn-warning view-notes-btn" 
                                                                data-eoi-id="{{ $row->eoi_id }}"
                                                                data-notes="{{ $eoiRecord->client_confirmation_notes }}"
                                                                title="View amendment notes">
                                                            <i class="fas fa-eye"></i> View Notes
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        </div>
                    </div>

                    {{-- Pagination --}}
                    @if($rows->hasPages())
                        <div class="card-footer">
                            {!! $rows->appends(\Request::except('page'))->render() !!}
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
jQuery(document).ready(function($) {
    // Horizontal scroll indicators
    var $scrollContainer = $('#table-scroll-container');
    var $leftIndicator = $('.scroll-indicator-left');
    var $rightIndicator = $('.scroll-indicator-right');

    function updateScrollIndicators() {
        var scrollLeft = $scrollContainer.scrollLeft();
        var scrollWidth = $scrollContainer[0].scrollWidth;
        var clientWidth = $scrollContainer[0].clientWidth;
        var maxScroll = scrollWidth - clientWidth;

        // Show/hide left indicator
        if (scrollLeft > 10) {
            $leftIndicator.addClass('visible');
        } else {
            $leftIndicator.removeClass('visible');
        }

        // Show/hide right indicator
        if (scrollLeft < maxScroll - 10) {
            $rightIndicator.addClass('visible');
        } else {
            $rightIndicator.removeClass('visible');
        }
    }

    // Update on scroll
    $scrollContainer.on('scroll', updateScrollIndicators);
    
    // Update on window resize
    $(window).on('resize', updateScrollIndicators);
    
    // Initial check
    setTimeout(updateScrollIndicators, 100);

    // Smooth scroll with mouse wheel horizontal
    $scrollContainer.on('wheel', function(e) {
        if (e.originalEvent.deltaY !== 0 && !e.shiftKey) {
            e.preventDefault();
            this.scrollLeft += e.originalEvent.deltaY;
        }
    });

    // Per page change
    $('#per_page').on('change', function() {
        var currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('per_page', $(this).val());
        currentUrl.searchParams.delete('page');
        window.location.href = currentUrl.toString();
    });

    // Filter panel toggle
    $('.filter_btn').on('click', function() {
        $('.filter_panel').toggleClass('show');
    });

    // Datepicker
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true
    });

    // Sortable columns
    $('.sortable').on('click', function() {
        var sortField = $(this).data('sort');
        var currentSort = '{{ request("sort") }}';
        var currentDirection = '{{ request("direction", "desc") }}';
        
        var newDirection = 'desc';
        if (currentSort === sortField && currentDirection === 'desc') {
            newDirection = 'asc';
        }
        
        var currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('sort', sortField);
        currentUrl.searchParams.set('direction', newDirection);
        currentUrl.searchParams.delete('page');
        window.location.href = currentUrl.toString();
    });

    // Verify EOI by staff
    $('.verify-btn').on('click', function() {
        var $btn = $(this);
        var eoiId = $btn.data('eoi-id');
        
        Swal.fire({
            title: 'Verify EOI Details?',
            text: 'Please confirm that you have reviewed and verified the EOI details.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#667eea',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Verify',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Verifying...');
                
                $.ajax({
                    url: '/clients/sheets/eoi-roi/' + eoiId + '/verify',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Verified!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#667eea'
                            }).then(() => {
                                // Update the UI
                                var $row = $btn.closest('tr');
                                $row.find('.confirmation-date-cell').html(response.confirmation_date);
                                $row.find('.checked-by-cell').html(response.checked_by);
                                
                                // Replace verify button with send email button
                                $btn.replaceWith(
                                    '<button type="button" class="btn btn-sm btn-primary send-email-btn" ' +
                                    'data-eoi-id="' + eoiId + '" title="Send confirmation email to client">' +
                                    '<i class="fas fa-envelope"></i> Send Email</button>'
                                );
                                
                                // Re-bind send email event
                                bindSendEmailEvents();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                            $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Verify');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Failed to verify EOI details. Please try again.', 'error');
                        $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Verify');
                    }
                });
            }
        });
    });

    // Send confirmation email to client
    function bindSendEmailEvents() {
        $('.send-email-btn').off('click').on('click', function() {
            var $btn = $(this);
            var eoiId = $btn.data('eoi-id');
            
            Swal.fire({
                title: 'Send Confirmation Email?',
                text: 'This will send an email to the client asking them to confirm their EOI details.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#667eea',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Send Email',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
                    
                    $.ajax({
                        url: '/clients/sheets/eoi-roi/' + eoiId + '/send-confirmation',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Email Sent!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonColor: '#667eea'
                                }).then(() => {
                                    // Update the UI
                                    var $row = $btn.closest('tr');
                                    $row.find('.client-confirmation-cell').html(
                                        '<span class="badge badge-secondary">' +
                                        '<i class="fas fa-clock"></i> Pending</span>' +
                                        '<br><small class="text-muted">Sent ' + response.sent_at.split(' ')[0] + '</small>'
                                    );
                                    
                                    // Replace send email button with awaiting status
                                    $btn.replaceWith(
                                        '<span class="badge badge-info">' +
                                        '<i class="fas fa-clock"></i> Awaiting Client</span>'
                                    );
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                                $btn.prop('disabled', false).html('<i class="fas fa-envelope"></i> Send Email');
                            }
                        },
                        error: function(xhr) {
                            var errorMsg = 'Failed to send email. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', errorMsg, 'error');
                            $btn.prop('disabled', false).html('<i class="fas fa-envelope"></i> Send Email');
                        }
                    });
                }
            });
        });
    }
    
    // Initial binding
    bindSendEmailEvents();

    // View amendment notes
    $('.view-notes-btn').on('click', function() {
        var notes = $(this).data('notes');
        
        Swal.fire({
            title: 'Client Amendment Request',
            html: '<div style="text-align: left; max-height: 400px; overflow-y: auto;">' +
                  '<p><strong>Requested Changes:</strong></p>' +
                  '<div style="background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;">' +
                  notes.replace(/\n/g, '<br>') +
                  '</div></div>',
            icon: 'info',
            confirmButtonColor: '#667eea',
            confirmButtonText: 'Close',
            width: '600px'
        });
    });
});
</script>
@endpush
