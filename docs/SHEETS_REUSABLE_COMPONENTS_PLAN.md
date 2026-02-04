# Sheets Reusable Components Plan

**Purpose:** Define reusable Blade components and structure for the CRM Sheets section so new sheets (ART, EOI/ROI, TR, and future ones) share a consistent layout while allowing different columns, filters, and content.

**Scope:** Plan only — no code changes applied until you approve.

**Related:** See `TR_SHEET_IMPLEMENTATION_PLAN.md` for TR-specific requirements (checklist box, reminder popup, edit dates modal, AJAX endpoints).

---

## 1. Current State Summary

### 1.1 Existing Sheets

| Sheet     | List View        | Insights View       | Unique Features                                      |
|-----------|------------------|---------------------|------------------------------------------------------|
| ART       | `art.blade.php`  | `art-insights.blade.php` | Status badges, payment columns, office filter in sticky bar |
| EOI/ROI   | `eoi-roi.blade.php` | `eoi-roi-insights.blade.php` | Workflow status badges, verification columns, subclass/state filters, warning rows |
| TR (planned) | `tr.blade.php` | `tr-insights.blade.php` | Dynamic "Checklist & Follow-ups" box, reminder Email/SMS buttons, events list, **Edit Dates** modal, **reminder popup** modal, AJAX-driven row refresh, expiry date highlight |
| Future    | —                | —                    | May have inline edit, custom action columns, etc.    |

### 1.2 Common Structure (What’s Shared)

All sheets currently share:

- Layout: `layouts.crm_client_detail`
- **Top bar:** Title (icon + text), "Back to Clients" button, sheet tabs (List | Insights)
- **Filter bar:** "Filters" button, active filter badge, clear filters link, office filter section, per-page selector
- **Filter panel:** Collapsible form with sheet-specific filter fields (search, status, dates, etc.)
- **Table:** Horizontal scroll container, scroll indicators, sortable headers, bordered table, pagination
- **Empty state:** Centered message when no rows
- **Insights view:** Insight cards grid, breakdown sections, monthly chart
- **Common CSS:** `sheet-tabs`, `sortable`, `filter_panel`, `scroll-indicator`, etc.
- **Common JS:** Per-page change, filter toggle, datepicker, sortable click, office filter auto-submit, scroll indicators

### 1.3 What Varies Per Sheet

- **Columns:** Number, labels, order, sortability, cell content (links, badges, dates, custom blocks), cell styling (e.g. TR expiry date — subtle yellow/cream highlight)
- **Filters:** Fields (search, status, agent, dates, subclass, state, matter type, follow-up status, rec_date range, expiry range, etc.)
- **Insights:** Metrics, breakdowns, chart data (TR: total records, expiring in 7/30 days, by matter type; no monthly grouping in charts)
- **Row behavior:** Row classes (e.g. `has-warning`), client URL params (e.g. `tab=eoiroi`)
- **Custom cells:** e.g. TR’s "Checklist & Follow-ups" box, EOI’s workflow status
- **Modals & AJAX:** TR needs reminder popup (preview → edit → send), Edit Dates modal (PATCH), and AJAX-driven checklist box refresh after send

---

## 2. Proposed Component Architecture

### 2.1 Directory Structure

```
resources/views/crm/clients/sheets/
├── components/                    # Reusable sheet components
│   ├── sheet-layout.blade.php     # Main layout shell (card, tabs, filter bar)
│   ├── sheet-tabs.blade.php       # List | Insights tabs
│   ├── sheet-filter-bar.blade.php # Filters button, clear, office filter, per-page
│   ├── sheet-filter-panel.blade.php # Collapsible filter form (slots for fields)
│   ├── sheet-table.blade.php      # Table wrapper (scroll, indicators, thead/tbody via slots)
│   ├── sheet-table-header.blade.php # Single sortable th (or plain th)
│   ├── sheet-pagination.blade.php # Pagination footer
│   ├── sheet-empty-state.blade.php # No records message
│   └── sheet-insights-layout.blade.php # Insights shell (tabs, filter alert, slots)
│   ├── tr-checklist-box.blade.php # TR-only: dates, events, reminder buttons, Edit link
│   ├── tr-reminder-modal.blade.php # TR-only: preview/edit/send reminder popup
│   └── sheet-edit-modal.blade.php # Optional: generic inline-edit modal (TR uses for Edit Dates)
│
├── partials/                      # Optional: shared partials for complex blocks
│   ├── office-filter-form.blade.php
│   └── scroll-indicators.blade.php
│
├── art.blade.php
├── art-insights.blade.php
├── eoi-roi.blade.php
├── eoi-roi-insights.blade.php
├── tr.blade.php
└── tr-insights.blade.php
```

