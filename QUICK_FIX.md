# 🚀 Quick Fix for Production Error

## 🎯 **3 Komando untuk Fix**

Jalankan ini di production server (10.100.11.220):

```bash
# 1. Create .env file dengan production settings
cat > /var/www/html/cctv/.env << 'EOF'
APP_ENV=production
DB_HOST=10.100.11.220
DB_USER=cctv_user
DB_PASS=cctv_N1HO
DB_NAME=cctv_db
EOF

# 2. Set proper permissions
chmod 644 /var/www/html/cctv/.env
chmod 755 /var/www/html/cctv

# 3. Restart web server
sudo systemctl restart nginx
# atau jika pakai Apache:
# sudo systemctl restart apache2
```

**Done! Try akses:** http://10.100.11.220/cctv/index.php

---

## 🔍 **Atau Gunakan Web Installer**

1. Upload `setup.php` ke server
2. Buka di browser: http://10.100.11.220/cctv/setup.php
3. Fill form dan klik "Save .env File"
4. Test database connection
5. Delete `setup.php`

---

## ✅ **Apa yang Fixed**

**Before:**
- Environment Mode: development ❌ (salah di production)
- Database Host: localhost ❌ (should be 10.100.11.220)

**After:**
- Environment Mode: production ✅
- Database Host: 10.100.11.220 ✅
- Config loading yang robust ✅
- Better error handling ✅

---

## 🧪 **Test Hasilnya**

Setelah apply fix, akses:
```
http://10.100.11.220/cctv/test_connection.php
```

Should show:
```
Environment Mode: production ✓
Database Host: 10.100.11.220 ✓
Connection: ✓ Connected
Total cameras: 3 ✓
```

---

## 📋 **What Changed**

1. **config.php** - Improved dengan .env file loading
2. **index.php** - Better file path handling & error messages
3. **unit.php** - Same improvements as index.php
4. **setup.php** - NEW - Web-based setup helper  
5. **test_connection.php** - NEW - Diagnostic tool

---

**Setelah fix ini, error seharusnya hilang! 🎉**
