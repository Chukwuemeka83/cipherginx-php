<?php

namespace CipherGinx;

use PDO;
use PDOException;

/**
 * Database Manager
 * Handles database initialization and connection
 */
class Database
{
    private ?PDO $pdo = null;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Connect to database
     */
    public function connect(): PDO
    {
        if ($this->pdo !== null) {
            return $this->pdo;
        }

        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;charset=utf8mb4',
                $this->config['db_host'],
                $this->config['db_port']
            );

            $this->pdo = new PDO(
                $dsn,
                $this->config['db_user'],
                $this->config['db_password']
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->pdo;

        } catch (PDOException $e) {
            throw new \Exception('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Initialize database schema
     */
    public function initializeSchema(): void
    {
        $pdo = $this->connect();

        try {
            // Create database if it doesn't exist
            $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . $this->config['db_name'] . '`');
            $pdo->exec('USE `' . $this->config['db_name'] . '`');

            // Create tables from SQL file
            $schema_file = __DIR__ . '/../database/schema.sql';
            if (file_exists($schema_file)) {
                $sql = file_get_contents($schema_file);
                $pdo->exec($sql);
            }

        } catch (PDOException $e) {
            throw new \Exception('Failed to initialize database schema: ' . $e->getMessage());
        }
    }

    /**
     * Get PDO instance
     */
    public function getPdo(): PDO
    {
        return $this->pdo ?? $this->connect();
    }
}
