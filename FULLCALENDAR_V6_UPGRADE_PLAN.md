# FullCalendar v6 Upgrade Plan

## Current vs. Proposed

### Current (v3.8.0)
- **Year:** 2017 (7 years old)
- **Architecture:** jQuery plugin
- **Bundle:** Monolithic
- **Support:** Deprecated

### Proposed (v6.x)
- **Year:** 2024 (Current)
- **Architecture:** Modern ES6 modules
- **Bundle:** Tree-shakable
- **Support:** Active development

---

## Installation Steps

### 1. Install via npm
```bash
npm install --save @fullcalendar/core @fullcalendar/daygrid @fullcalendar/timegrid @fullcalendar/interaction @fullcalendar/list
```

### 2. Update `resources/js/app.js`
```javascript
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';

window.FullCalendar = { Calendar };
window.FullCalendarPlugins = {
    dayGridPlugin,
    timeGridPlugin,
    interactionPlugin,
    listPlugin
};
```

### 3. Build assets
```bash
npm run build
```

---

## Code Migration

### Current Calendar Code (v3)
```javascript
// resources/views/Admin/booking/appointments/calendar.blade.php
$('#calendar').fullCalendar({
    header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
    },
    events: function(start, end, timezone, callback) {
        $.ajax({
            url: '{{ route("booking.api.appointments") }}',
            data: {
                type: '{{ $type }}',
                start: start.format('YYYY-MM-DD'),
                end: end.format('YYYY-MM-DD'),
            },
            success: function(response) {
                callback(response.data);
            }
        });
    },
    eventClick: function(event) {
        // Handle click
    }
});
```

### Proposed Calendar Code (v6)
```javascript
// resources/views/Admin/booking/appointments/calendar.blade.php
const calendarEl = document.getElementById('calendar');

const calendar = new FullCalendar.Calendar(calendarEl, {
    plugins: [
        FullCalendarPlugins.dayGridPlugin,
        FullCalendarPlugins.timeGridPlugin,
        FullCalendarPlugins.interactionPlugin,
        FullCalendarPlugins.listPlugin
    ],
    initialView: 'dayGridMonth',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
    },
    
    // Event source - modern async/await
    events: async function(fetchInfo) {
        const response = await fetch('{{ route("booking.api.appointments") }}?' + new URLSearchParams({
            type: '{{ $type }}',
            start: fetchInfo.startStr,
            end: fetchInfo.endStr,
            format: 'calendar'
        }));
        
        const data = await response.json();
        
        if (!data.success) {
            console.error('Failed to load appointments');
            return [];
        }
        
        return data.data.map(apt => ({
            id: apt.id,
            title: `${apt.client_name} (${apt.service_type})`,
            start: apt.appointment_datetime,
            end: moment(apt.appointment_datetime).add(apt.duration_minutes || 15, 'minutes').toISOString(),
            backgroundColor: getStatusColor(apt.status),
            borderColor: getStatusColor(apt.status),
            extendedProps: {
                client_name: apt.client_name,
                client_email: apt.client_email,
                client_phone: apt.client_phone,
                service_type: apt.service_type,
                status: apt.status,
                location: apt.location,
                consultant: apt.consultant?.name || 'Not Assigned',
                is_paid: apt.is_paid,
                payment_status: apt.is_paid ? 'Paid' : 'Free'
            }
        }));
    },
    
    // Event click handler
    eventClick: function(info) {
        const event = info.event;
        const props = event.extendedProps;
        
        const modalBody = `
            <div class="appointment-details">
                <p><strong>Client:</strong> ${props.client_name}</p>
                <p><strong>Email:</strong> ${props.client_email}</p>
                <p><strong>Phone:</strong> ${props.client_phone}</p>
                <p><strong>Service:</strong> ${props.service_type}</p>
                <p><strong>Date & Time:</strong> ${moment(event.start).format('DD MMM YYYY, hh:mm A')}</p>
                <p><strong>Location:</strong> ${props.location}</p>
                <p><strong>Consultant:</strong> ${props.consultant}</p>
                <p><strong>Status:</strong> <span class="badge badge-${getStatusClass(props.status)}">${props.status.toUpperCase()}</span></p>
                <p><strong>Payment:</strong> <span class="badge badge-${props.is_paid ? 'success' : 'secondary'}">${props.payment_status}</span></p>
            </div>
        `;
        
        $('#eventModalBody').html(modalBody);
        $('#viewFullDetails').attr('href', '/admin/booking/appointments/' + event.id);
        $('#eventModal').modal('show');
    },
    
    // Better mobile experience
    height: 'auto',
    
    // Better timezone handling
    timeZone: 'Australia/Melbourne',
    
    // Enable date clicking
    dateClick: function(info) {
        console.log('Clicked on: ' + info.dateStr);
        // Could open "Create appointment" modal here
    },
    
    // Loading indicator
    loading: function(isLoading) {
        if (isLoading) {
            console.log('Loading events...');
        } else {
            console.log('Events loaded');
        }
    },
    
    // More event display options
    eventDisplay: 'block',
    displayEventTime: true,
    displayEventEnd: false,
    
    // Better performance for many events
    eventMaxStack: 3,
    dayMaxEvents: true,
    moreLinkClick: 'popover'
});

calendar.render();

// Helper functions
function getStatusColor(status) {
    const colors = {
        'pending': '#ffc107',
        'confirmed': '#28a745',
        'completed': '#17a2b8',
        'cancelled': '#dc3545',
        'no_show': '#6c757d'
    };
    return colors[status] || '#6c757d';
}

function getStatusClass(status) {
    const classes = {
        'pending': 'warning',
        'confirmed': 'success',
        'completed': 'info',
        'cancelled': 'danger',
        'no_show': 'dark'
    };
    return classes[status] || 'secondary';
}
```

