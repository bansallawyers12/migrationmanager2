@extends('layouts.crm_client_detail_dashboard')

@section('content')
    <main class="main-content">
        <header class="header">
            <div class="header-title-section">
                <h1>Dashboard</h1>
            </div>
            <div class="header-actions">
                <button type="button" class="action-btn action-btn-secondary" id="refreshDashboard" title="Refresh Dashboard (Alt+R)">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </header>

        {{-- KPI Cards Section --}}
        <section class="kpi-cards">
            <x-dashboard.kpi-card 
                :title="'Active Matters'" 
                :count="$count_active_matter" 
                :route="route('clients.clientsmatterslist')"
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
                <div class="focus-header">
                    <h3>
                        <i class="fas fa-calendar-times" style="color: var(--danger-color);"></i> 
                        My Tasks & Deadlines
                    </h3>
                    <span class="badge-count" title="Total active tasks">{{ count($notesData) }}</span>
                </div>
                
                @if(count($notesData) > 10)
                    <div class="section-info">
                        <i class="fas fa-info-circle"></i>
                        Showing top 10 most urgent tasks (ordered by deadline)
                    </div>
                @endif
                
                <div class="task-list-container">
                    @if(count($notesData) > 0)
                        <ul class="task-list task-list-modern">
                            @foreach($notesData->take(10) as $note)
                                <x-dashboard.note-item :note="$note" />
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-state-modern">
                            <i class="fas fa-check-circle fa-3x"></i>
                            <h4>All Caught Up!</h4>
                            <p>No urgent tasks at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Cases Requiring Attention --}}
            <div class="focus-container">
                <div class="focus-header">
                    <h3>
                        <i class="fas fa-exclamation-circle" style="color: var(--warning-color);"></i> 
                        Cases Requiring Attention
                    </h3>
                    <span class="badge-count">{{ count($cases_requiring_attention_data) }}</span>
                </div>
                <div class="case-list-container">
                    @if(count($cases_requiring_attention_data) > 0)
                        <ul class="case-list">
                            @foreach($cases_requiring_attention_data as $case)
                                <x-dashboard.case-item :case="$case" />
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-state-modern">
                            <i class="fas fa-thumbs-up fa-3x"></i>
                            <h4>Great Work!</h4>
                            <p>No cases requiring immediate attention.</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Client Matters Overview Section --}}
        <section class="cases-overview">
            <div class="cases-overview-header">
                <div class="header-left">
                    <h3>
                        <i class="fas fa-table"></i> 
                        Client Matters 
                        <span class="total-count">({{ $data->total() }} total)</span>
                    </h3>
                </div>
                <div class="header-right">
                    <x-dashboard.column-toggle :visibleColumns="$visibleColumns" />
                </div>
            </div>

            {{-- Filter Controls --}}
            <x-dashboard.filter-form :filters="$filters" :workflowStages="$workflowStages" />

            {{-- Data Table --}}
            <div class="table-responsive">
                <table class="data-table data-table-enhanced" role="grid">
                    <thead>
                        <tr role="row">
                            <th class="col-matter" role="columnheader">Matter</th>
                            <th class="col-client_id" role="columnheader">Client ID</th>
                            <th class="col-client_name" role="columnheader">Client Name</th>
                            <th class="col-dob" role="columnheader">DOB</th>
                            <th class="col-migration_agent" role="columnheader">Migration Agent</th>
                            <th class="col-person_responsible" role="columnheader">Person Responsible</th>
                            <th class="col-person_assisting" role="columnheader">Person Assisting</th>
                            <th class="col-stage" role="columnheader">Stage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $matter)
                            <tr role="row" data-matter-id="{{ $matter->id }}">
                                <td class="col-matter" style="white-space: initial;">
                                    <a href="{{ route('clients.detail', [base64_encode(convert_uuencode($matter->client_id)), $matter->client_unique_matter_no]) }}" class="matter-link">
                                        @if($matter->sel_matter_id == 1)
                                            General matter
                                        @else
                                            {{ $matter->matter->title ?? 'NA' }}
                                        @endif
                                        ({{ $matter->client_unique_matter_no }})
                                    </a>
                                    @php
                                        $emailCount = $matter->mailReports()
                                            ->where('client_id', $matter->client_id)
                                            ->where('conversion_type', 'conversion_email_fetch')
                                            ->whereNull('mail_is_read')
                                            ->where(function($query) {
                                                $query->orWhere('mail_body_type', 'inbox')
                                                      ->orWhere('mail_body_type', 'sent');
                                            })->count();
                                    @endphp
                                    @if($emailCount > 0)
                                        <span class="badge badge-email" title="{{ $emailCount }} unread emails">
                                            <i class="fas fa-envelope"></i> {{ $emailCount }}
                                        </span>
                                    @endif
                                </td>
                                <td class="col-client_id">
                                    <a href="{{ route('clients.detail', base64_encode(convert_uuencode($matter->client_id))) }}" class="client-id-link">
                                        {{ $matter->client->client_id ?: config('constants.empty') }}
                                    </a>
                                </td>
                                <td class="col-client_name">
                                    {{ $matter->client->first_name ?: config('constants.empty') }} {{ $matter->client->last_name ?: config('constants.empty') }}
                                </td>
                                <td class="col-dob">
                                    @if($matter->client && $matter->client->dob)
                                        {{ \Carbon\Carbon::parse($matter->client->dob)->format('d/m/Y') }}
                                    @else
                                        {{ config('constants.empty') }}
                                    @endif
                                </td>
                                <td class="col-migration_agent">
                                    @if($matter->migrationAgent)
                                        <div class="user-avatar-cell">
                                            <div class="avatar-sm">
                                                {{ substr($matter->migrationAgent->first_name, 0, 1) }}{{ substr($matter->migrationAgent->last_name, 0, 1) }}
                                            </div>
                                            {{ $matter->migrationAgent->first_name }} {{ $matter->migrationAgent->last_name }}
                                        </div>
                                    @else
                                        {{ config('constants.empty') }}
                                    @endif
                                </td>
                                <td class="col-person_responsible">
                                    @if($matter->personResponsible)
                                        <div class="user-avatar-cell">
                                            <div class="avatar-sm">
                                                {{ substr($matter->personResponsible->first_name, 0, 1) }}{{ substr($matter->personResponsible->last_name, 0, 1) }}
                                            </div>
                                            {{ $matter->personResponsible->first_name }} {{ $matter->personResponsible->last_name }}
                                        </div>
                                    @else
                                        {{ config('constants.empty') }}
                                    @endif
                                </td>
                                <td class="col-person_assisting">
                                    @if($matter->personAssisting)
                                        <div class="user-avatar-cell">
                                            <div class="avatar-sm">
                                                {{ substr($matter->personAssisting->first_name, 0, 1) }}{{ substr($matter->personAssisting->last_name, 0, 1) }}
                                            </div>
                                            {{ $matter->personAssisting->first_name }} {{ $matter->personAssisting->last_name }}
                                        </div>
                                    @else
                                        {{ config('constants.empty') }}
                                    @endif
                                </td>
                                <td class="col-stage">
                                    <select class="form-select stageCls stage-select-enhanced" id="stage_{{ $matter->id }}" aria-label="Change stage for matter {{ $matter->client_unique_matter_no }}">
                                        @foreach($workflowStages as $stage)
                                            <option value="{{ $stage->id }}" {{ $matter->workflow_stage_id == $stage->id ? 'selected' : '' }}>
                                                {{ $stage->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="empty-state">
                                    <div class="empty-state-modern">
                                        <i class="fas fa-inbox fa-3x"></i>
                                        <h4>No Records Found</h4>
                                        <p>Try adjusting your filters or search criteria.</p>
                                        @if(isset($filters['client_name']) || isset($filters['client_stage']))
                                            <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">
                                                <i class="fas fa-times"></i> Clear All Filters
                                            </a>
                                        @endif
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
                        Showing <strong>{{ $data->firstItem() ?? 0 }}</strong> to <strong>{{ $data->lastItem() ?? 0 }}</strong> of <strong>{{ $data->total() }}</strong> results
                    </div>
                    <div class="pagination-links">
                        {{ $data->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </section>
    </main>

    {{-- Loading Overlay --}}
    <div class="loading-overlay" id="loadingOverlay" style="display: none;">
        <div class="spinner-container">
            <div class="spinner"></div>
            <p>Loading...</p>
        </div>
    </div>

    {{-- Toast Notification Container --}}
    <div class="toast-container" id="toastContainer"></div>

    {{-- Modals --}}
    @include('components.dashboard.modals')
@endsection

@push('styles')
@once
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<style>
/* Fix Layout - Remove blank space on the side */
.main-content {
    margin-left: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    padding: 20px !important;
    box-sizing: border-box !important;
}

.sidebar-expanded + .main-content {
    margin-left: 0 !important;
    width: 100% !important;
}

/* Ensure all sections take full width */
.kpi-cards,
.priority-focus,
.cases-overview,
.quick-stats-banner {
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box;
}

.table-responsive {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: auto !important;
}

/* Test Page Specific Styles */
.header-title-section h1 {
    margin: 0;
    font-size: 1.8em;
    font-weight: 600;
    color: #333;
}

.header-actions {
    display: flex;
    gap: 10px;
    align-self: flex-start;
}

/* Enhanced Action Buttons */
.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border: none;
    border-radius: 8px;
    font-size: 0.9em;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.action-btn-primary {
    background: linear-gradient(135deg, #005792 0%, #003d66 100%);
    color: white;
}

.action-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 87, 146, 0.4);
}

.action-btn-secondary {
    background: white;
    color: #005792;
    border: 2px solid #005792;
}

.action-btn-secondary:hover {
    background: #005792;
    color: white;
}

.action-btn-outline {
    background: transparent;
    color: #005792;
    border: 2px solid #005792;
}

.action-btn-outline:hover {
    background: #005792;
    color: white;
}

/* Focus Container Enhancements */
.focus-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.badge-count {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: 600;
}

.section-info {
    background: #e7f3ff;
    border-left: 3px solid #2196f3;
    padding: 10px 15px;
    border-radius: 4px;
    font-size: 0.85em;
    color: #0d47a1;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-info i {
    color: #2196f3;
}

.task-list-modern {
    list-style: none;
    padding: 0;
    margin: 0;
}

/* Modern Empty States */
.empty-state-modern {
    text-align: center;
    padding: 40px 20px;
    color: #999;
}

.empty-state-modern i {
    color: #cbd5e0;
    margin-bottom: 15px;
}

.empty-state-modern h4 {
    color: #666;
    margin: 10px 0 5px 0;
    font-size: 1.2em;
    font-weight: 600;
}

.empty-state-modern p {
    color: #999;
    font-size: 0.95em;
}

/* Enhanced Table Styles */
.data-table-enhanced {
    border-collapse: separate;
    border-spacing: 0;
}

.data-table-enhanced thead th {
    background: linear-gradient(to bottom, #f8f9fa 0%, #e9ecef 100%);
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding: 12px 8px;
}

.data-table-enhanced tbody tr {
    transition: all 0.2s ease;
}

.data-table-enhanced tbody tr:hover {
    background-color: #f1f8ff;
    transform: scale(1.001);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.matter-link {
    color: #005792;
    font-weight: 500;
    text-decoration: none;
}

.matter-link:hover {
    color: #003d66;
    text-decoration: underline;
}

.client-id-link {
    color: #005792;
    font-weight: 600;
    text-decoration: none;
    font-family: 'Courier New', monospace;
}

/* Badge Styles */
.badge-email {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.75em;
    font-weight: 600;
    margin-left: 8px;
}

/* User Avatar in Table */
.user-avatar-cell {
    display: flex;
    align-items: center;
    gap: 8px;
}

.avatar-sm {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7em;
    font-weight: 600;
    flex-shrink: 0;
}

/* Enhanced Stage Select */
.stage-select-enhanced {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 0.85em;
    transition: all 0.2s ease;
    width: 100%;
    min-width: 180px;
    white-space: normal;
    overflow: visible;
}

.stage-select-enhanced:focus {
    border-color: #005792;
    box-shadow: 0 0 0 3px rgba(0, 87, 146, 0.1);
    outline: none;
    z-index: 10;
    position: relative;
}

/* Fix stage column width */
.col-stage {
    min-width: 200px;
    max-width: 250px;
}

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(4px);
}

.spinner-container {
    text-align: center;
    color: white;
}

.spinner {
    width: 50px;
    height: 50px;
    border: 4px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 15px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Toast Notifications */
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.toast {
    min-width: 300px;
    padding: 15px 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 12px;
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast-success {
    border-left: 4px solid #28a745;
}

.toast-error {
    border-left: 4px solid #dc3545;
}

.toast-info {
    border-left: 4px solid #17a2b8;
}

.toast-warning {
    border-left: 4px solid #ffc107;
}

.toast i {
    font-size: 1.5em;
}

.toast-success i { color: #28a745; }
.toast-error i { color: #dc3545; }
.toast-info i { color: #17a2b8; }
.toast-warning i { color: #ffc107; }

.toast-message {
    flex: 1;
    font-size: 0.95em;
    color: #333;
}

.toast-close {
    background: none;
    border: none;
    color: #999;
    font-size: 1.2em;
    cursor: pointer;
    padding: 0;
    width: 24px;
    height: 24px;
}

.toast-close:hover {
    color: #333;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .dashboard-test-header {
        align-items: flex-start;
    }
    
    .header-actions {
        width: 100%;
    }
    
    .action-btn {
        flex: 1;
        justify-content: center;
    }
    
    .quick-stats-banner {
        grid-template-columns: 1fr;
    }
    
    .user-avatar-cell {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
}
</style>
@endonce
@endpush

@push('scripts')
@once
<script>
    // Define dashboard routes and data before loading the main script
    window.dashboardRoutes = {
        dashboard: "{{ route('dashboard') }}",
        updateStage: "{{ route('dashboard.update-stage') }}",
        columnPreferences: "{{ route('dashboard.column-preferences') }}",
        extendDeadline: "{{ route('dashboard.extend-deadline') }}",
        updateTaskCompleted: "{{ route('dashboard.update-task-completed') }}"
    };
    
    window.dashboardData = {
        visibleColumns: {!! json_encode($visibleColumns) !!}
    };
    
    // Error handling for missing routes
    if (typeof window.dashboardRoutes === 'undefined') {
        console.error('Dashboard routes not defined');
    }
</script>
<script src="{{ asset('js/dashboard-optimized.js') }}"></script>
<script>
// Enhanced Dashboard Test Page JavaScript

// Toast Notification System
window.showToast = function(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    const icon = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    }[type] || 'fa-info-circle';
    
    toast.innerHTML = `
        <i class="fas ${icon}"></i>
        <span class="toast-message">${message}</span>
        <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
    `;
    
    document.getElementById('toastContainer').appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
};

// Override the default showNotification function
window.showNotification = function(message, type = 'info') {
    window.showToast(message, type);
};

// Loading Overlay Functions
window.showLoading = function() {
    document.getElementById('loadingOverlay').style.display = 'flex';
};

window.hideLoading = function() {
    document.getElementById('loadingOverlay').style.display = 'none';
};

// Refresh Dashboard
document.getElementById('refreshDashboard').addEventListener('click', function() {
    showLoading();
    setTimeout(() => {
        location.reload();
    }, 500);
});

// Keyboard Shortcuts
document.addEventListener('keydown', function(e) {
    // Alt + R to refresh
    if (e.altKey && e.key === 'r') {
        e.preventDefault();
        document.getElementById('refreshDashboard').click();
    }
    
    // Escape to close dropdowns
    if (e.key === 'Escape') {
        document.getElementById('columnDropdown')?.classList.remove('show');
    }
});

// Show welcome toast on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        showToast('Welcome to your dashboard!', 'success');
    }, 500);
    
    // Add tooltips info
    console.log('%c🚀 Dashboard', 'font-size: 20px; color: #005792; font-weight: bold;');
    console.log('%cKeyboard Shortcuts:', 'font-size: 14px; font-weight: bold;');
    console.log('Alt + R: Refresh Dashboard');
    console.log('Esc: Close Dropdowns');
});

// Debounced search (override from original script)
let searchTimeout;
document.querySelector('input[name="client_name"]')?.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        console.log('Search query:', this.value);
    }, 500);
});

// Add animation to KPI cards on load
document.querySelectorAll('.card').forEach((card, index) => {
    card.style.animation = `fadeInUp 0.5s ease ${index * 0.1}s both`;
});

// Add CSS animation for cards
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideOutRight {
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
@endonce
@endpush
