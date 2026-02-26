# ✅ Production Deployment Checklist

**Project:** CCTV Dashboard - PT. Perkebunan Nusantara I  
**Target Server:** 10.100.11.220  
**Database Server:** 10.100.11.220  

---

## 🔧 Pre-Deployment Tasks

### Code & Security
- [ ] All files updated with prepared statements
- [ ] Input validation implemented
- [ ] Error logging configured
- [ ] `.env.example` reviewed
- [ ] No hardcoded credentials in code
- [ ] All sensitive data moved to environment variables

### Testing
- [ ] Test dashboard page locally
- [ ] Test unit monitoring page locally
- [ ] Test input validation/security
- [ ] Test database connectivity
- [ ] Test error handling

### Documentation
- [ ] Read [SECURITY.md](SECURITY.md)
- [ ] Review deployment steps
- [ ] Understand environment configuration

---

## 🖥️ Server Setup (Execute on 10.100.11.220)

### Step 1: Web Server Configuration
- [ ] Create web root directory
- [ ] Set correct file permissions (755 for dirs, 644 for files)
- [ ] Configure SSL/HTTPS certificate
- [ ] Setup Apache VirtualHost with security headers
- [ ] Redirect HTTP → HTTPS

### Step 2: PHP Configuration
- [ ] Edit `/etc/php/8.2/apache2/php.ini`
- [ ] Set `display_errors = Off`
- [ ] Set `log_errors = On`
- [ ] Create log directory: `/var/log/cctv`
- [ ] Set proper permissions for log directory

### Step 3: Database User Setup
- [ ] Create user: `cctv_user`
- [ ] Set password: `cctv_N1HO` (update if different)
- [ ] Grant permissions on `cctv_db` only
- [ ] Test connection: `mysql -u cctv_user -p cctv_db`
- [ ] Verify SELECT-only permissions

### Step 4: Application Deployment
- [ ] Copy all files to web root
- [ ] Create `.env` file from `.env.example`
- [ ] Update `.env` with production values
- [ ] Set `APP_ENV=production`
- [ ] Remove `.env.example` from web root (optional)
- [ ] Verify config.php detects production mode

### Step 5: Backup Strategy
- [ ] Create backup directory: `/backups/cctv`
- [ ] Create backup script in `/etc/cron.daily/`
- [ ] Test backup script
- [ ] Verify backups are being created

### Step 6: Firewall & Network
- [ ] Allow port 80 (HTTP)
- [ ] Allow port 443 (HTTPS)
- [ ] Allow port 3306 from app server (if separate)
- [ ] Verify network connectivity between servers
- [ ] Test from client network

### Step 7: Monitoring & Logging
- [ ] Setup log rotation for PHP errors
- [ ] Setup log rotation for MySQL errors
- [ ] Monitor disk space
- [ ] Setup alert for error logs

---

## 🧪 Post-Deployment Verification

### Connectivity Tests
- [ ] Test: `http://10.100.11.220/cctv/index.php`
- [ ] Test: `https://10.100.11.220/cctv/index.php` (if HTTPS)
- [ ] Test database connection
- [ ] Check MySQL error log
- [ ] Check PHP error log

### Functional Tests
- [ ] Dashboard loads with all 3 units
- [ ] Click unit → monitoring page loads
- [ ] Verify camera count is correct
- [ ] Verify IP addresses display correctly

### Security Tests
- [ ] Test SQL injection: `unit.php?nama=test' OR 1=1--`
- [ ] Test XSS: `unit.php?nama=<script>alert('xss')</script>`
- [ ] Verify errors don't display in browser
- [ ] Verify SSL certificate warning (for self-signed): None
- [ ] Check HTTP → HTTPS redirect works

### Performance Tests
- [ ] Load dashboard
- [ ] Load monitoring page
- [ ] Check response time
- [ ] Check server resource usage

---

## 📋 Environment Configuration (.env)

```env
# Production .env
APP_ENV=production
DB_HOST=10.100.11.220
DB_USER=cctv_user
DB_PASS=cctv_N1HO
DB_NAME=cctv_db
ERROR_LOG_PATH=/var/log/cctv/php_error.log
```

---

## 🚨 Rollback Plan

If something goes wrong:

1. **Immediate:** Switch to previous version (keep backup)
2. **Database:** Restore from daily backup
3. **Files:** Restore from version control
4. **Verification:** Re-run security tests
5. **Root Cause:** Document and prevent future issues

**Rollback Time Estimate:** 30-60 minutes

---

## 📞 Support Contacts

| Role | Contact |
|------|---------|
| System Admin | [Your contact] |
| Database Admin | [Your contact] |
| Network Admin | [Your contact] |

---

## 📊 Deployment Sign-off

- [ ] All pre-deployment tasks completed
- [ ] All server setup tasks completed
- [ ] All verification tests passed
- [ ] Security tests passed
- [ ] Performance acceptable
- [ ] Rollback plan in place
- [ ] Team trained on application
- [ ] Monitoring setup verified

**Deployed By:** ________________  
**Date:** ________________  
**Time:** ________________  
**Approved By:** ________________  

---

## 📝 Post-Deployment Handoff

### Operational Team
- Daily backup verification
- Log monitoring
- Performance monitoring
- User support

### Maintenance Team
- Security updates
- Database optimization
- Disk space management
- Password rotation (annually)

---

**Production deployment ready! 🚀**
