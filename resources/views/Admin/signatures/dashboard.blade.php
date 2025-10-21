@extends('layouts.admin_client_detail')
@section('title', 'Signature Dashboard')

@section('styles')
<style>
    .signature-dashboard {
        padding: 20px;
    }
    
    .dashboard-header {
        margin-bottom: 30px;
    }
    
    .dashboard-header h1 {
        font-size: 24px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
        border-radius: 10px;
        color: white;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .stat-card.sent {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .stat-card.pending {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .stat-card.signed {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .stat-card.overdue {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }
    
    .stat-card h3 {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 10px;
        opacity: 0.9;
    }
    
    .stat-card .number {
        font-size: 32px;
        font-weight: 700;
    }
    
    .tabs-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .nav-tabs {
        border-bottom: 2px solid #e9ecef;
        padding: 0 20px;
        background: #f8f9fa;
    }
    
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        padding: 15px 20px;
        font-weight: 500;
        position: relative;
    }
    
    .nav-tabs .nav-link:hover {
        color: #667eea;
        border-color: transparent;
    }
    
    .nav-tabs .nav-link.active {
        color: #667eea;
        background: transparent;
        border-color: transparent;
    }
    
    .nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background: #667eea;
    }
    
    .filter-bar {
        padding: 20px;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: center;
    }
    
    .filter-bar select,
    .filter-bar input {
        padding: 8px 12px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        font-size: 14px;
    }
    
    .documents-table {
        width: 100%;
        padding: 20px;
    }
    
    .documents-table table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }
    
    .documents-table th {
        text-align: left;
        padding: 12px;
        color: #6c757d;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .documents-table td {
        padding: 15px 12px;
        background: #f8f9fa;
        vertical-align: middle;
    }
    
    .documents-table tr {
        transition: all 0.2s ease;
    }
    
    .documents-table tr:hover td {
        background: #e9ecef;
    }
    
    .documents-table td:first-child {
        border-radius: 8px 0 0 8px;
    }
    
    .documents-table td:last-child {
        border-radius: 0 8px 8px 0;
    }
    
    .badge-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .badge-draft {
        background: #6c757d;
        color: white;
    }
    
    .badge-sent {
        background: #ffc107;
        color: #000;
    }
    
    .badge-signed {
        background: #28a745;
        color: white;
    }
    
    .badge-priority {
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }
    
    .badge-priority.low {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .badge-priority.normal {
        background: #d4edda;
        color: #155724;
    }
    
    .badge-priority.high {
        background: #f8d7da;
        color: #721c24;
    }
    
    .association-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        background: #e7f3ff;
        color: #0066cc;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .association-chip i {
        font-size: 10px;
    }
    
    .visibility-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: 500;
    }
    
    .badge-owner {
        background: #e3f2fd;
        color: #1565c0;
    }
    
    .badge-signer {
        background: #fff3e0;
        color: #e65100;
    }
    
    .badge-associated {
        background: #f3e5f5;
        color: #6a1b9a;
    }
    
    .badge-admin {
        background: #e8f5e9;
        color: #2e7d32;
    }
    
    .badge-visible {
        background: #fce4ec;
        color: #c2185b;
    }
    
    .btn-action {
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 5px;
        margin-right: 5px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .btn-view {
        background: #667eea;
        color: white;
    }
    
    .btn-view:hover {
        background: #5568d3;
        color: white;
    }
    
    .btn-remind {
        background: #ffc107;
        color: #000;
    }
    
    .btn-remind:hover {
        background: #e0a800;
    }
    
    .btn-attach {
        background: #28a745;
        color: white;
    }
    
    .btn-attach:hover {
        background: #218838;
        color: white;
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        color: white;
    }
    
    .overdue-indicator {
        color: #dc3545;
        font-weight: 600;
        font-size: 11px;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 48px;
        margin-bottom: 20px;
        opacity: 0.5;
    }
</style>
@endsection

@section('content')
<div class="signature-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>📝 Signature Dashboard</h1>
                <p style="color: #6c757d;">Manage and track document signatures</p>
            </div>
            <a href="{{ route('admin.signatures.create') }}" class="btn-primary-custom">
                <i class="fas fa-plus"></i> Send New Document
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card sent">
            <h3>My Documents</h3>
            <div class="number">{{ $counts['sent_by_me'] ?? 0 }}</div>
        </div>
        <div class="stat-card pending">
            <h3>Pending Signature</h3>
            <div class="number">{{ $counts['pending'] ?? 0 }}</div>
        </div>
        <div class="stat-card signed">
            <h3>Signed</h3>
            <div class="number">{{ $counts['signed'] ?? 0 }}</div>
        </div>
        @if($counts['overdue'] ?? 0 > 0)
        <div class="stat-card overdue">
            <h3>Overdue</h3>
            <div class="number">{{ $counts['overdue'] ?? 0 }}</div>
        </div>
        @endif
        @if($user->role !== 1)
        <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <h3>Visible to Me</h3>
            <div class="number">{{ $counts['visible_to_me'] ?? 0 }}</div>
        </div>
        @endif
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Tabs -->
    <div class="tabs-container">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ !request('tab') || request('tab') == 'sent_by_me' ? 'active' : '' }}" 
                   href="{{ route('admin.signatures.index', ['tab' => 'sent_by_me']) }}">
                    Sent by Me
                </a>
            </li>
            @if($user->role === 1)
            <li class="nav-item">
                <a class="nav-link {{ request('tab') == 'all' ? 'active' : '' }}" 
                   href="{{ route('admin.signatures.index', ['tab' => 'all']) }}">
                    All Documents
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ request('tab') == 'pending' ? 'active' : '' }}" 
                   href="{{ route('admin.signatures.index', ['tab' => 'pending']) }}">
                    Pending
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('tab') == 'signed' ? 'active' : '' }}" 
                   href="{{ route('admin.signatures.index', ['tab' => 'signed']) }}">
                    Signed
                </a>
            </li>
        </ul>

        <!-- Filters -->
        <div class="filter-bar">
            <form method="GET" action="{{ route('admin.signatures.index') }}" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center; width: 100%;">
                <input type="hidden" name="tab" value="{{ request('tab') }}">
                
                <!-- Visibility Scope Filter -->
                <div style="display: flex; gap: 5px; padding: 4px; background: #e9ecef; border-radius: 8px;">
                    <a href="{{ route('admin.signatures.index', array_merge(request()->except('scope'), ['scope' => 'team'])) }}" 
                       class="btn btn-sm {{ !request('scope') || request('scope') == 'team' ? 'btn-primary' : 'btn-light' }}"
                       style="padding: 6px 15px; font-size: 13px;">
                        👥 My Documents
                    </a>
                    @if($user->role === 1)
                    <a href="{{ route('admin.signatures.index', array_merge(request()->except('scope'), ['scope' => 'organization'])) }}" 
                       class="btn btn-sm {{ request('scope') == 'organization' ? 'btn-primary' : 'btn-light' }}"
                       style="padding: 6px 15px; font-size: 13px;">
                        🌐 Organization
                    </a>
                    @endif
                </div>
                
                <select name="association" class="form-control" style="width: auto;" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="associated" {{ request('association') == 'associated' ? 'selected' : '' }}>Associated</option>
                    <option value="adhoc" {{ request('association') == 'adhoc' ? 'selected' : '' }}>Ad-hoc</option>
                </select>
                
                <select name="status" class="form-control" style="width: auto;" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="signed" {{ request('status') == 'signed' ? 'selected' : '' }}>Signed</option>
                </select>
                
                <input type="text" name="search" class="form-control" placeholder="Search documents..." 
                       value="{{ request('search') }}" style="flex: 1; min-width: 200px;">
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                
                @if(request()->anyFilled(['association', 'status', 'search', 'scope']))
                <a href="{{ route('admin.signatures.index', ['tab' => request('tab')]) }}" class="btn btn-secondary">
                    Clear Filters
                </a>
                @endif
            </form>
        </div>

        <!-- Documents Table -->
        <div class="documents-table">
            @if($documents->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Document</th>
                        <th>Visibility</th>
                        <th>Signer</th>
                        <th>Status</th>
                        <th>Association</th>
                        <th>Priority</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $doc)
                    <tr>
                        <td>
                            <strong>{{ $doc->display_title }}</strong>
                            @if($doc->is_overdue)
                            <br><span class="overdue-indicator">⚠️ OVERDUE</span>
                            @endif
                        </td>
                        <td>
                            <span class="visibility-badge {{ $doc->visibility_badge['class'] }}" 
                                  title="{{ $doc->visibility_badge['label'] }}">
                                {{ $doc->visibility_badge['icon'] }} {{ $doc->visibility_badge['label'] }}
                            </span>
                        </td>
                        <td>
                            {{ $doc->primary_signer_email ?? 'N/A' }}
                            @if($doc->signer_count > 1)
                            <br><small>(+{{ $doc->signer_count - 1 }} more)</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge-status badge-{{ $doc->status }}">
                                {{ ucfirst($doc->status) }}
                            </span>
                        </td>
                        <td>
                            @if($doc->documentable)
                                @if($doc->documentable_type === 'App\Models\Admin')
                                <a href="{{ route('admin.client.detail', $doc->documentable_id) }}" class="association-chip">
                                    <i class="fas fa-user"></i>
                                    Client: {{ $doc->documentable->first_name }} {{ $doc->documentable->last_name }}
                                </a>
                                @elseif($doc->documentable_type === 'App\Models\Lead')
                                <a href="{{ route('admin.lead.detail', $doc->documentable_id) }}" class="association-chip">
                                    <i class="fas fa-user-tag"></i>
                                    Lead: {{ $doc->documentable->first_name }} {{ $doc->documentable->last_name }}
                                </a>
                                @endif
                            @else
                                <span style="color: #6c757d; font-size: 12px;">Ad-hoc</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-priority {{ $doc->priority }}">
                                {{ ucfirst($doc->priority) }}
                            </span>
                        </td>
                        <td>
                            {{ $doc->created_at->format('M d, Y') }}<br>
                            <small style="color: #6c757d;">{{ $doc->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <a href="{{ route('admin.signatures.show', $doc->id) }}" class="btn btn-action btn-view">
                                <i class="fas fa-eye"></i> View
                            </a>
                            @if($doc->status === 'sent')
                            <a href="{{ route('admin.signatures.reminder', $doc->id) }}" 
                               onclick="event.preventDefault(); document.getElementById('remind-form-{{ $doc->id }}').submit();"
                               class="btn btn-action btn-remind">
                                <i class="fas fa-bell"></i> Remind
                            </a>
                            <form id="remind-form-{{ $doc->id }}" action="{{ route('admin.signatures.reminder', $doc->id) }}" method="POST" style="display: none;">
                                @csrf
                                <input type="hidden" name="signer_id" value="{{ $doc->signers->first()->id ?? '' }}">
                            </form>
                            @endif
                            
                            @if($doc->status === 'signed' && !$doc->documentable)
                            <button type="button" class="btn btn-action btn-attach" onclick="openAttachModal({{ $doc->id }}, {{ json_encode($doc->display_title) }})">
                                <i class="fas fa-link"></i> Attach
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Pagination -->
            <div style="margin-top: 20px;">
                {{ $documents->appends(request()->query())->links() }}
            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>No documents found</h3>
                <p>Start by sending a new document for signature</p>
                <a href="{{ route('admin.signatures.create') }}" class="btn-primary-custom" style="margin-top: 20px;">
                    <i class="fas fa-plus"></i> Send New Document
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Attach Document Modal -->
<div class="modal fade" id="attachModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="attachForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-link"></i> Attach Document to Client/Lead
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Document</label>
                        <input type="text" id="attachDocTitle" class="form-control" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Attach To <span style="color: #dc3545;">*</span></label>
                        <select class="form-control" id="attachEntityType" name="entity_type" required>
                            <option value="">-- Select Type --</option>
                            <option value="client">Client</option>
                            <option value="lead">Lead</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="entitySelectGroup" style="display: none;">
                        <label id="entitySelectLabel">Select Entity <span style="color: #dc3545;">*</span></label>
                        <select class="form-control" id="attachEntityId" name="entity_id" required>
                            <option value="">-- Select --</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Note (Optional)</label>
                        <textarea class="form-control" name="note" rows="3" placeholder="Add a note about this attachment..."></textarea>
                        <small class="form-text text-muted">This note will appear in the audit trail</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Attach Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const clients = @json(\App\Models\Admin::where('role', '!=', 7)->get(['id', 'first_name', 'last_name', 'email']));
const leads = @json(\App\Models\Lead::get(['id', 'first_name', 'last_name', 'email']));

let currentDocId = null;

function openAttachModal(docId, docTitle) {
    currentDocId = docId;
    document.getElementById('attachDocTitle').value = docTitle;
    document.getElementById('attachEntityType').value = '';
    document.getElementById('entitySelectGroup').style.display = 'none';
    document.getElementById('attachEntityId').innerHTML = '<option value="">-- Select --</option>';
    document.getElementById('attachForm').action = '{{ url("/admin/signatures") }}/' + docId + '/associate';
    $('#attachModal').modal('show');
}

document.getElementById('attachEntityType').addEventListener('change', function() {
    const type = this.value;
    const selectGroup = document.getElementById('entitySelectGroup');
    const select = document.getElementById('attachEntityId');
    const label = document.getElementById('entitySelectLabel');
    
    if (!type) {
        selectGroup.style.display = 'none';
        return;
    }
    
    selectGroup.style.display = 'block';
    label.textContent = 'Select ' + (type === 'client' ? 'Client' : 'Lead') + ' ';
    
    const data = type === 'client' ? clients : leads;
    select.innerHTML = '<option value="">-- Select --</option>';
    
    data.forEach(item => {
        const option = document.createElement('option');
        option.value = item.id;
        option.textContent = `${item.first_name} ${item.last_name} (${item.email})`;
        select.appendChild(option);
    });
});
</script>
@endsection

