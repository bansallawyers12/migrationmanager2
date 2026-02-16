<?php

/**
 * Visa type sheet configuration.
 * Add new visa types here to enable additional sheets (Visitor, Student, PR, etc.).
 *
 * Each visa type requires:
 * - reference_table: e.g. client_tr_references
 * - reminders_table: e.g. tr_matter_reminders
 * - checklist_status_column: column on client_matters for checklist tab status (e.g. tr_checklist_status)
 * - matter identification + stage mappings
 */
return [
    'tr' => [
        'title' => 'TR Sheet',
        'route' => 'clients.sheets.visa-type',
        'reference_table' => 'client_tr_references',
        'reference_model' => \App\Models\ClientTrReference::class,
        'reference_alias' => 'tr_ref',
        'reminders_table' => 'tr_matter_reminders',
        'checklist_status_column' => 'tr_checklist_status',
        'session_prefix' => 'tr_sheet_',

        'matter_nick_names' => ['tvg', 'pt'],
        'matter_title_patterns' => ['485'],

        'ongoing_stages' => [
            'Initial consultation',
            'Initial Payment and Documents Received',
            'Cost Agreement, form 956 and First email Sent',
            'Cost Agreement, form 956 Received',
            'Pending documents and payment requested',
            'Documents Completed and Preparing for Lodgement',
            'Payment verified',
            'Ready for Lodgement/Draft Application sent for confirmation',
            'Draft Application confirmation received',
        ],
        'lodged_stages' => ['Application Lodged', 'Immi Request Received'],
        'checklist_early_stages' => ['Checklist'],
        'discontinue_stages' => ['Decision Received', 'File Closed', 'Ready to Close', 'Withdrawn', 'Refund', 'Discontinued'],
        'checklist_convert_to_client_stage' => 'Initial Payment and Documents Received',
    ],

    'visitor' => [
        'title' => 'Visitor Visa Sheet',
        'route' => 'clients.sheets.visa-type',
        'reference_table' => 'client_visitor_references',
        'reference_model' => \App\Models\ClientVisitorReference::class,
        'reference_alias' => 'visitor_ref',
        'reminders_table' => 'visitor_matter_reminders',
        'checklist_status_column' => 'visitor_checklist_status',
        'session_prefix' => 'visitor_sheet_',

        'matter_nick_names' => ['outside australia', 'vbv', 'vvd', 'vsf', 'inside australia'],
        'matter_title_patterns' => ['600 -'],

        'ongoing_stages' => [
            'Initial consultation',
            'Initial Payment and Documents Received',
            'Cost Agreement, form 956 and First email Sent',
            'Cost Agreement, form 956 Received',
            'Pending documents and payment requested',
            'Documents Completed and Preparing for Lodgement',
            'Payment verified',
            'Ready for Lodgement/Draft Application sent for confirmation',
            'Draft Application confirmation received',
        ],
        'lodged_stages' => ['Application Lodged', 'Immi Request Received'],
        'checklist_early_stages' => ['Checklist'],
        'discontinue_stages' => ['Decision Received', 'File Closed', 'Ready to Close', 'Withdrawn', 'Refund', 'Discontinued'],
        'checklist_convert_to_client_stage' => 'Initial Payment and Documents Received',
    ],

    'student' => [
        'title' => 'Student Visa Sheet',
        'route' => 'clients.sheets.visa-type',
        'reference_table' => 'client_student_references',
        'reference_model' => \App\Models\ClientStudentReference::class,
        'reference_alias' => 'student_ref',
        'reminders_table' => 'student_matter_reminders',
        'checklist_status_column' => 'student_checklist_status',
        'session_prefix' => 'student_sheet_',

        'matter_nick_names' => ['s', 'sse'],
        'matter_title_patterns' => ['500 -'],

        'ongoing_stages' => [
            'Initial consultation',
            'Initial Payment and Documents Received',
            'Cost Agreement, form 956 and First email Sent',
            'Cost Agreement, form 956 Received',
            'Pending documents and payment requested',
            'Documents Completed and Preparing for Lodgement',
            'Payment verified',
            'Ready for Lodgement/Draft Application sent for confirmation',
            'Draft Application confirmation received',
        ],
        'lodged_stages' => ['Application Lodged', 'Immi Request Received'],
        'checklist_early_stages' => ['Checklist'],
        'discontinue_stages' => ['Decision Received', 'File Closed', 'Ready to Close', 'Withdrawn', 'Refund', 'Discontinued'],
        'checklist_convert_to_client_stage' => 'Initial Payment and Documents Received',
    ],

    'pr' => [
        'title' => 'PR Application Sheet',
        'route' => 'clients.sheets.visa-type',
        'reference_table' => 'client_pr_references',
        'reference_model' => \App\Models\ClientPrReference::class,
        'reference_alias' => 'pr_ref',
        'reminders_table' => 'pr_matter_reminders',
        'checklist_status_column' => 'pr_checklist_status',
        'session_prefix' => 'pr_sheet_',

        'matter_nick_names' => ['si', 'sn', 'sh', 'nn', 'swr', 'ph', 'rps'],
        'matter_title_patterns' => ['189', '190', '191', '491'],

        'ongoing_stages' => [
            'Initial consultation',
            'Initial Payment and Documents Received',
            'Cost Agreement, form 956 and First email Sent',
            'Cost Agreement, form 956 Received',
            'Pending documents and payment requested',
            'Documents Completed and Preparing for Lodgement',
            'Payment verified',
            'Ready for Lodgement/Draft Application sent for confirmation',
            'Draft Application confirmation received',
        ],
        'lodged_stages' => ['Application Lodged', 'Immi Request Received'],
        'checklist_early_stages' => ['Checklist'],
        'discontinue_stages' => ['Decision Received', 'File Closed', 'Ready to Close', 'Withdrawn', 'Refund', 'Discontinued'],
        'checklist_convert_to_client_stage' => 'Initial Payment and Documents Received',
    ],

    'employer-sponsored' => [
        'title' => 'Employer Sponsored Visa Sheet',
        'route' => 'clients.sheets.visa-type',
        'reference_table' => 'client_employer_sponsored_references',
        'reference_model' => \App\Models\ClientEmployerSponsoredReference::class,
        'reference_alias' => 'emp_ref',
        'reminders_table' => 'employer_sponsored_matter_reminders',
        'checklist_status_column' => 'employer_sponsored_checklist_status',
        'session_prefix' => 'employer_sponsored_sheet_',

        'matter_nick_names' => ['en', 'es', 'ee', 'st', 'sd', 'ls', 'ssr', 'ses', '407'],
        'matter_title_patterns' => ['482', '494', '186', '407'],

        'ongoing_stages' => [
            'Initial consultation',
            'Initial Payment and Documents Received',
            'Cost Agreement, form 956 and First email Sent',
            'Cost Agreement, form 956 Received',
            'Pending documents and payment requested',
            'Documents Completed and Preparing for Lodgement',
            'Payment verified',
            'Ready for Lodgement/Draft Application sent for confirmation',
            'Draft Application confirmation received',
        ],
        'lodged_stages' => ['Application Lodged', 'Immi Request Received'],
        'checklist_early_stages' => ['Checklist'],
        'discontinue_stages' => ['Decision Received', 'File Closed', 'Ready to Close', 'Withdrawn', 'Refund', 'Discontinued'],
        'checklist_convert_to_client_stage' => 'Initial Payment and Documents Received',
    ],
];
