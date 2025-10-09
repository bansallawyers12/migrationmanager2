# ANZSCO Database - Quick Start Guide

## 🚀 Get Started in 5 Minutes

### Step 1: Access the Admin Panel
Open your browser and navigate to:
```
http://your-domain/admin/anzsco
```

You should see the ANZSCO Occupations management page with 10 sample occupations.

---

### Step 2: Test the Autocomplete

1. Go to **any client edit page**
2. Scroll to **"Occupation & Skills"** section
3. In the **"Nominated Occupation"** field, type: `soft`
4. You should see **"Software Engineer (261313)"** appear in the dropdown
5. Click it to select
6. **Watch the magic:** The system automatically fills:
   - Occupation Code: `261313`
   - Assessing Authority: `ACS`

7. Now enter an **Assessment Date**: `01/01/2024`
8. The **Expiry Date** auto-calculates to: `01/01/2027` (3 years later)

✅ **If this works, your integration is perfect!**

---

### Step 3: Import Your Real Data

Now it's time to add your actual occupation data.

#### Option A: Use the Template (Recommended for beginners)

1. Go to: `http://your-domain/admin/anzsco/import`
2. Click **"Download Template"**
3. Open the CSV file in Excel
4. **Delete the sample rows** (keep the header row!)
5. Add your occupation data:
   - Column A: ANZSCO Code (e.g., 261313)
   - Column B: Occupation Title (e.g., Software Engineer)
   - Column C: Skill Level (1-5, optional)
   - Columns D-G: Yes/No for lists (MLTSSL, STSOL, ROL, CSOL)
   - Column H: Assessing Authority (e.g., ACS, VETASSESS)
   - Column I: Validity Years (default: 3)
   - Columns J-K: Additional info and alternate titles (optional)
6. Save as CSV
7. Upload and import

#### Option B: Import from Your Existing Files

You mentioned you have multiple files. Here's the strategy:

**First Import - Base Data:**
1. Take your most complete file (e.g., VETASSESS occupation list)
2. Go to import page
3. Upload the file
4. The system will show you the columns
5. Map them:
   - Your "Code" column → `anzsco_code`
   - Your "Occupation" column → `occupation_title`
   - Your "Authority" column → `assessing_authority`
6. Check **"Update existing occupations"**
7. Click **"Import Data"**

**Second Import - Add MLTSSL:**
1. Take your MLTSSL list
2. Upload it
3. Map:
   - Your code column → `anzsco_code`
   - Add a column with "Yes" → `mltssl`
4. Import with **"Update existing"** checked
5. This will ADD the MLTSSL flag to existing occupations

**Third Import - Add STSOL:**
- Same process as above, but map to `stsol`

**Repeat for other lists...**

---

### Step 4: Verify Your Data

1. Go to: `http://your-domain/admin/anzsco`
2. You should see all your imported occupations
3. Use the filters:
   - **Status**: Active/Inactive
   - **Occupation List**: Filter by MLTSSL, STSOL, etc.
4. Test the search bar (searches codes and titles)

---

### Step 5: Test Again in Client Form

1. Go back to a client edit page
2. Type an occupation from YOUR data
3. Verify it autocompletes correctly
4. Verify all fields auto-fill

🎉 **Congratulations! Your ANZSCO database is live!**

---

## 📝 Common Import Scenarios

### Scenario 1: "I have one Excel file with everything"
- Perfect! Just map all columns to the template
- One import and you're done

### Scenario 2: "I have separate files for each list"
- Import the base occupations first (codes + titles)
- Then import each list file with "Update existing" checked
- The system merges the data by ANZSCO code

### Scenario 3: "My file has different column names"
- No problem! The import page lets you map columns
- Your "SOL Code" can map to `anzsco_code`
- Your "Job Title" can map to `occupation_title`

### Scenario 4: "Some codes are duplicated across my files"
- That's expected! Use "Update existing occupations" option
- The system updates records instead of creating duplicates
- It merges data from multiple sources

---

## 🔍 Tips & Tricks

### For Better Autocomplete Results:
- Add **alternate titles** (e.g., "Developer, Programmer, Coder" for Software Engineer)
- The search looks in all these fields

### For Data Management:
- Don't delete old occupations - **mark them inactive** instead
- Inactive occupations won't show in autocomplete but preserve history

### For Import:
- Excel's "Yes/No" columns work perfectly for list flags
- Empty cells are treated as "No" for boolean fields
- The system is forgiving - try importing, check results, adjust, re-import

---

## 🆘 Need Help?

### Check These First:
1. **Read**: `ANZSCO_DATABASE_GUIDE.md` for detailed instructions
2. **Review**: `ANZSCO_IMPLEMENTATION_SUMMARY.md` for technical details

### Common Issues:

**"Autocomplete not working"**
- Press F12 in browser, check Console for errors
- Verify you're on the edit page, not detail page
- Clear browser cache

**"Import says 'ANZSCO code required'"**
- Check your CSV has the anzsco_code column
- Verify the column mapping in import step

**"Can't find the admin page"**
- URL is `/admin/anzsco` (not `/anzsco`)
- Make sure you're logged in as admin

---

## 🎯 What's Next?

Once your data is imported:

1. **Train your team** on using autocomplete
2. **Update the data** quarterly (occupation lists change)
3. **Add missing occupations** as clients need them
4. **Review and correct** any import errors

---

## 📊 Quick Reference

| Task | URL |
|------|-----|
| Manage Occupations | `/admin/anzsco` |
| Add New | `/admin/anzsco/create` |
| Import Data | `/admin/anzsco/import` |
| Download Template | `/admin/anzsco/download-template` |

---

**You're all set! Start by testing with the sample data, then import your real occupation lists.** 

Good luck! 🚀

