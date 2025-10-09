# ANZSCO Occupation Database Guide

## Overview

The ANZSCO (Australian and New Zealand Standard Classification of Occupations) database system allows you to:
- Store and manage occupation data with ANZSCO codes
- Track which occupation lists each occupation belongs to (MLTSSL, STSOL, ROL, CSOL)
- Store skill assessment authority information and validity periods
- Auto-fill occupation details in client forms using autocomplete
- Import occupation data from CSV/Excel files

## Features

### 1. Admin Management Interface

Access the ANZSCO management interface at: **`/admin/anzsco`**

Features:
- ✅ View all occupations in a searchable DataTable
- ✅ Filter by status (Active/Inactive) and occupation lists
- ✅ Add new occupations manually
- ✅ Edit existing occupations
- ✅ Delete occupations
- ✅ Toggle active/inactive status
- ✅ Import data from CSV/Excel files

### 2. Data Import System

#### Access Import Page
Navigate to: **`/admin/anzsco/import`**

#### Download Template
Click "Download Template" to get a CSV file with:
- Sample data showing the correct format
- All required and optional columns

#### Template Structure

| Column Name | Required | Description | Example |
|------------|----------|-------------|---------|
| `anzsco_code` | **Yes** | 6-digit ANZSCO code | 261313 |
| `occupation_title` | **Yes** | Official occupation name | Software Engineer |
| `skill_level` | No | Skill level 1-5 | 1 |
| `mltssl` | No | On MLTSSL list? | Yes/No/1/0 |
| `stsol` | No | On STSOL list? | Yes/No/1/0 |
| `rol` | No | On ROL list? | Yes/No/1/0 |
| `csol` | No | On CSOL list? (legacy) | Yes/No/1/0 |
| `assessing_authority` | No | Assessment authority | ACS, VETASSESS, TRA |
| `validity_years` | No | Assessment validity (default: 3) | 3 |
| `additional_info` | No | Extra notes | Trade qualification required |
| `alternate_titles` | No | Other names (comma-separated) | Developer, Programmer |

#### Boolean Values
The system accepts multiple formats for Yes/No fields:
- **True**: `Yes`, `Y`, `1`, `True`, `X`, `yes`, `true`
- **False**: `No`, `N`, `0`, `False`, `no`, `false`, or empty

#### Import Process
1. Prepare your CSV or Excel file with occupation data
2. Upload the file
3. Choose whether to update existing occupations (checked by default)
4. Click "Import Data"
5. Review the results:
   - **Total**: Number of rows processed
   - **Inserted**: New occupations added
   - **Updated**: Existing occupations modified
   - **Skipped**: Duplicates not updated (if update option unchecked)
   - **Errors**: Validation failures

### 3. Client Form Integration

The system automatically enhances the occupation fields in the client edit page.

#### Autocomplete by Occupation Name
1. Start typing an occupation name (minimum 2 characters)
2. Wait 300ms for autocomplete dropdown to appear
3. Select from matching occupations
4. All fields auto-fill:
   - Occupation Code
   - Assessing Authority
   - Expiry Date (calculated from assessment date + validity)

#### Autocomplete by Code
1. Type an ANZSCO code (minimum 3 digits)
2. If found, you'll be prompted to auto-fill
3. Click "OK" to fill all fields automatically

#### Visual Indicators
- Fields filled from the database show a green database icon
- Occupation lists are displayed as colored badges:
  - 🟢 **MLTSSL** (Green)
  - 🔵 **STSOL** (Blue)
  - 🟡 **ROL** (Yellow)
  - ⚪ **CSOL** (Gray)

### 4. Auto-Calculation

When you:
1. Select an occupation from the database
2. Enter an assessment date

The system automatically:
- Fills the occupation code
- Fills the assessing authority
- **Calculates the expiry date** based on the authority's validity period

**Example:**
- Assessment Date: 15/01/2024
- Validity: 3 years
- **Auto-calculated Expiry**: 15/01/2027

## Data Import Workflow

### Step 1: Gather Your Data

Collect occupation information from various sources:
- VETASSESS occupation lists
- CSOL/MLTSSL/STSOL official lists
- Skill assessment authority websites

### Step 2: Match and Merge Data

