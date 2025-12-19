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
8. [Search Patterns](#search-patterns)

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

**Example from Codebase:**
- **File:** `app/Traits/LogsClientActivity.php`
- **Line 27:** Added `'task_status' => 0,` to ActivitiesLog::create()
- **Files Fixed:** All files creating ActivitiesLog instances (40+ instances across 13 files)

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
- Check migration files to identify which columns have NOT NULL constraints
- PostgreSQL will reject the entire transaction if any NOT NULL constraint is violated

**Common Patterns to Fix:**
- `Model::create([...])` - Add missing NOT NULL fields to the array
- `new Model; $obj->field = value; $obj->save();` - Add `$obj->not_null_field = default_value;` before save
- Mass assignment - Ensure `$fillable` array includes the NOT NULL field in the model

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
