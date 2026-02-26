<?php
/**
 * CCTV Unit Monitoring Page
 * Menampilkan feed CCTV untuk unit tertentu dengan security
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
    
    // 1. GET & VALIDATE INPUT
    $unit_name = isset($_GET['nama']) ? trim($_GET['nama']) : '';
    
    // Input validation
    if (empty($unit_name)) {
        throw new Exception("Unit name tidak boleh kosong");
    }
    
    if (strlen($unit_name) > 255) {
        throw new Exception("Unit name terlalu panjang");
    }
    
    // Sanitize untuk display
    $unit_name_safe = htmlspecialchars($unit_name, ENT_QUOTES, 'UTF-8');
    
    // Log access
    error_log("[CCTV Monitoring] Unit: $unit_name | From: {$_SERVER['REMOTE_ADDR']} at " . date('Y-m-d H:i:s'));
    
    // 2. QUERY DATA KAMERA DENGAN PREPARED STATEMENT
    $sql = "SELECT id, nama_kebun, ip_cctv FROM data_cctv WHERE nama_kebun = ? ORDER BY id ASC";
    $result = $db->query($sql, "s", [$unit_name]);
    
    $cameras = [];
    if ($result) {
        $cameras = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // 3. COUNT FOR LAYOUT
    $total_kamera = count($cameras);
    
} catch (Exception $e) {
    error_log("[ERROR] Unit Page: " . $e->getMessage());
    $unit_name_safe = "Unknown";
    $cameras = [];
    $total_kamera = 0;
    $error_message = "Terjadi kesalahan: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring <?php echo $unit_name_safe; ?></title>
    
    <link rel="stylesheet" href="style.css">

    <style>
        /* Jika hanya ada 1 kamera, buat full screen */
        <?php if($total_kamera == 1): ?>
        .monitor-wrapper { grid-template-columns: 1fr; }
        .cam-card { height: calc(100vh - 130px) !important; }
        <?php endif; ?>

        /* Jika jumlah ganjil, kamera terakhir tampil di tengah */
        .cam-card:last-child:nth-child(odd):not(:first-child) {
            grid-column: 1 / span 2;
            width: 50%;
            justify-self: center;
        }
    </style>
</head>
<body>

<header>
    <div class="header-left">
        <img src="logo_ptpn.png" class="logo-ptpn" alt="Logo">
        <div class="title-section">
            <h1>PT. Perkebunan Nusantara I</h1>
            <p>CCTV <?php echo strtoupper($unit_name_safe); ?></p>
        </div>
    </div>
    <div id="clock" class="clock-display">00.00.00 WIB</div>
</header>

    <div class="monitor-wrapper">
        <?php if (!empty($error_message)): ?>
            <div style="grid-column: 1/-1; padding: 20px; background: #8b0000; border-radius: 8px; color: white; margin-bottom: 10px;">
                <strong>⚠️ Error:</strong> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($total_kamera > 0): ?>
            <?php foreach ($cameras as $index => $cam): ?>
                <div class="cam-card">
                    <iframe src="<?php echo htmlspecialchars($cam['ip_cctv'], ENT_QUOTES, 'UTF-8'); ?>" 
                            allow="autoplay; encrypted-media" 
                            allowfullscreen></iframe>
                    
                    <div class="cam-label">
                        <span class="live-dot"></span>
                        CAM <?php echo intval($index + 1); ?> - <?php echo $unit_name_safe; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 300px; opacity: 0.5;">
                <div style="font-size: 50px;">🚫</div>
                <h2 style="color: #fff;">Data Kebun Tidak Ditemukan</h2>
                <p>Silakan hubungi Administrator atau cek database.</p>
                <a href="index.php" style="color: var(--accent-green); text-decoration: none; margin-top: 20px;">&larr; Kembali ke Menu Utama</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="script.js"></script>

</body>
</html>