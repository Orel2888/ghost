<?php

return [

    /**
     * Fill started data, table the admin accounts a rows
     */
    'admin_accounts' => [
        [
            'login'     => 'user',
            'password'  => bcrypt(111),
            'created_at'    => Carbon\Carbon::now()->format('Y-m-d H:i:s')
        ],
        [
            'login'     => 'user2',
            'password'  => bcrypt(111),
            'created_at'    => Carbon\Carbon::now()->format('Y-m-d H:i:s')
        ]
        // ...
    ]
];