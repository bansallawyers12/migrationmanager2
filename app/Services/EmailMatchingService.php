<?php

namespace App\Services;

use App\Models\Admin;
use App\Support\StaffClientVisibility;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Suggests client+matter assignments for unattached .msg files.
 *
 * All queries start from Admin::query() + restrictAdminEloquentQuery() so staff
 * visibility rules are always enforced. Never starts from ClientMatter::query() or
 * ClientEmail::query() as the root.
 */
class EmailMatchingService
{
    private const CONFIDENCE_MATTER_REF  = 92;
    private const CONFIDENCE_CLIENT_REF  = 88;
    private const CONFIDENCE_EMAIL_MATCH = 85;
    private const CONFIDENCE_BOOST       = 5;
    private const CONFIDENCE_CAP         = 99;
    private const HIGH_CONFIDENCE        = 80;

    /** Matter ref: NICK_N  e.g. PSA_1, GN_2, EMPLOYER_SPONSORED_1 */
    private const MATTER_REF_PATTERN = '/\b([A-Z][A-Z0-9]*_\d{1,5})\b/i';

    /** Client ref: PREFIX4 + YY + 5digits  e.g. JOHN2500337, VIPL2400001 */
    private const CLIENT_REF_PATTERN = '/\b([A-Z]{2,8}\d{7,11})\b/';

    private array $companyDomains;

    public function __construct()
    {
        $this->companyDomains = config('crm.company_email_domains', [
            'bansalimmigration.com.au',
            'bansaleducation.com.au',
            'bansallawyers.com.au',
        ]);
    }

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    /**
     * Return up to 5 ranked suggestions for a single parsed email.
     *
     * @param  array                    $parsed  Python /email/parse response
     * @param  Authenticatable|null     $user    Authenticated staff (for visibility)
     * @return array{
     *   suggestions: list<array{
     *     client_id: int,
     *     client_name: string,
     *     client_reference: string,
     *     client_matter_id: int,
     *     matter_no: string,
     *     matter_display: string,
     *     confidence: int,
     *     match_signals: list<string>
     *   }>,
     *   suggested_mail_type: string
     * }
     */
    public function suggest(array $parsed, ?Authenticatable $user = null): array
    {
        $subject = (string) ($parsed['subject'] ?? '');
        $snippet = mb_substr((string) ($parsed['text_content'] ?? ''), 0, 500);
        $senderEmail = strtolower(trim((string) ($parsed['sender_email'] ?? '')));
        $recipients  = array_map(
            fn (string $r) => strtolower(trim($r)),
            (array) ($parsed['recipients'] ?? [])
        );

        $allResults = [];

        try {
            $allResults = array_merge(
                $allResults,
                $this->matchByMatterRef($subject, $snippet, $user)
            );
        } catch (\Exception $e) {
            Log::warning('EmailMatchingService: matter-ref match failed', ['error' => $e->getMessage()]);
        }

        try {
            $allResults = array_merge(
                $allResults,
                $this->matchByClientRef($subject, $snippet, $user)
            );
        } catch (\Exception $e) {
            Log::warning('EmailMatchingService: client-ref match failed', ['error' => $e->getMessage()]);
        }

        try {
            $nonCompanyAddresses = $this->filterCompanyAddresses(
                array_filter(array_merge([$senderEmail], $recipients))
            );
            if ($nonCompanyAddresses !== []) {
                $allResults = array_merge(
                    $allResults,
                    $this->matchByEmailAddress($nonCompanyAddresses, $user)
                );
            }
        } catch (\Exception $e) {
            Log::warning('EmailMatchingService: email-address match failed', ['error' => $e->getMessage()]);
        }

        $merged      = $this->mergeAndBoost($allResults);
        $suggestions = array_slice(
            array_values(array_filter($merged, fn ($s) => $s['client_matter_id'] > 0)),
            0,
            5
        );

        return [
            'suggestions'          => $suggestions,
            'suggested_mail_type'  => $this->detectMailType($senderEmail),
        ];
    }

    /**
     * Whether the given confidence meets the high-confidence threshold.
     */
    public static function isHighConfidence(int $confidence): bool
    {
        return $confidence >= self::HIGH_CONFIDENCE;
    }

    // -------------------------------------------------------------------------
    // Match strategies
    // -------------------------------------------------------------------------

