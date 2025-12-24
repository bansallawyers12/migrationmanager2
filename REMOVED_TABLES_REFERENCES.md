# Files That Reference Removed Database Tables

This document lists all files that reference the following removed tables:
- `lead_followups`
- `nature_of_enquiry`
- `service_fee_options`
- `service_fee_option_types`
- `sub_categories`
- `test_scores`
- `tasks`
- `users`

---

## 1. lead_followups

### Models
- `app/Models/LeadFollowup.php` - **ENTIRE MODEL FILE** (needs removal or refactoring)

### Controllers
- `app/Http/Controllers/CRM/Leads/LeadFollowupController.php` - **ENTIRE CONTROLLER** (uses LeadFollowup model extensively)

### Services
- `app/Services/LeadFollowupService.php` - **ENTIRE SERVICE** (uses LeadFollowup model extensively)
- `app/Services/LeadAnalyticsService.php` - Uses LeadFollowup model

### Commands
- `app/Console/Commands/MarkOverdueFollowups.php` - Uses LeadFollowupService
- `app/Console/Commands/SendFollowupReminders.php` - Uses LeadFollowupService

### Routes
- `routes/web.php` - Lines 13, 206-213 (LeadFollowupController routes)

### Relationships in Other Models
- `app/Models/Admin.php` - Line 166 (hasMany relationship to LeadFollowup)
- `app/Models/Lead.php` - Lines 145-146, 169 (references LeadFollowup model)

### Migrations
- `database/migrations/2025_10_15_133758_create_lead_followups_table.php` - Migration file (can be removed)
- `database/migrations/2025_12_24_000001_drop_unused_legacy_tables.php` - Drop migration (already executed)

---

## 2. nature_of_enquiry

### JavaScript Files
- `public/js/crm/clients/detail-main.js` - Lines 5859, 5889, 5903 (references nature_of_enquiry tab)

### Documentation
- `app/Models/model_list.txt` - Line 71 (lists NatureOfEnquiry.php model)
- `CRM_SYSTEM_DOCUMENTATION.md` - Line 184 (documents NatureOfEnquiry model)

### Migrations
- `database/migrations/2025_12_24_000001_drop_unused_legacy_tables.php` - Drop migration (already executed)

**Note:** The NatureOfEnquiry model file itself was not found, suggesting it may have already been removed.

---

## 3. service_fee_options

### Models
- `app/Models/ServiceFeeOption.php` - **ENTIRE MODEL FILE** (needs removal)

### Controllers
- `app/Http/Controllers/CRM/ClientsController.php` - Lines 15-16 (imports ServiceFeeOption and ServiceFeeOptionType)

### Documentation
- `app/Models/model_list.txt` - Line 88 (lists ServiceFeeOption.php model)

### Migrations
- `database/migrations/2025_12_24_000001_drop_unused_legacy_tables.php` - Drop migration (already executed)

**Note:** Check ClientsController for actual usage of ServiceFeeOption model (may need to remove imports if not used).

---

## 4. service_fee_option_types

### Models
- `app/Models/ServiceFeeOptionType.php` - **ENTIRE MODEL FILE** (needs removal)

### Controllers
- `app/Http/Controllers/CRM/ClientsController.php` - Lines 15-16 (imports ServiceFeeOption and ServiceFeeOptionType)

### Documentation
- `app/Models/model_list.txt` - Line 89 (lists ServiceFeeOptionType.php model)

### Migrations
- `database/migrations/2025_12_24_000001_drop_unused_legacy_tables.php` - Drop migration (already executed)

**Note:** Check ClientsController for actual usage of ServiceFeeOptionType model (may need to remove imports if not used).

---

## 5. sub_categories

### Models
- `app/Models/SubCategory.php` - **ENTIRE MODEL FILE** (needs removal)

### Controllers
- `app/Http/Controllers/CRM/CRMUtilityController.php` - Line 1375 (uses SubCategory::where('cat_id', $catid)->get())

### Documentation
- `app/Models/model_list.txt` - Line 96 (lists SubCategory.php model)

### Migrations
- `database/migrations/2025_12_24_000001_drop_unused_legacy_tables.php` - Drop migration (already executed)

**Note:** The `getsubcategories` method in CRMUtilityController needs to be updated or removed.

---

## 6. test_scores

