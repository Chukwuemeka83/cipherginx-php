# Architecture - CipherGinx PHP

## System Design

### High-Level Flow

```
Client Request
    ↓
  [ProxyServer] (Workerman TCP Server)
    ├─→ Listen on PROXY_HOST:PROXY_PORT (SSL/TLS)
    ├─→ Accept incoming HTTP/HTTPS
    └─→ handleMessage()
        ├─→ parseHttpRequest()
        ├─→ [RequestModifier] - Modify request
        │   ├─ injectDomainReplacements()
        │   ├─ modifyHeaders()
        │   └─ modifyBody()
        ├─→ [Forward to Target] (via Guzzle)
        ├─→ [ResponseModifier] - Modify response
        │   ├─ injectDomainReplacements()
        │   ├─ modifyHeaders()
        │   └─ modifyBody()
        ├─→ [CookieCapture] - Extract cookies
        │   ├─ extractSetCookieHeaders()
        │   ├─ parseCookieString()
        │   ├─ Database.insert()
        │   └─ File.append()
        └─→ Send response back to client
```

## Core Components

### 1. ProxyServer (Orchestrator)

**File:** `src/ProxyServer.php`

**Responsibility:** Main reverse proxy engine

**Key Methods:**
- `__construct()` — Initialize logger, modifiers, and Workerman
- `initializeWorker()` — Setup Workerman TCP server with SSL
- `handleConnect()` — New connection event
- `handleMessage()` — Process incoming HTTP request
- `handleClose()` — Clean up on disconnect
- `handleError()` — Error handling
- `parseHttpRequest()` — Parse raw HTTP into array
- `forwardRequest()` — Send to target server (TODO: Guzzle)

**Dependencies:**
- Workerman\Worker
- Monolog\Logger
- RequestModifier
- ResponseModifier
- CookieCapture

---

### 2. RequestModifier (Outbound)

**File:** `src/RequestModifier.php`

**Responsibility:** Modify requests before sending to target

**Key Methods:**
- `modify()` — Main modification orchestrator
- `injectDomainReplacements()` — Replace domains in headers/body
- `modifyHeaders()` — Apply header rules based on path
- `modifyBody()` — Apply body replacements
- `pathMatches()` — Check if path matches pattern (substring/regex)

**Configuration Used:**
- `inject_domain` — Domain replacements
- `req_headers` — Header modifications
- `req_body` — Body text replacements

---

### 3. ResponseModifier (Inbound)

**File:** `src/ResponseModifier.php`

**Responsibility:** Modify responses before sending to client

**Key Methods:**
- `modify()` — Main modification orchestrator
- `parseResponse()` — Split HTTP into headers + body
- `reconstructResponse()` — Rebuild HTTP response
- `injectDomainReplacements()` — Replace domains back
- `modifyHeaders()` — Apply response header rules
- `modifyBody()` — Apply response body replacements

**Configuration Used:**
- `inject_domain` — Domain back-replacements
- `resp_headers` — Response header modifications
- `resp_body` — Response body replacements

---

### 4. CookieCapture (Persistence)

**File:** `src/CookieCapture.php`

**Responsibility:** Extract and store captured cookies

**Key Methods:**
- `__construct()` — Initialize database connection
- `parse()` — Main entry point for Set-Cookie headers
- `extractSetCookieHeaders()` — Find Set-Cookie lines
- `parseCookieString()` — Parse individual cookie
- `storeCookie()` — Insert into database
- `logToFile()` — Append to tokens.txt

**Database Tables:**
- `captured_cookies` — Structured cookie storage
- `captured_credentials` — Form submissions
- `proxy_events` — Server events

---

### 5. PathMatcher (Routing)

**File:** `src/PathMatcher.php`

**Responsibility:** Match URL paths against config patterns

**Key Methods:**
- `matches()` — Check if path matches pattern
  - Empty pattern → all paths
  - `/regex/` → regex matching
  - Substring → literal substring match
- `stripQueryString()` — Remove ?query from path

**Usage:**
```php
if (PathMatcher::matches('/api/login', '/api')) {
    // Modify this request
}
```

---

### 6. ConfigLoader (Configuration)

**File:** `src/ConfigLoader.php`

**Responsibility:** Load and manage configuration

**Key Methods:**
- `load()` — Load from .env file
- `loadTargetConfig()` — Merge target-specific config
- `get()` — Get config value by key
- `set()` — Set config value

**Configuration Sources:**
1. `.env` (base configuration)
2. `config/example.php` (target-specific)
3. CLI overrides

---

### 7. Database (Schema Management)

**File:** `src/Database.php`

**Responsibility:** Database connection and schema initialization

**Key Methods:**
- `connect()` — PDO connection
- `initializeSchema()` — Create tables from `database/schema.sql`
- `getPdo()` — Get PDO instance

**Connection:** MySQL 8.0+ via PDO

---

## Request/Response Lifecycle

### 1. Client → Proxy

```
Client sends HTTP request to proxy:443
↓
Workerman::handleMessage() received
↓
ProxyServer::parseHttpRequest() → array
```

**HTTP Request Array:**
```php
[
    'method' => 'POST',
    'path' => '/api/login',
    'version' => 'HTTP/1.1',
    'headers' => ['Host' => 'localhost', ...],
    'body' => 'username=...&password=...',
    'raw' => 'POST /api/login HTTP/1.1\r\n...'
]
```

### 2. Proxy → Target (Modified)

```
ProxyServer::modify_request()
↓
RequestModifier::modify()
├─ injectDomainReplacements()     // Replace localhost → example.com
├─ modifyHeaders()                 // Add/modify headers based on path
└─ modifyBody()                    // Replace body content
↓
Forward to target server via Guzzle
```

### 3. Target → Proxy (Raw Response)

