# TR Sheet Implementation Plan

**Purpose:** Add a new "TR Checklist" sheet to the CRM Sheets section: dynamic checklist + follow-up dates in one box, reminder actions (Email/SMS) with review popup, event recording, and one flat list for all TR records (no monthly grouping).  
**Scope:** Plan only — no code changes applied until you approve.

---

## 1. Business Requirements

### 1.1 From NEW TR EXCEL (first screenshot)

| Column        | Description                                      | Notes |
|---------------|--------------------------------------------------|-------|
| **Name**      | Client full name                                 | From `admins` (client record). |
| **CRM Ref**   | Client reference (e.g. PAVN2501532, MANP2501758-TGV_1) | From `admins.client_id` + matter ref. |
| **Matter Type** | Always "TR Checklist" with variant in parentheses | e.g. "(Single)", "(Couple)", "(Family)" — stored or derived. |
| **Expiry Date** | Key date (highlighted in yellow in Excel)        | Stored in TR reference table. |
| **Checklist Send** | Date checklist was sent                         | Stored in TR reference table. |
| **1st Reminder** | First follow-up reminder date                    | Stored in TR reference table. |
| **2nd Reminder** | Second follow-up reminder date                   | Can be empty or "NP" (Not Provided). |
| **Call**      | Call / action date                               | Can include "NP" (e.g. "23.01.2026-NP"). |
| **Comments**  | Free-text notes (PTE, follow-up, etc.)           | Text field. |

### 1.2 From second screenshot (consolidated tracking)

- **One sheet for all:** Single flat list — all TR (and optionally other matter) records in one table. **No monthly grouping** (no "Apr-25", "May-25" section headers).
- Second sheet columns (Name, CRM Ref, **SOS**, **TRN**, **Skill Assessment Uploaded**, **AFP Uploaded**, **Medical Test Required**, **Lodgement**, **Checklist**) can be merged into the same view if you want one consolidated sheet; the **Checklist** column becomes the dynamic box (see below). If merged, add data source for SOS, TRN, skill assessment, AFP, medical, lodgement (existing tables or new columns); otherwise the TR sheet stays TR-checklist–only with the dynamic box.

### 1.3 From third screenshot (Docs Requested / Rec / Last / Follow Up)

- **Columns to include in the same sheet (one sheet for all):**

| Column           | Description                                                                 | Mapping / storage |
|------------------|-----------------------------------------------------------------------------|-------------------|
| **CRM Ref**      | Client reference (e.g. AYUS2502841-PT_1)                                   | From `admins.client_id` + matter ref (existing). |
| **Name**         | Client full name                                                            | From `admins` (existing). |
| **Docs Requested** | What documents or actions were requested (e.g. "Current Passport", "Skills assessment Outcome and AFP", "AFP correct Name for both applicants", "Natural Justice") | New column: `docs_requested` (text). |
| **Rec Date**     | Received / Request date — when the request was received or sent (DD.MM.YYYY) | New column: `rec_date` (date). Can align with checklist send or be separate. |
| **Last Date**    | Last contact / last update date (DD.MM.YYYY)                                 | New column: `last_date` (date). |
| **Follow Up Date** | Follow-up action date(s) — in the screenshot a single date; plan keeps **Follow up 1** and **Follow up 2** for reminders | Existing: `first_reminder_date`, `second_reminder_date`. Display in one box with Rec Date and Last Date. |
| **Comments**     | Free-form notes (next steps, delays, communication status)                   | Existing: `comments` (text). |

- **Alignment with dynamic box:** The **one box** per row will show: **Rec Date** (or Checklist sent), **Last Date**, **Follow up 1**, **Follow up 2** (and optionally Call), plus **recorded reminder events** and **buttons** for Email/SMS reminders. So one unified block for "dates and follow-up" that covers both the third screenshot’s Rec/Last/Follow Up and the first screenshot’s Checklist send + reminders.
- **Docs Requested** is shown as its own column (or combined with Matter Type if preferred); filter/search by docs requested can be added later.

### 1.4 Dynamic behaviour (required)

- **One box per row for checklist + follow-ups:** One combined cell/block showing:
  - **Rec Date** (request/received date) and/or **Checklist sent** date
  - **Last Date** (last contact/update)
  - **Follow up 1** date
  - **Follow up 2** date  
  (Optionally **Call** in the same block.)  
  Display compactly, e.g. `Rec: 13.01.2026 | Last: 09.02.2026 | F1: 27.11.2025 | F2: 20.12.2025` or a small card. Dates editable (inline or via modal).

