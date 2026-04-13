<?php

namespace App\Services\BansalAppointmentSync;

use App\Models\AppointmentConsultant;
use Illuminate\Support\Facades\Log;

class ConsultantAssignmentService
{
    /**
     * Assign consultant based on appointment details.
     *
     * Adelaide: all bookings for that office → Adelaide calendar.
     * Melbourne: calendar follows service line (tourist, education, Ajay, JRP, employer-sponsored, Kunal, etc.).
     */
    public function assignConsultant(array $appointmentData): ?AppointmentConsultant
    {
        $calendarType = $this->determineCalendarType($appointmentData);

        if (!$calendarType) {
            Log::warning('Could not determine calendar type for appointment', [
                'appointment_id' => $appointmentData['id'] ?? null,
                'noe_id' => $appointmentData['noe_id'] ?? null,
                'service_id' => $appointmentData['service_id'] ?? null,
                'location' => $appointmentData['location'] ?? null,
            ]);

            return null;
        }

        $consultant = AppointmentConsultant::where('calendar_type', $calendarType)
            ->where('is_active', true)
            ->first();

        if (!$consultant) {
            Log::error('No active consultant found for calendar type', [
                'calendar_type' => $calendarType,
            ]);
        }

        return $consultant;
    }

    /**
     * Determine calendar type based on appointment data.
     */
    protected function determineCalendarType(array $appointment): ?string
    {
        $location = $this->normalizeLocationString($appointment['location'] ?? null);
        $inpersonAddress = $appointment['inperson_address'] ?? null;
        $serviceId = $appointment['service_id'] ?? null;
        $noeId = $this->resolveNoeId($appointment);

        if ($this->isAdelaideOffice($location, $inpersonAddress)) {
            return 'adelaide';
        }

        if (! $this->shouldUseMelbourneCalendars($location, $inpersonAddress)) {
            return null;
        }

        // Tourist
        if ($noeId === 4) {
            return 'tourist';
        }

        // Education (incl. student / dependent flows that map to NOE 5)
        if ($noeId === 5) {
            return 'education';
        }

        // Complex matters → Ajay
        if ($noeId === 6) {
            return 'ajay';
        }

        // Visa cancellation / refusals → Ajay
        if ($noeId === 7) {
            return 'ajay';
        }

        // TR (485) + JRP / Skill assessment → same JRP calendar (paid or free)
        if (in_array($noeId, [2, 3], true)) {
            return 'jrp';
        }

        // Outside Australia / international migration → Employer sponsored calendar
        if ($noeId === 8) {
            return 'paid';
        }

        // EOI / ROI
        if ($noeId === 9) {
            return 'kunal';
        }

        // Employer sponsored (494, 482, 186, DAMA)
        if ($noeId === 10) {
            return 'paid';
        }

        // GSM (491, 190, 189, 191) → Kunal calendar (explicit NOE; not overseas/employer split)
        if ($noeId === 1) {
            return 'kunal';
        }

        // Unknown NOE: classify from text (EOI/GSM/employer keywords), else employer-sponsored (legacy PR)
        if ($noeId === null) {
            return $this->melbournePrCalendarType($appointment, $serviceId);
        }

        return 'paid';
    }

    /**
     * Classify Melbourne calendar when `noe_id` is missing (sync/legacy): EOI/GSM → kunal, employer keywords → paid, else paid (old PR bucket).
     */
    protected function melbournePrCalendarType(array $appointment, $serviceId): string
    {
        $haystack = $this->buildSearchableText($appointment);

        // Outside Australia (paid overseas path)
        if (($appointment['specific_service'] ?? null) === 'overseas-enquiry') {
            return 'paid';
        }
        if ((int) $serviceId === 3) {
            return 'paid';
        }

        // EOI / ROI
        if (preg_match('/\beoi\b|expression\s+of\s+interest|\broi\b|points\s+table|skillselect/i', $haystack)) {
            return 'kunal';
        }

        // GSM subclasses / general skilled
        if (preg_match('/\b(491|190|189|191)\b|gsm|general\s+skilled|skilled\s+nominated|subclass\s*(491|190|189|191)/i', $haystack)) {
            return 'kunal';
        }

        // Employer sponsored (494, 482, 186, 407, DAMA, etc.)
        if (preg_match('/\b(482|494|186|407)\b|dama|employer\s+sponsored|sponsored\s+visa|labour\s+agreement|tss\b|subclass\s*(482|494|186|407)/i', $haystack)) {
            return 'paid';
        }

        // Legacy PR / default → Employer sponsored calendar (replaces old PR calendar)
        return 'paid';
    }

