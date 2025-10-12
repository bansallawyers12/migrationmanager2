## EOI / ROI Implementation Plan (CRM)

### Purpose
Design and implement a dedicated EOI/ROI workflow inside the client detail area that:
- Supports multiple EOI/ROI records per client
- Tracks multiple subclasses and multiple states per EOI (arrays)
- Surfaces a unified form, an entries table, and a points summary with upcoming-change warnings
- Only appears when the selected matter is EOI

### Scope note (Reporting later, reporting-ready now)
- Reporting UI (filters/exports/dashboards) is OUT OF SCOPE for this delivery.
- This implementation will be REPORTING-READY by structuring and indexing data so that a reporting page can be added later without refactors.
- See “Reporting readiness checklist” below.

### High-Level UX
1) Client Detail → when active matter is EOI → show a new tab: EOI / ROI
2) Inside the tab:
   - Top: EOI / ROI Entries (table)
     - Columns: EOI Ref, Subclass (comma-separated), Occupation, Points, State(s) (comma-separated), Submission, ROI
     - Click row → loads the EOI into the form
   - Middle: EOI / ROI Form (create/update/delete)
     - Fields: EOI number, subclass (primary), occupation, points, state (primary), submission date, ROI, password, status, (optional: invitation/nomination dates)
     - Arrays: eoi_subclasses[], eoi_states[] maintained and displayed in table; primary fields show first selected
   - Below form: Points Summary
     - Total, breakdown, and warnings (within next N months)

### Data Model
Existing: `client_eoi_references`

Add columns:
- `eoi_subclasses` JSON nullable — canonical list of subclasses (e.g., ["189","491"]) 
- `eoi_states` JSON nullable — canonical list of states (e.g., ["VIC","SA"]) 
- `eoi_invitation_date` DATE nullable
- `eoi_nomination_date` DATE nullable
- `eoi_status` ENUM('draft','submitted','invited','nominated','rejected','withdrawn') default 'draft'

Backwards compatibility:
- Keep scalar `EOI_subclass` and `EOI_state` for compatibility with existing code; set them as the first element of the arrays
- Backfill arrays from existing scalar values during migration

Model casts (`App\Models\ClientEoiReference`):
- `eoi_subclasses: array`
- `eoi_states: array`
- `EOI_submission_date: date`
- `eoi_invitation_date: date`
- `eoi_nomination_date: date`

### Controller & Routes
Routes:
```
POST   /admin/clients/{client}/eoi-roi      → upsert (create/update)
GET    /admin/clients/{client}/eoi-roi/{id} → show
DELETE /admin/clients/{client}/eoi-roi/{id} → destroy
```

Controller: `App\Http\Controllers\Admin\ClientEoiRoiController`
- Validates arrays for subclasses/states
- Normalizes date strings dd/mm/yyyy → Y-m-d
- Writes arrays and sets legacy scalar fields to the first selected item
- Returns JSON payloads for frontend updates

Validation highlights:
- `eoi_subclasses.* ∈ {189,190,491}`
- `eoi_states.* ∈ {ACT,NSW,NT,QLD,SA,TAS,VIC,WA,FED}`
- Numeric `EOI_point`
- Optional `eoi_status ∈ {draft,submitted,invited,nominated,rejected,withdrawn}`

### Points Service
Create `App\Services\PointsService`:
- compute(Admin $client, ?string $selectedSubclass, int $monthsAhead = 6): array
- Output: `{ total, breakdown: [category=>{detail,points}], warnings: [messages...] }`
- Data sources: DOB/age, English test (expiry), skills assessment validity, education, overseas/aus work thresholds, partner, NAATI/CCL, nomination
- Nomination rule: add 5 (190) or 15 (491) to total depending on selected EOI subclass

### View Integration
Parent view: `resources/views/Admin/clients/detail.blade.php`
1) Detect EOI matter in `ClientsController@detail` by joining `client_matters` → `matters` and checking `nick_name == 'eoi'` or title contains 'eoi'
2) When EOI: add sidebar button and include `Admin.clients.tabs.eoi_roi`
3) Hide the compact EOI block in `personal_details` when EOI tab is active

New view: `resources/views/Admin/clients/tabs/eoi_roi.blade.php`
- Entries table (top): matches the demo `public/demo-eoi-roi.html`
- EOI/ROI form (middle): create/update/delete actions via AJAX
- Points Summary (bottom): total + breakdown + warnings

### Frontend Logic
New file: `public/js/clients/eoi-roi.js`
- Stores EOI list state and selected record
- Renders table, form and points summary
- Events: Save (POST), Delete (DELETE), Row select (GET/inline load), input delta sync
- Updates the top selector label (e.g., `VIC, SA • 189, 491 • 85 pts`)
- Warnings threshold configurable (default 6 months)

