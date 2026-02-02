# Checklist Sending System - Design Document

**Version:** 2.0 (Updated for Lead/Client Tab Integration, TR Sheet Recording, Follow-ups & Status Options)  
**Last Updated:** February 2026

---

## Context

I'm building a Laravel migration management CRM system. I need to design a **general-purpose checklist sending system** that can generate, customize, and send checklists to leads/clients via email/SMS. The system integrates with the **TR Sheet** and provides follow-up workflows (Email/SMS), status tracking (convert, abandon, follow up in 6 months, etc.), and supports multiple checklists per client (multiple matters).

---

## 1. Primary Integration: Checklists Tab in Lead & Client Detail

### 1.1 New "Checklists" Tab

- **Location:** Add a new sidebar tab **"Checklists"** in both **Lead** and **Client** detail views (same pattern as Emails, Notes, etc.).
- **Visibility:**
  - **Clients (with matters):** Full tab visible; matter dropdown available.
  - **Leads (no matters):** Tab visible; matter selection optional or "General" — allows sending pre-engagement checklists; or show "Add a matter first" prompt if business rules require matter.
- **Tab icon:** e.g. `fa-clipboard-list` or `fa-tasks`.
- **Files to modify:**
  - `resources/views/crm/clients/detail.blade.php` — add sidebar button and tab pane.
  - Create `resources/views/crm/clients/tabs/checklists.blade.php` — main tab content.
  - `public/js/crm/clients/sidebar-tabs.js` — register new tab.
  - `resources/views/crm/companies/detail.blade.php` — add tab for company clients if applicable.

### 1.2 Tab Content Layout

