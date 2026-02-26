<?php
/**
 * CCTV Dashboard - Environment Setup Helper
 * 
 * Yang dilakukan:
 * 1. Deteksi environment (development/production)
 * 2. Check file permissions
 * 3. Create .env file jika belum ada
 * 4. Verify database connectivity
 * 
 * Cara pakai:
 *   1. Upload file ini ke /var/www/html/cctv/setup.php
 *   2. Buka di browser: http://10.100.11.220/cctv/setup.php
 *   3. Follow instructions
 *   4. Delete file ini setelah selesai
 */

?>
<!DOCTYPE html>
<html>
<head>
    <title>CCTV Dashboard - Setup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { color: #333; }
        .section { margin: 25px 0; padding: 15px; border-left: 4px solid #007bff; background: #f8f9fa; }
        .status { margin: 10px 0; }
        .ok { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        input, textarea { width: 100%; padding: 8px; margin: 5px 0; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .code { background: #f4f4f4; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>

<div class="container">
    <h1>🔧 CCTV Dashboard - Setup Helper</h1>

    <?php
    $current_dir = __DIR__;
    $env_file = $current_dir . '/.env';
    $config_file = $current_dir . '/config.php';
    
    ?>

    <!-- Step 1: Environment Detection -->
    <div class="section">
        <h2>Step 1: Environment Detection</h2>
        <div class="status">
            <strong>Hostname:</strong> <?php echo gethostname(); ?><br>
            <strong>PHP Version:</strong> <?php echo phpversion(); ?><br>
            <strong>Current Directory:</strong> <?php echo $current_dir; ?><br>
            <strong>Web Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?><br>
        </div>
        
        <div class="status">
            <strong>APP_ENV Variable:</strong>
            <?php
            $app_env = getenv('APP_ENV');
            if ($app_env === false || $app_env === '') {
                echo '<span class="warning">NOT SET (will auto-detect)</span>';
            } else {
                echo '<span class="ok">' . htmlspecialchars($app_env) . '</span>';
            }
            ?>
        </div>
    </div>

    <!-- Step 2: Server Environment Setup -->
    <div class="section">
        <h2>Step 2: Configure Production Environment</h2>
        
        <?php if (file_exists($env_file)): ?>
            <p><span class="ok">✓ .env file already exists</span></p>
            <h3>Current .env content:</h3>
            <pre><?php echo htmlspecialchars(file_get_contents($env_file)); ?></pre>
            
            <h3>To update, edit the file directly or use form below:</h3>
        <?php else: ?>
            <p><span class="warning">⚠ .env file does NOT exist</span></p>
            <p>Use the form below to CREATE it:</p>
        <?php endif; ?>

        <h3>Create/Update .env File</h3>
        <form method="post">
            <textarea name="env_content" rows="10" placeholder="APP_ENV=production
DB_HOST=10.100.11.220
DB_USER=cctv_user
DB_PASS=cctv_N1HO
DB_NAME=cctv_db">
<?php 
if (file_exists($env_file)) {
    echo htmlspecialchars(file_get_contents($env_file));
} else {
    echo "APP_ENV=production\nDB_HOST=10.100.11.220\nDB_USER=cctv_user\nDB_PASS=cctv_N1HO\nDB_NAME=cctv_db\n";
}
?>
            </textarea>
            <button type="submit" name="save_env">Save .env File</button>
        </form>

        <?php
        if ($_POST['save_env'] ?? false) {
            $content = $_POST['env_content'] ?? '';
            if (file_put_contents($env_file, $content)) {
                echo '<p class="ok">✓ .env file saved successfully!</p>';
            } else {
                echo '<p class="error">✗ Failed to save .env file. Check permissions.</p>';
            }
        }
        ?>
    </div>

    <!-- Step 3: File Permissions -->
    <div class="section">
        <h2>Step 3: File Permissions Check</h2>
        
        <?php
        $files_to_check = [
            'config.php' => '644',
            'Database.php' => '644',
            'index.php' => '644',
            'unit.php' => '644',
            '.env' => '600',
        ];
        
        foreach ($files_to_check as $file => $recommended_perms) {
            $filepath = $current_dir . '/' . $file;
            if (file_exists($filepath)) {
                $perms = substr(sprintf('%o', fileperms($filepath)), -3);
                $readable = is_readable($filepath) ? '✓' : '✗';
                $writable = is_writable($filepath) ? '✓' : '✗';
                echo "📄 $file: $perms (Read: $readable Write: $writable)<br>";
            } else {
                echo "📄 $file: <span class=\"warning\">NOT FOUND</span><br>";
            }
        }
        ?>
    </div>

    <!-- Step 4: Database Connectivity Test -->
    <div class="section">
        <h2>Step 4: Database Connectivity Test</h2>
        
        <form method="post">
            <h3>Manual Connection Test:</h3>
            <label>Database Host:</label>
            <input type="text" name="db_host" value="<?php echo htmlspecialchars($_POST['db_host'] ?? '10.100.11.220'); ?>" placeholder="10.100.11.220">
            
            <label>Database User:</label>
            <input type="text" name="db_user" value="<?php echo htmlspecialchars($_POST['db_user'] ?? 'cctv_user'); ?>" placeholder="cctv_user">
            
            <label>Database Password:</label>
            <input type="password" name="db_pass" value="<?php echo htmlspecialchars($_POST['db_pass'] ?? 'cctv_N1HO'); ?>" placeholder="password">
            
            <label>Database Name:</label>
            <input type="text" name="db_name" value="<?php echo htmlspecialchars($_POST['db_name'] ?? 'cctv_db'); ?>" placeholder="cctv_db">
            
            <button type="submit" name="test_connection">Test Connection</button>
        </form>

        <?php
        if ($_POST['test_connection'] ?? false) {
            $db_host = $_POST['db_host'] ?? '';
            $db_user = $_POST['db_user'] ?? '';
            $db_pass = $_POST['db_pass'] ?? '';
            $db_name = $_POST['db_name'] ?? '';
            
            echo "<h3>Test Result:</h3>";
            
            $conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);
            
            if ($conn->connect_error) {
                echo '<p class="error">✗ Connection Failed</p>';
                echo '<p>Error: ' . htmlspecialchars($conn->connect_error) . '</p>';
            } else {
                echo '<p class="ok">✓ Connection Successful!</p>';
                
                // Test query
                $result = $conn->query("SELECT COUNT(*) as total FROM data_cctv;");
                if ($result) {
                    $row = $result->fetch_assoc();
                    echo '<p>Total cameras in database: ' . $row['total'] . '</p>';
                } else {
                    echo '<p class="warning">⚠ Query failed: ' . htmlspecialchars($conn->error) . '</p>';
                }
                
                $conn->close();
            }
        }
        ?>
    </div>

    <!-- Step 5: Verification -->
    <div class="section">
        <h2>Step 5: Final Verification</h2>
        
        <?php
        $checks = [
            'config.php exists' => file_exists($config_file),
            'Database.php exists' => file_exists($current_dir . '/Database.php'),
            '.env file exists' => file_exists($env_file),
            'config.php is readable' => is_readable($config_file),
            '.env is readable' => file_exists($env_file) && is_readable($env_file),
        ];
        
        $all_ok = true;
        foreach ($checks as $check => $result) {
            $status = $result ? '<span class="ok">✓</span>' : '<span class="error">✗</span>';
            echo "$status $check<br>";
            if (!$result) $all_ok = false;
        }
        ?>
        
        <h3>Next Steps:</h3>
        <ol>
            <li><?php echo file_exists($env_file) ? '✓ .env file is configured' : '✗ Create .env file above first'; ?></li>
            <li>Test database connection above</li>
            <li>Open: <code>http://10.100.11.220/cctv/index.php</code></li>
            <li>Delete this file: <code>rm setup.php</code> (for security)</li>
        </ol>
        
        <?php if ($all_ok): ?>
            <p><span class="ok"><strong>✓ All checks passed! Application should be ready.</strong></span></p>
        <?php else: ?>
            <p><span class="error"><strong>✗ Some checks failed. Fix issues above.</strong></span></p>
        <?php endif; ?>
    </div>

    <hr>
    <p style="text-align: center; color: #666; font-size: 12px;">
        <strong>Security Note:</strong> Delete this file after setup: <code>rm <?php echo $_SERVER['SCRIPT_FILENAME']; ?></code>
    </p>

</div>

</body>
</html>
