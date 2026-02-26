# 🚀 Quick Start Guide - CCTV Dashboard

## ⚡ 5-Minute Setup

### Local Development (Laragon)
```bash
1. Start Laragon (Apache + MySQL)
2. Open: http://localhost/cctv/index.php
3. Done! 🎉
```

### Configuration
- Database config: `config.php` (auto-detects development vs production)
- Environment: Set `APP_ENV` env variable (default: "development")

---

## 📝 Common Tasks

### Add New Camera
```sql
INSERT INTO data_cctv (nama_kebun, ip_cctv) VALUES ('Unit Name', '192.168.1.100:8000');
```

### Update Camera Stream URL
```sql
UPDATE data_cctv SET ip_cctv = 'NEW_URL' WHERE nama_kebun = 'Unit Name';
```

### Add New Database Query
```php
// Safe way with prepared statements:
$sql = "SELECT * FROM data_cctv WHERE id = ?";
$result = $db->query($sql, "i", [1]);
$row = $result->fetch_assoc();
```

---

## 🔒 Security Quick Wins

✅ **Always use prepared statements:**
```php
// Good
$db->query("SELECT * FROM data_cctv WHERE nama_kebun = ?", "s", [$unit_name]);

// Bad
$result = $conn->query("SELECT * FROM data_cctv WHERE id = $id");
```

✅ **Always output-encode in HTML:**
```php
// Good
<?php echo htmlspecialchars($user_input); ?>

// Bad
<?php echo $user_input; ?>
```

✅ **Validate input:**
```php
if (empty($input) || strlen($input) > 255) {
    die("Invalid input");
}
```

---

## 🧪 Testing

### Test Dashboard
```
http://localhost/cctv/index.php
```
Should show 3 units with camera counts

### Test Unit Monitoring
```
http://localhost/cctv/unit.php?nama=Kertamanah
```
Should display camera feed

### Test Security
```
http://localhost/cctv/unit.php?nama=test' OR 1=1--
```
Should return "Data tidak ditemukan" (not database error)

---

## 📊 Database Schema

```
Table: data_cctv
├── id (INT, Primary Key, Auto Increment)
├── nama_kebun (VARCHAR 255) - Unit/location name
└── ip_cctv (VARCHAR 45) - IP address or URL
```

**Current Data:**
- Kertamanah: 118.97.184.202:11000
- Malabar: 118.97.184.202:12000
- Sedep: 118.97.184.202:15000

---

## 🆘 Troubleshooting

| Problem | Solution |
|---------|----------|
| **"Database connection error"** | Check MySQL is running → Verify credentials in config.php |
| **No data displayed** | Verify data exists: `mysql cctv_db -e "SELECT * FROM data_cctv;"` |
| **Camera not loading** | IP/URL format incorrect → Should be "IP:PORT" or "http://IP:PORT" |
| **Errors not showing** | In production, check PHP error_log file, not browser |

---

## 📚 Full Documentation

- **Security & Deployment:** [SECURITY.md](SECURITY.md)
- **What was fixed:** [FIXES_SUMMARY.md](FIXES_SUMMARY.md)
- **Original docs:** [README.md](README.md)

---

## 💾 Files Structure

```
Important Files:
- config.php         ← Database configuration
- Database.php       ← Database wrapper (use this!)
- index.php          ← Dashboard page
- unit.php           ← Monitoring page
- .env.example       ← Environment template
```

---

**Happy coding! 🎉**
