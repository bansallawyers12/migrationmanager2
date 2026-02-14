# Plan: Rename "User" to Client/Staff Terminology

**Status:** Planning only — do not apply until approved.  
**Created:** 2026-02-14

---

## Overview

This plan covers renaming variables, UI text, comments, and database columns where:
- **Clients/Leads** are incorrectly called "user" → rename to "client"
- **Staff** are incorrectly called "user" → rename to "staff"

---

## Phase 0: Pre-requisites & Risk Mitigation

### 0.1 Before Starting
- [ ] Create a full database backup
- [ ] Create a new Git branch: `refactor/user-to-client-staff-rename`
- [ ] Run full test suite and document baseline
- [ ] Schedule maintenance window if production changes are involved

### 0.2 Scope Exclusions (Do NOT Change)
- `Auth::user()` — Laravel auth convention
- Policy method parameter `$user` — Laravel policy convention
- `$request->user()` in Client Portal API — authenticated client
- `device_tokens.user_id`, `refresh_tokens.user_id` — Client Portal; FK to admins (clients). **Keep as `user_id`** per existing plan (or rename to `client_id` only if no conflict)
- `usertype` / `UserRole` — role/type relationship name; leave as-is unless explicitly in scope
- Third-party files (tinymce, jquery.dataTables.js, etc.)

---

## Phase 1: Code-Only Changes (No Database Migrations)

Apply these changes first. No DB schema changes; safe to deploy independently.

### 1.1 Clients/Leads — Variable & Comment Renames

| # | File | Change |
|---|------|--------|
| 1 | `app/Http/Controllers/CRM/ClientsController.php` | `$userInfo` → `$clientInfo`, `$userPhone` → `$clientPhone` (lines 3162-3167) |
| 2 | `app/Http/Controllers/CRM/ClientsController.php` | `$user_type` → `$client_type` (lines 5548, 5576, 5587) |
| 3 | `app/Http/Controllers/CRM/AssigneeController.php` | `$user_name` → `$client_name` (lines 371-377) |
| 4 | `resources/views/crm/assignee/index.blade.php` | `$user_name` → `$client_name` (lines 114-121) |
| 5 | `resources/views/crm/assignee/completed.blade.php` | `$user_name` → `$client_name` (lines 99-106) |
| 6 | `resources/views/crm/assignee/assign_to_me.blade.php` | `$user_name` → `$client_name` (lines 110-112, 286-288) |
| 7 | `resources/views/crm/assignee/assign_by_me.blade.php` | `$user_name` → `$client_name` (lines 156, 165) |
| 8 | `resources/views/crm/assignee/action_completed.blade.php` | `$user_name` → `$client_name` (lines 278, 287) |
| 9 | `app/Http/Controllers/API/ClientPortalMessageController.php` | Log keys `'user_id'` → `'client_id'`, `'user_name'` → `'client_name'` (lines 1034-1035) |

### 1.2 Staff — UI Text (Labels, Buttons, Placeholders)

| # | File | Change |
|---|------|--------|
| 10 | `resources/views/crm/clients/modals/client-management.blade.php` | "Assign User" → "Assign Staff" (modal title, button) |
| 11 | `resources/views/crm/clients/modals/client-management.blade.php` | "Search or select users..." → "Search or select staff..." |
| 12 | `resources/views/crm/assignee/index.blade.php` | "Re-Assign User", "Assign User" → "Re-Assign Staff", "Assign Staff" |
| 13 | `resources/views/crm/assignee/completed.blade.php` | "Re-Assign User", "Assign User" → "Re-Assign Staff", "Assign Staff" |
| 14 | `resources/views/crm/assignee/assign_to_me.blade.php` | "Re-Assign User", "Assign User" → "Re-Assign Staff", "Assign Staff" (4 places) |
| 15 | `app/Http/Controllers/CRM/ClientsController.php` | Comment "Assign User popup" → "Assign Staff popup" (line 5617) |
| 16 | `resources/views/crm/assignee/action_completed.blade.php` | Comment "Assign user" → "Assign staff" (line 464) |

### 1.3 Staff — Model Comments

| # | File | Change |
|---|------|--------|
| 17 | `app/Models/Lead.php` | "Get the user who created this lead" → "Get the staff member who created this lead" (line 92-93) |

