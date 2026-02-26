<?php
/**
 * CCTV Dashboard - Main Page
 * Menampilkan daftar unit kebun dengan jumlah kamera
 */

try {
    // Load configuration with error handling
    $config_file = __DIR__ . '/config.php';
    if (!file_exists($config_file)) {
        throw new Exception("Config file not found: $config_file");
    }
    
    $config = require $config_file;
    
    if (!is_array($config) || empty($config['host'])) {
        throw new Exception("Invalid configuration returned from config.php");
    }
    
    // Load Database class
    $db_class_file = __DIR__ . '/Database.php';
    if (!file_exists($db_class_file)) {
        throw new Exception("Database class not found: $db_class_file");
    }
    
    require $db_class_file;
    
    // Initialize database
    $db = new Database($config);
    
    // Log access
    error_log("[CCTV Dashboard] Access from: {$_SERVER['REMOTE_ADDR']} at " . date('Y-m-d H:i:s'));
    
    // Query dengan prepared statement
    $sql = "SELECT nama_kebun, COUNT(*) as jumlah_kamera 
            FROM data_cctv 
            GROUP BY nama_kebun 
            ORDER BY nama_kebun ASC";
    
    $result = $db->query($sql);
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    
} catch (Exception $e) {
    error_log("[ERROR] Dashboard: " . $e->getMessage());
    $rows = [];
    $error_message = "Terjadi kesalahan saat memuat data. Hubungi administrator.";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Portal CCTV - PTPN I</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header>
        <div class="header-left">
            <img src="logo_ptpn.png" class="logo-ptpn" alt="Logo">
            <div class="title-section">
                <h1>PT. Perkebunan Nusantara I</h1>
                <p>PORTAL MONITORING CCTV</p>
            </div>
        </div>
        <div id="clock" class="clock-display">00.00.00 WIB</div>
    </header>


    <main class="main-grid">
        <?php if (!empty($error_message)): ?>
            <div style="grid-column: 1/-1; padding: 20px; background: #8b0000; border-radius: 8px; color: white;">
                <strong>⚠️ Error:</strong> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $row): ?>
                <a href="unit.php?nama=<?php echo urlencode($row['nama_kebun']); ?>" class="kebun-card">
                    <div class="icon">🏢</div>
                    <div class="name"><?php echo htmlspecialchars($row['nama_kebun']); ?></div>
                    <div class="badge"><?php echo intval($row['jumlah_kamera']); ?> KAMERA TERSEDIA</div>
                    <div style="margin-top:15px; font-size:12px; color:#94a3b8;">Klik untuk memantau &rarr;</div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; padding: 20px; text-align: center; color: #94a3b8;">
                <p>📭 Data kebun tidak tersedia</p>
            </div>
        <?php endif; ?>
    </main>

    <script src="script.js"></script>

</body>

</html>

</body>

</html>