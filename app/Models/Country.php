<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Country extends Model
{
    protected $table = 'countries';

    protected $fillable = [
        'id', 'sortname', 'name', 'phonecode', 'status', 'created_at', 'updated_at',
    ];

    /**
     * Exclude countries with no usable dial code. MySQL may store phonecode as string;
     * PostgreSQL often uses integer — comparing to '' there causes SQLSTATE[22P02].
     */
    public function scopeWhereDialCodePresent(Builder $query): Builder
    {
        $query->whereNotNull('phonecode');

        return match ($query->getConnection()->getDriverName()) {
            'pgsql' => $query->whereRaw('phonecode::text <> ?', ['']),
            'sqlite' => $query->whereRaw('CAST(phonecode AS TEXT) <> ?', ['']),
            default => $query->where('phonecode', '!=', ''),
        };
    }

    public static function getAllWithPhoneCodes()
    {
        $ttl = (int) config('phone.cache_ttl_seconds', 0);

        $callback = fn () => static::query()
            ->whereDialCodePresent()
            ->orderBy('name')
            ->get();

        if ($ttl > 0) {
            return Cache::remember('countries_with_phonecodes', $ttl, $callback);
        }

        return $callback();
    }

    /**
     * @return \Illuminate\Support\Collection<int, self>
     */
    public static function getPreferredCountries()
    {
        $preferredCodes = config('phone.popular_countries', ['AU', 'IN', 'PK', 'NP', 'GB', 'CA']);
        $ttl = (int) config('phone.cache_ttl_seconds', 0);

        $upperPreferred = array_values(array_unique(array_map(
            static fn ($c) => strtoupper(trim((string) $c)),
            is_array($preferredCodes) ? $preferredCodes : []
        )));

        $callback = function () use ($upperPreferred) {
            if ($upperPreferred === []) {
                return collect();
            }

            $placeholders = implode(',', array_fill(0, count($upperPreferred), '?'));

            $countries = static::query()
                ->whereDialCodePresent()
                ->whereRaw('UPPER(TRIM(sortname)) IN ('.$placeholders.')', $upperPreferred)
                ->get();

            return $countries->sortBy(function ($country) use ($upperPreferred) {
                $idx = array_search(strtoupper(trim((string) $country->sortname)), $upperPreferred, true);

                return $idx === false ? 999 : $idx;
            })->values();
        };

        if ($ttl > 0) {
            return Cache::remember('preferred_countries_phone', $ttl, $callback);
        }

        return $callback();
    }

    public static function getByPhoneCode(string $phoneCode): ?self
    {
        $clean = ltrim(trim($phoneCode), '+');
        if ($clean === '' || ! ctype_digit($clean)) {
            return null;
        }

        $ttl = (int) config('phone.cache_ttl_seconds', 0);

        $callback = fn () => static::where('phonecode', $clean)->first();

        if ($ttl > 0) {
            return Cache::remember('country_by_phonecode_' . $clean, $ttl, $callback);
        }

        return $callback();
    }

    public static function isValidPhoneCode(string $phoneCode): bool
    {
        $clean = ltrim(trim($phoneCode), '+');
        if ($clean === '' || ! ctype_digit($clean)) {
            return false;
        }

        $ttl = (int) config('phone.cache_ttl_seconds', 0);

        $callback = fn () => static::where('phonecode', $clean)->exists();

        if ($ttl > 0) {
            return Cache::remember('valid_phonecode_' . $clean, $ttl, $callback);
        }

        return $callback();
    }

    public static function clearDialCodeCache(): void
    {
        Cache::forget('countries_with_phonecodes');
        Cache::forget('preferred_countries_phone');
        foreach (static::query()->pluck('phonecode') as $code) {
            $code = ltrim((string) $code, '+');
            if ($code !== '') {
                Cache::forget('country_by_phonecode_' . $code);
                Cache::forget('valid_phonecode_' . $code);
            }
        }
    }
}