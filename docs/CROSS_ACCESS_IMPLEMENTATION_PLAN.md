# Cross-access & allocated-only visibility — implementation plan

**v4.0** — `teams` table confirmed; all open items fully resolved; plan is implementation-ready.

This document turns agreed product rules into build phases for the Laravel/PostgreSQL CRM.

---

## 1. Goals (summary)

| Area | Rule |
|------|------|
| Default visibility | **Allocated only** for **all** staff **except** configurable **exempt roles** (Super Admin, Admin, + extras). |
| Exempt staff | No quick-access or supervisor request flow; full access; still **logged** in same audit stream as `access_type = exempt`. |
| Extra access | **Quick** (15 min, selectable reason) or **Supervisor-approved** (24 h from approval). Quick-flagged users may **also** request 24 h. |
| Record types | **Clients and leads** — same cross-access model for both. |
| Initiation | **Search only** (header/global search + equivalent APIs); other deep links enforce grant check or 403/redirect. |
| Context | **Office** from `branches` table (FK on `staff.office_id` + `client_matters.office_id`); **team** from `teams` table (`id`, `name`, `color`) — `staff.team` is an integer FK into `teams.id`. Both stored as id + label snapshot on each grant. |
| Enforcement | **Server-side**, on **every protected action**, using **server UTC** time. |
| Audit | One reporting stream: grants + exempt access; `access_type` = `quick` / `supervisor_approved` / `exempt`. |
| Approvers | **Super Admin (role 1)** + fixed staff ids: `36834, 36524, 36692, 36483, 36484, 36718, 36523, 36836, 36830`. |
| Approval queue UI | Visible **only** to Super Admin + approver staff ids; entry from **dashboard** and **popup**. |

---

## 2. Confirmed role IDs (from `user_roles` table)

| `user_roles.id` | Name | Visibility today | v3 treatment |
|----------------|------|-----------------|--------------|
| **1** | Super Admin | All | **Exempt** — no flow, logged as `exempt` |
| **12** | Person Responsible | All leads + all clients | **Restrict to allocated** in Phase C |
| **13** | Person Assisting | Clients/leads where PA on matter or `user_id` match | Already restricted; grant flow adds temp extension |
| **14** | Calling Team | All clients today | **Restrict**; **quick access only** (no supervisor path); `quick_access_enabled` defaults `true` for this role |
| **15** | Accountant | All clients today | **Restrict**; standard grant flow |
| **16** | Migration Agent | All clients today | **Restrict**; standard grant flow |
| **17** | Admin | All | **Exempt** — no flow, logged as `exempt` |

**Key decisions locked:**

- **Exempt roles** (bypass all flows, logged as `exempt`): `1` (Super Admin) and `17` (Admin). Config key: `CRM_ACCESS_EXEMPT_ROLE_IDS=1,17`.
- **Calling Team (14) — quick access only:** the supervisor approval path is **hard-blocked** for role 14 in the service layer. They may only get a 15-minute quick grant. In the setup migration, set `quick_access_enabled = true` for **all existing** role-14 staff. New role-14 staff: default `true` at creation.
- **Leads — same restriction as clients after Phase C:** everyone except exempt roles is restricted to allocated leads. This removes the current "Person Responsible sees all leads" exception.
- **Exempt logging:** write **one row per calendar day per staff + admin record combo** (not every page load). Keeps the audit table clean while full coverage is maintained.
- **Search results:** show non-allocated records as **locked** with "Request Access" CTA — never hide them. Required for search-only initiation.
- **Notifications:** use **existing** `Notification` model + `BroadcastNotificationCreated` broadcast. No email in v1.

---

## 3. Configuration (no code deploy for list tweaks)

File: `config/crm_access.php` (new file, all overridable via `.env`):

