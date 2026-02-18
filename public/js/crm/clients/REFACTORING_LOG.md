# detail-main.js Refactoring Log

This document tracks refactoring changes to `detail-main.js` for maintainability and troubleshooting.

---

## Overview

| Date | Phase | Status | Lines (approx) |
|------|-------|--------|----------------|
| Feb 2025 | Phase 1: Preparation | ✅ Complete | ~17,374 |
| Feb 2025 | Phase 2: Extract Utilities | ✅ Complete | ~17,190 |
| Feb 2025 | Phase 3a: references + send-to-client | ✅ Complete | ~16,457 |
| Feb 2025 | Phase 3b: notes module | ✅ Complete | ~16,200 |

---

## Phase 1: Preparation (Completed)

### 1. URL Constants Extracted to ClientDetailConfig

**Hardcoded URLs were replaced with config references.**

| Old (Hardcoded) | New (Config) |
|-----------------|--------------|
| `'/documents/update-personal-category'` | `window.ClientDetailConfig.urls.updatePersonalCategory` |
| `'/documents/update-visa-category'` | `window.ClientDetailConfig.urls.updateVisaCategory` |
| `'/documents/delete-personal-category'` | `window.ClientDetailConfig.urls.deletePersonalCategory` |
| `'/clients/send-invoice-to-client/' + id` | `window.ClientDetailConfig.urls.sendInvoiceToClient + '/' + id` |
| `'/clients/send-client-fund-receipt-to-client/' + id` | `window.ClientDetailConfig.urls.sendClientFundReceiptToClient + '/' + id` |
| `'/clients/send-office-receipt-to-client/' + id` | `window.ClientDetailConfig.urls.sendOfficeReceiptToClient + '/' + id` |

**Blade views updated:** `resources/views/crm/clients/detail.blade.php`, `resources/views/crm/companies/detail.blade.php`  
**Config keys added:** `updatePersonalCategory`, `updateVisaCategory`, `deletePersonalCategory`, `sendInvoiceToClient`, `sendClientFundReceiptToClient`, `sendOfficeReceiptToClient`

---

### 2. Dead Code Removed

| Removed | Location (approx) | Reason |
|---------|-------------------|--------|
| Commented `delete-personal-cat-title` handler | ~lines 314–352 | Unused, route removed |
| Commented `opentaskmodal` handler | ~lines 8799–8813 | Task table/model no longer exists |
| Vanilla JS duplicate download handler | ~lines 861–1045 | Duplicate of jQuery handler |
| Duplicate `download-file` direct binding | ~lines 16026–16180 | Caused duplicate downloads |
| Commented `opentaskview` handler | ~lines 9948–9962 | Task system deprecated |

---

### 3. Debug Logs Removed

- Removed `console.log()` calls (activity feed, ledger drag/drop, office receipt drag/drop, personal docs, etc.)
- **Kept:** `console.error()` for real errors
- **Kept:** `console.warn()` for Flatpickr library checks

---

### 4. document.ready Blocks Flattened

- Removed nested `$(document).ready` around Sidebar Tabs init
- Removed nested `$(document).ready` around preview container styles
- **Note:** Multiple top-level `$(document).ready` blocks remain; full merge deferred

---

## Files Modified (Phase 1)

```
resources/views/crm/clients/detail.blade.php      # Added URL config entries
resources/views/crm/companies/detail.blade.php     # Added URL config entries
public/js/crm/clients/detail-main.js              # All Phase 1 changes
```

---

## Dependencies

- **jQuery** (`$`)
- **ClientDetailConfig** – must be defined before `detail-main.js` loads (set in Blade views)
- **Flatpickr**, **TinyMCE**, **Select2**, **iziToast**, **Swal**, **DataTables**

---

## Troubleshooting

### "ClientDetailConfig.urls.updatePersonalCategory is undefined"

- Ensure `ClientDetailConfig` is defined in the Blade view before `detail-main.js` loads
- Check `resources/views/crm/clients/detail.blade.php` (and `companies/detail.blade.php`) for the `window.ClientDetailConfig = { ... urls: { ... } }` block

### Document category update / Send-to-client not working

- Verify routes exist in `routes/clients.php`:
  - `clients.documents.updatePersonalDocCategory`
  - `clients.documents.updateVisaDocCategory`
  - `clients.documents.deletePersonalDocCategory`
  - Send-to-client endpoints: `sendInvoiceToClient`, `sendClientFundReceiptToClient`, `sendOfficeReceiptToClient`

### Download file not working

- Primary handler: `$(document).on('click', '.download-file', ...)`
- Uses `window.ClientDetailConfig.urls.downloadDocument`
- If CSRF token is missing, check `<meta name="csrf-token">` in the page

### Activity feed height issues

- `adjustActivityFeedHeight()` runs on load and window resize
- Requires `.activity-feed`, `.main-content`, `.crm-container` in the DOM

---

## Phase 2: Extract Shared Utilities (Completed)

### New Files Created

| File | Functions | Dependencies |
|------|-----------|--------------|
| `utils/flatpickr-helpers.js` | `initFlatpickrForClass`, `initFlatpickrWithAjax` | jQuery, Flatpickr |
| `utils/editor-helpers.js` | `getEditorContent`, `setEditorContent`, `clearEditor`, `isEditorInitialized` | jQuery, TinyMCE (optional) |
| `utils/dom-helpers.js` | `adjustActivityFeedHeight`, `adjustPreviewContainers`, `downloadFile` | jQuery |

