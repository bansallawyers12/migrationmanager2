# TR Sheet with 4 Tabs — Implementation Plan

**Purpose:** Create **one** TR Sheet page with **4 tabs** (Ongoing, Lodged, Checklist, Discontinue), based on bansalcrm2's sheet structure.  
**Scope:** Plan only — no code changes applied until you approve.

---

## 1. Overview

### 1.1 Bansalcrm2 vs Migrationmanager2 (TR Sheet)

| Bansalcrm2 | Migrationmanager2 (TR) |
|------------|------------------------|
| 4 separate sheet routes (ongoing, coe-enrolled, discontinue, checklist) | **1 route** with **4 tabs** on the same page |
| `applications` table (one row per application) | `client_matters` table (one row per matter) |
| `applications.stage` (string) | `client_matters.workflow_stage_id` → `workflow_stages.name` |
| `applications.status` (2=Discontinue, 8=Refund) | `client_matters.matter_status` (1=active, 0=inactive) |
| `applications.checklist_sheet_status` | New: `client_matters.tr_checklist_status` (optional) |
| `client_ongoing_references` | New: `client_tr_references` (shared reference table) |

### 1.2 UX: One Sheet, Four Tabs

```
┌─────────────────────────────────────────────────────────────────┐
│  TR Sheet                                                        │
│  [Filters bar: Branch | Assignee | Stage | Visa | Search]        │
├─────────────────────────────────────────────────────────────────┤
│  [Ongoing] [Lodged] [Checklist] [Discontinue]  ← Tab navigation  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  [Table: one row per TR matter — filtered by active tab]         │
│  Course/Matter | CRM Ref | Name | DOB | Payment | ... | Comment  │
│  ...                                                             │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

- **URL:** `/clients/sheets/tr` or `/clients/sheets/tr?tab=ongoing` (default)
- **Tab change:** Preserves filters; only the data subset changes (same controller, different `buildBaseQuery` criteria).

---

## 2. Tab Criteria (TR-Specific Stage Mapping)

### 2.1 Config: `config/sheets/tr.php`

Create a new config file for TR stage mappings (mirrors bansalcrm2's `config/sheets.php`):

```php
<?php
return [
    /*
    |--------------------------------------------------------------------------
    | TR Sheet — Stage names per tab (workflow_stages.name)
    |--------------------------------------------------------------------------
    | Define which workflow stage names belong to each tab.
    | Case-insensitive matching. Add/remove stages to match your TR workflow.
    */
    'ongoing_stages' => [
        'Document received',
        'Visa applied',
        'Visa received',
        'Enrollment',
        // Add other ongoing stages (exclude Lodged, Checklist, Discontinue)
    ],

    'lodged_stages' => [
        'Lodged',
        'Submitted',
        // Equivalent to bansalcrm2's "COE Issued & Enrolled"
    ],

    'checklist_early_stages' => [
        'Awaiting documents',
        'Checklist',
        // Early-stage; pre-lodgement document collection
    ],

    'discontinue_stages' => [
        'Withdrawn',
        'Refund',
        'Discontinued',
        // Or rely on matter_status = 0
    ],

    /*
    | Stage to set when user selects "Convert to client" on Checklist tab.
    | Row moves from Checklist → Ongoing.
    */
    'checklist_convert_to_client_stage' => 'Document received',

    /*
    | Optional: TR matter identification (override default nick_name/title logic)
    */
    'matter_nick_names' => ['tr', 'tr checklist'],
    'matter_title_patterns' => ['tr', 'tr checklist', 'temporary residence'],
];
```

### 2.2 Tab Logic

| Tab | Criteria |
|-----|----------|
| **Ongoing** | `matter_status = 1`, `workflow_stages.name` IN `config tr.ongoing_stages` |
| **Lodged** | `matter_status = 1`, `workflow_stages.name` IN `config tr.lodged_stages` |
| **Checklist** | `matter_status = 1`, `workflow_stages.name` IN `config tr.checklist_early_stages`, AND (`tr_checklist_status` IS NULL OR IN ('active','hold')) |
| **Discontinue** | `matter_status = 0` OR `workflow_stages.name` IN `config tr.discontinue_stages` |

**Checklist tab extras (like bansalcrm2):**
- `tr_checklist_status`: `active` | `hold` | `convert_to_client` | `discontinue`
- Status dropdown per row; "Convert to client" → set stage to `checklist_convert_to_client_stage`, row moves to Ongoing
- "Discontinue" → set `matter_status = 0`, row moves to Discontinue
- Hold rows sort to bottom

---

## 3. Database

### 3.1 Existing / Reuse

- `client_matters` — core table
- `matters` — TR identified by `nick_name = 'tr'` or `title` LIKE '%tr%'
- `workflow_stages` — stage names for tab filtering
- `admins` — client and assignee
- `branches` — office
- `account_client_receipts` / `account_all_invoice_receipts` — payment totals

### 3.2 New: `client_tr_references` (from existing TR plan)

One row per client per TR matter. Columns:

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `client_id` | bigint | FK admins |
| `client_matter_id` | bigint | FK client_matters |
| `current_status` | text | Status notes (like client_ongoing_references) |
| `payment_display_note` | string(100) | Override for payment display |
| `institute_override` | string(255) | Optional |
| `visa_category_override` | string(50) | Optional |
| `comments` | text | Sheet comment / free text |
| `created_by`, `updated_by` | bigint | Audit |
| `timestamps` | | |

**Unique:** `(client_id, client_matter_id)`

### 3.3 New: `tr_checklist_status` on `client_matters` (optional)

**Option A — Add column to client_matters (recommended):**

```php
// Migration: add_tr_checklist_status_to_client_matters
$table->string('tr_checklist_status', 32)->nullable(); // active, hold, convert_to_client, discontinue
```

- Only relevant for matters in checklist stages.
- When status = `convert_to_client` → update `workflow_stage_id` to match Ongoing; row leaves Checklist tab.
- When status = `discontinue` → set `matter_status = 0`; row moves to Discontinue tab.

**Option B — Store in client_tr_references:**

Add `checklist_status` to `client_tr_references`. Same logic, but status lives on the reference row instead of the matter.

**Recommendation:** Option A — mirrors bansalcrm2's `applications.checklist_sheet_status` and keeps matter state on the matter.

### 3.4 Checklist tab: reminder/event tables (from existing TR plan)

- `client_tr_references` — add: `expiry_date`, `rec_date`, `last_date`, `checklist_send_date`, `first_reminder_date`, `second_reminder_date`, `docs_requested`, etc., if full checklist workflow is needed.
- `tr_reminder_events` — for reminder audit trail (optional; can be Phase 2).

---

## 4. Routes

**File:** `routes/clients.php`

```php
// TR Sheet (one page, 4 tabs)
Route::get('/clients/sheets/tr', [TrSheetController::class, 'index'])
    ->name('clients.sheets.tr')
    ->defaults('tab', 'ongoing');