Security
- Use `auth:admin` guard
- Add policy/gate checks for CRUD
- CSRF token for AJAX

### Migration Strategy
1) Create migration to add JSON arrays and status/dates
2) Backfill eoi_subclasses/eoi_states from existing EOI_subclass/EOI_state
3) Test read paths remain backward-compatible

### Testing
Feature tests
- Create/update with arrays for subclasses/states
- Date normalization behavior and response payloads
- Delete authorization checks

Unit tests
- PointsService totals for 189 vs 190 vs 491
- Warnings when a change is within the next N months

### Rollout Checklist
- Deploy migration
- Deploy controller/routes/service/view/js
- `php artisan route:cache && view:clear && config:cache`
- Staging smoke tests: CRUD EOI, multi subclass/state, points computation, warnings
- Enable for all admins

### Future Enhancements
- Inline editing of subclass/state via modal or tags
- Saved filters and exports for EOI/ROI entries
- Scheduler to notify about upcoming points changes
- State-specific ROI checklists per entry

### References
- Demo prototype: `public/demo-eoi-roi.html`
- Existing EOI model: `App\Models\ClientEoiReference`
- Client detail: `Admin\ClientsController@detail`, `resources/views/Admin/clients/detail.blade.php`

---

## Points Calculation Logic (detailed)

This section specifies how the PointsService will compute the legislated points test figures and raise upcoming-change warnings. Rules reflect the commonly used points test for subclasses 189/190/491 as of 2025; confirm policy updates before release.

### Inputs (from CRM)
- Client profile: DOB (Age), country, marital/partner status
- English test(s): test type, component scores, test date, expiry policy
- Skills assessment: occupation, authority, outcome date, validity end date
- Skilled employment: Australian and Overseas roles, start/end dates, relevance, full-time equivalent
- Education: highest qualification (Doctorate/Masters/Bachelor/Diploma/Trade), Australian study, specialist education (STEM by research), regional study
- Partner: partner age, English evidence, skills assessment, PR/citizenship
- Misc: Professional Year (IT/Accounting/Engineering), Credentialed Community Language (NAATI/CCL)
- Nomination: selected EOI subclass for nomination bonus (190/491)

Recommended DB sources (existing):
- `admins` (DOB), `client_points` (manual overrides), `client_occupations`, `client_qualification(s)`, `client_test_scores`, skills-assessment tables (or `update_assessment_validity_periods` rules), partner tables.

### Scoring Rules (summary)
1) Age (at time of invitation)
   - 18–24: 25
   - 25–32: 30
   - 33–39: 25
   - 40–44: 15
   - ≥45: 0 (ineligible for 189/190; treat as 0 for 491 points as well)

2) English language ability (best current valid test)
   - Competent: 0
   - Proficient: 10
   - Superior: 20
   - Validity: typically 3 years from test date (configurable)

3) Skilled employment (cap combined at 20)
   - Australian experience (in nominated/closely-related occupation, full-time eq.):
     - 1 yr: 5, 3 yrs: 10, 5 yrs: 15, 8 yrs: 20
   - Overseas experience (relevant):
     - 3 yrs: 5, 5 yrs: 10, 8 yrs: 15
   - Combined cap: min(20, AU_points + OS_points)

4) Educational qualifications (highest)
   - Doctorate: 20
   - Bachelor (or equivalent): 15
   - Diploma/Trade: 10
   - Australian Study Requirement (separate): +5 if met
   - Specialist Education (STEM Masters by research/PhD in Australia): +10

5) Credentialed Community Language (NAATI/CCL): +5 (no expiry for points purposes; verify current policy)

6) Professional Year (Accounting/IT/Engineering): +5

7) Study in Regional Australia: +5 (if meets regional study criteria)

8) Partner points (choose one stream only)
   - Partner with skills (age <45, competent English, valid skills assessment in eligible occupation): +10
   - Partner with competent English only: +5
   - Single or partner is Australian citizen/PR: +10

9) Nomination / Sponsorship bonus (based on selected EOI subclass)
   - Subclass 190 (Skilled Nominated): +5
   - Subclass 491 (Skilled Work Regional): +15
   - Subclass 189: +0

Business rules to note:
- Invitation-time rules apply: compute age/validity against invitation date if known; else default to “today” for estimates.
- Experience calculations use full-time-equivalent and only count periods post-qualification per assessing authority rules if applicable. For MVP, rely on “relevant” flags curated by staff.
- Enforce combined employment cap at 20.