```php
return [
    // Role IDs that bypass allocation and the grant flow entirely
    'exempt_role_ids' => env('CRM_ACCESS_EXEMPT_ROLE_IDS', '1,17'),

    // staff.id values allowed to approve requests (plus all role-1 users)
    'approver_staff_ids' => env('CRM_ACCESS_APPROVER_STAFF_IDS',
        '36834,36524,36692,36483,36484,36718,36523,36836,36830'),

    // Quick-access options presented in the modal (code => label)
    // Calling Team always sees this list; others see it if quick_access_enabled = true
    'quick_reason_options' => [
        'calling'     => 'Calling / Reception',
        'cover'       => 'Covering Absent Colleague',
        'urgent'      => 'Urgent Client Follow-up',
        'admin_task'  => 'Administrative Task',
    ],

    // Roles restricted to quick access only (supervisor path blocked)
    'quick_access_only_role_ids' => env('CRM_ACCESS_QUICK_ONLY_ROLE_IDS', '14'),

    // Quick grant duration (minutes)
    'quick_grant_minutes' => 15,

    // Supervisor-approved grant duration (hours)
    'supervisor_grant_hours' => 24,

    // Strict allocation enforcement on/off (flip false → true in Phase C)
    'strict_allocation' => env('CRM_ACCESS_STRICT_ALLOCATION', false),
];
```

Document all keys in `.env.example`.

---

## 4. Data model (migrations)

### 4.1 `staff` table

Add one column:

```sql
ALTER TABLE staff ADD COLUMN quick_access_enabled BOOLEAN NOT NULL DEFAULT FALSE;

-- Immediately enable for all existing Calling Team staff (role 14)
UPDATE staff SET quick_access_enabled = TRUE WHERE role = 14;
```

- Only Super Admin may update this flag — enforce in controller/policy.
- New staff created with role 14: set `quick_access_enabled = true` in the staff-creation controller at save time.

### 4.2 `client_access_grants` (new table)

```sql
CREATE TABLE client_access_grants (
    id                    BIGSERIAL PRIMARY KEY,
    staff_id              BIGINT NOT NULL REFERENCES staff(id),
    admin_id              BIGINT NOT NULL REFERENCES admins(id),  -- client or lead row
    record_type           VARCHAR(10) NOT NULL CHECK (record_type IN ('client','lead')),
    grant_type            VARCHAR(20) NOT NULL CHECK (grant_type IN ('quick','supervisor_approved','exempt')),
    access_type           VARCHAR(20) NOT NULL,  -- mirrors grant_type; exists for dashboard grouping
    status                VARCHAR(20) NOT NULL DEFAULT 'pending'
                          CHECK (status IN ('pending','active','expired','revoked','rejected')),
    quick_reason_code     VARCHAR(50),           -- required when grant_type = 'quick'
    requester_note        TEXT,                  -- optional note on supervisor request
    office_id             BIGINT REFERENCES branches(id),
    office_label_snapshot VARCHAR(255),          -- branches.office_name at time of grant
    team_id               INTEGER REFERENCES teams(id),
    team_label_snapshot   VARCHAR(255),          -- teams.name at time of grant (colour optional)
    requested_at          TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    approved_at           TIMESTAMPTZ,
    approved_by_staff_id  BIGINT REFERENCES staff(id),
    starts_at             TIMESTAMPTZ,
    ends_at               TIMESTAMPTZ,
    revoked_at            TIMESTAMPTZ,
    revoke_reason         TEXT,
    created_at            TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at            TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Performance indexes
CREATE INDEX idx_cag_staff_admin_status  ON client_access_grants (staff_id, admin_id, status);
CREATE INDEX idx_cag_ends_at             ON client_access_grants (ends_at) WHERE status = 'active';
CREATE INDEX idx_cag_status_requested    ON client_access_grants (status, requested_at);
CREATE INDEX idx_cag_approver_queue      ON client_access_grants (status, approved_by_staff_id);
```

> `exempt` rows: write one row per record open (or per session if volume is a concern — decide in Phase D). Fields `approved_by_staff_id`, `quick_reason_code` are NULL for exempt rows.

### 4.3 No separate `purposes` table (v1)

Reason options live in config. Add a migration later if you want a DB-managed list.

---

## 5. `staff.team` field — confirmed structure

**Confirmed:** `staff.team` is an integer FK into the existing `teams` table (`id`, `name`, `color`, `created_at`, `updated_at`). No new table needed.

**16 teams in the DB:**

| `teams.id` | Name | Staff count |
|-----------|------|-------------|
| 1 | JRP | 3 |
| 2 | PR | 6 |
| 3 | SKILL ASSESSMENT | 3 |
| 4 | VISITOR | 2 |
| 5 | TR | 3 |
| 6 | HEAD OFFICE | 17 |
| 7 | Accounts | 8 |
| 8 | Tourist visa | 6 |
| 9 | ART | 4 |
| 10 | Student Visa | 15 |
| 11 | Nomination | 5 |
| 12 | 408 Celebrity Visa | 2 |
| 13 | Misc Migration | 3 |
| **14** | **Calling Team Melbourne** | **8** |
| **15** | **Calling Team Adelaide** | **7** |
| 16 | Education | 4 |

