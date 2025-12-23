# Unused Database Tables Report

## Summary
- **Total tables in database:** 120 (after dropping 16 unused tables)
- **Used tables:** 110
- **Potentially unused tables:** 10

## List of Potentially Unused Tables

The following tables appear to be unused based on analysis of:
- Model definitions
- Code references (DB::table, ->from, ->join, etc.)
- Foreign key relationships
- Migration files

### Remaining Unused Tables (10 total):

1. **admin_to_client_mapping** - No model or code references found
2. **api_tokens** - No model or code references found
3. **application_notes** - No model or code references found
4. **attachments** - No model or code references found (Note: There's an `Attachment` model but it may use a different table)
5. **email_attachments** - No model or code references found
6. **email_uploads** - No model or code references found
7. **personal_access_tokens** - No model or code references found (Note: This might be used by Laravel Sanctum for API authentication)
8. **responsible_people** - No model or code references found
9. **task_logs** - No model or code references found
10. **tbl_paid_appointment_payment** - No model or code references found

### Legacy/Deprecated Tables:

11. **sliders** ⚠️ **LEGACY/DEPRECATED**
   - **Status:** Legacy code from an incomplete feature / A planned feature that was never implemented / Deprecated functionality
   - **Evidence:**
     - Model exists (`app/Models/Slider.php`) but is never actually used in the codebase
     - Imported in `HomeController` but no `Slider::` queries found
     - No migration file found for this table
     - No controller methods or views reference this table
   - **Recommendation:** Can be safely removed as it appears to be leftover from an incomplete or deprecated feature

12. **testimonials** ⚠️ **LEGACY/DEPRECATED**
   - **Status:** Legacy code from an incomplete feature / A planned feature that was never implemented / Deprecated functionality
   - **Evidence:**
     - Model exists (`app/Models/Testimonial.php`) but is never actually used in the codebase
     - Imported in `HomeController` but no `Testimonial::` queries found
     - No controller methods or views reference this table
   - **Recommendation:** Can be safely removed as it appears to be leftover from an incomplete or deprecated feature

13. **our_services** ⚠️ **LEGACY/DEPRECATED**
   - **Status:** Legacy code from an incomplete feature / A planned feature that was never implemented / Deprecated functionality
   - **Evidence:**
     - Model exists (`app/Models/OurService.php`) but is never actually used in the codebase
     - Imported in `HomeController` but no `OurService::` queries found
     - No controller methods or views reference this table
   - **Recommendation:** Can be safely removed as it appears to be leftover from an incomplete or deprecated feature

## Verified Tables (Confirmed as IN USE)

The following tables were verified and confirmed as actively used:

1. **website_settings** ✅ **VERIFIED - IN USE**
   - **Usage Evidence:**
     - Used in `CRMUtilityController::websiteSetting()` method (lines 293, 297, 317)
     - Has active controller method for creating/updating website settings
     - Route: `/website_setting` (referenced in controller)
     - View: `crm.website_setting` (referenced in controller)
   - **Status:** ✅ **DO NOT DELETE** - Actively used for website configuration

2. **settings** ✅ **VERIFIED - IN USE**
   - **Usage Evidence:**
     - Used via `Settings` helper class (`app/Helpers/Settings.php`)
     - Helper queries: `Setting::where('office_id', '=', @Auth::user()->office_id)->first()`
     - Used in views for date/time formatting: `Settings::sitedata('date_format')` and `Settings::sitedata('time_format')`
     - Referenced in multiple blade templates (crm_client_detail.blade.php, etc.)
     - Registered as facade in `config/app.php`: `'Settings' => App\Helpers\Settings::class`
   - **Status:** ✅ **DO NOT DELETE** - Actively used for office-specific settings and date/time formatting

### Tables Already Dropped (16 tables):

The following tables were successfully dropped via migration `2025_12_23_175551_drop_unused_tables.php`:
- ✅ blog_categories
- ✅ blogs
- ✅ client_married_details
- ✅ client_ratings
- ✅ cms_pages
- ✅ contacts
- ✅ currencies
- ✅ theme_options
- ✅ verified_numbers
- ✅ why_chooseuses
- ✅ enquiries
- ✅ enquiry_sources
- ✅ lead_services
- ✅ leads
- ✅ our_offices
- ✅ password_resets

## Important Notes

⚠️ **Before deleting any tables, please:**

1. **Verify manually** - Some tables might be used in:
   - Raw SQL queries not detected by the analysis
   - External applications/services
   - Scheduled jobs or background processes
   - Views or stored procedures

2. **Check for data** - Some tables might contain important historical data:
   ```sql
   SELECT COUNT(*) FROM table_name;
   ```

3. **Check foreign key dependencies** - Even if not directly referenced, tables might be referenced by other tables:
   ```sql
   SELECT * FROM information_schema.KEY_COLUMN_USAGE 
   WHERE REFERENCED_TABLE_NAME = 'table_name';
   ```

4. **Backup first** - Always backup your database before deleting tables:
   ```bash
   mysqldump -u username -p database_name > backup.sql
   ```

5. **Special considerations:**
   - `personal_access_tokens` - Might be used by Laravel Sanctum for API authentication
   - `password_resets` - Legacy Laravel table (might be replaced by `password_reset_tokens`)
   - `leads` - The `Lead` model uses `admins` table, so `leads` table might be legacy

## Verified Tables (Previously Flagged, Now Confirmed as Used)

The following tables were initially flagged as potentially unused but have been verified as actively used:

1. **email_label_mail_report** ✅ **VERIFIED - IN USE**
   - **Type:** Pivot table for many-to-many relationship
   - **Purpose:** Links `email_labels` and `mail_reports` tables
   - **Usage Evidence:**
     - Used by `MailReport::labels()` relationship (line 83 in `app/Models/MailReport.php`)
     - Used by `EmailLabel::mailReports()` relationship (line 41 in `app/Models/EmailLabel.php`)
     - `EmailLabelController::apply()` uses `attach()` to add labels to emails (line 140)
     - `EmailLabelController::remove()` uses `detach()` to remove labels from emails (line 175)
     - `EmailUploadController` attaches labels when processing emails (line 703)
     - `ClientsController` loads emails with labels using `->with(['labels', 'attachments'])` (lines 6432, 6525)
     - `MailReport::scopeWithLabel()` queries this relationship for filtering (line 186)
     - API routes registered in `routes/clients.php` for label management
     - Migration file: `database/migrations/2025_10_25_172234_create_email_label_mail_report_pivot.php`
   - **Status:** ✅ **DO NOT DELETE** - Actively used in email label functionality

## Recommended Action Plan

1. **Phase 1 - Safe to remove (after verification):**
   - Tables with 0 rows and no foreign key references
   - Legacy tables explicitly replaced by newer ones

2. **Phase 2 - Archive first:**
   - Tables with data but confirmed unused
   - Export data to CSV/JSON before deletion

3. **Phase 3 - Keep for now:**
   - Tables that might be used by external systems
   - Tables with unclear usage patterns

## How to Verify Each Table

For each table, run these queries:

```sql
-- Check row count
SELECT COUNT(*) as row_count FROM table_name;

-- Check if referenced by foreign keys
SELECT 
    TABLE_NAME, 
    COLUMN_NAME, 
    REFERENCED_TABLE_NAME 
FROM information_schema.KEY_COLUMN_USAGE 
WHERE REFERENCED_TABLE_NAME = 'table_name';

-- Check if table has foreign keys to other tables
SELECT 
    TABLE_NAME, 
    COLUMN_NAME, 
    REFERENCED_TABLE_NAME 
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_NAME = 'table_name' 
AND REFERENCED_TABLE_NAME IS NOT NULL;
```

## Migration History

- **2025-12-23:** Created migration `2025_12_23_175551_drop_unused_tables.php` to drop 16 unused tables
- **2025-12-23:** Migration executed successfully - 16 tables removed from database

## Generated On
Date: 2025-12-23 07:47:49
Last Updated: 2025-12-23 (after dropping 16 tables)

