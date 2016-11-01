<?php

return [
    'transport' => env('UNOCONV_TRANSPORT', 'Guzzle'),
    'scheme' => env('UNOCONV_SCHEME', 'http'),
    'host' => env('UNOCONV_HOST', 'localhost'),
    'port' => env('UNOCONV_PORT', 80),
    'path' => env('UNOCONV_PATH', 'unoconv'),
];
