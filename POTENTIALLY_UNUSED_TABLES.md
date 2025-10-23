# Potentially Unused/Obsolete Database Tables

This document lists tables in the `migration_manager_crm` database that may no longer be needed and could be candidates for deletion.

## Analysis Date
**Last Updated:** October 23, 2025
**Previous Analysis:** October 22, 2025

## Progress Summary
- **Previous Total:** 205 tables
- **Current Total:** 119 tables
- **Tables Deleted:** 86 tables ✅
- **Remaining Candidates for Deletion:** 8 tables

## Current Database Tables: 119 (86 tables deleted since last analysis ✅)

---

## REMAINING DELETION CANDIDATES

After reviewing the codebase and checking for actual usage, the following tables can still be safely deleted:

### ✅ SAFE TO DELETE (No Model, Not Referenced in Code)

1. **`api_tokens`** 
   - No ApiToken model
   - Replaced by `personal_access_tokens` (Laravel Sanctum)
   - Not referenced anywhere in the code
   
2. **`client_married_details`**
   - No ClientMarriedDetail model
   - Not referenced in any controller or service
   - Appears to be obsolete feature

3. **`client_ratings`**
   - No ClientRating model
   - Not referenced anywhere in the code
   - Review/rating system not implemented

4. **`email_uploads`**
   - No EmailUpload model
   - Not referenced anywhere in the code
   - Likely replaced by email_attachments

5. **`service_accounts`**
   - No ServiceAccount model
   - Not referenced anywhere in the code
   - Purpose unclear, appears obsolete

6. **`tbl_paid_appointment_payment`**
   - Old naming convention (tbl_ prefix)
   - No model, not referenced anywhere
   - Likely replaced by newer invoice/payment system

7. **`theme_options`**
   - No ThemeOption model
   - Not referenced in code, views, or routes
   - Theme settings likely moved to website_settings

8. **`verified_numbers`**
   - No VerifiedNumber model
   - Not referenced anywhere in the code
   - Phone verification likely handled differently now

---

## ⚠️ TABLES TO KEEP (Currently in Use)

These tables were flagged previously but are **ACTIVELY USED** and should **NOT** be deleted:

1. **`account_all_invoice_receipts`** ✅ IN USE
   - Heavily used in ClientsController for invoice management
   - Contains all invoice line items and receipt details
   - Critical for accounting system

2. **`account_client_receipts`** ✅ IN USE
   - Has AccountClientReceipt model
   - Used for client receipt tracking

3. **`currencies`** ✅ IN USE
   - Referenced in AdminController
   - Used for multi-currency support

4. **`message_recipients`** ✅ IN USE
   - Heavily used in ClientPortalMessageController
   - Critical for internal messaging system
   - Tracks message read/unread status

5. **`client_occupation_lists`** ✅ IN USE
   - Used in ClientPersonalDetailsController
   - Powers occupation search functionality
   - Contains ANZSCO occupation data

---

## OAUTH TABLES (Keep - Laravel Passport)
✅ Currently in use by OAuth system:
- `oauth_access_tokens`
- `oauth_auth_codes`
- `oauth_clients`
- `oauth_personal_access_clients`
- `oauth_refresh_tokens`

---

## SYSTEM TABLES (DO NOT DELETE)
✅ Required by Laravel framework:
- `cache`
- `cache_locks`
- `failed_jobs`
- `jobs`
- `migrations`
- `password_resets`
- `password_reset_links`
- `personal_access_tokens`
- `sessions`

---

## DELETION RECOMMENDATION

**Immediate Safe Deletion (8 tables):**
```sql
-- Backup first!
DROP TABLE IF EXISTS `api_tokens`;
DROP TABLE IF EXISTS `client_married_details`;
DROP TABLE IF EXISTS `client_ratings`;
DROP TABLE IF EXISTS `email_uploads`;
DROP TABLE IF EXISTS `service_accounts`;
DROP TABLE IF EXISTS `tbl_paid_appointment_payment`;
DROP TABLE IF EXISTS `theme_options`;
DROP TABLE IF EXISTS `verified_numbers`;
```

**Steps:**
1. ✅ Backup database before deletion
2. ✅ Run the DROP TABLE commands
3. ✅ Test application thoroughly
4. ✅ Monitor logs for any errors
5. ✅ Keep backup for 30 days

**Expected Result:**
- Database will have **111 tables** (down from 119)
- All remaining tables are either in active use or system tables
- Cleaner database schema with only functional tables

---

## SUMMARY

| Metric | Before (Oct 22) | After Cleanup | Final (After 8 more) |
|--------|----------------|---------------|---------------------|
| Total Tables | 205 | 119 | 111 |
| Tables Deleted | 0 | 86 | 94 |
| Deletion Candidates | 80-100 | 8 | 0 |
| Active Tables | ~120 | 111 | 111 |
| Progress | 0% | 42% | 46% |

**Database Cleanup Status:** 🎯 Almost Complete! Only 8 obsolete tables remaining.

