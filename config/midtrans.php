<?php

return [
    'merchant_id' => env('MIDTRANS_MERCHANT_ID', 'G668914050'),
    'client_key'  => env('MIDTRANS_CLIENT_KEY'),
    'server_key'  => env('MIDTRANS_SERVER_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'sanitize'      => true,
    '3ds'           => true, // `false` untuk sandbox
];

