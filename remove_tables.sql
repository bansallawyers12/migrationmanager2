-- ============================================
-- Remove Tables from PostgreSQL
-- Tables that exist in PostgreSQL but NOT in MySQL
-- Generated: 2025-12-20
-- ============================================
-- 
-- INSTRUCTIONS:
-- 1. Open pgAdmin
-- 2. Right-click on your database → Query Tool
-- 3. Copy and paste this entire file
-- 4. Review the tables list
-- 5. Click Execute (F5) or press F5
-- ============================================

BEGIN;

-- Backup Tables (8 tables) - Safe to remove
DROP TABLE IF EXISTS admins_bkk_24oct2025 CASCADE;
DROP TABLE IF EXISTS client_eoi_references_backup_before_update CASCADE;
DROP TABLE IF EXISTS client_experiences_backup_before_update CASCADE;
DROP TABLE IF EXISTS client_occupations_backup_before_update CASCADE;
DROP TABLE IF EXISTS client_qualifications_backup_before_update CASCADE;
DROP TABLE IF EXISTS client_relationships_backup_before_update CASCADE;
DROP TABLE IF EXISTS refresh_tokens_bkk_27oct2025 CASCADE;
DROP TABLE IF EXISTS visa_document_types_bkk_31oct2025 CASCADE;

-- Unused/Deprecated Tables (22 tables) - Review before removing
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

-- Verification Query (run separately after execution)
-- SELECT table_name 
-- FROM information_schema.tables 
-- WHERE table_schema = 'public' 
--   AND table_name IN (
--     'admins_bkk_24oct2025', 'client_eoi_references_backup_before_update',
--     'client_experiences_backup_before_update', 'client_occupations_backup_before_update',
--     'client_qualifications_backup_before_update', 'client_relationships_backup_before_update',
--     'refresh_tokens_bkk_27oct2025', 'visa_document_types_bkk_31oct2025',
--     'admin_to_client_mapping', 'api_tokens', 'attachments', 'blog_categories',
--     'blogs', 'categories', 'client_married_details', 'client_ratings',
--     'cms_pages', 'contacts', 'email_attachments', 'email_uploads',
--     'enquiries', 'enquiry_sources', 'home_contents', 'lead_services',
--     'our_offices', 'responsible_people', 'sliders', 'task_logs',
--     'theme_options', 'verified_numbers'
--   );
-- Should return 0 rows if all tables are successfully removed
