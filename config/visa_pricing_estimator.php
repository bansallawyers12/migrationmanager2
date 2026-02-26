<?php

/**
 * Visa Pricing Estimator Configuration
 *
 * Data aligned with Australian Department of Home Affairs Visa Pricing Estimator:
 * https://immi.homeaffairs.gov.au/visas/visa-pricing-estimator
 *
 * Prices are in AUD. Update annually (typically 1 July). Source:
 * https://immi.homeaffairs.gov.au/visas/getting-a-visa/fees-and-charges
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default charges for additional applicants (2025-26 rates)
    |--------------------------------------------------------------------------
    */
    'additional_applicant_charge_18_plus' => 4685,
    'additional_applicant_charge_u18' => 2345,

    /*
    |--------------------------------------------------------------------------
    | Visa list - aligned with Home Affairs Visa Pricing Estimator dropdown
    |--------------------------------------------------------------------------
    | id: unique identifier for API/estimate
    | label: display name (e.g. "Partner visa (subclass 309/100)")
    | subclass: visa subclass code(s)
    | stream: optional stream (First, Second, Third, Main, etc.)
    | base_charge: primary applicant charge in AUD
    | additional_18_plus: charge per additional applicant 18+ (null = use default)
    | additional_u18: charge per additional applicant under 18 (null = use default)
    */
    'visas' => [
        [
            'id' => 'partner_309_100',
            'label' => 'Partner (Provisional and Migrant) visa (subclass 309/100)',
            'subclass' => '309/100',
            'stream' => null,
            'base_charge' => 9365,
            'additional_18_plus' => 4685,
            'additional_u18' => 2345,
        ],
        [
            'id' => 'partner_820_801',
            'label' => 'Partner (Temporary and Permanent) visa (subclass 820/801)',
            'subclass' => '820/801',
            'stream' => null,
            'base_charge' => 9365,
            'additional_18_plus' => 4685,
            'additional_u18' => 2345,
        ],
        [
            'id' => 'skilled_independent_189',
            'label' => 'Skilled Independent visa (subclass 189)',
            'subclass' => '189',
            'stream' => null,
            'base_charge' => 4910,
            'additional_18_plus' => 2455,
            'additional_u18' => 1230,
        ],
        [
            'id' => 'skilled_nominated_190',
            'label' => 'Skilled Nominated visa (subclass 190)',
            'subclass' => '190',
            'stream' => null,
            'base_charge' => 4910,
            'additional_18_plus' => 2455,
            'additional_u18' => 1230,
        ],
        [
            'id' => 'skilled_regional_491',
            'label' => 'Skilled Work Regional (Provisional) visa (subclass 491)',
            'subclass' => '491',
            'stream' => null,
            'base_charge' => 4910,
            'additional_18_plus' => 2455,
            'additional_u18' => 1230,
        ],
        [
            'id' => 'skills_in_demand_482',
            'label' => 'Skills in Demand visa (subclass 482)',
            'subclass' => '482',
            'stream' => null,
            'base_charge' => 3210,
            'additional_18_plus' => 3210,
            'additional_u18' => 805,
        ],
        [
            'id' => 'employer_nomination_186',
            'label' => 'Employer Nomination Scheme visa (subclass 186)',
            'subclass' => '186',
            'stream' => null,
            'base_charge' => 4910,
            'additional_18_plus' => 2455,
            'additional_u18' => 1230,
        ],
        [
            'id' => 'student_500',
            'label' => 'Student visa (subclass 500)',
            'subclass' => '500',
            'stream' => null,
            'base_charge' => 2000,
            'additional_18_plus' => 1225,
            'additional_u18' => 400,
        ],
        [
            'id' => 'working_holiday_417_first',
            'label' => 'Working Holiday visa (subclass 417) - First',
            'subclass' => '417',
            'stream' => 'First',
            'base_charge' => 670,
            'additional_18_plus' => 670,
            'additional_u18' => null,
        ],
        [
            'id' => 'working_holiday_417_second',
            'label' => 'Working Holiday visa (subclass 417) - Second',
            'subclass' => '417',
            'stream' => 'Second',
            'base_charge' => 670,
            'additional_18_plus' => 670,
            'additional_u18' => null,
        ],
        [
            'id' => 'working_holiday_417_third',
            'label' => 'Working Holiday visa (subclass 417) - Third',
            'subclass' => '417',
            'stream' => 'Third',
            'base_charge' => 670,
            'additional_18_plus' => 670,
            'additional_u18' => null,
        ],
        [
            'id' => 'work_and_holiday_462',
            'label' => 'Work and Holiday visa (subclass 462)',
            'subclass' => '462',
            'stream' => null,
            'base_charge' => 670,
            'additional_18_plus' => 670,
            'additional_u18' => null,
        ],
        [
            'id' => 'visitor_600',
            'label' => 'Visitor visa (subclass 600)',
            'subclass' => '600',
            'stream' => null,
            'base_charge' => 200,
            'additional_18_plus' => 200,
            'additional_u18' => 50,
        ],
        [
            'id' => 'retirement_410',
            'label' => 'Retirement visa (subclass 410)',
            'subclass' => '410',
            'stream' => null,
            'base_charge' => 435,
            'additional_18_plus' => 435,
            'additional_u18' => null,
        ],
        [
            'id' => 'temporary_work_400',
            'label' => 'Temporary Work (Short Stay Specialist) visa (subclass 400)',
            'subclass' => '400',
            'stream' => null,
            'base_charge' => 430,
            'additional_18_plus' => 430,
            'additional_u18' => 110,
        ],
        [
            'id' => 'training_407',
            'label' => 'Training visa (subclass 407)',
            'subclass' => '407',
            'stream' => null,
            'base_charge' => 430,
            'additional_18_plus' => 430,
            'additional_u18' => 110,
        ],
        [
            'id' => 'temporary_activity_408',
            'label' => 'Temporary Activity visa (subclass 408)',
            'subclass' => '408',
            'stream' => null,
            'base_charge' => 430,
            'additional_18_plus' => 430,
            'additional_u18' => 110,
        ],
        [
            'id' => 'sponsored_parent_870_3yr',
            'label' => 'Sponsored Parent (Temporary) visa (subclass 870) - 3 years',
            'subclass' => '870',
            'stream' => '3 years',
            'base_charge' => 6070,
            'additional_18_plus' => null,
            'additional_u18' => null,
        ],
        [
            'id' => 'sponsored_parent_870_5yr',
            'label' => 'Sponsored Parent (Temporary) visa (subclass 870) - 5 years',
            'subclass' => '870',
            'stream' => '5 years',
            'base_charge' => 12140,
            'additional_18_plus' => null,
            'additional_u18' => null,
        ],
    ],

];
