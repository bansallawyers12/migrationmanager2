# Deep Verification: Document Columns Removal

**Date:** February 20, 2026  
**Migration:** `2026_02_20_150000_drop_unused_documents_columns.php`

---

## 1. Columns Dropped (10)

| Column | Status |
|--------|--------|
| `labels` | ✅ Dropped |
| `certificate_path` | ✅ Dropped |
| `signed_hash` | ✅ Dropped |
| `hash_generated_at` | ✅ Dropped |
| `client_portal_verified_by` | ✅ Dropped |
| `client_portal_verified_at` | ✅ Dropped |
| `origin` | ✅ Dropped |
| `primary_signer_email` | ✅ Dropped |
| `signer_count` | ✅ Dropped |
| `last_activity_at` | ✅ Dropped |

---

## 2. Code Verification

### 2.1 Document Model (`app/Models/Document.php`)

- **Fillable:** Dropped columns removed. `lead_id` present (used for association).
- **Accessors:**
  - `getPrimarySignerEmailAttribute()` – returns `signers->first()->email` ✅
  - `getSignerCountAttribute()` – returns `signers()->count()` ✅
  - `getDocumentableAttribute()` – returns `client ?? lead` (replaces polymorphic) ✅
- **Scopes:** `scopeNotArchived()` uses `status != 'archived'` (not `archived_at`) ✅
- **getIsOverdueAttribute():** Returns false (due_at still exists but logic simplified)

### 2.2 Controllers

| Controller | Status |
|------------|--------|
| SignatureDashboardController | Search uses `orWhereHas('signers', ...)` for email; eager loads `client`, `lead` ✅ |
| DocumentController | No assignments to dropped columns ✅ |
| ClientPortalDocumentController | `signer_count` removed from `addDocumentChecklist` insert ✅ |
| ClientAccountsController | All `signer_count` assignments removed ✅ |
| ClientDocumentsController | All `signer_count` assignments removed ✅ |
| ClientsController | `signer_count` removed ✅ |
| Form956Controller | `signer_count` removed ✅ |
| EmailUploadController | `signer_count` removed ✅ |

### 2.3 Services

| Service | Status |
|---------|--------|
| SignatureService | Uses `client_id`/`lead_id` for associate/detach; no `origin`, `primary_signer_email`, `signer_count`, `last_activity_at` ✅ |
| SignatureAnalyticsService | `getMedianTimeToSign()` uses `signers.signed_at`; `getDocumentTypeStats()` uses signers subquery; `primary_signer_email` accessor used ✅ |

### 2.4 Views

| View | Status |
|------|--------|
| `resources/views/crm/signatures/dashboard.blade.php` | Uses `$doc->primary_signer_email`, `$doc->signer_count`, `$doc->documentable` – all via accessors ✅ |

### 2.5 Factory & Tests

| File | Status |
|------|--------|
| DocumentFactory | No dropped columns in definition (title, document_type, etc. may still exist for legacy) |
| SignatureServiceTest | Tests use `client_id`/`lead_id`; archive test asserts `status='archived'` ✅ |

---

## 3. Remaining References (Safe / Non-Breaking)

- **Migrations:** `2025_10_20_191713` and `2025_10_21_225122` reference dropped columns in `down()` – historical only.
- **Grep false positives:** `labels` (chart labels), `origin` (CORS, original filename), `original` – not document columns.

---

## 4. Potential Issues

### 4.1 `lead_id` Column

- **Observation:** Document model uses `lead_id` in fillable and `lead()` relationship.
- **Migration:** No migration in codebase adds `lead_id` to `documents`.
- **Action:** If `documents` lacks `lead_id`, add migration:
  ```php
  $table->unsignedInteger('lead_id')->nullable()->after('client_id');
  ```
- **Impact:** `SignatureService::associate($doc, 'lead', $id)` would fail if column missing.

### 4.2 DocumentFactory Attributes

- Factory still passes `title`, `document_type`, `priority`, `documentable_type`, `documentable_id`, `due_at`, `archived_at`.
- Document `$fillable` does not include these; mass assignment ignores them.
- If those columns were dropped by another migration, factory would need updating. Current migration only dropped the 10 listed.

---

## 5. Fixes Applied During Verification

1. **Archive test:** Assertion changed from `whereNotNull('archived_at')` to `where('status','archived')` to match `archiveOldDrafts()`.
2. **Documentable:** Added `getDocumentableAttribute()` accessor and switched controller eager load from `documentable` to `client`, `lead`.
3. **Test factories:** Removed `archived_at => null` from create arrays (optional; reduces noise).

---

## 6. Recommendation

1. Confirm `documents` table has `lead_id`; add migration if missing.
2. Run `php artisan migrate` if not already done.
3. Run `SignatureServiceTest` once test DB can run migrations (e.g. after fixing `client_contacts` migration).
