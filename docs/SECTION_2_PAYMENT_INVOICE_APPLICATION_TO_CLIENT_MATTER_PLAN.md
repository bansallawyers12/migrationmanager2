# Section 2: Payment Schedule & Invoice Forms – application_id → client_matter_id

**Status:** Plan ready for implementation  
**Risk:** Medium – core billing/forms behavior  
**Dependencies:** None (routes may need to be added if missing)

---

## Summary of Changes

Replace `application_id` / `app_id` / `application` (when used as matter reference) with `client_matter_id` across payment schedule and invoice forms, JavaScript, and backend controllers.

---

## 1. Blade Views

### 1.1 `resources/views/crm/clients/modals/payment-schedules.blade.php`

| Line | Current | Change |
|------|---------|--------|
| 21 | `<input type="hidden" name="application_id" id="application_id" value="">` | `<input type="hidden" name="client_matter_id" id="payment_schedule_client_matter_id" value="">` |
| 157 | `<input type="hidden" name="application" id="app_id">` | `<input type="hidden" name="client_matter_id" id="create_invoice_client_matter_id">` |

**Note:** Both modals exist in the DOM simultaneously, so IDs must be unique:
- Setup form (modal #1): `id="payment_schedule_client_matter_id"`
- Create Invoice form (modal #4): `id="create_invoice_client_matter_id"`

### 1.2 `resources/views/crm/clients/modals/documents.blade.php`

| Line | Current | Change |
|------|---------|--------|
| 23 | `<input type="hidden" class="application_id" value="">` | Remove (keep only `.client_matter_id` at line 24) |

**Note:** Line 24 already has `<input type="hidden" class="client_matter_id" value="">`. Remove the `.application_id` input since checklist.js will only use `.client_matter_id`.

### 1.3 `resources/views/crm/clients/modals/financial.blade.php`

| Line | Form | Current | Change |
|------|------|---------|--------|
| 47 | noteinvform (Commission Invoice) | `<select ... name="application">` | `<select ... name="client_matter_id">` |
| 116 | notegetinvform (General Invoice) | `<select ... name="application">` | **DO NOT CHANGE** – see below |

**Important – notegetinvform (General Invoice):** The select at line 116 has options with `value="{{$workflow->id}}"` – it holds **workflow_id**, not client_matter_id. The create-invoice backend for invoice_type=3 expects this. Renaming to `client_matter_id` would be semantically wrong and could break General Invoice creation. **Exclude from this migration.**

---

## 2. JavaScript

### 2.1 `public/js/crm/clients/modules/checklist.js`

| Line | Current | Change |
|------|---------|--------|
| 45 | `formData.append("client_matter_id", $('.client_matter_id').val() \|\| $('.application_id').val());` | `formData.append("client_matter_id", $('.client_matter_id').val());` |
| 63-64 | `$(".client_matter_id").val(aid); $(".application_id").val(aid);` | `$(".client_matter_id").val(aid);` (remove `.application_id` line) |
| 74-75 | Same as above (opendocnote handler) | Same change |

### 2.2 `public/js/custom-form-validation.js`

| Line | Current | Change |
|------|---------|--------|
| 1438 | `data:{appid:obj.application_id, client_id:obj.client_id}` | `data:{appid: (obj.client_matter_id ?? obj.application_id), client_id:obj.client_id}` |
| 1502 | `data:{appid:obj.application_id, client_id:obj.client_id}` | `data:{appid: (obj.client_matter_id ?? obj.application_id), client_id:obj.client_id}` |

**Note:** Use fallback `obj.client_matter_id ?? obj.application_id` so both old (application_id) and new (client_matter_id) backend responses work during rollout. The `get-all-paymentschedules` endpoint accepts `appid` as the query param; the value is the matter ID.

### 2.3 `public/js/crm/clients/modules/invoices.js`

| Line | Current | Change |
|------|---------|--------|
| 112-114 | `$('#client_id').val(cid); $('#app_id').val(aid);` | `$('#client_id').val(cid); $('#create_invoice_client_matter_id').val(aid);` |
| 111 | `data-app-id` | Keep as `data-app-id` (or add `data-client-matter-id`) – the value is the matter ID. The trigger element passes matter ID; we just need to put it in the correct hidden input. |

**Note:** The Create Invoice modal’s hidden input will be `id="create_invoice_client_matter_id"` and `name="client_matter_id"`. Update the selector in invoices.js to match.

### 2.4 `public/js/crm/clients/detail-main.js`

- **deletepaymentschedule** (line ~6535): Already uses `appid:res.client_matter_id` – no change needed.
- **Payment Schedule Setup modal opener:** Search for code that opens `#create_apppaymentschedule` and sets `#application_id`. Update to set `#payment_schedule_client_matter_id` instead. If this logic lives in backend-rendered HTML (from get-all-paymentschedules), update that view.
- **Create Invoice modal opener:** Handled by invoices.js (`.createapplicationnewinvoice`) – see 2.3.

### 2.5 Backend-rendered HTML (get-all-paymentschedules response)

The payment schedule list is loaded via `get-all-paymentschedules`. If the returned HTML contains:
- `onclick` or `data-` attributes that open the Setup modal and pass matter ID
- Inline scripts that set `#application_id`

Update these to use `#payment_schedule_client_matter_id` and pass `client_matter_id`.

---

## 3. Backend Controllers

### 3.1 Locate Routes (Critical First Step)

**Action:** Confirm where `/setup-paymentschedule`, `/create-invoice`, and `/get-all-paymentschedules` are defined.

- **Current status:** These routes do not appear in `php artisan route:list`. They may be registered elsewhere (fallback, implicit binding, or legacy) or may be returning 404. **Verify the routes work before making frontend changes.**
- If missing, add to `routes/clients.php`:
  ```php
  Route::post('/setup-paymentschedule', 'CRM\ClientAccountsController@setupPaymentSchedule');
  Route::post('/create-invoice', 'CRM\ClientAccountsController@createInvoice');
  Route::get('/get-all-paymentschedules', 'CRM\ClientAccountsController@getAllPaymentSchedules');
  ```
- Controller method names may differ (e.g. `setuppaymentschedule`, `createinvoice`). Search the controller for the actual method names.

### 3.2 setup-paymentschedule Handler

- Accept `client_matter_id` from request (with fallback to `application_id` for backward compatibility during rollout).
- Use `$request->client_matter_id ?? $request->application_id` when resolving the matter.
- In JSON response, return `client_matter_id` (and optionally `application_id` for legacy JS during transition).

### 3.3 create-invoice Handler

- Accept `client_matter_id` (fallback to `application` or `application_id` if present).
- `ClientAccountsController` already uses `client_matter_id` in many places (e.g. lines 3095, 3120, 3184, etc.).
- Ensure create-invoice flow uses `$request->client_matter_id ?? $request->application ?? $request->application_id`.

### 3.4 get-all-paymentschedules Handler

- Accept `client_matter_id` or `appid` as the matter identifier.
- Use `$request->client_matter_id ?? $request->appid` when filtering schedules.
- Response does not need to change if it doesn’t include matter ID.

### 3.5 Edit/Add Payment Schedule Responses

For `editinvpaymentschedule` and `addinvpaymentschedule` success responses, ensure the JSON includes:

```json
{
  "status": true,
  "message": "...",
  "client_matter_id": 123,
  "client_id": 456
}
```

So that `custom-form-validation.js` can use `obj.client_matter_id` for the `get-all-paymentschedules` reload.

---

## 4. Execution Order

**Pre-flight:** Run `php artisan route:list` and search for setup-paymentschedule, create-invoice, get-all-paymentschedules. If not found, trace where the app posts these requests and whether they succeed (check Network tab when using the features).

1. **Backend first**  
   - Find or add routes for setup-paymentschedule, create-invoice, get-all-paymentschedules.  
   - Update handlers to accept `client_matter_id` (with `application_id`/`application` fallback).  
   - Ensure edit/add payment schedule responses include `client_matter_id`.

2. **Blade views**  
   - payment-schedules.blade.php  
   - documents.blade.php (remove `.application_id`)  
   - financial.blade.php: only noteinvform (Commission Invoice) – change Matter select `name` to `client_matter_id`; **do not change notegetinvform** (General Invoice uses workflow_id)

3. **JavaScript**  
   - checklist.js  
   - custom-form-validation.js  
   - invoices.js  
   - detail-main.js (modal openers and response handling)

4. **Verification**  
   - Payment Schedule Setup: create schedule for a matter  
   - Create Invoice (from schedule): Net Claim, Gross Claim, Client General via opencreateinvoiceform  
   - Commission Invoice (noteinvform): create from financial modal after change  
   - General Invoice (notegetinvform): confirm still works (no change to this form)  
   - Edit/Add Payment Schedule: verify list reloads correctly  
   - Checklist upload: verify document upload with matter context

---

## 5. Files Checklist

| File | Changes |
|------|---------|
| `resources/views/crm/clients/modals/payment-schedules.blade.php` | application_id → client_matter_id (x2); app_id → create_invoice_client_matter_id |
| `resources/views/crm/clients/modals/documents.blade.php` | Remove `.application_id` input |
| `resources/views/crm/clients/modals/financial.blade.php` | name="application" → name="client_matter_id" for **noteinvform only** (exclude notegetinvform – uses workflow_id) |
| `public/js/crm/clients/modules/checklist.js` | Use only .client_matter_id; remove .application_id |
| `public/js/custom-form-validation.js` | Use obj.client_matter_id ?? obj.application_id (lines 1438, 1502) |
| `public/js/crm/clients/modules/invoices.js` | #app_id → #create_invoice_client_matter_id |
| `public/js/crm/clients/detail-main.js` | Update modal openers, response handling (client_matter_id) |
| Backend (ClientAccountsController or equivalent) | Accept client_matter_id; return it in responses |
| Routes | Add setup-paymentschedule, create-invoice, get-all-paymentschedules if missing |

---

## 6. Known Gaps / Out of Scope

| Item | Reason |
|------|--------|
| **notegetinvform `name="application"`** | Holds workflow_id, not client_matter_id. General Invoice (invoice_type=3) uses workflow for service selection. |
| **Route discovery** | setup-paymentschedule, create-invoice, get-all-paymentschedules not in route:list – may use implicit routing or need to be added. |
| **Backend-rendered HTML** | get-all-paymentschedules response may contain HTML that references #application_id when opening Setup modal – requires backend view inspection. |

---

## 7. Risk Mitigation

- Use `$request->client_matter_id ?? $request->application_id ?? $request->application` in backend during transition.
- In JS, use `obj.client_matter_id ?? obj.application_id` in custom-form-validation.js until all backend responses are updated.
- Test on staging before production.
- After migration is stable, remove fallbacks in a follow-up change.

---

## 8. Post-Implementation

- Run `php artisan view:clear`
- Test: Payment Schedule Setup, Create Invoice, Edit/Add Schedule, Checklist upload
- Check browser console for 404s or validation errors
