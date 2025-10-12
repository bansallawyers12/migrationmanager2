# EOI/ROI Feature - Deployment Guide

## Pre-Deployment Checklist

### ✅ Code Readiness
- [x] All migrations created and tested
- [x] Models updated with relationships and casts
- [x] Controllers implemented with full CRUD
- [x] Routes registered and tested
- [x] Frontend views created (Blade + JavaScript)
- [x] Authorization gates configured
- [x] Unit tests written (PointsService)
- [x] Feature tests written (CRUD operations)

### ✅ Database Readiness
- [x] Migration file: `2025_10_12_185509_add_eoi_roi_workflow_columns_to_client_eoi_references_table.php`
- [x] Backfill command: `php artisan eoi:backfill-arrays`
- [x] 296/303 existing records migrated (97.69% success rate)

### ✅ Dependencies
- [x] Laravel 12.20.0
- [x] PHP 8.2.12
- [x] MySQL database
- [x] Select2 (for multi-select dropdowns)
- [x] jQuery (for AJAX operations)
- [x] Bootstrap Datepicker

---

## Deployment Steps

### Step 1: Backup

```bash
# Backup database
mysqldump -u root -p your_database > backup_before_eoi_roi_$(date +%Y%m%d).sql

# Backup code (if not using git)
tar -czf backup_code_$(date +%Y%m%d).tar.gz /path/to/migrationmanager
```

### Step 2: Pull Latest Code

```bash
cd /path/to/migrationmanager

# If using git
git pull origin feature/controller-separation-document-fixes

# Or upload files manually:
# - app/Http/Controllers/Admin/ClientEoiRoiController.php
# - app/Services/PointsService.php
# - app/Models/ClientEoiReference.php (updated)
# - app/Providers/AuthServiceProvider.php (updated)
# - resources/views/Admin/clients/tabs/eoi_roi.blade.php
# - public/js/clients/eoi-roi.js
# - routes/web.php (updated)
# - database/migrations/2025_10_12_185509_*.php
# - app/Console/Commands/BackfillEoiRoiData.php
```

### Step 3: Install Dependencies (if needed)

```bash
composer install --no-dev --optimize-autoloader
```

### Step 4: Run Migration

```bash
# Test migration first
php artisan migrate --path=database/migrations/2025_10_12_185509_add_eoi_roi_workflow_columns_to_client_eoi_references_table.php --pretend

# Run actual migration
php artisan migrate --path=database/migrations/2025_10_12_185509_add_eoi_roi_workflow_columns_to_client_eoi_references_table.php
```

### Step 5: Backfill Existing Data

```bash
# Dry run first to see what will be updated
php artisan eoi:backfill-arrays --dry-run

# Run actual backfill
php artisan eoi:backfill-arrays
```

### Step 6: Clear Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Recache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 7: Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Step 8: Verify Deployment

```bash
# Check routes are registered
php artisan route:list --name=eoi-roi

# Should show 6 routes:
# - admin.clients.eoi-roi.index
# - admin.clients.eoi-roi.upsert
# - admin.clients.eoi-roi.show
# - admin.clients.eoi-roi.destroy
# - admin.clients.eoi-roi.calculatePoints
# - admin.clients.eoi-roi.revealPassword
```

---

## Post-Deployment Testing

### 1. Smoke Tests

1. **Login to Admin Panel**
   - Navigate to a client with an EOI matter
   - Verify "EOI / ROI" tab appears in sidebar

2. **Create EOI Record**
   - Click "Add New EOI"
   - Fill in form with test data
   - Submit and verify success message
   - Check record appears in table

3. **View EOI Record**
   - Click on a row in the EOI table
   - Verify form populates correctly
   - Check all fields display properly

4. **Update EOI Record**
   - Modify some fields
   - Save changes
   - Verify update success

5. **Points Calculation**
   - Select a subclass (189/190/491)
   - Verify points calculation displays
   - Check breakdown shows all categories
   - Verify warnings section (if applicable)

6. **Delete EOI Record**
   - Click delete button
   - Confirm deletion
   - Verify record removed from table

### 2. Validation Tests

Test that validation works:
- Try saving without EOI number → Should show error
- Try saving without subclass → Should show error
- Try saving without state → Should show error
- Try invalid subclass (e.g., "999") → Should show error
- Try invalid state (e.g., "XX") → Should show error

### 3. Performance Tests

