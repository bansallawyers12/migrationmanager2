# Flatpickr Migration Plan
## One Page at a Time Approach

**Status:** Planning Phase - DO NOT APPLY YET  
**Created:** 2026-01-09  
**Updated:** 2026-01-09 (Verification complete - all files identified)  
**Approach:** Migrate one page at a time, test manually after each change

---

## 📋 Pre-Migration Setup

### Step 0: Install Flatpickr (Do Once)
1. Add to `package.json` dependencies:
   ```json
   "flatpickr": "^4.6.13"
   ```
2. Run: `npm install`
3. Build assets: `npm run build`
4. Copy Flatpickr CSS to public folder OR import via Vite

### Step 1: Create Flatpickr Wrapper Utility
Create `public/js/crm-flatpickr.js` - A wrapper that mimics your current `CRM_DatePicker` API to minimize code changes.

---

## 🎯 Migration Priority Order

Pages are ordered from **SIMPLEST to MOST COMPLEX** to minimize risk.

### **Phase 1: Proof of Concept** (Low Risk)
New/isolated features - safe testing ground.

### **Phase 2: Simple Standalone Pages** (Low-Medium Risk)
Pages with isolated datepicker usage, easy to test.

### **Phase 3: Form Pages** (Medium Risk)
Create/Edit forms with multiple date fields.

### **Phase 4: Complex Client Detail Pages** (High Risk)
Heavy datepicker usage, multiple modals, dynamic content.

### **Phase 5: Global Utilities** (Critical Risk)
Shared scripts used across multiple pages.

### **Phase 6: Cleanup** (Final)
Remove old libraries and CSS files.

---

## 📝 Detailed Migration Steps

---

## **PAGE 1: Address Autocomplete (Proof of Concept - Start Here)**

**File:** `public/js/address-autocomplete.js`

**Current Usage:**
- Bootstrap datepicker for `.date-picker` class
- Used in dynamically added fields
- This is a NEW untracked feature (safe to test)

**Migration Steps:**
1. ✅ Replace `.datepicker()` calls with Flatpickr (lines 55, 127)
2. ✅ Update format from `dd/mm/yyyy` to `d/m/Y`
3. ✅ Test in context where this script is used
4. ✅ Verify dynamic field addition works with Flatpickr

**Test Checklist:**
- [ ] Date picker works in address autocomplete modals
- [ ] Dynamic field addition preserves datepicker
- [ ] Date format displays as DD/MM/YYYY
- [ ] No conflicts with other datepickers on same page
- [ ] No console errors

**Estimated Time:** 30 minutes  
**Risk Level:** ⭐ Very Low (new feature, limited usage)

**Why Start Here?**
- Isolated feature with minimal user impact
- Good proof-of-concept before touching production pages
- Easy rollback if issues occur

---

## **PAGE 2: Enhanced Date Filter**

**File:** `resources/views/crm/clients/partials/enhanced-date-filter-scripts.blade.php`

**Current Usage:**
- Bootstrap datepicker for `.datepicker` class
- Simple date range filtering (from/to dates)

**Migration Steps:**
1. ✅ Add Flatpickr CSS/JS includes to parent layout
2. ✅ Replace `.datepicker()` initialization with Flatpickr
3. ✅ Update format from `dd/mm/yyyy` to Flatpickr format `d/m/Y`
4. ✅ Update `parseDate()` function to work with Flatpickr
5. ✅ Test: Date selection, form submission, date validation

**Test Checklist:**
- [ ] From date picker opens and selects correctly
- [ ] To date picker opens and selects correctly
- [ ] Date format displays as DD/MM/YYYY
- [ ] Form submission works with selected dates
- [ ] Date validation (from < to) still works
- [ ] Clear button functionality
- [ ] Quick filter chips still work
- [ ] Financial year dropdown integration

**Estimated Time:** 30 minutes  
**Risk Level:** ⭐ Low

---

## **PAGE 3: Dashboard Widget (Optional)**

**File:** `public/js/dashboard.js`

**Current Usage:**
- Daterangepicker for `.daterange` class (line 34)
- Date range picker with preset ranges (Today, Yesterday, Last 7 Days, etc.)
- Used in dashboard widgets

