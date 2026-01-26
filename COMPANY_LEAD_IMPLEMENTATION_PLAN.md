# Company Lead Feature - Implementation Plan

## 📋 Table of Contents
1. [Overview](#overview)
2. [Architecture Decisions](#architecture-decisions)
3. [Database Changes](#database-changes)
4. [Model Updates](#model-updates)
5. [Controller Changes](#controller-changes)
6. [View Changes](#view-changes)
7. [API Endpoints](#api-endpoints)
8. [JavaScript Functionality](#javascript-functionality)
9. [Step-by-Step Implementation](#step-by-step-implementation)
10. [Implementation Checklist (Progress)](#implementation-checklist-progress)
11. [Testing Checklist](#testing-checklist)
12. [Things to Be Careful About](#things-to-be-careful-about)
13. [Questions for Clarification](#questions-for-clarification)

---

## Overview

### Feature Description
Add the ability to create and manage company leads/clients in the CRM system. Companies can have:
- Company information (name, ABN, ACN, business type, website)
- Primary contact person (searchable from existing database, auto-fill details)
- Business address and contacts
- Separate edit and detail pages for better flexibility

### Key Requirements
1. **Lead Creation Form**: Add toggle "Is this new lead a company?" (Yes/No, default: No)
2. **Company Fields**: Show company-specific fields when "Yes" is selected
3. **Contact Person Search**: Search existing clients/leads by email/name and auto-fill details
4. **Separate Pages**: Create dedicated edit and detail pages for companies
5. **Sidebar**: Keep same sidebar structure, but show company name and contact person info
6. **Matter Creation**: Companies should be able to create matters (same process as personal clients)
7. **Backward Compatibility**: Existing personal leads/clients should continue working

---

## Architecture Decisions

### ✅ Decisions Made

1. **Database Structure**: Use existing `admins` table with new columns (Option A - MVP approach)
   - Add: `is_company`, `company_type`, `contact_person_position`, `contact_person_id`
   - Reuse: `company_name`, `ABN_number`, `company_website` (already exist)

2. **Page Structure**: 
   - **Detail Page**: Keep same `detail.blade.php` with conditional logic for sidebar header
   - **Edit Page**: Create separate `company_edit.blade.php` for companies
   - **Display Page**: Create separate `company_detail.blade.php` for companies (future flexibility)

3. **Contact Person Search**: Use Select2 with AJAX search (similar to existing email recipient search)

4. **Sidebar**: Keep same structure, conditionally show company name vs personal name

5. **Tabs**: 
   - Rename "Personal Details" to "Company Details" for companies
   - Hide EOI/ROI tab for companies
   - Keep other tabs (Notes, Documents, etc.)

### 🔄 Future Enhancements (Option B)
- Separate `companies` table for full company management
- Multiple contact persons per company
- Company hierarchy support
- Company-specific workflows

---

## Database Changes

### Migration 1: Add Company Fields to Admins Table
**File**: `database/migrations/YYYY_MM_DD_HHMMSS_add_company_fields_to_admins_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds company-related fields to admins table for company leads/clients
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // Check if columns already exist before adding
            if (!Schema::hasColumn('admins', 'is_company')) {
                $table->boolean('is_company')->default(false)->nullable()
                    ->comment('Flag to indicate if this is a company lead/client');
            }
            
            if (!Schema::hasColumn('admins', 'company_type')) {
                $table->string('company_type', 50)->nullable()
                    ->comment('Business type: Sole Trader, Partnership, Proprietary Company, etc.');
            }
            
            if (!Schema::hasColumn('admins', 'contact_person_position')) {
                $table->string('contact_person_position', 255)->nullable()
                    ->comment('Position/Title of primary contact person (e.g., HR Manager, Director)');
            }
            
            if (!Schema::hasColumn('admins', 'contact_person_id')) {
                $table->integer('contact_person_id')->nullable()
                    ->comment('Reference to admins.id of the primary contact person');
                
                // Add foreign key constraint (optional, can be added later)
                // $table->foreign('contact_person_id')->references('id')->on('admins')->onDelete('set null');
            }
            
            // Add trading_name if it doesn't exist
            if (!Schema::hasColumn('admins', 'trading_name')) {
                $table->string('trading_name', 255)->nullable()
                    ->comment('Trading name if different from company name');
            }
            
            // Add ACN if it doesn't exist (ACN is different from ABN)
            if (!Schema::hasColumn('admins', 'ACN')) {
                $table->string('ACN', 20)->nullable()
                    ->comment('Australian Company Number');
            }
        });
        
        // For PostgreSQL, add index for better query performance
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE INDEX IF NOT EXISTS idx_admins_is_company ON admins(is_company) WHERE is_company = true');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_admins_contact_person_id ON admins(contact_person_id) WHERE contact_person_id IS NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // Drop indexes first (PostgreSQL)
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('DROP INDEX IF EXISTS idx_admins_is_company');
                DB::statement('DROP INDEX IF EXISTS idx_admins_contact_person_id');
            }
            
            // Drop columns if they exist
            if (Schema::hasColumn('admins', 'is_company')) {
                $table->dropColumn('is_company');
            }
            
            if (Schema::hasColumn('admins', 'company_type')) {
                $table->dropColumn('company_type');
            }
            
            if (Schema::hasColumn('admins', 'contact_person_position')) {
                $table->dropColumn('contact_person_position');
            }
            
            if (Schema::hasColumn('admins', 'contact_person_id')) {
                $table->dropColumn('contact_person_id');
            }
            
            if (Schema::hasColumn('admins', 'trading_name')) {
                $table->dropColumn('trading_name');
            }
            
            if (Schema::hasColumn('admins', 'ACN')) {
                $table->dropColumn('ACN');
            }
        });
    }
};
```

### ⚠️ Important Notes
- Check column existence before adding (prevents errors on re-run)
- Use nullable() for all new fields (backward compatibility)
- Default `is_company` to `false` (existing records remain personal)
- Consider adding foreign key constraint later (after testing)

### Migration 2: Add Company Toggle to Matters Table
**File**: `database/migrations/YYYY_MM_DD_HHMMSS_add_is_for_company_to_matters_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds is_for_company field to matters table to filter matters by client type
     */
    public function up(): void
    {
        Schema::table('matters', function (Blueprint $table) {
            // Check if column already exists before adding
            if (!Schema::hasColumn('matters', 'is_for_company')) {
                $table->boolean('is_for_company')->default(false)->nullable()
                    ->comment('If true, this matter is only available for company clients. If false/null, available for personal clients.');
            }
        });
        
        // For PostgreSQL, add index for better query performance
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE INDEX IF NOT EXISTS idx_matters_is_for_company ON matters(is_for_company) WHERE is_for_company = true');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matters', function (Blueprint $table) {
            // Drop index first (PostgreSQL)
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('DROP INDEX IF EXISTS idx_matters_is_for_company');
            }
            
            // Drop column if it exists
            if (Schema::hasColumn('matters', 'is_for_company')) {
                $table->dropColumn('is_for_company');
            }
        });
    }
};
```

**Important Notes**:
- Default `is_for_company` to `false` (existing matters remain for personal clients)
- `null` values should be treated as `false` (for backward compatibility)
- When `is_for_company = true`: Matter only shows for company clients
- When `is_for_company = false/null`: Matter only shows for personal clients

---

## Model Updates

### Matter Model (`app/Models/Matter.php`)

#### Update `$fillable` Array
Add `is_for_company` to the fillable array:

```php
protected $fillable = [
    'id', 
    'title', 
    'nick_name', 
    'is_for_company',  // NEW
    'created_at', 
    'updated_at'
];
```

#### Add Helper Method (Optional)
```php
/**
 * Check if this matter is for companies only
 */
public function isForCompany(): bool
{
    return (bool) $this->is_for_company;
}

/**
 * Scope to filter matters by client type
 */
public function scopeForClientType($query, bool $isCompany)
{
    if ($isCompany) {
        // For companies: show only matters where is_for_company = true
        return $query->where('is_for_company', true);
    } else {
        // For personal clients: show only matters where is_for_company = false or null
        return $query->where(function($q) {
            $q->where('is_for_company', false)
              ->orWhereNull('is_for_company');
        });
    }
}
```

### Admin Model (`app/Models/Admin.php`)

#### 1. Update `$fillable` Array
Add new fields to the fillable array:

```php
protected $fillable = [
    // ... existing fields ...
    'is_company',                    // NEW
    'company_type',                  // NEW
    'contact_person_position',       // NEW
    'contact_person_id',             // NEW
    'trading_name',                  // NEW (if not already exists)
    'ACN',                           // NEW (if not already exists)
    // ... rest of existing fields ...
];
```

#### 2. Add Relationships
Add relationship methods for contact person:

```php
/**
 * Get the primary contact person for this company
 */
public function contactPerson()
{
    return $this->belongsTo(Admin::class, 'contact_person_id', 'id');
}

/**
 * Get companies where this person is the contact person
 */
public function companiesAsContactPerson()
{
    return $this->hasMany(Admin::class, 'contact_person_id', 'id')
                ->where('is_company', true);
}

/**
 * Check if this is a company
 */
public function isCompany(): bool
{
    return (bool) $this->is_company;
}

/**
 * Get display name (company name or personal name)
 * For companies: "Company Name (Contact: Person Name)"
 * For personal: "First Name Last Name"
 */
public function getDisplayNameAttribute(): string
{
    if ($this->is_company) {
        $companyName = $this->company_name ?? 'Unnamed Company';
        if ($this->contactPerson) {
            $contactName = trim($this->contactPerson->first_name . ' ' . $this->contactPerson->last_name);
            return "{$companyName} (Contact: {$contactName})";
        }
        return $companyName;
    }
    return trim($this->first_name . ' ' . $this->last_name);
}
```

#### 3. Add Accessor for Company Name
```php
/**
 * Get company name or fallback to personal name
 */
public function getCompanyNameOrPersonalNameAttribute(): string
{
    if ($this->is_company) {
        return $this->company_name ?? 'Unnamed Company';
    }
    return trim($this->first_name . ' ' . $this->last_name);
}
```

---

## Controller Changes

### 1. LeadController (`app/Http/Controllers/CRM/Leads/LeadController.php`)

#### Update `create()` Method
No changes needed - form will handle the toggle.

#### Update `store()` Method
Modify validation and data handling:

```php
public function store(Request $request)
{
    $requestData = $request->all();
    
    // Check if this is a company lead
    $isCompany = $request->input('is_company', false) == 'yes' || 
                 $request->input('is_company') == true || 
                 $request->input('is_company') == 1;
    
    // Conditional validation
    if ($isCompany) {
        $validationRules = [
            'company_name' => [
                'required',
                'max:255',
                'unique:admins,company_name,NULL,id,is_company,1', // Unique for company leads/clients only
            ],
            'contact_person_id' => [
                'required',
                'exists:admins,id',
                function ($attribute, $value, $fail) {
                    $contactPerson = Admin::find($value);
                    if (!$contactPerson || $contactPerson->role != 7) {
                        $fail('The selected contact person must be a client or lead.');
                    }
                },
            ],
            'contact_person_position' => 'nullable|max:255',
            'ABN_number' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        // Strip non-digits and validate
                        $cleanAbn = preg_replace('/\D/', '', $value);
                        if (strlen($cleanAbn) !== 11) {
                            $fail('ABN must be exactly 11 digits.');
                        }
                    }
                },
            ],
            'ACN' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        // Strip non-digits and validate
                        $cleanAcn = preg_replace('/\D/', '', $value);
                        if (strlen($cleanAcn) !== 9) {
                            $fail('ACN must be exactly 9 digits.');
                        }
                    }
                },
            ],
            'phone.0' => 'required|max:255',
            'email.0' => 'required|email|max:255',
        ];
    } else {
        $validationRules = [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'gender' => 'required|max:255',
            'dob' => 'required',
            'phone.0' => 'required|max:255',
            'email.0' => 'required|email|max:255',
        ];
    }
    
    $this->validate($request, $validationRules);
    
    // ... existing duplicate handling code ...
    
    // Prepare data for insertion
    $adminData = [
        // ... existing system fields ...
        'is_company' => $isCompany ? 1 : 0,
        
        // Conditional field assignment
        ...($isCompany ? [
            'company_name' => $requestData['company_name'],
            'trading_name' => $requestData['trading_name'] ?? null,
            // Normalize ABN/ACN: strip spaces and formatting, validate format
            'ABN_number' => isset($requestData['ABN_number']) && !empty($requestData['ABN_number']) 
                ? preg_replace('/\D/', '', $requestData['ABN_number']) // Strip non-digits
                : null,
            'ACN' => isset($requestData['ACN']) && !empty($requestData['ACN'])
                ? preg_replace('/\D/', '', $requestData['ACN']) // Strip non-digits
                : null,
            'company_type' => $requestData['company_type'] ?? null,
            'company_website' => $requestData['company_website'] ?? null,
            'contact_person_id' => $requestData['contact_person_id'] ?? null,
            'contact_person_position' => $requestData['contact_person_position'] ?? null,
            // For company leads, store contact person name in first_name/last_name
            'first_name' => $requestData['contact_person_first_name'] ?? null,
            'last_name' => $requestData['contact_person_last_name'] ?? null,
            // DOB, gender, marital_status not required for companies
            'dob' => null,
            'gender' => null,
            'marital_status' => null,
        ] : [
            'first_name' => $requestData['first_name'],
            'last_name' => $requestData['last_name'],
            'gender' => $requestData['gender'],
            'dob' => $dob,
            'age' => $requestData['age'] ?? null,
            'marital_status' => $requestData['marital_status'] ?? null,
            // Company fields remain null
            'company_name' => null,
            'is_company' => 0,
        ]),
        
        // ... rest of existing fields ...
    ];
    
    // ... existing insert logic ...
}
```

### 2. ClientsController (`app/Http/Controllers/CRM/ClientsController.php`)

#### Update `detail()` Method
Add conditional routing:

```php
public function detail($id)
{
    // Decode ID if needed (based on your current implementation)
    $decodedId = convert_uudecode(base64_decode($id));
    $fetchedData = Admin::findOrFail($decodedId);
    
    // Route to appropriate detail page
    if ($fetchedData->is_company) {
        return view('crm.clients.company_detail', compact('fetchedData', 'id'));
    } else {
        // Existing detail page logic
        return view('crm.clients.detail', compact('fetchedData', 'id'));
    }
}
```

#### Update `edit()` Method
Add conditional routing:

```php
public function edit($id)
{
    // Decode ID if needed
    $decodedId = convert_uudecode(base64_decode($id));
    $fetchedData = Admin::findOrFail($decodedId);
    
    // Route to appropriate edit page
    if ($fetchedData->is_company) {
        return view('crm.clients.company_edit', compact('fetchedData', 'id'));
    } else {
        // Existing edit page logic
        return view('crm.clients.edit', compact('fetchedData', 'id'));
    }
}
```

#### Update `update()` Method
Add conditional validation and data handling (similar to LeadController@store):

```php
public function update(Request $request)
{
    if ($request->isMethod('post')) {
        $requestData = $request->all();
        $clientId = $requestData['id'];
        $client = Admin::findOrFail($clientId);
        $isCompany = $client->is_company;
        
        // Conditional validation
        if ($isCompany) {
            $validationRules = [
                'company_name' => [
                    'required',
                    'max:255',
                    'unique:admins,company_name,'.$clientId.',id,is_company,1', // Unique for companies, exclude current record
                ],
                'contact_person_id' => [
                    'required',
                    'exists:admins,id',
                    function ($attribute, $value, $fail) {
                        $contactPerson = Admin::find($value);
                        if (!$contactPerson || $contactPerson->role != 7) {
                            $fail('The selected contact person must be a client or lead.');
                        }
                    },
                ],
                // ... ABN/ACN validation (same as create) ...
                // ... other company validation rules ...
            ];
        } else {
            // Personal client validation (existing rules)
            $validationRules = [
                'first_name' => 'required|max:255',
                'last_name' => 'nullable|max:255',
                // ... existing personal validation rules ...
            ];
        }
        
        $this->validate($request, $validationRules);
        
        // ... rest of update logic with conditional data handling ...
    }
}
```

### 3. Create New Controller Method for Contact Person Search

**File**: `app/Http/Controllers/CRM/ClientsController.php` or create new `ContactPersonController.php`

```php
/**
 * Search for contact persons (clients/leads) by email, phone, name, or client ID
 * Used for company contact person selection
 * 
 * Search priority: Phone and Email are primary search fields
 */
public function searchContactPerson(Request $request)
{
    $query = $request->input('q', '');
    $excludeId = $request->input('exclude_id'); // Exclude current lead/client being edited
    
    if (strlen($query) < 2) {
        return response()->json(['results' => []]);
    }
    
    $results = Admin::where(function($q) use ($query) {
            // Primary search: Phone and Email (as per requirement)
            $q->where('phone', 'ILIKE', "%{$query}%")
              ->orWhere('email', 'ILIKE', "%{$query}%")
              // Secondary search: Name and Client ID
              ->orWhere('first_name', 'ILIKE', "%{$query}%")
              ->orWhere('last_name', 'ILIKE', "%{$query}%")
              ->orWhere('client_id', 'ILIKE', "%{$query}%")
              ->orWhereRaw("CONCAT(first_name, ' ', last_name) ILIKE ?", ["%{$query}%"]);
        })
        ->where('role', 7) // Clients/Leads only
        ->where(function($q) {
            $q->where('type', 'client')
              ->orWhere('type', 'lead');
        })
        ->when($excludeId, function($q) use ($excludeId) {
            $q->where('id', '!=', $excludeId);
        })
        ->select('id', 'first_name', 'last_name', 'email', 'phone', 'client_id', 'type')
        ->limit(20)
        ->get()
        ->map(function($person) {
            $fullName = trim($person->first_name . ' ' . $person->last_name);
            // Show phone and email in display text
            $displayText = "{$fullName}";
            if ($person->email) {
                $displayText .= " ({$person->email})";
            }
            if ($person->phone) {
                $displayText .= " - {$person->phone}";
            }
            $displayText .= " - {$person->client_id}";
            
            return [
                'id' => $person->id,
                'text' => $displayText,
                'first_name' => $person->first_name,
                'last_name' => $person->last_name,
                'email' => $person->email,
                'phone' => $person->phone,
                'client_id' => $person->client_id,
                'type' => $person->type
            ];
        });
    
    return response()->json(['results' => $results]);
}
```

**Route**: Add to `routes/clients.php` or `routes/web.php`
```php
Route::get('/api/search-contact-person', [ClientsController::class, 'searchContactPerson'])
    ->name('api.search.contact.person');
```

---

## View Changes

### 1. Lead Creation Form (`resources/views/crm/leads/create.blade.php`)

#### Add Company Toggle Section
Insert after line 152 (after "Basic Information" header):

```blade
{{-- Company Toggle Section --}}
<div class="form-section" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
    <div class="section-header">
        <h3><i class="fas fa-building"></i> Lead Type</h3>
    </div>
    
    <div class="content-grid">
        <div class="form-group full-width">
            <label style="display: block; margin-bottom: 10px; font-weight: 600;">
                Is this new lead a company?
            </label>
            <div style="display: flex; gap: 20px; align-items: center;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="radio" name="is_company" value="no" id="is_company_no" 
                           {{ old('is_company', 'no') == 'no' ? 'checked' : '' }} 
                           onchange="toggleCompanyFields(false)" style="margin-right: 8px;">
                    <span>No (Personal Lead)</span>
                </label>
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="radio" name="is_company" value="yes" id="is_company_yes" 
                           {{ old('is_company') == 'yes' ? 'checked' : '' }} 
                           onchange="toggleCompanyFields(true)" style="margin-right: 8px;">
                    <span>Yes (Company Lead)</span>
                </label>
            </div>
            @error('is_company')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
```

#### Modify Basic Information Section
Wrap existing personal fields in conditional:

```blade
{{-- Personal Information Fields (shown when is_company = no) --}}
<div id="personalFields" class="personal-lead-fields">
    {{-- Existing First Name, Last Name, DOB, Age, Gender, Marital Status fields --}}
</div>

{{-- Company Information Fields (shown when is_company = yes) --}}
<div id="companyFields" class="company-lead-fields" style="display: none;">
    <div class="content-grid">
        <div class="form-group">
            <label for="companyName">Company Name <span class="text-danger">*</span></label>
            <input type="text" id="companyName" name="company_name" 
                   value="{{ old('company_name') }}" 
                   class="company-field" required>
            @error('company_name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="tradingName">Trading Name</label>
            <input type="text" id="tradingName" name="trading_name" 
                   value="{{ old('trading_name') }}" 
                   class="company-field" 
                   placeholder="If different from company name">
        </div>
        
        <div class="form-group">
            <label for="abn">ABN</label>
            <input type="text" id="abn" name="ABN_number" 
                   value="{{ old('ABN_number') }}" 
                   class="company-field" 
                   placeholder="12 345 678 901">
        </div>
        
        <div class="form-group">
            <label for="acn">ACN</label>
            <input type="text" id="acn" name="ACN" 
                   value="{{ old('ACN') }}" 
                   class="company-field" 
                   placeholder="123 456 789">
        </div>
        
        <div class="form-group">
            <label for="companyType">Business Type</label>
            <select id="companyType" name="company_type" class="company-field">
                <option value="">Select Business Type</option>
                <option value="Sole Trader" {{ old('company_type') == 'Sole Trader' ? 'selected' : '' }}>
                    Sole Trader
                </option>
                <option value="Partnership" {{ old('company_type') == 'Partnership' ? 'selected' : '' }}>
                    Partnership
                </option>
                <option value="Proprietary Company" {{ old('company_type') == 'Proprietary Company' ? 'selected' : '' }}>
                    Proprietary Company (Pty Ltd)
                </option>
                <option value="Public Company" {{ old('company_type') == 'Public Company' ? 'selected' : '' }}>
                    Public Company
                </option>
                <option value="Not-for-Profit" {{ old('company_type') == 'Not-for-Profit' ? 'selected' : '' }}>
                    Not-for-Profit Organization
                </option>
                <option value="Other" {{ old('company_type') == 'Other' ? 'selected' : '' }}>
                    Other
                </option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="companyWebsite">Company Website</label>
            <input type="url" id="companyWebsite" name="company_website" 
                   value="{{ old('company_website') }}" 
                   class="company-field" 
                   placeholder="https://www.example.com">
        </div>
    </div>
    
    {{-- Primary Contact Person Section --}}
    <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e0e0e0;">
        <h4 style="margin-bottom: 15px;">
            <i class="fas fa-user-tie"></i> Primary Contact Person
        </h4>
        
            <div class="content-grid">
            <div class="form-group full-width">
                <label for="contactPersonEmail">Search Contact Person <span class="text-danger">*</span></label>
                <select id="contactPersonEmail" name="contact_person_id" 
                        class="form-control select2-contact-person" 
                        data-placeholder="Type phone, email, name, or client ID to search..."
                        data-valid="required"
                        style="width: 100%;"
                        required>
                    @if(old('contact_person_id'))
                        @php
                            $oldContactPerson = \App\Models\Admin::find(old('contact_person_id'));
                        @endphp
                        @if($oldContactPerson)
                            <option value="{{ $oldContactPerson->id }}" selected>
                                {{ $oldContactPerson->first_name }} {{ $oldContactPerson->last_name }} 
                                ({{ $oldContactPerson->email }})
                            </option>
                        @endif
                    @endif
                </select>
                <small class="form-text text-muted">
                    Search existing clients/leads by email, name, or client ID. Selected person's details will auto-fill below.
                </small>
                @error('contact_person_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="contactPersonFirstName">First Name <span class="text-danger">*</span></label>
                <input type="text" id="contactPersonFirstName" name="contact_person_first_name" 
                       value="{{ old('contact_person_first_name') }}" 
                       class="company-field contact-person-field" required readonly>
                <small class="form-text text-muted">Auto-filled from selected contact person</small>
            </div>
            
            <div class="form-group">
                <label for="contactPersonLastName">Last Name <span class="text-danger">*</span></label>
                <input type="text" id="contactPersonLastName" name="contact_person_last_name" 
                       value="{{ old('contact_person_last_name') }}" 
                       class="company-field contact-person-field" required readonly>
            </div>
            
            <div class="form-group">
                <label for="contactPersonPosition">Position/Title</label>
                <input type="text" id="contactPersonPosition" name="contact_person_position" 
                       value="{{ old('contact_person_position') }}" 
                       class="company-field" 
                       placeholder="e.g., HR Manager, Director">
            </div>
            
            <div class="form-group">
                <label for="contactPersonPhone">Phone</label>
                <input type="text" id="contactPersonPhone" name="contact_person_phone" 
                       value="{{ old('contact_person_phone') }}" 
                       class="company-field contact-person-field" readonly>
                <small class="form-text text-muted">Auto-filled from selected contact person</small>
            </div>
            
            <div class="form-group">
                <label for="contactPersonEmailDisplay">Email</label>
                <input type="email" id="contactPersonEmailDisplay" 
                       value="{{ old('contact_person_email_display') }}" 
                       class="company-field contact-person-field" readonly>
                <small class="form-text text-muted">Auto-filled from selected contact person</small>
            </div>
        </div>
    </div>
</div>
```

#### Add JavaScript for Toggle Functionality
Add before closing `@push('scripts')`:

```javascript
// Toggle between personal and company fields
function toggleCompanyFields(isCompany) {
    const personalFields = document.getElementById('personalFields');
    const companyFields = document.getElementById('companyFields');
    const personalRequiredFields = personalFields.querySelectorAll('[required]');
    const companyRequiredFields = companyFields.querySelectorAll('[required]');
    
    if (isCompany) {
        // Show company fields, hide personal fields
        personalFields.style.display = 'none';
        companyFields.style.display = 'block';
        
        // Remove required from personal fields
        personalRequiredFields.forEach(field => {
            field.removeAttribute('required');
        });
        
        // Add required to company fields
        companyRequiredFields.forEach(field => {
            field.setAttribute('required', 'required');
        });
        
        // Clear personal field values (optional)
        personalFields.querySelectorAll('input, select').forEach(field => {
            if (field.type !== 'hidden') {
                field.value = '';
            }
        });
    } else {
        // Show personal fields, hide company fields
        personalFields.style.display = 'block';
        companyFields.style.display = 'none';
        
        // Remove required from company fields
        companyRequiredFields.forEach(field => {
            field.removeAttribute('required');
        });
        
        // Add required to personal fields
        personalRequiredFields.forEach(field => {
            field.setAttribute('required', 'required');
        });
        
        // Clear company field values (optional)
        companyFields.querySelectorAll('input, select').forEach(field => {
            if (field.type !== 'hidden' && field.id !== 'contactPersonEmail') {
                field.value = '';
            }
        });
    }
}

// Initialize Select2 for contact person search
$(document).ready(function() {
    $('#contactPersonEmail').select2({
        ajax: {
            url: '{{ route("api.search.contact.person") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    exclude_id: null // Can exclude current lead if editing
                };
            },
            processResults: function (data) {
                return {
                    results: data.results.map(function(item) {
                        return {
                            id: item.id,
                            text: item.text,
                            first_name: item.first_name,
                            last_name: item.last_name,
                            email: item.email,
                            phone: item.phone,
                            client_id: item.client_id
                        };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2,
        placeholder: 'Type phone, email, name, or client ID to search...',
        allowClear: true
    });
    
    // Auto-fill contact person details when selected
    $('#contactPersonEmail').on('select2:select', function (e) {
        const data = e.params.data;
        
        // Auto-fill fields
        $('#contactPersonFirstName').val(data.first_name);
        $('#contactPersonLastName').val(data.last_name);
        $('#contactPersonPhone').val(data.phone || '');
        $('#contactPersonEmailDisplay').val(data.email);
        
        // Add visual indicator
        $('.contact-person-field').addClass('field-auto-filled');
        
        // Store contact person ID in hidden field if needed
        $('input[name="contact_person_id"]').val(data.id);
    });
    
    // Clear fields when selection is cleared
    $('#contactPersonEmail').on('select2:clear', function (e) {
        $('#contactPersonFirstName').val('');
        $('#contactPersonLastName').val('');
        $('#contactPersonPhone').val('');
        $('#contactPersonEmailDisplay').val('');
        $('.contact-person-field').removeClass('field-auto-filled');
        $('input[name="contact_person_id"]').val('');
    });
    
    // Initialize on page load if old input exists
    @if(old('is_company') == 'yes')
        toggleCompanyFields(true);
    @endif
});
```

### 2. Client Detail Page (`resources/views/crm/clients/detail.blade.php`)

#### Update Sidebar Header (around line 24-51)
Replace the client name display section:

```blade
<div class="client-info">
    <h3 class="client-id">
        {{-- Existing client ID display logic --}}
    </h3>
    
    @if($fetchedData->is_company)
        {{-- Company Lead Display --}}
        <p class="client-name">
            {{ $fetchedData->company_name ?? 'Unnamed Company' }}
            <a href="{{route('clients.edit', base64_encode(convert_uuencode(@$fetchedData->id)))}}" 
               title="Edit" class="client-name-edit">
                <i class="fa fa-edit"></i>
            </a>
        </p>
        
        {{-- Primary Contact Person Info --}}
        @if($fetchedData->contact_person_id)
            @php
                $contactPerson = \App\Models\Admin::find($fetchedData->contact_person_id);
            @endphp
            @if($contactPerson)
                <div class="contact-person-info" style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #e0e0e0;">
                    <small style="color: #6c757d; display: block; margin-bottom: 5px;">Primary Contact:</small>
                    <a href="{{ route('clients.detail', base64_encode(convert_uuencode($contactPerson->id))) }}" 
                       class="contact-person-link" 
                       style="color: #007bff; text-decoration: none; font-weight: 500;">
                        {{ $contactPerson->first_name }} {{ $contactPerson->last_name }}
                    </a>
                    @if($fetchedData->contact_person_position)
                        <br><small style="color: #6c757d;">{{ $fetchedData->contact_person_position }}</small>
                    @endif
                </div>
            @endif
        @endif
    @else
        {{-- Personal Lead Display (existing) --}}
        <p class="client-name">
            {{$fetchedData->first_name}} {{$fetchedData->last_name}} 
            <a href="{{route('clients.edit', base64_encode(convert_uuencode(@$fetchedData->id)))}}" 
               title="Edit" class="client-name-edit">
                <i class="fa fa-edit"></i>
            </a>
        </p>
    @endif
    
    {{-- Rest of existing sidebar content --}}
</div>
```

#### Update Tab Labels (around line 321-382)
Modify tab button text conditionally:

```blade
<button class="client-nav-button active" data-tab="personaldetails">
    <i class="fas fa-user"></i>
    <span>
        @if($fetchedData->is_company)
            Company Details
        @else
            Personal Details
        @endif
    </span>
</button>

{{-- Similar for Documents tab --}}
<button class="client-nav-button" data-tab="personaldocuments">
    <i class="fas fa-folder-open"></i>
    <span>
        @if($fetchedData->is_company)
            Company Documents
        @else
            Personal Documents
        @endif
    </span>
</button>

{{-- Hide EOI/ROI tab for companies --}}
@if(isset($isEoiMatter) && $isEoiMatter && !$fetchedData->is_company)
    <button class="client-nav-button" data-tab="eoiroi">
        <i class="fas fa-passport"></i>
        <span>EOI / ROI</span>
    </button>
@endif
```

### 3. Create Company Detail Page (`resources/views/crm/clients/company_detail.blade.php`)

**Note**: This is for future flexibility. For MVP, you can use the same `detail.blade.php` with conditionals.

If creating separate page, copy `detail.blade.php` and modify:
- Sidebar header (already shown above)
- Tab content in `personal_details.blade.php` (see next section)

### 4. Personal Details Tab (`resources/views/crm/clients/tabs/personal_details.blade.php`)

Add conditional rendering at the top:

```blade
@if($fetchedData->is_company)
    {{-- Company Information Card --}}
    <div class="info-card" style="margin-bottom: 20px;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3><i class="fas fa-building"></i> Company Information</h3>
            <a href="{{ route('clients.edit', base64_encode(convert_uuencode($fetchedData->id))) }}" 
               class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
        <div class="card-body">
            <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                <div class="info-item">
                    <span class="info-label">Company Name:</span>
                    <span class="info-value">{{ $fetchedData->company_name ?? 'N/A' }}</span>
                </div>
                @if($fetchedData->trading_name)
                <div class="info-item">
                    <span class="info-label">Trading Name:</span>
                    <span class="info-value">{{ $fetchedData->trading_name }}</span>
                </div>
                @endif
                @if($fetchedData->ABN_number)
                <div class="info-item">
                    <span class="info-label">ABN:</span>
                    <span class="info-value">{{ $fetchedData->ABN_number }}</span>
                </div>
                @endif
                @if($fetchedData->ACN)
                <div class="info-item">
                    <span class="info-label">ACN:</span>
                    <span class="info-value">{{ $fetchedData->ACN }}</span>
                </div>
                @endif
                @if($fetchedData->company_type)
                <div class="info-item">
                    <span class="info-label">Business Type:</span>
                    <span class="info-value">{{ $fetchedData->company_type }}</span>
                </div>
                @endif
                @if($fetchedData->company_website)
                <div class="info-item">
                    <span class="info-label">Website:</span>
                    <span class="info-value">
                        <a href="{{ $fetchedData->company_website }}" target="_blank">
                            {{ $fetchedData->company_website }}
                        </a>
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Primary Contact Person Card --}}
    @if($fetchedData->contact_person_id)
        @php
            $contactPerson = \App\Models\Admin::find($fetchedData->contact_person_id);
        @endphp
        @if($contactPerson)
            <div class="info-card" style="margin-bottom: 20px;">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3><i class="fas fa-user-tie"></i> Primary Contact Person</h3>
                    <a href="{{ route('clients.detail', base64_encode(convert_uuencode($contactPerson->id))) }}" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt"></i> View Profile
                    </a>
                </div>
                <div class="card-body">
                    <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                        <div class="info-item">
                            <span class="info-label">Name:</span>
                            <span class="info-value">
                                <a href="{{ route('clients.detail', base64_encode(convert_uuencode($contactPerson->id))) }}">
                                    {{ $contactPerson->first_name }} {{ $contactPerson->last_name }}
                                </a>
                            </span>
                        </div>
                        @if($fetchedData->contact_person_position)
                        <div class="info-item">
                            <span class="info-label">Position:</span>
                            <span class="info-value">{{ $fetchedData->contact_person_position }}</span>
                        </div>
                        @endif
                        @if($contactPerson->email)
                        <div class="info-item">
                            <span class="info-label">Email:</span>
                            <span class="info-value">
                                <a href="mailto:{{ $contactPerson->email }}">{{ $contactPerson->email }}</a>
                            </span>
                        </div>
                        @endif
                        @if($contactPerson->phone)
                        <div class="info-item">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">{{ $contactPerson->phone }}</span>
                        </div>
                        @endif
                        @if($contactPerson->client_id)
                        <div class="info-item">
                            <span class="info-label">Client ID:</span>
                            <span class="info-value">{{ $contactPerson->client_id }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endif
@else
    {{-- Personal Information Card (existing code) --}}
    {{-- Keep all existing personal details display --}}
@endif
```

### 5. Create Company Edit Page (`resources/views/crm/clients/company_edit.blade.php`)

**Action**: Copy `edit.blade.php` and create `company_edit.blade.php`

**Key Changes**:
1. Update sidebar navigation (remove personal-specific sections, add company sections)
2. Replace personal sections with company sections
3. Add contact person search functionality
4. Remove: DOB, Gender, Marital Status, Skills, Education, Family, EOI sections
5. Add: Company Information, Contact Person, Business Address sections

**Sections to Include**:
- Company Information
- Primary Contact Person
- Business Address
- Company Contacts (Phone, Email)
- Company Documents
- Other Information

### 6. Matter Creation Form (AdminConsole)

**File**: `resources/views/AdminConsole/features/matter/create.blade.php`

#### Add Company Toggle Field
Add after line 57 (after "Nick Name" field):

```blade
<div class="col-12 col-md-6 col-lg-6">
    <div class="form-group">
        <label for="is_for_company">Is this matter for companies? <span class="span_req">*</span></label>
        <select name="is_for_company" id="is_for_company" class="form-control" data-valid="required">
            <option value="0" {{ old('is_for_company', '0') == '0' ? 'selected' : '' }}>No (For Personal Clients)</option>
            <option value="1" {{ old('is_for_company') == '1' ? 'selected' : '' }}>Yes (For Company Clients Only)</option>
        </select>
        <small class="form-text text-muted">
            If "Yes", this matter will only be available when creating matters for company clients. 
            If "No", it will only be available for personal clients.
        </small>
        @if ($errors->has('is_for_company'))
            <span class="custom-error" role="alert">
                <strong>{{ $errors->first('is_for_company') }}</strong>
            </span>
        @endif
    </div>
</div>
```

**File**: `resources/views/AdminConsole/features/matter/edit.blade.php`

#### Add Company Toggle Field (Same as create form)
Add the same field in the edit form, pre-populated with existing value:

```blade
<div class="col-12 col-md-6 col-lg-6">
    <div class="form-group">
        <label for="is_for_company">Is this matter for companies? <span class="span_req">*</span></label>
        <select name="is_for_company" id="is_for_company" class="form-control" data-valid="required">
            <option value="0" {{ ($fetchedData->is_for_company ?? false) ? '' : 'selected' }}>No (For Personal Clients)</option>
            <option value="1" {{ ($fetchedData->is_for_company ?? false) ? 'selected' : '' }}>Yes (For Company Clients Only)</option>
        </select>
        <small class="form-text text-muted">
            If "Yes", this matter will only be available when creating matters for company clients. 
            If "No", it will only be available for personal clients.
        </small>
        @if ($errors->has('is_for_company'))
            <span class="custom-error" role="alert">
                <strong>{{ $errors->first('is_for_company') }}</strong>
            </span>
        @endif
    </div>
</div>
```

### 7. Matter Selection Dropdowns (Filter by Client Type)

**Files to Update** (All places where matters are queried):

#### A. Client Detail Page - Matter Dropdown
**File**: `resources/views/crm/clients/detail.blade.php`

**Current Code** (around line 65):
```blade
@foreach(\App\Models\Matter::select('id','title')->where('status',1)->get() as $matterlist)
```

**Updated Code**:
```blade
@php
    // Filter matters based on client type
    $matterQuery = \App\Models\Matter::select('id','title')->where('status',1);
    
    if ($fetchedData->is_company) {
        // For companies: show only matters where is_for_company = true
        $matterQuery->where('is_for_company', true);
    } else {
        // For personal clients: show only matters where is_for_company = false or null
        $matterQuery->where(function($q) {
            $q->where('is_for_company', false)
              ->orWhereNull('is_for_company');
        });
    }
    $matterList = $matterQuery->get();
@endphp

@foreach($matterList as $matterlist)
    <option value="{{$matterlist->id}}">{{@$matterlist->title}}</option>
@endforeach
```

#### B. Convert Lead to Client Modal
**File**: `resources/views/crm/clients/modals/client-management.blade.php`

**Current Code** (line 65):
```blade
@foreach(\App\Models\Matter::select('id','title')->where('status',1)->get() as $matterlist)
```

**Updated Code**:
```blade
@php
    // Filter matters based on client type
    $matterQuery = \App\Models\Matter::select('id','title')->where('status',1);
    
    if (isset($fetchedData) && $fetchedData->is_company) {
        // For companies: show only matters where is_for_company = true
        $matterQuery->where('is_for_company', true);
    } else {
        // For personal clients: show only matters where is_for_company = false or null
        $matterQuery->where(function($q) {
            $q->where('is_for_company', false)
              ->orWhereNull('is_for_company');
        });
    }
    $matterList = $matterQuery->get();
@endphp

@foreach($matterList as $matterlist)
    <option value="{{$matterlist->id}}">{{@$matterlist->title}}</option>
@endforeach
```

#### C. Client Detail Info (Other Matter Dropdowns)
**File**: `resources/views/crm/clients/client_detail_info.blade.php`

**Locations**: Lines 773, 823, 897, 1005

**Update all occurrences**:
```blade
@php
    // Filter matters based on client type
    $matterQuery = \App\Models\Matter::select('id','title','nick_name')
        ->where('status',1)
        ->orderby('id','ASC');
    
    if (isset($fetchedData) && $fetchedData->is_company) {
        $matterQuery->where('is_for_company', true);
    } else {
        $matterQuery->where(function($q) {
            $q->where('is_for_company', false)
              ->orWhereNull('is_for_company');
        });
    }
    $matterList = $matterQuery->get();
@endphp

@foreach($matterList as $matterlist)
    {{-- existing option code --}}
@endforeach
```

**Note**: Update all 4 occurrences in this file (lines 773, 823, 897, 1005).

### 8. Matter Creation Modal (Client Detail Page)

**File**: `resources/views/crm/clients/modals/client-management.blade.php`

**Status**: ✅ **Updated** - Matter dropdown now filters based on client type (see section 7.B above)

**How it works**:
- Modal is triggered from client detail page
- Form collects: Migration Agent, Person Responsible, Person Assisting, Matter Selection (filtered)
- Submits to: `/clients/changetype/{id}/client`
- Creates `ClientMatter` record linked to company via `client_id`

**Verification**:
- Ensure modal is accessible from company detail page
- Test matter creation for company leads/clients (should only show company matters)
- Test matter creation for personal clients (should only show personal matters)
- Verify matter appears in sidebar dropdown for companies

---

## API Endpoints

### 1. Contact Person Search Endpoint

**Route**: `GET /api/search-contact-person`

**Parameters**:
- `q` (string, required): Search query (min 2 characters)
- `exclude_id` (integer, optional): Exclude this ID from results

**Response**:
```json
{
    "results": [
        {
            "id": 123,
            "text": "John Smith (john@example.com) - JOHN123",
            "first_name": "John",
            "last_name": "Smith",
            "email": "john@example.com",
            "phone": "+61400123456",
            "client_id": "JOHN123",
            "type": "client"
        }
    ]
}
```

**Implementation**: See Controller Changes section above.

---

## JavaScript Functionality

### Files to Create/Modify

1. **`public/js/leads/lead-form-company.js`** (NEW)
   - Toggle function for company fields
   - Select2 initialization for contact person search
   - Auto-fill functionality

2. **`public/js/clients/company-edit.js`** (NEW)
   - Company edit form functionality
   - Contact person search and auto-fill
   - Form validation

3. **`public/js/crm/clients/company-detail.js`** (NEW)
   - Company detail page interactions
   - Contact person profile linking

### Key Functions

```javascript
// Toggle company/personal fields
function toggleCompanyFields(isCompany) { ... }

// Initialize contact person search
function initContactPersonSearch() { ... }

// Auto-fill contact person details
function autoFillContactPerson(data) { ... }

// Clear contact person fields
function clearContactPersonFields() { ... }
```

---

## Step-by-Step Implementation

### Phase 1: Database Setup

1. **Create Migration**
   ```bash
   php artisan make:migration add_company_fields_to_admins_table
   ```
   
2. **Implement Migration**
   - Copy migration code from Database Changes section
   - Test migration: `php artisan migrate`
   - Test rollback: `php artisan migrate:rollback`

3. **Verify Database**
   - Check columns exist: `is_company`, `company_type`, `contact_person_position`, `contact_person_id`, `trading_name`, `ACN`
   - Verify default values
   - Check indexes (if added)

### Phase 2: Model Updates

1. **Update Admin Model**
   - Add fields to `$fillable` array
   - Add relationship methods (`contactPerson()`, `companiesAsContactPerson()`)
   - Add helper methods (`isCompany()`, `getDisplayNameAttribute()`)
   - Test model in Tinker:
     ```php
     php artisan tinker
     $admin = Admin::find(1);
     $admin->is_company = true;
     $admin->save();
     ```

### Phase 3: API Endpoint

1. **Create Search Endpoint**
   - Add method to `ClientsController` or create `ContactPersonController`
   - Add route to `routes/clients.php` or `routes/web.php`
   - Test endpoint:
     ```bash
     curl "http://localhost/api/search-contact-person?q=john"
     ```

### Phase 4: Lead Creation Form

1. **Add Company Toggle**
   - Insert toggle section in `create.blade.php`
   - Add company fields section (initially hidden)
   - Add contact person search section

2. **Add JavaScript**
   - Create `lead-form-company.js` or add to existing `lead-form.js`
   - Implement toggle function
   - Implement Select2 initialization
   - Implement auto-fill functionality

3. **Update Controller**
   - Modify `LeadController@store()` method
   - Add conditional validation
   - Add conditional data handling

4. **Test Lead Creation**
   - Test personal lead creation (should work as before)
   - Test company lead creation
   - Test toggle switching
   - Test contact person search and auto-fill

### Phase 5: Client Detail Page

1. **Update Sidebar Header**
   - Add conditional display for company name
   - Add contact person info section
   - Test display for both personal and company leads

2. **Update Tab Labels**
   - Conditionally rename tabs
   - Hide EOI/ROI tab for companies
   - Test tab visibility

3. **Update Personal Details Tab**
   - Add conditional rendering
   - Add company information card
   - Add contact person card
   - Test display

### Phase 6: Matter Creation Updates (2-3 hours)

1. **Update Matter Creation Form**
   - Add `is_for_company` toggle to `create.blade.php`
   - Add `is_for_company` toggle to `edit.blade.php`
   - Test form submission

2. **Update Matter Queries**
   - Update matter dropdown in `detail.blade.php`
   - Update matter dropdown in `client-management.blade.php` modal
   - Update all matter queries in `client_detail_info.blade.php` (4 locations)
   - Test filtering: Company clients see only company matters, personal clients see only personal matters

3. **Test Matter Creation**
   - Create matter with `is_for_company = true`
   - Create matter with `is_for_company = false`
   - Verify filtering works in client detail page
   - Verify filtering works in convert lead modal

### Phase 7: Client Edit Page

1. **Create Company Edit Page**
   - Copy `edit.blade.php` to `company_edit.blade.php`
   - Remove personal-specific sections
   - Add company-specific sections
   - Update sidebar navigation

2. **Update Controller**
   - Modify `ClientsController@edit()` to route to company edit page
   - Modify `ClientsController@update()` for company data handling

3. **Add JavaScript**
   - Create `company-edit.js`
   - Implement contact person search
   - Implement form validation

4. **Test Edit Functionality**
   - Test editing company leads
   - Test editing personal leads (should work as before)
   - Test contact person update

### Phase 8: Testing & Refinement

1. **Functional Testing**
   - Create personal lead → verify works
   - Create company lead → verify all fields save
   - Edit company lead → verify updates work
   - Search contact person → verify auto-fill
   - View company detail → verify display
   - Convert company lead to client → verify data preserved

2. **Edge Cases**
   - Company without contact person
   - Contact person deleted (orphaned reference)
   - Invalid ABN/ACN format
   - Very long company names
   - Special characters in company name

3. **Performance Testing**
   - Contact person search with many results
   - Page load time for company detail
   - Form submission time

---

## Implementation Checklist (Progress)

Use this checklist to track implementation progress. Tick items as they are completed.

**📊 Current Status:** Backend Complete (Phases 1-2) | Frontend Pending (Phases 3-7)

**✅ Completed:**
- Database migrations (3 migrations executed successfully)
- Company model and relationships
- Admin model updates (using separate `companies` table)
- LeadController and ClientsController backend logic
- Contact person search API endpoint
- All validation rules and data persistence

**⏳ Remaining:**
- View updates (lead creation form, detail page, edit page)
- JavaScript functionality (toggle, Select2 search)
- Matter filtering in dropdowns
- Testing and refinement

### Phase 1: Database & Models
- [x] Create migration: add `is_company` flag to `admins` table
- [x] Create migration: create `companies` table (separate table approach)
- [x] Create migration: add `is_for_company` to `matters`
- [x] Run migrations and verify columns/indexes ✅ (Batch 76, 77, 78)
- [x] Create `Company` model with relationships
- [x] Update `Admin` model fillable, relations, accessors (uses `company` relationship)
- [x] Update `Matter` model fillable and helper scope

### Phase 2: API & Backend
- [x] Add contact person search endpoint + route (`/api/search-contact-person`)
- [x] Update `LeadController@store` (conditional validation/data, saves to `companies` table)
- [x] Update `ClientsController@detail` and `@edit` routing (routes to `company_edit` for companies)
- [x] Update `ClientsController@update` (conditional validation/data, updates `companies` table)
- [ ] Update matter create/edit handling for `is_for_company` (backend ready, forms pending)
- [x] Add context-aware validation messages

### Phase 3: Lead Creation Form
- [ ] Add lead type toggle UI in `create.blade.php`
- [ ] Add company fields + contact person section
- [ ] Add Select2 search + auto-fill JS
- [ ] Toggle required fields correctly
- [ ] Verify lead creation for personal and company

### Phase 4: Client Detail Page
- [ ] Update sidebar header for company display
- [ ] Update tab labels and EOI/ROI visibility
- [ ] Add company info card in `personal_details.blade.php`
- [ ] Add contact person card and links

### Phase 5: Matter Creation Updates
- [ ] Add `is_for_company` to matter create form
- [ ] Add `is_for_company` to matter edit form
- [ ] Filter dropdowns in `detail.blade.php`
- [ ] Filter dropdowns in `client-management.blade.php`
- [ ] Filter dropdowns in `client_detail_info.blade.php` (all 4)
- [ ] Verify matter filtering for company vs personal

### Phase 6: Client Edit Page
- [ ] Create `company_edit.blade.php`
- [ ] Route company edits in `ClientsController@edit`
- [ ] Save company fields in `ClientsController@update`
- [ ] Add contact person search/auto-fill on edit page
- [ ] Verify company edit workflow

### Phase 7: Testing & Refinement
- [ ] Complete lead creation tests
- [ ] Complete client detail/edit tests
- [ ] Complete matter creation/filtering tests
- [ ] Validate edge cases (ABN/ACN, missing contact)
- [ ] Run basic performance checks

---

## Testing Checklist

### Lead Creation
- [ ] Personal lead creation works (backward compatibility)
- [ ] Company lead creation works
- [ ] Toggle between personal/company works
- [ ] Required field validation works for both types
- [ ] Contact person search works (by phone and email)
- [ ] Contact person auto-fill works
- [ ] Contact person is required (validation prevents saving without it)
- [ ] Form submission saves all company data correctly
- [ ] ABN validation works (11 digits, strips formatting)
- [ ] ACN validation works (9 digits, strips formatting)
- [ ] Company name uniqueness validation works (prevents duplicates)
- [ ] Error handling for invalid data

### Client Detail Page
- [ ] Personal lead detail shows correctly
- [ ] Company lead detail shows correctly
- [ ] Company name displays in sidebar
- [ ] Contact person info displays in sidebar
- [ ] Contact person link works
- [ ] Tab labels are correct
- [ ] EOI/ROI tab hidden for companies
- [ ] Company information card displays
- [ ] Contact person card displays

### Client Edit Page
- [ ] Personal lead edit works (backward compatibility)
- [ ] Company lead edit page loads
- [ ] Company fields display correctly
- [ ] Contact person search works in edit mode
- [ ] Form updates save correctly
- [ ] Validation works

### Matter Creation & Filtering
- [ ] Matter creation form has `is_for_company` toggle
- [ ] Matter edit form has `is_for_company` toggle
- [ ] Matter can be saved with `is_for_company = true`
- [ ] Matter can be saved with `is_for_company = false`
- [ ] Company clients see only company matters in dropdown
- [ ] Personal clients see only personal matters in dropdown
- [ ] Matter dropdown in client detail page filters correctly
- [ ] Matter dropdown in convert lead modal filters correctly
- [ ] Matter dropdown in client_detail_info.blade.php filters correctly (all 4 locations)
- [ ] Existing matters (with null `is_for_company`) show for personal clients
- [ ] Matter creation for company clients works
- [ ] Matter creation for personal clients works

### Data Integrity
- [ ] Existing personal leads unchanged
- [ ] Company leads have correct `is_company` flag
- [ ] Contact person relationship works
- [ ] No data loss on conversion
- [ ] Foreign key constraints work (if added)
- [ ] Existing matters unchanged (null `is_for_company` treated as false)
- [ ] New matters have correct `is_for_company` flag
- [ ] Matter filtering doesn't break existing functionality

---

## Things to Be Careful About

### 1. Backward Compatibility
- ⚠️ **CRITICAL**: All existing personal leads/clients must continue working
- Default `is_company` to `false` for all existing records
- Don't break existing validation rules
- Don't change existing form behavior for personal leads

### 2. Data Migration
- ⚠️ Existing records: `is_company` will be `false` by default (correct)
- ⚠️ If any existing records have `company_name` set, they won't automatically become companies
- ⚠️ Existing matters: `is_for_company` will be `null` (treated as `false` for personal clients)
- ⚠️ No data migration script needed - handled in query logic

### 3. Validation Rules
- ⚠️ Conditional validation must be correct
- ⚠️ Don't require personal fields when `is_company = true`
- ⚠️ Don't require company fields when `is_company = false`
- ⚠️ **Contact person is REQUIRED** when `is_company = true` - validation must prevent saving without it
- ⚠️ Contact person validation: ensure selected person exists and is a client/lead (role = 7)
- ⚠️ Contact person can be replaced/updated later in edit page
- ⚠️ **Company name must be UNIQUE** - validation must prevent duplicate company names
- ⚠️ Uniqueness check only applies to company leads/clients (`is_company = true`)
- ⚠️ On update: exclude current record from uniqueness check

### 4. Contact Person Relationship
- ⚠️ Contact person must be a client/lead (role = 7)
- ⚠️ Don't allow selecting staff members as contact persons
- ⚠️ Handle case where contact person is deleted (set to null)
- ⚠️ Prevent circular references (company can't be its own contact person)

### 5. Form Field Clearing
- ⚠️ When toggling from company to personal, clear company fields
- ⚠️ When toggling from personal to company, clear personal fields
- ⚠️ But preserve contact person selection if user switches back

### 6. Select2 Configuration
- ⚠️ Ensure Select2 is loaded before initialization
- ⚠️ Handle case where Select2 fails to load
- ⚠️ Minimum input length (2 characters) to avoid too many results
- ⚠️ Limit results to 20 to avoid performance issues

### 7. Database Constraints
- ⚠️ Foreign key constraint on `contact_person_id` (optional):
  - If added: prevents deleting contact person (use `onDelete('set null')`)
  - If not added: need manual cleanup if contact person deleted
- ⚠️ Index on `is_company` for better query performance
- ⚠️ Index on `contact_person_id` for relationship queries

### 8. URL Encoding
- ⚠️ Current system uses `base64_encode(convert_uuencode($id))` for URLs
- ⚠️ Ensure this works for company leads too
- ⚠️ Test routing with encoded IDs

### 9. Search Performance
- ⚠️ Contact person search should be fast (< 500ms)
- ⚠️ Use database indexes on searchable fields
- ⚠️ Limit results to prevent large responses
- ⚠️ Consider caching if search is slow

### 10. Display Logic
- ⚠️ Company name might be very long - handle truncation
- ⚠️ Contact person might not exist (handle null gracefully)
- ⚠️ **ABN/ACN validation**: Strip spaces/formatting before storing, validate digit count (11 for ABN, 9 for ACN)
- ⚠️ ABN/ACN stored as clean digits only - format for display if needed

---

## Matter Creation for Companies

### Current Matter Creation Process

Matters are created for clients through:
1. **Convert Lead to Client Modal** (`convertLeadToClientModal`) - Creates first matter when converting lead to client
2. **Add Matter to Existing Client** - Uses same modal/form to add additional matters

**Location**: `resources/views/crm/clients/modals/client-management.blade.php`

**Route**: `/clients/changetype/{id}/client` → `ClientsController@changetype()`

**Matter Creation Requirements**:
- Migration Agent (role 16) - Required
- Person Responsible (role 12) - Required  
- Person Assisting (role 13) - Required
- Matter Selection - Required (from `matters` table or "General Matter" checkbox with value=1)
- Office ID - Optional (defaults to user's office)

**Matter Storage**:
- Stored in `client_matters` table
- Links to client via `client_id` (references `admins.id`)
- Auto-generates `client_unique_matter_no`:
  - General Matter: `GN_1`, `GN_2`, etc.
  - Specific Matter: `{nick_name}_1`, `{nick_name}_2`, etc. (e.g., `482_1`, `407_1`)

**Matter Display**:
- Matter dropdown in sidebar (`detail.blade.php` lines 114-198)
- Matter list page (`clientsmatterslist.blade.php`)
- Matter selection in notes, documents, forms, etc.

### Company Matter Considerations

**Current System Compatibility**:
- ✅ Matter creation process works for companies (no changes needed)
- ✅ `client_matters.client_id` can link to company leads/clients
- ✅ Matter dropdown in sidebar works for companies
- ✅ Matter list page (`clientsmatterslist`) works for companies
- ✅ Matter creation modal accessible from company detail page

**Implementation Status**:
- **Matter creation flow works for companies** (no changes needed to `changetype()` or modal wiring)
- **Code changes still required** for this feature set:
  - Add `is_for_company` toggle to matter create/edit forms
  - Filter matter dropdowns by client type (as outlined above)
- Matter list queries work for companies (filters by `client_id`)

**Optional Enhancements** (Future):
- Add company badge/indicator in matter list
- Filter matters by client type (company/personal) in matter list page
- Display company name in matter list instead of personal name
- Company-specific matter types (if needed)
- Company-specific matter workflows

### Matter List Page Considerations

**File**: `resources/views/crm/clients/clientsmatterslist.blade.php`

**Current Display**:
- Shows client name (from `admins.first_name` + `admins.last_name`)
- Shows matter details, migration agent, person responsible, etc.

**For Companies**:
- Should display company name instead of personal name
- May need conditional logic: `if ($matter->client->is_company) { show company_name } else { show first_name + last_name }`

**Implementation**:
- Add conditional display in matter list table
- Update client name column to show company name for companies
- Add company badge/indicator (optional)

---

## Questions for Clarification

**Note**: Please answer these questions one at a time. After each answer, the plan will be updated accordingly.

**Progress**: 
- Question 1 answered ✅ - Matter filtering implementation
- Question 2 answered ✅ - Data migration for existing matters
- Question 3 answered ✅ - Contact person requirements (required, searchable by phone/email)
- Question 4 answered ✅ - ABN/ACN validation (normalize and strip formatting)
- Question 5 answered ✅ - Company name uniqueness (enforce uniqueness)
- Question 6 answered ✅ - Contact person permissions (allow multiple companies)
- Question 7 answered ✅ - Foreign key constraint (add later after testing)
- Question 8 answered ✅ - Display name priority (company name with contact person)
- Question 9 answered ✅ - Search/Filter (no filter, show all together)
- Question 10 answered ✅ - Error messages (context-aware messages)
- Question 11 answered ✅ - Company conversion (no conversion in MVP)

**Status**: All critical clarification questions answered! ✅ Plan is ready for implementation.

---

### Question 1: Matter Types for Companies ✅ ANSWERED

**Answer**: Option C - Add toggle to matter creation form

**Implementation**:
- ✅ Add `is_for_company` field to `matters` table
- ✅ Add toggle in matter creation/edit form (AdminConsole)
- ✅ Filter matters in dropdowns based on client type:
  - Company clients: Show only matters where `is_for_company = true`
  - Personal clients: Show only matters where `is_for_company = false` or `null`
- ✅ Update all matter queries to filter by client type

**Status**: Plan updated with all required changes.

---

### Question 2: Data Migration for Existing Matters ✅ ANSWERED

**Answer**: Option A - Treat `null` as `false` (for personal clients only)

**Implementation**:
- ✅ Migration sets default `is_for_company = false` for new matters
- ✅ Existing matters with `null` value are treated as `false` in queries
- ✅ Query logic: `where('is_for_company', false)->orWhereNull('is_for_company')` for personal clients
- ✅ No data migration script needed - handled in query logic

**Status**: Plan updated. No additional migration script required.

---

### Question 3: Contact Person Requirements ✅ ANSWERED

**Answer**: Contact person is required - Cannot save company lead without selecting a contact person. Can be replaced later.

**Additional Requirements**:
- ✅ Contact person must be searchable by **phone AND email** (not just email)
- ✅ Contact person is **required** - validation must prevent saving without it
- ✅ Contact person can be **replaced/updated later** in edit page
- ✅ Every company must have exactly one contact person

**Implementation Updates**:
- ✅ Update search endpoint to search by phone number in addition to email
- ✅ Add required validation for `contact_person_id` when `is_company = true`
- ✅ Update form to show required indicator and validation message
- ✅ Allow updating contact person in edit page (can replace existing one)

**Status**: Plan updated with required validation and phone/email search.

---

### Question 4: ABN/ACN Validation ✅ ANSWERED

**Answer**: Option C - Validate and normalize format, strip spaces and formatting while storing

**Implementation**:
- ✅ Strip all spaces and formatting from ABN/ACN before storing
- ✅ Validate ABN: Must be exactly 11 digits (after stripping)
- ✅ Validate ACN: Must be exactly 9 digits (after stripping)
- ✅ Store as clean digits only (no spaces, no formatting)
- ✅ Display can be formatted later if needed (e.g., "12 345 678 901")

**Validation Rules**:
- ABN: `regex:/^\d{11}$/` (after stripping spaces)
- ACN: `regex:/^\d{9}$/` (after stripping spaces)
- Both fields are optional (nullable)

**Status**: Plan updated with validation and normalization logic.

---

### Question 5: Company Name Uniqueness ✅ ANSWERED

**Answer**: Option B - Enforce uniqueness - Company name must be unique across all leads/clients

**Implementation**:
- ✅ Add unique validation rule for `company_name` when `is_company = true`
- ✅ Validation: `'company_name' => 'required|unique:admins,company_name'` (with exception for updates)
- ✅ Check uniqueness only for company leads/clients (ignore personal leads)
- ✅ Show clear error message if duplicate company name exists
- ✅ On edit: Exclude current record from uniqueness check

**Validation Rules**:
- Create: `'company_name' => 'required|unique:admins,company_name,NULL,id,is_company,1'`
- Update: `'company_name' => 'required|unique:admins,company_name,'.$id.',id,is_company,1'`
- Only check uniqueness for records where `is_company = true`

**Status**: Plan updated with uniqueness validation logic.

---

### Question 6: Contact Person Permissions ✅ ANSWERED

**Answer**: Option A - Yes, allow one person to be contact for multiple companies

**Implementation**:
- ✅ No restriction on `contact_person_id` - one person can be contact for multiple companies
- ✅ No validation needed to prevent multiple associations
- ✅ This is the default behavior - no code changes required
- ✅ Relationship `companiesAsContactPerson()` in Admin model supports this

**Status**: Plan confirmed - no changes needed, current implementation already supports this.

---

### Question 7: Foreign Key Constraint ✅ ANSWERED

**Answer**: Option C - Add later after testing

**Implementation**:
- ✅ No foreign key constraint in initial migration
- ✅ Test functionality without constraint first
- ✅ Add constraint in future migration if needed: `$table->foreign('contact_person_id')->references('id')->on('admins')->onDelete('set null');`
- ✅ Manual cleanup required if contact person is deleted (until constraint is added)

**Status**: Plan confirmed - constraint will be added later after testing.

---

### Question 8: Display Name Priority ✅ ANSWERED

**Answer**: Option C - Company name (primary) with contact person as secondary

**Implementation**:
- ✅ Display format: "Company Name (Contact: Person Name)"
- ✅ Update `getDisplayNameAttribute()` in Admin model to return this format for companies
- ✅ Update lead/client list views to use this display format
- ✅ Example: "ABC Corporation (Contact: John Smith)"
- ✅ If contact person not set: "ABC Corporation (Contact: Not Set)" or just "ABC Corporation"

**Code Example**:
```php
public function getDisplayNameAttribute(): string
{
    if ($this->is_company) {
        $companyName = $this->company_name ?? 'Unnamed Company';
        if ($this->contactPerson) {
            $contactName = trim($this->contactPerson->first_name . ' ' . $this->contactPerson->last_name);
            return "{$companyName} (Contact: {$contactName})";
        }
        return $companyName;
    }
    return trim($this->first_name . ' ' . $this->last_name);
}
```

**Status**: Plan updated with display format implementation.

---

### Question 9: Search/Filter in Lead/Client Lists ✅ ANSWERED

**Answer**: Option A - No filter, show all leads/clients together

**Implementation**:
- ✅ No filter dropdown or tabs needed
- ✅ All leads/clients (personal and company) shown together in same list
- ✅ Display format: Company leads show as "Company Name (Contact: Person Name)", personal leads show as "First Name Last Name"
- ✅ Users can identify company vs personal leads by the display format

**Status**: Plan confirmed - no filter implementation needed.

---

### Question 10: Error Messages ✅ ANSWERED

**Answer**: Option C - Context-aware messages

**Implementation**:
- ✅ Show company-specific error messages when `is_company = true`
- ✅ Show personal-specific error messages when `is_company = false`
- ✅ Examples:
  - Company: "Company name is required" instead of "This field is required"
  - Company: "Contact person must be selected" instead of "Contact person ID is required"
  - Personal: "First name is required" instead of "This field is required"
- ✅ Use custom validation messages in Laravel validation rules

**Code Example**:
```php
$messages = [
    'company_name.required' => 'Company name is required for company leads.',
    'contact_person_id.required' => 'A contact person must be selected for company leads.',
    'first_name.required' => 'First name is required for personal leads.',
    // ... more context-aware messages
];
```

**Status**: Plan updated with context-aware error message implementation.

---

### Question 11: Company Conversion ✅ ANSWERED

**Answer**: Option A - No conversion - Once created as personal/company, cannot change type

**Implementation**:
- ✅ No conversion functionality in MVP
- ✅ Once a lead is created as personal or company, the type cannot be changed
- ✅ If incorrect type is selected, user must create a new lead with correct type
- ✅ This prevents data integrity issues and simplifies implementation
- ✅ Can be considered as future enhancement if needed

**Status**: Plan confirmed - no conversion functionality needed in MVP.

---

### Question 12: Export/Import (Future Consideration)

**Question**: Should company leads be exportable/importable differently than personal leads?

**Context**: This is a future enhancement consideration.

**Recommendation**: Handle in export/import logic with conditional fields.

---

### Question 13: Reports (Future Consideration)

**Question**: Do you need company-specific reports (e.g., companies by type, companies by contact person)?

**Context**: This is a future enhancement consideration.

**Recommendation**: Future enhancement.

---

## Implementation Timeline Estimate

### Phase 1: Database & Models (3-4 hours)
- Migration 1: Add company fields to `admins` table
- Migration 2: Add `is_for_company` to `matters` table
- Model updates (Admin, Matter)
- Relationship testing

### Phase 2: API & Backend (4-5 hours)
- Search endpoint (contact person)
- Controller updates (LeadController, ClientsController)
- MatterController updates (store, update methods)
- Validation logic

### Phase 3: Lead Creation Form (4-5 hours)
- UI updates
- JavaScript functionality
- Testing

### Phase 4: Detail Page Updates (4-5 hours)
- Sidebar header updates
- Tab label updates and EOI/ROI visibility
- Company info and contact person cards

### Phase 5: Matter Creation Updates (2-3 hours)
- Add `is_for_company` toggle to matter create/edit forms
- Update matter dropdown queries to filter by client type
- Verify filtering behavior for company vs personal clients

### Phase 6: Client Edit Page (4-5 hours)
- Create `company_edit.blade.php` and update controller routing
- Add contact person search/auto-fill on edit page
- Validate and save company fields on update

### Phase 7: Testing & Refinement (4-5 hours)
- Functional testing (lead creation, client detail, matter creation)
- Edge case handling
- Matter filtering verification
- Bug fixes

**Total Estimate**: 25-32 hours

---

## Next Steps

1. **Review this document** and answer clarification questions
2. **Confirm approach** for each decision point
3. **Start with Phase 1** (Database & Models)
4. **Test incrementally** after each phase
5. **Get feedback** before moving to next phase

---

## Notes

- This plan assumes PostgreSQL database (based on existing migrations)
- All code examples use Laravel conventions
- JavaScript uses jQuery and Select2 (already in project)
- Follow existing code style and patterns
- Test on development environment first
- Backup database before running migrations

---

**Document Version**: 1.0  
**Last Updated**: 2026-01-26  
**Status**: Ready for Review
