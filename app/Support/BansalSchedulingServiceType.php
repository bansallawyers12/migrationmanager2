<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Maps CRM "nature of enquiry" (noe_id / enquiry_item) to Bansal schedule API service_type
 * and builds Melbourne-only extras (is_paid, preferred_language) for get-datetime-backend
 * and get-disabled-datetime. Adelaide uses no extras so payloads stay unchanged for legacy behaviour.
 * Melbourne Family Visas (11) and Citizenship (12) use employer-sponsored timeslots and
 * employer_sponsored enquiry_type on Bansal add-appointment sync (CRM keeps original labels locally).
 */
class BansalSchedulingServiceType
{
    /** Melbourne NOE ids routed through Employer Sponsored calendar on the Bansal website. */
    private const MELBOURNE_EMPLOYER_SPONSORED_NOE_IDS = [11, 12];
    /**
     * @var array<int, string>
     */
    public const ENQUIRY_TO_SERVICE_TYPE = [
        1 => 'permanent-residency',
        2 => 'temporary-residency',
        3 => 'jrp-skill-assessment',
        4 => 'tourist-visa',
        5 => 'education-visa',
        6 => 'complex-matters',
        7 => 'visa-cancellation',
        8 => 'international-migration',
        9 => 'eoi-roi',
        10 => 'employer-sponsored',
        11 => 'family-visas',
        12 => 'citizenship',
    ];

    public static function fromEnquiryItem(mixed $enquiryItem, ?string $location = null): string
    {
        $key = (int) $enquiryItem;

        if (self::melbourneUsesEmployerSponsoredRouting($key, $location)) {
            return 'employer-sponsored';
        }

        return self::ENQUIRY_TO_SERVICE_TYPE[$key] ?? 'permanent-residency';
    }

    /**
     * enquiry_type for Bansal add-appointment / re-sync API only.
     * Local CRM records keep family_visas / citizenship for display and reporting.
     */
    public static function bansalEnquiryTypeForApi(mixed $noeId, ?string $location, string $crmEnquiryType): string
    {
        if (self::melbourneUsesEmployerSponsoredRouting((int) $noeId, $location)) {
            return 'employer_sponsored';
        }

        return $crmEnquiryType;
    }

    public static function melbourneUsesEmployerSponsoredRouting(int $noeId, ?string $location): bool
    {
        if ($location === null || $location === '') {
            return false;
        }

        return strtolower(trim($location)) === 'melbourne'
            && in_array($noeId, self::MELBOURNE_EMPLOYER_SPONSORED_NOE_IDS, true);
    }

    /**
     * @return array{0: bool|null, 1: string|null} is_paid and preferred_language; nulls mean omit (non-Melbourne)
     */
    public static function melbourneApiExtras(Request $request, string $location, int $formServiceId): array
    {
        if ($location !== 'melbourne') {
            return [null, null];
        }
        $isPaid = $request->has('is_paid')
            ? $request->boolean('is_paid')
            : in_array($formServiceId, [2, 3], true);
        $lang = trim((string) $request->input('preferred_language', ''));
        if ($lang === '') {
            $lang = 'English';
        }

        return [$isPaid, $lang];
    }
}