### 2.2 Shared CSS & JS

Move duplicated sheet styles/scripts into shared assets:

- **`public/css/sheets.css`** — All common sheet styles (tabs, filter panel, table, sortable, scroll indicators, insight cards, breakdown sections, etc.). Include TR checklist box and modal styles.
- **`public/js/sheets.js`** — Common sheet JS (scroll indicators, per-page, filter toggle, sortable, office filter, datepicker init).
- **`public/js/tr-sheet.js`** — TR-only: reminder modal (preview, edit, send), edit dates modal, checklist box AJAX refresh. Depends on sheets.js.

Each sheet view includes `sheets.css` + `sheets.js`; TR adds `tr-sheet.js`. Date format: **d/m/Y** (shared standard).

---

## 3. Component Specifications

### 3.1 `sheet-layout.blade.php`

**Purpose:** Main shell for a sheet list or insights view.

**Props:**
- `title` (string) — e.g. "ART Submission and Hearing Files"
- `titleIcon` (string, default `fa-list`) — Font Awesome class
- `baseRoute` (string) — Base route name without `.insights` (e.g. `clients.sheets.art`)
- `insightsRoute` (string) — Full insights route name
- `showInsightsTab` (bool, default true)
- `stickyHeader` (bool, default false) — ART uses sticky; EOI doesn’t

**Slots:**
- `tabs` (optional) — Override default sheet tabs
- `filterBar` — Filter bar content
- `default` — Main content (table, insights, etc.)

**Usage:**
```blade
<x-crm.sheets.components.sheet-layout
    title="ART Submission and Hearing Files"
    titleIcon="fa-gavel"
    :baseRoute="'clients.sheets.art'"
    :insightsRoute="'clients.sheets.art.insights'"
    :stickyHeader="true"
>
    <x-slot:filterBar>
        {{-- Filter bar content --}}
    </x-slot>
    {{-- Table or main content --}}
</x-crm.sheets.components.sheet-layout>
```

---

### 3.2 `sheet-tabs.blade.php`

**Purpose:** List | Insights tabs, shared styling.

**Props:**
- `listRoute` (string)
- `insightsRoute` (string)
- `active` ('list' | 'insights')
- `listLabel` (default "List")
- `insightsLabel` (default "Insights")

---

### 3.3 `sheet-filter-bar.blade.php`

**Purpose:** Bar with Filters button, clear filters, office filter section, per-page selector.

**Props:**
- `filterFormRoute` (string) — Route for filter form action
- `clearRoute` (string) — Route for "Clear Filters"
- `officeFilterRoute` (string) — Same as filter form route usually
- `activeFilterCount` (int)
- `perPage` (int)
- `perPageOptions` (array, default [10, 25, 50, 100, 200])
- `preserveQueryParams` (array) — Keys to preserve in hidden inputs (e.g. `per_page`)

**Slots:**
- `beforeOffice` (optional) — Content before office filter
- `afterOffice` (optional) — Content after office filter

**Notes:** Office filter uses `Branch::orderBy('office_name')->get()`; consider passing offices as prop or using `@inject` for consistency.

---

### 3.4 `sheet-filter-panel.blade.php`

**Purpose:** Collapsible filter form panel.

**Props:**
- `action` (string) — Form action URL
- `show` (bool) — Whether panel is expanded by default
- `method` (string, default "get")

**Slots:**
- `default` — Filter fields (rows of form groups)
- `submit` (optional) — Override submit button block
- `reset` (optional) — Override reset link

---

### 3.5 `sheet-table.blade.php`

**Purpose:** Table wrapper with horizontal scroll, scroll indicators, consistent styling.

**Props:**
- `tableId` (string) — e.g. `art-sheet-table`
- `tableClass` (string, optional) — e.g. `art-table`
- `scrollContainerId` (string, default `table-scroll-container`)

