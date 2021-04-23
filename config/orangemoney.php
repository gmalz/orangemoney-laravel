<?php

return [
    'url' => env('OM_BASE_URL', null),
    'endpoints' => [
        'token' => 'oauth/v2/token',
        'web_payment' => 'orange-money-webpay/dev/v1/webpayment',
        'transaction_status' => 'orange-money-webpay/dev/v1/transactionstatus',
    ],
    'header' => [
        'content_type' => 'application/json',
    ],

    'auth_header' => env('OM_AUTH_HEADER', null),
    'merchant_key' => env('OM_MERCHANT_KEY', null),
    'return_url' => env('OM_RETURN_URL', null),
    'cancel_url' => env('OM_CANCEL_URL', null),
    'notif_url' => env('OM_NOTIF_URL', null),
];
