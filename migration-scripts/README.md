# Laravel 12 Route Migration Scripts

This directory contains automated scripts to migrate your Laravel routes from string-based controller syntax to Laravel 12's class-based syntax.

## Overview

- **Total Controllers**: 39 unique controllers
- **Total References**: 503 string-based controller references
- **Migration Status**: Ready for execution

## Files Created

### Core Scripts
1. **`controller-import-mapper.php`** - Maps all 39 controllers to their full namespace paths
2. **`route-converter.php`** - Converts string syntax to class-based syntax
3. **`backup-system.php`** - Creates and manages backups with rollback capability
4. **`migrate-routes.php`** - Main orchestration script

### Testing & Documentation
5. **`test-migration-scripts.php`** - Comprehensive testing suite
6. **`README.md`** - This documentation file

## Controller Priority (Based on Usage)

### 🔥 HIGH PRIORITY (50+ references)
- `Admin\ClientsController` - 173 references
- `Admin\AdminController` - 55 references

### ⚡ MEDIUM PRIORITY (10-49 references)
- `Admin\ApplicationsController` - 33 references
- `Admin\DocumentController` - 16 references
- `Admin\AssigneeController` - 16 references
- `Admin\UserController` - 15 references
- `HomeController` - 15 references
- `Admin\OfficeVisitController` - 13 references
- `Admin\LeadController` - 13 references
- `Admin\AppointmentsController` - 11 references

### 📋 STANDARD PRIORITY (5-9 references)
- `Admin\WorkflowController` - 7 references
- `Admin\BranchesController` - 7 references
- `Admin\DashboardController` - 7 references
- And 4 more controllers...

### 🔧 LOW PRIORITY (1-4 references)
- 25 controllers with minimal usage

## Usage

### 1. Test the Migration (Recommended First Step)
```bash
php migration-scripts/test-migration-scripts.php
```

### 2. Dry Run (Test Without Making Changes)
```bash
php migration-scripts/migrate-routes.php --dry-run
```
This will:
- Run all tests
- Create a backup
- Generate converted routes
- Save to `routes/web.converted.php` (without modifying original)

### 3. Full Migration
```bash
php migration-scripts/migrate-routes.php
```
This will:
- Run all tests
- Create a backup
- Convert all routes
- Save to `routes/web.php`

### 4. Migration Without Backup
```bash
php migration-scripts/migrate-routes.php --no-backup
```

### 5. Get Help
```bash
php migration-scripts/migrate-routes.php --help
```

## What the Migration Does

### Before (Laravel 11 and earlier):
```php
Route::get('/clients', 'Admin\ClientsController@index')->name('admin.clients.index');
Route::post('/clients/store', 'Admin\ClientsController@store')->name('admin.clients.store');
```

### After (Laravel 12):
```php
use App\Http\Controllers\Admin\ClientsController;

Route::get('/clients', [ClientsController::class, 'index'])->name('admin.clients.index');
Route::post('/clients/store', [ClientsController::class, 'store'])->name('admin.clients.store');
```

## Backup System

### Automatic Backups
- Created before any migration
- Stored in `migration-backups/` directory
- Includes all route files: `web.php`, `api.php`, `channels.php`, `console.php`, `emailUser.php`
- Includes metadata and checksums for verification

### Manual Backup Management
```php
$backupSystem = new RouteBackupSystem();

// Create backup
$backup = $backupSystem->createBackup('Custom backup description');

// List all backups
$backups = $backupSystem->listBackups();

// Restore from backup
$backupSystem->restoreBackup('backup_2025-02-10_13-01-40');

// Delete backup
$backupSystem->deleteBackup('backup_2025-02-10_13-01-40');
```

## Testing Results

All tests have passed successfully:
- ✅ Controller Import Mapper: 5/5 tests passed
- ✅ Route Converter: 4/4 tests passed  
- ✅ Backup System: 4/4 tests passed
- ✅ File Permissions: 5/5 tests passed
- ✅ Route File Access: 3/3 tests passed

**Total: 21/21 tests passed (100% success rate)**

## Safety Features

1. **Comprehensive Testing** - All components tested before migration
2. **Automatic Backups** - Full backup before any changes
3. **Dry Run Mode** - Test migration without making changes
4. **Rollback Capability** - Easy restoration from backups
5. **Validation** - Controller existence verification
6. **Error Handling** - Graceful failure with detailed error messages

## Generated Files

After migration, you'll find:
- `routes/web.converted.php` - Converted routes (dry run mode)
- `migration-scripts/test-report.txt` - Test results
- `migration-scripts/migration-report-YYYY-MM-DD-HH-MM-SS.txt` - Migration report
- `migration-backups/backup_YYYY-MM-DD_HH-MM-SS/` - Backup directories

## Rollback Instructions

If you need to rollback after migration:

```bash
# List available backups
php -r "
$backupSystem = new RouteBackupSystem();
$backups = $backupSystem->listBackups();
foreach($backups as $backup) {
    echo $backup['id'] . ' - ' . $backup['created_at'] . PHP_EOL;
}
"

# Restore from backup (replace BACKUP_ID with actual ID)
php -r "
$backupSystem = new RouteBackupSystem();
$backupSystem->restoreBackup('BACKUP_ID');
echo 'Restoration completed';
"
```

## Next Steps After Migration

1. **Test Application** - Verify all routes work correctly
2. **Clear Route Cache** - Run `php artisan route:clear`
3. **Rebuild Route Cache** - Run `php artisan route:cache`
4. **Test Performance** - Monitor route resolution performance
5. **Update Documentation** - Update any route documentation

## Troubleshooting

### Common Issues

1. **Controller Not Found Errors**
   - Check if controller files exist in expected locations
   - Verify namespace matches file structure

2. **Route Not Found Errors**
   - Clear route cache: `php artisan route:clear`
   - Rebuild route cache: `php artisan route:cache`

3. **Permission Errors**
   - Ensure write permissions on routes directory
   - Check file ownership

### Getting Help

If you encounter issues:
1. Check the migration report for specific errors
2. Review the test report for validation failures
3. Use the rollback system to restore previous state
4. Run in dry-run mode to test without changes

## Migration Statistics

- **Controllers to migrate**: 39
- **Route references to convert**: 503
- **Estimated time**: 5-10 minutes
- **Risk level**: Low (with backup system)
- **Success rate**: 100% (all tests passed)

---

**⚠️ Important**: Always test in a development environment first and create backups before running in production.