- **Follow-up status:** Derived or stored status (e.g. `not_sent` → `checklist_sent` → `reminder_1_sent` → `reminder_2_sent`) to drive which reminder buttons are shown.

- **Reminder buttons (Email & SMS):**
  - **Trigger:** Once **rec_date** (or checklist_send_date if rec_date is empty) is entered, show options to **Send Reminder 1**.
  - After **Reminder 1** has been sent (at least one event with reminder_number=1 exists), show **Send Reminder 2** buttons.
  - Two actions per reminder: **Email** and **SMS** (4 buttons total: "Reminder 1 – Email", "Reminder 1 – SMS", "Reminder 2 – Email", "Reminder 2 – SMS").
  - **Button state:** Buttons are enabled if the trigger condition is met; optionally disable if already sent recently (e.g. within last 24 hours) or show "Resend" instead of "Send".
  - On click: **open a popup** with the email body (or SMS text) — template merged with client name, CRM ref, etc. — for staff to **review and edit**, then **Send**. After send, record the event, auto-update `last_date` to today, and refresh the checklist box.

- **Record events in the sheet:** Each reminder send (email or SMS) is logged so the sheet can show e.g. "Reminder 1 email sent 27.11.2025", "Reminder 2 SMS sent 20.12.2025". Events visible in the same row (e.g. in the checklist box or an "Events" column).

- **Date format:** **d/m/Y** (to match existing sheets).
- **"NP"** for reminders/call: nullable dates plus optional flags (`second_reminder_np`, `call_np`) where applicable.

### 1.5 Business Logic & Data Flow Clarifications

**When is a TR reference created?**
- A `client_tr_references` row is created when staff manually add TR checklist data for a client with a TR matter, OR when TR matters are imported/migrated. The sheet shows only clients who have a TR matter (from `client_matters` + `matters` where matter type is TR); the left join to `client_tr_references` means rows without checklist data will show empty dates/comments but still appear in the list if they have a TR matter.

**Date semantics (Rec Date vs Checklist Send):**
- **`rec_date`**: Primary date = when the request was received from client or request sent to client (third screenshot usage). This is the trigger for reminder buttons.
- **`checklist_send_date`**: Date the checklist was sent to client (first screenshot usage). Can be the same as rec_date or different. If only one date is used in practice, store in `rec_date` and leave `checklist_send_date` null or vice versa.
- **`last_date`**: Last contact or update; auto-updated when reminders are sent (set to current date when `sendReminder` is called).
- **`first_reminder_date` / `second_reminder_date`**: Planned or actual follow-up dates. Auto-set to current date when corresponding reminder is sent (if not already set by staff).

**Button visibility logic:**
- **Reminder 1 buttons:** Show if `rec_date` (or `checklist_send_date` as fallback) is set. Optionally check if Reminder 1 was already sent today and show "Resend" or disable.
- **Reminder 2 buttons:** Show only if at least one Reminder 1 event exists (check `tr_reminder_events` where reminder_number=1).

**Auto-updates on reminder send:**
- When `sendReminder` is called and succeeds:
  1. Create `TrReminderEvent` row.
  2. Set `last_date` = today (if not already today).
  3. If `first_reminder_date` (or `second_reminder_date`) is null, set it to today.
  4. Return event data to frontend to append to checklist box without full page reload.

**Duplicate send handling:**
- Plan allows multiple sends (e.g. staff can click "Reminder 1 – Email" again if needed). Each send creates a new event row. Frontend can optionally warn "Reminder 1 email was already sent on X date. Send again?" before calling `sendReminder`.

**Template resolution:**
- Email templates: stored in Admin Console CRM Email Templates or Matter Email Templates. Use slugs like `tr_checklist_reminder_1` and `tr_checklist_reminder_2` (or similar); controller searches by slug and merges placeholders: `{client_name}`, `{crm_ref}`, `{expiry_date}`, `{docs_requested}`, etc.
- SMS templates: same pattern, with SMS-specific templates (shorter body). If no template found, return error in `reminderPreview`.

**Client email/phone resolution:**
- For email: query `client_emails` where `client_id = <client_id>` and `is_primary = 1` or `email_type = 'primary'` or first non-deleted email.
- For SMS: query `client_contacts` where `client_id = <client_id>` and `contact_type = 'mobile'` or `is_primary = 1`, or first mobile number.
- If not found, `sendReminder` returns error: "No email/phone found for client."

