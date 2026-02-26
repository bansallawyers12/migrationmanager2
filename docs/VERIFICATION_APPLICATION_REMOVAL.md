# Deep Verification: Application → Client Portal / Matter Refactor

**Date:** 2026-02-26  
**Scope:** Removal of backward compatibility, UI labels, technical identifiers (sections 1–3)

---

## 1. Routes & Controllers ✓

| Route | Method | Controller Method | Status |
|-------|--------|-------------------|--------|
| POST /client-portal/load-matter-upsert | loadMatterUpsert | ✓ |
| GET /client-portal/detail | getClientPortalDetail | ✓ Added (was 404) |
| GET /client-portal/logs | getMatterLogs | ✓ |
| GET /client-portal/list | getapplications | ✓ |
| POST /client-portal/discontinue | discontinueMatter | ✓ |
| POST /client-portal/revert | revertMatter | ✓ |
| GET /client-portal/notes | getMatterNotes | ✓ |
| POST /client-portal/sendmail | clientPortalSendmail | ✓ |
| GET /client-portal/updateintake | updateintake | ✓ |
| GET /client-portal/updatedates | updatedates | ✓ |
| GET /client-portal/updateexpectwin | updateexpectwin | ✓ |
| POST /client-portal/ownership | application_ownership | ✓ |
| POST /client-portal/checklistupload | checklistupload | ✓ |
| GET /client-portal/delete-docs | deleteClientPortalDocs | ✓ |
| GET /client-portal/publishdoc | publishdoc | ✓ |
| POST /client-portal/approve-document | approveDocument | ✓ |
| POST /client-portal/reject-document | rejectDocument | ✓ |
| GET /client-portal/download-document | downloadDocument | ✓ |

---

## 2. ClientDetailConfig URLs ✓

All URLs in `detail.blade.php` and `companies/detail.blade.php` use `/client-portal/*` paths. Keys: `loadMatterUpsert`, `getClientPortalDetail`, `getMatterNotes`, `deleteClientPortalDoc`, `checklistUpload`, `publishDoc`, `updateIntake`, `updateExpectWin`, `updateDates`.

---

## 3. Form & Modal Consistency ✓

| Form/Modal | ID/Name | Action URL | Status |
|------------|---------|------------|--------|
| Discontinue | discontinue_matter | /client-portal/discontinue | ✓ |
| Revert | revert_matter (modal), revertapplication (form) | /client-portal/revert | ✓ |
| Application send mail | appkicationsendmail | /client-portal/sendmail | ✓ |
| Tags form | stags_matter | /save_tag | ✓ |

---

## 4. Delete Flow (data-href) ✓

- `deleteclientportaldocs` → uses `urls.deleteClientPortalDoc` (/client-portal/delete-docs)
- Response key `client_portal_upload_count` used in JS ✓

---

## 5. Tab & Navigation ✓

- Tab pane: `#client_portal-tab`
- Sidebar button: `data-tab="client_portal"`
- Sidebar handler: `case 'client_portal'` → `showClientMatterPortalData`
- Valid tab names include `'client_portal'`

---

## 6. CSS Classes ✓

All `.application-tab-*` / `.application-tabs-*` renamed to `.client-portal-tab-*` / `.client-portal-tabs-*`.

---

## 7. Known / Minor Items

1. **obj.application_id** (custom-form-validation.js, payment schedules): Form/response may still use `application_id`; fallback `res.client_matter_id || res.application_id` kept in detail-main.js for delete-payment-schedule flow.

2. **documents.blade.php**: Keeps `.application_id` as fallback; `.client_matter_id` is primary. Checklist.js sends `client_matter_id` and falls back to `application_id` if needed.

3. **payment-schedules.blade.php**: Hidden input `application_id` / `app_id` retained for form compatibility; backend may expect it.

4. **StoreClientRequest**: `application_id` in validation rules kept for legacy form support.

5. **ClientPortalWorkflowController API**: Returns `client_matter_id` only (no `application_id`).

---

## 8. Fix Applied During Verification

- **getClientPortalDetail 404**: Route and method were missing. Added:
  - Route: `GET /client-portal/detail`
  - Method: `ClientPortalController::getClientPortalDetail()`
  - Renders client portal tab HTML for matter-change AJAX load
  - Config updated to use `/client-portal/detail`

---

## 9. Recommended Manual Tests

1. **Client Portal tab**: Change matter dropdown → click Client Portal → tab loads for selected matter
2. **Discontinue / Revert**: Discontinue matter → revert matter → activities accordion refreshes
3. **Client portal documents**: Upload checklist doc → delete doc → upload count updates
4. **Send to Client Portal**: From accounts, send invoice to client portal
5. **Application send mail**: Compose and send application email
6. **Checklist upload**: Add checklist → upload document for matter