### Upcoming-Change Warnings (within N months, default 6)
Emit warning entries when any of the following will occur before threshold date T = today + N months:
1) Age bracket change (e.g., turning 33 or 40 or 45) → show new points after change
2) English test expiry (e.g., 3-year validity) → points could drop to Competent/0
3) Skills assessment expiry → may affect eligibility; add prominent warning
4) Work experience thresholds crossing soon (AU: 1/3/5/8 yrs; OS: 3/5/8 yrs) → show potential next bracket points
5) Partner English/skills expiry (mirrors applicant test/assessment)
6) Any configured policy dates (local config for validity windows)

### Algorithm (pseudo)
```
function computePoints(client, selectedSubclass, monthsAhead = 6):
  invitationDate = client.nextExpectedInvitationDate or now()

  // Age
  ageYears = yearsBetween(client.dob, invitationDate)
  agePoints = case:
    18<=age<=24 -> 25
    25<=age<=32 -> 30
    33<=age<=39 -> 25
    40<=age<=44 -> 15
    else -> 0

  // English (pick best valid test)
  englishLevel = deriveEnglishLevel(client.tests, invitationDate)
  englishPoints = {competent:0, proficient:10, superior:20}[englishLevel]

  // Employment
  auYears = fteYears(client.employment.auRelevant, invitationDate)
  osYears = fteYears(client.employment.osRelevant, invitationDate)
  auPoints = bracket(auYears, [1:5, 3:10, 5:15, 8:20])
  osPoints = bracket(osYears, [3:5, 5:10, 8:15])
  employmentPoints = min(20, auPoints + osPoints)

  // Education & bonuses
  eduPoints = highestQualificationPoints(client.education)
  ausStudy = qualifiesAustralianStudy(client.education)
  specialist = qualifiesSpecialistEducation(client.education)
  regionalStudy = qualifiesRegionalStudy(client.education)
  ccl = hasCCL(client.naati)
  py = hasProfessionalYear(client.py)

  bonusEdu = (ausStudy?5:0) + (specialist?10:0) + (regionalStudy?5:0) + (ccl?5:0) + (py?5:0)

  // Partner
  partnerPts = computePartnerPoints(client.partner)

  // Nomination based on selectedSubclass
  nominationPts = selectedSubclass=="491" ? 15 : (selectedSubclass=="190" ? 5 : 0)

  total = agePoints + englishPoints + employmentPoints + eduPoints + bonusEdu + partnerPts + nominationPts

  warnings = computeUpcomingWarnings(client, monthsAhead, selectedSubclass)

  return { total, breakdown: {
      age: agePoints, english: englishPoints, employment: employmentPoints,
      education: eduPoints, bonuses: bonusEdu, partner: partnerPts, nomination: nominationPts
    }, warnings }
```

Helper rules (examples):
- `deriveEnglishLevel` maps IELTS/PTE/TOEFL/OET scores to Competent/Proficient/Superior and checks validity window (default 3 years).
- `fteYears` computes full-time equivalent to the invitation date, deduping overlapping periods.
- `highestQualificationPoints` picks the single best of Doctorate(20)/Bachelor(15)/Diploma-Trade(10).
- `computePartnerPoints` resolves exclusive options (10 skilled vs 10 single/PR vs 5 competent English only).

### Service Contract
`App\Services\PointsService`
```
public function compute(\App\Admin $client, ?string $selectedSubclass, int $monthsAhead = 6): array
// returns ['total'=>int, 'breakdown'=>array, 'warnings'=>array]
```

Performance
- Use eager loading and pre-aggregation for employment periods to avoid N+1
- Cache results per client+selectedSubclass for 5–15 minutes

### Data Sourcing from Client Edit & Live Calculation on EOI/ROI Page

Goal: The EOI/ROI tab must not require duplicate data entry. It should read from the same sources as the Client Edit page and calculate points/expiries on load and when inputs (e.g., selected subclass) change.

Server-side inputs (pulled in `ClientsController@detail`):
- Client (Admin) core: DOB, marital/partner status, country (from `admins` / personal details)
- English test(s): `ClientTestScore` (type, scores, test_date)
- Employment: `ClientExperience` (country, start/end dates, relevant flag, FTE equivalent)
- Education: `ClientQualification` (level, AU study, specialist/STEM, regional campus)
- Occupation/assessment: `ClientOccupation` (+ any assessment validity fields)
- Partner: `ClientSpouseDetail` (+ partner English/tests, skills if stored)
- NAATI/CCL, Professional Year, overrides: `ClientPoint` (or dedicated flags if present)