**Authorization:**
- Any admin with module access (e.g. `hasModuleAccess('20')`) can view the sheet and send reminders. Optionally restrict send to assigned agent or migration agent for that matter; plan recommends allowing all authorized admins to send (record `sent_by` in events for audit).

**Index uniqueness (`client_id`, `client_matter_id`):**
- **Recommendation: Unique index.** One `client_tr_references` row per client per TR matter. If a client has multiple TR matters (unlikely but possible), each matter gets its own row. If only one TR matter per client is expected, unique index prevents duplicates.

---

## 2. Database: Tables and Models

### 2.1 Migration: `client_tr_references`

Create a new table (same pattern as `client_art_references`):

- **Primary key:** `id`
- **Links:** `client_id` → `admins.id`, `client_matter_id` → `client_matters.id`
- **TR-specific columns:**

| Column               | Type           | Nullable | Notes |
|----------------------|----------------|----------|--------|
| `client_id`          | bigint unsigned | No       | FK to admins |
| `client_matter_id`   | bigint unsigned | No       | FK to client_matters |
| `matter_type`        | string(50)     | Yes      | e.g. "single", "couple", "family" (for "TR Checklist (Single)" etc.) |
| `expiry_date`        | date           | Yes      | Yellow-highlight column in Excel |
| `docs_requested`     | text           | Yes      | What was requested (e.g. "Current Passport", "Skills assessment Outcome and AFP", "Natural Justice") — from third screenshot |
| `rec_date`           | date           | Yes      | Rec Date — request/received date (DD.MM.YYYY) |
| `last_date`          | date           | Yes      | Last Date — last contact/update date (DD.MM.YYYY) |
| `checklist_send_date`| date           | Yes      | Checklist Send (can align with rec_date or be separate) |
| `first_reminder_date` | date           | Yes      | Follow up 1 / 1st Reminder |
| `second_reminder_date`| date           | Yes      | Follow up 2 / 2nd Reminder (NP = null or separate flag) |
| `second_reminder_np`  | boolean        | No, default false | True when "NP" for 2nd reminder |
| `call_date`          | date           | Yes      | Call date |
| `call_np`             | boolean        | No, default false | True when "NP" for Call |
| `comments`            | text           | Yes      | Free-text comments |

- **Audit (recommended):** `created_by`, `updated_by`, `timestamps`
- **Indexes:**
  - **Unique:** `(client_id, client_matter_id)` — ensures one TR reference per client per TR matter.
  - **Non-unique:** `expiry_date`, `rec_date`, `last_date`, `checklist_send_date` — for filtering/sorting.
- **Foreign keys:** `client_id` → `admins(id)` ON DELETE CASCADE, `client_matter_id` → `client_matters(id)` ON DELETE CASCADE, `created_by` / `updated_by` → `admins(id)` ON DELETE SET NULL.

**No monthly grouping:** Table stores one row per client/matter; list view is a single flat table (no month headers).

**Validation (controller level):**
- Dates: valid date format; optionally enforce rec_date <= last_date <= today.
- `matter_type`: if provided, must be one of 'single', 'couple', 'family' (or open text).
- `docs_requested`, `comments`: max length (e.g. 65535 chars for text fields).

### 2.2 Migration: `tr_reminder_events` (record reminder sends)

New table to **record each reminder send** so the sheet can show "Reminder 1 email sent on …", "Reminder 2 SMS sent on …".

| Column            | Type             | Nullable | Notes |
|-------------------|------------------|----------|--------|
| `id`              | bigint unsigned  | No       | PK |
| `client_tr_reference_id` | bigint unsigned | No | FK to client_tr_references |
| `reminder_number` | tinyint unsigned | No       | 1 or 2 |
| `channel`         | string(10)       | No       | `email` or `sms` |
| `sent_at`         | timestamp        | No       | When sent |
| `sent_by`         | bigint unsigned  | Yes      | FK to admins |
| `recipient`       | string(255)      | Yes      | Email address or phone number used |
| `template_id`     | bigint unsigned  | Yes      | Optional: email_template_id or sms_template_id |
| `subject`         | string(255)      | Yes      | Email subject (null for SMS) |
| `body_preview`    | text             | Yes      | Optional: first N chars of body for display |
| `created_at`      | timestamp        | No       | |

