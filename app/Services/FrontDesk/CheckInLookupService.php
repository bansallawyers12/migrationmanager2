<?php

namespace App\Services\FrontDesk;

use App\Models\Admin;
use Illuminate\Support\Collection;

class CheckInLookupService
{
    /**
     * Normalize a phone string: strip all non-digit characters.
     */
    public function normalizePhone(string $phone): string
    {
        return preg_replace('/\D/', '', $phone);
    }

    /**
     * Search clients and leads by phone (and optionally email).
     *
     * Returns a Collection of Admin models (type = client|lead).
     * When $email is provided the query is ANDed so the result set is narrower.
     */
    public function lookup(string $phone, ?string $email = null): Collection
    {
        $normalized = $this->normalizePhone($phone);

        if (empty($normalized)) {
            return collect();
        }

        $query = Admin::whereIn('type', ['client', 'lead'])
            ->whereNull('is_deleted')
            ->where(function ($q) use ($normalized) {
                // Match stored phone after stripping non-digits via REGEXP_REPLACE (MySQL 8+)
                // Fallback: also try literal phone column value
                $q->whereRaw("REGEXP_REPLACE(phone, '[^0-9]', '') LIKE ?", ["%{$normalized}%"])
                  ->orWhere('phone', 'like', "%{$normalized}%");
            });

        if (!empty($email)) {
            $query->where('email', 'like', '%' . trim($email) . '%');
        }

        return $query
            ->with('company')
            ->orderByRaw("CASE WHEN type = 'client' THEN 0 ELSE 1 END")
            ->limit(20)
            ->get();
    }

    /**
     * Format a single Admin for the wizard summary card.
     */
    public function formatForWizard(Admin $record): array
    {
        return [
            'id'           => $record->id,
            'type'         => $record->type,
            'name'         => $record->display_name,
            'email'        => $record->email,
            'phone'        => $record->phone,
            'is_company'   => (bool) $record->is_company,
            'company_name' => $record->company?->company_name,
        ];
    }
}
