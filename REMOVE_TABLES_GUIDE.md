# Guide to Remove Tables from PostgreSQL using pgAdmin

## Method 1: Using pgAdmin GUI (Individual Tables)

### Steps:
1. **Open pgAdmin** and connect to your PostgreSQL server
2. **Navigate to your database:**
   - Expand: Servers → Your Server → Databases → Your Database → Schemas → public → Tables
3. **For each table to remove:**
   - Right-click on the table name
   - Select **"Delete/Drop"** from the context menu
   - In the confirmation dialog, check **"Cascade"** if you want to drop dependent objects
   - Click **"Yes"** to confirm

---

## Method 2: Using SQL Query (Recommended for Multiple Tables)

### Steps:
1. **Open pgAdmin** and connect to your PostgreSQL server
2. **Open Query Tool:**
   - Right-click on your database name
   - Select **"Query Tool"**
3. **Copy and paste the SQL commands below** into the query editor
4. **Review the commands** to ensure they're correct
5. **Execute** by clicking the "Execute" button (or press F5)

---

## SQL Commands to Remove All 30 Tables

```sql
-- ============================================
-- TABLES TO REMOVE FROM POSTGRESQL
-- Generated: 2025-12-20
-- ============================================

-- Backup Tables (Safe to remove - these are temporary backups)
DROP TABLE IF EXISTS admins_bkk_24oct2025 CASCADE;
DROP TABLE IF EXISTS client_eoi_references_backup_before_update CASCADE;
DROP TABLE IF EXISTS client_experiences_backup_before_update CASCADE;
DROP TABLE IF EXISTS client_occupations_backup_before_update CASCADE;
DROP TABLE IF EXISTS client_qualifications_backup_before_update CASCADE;
DROP TABLE IF EXISTS client_relationships_backup_before_update CASCADE;
DROP TABLE IF EXISTS refresh_tokens_bkk_27oct2025 CASCADE;
DROP TABLE IF EXISTS visa_document_types_bkk_31oct2025 CASCADE;

-- Unused/Deprecated Tables (Review before removing)
DROP TABLE IF EXISTS admin_to_client_mapping CASCADE;
DROP TABLE IF EXISTS api_tokens CASCADE;
DROP TABLE IF EXISTS attachments CASCADE;
DROP TABLE IF EXISTS blog_categories CASCADE;
DROP TABLE IF EXISTS blogs CASCADE;
DROP TABLE IF EXISTS categories CASCADE;
DROP TABLE IF EXISTS client_married_details CASCADE;
DROP TABLE IF EXISTS client_ratings CASCADE;
DROP TABLE IF EXISTS cms_pages CASCADE;
DROP TABLE IF EXISTS contacts CASCADE;
DROP TABLE IF EXISTS email_attachments CASCADE;
DROP TABLE IF EXISTS email_uploads CASCADE;
DROP TABLE IF EXISTS enquiries CASCADE;
DROP TABLE IF EXISTS enquiry_sources CASCADE;
DROP TABLE IF EXISTS home_contents CASCADE;
DROP TABLE IF EXISTS lead_services CASCADE;
DROP TABLE IF EXISTS our_offices CASCADE;
DROP TABLE IF EXISTS responsible_people CASCADE;
DROP TABLE IF EXISTS sliders CASCADE;
DROP TABLE IF EXISTS task_logs CASCADE;
DROP TABLE IF EXISTS theme_options CASCADE;
DROP TABLE IF EXISTS verified_numbers CASCADE;
```

---

## Method 3: Generate and Execute SQL Script

### Steps:
1. **Open Query Tool** in pgAdmin
2. **Copy the SQL above** into the query editor
3. **Review carefully** - especially check for:
   - Foreign key dependencies
   - Data you might want to backup first
4. **Execute the script** (F5 or Execute button)

---

## Important Notes:

### ⚠️ Before Removing Tables:

1. **Backup First:**
   ```sql
   -- Example: Backup a table before dropping
   CREATE TABLE table_name_backup AS SELECT * FROM table_name;
   ```

2. **Check Dependencies:**
   ```sql
   -- Check what depends on a table
   SELECT 
       tc.table_name, 
       kcu.column_name, 
       ccu.table_name AS foreign_table_name,
       ccu.column_name AS foreign_column_name 
   FROM information_schema.table_constraints AS tc 
   JOIN information_schema.key_column_usage AS kcu
     ON tc.constraint_name = kcu.constraint_name
   JOIN information_schema.constraint_column_usage AS ccu
     ON ccu.constraint_name = tc.constraint_name
   WHERE tc.constraint_type = 'FOREIGN KEY' 
     AND ccu.table_name = 'table_name';
   ```