- **Indexes:** `(client_tr_reference_id)`, `(sent_at)`
- **Relations:** One `ClientTrReference` has many `TrReminderEvent`; event belongs to `ClientTrReference` and `sent_by` → Admin.

**Alternative:** Instead of a separate events table, add columns on `client_tr_references`: e.g. `reminder_1_email_sent_at`, `reminder_1_sms_sent_at`, `reminder_2_email_sent_at`, `reminder_2_sms_sent_at`. Simpler but less flexible for multiple sends per reminder; plan recommends **events table** for full audit trail.

### 2.3 Model: `ClientTrReference`

- **File:** `app/Models/ClientTrReference.php`
- **Table:** `client_tr_references`
- **Fillable:** all data columns (excluding id, created_at, updated_at if not mass-assigned), including `docs_requested`, `rec_date`, `last_date`
- **Casts:** `expiry_date`, `rec_date`, `last_date`, `checklist_send_date`, `first_reminder_date`, `second_reminder_date`, `call_date` → `date`; `second_reminder_np`, `call_np` → `boolean`
- **Relations:** `client()` → Admin, `clientMatter()` → ClientMatter, `reminderEvents()` → hasMany TrReminderEvent, `creator()` / `updater()` → Admin (if audit columns exist)
- **Computed / accessors:** e.g. `followup_status` (not_sent, checklist_sent, reminder_1_sent, reminder_2_sent) derived from checklist_send_date and latest reminder events, for button visibility.

### 2.4 Model: `TrReminderEvent`

- **File:** `app/Models/TrReminderEvent.php`
- **Table:** `tr_reminder_events`
- **Relations:** `clientTrReference()` → ClientTrReference, `sentBy()` → Admin
- **Casts:** `sent_at` → datetime

---

## 3. Matter Type: TR in `matters` / `client_matters`

- **Identification:** Same pattern as ART. In `ArtSheetController`, ART matters are identified by `matters.nick_name = 'art'` or `matters.title` containing "art" / "administrative appeals" / "tribunal".
- **For TR:** In the new TR sheet controller, identify TR matters by:
  - `matters.nick_name` = `'tr'` (or `'tr checklist'`), **or**
  - `matters.title` containing "TR" / "TR Checklist" / "Tribunal Review" (or equivalent).
- **Data setup:** Ensure there is at least one matter type in `matters` with nick_name/title representing TR so that `client_matters` rows can be linked. If no such matter exists yet, add via seed or manual DB entry and document in release notes.

---

## 4. Routes

**File:** `routes/clients.php` (inside the existing "Sheets" comment block)

**GET (list & insights):**

```php
Route::get('/clients/sheets/tr', [TrSheetController::class, 'index'])->name('clients.sheets.tr');
Route::get('/clients/sheets/tr/insights', [TrSheetController::class, 'insights'])->name('clients.sheets.tr.insights');
```

**Reminder workflow (AJAX endpoints):**

```php
Route::post('/clients/sheets/tr/reminder-preview', [TrSheetController::class, 'reminderPreview'])->name('clients.sheets.tr.reminder-preview');
Route::post('/clients/sheets/tr/send-reminder', [TrSheetController::class, 'sendReminder'])->name('clients.sheets.tr.send-reminder');
```

- **`reminder-preview`**: Params: `client_tr_reference_id`, `reminder_number` (1|2), `channel` (email|sms). Returns JSON: `{ success: true, subject: '...', body: '...' }` (email) or `{ success: true, body: '...' }` (SMS). Merges template with client data (name, CRM ref, expiry date, docs requested, etc.).
- **`send-reminder`**: Params: `client_tr_reference_id`, `reminder_number`, `channel`, `subject` (email only), `body`. Validates, sends email/SMS, creates `TrReminderEvent`, auto-updates `last_date` to current date, sets `first_reminder_date` or `second_reminder_date` if not already set. Returns JSON: `{ success: true, message: '...', event: { ... } }` or `{ success: false, error: '...' }`.

**Editing from sheet (optional):**

```php
Route::patch('/clients/sheets/tr/{trReferenceId}', [TrSheetController::class, 'updateReference'])->name('clients.sheets.tr.update');
```

- Updates `client_tr_references` row (docs_requested, rec_date, last_date, checklist_send_date, first_reminder_date, second_reminder_date, call_date, comments, etc.). Returns JSON or redirects.