> **Important for Calling Team rule:** "Calling Team" is split into two `teams` records (id 14 = Melbourne, id 15 = Adelaide). Both contain staff with `user_role = 14`. The `quick_access_only` rule applies to **`user_roles.id = 14`**, not `teams.id` — so both city sub-teams are correctly covered.

**Grant modal "Team" dropdown:** query `SELECT id, name, color FROM teams ORDER BY name`. Pre-fill from `staff.team` of the logged-in user. If `staff.team` is null, leave blank (optional field). Fall back to showing "Team {id}" only if `teams` has no matching row (per your rule).

---

## 6. Domain service: `App\Services\CrmAccess\CrmAccessService`

Single class, injected via DI or called statically. Centralises all rules.

### Method contracts

```
isExemptRole(Staff $user): bool
    → intval($user->role) in config exempt_role_ids

isApprover(Staff $user): bool
    → isExemptRole (role 1) OR $user->id in approver_staff_ids

hasActiveGrant(Staff $user, int $adminId): bool
    → client_access_grants WHERE staff_id=$user->id
        AND admin_id=$adminId
        AND status='active'
        AND ends_at > UTC_NOW()
    → false if user deactivated (status != 1) regardless of row

canAccessClientOrLead(Staff $user, int $adminId): bool
    1. isExemptRole → true  (+ log exempt row if Phase D active)
    2. StaffClientVisibility::canAccessClientOrLead → true  (allocated)
    3. hasActiveGrant → true  (granted extra access)
    4. → false

requestQuickGrant(Staff $user, int $adminId, string $recordType,
                  int $officeId, ?int $teamId, string $reasonCode): Grant
    - assert $user->quick_access_enabled === true
    - assert $reasonCode in config quick_reason_options keys
    - assert no active quick grant for same staff+admin already running
    - create row: status=active, starts_at=now, ends_at=now+15min

requestSupervisorGrant(Staff $user, int $adminId, string $recordType,
                       int $officeId, ?int $teamId, string $note = ''): Grant
    - REJECT if user role is in quick_access_only_role_ids (role 14 = Calling Team)
      → throw AccessDeniedException('Your role only supports quick access.')
    - create row: status=pending
    - fire notification to all approvers (see §7)

approveGrant(Staff $approver, int $grantId): Grant
    - assert isApprover($approver)
    - set status=active, approved_by_staff_id, approved_at=now,
      starts_at=now, ends_at=now+24h

rejectGrant(Staff $approver, int $grantId, string $reason): Grant
    - assert isApprover($approver)
    - set status=rejected

revokeGrantsForStaff(int $staffId, string $reason): int
    - UPDATE status=revoked WHERE staff_id=$staffId AND status IN ('active','pending')
    - call when: quick_access_enabled toggled off, staff deactivated

expireStaleGrants(): int  (called by scheduled job, safety net only)
    - UPDATE status=expired WHERE status=active AND ends_at < now()
```

### Integration with `StaffClientVisibility`

Add two static helpers:

```php
// StaffClientVisibility.php

public static function isExemptFromAllocation(\App\Models\Staff $user): bool
{
    $exemptIds = array_map('intval',
        explode(',', config('crm_access.exempt_role_ids', '1,17')));
    return in_array((int) $user->role, $exemptIds, true);
}

public static function isQuickAccessOnly(\App\Models\Staff $user): bool
{
    $roleIds = array_map('intval',
        explode(',', config('crm_access.quick_access_only_role_ids', '14')));
    return in_array((int) $user->role, $roleIds, true);
}
```

Then modify `canAccessClientOrLead` and `restrictAdminEloquentQuery` to **skip restrictions** for exempt roles and **additionally check grants** for non-exempt users (Phase C).

---

## 7. HTTP / API surface

All under `auth:admin` middleware. Prefix: `/crm/access/` (or integrate into existing CRM route groups).

