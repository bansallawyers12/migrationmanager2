@extends('layouts.crm_client_detail')
@section('title', 'Website Bookings')

@section('content')

<style>
/* Force prevent horizontal scroll on this page */
html, body {
    overflow-x: hidden !important;
    overflow-y: auto !important;
    max-width: 100% !important;
}

/* Fix for white text color in tables */
.card .card-body table.table {
    --bs-table-color: #000 !important;
    --bs-table-striped-color: #000 !important;
    --bs-table-active-color: #000 !important;
    --bs-table-hover-color: #000 !important;
}

.card .card-body table.table th,
.card .card-body table.table td {
    color: #000 !important;
}

/* Ensure badges are visible */
.card .card-body table.table tbody td .badge {
    color: #fff !important;
}

/* Status badges */
.badge-pending { background-color: #ffc107; }
.badge-confirmed { background-color: #28a745; }
.badge-completed { background-color: #17a2b8; }
.badge-cancelled { background-color: #dc3545; }
.badge-no-show { background-color: #6c757d; }

/* Filter section styling */
.filter-section {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}
</style>

<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>
                        <i class="fas fa-globe mr-2"></i> 
                        Website Bookings 
                        <small class="text-muted">(Synced from Bansal Immigration Website)</small>
                    </h4>
                    <div class="card-header-action">
                        @if(Auth::user() && in_array(Auth::user()->role, [1, 12]))
                        <a href="{{ route('booking.sync.dashboard') }}" class="btn btn-sm btn-info">
                            <i class="fas fa-sync"></i> Sync Status
                        </a>
                        <button onclick="manualSync()" class="btn btn-sm btn-primary">
                            <i class="fas fa-sync-alt"></i> Manual Sync
                        </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-warning">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Pending</h4>
                                    </div>
                                    <div class="card-body">
                                        {{ $stats['pending'] ?? 0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-success">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Confirmed</h4>
                                    </div>
                                    <div class="card-body">
                                        {{ $stats['confirmed'] ?? 0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-info">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Today</h4>
                                    </div>
                                    <div class="card-body">
                                        {{ $stats['today'] ?? 0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-primary">
                                    <i class="fas fa-list"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Total</h4>
                                    </div>
                                    <div class="card-body">
                                        {{ $stats['total'] ?? 0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="filter-section">
                        <form method="GET" action="{{ route('booking.appointments.index') }}" id="filter-form">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Status</label>
                                    <select class="form-control" name="status" id="filter-status">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>No Show</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Consultant</label>
                                    <select class="form-control" name="consultant_id" id="filter-consultant">
                                        <option value="">All Consultants</option>
                                        @foreach($consultants as $consultant)
                                            <option value="{{ $consultant->id }}" {{ request('consultant_id') == $consultant->id ? 'selected' : '' }}>
                                                {{ $consultant->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>From Date</label>
                                    <input type="date" class="form-control" name="date_from" id="filter-date-from" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <label>To Date</label>
                                    <input type="date" class="form-control" name="date_to" id="filter-date-to" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="appointments-table">
                            <thead>
                                <tr>
                                    <th width="50">ID</th>
                                    <th>Client</th>
                                    <th>Appointment</th>
                                    <th>Service</th>
                                    <th>Consultant</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->id }}</td>
                                    <td>
                                        <strong>{{ $appointment->client_name }}</strong><br>
                                        <small>{{ $appointment->client_email }}</small><br>
                                        <small>{{ $appointment->client_phone }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $appointment->appointment_datetime->format('d M Y') }}</strong><br>
                                        <small>{{ $appointment->appointment_datetime->format('h:i A') }}</small><br>
                                        <small><i class="fas fa-map-marker-alt"></i> {{ ucfirst($appointment->location) }}</small>
                                    </td>
                                    <td>
                                        {{ $appointment->service_type ?? 'N/A' }}<br>
                                        <small>{{ $appointment->enquiry_type ?? '' }}</small>
                                    </td>
                                    <td>
                                        @if($appointment->consultant)
                                            {{ $appointment->consultant->name }}
                                        @else
                                            <span class="text-muted">Not Assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = 'secondary';
                                            $statusText = ucfirst($appointment->status);
                                            switch($appointment->status) {
                                                case 'pending': $statusClass = 'warning'; break;
                                                case 'confirmed': $statusClass = 'success'; break;
                                                case 'completed': $statusClass = 'info'; break;
                                                case 'cancelled': $statusClass = 'danger'; break;
                                                case 'no_show': $statusClass = 'dark'; break;
                                            }
                                        @endphp
                                        <span class="badge badge-{{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td>
                                        @if($appointment->is_paid)
                                            <span class="badge badge-success">Paid</span><br>
                                            <small>${{ number_format($appointment->final_amount, 2) }}</small>
                                        @else
                                            <span class="badge badge-secondary">Free</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('booking.appointments.show', $appointment->id) }}" class="btn btn-sm btn-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button onclick="quickAction('{{ $appointment->id }}')" class="btn btn-sm btn-info" title="Quick Actions">
                                            <i class="fas fa-bolt"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <p class="text-muted mt-3 mb-3">
                                            <i class="fas fa-info-circle"></i> No appointments found.
                                        </p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($appointments->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $appointments->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function manualSync() {
    if (!confirm('Start manual sync now? This will fetch latest appointments from Bansal website.')) {
        return;
    }
    
    const hasSweetAlert = typeof Swal !== 'undefined';
    
    if (hasSweetAlert) {
        Swal.fire({
            title: 'Syncing...',
            text: 'Fetching appointments from Bansal website',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    $.ajax({
        url: '{{ route("booking.sync.manual") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (hasSweetAlert) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sync Completed!',
                    html: `
                        <p>Fetched: ${response.stats.fetched}</p>
                        <p>New: ${response.stats.new}</p>
                        <p>Updated: ${response.stats.updated}</p>
                        <p>Failed: ${response.stats.failed}</p>
                    `,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                alert(
                    'Sync completed!\n' +
                    'Fetched: ' + response.stats.fetched + '\n' +
                    'New: ' + response.stats.new + '\n' +
                    'Updated: ' + response.stats.updated + '\n' +
                    'Failed: ' + response.stats.failed
                );
                window.location.reload();
            }
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'An error occurred during sync';
            
            if (hasSweetAlert) {
                Swal.fire({
                    icon: 'error',
                    title: 'Sync Failed',
                    text: message,
                    confirmButtonText: 'OK'
                });
            } else {
                alert('Sync failed: ' + message);
            }
        }
    });
}

function quickAction(appointmentId) {
    // This can open a modal with quick actions
    window.location.href = '{{ url("/booking/appointments") }}/' + appointmentId;
}

// Auto-reload every 5 minutes to get latest synced data
setInterval(function() {
    console.log('Auto-refreshing appointments...');
    window.location.reload();
}, 5 * 60 * 1000);
</script>

@endsection

