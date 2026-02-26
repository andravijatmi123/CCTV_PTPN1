# 🔒 CCTV Dashboard - Security & Deployment Guide

## ✅ Security Improvements Implemented

### 1. **SQL Injection Prevention**
- ✅ Implemented `Database` class with prepared statements
- ✅ All queries use parameterized statements with proper type binding
- ✅ No direct SQL concatenation with user input

**Example (Before → After):**
```php
// ❌ BEFORE (Vulnerable)
$sql = "SELECT * FROM data_cctv WHERE nama_kebun = '$unit_name'";

// ✅ AFTER (Safe)
$sql = "SELECT * FROM data_cctv WHERE nama_kebun = ?";
$result = $db->query($sql, "s", [$unit_name]);
```

### 2. **Input Validation & Sanitization**
- ✅ Validate input length (max 255 characters)
- ✅ Trim whitespace
- ✅ HTML encode output with `htmlspecialchars()`
- ✅ Type casting for numeric values

### 3. **Error Handling & Logging**
- ✅ Try-catch blocks for error handling
- ✅ Sensitive errors logged to file, not displayed to users
- ✅ User-friendly error messages
- ✅ Access logging for audit trail

### 4. **Environment Configuration**
- ✅ Support for `.env` files
- ✅ Environment-based configuration (development/production)
- ✅ Secure credential management
- ✅ Separated config from code

### 5. **Database Security**
- ✅ Use limited database user (cctv_user) for production
- ✅ Restricted permissions (SELECT only for most operations)
- ✅ Unique password for production
- ✅ User cannot access system tables

---

## 📁 New Files Created

| File | Purpose |
|------|---------|
| `config.php` | Centralized database configuration with environment support |
| `Database.php` | Database wrapper class with prepared statements |
| `Env.php` | Environment variable loader |
| `.env.example` | Environment configuration template |
| `SECURITY.md` | This file - Security documentation |

---

## 🚀 Deployment Steps for Production

### Step 1: Setup Server Environment

```bash
# On production server
sudo mkdir /var/log/cctv
sudo chown www-data:www-data /var/log/cctv
sudo chmod 755 /var/log/cctv
```

### Step 2: Copy Application Files

```bash
# Copy all files to web root
scp -r cctv/ user@10.100.11.220:/var/www/html/

# Set proper permissions
sudo chown -R www-data:www-data /var/www/html/cctv
sudo chmod 755 /var/www/html/cctv
sudo chmod 644 /var/www/html/cctv/*.php
sudo chmod 644 /var/www/html/cctv/*.css
sudo chmod 644 /var/www/html/cctv/*.js
```

### Step 3: Create .env File

```bash
cd /var/www/html/cctv

# Copy template
cp .env.example .env

# Edit with production values
nano .env
```

**Production .env:**
```
APP_ENV=production
DB_HOST=10.100.11.220
DB_USER=cctv_user
DB_PASS=cctv_N1HO
DB_NAME=cctv_db
```

### Step 4: Create Production Database User

```sql
-- On MySQL server (10.100.11.220)
CREATE USER 'cctv_user'@'%' IDENTIFIED BY 'cctv_N1HO';

-- Grant minimal permissions
GRANT SELECT, INSERT, UPDATE ON cctv_db.* TO 'cctv_user'@'%';

-- Apply changes
FLUSH PRIVILEGES;

-- Verify
SHOW GRANTS FOR 'cctv_user'@'%';
```

### Step 5: Configure Web Server (Apache/Nginx)

**Apache VirtualHost:**
```apache
<VirtualHost *:443>
    ServerName cctv.ptpn1.com
    DocumentRoot /var/www/html/cctv
    
    # Enable SSL
    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/cctv.ptpn1.com.crt
    SSLCertificateKeyFile /etc/ssl/private/cctv.ptpn1.com.key
    
    # Security headers
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
    
    # PHP Settings
    php_flag display_errors off
    php_flag log_errors on
    php_value error_log /var/log/cctv/php_error.log
    
    # Restrict access to config files
    <FilesMatch "\.env|config\.php|Database\.php">
        Order allow,deny
        Deny from all
    </FilesMatch>
</VirtualHost>

# Redirect HTTP to HTTPS
<VirtualHost *:80>
    ServerName cctv.ptpn1.com
    Redirect permanent / https://cctv.ptpn1.com/
</VirtualHost>
```

### Step 6: Configure PHP

Edit `/etc/php/8.2/apache2/php.ini`:

```ini
; Display errors
display_errors = Off
log_errors = On
error_log = /var/log/cctv/php_error.log
error_reporting = E_ALL

; Security
expose_php = Off
disable_functions = exec,passthru,shell_exec,system

; Sessions
session.secure = On
session.httponly = On
session.samesite = Strict

; Database
mysqli.default_socket = /var/run/mysqld/mysqld.sock
```

### Step 7: Setup Backups

```bash
# Create backup script: /etc/cron.daily/cctv-backup
#!/bin/bash
BACKUP_DIR="/backups/cctv"
DATE=$(date +%Y%m%d_%H%M%S)

# Backup database
mysqldump -u cctv_user -p'cctv_N1HO' cctv_db > $BACKUP_DIR/cctv_db_$DATE.sql

# Compress
gzip $BACKUP_DIR/cctv_db_$DATE.sql

# Keep only last 7 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +7 -delete
```

### Step 8: Enable Firewall Rules

```bash
# UFW (Ubuntu)
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow from 10.100.11.220 to any port 3306  # MySQL access from DB server
```

---

## 🔍 Security Checklist

- [ ] Environment variables configured in `.env`
- [ ] Database user has minimal permissions (SELECT only)
- [ ] SSL/HTTPS enabled
- [ ] PHP display_errors = Off
- [ ] Error logging enabled
- [ ] Backup strategy implemented
- [ ] Firewall rules configured
- [ ] Web server security headers set
- [ ] Access logs monitored
- [ ] Database connection encrypted (SSL)
- [ ] Config files not web-accessible
- [ ] File permissions set correctly (644 for files, 755 for dirs)

---

## 🧪 Testing Before Production

```php
// test_connection.php
<?php
require 'config.php';
require 'Database.php';

try {
    $db = new Database($config);
    $result = $db->query("SELECT COUNT(*) as total FROM data_cctv;");
    $row = $result->fetch_assoc();
    echo "✅ Database connection: OK\n";
    echo "Total cameras: " . $row['total'] . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
```

Run: `php test_connection.php`

---

## 📊 Monitoring & Maintenance

### Daily Tasks
- [ ] Check error logs: `tail -f /var/log/cctv/php_error.log`
- [ ] Monitor disk space
- [ ] Verify backup completed

### Weekly Tasks
- [ ] Review access logs
- [ ] Check database size
- [ ] Verify SSL certificate expiration

### Monthly Tasks
- [ ] Security updates
- [ ] Password rotation
- [ ] Performance analysis

---

## 🆘 Troubleshooting

### Error: "Database connection error"
- Check MySQL server is running
- Verify credentials in `.env`
- Check network connectivity
- Review `/var/log/cctv/php_error.log`

### Error: "Access denied for user"
- Verify `cctv_user` permissions
- Check database name
- Test with: `mysql -u cctv_user -p cctv_db`

### Slow Performance
- Add database indexes
- Check query logs: `mysql> SET GLOBAL slow_query_log = 'ON';`
- Monitor Apache processes

---

## 📞 Support

For security issues or questions:
1. Check error logs
2. Review this documentation
3. Contact system administrator
4. Document the issue for future reference

**Last Updated:** 2026-02-26  
**Version:** 1.0 - Production Ready
