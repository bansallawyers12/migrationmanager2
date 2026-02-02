# Plan: Add `source` Column to `activities_logs` (Option 2 – Client Portal Filter)

## 1. Requirement & Confirmation

**Requirement:** Show in the Client Portal “Recent Activity (View All)” API only activities that the user did in the Client Portal (Activities, Documents, Messages, Details tabs).

**Will Option 2 fulfill this exactly?**  
Yes. Filtering by `source = 'client_portal'` (with transition fallback) limits the feed to rows explicitly logged from the Client Portal. CRM and other flows do not set `source = 'client_portal'`, so they will not appear in that API.

**Will it create issues with the existing system except the Client Portal tab?**  
No, if changes are limited as below:

- **New column:** `source` is **nullable**. Existing rows stay `NULL`; no data loss.
- **CRM / Admin reads:** Activity feed (CRM client detail → Activities tab), Activity Search, export, etc. do **not** filter by `source`. They keep showing all activities. No behavior change.
- **Client Portal only:** The **Recent Activity (View All)** API and the dashboard “recent activity” widget are the only places that filter by `source`. So impact is confined to the Client Portal “Recent Activity” experience.
- **Writes:** Only **Client Portal** insert sites need to set `source = 'client_portal'`. All other insert sites can leave `source` unset (NULL). Optional: set `source = 'crm'` (or similar) in CRM/admin inserts for clarity; not required for correctness.

**Review: No other functionality will break.** Every other use of `activities_logs` / `ActivitiesLog` has been checked: CRM activity feed, Activity Search, Client export, CRM Dashboard (DashboardService), CRM ClientPortalController application lists, ClientsController (list/find/delete/merge), ClientNotesController, CleanupActivityDescriptions, SmsLog relationship, and all CRM/admin insert sites. None of them filter by or depend on a `source` column. Adding a nullable `source` column does not change their behavior. Only the two Client Portal read paths (recentActivityViewAll and getRecentActivity) will add the filter.

---

## 2. Transition Filter (Client Portal API only)

Use this in the Client Portal Recent Activity logic so old “client did it” rows still show until backfilled (if you ever do):

- **Filter:**  
  `(source = 'client_portal' OR (source IS NULL AND created_by = client_id))`

So:
- New Client Portal actions (with `source = 'client_portal'`) always show.
- Old rows without `source` still show if they were done by the client (`created_by = client_id`).
- CRM-only rows (`created_by` = staff, `source` NULL) stay excluded.

---

## 3. Plan Sheet – All Places to Change

### 3.1 Database

| # | File / Location | Change |
|---|------------------|--------|
| 1 | **New migration** (e.g. `database/migrations/YYYY_MM_DD_HHMMSS_add_source_to_activities_logs_table.php`) | Add nullable string column `source` (e.g. `VARCHAR(50)`), e.g. after `activity_type`. Optional: add index on `(client_id, source)` for the Client Portal API query. No default; existing rows remain `NULL`. |

---

### 3.2 Model

| # | File | Change |
|---|------|--------|
| 2 | `app/Models/ActivitiesLog.php` | Add `'source'` to `$fillable`. Optional: add scope `scopeFromClientPortal($query)` returning `$query->where('source', 'client_portal')` for reuse. |

---

### 3.3 Client Portal – INSERT into `activities_logs` (set `source = 'client_portal'`)

Only these should set `source = 'client_portal'` (actions done by the client in the portal).