---

## New Features You Can Add

### 1. **Resource View** (Multiple Consultants Side-by-Side)
```javascript
import resourceTimelinePlugin from '@fullcalendar/resource-timeline';

// In calendar config:
plugins: [..., resourceTimelinePlugin],
initialView: 'resourceTimelineDay',
resources: [
    { id: '1', title: 'Arun Kumar' },
    { id: '2', title: 'Shubham/Yadwinder' },
    // ...
]
```

### 2. **Drag & Drop Rescheduling**
```javascript
editable: true,
eventDrop: function(info) {
    // Update appointment datetime via AJAX
    updateAppointment(info.event.id, info.event.start);
},
eventResize: function(info) {
    // Update duration
    updateAppointmentDuration(info.event.id, info.event.end);
}
```

### 3. **Better List View**
```javascript
views: {
    listDay: { buttonText: 'Today' },
    listWeek: { buttonText: 'This Week' }
}
```

### 4. **Business Hours**
```javascript
businessHours: {
    daysOfWeek: [1, 2, 3, 4, 5], // Monday - Friday
    startTime: '09:00',
    endTime: '17:00',
}
```

---

## Testing Checklist

- [ ] Calendar renders correctly
- [ ] Events load from API
- [ ] Event click opens modal
- [ ] Navigation (prev/next/today) works
- [ ] View switching works (month/week/day)
- [ ] Mobile responsiveness
- [ ] Status colors display correctly
- [ ] Payment badges show correctly
- [ ] All 5 calendar types work
- [ ] Statistics update correctly
- [ ] Print view works
- [ ] Performance with 100+ events

---

## Rollback Plan

If issues arise:
1. Keep old files: `fullcalendar.min.js.v3.backup`
2. Feature flag in config: `config/app.php`
   ```php
   'use_fullcalendar_v6' => env('USE_FULLCALENDAR_V6', false),
   ```
3. Conditional blade templates
4. Quick rollback: `git revert`

---

## Cost Analysis

### Time Investment
- **Development:** 6-12 hours
- **Testing:** 2-4 hours
- **Documentation:** 1-2 hours
- **Total:** 9-18 hours

### Benefits
- ✅ Modern, maintained codebase
- ✅ Better UX and performance
- ✅ Future-proof (5+ years)
- ✅ New features (drag & drop, resources)
- ✅ Better mobile experience
- ✅ Security updates

### ROI
**High** - One-time investment for 5+ years of benefits

---

## Recommendation

### Should You Upgrade? **YES ✅**

**Why:**
1. v3 is **7 years old** and unsupported
2. Security vulnerabilities won't be patched
3. v6 has **significantly better UX**
4. Modern codebase easier to maintain
5. Better integration with your Vite/Laravel setup
6. Can add advanced features (drag & drop, resources)

**When:**
- **Immediately:** If you have 1-2 days available
- **Soon:** After current sprint (within 1 month)
- **Gradually:** Create v6 version in parallel, test thoroughly, then switch

**Priority:** **Medium-High** (Technical debt + UX improvement)

---

## Implementation Phases

### Phase 1: Setup (1-2 hours)
- Install npm packages
- Configure Vite build
- Test basic calendar rendering

### Phase 2: Migration (4-6 hours)
- Rewrite calendar initialization
- Update event loading
- Update event interactions
- Apply styling

### Phase 3: Testing (2-4 hours)
- Cross-browser testing
- Mobile testing
- Load testing with many events
- User acceptance testing

### Phase 4: Deployment (1-2 hours)
- Deploy to staging
- Monitor for issues
- Deploy to production
- Monitor performance

---

## Alternative: Stay on v3

**Only if:**
- No development time available
- Calendar works perfectly now
- No plans to add features
- Willing to accept security risks

**Risks:**
- Security vulnerabilities
- Browser compatibility issues
- No bug fixes
- Difficult to hire developers familiar with v3

