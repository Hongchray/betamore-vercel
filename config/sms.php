<?php

return [
    'api_url' => env('PLASGATE_API_URL'),
    'private_key' => env('PLASGATE_API_PRIVATE_KEY'),
    'x_secret' => env('PLASGATE_API_X_SECRET'),
    'sender' => env('PLASGATE_SENDER', 'PlasGateUAT'),
];