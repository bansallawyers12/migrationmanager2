# CRM DatePicker Usage Guide

**Version:** 1.0  
**Last Updated:** 2025-01-20  
**Status:** ✅ Active Standard

---

## 📋 Quick Decision Matrix

| Page/Feature | Library to Use | Reason | Action |
|--------------|----------------|---------|--------|
| **Client Detail Page** | `bootstrap-datepicker` | Existing, working | ✅ Don't change |
| **Receipts/Invoices/Ledgers** | `bootstrap-datepicker` | Financial critical | ✅ Don't change |
| **NEW Features** | `CRM_DatePicker` | Standard going forward | ✅ Use this |
| **Lead Forms** | `CRM_DatePicker` | Already implemented | ✅ Keep using |
| **Appointments (old)** | Current system | Legacy | ⚠️ Don't change yet |
| **Dashboard Filters** | `daterangepicker` (via scripts.js) | Working | ✅ Don't change |

---

## 🚀 For Developers: Adding Date Pickers to NEW Code

### Option 1: Simple HTML-Based (Recommended)

Just add a `data-datepicker` attribute - the helper auto-initializes it!

**Standard Date Input:**
```html
<input type="text" 
       class="form-control" 
       data-datepicker="standard"
       placeholder="dd/mm/yyyy"
       name="my_date">
```

**Date of Birth with Age Calculation:**
```html
<div class="row">
    <div class="col-md-6">
        <label>Date of Birth</label>
        <input type="text" 
               id="dob" 
               data-datepicker="dob" 
               data-age-field="#age"
               placeholder="dd/mm/yyyy">
    </div>
    <div class="col-md-6">
        <label>Age</label>
        <input type="text" id="age" readonly>
    </div>
</div>
```

**Date Range Picker:**
```html
<input type="text" 
       data-datepicker="range" 
       placeholder="Select date range"
       name="date_range">
```

**Date & Time Picker:**
```html
<input type="text" 
       data-datepicker="datetime" 
       placeholder="dd/mm/yyyy hh:mm AM/PM"
       name="appointment_datetime">
```

---

### Option 2: JavaScript-Based Initialization

For dynamic forms or when you need more control:

**Simple Date:**
```javascript
// After element is added to DOM
CRM_DatePicker.initStandard('#my-date-field');

// Or with options
CRM_DatePicker.initStandard('#my-date-field', {
    minDate: '01/01/2020',
    maxDate: moment().format('DD/MM/YYYY')
});
```

**Date of Birth:**
```javascript
CRM_DatePicker.initDOB('#dob', '#age');
```

**Date Time:**
```javascript
CRM_DatePicker.initDateTime('#appointment-datetime');
```

**Date Range:**
```javascript
CRM_DatePicker.initRange('#date-range-filter');
```

---

## 📊 Format Standards

### Display Format (Frontend)
- **Format:** `DD/MM/YYYY`
- **Example:** `25/12/2024`
- **Usage:** All user-facing date inputs and displays

### Database Format (Backend)
- **Format:** `YYYY-MM-DD`
- **Example:** `2024-12-25`
- **Usage:** All database storage

### Conversion Functions

```javascript
// Convert DD/MM/YYYY to YYYY-MM-DD for database
var dbDate = CRM_DatePicker.toDatabase('25/12/2024');
// Returns: '2024-12-25'

// Convert YYYY-MM-DD to DD/MM/YYYY for display
var displayDate = CRM_DatePicker.toDisplay('2024-12-25');
// Returns: '25/12/2024'

// Validate date format
var isValid = CRM_DatePicker.isValid('25/12/2024', 'DD/MM/YYYY');
// Returns: true

// Calculate age from DOB
var age = CRM_DatePicker.calculateAge('15/03/1990');
// Returns: '34 years 10 months'
```

---

## ⚠️ Important Rules

### ✅ DO:
- Use `CRM_DatePicker` for all NEW features
- Use `data-datepicker` attributes for simple cases
- Test date save/load after implementation
- Follow DD/MM/YYYY format for users
- Convert to YYYY-MM-DD before saving to database

### ❌ DON'T:
- Don't modify `detail-main.js` datepicker code
- Don't change existing financial forms (receipts, invoices, ledgers)
- Don't change existing client detail page datepickers
- Don't use multiple datepicker libraries on same page
- Don't skip date validation

---

## 🧪 Testing Checklist

After adding a date picker to your feature:

```markdown
- [ ] Date picker opens when field is clicked
- [ ] Selected date appears in DD/MM/YYYY format
- [ ] Clear button works (empties the field)
- [ ] Date validates correctly (rejects invalid dates)
- [ ] Date saves to database in YYYY-MM-DD format
- [ ] Date loads correctly from database
- [ ] Works on mobile devices
- [ ] No console errors
- [ ] Browser back/forward buttons work
- [ ] Form submission works with date
```

---

## 📝 Complete Example: New Modal with Date