**Migration Steps:**
1. ✅ Check if dashboard uses this daterange picker
2. ✅ Replace `.daterange().daterangepicker()` with Flatpickr range mode
3. ✅ Migrate preset ranges to Flatpickr format
4. ✅ Test dashboard widget functionality

**Test Checklist:**
- [ ] Date range picker opens correctly
- [ ] Preset ranges work (Today, Yesterday, Last 7 Days, etc.)
- [ ] Custom date selection works
- [ ] Dashboard updates with selected range
- [ ] No console errors

**Estimated Time:** 30-45 minutes  
**Risk Level:** ⭐⭐ Low-Medium (depends on dashboard usage)

**Note:** Skip this page if dashboard.js is not actively used in your application.

---

## **PAGE 4: Leads Create Page**

**File:** `resources/views/crm/leads/create.blade.php`

**Current Usage:**
- Daterangepicker via `CRM_DatePicker` wrapper
- DOB field with age calculation

**Migration Steps:**
1. ✅ Check which date fields exist on this page
2. ✅ Replace `CRM_DatePicker.initStandard()` calls with Flatpickr
3. ✅ Replace `CRM_DatePicker.initDOB()` with Flatpickr + age calculation
4. ✅ Update format handling
5. ✅ Test all date fields

**Test Checklist:**
- [ ] All date input fields work
- [ ] DOB field calculates age correctly
- [ ] Date format consistent (DD/MM/YYYY)
- [ ] Form validation works
- [ ] Form submission successful
- [ ] No console errors

**Estimated Time:** 45 minutes  
**Risk Level:** ⭐⭐ Medium

---

## **PAGE 5: Clients Create Page**

**File:** `resources/views/crm/clients/create.blade.php`  
**JS File:** (Inline JavaScript in blade file)

**Current Usage:**
- Multiple date fields (visa expiry dates, dynamic fields)
- Daterangepicker initialization in multiple places (lines 844-878)
- Dynamic field generation with datepickers

**Migration Steps:**
1. ✅ Identify all date fields:
   - Visa expiry dates (dynamic array) - line 222, 834
   - Education/employment dates (if any)
   - Other date fields
2. ✅ Replace `initializeDatepickers()` function with Flatpickr version
3. ✅ Update dynamic field generation to use Flatpickr
4. ✅ Update format from `DD/MM/YYYY` to `d/m/Y`
5. ✅ Test static and dynamic date fields

**Test Checklist:**
- [ ] Static visa expiry date field works
- [ ] Add new visa field → datepicker initializes
- [ ] Remove visa field → no errors
- [ ] All date formats display as DD/MM/YYYY
- [ ] Form submission works
- [ ] Validation errors display correctly
- [ ] Tab navigation preserves datepicker functionality

**Estimated Time:** 1-1.5 hours  
**Risk Level:** ⭐⭐⭐ Medium-High

---

## **PAGE 6: Education Modal**

**File:** `resources/views/crm/clients/modals/education.blade.php`

**Current Usage:**
- Datepicker in education history forms (2 occurrences)
- Used within client detail page modals

**Migration Steps:**
1. ✅ Identify datepicker fields in education modal
2. ✅ Replace with Flatpickr initialization
3. ✅ Test modal open/close with datepickers
4. ✅ Test saving education records with dates

**Test Checklist:**
- [ ] Education modal opens correctly
- [ ] Date fields in education form work
- [ ] Date selection works
- [ ] Modal close/reopen preserves functionality
- [ ] Form submission with dates works
- [ ] Date format displays as DD/MM/YYYY

**Estimated Time:** 30-45 minutes  
**Risk Level:** ⭐⭐ Medium

---

## **PAGE 7: Client Detail - Account Tab**

**File:** `resources/views/crm/clients/tabs/account.blade.php`  
**JS File:** `public/js/crm/clients/detail-main.js` (lines 2577, 2583, 4673, etc.)

**Current Usage:**
- Bootstrap datepicker for ledger dates
- Report date fields
- Entry date fields
- Multiple initialization points

**Migration Steps:**
1. ✅ Identify all `.datepicker()` calls in detail-main.js
2. ✅ Replace with Flatpickr one by one
3. ✅ Test each modal/section independently
4. ✅ Check format consistency

**Test Checklist:**
- [ ] Edit ledger modal dates work
- [ ] Report date fields work
- [ ] Entry date fields work
- [ ] Date formatting consistent
- [ ] Modal open/close doesn't break datepickers
- [ ] Form submissions work

