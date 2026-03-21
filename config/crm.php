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

];
