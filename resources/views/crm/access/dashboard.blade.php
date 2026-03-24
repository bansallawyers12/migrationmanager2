@extends('layouts.crm_client_detail')
@section('title', 'Access grants dashboard')
@section('styles')
<style>
    /* Stronger contrast on this page only (tables, filters, badges, headers). */
    #crm-access-pending-card > .card-header,
    #crm-access-grants-card > .card-header {
        background: linear-gradient(135deg, #1a5f8f 0%, #134567 100%) !important;
        color: #fff !important;
    }

    #crm-access-pending-card .card-header h5,
    #crm-access-grants-card .card-header h4 {
        color: #fff !important;
    }

    #crm-access-grants-card .card-header .btn-secondary {
        background-color: #0d3d5c !important;
        border: 1px solid rgba(255, 255, 255, 0.95) !important;
        color: #fff !important;
    }

    #crm-access-grants-card .card-header .btn-secondary:hover,
    #crm-access-grants-card .card-header .btn-secondary:focus {
        background-color: #0a3048 !important;
        border-color: #fff !important;
        color: #fff !important;
    }

    #crm-pending-table thead th,
    #crm-access-dash-table thead th {
        background-color: #e9ecef !important;
        color: #212529 !important;
        border-color: #ced4da !important;
        font-weight: 600 !important;
        font-size: 0.8125rem !important;
        letter-spacing: 0.02em;
    }

    #crm-access-dash-filters label.small {
        color: #343a40 !important;
        font-weight: 600 !important;
    }

    #crm-access-dash-filters .form-control::placeholder {
        color: #5c636a !important;
        opacity: 1;
    }

    #crm-access-dash-summary .text-muted.small {
        color: #495057 !important;
    }

    #crm-access-dash-summary [data-field] {
        color: #212529 !important;
        font-weight: 600;
    }

    #crm-access-dash-table .badge-success,
    #crm-pending-table .badge-success {
        background-color: #146c43 !important;
        color: #fff !important;
    }

    #crm-access-dash-table .badge-warning {
        background-color: #b45309 !important;
        color: #fff !important;
    }

    #crm-access-pending-card tbody .text-muted,
    #crm-access-dash-table tbody .text-muted {
        color: #495057 !important;
    }

    #crm-access-pending-card a.small.text-muted {
        color: #0b5ed7 !important;
        text-decoration: underline;
    }

    #crm-access-pending-card a.small.text-muted:hover {
        color: #084298 !important;
    }
