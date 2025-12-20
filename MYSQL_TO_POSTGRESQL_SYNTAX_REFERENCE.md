# MySQL to PostgreSQL Syntax Reference Guide

This document serves as a quick reference for syntax changes made during the MySQL to PostgreSQL migration. Use this guide when pulling new code from MySQL to identify what needs to be changed for PostgreSQL compatibility.

---

## Table of Contents
1. [Date Handling](#date-handling)
2. [Invalid Date Values](#invalid-date-values)
3. [String Aggregation](#string-aggregation)
4. [Date Formatting](#date-formatting)
5. [Null Handling in ORDER BY](#null-handling-in-order-by)
6. [String Concatenation](#string-concatenation)
7. [NOT NULL Constraints](#not-null-constraints)
8. [Pending Migrations and Schema Mismatches](#pending-migrations-and-schema-mismatches)
9. [Handling Missing Form Fields](#handling-missing-form-fields)
10. [Notes Table - Missing Default Values](#notes-table---missing-default-values)
11. [Search Patterns](#search-patterns)

---

## Date Handling

### Issue: VARCHAR Date Fields Stored as dd/mm/yyyy

**Problem:** Some date fields are stored as VARCHAR in `dd/mm/yyyy` format (e.g., `trans_date` in `account_client_receipts` table). Direct string comparison doesn't work correctly for date ranges.

**MySQL Approach:**
```php
// ❌ MySQL - This doesn't work for date comparisons
->where('trans_date', '>=', '01/01/2024')
->where('trans_date', '<=', '31/01/2024')
```

**PostgreSQL Solution:**
```php
// ✅ PostgreSQL - Convert VARCHAR to DATE using TO_DATE()
->whereRaw("TO_DATE(trans_date, 'DD/MM/YYYY') BETWEEN TO_DATE(?, 'DD/MM/YYYY') AND TO_DATE(?, 'DD/MM/YYYY')", [$startDate, $endDate])
```

**Example from Codebase:**
- **File:** `app/Services/FinancialStatsService.php`
- **Lines:** 63-67
```php
$applyDateFilter = function($query, $start, $end) {
    return $query->whereRaw("TO_DATE(trans_date, 'DD/MM/YYYY') BETWEEN TO_DATE(?, 'DD/MM/YYYY') AND TO_DATE(?, 'DD/MM/YYYY')", [$start, $end]);
};
```

**Safety:** ✅ **SAFE** - This is the correct way to handle VARCHAR date fields in PostgreSQL. Always use when comparing dates stored as VARCHAR.

**Notes:**
- Format string is case-sensitive: `'DD/MM/YYYY'` (uppercase)
- Use parameterized queries (bindings) to prevent SQL injection
- Both operands must be converted with TO_DATE() for proper comparison

---

## Invalid Date Values

### Issue: '0000-00-00' Invalid Date

**Problem:** MySQL accepts `'0000-00-00'` as a valid date value, but PostgreSQL does not. PostgreSQL will throw an error or store NULL instead.

**MySQL Approach:**
```php
// ❌ MySQL - This works in MySQL but fails in PostgreSQL
->where('dob', '!=', '0000-00-00')
->where('dob', '=', '0000-00-00')  // Also problematic
```

**PostgreSQL Solution:**
```php
// ✅ PostgreSQL - Use NULL checks instead
->whereNotNull('dob')              // Instead of != '0000-00-00'
->whereNull('dob')                 // Instead of = '0000-00-00'
```

**PHP String Comparisons:**
```php
// ❌ MySQL Legacy Code
if ($date != '0000-00-00') { ... }
if ($date === '0000-00-00') { ... }

// ✅ PostgreSQL - Use empty/null checks
if (!empty($date) && $date !== null) { ... }
if (empty($date) || $date === null) { ... }
```

**Example from Codebase:**
- **File:** `app/Console/Commands/UpdateClientAges.php`
- **Line 54:** Changed from `->where('dob', '!=', '0000-00-00')` to `->whereNotNull('dob')`

**Safety:** 🔴 **CRITICAL** - Database queries with `'0000-00-00'` will **fail immediately** in PostgreSQL. This must be fixed before the code runs.

**Notes:**
- PostgreSQL stores invalid dates as NULL, not '0000-00-00'
- Always check for NULL instead of string comparison with '0000-00-00'
- Migration scripts should convert '0000-00-00' to NULL during data migration
- PHP code checking for '0000-00-00' should be updated to use empty/null checks (medium priority)

---

## String Aggregation

### Issue: GROUP_CONCAT() Not Available

**Problem:** MySQL's `GROUP_CONCAT()` function is not available in PostgreSQL.

**MySQL Approach:**
```php
// ❌ MySQL
DB::raw('GROUP_CONCAT(DISTINCT phone ORDER BY phone) as all_phones')
```

**PostgreSQL Solution:**
```php
// ✅ PostgreSQL
DB::raw('STRING_AGG(DISTINCT phone, \', \' ORDER BY phone) as all_phones')
```

**Example from Codebase:**
- **File:** `app/Http/Controllers/CRM/ClientsController.php`
- **Lines:** 4848-4849
```php
DB::raw('STRING_AGG(DISTINCT client_contacts.phone, \', \' ORDER BY client_contacts.contact_type) as all_phones'),
DB::raw('STRING_AGG(DISTINCT client_emails.email, \', \' ORDER BY client_emails.email_type) as all_emails')
```

**Syntax Differences:**
- **GROUP_CONCAT:** `GROUP_CONCAT([DISTINCT] column [ORDER BY column])`
- **STRING_AGG:** `STRING_AGG([DISTINCT] column, delimiter [ORDER BY column])`

**Safety:** 🔴 **CRITICAL** - Queries using GROUP_CONCAT() will **fail immediately** in PostgreSQL. Must be converted before execution.

**Notes:**
- STRING_AGG requires an explicit delimiter (usually `', '`)
- DISTINCT and ORDER BY work the same way in both
- ORDER BY clause comes after the delimiter in STRING_AGG
- Escape single quotes in delimiter: `\'` for `, `

---

## Date Formatting

### Issue: DATE_FORMAT() Not Available

**Problem:** MySQL's `DATE_FORMAT()` function uses different syntax than PostgreSQL's `TO_CHAR()`.

**MySQL Approach:**
```sql
-- ❌ MySQL
SELECT DATE_FORMAT(created_at, '%Y-%m') as month_key
SELECT DATE_FORMAT(created_at, '%b %Y') as label
```

**PostgreSQL Solution:**
```sql
-- ✅ PostgreSQL
SELECT TO_CHAR(created_at, 'YYYY-MM') as month_key
SELECT TO_CHAR(created_at, 'Mon YYYY') as label
```

**Example from Codebase:**
- **File:** `app/Http/Controllers/CRM/ClientsController.php`
- **Lines:** 285-286, 355-356
```php
DB::raw("TO_CHAR(created_at, 'YYYY-MM') as sort_key"),
DB::raw("TO_CHAR(created_at, 'Mon YYYY') as label"),
```

**Common Format Conversions:**

| MySQL (DATE_FORMAT) | PostgreSQL (TO_CHAR) | Description |
|---------------------|---------------------|-------------|
| `%Y` | `YYYY` | 4-digit year |
| `%y` | `YY` | 2-digit year |
| `%m` | `MM` | Month (01-12) |
| `%d` | `DD` | Day of month (01-31) |
| `%M` | `Month` | Full month name (January) |
| `%b` | `Mon` | Abbreviated month (Jan) |
| `%H` | `HH24` | Hour (00-23) |
| `%i` | `MI` | Minutes (00-59) |
| `%s` | `SS` | Seconds (00-59) |

**Safety:** 🔴 **CRITICAL** - Queries using DATE_FORMAT() will **fail immediately** in PostgreSQL. Must be converted.

**Notes:**
- TO_CHAR uses uppercase format codes for most values
- Format string is case-sensitive
- Use single quotes for format strings
- Different format specifiers - refer to PostgreSQL documentation for full list

---

## Null Handling in ORDER BY

### Issue: NULL Values Sort Differently

**Problem:** PostgreSQL and MySQL handle NULL values differently in ORDER BY clauses. In PostgreSQL, NULLs sort first by default (or last when using DESC), but we often want NULLs last when sorting DESC.

**MySQL Approach:**
```php
// ❌ MySQL - NULLs may sort inconsistently
->orderBy('finish_date', 'desc')
```

**PostgreSQL Solution:**
```php
// ✅ PostgreSQL - Explicitly place NULLs last
->orderByRaw('finish_date DESC NULLS LAST')
```

**Example from Codebase:**
- **File:** `resources/views/crm/clients/tabs/personal_details.blade.php`
- **Lines:** 367, 425
```php
->orderByRaw('finish_date DESC NULLS LAST')
->orderByRaw('job_finish_date DESC NULLS LAST')
```

**Other Examples:**
- **File:** `app/Http/Controllers/CRM/ClientsController.php`
- **Lines:** 4501-4502
```php
->orderByRaw('finish_date DESC NULLS LAST')
->orderByRaw('job_finish_date DESC NULLS LAST')
```

**Safety:** 🟡 **MEDIUM** - Not critical, but results may differ between MySQL and PostgreSQL. Recommended for consistency, especially when displaying data to users.

**Notes:**
- `NULLS LAST` places NULL values at the end when sorting DESC
- `NULLS FIRST` places NULL values at the beginning (default for DESC, but can be explicit)
- Use this when you want incomplete records (with NULL dates) to appear last in sorted lists
- Important for user-facing lists where you want complete records first

---

## String Concatenation

### Issue: CONCAT() vs || Operator

**Note:** Both MySQL and PostgreSQL support `CONCAT()` function, but PostgreSQL's `||` operator is more idiomatic and preferred.

**MySQL Approach:**
```sql
-- ✅ MySQL - Works in PostgreSQL too, but less efficient
SELECT CONCAT(first_name, ' ', last_name) as full_name
```

**PostgreSQL Preferred:**
```sql
-- ✅ PostgreSQL - More efficient and idiomatic
SELECT COALESCE(first_name, '') || ' ' || COALESCE(last_name, '') as full_name
```

**Safety:** 🟢 **LOW** - CONCAT() works in both databases, but `||` is preferred in PostgreSQL for better performance.

**Notes:**
- `||` is the standard SQL string concatenation operator (ANSI SQL)
- CONCAT() in PostgreSQL handles NULLs by converting them to empty strings (MySQL behavior)
- Using `||` with COALESCE() gives you explicit control over NULL handling
- For simple concatenation, both work, but `||` is more performant

---

## NOT NULL Constraints

### Issue: PostgreSQL Enforces NOT NULL Strictly

**Problem:** PostgreSQL strictly enforces NOT NULL constraints on columns. MySQL is more lenient and may allow NULL values even when a column is defined as NOT NULL (depending on SQL mode). When migrating from MySQL to PostgreSQL, any code that doesn't provide values for NOT NULL columns will fail.

**MySQL Approach:**
```php
// ❌ MySQL - May allow NULL even if column is NOT NULL
ActivitiesLog::create([
    'client_id' => $clientId,
    'created_by' => Auth::id(),
    'subject' => 'Activity subject',
    'description' => 'Activity description',
    'activity_type' => 'activity',
    // task_status missing - MySQL might allow this
]);
```

**PostgreSQL Solution:**
```php
// ✅ PostgreSQL - Must provide value for NOT NULL columns
ActivitiesLog::create([
    'client_id' => $clientId,
    'created_by' => Auth::id(),
    'subject' => 'Activity subject',
    'description' => 'Activity description',
    'activity_type' => 'activity',
    'task_status' => 0, // Required for NOT NULL column
]);
```

**For `new Model` Pattern:**
```php
// ❌ MySQL - May allow NULL
$objs = new ActivitiesLog;
$objs->client_id = $clientId;
$objs->created_by = Auth::id();
$objs->subject = 'Activity subject';
$objs->save(); // task_status missing

// ✅ PostgreSQL - Must set before save
$objs = new ActivitiesLog;
$objs->client_id = $clientId;
$objs->created_by = Auth::id();
$objs->subject = 'Activity subject';
$objs->task_status = 0; // Required before save
$objs->save();
```

**Examples from Codebase:**

1. **ActivitiesLog Table:**
   - **File:** `app/Traits/LogsClientActivity.php`
   - **Line 27:** Added `'task_status' => 0,` and `'pin' => 0,` to ActivitiesLog::create()
   - **Files Fixed:** All files creating ActivitiesLog instances (40+ instances across 13 files)
   - **Columns:** `task_status` (default: 0), `pin` (default: 0)

2. **ClientEmail Table:**
   - **File:** `app/Http/Controllers/CRM/ClientPersonalDetailsController.php`
   - **Lines 980, 2010:** Added `'is_verified' => false` to ClientEmail::create()
   - **Files Fixed:** All files creating ClientEmail instances (7 instances across 3 files)
   - **Columns:** `is_verified` (default: false)

3. **ClientContact Table:**
   - **Files:** `app/Http/Controllers/CRM/ClientPersonalDetailsController.php`, `app/Http/Controllers/CRM/ClientsController.php`, `app/Http/Controllers/CRM/Leads/LeadController.php`, `app/Services/BansalAppointmentSync/ClientMatchingService.php`
   - **Lines:** Multiple locations across 4 files
   - **Files Fixed:** All files creating ClientContact instances (9 instances across 4 files)
   - **Columns:** `is_verified` (default: false)

4. **ClientQualification Table:**
   - **Files:** `app/Http/Controllers/CRM/ClientPersonalDetailsController.php`, `app/Http/Controllers/CRM/ClientsController.php`
   - **Lines:** Multiple locations across 2 files
   - **Files Fixed:** All files creating ClientQualification instances (5 instances across 2 files)
   - **Columns:** `specialist_education` (default: 0), `stem_qualification` (default: 0), `regional_study` (default: 0)

5. **ClientExperience Table:**
   - **Files:** `app/Http/Controllers/CRM/ClientPersonalDetailsController.php`, `app/Http/Controllers/CRM/ClientsController.php`
   - **Lines:** Multiple locations across 2 files
   - **Files Fixed:** All files creating ClientExperience instances (4 instances across 2 files)
   - **Columns:** `fte_multiplier` (default: 1.00)

6. **Admins Table (verified column):**
   - **File:** `app/Http/Controllers/CRM/Leads/LeadController.php`
   - **Line 382:** Added `'verified' => 0` to `$adminData` array when creating leads via `DB::table('admins')->insertGetId()`
   - **Files Fixed:** LeadController.php (1 instance)
   - **Columns:** `verified` (default: 0 for new leads, 1 for verified users)
   - **Note:** When using `DB::table('admins')->insertGetId()` or `DB::table('admins')->insert()`, must include `'verified' => 0` for new leads/clients

7. **Admins Table (password column):**
   - **File:** `app/Http/Controllers/CRM/Leads/LeadController.php`
   - **Line 374:** Changed from `'password' => ''` to `'password' => Hash::make('LEAD_PLACEHOLDER')` when creating leads via `DB::table('admins')->insertGetId()`
   - **Files Fixed:** LeadController.php (1 instance)
   - **Columns:** `password` (NOT NULL, required for all admins table records)
   - **Issue:** PostgreSQL rejects empty strings `''` for NOT NULL string columns. Empty string may be treated as NULL or rejected entirely.
   - **Solution:** Use `Hash::make('LEAD_PLACEHOLDER')` as placeholder. This is safe because:
     - Leads (`type='lead'`) typically have `cp_status=0` and cannot login
     - When client portal is activated, password is overwritten with real password
     - Placeholder hash will never match any login attempt (`Hash::check()` returns false)
   - **Note:** The `admins` table is used for staff, leads, and clients. Password is required due to NOT NULL constraint, but leads don't need real passwords.

8. **Admins Table (show_dashboard_per column):**
   - **File:** `app/Http/Controllers/CRM/Leads/LeadController.php`
   - **Line 383:** Added `'show_dashboard_per' => 0` to `$adminData` array when creating leads via `DB::table('admins')->insertGetId()`
   - **Files Fixed:** LeadController.php (1 instance)
   - **Columns:** `show_dashboard_per` (NOT NULL, default: 0 for leads/clients, 1 for staff with dashboard permission)
   - **Note:** When using `DB::table('admins')->insertGetId()` or `DB::table('admins')->insert()`, must include `'show_dashboard_per' => 0` for new leads/clients

9. **Admins Table (EOI Qualification columns):**
   - **File:** `app/Http/Controllers/CRM/Leads/LeadController.php`
   - **Lines 386-388:** Added `'australian_study' => 0`, `'specialist_education' => 0`, `'regional_study' => 0` to `$adminData` array when creating leads via `DB::table('admins')->insertGetId()`
   - **Files Fixed:** LeadController.php (1 instance)
   - **Columns:** `australian_study` (NOT NULL, default: 0), `specialist_education` (NOT NULL, default: 0), `regional_study` (NOT NULL, default: 0)
   - **Issue:** These columns have `default(0)` in the migration, but PostgreSQL doesn't apply database defaults when using explicit column lists in `INSERT` statements with `DB::table()->insert()`. Must explicitly provide values.
   - **Note:** These fields track EOI (Expression of Interest) qualifications for immigration points calculation. For new leads, all should be `0` (false). When using `DB::table('admins')->insertGetId()` or `DB::table('admins')->insert()`, must include all three fields.

10. **Admins Table (Client Portal columns):**
   - **File:** `app/Http/Controllers/CRM/Leads/LeadController.php`
   - **Lines 385-386:** Added `'cp_status' => 0` and `'cp_code_verify' => 0` to `$adminData` array when creating leads via `DB::table('admins')->insertGetId()`
   - **Files Fixed:** LeadController.php (1 instance)
   - **Columns:** `cp_status` (NOT NULL, default: 0), `cp_code_verify` (NOT NULL, default: 0)
   - **Issue:** These columns have `default(0)` in the migration, but PostgreSQL doesn't apply database defaults when using explicit column lists in `INSERT` statements with `DB::table()->insert()`. Must explicitly provide values.
   - **Note:** `cp_status` controls client portal access (0 = inactive, 1 = active). `cp_code_verify` tracks verification code status. For new leads, both should be `0`. When using `DB::table('admins')->insertGetId()` or `DB::table('admins')->insert()`, must include both fields.

**Safety:** 🔴 **CRITICAL** - Code missing NOT NULL column values will **fail immediately** in PostgreSQL with errors like:
```
SQLSTATE[23502]: Not null violation: 7 ERROR: null value in column "task_status" 
of relation "activities_logs" violates not-null constraint
```

**Notes:**
- Always check database schema for NOT NULL columns
- When using `Model::create([...])`, include all NOT NULL columns
- When using `new Model` followed by `->save()`, set all NOT NULL properties before save
- Use appropriate default values (e.g., `0` for numeric fields, empty string for text fields)
- **IMPORTANT:** PostgreSQL may reject empty strings `''` for NOT NULL string columns. Use placeholder values instead (e.g., `Hash::make('PLACEHOLDER')` for password fields)
- Check migration files to identify which columns have NOT NULL constraints
- PostgreSQL will reject the entire transaction if any NOT NULL constraint is violated

**Common Patterns to Fix:**
- `Model::create([...])` - Add missing NOT NULL fields to the array
- `new Model; $obj->field = value; $obj->save();` - Add `$obj->not_null_field = default_value;` before save
- Mass assignment - Ensure `$fillable` array includes the NOT NULL field in the model

**ActivitiesLog Specific Pattern:**
When using `new ActivitiesLog` followed by `->save()`, always set:
```php
$objs = new ActivitiesLog;
$objs->client_id = $clientId;
$objs->created_by = Auth::user()->id;
$objs->subject = $subject;
$objs->description = $description;
$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
$objs->save();
```

**When to Use task_status = 1:**
- Only use `task_status = 1` when logging actual task completion (e.g., "Task completed for [assignee]")
- For all regular activities (document uploads, emails, appointments, etc.), use `task_status = 0`
- Example of task_status = 1: `app/Http/Controllers/CRM/AssigneeController.php` line 1080

**When to Use pin = 1:**
- Only use `pin = 1` when the activity should be pinned/featured
- For all regular activities, use `pin = 0`
- Pinned activities are typically displayed prominently in activity feeds

**Common Error:**
```
SQLSTATE[23502]: Not null violation: 7 ERROR: null value in column "task_status" 
of relation "activities_logs" violates not-null constraint
```
This occurs when `new ActivitiesLog` is used without setting `task_status` and `pin` before `save()`.

**Files Fixed (30 locations):**
- `app/Http/Controllers/CRM/ClientsController.php` - 8 locations
- `app/Http/Controllers/CRM/AppointmentsController.php` - 3 locations
- `app/Http/Controllers/CRM/AssigneeController.php` - 3 locations
- `app/Http/Controllers/CRM/ClientPersonalDetailsController.php` - 1 location
- `app/Http/Controllers/CRM/ClientAccountsController.php` - 12 locations
- `app/Http/Controllers/CRM/CRMUtilityController.php` - 2 locations
- `app/Http/Controllers/CRM/Leads/LeadConversionController.php` - 1 location (already fixed)

**Known Tables with NOT NULL Columns:**
- `activities_logs`: 
  - `task_status` (default: 0) - **CRITICAL**: Must set before save. Use `0` for regular activities, `1` for task completions
  - `pin` (default: 0) - **CRITICAL**: Must set before save. Use `0` for regular activities, `1` for pinned activities
  - **Pattern:** Always set both fields when using `new ActivitiesLog` followed by `->save()`
  - **Common mistake:** Forgetting to set these fields causes immediate PostgreSQL NOT NULL violations
- `client_emails`: `is_verified` (default: false)
- `client_contacts`: `is_verified` (default: false)
- `client_qualifications`: `specialist_education` (default: 0), `stem_qualification` (default: 0), `regional_study` (default: 0)
- `client_experiences`: `fte_multiplier` (default: 1.00)
- `client_matters`: `matter_status` (default: 1 for active) - **CRITICAL**: Must set `matter_status = 1` when creating new matters
- `admins`: 
  - `verified` (default: 0 for new leads/clients, 1 for verified users) - **CRITICAL**: Required when using `DB::table('admins')->insert()` or `insertGetId()`
  - `password` (NOT NULL, use `Hash::make('LEAD_PLACEHOLDER')` for leads) - **CRITICAL**: Empty strings may be rejected. Use hashed placeholder for leads/clients without portal access.
  - `show_dashboard_per` (default: 0 for leads/clients, 1 for staff with permission) - **CRITICAL**: Required when using `DB::table('admins')->insert()` or `insertGetId()`
  - `cp_status` (default: 0), `cp_code_verify` (default: 0) - **CRITICAL**: Database defaults not applied with explicit INSERT column lists. Must explicitly provide values.
  - `australian_study`, `specialist_education`, `regional_study` (all default: 0) - **CRITICAL**: Database defaults not applied with explicit INSERT column lists. Must explicitly provide values.
- Check migration files for other tables with NOT NULL columns that have defaults

---

## Pending Migrations and Schema Mismatches

### Issue: Code References Columns That Don't Exist Yet

**Problem:** When pulling new code from MySQL or adding new features, the code may reference database columns that don't exist in PostgreSQL yet. This typically happens when:
- Migration files exist but haven't been run
- Code was written assuming a column exists, but the migration is pending
- Schema changes were made in MySQL but not yet migrated to PostgreSQL

**Error Example:**
```
SQLSTATE[42703]: Undefined column: 7 ERROR: column "office_id" of relation "client_matters" does not exist
LINE 1: ...rt into "client_matters" ("user_id", "client_id", "office_id...
```

**Common Symptoms:**
- `QueryException` with `Undefined column` error
- Code sets a property on a model (e.g., `$matter->office_id = ...`) but the column doesn't exist
- Model's `$fillable` array includes a column that's not in the database table
- INSERT/UPDATE operations fail with column name errors

**How to Identify:**
1. Check if the error mentions a specific column that doesn't exist
2. Check if a migration file exists for that column:
   ```bash
   # Search for migration files
   ls -la database/migrations/ | grep -i "column_name"
   ```
3. Check migration status:
   ```bash
   php artisan migrate:status
   ```
4. Look for the column in the model's `$fillable` array or where it's being used in code

**Solution:**
1. **Find the migration file** that adds the missing column
2. **Check migration status** to see if it's pending:
   ```bash
   php artisan migrate:status
   ```
3. **Run the specific migration**:
   ```bash
   php artisan migrate --path=database/migrations/YYYY_MM_DD_HHMMSS_migration_name.php
   ```
4. **Or run all pending migrations** (if safe):
   ```bash
   php artisan migrate
   ```

**Example from Codebase:**
- **Error:** `column "office_id" of relation "client_matters" does not exist`
- **Location:** `app/Http/Controllers/CRM/ClientsController.php` line 8386
- **Code:**
  ```php
  $matter = new ClientMatter();
  $matter->office_id = $request['office_id'] ?? Auth::user()->office_id ?? null;
  // ... other assignments
  $matter->save(); // ❌ Fails because office_id column doesn't exist
  ```
- **Model:** `app/Models/ClientMatter.php` has `'office_id'` in `$fillable` array
- **Migration:** `database/migrations/2025_12_17_145310_add_office_to_client_matters_and_documents.php` was pending
- **Fix:** Ran the migration: `php artisan migrate --path=database/migrations/2025_12_17_145310_add_office_to_client_matters_and_documents.php`

**Safety:** 🔴 **CRITICAL** - Code referencing non-existent columns will **fail immediately** with `QueryException`. Must run pending migrations before the code can execute.

**Notes:**
- Always check `php artisan migrate:status` after pulling new code
- If you see "Undefined column" errors, check for pending migrations first
- Migration files may exist in `database/migrations/` but not have been executed
- Some migrations may fail if they depend on tables/columns that don't exist yet - run migrations in order
- When using `Model::create()` or `$model->save()`, Laravel will try to insert all `$fillable` columns, even if they don't exist in the database
- Check model's `$fillable` array matches actual database schema
- If a column is truly not needed, remove it from `$fillable` or make it conditional

**Prevention Checklist:**
- [ ] After pulling new code, run `php artisan migrate:status` to check for pending migrations
- [ ] If code references new columns, verify the migration exists and has been run
- [ ] Check model's `$fillable` array matches database schema
- [ ] Run migrations in development/staging before production
- [ ] If migration fails, check error message - may need to run dependent migrations first

**Common Migration Patterns:**
- New columns added to existing tables
- New tables created
- Indexes added/removed
- Foreign key constraints added
- Column type changes

---

## Handling Missing Form Fields

### Issue: Form Fields That Don't Exist in Request

**Problem:** When creating or updating records, controllers may expect form fields that don't always exist in the request. Different forms may submit different fields (e.g., simple form vs enhanced form). If the code directly accesses `$request->field_name` without checking if it exists, it may result in `null` values being assigned. PostgreSQL strictly enforces NOT NULL constraints, so missing required fields will cause database errors.

**MySQL Approach:**
```php
// ❌ MySQL - May silently allow NULL or fail inconsistently
$obj = new Note;
$obj->title = $request->title;  // Undefined index warning if title doesn't exist
$obj->matter_id = $request->matter_id;
$obj->mobile_number = $request->mobileNumber;  // Undefined if field doesn't exist
$obj->save();
```

**PostgreSQL Solution:**
```php
// ✅ PostgreSQL - Use null coalescing operator (??) to provide defaults
$obj = new Note;
$obj->title = $request->title ?? '';  // Default to empty string if not provided
$obj->matter_id = $request->matter_id;
$obj->mobile_number = $request->mobileNumber ?? null;  // Default to null if not provided
$obj->save();
```

**For Updates - Check Before Comparing:**
```php
// ❌ MySQL - May compare undefined values
if($oldNote->title !== $request->title) {  // Warning if title doesn't exist
    $changedFields['Title'] = ['old' => $oldNote->title, 'new' => $request->title];
}

// ✅ PostgreSQL - Check if field exists before comparing
if(isset($request->title) && $oldNote->title !== $request->title) {
    $changedFields['Title'] = ['old' => $oldNote->title, 'new' => $request->title];
}
```

**Error Handling Pattern:**
```php
// ✅ PostgreSQL - Wrap save operations in try-catch for better error handling
use Illuminate\Support\Facades\Log;

try {
    $saved = $obj->save();
} catch (\Exception $e) {
    Log::error('Error saving record: ' . $e->getMessage(), [
        'request_data' => $request->all(),
        'trace' => $e->getTraceAsString()
    ]);
    $response['status'] = false;
    $response['message'] = 'Error saving record. Please try again.';
    echo json_encode($response);
    return;
}
```

**Example from Codebase:**
- **File:** `app/Http/Controllers/CRM/Clients/ClientNotesController.php`
- **Lines:** 59-62, 68, 102-116
- **Issue:** Simple form doesn't include `title` field, but controller accessed `$request->title` directly
- **Fix:** Changed to `$obj->title = $request->title ?? '';` and added `isset($request->title)` check in update logic

**Common Patterns:**

1. **Direct property assignment:**
   ```php
   // ❌ Before
   $obj->field = $request->field;
   
   // ✅ After
   $obj->field = $request->field ?? null;  // or appropriate default
   ```

2. **Conditional checks:**
   ```php
   // ❌ Before
   if($request->field) { ... }
   
   // ✅ After (if field may not exist)
   if(isset($request->field) && $request->field) { ... }
   ```

3. **Comparisons in update logic:**
   ```php
   // ❌ Before
   if($oldValue !== $request->field) { ... }
   
   // ✅ After
   if(isset($request->field) && $oldValue !== $request->field) { ... }
   ```

**Safety:** 🔴 **CRITICAL** - Missing form fields can cause:
- `Undefined index` PHP warnings/errors
- `null value in column violates not-null constraint` PostgreSQL errors
- 500 Internal Server Error responses

**Notes:**
- Always use null coalescing operator (`??`) when accessing request fields that may not exist
- Check form views to see which fields are actually submitted
- Different forms (simple vs enhanced) may submit different fields
- Use appropriate defaults based on column type (empty string for text, null for nullable columns, 0 for numeric)
- Wrap database save operations in try-catch for better error handling and logging
- Check `isset()` before comparing values in update scenarios
- Log errors with request data to help debug issues in production

**When to Use:**
- When a field exists in some forms but not others
- When fields are conditionally displayed/shown in forms
- When creating new records where some fields are optional
- When updating records where only some fields may change
- When dealing with legacy code that may have inconsistent form submissions

---

## Notes Table - Missing Default Values

### Issue: Notes Table NOT NULL Constraints

**Problem:** After MySQL to PostgreSQL migration, creating notes fails with "Error saving note. Please try again." The `notes` table has NOT NULL constraints on fields that MySQL allowed to be NULL or had implicit defaults, but PostgreSQL strictly enforces.

**MySQL Approach:**
```php
// ❌ MySQL - May work without explicitly setting pin, folloup, status
$obj = new Note;
$obj->title = $request->title ?? '';
$obj->client_id = $request->client_id;
$obj->user_id = Auth::user()->id;
$obj->description = $request->description;
$obj->mail_id = $request->mailid;
$obj->type = $request->vtype;
$obj->task_group = $request->task_group;
$obj->save(); // May work in MySQL
```

**PostgreSQL Solution:**
```php
// ✅ PostgreSQL - Must explicitly set NOT NULL fields
$obj = new Note;
$obj->title = $request->title ?? '';
$obj->client_id = $request->client_id;
$obj->user_id = Auth::user()->id;
$obj->description = $request->description;
$obj->mail_id = $request->mailid;
$obj->type = $request->vtype;
$obj->task_group = $request->task_group;

// PostgreSQL NOT NULL constraints - must set these fields
if(!$isUpdate) {
    $obj->pin = 0; // Default to not pinned (0 = not pinned, 1 = pinned)
    $obj->folloup = 0; // Default to not a follow-up (0 = regular note, 1 = follow-up)
    $obj->status = '0'; // Default status (string '0' = active, '1' = completed)
}
$obj->save(); // Will now work in PostgreSQL
```

**Example from Codebase:**
- **File:** `app/Http/Controllers/CRM/Clients/ClientNotesController.php`
- **Lines:** 89-110
- **Issue:** Creating notes failed with database constraint violation
- **Fields Required:** 
  - `pin` (integer, NOT NULL, default: 0)
  - `folloup` (integer, NOT NULL, default: 0) 
  - `status` (string, NOT NULL, default: '0')

**Error Message:**
```
SQLSTATE[23502]: Not null violation: 7 ERROR: null value in column "pin" 
of relation "notes" violates not-null constraint
```
OR
```
Error saving note. Please try again.
```
(Displayed when exception is caught and logged)

**Safety:** 🔴 **CRITICAL** - Note creation will fail completely in PostgreSQL without these fields. This is a common issue after migration where MySQL's lenient NULL handling differs from PostgreSQL's strict enforcement.

**Notes:**
- MySQL may allow NULL values or have implicit defaults even when NOT NULL is specified (depending on SQL mode)
- PostgreSQL strictly enforces NOT NULL constraints and does not use implicit defaults
- Always check database schema for NOT NULL columns when migrating
- The `pin`, `folloup`, and `status` fields are used for note filtering and task management
- `pin`: Controls whether note is pinned to top of list
- `folloup`: Indicates if note is a follow-up task
- `status`: Tracks completion status (active/completed)

**When to Set:**
- Always set when creating new notes (`if(!$isUpdate)`)
- Not needed when updating existing notes (they already have values)
- Use `0` as default for all three fields for regular notes

---

## Search Patterns

### When Pulling Code from MySQL, Search For:

Use these patterns to find code that needs to be converted:

```bash
# Date format function
grep -r "DATE_FORMAT" app/
grep -r "STR_TO_DATE" app/

# String aggregation
grep -r "GROUP_CONCAT" app/

# Invalid date comparisons
grep -r "0000-00-00" app/
grep -r "'0000-00-00'" app/
grep -r '"0000-00-00"' app/

# Date functions
grep -r "UNIX_TIMESTAMP" app/
grep -r "FROM_UNIXTIME" app/
grep -r "TIMESTAMPDIFF" app/
grep -r "DATEDIFF" app/

# Raw SQL queries that might need review
grep -r "DB::raw" app/ | grep -i "date"
grep -r "whereRaw" app/

# NOT NULL constraint violations
# Check for Model::create or new Model patterns that might miss required fields
grep -r "::create([" app/ | grep -v "task_status"
grep -r "new ActivitiesLog" app/
grep -r "new.*Log" app/ | grep -v "task_status"
grep -r "ClientEmail::create" app/
grep -r "ClientContact::create" app/
grep -r "ClientQualification::create" app/
grep -r "ClientExperience::create" app/
grep -r "ActivitiesLog::create" app/

# Check for pending migrations
php artisan migrate:status | grep Pending

# Check for ActivitiesLog missing task_status/pin
grep -r "new ActivitiesLog" app/Http/Controllers/ | grep -v "task_status"
grep -r "new.*ActivitiesLog" app/Http/Controllers/ | grep -v "task_status"

# Check for Note creation missing pin/folloup/status
grep -r "new Note" app/Http/Controllers/ | grep -v "pin"
grep -r "\$.*= new Note;" app/Http/Controllers/

# Check for direct request field access (may need null coalescing)
grep -r "\$request->[a-zA-Z_]*;" app/Http/Controllers/ | grep -v "??"
grep -r "->[a-zA-Z_]* = \$request->" app/Http/Controllers/

# Check for comparison without isset check
grep -r "!== \$request->" app/Http/Controllers/
grep -r "== \$request->" app/Http/Controllers/ | grep -v "isset"
```

---

## Quick Reference Table

| MySQL Syntax | PostgreSQL Syntax | Safety Level | Notes |
|-------------|-------------------|--------------|-------|
| `DATE_FORMAT(date, '%Y-%m')` | `TO_CHAR(date, 'YYYY-MM')` | 🔴 Critical | Must convert format codes |
| `STR_TO_DATE(str, '%d/%m/%Y')` | `TO_DATE(str, 'DD/MM/YYYY')` | 🔴 Critical | Different format syntax |
| `GROUP_CONCAT(col)` | `STRING_AGG(col, ', ')` | 🔴 Critical | Requires delimiter |
| `column != '0000-00-00'` | `column IS NOT NULL` | 🔴 Critical | PostgreSQL rejects invalid dates |
| `column = '0000-00-00'` | `column IS NULL` | 🔴 Critical | Same as above |
| `ORDER BY col DESC` | `ORDER BY col DESC NULLS LAST` | 🟡 Medium | For consistency with NULL dates |
| `IFNULL(expr, default)` | `COALESCE(expr, default)` | 🟢 Low | COALESCE works in both |
| `CONCAT(a, b)` | `a \|\| b` | 🟢 Low | Both work, `\|\|` preferred |
| `Model::create([...])` missing NOT NULL fields | Add all NOT NULL fields to array | 🔴 Critical | PostgreSQL rejects NULL in NOT NULL columns |
| `new Model; $obj->save()` missing NOT NULL | Set `$obj->not_null_field = value;` before save | 🔴 Critical | Same as above |
| `ActivitiesLog::create()` missing `task_status`/`pin` | Add `'task_status' => 0, 'pin' => 0` | 🔴 Critical | activities_logs table |
| `ClientEmail::create()` missing `is_verified` | Add `'is_verified' => false` | 🔴 Critical | client_emails table |
| `ClientContact::create()` missing `is_verified` | Add `'is_verified' => false` | 🔴 Critical | client_contacts table |
| `ClientQualification::create()` missing `specialist_education`/`stem_qualification`/`regional_study` | Add `'specialist_education' => 0, 'stem_qualification' => 0, 'regional_study' => 0` | 🔴 Critical | client_qualifications table |
| `ClientExperience::create()` missing `fte_multiplier` | Add `'fte_multiplier' => 1.00` | 🔴 Critical | client_experiences table |
| `ClientMatter` creation missing `matter_status` | Add `$matter->matter_status = 1;` before save (1 = active) | 🔴 Critical | client_matters table |
| `new ActivitiesLog` missing `task_status`/`pin` | Add `$objs->task_status = 0; $objs->pin = 0;` before save | 🔴 Critical | activities_logs table |
| `DB::table('admins')->insert()` missing `verified` | Add `'verified' => 0` (for new leads/clients) | 🔴 Critical | admins table |
| `DB::table('admins')->insert()` password empty string | Use `'password' => Hash::make('LEAD_PLACEHOLDER')` | 🔴 Critical | admins table - PostgreSQL rejects empty strings for NOT NULL |
| `DB::table('admins')->insert()` missing `show_dashboard_per` | Add `'show_dashboard_per' => 0` (for new leads/clients) | 🔴 Critical | admins table |
| `DB::table('admins')->insert()` missing `cp_status`/`cp_code_verify` | Add `'cp_status' => 0, 'cp_code_verify' => 0` | 🔴 Critical | admins table - Database defaults not applied with explicit column lists |
| `DB::table('admins')->insert()` missing EOI fields | Add `'australian_study' => 0, 'specialist_education' => 0, 'regional_study' => 0` | 🔴 Critical | admins table - Database defaults not applied with explicit column lists |
| Code references column that doesn't exist | Run pending migration: `php artisan migrate --path=database/migrations/YYYY_MM_DD_HHMMSS_name.php` | 🔴 Critical | Check `php artisan migrate:status` for pending migrations |
| Missing form field accessed directly | Use null coalescing: `$obj->field = $request->field ?? default_value;` | 🔴 Critical | Prevents undefined index warnings and NULL constraint violations |
| Update logic comparing undefined field | Check `isset($request->field)` before comparing | 🔴 Critical | Prevents undefined index warnings in change tracking |
| Database save without error handling | Wrap in try-catch and log errors | 🟡 Medium | Improves debugging and provides better error messages |
| `new Note` missing `pin`/`folloup`/`status` | Add `$obj->pin = 0; $obj->folloup = 0; $obj->status = '0';` before save | 🔴 Critical | notes table - PostgreSQL NOT NULL constraints |

---

## Migration Checklist

When pulling new code from MySQL, check for:

- [ ] Any date comparisons with `'0000-00-00'` → Change to NULL checks
- [ ] `DATE_FORMAT()` → Change to `TO_CHAR()` with updated format codes
- [ ] `GROUP_CONCAT()` → Change to `STRING_AGG()` with delimiter
- [ ] VARCHAR date comparisons → Use `TO_DATE()` for proper comparison
- [ ] `ORDER BY` with date columns → Consider adding `NULLS LAST`
- [ ] `IFNULL()` → Consider `COALESCE()` (both work, but COALESCE is standard)
- [ ] Any raw SQL queries using MySQL-specific functions
- [ ] `Model::create([...])` calls → Verify all NOT NULL columns are included
- [ ] `new Model` followed by `->save()` → Verify all NOT NULL properties are set before save
- [ ] `ActivitiesLog::create()` → Verify `task_status` and `pin` are included
- [ ] `new ActivitiesLog` followed by `->save()` → Verify `task_status` and `pin` are set before save
- [ ] `ClientEmail::create()` → Verify `is_verified` is included
- [ ] `ClientContact::create()` → Verify `is_verified` is included
- [ ] `ClientQualification::create()` → Verify `specialist_education`, `stem_qualification`, and `regional_study` are included
- [ ] `ClientExperience::create()` → Verify `fte_multiplier` is included
- [ ] `ClientMatter` creation → Verify `matter_status` is set to `1` (active) before save
- [ ] `new ActivitiesLog` followed by `->save()` → Verify `task_status` and `pin` are set (both default to `0`)
- [ ] `DB::table('admins')->insert()` or `insertGetId()` → Verify `verified` is included (use `0` for new leads/clients)
- [ ] `DB::table('admins')->insert()` or `insertGetId()` → Verify `password` is included (use `Hash::make('LEAD_PLACEHOLDER')` for leads, not empty string)
- [ ] `DB::table('admins')->insert()` or `insertGetId()` → Verify `show_dashboard_per` is included (use `0` for new leads/clients)
- [ ] `DB::table('admins')->insert()` or `insertGetId()` → Verify client portal fields are included (`'cp_status' => 0, 'cp_code_verify' => 0`)
- [ ] `DB::table('admins')->insert()` or `insertGetId()` → Verify EOI qualification fields are included (`'australian_study' => 0, 'specialist_education' => 0, 'regional_study' => 0`)
- [ ] Check other models for NOT NULL columns with defaults that need explicit values
- [ ] **IMPORTANT:** Database defaults (`default()` in migrations) are NOT applied when using `DB::table()->insert()` with explicit column lists. Always provide explicit values for NOT NULL columns.
- [ ] Check for empty strings `''` being used for NOT NULL string columns - PostgreSQL may reject them
- [ ] **Check for pending migrations:** Run `php artisan migrate:status` after pulling new code
- [ ] If you see "Undefined column" errors, check for pending migrations that add those columns
- [ ] Verify model's `$fillable` array matches actual database schema
- [ ] Run specific pending migrations if needed: `php artisan migrate --path=database/migrations/YYYY_MM_DD_HHMMSS_name.php`
- [ ] **Check for missing form fields:** Use null coalescing operator (`??`) when accessing `$request->field` that may not exist
- [ ] **Update logic:** Check `isset($request->field)` before comparing values in change tracking
- [ ] **Error handling:** Wrap database save operations in try-catch blocks for better error handling
- [ ] **Form verification:** Check form views to see which fields are actually submitted (different forms may submit different fields)
- [ ] **ActivitiesLog creation:** Verify `task_status` and `pin` are set before `save()` when using `new ActivitiesLog`
- [ ] **ActivitiesLog pattern:** Use `task_status = 0` and `pin = 0` for regular activities, `task_status = 1` only for task completions
- [ ] **Note creation:** Verify `pin`, `folloup`, and `status` are set before `save()` when using `new Note`
- [ ] **Note pattern:** Use `pin = 0`, `folloup = 0`, `status = '0'` for new regular notes (only when `!$isUpdate`)

---

## Additional Notes

### Date Storage Format
- Many date fields in this codebase are stored as VARCHAR in `dd/mm/yyyy` format
- Always use `TO_DATE()` when comparing these fields in WHERE clauses
- When inserting/updating, convert from `dd/mm/yyyy` (user input) to `Y-m-d` (database format) in PHP before saving

### NULL vs Empty String
- PostgreSQL treats NULL and empty string differently
- Use `IS NULL` / `IS NOT NULL` for NULL checks
- Use `= ''` / `!= ''` for empty string checks
- Use `COALESCE()` to handle NULL values in expressions

### Performance Considerations
- `TO_DATE()` in WHERE clauses can prevent index usage - consider storing dates as DATE type if possible
- `STRING_AGG()` with DISTINCT may be slower than GROUP_CONCAT on large datasets
- Consider creating functional indexes for frequently used TO_DATE() conversions

### Testing
- Always test date range queries with edge cases (NULL dates, invalid formats)
- Test string aggregation with NULL values
- Verify ORDER BY behavior with NULL values matches expected user experience

---

**Last Updated:** Based on migration work completed during MySQL to PostgreSQL transition
**Status:** Reference guide for ongoing code pulls from MySQL source