### Controllers
- `app/Http/Controllers/CRM/ClientsController.php` - Line 3985 (case 'test_scores')
- `app/Http/Controllers/CRM/ClientPersonalDetailsController.php` - Line 1729 (case 'test_scores')
- `app/Http/Controllers/API/ClientPortalPersonalDetailsController.php` - Lines 56, 251, 253, 258, 262, 3754, 4010, 9627-9657, 9663, 9668, 9957 (extensive test_scores usage)

### JavaScript Files
- `public/js/clients/edit-client.js` - Line 5692 (formData.append('section', 'test_scores'))

### Views
- `resources/views/crm/clients/editclientmodal.blade.php` - Line 115 (comment references old test_scores table)
- `resources/views/crm/clients/client_detail_info.blade.php` - Multiple lines (3003, 3092, 3145, 3160, 3266) - test score form handling

### Routes
- `routes/clients.php` - Line 35 (edit-test-scores route)
- `routes/api.php` - Line 74 (update-client-testscore-detail route)

### Documentation/Collections
- `Client_Portal_Postman_Collection.json` - Multiple references to test_scores (lines 2517, 2522, 2801, 6358, 6397, 6424, 6446, 6473)

### Migrations
- `database/migrations/2025_12_24_000001_drop_unused_legacy_tables.php` - Drop migration (already executed)

**Note:** The system appears to have migrated from `test_scores` table to `client_testscore` table. Most code references are now using `ClientTestScore` model, but some string references to 'test_scores' still exist in code.

---

## 7. tasks

### Controllers
- `app/Http/Controllers/CRM/ClientsController.php` - Lines 8967, 9039, 9086, 9093 (references to tasks in comments/code)
- `app/Http/Controllers/CRM/AssigneeController.php` - Multiple references to tasks (lines 123, 141, 250, 299, 367-368, 417, 507, 812, 840, 896)

### JavaScript Files
- `public/js/crm/clients/detail-main.js` - Lines 4269, 4436, 8817, 8883, 9936 (task group filtering and comments)
- `public/js/custom-form-validation.js` - Line 372, 1317, 2534, 2632 (task references)
- `public/js/agent-custom-form-validation.js` - Line 327, 345 (task references)
- `public/js/dashboard-optimized.js` - Multiple task-related functions (lines 281, 308, 311, 353, 358, 365, 376, 419, 439, 457, 477)

### Views
- `resources/views/AdminConsole/system/roles/create.blade.php` - Lines 276, 280-281, 284 (TASKS module access)
- `resources/views/AdminConsole/system/roles/edit.blade.php` - Lines 282, 286-287, 290 (TASKS module access)
- `resources/views/Elements/CRM/header_client_detail.blade.php` - Line 54 (tasks icon)
- `resources/views/crm/assignee/action.blade.php` - Line 596 (search tasks placeholder)
- `resources/views/crm/clients/analytics-dashboard.blade.php` - Line 853 (tasks icon)
- `resources/views/crm/dashboard.blade.php` - Line 321, 1384, 1409 (task modal and styling)
- `resources/views/crm/dashboard-optimized.blade.php` - Lines 97, 102, 106, 178, 183, 187, 419, 1255, 1263, 1267, 1393, 1426, 1428, 1523, 1560, 1562 (extensive task-related UI)
- `resources/views/crm/assignee/assign_by_me.blade.php` - Lines 186-187, 189, 232, 237, 276, 282, 310, 422, 436 (task-related UI)
- `resources/views/crm/assignee/action_completed.blade.php` - Line 251 (Personal Task group)
- `resources/views/components/dashboard/task-item.blade.php` - Line 6, 37 (task component)
- `resources/views/components/dashboard/task-detail-panel.blade.php` - Entire file (task detail panel)
- `resources/views/components/dashboard/modals.blade.php` - Line 6, 75 (task modal)
- `resources/views/layouts/crm_client_detail_dashboard.blade.php` - Line 1563 (task notification)
- `resources/views/layouts/crm_client_detail.blade.php` - Line 2529 (task notification)
- `resources/views/crm/clients/addclientmodal.blade.php` - Line 9 (comment about removed task modals)

### Services
- `app/Services/DashboardService.php` - Lines 86-87, 105, 242, 468 (task-related methods)

### Routes
- `routes/web.php` - Lines 287-288 (task management routes comment)