| Method | URI | Who | Body / Params |
|--------|-----|-----|---------------|
| `POST` | `/crm/access/quick` | Any staff | `admin_id`, `record_type`, `office_id`, `reason_code` |
| `POST` | `/crm/access/supervisor` | Any staff | `admin_id`, `record_type`, `office_id`, `note?` |
| `GET` | `/crm/access/queue` | Approver / Super Admin only | — |
| `POST` | `/crm/access/{grant}/approve` | Approver / Super Admin only | — |
| `POST` | `/crm/access/{grant}/reject` | Approver / Super Admin only | `reason?` |
| `GET` | `/crm/access/my-grants` | Authenticated staff | — |
| `GET` | `/crm/access/dashboard` | Approver / Super Admin | Filters |

All return JSON. CSRF covered by standard `VerifyCsrfToken` middleware.

---

## 8. Notification system (leverage existing)

The app already has `Notification` model (`sender_id`, `receiver_id`, `module_id`, `url`, `message`) and `BroadcastNotificationCreated` / `NotificationCountUpdated` events for real-time pushes.

**Use the existing system:**

On `requestSupervisorGrant`:

```php
foreach (CrmAccessService::getApproverIds() as $approverId) {
    $notification = Notification::create([
        'sender_id'         => $requester->id,
        'receiver_id'       => $approverId,
        'module_id'         => $grantId,
        'url'               => '/crm/access/queue',
        'notification_type' => 'access_request',
        'message'           => "{$requester->first_name} requested access to client #{$adminId}",
        'receiver_status'   => 1,
        'seen'              => 0,
    ]);
    broadcast(new BroadcastNotificationCreated($notification));
}
broadcast(new NotificationCountUpdated(...));
```

On approve/reject: notify the requester in the same way.

---

## 9. Enforcement points (complete call-site inventory from codebase)

### Already using `canAccessClientOrLead` (must extend to check grants)

| File | Locations |
|------|-----------|
| `ClientsController` | Lines 627, 679, 1858, 2042, 2861, 2938, 7473 |
| `LeadController` | Lines 342, 850, 907, 1237, 1460 |
| `ClientDocumentsController` | Lines 51, 67 |
| `ClientPersonalDetailsController` | Line 1735 |
| `LeadConversionController` | Line 71 |

### Using `restrictAdminEloquentQuery` / `personAssistingStaffIdOrNull` (list queries — extend for Phase C)

| File | Usage |
|------|-------|
| `ClientsController` | Lines 176, 308, 548, 592, 2223, 2283 |
| `ClientQueries` trait | Applies to all controllers using it |
| `VisaTypeSheetController`, `ArtSheetController`, `EoiRoiSheetController` | Sheet-level queries |

### No current `canAccessClientOrLead` check — **add in Phase C**

These controllers accept `client_id` / `admin_id` from request but have **no access gate** today:

| File | Note |
|------|------|
| `DocumentController` | Accepts `client_id` for document operations — no gate found |
| `SignatureDashboardController` | Uses `StaffClientVisibility` scope but confirm coverage |
| `AuditLogController` | May expose log entries for any client_id |
| `ClientAccountsController` | Accepts client_id for account data |
| `BookingAppointmentsController` | Client-linked bookings |
| `BroadcastNotificationAjaxController` | If accepts client_id |
| `OfficeVisitController` | If accepts client_id |
| `EmailUploadController`, `EmailLabelController`, `EmailLogAttachmentController` | Email data tied to clients |

> **Action:** During Phase C, grep all controllers for `$request->client_id`, `$request->admin_id` etc. and insert the gate or delegate to service.

### Search (`getallclients` in `ClientsController`)

Today PA uses `personAssistingStaffIdOrNull` to filter search results. When strict allocation is on, **non-exempt, non-allocated** clients/leads should appear in search results as **locked** (show name only, show "Request Access" button) — **not** hidden. This is the entry point for the cross-access flow.

---

## 10. UI work

### 10.1 Search results (header/global search)

For records the user cannot open:
- Show client/lead name, file number — **locked visual** (greyed row + lock icon).
- **Calling Team (role 14):** show **"Request Quick Access"** only.
- **Other staff with `quick_access_enabled = true`:** show **"Request Quick Access"** and **"Request Supervisor Access"**.
- **Other staff with `quick_access_enabled = false`:** show **"Request Supervisor Access"** only.
- Clicking either opens the **access request modal**.

