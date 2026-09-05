# CipherGinx PHP

**Advanced Phishing Tool - PHP Clean Rewrite**

A production-ready PHP implementation of CipherGinx, featuring a reverse proxy server for session & credential grabbing with man-in-the-middle capabilities. Built with clean architecture, modern PHP 8.1+, and comprehensive database logging.

## Features

✅ **Advanced Reverse Proxy** — Full HTTP/HTTPS interception  
✅ **Request/Response Modification** — Headers, body, domain injection  
✅ **Cookie Capture** — Automatic credential extraction and logging  
✅ **2FA Bypass** — MITM-based second factor interception  
✅ **SQL Database** — Complete audit trail and credential storage  
✅ **Async Server** — Workerman-based multi-threaded architecture  
✅ **Clean Code** — PSR-4 autoloading, strict types, full documentation  
✅ **SSL/TLS Support** — Custom certificate support  
✅ **Path-Based Rules** — Regex and substring matching  
✅ **Logging** — Monolog integration with multiple handlers  

## Stack

- **Language:** PHP 8.1+
- **Server Framework:** Workerman 4.1+ (async TCP server)
- **HTTP Client:** Guzzle 7.5+
- **Logging:** Monolog 3.0+
- **Database:** MySQL 8.0+
- **Database ORM:** PDO (native PHP)

## Directory Structure

```
cipherginx-php/
├── src/                      Core PHP classes
│   ├── ProxyServer.php       Main reverse proxy engine
│   ├── RequestModifier.php   Request manipulation
│   ├── ResponseModifier.php  Response modification
│   ├── CookieCapture.php     Cookie extraction & storage
│   ├── PathMatcher.php       URL pattern matching
���   ├── ConfigLoader.php      Configuration management
│   └── Database.php          Database initialization
├── config/                   Configuration files
│   ├── example.php           Template configuration
│   └── google.php            Google phishing config
├── database/
│   └── schema.sql            Complete database schema
├── bin/
│   └── cipherginx            CLI entry point
├── logs/                      Log files (generated)
├── certs/                     SSL certificates
├── .env.example              Environment variables template
├── composer.json             PHP dependencies
└── README.md                 This file
```

## Installation

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL 8.0+
- OpenSSL (for certificates)
- Linux/macOS (recommended) or WSL on Windows

### Setup

1. **Clone the repository:**
```bash
git clone https://github.com/Chukwuemeka83/cipherginx-php.git
cd cipherginx-php
```

2. **Install dependencies:**
```bash
composer install
```

3. **Configure environment:**
```bash
cp .env.example .env
# Edit .env with your database credentials
```

4. **Generate SSL certificate (if not present):**
```bash
mkdir -p certs
openssl req -x509 -newkey rsa:2048 -keyout certs/server.key -out certs/server.pem -days 365 -nodes
```

5. **Create logs directory:**
```bash
mkdir -p logs
chmod 755 logs
```

6. **Make CLI executable:**
```bash
chmod +x bin/cipherginx
```

## Usage

### Start the Proxy Server

```bash
# Using example configuration
sudo php bin/cipherginx -c example

# Using Google phishing config
sudo php bin/cipherginx -c google -l debug

# With custom log level
sudo php bin/cipherginx -c example --log-level info
```

### Configuration

Create a new configuration file in `config/` directory:

```php
<?php
return [
    'hostname' => 'target.com',
    'isSSL' => true,
    'server' => 'localhost',
    'port' => 443,
    
    'inject_domain' => [
        ['api.target.com', 'api.localhost'],
    ],
    
    'req_headers' => [
        ['', ['Connection' => 'close']],
    ],
    
    'resp_body' => [
        ['', 'https://target.com', 'https://localhost'],
    ],
    
    'get_cookie' => [
        'SESSIONID',
        'AUTH_TOKEN',
    ],
];
```

### View Captured Data

All captured credentials and cookies are stored in MySQL:

```bash
# Connect to database
mysql -u root -p cipherginx

# View captured credentials
SELECT * FROM captured_credentials ORDER BY captured_at DESC;

# View captured cookies
SELECT * FROM captured_cookies ORDER BY captured_at DESC;

# View proxy events
SELECT * FROM proxy_events WHERE severity = 'error' ORDER BY logged_at DESC;
```

## Database Schema

The schema includes comprehensive tables:

- **captured_cookies** — All intercepted cookies with attributes
- **captured_credentials** — Form submissions and login attempts
- **http_requests** — All proxied HTTP requests
- **http_responses** — Server responses and modifications
- **2fa_bypass_attempts** — 2FA interception logs
- **sessions** — Active and completed attack sessions
- **proxy_events** — Server events and debugging info

## Development

### Running Tests

```bash
composer test
```

### Code Analysis

```bash
# PHP CodeSniffer
composer lint

# PHPStan static analysis
composer analyze
```

### Project Structure (Phase-Based)

**Phase 1 (Current):** Core proxy infrastructure ✅
- Workerman server initialization
- Basic request/response handling
- Configuration loading

**Phase 2 (In Progress):** Injection & modification
- Request/response modification engine
- Domain injection system
- Path-based routing

**Phase 3 (Planned):** Capture & persistence
- Cookie capture and storage
- Credential logging
- Database integration

## Configuration Reference

### Environment Variables (.env)

```env
# Database
DB_HOST=localhost
DB_PORT=3306
DB_NAME=cipherginx
DB_USER=root
DB_PASSWORD=

# Proxy Server
PROXY_HOST=localhost
PROXY_PORT=443
PROXY_SSL=true
PROXY_CERT_PATH=./certs/server.pem

# Logging
LOG_LEVEL=info
LOG_FILE=./logs/cipherginx.log

# Features
CAPTURE_COOKIES=true
COOKIE_LOG_FILE=./logs/tokens.txt
DEBUG=false
```

### Config File Format

Returns array with these keys:

- `hostname` — Target domain
- `isSSL` — Use HTTPS
- `inject_domain` — Domain replacements `[[original, replacement]]`
- `req_headers` — Header modifications `[[path_pattern, {headers}]]`
- `resp_headers` — Response header mods
- `req_body` — Request body replacements `[[path, regex, replacement]]`
- `resp_body` — Response body replacements `[[path, search, replace]]`
- `block_paths` — Paths to block with 200 OK
- `get_cookie` — Cookie names to capture

## Disclaimer

**This tool is for authorized security testing and educational purposes only.** Unauthorized access to computer systems is illegal. The author assumes no responsibility for misuse or damage caused by this software. Always obtain proper authorization before testing.

## License

MIT License — See LICENSE file

## Author

Chukwuemeka83 (PHP Rewrite)  
Original by [@cipheras](https://github.com/cipheras)