**Slots:**
- `header` — `<thead><tr>...</tr></thead>`
- `body` — `<tbody>...</tbody>`
- `emptyColspan` (int, optional) — Colspan for empty state row

---

### 3.6 `sheet-table-header.blade.php`

**Purpose:** Single table header cell, optionally sortable.

**Props:**
- `label` (string)
- `sortKey` (string|null) — If null, not sortable
- `currentSort` (string)
- `currentDirection` (string, default 'desc')
- `class` (string, optional)

**Output:** `<th class="sortable asc|desc" data-sort="...">Label</th>` or `<th>Label</th>`.

---

### 3.7 `sheet-pagination.blade.php`

**Purpose:** Pagination links below table.

**Props:**
- `paginator` (LengthAwarePaginator)
- `appends` (array) — Query params to preserve (default: `Request::except('page')`)

---

### 3.8 `sheet-empty-state.blade.php`

**Purpose:** Centered "No records found" message.

**Props:**
- `colspan` (int)
- `message` (string) — e.g. "No ART records found. Add an ART matter type..."
- `icon` (string, default `fa-info-circle`)

---

### 3.9 `sheet-insights-layout.blade.php`

**Purpose:** Layout for insights view: card, tabs, filter alert, slots for insight cards and breakdowns.

**Props:**
- `title` (string)
- `titleIcon` (string, default `fa-chart-bar`)
- `listRoute` (string)
- `insightsRoute` (string)
- `activeFilterCount` (int)
- `clearFiltersRoute` (string)

**Slots:**
- `filterAlert` (optional) — Override default "Showing insights for filtered data" alert
- `insightCards` — Grid of insight cards
- `breakdowns` — Breakdown sections (by status, by agent, etc.)
- `chart` (optional) — Monthly chart or other chart block

---

### 3.10 Column Definition Pattern (Data Structure)

To keep the table flexible, each sheet’s controller can pass a **column definition array** to the view:

```php
// Example: ArtSheetController
$columns = [
    [
        'key' => 'crm_ref',
        'label' => 'CRM Ref',
        'sortable' => true,
        'sortField' => 'crm_ref',
        'render' => 'link', // or 'text', 'date', 'badge', 'custom'
        'linkRoute' => 'clients.detail',
        'linkParams' => ['client_id' => 'encoded_client_id', 'client_unique_matter_ref_no' => 'matter_id'],
        'valueKey' => 'crm_ref',
    ],
    [
        'key' => 'status',
        'label' => 'Status of the File',
        'sortable' => true,
        'render' => 'badge',
        'valueKey' => 'status_of_file',
        'badgeMap' => ['submission_pending' => 'secondary', 'submission_done' => 'success', ...],
        'labelMap' => ['submission_pending' => 'Submission Pending', ...],
    ],
    [
        'key' => 'comments',
        'label' => 'Comments',
        'sortable' => false,
        'render' => 'text',
        'valueKey' => 'comments',
        'limit' => 80,
        'cellClass' => 'art-comments-cell',
    ],
    // TR-specific: custom block
    [
        'key' => 'checklist_box',
        'label' => 'Checklist & Follow-ups',
        'sortable' => false,
        'render' => 'component',
        'component' => 'crm.clients.sheets.components.tr-checklist-box',
        'row' => null, // passed per row
    ],
];
```

**Render types:**
- `text` — Plain text, optional `limit`
- `link` — Link to client detail (or other route)
- `date` — Formatted date (d/m/Y)
- `badge` — Status badge with optional mapping
- `component` — Blade component with `row` prop
- `raw` — Raw HTML (use sparingly)

**Alternative:** Instead of a generic column renderer, keep **per-sheet row partials** (e.g. `art-row.blade.php`, `eoi-roi-row.blade.php`, `tr-row.blade.php`). The table body loops over rows and includes the appropriate partial. This is simpler and allows full control per sheet without a complex column engine.

**Recommendation:** Use **row partials** for now. They are easier to maintain and allow sheet-specific logic (e.g. TR’s checklist box, EOI’s workflow status). The reusable parts are the **layout components** (layout, tabs, filter bar, table wrapper, pagination, empty state), not the cell rendering.

---

### 3.11 TR-Specific Components (see TR_SHEET_IMPLEMENTATION_PLAN.md)

**`tr-checklist-box.blade.php`**

