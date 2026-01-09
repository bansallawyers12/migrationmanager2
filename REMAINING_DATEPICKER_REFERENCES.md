# Remaining Datepicker References - Deep Check Results

**Date:** 2026-01-09  
**Status:** Comprehensive scan completed

---

## 🔴 **CRITICAL - High Priority Files**

### **1. public/js/scripts.js** (PAGE 13 - Global Initialization)
**Status:** ❌ **NOT MIGRATED**  
**Impact:** HIGH - Affects all listing pages and forms

**Found References:**
- Line 685: `.datepicker` → daterangepicker (YYYY-MM-DD format)
- Line 698: `.dobdatepicker` → daterangepicker (DD/MM/YYYY format)
- Line 708: `.dobdatepickers` → daterangepicker (DD/MM/YYYY format, with age calculation)
- Line 797: `.filterdatepicker` → daterangepicker (YYYY-MM-DD format)
- Line 808: `.contract_expiry` → daterangepicker (YYYY-MM-DD format)
- Line 820: `.datetimepicker` → daterangepicker (YYYY-MM-DD hh:mm format, with time picker)
- Line 831: `.daterange` → daterangepicker (YYYY-MM-DD format, range picker)

**Action Required:** Replace all with Flatpickr equivalents

---

### **2. public/js/global-datepicker.js** (PAGE 12 - CRM_DatePicker Wrapper)
**Status:** ❌ **NOT MIGRATED**  
**Impact:** HIGH - This is the wrapper utility for new code

**Found References:**
- Line 48: `CRM_DatePicker.initStandard()` → uses daterangepicker
- Line 88: `CRM_DatePicker.initDOB()` → uses daterangepicker
- Line 132: `CRM_DatePicker.initDateTime()` → uses daterangepicker
- Line 168: `CRM_DatePicker.initRange()` → uses daterangepicker

**Action Required:** 
- Create `CRM_Flatpickr` wrapper
- Replace all daterangepicker calls with Flatpickr
- Maintain same API for backward compatibility

---

### **3. public/js/dashboard.js**
**Status:** ❌ **NOT MIGRATED**  
**Impact:** MEDIUM - Dashboard widget date range

**Found References:**
- Line 34: `.daterange` → daterangepicker (with moment.js ranges)

**Action Required:** Replace with Flatpickr range picker

---

## 🟡 **MEDIUM Priority - Blade Files with Active Code**

### **4. resources/views/crm/leads/history.blade.php**
**Status:** ❌ **NOT MIGRATED**  
**Impact:** MEDIUM - Lead history page

**Found References:**
- Line 484: `.datepicker` → daterangepicker initialization
- Line 5: Bootstrap-datepicker CSS include
- Line 394: Bootstrap-datepicker JS include

**Action Required:** 
- Replace daterangepicker with Flatpickr
- Remove bootstrap-datepicker includes

---

### **5. resources/views/crm/clients/tabs/account.blade.php**
**Status:** ❌ **NOT MIGRATED**  
**Impact:** MEDIUM - Client account tab

**Found References:**
- Line 1148: `.account-datepicker` → bootstrap datepicker (dd/mm/yyyy)
- Line 3000: `#edit_office_trans_date, #edit_office_entry_date` → bootstrap datepicker

**Action Required:** Replace with Flatpickr

---

### **6. resources/views/crm/clients/invoicelist.blade.php**
**Status:** ❌ **NOT MIGRATED**  
**Impact:** MEDIUM - Invoice listing page

**Found References:**
- Line 1440: `.datepicker` → bootstrap datepicker (dd/mm/yyyy)
- Line 1401-1402: Bootstrap-datepicker CDN includes

**Action Required:** 
- Replace with Flatpickr
- Remove CDN includes

---

## 🟢 **LOW Priority - CSS/JS Includes (Cleanup Phase)**

### **Bootstrap-Datepicker Includes Still Present:**

1. **resources/views/crm/clients/detail.blade.php**
   - Line 1387: `<script src="{{URL::asset('js/bootstrap-datepicker.js')}}"></script>`

2. **resources/views/crm/leads/history.blade.php**
   - Line 5: Bootstrap-datepicker CSS
   - Line 394: Bootstrap-datepicker JS

3. **resources/views/crm/clients/invoicelist.blade.php**
   - Line 1401-1402: Bootstrap-datepicker CDN

4. **resources/views/layouts/crm_client_detail.blade.php**
   - Line 20: Bootstrap-datepicker CSS (marked as TEMPORARY)
   - Line 1626: Bootstrap-datepicker JS (marked as TEMPORARY)

**Action Required:** Remove after all migrations complete

---

## 📋 **Summary Statistics**

### **By File Type:**
- **JavaScript Files:** 3 critical files (scripts.js, global-datepicker.js, dashboard.js)
- **Blade Templates:** 3 files with active datepicker code
- **Layout Files:** 1 file with temporary includes

### **By Library:**
- **Daterangepicker:** 8+ initialization calls
- **Bootstrap Datepicker:** 3+ initialization calls
- **CSS/JS Includes:** 6+ references

### **By Priority:**
- **🔴 Critical:** 3 files (scripts.js, global-datepicker.js, dashboard.js)
- **🟡 Medium:** 3 files (history.blade.php, account.blade.php, invoicelist.blade.php)
- **🟢 Low:** 6+ files (CSS/JS includes for cleanup)

---

## 🎯 **Recommended Migration Order**

1. **PAGE 12: Create CRM_Flatpickr wrapper** (global-datepicker.js)
   - This will provide a utility for other migrations
   - Estimated: 1-2 hours

2. **PAGE 13: Migrate scripts.js** (Global initialization)
   - This affects all listing pages
   - Estimated: 2-3 hours

3. **Dashboard.js migration**
   - Simpler, isolated widget
   - Estimated: 30 minutes

4. **Blade file migrations** (history, account, invoicelist)
   - Each is independent
   - Estimated: 1 hour each

5. **Final cleanup** (Remove all bootstrap-datepicker includes)
   - After all migrations verified
   - Estimated: 30 minutes

**Total Estimated Time:** 8-10 hours

---

## ⚠️ **Important Notes**

1. **scripts.js is critical** - It initializes datepickers on many listing pages
   - Must be migrated carefully
   - Test on multiple pages after migration

2. **CRM_Flatpickr wrapper** should maintain API compatibility
   - Same method names as CRM_DatePicker
   - This allows gradual migration

3. **Bootstrap-datepicker includes** are intentionally kept during migration
   - Remove only after all migrations are complete and tested
   - Some pages may still need them temporarily

4. **Format consistency:**
   - Display: DD/MM/YYYY (d/m/Y in Flatpickr)
   - Backend: YYYY-MM-DD (Y-m-d in Flatpickr)
   - Some fields use different formats - check each case

---

## ✅ **Next Steps**

1. Review this report
2. Prioritize which files to migrate next
3. Create CRM_Flatpickr wrapper (PAGE 12)
4. Migrate scripts.js (PAGE 13)
5. Migrate remaining blade files
6. Final cleanup phase

---

**Last Updated:** 2026-01-09
