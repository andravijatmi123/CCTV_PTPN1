# 🆘 Production Error Troubleshooting Guide

## ⚠️ Error Found
```
⚠️ Error: Terjadi kesalahan saat memuat data. Hubungi administrator.
```

This error means the database connection or query failed. The actual error is hidden from users (security best practice).

---

## 🔍 Step 1: Check the Actual Error

### Create test file (temporary)
Upload `test_connection.php` to production and open:
```
http://10.100.11.220/cctv/test_connection.php
```

This will show:
- ✅ What's working
- ❌ What's failing
- 📊 Database structure
- 📋 Sample data

### OR Check Error Log Directly
```bash
# SSH into production server
ssh root@10.100.11.220

# Check PHP error log
tail -f /var/log/cctv/php_error.log

# Check MySQL error log
tail -f /var/log/mysql/error.log
```

---

## 🔧 Common Issues & Fixes

### ❌ Issue 1: Database Connection Refused

**Symptoms:**
```
Connection failed
Error Code: 2003
Can't connect to MySQL server on '10.100.11.220'
```

**Causes & Fixes:**
```bash
# Fix 1: Verify MySQL is running
sudo systemctl status mysql

# Fix 2: Check if port 3306 is listening
netstat -tlnp | grep 3306
# Should show: tcp  0  0  0.0.0.0:3306  0.0.0.0:*  LISTEN

# Fix 3: Check network connectivity
ping 10.100.11.220
telnet 10.100.11.220 3306

# Fix 4: Check firewall
sudo ufw status
sudo ufw allow from any to any port 3306
```

---

### ❌ Issue 2: Access Denied for User

**Symptoms:**
```
Access denied for user 'cctv_user'@'10.100.11.220'
Using password: YES
```

**Causes & Fixes:**
```bash
# Fix 1: Check MySQL user privileges
mysql -u root -p
MariaDB> SELECT user, host FROM mysql.user WHERE user = 'cctv_user';
MariaDB> SHOW GRANTS FOR 'cctv_user'@'10.100.11.220';

# Fix 2: Recreate user with correct password
MariaDB> DROP USER 'cctv_user'@'10.100.11.220';
MariaDB> CREATE USER 'cctv_user'@'10.100.11.220' IDENTIFIED BY 'TZdWyZUcsvtn18JTy626';
MariaDB> GRANT SELECT ON cctv_db.* TO 'cctv_user'@'10.100.11.220';
MariaDB> FLUSH PRIVILEGES;

# Fix 3: Test connection directly
mysql -h 10.100.11.220 -u cctv_user -p'TZdWyZUcsvtn18JTy626' cctv_db

# Fix 4: Check if password in config matches
cat /var/www/html/cctv/.env
# Should have: DB_PASS=TZdWyZUcsvtn18JTy626
```

---

### ❌ Issue 3: Unknown Database

**Symptoms:**
```
Unknown database 'cctv_db'
```

**Causes & Fixes:**
```bash
# Fix 1: Check if database exists
mysql -u root -p
MariaDB> SHOW DATABASES;
MariaDB> USE cctv_db;
MariaDB> SHOW TABLES;

# Fix 2: If missing, create database
MariaDB> CREATE DATABASE cctv_db CHARACTER SET utf8mb4;
MariaDB> USE cctv_db;

# Fix 3: Import database schema
mysql -u root cctv_db < data_cctv.sql

# Fix 4: Verify data
MariaDB> SELECT * FROM data_cctv;
# Should show: Kertamanah, Malabar, Sedep
```

---

### ❌ Issue 4: Table Not Found

**Symptoms:**
```
Table 'cctv_db.data_cctv' doesn't exist
```

**Fixes:**
```bash
# Check table exists
mysql -u root cctv_db -e "SHOW TABLES;"

# If not exists, import schema
mysql -u root cctv_db < data_cctv.sql

# Verify
mysql -u root cctv_db -e "DESCRIBE data_cctv;"
```

---

### ❌ Issue 5: Environment Variable Not Set

**Symptoms:**
Connecting to wrong server, wrong user, or wrong database

