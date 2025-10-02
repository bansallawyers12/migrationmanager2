@extends('layouts.admin_client_detail_dashboard')

@section('content')
    <main class="main-content">
        <header class="header">
            <h1>Dashboard</h1>
        </header>

        {{-- KPI Cards Section --}}
        <section class="kpi-cards">
            <x-dashboard.kpi-card 
                :title="'Active Matters'" 
                :count="$count_active_matter" 
                :route="route('admin.clients.clientsmatterslist')"
                icon="fas fa-briefcase"
                icon-class="icon-active" 
            />
            
            <x-dashboard.kpi-card 
                :title="'Urgent Notes Deadlines'" 
                :count="$count_note_deadline"
                icon="fas fa-hourglass-half"
                icon-class="icon-pending" 
            />
            
            <x-dashboard.kpi-card 
                :title="'Cases Requiring Attention'" 
                :count="$count_cases_requiring_attention_data"
                icon="fas fa-check-circle"
                icon-class="icon-success" 
            />
        </section>

        {{-- Priority Focus Section --}}
        <section class="priority-focus">
            {{-- Urgent Notes & Deadlines --}}
            <div class="focus-container">
                <h3>
                    <i class="fas fa-calendar-times" style="color: var(--danger-color);"></i> 
                    Urgent Notes & Deadlines
                </h3>
                <div class="task-list-container">
                    <ul class="task-list">
                        @forelse($notesData as $note)
                            <x-dashboard.note-item :note="$note" />
                        @empty
                            <li class="text-center text-muted py-3">No urgent notes found</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Cases Requiring Attention --}}
            <div class="focus-container">
                <h3>
                    <i class="fas fa-exclamation-circle" style="color: var(--warning-color);"></i> 
                    Cases Requiring Attention
                </h3>
                <div class="case-list-container">
                    <ul class="case-list">
                        @forelse($cases_requiring_attention_data as $case)
                            <x-dashboard.case-item :case="$case" />
                        @empty
                            <li class="text-center text-muted py-3">No cases requiring attention</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </section>

        {{-- Client Matters Overview Section --}}
        <section class="cases-overview">
            <div class="cases-overview-header">
                <div class="header-left">
                    <h3>Client Matters <span class="total-count">({{ $data->total() }} total)</span></h3>
                </div>
                <div class="header-right">
                    <x-dashboard.column-toggle :visibleColumns="$visibleColumns" />
                </div>
            </div>

            {{-- Filter Controls --}}
            <x-dashboard.filter-form :filters="$filters" :workflowStages="$workflowStages" />

            {{-- Data Table --}}
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="col-matter">Matter</th>
                            <th class="col-client_id">Client ID</th>
                            <th class="col-client_name">Client Name</th>
                            <th class="col-dob">DOB</th>
                            <th class="col-migration_agent">Migration Agent</th>
                            <th class="col-person_responsible">Person Responsible</th>
                            <th class="col-person_assisting">Person Assisting</th>
                            <th class="col-stage">Stage</th>
                            <th class="col-action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $matter)
                            <x-dashboard.client-matter-row :matter="$matter" :workflowStages="$workflowStages" />
                        @empty
                            <tr>
                                <td colspan="9" class="empty-state">
                                    <div>
                                        <i class="fas fa-inbox fa-3x mb-3" style="color: #cbd5e0;"></i>
                                        <p>No records found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            @if($data->hasPages())
                <div class="pagination-container">
                    <div class="pagination-info">
                        Showing {{ $data->firstItem() ?? 0 }} to {{ $data->lastItem() ?? 0 }} of {{ $data->total() }} results
                    </div>
                    <div class="pagination-links">
                        {{ $data->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </section>
    </main>

    {{-- Modals --}}
    @include('components.dashboard.modals')
@endsection

@push('styles')
@once
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endonce
@endpush

@push('scripts')
@once
<script src="{{ asset('js/dashboard.js') }}"></script>
@endonce
@endpush