- **Purpose:** One combined cell per TR row showing dates, events, and action buttons.
- **Props:** `row` (TR reference with eager-loaded `reminderEvents`), `clientTrReferenceId`, `clientId`.
- **Sections:**
  - **Dates:** rec_date, last_date, checklist_send_date, first_reminder_date (F1), second_reminder_date (F2), call_date; display "—" or "NP" for empty.
  - **Events:** List of reminder events (e.g. "Reminder 1 email sent 27.11.2025 (by Staff A)"); show latest 3–5, "+ N more" if many.
  - **Actions:** "Reminder 1 – Email", "Reminder 1 – SMS", "Reminder 2 – Email", "Reminder 2 – SMS" (button visibility per TR plan logic); "Edit Dates" link.
- **Button visibility:** Reminder 1 if rec_date or checklist_send_date set; Reminder 2 if at least one Reminder 1 event exists.
- **AJAX refresh:** After send, frontend can re-fetch checklist HTML or row fragment and replace; or full page reload. TR plan recommends append event without full reload.

**`tr-reminder-modal.blade.php`**

- **Purpose:** Bootstrap modal for reminder preview → edit → send.
- **Flow:** On open, AJAX to `clients.sheets.tr.reminder-preview` (params: `client_tr_reference_id`, `reminder_number`, `channel`); populate subject (email only) and body; staff edits; on Send, POST to `clients.sheets.tr.send-reminder`; on success, close modal, refresh checklist box or row.
- **Uses:** SweetAlert2 or Bootstrap modal; same UX as EOI for success/error toasts.

**`sheet-edit-modal.blade.php` (optional)**

- **Purpose:** Generic modal for inline edit of dates/fields. TR uses for "Edit Dates" — rec_date, last_date, checklist_send_date, first_reminder_date, second_reminder_date, call_date, docs_requested, comments.
- **Flow:** PATCH to `clients.sheets.tr.update`; on success, refresh row or table.
- **Reusability:** Could be generalized for other sheets that add inline edit later.

**TR JS:** `public/js/tr-sheet.js` or inline in `tr.blade.php` — reminder modal open/fetch/send, edit dates modal, checklist box refresh. Depends on `sheets.js` for common behavior.

---

## 4. Implementation Phases

### Phase 1: Shared Assets

1. Create `public/css/sheets.css` — extract common styles from `art.blade.php` and `eoi-roi.blade.php`.
2. Create `public/js/sheets.js` — extract common scripts (scroll indicators, per-page, filter toggle, sortable, office filter).
3. Include these in the layout or per-sheet `@section('styles')` / `@push('scripts')`.

### Phase 2: Layout Components

1. Create `sheet-layout.blade.php` (with optional `sheet-tabs` included).
2. Create `sheet-tabs.blade.php`.
3. Create `sheet-filter-bar.blade.php` and `office-filter-form` partial if needed.
4. Create `sheet-filter-panel.blade.php`.
5. Create `sheet-pagination.blade.php` and `sheet-empty-state.blade.php`.

### Phase 3: Table Components

1. Create `sheet-table.blade.php` (wrapper with scroll container and indicators).
2. Create `sheet-table-header.blade.php` (sortable/plain th).
3. Refactor `art.blade.php` to use these components (proof of concept).

### Phase 4: Insights Components

1. Create `sheet-insights-layout.blade.php`.
2. Create optional sub-components: `insight-card.blade.php`, `breakdown-section.blade.php`, `monthly-chart.blade.php`.
3. Refactor `art-insights.blade.php` to use them.

### Phase 5: EOI/ROI and TR Migration

1. Refactor `eoi-roi.blade.php` and `eoi-roi-insights.blade.php` to use the same components.
2. **TR sheet** (build on reusable components from the start):
   - Create `tr.blade.php` and `tr-insights.blade.php` using `sheet-layout`, `sheet-tabs`, `sheet-filter-bar`, `sheet-filter-panel`, `sheet-table`, `sheet-pagination`, `sheet-empty-state`, `sheet-insights-layout`.
   - Create `tr-checklist-box.blade.php`, `tr-reminder-modal.blade.php`, `sheet-edit-modal.blade.php`.
   - Create `tr-sheet.js` for reminder modal, edit modal, checklist box refresh.
   - TR filters: matter type, expiry from/to, rec_date from/to, last_date from/to, checklist send from/to, search (name, CRM ref, docs_requested, comments), optional follow-up status.
   - TR insights: total records, expiring in 7/30 days, by matter type (no monthly chart; see TR plan).

