# Company Information Edit Page - Research & Recommendations

## Executive Summary

Based on research of Australian visa processes (407, 482, 186), sponsorship requirements, and nomination information, this document outlines what additional information should be included in the company information edit page to support visa sponsorship and nomination processes.

**Current Status**: The company edit page currently includes:
- Company Information (name, ABN, ACN, trading name, business type, website)
- Primary Contact Person
- Business Address
- Contacts (Phone & Email)

**Recommended Additions**: Sponsorship & Nomination Information, Financial Information, Business Operations, and Compliance Tracking sections.

---

## 1. Australian Visa Process Overview

### Visa 407 (Training Visa)
- **Purpose**: Workplace-based occupational training (up to 24 months)
- **Sponsorship Type**: Temporary Activity Sponsorship (TAS) - valid for 5 years
- **Key Requirements**:
  - Structured workplace-based training program
  - Training contract (with salary if paid)
  - Training schedule (on-the-job, classroom, supervised activities)
  - Details of supervisors, trainers, and assessors with qualifications
  - Statement confirming trainee has functional English

### Visa 482 (Temporary Skill Shortage - TSS)
- **Purpose**: Temporary work visa (2-4 years depending on stream)
- **Sponsorship Type**: Standard Business Sponsor - valid for 5 years
- **Key Requirements**:
  - Financial documentation (P&L, Balance Sheet, BAS, Bank statements)
  - Business registration documents (ABN/ARBN, ASIC extracts)
  - Labour market testing evidence
  - Employment contract for nominated position
  - Organizational chart
  - Lease agreement or premises evidence
  - Salary and employment conditions compliance

### Visa 186 (Employer Nomination Scheme - ENS)
- **Purpose**: Permanent residency through employer nomination
- **Sponsorship Type**: Standard Business Sponsor (must be active and lawfully operating)
- **Key Requirements**:
  - Financial documentation (Balance sheets, P&L, Tax returns)
  - Employment contract with 2-year commitment
  - Financial capacity demonstration
  - Business operational evidence (12+ months for established businesses)
  - ABN/ACN registration (for start-ups less than 12 months)

---

## 2. Current Company Edit Page Structure

### Existing Sections:
1. **Company Information**
   - Company Name ✓
   - Trading Name ✓
   - ABN ✓
   - ACN ✓
   - Business Type ✓
   - Website ✓

2. **Primary Contact Person** ✓
3. **Business Address** ✓
4. **Contacts (Phone & Email)** ✓

### Missing Information for Visa Sponsorship/Nomination:
- Sponsorship status and details
- Financial information
- Business operations information
- Compliance tracking
- Training program details (for 407)
- Nomination history

---

## 3. Recommended Additions to Company Edit Page

### Section A: Sponsorship & Nomination Information

**Purpose**: Track company's sponsorship status and nomination capabilities

#### Fields to Add:

1. **Sponsorship Status**
   - `sponsorship_type` (enum): 
     - None
     - Temporary Activity Sponsor (TAS) - for 407
     - Standard Business Sponsor - for 482/186
     - Accredited Sponsor - priority processing
   - `sponsorship_approval_date` (date): When sponsorship was approved
   - `sponsorship_expiry_date` (date): When sponsorship expires (5 years from approval)
   - `sponsorship_status` (enum): 
     - Not Applied
     - Pending
     - Approved
     - Expired
     - Cancelled
     - Suspended
   - `sponsorship_application_number` (string): Reference number from ImmiAccount

2. **Nomination Capabilities**
   - `can_sponsor_407` (boolean): Can sponsor Training Visa (407)
   - `can_sponsor_482` (boolean): Can sponsor TSS Visa (482)
   - `can_sponsor_186` (boolean): Can sponsor ENS Visa (186)
   - `active_nominations_count` (integer): Number of active nominations
   - `total_nominations_count` (integer): Total nominations made

3. **Sponsorship Compliance**
   - `has_adverse_information` (boolean): Any adverse information known to Department
   - `compliance_notes` (text): Notes about compliance issues
   - `last_compliance_check_date` (date): Last time compliance was verified
   - `labour_market_testing_required` (boolean): Whether LMT is required for nominations

