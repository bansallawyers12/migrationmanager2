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

];