</style>
@endsection
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-body">

            {{-- ── Pending approvals section (always visible at the top for approvers) ──────── --}}
            <div class="card border-warning mb-4" id="crm-access-pending-card">
                <div class="card-header d-flex justify-content-between align-items-center bg-warning text-white py-2">
                    <h5 class="mb-0">
                        <i class="fas fa-clock mr-2"></i>
                        Pending approvals
                        <span class="badge badge-light text-warning ml-2" id="crm-pending-badge">—</span>
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-light" id="crm-pending-refresh" title="Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-light" id="crm-pending-toggle" data-collapsed="0" title="Collapse">
                            <i class="fas fa-chevron-up"></i>
                        </button>
                    </div>
                </div>
                <div id="crm-pending-body">
                    <div class="card-body p-2">
                        <div id="crm-pending-msg" class="alert d-none mb-2" role="alert"></div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0" id="crm-pending-table">
                                <thead>
                                    <tr>
                                        <th>Requested</th>
                                        <th>Requester</th>
                                        <th>Record</th>
                                        <th>Office / team</th>
                                        <th>Note</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody><tr><td colspan="6" class="text-center text-muted py-3">Loading…</td></tr></tbody>
                            </table>
                        </div>
                        <div class="mt-2 text-right">
                            <a href="{{ route('crm.access.queue') }}" class="small text-muted">View full queue page →</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Filtered grants table ───────────────────────────────────────────────────── --}}
            <div class="card" id="crm-access-grants-card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h4 class="mb-0">All grants (filterable)</h4>
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary">Main dashboard</a>
                </div>
                <div class="card-body">
                    <form id="crm-access-dash-filters" class="form-row align-items-end mb-3">
                        <div class="form-group col-md-2 col-sm-6">
                            <label class="small" for="crm-access-filter-staff">Staff ID</label>
                            <input type="number" name="staff_id" id="crm-access-filter-staff" class="form-control form-control-sm" min="1" placeholder="Staff #">
                        </div>
                        <div class="form-group col-md-2 col-sm-6">
                            <label class="small" for="crm-access-filter-admin">Record (admin) ID</label>
                            <input type="number" name="admin_id" id="crm-access-filter-admin" class="form-control form-control-sm" min="1" placeholder="Client/lead #">
                        </div>
                        <div class="form-group col-md-2 col-sm-6">
                            <label class="small" for="crm-access-filter-from">From</label>
                            <input type="date" name="date_from" id="crm-access-filter-from" class="form-control form-control-sm">
                        </div>
                        <div class="form-group col-md-2 col-sm-6">
                            <label class="small" for="crm-access-filter-to">To</label>
                            <input type="date" name="date_to" id="crm-access-filter-to" class="form-control form-control-sm">
                        </div>
                        <div class="form-group col-md-2 col-sm-6">
                            <label class="small" for="crm-access-filter-office">Office</label>
                            <select name="office_id" id="crm-access-filter-office" class="form-control form-control-sm">
                                <option value="">Any</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}">{{ $b->office_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2 col-sm-6">
                            <label class="small" for="crm-access-filter-team">Team</label>
                            <select name="team_id" id="crm-access-filter-team" class="form-control form-control-sm">
                                <option value="">Any</option>
                                @foreach($teams as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2 col-sm-6">
                            <label class="small" for="crm-access-filter-grant-type">Grant type</label>
                            <select name="grant_type" id="crm-access-filter-grant-type" class="form-control form-control-sm">
                                <option value="">Any</option>
                                <option value="quick">Quick</option>
                                <option value="supervisor_approved">Supervisor approved</option>
                                <option value="exempt">Exempt</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2 col-sm-6">
                            <label class="small" for="crm-access-filter-status">Status</label>
                            <select name="status" id="crm-access-filter-status" class="form-control form-control-sm">
                                <option value="">Any</option>
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="rejected">Rejected</option>
                                <option value="expired">Expired</option>
                                <option value="revoked">Revoked</option>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="crm-access-dash-reset">Reset</button>
                            <a class="btn btn-sm btn-outline-success" id="crm-access-dash-export" href="{{ $exportUrl }}">Export CSV</a>
                        </div>
                    </form>

                    <div id="crm-access-dash-msg" class="alert d-none mb-3" role="alert"></div>

                    <div class="row mb-3" id="crm-access-dash-summary">
                        <div class="col-md-3 col-6 mb-2">
                            <div class="text-muted small">Global pending</div>
                            <div class="h5 mb-0" data-field="pending_count">—</div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="text-muted small">Global active</div>
                            <div class="h5 mb-0" data-field="active_count">—</div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="text-muted small">Rows (filtered)</div>
                            <div class="h5 mb-0" data-field="matching_rows">—</div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="text-muted small">Distinct records</div>
                            <div class="h5 mb-0" data-field="distinct_records">—</div>
                        </div>
                        <div class="col-md-4 col-6 mb-2">
                            <div class="text-muted small">Quick / supervisor / exempt (filtered)</div>
                            <div class="mb-0" data-field="type_split">—</div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-sm table-bordered" id="crm-access-dash-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>When</th>
                                    <th>Staff</th>
                                    <th>Record</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Granted by</th>
                                    <th>Office / team</th>
                                    <th>Note / reason</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@push('scripts')
<script>
(function () {
    var dataUrl    = @json($dataUrl);
    var exportUrl  = @json($exportUrl);
    var queueUrl   = @json($queueUrl);
    var approveTpl = @json($approveUrlTpl);
    var rejectTpl  = @json($rejectUrlTpl);
    var form       = document.getElementById('crm-access-dash-filters');
    var msg        = document.getElementById('crm-access-dash-msg');
    var exportLink = document.getElementById('crm-access-dash-export');
    var token      = (document.querySelector('meta[name="csrf-token"]') || {}).getAttribute('content') || '';
    var fmtWhen    = typeof window.formatGrantWhen === 'function'
        ? window.formatGrantWhen
        : function (v) { return v == null || v === '' ? '' : String(v); };

    /* ── helpers ────────────────────────────────────────────────────────────── */
    function showMsg(el, text, isErr) {
        el.textContent = text;
        el.className = 'alert mb-3 ' + (isErr ? 'alert-danger' : 'alert-info');
        el.classList.remove('d-none');
    }

    function queryString() {
        var fd = new FormData(form);
        var p = new URLSearchParams();
        fd.forEach(function (v, k) {
            if (v !== null && String(v).trim() !== '') p.append(k, v);
        });
        return p.toString();
    }

    function jsonPost(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(body)
        }).then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); });
    }

    /* ── pending approvals section ──────────────────────────────────────────── */
    function loadPending() {
        var pMsg = document.getElementById('crm-pending-msg');
        var tb   = document.querySelector('#crm-pending-table tbody');
        tb.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-2">Loading…</td></tr>';

        fetch(queueUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var items = data.items || [];
                document.getElementById('crm-pending-badge').textContent = items.length;

                if (items.length === 0) {
                    tb.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3"><i class="fas fa-check-circle text-success mr-1"></i>No pending requests.</td></tr>';
                    return;
                }

                tb.innerHTML = '';
                items.forEach(function (g) {
                    var req  = g.staff ? (g.staff.first_name + ' ' + g.staff.last_name).trim() : ('#' + g.staff_id);
                    var rec  = g.admin ? (g.admin.first_name + ' ' + g.admin.last_name).trim() : ('#' + g.admin_id);
                    var ot   = '';
                    if (g.office_label_snapshot) ot = g.office_label_snapshot;
                    else if (g.office_id) ot = 'Office #' + g.office_id;
                    if (g.team_label_snapshot) ot += (ot ? ' · ' : '') + g.team_label_snapshot;
                    else if (g.team_id) ot += (ot ? ' · ' : '') + 'Team #' + g.team_id;
                    var note = (g.requester_note || '').toString().replace(/</g, '&lt;').slice(0, 200);
                    var tr = document.createElement('tr');
                    tr.setAttribute('data-pending-id', g.id);
                    tr.innerHTML =
                        '<td class="text-nowrap small">' + fmtWhen(g.requested_at) + '</td>' +
                        '<td class="small">' + req + '</td>' +
                        '<td class="small">' + rec + ' <span class="text-muted">(' + g.record_type + ' #' + g.admin_id + ')</span></td>' +
                        '<td class="small">' + (ot || '—') + '</td>' +
                        '<td class="small">' + note + '</td>' +
                        '<td class="text-nowrap">' +
                            '<button type="button" class="btn btn-sm btn-success py-0 px-2 js-pending-approve" data-id="' + g.id + '">Approve</button> ' +
                            '<button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 js-pending-reject" data-id="' + g.id + '">Reject</button>' +
                        '</td>';
                    tb.appendChild(tr);
                });
            })
            .catch(function () {
                tb.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-2">Failed to load pending requests.</td></tr>';
            });
    }

    /* inline approve / reject from pending section */
    document.addEventListener('click', function (e) {
        if (e.target.matches('.js-pending-approve')) {
            var id = e.target.getAttribute('data-id');
            e.target.disabled = true;
            jsonPost(approveTpl.replace('__ID__', id), {})
                .then(function (x) {
                    if (!x.ok) { alert(x.j.message || 'Approve failed'); }
                    loadPending();
                    load(); // refresh main table counts
                })
                .catch(function () { alert('Approve failed'); });
        }
        if (e.target.matches('.js-pending-reject')) {
            var id2 = e.target.getAttribute('data-id');
            var reason = window.prompt('Reject reason (optional):') || '';
            e.target.disabled = true;
            jsonPost(rejectTpl.replace('__ID__', id2), { reason: reason })
                .then(function (x) {
                    if (!x.ok) { alert(x.j.message || 'Reject failed'); }
                    loadPending();
                    load();
                })
                .catch(function () { alert('Reject failed'); });
        }
    });

    /* collapse toggle */
    document.getElementById('crm-pending-refresh').addEventListener('click', loadPending);
    document.getElementById('crm-pending-toggle').addEventListener('click', function () {
        var body = document.getElementById('crm-pending-body');
        var collapsed = this.getAttribute('data-collapsed') === '1';
        body.style.display = collapsed ? '' : 'none';
        this.setAttribute('data-collapsed', collapsed ? '0' : '1');
        this.querySelector('i').className = 'fas fa-chevron-' + (collapsed ? 'up' : 'down');
    });

    /* ── all-grants table ───────────────────────────────────────────────────── */
    function updateExportHref() {
        var qs = queryString();
        exportLink.href = exportUrl + (qs ? ('?' + qs) : '');
    }

    function statusBadge(status) {
        var map = { pending: 'warning', active: 'success', expired: 'secondary', revoked: 'dark', rejected: 'danger' };
        var cls = map[status] || 'secondary';
        return '<span class="badge badge-' + cls + '">' + status + '</span>';
    }

    function load() {
        updateExportHref();
        var url = dataUrl + (queryString() ? ('?' + queryString()) : '');
        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
            .then(function (x) {
                if (!x.ok) {
                    showMsg(msg, x.j.message || 'Failed to load', true);
                    return;
                }
                msg.classList.add('d-none');
                var d = x.j;
                document.querySelector('[data-field="pending_count"]').textContent = d.pending_count;
                document.querySelector('[data-field="active_count"]').textContent  = d.active_count;
                var f = d.filters || {};
                document.querySelector('[data-field="matching_rows"]').textContent    = f.matching_rows;
                document.querySelector('[data-field="distinct_records"]').textContent = f.distinct_records;
                document.querySelector('[data-field="type_split"]').textContent =
                    (f.grant_type_quick || 0) + ' / ' + (f.grant_type_supervisor_approved || 0) + ' / ' + (f.grant_type_exempt || 0);

                /* also update pending badge */
                document.getElementById('crm-pending-badge').textContent = d.pending_count;

                var tb = document.querySelector('#crm-access-dash-table tbody');
                tb.innerHTML = '';
                (d.rows || []).forEach(function (g) {
                    var st   = g.staff ? (g.staff.first_name + ' ' + g.staff.last_name).trim() : ('#' + g.staff_id);
                    var ad   = g.admin ? (g.admin.first_name + ' ' + g.admin.last_name).trim() : ('#' + g.admin_id);
                    var app  = g.approved_by ? (g.approved_by.first_name + ' ' + g.approved_by.last_name).trim() : '';
                    var grantedBy = app;
                    if (!grantedBy && g.grant_type === 'quick') {
                        grantedBy = 'Self (quick access)';
                    }
                    if (!grantedBy && g.grant_type === 'exempt') {
                        grantedBy = 'Role exempt';
                    }
                    if (!grantedBy) {
                        grantedBy = '—';
                    }
                    var ot   = '';
                    if (g.office_label_snapshot) ot = g.office_label_snapshot;
                    else if (g.office_id) ot = 'O' + g.office_id;
                    if (g.team_label_snapshot) ot += (ot ? ' · ' : '') + g.team_label_snapshot;
                    else if (g.team_id) ot += (ot ? ' · ' : '') + 'T' + g.team_id;
                    var note = (g.requester_note || g.quick_reason_code || '').toString().replace(/</g, '&lt;');
                    var tr = document.createElement('tr');
                    tr.innerHTML =
                        '<td class="small">' + g.id + '</td>' +
                        '<td class="text-nowrap small">' + fmtWhen(g.created_at) + '</td>' +
                        '<td class="small">' + st + '</td>' +
                        '<td class="small">' + ad + ' <span class="text-muted">(' + g.record_type + ' #' + g.admin_id + ')</span></td>' +
                        '<td class="small">' + g.grant_type + '</td>' +
                        '<td class="small">' + statusBadge(g.status) + '</td>' +
                        '<td class="small">' + grantedBy + '</td>' +
                        '<td class="small">' + (ot || '—') + '</td>' +
                        '<td class="small">' + note + '</td>';
                    tb.appendChild(tr);
                });
            })
            .catch(function () { showMsg(msg, 'Failed to load dashboard.', true); });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        load();
    });
    document.getElementById('crm-access-dash-reset').addEventListener('click', function () {
        form.reset();
        load();
    });

    document.addEventListener('DOMContentLoaded', function () {
        loadPending();
        load();
    });
})();
</script>
@endpush