---

## 5. Controller: `TrSheetController`

**File:** `app/Http/Controllers/CRM/TrSheetController.php`

**Pattern:** Mirror `ArtSheetController` for list/insights; add reminder preview/send and optional update.

- **Middleware:** `auth:admin`
- **Trait:** `ClientAuthorization`; use same module permission check as ART (e.g. `hasModuleAccess('20')`) or a dedicated TR module ID if different.
- **Methods:**
  - **`index(Request $request)`**  
    - Pagination (e.g. 10, 25, 50, 100, 200).  
    - Build base query (latest TR matter per client + left join `client_tr_references` + load `reminderEvents` for each row).  
    - Apply filters (see below).  
    - Apply sorting.  
    - Pass to view: `rows`, `perPage`, `activeFilterCount`, dropdown data (matter_type options).  
    - **No monthly grouping** — single flat list.
  - **`insights(Request $request)`**  
    - Same base query + filters, no pagination.  
    - Compute aggregates (total records, by matter_type, reminders due soon, etc.).  
    - Return view: `insights`, `activeFilterCount`.
  - **`reminderPreview(Request $request)`** (AJAX)  
    - Params: `client_tr_reference_id`, `reminder_number` (1|2), `channel` (email|sms).  
    - Load `ClientTrReference` with client; resolve template (e.g. TR Checklist Reminder 1/2 from CRM or matter email/SMS templates); merge placeholders (client name, CRM ref, expiry date, etc.); return JSON `{ subject, body }` (email) or `{ body }` (SMS).
  - **`sendReminder(Request $request)`** (AJAX)  
    - **Params:** `client_tr_reference_id`, `reminder_number`, `channel`, `subject` (email only), `body`.  
    - **Validation:** Validate params; load `ClientTrReference`; check authorization (module access).  
    - **Resolve recipient:** Get client email (from `client_emails` where primary or first) or phone (from `client_contacts` where mobile and primary or first). Return error if not found.  
    - **Send:** Use DB transaction; send email via `CRMUtilityController@sendmail` (or equivalent Mail facade) / SMS via Admin Console SMS API. If send fails, rollback and return error.  
    - **Record event:** Create `TrReminderEvent` row.  
    - **Auto-update dates:** Set `last_date` = today; if `first_reminder_date` (or `second_reminder_date`) is null, set to today.  
    - **Return:** JSON `{ success: true, message: 'Reminder sent', event: { id, channel, sent_at, ... } }` or `{ success: false, error: 'Email not found' }` (or 'Failed to send SMS', 'No template found', etc.).
  - **`updateReference(Request $request, $id)`** (optional)  
    - PATCH/POST to update docs_requested, rec_date, last_date, checklist_send_date, first_reminder_date, second_reminder_date, call_date, comments, etc. for inline/modal edit from the sheet.

- **Base query:** Subquery = latest TR matter per client (from `client_matters` + `matters` where matter is TR by nick_name/title); main query = that + left join `client_tr_references`, join `admins` for name/CRM ref, optionally agents. Eager-load `clientTrReference.reminderEvents` for event display in the checklist box.
- **Filters:** Matter type, expiry from/to, rec_date from/to, last_date from/to, checklist send from/to, search (name, CRM ref, docs_requested, comments). Optional: follow-up status filter.
- **Sorting:** expiry_date, checklist_send_date, client name, CRM ref (same pattern as ART).
- **Insights:** Total TR records, by matter_type, expiring in 7/30 days; no monthly grouping.

---

## 6. Views

### 6.1 List view: `resources/views/crm/clients/sheets/tr.blade.php`

- Extend `layouts.crm_client_detail`.
- **Title:** e.g. "TR Checklist Sheet".
- **Sheet tabs:** Two tabs — "List" (active), "Insights" (link to `clients.sheets.tr.insights`). Reuse ART CSS (`sheet-tabs`, `sheet-tab`, `sheet-tab.active`).
- **Header:** "TR Checklist Sheet", "Back to Clients" button.
- **Filters:** Matter type (Single/Couple/Family), expiry date range (from/to), rec_date range (from/to), last_date range (from/to), checklist send date range (from/to), search (name, CRM ref, docs_requested, comments), and optional follow-up status (e.g. "Not sent", "Reminder 1 sent", "Reminder 2 sent"). "Filters" button, clear/reset, active filter count badge.
- **One sheet for all:** Single flat table — **no monthly grouping** (no month header rows).
- **Table columns (recommended order):**
  1. **CRM Ref** (link to client detail).
  2. **Name** (link to client detail).
  3. **Matter Type** (e.g. "TR Checklist (Single)").
  4. **Docs Requested** (text: e.g. "Current Passport", "Skills assessment Outcome and AFP", "Natural Justice").
  5. **Expiry Date** (subtle yellow/cream background highlight).
  6. **Checklist & Follow-ups (one box)** — see below.
  7. **Comments** (free-text, truncated with "..." if long; hover/tooltip for full text).

