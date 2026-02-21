# Deep Verification: Documents Column Removal

**Date:** 2026-02-20  
**Migration:** `2026_02_20_150000_drop_unused_documents_columns.php`

---

## 1. Schema Verification

### Columns Removed
| Column | Status |
|--------|--------|
| documentable_type | ✅ Dropped |
| documentable_id | ✅ Dropped |
| title | ✅ Dropped |
| document_type | ✅ Dropped |
| due_at | ✅ Dropped |
| priority | ✅ Dropped |
| archived_at | ✅ Dropped |
| checklist_verified_by | ✅ Dropped |
| checklist_verified_at | ✅ Dropped |

### Column Added
| Column | Purpose |
|--------|---------|
| lead_id | Replaces polymorphic link to Lead (client_id already existed for Admin) |

### Data Migration Applied
- `documentable_type=Lead` → `lead_id = documentable_id`
- `documentable_type=Admin` (where client_id null) → `client_id = documentable_id`
- `archived_at` not null → `status = 'archived'`

---

## 2. Code Verification

### Document Model
- ✅ `fillable`: Removed all dropped columns; added `lead_id`
- ✅ `casts`: Removed checklist_verified_at, due_at, archived_at
- ✅ `sortable`: Removed title, document_type, priority
- ✅ `documentable()` morphTo removed; `lead()` belongsTo added
- ✅ `verifiedBy()` removed
- ✅ `scopeAssociated()`: Uses client_id/lead_id
- ✅ `scopeAdhoc()`: Uses client_id/lead_id
- ✅ `scopeNotArchived()`: Uses status != 'archived'
- ✅ `getDisplayTitleAttribute()`: Returns file_name
- ✅ `getIsOverdueAttribute()`: Returns false
- ✅ `getDocumentableAttribute()`: Accessor for backward compat (returns client or lead)

### Controllers Updated
| Controller | Changes |
|------------|---------|
| SignatureService | associate/detach use client_id/lead_id; archive sets status |
| SignatureDashboardController | client_id/lead_id; removed title from create; search uses file_name only |
| DocumentController | file_name instead of title |
| PublicDocumentController | client_id/lead_id for associations |
| ESignatureController | client/lead eager load; default values for removed fields |
| ClientDocumentsController | verifiedBy removed; Verified_By/At = "N/A" |
| SignatureAnalyticsService | document_type, due_at, archived_at references removed |

### Views Updated
| View | Changes |
|------|---------|
| signatures/dashboard | client/lead; association chip |
| signatures/show | client/lead association block |
| signatures/audit_report | client/lead; type/priority/due sections |
| documents/sign, edit | file_name instead of title |
| documents/error | display_title |

---

## 3. Cross-Reference Check

### document_type (Different Context)
- **ClientPortalController / ClientPortalWorkflowController**: `document_type` on `application_document_lists` table ✅ (not documents)
- **ClientPortalDocumentController**: `document_type` in API response = 'personal'/'visa' (doc_type) ✅

### title (Different Context)
- All remaining `->title` references are on: Matter, VisaDocumentType, PersonalDocumentType, Note, Template, etc. ✅ (not Document)

### priority (Different Context)
- FCMService, ClientsController, EmailUploadController: different models ✅

---

## 4. Edge Cases Addressed

1. **Dashboard search**: Was `where('title', 'like'...)` → **Fixed** to file_name only
2. **documentable in views**: getDocumentableAttribute() provides compat; dashboard updated to client/lead for clarity
3. **Eager loading**: SignatureDashboardController loads `client` and `lead` for association display

---

## 5. Test Status

- **RefreshDatabase** fails due to pre-existing `client_contacts` migration (table missing)
- Document-specific refactors verified via code review
- Run `php artisan migrate --path=database/migrations/2026_02_20_150000_drop_unused_documents_columns.php` on target DB

---

## 6. Manual Verification Checklist

- [ ] Signature dashboard loads and shows associations correctly
- [ ] Document show page displays client/lead link
- [ ] Create new signature document (no title field)
- [ ] Search on signature dashboard (uses file_name)
- [ ] Archive documents (sets status)
- [ ] Client documents tab: Verified By/At show "N/A"
- [ ] Attach/detach document to client or lead
