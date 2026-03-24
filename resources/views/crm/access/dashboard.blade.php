@extends('layouts.crm_client_detail')
@section('title', 'Access grants dashboard')
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h4 class="mb-0">Cross-access grants</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('crm.access.queue') }}" class="btn btn-sm btn-outline-primary">Pending queue</a>
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary">Main dashboard</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="crm-access-dash-filters" class="form-row align-items-end mb-3">
                        <div class="form-group col-md-2 col-sm-6">
                            <label class="small text-muted">Staff ID</label>
                            <input type="number" name="staff_id" class="form-control form-control-sm" min="1" placeholder="Staff #">
                        </div>
                        <div class="form-group col-md-2 col-sm-6">
                            <label class="small text-muted">Record (admin) ID</label>
                            <input type="number" name="admin_id" class="form-control form-control-sm" min="1" placeholder="Client/lead #">
                        </div>
                        <div class="form-group col-md-2 col-sm-6">
                            <label class="small text-muted">From</label>
                            <input type="date" name="date_from" class="form-control form-control-sm">
                        </div>
                        <div class="form-group col-md-2 col-sm-6">
                            <label class="small text-muted">To</label>
                            <input type="date" name="date_to" class="form-control form-control-sm">
                        </div>
                        <div class="form-group col-md-2 col-sm-6">
                            <label class="small text-muted">Office</label>
                            <select name="office_id" class="form-control form-control-sm">
                                <option value="">Any</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}">{{ $b->office_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2 col-sm-6">
                            <label class="small text-muted">Team</label>
                            <select name="team_id" class="form-control form-control-sm">
                                <option value="">Any</option>
                                @foreach($teams as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2 col-sm-6">
                            <label class="small text-muted">Grant type</label>
                            <select name="grant_type" class="form-control form-control-sm">
                                <option value="">Any</option>
                                <option value="quick">Quick</option>
                                <option value="supervisor_approved">Supervisor approved</option>
                                <option value="exempt">Exempt</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2 col-sm-6">
                            <label class="small text-muted">Status</label>
                            <select name="status" class="form-control form-control-sm">
                                <option value="">Any</option>
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="rejected">Rejected</option>
                                <option value="expired">Expired</option>
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
                                    <th>Office/team</th>
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
    var dataUrl = @json($dataUrl);
    var exportUrl = @json($exportUrl);
    var form = document.getElementById('crm-access-dash-filters');
    var msg = document.getElementById('crm-access-dash-msg');
    var exportLink = document.getElementById('crm-access-dash-export');

    function showMsg(text, isErr) {
        msg.textContent = text;
        msg.className = 'alert mb-3 ' + (isErr ? 'alert-danger' : 'alert-info');
        msg.classList.remove('d-none');
    }

    function queryString() {
        var fd = new FormData(form);
        var p = new URLSearchParams();
        fd.forEach(function (v, k) {
            if (v !== null && String(v).trim() !== '') p.append(k, v);
        });
        return p.toString();
    }

    function updateExportHref() {
        var qs = queryString();
        exportLink.href = exportUrl + (qs ? ('?' + qs) : '');
    }

    function load() {
        updateExportHref();
        var url = dataUrl + (queryString() ? ('?' + queryString()) : '');
        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
            .then(function (x) {
                if (!x.ok) {
                    showMsg(x.j.message || 'Failed to load', true);
                    return;
                }
                msg.classList.add('d-none');
                var d = x.j;
                document.querySelector('[data-field="pending_count"]').textContent = d.pending_count;
                document.querySelector('[data-field="active_count"]').textContent = d.active_count;
                var f = d.filters || {};
                document.querySelector('[data-field="matching_rows"]').textContent = f.matching_rows;
                document.querySelector('[data-field="distinct_records"]').textContent = f.distinct_records;
                document.querySelector('[data-field="type_split"]').textContent =
                    (f.grant_type_quick || 0) + ' / ' + (f.grant_type_supervisor_approved || 0) + ' / ' + (f.grant_type_exempt || 0);

                var tb = document.querySelector('#crm-access-dash-table tbody');
                tb.innerHTML = '';
                (d.rows || []).forEach(function (g) {
                    var st = g.staff ? (g.staff.first_name + ' ' + g.staff.last_name).trim() : ('#' + g.staff_id);
                    var ad = g.admin ? (g.admin.first_name + ' ' + g.admin.last_name).trim() : ('#' + g.admin_id);
                    var ot = '';
                    if (g.office_id) ot += 'O' + g.office_id;
                    if (g.team_id) ot += (ot ? ' ' : '') + 'T' + g.team_id;
                    var note = (g.requester_note || g.quick_reason_code || '').toString().replace(/</g, '&lt;');
                    var tr = document.createElement('tr');
                    tr.innerHTML =
                        '<td>' + g.id + '</td>' +
                        '<td class="text-nowrap small">' + (g.created_at || '') + '</td>' +
                        '<td class="small">' + st + '</td>' +
                        '<td class="small">' + ad + ' <span class="text-muted">(' + g.record_type + ' #' + g.admin_id + ')</span></td>' +
                        '<td class="small">' + g.grant_type + '</td>' +
                        '<td class="small">' + g.status + '</td>' +
                        '<td class="small">' + (ot || '—') + '</td>' +
                        '<td class="small">' + note + '</td>';
                    tb.appendChild(tr);
                });
            })
            .catch(function () { showMsg('Failed to load dashboard.', true); });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        load();
    });
    document.getElementById('crm-access-dash-reset').addEventListener('click', function () {
        form.reset();
        load();
    });

    document.addEventListener('DOMContentLoaded', load);
})();
</script>
@endpush