**Recommended: No standalone Rec Date or Last Date columns** — these dates are shown inside the **Checklist & follow-ups box** along with F1, F2, Call. This keeps the table compact and groups all date-related info in one place. If you prefer standalone columns for Rec Date and Last Date for easier sorting/filtering visibility, add them before the Checklist box (columns 6 and 7, shift Checklist box to column 8).

- **Checklist & follow-ups (one box):** One combined cell/block per row containing:
    - **Dates:** Rec Date, Last Date, Checklist sent (if different), Follow up 1, Follow up 2 (and optionally Call), e.g. `Rec: 13.01.2026 | Last: 09.02.2026 | F1: 27.11.2025 | F2: 20.12.2025` or a small card layout. Show "NP" for F2/Call when flag set.
    - **Events:** Short list of recorded reminder events in the same box, e.g. "Reminder 1 email 27.11.2025", "Reminder 2 SMS 20.12.2025".
    - **Buttons:**  
      - After **checklist sent** date exists: "Reminder 1 – Email", "Reminder 1 – SMS".  
      - After Reminder 1 has been sent (per logic): "Reminder 2 – Email", "Reminder 2 – SMS".  
      - Clicking a button opens the **reminder popup** (see below).
    - **Edit dates:** Inline edit or small "Edit" link that opens a modal to set/change rec_date, last_date, checklist_send_date, first_reminder_date, second_reminder_date, call_date, docs_requested, comments (calls optional PATCH endpoint).
  - **Comments** (free-text).
- **Rows:** Name and CRM Ref link to client detail (same route pattern as ART).
- **Pagination:** Same as ART (below table, preserve query string).
- **Empty state:** "No TR records found. Add a TR matter type and assign matters to clients, then add TR checklist data."

**Reminder popup (modal):**

- Triggered by "Reminder 1 – Email", "Reminder 1 – SMS", "Reminder 2 – Email", "Reminder 2 – SMS".
- **Content:** Subject (email only) and body (email or SMS) — loaded via AJAX from `reminder-preview` endpoint, merged with client data. Staff can **review and edit** subject/body in the popup.
- **Actions:** "Send" (POST to `send-reminder` with edited subject/body; on success close popup, refresh row or event list in the checklist box) and "Cancel".
- **Implementation:** Bootstrap modal or similar; JS fetches preview on open, submits send on "Send", then updates the checklist box (e.g. append new event line or re-fetch row data).

### 6.2 Insights view: `resources/views/crm/clients/sheets/tr-insights.blade.php`

- Same layout and sheet tabs; "Insights" tab active.
- **Title:** "TR Checklist Sheet - Insights".
- **Content:** Insight cards (total TR records, expiring in 7 days, expiring in 30 days); breakdown by matter type; **no monthly grouping** in charts (optional: by month for expiry/checklist send if useful). Filter-info alert and "View all data" link when filters are active.

---

## 7. Navigation: Sheets Dropdown

**File:** `resources/views/Elements/CRM/header_client_detail.blade.php`

- In the Sheets icon dropdown, add a third item:
  - Label: e.g. "TR Checklist Sheet"
  - Route: `clients.sheets.tr`
  - Icon: e.g. `fa-clipboard-list` or `fa-tasks` (distinct from EOI/ROI and ART).

---

## 8. No Monthly Grouping

**Confirmed:** The sheet is a **single flat list** for all TR records. There are **no month header rows** (no "Apr-25", "May-25", etc.). Optional filter by "Expiry month" or "Checklist send month" can be added later if needed; display remains one table.

---

## 9. Email & SMS Integration

