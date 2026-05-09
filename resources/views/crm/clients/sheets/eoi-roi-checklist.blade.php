@extends('layouts.crm_client_detail')
@section('title', 'EOI/ROI Sheet - Checklist')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-pagination.css') }}">
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

    /* Match List tab table (eoi-roi.blade.php) */
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
    .listing-container table.eoi-roi-table th:first-child,
    .listing-container table.eoi-roi-table td:first-child {
        width: 72px !important;
        min-width: 72px !important;
        max-width: none !important;
        white-space: nowrap;
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

    .table-responsive {
        position: relative;
        overflow-x: auto;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
    }
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

    .eoi-checklist-meta {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 16px;
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

                <div class="sheet-tabs">
                    <a href="{{ route('clients.sheets.eoi-roi', request()->query()) }}" class="sheet-tab">
                        <i class="fas fa-list"></i> List
                    </a>
                    <a href="{{ route('clients.sheets.eoi-roi.checklist', request()->query()) }}" class="sheet-tab active">
                        <i class="fas fa-clipboard-check"></i> Checklist
                    </a>
                    @if(Auth::user() && in_array(Auth::user()->role, [1, 12]))
                    <a href="{{ route('clients.sheets.eoi-roi.insights', request()->query()) }}" class="sheet-tab">
                        <i class="fas fa-chart-bar"></i> Insights
                    </a>
                    @endif
                </div>

                <div class="card-body">
                    @if($activeFilterCount > 0)
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i> You have {{ $activeFilterCount }} active filter(s) on the List view.
                            <a href="{{ route('clients.sheets.eoi-roi', request()->query()) }}" class="alert-link">Return to List with the same filters</a>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                        <div>
                            <strong class="d-block text-dark mb-1">Matter checklists (EOI / ROI matters)</strong>
                            <span class="eoi-checklist-meta mb-0 d-block">
                                Same items as <strong>Upload Checklists</strong> for EOI/ROI matter templates (attach when sending documents).
                            </span>
                        </div>
                        <div>
                            <a href="{{ route('upload_checklists.index') }}" class="btn btn-theme btn-theme-sm">
                                <i class="fas fa-cog"></i> Manage upload checklists
                            </a>
                        </div>
                    </div>

                    <div class="scroll-hint">
                        <i class="fas fa-arrows-alt-h"></i> Scroll horizontally to see all columns.
                    </div>

                    <div class="table-container" id="eoi-checklist-table-wrap">
                        <div class="scroll-indicator scroll-indicator-left"></div>
                        <div class="scroll-indicator scroll-indicator-right visible"></div>
                        <div class="table-responsive" id="eoi-checklist-scroll-container">
                            <table class="table table-bordered table-hover eoi-roi-table" id="eoi-roi-checklist-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Checklist name</th>
                                        <th>Matter</th>
                                        <th>Template file</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($matterChecklistRows->isEmpty())
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="fas fa-info-circle"></i> No checklist rows are configured for matter templates classified as EOI or ROI. Add them under Upload Checklists for the relevant matter.
                                            </td>
                                        </tr>
                                    @else
                                        @foreach($matterChecklistRows as $row)
                                            <tr>
                                                <td class="text-muted">{{ $row->id }}</td>
                                                <td>{{ $row->name }}</td>
                                                <td>
                                                    <span class="d-block">{{ $row->matter_title ?? '—' }}</span>
                                                    @if(!empty($row->matter_nick_name))
                                                        <small class="text-muted">Nick: {{ $row->matter_nick_name }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!empty($row->file))
                                                        <a href="{{ asset('checklists/' . $row->file) }}" target="_blank" rel="noopener noreferrer" class="eoi-link text-nowrap">{{ \Illuminate\Support\Str::limit($row->file, 36) }}</a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-nowrap">
                                                    <a href="{{ route('upload_checklists.matter', ['matterId' => $row->matter_id]) }}" class="btn btn-theme btn-theme-sm">
                                                        <i class="fas fa-edit"></i> Edit matter
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function($) {
    var $wrap = $('#eoi-checklist-table-wrap');
    if (!$wrap.length) return;
    var $scrollContainer = $('#eoi-checklist-scroll-container');
    var $leftIndicator = $wrap.find('.scroll-indicator-left');
    var $rightIndicator = $wrap.find('.scroll-indicator-right');

    function updateScrollIndicators() {
        if (!$scrollContainer.length || !$scrollContainer[0]) return;
        var scrollLeft = $scrollContainer.scrollLeft();
        var scrollWidth = $scrollContainer[0].scrollWidth;
        var clientWidth = $scrollContainer[0].clientWidth;
        var maxScroll = scrollWidth - clientWidth;

        if (scrollLeft > 10) {
            $leftIndicator.addClass('visible');
        } else {
            $leftIndicator.removeClass('visible');
        }

        if (scrollLeft < maxScroll - 10) {
            $rightIndicator.addClass('visible');
        } else {
            $rightIndicator.removeClass('visible');
        }
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
});
</script>
@endpush
