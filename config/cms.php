<?php

return [
    'locales' => [
        'al' => 'Shqip',
        'en' => 'English',
    ],

    'default_locale' => env('APP_LOCALE', 'al'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'al'),

    'initial_admin' => [
        'name' => env('INITIAL_ADMIN_NAME'),
        'email' => env('INITIAL_ADMIN_EMAIL'),
        'password' => env('INITIAL_ADMIN_PASSWORD'),
    ],

    'seed_demo_content' => filter_var(env('SEED_DEMO_CONTENT', false), FILTER_VALIDATE_BOOL),
];