---

## 5. Per-Sheet Customization Points

| Customization      | How to Handle                                                       |
|--------------------|---------------------------------------------------------------------|
| Different columns  | Each sheet defines its own `<thead>` and row partial/loop           |
| Different filters  | `sheet-filter-panel` slot with sheet-specific fields                |
| Different insights | `sheet-insights-layout` slots with sheet-specific cards/breakdowns   |
| TR checklist box   | `tr-checklist-box` component included in TR row partial             |
| TR modals          | `tr-reminder-modal`, `sheet-edit-modal` (Edit Dates)                |
| Cell highlight     | In row partial (e.g. TR expiry date: `td.sheet-cell-expiry`)         |
| Sticky vs normal   | `sheet-layout` prop `stickyHeader`                                  |
| Row CSS class      | In row partial (e.g. `has-warning` for EOI)                         |
| Client link params | In row partial (e.g. `tab=eoiroi` for EOI)                          |

---

## 6. Controller Contract (Optional Standardization)

To keep controllers consistent:

- **List view:** Pass `rows`, `perPage`, `activeFilterCount`, plus any dropdown/options (agents, statuses, offices).
- **Insights view:** Pass `insights` (array), `activeFilterCount`.
- **Filter routes:** Use same route for filter form and clear (with/without params).
- **Sort params:** `sort`, `direction` in query string.
- **Office filter:** Preserve other params when office changes.
- **TR extra methods:** `reminderPreview`, `sendReminder`, `updateReference` (see TR plan §4–5).

No mandatory base class; each controller can remain independent but follow this convention.

---

## 7. Summary

| Component               | Reusable? | Notes                                                |
|-------------------------|-----------|------------------------------------------------------|
| Sheet layout shell      | Yes       | Card, tabs, back button                              |
| Sheet tabs              | Yes       | List \| Insights                                     |
| Filter bar              | Yes       | Filters button, clear, office, per-page              |
| Filter panel            | Yes       | Collapsible form with slots                          |
| Table wrapper           | Yes       | Scroll container, indicators                         |
| Table header (th)       | Yes       | Sortable or plain                                    |
| Table body / rows       | Per-sheet | Each sheet uses its own thead + row partial          |
| Pagination              | Yes       | Standard Laravel paginator                           |
| Empty state             | Yes       | Colspan, message, icon                               |
| Insights layout         | Yes       | Tabs, alert, slots for cards/breakdowns/chart        |
| Insight card            | Yes       | Optional sub-component                               |
| Breakdown section       | Yes       | Optional sub-component                               |
| TR checklist box        | TR-only   | Dates, events, reminder buttons, Edit link; AJAX refresh      |
| TR reminder modal       | TR-only   | Preview/edit/send reminder; SweetAlert2 or Bootstrap          |
| Sheet edit modal        | Optional  | Generic inline edit; TR uses for Edit Dates                   |

---

## 8. Implementation Order & Dependencies

- **Reusable components (Phase 1–4)** should be in place before or alongside TR implementation.
- **TR sheet** relies on: `sheet-layout`, `sheet-tabs`, `sheet-filter-bar`, `sheet-filter-panel`, `sheet-table`, `sheet-pagination`, `sheet-empty-state`, `sheet-insights-layout`.
- TR-specific work: `tr-checklist-box`, `tr-reminder-modal`, `sheet-edit-modal`, `tr-sheet.js`, controller (index, insights, reminderPreview, sendReminder, updateReference), routes.
- See `TR_SHEET_IMPLEMENTATION_PLAN.md` §11 for full TR implementation order; it assumes views exist — integrate with this plan so TR views use the reusable components.

---

## 9. Open Questions

1. **Blade component namespace:** Use `x-crm.sheets.components.*` or a shorter alias?
2. **Office filter:** Keep inline in `sheet-filter-bar` or extract to `partials/office-filter-form.blade.php`?
3. **Column definition approach:** Implement generic column renderer later, or stick with row partials only?
4. **Sticky header:** Unify ART and EOI layout (both sticky or both normal), or keep as option?

---

*End of plan. No code changes applied yet.*