**Fixes:**
```bash
# Check environment variables
echo $APP_ENV
echo $DB_HOST
echo $DB_USER
echo $DB_NAME

# Set environment variables (Apache)
# Edit: /etc/apache2/envvars
export APP_ENV=production
export DB_HOST=10.100.11.220
export DB_USER=cctv_user
export DB_PASS='TZdWyZUcsvtn18JTy626'
export DB_NAME=cctv_db

# Restart Apache
sudo systemctl restart apache2

# Or use .env file
cat > /var/www/html/cctv/.env << EOF
APP_ENV=production
DB_HOST=10.100.11.220
DB_USER=cctv_user
DB_PASS=TZdWyZUcsvtn18JTy626
DB_NAME=cctv_db
EOF
```

---

### ❌ Issue 6: Wrong Server IP

**Config shows:** `10.100.11.220`
**But actual DB is on:** Different server

**Fixes:**
```
1. Find actual database server IP
2. Update .env file with correct IP
3. Verify network connectivity to new IP
4. Verify database user permissions for new IP
```

---

### ❌ Issue 7: Wrong Password

**Symptoms:**
```
Access denied for user 'cctv_user'@'10.100.11.220' (using password: YES)
```

**Fixes:**
```bash
# Check what password is in config
grep DB_PASS /var/www/html/cctv/.env

# Current password in config: TZdWyZUcsvtn18JTy626
# If different, update MySQL user:
mysql -u root
MariaDB> ALTER USER 'cctv_user'@'10.100.11.220' IDENTIFIED BY 'TZdWyZUcsvtn18JTy626';
MariaDB> FLUSH PRIVILEGES;

# Test new password
mysql -h 10.100.11.220 -u cctv_user -p'TZdWyZUcsvtn18JTy626' cctv_db
```

---

## 📋 Complete Diagnostic Checklist

### Network & Server
```bash
☐ MySQL server running?
   sudo systemctl status mysql

☐ Port 3306 open?
   netstat -tlnp | grep 3306

☐ Network connectivity?
   ping 10.100.11.220
   telnet 10.100.11.220 3306

☐ Firewall allows connection?
   sudo ufw status
```

### Database
```bash
☐ Database exists?
   mysql -u root -e "SHOW DATABASES LIKE 'cctv_db';"

☐ Table exists?
   mysql -u root cctv_db -e "SHOW TABLES;"

☐ Data exists?
   mysql -u root cctv_db -e "SELECT * FROM data_cctv;"

☐ User exists?
   mysql -u root -e "SELECT user, host FROM mysql.user WHERE user='cctv_user';"
```

### Configuration
```bash
☐ Environment variables set?
   echo $APP_ENV; echo $DB_HOST; echo $DB_USER

☐ .env file exists?
   ls -la /var/www/html/cctv/.env

☐ .env has correct values?
   cat /var/www/html/cctv/.env

☐ config.php is readable?
   ls -la /var/www/html/cctv/config.php
```

### Application
```bash
☐ test_connection.php works?
   curl http://10.100.11.220/cctv/test_connection.php

☐ PHP can write logs?
   ls -la /var/log/cctv/

☐ PHP error log shows errors?
   tail -f /var/log/cctv/php_error.log

☐ File permissions correct?
   ls -la /var/www/html/cctv/*.php
```

---

## 🚀 Quick Fix Steps

### If you just deployed:

1. **Create test file**
   ```bash
   cd /var/www/html/cctv
   # Upload test_connection.php
   ```

2. **Run test**
   ```
   Browser: http://10.100.11.220/cctv/test_connection.php
   ```

3. **Note the error**
   - Connection failed?
   - Authentication failed?
   - Query failed?

4. **Fix specific issue** (see Common Issues above)

5. **Re-test**
   ```
   Browser: http://10.100.11.220/cctv/index.php
   ```

6. **Delete test file** (security)
   ```bash
   rm /var/www/html/cctv/test_connection.php
   ```

---

## 📞 If Still Not Working

Provide these details to support:

```
1. Output dari test_connection.php:
   - What's the first error shown?
   
2. MySQL version:
   mysql --version
   
3. PHP version:
   php --version
   
4. Server info:
   uname -a
   
5. Config values:
   echo "Host: "; echo $DB_HOST
   echo "User: "; echo $DB_USER
   echo "Database: "; echo $DB_NAME

6. Error logs:
   tail -50 /var/log/cctv/php_error.log
   tail -50 /var/log/mysql/error.log
   
7. Network check:
   telnet 10.100.11.220 3306
```

---

**Remember: Check error logs first! They contain the real error message.** 🔍
