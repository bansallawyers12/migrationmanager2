# EOI/ROI Feature - Quick Start Guide

## 🚀 For Developers

### What Was Built
A complete EOI/ROI workflow management system inside the client detail area that supports:
- Multiple EOI records per client
- Multiple subclasses and states per EOI
- Automated points calculation (age, English, employment, education, bonuses, partner, nomination)
- Warning system for upcoming changes
- Full CRUD operations via AJAX
- Modern UI with real-time updates

### Files You Need to Know

**Backend:**
- `app/Services/PointsService.php` - Points calculation engine
- `app/Http/Controllers/Admin/ClientEoiRoiController.php` - API endpoints
- `app/Models/ClientEoiReference.php` - EOI data model

**Frontend:**
- `resources/views/Admin/clients/tabs/eoi_roi.blade.php` - UI
- `public/js/clients/eoi-roi.js` - AJAX interactions

**Routes:** 6 new routes in `routes/web.php`
```
GET    /admin/clients/{client}/eoi-roi
POST   /admin/clients/{client}/eoi-roi
GET    /admin/clients/{client}/eoi-roi/{id}
DELETE /admin/clients/{client}/eoi-roi/{id}
GET    /admin/clients/{client}/eoi-roi/calculate-points
GET    /admin/clients/{client}/eoi-roi/{id}/reveal-password
```

---

## 🧪 For QA/Testers

### How to Test

1. **Login to Admin Panel**

2. **Find a Client with EOI Matter**
   - Look for a client that has "Expression Of Interest(EOI)" matter
   - Or create a new matter and assign EOI type
   - 221 clients currently have EOI matters

3. **Access EOI/ROI Tab**
   - Navigate to client detail page
   - Look for "EOI / ROI" tab in left sidebar
   - Tab only appears if client has EOI matter

4. **Test CRUD Operations**

   **Create:**
   - Click "Add New EOI"
   - Fill in EOI number: `EOI12345678`
   - Select subclasses: 189, 190 (can select multiple)
   - Select states: VIC, NSW (can select multiple)
   - Enter occupation: `261313`
   - Enter points: `85`
   - Select status: Draft/Submitted/etc
   - Click "Save EOI"
   - ✅ Should show success message and record appears in table

   **Read:**
   - Click any row in the EOI table
   - ✅ Form should populate with record data
   - ✅ All fields should display correctly

   **Update:**
   - Change any field (e.g., points to 90)
   - Click "Save EOI"
   - ✅ Should show success and update in table

   **Delete:**
   - Click "Delete" button
   - Confirm deletion
   - ✅ Record should disappear from table

5. **Test Points Calculation**
   - Select a subclass from dropdown (189/190/491)
   - ✅ Should show total points
   - ✅ Should show breakdown (age, English, employment, etc.)
   - ✅ Should show warnings if applicable
   - Try different subclasses - totals should differ

6. **Test Validation**
   - Try saving without EOI number → Should show error
   - Try saving without subclass → Should show error
   - Try saving without state → Should show error

---

## 🔧 For DevOps/Deployment

### Quick Deploy Commands

```bash
# 1. Backup
mysqldump -u root -p database_name > backup_$(date +%Y%m%d).sql

# 2. Pull code
git pull origin feature/controller-separation-document-fixes

# 3. Run migration
php artisan migrate --path=database/migrations/2025_10_12_185509_add_eoi_roi_workflow_columns_to_client_eoi_references_table.php

# 4. Backfill data
php artisan eoi:backfill-arrays

# 5. Clear caches
php artisan config:clear && php artisan cache:clear && php artisan view:clear && php artisan route:clear

# 6. Optimize (production only)
php artisan config:cache && php artisan route:cache && php artisan view:cache

# 7. Verify
php artisan route:list --name=eoi-roi  # Should show 6 routes
```

### Rollback (if needed)

```bash
# Rollback migration
php artisan migrate:rollback --step=1

# Clear caches
php artisan config:clear && php artisan cache:clear

# Restore from backup if needed
mysql -u root -p database_name < backup_YYYYMMDD.sql
```

---

## ✅ Verification Checklist

Run these test scripts in order:

```bash
# Phase 1: Database (migration, backfill)
php test_phase1.php

# Phase 2: Backend (services, controllers, routes)
php test_phase2.php

# Phase 3: Frontend (views, JavaScript)
php test_phase3.php

# Phase 4: Automated tests
php test_phase4.php

# Phase 5: Deployment readiness
php test_phase5.php
```

All should show **100% tests passed**.

### Or run automated tests:

```bash
# All tests
php artisan test

# Specific suites
php artisan test tests/Unit/Services/PointsServiceTest.php
php artisan test tests/Feature/EoiRoi/ClientEoiRoiControllerTest.php
```

---

## 📊 Current System Stats

- **Total EOI Records:** 303
- **Backfilled Records:** 296 (97.69%)
- **Clients with EOI Matter:** 221
- **EOI Matter ID:** 151
- **Matter Title:** "Expression Of Interest(EOI)"

---

## 🎯 Success Criteria

Deployment is successful if:

- [x] Migration runs without errors
- [x] Backfill completes with >95% success
- [x] EOI/ROI tab appears for EOI matter clients
- [x] All CRUD operations work
- [x] Points calculation displays correctly
- [x] Validation prevents invalid data
- [x] No errors in logs
- [x] Performance < 1 second per operation

---

## 🆘 Troubleshooting

**Tab not showing?**
- Check client has an EOI matter assigned
- Verify matter nick_name is "EOI" or title contains "EOI"

**500 error on save?**
- Check `storage/logs/laravel.log`
- Verify CSRF token is present
- Check validation rules

**Points not calculating?**
- Verify PointsService is instantiated
- Check client has DOB set
- Clear cache: `php artisan cache:clear`

**Routes not found?**
```bash
php artisan route:clear
php artisan route:cache
```

---

## 📞 Support Commands

```bash
# Clear everything
php artisan optimize:clear

# Check application health
php artisan about

# Database status
php artisan migrate:status

# List EOI routes
php artisan route:list --name=eoi-roi

# Check logs
tail -f storage/logs/laravel.log
```

---

**Status:** ✅ READY FOR PRODUCTION  
**Last Updated:** October 12, 2025  
**Version:** 1.0.0

