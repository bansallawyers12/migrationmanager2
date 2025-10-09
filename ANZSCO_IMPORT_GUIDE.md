# ANZSCO Import Guide - Handling Overlapping Lists

## 📋 Overview

This guide explains how to import ANZSCO occupation data from multiple CSV files where occupations can appear on multiple lists (MLTSSL, STSOL, ROL, CSOL).

---

## 🎯 Understanding the Occupation Lists

### Current Lists (Not Legacy):

1. **MLTSSL** - Medium and Long-term Strategic Skills List
   - For permanent skilled migration visas
   - File: `anzsco_mltssl_completed.csv` (213 rows)

2. **STSOL** - Short-term Skilled Occupation List  
   - For state/territory nominated visas (190, 491)
   - File: `anzsco_stsol_completed.csv` (206 rows)

3. **ROL** - Regional Occupation List
   - For regional visa nominations (requires regional employment)
   - File: `anzsco_rol_completed.csv` (78 rows)

4. **CSOL** - Core Skills Occupation List ⚠️
   - **This is CURRENT, not legacy!**
   - For Skills in Demand visa (482) and Employer Nomination (186) Direct Entry
   - File: `anzsco_csol_completed.csv` (457 rows)

---

## ⚠️ The Overlapping Problem (NOW SOLVED)

### What Was the Problem?

Many occupations appear on **multiple lists**. For example:

**Chef (351311)**:
- ✅ On MLTSSL list
- ✅ On CSOL list

**Software Engineer (261313)**:
- ✅ On MLTSSL list
- ✅ On CSOL list

### The Issue with Old Import Logic:

Each CSV file only marks its own list as "Yes":
- In `anzsco_mltssl_completed.csv`: Chef has `mltssl=Yes, stsol=No, rol=No, csol=No`
- In `anzsco_csol_completed.csv`: Chef has `mltssl=No, stsol=No, rol=No, csol=Yes`

**Old Behavior**: If you imported MLTSSL first, then CSOL, the second import would **OVERWRITE** the first, causing Chef to lose its MLTSSL flag!

### ✅ The Solution (NOW IMPLEMENTED):

The import service now uses **MERGE logic** instead of REPLACE:

```php
// MERGE list flags instead of replacing them
// If a flag is already TRUE, keep it TRUE (don't overwrite with FALSE)
$mergedData['is_on_mltssl'] = $existing->is_on_mltssl || $mappedData['is_on_mltssl'];
$mergedData['is_on_stsol'] = $existing->is_on_stsol || $mappedData['is_on_stsol'];
$mergedData['is_on_rol'] = $existing->is_on_rol || $mappedData['is_on_rol'];
$mergedData['is_on_csol'] = $existing->is_on_csol || $mappedData['is_on_csol'];
```

**Result**: When importing multiple files, list flags are **ADDED** together, never overwritten!

---

## 📥 How to Import the CSV Files

### Step-by-Step Import Process:

1. **Navigate to Import Page**
   ```
   Admin Panel → ANZSCO Database → Import Data
   Or directly: /admin/anzsco/import
   ```

2. **Import Order** (recommended but not required anymore):
   ```
   1. anzsco_mltssl_completed.csv (213 rows)
   2. anzsco_stsol_completed.csv (206 rows)
   3. anzsco_rol_completed.csv (78 rows)
   4. anzsco_csol_completed.csv (457 rows)
   ```

3. **Import Settings**:
   - ✅ **Check** "Update existing occupations" - This is IMPORTANT!
   - This allows the merge logic to work properly

4. **Select File** and click **Import Data**

5. **Review Results**:
   - Check the statistics (Inserted, Updated, Skipped, Errors)
   - Review any errors or warnings
   - Verify that occupations appear with multiple list badges

### Expected Results:

After importing all 4 files:

| File | Action | Result |
|------|--------|--------|
| MLTSSL (1st) | Insert new occupations | ~213 inserted |
| STSOL (2nd) | Update + Insert | Some updated, some new inserted |
| ROL (3rd) | Update + Insert | Some updated, some new inserted |
| CSOL (4th) | Update + Insert | Most updated (largest file) |

