<?php
/**
 * CRUD Page untuk Manajemen Nama Kebun
 * - List semua kebun
 * - Tambah kebun baru
 * - Edit kebun
 * - Hapus kebun
 * - Dropdown untuk memilih kebun yang sudah ada
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
    
    // Handle Form Submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        // ADD - Kebun Baru
        if ($action === 'add') {
            $nama_kebun = trim($_POST['nama_kebun'] ?? '');
            
            if (empty($nama_kebun)) {
                $message = "Nama kebun tidak boleh kosong!";
                $message_type = 'error';
            } else {
                // Check if kebun already exists
                $check = $db->getRow(
                    "SELECT id FROM data_cctv WHERE nama_kebun = ?",
                    "s",
                    [$nama_kebun]
                );
                
                if ($check) {
                    $message = "Kebun '{$nama_kebun}' sudah ada!";
                    $message_type = 'error';
                } else {
                    // Insert kebun baru (minimal satu data untuk create tabel kebun)
                    $db->query(
                        "INSERT INTO data_cctv (nama_kebun, ip_cctv) VALUES (?, ?)",
                        "ss",
                        [$nama_kebun, '']
                    );
                    
                    $message = "Kebun '{$nama_kebun}' berhasil ditambahkan!";
                    $message_type = 'success';
                }
            }
        }
        
        // EDIT - Update nama kebun
        if ($action === 'edit') {
            $kebun_lama = trim($_POST['kebun_lama'] ?? '');
            $kebun_baru = trim($_POST['kebun_baru'] ?? '');
            
            if (empty($kebun_lama) || empty($kebun_baru)) {
                $message = "Nama kebun tidak boleh kosong!";
                $message_type = 'error';
            } else if ($kebun_lama === $kebun_baru) {
                $message = "Nama kebun sama dengan sebelumnya!";
                $message_type = 'error';
            } else {
                // Check if new kebun name already exists
                $check = $db->getRow(
                    "SELECT id FROM data_cctv WHERE nama_kebun = ? AND nama_kebun != ?",
                    "ss",
                    [$kebun_baru, $kebun_lama]
                );
                
                if ($check) {
                    $message = "Kebun '{$kebun_baru}' sudah ada!";
                    $message_type = 'error';
                } else {
                    // Update all data_cctv with old name
                    $db->query(
                        "UPDATE data_cctv SET nama_kebun = ? WHERE nama_kebun = ?",
                        "ss",
                        [$kebun_baru, $kebun_lama]
                    );
                    
                    $message = "Kebun berhasil diubah dari '{$kebun_lama}' menjadi '{$kebun_baru}'!";
                    $message_type = 'success';
                }
            }
        }
        
        // DELETE - Hapus kebun
        if ($action === 'delete') {
            $nama_kebun = trim($_POST['nama_kebun'] ?? '');
            
            if (empty($nama_kebun)) {
                $message = "Nama kebun tidak valid!";
                $message_type = 'error';
            } else {
                // Delete all data_cctv with this kebun name
                $db->query(
                    "DELETE FROM data_cctv WHERE nama_kebun = ?",
                    "s",
                    [$nama_kebun]
                );
                
                $message = "Kebun '{$nama_kebun}' dan semua data CCTV-nya telah dihapus!";
                $message_type = 'success';
            }
        }
    }
    
    // Get all unique kebun names
    $semua_kebun = $db->getAll(
        "SELECT DISTINCT nama_kebun FROM data_cctv ORDER BY nama_kebun ASC"
    );
    
    // Get kebun dengan statistik (jumlah kamera)
    $kebun_stats = $db->getAll(
        "SELECT nama_kebun, COUNT(*) as jumlah_kamera, 
                GROUP_CONCAT(DISTINCT ip_cctv) as ip_list
         FROM data_cctv 
         GROUP BY nama_kebun 
         ORDER BY nama_kebun ASC"
    );
    
} catch (Exception $e) {
    error_log("[KEBUN ERROR] " . $e->getMessage());
    $kebun_stats = [];
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
    <title>Manajemen Kebun - CCTV Dashboard</title>
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
            max-width: 1200px;
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
        }
        
        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
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
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
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
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .badge.warning {
            background: #ff9800;
        }
        
        .section-title {
            font-size: 16px;
            color: #667eea;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .info-text {
            font-size: 12px;
            color: #999;
            margin-top: 8px;
            font-style: italic;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🌱 Manajemen Kebun</h1>
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
                <h2>Tambah / Edit Kebun</h2>
                
                <!-- Tab untuk Add/Edit -->
                <div style="margin-bottom: 20px; display: flex; gap: 10px;">
                    <button onclick="switchTab('add')" id="btn-add" style="background: #667eea;">➕ Tambah Baru</button>
                    <button onclick="switchTab('edit')" id="btn-edit" style="background: #6c757d;">✏️ Edit</button>
                </div>
                
                <!-- Tab 1: Tambah Kebun Baru -->
                <div id="tab-add" class="tab-content">
                    <form method="POST" onsubmit="return validateAddForm()">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="form-group">
                            <label for="nama_kebun_baru">Nama Kebun Baru:</label>
                            <input type="text" id="nama_kebun_baru" name="nama_kebun" 
                                   placeholder="Misal: Kebun Baru, Kebun Utama..." required>
                            <p class="info-text">📝 Tulis nama kebun baru di sini</p>
                        </div>
                        
                        <div class="form-group">
                            <p class="section-title">atau Pilih dari yang Sudah Ada:</p>
                            <select id="select_existing" onchange="fillFromExisting()">
                                <option value="">-- Pilih Kebun --</option>
                                <?php foreach ($semua_kebun as $kebun): ?>
                                <option value="<?php echo htmlspecialchars($kebun['nama_kebun']); ?>">
                                    <?php echo htmlspecialchars($kebun['nama_kebun']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="info-text">💡 Pilih ini jika ingin duplikasi dari kebun yang sudah ada</p>
                        </div>
                        
                        <button type="submit">✓ Tambah Kebun</button>
                    </form>
                </div>
                
                <!-- Tab 2: Edit Kebun -->
                <div id="tab-edit" class="tab-content" style="display: none;">
                    <form method="POST" onsubmit="return validateEditForm()">
                        <input type="hidden" name="action" value="edit">
                        
                        <div class="form-group">
                            <label for="kebun_lama">Pilih Kebun yang Akan Diedit:</label>
                            <select id="kebun_lama" name="kebun_lama" required onchange="setSelectedKebun()">
                                <option value="">-- Pilih Kebun --</option>
                                <?php foreach ($semua_kebun as $kebun): ?>
                                <option value="<?php echo htmlspecialchars($kebun['nama_kebun']); ?>">
                                    <?php echo htmlspecialchars($kebun['nama_kebun']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="kebun_baru">Nama Kebun Baru:</label>
                            <input type="text" id="kebun_baru" name="kebun_baru" 
                                   placeholder="Masukkan nama baru..." required>
                        </div>
                        
                        <button type="submit" class="btn-edit">✓ Simpan Perubahan</button>
                    </form>
                </div>
            </div>
            
            <!-- List Section -->
            <div class="card">
                <h2>📋 Daftar Kebun</h2>
                
                <?php if (empty($kebun_stats)): ?>
                <p style="color: #999; text-align: center; padding: 20px;">
                    Belum ada kebun. Silakan tambah kebun baru di sebelah kiri.
                </p>
                <?php else: ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Kebun</th>
                                <th>Jumlah Kamera</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kebun_stats as $item): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['nama_kebun']); ?></strong>
                                </td>
                                <td>
                                    <span class="badge">
                                        📷 <?php echo $item['jumlah_kamera']; ?> Kamera
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-sm btn-edit" onclick="editKebun('<?php echo htmlspecialchars($item['nama_kebun']); ?>')">
                                            ✏️ Edit
                                        </button>
                                        <button class="btn-sm btn-delete" onclick="deleteKebun('<?php echo htmlspecialchars($item['nama_kebun']); ?>')">
                                            🗑️ Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Modal Edit Kebun -->
    <div id="modalEdit" class="modal">
        <div class="modal-content">
            <h3>Edit Kebun</h3>
            <form method="POST" onsubmit="return validateEditForm()">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="modal_kebun_lama" name="kebun_lama">
                
                <div class="form-group">
                    <label for="modal_kebun_baru">Nama Baru:</label>
                    <input type="text" id="modal_kebun_baru" name="kebun_baru" required>
                </div>
                
                <div class="modal-buttons">
                    <button type="submit" class="btn-edit">✓ Simpan</button>
                    <button type="button" class="secondary" onclick="closeModals()">✕ Batal</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal Hapus Kebun -->
    <div id="modalDelete" class="modal">
        <div class="modal-content">
            <h3>⚠️ Konfirmasi Penghapusan</h3>
            <p style="color: #666; margin-bottom: 15px;">
                Apakah Anda yakin ingin menghapus kebun <strong id="deleteKebunName"></strong>?
                <br><br>
                <span style="color: #d32f2f; font-weight: 500;">
                    ⚠️ Semua data CCTV untuk kebun ini akan ikut dihapus!
                </span>
            </p>
            
            <form method="POST" onsubmit="return true;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" id="modal_delete_kebun" name="nama_kebun">
                
                <div class="modal-buttons">
                    <button type="submit" class="danger">🗑️ Hapus</button>
                    <button type="button" class="secondary" onclick="closeModals()">✕ Batal</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Switch between tabs
        function switchTab(tab) {
            const addTab = document.getElementById('tab-add');
            const editTab = document.getElementById('tab-edit');
            const btnAdd = document.getElementById('btn-add');
            const btnEdit = document.getElementById('btn-edit');
            
            if (tab === 'add') {
                addTab.style.display = 'block';
                editTab.style.display = 'none';
                btnAdd.style.background = '#667eea';
                btnEdit.style.background = '#6c757d';
            } else {
                addTab.style.display = 'none';
                editTab.style.display = 'block';
                btnAdd.style.background = '#6c757d';
                btnEdit.style.background = '#667eea';
            }
        }
        
        // Fill form from existing kebun
        function fillFromExisting() {
            const select = document.getElementById('select_existing');
            const input = document.getElementById('nama_kebun_baru');
            
            if (select.value) {
                input.value = select.value;
                input.focus();
            }
        }
        
        // Set selected kebun in edit form
        function setSelectedKebun() {
            const select = document.getElementById('kebun_lama');
            const input = document.getElementById('kebun_baru');
            
            if (select.value) {
                input.value = select.value;
            }
        }
        
        // Edit kebun
        function editKebun(namaKebun) {
            document.getElementById('modal_kebun_lama').value = namaKebun;
            document.getElementById('modal_kebun_baru').value = namaKebun;
            document.getElementById('modalEdit').classList.add('active');
            document.getElementById('modal_kebun_baru').focus();
            document.getElementById('modal_kebun_baru').select();
        }
        
        // Delete kebun
        function deleteKebun(namaKebun) {
            document.getElementById('deleteKebunName').textContent = namaKebun;
            document.getElementById('modal_delete_kebun').value = namaKebun;
            document.getElementById('modalDelete').classList.add('active');
        }
        
        // Close modals
        function closeModals() {
            document.getElementById('modalEdit').classList.remove('active');
            document.getElementById('modalDelete').classList.remove('active');
        }
        
        // Validate add form
        function validateAddForm() {
            const input = document.getElementById('nama_kebun_baru').value.trim();
            
            if (!input) {
                alert('Silakan masukkan nama kebun!');
                return false;
            }
            
            if (input.length < 2) {
                alert('Nama kebun minimal 2 karakter!');
                return false;
            }
            
            if (input.length > 255) {
                alert('Nama kebun maksimal 255 karakter!');
                return false;
            }
            
            return confirm('Tambah kebun: ' + input + '?');
        }
        
        // Validate edit form
        function validateEditForm() {
            const selects = document.querySelectorAll('select[name="kebun_lama"]');
            const oldName = selects.length > 0 ? selects[0].value : document.getElementById('modal_kebun_lama').value;
            const newName = document.getElementById('modal_kebun_baru')?.value || document.getElementById('kebun_baru').value;
            
            if (!oldName || !newName) {
                alert('Silakan pilih kebun dan masukkan nama baru!');
                return false;
            }
            
            if (newName.length < 2) {
                alert('Nama kebun minimal 2 karakter!');
                return false;
            }
            
            if (newName.length > 255) {
                alert('Nama kebun maksimal 255 karakter!');
                return false;
            }
            
            if (oldName === newName) {
                alert('Nama kebun sama dengan sebelumnya!');
                return false;
            }
            
            return confirm('Ubah nama kebun dari "' + oldName + '" menjadi "' + newName + '"?');
        }
        
        // Close modal when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModals();
                }
            });
        });
    </script>
</body>
</html>
