<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Reader Session
    |--------------------------------------------------------------------------
    */

    'session' => [

        /*
         * Rolling inactivity window.
         */
        'idle_minutes' => 15,

        /*
         * Absolute reader session lifetime.
         */
        'max_minutes' => 240,

    ],


    /*
    |--------------------------------------------------------------------------
    | Device Limits
    |--------------------------------------------------------------------------
    */

    'devices' => [

        'student' => 2,

        'teacher' => 3,

        'school_admin' => 3,

        'individual_subscriber' => 2,

    ],


    /*
    |--------------------------------------------------------------------------
    | Concurrent Reader Sessions
    |--------------------------------------------------------------------------
    */

    'concurrent_sessions' => [

        'student' => 1,

        'teacher' => 2,

        'school_admin' => 2,

        'individual_subscriber' => 1,

    ],


    /*
    |--------------------------------------------------------------------------
    | Page Loading
    |--------------------------------------------------------------------------
    */

    'page_window' => 1,

    'page_rate_limit_per_minute' => 60,


    /*
    |--------------------------------------------------------------------------
    | Page Rendering
    |--------------------------------------------------------------------------
    */

    'render' => [

        'format' => 'webp',

        'quality' => 82,

        'dpi' => 150,

    ],


    /*
    |--------------------------------------------------------------------------
    | Watermark
    |--------------------------------------------------------------------------
    */

    'watermark' => [

        'enabled' => true,

        /*
         * Percent-like conceptual opacity.
         * Actual conversion depends on renderer implementation.
         */
        'opacity' => 12,

        'include_name' => true,

        'include_email' => true,

        'include_school' => true,

        'include_timestamp' => true,

        'include_forensic_id' => true,

    ],

];