### 10.2 Access request modal

Fields:
1. **Record** (pre-filled from search — client/lead name + ID, read-only).
2. **Office** — dropdown from `branches` (pre-fill from `staff.office_id` but editable).
3. **Team** — dropdown from `teams` table (`id`, `name`; show colour swatch if useful). Pre-fill from `staff.team` of the logged-in user. Show "Team {id}" only if no matching `teams` row exists.
4. **Reason** — dropdown from `config quick_reason_options` (required for quick; optional/note for supervisor). **Calling Team (role 14):** only sees quick reasons; supervisor note field is hidden.
5. **Note** — optional free text (supervisor requests only; hidden for Calling Team).
6. **Submit button(s):** adapted per role — "Quick Access (15 min)" for Calling Team; dual buttons for others with `quick_access_enabled`.

### 10.3 Approver popup / notification

- Approvers see **pending count badge** on their bell / notification icon (existing `NotificationCountUpdated` mechanism).
- Clicking opens a **mini-queue popup** showing pending requests: requester name, client name, reason, requested at — with **Approve** / **Reject** inline.
- Full queue also accessible from dashboard.

### 10.4 Dashboard

- Filters: user, client/lead, date range, office, team, grant type (`quick` / `supervisor_approved` / `exempt`), status.
- Aggregates: total grants, distinct clients per user, quick vs supervisor split.
- **"Pending Approvals"** section or tab — visible to approvers + Super Admin only.
- Export CSV for audits.

### 10.5 Superadmin staff settings

- Add `quick_access_enabled` toggle on the staff edit screen.
- Visible / editable only to Super Admin (role 1).
- Toggling **off** immediately fires `revokeGrantsForStaff`.

---

## 11. Phased rollout

### Phase A — Foundation (no visible change to staff yet)

1. Migration: `staff.quick_access_enabled`.
2. Migration: `client_access_grants` table.
3. `config/crm_access.php` with all keys.
4. `CrmAccessService` class + unit tests (time mocking, expiry, flag checks).
5. `StaffClientVisibility::isExemptFromAllocation` helper.
6. Feature flag `CRM_ACCESS_STRICT_ALLOCATION=false` (no tightening yet).

### Phase B — Grant flow + search UX

1. HTTP routes + controllers for request / approve / reject / queue / my-grants.
2. Notification wiring (existing `Notification` model + `BroadcastNotificationCreated`).
3. Extend `canAccessClientOrLead` to honour active grants (Phase B safe — without strict allocation, only newly allocated users are affected).
4. Search results: show locked state + "Request Access" buttons for unallocated records.
5. Access request modal UI.
6. `quick_access_enabled` toggle on staff edit (Super Admin only).
7. Logging: quick/supervisor grant rows in `client_access_grants`.

### Phase C — Allocated-only tightening

1. Extend `restrictAdminEloquentQuery` and `canAccessClientOrLead` in `StaffClientVisibility` to apply allocated-only to **all** non-exempt roles (not just PA role 13).
2. Reconcile leads: today `isRestrictedPersonAssisting` only targets role 13; tighten to all non-exempt roles for leads too.
3. Audit and fix **all** controllers that accept `client_id` / `admin_id` without a gate (§9 list above).
4. Deploy to staging with `CRM_ACCESS_STRICT_ALLOCATION=true`; test each role.
5. Promote to production.

### Phase D — Exempt logging + full dashboard

1. Log exempt access rows in `client_access_grants` — **once per calendar day per staff + admin_id combo** (decided in §2).
2. Build dashboard view with all filters, aggregates, Type column, CSV export.
3. Approver queue popup on notification bell.

### Phase E — Hardening + scheduled jobs

1. Scheduled command `access:expire-grants` as safety net (in addition to per-request enforcement).
2. Scheduled check: deactivated staff → revoke active grants.
3. Rate-limit request endpoints (e.g. max 5 pending supervisor requests per user at a time).
4. Security review: IDOR on approve endpoint (ensure approver cannot approve grants for themselves); verify `admin_id` ownership.
5. Load test approver queue with realistic data volume.

---

## 12. Testing checklist