### CSS Files
- `public/css/task-popover-modern.css` - Entire file (task popover styling)
- `public/css/dashboard.css` - Lines 144, 181 (task and case list styling)

### Migrations
- `database/migrations/2025_12_24_000001_drop_unused_legacy_tables.php` - Drop migration (already executed)
- `database/migrations/2025_10_24_152207_add_composite_indexes_to_notes_table.php` - Lines 18, 30 (references to tasks in comments)

### Documentation
- `README.md` - Line 31 (Task Management feature documentation)
- `REMOVE_TABLES_GUIDE.md` - Line 132 (task_logs reference)
- `MYSQL_TO_POSTGRESQL_SYNTAX_REFERENCE.md` - Line 515 (task_status reference)

### Other
- `app/Console/Kernel.php` - Line 74 (comment about task removal)

**Important Note:** Most references to "tasks" in this codebase appear to refer to the Notes system with `task_group` field, NOT the old `tasks` table. The old tasks table/model appears to have been removed already, but many UI elements and comments still reference "tasks" conceptually. The actual functionality uses the `notes` table with a `task_group` field.

---

## 8. users

### Models
- `app/Models/User.php` - **ENTIRE MODEL FILE** (still exists and may be actively used)

### Controllers
- `app/Http/Controllers/API/UserController.php` - **ENTIRE FILE** (extensive User model usage - lines 9, 28, 30, 80, 95, 119, 122, 128)
- `app/Http/Controllers/API/RegisterController.php` - Lines 9, 71, 80, 103 (User model usage)
- `app/Http/Controllers/Auth/AuthController.php` - Lines 5, 43, 49 (User model usage)
- `app/Http/Controllers/AdminConsole/UserController.php` - May reference User (needs verification)

### Services
- `app/Services/BroadcastNotificationService.php` - Line 8 (imports User, but lines 504-509 show it's deprecated/unused)
- `app/Helpers/Helper.php` - Line 3 (imports User, but no actual usage found)

### Imports
- `app/Imports/ImportUser.php` - Line 3 (imports User, but actually uses AgentDetails - lines 41, 59)

### Database
- `app/Console/Commands/CronJob.php` - Line 148 (commented out DB::table('users'))

### Migrations
- `database/migrations/0001_01_01_000000_create_users_table.php` - **ENTIRE MIGRATION FILE** (creates users table)
- `database/migrations/2025_12_24_000001_drop_unused_legacy_tables.php` - Drop migration (already executed)

### Seeders/Factories
- `database/seeders/DatabaseSeeder.php` - Line 5 (imports User)
- `database/factories/EmailAccountFactory.php` - Line 6 (imports User)

### Tests
- `tests/Feature/AdminConsoleRoutesTest.php` - May reference User (needs verification)

### Documentation
- `CRM_SYSTEM_DOCUMENTATION.md` - Lines 275-276 (documents users table structure)

### Request Classes
- `app/Http/Requests/ProfileUpdateRequest.php` - Line 5 (imports User)

**Important Note:** The User model appears to still be actively used in API controllers for client portal registration and authentication. This may need careful review - the users table may have been replaced by the admins table, but the User model might still be in use for client portal functionality. Verify if this table removal was intentional or if client portal functionality still depends on it.

---

## Summary

### Critical Files Requiring Immediate Attention:

1. **lead_followups:**
   - Entire model, controller, service, and related files need review/removal
   - Multiple model relationships need updating

2. **service_fee_options & service_fee_option_types:**
   - Models need removal
   - ClientsController imports need verification/removal

3. **sub_categories:**
   - Model needs removal
   - CRMUtilityController::getsubcategories() method needs updating/removal

4. **users:**
   - **HIGH PRIORITY** - Still actively used in API controllers
   - User model and multiple controllers need review
   - May affect client portal functionality

### Medium Priority:

5. **test_scores:**
   - Mostly migrated to ClientTestScore, but string references remain
   - API documentation needs updating

6. **nature_of_enquiry:**
   - Only JavaScript references remain
   - View tab references need cleanup

### Low Priority (Conceptual References):

7. **tasks:**
   - Most references are to the Notes system (task_group), not the old table
   - UI and comments use "tasks" terminology but don't reference the table directly

---

**Last Updated:** Based on codebase search as of migration removal

