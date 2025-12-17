@extends('layouts.crm_client_detail')
@section('title', ucfirst($type) . ' Calendar - Website Bookings')

@section('content')

{{-- ✅ Load FullCalendar v6 CSS from reliable CDN --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />

@vite(['resources/css/fullcalendar-v6.css'])

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
                       class="btn btn-sm {{ $type === 'paid' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="far fa-calendar-check"></i> Pr_complex matters
                    </a>
                    <a href="{{ route('booking.appointments.calendar', ['type' => 'jrp']) }}" 
                       class="btn btn-sm {{ $type === 'jrp' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="far fa-calendar"></i> JRP
                    </a>
                    <a href="{{ route('booking.appointments.calendar', ['type' => 'education']) }}" 
                       class="btn btn-sm {{ $type === 'education' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-graduation-cap"></i> Education
                    </a>
                    <a href="{{ route('booking.appointments.calendar', ['type' => 'tourist']) }}" 
                       class="btn btn-sm {{ $type === 'tourist' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-plane"></i> Tourist
                    </a>
                    <a href="{{ route('booking.appointments.calendar', ['type' => 'adelaide']) }}" 
                       class="btn btn-sm {{ $type === 'adelaide' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-city"></i> Adelaide
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>
                        <i class="fas fa-calendar-alt mr-2"></i>
                        {{ $calendarTitle }}
                        <small class="text-muted">(Website Bookings - v6)</small>
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
                            <p>Payment Pending</p>
                        </div>
                        <div class="stat-box">
                            <h3>{{ $stats['paid'] ?? 0 }}</h3>
                            <p>Paid</p>
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
                            <span>Payment Pending</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color event-paid"></div>
                            <span>Paid</span>
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
                    <div id="calendar" class="calendar-v6-container"></div>
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

@vite(['resources/js/app.js'])

<script>
// Wait for FullCalendar v6 to be loaded from Vite module
// Vite modules load asynchronously, so we need to wait for it
function waitForFullCalendar(callback, maxAttempts = 50) {
    let attempts = 0;
    
    const checkInterval = setInterval(() => {
        attempts++;
        
        if (typeof FullCalendar !== 'undefined' && FullCalendar.Calendar && 
            typeof FullCalendarPlugins !== 'undefined') {
            clearInterval(checkInterval);
            console.log('✅ FullCalendar v6 detected, initializing calendar...');
            callback();
        } else if (attempts >= maxAttempts) {
            clearInterval(checkInterval);
            console.error('❌ FullCalendar v6 not loaded after waiting. Please rebuild assets: npm run build');
            // Still try to initialize if calendar element exists (graceful degradation)
            const calendarEl = document.getElementById('calendar');
            if (calendarEl) {
                calendarEl.innerHTML = '<div class="alert alert-danger">FullCalendar v6 failed to load. Please refresh the page or rebuild assets.</div>';
            }
        }
    }, 100); // Check every 100ms
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Waiting for FullCalendar v6 to load...');
    
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) {
        console.error('Calendar element not found!');
        return;
    }
    
    // Wait for FullCalendar to be available before initializing
    waitForFullCalendar(function() {
        // Initialize FullCalendar v6
        const calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: [
            FullCalendarPlugins.dayGridPlugin,
            FullCalendarPlugins.timeGridPlugin,
            FullCalendarPlugins.interactionPlugin,
            FullCalendarPlugins.listPlugin
        ],
        
        // Initial view and header
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        
        // Calendar settings
        height: 'auto',
        timeZone: 'Australia/Melbourne',
        firstDay: 1, // Monday
        
        // Event display
        eventDisplay: 'block',
        displayEventTime: true,
        displayEventEnd: false,
        eventMaxStack: 3,
        dayMaxEvents: true,
        moreLinkClick: 'popover',
        
        // Navigation
        navLinks: true,
        nowIndicator: true,
        
        // Time format
        eventTimeFormat: {
            hour: 'numeric',
            minute: '2-digit',
            meridiem: 'short'
        },
        
        // Business hours (optional)
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5], // Monday - Friday
            startTime: '09:00',
            endTime: '17:00',
        },
        
        // Event source - fetch from API
        events: async function(fetchInfo, successCallback, failureCallback) {
            console.log('Loading events for v6...', {
                start: fetchInfo.startStr,
                end: fetchInfo.endStr
            });
            
            try {
                const response = await fetch('{{ route("booking.api.appointments") }}?' + new URLSearchParams({
                    type: '{{ $type }}',
                    start: fetchInfo.startStr,
                    end: fetchInfo.endStr,
                    format: 'calendar'
                }));
                
                const data = await response.json();
                console.log('API Response:', data);
                
                if (!data.success) {
                    console.error('API returned error');
                    failureCallback('Failed to load appointments');
                    return;
                }
                
                // Transform appointments to FullCalendar v6 event format
                const events = data.data.map(apt => {
                    const endTime = moment(apt.appointment_datetime)
                        .add(apt.duration_minutes || 15, 'minutes')
                        .toISOString();
                    
                    // Format meeting_type for display (e.g., 'in_person' -> 'In Person')
                    const meetingTypeDisplay = apt.meeting_type 
                        ? apt.meeting_type.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
                        : 'N/A';
                    
                    // Determine color based on status column only
                    // If status = 'paid' → blue color, if status = 'pending' → pending color, etc.
                    const backgroundColor = getStatusColor(apt.status);
                    const borderColor = getStatusColor(apt.status);
                    const textColor = getStatusTextColor(apt.status);
                    
                    // Temporary debug logging - check browser console
                    if (apt.status === 'paid') {
                        console.log('Paid status appointment detected:', {
                            id: apt.id,
                            name: apt.client_name,
                            status: apt.status,
                            backgroundColor: backgroundColor
                        });
                    }
                    
                    return {
                        id: apt.id,
                        title: `${apt.client_name} (${meetingTypeDisplay})`,
                        start: apt.appointment_datetime,
                        end: endTime,
                        backgroundColor: backgroundColor,
                        borderColor: borderColor,
                        textColor: textColor,
                        classNames: ['event-' + apt.status, apt.status === 'paid' ? 'event-paid' : ''],
                        extendedProps: {
                            client_id: apt.client_id,
                            client_id_encoded: apt.client_id_encoded,
                            client_name: apt.client_name,
                            client_email: apt.client_email,
                            client_phone: apt.client_phone,
                            service_type: apt.service_type,
                            status: apt.status,
                            location: apt.location,
                            meeting_type: apt.meeting_type,
                            preferred_language: apt.preferred_language || 'English',
                            consultant: apt.consultant?.name || 'Not Assigned',
                            is_paid: apt.is_paid,
                            payment_status: apt.is_paid ? 'Paid' : 'Free',
                            final_amount: apt.final_amount,
                            duration_minutes: apt.duration_minutes || 15,
                            appointment_datetime: apt.appointment_datetime,
                            ...(apt.status === 'paid' && { 'data-paid': 'true' })
                        }
                    };
                });
                
                console.log('Processed events:', events.length);
                successCallback(events);
                
            } catch (error) {
                console.error('Error loading events:', error);
                failureCallback(error);
                alert('Failed to load appointments: ' + error.message);
            }
        },
        
        // Event click handler
        eventClick: function(info) {
            console.log('Event clicked:', info.event);
            
            const event = info.event;
            const props = event.extendedProps;
            
            // Format date/time in Australia/Melbourne timezone
            // The ISO datetime string from API is in UTC, we need to convert to Melbourne time
            // Melbourne is UTC+10 (AEST) or UTC+11 (AEDT) - we'll use a fixed offset approach
            const originalDateTime = props.appointment_datetime || event.startStr;
            // Parse the ISO string and create a Date object (JavaScript Date parses ISO as UTC)
            const utcDate = new Date(originalDateTime);
            // Melbourne timezone offset: UTC+11 (AEDT) or UTC+10 (AEST)
            // For simplicity, we'll use the browser's Intl API which handles DST automatically
            const formattedDate = utcDate.toLocaleString('en-AU', {
                timeZone: 'Australia/Melbourne',
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            
            // Calculate duration - use end time if available, otherwise use duration_minutes
            let duration = props.duration_minutes || 15;
            if (event.end) {
                const startTime = event.start.getTime();
                const endTime = event.end.getTime();
                const diffMinutes = Math.round((endTime - startTime) / (1000 * 60));
                if (diffMinutes > 0 && diffMinutes < 1440) { // Valid duration (less than 24 hours)
                    duration = diffMinutes;
                }
            }
            
            // Generate client profile URL if client_id exists
            let clientNameDisplay = props.client_name;
            if (props.client_id_encoded) {
                const clientProfileUrl = `/clients/detail/${props.client_id_encoded}`;
                clientNameDisplay = `<a href="${clientProfileUrl}" target="_blank" style="color: #007bff; text-decoration: underline;">${props.client_name}</a>`;
            }
            
            // Format meeting type for display
            const meetingTypeDisplay = props.meeting_type 
                ? props.meeting_type.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
                : 'N/A';
            
            const modalBody = `
                <div class="appointment-details">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Client:</strong> ${clientNameDisplay}</p>
                            <p><strong>Email:</strong> ${props.client_email}</p>
                            <p><strong>Phone:</strong> ${props.client_phone}</p>
                            <p><strong>Service:</strong> ${props.service_type}</p>
                            <p><strong>Date & Time:</strong> ${formattedDate}</p>
                            <p><strong>Duration:</strong> ${duration} minutes</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Location:</strong> ${props.location ? props.location.charAt(0).toUpperCase() + props.location.slice(1) : 'N/A'}</p>
                            <p><strong>Meeting Type:</strong> ${meetingTypeDisplay}</p>
                            <p><strong>Preferred Language:</strong> ${props.preferred_language ? props.preferred_language.charAt(0).toUpperCase() + props.preferred_language.slice(1).toLowerCase() : 'English'}</p>
                            <p><strong>Consultant:</strong> ${props.consultant}</p>
                            <p><strong>Status:</strong> <span class="badge badge-${getStatusClass(props.status)}" id="statusBadge">${props.status.toUpperCase()}</span></p>
                            <p><strong>Payment:</strong> <span class="badge badge-${props.is_paid ? 'success' : 'secondary'}">${props.payment_status}</span></p>
                            ${props.is_paid ? `<p><strong>Amount:</strong> $${props.final_amount ? parseFloat(props.final_amount).toFixed(2) : '0.00'}</p>` : ''}
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
                                <!-- <button type="button" class="btn btn-sm btn-outline-warning" onclick="updateAppointmentStatus(${event.id}, 'pending')">
                                    <i class="fas fa-clock"></i> Mark as Pending
                                </button> -->
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
                                    <option value="1" ${props.consultant.includes('Arun') ? 'selected' : ''}>Arun Kumar (Pr_complex matters)</option>
                                    <option value="2" ${props.consultant.includes('Shubham') ? 'selected' : ''}>Shubham/Yadwinder (JRP)</option>
                                    <option value="3" ${props.consultant.includes('Education') ? 'selected' : ''}>Education Team</option>
                                    <option value="4" ${props.consultant.includes('Tourist') ? 'selected' : ''}>Tourist Visa Team</option>
                                    <option value="5" ${props.consultant.includes('Adelaide') ? 'selected' : ''}>Adelaide Office</option>
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
            
            document.getElementById('eventModalBody').innerHTML = modalBody;
            document.getElementById('viewFullDetails').href = '/booking/appointments/' + event.id;
            $('#eventModal').modal('show');
        },
        
        // Date click handler (optional - for creating appointments)
        dateClick: function(info) {
            console.log('Date clicked:', info.dateStr);
            // Could open "Create appointment" modal here
        },
        
        // Loading indicator
        loading: function(isLoading) {
            if (isLoading) {
                console.log('Loading calendar events...');
            } else {
                console.log('Calendar events loaded');
            }
        },
        
        // Error handler
        eventDidMount: function(info) {
            // Add tooltip - format time in Australia/Melbourne timezone
            // Use the original ISO datetime string from extendedProps
            const props = info.event.extendedProps;
            const originalDateTime = props.appointment_datetime || info.event.startStr;
            // Parse the ISO string as UTC and convert to Melbourne timezone
            const utcDate = new Date(originalDateTime);
            const formattedTime = utcDate.toLocaleString('en-AU', {
                timeZone: 'Australia/Melbourne',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            $(info.el).tooltip({
                title: info.event.title + ' - ' + formattedTime,
                placement: 'top',
                trigger: 'hover',
                container: 'body'
            });
            
            // Apply blue color with !important for paid status appointments
            if (props.status === 'paid') {
                info.el.style.setProperty('background-color', '#007bff', 'important');
                info.el.style.setProperty('border-color', '#007bff', 'important');
                info.el.style.setProperty('color', '#fff', 'important');
            }
        }
    });
    
        // Render the calendar
        calendar.render();
        console.log('FullCalendar v6 initialized successfully');
        
        // Helper functions
    function getStatusColor(status) {
        const colors = {
            'pending': '#ffc107',
            'paid': '#007bff',
            'confirmed': '#28a745',
            'completed': '#17a2b8',
            'cancelled': '#dc3545',
            'no_show': '#6c757d',
            'rescheduled': '#007bff'
        };
        return colors[status] || '#6c757d';
    }
    
    function getStatusTextColor(status) {
        return status === 'pending' ? '#000' : '#fff';
    }
    
    function getStatusClass(status) {
        const classes = {
            'pending': 'warning',
            'confirmed': 'success',
            'completed': 'info',
            'cancelled': 'danger',
            'no_show': 'dark',
            'rescheduled': 'primary'
        };
        return classes[status] || 'secondary';
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
        
        fetch(`/booking/appointments/${appointmentId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the status badge in the modal
                const statusBadge = document.getElementById('statusBadge');
                if (statusBadge) {
                    statusBadge.textContent = newStatus.toUpperCase();
                    statusBadge.className = `badge badge-${getStatusClass(newStatus)}`;
                }
                
                // Close the modal and refresh calendar
                $('#eventModal').modal('hide');
                calendar.refetchEvents();
                
                // Show success message
                showAlert('success', 'Status updated successfully!');
            } else {
                showAlert('danger', 'Failed to update status: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error updating status:', error);
            showAlert('danger', 'Failed to update status. Please try again.');
        })
        .finally(() => {
            // Restore button state
            button.innerHTML = originalText;
            button.disabled = false;
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
        
        fetch(`/booking/appointments/${appointmentId}/update-consultant`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                consultant_id: consultantId
            })
        })
        .then(response => {
            // Check if response is OK
            if (!response.ok) {
                // Try to parse JSON error response
                return response.json().then(errorData => {
                    throw { status: response.status, data: errorData };
                }).catch(() => {
                    // If not JSON, throw with status
                    throw { status: response.status, message: 'Server error occurred' };
                });
            }
            
            // Check content type before parsing
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response is not JSON');
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Close the modal and refresh calendar
                $('#eventModal').modal('hide');
                calendar.refetchEvents();
                
                // Show success message
                showAlert('success', 'Consultant updated successfully! The appointment has been moved to the new calendar.');
            } else {
                showAlert('danger', 'Failed to update consultant: ' + (data.message || 'Unknown error'));
                select.value = originalValue;
            }
        })
        .catch(error => {
            console.error('Error updating consultant:', error);
            
            // Handle different error types
            if (error.status === 422) {
                // Validation error
                const errorMsg = error.data?.message || 'Validation failed';
                const errors = error.data?.errors || {};
                showAlert('danger', errorMsg);
                if (Object.keys(errors).length > 0) {
                    console.error('Validation errors:', errors);
                }
            } else if (error.status === 404) {
                showAlert('danger', 'Appointment not found');
            } else if (error.status === 500) {
                const errorMsg = error.data?.message || 'Server error occurred';
                showAlert('danger', errorMsg + ' Please try again later.');
            } else if (error instanceof SyntaxError && error.message.includes('JSON')) {
                // JSON parsing error - server returned non-JSON response
                showAlert('danger', 'Server returned invalid response. Please check server logs or try again.');
                console.error('Server returned non-JSON response. Check network tab.');
            } else {
                showAlert('danger', 'Failed to update consultant. Please try again.');
            }
            
            select.value = originalValue;
        })
        .finally(() => {
            select.disabled = false;
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
    }); // Close waitForFullCalendar callback
}); // Close DOMContentLoaded
</script>

<style>
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

/* Additional inline styles for stats and legend */
.calendar-stats {
    display: flex;
    justify-content: space-around;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}

.stat-box {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
    min-width: 120px;
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

/* Legend color styles */
.legend-color.event-pending {
    background-color: #ffc107;
}

.legend-color.event-paid {
    background-color: #007bff;
}

.legend-color.event-confirmed {
    background-color: #28a745;
}

.legend-color.event-completed {
    background-color: #17a2b8;
}

.legend-color.event-cancelled {
    background-color: #dc3545;
}

.legend-color.event-no-show {
    background-color: #6c757d;
}

/* Paid appointment color - blue with !important to override FullCalendar styles */
.fc-event.event-paid,
.fc-event[class*="event-paid"],
.fc-event[data-paid="true"] {
    background-color: #007bff !important;
    border-color: #007bff !important;
    color: #fff !important;
}
</style>

@endsection