### 1.4 Staff — Service/API Response Keys (Client-Facing)

| # | File | Change |
|---|------|--------|
| 18 | `app/Services/UserLoginAnalyticsService.php` | `'user_name'` → `'staff_name'`, fallback `"User #..."` → `"Staff #..."` (line 259) |
| 19 | `resources/views/crm/user-login-analytics/index.blade.php` | `user.user_name` → `user.staff_name` (lines 408, 417) |
| 20 | `resources/views/crm/broadcasts/index.blade.php` | `User #${recipient.receiver_id}` → `Staff #${recipient.receiver_id}` (lines 1093, 1462) |

### 1.5 ImportUser Class Rename (Optional)

| # | File | Change |
|---|------|--------|
| 21 | `app/Imports/ImportUser.php` | Rename class `ImportUser` → `ImportAgent` (imports agent details, not users) |
| 22 | Any controller using `ImportUser` | Update `use` statements and instantiation |

**Note:** This class imports agent details (`AgentDetails` model), not users, so the rename would improve clarity. Only do this if agent import is still in use.

---

## Phase 2: JS/CSS Identifiers (Assign Staff Modal)

These changes affect DOM IDs, class names, and JS functions. Must be done atomically.

### 2.1 client-management.blade.php

| Element | Current | New |
|---------|---------|-----|
| CSS classes | `assign-user-modal`, `assign-user-header`, `assign-user-body`, `btn-assign-user` | `assign-staff-modal`, `assign-staff-header`, `assign-staff-body`, `btn-assign-staff` |
| Input ID | `user-search-input` | `staff-search-input` |
| Placeholder | "Search or select users..." | "Search or select staff..." |
| Span ID | `selected-users-display` | `selected-staff-display` |
| Menu ID | `userDropdownMenu` | `staffDropdownMenu` |
| Button IDs | `select-all-users`, `select-none-users` | `select-all-staff`, `select-none-staff` |
| Container ID | `users-list` | `staff-list` |
| Item class | `user-item`, `modern-user-item` | `staff-item`, `modern-staff-item` |
| Span classes | `user-name`, `user-branch` | `staff-name`, `staff-branch` |
| Button ID | `assignUser` | `assignStaff` |

### 2.2 addclientmodal.blade.php

| Element | Current | New |
|---------|---------|-----|
| Function | `filterUsers()` | `filterStaff()` |
| Selector | `.user-item` | `.staff-item` |
| Variable | `userName` | `staffName` |
| Button ID | `select-all-users` | `select-all-staff` |
| Variable | `userItem` | `staffItem` |
| `#select-all-users` references | All | `#select-all-staff` |

### 2.3 components/dashboard/modals.blade.php

| Element | Current | New |
|---------|---------|-----|
| Button ID | `dashboard-select-all-users` | `dashboard-select-all-staff` |
| Button ID (submit) | `dashboard_assignUser` | `dashboard_assignStaff` |
| Item class | `user-item`, `modern-user-item` | `staff-item`, `modern-staff-item` |

### 2.4 CSS in client-management.blade.php

Update all selectors that reference:
- `#create_action_popup .user-item`
- `.modern-user-item`
- `.btn-assign-user`

### 2.5 JavaScript Event Handlers

Update jQuery/JS event handlers that reference the button:

| File | Current | New |
|------|---------|-----|
| `resources/views/crm/assignee/index.blade.php` | `$('#assignUser').click()` | `$('#assignStaff').click()` |
| `resources/views/crm/assignee/completed.blade.php` | `$('#assignUser').click()` | `$('#assignStaff').click()` |
| `resources/views/crm/assignee/assign_to_me.blade.php` | `$('#assignUser').click()` | `$('#assignStaff').click()` |
| `resources/views/crm/assignee/action_completed.blade.php` | `$('#assignUser').click()` | `$('#assignStaff').click()` |
| `public/js/crm/clients/detail-main.js` | `$('#assignUser').on('click')` | `$('#assignStaff').on('click')` |
| `resources/views/components/dashboard/modals.blade.php` | Event handler for `#dashboard_assignUser` | Update to `#dashboard_assignStaff` |

---