- **Email:** Use existing CRM flow — e.g. `CRMUtilityController@sendmail`, `clients.gettemplates` or matter/CRM email templates. TR reminder templates (e.g. "TR Checklist Reminder 1", "TR Checklist Reminder 2") can be added in Admin Console (CRM Email Template or Matter Email Template) with placeholders like `{client_name}`, `{crm_ref}`, `{expiry_date}`. Reminder preview merges these; send-reminder passes final subject/body to the same sendmail path.
- **SMS:** Use existing Admin Console SMS send (e.g. `sendFromTemplate` or equivalent). TR SMS templates with same placeholders; reminder preview returns merged body; send-reminder calls SMS API and records event.

---

## 10. Edge Cases & Error Handling

### 10.1 Missing client email or phone

- **Issue:** Client has no email (for email reminder) or no mobile (for SMS reminder).
- **Solution:** `sendReminder` returns JSON error: `{ success: false, error: 'No primary email found for this client' }` (or 'No mobile phone found'). Frontend shows alert; staff can manually add email/phone in client detail and retry.

### 10.2 Template not found

- **Issue:** No TR Checklist Reminder 1/2 template exists in Admin Console.
- **Solution:** `reminderPreview` returns error: `{ success: false, error: 'Template "tr_checklist_reminder_1" not found' }`. Document template slugs in implementation notes; seed or instruct admin to create templates before using reminders.

### 10.3 Email/SMS send failure

- **Issue:** Mail server error, SMS API failure, network issue.
- **Solution:** `sendReminder` catches exception; **does not create event row** (transaction rollback); returns `{ success: false, error: 'Failed to send email: [error message]' }`. Frontend shows error; staff can retry or contact support.

### 10.4 Duplicate reminder sends

- **Issue:** Staff clicks "Reminder 1 – Email" multiple times (intentional or accidental).
- **Solution:** Plan allows multiple sends (creates multiple event rows). Frontend can optionally check if a recent event exists (e.g. within last 24 hours) and show confirmation dialog: "Reminder 1 email was already sent on 01/02/2026. Send again?" before calling `sendReminder`.

### 10.5 No TR matter for client

- **Issue:** Client has no TR matter in `client_matters` (or matter is inactive).
- **Solution:** Base query only returns clients with at least one active TR matter. If a client has no TR matter, they don't appear in the list. To add TR checklist for a client, staff must first create a TR matter (via client detail Matter tab) and then add the TR reference.

### 10.6 Multiple TR matters for one client

- **Issue:** Client has 2+ active TR matters (rare but possible).
- **Solution:** Base query uses "latest TR matter per client" (MAX(id) or ORDER BY id DESC LIMIT 1 per client). Only one row per client appears in the sheet. If multiple TR matters need tracking, adjust base query to show all (remove the "latest" limit) or group by matter.

### 10.7 Missing rec_date and checklist_send_date

- **Issue:** TR reference row exists but both rec_date and checklist_send_date are null; buttons should not show.
- **Solution:** Button visibility check in view: `@if($row->rec_date || $row->checklist_send_date) ... show buttons @endif`. Empty dates = no buttons.

### 10.8 Date validation errors

- **Issue:** Staff enters invalid date (e.g. 32/13/2026) or future date for last_date.
- **Solution:** Controller validates dates; Laravel's `date` validation; optionally enforce `last_date <= today`. Return validation error JSON; frontend shows validation message.

### 10.9 Large body preview in event table

- **Issue:** Email body can be very long (thousands of chars); storing full body in `body_preview` bloats events table.
- **Solution:** Store first 200 chars in `body_preview` for display in checklist box (e.g. "Reminder 1 email: Dear John, We are writing to..."). Full body not stored (rely on email log or activities_logs if needed for audit).

---

## 11. Implementation Order (when you apply)

