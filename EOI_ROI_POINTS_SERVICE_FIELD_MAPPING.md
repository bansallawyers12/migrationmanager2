# PointsService Field Mapping - Actual vs Expected

## ✅ **FIELDS THAT EXIST**

### Admin (Client) Model
| Expected by PointsService | Actual Field in DB | Status |
|--------------------------|-------------------|---------|
| `naati_credential` | `naati_test` (0/1) | ✅ EXISTS (different name) |
| `ccl_credential` | N/A | ❌ MISSING |
| `professional_year` | `py_test` (0/1) | ✅ EXISTS (different name) |
| N/A | `naati_date` | ✅ EXISTS |
| N/A | `py_date` | ✅ EXISTS |

### ClientQualification Model
| Expected by PointsService | Actual Field in DB | Status |
|--------------------------|-------------------|---------|
| `australian_study` | N/A | ❌ MISSING |
| `duration_years` | Can be calculated from `start_date` & `finish_date` | ⚠️ CALCULABLE |
| `specialist_education` | N/A | ❌ MISSING |
| `stem_qualification` | N/A | ❌ MISSING |
| `regional_study` | N/A | ❌ MISSING |
| N/A | `country` | ✅ EXISTS |
| N/A | `relevant_qualification` | ✅ EXISTS |
| N/A | `level` | ✅ EXISTS |

### ClientExperience Model
| Expected by PointsService | Actual Field in DB | Status |
|--------------------------|-------------------|---------|
| `fte_multiplier` | N/A | ❌ MISSING |
| `country` | `job_country` | ✅ EXISTS (different name) |
| `start_date` | `job_start_date` | ✅ EXISTS (different name) |
| `end_date` | `job_finish_date` | ✅ EXISTS (different name) |
| N/A | `relevant_experience` | ✅ EXISTS |
| N/A | `job_type` | ✅ EXISTS |

### ClientSpouseDetail (Partner) Model  
| Expected by PointsService | Actual Field in DB | Status |
|--------------------------|-------------------|---------|
| `has_skills_assessment` | `spouse_has_skill_assessment` | ✅ EXISTS (different name) |
| `english_level` | Calculated from `spouse_has_english_score` + scores | ⚠️ CALCULABLE |
| `is_citizen` | N/A | ❌ MISSING |
| `has_pr` | N/A | ❌ MISSING |
| N/A | `spouse_test_type` | ✅ EXISTS |
| N/A | `spouse_listening_score` | ✅ EXISTS |
| N/A | `spouse_reading_score` | ✅ EXISTS |
| N/A | `spouse_writing_score` | ✅ EXISTS |
| N/A | `spouse_speaking_score` | ✅ EXISTS |
| N/A | `spouse_overall_score` | ✅ EXISTS |

---

## 🔧 **REQUIRED FIXES TO POINTSSERVICE**

### Fix #1: Map naati_test to naati_credential

**Location:** `app/Services/PointsService.php` line 478-481

**Current Code:**
```php
protected function hasNAATI(Admin $client): bool
{
    return $client->naati_credential === 1 
        || $client->ccl_credential === 1;
}
```

**Fixed Code:**
```php
protected function hasNAATI(Admin $client): bool
{
    // Check naati_test field (0/1 boolean in DB)
    return $client->naati_test == 1;
}
```

---

### Fix #2: Map py_test to professional_year

**Location:** `app/Services/PointsService.php` line 485-489

**Current Code:**
```php
protected function hasProfessionalYear(Admin $client): bool
{
    return $client->professional_year === 1;
}
```

**Fixed Code:**
```php
protected function hasProfessionalYear(Admin $client): bool
{
    // Check py_test field (0/1 boolean in DB)
    return $client->py_test == 1;
}
```

---

### Fix #3: Update hasAustralianStudy to use country field

**Location:** `app/Services/PointsService.php` line 438-447

**Current Code:**
```php
protected function hasAustralianStudy(Admin $client): bool
{
    $qualifications = $client->qualifications ?? collect();
    
    return $qualifications->contains(function ($qual) {
        return ($qual->country === 'Australia' || $qual->australian_study === 1) 
            && $qual->duration_years >= 2;
    });
}
```

**Fixed Code:**
```php
protected function hasAustralianStudy(Admin $client): bool
{
    $qualifications = $client->qualifications ?? collect();
    
    return $qualifications->contains(function ($qual) {
        // Check if country is Australia
        if ($qual->country !== 'Australia') {
            return false;
        }
        
        // Calculate duration from start_date and finish_date
        if (!$qual->start_date || !$qual->finish_date) {
            return false;
        }
        
        $start = Carbon::parse($qual->start_date);
        $finish = Carbon::parse($qual->finish_date);
        $durationYears = $finish->diffInYears($start);
        
        return $durationYears >= 2; // Minimum 2 years required
    });
}
```

---

### Fix #4: Handle missing specialist_education and stem_qualification

