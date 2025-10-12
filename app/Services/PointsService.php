<?php

namespace App\Services;

use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Points Calculation Service for EOI/ROI
 * 
 * Calculates points based on Australian skilled migration criteria
 * for subclasses 189, 190, and 491
 */
class PointsService
{
    // Points thresholds for age brackets
    const AGE_BRACKETS = [
        [18, 24, 25],
        [25, 32, 30],
        [33, 39, 25],
        [40, 44, 15],
    ];

    // English level points
    const ENGLISH_COMPETENT = 0;
    const ENGLISH_PROFICIENT = 10;
    const ENGLISH_SUPERIOR = 20;

    // Nomination bonuses by subclass
    const NOMINATION_189 = 0;
    const NOMINATION_190 = 5;
    const NOMINATION_491 = 15;

    // Cache duration in minutes
    const CACHE_DURATION = 15;

    /**
     * Compute points for a client with optional subclass and forward-looking period
     * 
     * @param Admin $client The client to calculate points for
     * @param string|null $selectedSubclass EOI subclass (189, 190, 491)
     * @param int $monthsAhead Number of months ahead to check for warnings
     * @return array ['total' => int, 'breakdown' => array, 'warnings' => array]
     */
    public function compute(Admin $client, ?string $selectedSubclass = null, int $monthsAhead = 6): array
    {
        // Use cache to avoid expensive recalculations
        $cacheKey = "points_{$client->id}_{$selectedSubclass}_{$monthsAhead}";
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($client, $selectedSubclass, $monthsAhead) {
            return $this->calculatePoints($client, $selectedSubclass, $monthsAhead);
        });
    }

    /**
     * Clear cache for a specific client
     */
    public function clearCache(int $clientId): void
    {
        $subclasses = ['189', '190', '491', null];
        
        foreach ($subclasses as $subclass) {
            Cache::forget("points_{$clientId}_{$subclass}_6");
        }
    }

    /**
     * Perform the actual points calculation
     */
    protected function calculatePoints(Admin $client, ?string $selectedSubclass, int $monthsAhead): array
    {
        $breakdown = [];
        $warnings = [];
        
        // Determine invitation date (or use today)
        $invitationDate = $this->getInvitationDate($client);
        
        // 1. Age points
        $ageData = $this->calculateAgePoints($client, $invitationDate);
        $breakdown['age'] = $ageData;
        
        // 2. English points
        $englishData = $this->calculateEnglishPoints($client, $invitationDate);
        $breakdown['english'] = $englishData;
        
        // 3. Employment points (combined AU + Overseas, capped at 20)
        $employmentData = $this->calculateEmploymentPoints($client, $invitationDate);
        $breakdown['employment'] = $employmentData;
        
        // 4. Education points
        $educationData = $this->calculateEducationPoints($client);
        $breakdown['education'] = $educationData;
        
        // 5. Other bonuses (Australian study, specialist, regional, NAATI, PY)
        $bonusData = $this->calculateBonusPoints($client);
        $breakdown['bonuses'] = $bonusData;
        
        // 6. Partner points
        $partnerData = $this->calculatePartnerPoints($client, $invitationDate);
        $breakdown['partner'] = $partnerData;
        
        // 7. Nomination points based on subclass
        $nominationData = $this->calculateNominationPoints($selectedSubclass);
        $breakdown['nomination'] = $nominationData;
        
        // Calculate total
        $total = array_sum(array_column($breakdown, 'points'));
        
        // Generate warnings for upcoming changes
        $warnings = $this->generateWarnings($client, $invitationDate, $monthsAhead, $breakdown);
        
        return [
            'total' => $total,
            'breakdown' => $breakdown,
            'warnings' => $warnings,
        ];
    }

    /**
     * Calculate age points based on DOB
     */
    protected function calculateAgePoints(Admin $client, Carbon $referenceDate): array
    {
        if (!$client->dob) {
            return [
                'detail' => 'Date of birth not set',
                'points' => 0,
                'warning' => true,
            ];
        }

        $age = Carbon::parse($client->dob)->diffInYears($referenceDate);
        
        foreach (self::AGE_BRACKETS as [$min, $max, $points]) {
            if ($age >= $min && $age <= $max) {
                return [
                    'detail' => "{$age} years old",
                    'points' => $points,
                    'age' => $age,
                ];
            }
        }
        
        // Age 45+ or under 18
        return [
            'detail' => "{$age} years old (outside eligible range)",
            'points' => 0,
            'age' => $age,
            'warning' => $age >= 45,
        ];
    }

    /**
     * Calculate English language points
     */
    protected function calculateEnglishPoints(Admin $client, Carbon $referenceDate): array
    {
        // Load test scores (assuming relationship exists)
        $testScores = $client->testScores ?? collect();
        
        if ($testScores->isEmpty()) {
            return [
                'detail' => 'No English test recorded',
                'points' => self::ENGLISH_COMPETENT,
                'level' => 'competent',
                'warning' => true,
            ];
        }

        $validTests = $testScores->filter(function ($test) use ($referenceDate) {
            if (!$test->test_date) {
                return false;
            }
            
            $testDate = Carbon::parse($test->test_date);
            $expiryDate = $testDate->copy()->addYears(3); // Default 3-year validity
            
            return $referenceDate->lte($expiryDate);
        });

        if ($validTests->isEmpty()) {
            return [
                'detail' => 'English test expired',
                'points' => self::ENGLISH_COMPETENT,
                'level' => 'competent',
                'warning' => true,
            ];
        }

        // Get best valid test
        $bestTest = $validTests->sortByDesc(function ($test) {
            return $this->deriveEnglishLevel($test);
        })->first();

        $level = $this->deriveEnglishLevel($bestTest);
        $points = match ($level) {
            'superior' => self::ENGLISH_SUPERIOR,
            'proficient' => self::ENGLISH_PROFICIENT,
            default => self::ENGLISH_COMPETENT,
        };

        return [
            'detail' => ucfirst($level) . ' English',
            'points' => $points,
            'level' => $level,
            'test_type' => $bestTest->test_type ?? 'Unknown',
            'test_date' => $bestTest->test_date,
            'expiry_date' => Carbon::parse($bestTest->test_date)->addYears(3)->format('Y-m-d'),
        ];
    }

    /**
     * Derive English level from test scores
     * This is simplified - actual logic should check each component (L/R/W/S)
     */
    protected function deriveEnglishLevel($test): string
    {
        // Simplified logic - in production, check all 4 components
        $overallScore = $test->overall_score ?? 0;
        
        // Example thresholds (IELTS-based)
        if ($overallScore >= 8) {
            return 'superior';
        } elseif ($overallScore >= 7) {
            return 'proficient';
        }
        
        return 'competent';
    }

    /**
     * Calculate employment points (Australian + Overseas, capped at 20)
     */
    protected function calculateEmploymentPoints(Admin $client, Carbon $referenceDate): array
    {
        // Load employment history
        $experiences = $client->experiences ?? collect();
        
        $auYears = $this->calculateFTEYears($experiences->where('country', 'Australia'), $referenceDate);
        $osYears = $this->calculateFTEYears($experiences->where('country', '!=', 'Australia'), $referenceDate);
        
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

    /**
     * Calculate full-time equivalent years from experience records
     */
    protected function calculateFTEYears($experiences, Carbon $referenceDate): float
    {
        $totalDays = 0;
        
        foreach ($experiences as $exp) {
            if (!$exp->start_date) {
                continue;
            }
            
            $start = Carbon::parse($exp->start_date);
            $end = $exp->end_date ? Carbon::parse($exp->end_date) : $referenceDate;
            
            // Don't count future experience
            if ($start->gt($referenceDate)) {
                continue;
            }
            
            $end = $end->min($referenceDate);
            
            $days = $start->diffInDays($end);
            
            // Apply FTE multiplier if available (default 1.0 for full-time)
            $fte = $exp->fte_multiplier ?? 1.0;
            $totalDays += ($days * $fte);
        }
        
        return round($totalDays / 365, 2);
    }

    /**
     * Get employment points based on years and type
     */
    protected function getEmploymentPoints(float $years, string $type): int
    {
        if ($type === 'australian') {
            if ($years >= 8) return 20;
            if ($years >= 5) return 15;
            if ($years >= 3) return 10;
            if ($years >= 1) return 5;
        } else {
            // Overseas
            if ($years >= 8) return 15;
            if ($years >= 5) return 10;
            if ($years >= 3) return 5;
        }
        
        return 0;
    }

    /**
     * Calculate education points
     */
    protected function calculateEducationPoints(Admin $client): array
    {
        // Load qualifications
        $qualifications = $client->qualifications ?? collect();
        
        if ($qualifications->isEmpty()) {
            return [
                'detail' => 'No qualifications recorded',
                'points' => 0,
                'warning' => true,
            ];
        }

        // Find highest qualification
        $highest = $qualifications->sortByDesc(function ($qual) {
            return $this->getQualificationLevel($qual->level);
        })->first();

        $points = $this->getQualificationPoints($highest->level);
        
        return [
            'detail' => $highest->level ?? 'Unknown',
            'points' => $points,
            'qualification' => $highest->qualification_name ?? 'N/A',
        ];
    }

    /**
     * Get qualification level for sorting
     */
    protected function getQualificationLevel(string $level): int
    {
        $levels = [
            'Doctorate' => 4,
            'PhD' => 4,
            'Masters' => 3,
            'Bachelor' => 2,
            'Diploma' => 1,
            'Trade' => 1,
        ];
        
        foreach ($levels as $key => $value) {
            if (stripos($level, $key) !== false) {
                return $value;
            }
        }
        
        return 0;
    }

    /**
     * Get points for qualification level
     */
    protected function getQualificationPoints(string $level): int
    {
        if (stripos($level, 'Doctorate') !== false || stripos($level, 'PhD') !== false) {
            return 20;
        }
        
        if (stripos($level, 'Bachelor') !== false || stripos($level, 'Masters') !== false) {
            return 15;
        }
        
        if (stripos($level, 'Diploma') !== false || stripos($level, 'Trade') !== false) {
            return 10;
        }
        
        return 0;
    }

    /**
     * Calculate bonus points (Australian study, specialist education, regional, NAATI, PY)
     */
    protected function calculateBonusPoints(Admin $client): array
    {
        $bonuses = [];
        $totalPoints = 0;
        
        // Australian Study Requirement
        $ausStudy = $this->hasAustralianStudy($client);
        if ($ausStudy) {
            $bonuses[] = 'Australian Study (5 pts)';
            $totalPoints += 5;
        }
        
        // Specialist Education (STEM Masters/PhD)
        $specialist = $this->hasSpecialistEducation($client);
        if ($specialist) {
            $bonuses[] = 'Specialist Education (10 pts)';
            $totalPoints += 10;
        }
        
        // Regional Study
        $regional = $this->hasRegionalStudy($client);
        if ($regional) {
            $bonuses[] = 'Regional Study (5 pts)';
            $totalPoints += 5;
        }
        
        // NAATI/CCL
        $naati = $this->hasNAATI($client);
        if ($naati) {
            $bonuses[] = 'NAATI/CCL (5 pts)';
            $totalPoints += 5;
        }
        
        // Professional Year
        $py = $this->hasProfessionalYear($client);
        if ($py) {
            $bonuses[] = 'Professional Year (5 pts)';
            $totalPoints += 5;
        }
        
        return [
            'detail' => !empty($bonuses) ? implode(', ', $bonuses) : 'None',
            'points' => $totalPoints,
            'items' => $bonuses,
        ];
    }

    /**
     * Check if client has Australian study requirement
     */
    protected function hasAustralianStudy(Admin $client): bool
    {
        $qualifications = $client->qualifications ?? collect();
        
        return $qualifications->contains(function ($qual) {
            return ($qual->country === 'Australia' || $qual->australian_study === 1) 
                && $qual->duration_years >= 2; // Minimum 2 years
        });
    }

    /**
     * Check if client has specialist education (STEM)
     */
    protected function hasSpecialistEducation(Admin $client): bool
    {
        $qualifications = $client->qualifications ?? collect();
        
        return $qualifications->contains(function ($qual) {
            return $qual->specialist_education === 1 
                || $qual->stem_qualification === 1;
        });
    }

    /**
     * Check if client has regional study
     */
    protected function hasRegionalStudy(Admin $client): bool
    {
        $qualifications = $client->qualifications ?? collect();
        
        return $qualifications->contains(function ($qual) {
            return $qual->regional_study === 1;
        });
    }

    /**
     * Check if client has NAATI/CCL
     */
    protected function hasNAATI(Admin $client): bool
    {
        // Check in client points or qualifications
        return $client->naati_credential === 1 
            || $client->ccl_credential === 1;
    }

    /**
     * Check if client has Professional Year
     */
    protected function hasProfessionalYear(Admin $client): bool
    {
        return $client->professional_year === 1;
    }

    /**
     * Calculate partner points
     */
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
        
        // Partner is citizen/PR
        if ($partner->is_citizen || $partner->has_pr) {
            return [
                'detail' => 'Partner is citizen/PR (10 pts)',
                'points' => 10,
                'category' => 'single_or_pr',
            ];
        }

        // Partner with skills assessment
        $hasSkills = $partner->has_skills_assessment === 1;
        $hasEnglish = $partner->english_level === 'competent' || $partner->english_level === 'proficient';
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

    /**
     * Calculate nomination points based on subclass
     */
    protected function calculateNominationPoints(?string $subclass): array
    {
        $points = match ($subclass) {
            '491' => self::NOMINATION_491,
            '190' => self::NOMINATION_190,
            default => self::NOMINATION_189,
        };

        $detail = match ($subclass) {
            '491' => 'Subclass 491 nomination (15 pts)',
            '190' => 'Subclass 190 nomination (5 pts)',
            '189' => 'Subclass 189 - no nomination',
            default => 'No subclass selected',
        };

        return [
            'detail' => $detail,
            'points' => $points,
            'subclass' => $subclass ?? 'N/A',
        ];
    }

    /**
     * Generate warnings for upcoming changes
     */
    protected function generateWarnings(Admin $client, Carbon $referenceDate, int $monthsAhead, array $breakdown): array
    {
        $warnings = [];
        $thresholdDate = $referenceDate->copy()->addMonths($monthsAhead);
        
        // Age bracket changes
        if ($client->dob) {
            $age = Carbon::parse($client->dob)->diffInYears($referenceDate);
            $nextBirthdayDate = Carbon::parse($client->dob)->addYears($age + 1);
            
            // Check if turning 33, 40, or 45 within threshold
            $criticalAges = [33, 40, 45];
            foreach ($criticalAges as $criticalAge) {
                $criticalDate = Carbon::parse($client->dob)->addYears($criticalAge);
                
                if ($criticalDate->between($referenceDate, $thresholdDate)) {
                    $currentPoints = $breakdown['age']['points'];
                    $futureAge = $criticalAge;
                    $futurePoints = $this->getAgePoints($futureAge);
                    
                    $warnings[] = [
                        'type' => 'age_bracket_change',
                        'date' => $criticalDate->format('Y-m-d'),
                        'message' => "Age will be {$futureAge} on {$criticalDate->format('d/m/Y')} - points will change from {$currentPoints} to {$futurePoints}",
                        'severity' => $futurePoints < $currentPoints ? 'high' : 'info',
                    ];
                }
            }
        }
        
        // English test expiry
        if (isset($breakdown['english']['expiry_date'])) {
            $expiryDate = Carbon::parse($breakdown['english']['expiry_date']);
            
            if ($expiryDate->between($referenceDate, $thresholdDate)) {
                $warnings[] = [
                    'type' => 'english_expiry',
                    'date' => $expiryDate->format('Y-m-d'),
                    'message' => "English test will expire on {$expiryDate->format('d/m/Y')} - take new test before invitation",
                    'severity' => 'high',
                ];
            }
        }
        
        // Work experience thresholds approaching
        $this->addEmploymentWarnings($warnings, $breakdown, $client, $referenceDate, $thresholdDate);
        
        return $warnings;
    }

    /**
     * Add employment threshold warnings
     */
    protected function addEmploymentWarnings(array &$warnings, array $breakdown, Admin $client, Carbon $referenceDate, Carbon $thresholdDate): void
    {
        $auYears = $breakdown['employment']['au_years'] ?? 0;
        $osYears = $breakdown['employment']['os_years'] ?? 0;
        
        // Check AU thresholds: 1, 3, 5, 8 years
        $auThresholds = [1, 3, 5, 8];
        foreach ($auThresholds as $threshold) {
            if ($auYears < $threshold && $auYears > ($threshold - 0.5)) {
                $monthsToThreshold = ($threshold - $auYears) * 12;
                
                if ($monthsToThreshold <= 6) {
                    $targetDate = $referenceDate->copy()->addMonths((int)$monthsToThreshold);
                    
                    if ($targetDate->lte($thresholdDate)) {
                        $currentPoints = $breakdown['employment']['au_points'];
                        $futurePoints = $this->getEmploymentPoints($threshold, 'australian');
                        
                        $warnings[] = [
                            'type' => 'employment_threshold',
                            'date' => $targetDate->format('Y-m-d'),
                            'message' => "Will reach {$threshold} years AU experience on {$targetDate->format('d/m/Y')} - points will increase from {$currentPoints} to {$futurePoints}",
                            'severity' => 'info',
                        ];
                    }
                }
            }
        }
    }

    /**
     * Get age points for a specific age
     */
    protected function getAgePoints(int $age): int
    {
        foreach (self::AGE_BRACKETS as [$min, $max, $points]) {
            if ($age >= $min && $age <= $max) {
                return $points;
            }
        }
        
        return 0;
    }

    /**
     * Get invitation date (or default to today)
     */
    protected function getInvitationDate(Admin $client): Carbon
    {
        // Check if client has upcoming invitation date from EOI records
        $eoiReference = $client->eoiReferences()->latest()->first();
        
        if ($eoiReference && $eoiReference->eoi_invitation_date) {
            return Carbon::parse($eoiReference->eoi_invitation_date);
        }
        
        return Carbon::today();
    }
}