**Form Layout Suggestion**:
```
┌─────────────────────────────────────────────────┐
│ Sponsorship & Nomination Information            │
├─────────────────────────────────────────────────┤
│ Sponsorship Type: [Dropdown]                    │
│ Approval Date: [Date Picker]                     │
│ Expiry Date: [Date Picker] (Auto-calc +5 years) │
│ Status: [Dropdown]                               │
│ Application Number: [Text]                       │
│                                                 │
│ Can Sponsor:                                     │
│ ☐ 407 Training Visa                             │
│ ☐ 482 TSS Visa                                  │
│ ☐ 186 ENS Visa                                  │
│                                                 │
│ Active Nominations: [Number]                     │
│ Total Nominations: [Number]                      │
│                                                 │
│ Compliance:                                      │
│ ☐ Has Adverse Information                       │
│ Last Compliance Check: [Date]                   │
│ Notes: [Textarea]                                │
└─────────────────────────────────────────────────┘
```

---

### Section B: Financial Information

**Purpose**: Store financial documentation details required for sponsorship applications

#### Fields to Add:

1. **Financial Documents Status**
   - `financial_year_end` (date): Company's financial year end date
   - `last_financial_report_date` (date): Date of last financial report
   - `has_profit_loss_statement` (boolean): P&L statement available
   - `has_balance_sheet` (boolean): Balance sheet available
   - `has_tax_returns` (boolean): Tax returns available
   - `has_bas_statements` (boolean): BAS statements available (last 4 quarters)
   - `has_bank_statements` (boolean): Recent bank statements available
   - `has_accountant_letter` (boolean): Letter of support from accountant available

2. **Financial Health Indicators**
   - `annual_revenue` (decimal): Annual revenue (optional, for reference)
   - `number_of_employees` (integer): Current number of employees
   - `financial_viability_status` (enum):
     - Not Assessed
     - Viable
     - Needs Review
     - Not Viable
   - `financial_notes` (text): Notes about financial status

3. **Document Tracking** (Link to Documents section)
   - Financial documents should be uploaded to Documents section
   - This section tracks which documents are available/required

**Form Layout Suggestion**:
```
┌─────────────────────────────────────────────────┐
│ Financial Information                            │
├─────────────────────────────────────────────────┤
│ Financial Year End: [Date Picker]               │
│ Last Financial Report: [Date Picker]            │
│                                                 │
│ Available Documents:                            │
│ ☐ Profit & Loss Statement                      │
│ ☐ Balance Sheet                                 │
│ ☐ Tax Returns                                   │
│ ☐ BAS Statements (Last 4 Quarters)              │
│ ☐ Bank Statements                                │
│ ☐ Accountant Letter of Support                 │
│                                                 │
│ Business Metrics:                                │
│ Annual Revenue: [Currency Input]                 │
│ Number of Employees: [Number]                   │
│                                                 │
│ Viability Status: [Dropdown]                    │
│ Notes: [Textarea]                                │
│                                                 │
│ [View Financial Documents] [Upload Documents]   │
└─────────────────────────────────────────────────┘
```

---

### Section C: Business Operations Information

**Purpose**: Track operational details required for sponsorship applications

#### Fields to Add:

1. **Business Registration Details**
   - `arbn_number` (string): Australian Registered Body Number (for overseas businesses)
   - `asic_company_extract_date` (date): Date of ASIC company extract
   - `business_name_registration_date` (date): Business name registration date
   - `registration_country` (string): Country of registration (if not Australia)

2. **Business Premises**
   - `premises_type` (enum):
     - Owned
     - Leased
     - Co-working Space
     - Home Office
     - Other
   - `lease_expiry_date` (date): If leased, when lease expires
   - `premises_address` (text): Can link to existing address section
   - `has_lease_agreement` (boolean): Lease agreement document available

3. **Organizational Structure**
   - `has_organizational_chart` (boolean): Organizational chart available
   - `organizational_chart_date` (date): Date of organizational chart
   - `number_of_departments` (integer): Number of departments
   - `reporting_structure_notes` (text): Notes about reporting structure