**Estimated Time:** 1.5 hours  
**Risk Level:** ⭐⭐⭐ High (heavily used page)

---

## **PAGE 8: Client Detail - EOI/ROI Tab**

**File:** `resources/views/crm/clients/tabs/eoi_roi.blade.php`  
**JS File:** `public/js/clients/eoi-roi.js`

**Current Usage:**
- `.eoi-datepicker` class (line 86 in eoi-roi.js)
- Bootstrap datepicker with specific options

**Migration Steps:**
1. ✅ Replace `.eoi-datepicker` initialization in eoi-roi.js
2. ✅ Update format from `dd/mm/yyyy` to `d/m/Y`
3. ✅ Test EOI/ROI form functionality
4. ✅ Test date field behavior in tab

**Test Checklist:**
- [ ] EOI date fields work
- [ ] ROI date fields work
- [ ] Date format displays as DD/MM/YYYY
- [ ] Form submission works
- [ ] Date validation works
- [ ] Tab switching preserves datepicker

**Estimated Time:** 30-45 minutes  
**Risk Level:** ⭐⭐ Medium

---

## **PAGE 9: Client Detail - Main Page**

**File:** `resources/views/crm/clients/detail.blade.php`  
**Layout:** `resources/views/layouts/crm_client_detail.blade.php`  
**JS File:** `public/js/crm/clients/detail-main.js`

**Current Usage:**
- Bootstrap datepicker loaded in layout (line 20)
- Multiple date fields throughout page
- Heavy usage in detail-main.js (lines 2577, 2583, 4673, 4675, 4685, etc.)
- Multiple modals with date fields

**Migration Steps:**
1. ✅ **DON'T remove bootstrap-datepicker CSS/JS yet** (keep for backward compatibility)
2. ✅ Add Flatpickr CSS/JS to layout
3. ✅ Find and replace all `.datepicker()` calls in detail-main.js
4. ✅ Update all modals with date fields
5. ✅ Test thoroughly - this is the MOST CRITICAL page
6. ✅ Test each modal individually before moving to next

**Modal Testing Order:**
1. ✅ Notes modal (editnotetermform)
2. ✅ Appointment modal (create_appoint, edit_appointment)
3. ✅ Education modal (already migrated in PAGE 6)
4. ✅ Financial modal
5. ✅ Payment schedules modal
6. ✅ Applications modal
7. ✅ Forms modal
8. ✅ Documents modal
9. ✅ Receipts modal
10. ✅ Checklists modal
11. ✅ Activities modal
12. ✅ Edit matter office modal
13. ✅ Client management modal
14. ✅ Emails modal

**Test Checklist:**
- [ ] All date fields on main page work
- [ ] Ledger edit modal dates work (trans_date, entry_date)
- [ ] Report date fields work (.report_date_fields)
- [ ] Entry date fields work (.report_entry_date_fields)
- [ ] Invoice date fields work (.report_date_fields_invoice, .report_entry_date_fields_invoice)
- [ ] All 14 modals tested (see list above)
- [ ] Date formatting consistent (DD/MM/YYYY)
- [ ] No console errors
- [ ] Page load performance acceptable
- [ ] Mobile responsiveness maintained
- [ ] Dynamic field addition/removal works

**Estimated Time:** 3-4 hours  
**Risk Level:** ⭐⭐⭐⭐⭐ CRITICAL (most important page, heavy user traffic)

---

## **PAGE 10: Custom Popover**

**File:** `public/js/custom-popover.js`

**Current Usage:**
- `#embeddingDatePicker` - bootstrap datepicker
- Multiple `.datepicker('update')` calls (lines 42, 89, 145, 501)
- `.datepicker()` initialization (line 816)
- `.datepicker('getFormattedDate')` call (line 823)

**Migration Steps:**
1. ✅ Replace datepicker initialization with Flatpickr
2. ✅ Replace `.datepicker('update')` with Flatpickr `setDate()` method
3. ✅ Replace `.datepicker('getFormattedDate')` with Flatpickr API
4. ✅ Update format from `dd/mm/yyyy` to `d/m/Y`
5. ✅ Test popover functionality

