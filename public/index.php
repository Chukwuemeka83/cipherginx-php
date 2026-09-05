<?php
/**
 * CipherGinx PHP - Bootstrap Application
 * Initialize and start the proxy server
 */

require_once __DIR__ . '/vendor/autoload.php';

use CipherGinx\ProxyServer;
use CipherGinx\ConfigLoader;
use CipherGinx\Database;

/**
 * Main application entry point
 */
class Application
{
    private array $config;
    private ProxyServer $proxy;

    public function __construct()
    {
        $this->loadConfiguration();
    }

    /**
     * Load configuration from environment and config files
     */
    private function loadConfiguration(): void
    {
        $loader = new ConfigLoader();
        $this->config = $loader->load('.env');
    }

    /**
     * Run the application
     */
    public function run(string $config_name, string $log_level = 'info'): void
    {
        try {
            // Load target-specific config
            $loader = new ConfigLoader();
            $config_path = __DIR__ . "/config/{$config_name}.php";
            
            if (!file_exists($config_path)) {
                throw new \Exception("Config file not found: {$config_path}");
            }

            $this->config = $loader->loadTargetConfig($config_path);
            $this->config['log_level'] = $log_level;

            // Ensure directories exist
            $this->ensureDirectories();

            // Initialize database
            $this->initializeDatabase();

            // Start proxy server
            $this->proxy = new ProxyServer($this->config);
            $this->proxy->start();

        } catch (\Exception $e) {
            echo "[ERROR] " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    /**
     * Ensure required directories exist
     */
    private function ensureDirectories(): void
    {
        $directories = ['logs', 'certs', 'database'];
        
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    /**
     * Initialize database connection and schema
     */
    private function initializeDatabase(): void
    {
        try {
            $db = new Database($this->config);
            $db->initializeSchema();
            echo "[+] Database initialized successfully\n";
        } catch (\Exception $e) {
            echo "[!] Database initialization warning: " . $e->getMessage() . "\n";
            echo "[!] Continuing without database persistence...\n";
        }
    }
}