**Location:** `app/Services/PointsService.php` line 450-460

**Current Code:**
```php
protected function hasSpecialistEducation(Admin $client): bool
{
    $qualifications = $client->qualifications ?? collect();
    
    return $qualifications->contains(function ($qual) {
        return $qual->specialist_education === 1 
            || $qual->stem_qualification === 1;
    });
}
```

**Fixed Code:**
```php
protected function hasSpecialistEducation(Admin $client): bool
{
    // This feature requires manual entry by staff
    // For now, return false until specialist_education column is added
    // TODO: Add specialist_education boolean column to client_qualifications table
    
    return false;
    
    /* FUTURE: When column is added, uncomment:
    $qualifications = $client->qualifications ?? collect();
    
    return $qualifications->contains(function ($qual) {
        return ($qual->specialist_education ?? 0) === 1;
    });
    */
}
```

---

### Fix #5: Handle missing regional_study

**Location:** `app/Services/PointsService.php` line 463-472

**Current Code:**
```php
protected function hasRegionalStudy(Admin $client): bool
{
    $qualifications = $client->qualifications ?? collect();
    
    return $qualifications->contains(function ($qual) {
        return $qual->regional_study === 1;
    });
}
```

**Fixed Code:**
```php
protected function hasRegionalStudy(Admin $client): bool
{
    // This feature requires manual entry by staff
    // For now, return false until regional_study column is added
    // TODO: Add regional_study boolean column to client_qualifications table
    
    return false;
    
    /* FUTURE: When column is added, uncomment:
    $qualifications = $client->qualifications ?? collect();
    
    return $qualifications->contains(function ($qual) {
        return ($qual->regional_study ?? 0) === 1;
    });
    */
}
```

---

### Fix #6: Update FTE calculation to use actual field names

**Location:** `app/Services/PointsService.php` line 263-291

**Current Code:**
```php
protected function calculateFTEYears($experiences, Carbon $referenceDate): float
{
    $totalDays = 0;
    
    foreach ($experiences as $exp) {
        if (!$exp->start_date) {
            continue;
        }
        
        $start = Carbon::parse($exp->start_date);
        $end = $exp->end_date ? Carbon::parse($exp->end_date) : $referenceDate;
        
        // ... calculations ...
        
        // Apply FTE multiplier if available (default 1.0 for full-time)
        $fte = $exp->fte_multiplier ?? 1.0;
        $totalDays += ($days * $fte);
    }
    
    return round($totalDays / 365, 2);
}
```

**Fixed Code:**
```php
protected function calculateFTEYears($experiences, Carbon $referenceDate): float
{
    $totalDays = 0;
    
    foreach ($experiences as $exp) {
        // Use actual field names from ClientExperience model
        if (!$exp->job_start_date) {
            continue;
        }
        
        $start = Carbon::parse($exp->job_start_date);
        $end = $exp->job_finish_date ? Carbon::parse($exp->job_finish_date) : $referenceDate;
        
        // Don't count future experience
        if ($start->gt($referenceDate)) {
            continue;
        }
        
        $end = $end->min($referenceDate);
        
        $days = $start->diffInDays($end);
        
        // Default to full-time (1.0) since fte_multiplier column doesn't exist
        // Can be added later if part-time tracking is needed
        $fte = 1.0; // Assume all experience is full-time
        
        // If job_type field indicates part-time, could use a multiplier
        // For now, treat all as full-time
        if (isset($exp->job_type) && stripos($exp->job_type, 'part') !== false) {
            $fte = 0.5; // Rough estimate for part-time
        }
        
        $totalDays += ($days * $fte);
    }
    
    return round($totalDays / 365, 2);
}
```

---

### Fix #7: Update calculateEmploymentPoints to filter by job_country

**Location:** `app/Services/PointsService.php` line 235-258

**Current Code:**
```php
protected function calculateEmploymentPoints(Admin $client, Carbon $referenceDate): array
{
    $experiences = $client->experiences ?? collect();
    
    $auYears = $this->calculateFTEYears($experiences->where('country', 'Australia'), $referenceDate);
    $osYears = $this->calculateFTEYears($experiences->where('country', '!=', 'Australia'), $referenceDate);
    
    // ...
}
```

**Fixed Code:**
```php
protected function calculateEmploymentPoints(Admin $client, Carbon $referenceDate): array
{
    $experiences = $client->experiences ?? collect();
    
    // Use actual field name: job_country
    $auYears = $this->calculateFTEYears($experiences->where('job_country', 'Australia'), $referenceDate);
    $osYears = $this->calculateFTEYears($experiences->where('job_country', '!=', 'Australia'), $referenceDate);
    
    $auPoints = $this->getEmploymentPoints($auYears, 'australian');
    $osPoints = $this->getEmploymentPoints($osYears, 'overseas');
    
    // Combined cap at 20
    $totalPoints = min(20, $auPoints + $osPoints);
    
    return [
        'detail' => sprintf('AU: %.1f yrs (%d pts), OS: %.1f yrs (%d pts)', 
            $auYears, $auPoints, $osYears, $osPoints),
        'points' => $totalPoints,
        'au_years' => $auYears,
        'au_points' => $auPoints,
        'os_years' => $osYears,
        'os_points' => $osPoints,
    ];
}
```