**Test Checklist:**
- [ ] Popover datepicker opens correctly
- [ ] Date updates work (programmatic setDate)
- [ ] Date selection works (user interaction)
- [ ] Get formatted date works
- [ ] Integration with parent form works
- [ ] No console errors

**Estimated Time:** 45-60 minutes  
**Risk Level:** ⭐⭐⭐ Medium-High

---

## **PAGE 11: Popover.js**

**File:** `public/js/popover.js`

**Current Usage:**
- Similar to custom-popover.js
- Datepicker in popover context

**Migration Steps:**
1. ✅ Same as custom-popover.js
2. ✅ Test in context where used

**Test Checklist:**
- [ ] Same as custom-popover.js

**Estimated Time:** 30-45 minutes  
**Risk Level:** ⭐⭐ Medium

---

## **PAGE 12: Global Datepicker Utility**

**File:** `public/js/global-datepicker.js`

**Current Usage:**
- `CRM_DatePicker` wrapper around daterangepicker
- Used by multiple pages for new features
- Auto-initialization via data attributes
- Helper methods for date manipulation

**Migration Steps:**
1. ✅ Create new `CRM_Flatpickr` wrapper (in new file: `public/js/crm-flatpickr.js`)
2. ✅ Match existing API methods exactly:
   - `initStandard()` - single date picker
   - `initDOB()` - DOB with age calculation
   - `initDateTime()` - date + time picker
   - `initRange()` - date range picker
   - `calculateAge()` - age calculation helper
   - `toDatabase()` - DD/MM/YYYY → YYYY-MM-DD
   - `toDisplay()` - YYYY-MM-DD → DD/MM/YYYY
   - `isValid()` - date validation
3. ✅ Update auto-initialization for data attributes:
   - `[data-datepicker="standard"]`
   - `[data-datepicker="dob"]`
   - `[data-datepicker="datetime"]`
   - `[data-datepicker="range"]`
4. ✅ **KEEP** old `CRM_DatePicker` for backward compatibility
5. ✅ Document how to use new wrapper
6. ✅ Gradually migrate pages to use `CRM_Flatpickr`

**Test Checklist:**
- [ ] All wrapper methods work identically to CRM_DatePicker
- [ ] Data attribute auto-init works
- [ ] Format conversions work (toDatabase, toDisplay)
- [ ] Age calculation works
- [ ] Date validation works
- [ ] Backward compatibility maintained (old code still works)
- [ ] Console logs helpful messages
- [ ] No breaking changes

**Estimated Time:** 2-3 hours  
**Risk Level:** ⭐⭐⭐⭐ Very High (affects multiple pages)

---

## **PAGE 13: Scripts.js (Global)**

**File:** `public/js/scripts.js`

**Current Usage:**
- Global daterangepicker initialization (lines 673-706)
- `.datepicker` class handler
- `.dobdatepicker` class handler
- `.dobdatepickers` class handler
- Conditional logic to avoid conflicts with client detail pages

**Migration Steps:**
1. ✅ Replace daterangepicker initialization with Flatpickr
2. ✅ Update conditional logic if needed (check for client detail page)
3. ✅ Replace `.datepicker`, `.dobdatepicker`, `.dobdatepickers` handlers
4. ✅ Update format from `YYYY-MM-DD` to `d/m/Y` for display
5. ✅ Test on multiple page types

**Test Checklist:**
- [ ] Works on non-client-detail pages (leads, etc.)
- [ ] Doesn't conflict with client detail pages
- [ ] DOB datepickers work (.dobdatepicker)
- [ ] Standard datepickers work (.datepicker)
- [ ] Detection logic works (isClientDetailPage check)
- [ ] Format consistency maintained
- [ ] No console errors on any page type

**Estimated Time:** 1-1.5 hours  
**Risk Level:** ⭐⭐⭐⭐ High (global impact, affects many pages)

---

## **PAGE 14-25: Remaining List Pages**

### **List Pages:**
- `resources/views/crm/clients/invoicelist.blade.php`
- `resources/views/crm/clients/officereceiptlist.blade.php`
- `resources/views/crm/clients/journalreceiptlist.blade.php`
- `resources/views/crm/clients/clientreceiptlist.blade.php`
- `resources/views/crm/clients/clientsmatterslist.blade.php`
- `resources/views/crm/clients/analytics-dashboard.blade.php`
- `resources/views/crm/leads/index.blade.php`
- `resources/views/crm/leads/detail.blade.php`
- `resources/views/crm/leads/history.blade.php`
- `resources/views/crm/assignee/action.blade.php`
- `resources/views/crm/assignee/assign_by_me.blade.php`
- `resources/views/crm/assignee/action_completed.blade.php`
- `resources/views/crm/archived/index.blade.php`