```
┌─────────────────────────────────────────────────────────────────────────────┐
│  Checklists                                                     [Send Checklist] │
├─────────────────────────────────────────────────────────────────────────────┤
│  Matter: [Dropdown: Select Matter ▼]  (or "General" for leads)               │
├─────────────────────────────────────────────────────────────────────────────┤
│  SENT CHECKLISTS                                                            │
│  ┌─────────────────────────────────────────────────────────────────────────┐ │
│  │ TR Checklist • Matter: TR_1 • Sent 01/02/2026 • Status: Active          │ │
│  │ [Follow up by Email] [Follow up by SMS] [Change Status ▼]                │ │
│  └─────────────────────────────────────────────────────────────────────────┘ │
│  ┌─────────────────────────────────────────────────────────────────────────┐ │
│  │ Visa Document Checklist • Matter: PT_1 • Sent 28/01/2026 • Converted    │ │
│  │ [View] [Change Status ▼]                                                │ │
│  └─────────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Checklist Send Workflow

### 2.1 Step-by-Step Flow

1. **Select Matter** — Dropdown of client's active matters (or "General" for leads). Required for TR sheet recording when matter is TR type.
2. **Select Template** — Dropdown of checklist templates (TR Checklist, Visa Document Checklist, Medical Checklist, etc.) from Admin Console.
3. **Add Attachments** — Optional: attach files from S3 (client docs) or upload; e.g. checklist PDF, sample forms.
4. **Create/Send** — Click "Send Checklist" → opens **Send Checklist Popup**.
5. **Popup Contents:**
   - **Subject** (from template, editable).
   - **Body** (merged with placeholders, editable).
   - **Attachments** (list of selected files; can remove before send).
   - **Send** and **Cancel** buttons.
6. **On Send:**
   - Send email via existing CRM email system.
   - Record in `checklist_instances`, `activities_logs`.
   - **If TR matter:** Create/update `client_tr_references` and record `checklist_send_date`, `rec_date`; optionally create initial event in `tr_reminder_events` or equivalent for "Checklist Sent".

### 2.2 Popup Behavior

- **Preview before send:** Body and subject are pre-filled from template merge; staff can edit.
- **Attachments:** Shown as list with remove option; new attachments can be added in the parent form before opening popup.
- **Validation:** Ensure client has valid email before enabling Send.

---

## 3. TR Sheet Integration & Recording

Per `docs/TR_SHEET_IMPLEMENTATION_PLAN.md`:

### 3.1 When Checklist Is Sent (TR Matter)

- **Create or update `client_tr_references`:**
  - Set `checklist_send_date` = today.
  - Set `rec_date` = today (or keep existing).
  - Set `last_date` = today.
- **Record event:** Create row in `tr_reminder_events` (or `checklist_send_events` if separate) with:
  - `channel` = 'checklist' or 'email',
  - `reminder_number` = 0 (initial checklist),
  - `sent_at`, `sent_by`, `recipient`, `subject`, `body_preview`.
- **Activity log:** Log "Checklist sent" in `activities_logs` with `type` = 'checklist_send' or 'email'.

### 3.2 When Follow-up (Email or SMS) Is Sent

- **Create `tr_reminder_events` row** with `reminder_number` = 1 or 2, `channel` = 'email' or 'sms'.
- **Update `client_tr_references`:**
  - `last_date` = today.
  - `first_reminder_date` or `second_reminder_date` as per TR plan logic.
- **Activity log:** Log "Checklist follow-up email sent" or "Checklist follow-up SMS sent" in `activities_logs`.

---

## 4. Follow-up Buttons (By Email & By SMS)

### 4.1 Placement

- In the **Checklists tab:** Each sent checklist row shows **"Follow up by Email"** and **"Follow up by SMS"**.
- In the **TR Sheet** "Checklist & Follow-ups" box: Same buttons (Reminder 1 – Email, Reminder 1 – SMS, Reminder 2 – Email, Reminder 2 – SMS) as per TR plan.

### 4.2 Behaviour

- **Follow up by Email:** Opens popup with merged email template (Reminder 1 or 2); staff reviews/edits; sends. On success: record event, update TR sheet dates, refresh Checklists tab.
- **Follow up by SMS:** Same flow for SMS template.
- **Sheet updates:** Both Checklists tab and TR Sheet consume the same `tr_reminder_events` and `client_tr_references`; any follow-up sent from either place updates both views.

---

## 5. Add New Checklist (Multiple Matters)

### 5.1 Multiple Checklists Per Client

- A client can have **multiple matters**; each matter can have **multiple checklists** (e.g. TR Checklist for TR_1, Visa Document Checklist for PT_1).
- **"Add new checklist" / "Send Checklist"** always creates a new `checklist_instance` linked to the selected matter.
- **List view:** Group or filter by matter; show matter ref in each row.

### 5.2 Add New Checklist Button

- Primary CTA: **"Send Checklist"** or **"Add Checklist"** at top of Checklists tab.
- Opens the same send workflow: Matter → Template → Attachments → Popup → Send.

---

## 6. Checklist Status / Outcome Options

### 6.1 Status Field

Add to `checklist_instances` (or equivalent):

| Status           | Description                                      |
|------------------|--------------------------------------------------|
| `active`         | Checklist sent; awaiting response / in progress  |
| `converted`      | Lead converted to client (or matter progressed)  |
| `abandoned`      | Matter/lead abandoned; no further action         |
| `follow_up_6m`   | Follow up in 6 months                            |
| `follow_up_3m`   | Follow up in 3 months                            |
| `follow_up_1m`   | Follow up in 1 month                             |
| `completed`      | All items completed / matter closed              |
| `other`          | Other (with optional notes)                      |

### 6.2 UI: Change Status Dropdown

- Each checklist row has **"Change Status"** dropdown.
- Staff selects new status; optional notes field for `other` or custom follow-up.
- On save: update `checklist_instances.status`, optionally set `client_tr_references.comments` or a dedicated status field if TR-specific.
- **Convert to client:** When status = `converted`, optionally trigger existing lead-to-client conversion workflow (if lead).

---

## 7. Current System Overview

**Existing features:**
- **Sheets system**: EOI/ROI Sheet, ART Sheet, TR Sheet (in progress) — track client matters with list view + insights
- **Email system**: Laravel Mail + Mailables; templates in Admin Console (CRM Email Templates, Matter Email Templates) with placeholders like `{client_name}`, `{crm_ref}`, etc.
- **SMS system**: Admin Console SMS send with templates; SMS logs in database
- **Documents**: S3 storage for client documents; can attach to emails
- **Activity logs**: `activities_logs` table tracks all client interactions (emails, SMS, notes, etc.)
- **Client portal**: Clients can log in, view matters, upload docs, confirm data (e.g. EOI confirmation workflow)

**TR Sheet context** (the use case that triggered this request):
- TR (Tribunal Review) checklist sheet tracks clients with TR matters
- Need to send reminders to clients: "Reminder 1" (email/SMS) and "Reminder 2" after initial checklist sent
- Current plan: hardcoded "TR Checklist Reminder 1/2" templates; staff review/edit email body in popup before sending
- **Problem:** This approach is rigid — each new checklist type (TR, visa document, medical, skill assessment, etc.) would need separate templates and hardcoded logic

## Requirements for General Checklist System

### 1. **Checklist Types (examples)**
- **TR Checklist**: Documents needed for tribunal review (passport, AFP, medical, skills assessment, etc.)
- **Visa Document Checklist**: General visa application docs (birth cert, marriage cert, police checks, etc.)
- **Medical Checklist**: Medical examination requirements (chest X-ray, blood test, appointment booking, etc.)
- **Post-Visa Checklist**: After visa grant (activate visa, book travel, inform authorities, etc.)
- **Skills Assessment Checklist**: Docs needed for skills assessment body (qualifications, work refs, English test, etc.)

Each checklist type can have **different items/sections** and **different email/SMS templates**.

### 2. **Checklist Items Structure**
Each checklist should support:
- **Checklist name/title** (e.g. "TR Document Checklist", "Visa 189 Medical Requirements")
- **Items/tasks** (checkbox list):
  - Item name (e.g. "Current Passport", "AFP Check", "Chest X-ray")
  - Item description (optional, e.g. "Valid for at least 6 months")
  - Status: Pending / Completed / Not Applicable
  - Due date (optional)
  - Notes (staff can add notes per item)
- **Sections** (optional grouping, e.g. "Identity Documents", "Health Requirements", "Financial Documents")
- **Attachments** (optional): Attach relevant docs from client's S3 folder (e.g. checklist PDF, sample forms)

### 3. **Checklist Generation Workflow**
**How staff create and send checklists:**
1. **Select checklist template** (e.g. "TR Checklist" from dropdown or pre-defined library)
2. **Customize items** (add/remove/edit items; check "Not Applicable" for irrelevant items)
3. **Preview checklist** (see what client will receive: email body with checklist items formatted as HTML table or list)
4. **Send to client** (via email and/or SMS):
   - Email: Checklist embedded in email body (HTML) + optional PDF attachment
   - SMS: Short message with link to view full checklist online (client portal or public page)
5. **Track status**:
   - Log send event in `activities_logs`
   - Store checklist instance in database (so we can see what was sent, when, and track client progress if they update it)
   - Optionally allow client to mark items as completed via portal (like EOI confirmation workflow)

### 4. **Integration with Existing Sheets**
**Checklists tab (primary):**
- New tab in Lead & Client detail with full send workflow: Matter → Template → Attachments → Popup → Send.
- Lists all sent checklists with Follow up by Email/SMS and Change Status.

**TR Sheet:**
- "Checklist & Follow-ups" box has **"Send Checklist"** button.
- Click → Opens same modal: Select template, add attachments, preview, send.
- Records in `client_tr_references` (checklist_send_date, rec_date, last_date), `tr_reminder_events`, `activities_logs`.
- Reminder 1/2 – Email/SMS buttons for follow-ups; updates sync with Checklists tab.

**EOI/ROI Sheet**: Could add "Send Document Checklist" button after EOI verified.

**ART Sheet**: Similar — send post-hearing checklist.

### 5. **Checklist Templates (Library)**
Store checklist templates in database:
- **Table**: `checklist_templates` (id, name, description, type, items_json, email_template_id, sms_template_id, is_active, created_by, updated_by, timestamps)
- **items_json**: JSON array of default items, e.g.:
  ```json
  [
    {"section": "Identity Documents", "items": [
      {"name": "Current Passport", "description": "Valid for at least 6 months", "required": true},
      {"name": "Birth Certificate", "description": "", "required": true}
    ]},
    {"section": "Health Documents", "items": [
      {"name": "Medical Examination Report", "description": "Form 160 or 26", "required": true},
      {"name": "Chest X-ray", "description": "", "required": false}
    ]}
  ]
  ```
- **Admin UI**: CRUD for checklist templates (Admin Console → Checklist Templates)

### 6. **Checklist Instances (Sent Checklists)**
When a checklist is sent to a client, store it:
- **Table**: `checklist_instances` (id, client_id, client_matter_id nullable, template_id, template_name, items_json, sent_at, sent_by, sent_via (email/sms/both), client_viewed_at, client_completed_at, status (active/converted/abandoned/follow_up_6m/follow_up_3m/follow_up_1m/completed/other), client_tr_reference_id nullable, notes, created_at, updated_at)
- **items_json**: Snapshot of items at time of send (with status: pending/completed/na)
- **client_matter_id**: Nullable for leads (no matter); required for clients with matters when recording in TR sheet
- **client_tr_reference_id**: FK to `client_tr_references` when matter is TR type (for TR sheet integration)
- **Status tracking**: If client portal allows, client can mark items as completed; staff can update outcome status (convert, abandon, follow up in 6m, etc.) via "Change Status" dropdown

### 7. **Email/SMS Templates**
- **Email template**: Subject + body with placeholder `{checklist_items_html}` that renders the checklist as HTML table or list
  - Example subject: "Action Required: Document Checklist for Your {matter_type}"
  - Body: "Dear {client_name}, Please provide the following documents: {checklist_items_html}. Contact us if you have questions."
  - `{checklist_items_html}` = rendered HTML:
    ```html
    <h3>Identity Documents</h3>
    <ul>
      <li>☐ Current Passport (Valid for at least 6 months)</li>
      <li>☐ Birth Certificate</li>
    </ul>
    <h3>Health Documents</h3>
    <ul>
      <li>☐ Medical Examination Report (Form 160 or 26)</li>
      <li>☐ Chest X-ray</li>
    </ul>
    ```
- **SMS template**: Short message with link, e.g. "Hi {client_name}, we've sent you a document checklist. View it here: {checklist_link}"
- Templates can be per-checklist-type or shared; link via `checklist_templates.email_template_id`

### 8. **Client Portal Integration (Optional but Recommended)**
- **View checklist**: Client logs in, sees "My Checklists" tab; lists all checklists sent to them (with status)
- **Mark items complete**: Client checks off items as they complete them (saves to `checklist_instances.items_json`)
- **Upload docs**: Client can upload docs against checklist items (link to `documents` table)
- **Public link (token-based)**: For clients without portal login, generate token and send link (like EOI confirmation workflow)

### 9. **Reminders & Follow-ups**
- **Auto-reminders**: If checklist not completed within X days, auto-send reminder (cron job)
- **Manual reminders**: Staff can click **"Follow up by Email"** or **"Follow up by SMS"** in Checklists tab, or Reminder 1/2 – Email/SMS in TR sheet (uses same checklist instance, re-sends email/SMS)
- **Reminder templates**: Separate email/SMS templates for reminders (e.g. "TR Checklist Reminder 1", "TR Checklist Reminder 2")
- **Sheet updates**: Follow-up sends update `tr_reminder_events`, `client_tr_references` (last_date, first_reminder_date, second_reminder_date); both Checklists tab and TR Sheet reflect changes

### 10. **Reporting & Tracking**
- **Dashboard widget**: "Checklists Pending" count, "Checklists Completed This Week", etc.
- **Checklist status in sheets**: TR sheet (and others) shows checklist status in "Checklist & Follow-ups" box:
  - "Checklist sent 01/02/2026 (3/10 items completed)"
  - Click to view details or send reminder

---

## Design Questions for You

1. **Database schema**: Design `checklist_templates` and `checklist_instances` tables. Should items be JSON or separate `checklist_template_items` and `checklist_instance_items` tables? (JSON is simpler; separate tables are more queryable/reportable)

2. **Controller structure**: Should there be:
   - One `ChecklistController` for all checklist operations (send, view, update)?
   - Or separate controllers per checklist type (e.g. `TrChecklistController`, `VisaChecklistController`)?
   - Recommend one general `ChecklistController` + `ChecklistService` for business logic.

3. **Modal/popup for sending**: When staff clicks "Send Checklist" in TR sheet:
   - Open modal: Select template → Customize items → Preview → Send
   - Or: Open separate page (e.g. `/clients/{id}/send-checklist`)?
   - Recommend modal for in-sheet workflow (consistent with TR reminder popup).

4. **Client portal vs public link**: Should clients:
   - Log in to portal to view/complete checklists (requires portal account)?
   - Use token-based public link (no login, like EOI confirmation)?
   - Both options (staff chooses when sending)?
   - Recommend both: default = portal (if client has account); fallback = public link (generate token if no portal login).

5. **Integration points**:
   - TR sheet: "Send Checklist" button in checklist box
   - Client detail: "Checklists" tab (list all sent, send new, view status)
   - EOI/ROI sheet, ART sheet: Add "Send Checklist" button?
   - Recommend: Add "Send Checklist" action to all sheets + client detail tab for central access.

6. **Template merge placeholders**: What placeholders should be supported?
   - Client: `{client_name}`, `{crm_ref}`, `{email}`, `{phone}`
   - Matter: `{matter_type}`, `{matter_ref}`, `{expiry_date}`
   - Checklist: `{checklist_name}`, `{checklist_items_html}`, `{checklist_link}`, `{due_date}`
   - Staff: `{staff_name}`, `{staff_email}`, `{staff_phone}`
   - System: `{current_date}`, `{company_name}`, `{company_website}`

7. **PDF generation**: Should system auto-generate checklist PDF and attach to email?
   - Use Laravel PDF library (e.g. Dompdf, Snappy/wkhtmltopdf)?
   - Or staff manually attach pre-made checklist PDFs from S3?
   - Recommend auto-generate (using items_json → PDF) for consistency.

8. **Permissions**: Who can:
   - Create/edit checklist templates? (Admin only or all staff with module access?)
   - Send checklists? (All staff with client access or only assigned agent?)
   - View client checklist status? (All staff or only assigned agent?)
   - Recommend: Templates = admin only; send/view = all staff with module access (same as sheets).

9. **Localization**: Support multiple languages for checklist items and email templates? Or English only?
   - If multi-language, need `checklist_template_items_translations` table.
   - Recommend: English only for MVP; add localization later if needed.

10. **Integration with existing reminder system**: TR sheet has Reminder 1/2 (email/SMS with popup). Should:
    - Checklists be separate from reminders (different buttons: "Send Checklist" vs "Send Reminder 1")?
    - Or merge: "Send Reminder 1" = send checklist + reminder message?
    - Recommend: Separate initially (checklist = send once at start; reminders = follow-up nudges). Can merge later if workflows overlap.

---

## Deliverables Requested

1. **Database schema** (migrations for `checklist_templates`, `checklist_instances`, and any related tables)
2. **Controller methods** (ChecklistController: send, view, update, remind, etc.)
3. **Routes** (web + AJAX)
4. **Models** (ChecklistTemplate, ChecklistInstance with relations and casts)
5. **Views**:
   - **Checklists tab**: `resources/views/crm/clients/tabs/checklists.blade.php` — list sent checklists, Send Checklist button, Follow up by Email/SMS, Change Status dropdown
   - **Send Checklist modal**: Select matter, template, attachments; popup with body + attachments for review before send
   - Admin Console: Checklist templates CRUD (list, create, edit)
   - Client portal: View checklists, mark items complete, upload docs
   - Public page: Token-based checklist view (no login)
6. **Email/SMS sending logic** (integrate with existing Mail/SMS system)
7. **Activity logging** (log checklist sends to `activities_logs`)
8. **Integration examples**:
   - **Checklists tab** (primary): New tab in Lead & Client detail — Send Checklist, list sent checklists, Follow up by Email/SMS, Change Status
   - **TR sheet**: "Send Checklist" button in checklist box; Reminder 1/2 – Email/SMS buttons; events and dates sync with Checklists tab
9. **PDF generation** (optional but recommended)
10. **Implementation plan** (step-by-step, similar to TR_SHEET_IMPLEMENTATION_PLAN.md)

---

## Existing Files to Reference

- **EOI/ROI verification workflow**: `app/Http/Controllers/CRM/EoiRoiSheetController.php` (lines 473-589) — shows email sending, token generation, activity logging pattern
- **TR Sheet plan**: `docs/TR_SHEET_IMPLEMENTATION_PLAN.md` — shows reminder popup, template merge, event recording
- **Email templates**: Admin Console → CRM Email Templates (stored in DB, rendered via Blade or similar)
- **SMS sending**: Admin Console → SMS Management (existing SMS API integration)
- **Documents**: `app/Models/Document.php`, S3 storage pattern
- **Client portal**: Existing portal pages (client login, view matters, upload docs)

---

## Design Priorities

1. **Reusable**: One system for all checklist types (not hardcoded per sheet)
2. **Flexible**: Easy to add new checklist templates without code changes (CRUD in Admin Console)
3. **User-friendly**: Modal workflow for staff (select, customize, preview, send in 4 steps)
4. **Trackable**: Full audit trail (activity logs, checklist instances, client completion status)
5. **Consistent**: Follows existing patterns (Mail/SMS, token-based public links, module permissions, SweetAlert2, etc.)

---

## Optional Enhancements (Out of Scope for MVP, but note for future)

- **Conditional items**: Show/hide items based on client data (e.g. "Partner docs" only if marital_status = married)
- **Auto-population**: Pre-fill checklist items based on client data (e.g. "Passport expiry: {passport_expiry_date}")
- **Checklist versioning**: Track changes to templates over time; see what version was sent to each client
- **Bulk send**: Send same checklist to multiple clients at once (e.g. all TR clients missing docs)
- **Checklist dependencies**: Item 2 can't be done until Item 1 is complete (task workflow)
- **Integration with document categories**: Link checklist items to specific document categories in S3 (so when client uploads "Passport", it auto-marks checklist item as complete)

---

## Expected Output

A comprehensive design document (similar to TR_SHEET_IMPLEMENTATION_PLAN.md) with:
- Business requirements
- Database schema (migrations)
- Models (with fillable, casts, relations)
- Controller methods (detailed specs)
- Routes (GET/POST endpoints)
- Views (mockups or descriptions)
- Email/SMS templates (structure and placeholders)
- Integration points (how it connects to TR sheet, client detail, other sheets)
- Implementation order (step-by-step)
- Testing checklist
- Edge cases and error handling

---

## Summary: Checklist Sending System v2 — Complete Workflow

| Component | Description |
|-----------|-------------|
| **Tab** | New "Checklists" tab in Lead & Client detail (sidebar) |
| **Send flow** | 1. Select matter → 2. Select template → 3. Add attachments → 4. Open popup (body + attachments) → 5. Review & Send → 6. Record |
| **Recording** | `checklist_instances`, `activities_logs`; for TR matters: `client_tr_references`, `tr_reminder_events` |
| **Follow-ups** | "Follow up by Email" / "Follow up by SMS" buttons; update TR sheet dates and events |
| **Add new** | "Send Checklist" / "Add Checklist" — supports multiple checklists per matter per client |
| **Status options** | Active, Converted, Abandoned, Follow up (1m/3m/6m), Completed, Other |
| **TR Sheet sync** | Checklist send and follow-ups update `client_tr_references`, `tr_reminder_events`; both Checklists tab and TR Sheet stay in sync |

---

## Implementation Checklist

- [ ] Add `client_tr_reference_id` and `status` (enum) to `checklist_instances` migration
- [ ] Create Checklists tab view (`checklists.blade.php`) and register in sidebar
- [ ] Implement Send Checklist modal: matter select, template select, attachments, preview popup
- [ ] Wire ChecklistController: send, followUpEmail, followUpSms, updateStatus
- [ ] Integrate with TR sheet: create/update `client_tr_references`, record events on send and follow-up
- [ ] Add Follow up by Email/SMS buttons and Change Status dropdown to each checklist row
- [ ] Log all sends and follow-ups to `activities_logs`

---

Ready to start? Let me know if you need any clarification on the current system or requirements!