| # | File | Method / Context | Line (approx) | Change |
|---|------|------------------|---------------|--------|
| 3 | `app/Http/Controllers/API/ClientPortalMessageController.php` | Send message **to** client (staff sends to client) | ~239 | Do **not** set `source` (or set `source = 'crm'` if you want). This is not a Client Portal action by the client. |
| 4 | `app/Http/Controllers/API/ClientPortalMessageController.php` | **sendMessage** (client sends message) | ~543 | Add `'source' => 'client_portal'` to the `activities_logs` insert array. |
| 5 | `app/Http/Controllers/API/ClientPortalDocumentController.php` | Document upload via client portal | ~623 | Add `'source' => 'client_portal'` to the `activities_logs` insert array. |
| 6 | `app/Http/Controllers/API/ClientPortalWorkflowController.php` | Allowed checklist document upload (currently **commented out**) | ~613 | When you uncomment/enable this insert, add `'source' => 'client_portal'` to the insert array. |
| 7 | `app/Http/Controllers/API/ClientPortalAppointmentController.php` | Appointment status updated via mobile app (client updates status) | ~1169 | When creating the `ActivitiesLog` (e.g. before `$activityLog->save()`), set `$activityLog->source = 'client_portal'` so this Client Portal action is included in Recent Activity. |

**Details tab:**  
`ClientPortalPersonalDetailsController` does **not** currently insert into `activities_logs`. If you later add activity logging for Personal Details updates from the Client Portal, set `'source' => 'client_portal'` and `created_by => $clientId` there as well (not listed in the table above until you add that feature).

---

### 3.4 Client Portal – READ from `activities_logs` (filter by source)

Apply the transition filter only here. No other reads should filter by `source`.

| # | File | Method | Line (approx) | Change |
|---|------|--------|---------------|--------|
| 8 | `app/Http/Controllers/API/ClientPortalDashboardController.php` | **recentActivityViewAll** | ~598–600 (base query) and ~691 (type-count query) | After `->where('client_id', $clientId)`, add: `->where(function ($q) use ($clientId) { $q->where('source', 'client_portal')->orWhere(function ($q2) use ($clientId) { $q2->whereNull('source')->where('created_by', $clientId); }); })`. Apply the same condition to the second query used for `type_summary` (the one that gets all activities for type counting). |
| 9 | `app/Http/Controllers/API/ClientPortalDashboardController.php` | **getRecentActivity** (private, used for dashboard widget) | ~1125–1127 | Add the same filter to the query so the small “recent activity” widget on the dashboard also shows only Client Portal activities. |

---

### 3.5 CRM / Other – COPY `activities_logs` (preserve `source` on merge)

When merging clients, copied activity rows should keep their `source` value.

| # | File | Method / Context | Line (approx) | Change |
|---|------|------------------|---------------|--------|
| 10 | `app/Http/Controllers/CRM/ClientsController.php` | Client merge – copy `activities_logs` | ~2822–2834 | In the `DB::table('activities_logs')->insert([...])` array, add `'source' => $actval->source ?? null` so merged rows retain the source. (After migration, existing rows will have `source` NULL.) |

---

### 3.6 CRM / Other – INSERT into `activities_logs` (no required change)

These are **not** Client Portal. They should **not** set `source = 'client_portal'`. You can leave `source` NULL or optionally set `source = 'crm'` (or another value) for clarity. **No change is required** for Option 2 to work; listing for completeness.

| # | File | Note |
|---|------|------|
| - | `app/Http/Controllers/CRM/ClientsController.php` | Activity add (e.g. rating ~2571, application ~2620), `ActivitiesLog` model create ~6802 – leave `source` unset or set `'crm'`. |
| - | `app/Http/Controllers/CRM/ClientEoiRoiController.php` | `logActivity()` ~655 – CRM; leave unset or set `'crm'`. |
| - | `app/Http/Controllers/CRM/EoiRoiSheetController.php` | `logActivity()` ~817 – CRM; leave unset or set `'crm'`. |
| - | `app/Services/ClientImportService.php` | ~288 – leave unset or set `'crm'`. |
| - | `app/Services/DashboardService.php` | ~518 – leave unset or set `'crm'`. |
| - | `app/Services/SignatureService.php` | ~351, 426, 489 – leave unset or set `'crm'`. |
| - | `app/Services/BansalAppointmentSync/AppointmentSyncService.php` | ~629 – leave unset or set `'crm'`. |
| - | `app/Http/Controllers/PublicDocumentController.php` | ~1304 – leave unset or set as appropriate (e.g. `'crm'` or `'public'`). |
| - | `app/Models/Lead.php` | ~149 – leave unset or set `'crm'`. |
| - | `app/Services/Sms/UnifiedSmsManager.php` | ~235 – leave unset or set e.g. `'sms'`/`'crm'`. |
| - | `app/Console/Commands/FixDuplicateClientReferences.php` | ~210 – system; leave unset or set e.g. `'system'`. |
| - | `app/Traits/LogsClientActivity.php` | `ActivitiesLog::create()` – used by CRM; if you add `source` to fillable, callers can set it; otherwise leave NULL. |

