@extends('layouts.crm_client_detail')
@section('title', ucfirst($type) . ' Calendar - Website Bookings')

@section('content')

<link rel="stylesheet" href="{{URL::asset('css/fullcalendar.min.css')}}">

<style>
html, body {
    overflow-x: hidden !important;
    max-width: 100% !important;
}

/* Calendar styling */
#calendar {
    max-width: 100%;
    margin: 0 auto;
}

.fc-event {
    cursor: pointer;
    border-radius: 3px;
    padding: 2px 5px;
    white-space: nowrap;
    overflow: visible;
    text-overflow: ellipsis;
    min-width: 120px;
}

.fc-event:hover {
    opacity: 0.8;
}

.fc-event .fc-title {
    white-space: nowrap;
    overflow: visible;
    text-overflow: ellipsis;
    max-width: none;
}

/* Ensure calendar cells have enough space */
.fc-day-grid-event {
    margin: 1px 2px 0;
    white-space: nowrap;
}

.fc-time-grid-event {
    white-space: nowrap;
}

/* Status colors for calendar events */
.event-pending {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.event-confirmed {
    background-color: #28a745;
    border-color: #28a745;
    color: #fff;
}

.event-completed {
    background-color: #17a2b8;
    border-color: #17a2b8;
    color: #fff;
}

.event-cancelled {
    background-color: #dc3545;
    border-color: #dc3545;
    color: #fff;
    text-decoration: line-through;
}

.event-no-show {
    background-color: #6c757d;
    border-color: #6c757d;
    color: #fff;
    opacity: 0.7;
}

/* Paid appointment color - blue (overrides status colors) */
.event-paid {
    background-color: #007bff !important;
    border-color: #007bff !important;
    color: #fff !important;
}

.calendar-legend {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 3px;
}

.calendar-stats {
    display: flex;
    justify-content: space-around;
    margin-bottom: 20px;
}

.stat-box {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
}

.stat-box h3 {
    margin: 0;
    font-size: 2rem;
    color: #007bff;
}

.stat-box p {
    margin: 5px 0 0 0;
    color: #6c757d;
}

/* Fix navigation button contrast - Ensure proper visibility */
.btn-outline-primary {
    color: #007bff !important;
    border-color: #007bff !important;
    background-color: transparent !important;
    font-weight: 500 !important;
}

.btn-outline-primary:hover {
    color: #fff !important;
    background-color: #007bff !important;
    border-color: #007bff !important;
}

.btn-outline-primary:focus {
    color: #007bff !important;
    border-color: #007bff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

.btn-outline-primary:active {
    color: #fff !important;
    background-color: #007bff !important;
    border-color: #007bff !important;
}

/* Additional specificity for calendar navigation buttons */
.btn-group .btn-outline-primary {
    color: #007bff !important;
    border-color: #007bff !important;
    background-color: transparent !important;
}

.btn-group .btn-outline-primary:hover {
    color: #fff !important;
    background-color: #007bff !important;
    border-color: #007bff !important;
}

/* Override Bootstrap CSS variables for better visibility */
.btn-outline-primary {
    --bs-btn-color: #007bff !important;
    --bs-btn-border-color: #007bff !important;
    --bs-btn-hover-color: #fff !important;
    --bs-btn-hover-bg: #007bff !important;
    --bs-btn-hover-border-color: #007bff !important;
}
</style>

<div class="section-body">
    <div class="row">
        <div class="col-12">
            <!-- Back and Calendar Type Navigation -->
            <div class="mb-3">
                <a href="{{ route('booking.appointments.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                <div class="btn-group ml-2" role="group">
                    <a href="{{ route('booking.appointments.calendar', ['type' => 'paid']) }}" 
                       class="btn btn-sm {{ $type === 'paid' ? 'btn-primary' : 'btn-outline-primary' }}" 
                       style="{{ $type === 'paid' ? '' : 'color: #007bff !important; border-color: #007bff !important;' }}">
                        <i class="far fa-calendar-check"></i> Pr_complex matters
                    </a>
                    <a href="{{ route('booking.appointments.calendar', ['type' => 'jrp']) }}" 
                       class="btn btn-sm {{ $type === 'jrp' ? 'btn-primary' : 'btn-outline-primary' }}" 
                       style="{{ $type === 'jrp' ? '' : 'color: #007bff !important; border-color: #007bff !important;' }}">
                        <i class="far fa-calendar"></i> JRP
                    </a>
                    <a href="{{ route('booking.appointments.calendar', ['type' => 'education']) }}" 
                       class="btn btn-sm {{ $type === 'education' ? 'btn-primary' : 'btn-outline-primary' }}" 
                       style="{{ $type === 'education' ? '' : 'color: #007bff !important; border-color: #007bff !important;' }}">
                        <i class="fas fa-graduation-cap"></i> Education
                    </a>
                    <a href="{{ route('booking.appointments.calendar', ['type' => 'tourist']) }}" 
                       class="btn btn-sm {{ $type === 'tourist' ? 'btn-primary' : 'btn-outline-primary' }}" 
                       style="{{ $type === 'tourist' ? '' : 'color: #007bff !important; border-color: #007bff !important;' }}">
                        <i class="fas fa-plane"></i> Tourist
                    </a>
                    <a href="{{ route('booking.appointments.calendar', ['type' => 'adelaide']) }}" 
                       class="btn btn-sm {{ $type === 'adelaide' ? 'btn-primary' : 'btn-outline-primary' }}" 
                       style="{{ $type === 'adelaide' ? '' : 'color: #007bff !important; border-color: #007bff !important;' }}">
                        <i class="fas fa-city"></i> Adelaide
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>
                        <i class="fas fa-calendar-alt mr-2"></i>
                        {{ ucfirst($type) }} Calendar
                        <small class="text-muted">(Website Bookings)</small>
                    </h4>
                    <div class="card-header-action">
                        <button onclick="location.reload()" class="btn btn-sm btn-info">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Stats -->
                    <div class="calendar-stats">
                        <div class="stat-box">
                            <h3>{{ $stats['this_month'] ?? 0 }}</h3>
                            <p>This Month</p>
                        </div>
                        <div class="stat-box">
                            <h3>{{ $stats['today'] ?? 0 }}</h3>
                            <p>Today</p>
                        </div>
                        <div class="stat-box">
                            <h3>{{ $stats['upcoming'] ?? 0 }}</h3>
                            <p>Upcoming</p>
                        </div>
                        <div class="stat-box">
                            <h3>{{ $stats['pending'] ?? 0 }}</h3>
                            <p>Pending</p>
                        </div>
                        <div class="stat-box">
                            <h3>{{ $stats['no_show'] ?? 0 }}</h3>
                            <p>No Show</p>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="calendar-legend">
                        <div class="legend-item">
                            <div class="legend-color event-pending"></div>
                            <span>Pending</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color event-confirmed"></div>
                            <span>Confirmed</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color event-completed"></div>
                            <span>Completed</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color event-cancelled"></div>
                            <span>Cancelled</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color event-no-show"></div>
                            <span>No Show</span>
                        </div>
                    </div>

                    <!-- Calendar -->
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Detail Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appointment Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <a href="#" id="viewFullDetails" class="btn btn-primary" target="_blank">View Full Details</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="{{URL::asset('js/moment.min.js')}}"></script>
<script src="{{URL::asset('js/fullcalendar.min.js')}}"></script>

<script>
// Ensure jQuery is loaded
if (typeof jQuery === 'undefined') {
    console.error('jQuery is required but not loaded!');
}

$(document).ready(function() {
    console.log('Initializing calendar with jQuery...');
    
    var calendarEl = $('#calendar');
    if (!calendarEl.length) {
        console.error('Calendar element not found!');
        return;
    }
    
    // Use v3.x jQuery plugin syntax instead of v4+ class syntax
    calendarEl.fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        defaultView: 'month',
        editable: false,
        selectable: false,
        dayMaxEvents: true,
        navLinks: true,
        timeFormat: 'h:mm A',
        
        // Fetch events from server
        events: function(start, end, timezone, callback) {
            console.log('Loading events...');
            
            $.ajax({
                url: '{{ route("booking.api.appointments") }}',
                method: 'GET',
                data: {
                    type: '{{ $type }}',
                    start: start.format('YYYY-MM-DD'),
                    end: end.format('YYYY-MM-DD'),
                    format: 'calendar'
                },
                success: function(response) {
                    console.log('API Response:', response);
                    
                    if (response.success && response.data) {
                        var events = response.data.map(function(appointment) {
                            var endTime = moment(appointment.appointment_datetime)
                                .add(appointment.duration_minutes || 15, 'minutes');
                            
                            // Create a more readable title with proper truncation handling
                            // Format meeting_type for display (e.g., 'in_person' -> 'In Person')
                            var meetingType = appointment.meeting_type || 'N/A';
                            var meetingTypeDisplay = meetingType !== 'N/A' 
                                ? meetingType.split('_').map(function(word) {
                                    return word.charAt(0).toUpperCase() + word.slice(1);
                                }).join(' ')
                                : 'N/A';
                            var clientName = appointment.client_name || 'Unknown Client';
                            var title = clientName + ' (' + meetingTypeDisplay + ')';
                            
                            // Determine className: If paid, add 'event-paid' class
                            // Handle different data types: boolean, integer (1/0), string ('1'/'0', 'true'/'false')
                            // JSON may convert PHP boolean true to integer 1
                            const isPaid = appointment.is_paid === true || appointment.is_paid === 1 || appointment.is_paid === '1' || 
                                          appointment.is_paid === 'true' || String(appointment.is_paid).toLowerCase() === 'true';
                            const className = 'event-' + appointment.status + (isPaid ? ' event-paid' : '');
                            
                            return {
                                id: appointment.id,
                                title: title,
                                start: appointment.appointment_datetime,
                                end: endTime.format('YYYY-MM-DD HH:mm:ss'),
                                className: className,
                                client_name: clientName,
                                client_email: appointment.client_email,
                                client_phone: appointment.client_phone,
                                service_type: serviceType,
                                status: appointment.status,
                                location: appointment.location,
                                consultant: appointment.consultant ? appointment.consultant.name : 'Not Assigned',
                                payment_status: appointment.is_paid ? 'Paid' : 'Free',
                                final_amount: appointment.final_amount || 0
                            };
                        });
                        console.log('Processed events:', events);
                        callback(events);
                    } else {
                        console.log('No data received');
                        callback([]);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    callback([]);
                    alert('Failed to load appointments: ' + error);
                }
            });
        },
        
        // Event click handler
        eventClick: function(event, jsEvent, view) {
            console.log('Event clicked:', event);
            
            var modalBody = `
                <div class="appointment-details">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Client:</strong> ${event.client_name}</p>
                            <p><strong>Email:</strong> ${event.client_email}</p>
                            <p><strong>Phone:</strong> ${event.client_phone}</p>
                            <p><strong>Service:</strong> ${event.service_type}</p>
                            <p><strong>Date & Time:</strong> ${moment(event.start).format('DD MMM YYYY, hh:mm A')}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Location:</strong> ${event.location}</p>
                            <p><strong>Consultant:</strong> ${event.consultant}</p>
                            <p><strong>Status:</strong> <span class="badge badge-${getStatusClass(event.status)}" id="statusBadge">${event.status.toUpperCase()}</span></p>
                            <p><strong>Payment:</strong> <span class="badge badge-${event.payment_status === 'Paid' ? 'success' : 'secondary'}">${event.payment_status}</span></p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Action Controls -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-edit"></i> Change Status</h6>
                            <div class="btn-group-vertical w-100" role="group">
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="updateAppointmentStatus(${event.id}, 'confirmed')">
                                    <i class="fas fa-check"></i> Mark as Confirmed
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="updateAppointmentStatus(${event.id}, 'completed')">
                                    <i class="fas fa-check-circle"></i> Mark as Complete
                                </button>
                                ${event.final_amount && parseFloat(event.final_amount) > 0 ? `
                                <button type="button" class="btn btn-sm btn-outline-info" onclick="updateAppointmentStatus(${event.id}, 'paid')">
                                    <i class="fas fa-dollar-sign"></i> Mark As Payment Done
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="updateAppointmentStatus(${event.id}, 'pending')">
                                    <i class="fas fa-clock"></i> Mark As Payment Pending
                                </button>
                                ` : ''}
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="updateAppointmentStatus(${event.id}, 'cancelled')">
                                    <i class="fas fa-times"></i> Mark as Cancelled
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateAppointmentStatus(${event.id}, 'no_show')">
                                    <i class="fas fa-user-times"></i> Mark as No Show
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-exchange-alt"></i> Change Calendar Type</h6>
                            <div class="form-group">
                                <select class="form-control form-control-sm" id="consultantSelect" onchange="updateAppointmentConsultant(${event.id}, this.value)">
                                    <option value="">Select Consultant...</option>
                                    <option value="1" ${event.consultant.includes('Arun') ? 'selected' : ''}>Arun Kumar (Pr_complex matters)</option>
                                    <option value="2" ${event.consultant.includes('Shubham') ? 'selected' : ''}>Shubham/Yadwinder (JRP)</option>
                                    <option value="3" ${event.consultant.includes('Education') ? 'selected' : ''}>Education Team</option>
                                    <option value="4" ${event.consultant.includes('Tourist') ? 'selected' : ''}>Tourist Visa Team</option>
                                    <option value="5" ${event.consultant.includes('Adelaide') ? 'selected' : ''}>Adelaide Office</option>
                                </select>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Changing consultant will move this appointment to the selected calendar type.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('#eventModalBody').html(modalBody);
            $('#viewFullDetails').attr('href', '/booking/appointments/' + event.id);
            $('#eventModal').modal('show');
        }
    });
    
    console.log('Calendar initialized successfully');
    
    // Helper function for status badge class
    function getStatusClass(status) {
        switch(status) {
            case 'pending': return 'warning';
            case 'paid': return 'info';
            case 'confirmed': return 'success';
            case 'completed': return 'info';
            case 'cancelled': return 'danger';
            case 'no_show': return 'dark';
            case 'rescheduled': return 'primary';
            default: return 'secondary';
        }
    }
    
    // Global functions for modal actions
    window.updateAppointmentStatus = function(appointmentId, newStatus) {
        if (!confirm(`Are you sure you want to change the status to "${newStatus}"?`)) {
            return;
        }
        
        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        button.disabled = true;
        
        $.ajax({
            url: `/booking/appointments/${appointmentId}/update-status`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                status: newStatus
            },
            success: function(data) {
                if (data.success) {
                    // Update the status badge in the modal
                    const statusBadge = document.getElementById('statusBadge');
                    if (statusBadge) {
                        statusBadge.textContent = newStatus.toUpperCase();
                        statusBadge.className = `badge badge-${getStatusClass(newStatus)}`;
                    }
                    
                    // Close the modal and refresh calendar
                    $('#eventModal').modal('hide');
                    $('#myEvent').fullCalendar('refetchEvents');
                    
                    // Show success message
                    showAlert('success', 'Status updated successfully!');
                } else {
                    showAlert('danger', 'Failed to update status: ' + (data.message || 'Unknown error'));
                }
            },
            error: function(xhr) {
                console.error('Error updating status:', xhr.responseText);
                showAlert('danger', 'Failed to update status. Please try again.');
            },
            complete: function() {
                // Restore button state
                button.innerHTML = originalText;
                button.disabled = false;
            }
        });
    };
    
    window.updateAppointmentConsultant = function(appointmentId, consultantId) {
        if (!consultantId) {
            return;
        }
        
        if (!confirm('Are you sure you want to change the consultant? This will move the appointment to a different calendar.')) {
            // Reset the select to previous value
            const select = document.getElementById('consultantSelect');
            select.value = '';
            return;
        }
        
        // Show loading state
        const select = document.getElementById('consultantSelect');
        const originalValue = select.value;
        select.disabled = true;
        
        $.ajax({
            url: `/booking/appointments/${appointmentId}/update-consultant`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                consultant_id: consultantId
            },
            success: function(data) {
                if (data.success) {
                    // Close the modal and refresh calendar
                    $('#eventModal').modal('hide');
                    $('#myEvent').fullCalendar('refetchEvents');
                    
                    // Show success message
                    showAlert('success', 'Consultant updated successfully! The appointment has been moved to the new calendar.');
                } else {
                    showAlert('danger', 'Failed to update consultant: ' + (data.message || 'Unknown error'));
                    select.value = originalValue;
                }
            },
            error: function(xhr) {
                console.error('Error updating consultant:', xhr.responseText);
                showAlert('danger', 'Failed to update consultant. Please try again.');
                select.value = originalValue;
            },
            complete: function() {
                select.disabled = false;
            }
        });
    };
    
    function showAlert(type, message) {
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;
        
        // Insert at the top of the page
        const container = document.querySelector('.section-body');
        if (container) {
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    }
});
</script>

@endsection

