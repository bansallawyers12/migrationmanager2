# Admin Table Refactoring: Field Categorization

## Overview
Splitting the `admins` table into:
- **admins** - Staff/employees only
- **clients** - Clients and leads combined

---

## STAFF FIELDS (Stay in `admins` table)

### Core Identity
- id
- first_name
- last_name
- email
- password
- decrypt_password
- remember_token
- staff_id

### Role & Permissions
- role
- position
- team
- permission
- office_id

### Contact (Staff)
- phone
- country_code
- country
- state
- city
- address
- zip
- latitude
- longitude
- time_zone
- telephone

### Profile
- profile_img
- status
- verified

### Business/Professional (Staff)
- marn_number
- legal_practitioner_number
- exempt_person_reason
- company_name (if staff member's company)
- company_website
- primary_email
- gst_no
- gstin
- gst_date
- is_business_gst
- ABN_number
- business_mobile
- business_fax
- company_fax

### Email Configuration (Staff)
- smtp_host
- smtp_port
- smtp_enc
- smtp_username
- smtp_password

### API/Service
- service_token (if for staff authentication)
- token_generated_at

### Permissions
- show_dashboard_per

### Timestamps
- created_at
- updated_at

---

## CLIENT/LEAD FIELDS (Move to `clients` table)

### Core Identity
- id (new auto-increment)
- client_id (unique identifier)
- client_counter
- first_name
- last_name
- email
- password (for client portal)
- decrypt_password
- remember_token

### Type & Classification
- type (client/lead)
- role (if used for client categorization)

### Contact Information
- phone
- country_code
- att_phone (alternative phone)
- att_email (alternative email)
- att_country_code
- country
- state
- city
- address
- zip
- latitude
- longitude
- contact_type
- email_type
- emergency_country_code
- emergency_contact_no
- emergency_contact_type

### Personal Information
- dob
- age
- gender
- marital_status
- profile_img

### Immigration/Visa
- passport_number
- country_passport
- visa_type
- visaExpiry
- visaGrant
- visa_opt
- prev_visa
- preferredIntake
- applications
- is_visa_expire_mail_sent

### Verification Fields
- dob_verified_date
- dob_verified_by
- phone_verified_date
- phone_verified_by
- visa_expiry_verified_at
- visa_expiry_verified_by
- email_verified_at
- dob_verify_document

### EOI/Points Assessment
- nomi_occupation
- skill_assessment
- high_quali_aus
- high_quali_overseas
- relevant_work_exp_aus
- relevant_work_exp_over
- naati_test
- py_test
- naati_date
- py_date
- naati_py
- nati_language
- py_field
- married_partner
- total_points
- start_process
- qualification_level
- qualification_name
- experience_job_title
- experience_country
- australian_study
- australian_study_date
- specialist_education
- specialist_education_date
- regional_study
- regional_study_date
- regional_points

### CRM/Lead Management
- lead_id
- lead_status
- lead_quality
- service
- source
- assignee (foreign key to admins.id)
- followers
- tagname
- tags
- rating
- comments_note
- followup_date
- is_archived
- archived_on
- is_deleted
- is_star_client

### Relationships
- user_id (if linking to another table)
- agent_id (foreign key to admins.id)
- office_id (which office handles this client)
- wp_customer_id
- not_picked_call

### Client Portal
- cp_status
- cp_random_code
- cp_token_generated_at
- cp_code_verify
- status (active/inactive)
- verified

### Files & Documents
- related_files

### Timestamps
- created_at
- updated_at

---

## DESIGN DECISIONS

### 1. Type Field
- Keep `type` field in clients table to distinguish:
  - `staff` = Internal staff member (for reference)
  - `client` = Active client
  - `lead` = Potential client/lead

### 2. Shared Fields
Some fields like `first_name`, `last_name`, `email` exist in both tables but serve different purposes:
- **admins**: Staff authentication and identity
- **clients**: Client contact information and portal access

### 3. Foreign Keys
- `assignee` in clients → references `admins.id`
- `agent_id` in clients → references `admins.id`
- `*_verified_by` fields → references `admins.id`
- `office_id` → references offices table

### 4. Client Portal Authentication
Clients should authenticate through their own guard, separate from staff

---

## MIGRATION STRATEGY

### Phase 1: Create New Clients Table
1. Create comprehensive `clients` table with all client/lead fields
2. Add proper indexes and foreign keys
3. Add unique constraints

### Phase 2: Data Migration
1. Copy all records where `type` IN ('client', 'lead') or where client-specific fields are populated
2. Preserve original IDs in a mapping table for reference
3. Update foreign key references in related tables

### Phase 3: Update Admins Table
1. Remove client/lead specific columns
2. Keep only records where `type` = 'staff' or staff-specific fields are populated
3. Add constraints and indexes

### Phase 4: Update Application Code
1. Update models
2. Update controllers
3. Update views
4. Update API endpoints
5. Update authentication guards

---

## RELATED TABLES TO UPDATE

Tables that currently reference `admins` table:
- forms_956 (client_id → clients.id)
- client_eoi_references (client_id → clients.id)
- client_test_scores (client_id → clients.id)
- client_experiences (client_id → clients.id)
- client_qualifications (client_id → clients.id)
- client_spouse_details (client_id → clients.id)
- client_occupations (client_id → clients.id)
- client_relationships (client_id → clients.id)
- lead_followups (lead_id → clients.id, assigned_to → admins.id)
- documents (created_by → admins.id)
- sms_log (likely references admins for both staff and clients)

---

## BENEFITS

1. **Clearer Data Model**: Separate concerns for staff vs clients
2. **Better Performance**: Smaller tables with focused indexes
3. **Easier Maintenance**: Clear which fields belong to which entity
4. **Improved Security**: Separate authentication contexts
5. **Scalability**: Each table can grow independently
6. **Code Clarity**: Controllers and models have clear responsibilities

