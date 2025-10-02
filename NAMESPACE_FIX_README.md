# Namespace Fix Script Documentation

## Overview
This PowerShell script automatically fixes all Laravel namespace issues where models are referenced as `App\ModelName` instead of `App\Models\ModelName`.

## What It Fixes

### 1. Use Statements
```php
// Before
use App\Admin;
use App\Invoice;

// After
use App\Models\Admin;
use App\Models\Invoice;
```

### 2. Static Method Calls
```php
// Before
\App\Admin::where('id', 1)->first();
\App\Invoice::find($id);

// After
\App\Models\Admin::where('id', 1)->first();
\App\Models\Invoice::find($id);
```

### 3. New Object Instantiation
```php
// Before
$obj = new \App\Invoice;

// After
$obj = new \App\Models\Invoice;
```

### 4. Blade Template References
```php
// Before
@foreach(\App\SubjectArea::all() as $item)

// After
@foreach(\App\Models\SubjectArea::all() as $item)
```

### 5. Config File Class References
```php
// Before
'model' => App\Provider::class,

// After
'model' => App\Models\Provider::class,
```

## Model Name Mappings

The script automatically handles renamed models:

| Old Name | New Name |
|----------|----------|
| Agent | AgentDetails |
| Tax | TaxRate |

## Files Processed

The script processes files in the following directories:
- `app/` - All PHP files (Controllers, Commands, Helpers, Services, etc.)
- `resources/views/` - All Blade template files
- `config/` - All configuration files
- `database/` - All migration and seeder files
- `routes/` - All route files

## How to Use

### Step 1: Review the Script (Optional)
```powershell
notepad fix_all_namespaces.ps1
```

### Step 2: Run the Script
```powershell
.\fix_all_namespaces.ps1
```

### Step 3: Review Changes
The script will:
1. Create a backup directory with timestamp (e.g., `namespace_fix_backup_20250101_143022`)
2. Process all files and show progress
3. Display statistics of changes made

### Step 4: Test Your Application
After the script completes:
1. Test all major features
2. Check for any errors in logs
3. Verify invoice, client, and partner management systems

## Backup & Recovery

### Automatic Backup
- All modified files are automatically backed up to `namespace_fix_backup_YYYYMMDD_HHMMSS/`
- The backup preserves the original directory structure

### To Restore a File
```powershell
# Copy from backup
Copy-Item "namespace_fix_backup_YYYYMMDD_HHMMSS\path\to\file.php" "path\to\file.php" -Force
```

### To Restore All Files
```powershell
# Restore entire backup
$backupDir = "namespace_fix_backup_YYYYMMDD_HHMMSS"
Copy-Item "$backupDir\*" . -Recurse -Force
```

## Non-Existent Models

The following models are referenced in your code but don't exist. You'll need to handle these manually:

- **Company** - Referenced in `app/Helpers/Helper.php`
- **Provider** - Referenced in `config/auth.php`
- **Package** - Referenced in `app/Http/Controllers/Admin/LeadController.php`
- **LoginLog** - Referenced in `app/Http/Controllers/Auth/AdminEmailController.php`
- **HolidayTheme** - Referenced in `app/Http/Controllers/Admin/ThemeController.php`
- **MediaImage** - Referenced in `app/Http/Controllers/Admin/MediaController.php`
- **Education** - Referenced in multiple view files
- **Markup** - Referenced in flight management views

### How to Find These References
```powershell
# Search for specific model
grep -r "App\\Company" app/

# Search in all files
grep -r "App\\Package" .
```

## Expected Results

Based on the analysis:
- **260+ replacements** across **30+ files**
- Most critical: `InvoiceController.php` (~94 fixes)
- Views: 55+ fixes across 19+ blade files

## Troubleshooting

### Issue: "Access Denied" Error
**Solution:** Run PowerShell as Administrator

### Issue: Script Execution Policy Error
```powershell
# Allow script execution for current session
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
```

### Issue: Files Not Being Modified
**Possible causes:**
1. Files are read-only - Check file properties
2. Files are locked by another process - Close your IDE temporarily
3. Insufficient permissions - Run as Administrator

### Issue: Application Errors After Running Script
1. Check the error message for specific model names
2. Look for non-existent models in the error
3. Restore from backup if needed
4. Run the script again after fixing non-existent model references

## Manual Review Checklist

After running the script, manually check:

- [ ] Invoice creation and editing
- [ ] Client management pages
- [ ] Partner management
- [ ] Application workflow
- [ ] Product/Service pages
- [ ] Email templates and sending
- [ ] Authentication and login
- [ ] Database seeders

## Performance Impact

- **Small projects (<100 files):** ~5-10 seconds
- **Medium projects (100-500 files):** ~10-30 seconds
- **Large projects (500+ files):** ~30-60 seconds

## Safety Features

1. ✅ Automatic backup before any changes
2. ✅ UTF-8 encoding preservation
3. ✅ Only modifies files that need changes
4. ✅ Detailed change reporting
5. ✅ Preserves directory structure
6. ✅ No database modifications

## Support

If you encounter issues:
1. Check the backup directory exists
2. Review the console output for errors
3. Test on a single file first if concerned
4. Keep the backup until you're confident everything works

---

**Created:** $(Get-Date -Format 'yyyy-MM-dd')
**Script Version:** 1.0
**Laravel Version:** 8+

