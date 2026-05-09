<?php

return [
    'default_country_code' => env('DEFAULT_COUNTRY_CODE', '+61'),

    'default_country' => env('DEFAULT_COUNTRY', 'au'),

    /**
     * ISO 3166-1 alpha-2 (uppercase). Order = optgroup "Popular" order.
     */
    'popular_countries' => ['AU', 'IN', 'PK', 'NP', 'GB', 'CA', 'US'],

    /**
     * Cache TTL for Country dial-code queries (seconds). 0 = no cache.
     */
    'cache_ttl_seconds' => (int) env('PHONE_COUNTRY_CACHE_TTL', 0),

    'validate_against_db' => env('PHONE_VALIDATE_COUNTRY_CODE_DB', false),
];
