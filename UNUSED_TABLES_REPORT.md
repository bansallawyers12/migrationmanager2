# Unused Database Tables Report

## Summary
- **Total tables in database:** 136
- **Used tables:** 110
- **Potentially unused tables:** 26

## List of Potentially Unused Tables

The following tables appear to be unused based on analysis of:
- Model definitions
- Code references (DB::table, ->from, ->join, etc.)
- Foreign key relationships
- Migration files

### Unused Tables:

1. **admin_to_client_mapping** - No model or code references found
2. **api_tokens** - No model or code references found
3. **application_notes** - No model or code references found
4. **attachments** - No model or code references found (Note: There's an `Attachment` model but it may use a different table)
5. **blog_categories** - No model or code references found
6. **blogs** - No model or code references found
7. **client_married_details** - No model or code references found
8. **client_ratings** - No model or code references found
9. **cms_pages** - No model or code references found
10. **contacts** - No model or code references found (Note: There's a `Contact` model but it may use a different table)
11. **currencies** - No model or code references found
12. **email_attachments** - No model or code references found
13. **email_uploads** - No model or code references found
14. **enquiries** - No model or code references found
15. **enquiry_sources** - No model or code references found
16. **lead_services** - No model or code references found
17. **leads** - No model or code references found (Note: The `Lead` model uses the `admins` table instead)
18. **our_offices** - No model or code references found
19. **password_resets** - No model or code references found (Note: Laravel may use `password_reset_tokens` instead)
20. **personal_access_tokens** - No model or code references found (Note: This might be used by Laravel Sanctum)
21. **responsible_people** - No model or code references found
22. **task_logs** - No model or code references found
23. **tbl_paid_appointment_payment** - No model or code references found
24. **theme_options** - No model or code references found
25. **verified_numbers** - No model or code references found
26. **why_chooseuses** - No model or code references found

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

## Generated On
Date: 2025-12-23 07:47:49