For each file you have:

1. **First Import (e.g., VETASSESS)**
   - Contains: occupation names, codes, assessment authority
   - Map columns: `occupation_title` → your file's occupation column
   - Import with "Update existing" checked

2. **Second Import (e.g., MLTSSL list)**
   - Contains: codes and list membership
   - Map columns: `anzsco_code` → code column, `mltssl` → "Yes"
   - Import with "Update existing" checked
   - This will **update** existing records with list information

3. **Continue for other lists**
   - Each import updates existing records by ANZSCO code
   - No duplicates are created

### Step 3: Verify and Correct

1. Go to `/admin/anzsco`
2. Filter by occupation list to verify
3. Edit individual records if needed

## Common Assessing Authorities

- **ACS** - Australian Computer Society (ICT Professionals)
- **VETASSESS** - Vocational Education Training and Assessment Services
- **TRA** - Trades Recognition Australia
- **Engineers Australia** - For engineering professions
- **ANMAC** - Australian Nursing and Midwifery Accreditation Council
- **CPA Australia** - For accounting professions
- **AITSL** - Australian Institute for Teaching and School Leadership

## API Endpoints

The system provides RESTful API endpoints:

### Search Occupations
```
GET /api/occupations/search?q={query}
```
Returns matching occupations with all details.

### Get by Code
```
GET /api/occupations/{code}
```
Returns occupation details for a specific ANZSCO code.

## Database Schema

Table: `anzsco_occupations`

```sql
- id (Primary Key)
- anzsco_code (Unique, Indexed)
- occupation_title (Indexed)
- occupation_title_normalized (Indexed, for searching)
- skill_level (1-5)
- is_on_mltssl (Boolean)
- is_on_stsol (Boolean)
- is_on_rol (Boolean)
- is_on_csol (Boolean)
- assessing_authority
- assessment_validity_years (Default: 3)
- additional_info (Text)
- alternate_titles (Text)
- is_active (Boolean, Indexed)
- created_by (Admin ID)
- updated_by (Admin ID)
- created_at
- updated_at
```

## Troubleshooting

### Import Errors

**Error: "ANZSCO code already exists"**
- Solution: Check "Update existing occupations" option

**Error: "ANZSCO code required"**
- Solution: Ensure your CSV has a column mapped to `anzsco_code`

**Error: "Invalid file format"**
- Solution: Use CSV, XLSX, or XLS files only

### Autocomplete Not Working

1. Check browser console for JavaScript errors
2. Verify `/api/occupations/search` endpoint is accessible
3. Clear browser cache
4. Ensure occupation fields have correct CSS classes:
   - `.nomi_occupation` for occupation name
   - `.occupation_code` for code
   - `.autocomplete-items` div next to occupation name input

### Expiry Date Not Calculating

1. Ensure assessment date is in `dd/mm/yyyy` format
2. Verify the occupation was selected from the database (green icon)
3. Check that assessment authority has a validity value

## Best Practices

1. **Import Data Incrementally**
   - Start with basic occupation list (code + name)
   - Add authorities in second import
   - Add list memberships in subsequent imports

2. **Keep Data Updated**
   - Occupation lists change periodically
   - Review and update quarterly
   - Mark obsolete occupations as inactive (don't delete)

3. **Use Alternate Titles**
   - Add common variations of occupation names
   - Improves autocomplete matching
   - Example: "Developer, Programmer, Coder" for Software Engineer

4. **Validate Regularly**
   - Cross-check with official government sources
   - Verify assessment authority assignments
   - Update validity periods if they change

## Quick Reference

| Task | URL |
|------|-----|
| View All Occupations | `/admin/anzsco` |
| Add New Occupation | `/admin/anzsco/create` |
| Import Data | `/admin/anzsco/import` |
| Download Template | `/admin/anzsco/download-template` |
| Edit Occupation | `/admin/anzsco/{id}/edit` |

## Support

If you encounter issues:
1. Check this guide
2. Review import error messages
3. Test with the sample template data
4. Verify all routes are accessible
5. Check server logs for errors

---

**Note**: The system is designed to be flexible. You can start with basic data and enhance it over time. The "Update existing" feature allows you to gradually build a comprehensive database.

