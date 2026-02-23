# Plan: Unify Lead Reference Tables into `lead_matter_references`

## Executive Summary

Merge the five visa-type-specific lead reference tables (`lead_tr_references`, `lead_visitor_references`, `lead_student_references`, `lead_pr_references`, `lead_employer_sponsored_references`) into a single `lead_matter_references` table with a `type` discriminator column. This mirrors the existing pattern used for `client_matter_references`.

---

## Current State

### Tables (all have identical schema)

| Table | Purpose |
|-------|---------|
| `lead_tr_references` | TR Sheet Checklist tab – leads in follow-up queue |
| `lead_visitor_references` | Visitor Visa Sheet Checklist tab |
| `lead_student_references` | Student Visa Sheet Checklist tab |
| `lead_pr_references` | PR Application Sheet Checklist tab |
| `lead_employer_sponsored_references` | Employer Sponsored Visa Sheet Checklist tab |

### Schema (identical across all five)

```php
- id (bigint, PK)
- lead_id (bigint, FK → admins) -- lead appears in Checklist tab
- matter_id (bigint, FK → matters)
- checklist_sent_at (date, nullable)
- created_by (bigint nullable, FK → staff)
- updated_by (bigint nullable, FK → staff)
- created_at, updated_at
- UNIQUE(lead_id, matter_id)
```

### Usage Points

| Location | What it does |
|----------|---------------|
| `config/sheets/visa_types.php` | Each visa type has `lead_reference_table` key |
| `app/Services/VisaSheetService.php` | `recordLeadChecklistSent()` inserts/updates in config-driven table |
| `app/Http/Controllers/CRM/VisaTypeSheetController.php` | `buildChecklistTabWithLeads()` queries leads from config-driven table for Checklist tab |

---

## Proposed New Schema

### `lead_matter_references` table

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint PK | Auto-increment |
| `type` | string(50), indexed | `tr`, `visitor`, `student`, `pr`, `employer-sponsored` |
| `lead_id` | bigint FK | → `admins` (lead) |
| `matter_id` | bigint FK | → `matters` |
| `checklist_sent_at` | date nullable | When checklist was sent |
| `created_by` | bigint nullable FK | → `staff` |
| `updated_by` | bigint nullable FK | → `staff` |
| `created_at`, `updated_at` | timestamps | |

### Indexes

- `(type, lead_id, matter_id)` – unique constraint (business key)
- `type` – index for filtering by visa type
- `lead_id`, `matter_id` – indexes (Laravel foreign keys add these)
- Foreign keys: `lead_id` → `admins`, `matter_id` → `matters`, `created_by` / `updated_by` → `staff`

---

## Implementation Plan

### Phase 1: Database Migration

1. **Create migration** `YYYY_MM_DD_HHMMSS_create_lead_matter_references_and_migrate.php`:
   - Create `lead_matter_references` with schema above
   - Add unique constraint: `(type, lead_id, matter_id)`
   - Map old tables to type values:
     - `lead_tr_references` → `tr`
     - `lead_visitor_references` → `visitor`
     - `lead_student_references` → `student`
     - `lead_pr_references` → `pr`
     - `lead_employer_sponsored_references` → `employer-sponsored`
   - For each old table: if it exists, migrate rows with the correct `type`, then drop it
   - Handle fresh installs where the original migration may have already created the five tables

2. **Modify original migration** `2026_02_20_100000_create_lead_visa_checklist_references_tables.php`:
   - **Option A (recommended)**: Do **not** modify. Run the new migration after it. The new migration will migrate data and drop the five tables. If this is a fresh install, the five tables will be created by the original migration, then immediately migrated and dropped by the new one.
   - **Option B**: Update the original migration to create `lead_matter_references` instead of the five tables (only if the original migration has never run in production).

### Phase 2: Config Changes

3. **Update `config/sheets/visa_types.php`**:
   - Replace `lead_reference_table` with a single value: `lead_matter_references`
   - Add or ensure `reference_type` / `lead_reference_type` for filtering (already exists as `reference_type` with values: `tr`, `visitor`, `student`, `pr`, `employer-sponsored`)
   - Config change: each visa type keeps `reference_type` and the unified table is shared:
     ```php
     'lead_reference_table' => 'lead_matter_references',
     'lead_reference_type' => 'tr',  // or visitor, student, pr, employer-sponsored
     ```

### Phase 3: Code Changes

4. **`app/Services/VisaSheetService.php`** – `recordLeadChecklistSent()`:
   - Use `lead_matter_references` table
   - Add `type` column to insert/update with the visa type from config
   - Change unique lookup from `(lead_id, matter_id)` to `(type, lead_id, matter_id)`
   - Insert: include `'type' => $refType` where `$refType` comes from `$config['reference_type']` or sheet type

5. **`app/Http/Controllers/CRM/VisaTypeSheetController.php`** – `buildChecklistTabWithLeads()`:
   - Use `lead_matter_references` for all visa types
   - Add `WHERE lr.type = ?` (or equivalent) using `$config['reference_type']` when querying
   - Remove per-visa-type table switching; single table, filter by `type`

### Phase 4: Model (Optional)