### Script Load Order (Blade views)

```html
<!-- Must load BEFORE detail-main.js -->
<script src=".../utils/flatpickr-helpers.js"></script>
<script src=".../utils/editor-helpers.js"></script>
<script src=".../utils/dom-helpers.js"></script>
<script src=".../detail-main.js"></script>
```

### Consumers

- **detail-main.js** – uses all helpers
- **activity-feed.js** – calls `adjustActivityFeedHeight()`
- **sidebar-tabs.js** – calls `adjustActivityFeedHeight()`

### Files Modified (Phase 2)

```
public/js/crm/clients/utils/flatpickr-helpers.js   # NEW
public/js/crm/clients/utils/editor-helpers.js      # NEW
public/js/crm/clients/utils/dom-helpers.js         # NEW
public/js/crm/clients/detail-main.js               # Removed ~230 lines of helper code
resources/views/crm/clients/detail.blade.php       # Added util script tags
resources/views/crm/companies/detail.blade.php     # Added util script tags
```

### Troubleshooting (Phase 2)

**"initFlatpickrForClass is not a function"**  
- Ensure `flatpickr-helpers.js` loads before `detail-main.js`  
- Check script order in Blade view

**"adjustActivityFeedHeight is not a function"**  
- Ensure `dom-helpers.js` loads before `detail-main.js`  
- activity-feed.js and sidebar-tabs.js call this when tabs switch (after full load, so it should exist)

**jQuery is null in utils**  
- Utils require jQuery; they load after layout scripts. If utils run before jQuery, functions won't be attached.

---

## Phase 3a: Extract references + send-to-client (Completed)

### New Files Created

| File | Purpose | Dependencies |
|------|---------|--------------|
| `modules/references.js` | Sidebar reference chips UI (edit, delete, save) | jQuery, ClientDetailConfig, iziToast |
| `modules/send-to-client.js` | Send invoice/receipt to client via email | jQuery, ClientDetailConfig, Swal |

### Script Load Order (updated)

```html
<script src=".../utils/flatpickr-helpers.js"></script>
<script src=".../utils/editor-helpers.js"></script>
<script src=".../utils/dom-helpers.js"></script>
<script src=".../modules/references.js"></script>
<script src=".../modules/send-to-client.js"></script>
<script src=".../detail-main.js"></script>
```

### Integration

- **references.js** – Listens for `.saveReferenceValue` click (from detail-main.js) to re-render chips
- **send-to-client.js** – Standalone; attaches to `.send-invoice-to-client`, `.send-client-fund-receipt-to-client`, `.send-office-receipt-to-client`

### Files Modified (Phase 3a)

```
public/js/crm/clients/modules/references.js      # NEW (~280 lines)
public/js/crm/clients/modules/send-to-client.js  # NEW (~270 lines)
public/js/crm/clients/detail-main.js            # Removed ~660 lines
resources/views/crm/clients/detail.blade.php     # Added module script tags
resources/views/crm/companies/detail.blade.php   # Added module script tags
```

### Troubleshooting (Phase 3a)

**References chips not showing**  
- Ensure `#references-container` exists in DOM  
- Check `modules/references.js` loads before `detail-main.js`

**Send to client buttons not working**  
- Ensure `ClientDetailConfig.urls.sendInvoiceToClient` (etc.) exist  
- Check `modules/send-to-client.js` loads

---

## Phase 3b: Extract Notes Module (Completed)

### New File Created

| File | Purpose | Dependencies |
|------|---------|--------------|
| `modules/notes.js` | Notes CRUD, getallnotes, Select2 format helpers | jQuery, ClientDetailConfig, editor-helpers, dom-helpers |

### Extracted Logic

- `getallnotes()` – fetches notes, filters by matter and task group, exposes on `window.getallnotes`
- `formatRepo`, `formatRepoSelection` – Select2 template helpers, exposed on `window` for use by `.js-data-example-ajaxccapp`, `.js-data-example-ajaxcontact` in detail-main.js
- Create note: `.create_note_d`, `.create_note` delegates
- Edit note: `.opennoteform` click handler
- View note: `.viewnote`, `.viewapplicationnote` delegates
- `.js-data-example-ajaxcc` Select2 (recipients in create note modal)

### Config Keys Used

- `ClientDetailConfig.urls.getNotes`
- `ClientDetailConfig.urls.getRecipients`
- `ClientDetailConfig.urls.getNoteDetail`
- `ClientDetailConfig.urls.viewNoteDetail`
- `ClientDetailConfig.urls.viewApplicationNote`

### Script Load Order (updated)

```html
<script src=".../modules/references.js"></script>
<script src=".../modules/send-to-client.js"></script>
<script src=".../modules/notes.js"></script>
<script src=".../detail-main.js"></script>
```

### Troubleshooting (Phase 3b)

**getallnotes is not a function**  
- Ensure `modules/notes.js` loads before `detail-main.js`  
- Notes module exposes `window.getallnotes`

**formatRepo / formatRepoSelection undefined**  
- Notes module exposes both on `window`; `.js-data-example-ajaxccapp` and `.js-data-example-ajaxcontact` (in detail-main.js) rely on them

---

## Planned Next Steps

| Phase | Description |
|-------|-------------|
| Phase 3c | Extract checklist, documents, accounts, invoices |
| Phase 4 | Module architecture (IIFE or ES modules) |

---

*Last updated: Feb 2025*