---

### Fix #8: Update partner points calculation

**Location:** `app/Services/PointsService.php` line 495-544

**Current Code:**
```php
protected function calculatePartnerPoints(Admin $client, Carbon $referenceDate): array
{
    // Check marital status
    if (!$client->partner || $client->marital_status === 'Single') {
        return [ /* ... */ ];
    }

    $partner = $client->partner;
    
    // Partner is citizen/PR
    if ($partner->is_citizen || $partner->has_pr) {
        return [ /* ... */ ];
    }

    // Partner with skills assessment
    $hasSkills = $partner->has_skills_assessment === 1;
    $hasEnglish = $partner->english_level === 'competent' || $partner->english_level === 'proficient';
    
    // ...
}
```

**Fixed Code:**
```php
protected function calculatePartnerPoints(Admin $client, Carbon $referenceDate): array
{
    // Check marital status
    if (!$client->partner || $client->marital_status === 'Single') {
        return [
            'detail' => 'Single or partner is citizen/PR (10 pts)',
            'points' => 10,
            'category' => 'single_or_pr',
        ];
    }

    $partner = $client->partner;
    
    // TODO: Add is_citizen and has_pr fields to client_spouse_details table
    // For now, these can't be checked - default to treating as needing skills/English
    
    // Partner with skills assessment (use actual field name)
    $hasSkills = ($partner->spouse_has_skill_assessment ?? 0) == 1;
    
    // Partner English level - derive from scores if has test
    $hasEnglish = false;
    if (($partner->spouse_has_english_score ?? 0) == 1) {
        // Check if scores meet competent threshold (e.g., IELTS 6.0 or PTE 50)
        $overallScore = $partner->spouse_overall_score ?? 0;
        
        // Simplified check - in production, should check each component
        if ($partner->spouse_test_type === 'IELTS') {
            $hasEnglish = $overallScore >= 6.0; // Competent English
        } elseif ($partner->spouse_test_type === 'PTE') {
            $hasEnglish = $overallScore >= 50; // Competent English
        }
    }
    
    // Get partner age if DOB exists
    $partnerAge = $partner->dob ? Carbon::parse($partner->dob)->diffInYears($referenceDate) : 99;
    
    if ($hasSkills && $hasEnglish && $partnerAge < 45) {
        return [
            'detail' => 'Partner with skills (10 pts)',
            'points' => 10,
            'category' => 'skilled_partner',
        ];
    }

    // Partner with competent English only
    if ($hasEnglish) {
        return [
            'detail' => 'Partner with English (5 pts)',
            'points' => 5,
            'category' => 'english_partner',
        ];
    }

    return [
        'detail' => 'Partner (no points)',
        'points' => 0,
        'category' => 'none',
    ];
}
```

---

## 📊 **SUMMARY OF CHANGES NEEDED**

### Immediate Fixes (Field Name Mappings):
1. ✅ `naati_credential` → `naati_test`
2. ✅ `professional_year` → `py_test`
3. ✅ `country` (experience) → `job_country`
4. ✅ `start_date` (experience) → `job_start_date`
5. ✅ `end_date` (experience) → `job_finish_date`
6. ✅ `has_skills_assessment` (partner) → `spouse_has_skill_assessment`
7. ✅ Calculate `duration_years` from `start_date` and `finish_date`
8. ✅ Derive `english_level` (partner) from test scores

### Missing Features (Return false for now):
1. ❌ `ccl_credential` - Not captured, merge with NAATI check
2. ❌ `specialist_education` - Needs new column
3. ❌ `stem_qualification` - Needs new column  
4. ❌ `regional_study` - Needs new column
5. ❌ `is_citizen` (partner) - Needs new column
6. ❌ `has_pr` (partner) - Needs new column
7. ❌ `fte_multiplier` (experience) - Default to 1.0 (full-time)

---

## 🎯 **RECOMMENDED APPROACH**

### Phase 1: Quick Fix (Today) ⚡
- Update PointsService to use actual field names
- Return `false` for missing features
- Add proper null checks
- **Result:** Feature works with basic points calculation

### Phase 2: Database Enhancement (Later) 🔧
- Add missing columns to `client_qualifications`:
  - `specialist_education` BOOLEAN
  - `stem_qualification` BOOLEAN  
  - `regional_study` BOOLEAN
- Add missing columns to `client_spouse_details`:
  - `is_citizen` BOOLEAN
  - `has_pr` BOOLEAN
- Add to `client_experiences`:
  - `fte_multiplier` DECIMAL(3,2) default 1.0
- Update UI to allow staff to enter these values

---

## Date: October 12, 2025