## Phase 3: Route & View Path (Optional)

Rename "user-login-analytics" to "staff-login-analytics" for consistency.

| # | File | Change |
|---|------|--------|
| 1 | `routes/web.php` | Route name/path `user-login-analytics` → `staff-login-analytics` |
| 2 | `app/Http/Controllers/CRM/UserLoginAnalyticsController.php` | Rename to `StaffLoginAnalyticsController` (or keep controller name, update view path only) |
| 3 | `resources/views/crm/user-login-analytics/` | Rename directory to `staff-login-analytics/` |
| 4 | `resources/views/crm/broadcasts/index.blade.php` | Update `route('user-login-analytics.index')` → `route('staff-login-analytics.index')` |
| 5 | All links/redirects | Update any references to old route |

**Note:** This may break bookmarks. Consider keeping route as `user-login-analytics` and only renaming internal variable/service names.

---

## Phase 4: Database Column Renames (Migrations)

**Warning:** Column renames require migrations and will affect all code that reads/writes these columns.

### 4.1 Columns to Rename: Staff References (`user_id` → `staff_id`)

These tables have `user_id` that references **staff** (Admin with role != 7, or Staff table after dedicated staff migration):

| Table | Column | References | New Column | Migration Order |
|-------|--------|------------|------------|------------------|
| `admins` | `user_id` | Staff (lead owner) | `staff_id` | 1 |
| `user_logs` | `user_id` | Staff (login log) | `staff_id` | 2 |
| `notes` | `user_id` | Staff (note owner) | `staff_id` | 3 |
| `notes` | `assigned_to` | Staff | Keep or rename to `assigned_staff_id` | Optional |
| `email_labels` | `user_id` | Staff (label owner) | `staff_id` | 4 |
| `documents` | `user_id` | Staff (document owner) | `staff_id` | 5 |
| `documents` | `created_by` | Staff | Keep (semantic) or rename | Optional |
| `mail_reports` | `user_id` | Staff (sender) | `staff_id` | 6 |
| `applications` | `user_id` | Staff (creator) | `staff_id` | 7 |
| `checkin_log` | `user_id` | Staff (attending staff) | `staff_id` | 8 |
| `sessions` | `user_id` | Staff | `staff_id` | 9 |

### 4.2 Columns to Rename: Client References

| Table | Column | References | New Column | Notes |
|-------|--------|------------|------------|-------|
| `device_tokens` | `user_id` | Client (admins) | `client_id` | Client Portal only |
| `refresh_tokens` | `user_id` | Client (admins) | `client_id` | Client Portal only |

**Caution:** `client_id` may already exist in some tables for a different purpose (e.g. `client_matters.client_id` = the client for the matter). Verify before renaming.

### 4.3 client_matters.user_id (Verify If Column Exists)

**Status:** Code sets `user_id` in LeadConversionController and ClientsController changetype methods, BUT `ClientMatter` model `$fillable` does NOT include `user_id`.

**Investigation needed:**
1. Check if `client_matters` table actually has a `user_id` column in production/dev DB
2. If column exists: It likely references **staff** (the person who created/assigned the matter)
3. If column does NOT exist: Remove the code that attempts to set `user_id` in controllers

**If column exists:**
- **Action:** Add migration to rename `user_id` → `created_by_staff_id` or `assigned_staff_id` (more descriptive than generic `staff_id`)
- **Action:** Add to `ClientMatter` model `$fillable`

**If column does NOT exist:**
- **Action:** Remove these lines:
  - `LeadConversionController.php` line 79: `$matter->user_id = ...`
  - `ClientsController.php` line 5552: `$matter->user_id = ...`
  - `ClientsController.php` line 5555: `$obj->user_id = ...`

### 4.4 Migration Strategy

**Option A: Single Migration per Table**
- One migration per table for clarity and easy rollback.

**Option B: Batched Migrations**
- Migration 1: `admins.user_id` → `admins.staff_id`
- Migration 2: `user_logs`, `notes`, `email_labels` (audit/log tables)
- Migration 3: `documents`, `mail_reports`, `applications` (content tables)
- Migration 4: `checkin_log`, `sessions`, `client_matters` (operational tables)
- Migration 5: `device_tokens`, `refresh_tokens` (client portal)