4. **Business Activity**
   - `primary_business_activity` (text): Main business activity/industry
   - `anzsic_code` (string): ANZSIC industry code (if applicable)
   - `years_in_operation` (integer): Years company has been operating
   - `business_expansion_plans` (text): Future expansion plans
   - `has_marketing_materials` (boolean): Advertisements/promotional materials available

**Form Layout Suggestion**:
```
┌─────────────────────────────────────────────────┐
│ Business Operations Information                  │
├─────────────────────────────────────────────────┤
│ Registration Details:                           │
│ ARBN Number: [Text]                             │
│ ASIC Extract Date: [Date Picker]                │
│ Business Name Registration: [Date Picker]       │
│ Registration Country: [Text]                      │
│                                                 │
│ Premises:                                       │
│ Type: [Dropdown]                                │
│ Lease Expiry: [Date Picker]                     │
│ ☐ Lease Agreement Available                     │
│                                                 │
│ Organizational Structure:                       │
│ ☐ Organizational Chart Available                │
│ Chart Date: [Date Picker]                       │
│ Number of Departments: [Number]                 │
│ Reporting Structure: [Textarea]                  │
│                                                 │
│ Business Activity:                              │
│ Primary Activity: [Text]                         │
│ ANZSIC Code: [Text]                             │
│ Years in Operation: [Number]                     │
│ Expansion Plans: [Textarea]                      │
│ ☐ Marketing Materials Available                 │
└─────────────────────────────────────────────────┘
```

---

### Section D: Training Program Information (For 407 Visa Sponsorship)

**Purpose**: Track training program details for companies sponsoring 407 Training Visas

#### Fields to Add:

1. **Training Program Details**
   - `has_training_program` (boolean): Company has structured training program
   - `training_program_name` (string): Name of training program
   - `training_program_type` (enum):
     - On-the-job Training
     - Classroom Training
     - Supervised Activities
     - Combined Program
   - `training_program_duration_months` (integer): Duration in months (max 24)
   - `training_program_start_date` (date): When program starts
   - `training_program_description` (text): Description of training program

2. **Training Staff**
   - `has_qualified_supervisors` (boolean): Qualified supervisors available
   - `number_of_supervisors` (integer): Number of supervisors
   - `number_of_trainers` (integer): Number of trainers
   - `number_of_assessors` (integer): Number of assessors
   - `supervisor_qualifications` (text): Details of supervisor qualifications

3. **Training Documentation**
   - `has_training_contract` (boolean): Training contract available
   - `has_training_schedule` (boolean): Training schedule available
   - `training_contract_includes_salary` (boolean): Training includes paid salary
   - `training_salary_amount` (decimal): Salary amount (if paid)

**Form Layout Suggestion**:
```
┌─────────────────────────────────────────────────┐
│ Training Program Information (407 Visa)          │
├─────────────────────────────────────────────────┤
│ ☐ Has Structured Training Program               │
│ Program Name: [Text]                             │
│ Type: [Dropdown]                                 │
│ Duration (Months): [Number] (Max: 24)           │
│ Start Date: [Date Picker]                        │
│ Description: [Textarea]                          │
│                                                 │
│ Training Staff:                                 │
│ ☐ Qualified Supervisors Available                │
│ Number of Supervisors: [Number]                  │
│ Number of Trainers: [Number]                    │
│ Number of Assessors: [Number]                    │
│ Supervisor Qualifications: [Textarea]           │
│                                                 │
│ Training Documentation:                          │
│ ☐ Training Contract Available                    │
│ ☐ Training Schedule Available                   │
│ ☐ Training Includes Salary                       │
│ Salary Amount: [Currency Input]                  │
└─────────────────────────────────────────────────┘
```

---

### Section E: Nomination History & Tracking

**Purpose**: Track nomination applications and their status

#### Fields to Add:

**Note**: This could be a separate table or a section that links to nominations