// TR Sheet actions (tab-specific)
Route::post('/clients/sheets/tr/reference/{clientId}', [TrSheetController::class, 'updateReference'])
    ->name('clients.sheets.tr.reference-update');
Route::post('/clients/sheets/tr/sheet-comment', [TrSheetController::class, 'storeSheetComment'])
    ->name('clients.sheets.tr.sheet-comment');
Route::post('/clients/sheets/tr/checklist/status', [TrSheetController::class, 'updateChecklistStatus'])
    ->name('clients.sheets.tr.checklist.update-status');
Route::post('/clients/sheets/tr/checklist/phone-reminder', [TrSheetController::class, 'storePhoneReminder'])
    ->name('clients.sheets.tr.checklist.phone-reminder');

// Optional: Insights (separate page or tab)
Route::get('/clients/sheets/tr/insights', [TrSheetController::class, 'insights'])
    ->name('clients.sheets.tr.insights');
```

---

## 5. Controller: `TrSheetController`

**File:** `app/Http/Controllers/CRM/TrSheetController.php`

**Pattern:** Single controller (like bansalcrm2 `OngoingSheetController`) with tab parameter.

### 5.1 Constants & config

```php
public const TABS = ['ongoing', 'lodged', 'checklist', 'discontinue'];

public static function getTabConfig(string $tab): array
{
    $configs = [
        'ongoing'    => ['title' => 'Ongoing', 'route' => 'clients.sheets.tr', 'session_key' => 'tr_sheet_ongoing_filters'],
        'lodged'     => ['title' => 'Lodged', 'route' => 'clients.sheets.tr', 'session_key' => 'tr_sheet_lodged_filters'],
        'checklist'  => ['title' => 'Checklist', 'route' => 'clients.sheets.tr', 'session_key' => 'tr_sheet_checklist_filters'],
        'discontinue'=> ['title' => 'Discontinue', 'route' => 'clients.sheets.tr', 'session_key' => 'tr_sheet_discontinue_filters'],
    ];
    return $configs[$tab] ?? $configs['ongoing'];
}
```

### 5.2 `index(Request $request, $tab = null)`

1. Resolve `$tab` from route/query (`?tab=ongoing` default).
2. Validate `$tab` in `TABS`; fallback to `ongoing`.
3. Load filters from session (per-tab session key).
4. Build base query with `buildBaseQuery($request, $tab)`.
5. Apply filters and sorting.
6. Paginate.
7. Return view `crm.clients.sheets.tr` with: `rows`, `activeTab`, `tabConfig`, `perPage`, `activeFilterCount`, `branches`, `assignees`, `currentStages`, etc.

### 5.3 `buildBaseQuery(Request $request, string $tab)`

- Base: `client_matters` joined with `matters` where TR (nick_name/title).
- Join: `admins` (client), `workflow_stages`, `branches`, assignee, `client_tr_references` (left join).
- Select: matter fields, client fields, payment totals (from receipts), sheet comment (from activities or client_tr_references.comments).
- **Tab-specific WHERE:**
  - **Ongoing:** `matter_status=1`, stage IN ongoing_stages, stage NOT IN (lodged, checklist, discontinue).
  - **Lodged:** `matter_status=1`, stage IN lodged_stages.
  - **Checklist:** `matter_status=1`, stage IN checklist_early_stages, tr_checklist_status IN (NULL, 'active', 'hold').
  - **Discontinue:** `matter_status=0` OR stage IN discontinue_stages.

### 5.4 Filters (shared across tabs)

- Branch (multi)
- Assignee (default: current user)
- Current stage (dropdown from config for active tab)
- Visa expiry from/to
- Search (name, CRM ref, status, stage)
- Per-page: 10, 25, 50, 100, 200

### 5.5 Actions

- `updateReference` — update `client_tr_references` (current_status, payment_display_note, etc.)
- `storeSheetComment` — store comment (in `client_tr_references.comments` or `application_activities_logs`-style table if exists)
- `updateChecklistStatus` — set `tr_checklist_status` on `client_matters`; if convert_to_client → update stage; if discontinue → set matter_status=0
- `storePhoneReminder` — record phone reminder (if `application_reminders`-style table exists for TR)
- `insights` — conversions, discontinues, charts (optional; can mirror bansalcrm2 insights)

---

## 6. Views

### 6.1 Main view: `resources/views/crm/clients/sheets/tr.blade.php`

**Layout:**
- Extend CRM layout (e.g. `layouts.crm` or `layouts.crm_client_detail`).
- Page header: "TR Sheet".
- Filter bar: collapse/expand; Branch, Assignee, Stage, Visa From/To, Search, Clear, Per-page.
- **Tab bar:** Bootstrap nav-tabs or custom:
  ```html
  <ul class="nav nav-tabs tr-sheet-tabs">
    <li><a href="{{ route('clients.sheets.tr', ['tab' => 'ongoing'] + request()->query()) }}">Ongoing</a></li>
    <li><a href="{{ route('clients.sheets.tr', ['tab' => 'lodged'] + request()->query()) }}">Lodged</a></li>
    <li><a href="{{ route('clients.sheets.tr', ['tab' => 'checklist'] + request()->query()) }}">Checklist</a></li>
    <li><a href="{{ route('clients.sheets.tr', ['tab' => 'discontinue'] + request()->query()) }}">Discontinue</a></li>
  </ul>
  ```
- Table: columns depend on active tab (see below).
- Pagination.
- Modals: Sheet comment, Change assignee, Checklist status (for Checklist tab).

### 6.2 Table columns (by tab)

**Shared columns (Ongoing, Lodged, Discontinue):**
- Matter / Course name (link to client detail)
- CRM Reference
- Client Name
- DOB
- Payment Received
- Institute
- Branch
- Assignee (with change-assignee link)
- Visa Expiry
- Visa Category
- Current Stage
- Comment (with edit link)

**Checklist tab only — extra columns:**
- Status (dropdown: Active, Convert to client, Discontinue, Hold)
- Checklist sent (date + Send/Resend link)
- Email reminder (latest + count + link)
- SMS reminder (latest + count + link)
- Phone reminder (button to record)

### 6.3 Styling

- Reuse bansalcrm2-style CSS: sticky header, filter card, horizontal scroll for table, scroll hint.
- Match migrationmanager2 theme (purple/gradient if used).
- Tab styling: active tab highlighted.

---

## 7. Navigation

**File:** e.g. `resources/views/Elements/CRM/header_client_detail.blade.php` or sidebar

Add to Sheets dropdown:

- Label: "TR Sheet"
- Route: `clients.sheets.tr`
- Icon: `fa-clipboard-list` or `fa-tasks`

---

## 8. Module Permission

- Use same module as ART (e.g. `hasModuleAccess('20')`) or dedicated TR module.
- Check permission in `TrSheetController@index` and actions.

---

## 9. Implementation Order

1. **Config** — Create `config/sheets/tr.php` with stage mappings.
2. **Migration** — `client_tr_references` table; optional `tr_checklist_status` on `client_matters`.
3. **Models** — `ClientTrReference`; ensure `ClientMatter` has `tr_checklist_status` if used.
4. **Controller** — `TrSheetController`: index (with tab), buildBaseQuery (tab-specific), filters, sorting, updateReference, storeSheetComment, updateChecklistStatus, storePhoneReminder.
5. **Routes** — Add TR sheet and action routes.
6. **View** — `tr.blade.php` with 4 tabs, filter bar, table, modals.
7. **Navigation** — Add TR Sheet to Sheets menu.
8. **Matter type** — Ensure TR matter exists in `matters` (nick_name='tr' or title contains 'TR').
9. **Workflow stages** — Verify workflow_stages names match config (or adjust config to match DB).
10. **Testing** — Create TR matter, assign to client, confirm rows appear in correct tab; test filters, status changes, assignee change, comments.

---

## 10. Files to Create / Modify

| Action | File |
|--------|------|
| Create | `config/sheets/tr.php` |
| Create | `database/migrations/xxxx_create_client_tr_references_table.php` |
| Create | `database/migrations/xxxx_add_tr_checklist_status_to_client_matters.php` (optional) |
| Create | `app/Models/ClientTrReference.php` |
| Create | `app/Http/Controllers/CRM/TrSheetController.php` |
| Create | `resources/views/crm/clients/sheets/tr.blade.php` |
| Modify | `routes/clients.php` |
| Modify | `resources/views/Elements/CRM/header_client_detail.blade.php` (or equivalent) |
| Modify | `app/Models/ClientMatter.php` (add `tr_checklist_status` to fillable if column added) |

---

## 11. Relationship to Existing TR Plan

The existing `TR_SHEET_IMPLEMENTATION_PLAN.md` covers:
- `client_tr_references` with checklist fields (expiry, rec_date, reminders)
- `tr_reminder_events`
- Reminder workflow (Email/SMS preview + send)

**This 4-tabs plan:**
- Reuses `client_tr_references` for reference data (current_status, comments, etc.).
- Adds the **4-tab structure** (Ongoing, Lodged, Checklist, Discontinue) aligned with bansalcrm2.
- Checklist tab can later integrate the full reminder workflow from the existing TR plan (Phase 2).

---

## 12. Summary

| Item | Detail |
|------|--------|
| **Page** | One TR Sheet page |
| **Tabs** | Ongoing, Lodged, Checklist, Discontinue |
| **Data** | TR matters from `client_matters` + `matters` |
| **Filtering** | Config-driven stages per tab; matter_status for Discontinue |
| **Checklist** | Status dropdown; Convert/Discontinue moves row between tabs |
| **Reference table** | `client_tr_references` (status, comments, payment override) |
| **Pattern** | Mirrors bansalcrm2 `OngoingSheetController` + config |

---

**Next steps:** Review and approve; then implement in the order above.