**Recommended:** Option A for maximum control and rollback granularity.

### 4.5 Migration Template (Example)

```php
// database/migrations/YYYY_MM_DD_HHMMSS_rename_user_id_to_staff_id_on_admins.php
public function up()
{
    Schema::table('admins', function (Blueprint $table) {
        $table->renameColumn('user_id', 'staff_id');
    });
}

public function down()
{
    Schema::table('admins', function (Blueprint $table) {
        $table->renameColumn('staff_id', 'user_id');
    });
}
```

### 4.6 Model Updates After Column Renames

| Model | Update |
|-------|--------|
| `Admin` | `$fillable`: replace `user_id` with `staff_id`; update `createdBy()` if it uses `user_id` |
| `Lead` | `createdBy()`: change `'user_id'` to `'staff_id'` in belongsTo |
| `UserLog` | `$fillable` / `$casts`: `user_id` → `staff_id` |
| `Note` | `$fillable`: `user_id` → `staff_id` |
| `EmailLabel` | `$fillable`: `user_id` → `staff_id` |
| `Document` | `$fillable`: `user_id` → `staff_id` |
| `MailReport` | `$fillable`: `user_id` → `staff_id` |
| `ClientMatter` | Add `staff_id` to `$fillable` if column exists |
| `CheckinLog` | `user_id` → `staff_id` |
| `DeviceToken`, `RefreshToken` | `user_id` → `client_id` |

---

## Phase 5: Full Codebase Update After Migrations

After migrations run, update all PHP/Blade/JS that reference old column names:

### 5.1 Grep for Old Column Names
```bash
rg "user_id" --type php
rg "'user_id'" app/
rg "user_id" resources/
```

### 5.2 Key Files to Update (staff_id)
- `app/Http/Controllers/CRM/ClientsController.php` — multiple `user_id` assignments
- `app/Http/Controllers/CRM/Leads/LeadConversionController.php`
- `app/Http/Controllers/CRM/ClientAccountsController.php`
- `app/Http/Controllers/API/ClientPortalController.php` (for client_id on device_tokens/refresh_tokens)
- `app/Http/Controllers/Auth/AdminLoginController.php`
- All views that use `$list->user_id`, `$fetch->user_id`, etc.
- `resources/views/crm/assignee/*.blade.php`
- `resources/views/crm/clients/tabs/*.blade.php`
- `resources/views/crm/officevisits/index.blade.php`

### 5.3 Constants
- `config/constants.php` line 55: `'reception_user_id'` → `'reception_staff_id'` (references staff member for office visit notifications)
- Update all code using `config('constants.reception_user_id')` → `config('constants.reception_staff_id')`

### 5.4 Service/Controller Renames (Optional - Phase 3 Alternative)

If you decide to rename "user-login-analytics" services but keep the route:

| Class | Current | New |
|-------|---------|-----|
| `app/Services/UserLoginAnalyticsService.php` | `UserLoginAnalyticsService` | `StaffLoginAnalyticsService` |
| `app/Http/Controllers/CRM/UserLoginAnalyticsController.php` | `UserLoginAnalyticsController` | `StaffLoginAnalyticsController` |
| `app/Models/UserLog.php` | `UserLog` | `StaffLog` (and table `user_logs` → `staff_logs`) |

**Note:** Renaming `UserLog` model and table is high-risk. Only do this if you're already running Phase 4 migrations.

---

## Phase 6: Testing Checklist

### 6.1 Client/Lead Features
- [ ] Lead creation and assignment (uses `admins.user_id` → becomes `staff_id`)
- [ ] Lead-to-client conversion (sets `user_id` in multiple places)
- [ ] Client detail view (displays client info, not user info)
- [ ] Client search and filtering

### 6.2 Staff Assignment Features
- [ ] Assign Staff modal (select staff, assign to client/note)
  - [ ] Modal opens and displays staff list correctly
  - [ ] Search/filter staff works
  - [ ] Select all/none buttons work
  - [ ] Assignment saves correctly
- [ ] Dashboard "Assign Staff" functionality
- [ ] Assignee pages (index, completed, assign_to_me, action_completed)
  - [ ] Staff names display correctly (not "user_name")
  - [ ] Re-assign staff works