```
Guzzle receives response from target
↓
ProxyServer::modify_response()
↓
ResponseModifier::modify()
├─ parseResponse()                 // Split headers + body
├─ injectDomainReplacements()     // Replace example.com → localhost
├─ modifyHeaders()                 // Modify response headers
└─ modifyBody()                    // Replace response content
↓
Extract Set-Cookie headers
```

### 4. Cookie Capture

```
ProxyServer::handleMessage()
↓
if config['capture_cookies']:
  CookieCapture::parse(response)
    ├─ extractSetCookieHeaders()    // Find Set-Cookie: lines
    ├─ parseCookieString()          // Extract attributes
    ├─ storeCookie()                // INSERT into database
    └─ logToFile()                  // Append to tokens.txt
```

### 5. Proxy → Client (Modified)

```
ProxyServer::send(modified_response)
↓
Client receives phishing response
```

---

## Data Flow Diagram

```
┌───────────────────────────────────────���─────────────────────┐
│                      CLIENT BROWSER                         │
└────────────────┬────────────────────────────────────────────┘
                 │ HTTP Request (with browser cookies)
                 ↓
        ┌────────────────────┐
        │   ProxyServer      │
        │  (Workerman TCP)   │
        └────────┬───────────┘
                 │
        ┌────────▼───────────────────────┐
        │  RequestModifier               │
        │  ├─ Domain replacement         │
        │  ├─ Header injection           │
        │  └─ Body modification          │
        └────────┬───────────────────────┘
                 │
        ┌────────▼─────────────────────┐
        │  Guzzle HTTP Client          │
        │  (Forward to target server)  │
        └────────��─────────────────────┘
                 │
        ┌────────▼──────────────────────┐
        │   TARGET SERVER                │
        │  (e.g., accounts.google.com)   │
        └────────┬──────────────────────┘
                 │ HTTP Response (with Set-Cookie)
        ┌────────▼────────────────────────┐
        │  ResponseModifier              │
        │  ├─ Domain replacement         │
        │  ├─ Header modification        │
        │  └─ Body replacement           │
        └────────┬────────────────────────┘
                 │
        ┌────────▼──────────────────────┐
        │  CookieCapture                │
        │  ├─ Parse Set-Cookie headers  │
        │  ├─ Extract attributes        │
        │  ├─ Database INSERT           │
        │  └─ File logging              │
        └────────┬──────────────────────┘
                 │ Modified Response
                 ↓
┌─────────────────────────────────────────────────────────────┐
│                   DATABASE (MySQL)                          │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ captured_cookies                                     │  │
│  │ captured_credentials                                 │  │
│  │ http_requests / http_responses                       │  │
│  │ 2fa_bypass_attempts                                  │  │
│  │ sessions / proxy_events                              │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

---

## Configuration Cascade

```
1. .env (Environment defaults)
   ├─ DB_HOST, DB_PORT, DB_NAME
   ├─ PROXY_HOST, PROXY_PORT, PROXY_SSL
   └─ LOG_LEVEL, CAPTURE_COOKIES

2. config/example.php (Target-specific)
   ├─ hostname, isSSL, server, port
   ├─ inject_domain, req_headers, resp_headers
   ├─ req_body, resp_body, block_paths
   └─ get_cookie

3. CLI Arguments (Overrides)
   ├─ -c config_name
   ├─ -l log_level
   └─ --debug
```

---

## Async Architecture (Workerman)

Workerman handles concurrent connections efficiently:

```
┌─ Worker Process 1 ─┐
│  Client Connection │ → Request Handling
│  Client Connection │ → Request Handling
└────────────────────┘

┌─ Worker Process 2 ─┐
│  Client Connection │ → Request Handling
│  Client Connection │ → Request Handling
└────────────────────┘

┌─ Worker Process 3 ─┐
│  Client Connection │ → Request Handling
└────────────────────┘

┌─ Worker Process 4 ─┐
│  Client Connection │ → Request Handling
└────────────────────┘
```

**Worker Count:** 4 (configurable in ProxyServer)

**Connection Handling:**
- Each process can handle multiple connections
- Non-blocking I/O for efficient resource usage
- Auto-restart on crash

---

## Error Handling

### Exception Flow

```
ProxyServer::handleMessage()
  ├─ try:
  │  ├─ parseHttpRequest()
  │  ├─ RequestModifier::modify()
  │  ├─ forwardRequest()
  │  ├─ ResponseModifier::modify()
  │  ├─ CookieCapture::parse()
  │  └─ send(response)
  │
  └─ catch Exception:
     ├─ Logger::error()
     └─ send(500 Internal Server Error)
```

### Logging Levels

- **DEBUG** — Detailed information for development
- **INFO** — General informational messages
- **WARNING** — Warning messages (e.g., DB connection issues)
- **ERROR** — Error messages (e.g., parsing failures)
- **CRITICAL** — Critical failures (e.g., server crash)

---

## Future Enhancements (Phase 2 & 3)

### Phase 2: Advanced Injection
- JavaScript injection
- Form field injection
- WebSocket support
- HTTP/2 support

### Phase 3: 2FA Bypass
- OTP interception
- SMS bypass techniques
- Email-based 2FA
- Authenticator app bypass

### Phase 4: Advanced Features
- Rate limiting
- Geo-spoofing
- Device fingerprint manipulation
- Credential validation

---

## Performance Considerations

### Memory Usage
- Each request parsed into array (~1-10KB)
- Response buffering (configurable limit)
- Cookie storage in DB (minimal memory)

### Throughput
- Theoretical: 100+ requests/second per worker
- Actual depends on target server response time
- Database write operations may bottleneck

### Optimization
- Connection pooling (Guzzle)
- Request caching
- Async database operations (future)

---