---

### 3.7 No changes (confirm behavior unchanged)

| Area | Files / Locations | Reason |
|------|-------------------|--------|
| CRM client Activities tab | `resources/views/crm/clients/tabs/activity_feed.blade.php`, compiled view under `storage/framework/views/` | No `source` filter; continues to show all activities for the client. |
| Activity Search | `app/Http/Controllers/AdminConsole/ActivitySearchController.php` | No `source` filter; search still over all activities. |
| Client export | `app/Services/ClientExportService.php` | Uses `ActivitiesLog::where('client_id', ...)`; no filter by source. |
| CRM Dashboard (latest activity) | `app/Services/DashboardService.php` (used by `app/Http/Controllers/CRM/DashboardController.php`) | Reads by `client_id` for CRM dashboard; do **not** add source filter so CRM still sees all activities. |
| CRM Client Portal tab (application lists) | `app/Http/Controllers/CRM/ClientPortalController.php` (~956, ~1019) | Application/stage activity lists and notes; no source filter so staff see all. |
| ClientsController activity list / find / delete | `app/Http/Controllers/CRM/ClientsController.php` (2499, 3131, 3150, 5306, etc.) | All by `client_id` or `id`; no source filter. |
| ClientNotesController | `app/Http/Controllers/CRM/Clients\ClientNotesController.php` | Select by activity_type, use_for, id; no source filter. |
| CleanupActivityDescriptions command | `app/Console/Commands/CleanupActivityDescriptions.php` | Reads by description; no source filter. |
| Other reads | Any other `ActivitiesLog` or `activities_logs` reads | Do not add a `source` filter unless they are explicitly “Client Portal only” features. |

---

## 4. Summary Checklist

- [ ] **Migration:** Add nullable `source` column (and optional index) to `activities_logs`.
- [ ] **Model:** Add `source` to `ActivitiesLog::$fillable` (and optional scope).
- [ ] **Client Portal inserts:** Set `'source' => 'client_portal'` in: MessageController sendMessage (~543), DocumentController upload (~623), WorkflowController insert when enabled (~613), **ClientPortalAppointmentController** appointment status update (~1169). Do **not** set it for “Message sent to client” (~239).
- [ ] **Client Portal reads:** In `ClientPortalDashboardController`, add transition filter in **recentActivityViewAll** and **getRecentActivity**.
- [ ] **Merge:** In `ClientsController` merge, copy `source` when copying `activities_logs` rows.
- [ ] **CRM/other inserts:** No change required; optional to set `source = 'crm'` (or similar) later.

---

## 5. Optional Later Steps

- **Backfill (optional):** Run an update such as:  
  `UPDATE activities_logs SET source = 'client_portal' WHERE source IS NULL AND created_by = client_id`  
  so old client-originated rows get an explicit source. After that, you can simplify the API filter to only `source = 'client_portal'` if you wish.
- **Details tab:** When adding activity logging for Client Portal Personal Details updates, set `source = 'client_portal'` and `created_by = $clientId` there.
- **Uncomment WorkflowController:** When enabling the checklist upload activity log, include `'source' => 'client_portal'` in the insert.

This plan is for implementation reference only; no code has been changed in the repo per your request.
