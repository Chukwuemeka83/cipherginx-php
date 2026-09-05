<?php
/**
 * CipherGinx PHP - Version Information
 */

define('CIPHERGINX_VERSION', '2.0');
define('CIPHERGINX_NAME', 'CipherGinx PHP');
define('CIPHERGINX_AUTHOR', 'Chukwuemeka83');
define('CIPHERGINX_REPO', 'https://github.com/Chukwuemeka83/cipherginx-php');
define('CIPHERGINX_LICENSE', 'MIT');
define('CIPHERGINX_RELEASE_DATE', '2026-09-05');

/**
 * Get version string
 */
function getCipherGinxVersion(): string
{
    return sprintf(
        '%s v%s',
        CIPHERGINX_NAME,
        CIPHERGINX_VERSION
    );
}

/**
 * Get build information
 */
function getCipherGinxInfo(): array
{
    return [
        'name' => CIPHERGINX_NAME,
        'version' => CIPHERGINX_VERSION,
        'author' => CIPHERGINX_AUTHOR,
        'repository' => CIPHERGINX_REPO,
        'license' => CIPHERGINX_LICENSE,
        'released' => CIPHERGINX_RELEASE_DATE,
        'php_version' => PHP_VERSION,
        'php_sapi' => php_sapi_name(),
    ];
}
