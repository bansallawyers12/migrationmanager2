-- ============================================
-- Verification Query - Check if Tables Were Removed
-- ============================================
-- This query will return 0 rows if all tables are successfully removed
-- If any tables still exist, they will be listed here

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
  )
ORDER BY table_name;

-- ============================================
-- Alternative: Count how many tables still exist
-- ============================================
-- Uncomment the query below to see a count instead

-- SELECT COUNT(*) as remaining_tables
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
-- Expected result: 0 (if all tables removed successfully)
