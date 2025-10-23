# Admin to Client Refactoring - Quick Start

## ⚠️ IMPORTANT: Read This First!

This refactoring will **permanently change your database structure**. 

**Before you begin:**
1. ✅ Make a complete database backup
2. ✅ Test on development environment first
3. ✅ Read the full guide: `ADMIN_TO_CLIENT_REFACTORING_GUIDE.md`
4. ✅ Ensure you have database admin access
5. ✅ Schedule maintenance window for production

---

## 🚀 Quick Start Steps

### Step 1: Backup Database

```bash
# Create backup with timestamp
mysqldump -u your_username -p your_database > backup_$(date +%Y%m%d_%H%M%S).sql

# Compress it
gzip backup_*.sql

# Verify backup exists and has content
ls -lh backup_*.sql.gz
```

### Step 2: Run Migrations (In Order!)

```bash
# 1. Create mapping table
php artisan migrate --path=database/migrations/2025_10_23_000000_create_admin_to_client_mapping_table.php

# 2. Create clients table
php artisan migrate --path=database/migrations/2025_10_23_000001_create_clients_table.php

# 3. Migrate data from admins to clients
php artisan migrate --path=database/migrations/2025_10_23_000002_migrate_data_from_admins_to_clients.php

# 4. Update foreign keys in related tables
php artisan migrate --path=database/migrations/2025_10_23_000003_update_foreign_keys_to_clients_table.php
```

### Step 3: Verify Migration

```bash
# Run verification script
php verify_migration.php
```

**Expected Output:**
- ✓ All tables exist
- ✓ Record counts match
- ✓ No unmapped records
- ✓ Foreign keys updated
- ✓ Data integrity checks pass

**If verification fails:** STOP! Do not proceed. Review issues and fix them first.

### Step 4: Test Application

**Critical Tests:**
- [ ] Staff login works
- [ ] Client portal login works
- [ ] View clients list
- [ ] View leads list
- [ ] Create/edit client
- [ ] Create/edit lead
- [ ] Client forms load
- [ ] EOI data displays
- [ ] Followups work
- [ ] Documents link correctly

**Only proceed to Step 5 if all tests pass!**

### Step 5: Run Cleanup Migration (⚠️ IRREVERSIBLE!)

```bash
# This deletes client/lead data from admins table
# ONLY run after thorough testing!

php artisan migrate --path=database/migrations/2025_10_23_000004_cleanup_admins_table_remove_client_fields.php
```

### Step 6: Update Application Code

See `ADMIN_TO_CLIENT_REFACTORING_GUIDE.md` section "Code Updates Required"

Key changes needed:
1. Update controllers to use `Client` model instead of `Admin` for clients/leads
2. Update authentication guards
3. Update middleware
4. Update relationships
5. Update views
6. Update API endpoints

---

## 📋 What Gets Changed?

### Created:
- ✅ `clients` table - New table for clients and leads
- ✅ `admin_to_client_mapping` table - ID mapping reference
- ✅ `Client` model - New model with relationships

### Modified:
- 🔄 `admins` table - Becomes staff-only, client fields removed
- 🔄 `Admin` model - Updated to staff-only relationships
- 🔄 Foreign keys in related tables - Point to `clients` instead of `admins`

### Affected Tables:
- `forms_956`
- `client_eoi_references`
- `client_test_scores`
- `client_experiences`
- `client_qualifications`
- `client_spouse_details`
- `client_occupations`
- `client_relationships`
- `lead_followups`
- `documents`
- `sms_log`

---

## 🔄 Rollback (If Needed)

If something goes wrong **before cleanup migration:**

```bash
# Rollback migrations
php artisan migrate:rollback --step=4

# Restore from backup if needed
gunzip backup_*.sql.gz
mysql -u your_username -p your_database < backup_*.sql
```

If something goes wrong **after cleanup migration:**

```bash
# MUST restore from backup - rollback won't restore data
gunzip backup_*.sql.gz
mysql -u your_username -p your_database < backup_*.sql
```

---

## ⏱️ Estimated Time

- Small database (< 1000 records): **15-30 minutes**
- Medium database (1000-5000 records): **30-60 minutes**
- Large database (> 5000 records): **1-2 hours**

Includes:
- Migration execution: 5-15 minutes
- Verification: 5 minutes
- Testing: 30-60 minutes
- Code updates: Variable

---

## 🆘 Troubleshooting

### Migration Fails with Foreign Key Error

```bash
# Temporarily disable foreign key checks (use with caution!)
# Run in MySQL:
SET FOREIGN_KEY_CHECKS = 0;
# Run your migration
# Then re-enable:
SET FOREIGN_KEY_CHECKS = 1;
```

### Verification Shows Unmapped Records

```bash
# Check what records weren't migrated
php artisan tinker
>>> DB::table('admins')->whereNotIn('id', function($q) { 
    $q->select('old_admin_id')->from('admin_to_client_mapping'); 
})->where('type', 'client')->limit(10)->get(['id','email','type']);
```

Review and manually fix if needed, then re-run migration.

### Application Errors After Migration

1. Clear cache:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

2. Check logs:
```bash
tail -f storage/logs/laravel.log
```

3. Verify guards in `config/auth.php`

---

## ✅ Success Criteria

Migration is successful when:
- ✅ All verification checks pass
- ✅ Application functions normally
- ✅ Staff can log in
- ✅ Clients can log in (portal)
- ✅ All relationships work
- ✅ No error logs
- ✅ Performance is acceptable

---

## 📞 Need Help?

1. Check `ADMIN_TO_CLIENT_REFACTORING_GUIDE.md` for detailed information
2. Review troubleshooting section
3. Check application logs
4. Run verification script
5. Restore from backup if needed

---

## 📝 Post-Migration Checklist

- [ ] Migrations completed successfully
- [ ] Verification passed
- [ ] Application tested
- [ ] Code updated
- [ ] Documentation updated
- [ ] Team notified
- [ ] Backups archived
- [ ] Monitoring configured

---

**Remember:** Always test in development first! Never run these migrations directly in production without testing.

Good luck! 🚀

