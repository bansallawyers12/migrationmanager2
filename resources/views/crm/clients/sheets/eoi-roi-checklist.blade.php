@extends('layouts.crm_client_detail')
@section('title', 'EOI/ROI Sheet - Checklist')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
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
    .eoi-checklist-section {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 24px;
    }
    .eoi-checklist-section h5 {
        font-weight: 700;
        color: #495057;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 2px solid #667eea;
    }
    .eoi-checklist-item {
        padding: 14px 16px;
        margin-bottom: 10px;
        background: linear-gradient(135deg, #f8f9ff 0%, #f8f9fa 100%);
        border-radius: 8px;
        border-left: 4px solid #667eea;
    }
    .eoi-checklist-item strong {
        color: #495057;
        display: block;
        margin-bottom: 6px;
    }
    .eoi-checklist-item p {
        margin: 0;
        font-size: 14px;
        color: #6c757d;
        line-height: 1.5;
    }
    .eoi-checklist-table th {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #495057;
        border-bottom: 2px solid #667eea;
    }
</style>
@endsection

@section('content')
<div class="listing-container">
    <section class="listing-section" style="padding-top: 40px;">
        <div class="listing-section-body">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h4><i class="fas fa-tasks"></i> EOI/ROI Sheet - Checklist</h4>
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

                    <div class="eoi-checklist-section">
                        <h5><i class="fas fa-file-alt mr-2"></i> Matter checklists (EOI / ROI matters)</h5>
                        <p class="text-muted mb-3">
                            These rows come from <strong>Upload Checklists</strong> configured per matter template. They are the same items staff can attach when sending documents for EOI-related (and ROI-titled) matters.
                            <a href="{{ route('upload_checklists.index') }}">Manage upload checklists</a>.
                        </p>
                        @if($matterChecklistRows->isEmpty())
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle"></i> No checklist rows are configured for matter templates classified as EOI or ROI (by matter title / nick name). Add them under Upload Checklists for the relevant matter.
                            </p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover eoi-checklist-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">ID</th>
                                            <th>Checklist name</th>
                                            <th>Matter</th>
                                            <th style="width: 140px;">Template file</th>
                                            <th style="width: 100px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
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
                                                        <a href="{{ asset('checklists/' . $row->file) }}" target="_blank" rel="noopener noreferrer" class="text-nowrap">{{ \Illuminate\Support\Str::limit($row->file, 24) }}</a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-nowrap">
                                                    <a href="{{ route('upload_checklists.matter', ['matterId' => $row->matter_id]) }}" class="btn btn-sm btn-outline-primary">Edit matter</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="eoi-checklist-section">
                        <h5><i class="fas fa-folder-open mr-2"></i> Visa document categories (EOI confirmation emails)</h5>
                        <p class="text-muted mb-3">Categories used under Visa Documents on the client file; attachments for EOI confirmation emails are resolved from here when configured.</p>
                        @foreach($eoiCategoryGuide as $item)
                            <div class="eoi-checklist-item">
                                <strong><i class="fas fa-check-square text-primary mr-1"></i> {{ $item['title'] }}</strong>
                                <p>{{ $item['description'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
