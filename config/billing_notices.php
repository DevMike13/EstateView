<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable Automatic Billing Notices
    |--------------------------------------------------------------------------
    */

    'enabled' => env(
        'BILLING_NOTICES_ENABLED',
        true
    ),

    /*
    |--------------------------------------------------------------------------
    | Company Details
    |--------------------------------------------------------------------------
    */

    'company_name' => env(
        'BILLING_NOTICE_COMPANY',
        'DGA Realty Corporation'
    ),

    'project_name' => env(
        'BILLING_NOTICE_PROJECT',
        'Manhattan Residences Candelaria'
    ),

    'address' => env(
        'BILLING_NOTICE_ADDRESS',
        'Bansalagin St. Brgy. Pahinga Norte, Candelaria, Quezon'
    ),

    'phone' => env(
        'BILLING_NOTICE_PHONE',
        '(042) 322 5355'
    ),

    'email' => env(
        'BILLING_NOTICE_EMAIL',
        'dgarealtycorp@gmail.com'
    ),

    'facebook' => env(
        'BILLING_NOTICE_FACEBOOK',
        'Manhattan Residences - Candelaria'
    ),

    /*
    |--------------------------------------------------------------------------
    | Monthly Payment Reminder
    |--------------------------------------------------------------------------
    |
    | Example:
    | Due August 15
    | Reminder sent August 10
    |
    */

    'monthly_days_before' => (int) env(
        'BILLING_NOTICE_DAYS_BEFORE',
        5
    ),

    /*
    |--------------------------------------------------------------------------
    | Cancellation / Forfeiture Rules
    |--------------------------------------------------------------------------
    */

    'cancellation_month' => (int) env(
        'BILLING_NOTICE_CANCELLATION_MONTH',
        5
    ),

    'forfeiture_month' => (int) env(
        'BILLING_NOTICE_FORFEITURE_MONTH',
        6
    ),

    /*
    |--------------------------------------------------------------------------
    | Response Period
    |--------------------------------------------------------------------------
    */

    'response_days' => (int) env(
        'BILLING_NOTICE_RESPONSE_DAYS',
        7
    ),

    /*
    |--------------------------------------------------------------------------
    | PDF Storage
    |--------------------------------------------------------------------------
    */

    'disk' => env(
        'BILLING_NOTICE_DISK',
        'local'
    ),

];