```html
<!-- Modal -->
<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Add New Record</h5>
            </div>
            <div class="modal-body">
                <form id="myForm" method="POST" action="/save">
                    @csrf
                    
                    <!-- Simple date input -->
                    <div class="form-group">
                        <label>Event Date <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="event_date" 
                               class="form-control"
                               data-datepicker="standard"
                               placeholder="dd/mm/yyyy"
                               required>
                    </div>
                    
                    <!-- Date range -->
                    <div class="form-group">
                        <label>Duration</label>
                        <input type="text" 
                               name="duration" 
                               class="form-control"
                               data-datepicker="range"
                               placeholder="Start - End">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Date pickers auto-initialize via data-datepicker attributes
    // No JavaScript needed!
    
    // Form submission
    $('#myForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        
        // Convert date to database format before sending
        var eventDate = $('[name="event_date"]').val();
        if (eventDate) {
            formData.set('event_date', CRM_DatePicker.toDatabase(eventDate));
        }
        
        // AJAX submit
        $.ajax({
            url: '/save',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Saved successfully');
                $('#myModal').modal('hide');
            }
        });
    });
});
</script>
```

---

## 🔍 Troubleshooting

### Problem: Datepicker doesn't appear
**Solution:**
```javascript
// Check if daterangepicker is loaded
console.log('Daterangepicker:', typeof $.fn.daterangepicker);
// Should show: 'function'

// Check if CRM_DatePicker is loaded
console.log('CRM_DatePicker:', typeof window.CRM_DatePicker);
// Should show: 'object'
```

### Problem: Date format is wrong
**Solution:**
- Frontend should always show DD/MM/YYYY
- Backend should always use YYYY-MM-DD
- Use conversion functions

### Problem: Conflicts with existing datepickers
**Solution:**
- Client detail pages use bootstrap-datepicker (don't change)
- New pages should use CRM_DatePicker
- Never load multiple datepicker libraries

### Problem: Date doesn't save to database
**Solution:**
```javascript
// Always convert before saving
var displayDate = '25/12/2024';
var dbDate = CRM_DatePicker.toDatabase(displayDate);
// Now save dbDate: '2024-12-25'
```

---

## 📚 API Reference

### CRM_DatePicker.initStandard(selector, options)
Initialize a standard single date picker.

**Parameters:**
- `selector` (string|jQuery): CSS selector or jQuery object
- `options` (object, optional): Configuration overrides

**Returns:** void

---

### CRM_DatePicker.initDOB(dobSelector, ageSelector, options)
Initialize a date of birth picker with age calculation.

**Parameters:**
- `dobSelector` (string|jQuery): DOB input selector
- `ageSelector` (string|jQuery): Age output field selector (optional)
- `options` (object, optional): Configuration overrides

**Returns:** void

---

### CRM_DatePicker.initDateTime(selector, options)
Initialize a date and time picker.

**Parameters:**
- `selector` (string|jQuery): CSS selector or jQuery object
- `options` (object, optional): Configuration overrides

**Returns:** void

---

### CRM_DatePicker.initRange(selector, options)
Initialize a date range picker.

**Parameters:**
- `selector` (string|jQuery): CSS selector or jQuery object
- `options` (object, optional): Configuration overrides

**Returns:** void

---

### CRM_DatePicker.calculateAge(dob)
Calculate age from date of birth.

**Parameters:**
- `dob` (string): Date in DD/MM/YYYY format

**Returns:** string - Age in "XX years YY months" format

---

### CRM_DatePicker.toDatabase(dateStr)
Convert DD/MM/YYYY to YYYY-MM-DD.

**Parameters:**
- `dateStr` (string): Date in DD/MM/YYYY format

**Returns:** string|null - Date in YYYY-MM-DD format or null if invalid

---

### CRM_DatePicker.toDisplay(dateStr)
Convert YYYY-MM-DD to DD/MM/YYYY.

**Parameters:**
- `dateStr` (string): Date in YYYY-MM-DD format

**Returns:** string - Date in DD/MM/YYYY format or empty string if invalid

---

### CRM_DatePicker.isValid(dateStr, format)
Validate date string format.

**Parameters:**
- `dateStr` (string): Date string to validate
- `format` (string): Expected format ('DD/MM/YYYY' or 'YYYY-MM-DD')

**Returns:** boolean - True if valid

---

## 🎓 Training Resources

### For New Developers
1. Read this guide completely
2. Review `public/js/global-datepicker.js` source code
3. Look at lead creation form for working example
4. Test in development environment first

### For Code Reviews
Check for:
- [ ] Uses CRM_DatePicker for new features
- [ ] Doesn't modify existing client detail code
- [ ] Follows DD/MM/YYYY display format
- [ ] Converts to YYYY-MM-DD before database save
- [ ] Includes proper validation
- [ ] Tested on mobile

---

## 🔗 Related Files

- `public/js/global-datepicker.js` - Main helper file
- `resources/views/layouts/admin.blade.php` - Loads global helper
- `resources/views/layouts/admin_client_detail.blade.php` - Client detail layout
- `public/js/scripts.js` - Legacy datepicker initializations
- `resources/views/Admin/leads/create.blade.php` - Example implementation

---

## 📞 Support

If you have questions or need help:
1. Check this guide first
2. Review the example implementations
3. Check browser console for errors
4. Ask the team lead

---

**Last Updated:** 2025-01-20  
**Maintained By:** Development Team  
**Review Schedule:** Quarterly