### 6.3 Staff Activity Tracking
- [ ] Staff login analytics (page loads, data displays)
- [ ] Office visits / check-in log (staff check-ins work)
- [ ] Staff activity logs (`user_logs` table)
- [ ] Reception staff notifications (uses `reception_staff_id`)

### 6.4 Document & Content Ownership
- [ ] Document upload and ownership (uses `documents.user_id` → becomes `staff_id`)
- [ ] Notes creation and assignment (uses `notes.user_id` → becomes `staff_id`)
- [ ] Email labels (staff-owned labels work)
- [ ] Mail reports (email tracking by staff)

### 6.5 Client Portal
- [ ] Client Portal login and authentication
- [ ] Client Portal messaging (unread count, send/receive)
- [ ] Client Portal API (device tokens, refresh tokens with `user_id` or `client_id`)
- [ ] Client Portal mobile app (if applicable)

### 6.6 Notifications & Communication
- [ ] Broadcast notifications (staff-to-staff)
- [ ] Client notifications (staff-to-client)
- [ ] SMS notifications

### 6.7 Regression Testing
- [ ] Full regression test suite
- [ ] Check all pages that list staff (dropdowns, selects)
- [ ] Check all pages that display client/lead info
- [ ] Verify no broken queries (grep for old column names)

---

## Execution Order Summary

| Phase | Description | Risk |
|-------|-------------|------|
| 1 | Code-only (variables, UI text, comments) | Low |
| 2 | JS/CSS identifiers (Assign Staff modal) | Low |
| 3 | Route/view rename (user-login-analytics) | Low |
| 4 | Database migrations (column renames) | **High** |
| 5 | Codebase update for new column names | Medium |
| 6 | Testing | — |

**Recommended approach:** Complete Phases 1–2 first, deploy, verify. Then do Phase 4–5 in a separate release with a maintenance window.

---

## Rollback Plan

- **Phase 1–2:** Revert commits; no DB impact.
- **Phase 3:** If route was renamed, update bookmarks/links or use route alias to maintain backward compatibility.
- **Phase 4:** Run `php artisan migrate:rollback` for each migration. Ensure `down()` correctly restores old column names.
- **Phase 5:** Revert code changes; if migrations rolled back, old column names work again.

---

## Additional Notes & Recommendations

### 1. Client Matters `user_id` Column Investigation
Before starting Phase 4, run this query to check if the column exists:
```sql
SELECT column_name FROM information_schema.columns 
WHERE table_name = 'client_matters' AND column_name = 'user_id';
```

If it doesn't exist, clean up the controller code that tries to set it.

### 2. Foreign Key Considerations
When renaming columns with foreign keys:
1. Drop the foreign key first
2. Rename the column
3. Re-create the foreign key with the new column name

Example migration:
```php
public function up()
{
    Schema::table('admins', function (Blueprint $table) {
        // Drop FK if it exists
        $table->dropForeign(['user_id']); // or specific constraint name
        
        // Rename column
        $table->renameColumn('user_id', 'staff_id');
        
        // Re-add FK (update this based on your target table: staff or admins)
        $table->foreign('staff_id')->references('id')->on('staff')->onDelete('set null');
    });
}
```

### 3. Staff Table Migration Dependency
If you're also implementing the dedicated `staff` table migration from `PLAN_DEDICATED_STAFF_TABLE.md`, coordinate the timing:
- Option A: Do staff table migration first, then rename columns to reference `staff` table
- Option B: Rename columns first (keeping FK to `admins`), then migrate to `staff` table and update FKs

**Recommended:** Option A (staff table first) to avoid renaming FKs twice.

### 4. Search for Hardcoded String References
After completing all phases, search for any missed references:
```bash
rg -i "assign user|assign-user" --type php --type blade
rg "user_id|user_name|userInfo|userName" app/ resources/
rg "filterUsers|assignUser" public/js/
```

### 5. API Breaking Changes
If Phase 4 renames `device_tokens.user_id` → `client_id`, this MAY break mobile API if:
- Mobile app sends `user_id` in requests
- API returns `user_id` in responses

**Mitigation:** Add API versioning or temporary field aliases during transition period.

---

*End of plan. Review complete.*