```bash
# Test points calculation speed
time curl -X GET "http://your-domain.com/admin/clients/1/eoi-roi/calculate-points?subclass=190" \
  -H "Cookie: laravel_session=YOUR_SESSION_COOKIE"

# Should complete in < 1 second (with caching)
```

### 4. Security Tests

- Verify unauthorized users cannot access `/admin/clients/{id}/eoi-roi`
- Verify clients cannot access other clients' EOI records
- Verify passwords are encrypted in database
- Verify password reveal is logged

---

## Rollback Plan

If issues occur, rollback using these steps:

### Quick Rollback

```bash
# Rollback migration
php artisan migrate:rollback --step=1

# Restore code from backup
git checkout HEAD~1  # or restore from backup

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Full Rollback

```bash
# Restore database from backup
mysql -u root -p your_database < backup_before_eoi_roi_YYYYMMDD.sql

# Restore code from backup
tar -xzf backup_code_YYYYMMDD.tar.gz -C /path/to/restore
```

---

## Monitoring

### Key Metrics to Monitor

1. **Error Logs**
   ```bash
   tail -f storage/logs/laravel.log | grep -i "eoi"
   ```

2. **Database Performance**
   ```sql
   SHOW INDEX FROM client_eoi_references;
   -- Should see: idx_client_status, idx_submission_date, idx_status
   ```

3. **Cache Performance**
   - Points calculations should be cached (15 min TTL)
   - Second call should be faster than first

### Common Issues

| Issue | Symptom | Solution |
|-------|---------|----------|
| Tab not showing | No EOI/ROI tab in sidebar | Check client has EOI matter assigned |
| 500 error on save | Server error when saving | Check logs, verify validation rules |
| Points not calculating | Blank points summary | Check PointsService, verify relationships |
| Routes not found | 404 on API calls | Run `php artisan route:cache` |
| Old data not visible | Records missing from table | Run backfill command |

---

## Production Configuration

### Environment Variables

No new environment variables required. Uses existing:
- `DB_CONNECTION`
- `CACHE_DRIVER`
- `APP_KEY` (for password encryption)

### Cron Jobs

No cron jobs required for this feature.

### Queue Workers

No queue workers required (all operations are synchronous).

---

## Feature Flags (Optional)

If you want to enable gradually:

```php
// In config/features.php or similar
return [
    'eoi_roi_enabled' => env('FEATURE_EOI_ROI', true),
];

// In detail.blade.php
@if(config('features.eoi_roi_enabled') && isset($isEoiMatter) && $isEoiMatter)
    @include('Admin.clients.tabs.eoi_roi')
@endif
```

---

## Support

### Documentation
- Implementation Plan: `EOI_ROI_IMPLEMENTATION_PLAN.md`
- This Guide: `DEPLOYMENT_GUIDE.md`

### Testing Scripts
- Phase 1: `php test_phase1.php` (Database)
- Phase 2: `php test_phase2.php` (Backend)
- Phase 3: `php test_phase3.php` (Frontend)
- Phase 4: `php test_phase4.php` (Tests)

### Automated Tests
```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test tests/Unit/Services/PointsServiceTest.php
php artisan test tests/Feature/EoiRoi/ClientEoiRoiControllerTest.php
```

---

## Success Criteria

✅ Deployment is successful if:
1. Migration completes without errors
2. Backfill runs successfully (>95% coverage)
3. EOI/ROI tab appears for clients with EOI matter
4. CRUD operations work (Create, Read, Update, Delete)
5. Points calculation displays correctly
6. All validation rules work
7. No errors in logs
8. Performance is acceptable (<1s per operation)

---

## Post-Deployment Tasks

### Day 1
- [ ] Monitor error logs closely
- [ ] Check with 2-3 staff members to test feature
- [ ] Verify points calculations are accurate
- [ ] Ensure no performance degradation

### Week 1
- [ ] Gather user feedback
- [ ] Monitor database performance
- [ ] Check cache hit rates
- [ ] Review audit logs for password access

### Month 1
- [ ] Analyze usage statistics
- [ ] Consider adding requested features
- [ ] Optimize if needed
- [ ] Update documentation based on feedback

---

## Contact

For issues during deployment:
- Check logs: `storage/logs/laravel.log`
- Database: Check migration status with `php artisan migrate:status`
- Code: Verify all files uploaded correctly
- Cache: Clear all caches if strange behavior

**Deployment prepared by:** AI Assistant  
**Date:** October 12, 2025  
**Version:** 1.0.0

