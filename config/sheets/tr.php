<?php

return [
    /*
    |--------------------------------------------------------------------------
    | TR Sheet — Stage names per tab (workflow_stages.name)
    |--------------------------------------------------------------------------
    | Define which workflow stage names belong to each tab.
    | Case-insensitive matching. Add/remove stages to match your TR workflow.
    */
    'ongoing_stages' => [
        'Document received',
        'Visa applied',
        'Visa received',
        'Enrollment',
    ],

    'lodged_stages' => [
        'Lodged',
        'Submitted',
    ],

    'checklist_early_stages' => [
        'Awaiting documents',
        'Checklist',
    ],

    'discontinue_stages' => [
        'Withdrawn',
        'Refund',
        'Discontinued',
    ],

    /*
    | Stage to set when user selects "Convert to client" on Checklist tab.
    */
    'checklist_convert_to_client_stage' => 'Document received',

    /*
    | TR matter identification
    */
    'matter_nick_names' => ['tr', 'tr checklist'],
    'matter_title_patterns' => ['tr', 'tr checklist', 'temporary residence'],
];