EOI/ROI records:
- `ClientEoiReference` (arrays: `eoi_subclasses`, `eoi_states`; scalar first-value compatibility; ROI refs; submission/invitation/nomination dates; status)

Flow:
1) `ClientsController@detail` loads all above with eager-loading and passes compacted DTOs to the Blade.
2) `PointsService::compute($client, $selectedSubclass)` runs server-side to produce `total`, `breakdown`, and `warnings` for initial render.
3) The EOI/ROI tab embeds the initial points JSON for the selected EOI. When the user switches EOI or changes subclass, the page either:
   - Re-computes via a lightweight endpoint: `GET /admin/clients/{id}/points?subclass=190` → `{total, breakdown, warnings}`; or
   - Uses embedded precomputed options for 189/190/491 if preferred (and refreshes after save).
4) Expiry dates are derived from source data:
   - English expiry: `test_date + validity_window` (config, default 3y)
   - Skills assessment expiry: from assessment validity fields; warning if missing
   - Upcoming thresholds (employment and age) computed relative to invitation/now

Caching & consistency:
- Cache points for 5–15 minutes keyed by `(client_id, selectedSubclass)` to avoid repeated heavy calculations.
- Bust cache on save events from Client Edit affecting inputs (tests, employment, education, partner, assessment).

Resilience & fallbacks:
- If any critical source is missing (e.g., DOB), show a warning badge and exclude that component from points until resolved.
- Allow manual override via `ClientPoint` if business requires provisional totals; overrides should be clearly flagged in breakdown.

### EOI Password Handling & Retrieval
- If the EOI portal password must be viewable later by staff, store it encrypted at rest using Laravel's `Crypt` (application key) rather than a one-way hash. This allows decryption for authorized viewing.
- Never expose the password in list APIs or tables; return only on explicit, authorized requests (e.g., a dedicated endpoint with policy gate and audit log).
- Add a “View password” action with explicit confirmation + audit trail (who viewed and when) and optionally a short-lived reveal window on the UI.
- Consider per-record access scoping: only assigned case managers or admins with elevated permission can reveal.

Example (model accessor/mutator):
```php
// In ClientEoiReference
public function setEOIPasswordAttribute($value){ $this->attributes['EOI_password'] = $value ? encrypt($value) : null; }
public function getEOIPasswordDecrypted(): ?string { return $this->EOI_password ? decrypt($this->EOI_password) : null; }
```

### Reporting & Search (Occupation / State / Subclass / Status)
Use a dedicated reporting page to query across EOI/ROI records:
- Filters: occupation (ANZSCO), subclass (multi), states (multi), status, submission date range, points range, ROI presence
- Columns: client, EOI ref, occupation, subclasses, states, status, points, submission/invitation/nomination dates, owner/assignee
- Actions: open client, copy ROI ref, export CSV (no passwords)

Data structure options:
1) MVP (fast): keep `eoi_subclasses` and `eoi_states` as JSON arrays + add functional indexes and use JSON_CONTAINS for filtering
   - MySQL: `JSON_CONTAINS(eoi_subclasses, '"190"')` and similar for states; add regular indexes on `(client_id)`, `(eoi_status)`, `(EOI_submission_date)`
2) Scalable: add child tables `client_eoi_subclasses (eoi_id, subclass)` and `client_eoi_states (eoi_id, state)` for indexed, fast WHERE IN queries
   - Pros: better performance and simpler SQL; Cons: extra writes on update

Recommendation: start with JSON (MVP). If reporting queries grow heavy/slow, migrate to child tables (provide a backfill migration).

Security for reporting:
- Only `auth:admin` with appropriate role can access.
- Pagination and server-side sorting to protect performance.
- Audit export downloads.

Reporting readiness checklist (to implement now, without building reports):
- [x] Persist arrays: `eoi_subclasses` and `eoi_states` (JSON) and maintain scalar first-values for compatibility
- [x] Add indexes on `(client_id)`, `(eoi_status)`, `(EOI_submission_date)`; consider covering index `(client_id, eoi_status)`
- [x] Normalize ANZSCO occupation codes in `EOI_occupation` (store code prefix consistently)
- [x] Store `created_by` / `updated_by` (assignee/owner) for later filtering
- [x] Never store passwords in exports; exclude `EOI_password` from any query scopes by default
- [x] Provide stable DTO shape in controller responses (so reporting API can reuse serializers)

### Display in UI
- Show total as a badge
- Show breakdown rows (age, English, employment, education, bonuses, partner, nomination)
- Show warnings with icons and expected date of change
- Show delta badge near EOI “Points” field: match / +Δ / −Δ vs computed total



