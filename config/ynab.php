<?php

return [
    'client' => [
        'id' => env('YNAB_CLIENT_ID'),
        'secret' => env('YNAB_CLIENT_SECRET'),
    ],
    'redirect_uri' => env('YNAB_REDIRECT_URI', 'http://localhost'),
];
