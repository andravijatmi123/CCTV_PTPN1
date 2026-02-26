<?php
// 1. KONFIGURASI KONEKSI
$host     = 'localhost';
$user     = 'root';
$password = '';
$database = 'cctv';

$conn = new mysqli($host, $user, $password, $database);
$conn->set_charset("utf8");

// 2. MENGAMBIL PARAMETER DARI URL
$unit_name = isset($_GET['nama']) ? $_GET['nama'] : 'Unit Tidak Diketahui';

// 3. QUERY DATA KAMERA SESUAI UNIT
$sql = "SELECT * FROM data_cctv WHERE nama_kebun = '$unit_name' ORDER BY id ASC";
$result = $conn->query($sql);

$cameras = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $cameras[] = $row;
    }
}

// Menghitung total untuk keperluan layouting dinamis
$total_kamera = count($cameras);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring <?php echo htmlspecialchars($unit_name); ?></title>
    
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
            <p>CCTV <?php echo strtoupper(htmlspecialchars($unit_name)); ?></p>
        </div>
    </div>
    <div id="clock" class="clock-display">00.00.00 WIB</div>
</header>

    <div class="monitor-wrapper">
        <?php if ($total_kamera > 0): ?>
            <?php foreach ($cameras as $index => $cam): ?>
                <div class="cam-card">
                    <iframe src="<?php echo htmlspecialchars($cam['ip_cctv']); ?>" 
                            allow="autoplay; encrypted-media" 
                            allowfullscreen></iframe>
                    
                    <div class="cam-label">
                        <span class="live-dot"></span>
                        CAM <?php echo $index + 1; ?> - <?php echo htmlspecialchars($unit_name); ?>
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