| Scenario | Expected |
|----------|----------|
| Exempt role opens any client/lead | Access granted; exempt row written |
| Non-exempt, allocated (PA on matter / user_id match) | Access granted; no grant row needed |
| Non-exempt, not allocated — no grant | Access denied (403 / redirect + message) |
| Quick access: `quick_access_enabled = false`, non-calling-team | "Request Supervisor Access" only in modal |
| Quick access: `quick_access_enabled = true`, non-calling-team | Both paths offered in modal |
| **Calling Team (role 14), `quick_access_enabled = true`** | "Quick Access" only — no supervisor button shown |
| **Calling Team (role 14) calls supervisor API directly** | 403 / service exception |
| **Calling Team (role 14), `quick_access_enabled = false`** | Cannot access unallocated at all (no button shown) |
| Quick grant created | 15-min expiry; `status = active`; opens client |
| After 15 min | Next access check → denied; grant row `expired` |
| Supervisor grant: pending | All approvers notified via broadcast; requester sees "Pending" |
| Supervisor grant: approved | 24 h from approval moment; requester notified |
| Supervisor grant: rejected | Requester notified; no access |
| Quick-flagged user requests supervisor 24 h | Works (non-calling-team); logged as `supervisor_approved` |
| Multiple quick grants on different clients simultaneously | Each independent timer; all `active` concurrently |
| Exempt logging: same record opened twice same day | Only **one** exempt row written that day |
| `quick_access_enabled` toggled off (any role) | Active quick grants revoked immediately |
| Staff deactivated | All active + pending grants revoked |
| Non-approver hits `/crm/access/queue` | 403 |
| Deep link to client with no allocation + no grant | 403 / redirect to search |
| Approver tries to approve their own request | Service rejects; 422 error |
| Phase C: PR (role 12) opens unallocated client | Denied; must request access |
| Phase C: PR (role 12) opens unallocated lead | Denied; same rule as clients |

---

## 13. Open items — all resolved ✅

| # | Item | Decision |
|---|------|---------|
| 1 | `staff.team` field | `teams` table already exists with 16 named rows + colours. Use FK. No new table needed. |
| 2 | Search: locked vs hidden | **Show locked + Request Access** |
| 3 | Lead restriction post-Phase C | **Everyone restricted** (PR role 12 included) |
| 4 | Calling Team grant path | **Quick access only**; supervisor path hard-blocked for `user_roles.id = 14` |
| 5 | Exempt logging granularity | **Once per calendar day** per staff + record combo |
| 6 | Notifications | **Existing broadcast system** only (no email v1) |

**No open items remain. Plan is implementation-ready.**

---

## 14. Reference

### Approver `staff.id` values (Postgres-confirmed)

| `staff.id` | Name in DB | Email |
|-----------|-----------|-------|
| 36834 | Ajay Bansal | ajay@bansalimmigration.com.au |
| 36524 | ARUN BANSAL | Immi@bansalimmigration.com.au |
| 36692 | Celesty Parmar | Manager@bansalimmigration.com.au |
| 36483 | Bipan Chander | bansalimmigration123@gmail.com |
| 36484 | KHUSHI Sangroya | khusi.bansal01@gmail.com |
| 36718 | Vipul Goyal | Immi2@bansalimmigration.com.au |
| 36523 | Sam (Shubam) | shubambansal.au1@gmail.com |
| 36836 | Yadwinder Pal Singh | migration8899@gmail.com |
| 36830 | Ankit Bansal | admin@bansaleducation.com.au |

### `user_roles` (confirmed from DB)

| Role ID | Name |
|---------|------|
| 1 | Super Admin |
| 12 | Person Responsible |
| 13 | Person Assisting |
| 14 | Calling Team |
| 15 | Accountant |
| 16 | Migration Agent |
| 17 | Admin |

### Key files to touch

| File | Change |
|------|--------|
| `app/Support/StaffClientVisibility.php` | Add `isExemptFromAllocation`; extend `canAccessClientOrLead` and list queries |
| `app/Models/Staff.php` | Cast `quick_access_enabled` as boolean |
| `config/crm_access.php` | New file |
| `app/Services/CrmAccess/CrmAccessService.php` | New service class |
| `app/Models/ClientAccessGrant.php` | New Eloquent model |
| `app/Http/Controllers/CRM/AccessGrantController.php` | New controller |
| `resources/views/crm/` | Modal, dashboard tab, popup changes |

---

*Document version: 4.0 — all decisions locked; `teams` table confirmed; implementation-ready.*