    private function matchByMatterRef(string $subject, string $snippet, ?Authenticatable $user): array
    {
        $text = $subject . ' ' . $snippet;
        preg_match_all(self::MATTER_REF_PATTERN, $text, $matches);
        $refs = array_unique(array_map('strtoupper', $matches[1] ?? []));

        if ($refs === []) {
            return [];
        }

        $results = [];
        foreach ($refs as $ref) {
            $query = Admin::query()
                ->join('client_matters', 'client_matters.client_id', '=', 'admins.id')
                ->leftJoin('matters', 'matters.id', '=', 'client_matters.sel_matter_id')
                ->leftJoin('companies', 'companies.admin_id', '=', 'admins.id')
                ->whereIn('admins.type', ['client', 'lead'])
                ->whereNull('admins.is_deleted')
                ->where('admins.is_archived', 0)
                ->where('client_matters.matter_status', 1)
                ->whereRaw('UPPER(client_matters.client_unique_matter_no) = ?', [strtoupper($ref)])
                ->select(
                    'admins.id as client_id',
                    'admins.first_name',
                    'admins.last_name',
                    'admins.is_company',
                    'admins.client_id as client_reference',
                    'admins.type as record_type',
                    'client_matters.id as client_matter_id',
                    'client_matters.client_unique_matter_no as matter_no',
                    'matters.title as matter_title',
                    DB::raw("NULL as company_name")
                );

            StaffClientVisibility::restrictAdminEloquentQuery($query);

            foreach ($query->get() as $row) {
                $results[] = $this->buildSuggestion($row, self::CONFIDENCE_MATTER_REF, ["Matter ref: {$ref}"]);
            }
        }

        return $results;
    }

    private function matchByClientRef(string $subject, string $snippet, ?Authenticatable $user): array
    {
        $text = $subject . ' ' . $snippet;
        preg_match_all(self::CLIENT_REF_PATTERN, $text, $matches);
        $refs = array_unique($matches[1] ?? []);

        if ($refs === []) {
            return [];
        }

        $results = [];
        foreach ($refs as $ref) {
            $query = Admin::query()
                ->leftJoin('client_matters', function ($join) {
                    $join->on('client_matters.client_id', '=', 'admins.id')
                         ->where('client_matters.matter_status', 1);
                })
                ->leftJoin('matters', 'matters.id', '=', 'client_matters.sel_matter_id')
                ->leftJoin('companies', 'companies.admin_id', '=', 'admins.id')
                ->whereIn('admins.type', ['client', 'lead'])
                ->whereNull('admins.is_deleted')
                ->where('admins.is_archived', 0)
                ->whereRaw('UPPER(admins.client_id) = ?', [strtoupper($ref)])
                ->orderByDesc('client_matters.id')
                ->select(
                    'admins.id as client_id',
                    'admins.first_name',
                    'admins.last_name',
                    'admins.is_company',
                    'admins.client_id as client_reference',
                    'admins.type as record_type',
                    'client_matters.id as client_matter_id',
                    'client_matters.client_unique_matter_no as matter_no',
                    'matters.title as matter_title',
                    DB::raw("NULL as company_name")
                );

            StaffClientVisibility::restrictAdminEloquentQuery($query);

            foreach ($query->get() as $row) {
                $results[] = $this->buildSuggestion($row, self::CONFIDENCE_CLIENT_REF, ["Client ref: {$ref}"]);
            }
        }

        return $results;
    }