1. **Nomination Summary**
   - `total_nominations_submitted` (integer): Total nominations submitted
   - `approved_nominations_count` (integer): Number of approved nominations
   - `pending_nominations_count` (integer): Number of pending nominations
   - `rejected_nominations_count` (integer): Number of rejected nominations
   - `last_nomination_date` (date): Date of last nomination submission

2. **Nomination List** (Display only, links to detailed nomination records)
   - Table showing:
     - Nomination ID/Reference
     - Visa Type (407/482/186)
     - Nominee Name
     - Position Title
     - Submission Date
     - Status
     - Actions (View/Edit)

**Form Layout Suggestion**:
```
┌─────────────────────────────────────────────────┐
│ Nomination History                              │
├─────────────────────────────────────────────────┤
│ Summary:                                        │
│ Total Submitted: [Number]                        │
│ Approved: [Number]                               │
│ Pending: [Number]                                │
│ Rejected: [Number]                               │
│ Last Nomination: [Date]                          │
│                                                 │
│ Recent Nominations:                              │
│ [Table with columns: ID, Visa Type, Nominee,     │
│  Position, Date, Status, Actions]               │
│                                                 │
│ [View All Nominations] [Create New Nomination] │
└─────────────────────────────────────────────────┘
```

---

## 4. Database Schema Recommendations

### New Migration: Add Sponsorship & Financial Fields to Companies Table

```php
Schema::table('companies', function (Blueprint $table) {
    // Sponsorship Information
    $table->enum('sponsorship_type', [
        'none', 
        'tas', 
        'standard_business', 
        'accredited'
    ])->default('none')->after('company_website');
    
    $table->date('sponsorship_approval_date')->nullable();
    $table->date('sponsorship_expiry_date')->nullable();
    $table->enum('sponsorship_status', [
        'not_applied',
        'pending',
        'approved',
        'expired',
        'cancelled',
        'suspended'
    ])->default('not_applied');
    
    $table->string('sponsorship_application_number', 100)->nullable();
    
    // Nomination Capabilities
    $table->boolean('can_sponsor_407')->default(false);
    $table->boolean('can_sponsor_482')->default(false);
    $table->boolean('can_sponsor_186')->default(false);
    $table->integer('active_nominations_count')->default(0);
    $table->integer('total_nominations_count')->default(0);
    
    // Compliance
    $table->boolean('has_adverse_information')->default(false);
    $table->text('compliance_notes')->nullable();
    $table->date('last_compliance_check_date')->nullable();
    $table->boolean('labour_market_testing_required')->default(true);
    
    // Financial Information
    $table->date('financial_year_end')->nullable();
    $table->date('last_financial_report_date')->nullable();
    $table->boolean('has_profit_loss_statement')->default(false);
    $table->boolean('has_balance_sheet')->default(false);
    $table->boolean('has_tax_returns')->default(false);
    $table->boolean('has_bas_statements')->default(false);
    $table->boolean('has_bank_statements')->default(false);
    $table->boolean('has_accountant_letter')->default(false);
    $table->decimal('annual_revenue', 15, 2)->nullable();
    $table->integer('number_of_employees')->nullable();
    $table->enum('financial_viability_status', [
        'not_assessed',
        'viable',
        'needs_review',
        'not_viable'
    ])->default('not_assessed');
    $table->text('financial_notes')->nullable();
    
    // Business Operations
    $table->string('arbn_number', 20)->nullable();
    $table->date('asic_company_extract_date')->nullable();
    $table->date('business_name_registration_date')->nullable();
    $table->string('registration_country', 100)->nullable();
    $table->enum('premises_type', [
        'owned',
        'leased',
        'coworking',
        'home_office',
        'other'
    ])->nullable();
    $table->date('lease_expiry_date')->nullable();
    $table->boolean('has_lease_agreement')->default(false);
    $table->boolean('has_organizational_chart')->default(false);
    $table->date('organizational_chart_date')->nullable();
    $table->integer('number_of_departments')->nullable();
    $table->text('reporting_structure_notes')->nullable();
    $table->string('primary_business_activity', 255)->nullable();
    $table->string('anzsic_code', 20)->nullable();
    $table->integer('years_in_operation')->nullable();
    $table->text('business_expansion_plans')->nullable();
    $table->boolean('has_marketing_materials')->default(false);
    
    // Training Program (407 Visa)
    $table->boolean('has_training_program')->default(false);
    $table->string('training_program_name', 255)->nullable();
    $table->enum('training_program_type', [
        'on_the_job',
        'classroom',
        'supervised',
        'combined'
    ])->nullable();
    $table->integer('training_program_duration_months')->nullable();
    $table->date('training_program_start_date')->nullable();
    $table->text('training_program_description')->nullable();
    $table->boolean('has_qualified_supervisors')->default(false);
    $table->integer('number_of_supervisors')->nullable();
    $table->integer('number_of_trainers')->nullable();
    $table->integer('number_of_assessors')->nullable();
    $table->text('supervisor_qualifications')->nullable();
    $table->boolean('has_training_contract')->default(false);
    $table->boolean('has_training_schedule')->default(false);
    $table->boolean('training_contract_includes_salary')->default(false);
    $table->decimal('training_salary_amount', 10, 2)->nullable();
    
    // Nomination Tracking
    $table->integer('total_nominations_submitted')->default(0);
    $table->integer('approved_nominations_count')->default(0);
    $table->integer('pending_nominations_count')->default(0);
    $table->integer('rejected_nominations_count')->default(0);
    $table->date('last_nomination_date')->nullable();
});
```