**Migration Steps:**
1. ✅ Identify datepicker usage in each
2. ✅ Replace with Flatpickr
3. ✅ Test page-specific functionality

**Estimated Time:** 30-60 minutes per page  
**Risk Level:** ⭐⭐ Medium (lower traffic pages)

---

---

## **PHASE 6: Final Cleanup**

**After ALL pages are migrated and tested:**

### Step 1: Remove Old Libraries
1. ✅ Remove from layouts:
   - `<link rel="stylesheet" href="{{asset('css/bootstrap-datepicker.min.css')}}">`
   - `<script src="{{asset('js/bootstrap-datepicker.min.js')}}"></script>`
   - `<link rel="stylesheet" href="{{asset('css/daterangepicker.css')}}">`
   - `<script src="{{asset('js/daterangepicker.js')}}"></script>`

2. ✅ Backup old files (rename with .backup extension):
   - `public/js/bootstrap-datepicker.js` → `.backup`
   - `public/js/daterangepicker.js` → `.backup`
   - `public/css/bootstrap-datepicker.min.css` → `.backup`
   - `public/css/datepicker.css` → `.backup`
   - `public/css/daterangepicker.css` → `.backup`
   - `public/css/daterangepicker-bs3.css` → `.backup`
   - `public/css/bootstrap-datetimepicker.min.css` → `.backup`
   - `public/css/listing-datepicker.css` → `.backup` (if not needed)

3. ✅ Remove old global-datepicker.js:
   - Archive `public/js/global-datepicker.js` → `.backup`
   - Keep only `public/js/crm-flatpickr.js`

4. ✅ Full application smoke test:
   - Test every major page
   - Verify no console errors
   - Confirm all dates work

**Estimated Time:** 2-3 hours (including thorough testing)  
**Risk Level:** ⭐⭐⭐ High (final verification)

---

## 🔧 Technical Implementation Notes

### Format Conversion
Your codebase uses THREE different format syntaxes:
- **Bootstrap Datepicker:** `dd/mm/yyyy` (lowercase)
- **Daterangepicker:** `DD/MM/YYYY` (uppercase, moment.js format)
- **Flatpickr:** `d/m/Y` (PHP-style format)

**All display the same:** DD/MM/YYYY  
**Action:** Replace all with Flatpickr's `d/m/Y` format

### API Differences

**Bootstrap Datepicker:**
```javascript
// OLD
$('.datepicker').datepicker({
    format: 'dd/mm/yyyy',
    autoclose: true
});
$('.datepicker').datepicker('update', date);
$('.datepicker').datepicker('getFormattedDate');

// NEW
flatpickr('.datepicker', {
    dateFormat: 'd/m/Y',
    onChange: function(dates, dateStr) { }
});
instance.setDate(date);
instance.formatDate(date, 'd/m/Y');
```

**Daterangepicker:**
```javascript
// OLD
$(el).daterangepicker(options).on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('DD/MM/YYYY'));
});

// NEW
flatpickr(el, {
    dateFormat: 'd/m/Y',
    onChange: function(dates, dateStr, instance) {
        $(el).val(dateStr);
    }
});
```

### Dynamic Fields
- **Current:** Re-initialize datepicker after DOM changes
- **Flatpickr:** Call `flatpickr()` on new elements, or destroy/recreate instances

### Date Range
- **Daterangepicker:** Built-in range support
- **Flatpickr:** Use `mode: "range"` option

### Time Picker
- **Daterangepicker:** `timePicker: true`
- **Flatpickr:** `enableTime: true, time_24hr: false, dateFormat: 'd/m/Y h:i K'`

---

## ✅ Testing Strategy

### After Each Page Migration:

1. **Visual Testing:**
   - [ ] Datepicker opens correctly
   - [ ] Calendar displays properly
   - [ ] Styling matches design
   - [ ] Mobile responsive

2. **Functional Testing:**
   - [ ] Date selection works
   - [ ] Date format correct (DD/MM/YYYY)
   - [ ] Form submission works
   - [ ] Validation works
   - [ ] Clear/reset works