1. **Migrations** – Create `client_tr_references` and `tr_reminder_events`; run `php artisan migrate`.
2. **Models** – Create `ClientTrReference` and `TrReminderEvent` (fillable, casts, relations).
3. **Routes** – Add TR sheet, TR insights, reminder-preview, send-reminder; optional PATCH for updating reference.
4. **Controller** – Create `TrSheetController`: index, insights, reminderPreview, sendReminder; optional updateReference. Base query with eager-load of reminderEvents; no monthly grouping.
5. **Views** – Create `tr.blade.php` (table with **one checklist box** per row: dates + events + Reminder 1/2 Email/SMS buttons + optional Edit dates) and `tr-insights.blade.php`.
6. **Reminder popup** – Modal + JS: on button click, AJAX reminder-preview → show subject/body in modal; on Send, POST send-reminder with edited content; on success, refresh checklist box (events) or row.
7. **Header** – Add TR Checklist link to Sheets dropdown.
8. **Templates** – Add TR Checklist Reminder 1 & 2 email (and optionally SMS) templates in Admin Console with placeholders; wire template IDs or slugs in controller for preview/send.
9. **Matter type** – Ensure a TR matter type exists in `matters` (manual or seed).
10. **Testing** – Add TR matter + `client_tr_references` row; confirm list, checklist box, reminder buttons; test preview popup and send (email/SMS); confirm events appear in sheet.
11. **Optional** – Inline/modal edit for checklist and follow-up dates (PATCH endpoint + JS); consolidated columns (SOS, TRN, Skill Assessment, AFP, Medical, Lodgement) if merging second sheet.

---

## 12. Files to Create / Modify (summary)

| Action  | File |
|---------|------|
| Create  | `database/migrations/xxxx_create_client_tr_references_table.php` |
| Create  | `database/migrations/xxxx_create_tr_reminder_events_table.php` |
| Create  | `app/Models/ClientTrReference.php` |
| Create  | `app/Models/TrReminderEvent.php` |
| Create  | `app/Http/Controllers/CRM/TrSheetController.php` |
| Create  | `resources/views/crm/clients/sheets/tr.blade.php` |
| Create  | `resources/views/crm/clients/sheets/tr-insights.blade.php` |
| Modify  | `routes/clients.php` (add TR list, insights, reminder-preview, send-reminder; optional update) |
| Modify  | `resources/views/Elements/CRM/header_client_detail.blade.php` (add TR Checklist dropdown item) |
| Add (JS) | Inline script or asset for reminder popup (preview, edit, send, refresh events) |

---

## 13. Out of Scope (unless you request later)

- EOI-style **client-facing** TR checklist confirmation (e.g. client portal link to confirm checklist received). Not in this plan.
- **Excel import/export** for TR. Can be added later using the same tables.
- **Consolidated sheet** with SOS, TRN, Skill Assessment, AFP, Medical, Lodgement columns (second screenshot) — plan describes optional merge; implement when data sources for those columns are defined.

---

## 14. Summary & Key Decisions

This plan delivers:

1. **One sheet for all** — Single flat list for all TR records; no monthly grouping; single table with pagination and filters.
2. **Dynamic checklist box** — One combined cell per row showing: Rec Date, Last Date, Checklist sent, Follow up 1, Follow up 2 (and optionally Call), plus recorded reminder events (email/SMS sent dates).
3. **Reminder workflow** — Buttons for "Reminder 1 – Email/SMS" and "Reminder 2 – Email/SMS"; click opens popup with merged template for staff to review/edit; send records event and auto-updates last_date and follow-up dates.
4. **Event recording** — New `tr_reminder_events` table tracks every reminder send (who, when, channel, recipient); events displayed in checklist box.
5. **Inline/modal editing (optional)** — PATCH endpoint to update dates, docs_requested, comments from the sheet without navigating away.
6. **Full audit trail** — created_by, updated_by, sent_by, event timestamps for compliance.
7. **Consistent with existing sheets** — Mirrors ART/EOI sheet patterns (controller, filters, sorting, insights, tabs, dropdown navigation).

**Key decisions made in this plan:**

- **rec_date** is the primary date (trigger for reminders); checklist_send_date is optional or can be same as rec_date.
- **last_date** auto-updated on reminder send.
- **Unique index** on (client_id, client_matter_id) = one TR reference per client per TR matter.
- **Separate events table** (not columns on TR references) for full audit and flexibility.
- **Template resolution** by slug (e.g. `tr_checklist_reminder_1`); placeholders: `{client_name}`, `{crm_ref}`, `{expiry_date}`, `{docs_requested}`.
- **Authorization:** Any admin with module access can send reminders (record sent_by); no restriction to assigned agent in this plan.
- **Error handling:** JSON errors for missing email/phone, template not found, send failures; transactions for send + event creation.
- **Duplicate sends allowed** (creates multiple events); frontend can optionally warn/confirm.
- **Base query** = latest TR matter per client + left join TR references; clients with TR matter but no TR reference row will show with empty checklist.

**Next steps:** Review and approve this plan; then proceed with implementation step by step (section 11) or request specific sections first.
