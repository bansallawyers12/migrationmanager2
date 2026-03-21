<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Roles allowed to delete CRM email logs (inbox/sent thread rows)
    |--------------------------------------------------------------------------
    |
    | Comma-separated user_roles.id values. Default matches legacy behaviour
    | (Super Admin, Admin, Migration Agent). Set CRM_EMAIL_LOG_DELETE_ROLE_IDS
    | in .env to add roles without code changes, e.g. "1,12,16,20".
    |
    */
    'email_log_delete_role_ids' => array_values(array_filter(array_map(
        'intval',
        explode(',', (string) env('CRM_EMAIL_LOG_DELETE_ROLE_IDS', '1,12,16'))
    ), static fn (int $id) => $id > 0)),

    /*
    |--------------------------------------------------------------------------
    | Person Assisting role IDs (user_roles.id)
    |--------------------------------------------------------------------------
    |
    | Staff with these roles only see clients/leads where they are set as
    | Person Assisting on a matter (client_matters.sel_person_assisting) or
    | assigned on the lead record (admins.user_id). Super admin (role 1) is
    | never restricted. Override via CRM_PERSON_ASSISTING_ROLE_IDS e.g. "13,21".
    |
    */
    'person_assisting_role_ids' => array_values(array_filter(array_map(
        'intval',
        explode(',', (string) env('CRM_PERSON_ASSISTING_ROLE_IDS', '13'))
    ), static fn (int $id) => $id > 0)),

    /*
    |--------------------------------------------------------------------------
    | Lead list / lead record full access (staff.role → user_roles.id)
    |--------------------------------------------------------------------------
    |
    | These roles see every lead (list + detail). Everyone else only sees leads
    | where admins.user_id = logged-in staff.id (and type=lead).
    |
    | Default mapping: 1 = Super Admin, 17 = Admin, 12 = PR.
    | Override via CRM_LEAD_FULL_ACCESS_ROLE_IDS e.g. "1,17,12".
    |
    */
    'lead_full_access_role_ids' => array_values(array_filter(array_map(
        'intval',
        explode(',', (string) env('CRM_LEAD_FULL_ACCESS_ROLE_IDS', '1,17,12'))
    ), static fn (int $id) => $id > 0)),

    /*
    |--------------------------------------------------------------------------
    | Lead list: which user_roles.module_access keys unlock the page
    |--------------------------------------------------------------------------
    |
    | Previously only key "20" (view all clients) was checked, so staff with
    | only 21–23 (add/edit/view assigned clients) saw an empty list. Default
    | includes 20–23. Set CRM_LEAD_LIST_MODULE_ACCESS_KEYS e.g. "20,21,22,23".
    |
    | CRM_LEAD_LIST_EXTRA_ROLE_IDS: optional staff role ids that may open the
    | lead list even without those keys (row visibility still uses
    | restrictLeadListQuery).
    |
    */
    'lead_list_module_access_keys' => array_values(array_filter(
        array_map(
            static fn (string $k) => trim($k),
            array_map('strval', explode(',', (string) env('CRM_LEAD_LIST_MODULE_ACCESS_KEYS', '20,21,22,23')))
        ),
        static fn (string $k) => $k !== ''
    )),

    'lead_list_extra_role_ids' => array_values(array_filter(array_map(
        'intval',
        explode(',', (string) env('CRM_LEAD_LIST_EXTRA_ROLE_IDS', ''))
    ), static fn (int $id) => $id > 0)),

    /*
    |--------------------------------------------------------------------------
    | Lead list: “assigned leads only” roles (staff.role → user_roles.id)
    |--------------------------------------------------------------------------
    |
    | These roles may open /leads without client module keys 20–23. They only
    | see rows where admins.user_id = their staff id (via restrictLeadListQuery).
    | Default: PA (13), Calling (14), Accountant (15), Migration Agent (16).
    | Set CRM_LEAD_LIST_ASSIGNED_ONLY_ROLE_IDS to override, e.g. "13,14,15,16".
    |
    */
    'lead_list_assigned_only_role_ids' => (($__leadAssignedRoles = array_values(array_filter(array_map(
        'intval',
        explode(',', (string) env('CRM_LEAD_LIST_ASSIGNED_ONLY_ROLE_IDS', '13,14,15,16'))
    ), static fn (int $id) => $id > 0))) !== []
        ? $__leadAssignedRoles
        : [13, 14, 15, 16]),

];
