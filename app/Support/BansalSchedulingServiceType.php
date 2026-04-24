<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Maps CRM "nature of enquiry" (noe_id / enquiry_item) to Bansal schedule API service_type
 * and builds Melbourne-only extras (is_paid, preferred_language) for get-datetime-backend
 * and get-disabled-datetime. Adelaide uses no extras so payloads stay unchanged for legacy behaviour.
 */
class BansalSchedulingServiceType
{
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

    public static function fromEnquiryItem(mixed $enquiryItem): string
    {
        $key = (int) $enquiryItem;

        return self::ENQUIRY_TO_SERVICE_TYPE[$key] ?? 'permanent-residency';
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
