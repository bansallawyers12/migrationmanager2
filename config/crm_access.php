<?php

return [
    'exempt_role_ids' => array_values(array_filter(array_map(
        'intval',
        explode(',', env('CRM_ACCESS_EXEMPT_ROLE_IDS', '1,17'))
    ))),

    'approver_staff_ids' => array_values(array_filter(array_map(
        'intval',
        explode(',', env('CRM_ACCESS_APPROVER_STAFF_IDS', '36834,36524,36692,36483,36484,36718,36523,36836,36830'))
    ))),

    'quick_reason_options' => [
        'calling' => 'Calling / Reception',
        'cover' => 'Covering Absent Colleague',
        'urgent' => 'Urgent Client Follow-up',
        'admin_task' => 'Administrative Task',
    ],

    'quick_access_only_role_ids' => array_values(array_filter(array_map(
        'intval',
        explode(',', env('CRM_ACCESS_QUICK_ONLY_ROLE_IDS', '14'))
    ))),

    'quick_grant_minutes' => (int) env('CRM_ACCESS_QUICK_GRANT_MINUTES', 15),

    'supervisor_grant_hours' => (int) env('CRM_ACCESS_SUPERVISOR_GRANT_HOURS', 24),

    'strict_allocation' => filter_var(env('CRM_ACCESS_STRICT_ALLOCATION', false), FILTER_VALIDATE_BOOLEAN),

    'max_pending_supervisor_requests' => max(1, (int) env('CRM_ACCESS_MAX_PENDING_SUPERVISOR_REQUESTS', 5)),
];