3. **Check if Tables Have Data:**
   ```sql
   -- Check row count for each table
   SELECT 'table_name' as table_name, COUNT(*) as row_count FROM table_name;
   ```

### ✅ Safe Removal Order:

**Recommended order (remove backups first, then unused tables):**

1. **First:** Backup tables (8 tables)
   - These are clearly temporary backups
   
2. **Second:** Review and remove unused feature tables
   - Check if your application uses these features
   - `blog_categories`, `blogs` - Blog feature
   - `enquiries`, `enquiry_sources` - Enquiry system
   - `cms_pages`, `home_contents`, `sliders`, `theme_options` - CMS/Theme features
   - `task_logs` - Task logging
   - `lead_services` - Lead management
   - `our_offices` - Office management
   - `responsible_people` - People management
   - `contacts`, `verified_numbers` - Contact management

3. **Third:** Remove deprecated/legacy tables
   - `admin_to_client_mapping` - May be replaced by another system
   - `api_tokens` - May be using Laravel's `personal_access_tokens` instead
   - `attachments` - May be using another attachment system
   - `email_attachments`, `email_uploads` - May be integrated into `emails` table
   - `client_married_details` - May be merged into `client_spouse_details`
   - `client_ratings` - May not be in use
   - `categories` - May be replaced by another categorization system

---

## Verification After Removal:

```sql
-- Verify tables are removed
SELECT table_name 
FROM information_schema.tables 
WHERE table_schema = 'public' 
  AND table_name IN (
    'admins_bkk_24oct2025',
    'client_eoi_references_backup_before_update',
    'client_experiences_backup_before_update',
    'client_occupations_backup_before_update',
    'client_qualifications_backup_before_update',
    'client_relationships_backup_before_update',
    'refresh_tokens_bkk_27oct2025',
    'visa_document_types_bkk_31oct2025',
    'admin_to_client_mapping',
    'api_tokens',
    'attachments',
    'blog_categories',
    'blogs',
    'categories',
    'client_married_details',
    'client_ratings',
    'cms_pages',
    'contacts',
    'email_attachments',
    'email_uploads',
    'enquiries',
    'enquiry_sources',
    'home_contents',
    'lead_services',
    'our_offices',
    'responsible_people',
    'sliders',
    'task_logs',
    'theme_options',
    'verified_numbers'
  );
-- Should return 0 rows if all tables are removed
```

---

## Quick Copy-Paste SQL (All in One):

```sql
-- Remove all 30 tables that don't exist in MySQL
BEGIN;

DROP TABLE IF EXISTS admins_bkk_24oct2025 CASCADE;
DROP TABLE IF EXISTS client_eoi_references_backup_before_update CASCADE;
DROP TABLE IF EXISTS client_experiences_backup_before_update CASCADE;
DROP TABLE IF EXISTS client_occupations_backup_before_update CASCADE;
DROP TABLE IF EXISTS client_qualifications_backup_before_update CASCADE;
DROP TABLE IF EXISTS client_relationships_backup_before_update CASCADE;
DROP TABLE IF EXISTS refresh_tokens_bkk_27oct2025 CASCADE;
DROP TABLE IF EXISTS visa_document_types_bkk_31oct2025 CASCADE;
DROP TABLE IF EXISTS admin_to_client_mapping CASCADE;
DROP TABLE IF EXISTS api_tokens CASCADE;
DROP TABLE IF EXISTS attachments CASCADE;
DROP TABLE IF EXISTS blog_categories CASCADE;
DROP TABLE IF EXISTS blogs CASCADE;
DROP TABLE IF EXISTS categories CASCADE;
DROP TABLE IF EXISTS client_married_details CASCADE;
DROP TABLE IF EXISTS client_ratings CASCADE;
DROP TABLE IF EXISTS cms_pages CASCADE;
DROP TABLE IF EXISTS contacts CASCADE;
DROP TABLE IF EXISTS email_attachments CASCADE;
DROP TABLE IF EXISTS email_uploads CASCADE;
DROP TABLE IF EXISTS enquiries CASCADE;
DROP TABLE IF EXISTS enquiry_sources CASCADE;
DROP TABLE IF EXISTS home_contents CASCADE;
DROP TABLE IF EXISTS lead_services CASCADE;
DROP TABLE IF EXISTS our_offices CASCADE;
DROP TABLE IF EXISTS responsible_people CASCADE;
DROP TABLE IF EXISTS sliders CASCADE;
DROP TABLE IF EXISTS task_logs CASCADE;
DROP TABLE IF EXISTS theme_options CASCADE;
DROP TABLE IF EXISTS verified_numbers CASCADE;

COMMIT;
```

**Note:** The `BEGIN;` and `COMMIT;` wrap the operations in a transaction. If any table fails to drop, you can rollback with `ROLLBACK;` instead of `COMMIT;`.
