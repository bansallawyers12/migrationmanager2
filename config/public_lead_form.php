<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Public lead inquiry form (no login)
    |--------------------------------------------------------------------------
    */

    'admin_notification_email' => env('PUBLIC_LEAD_FORM_ADMIN_EMAIL'),

    /**
     * Staff user id for user_id on new leads and for client_contacts / client_emails admin_id.
     * If null, the first active staff row (lowest id) is used.
     */
    'default_assignee_staff_id' => env('PUBLIC_LEAD_FORM_DEFAULT_STAFF_ID'),

    /**
     * Form route path (used for rate limiting key and links if needed)
     */
    'path' => env('PUBLIC_LEAD_FORM_PATH', 'lead-client-info-form'),
];
