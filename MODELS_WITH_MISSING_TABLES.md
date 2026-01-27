# Model-Table Mismatch Report

This document lists models without tables and tables without models.

**Generated:** January 27, 2026  
**Total Models Checked:** 85  
**Total Tables in Database:** 81  

## Models with Missing Tables

**Count:** 15

| # | Model | Expected Table |
|---|-------|----------------|
| 1 | **AuditLog** | `audit_logs` |
| 2 | **Course** | `courses` |
| 3 | **EmailTemplate** | `email_templates` |
| 4 | **FileStatus** | `file_statuses` |
| 5 | **Group** | `groups` |
| 6 | **Invoice** | `invoices` |
| 7 | **InvoiceFollowup** | `invoice_followups` |
| 8 | **OfficeVisit** | `office_visits` |
| 9 | **OnlineForm** | `online_forms` |
| 10 | **ShareInvoice** | `share_invoices` |
| 11 | **State** | `states` |
| 12 | **User** | `users` |
| 13 | **VerifyUser** | `verify_users` |
| 14 | **VisaDocChecklist** | `visa_doc_checklists` |
| 15 | **WebsiteSetting** | `website_settings` |

## Tables without Models

**Count:** 3 (2 models created, 1 pivot table)

| # | Table Name | Status |
|---|------------|--------|
| 1 | `account_all_invoice_receipts` | ✅ **Model Created** - `AccountAllInvoiceReceipt` |
| 2 | `message_recipients` | ✅ **Model Created** - `MessageRecipient` |
| 3 | `client_occupation_lists` | ⚪ Optional - Can stay as `DB::table()` |
| 4 | `email_label_mail_report` | ✅ Pivot table (no model needed) |

---

## Actions Completed

1. ✅ Created `AccountAllInvoiceReceipt` model
2. ✅ Created `MessageRecipient` model  
3. ✅ Updated 4 controller files
4. ✅ Updated 1 job file
5. ✅ Replaced 74 `DB::table()` calls with model usage

**See `MODEL_CREATION_SUMMARY.md` for full details**