**Final Total**: ~900+ unique occupations with correct list flags

---

## 🔍 Verification After Import

### Check for Overlapping Occupations:

1. Go to `/admin/anzsco`
2. Search for "Chef" or "Software Engineer"
3. Check the "Lists" column
4. You should see multiple badges:
   - Chef: **MLTSSL** (green) + **CSOL** (gray)
   - Software Engineer: **MLTSSL** (green) + **CSOL** (gray)

### SQL Verification:

```sql
-- Find occupations on multiple lists
SELECT 
    anzsco_code,
    occupation_title,
    is_on_mltssl,
    is_on_stsol,
    is_on_rol,
    is_on_csol,
    (is_on_mltssl + is_on_stsol + is_on_rol + is_on_csol) as total_lists
FROM anzsco_occupations
WHERE (is_on_mltssl + is_on_stsol + is_on_rol + is_on_csol) > 1
ORDER BY total_lists DESC;
```

---

## 🎨 Badge Color Reference

In the admin interface:

- 🟢 **MLTSSL** - Green badge (badge-success)
- 🔵 **STSOL** - Blue badge (badge-info)
- 🟡 **ROL** - Yellow badge (badge-warning)
- ⚫ **CSOL** - Gray badge (badge-secondary)

---

## 🔄 Re-importing Data

### If You Need to Re-import:

**Option 1: Clear and Re-import All**
```sql
-- Backup first!
TRUNCATE TABLE anzsco_occupations;
```
Then import all 4 files again.

**Option 2: Selective Update**
- The merge logic allows safe re-importing
- Existing TRUE flags will never be set to FALSE
- New TRUE flags will be added
- Use "Update existing occupations" checkbox

---

## ⚡ Quick Import Commands (Optional)

If you want to create a single merged CSV file:

```powershell
# Merge all CSV files into one (PowerShell script)
# This removes duplicates and merges flags
# Coming soon...
```

---

## 🐛 Troubleshooting

### Problem: Occupation appears in only one list when it should be in multiple

**Solution**: 
1. Check import logs for errors
2. Verify "Update existing occupations" was checked
3. Re-import the affected CSV file
4. The merge logic will add the missing flag

### Problem: Import shows "Skipped" count

**Cause**: "Update existing occupations" was NOT checked

**Solution**: Re-import with checkbox enabled

---

## 📊 Statistics

Based on the CSV files:

| List | Unique Occupations | Notes |
|------|-------------------|-------|
| MLTSSL | 213 | Highest skilled occupations |
| STSOL | 206 | State nominated |
| ROL | 78 | Regional only |
| CSOL | 457 | Largest list, includes many overlaps |
| **Total Unique** | ~900+ | After deduplication |

Many occupations appear on 2+ lists simultaneously.

---

## ✅ Import Checklist

- [ ] Corrected CSOL label from "Legacy" to "Core Skills Occupation List"
- [ ] Updated import service with MERGE logic for boolean flags
- [ ] Verified import files are in `public/` folder
- [ ] Backed up database before import (recommended)
- [ ] Navigate to `/admin/anzsco/import`
- [ ] Import MLTSSL file first (213 rows expected)
- [ ] Check "Update existing occupations" ✅
- [ ] Import STSOL file (some updates, some inserts)
- [ ] Import ROL file (some updates, some inserts)
- [ ] Import CSOL file (most updates, some inserts)
- [ ] Verify results in `/admin/anzsco` list
- [ ] Check that overlapping occupations show multiple badges
- [ ] Test autocomplete in client forms

---

## 📞 Support

If you encounter any issues during import:
1. Check the import results for specific error messages
2. Review the import logs
3. Verify CSV file format matches template
4. Ensure database connection is stable
5. Check for unique constraint violations

---

**Last Updated**: October 8, 2025
**Version**: 1.1 (with MERGE logic)