    protected function buildSearchableText(array $appointment): string
    {
        $parts = [
            $appointment['enquiry_type'] ?? '',
            $appointment['service_type'] ?? '',
            $appointment['enquiry_details'] ?? '',
        ];

        return strtolower(implode(' ', array_filter($parts, fn ($p) => $p !== null && $p !== '')));
    }

    /**
     * Resolve NOE when only Bansal slug / display service_type is present.
     */
    protected function resolveNoeId(array $appointment): ?int
    {
        $raw = $appointment['noe_id'] ?? null;
        if ($raw !== null && $raw !== '') {
            return (int) $raw;
        }

        $slug = $appointment['service_type'] ?? null;
        if (! is_string($slug) || $slug === '') {
            return null;
        }

        $s = strtolower(trim($slug));

        $fromSlug = match ($s) {
            'permanent-residency' => 1,
            'gsm-visas' => 1,
            'temporary-residency' => 2,
            'jrp-skill-assessment' => 3,
            'tourist-visa' => 4,
            'education-visa' => 5,
            'complex-matters' => 6,
            'visa-cancellation' => 7,
            'international-migration' => 8,
            'eoi-roi' => 9,
            'employer-sponsored' => 10,
            default => null,
        };

        if ($fromSlug !== null) {
            return $fromSlug;
        }

        // Display-style labels from CRM / API
        return match (true) {
            str_contains($s, 'permanent') && str_contains($s, 'residency') => 1,
            str_contains($s, 'gsm') || (str_contains($s, '491') && str_contains($s, '190')) => 1,
            str_contains($s, 'temporary') && str_contains($s, 'residency') => 2,
            str_contains($s, 'jrp') || str_contains($s, 'skill assessment') => 3,
            str_contains($s, 'tourist') => 4,
            str_contains($s, 'education') || str_contains($s, 'student') => 5,
            str_contains($s, 'complex') || str_contains($s, 'aat') || str_contains($s, 'art,') || str_contains($s, 'protection') || str_contains($s, 'federal case') => 6,
            str_contains($s, 'cancellation') || str_contains($s, 'refusal') || str_contains($s, 'noicc') => 7,
            str_contains($s, 'outside australia') || str_contains($s, 'india') || str_contains($s, 'international') || str_contains($s, 'europe') || str_contains($s, 'canada') => 8,
            (str_contains($s, 'eoi') || str_contains($s, 'roi')) && ! str_contains($s, 'employer') => 9,
            str_contains($s, 'employer sponsored') || str_contains($s, '494') || str_contains($s, '482') || str_contains($s, '186') || str_contains($s, 'dama') => 10,
            default => null,
        };
    }

    protected function normalizeLocationString(?string $location): ?string
    {
        if ($location === null || $location === '') {
            return null;
        }

        return strtolower(trim($location));
    }

    protected function isAdelaideOffice(?string $location, $inpersonAddress): bool
    {
        if ($inpersonAddress === 1 || $inpersonAddress === '1') {
            return true;
        }

        return $location === 'adelaide';
    }

    /**
     * Melbourne office + legacy default when office is unspecified (empty inperson).
     */
    protected function shouldUseMelbourneCalendars(?string $location, $inpersonAddress): bool
    {
        if ($this->isAdelaideOffice($location, $inpersonAddress)) {
            return false;
        }

        if ($location === 'melbourne') {
            return true;
        }

        if ($inpersonAddress === 2 || $inpersonAddress === '2') {
            return true;
        }

        $inpersonEmpty = $inpersonAddress === null || $inpersonAddress === '';

        return $inpersonEmpty;
    }

    /**
     * Get consultant by calendar type
     */
    public function getConsultantByCalendarType(string $calendarType): ?AppointmentConsultant
    {
        return AppointmentConsultant::where('calendar_type', $calendarType)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get all active consultants
     */
    public function getAllConsultants(): \Illuminate\Database\Eloquent\Collection
    {
        return AppointmentConsultant::where('is_active', true)->get();
    }
}