### Alternative: Separate Tables Approach

If the companies table becomes too large, consider separate tables:

1. **`company_sponsorships`** table
2. **`company_financials`** table
3. **`company_operations`** table
4. **`company_training_programs`** table

This approach provides better normalization but requires more joins.

---

## 5. Form Organization Recommendations

### Suggested Tab/Section Structure:

```
Company Edit Page
├── Company Information (Existing)
│   ├── Basic Details
│   └── Registration Numbers
│
├── Contact Person (Existing)
│
├── Business Address (Existing)
│
├── Contacts (Existing)
│
├── Sponsorship & Nomination (NEW)
│   ├── Sponsorship Status
│   ├── Nomination Capabilities
│   └── Compliance Tracking
│
├── Financial Information (NEW)
│   ├── Financial Documents Status
│   ├── Financial Health
│   └── Document Links
│
├── Business Operations (NEW)
│   ├── Registration Details
│   ├── Premises Information
│   ├── Organizational Structure
│   └── Business Activity
│
└── Training Program (NEW - Conditional)
    ├── Program Details
    ├── Training Staff
    └── Documentation
```

### Conditional Display Logic:

- **Training Program Section**: Only show if `can_sponsor_407 = true` or `sponsorship_type = 'tas'`
- **Financial Information**: Show for all companies, but highlight required fields based on sponsorship type
- **Nomination History**: Show if `total_nominations_count > 0` or company is a client (not just a lead)

---

## 6. Integration with Existing Systems

### Documents Section Integration:

- Financial documents should be uploaded to the Documents section with specific document types:
  - `financial_profit_loss`
  - `financial_balance_sheet`
  - `financial_tax_return`
  - `financial_bas_statement`
  - `financial_bank_statement`
  - `financial_accountant_letter`
  - `business_lease_agreement`
  - `business_organizational_chart`
  - `business_registration_abn`
  - `business_registration_asic`
  - `training_contract`
  - `training_schedule`

- The Financial Information section should link to these documents and show status (uploaded/not uploaded)

### Matters Integration:

- When creating matters for companies, link to sponsorship information
- Show sponsorship status in matter detail view
- Filter matters by visa type (407/482/186) if needed

### Notes Integration:

- Allow notes to be tagged with sponsorship/nomination context
- Link notes to specific nominations or sponsorship applications

---

## 7. Priority Implementation Recommendations

### Phase 1: Essential for Sponsorship (High Priority)
1. **Sponsorship & Nomination Information** section
   - Sponsorship type, status, dates
   - Nomination capabilities
   - Basic compliance tracking

2. **Financial Information** section
   - Financial documents status checkboxes
   - Link to Documents section
   - Basic financial health indicators

