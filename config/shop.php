<?php

/**
 * Settings a shop
 */

return [
    'private_keys'  => [
        'K2'    => env('K2'),
        'K5'    => env('K5')
    ],
    /**
     * Bot a token api
     */
    'TGBOT_TOKEN'   => env('TGBOT_TOKEN'),
    'TGBOT_NAME'    => 'sample',
    /**
     * Usernames a admins in telegram
     * name,name,name,name
     */
    'TGBOT_ADMINS'  => env('TGBOT_ADMINS'),
    /**
     * Api to url for bot
     */
    'API_URL'   => env('API_URL'),
    'API_KEY'   => env('API_KEY'),

    'test_telegram_clients'     => env('TEST_TELEGRAM_CLIENTS'),
    'test_execute_async_jobs'   => env('TEST_EXECUTE_ASYNC_JOBS', false),

    /**
     * Config orders
     */
    'order_count_user'          => 15
];