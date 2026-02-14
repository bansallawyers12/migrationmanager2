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
        'reference_alias' => 'tr_ref',
        'reminders_table' => 'tr_matter_reminders',
        'checklist_status_column' => 'tr_checklist_status',
        'session_prefix' => 'tr_sheet_',

        'matter_nick_names' => ['tr', 'tr checklist'],
        'matter_title_patterns' => ['tr', 'tr checklist', 'temporary residence'],

        'ongoing_stages' => ['Document received', 'Visa applied', 'Visa received', 'Enrollment'],
        'lodged_stages' => ['Lodged', 'Submitted'],
        'checklist_early_stages' => ['Awaiting documents', 'Checklist'],
        'discontinue_stages' => ['Withdrawn', 'Refund', 'Discontinued'],
        'checklist_convert_to_client_stage' => 'Document received',
    ],

    'visitor' => [
        'title' => 'Visitor Visa Sheet',
        'route' => 'clients.sheets.visa-type',
        'reference_table' => 'client_visitor_references',
        'reference_alias' => 'visitor_ref',
        'reminders_table' => 'visitor_matter_reminders',
        'checklist_status_column' => 'visitor_checklist_status',
        'session_prefix' => 'visitor_sheet_',

        'matter_nick_names' => ['visitor', 'tourist', 'bv'],
        'matter_title_patterns' => ['visitor', 'tourist', 'business visitor'],

        'ongoing_stages' => ['Document received', 'Application lodged'],
        'lodged_stages' => ['Lodged', 'Submitted', 'Under review'],
        'checklist_early_stages' => ['Awaiting documents', 'Checklist'],
        'discontinue_stages' => ['Withdrawn', 'Refund', 'Refused'],
        'checklist_convert_to_client_stage' => 'Document received',
    ],

    'student' => [
        'title' => 'Student Visa Sheet',
        'route' => 'clients.sheets.visa-type',
        'reference_table' => 'client_student_references',
        'reference_alias' => 'student_ref',
        'reminders_table' => 'student_matter_reminders',
        'checklist_status_column' => 'student_checklist_status',
        'session_prefix' => 'student_sheet_',

        'matter_nick_names' => ['student', 'sv'],
        'matter_title_patterns' => ['student', 'student visa', 'education'],

        'ongoing_stages' => ['Document received', 'COE requested', 'Application lodged'],
        'lodged_stages' => ['Lodged', 'Submitted', 'Under review'],
        'checklist_early_stages' => ['Awaiting documents', 'Checklist'],
        'discontinue_stages' => ['Withdrawn', 'Refund', 'Refused'],
        'checklist_convert_to_client_stage' => 'Document received',
    ],

    'pr' => [
        'title' => 'PR Application Sheet',
        'route' => 'clients.sheets.visa-type',
        'reference_table' => 'client_pr_references',
        'reference_alias' => 'pr_ref',
        'reminders_table' => 'pr_matter_reminders',
        'checklist_status_column' => 'pr_checklist_status',
        'session_prefix' => 'pr_sheet_',

        'matter_nick_names' => ['pr', 'skilled'],
        'matter_title_patterns' => ['pr', 'permanent residence', 'skilled', '189', '190', '491'],

        'ongoing_stages' => ['Document received', 'EOI lodged', 'Invitation received'],
        'lodged_stages' => ['Lodged', 'Submitted', 'Under review'],
        'checklist_early_stages' => ['Awaiting documents', 'Checklist'],
        'discontinue_stages' => ['Withdrawn', 'Refund', 'Refused'],
        'checklist_convert_to_client_stage' => 'Document received',
    ],
];