### Phase 2: Important for Applications (Medium Priority)
3. **Business Operations Information** section
   - Registration details (ARBN, ASIC)
   - Premises information
   - Organizational structure

4. **Nomination History** section
   - Summary statistics
   - List of nominations (if separate nominations table exists)

### Phase 3: Specialized Features (Lower Priority)
5. **Training Program Information** section
   - Only needed for companies sponsoring 407 visas
   - Can be added when first 407 sponsorship is needed

---

## 8. User Experience Considerations

### Form Usability:

1. **Progressive Disclosure**: 
   - Show basic sponsorship info first
   - Expand sections as user fills in information
   - Hide Training Program section until 407 sponsorship is selected

2. **Validation**:
   - If sponsorship type is selected, require approval date
   - If financial documents are marked as available, require link to document
   - Date validation (expiry date must be after approval date)

3. **Help Text**:
   - Add tooltips explaining each field
   - Link to Department of Home Affairs resources
   - Show examples of required documents

4. **Status Indicators**:
   - Visual indicators for sponsorship status (green/yellow/red)
   - Progress indicators for document completion
   - Warnings for expired sponsorships

5. **Auto-calculations**:
   - Auto-calculate sponsorship expiry date (approval date + 5 years)
   - Auto-update nomination counts when nominations are created/updated
   - Show days until expiry

---

## 9. Reporting & Analytics Opportunities

With this additional data, you can create:

1. **Sponsorship Dashboard**:
   - Companies with expiring sponsorships (next 30/60/90 days)
   - Companies with incomplete documentation
   - Companies by sponsorship type
   - Nomination success rates

2. **Compliance Reports**:
   - Companies with adverse information
   - Companies needing compliance checks
   - Financial viability status overview

3. **Business Intelligence**:
   - Average time to sponsorship approval
   - Most common sponsorship types
   - Companies by industry/ANZSIC code
   - Training program statistics

---

## 10. Next Steps

### Decision Points:

1. **Database Structure**: 
   - Single table (companies) vs. separate tables (normalized)
   - Recommendation: Start with single table, normalize later if needed

2. **Implementation Phases**:
   - All at once vs. phased approach
   - Recommendation: Phased approach (Phase 1 first)

3. **Document Storage**:
   - Use existing Documents system vs. separate storage
   - Recommendation: Use existing Documents system with document types

4. **Nomination Tracking**:
   - Separate nominations table vs. notes/forms
   - Recommendation: Separate table for better tracking and reporting

### Action Items:

1. ✅ Research completed
2. ⏳ Review recommendations with stakeholders
3. ⏳ Decide on database structure
4. ⏳ Prioritize which sections to implement first
5. ⏳ Design form layouts and user experience
6. ⏳ Create database migration
7. ⏳ Implement form sections
8. ⏳ Add validation and business logic
9. ⏳ Test with sample data
10. ⏳ Deploy and train users

---

## 11. References

### Australian Government Resources:
- [Department of Home Affairs - Becoming a Sponsor](https://immi.homeaffairs.gov.au/visas/employing-and-sponsoring-someone/sponsoring-workers/becoming-a-sponsor)
- [Standard Business Sponsor Requirements](https://immi.homeaffairs.gov.au/visas/employing-and-sponsoring-someone/sponsoring-workers/becoming-a-sponsor/standard-business-sponsor)
- [Training Visa (407) Requirements](https://immi.homeaffairs.gov.au/visas/getting-a-visa/visa-listing/training-407)
- [TSS Visa (482) Requirements](https://immi.homeaffairs.gov.au/visas/getting-a-visa/visa-listing/temporary-skill-shortage-482)
- [ENS Visa (186) Requirements](https://immi.homeaffairs.gov.au/visas/getting-a-visa/visa-listing/employer-nomination-scheme-186)

### Research Sources:
- Visa Envoy - Business Sponsor Questionnaire
- Migration Plus - Employer Sponsorship Information
- HECT - Employer Requirements for 482, 494, and 186 Visa Sponsorship

---

**Document Version**: 1.0  
**Date**: 2026-01-27  
**Status**: Research Complete - Awaiting Feedback & Decisions
