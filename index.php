<?php
$host = 'localhost'; $user = 'root'; $pass = ''; $db = 'cctv';
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

if ($conn->connect_error) { die("Koneksi Gagal"); }

//melakukan grouping berdasarkan nama kebun untuk mendapatkan jumlah kamera per unit
$sql = "SELECT nama_kebun, COUNT(*) as jumlah_kamera FROM data_cctv GROUP BY nama_kebun ORDER BY nama_kebun ASC";
$result = $conn->query($sql);
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
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <a href="unit.php?nama=<?php echo urlencode($row['nama_kebun']); ?>" class="kebun-card">
                <div class="icon">🏢</div>
                <div class="name"><?php echo htmlspecialchars($row['nama_kebun']); ?></div>
                <div class="badge"><?php echo $row['jumlah_kamera']; ?> KAMERA TERSEDIA</div>
                <div style="margin-top:15px; font-size:12px; color:#94a3b8;">Klik untuk memantau &rarr;</div>
            </a>
        <?php endwhile; ?>
    <?php endif; ?>
</main>

<script src="script.js"></script>

</body>
</html>

</body>
</html>