3. **Integration Testing:**
   - [ ] No console errors
   - [ ] No conflicts with other scripts
   - [ ] Backend receives correct format
   - [ ] Database saves correctly

4. **Regression Testing:**
   - [ ] Other datepickers on page still work (if not migrated yet)
   - [ ] Related functionality unaffected
   - [ ] Page performance acceptable

---

## 📊 Progress Tracking

Use this table to track migration progress:

| # | Page/Component | Status | Date Completed | Tester | Notes |
|---|----------------|--------|----------------|--------|-------|
| 1 | Address Autocomplete | ⏳ Pending | - | - | Proof of concept - START HERE |
| 2 | Enhanced Date Filter | ⏳ Pending | - | - | - |
| 3 | Dashboard Widget | ⏳ Pending | - | - | Optional - skip if not used |
| 4 | Leads Create | ⏳ Pending | - | - | - |
| 5 | Clients Create | ⏳ Pending | - | - | - |
| 6 | Education Modal | ⏳ Pending | - | - | - |
| 7 | Client Detail - Account | ⏳ Pending | - | - | - |
| 8 | Client Detail - EOI/ROI | ⏳ Pending | - | - | - |
| 9 | Client Detail - Main | ⏳ Pending | - | - | CRITICAL - most complex |
| 10 | Custom Popover | ⏳ Pending | - | - | - |
| 11 | Popover.js | ⏳ Pending | - | - | - |
| 12 | Global Datepicker (CRM_Flatpickr) | ⏳ Pending | - | - | High impact |
| 13 | Scripts.js (Global) | ⏳ Pending | - | - | High impact |
| 14-25 | List Pages (12 pages) | ⏳ Pending | - | - | Should auto-work after #13 |
| FINAL | Cleanup & Remove Old Libraries | ⏳ Pending | - | - | Do LAST |

**Legend:**
- ⏳ Pending
- 🔄 In Progress
- ✅ Completed
- ⚠️ Issues Found
- ❌ Blocked
- ⏭️ Skipped

**Current Progress:** 0/26 pages completed (0%)

---

## 🚨 Rollback Plan

If issues are found after migration:

1. **Immediate Rollback:**
   - Revert the specific page's changes
   - Restore original datepicker library includes
   - Test to confirm functionality restored

2. **Partial Rollback:**
   - Keep Flatpickr installed
   - Use both libraries temporarily
   - Migrate problematic pages back to old datepicker

3. **Document Issues:**
   - Note what didn't work
   - Identify root cause
   - Plan fix before retrying

---

## 📝 Notes

- **DO NOT** migrate multiple pages at once
- **ALWAYS** test manually after each page
- **KEEP** old datepicker libraries until all pages migrated
- **DOCUMENT** any issues or edge cases found
- **COMMUNICATE** with team about which pages are migrated

---

## 🎯 Success Criteria

Migration is complete when:
- [ ] All pages migrated to Flatpickr
- [ ] All old datepicker libraries removed
- [ ] No console errors on any page
- [ ] All date fields work correctly
- [ ] Date format consistent (DD/MM/YYYY)
- [ ] Form submissions work
- [ ] Database saves correct format
- [ ] Mobile responsiveness maintained
- [ ] Performance acceptable
- [ ] User acceptance testing passed

---

## 📚 Summary

**Total Files to Migrate:** 26+ pages/components  
**Estimated Total Time:** 20-30 hours  
**Recommended Team:** 2 developers (1 developer + 1 tester)  
**Timeline:** 2-3 weeks at 2-3 hours per day  

**Critical Success Factors:**
1. ✅ Test manually after EACH page migration
2. ✅ Don't skip any test checklist items
3. ✅ Keep old libraries until EVERYTHING is migrated
4. ✅ Document any issues immediately
5. ✅ Have rollback plan ready
6. ✅ Communicate progress to team daily

**High Risk Pages (Extra Caution):**
- PAGE 9: Client Detail - Main (most critical)
- PAGE 12: Global Datepicker (affects multiple pages)
- PAGE 13: Scripts.js (global impact)

---

**Next Step:** Review this plan, install Flatpickr (Step 0), then start with **PAGE 1: Address Autocomplete** as proof of concept.