    private function matchByEmailAddress(array $emails, ?Authenticatable $user): array
    {
        $results = [];

        foreach ($emails as $email) {
            if (! str_contains($email, '@')) {
                continue;
            }

            // Primary admins.email
            $query = Admin::query()
                ->leftJoin('client_matters', function ($join) {
                    $join->on('client_matters.client_id', '=', 'admins.id')
                         ->where('client_matters.matter_status', 1);
                })
                ->leftJoin('matters', 'matters.id', '=', 'client_matters.sel_matter_id')
                ->leftJoin('companies', 'companies.admin_id', '=', 'admins.id')
                ->whereIn('admins.type', ['client', 'lead'])
                ->whereNull('admins.is_deleted')
                ->where('admins.is_archived', 0)
                ->whereRaw('LOWER(admins.email) = ?', [$email])
                ->orderByDesc('client_matters.id')
                ->select(
                    'admins.id as client_id',
                    'admins.first_name',
                    'admins.last_name',
                    'admins.is_company',
                    'admins.client_id as client_reference',
                    'admins.type as record_type',
                    'client_matters.id as client_matter_id',
                    'client_matters.client_unique_matter_no as matter_no',
                    'matters.title as matter_title',
                    DB::raw("NULL as company_name")
                );

            StaffClientVisibility::restrictAdminEloquentQuery($query);

            foreach ($query->get() as $row) {
                $results[] = $this->buildSuggestion($row, self::CONFIDENCE_EMAIL_MATCH, ["Email: {$email}"]);
            }

            // Secondary client_emails (admin_id is the FK to admins.id)
            $query2 = Admin::query()
                ->join('client_emails', 'client_emails.admin_id', '=', 'admins.id')
                ->leftJoin('client_matters', function ($join) {
                    $join->on('client_matters.client_id', '=', 'admins.id')
                         ->where('client_matters.matter_status', 1);
                })
                ->leftJoin('matters', 'matters.id', '=', 'client_matters.sel_matter_id')
                ->leftJoin('companies', 'companies.admin_id', '=', 'admins.id')
                ->whereIn('admins.type', ['client', 'lead'])
                ->whereNull('admins.is_deleted')
                ->where('admins.is_archived', 0)
                ->whereRaw('LOWER(client_emails.email) = ?', [$email])
                ->orderByDesc('client_matters.id')
                ->select(
                    'admins.id as client_id',
                    'admins.first_name',
                    'admins.last_name',
                    'admins.is_company',
                    'admins.client_id as client_reference',
                    'admins.type as record_type',
                    'client_matters.id as client_matter_id',
                    'client_matters.client_unique_matter_no as matter_no',
                    'matters.title as matter_title',
                    DB::raw("NULL as company_name")
                );

            StaffClientVisibility::restrictAdminEloquentQuery($query2);

            foreach ($query2->get() as $row) {
                $results[] = $this->buildSuggestion($row, self::CONFIDENCE_EMAIL_MATCH, ["Secondary email: {$email}"]);
            }
        }

        return $results;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function buildSuggestion(object $row, int $confidence, array $signals): array
    {
        $clientName = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''));
        $matterTitle = ($row->matter_title && $row->matter_title !== 'General') ? $row->matter_title : 'General Matter';
        $matterNo    = $row->matter_no ?? '';
        $matterDisplay = $matterNo
            ? "{$matterTitle} ({$matterNo})"
            : $matterTitle;

        return [
            'client_id'        => (int) $row->client_id,
            'client_name'      => $clientName,
            'client_reference' => (string) ($row->client_reference ?? ''),
            'record_type'      => (string) ($row->record_type ?? 'client'),
            'client_matter_id' => (int) ($row->client_matter_id ?? 0),
            'matter_no'        => $matterNo,
            'matter_display'   => $matterDisplay,
            'confidence'       => $confidence,
            'match_signals'    => $signals,
        ];
    }

    /**
     * Merge results from all strategies, boost when the same client+matter appears
     * in multiple signals, sort descending by confidence, deduplicate.
     */
    private function mergeAndBoost(array $allResults): array
    {
        // Key: "{client_id}:{client_matter_id}"
        $merged = [];

        foreach ($allResults as $result) {
            $key = $result['client_id'] . ':' . $result['client_matter_id'];

            if (! isset($merged[$key])) {
                $merged[$key] = $result;
            } else {
                // Merge signals and apply boost for multiple hits
                $merged[$key]['match_signals'] = array_unique(
                    array_merge($merged[$key]['match_signals'], $result['match_signals'])
                );
                $newConf = min(
                    $merged[$key]['confidence'] + self::CONFIDENCE_BOOST,
                    self::CONFIDENCE_CAP
                );
                $merged[$key]['confidence'] = max($merged[$key]['confidence'], $newConf);
            }
        }

        usort($merged, fn ($a, $b) => $b['confidence'] <=> $a['confidence']);

        return array_values($merged);
    }

    /**
     * Remove addresses that belong to the company (we don't want to match against
     * our own staff/domain as a "client").
     * Domain values in config may include or omit the leading '@'; we normalise here.
     */
    private function filterCompanyAddresses(array $addresses): array
    {
        return array_values(array_filter($addresses, function (string $addr) {
            return ! $this->isCompanyAddress($addr);
        }));
    }

    /**
     * Detect inbox vs sent based on sender domain.
     */
    private function detectMailType(string $senderEmail): string
    {
        return $this->isCompanyAddress($senderEmail) ? 'sent' : 'inbox';
    }

    private function isCompanyAddress(string $email): bool
    {
        $email = strtolower($email);
        foreach ($this->companyDomains as $domain) {
            // Normalise: strip leading '@' if present, then check "@domain" presence
            $domain = '@' . ltrim(strtolower($domain), '@');
            if (str_contains($email, $domain)) {
                return true;
            }
        }
        return false;
    }
}
