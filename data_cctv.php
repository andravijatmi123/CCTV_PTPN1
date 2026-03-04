<?php
/**
 * CRUD Page untuk Tabel data_cctv
 * - List semua data CCTV
 * - Tambah data CCTV baru
 * - Edit data CCTV
 * - Hapus data CCTV
 * - nama_kebun: dropdown jika ada, textfield jika baru
 */

session_start();

try {
    // Load configuration
    $config = require __DIR__ . '/config.php';
    require __DIR__ . '/Database.php';
    
    // Initialize database
    $db = new Database($config);
    
    $message = '';
    $message_type = '';
    $edit_data = null;
    $edit_id = null;
    
    // Handle Form Submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        // ADD - Data CCTV Baru
        if ($action === 'add') {
            $nama_kebun = trim($_POST['nama_kebun'] ?? '');
            $nama_kebun_new = trim($_POST['nama_kebun_new'] ?? '');
            $ip_cctv = trim($_POST['ip_cctv'] ?? '');
            
            // Gunakan textfield jika diisi, otherwise dropdown
            $nama_kebun_final = !empty($nama_kebun_new) ? $nama_kebun_new : $nama_kebun;
            
            if (empty($nama_kebun_final)) {
                $message = "Nama kebun tidak boleh kosong!";
                $message_type = 'error';
            } else if (empty($ip_cctv)) {
                $message = "IP CCTV tidak boleh kosong!";
                $message_type = 'error';
            } else if (!filter_var($ip_cctv, FILTER_VALIDATE_URL)) {
                $message = "IP CCTV harus URL yang valid (misal: http://...)!";
                $message_type = 'error';
            } else {
                // Insert data baru
                $db->query(
                    "INSERT INTO data_cctv (nama_kebun, ip_cctv) VALUES (?, ?)",
                    "ss",
                    [$nama_kebun_final, $ip_cctv]
                );
                
                $message = "Data CCTV berhasil ditambahkan!";
                $message_type = 'success';
            }
        }
        
        // EDIT - Update data CCTV
        if ($action === 'edit') {
            $id = intval($_POST['id'] ?? 0);
            $nama_kebun = trim($_POST['nama_kebun'] ?? '');
            $nama_kebun_new = trim($_POST['nama_kebun_new'] ?? '');
            $ip_cctv = trim($_POST['ip_cctv'] ?? '');
            
            // Gunakan textfield jika diisi, otherwise dropdown
            $nama_kebun_final = !empty($nama_kebun_new) ? $nama_kebun_new : $nama_kebun;
            
            if ($id <= 0) {
                $message = "Data tidak valid!";
                $message_type = 'error';
            } else if (empty($nama_kebun_final)) {
                $message = "Nama kebun tidak boleh kosong!";
                $message_type = 'error';
            } else if (empty($ip_cctv)) {
                $message = "IP CCTV tidak boleh kosong!";
                $message_type = 'error';
            } else if (!filter_var($ip_cctv, FILTER_VALIDATE_URL)) {
                $message = "IP CCTV harus URL yang valid (misal: http://...)!";
                $message_type = 'error';
            } else {
                // Update data
                $db->query(
                    "UPDATE data_cctv SET nama_kebun = ?, ip_cctv = ? WHERE id = ?",
                    "ssi",
                    [$nama_kebun_final, $ip_cctv, $id]
                );
                
                $message = "Data CCTV berhasil diperbarui!";
                $message_type = 'success';
            }
        }
        
        // DELETE - Hapus data CCTV
        if ($action === 'delete') {
            $id = intval($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                $message = "Data tidak valid!";
                $message_type = 'error';
            } else {
                // Get data before delete for display
                $old_data = $db->getRow("SELECT * FROM data_cctv WHERE id = ?", "i", [$id]);
                
                // Delete
                $db->query("DELETE FROM data_cctv WHERE id = ?", "i", [$id]);
                
                $message = "Data CCTV berhasil dihapus!";
                $message_type = 'success';
            }
        }
    }
    
    // Get all unique kebun names for dropdown
    $semua_kebun = $db->getAll(
        "SELECT DISTINCT nama_kebun FROM data_cctv ORDER BY nama_kebun ASC"
    );
    
    // Pagination setup
    $items_per_page = 10;
    $current_page = max(1, intval($_GET['page'] ?? 1));
    
    // Get total count
    $count_result = $db->getRow(
        "SELECT COUNT(*) as total FROM data_cctv"
    );
    $total_items = $count_result['total'];
    $total_pages = max(1, ceil($total_items / $items_per_page));
    
    // Validate current page
    if ($current_page > $total_pages) {
        $current_page = $total_pages;
    }
    
    // Calculate offset
    $offset = ($current_page - 1) * $items_per_page;
    
    // Get paginated data
    $all_cctv = $db->getAll(
        "SELECT id, nama_kebun, ip_cctv FROM data_cctv ORDER BY nama_kebun ASC, id ASC LIMIT ? OFFSET ?",
        "ii",
        [$items_per_page, $offset]
    );
    
    // Check if edit mode
    if (isset($_GET['edit'])) {
        $edit_id = intval($_GET['edit']);
        $edit_data = $db->getRow("SELECT * FROM data_cctv WHERE id = ?", "i", [$edit_id]);
    }
    
} catch (Exception $e) {
    error_log("[CCTV CRUD ERROR] " . $e->getMessage());
    $all_cctv = [];
    $semua_kebun = [];
    $message = "Terjadi kesalahan. Hubungi administrator.";
    $message_type = 'error';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Data CCTV - CCTV Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #333;
            font-size: 28px;
        }
        
        .header a {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .header a:hover {
            background: #5568d3;
        }
        
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }
        
        .content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
        }
        
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .card h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 20px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
            font-size: 14px;
        }
        
        input[type="text"],
        input[type="url"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="url"]:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }
        
        .kebun-input-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            align-items: end;
        }
        
        .kebun-input-group .form-group {
            margin-bottom: 0;
        }
        
        .kebun-label {
            font-size: 12px;
            color: #999;
        }
        
        button {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
            width: 100%;
            font-weight: 500;
        }
        
        button:hover {
            background: #5568d3;
        }
        
        button.secondary {
            background: #6c757d;
        }
        
        button.secondary:hover {
            background: #5a6268;
        }
        
        button.danger {
            background: #dc3545;
        }
        
        button.danger:hover {
            background: #c82333;
        }
        
        .table-wrapper {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #f0f0f0;
            color: #333;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #ddd;
            font-size: 14px;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        
        tr:hover {
            background: #f9f9f9;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            width: auto;
            flex: 1;
        }
        
        .btn-edit {
            background: #28a745;
        }
        
        .btn-edit:hover {
            background: #218838;
        }
        
        .btn-delete {
            background: #dc3545;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
        
        .btn-copy {
            background: #17a2b8;
        }
        
        .btn-copy:hover {
            background: #138496;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            max-width: 400px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-content h3 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .modal-buttons button {
            flex: 1;
        }
        
        .badge {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .info-text {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
            font-style: italic;
        }
        
        .edit-mode {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .edit-mode h3 {
            color: #856404;
            margin-bottom: 10px;
        }
        
        .edit-mode small {
            color: #856404;
        }
        
        .url-cell {
            font-family: monospace;
            font-size: 12px;
            color: #667eea;
            word-break: break-all;
        }
        
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #999;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .pagination a,
        .pagination span {
            display: inline-block;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #667eea;
            font-size: 13px;
            transition: all 0.3s;
            text-align: center;
            min-width: 35px;
        }
        
        .pagination a:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .pagination .active {
            background: #667eea;
            color: white;
            border-color: #667eea;
            font-weight: 600;
            cursor: default;
        }
        
        .pagination .disabled {
            color: #ccc;
            border-color: #eee;
            cursor: not-allowed;
            background: #f5f5f5;
        }
        
        .pagination .disabled:hover {
            background: #f5f5f5;
            color: #ccc;
            border-color: #eee;
        }
        
        .pagination-info {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin-bottom: 10px;
        }
        

        @media (max-width: 768px) {
            .content {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .kebun-input-group {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📷 CRUD Data CCTV</h1>
            <a href="index.php">← Kembali ke Dashboard</a>
        </div>
        
        <!-- Message -->
        <?php if (!empty($message)): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        
        <!-- Content -->
        <div class="content">
            <!-- Form Section -->
            <div class="card">
                <h2><?php echo $edit_data ? '✏️ Edit Data CCTV' : '➕ Tambah Data CCTV'; ?></h2>
                
                <?php if ($edit_data): ?>
                <div class="edit-mode">
                    <h3>Mode Edit</h3>
                    <small>ID: <strong><?php echo htmlspecialchars($edit_data['id']); ?></strong></small>
                    <br>
                    <a href="data_cctv.php" style="color: #667eea; text-decoration: none; margin-top: 10px; display: inline-block;">← Buat Data Baru</a>
                </div>
                <?php endif; ?>
                
                <form method="POST" onsubmit="return validateForm()">
                    <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
                    <?php if ($edit_data): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                    <?php endif; ?>
                    
                    <!-- Nama Kebun -->
                    <div class="form-group">
                        <label>Nama Kebun:</label>
                        <div class="kebun-input-group">
                            <div class="form-group">
                                <label class="kebun-label">📍 Pilih dari yang ada:</label>
                                <select id="nama_kebun" name="nama_kebun" onchange="updateKebunNewField()">
                                    <option value="">-- Baru / Lainnya --</option>
                                    <?php 
                                    foreach ($semua_kebun as $kebun): 
                                        $selected = ($edit_data && $edit_data['nama_kebun'] === $kebun['nama_kebun']) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo htmlspecialchars($kebun['nama_kebun']); ?>" <?php echo $selected; ?>>
                                        <?php echo htmlspecialchars($kebun['nama_kebun']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="kebun-label">✏️ Atau ketik nama baru:</label>
                                <input type="text" id="nama_kebun_new" name="nama_kebun_new" 
                                       placeholder="Nama kebun baru..."
                                       value="<?php echo ($edit_data && !in_array($edit_data['nama_kebun'], array_column($semua_kebun, 'nama_kebun'))) ? htmlspecialchars($edit_data['nama_kebun']) : ''; ?>">
                            </div>
                        </div>
                        <p class="info-text">💡 Pilih dari dropdown atau tulis nama baru (jika tidak ada di list)</p>
                    </div>
                    
                    <!-- IP CCTV -->
                    <div class="form-group">
                        <label for="ip_cctv">IP CCTV (URL):</label>
                        <input type="url" id="ip_cctv" name="ip_cctv" 
                               placeholder="http://192.168.1.1:8080/"
                               value="<?php echo $edit_data ? htmlspecialchars($edit_data['ip_cctv']) : ''; ?>"
                               required>
                        <p class="info-text">Misal: http://localhost:8889/nvr1/</p>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit">
                        <?php echo $edit_data ? '✓ Simpan Perubahan' : '✓ Tambah Data'; ?>
                    </button>
                </form>
            </div>
            
            <!-- List Section -->
            <div class="card">
                <h2>📋 Daftar Data CCTV</h2>
                
                <?php if (empty($all_cctv)): ?>
                <div class="empty-state">
                    <p>📭 Belum ada data CCTV</p>
                    <p style="font-size: 12px; margin-top: 10px;">Silakan tambah data baru di sebelah kiri</p>
                </div>
                <?php else: ?>
                <div class="pagination-info">
                    Menampilkan <?php echo count($all_cctv); ?> dari <?php echo $total_items; ?> data CCTV 
                    (Halaman <?php echo $current_page; ?> dari <?php echo $total_pages; ?>)
                </div>
                
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th width="50">ID</th>
                                <th width="120">Nama Kebun</th>
                                <th>IP CCTV (URL)</th>
                                <th width="140">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_cctv as $item): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($item['id']); ?></strong></td>
                                <td>
                                    <span class="badge">
                                        🌱 <?php echo htmlspecialchars($item['nama_kebun']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="url-cell">
                                        <a href="<?php echo htmlspecialchars($item['ip_cctv']); ?>" target="_blank" title="Buka di tab baru">
                                            🔗 <?php echo htmlspecialchars($item['ip_cctv']); ?>
                                        </a>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="data_cctv.php?edit=<?php echo $item['id']; ?>" style="background: #28a745; color: white; padding: 6px 12px; font-size: 12px; text-align: center; border-radius: 5px; text-decoration: none; transition: background 0.3s; display: flex; align-items: center; justify-content: center;">
                                            ✏️ Edit
                                        </a>
                                        <button class="btn-sm btn-delete" onclick="deleteData(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['nama_kebun']); ?>')">
                                            🗑️ Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Controls -->
                <div class="pagination">
                    <!-- Previous Button -->
                    <?php if ($current_page > 1): ?>
                        <a href="data_cctv.php?page=1" title="Ke halaman pertama">« Awal</a>
                        <a href="data_cctv.php?page=<?php echo $current_page - 1; ?>" title="Halaman sebelumnya">‹ Sebelumnya</a>
                    <?php else: ?>
                        <span class="disabled">« Awal</span>
                        <span class="disabled">‹ Sebelumnya</span>
                    <?php endif; ?>
                    
                    <!-- Page Numbers -->
                    <?php 
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);
                    
                    if ($start_page > 1): ?>
                        <a href="data_cctv.php?page=1">1</a>
                        <?php if ($start_page > 2): ?>
                            <span style="color: #999;">...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <?php if ($i == $current_page): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="data_cctv.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                            <span style="color: #999;">...</span>
                        <?php endif; ?>
                        <a href="data_cctv.php?page=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a>
                    <?php endif; ?>
                    
                    <!-- Next Button -->
                    <?php if ($current_page < $total_pages): ?>
                        <a href="data_cctv.php?page=<?php echo $current_page + 1; ?>" title="Halaman berikutnya">Berikutnya ›</a>
                        <a href="data_cctv.php?page=<?php echo $total_pages; ?>" title="Ke halaman terakhir">Akhir »</a>
                    <?php else: ?>
                        <span class="disabled">Berikutnya ›</span>
                        <span class="disabled">Akhir »</span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Modal Hapus Data -->
    <div id="modalDelete" class="modal">
        <div class="modal-content">
            <h3>⚠️ Konfirmasi Penghapusan</h3>
            <p style="color: #666; margin-bottom: 15px;">
                Apakah Anda yakin ingin menghapus data CCTV ini?
                <br><br>
                <strong>Kebun:</strong> <span id="deleteKebunName"></span>
                <br>
                <strong>ID:</strong> <span id="deleteId"></span>
            </p>
            
            <form method="POST" onsubmit="return true;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" id="modal_delete_id" name="id">
                
                <div class="modal-buttons">
                    <button type="submit" class="danger">🗑️ Hapus</button>
                    <button type="button" class="secondary" onclick="closeModal()">✕ Batal</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Update nama_kebun_new field based on dropdown
        function updateKebunNewField() {
            const dropdown = document.getElementById('nama_kebun');
            const textfield = document.getElementById('nama_kebun_new');
            
            if (dropdown.value) {
                // Jika pilih dari dropdown, kosongkan textfield
                textfield.value = '';
                textfield.placeholder = 'Atau ketik nama kebun lain...';
            }
        }
        
        // Delete data confirmation
        function deleteData(id, namaKebun) {
            document.getElementById('deleteId').textContent = id;
            document.getElementById('deleteKebunName').textContent = namaKebun;
            document.getElementById('modal_delete_id').value = id;
            document.getElementById('modalDelete').classList.add('active');
        }
        
        // Close modal
        function closeModal() {
            document.getElementById('modalDelete').classList.remove('active');
        }
        
        // Validate form
        function validateForm() {
            const dropdown = document.getElementById('nama_kebun');
            const textfield = document.getElementById('nama_kebun_new');
            const ip = document.getElementById('ip_cctv');
            
            const namaKebun = dropdown.value || textfield.value.trim();
            
            if (!namaKebun) {
                alert('Silakan pilih atau tulis nama kebun!');
                return false;
            }
            
            if (namaKebun.length < 2) {
                alert('Nama kebun minimal 2 karakter!');
                return false;
            }
            
            if (!ip.value || !ip.value.match(/^https?:\/\//i)) {
                alert('IP CCTV harus URL yang valid (mulai dengan http:// atau https://)');
                ip.focus();
                return false;
            }
            
            return true;
        }
        
        // Close modal when clicking outside
        document.getElementById('modalDelete').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