6. **Create `app/Models/LeadMatterReference.php`** (optional but recommended):
   - Mirror `ClientMatterReference` structure
   - `scopeOfType($query, $type)` for filtering
   - Relationships: `lead()` → Admin, `matter()` → Matter
   - Use for future refactoring; current code uses `DB::table()` so not strictly required initially

### Phase 5: Cleanup & Validation

7. **Drop old tables** in the migration (already part of Phase 1)
8. **Verify** no remaining references to the five old table names in code
9. **Run** Checklist tab for each visa type to confirm leads display correctly
10. **Test** `recordLeadChecklistSent()` for a lead in each visa type

---

## Migration Pseudocode

```php
// In migration up():
$oldTables = [
    'tr' => 'lead_tr_references',
    'visitor' => 'lead_visitor_references',
    'student' => 'lead_student_references',
    'pr' => 'lead_pr_references',
    'employer-sponsored' => 'lead_employer_sponsored_references',
];

Schema::create('lead_matter_references', function (Blueprint $t) {
    $t->id();
    $t->string('type', 50)->index();
    $t->unsignedBigInteger('lead_id')->index();
    $t->unsignedBigInteger('matter_id')->index();
    $t->date('checklist_sent_at')->nullable();
    $t->unsignedBigInteger('created_by')->nullable();
    $t->unsignedBigInteger('updated_by')->nullable();
    $t->timestamps();

    $t->foreign('lead_id')->references('id')->on('admins')->onDelete('cascade');
    $t->foreign('matter_id')->references('id')->on('matters')->onDelete('cascade');
    $t->foreign('created_by')->references('id')->on('staff')->onDelete('set null');
    $t->foreign('updated_by')->references('id')->on('staff')->onDelete('set null');

    $t->unique(['type', 'lead_id', 'matter_id'], 'lead_matter_ref_type_lead_matter_unique');
});

foreach ($oldTables as $type => $tableName) {
    if (!Schema::hasTable($tableName)) continue;
    $rows = DB::table($tableName)->get();
    foreach ($rows as $row) {
        DB::table('lead_matter_references')->insert([
            'type' => $type,
            'lead_id' => $row->lead_id,
            'matter_id' => $row->matter_id,
            'checklist_sent_at' => $row->checklist_sent_at,
            'created_by' => $row->created_by,
            'updated_by' => $row->updated_by,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
        ]);
    }
    Schema::dropIfExists($tableName);
}
```

---

## Config Change Summary

**Before** (per visa type):
```php
'tr' => [
    'lead_reference_table' => 'lead_tr_references',
    ...
],
'visitor' => [
    'lead_reference_table' => 'lead_visitor_references',
    ...
],
// etc.
```

**After** (unified):
```php
'tr' => [
    'lead_reference_table' => 'lead_matter_references',
    'reference_type' => 'tr',  // already exists
    ...
],
'visitor' => [
    'lead_reference_table' => 'lead_matter_references',
    'reference_type' => 'visitor',
    ...
],
// etc. - all share same table, filter by reference_type
```

Code already uses `reference_type` for `client_matter_references`; the controller will add `WHERE type = {reference_type}` when querying `lead_matter_references`.

---

## Query Change in buildChecklistTabWithLeads()

**Before:**
```php
$leadRefTable = $config['lead_reference_table'];  // e.g. lead_tr_references
$leadQuery = DB::table($leadRefTable . ' as lr')
    ->join('admins as a', 'lr.lead_id', '=', 'a.id')
    ->join('matters as m', 'lr.matter_id', '=', 'm.id')
    ->whereIn('lr.matter_id', $matterIds)
    ...
```

**After:**
```php
$leadRefTable = 'lead_matter_references';  // or $config['lead_reference_table']
$refType = $config['reference_type'] ?? $visaType;
$leadQuery = DB::table($leadRefTable . ' as lr')
    ->where('lr.type', $refType)
    ->join('admins as a', 'lr.lead_id', '=', 'a.id')
    ->join('matters as m', 'lr.matter_id', '=', 'm.id')
    ->whereIn('lr.matter_id', $matterIds)
    ...
```

---

## Risks & Mitigations

| Risk | Mitigation |
|------|------------|
| Data loss during migration | Migration runs in transaction; test on staging with production-like data |
| Unique constraint violations | Old tables had per-table uniqueness; new table has (type, lead_id, matter_id). Same lead+matter cannot appear in two visa types – safe. |
| Config typo | Use `reference_type` which already exists and is validated by sheet routing |

---

## Out of Scope (for reference)

- **Lead reminders** (`lead_tr_reminders`, etc.): Separate tables, not part of this plan. Could be unified similarly in a future `lead_reminders` table with `type` column if desired.
- **client_matter_references**: Already unified; no changes.

---

## File Checklist

| File | Action |
|------|--------|
| `database/migrations/YYYY_MM_DD_*_create_lead_matter_references_and_migrate.php` | Create (new migration) |
| `config/sheets/visa_types.php` | Update `lead_reference_table` to `lead_matter_references` for all 5 types |
| `app/Services/VisaSheetService.php` | Add `type` to insert/update, use unified table |
| `app/Http/Controllers/CRM/VisaTypeSheetController.php` | Add `WHERE lr.type = ?` when querying lead refs |
| `app/Models/LeadMatterReference.php` | Create (optional) |
