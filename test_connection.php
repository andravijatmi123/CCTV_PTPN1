<?php
/**
 * Database Connection Test
 * Debug tool untuk production issues
 * 
 * Buka: http://10.100.11.220/cctv/test_connection.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h2>🔧 CCTV Database Connection Test</h2>";
echo "<pre>";

// Check PHP version
echo "PHP Version: " . phpversion() . "\n";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "\n\n";

// Load config
echo "=== CONFIG DETECTION ===\n";
$env = getenv('APP_ENV') ?: 'development';
echo "Environment Mode: $env\n";

try {
    $config = require 'config.php';
    
    echo "Database Host: " . $config['host'] . "\n";
    echo "Database User: " . $config['user'] . "\n";
    echo "Database Name: " . $config['db'] . "\n";
    echo "Charset: " . $config['charset'] . "\n";
    echo "Timezone: " . $config['timezone'] . "\n\n";
    
    // Test connection
    echo "=== CONNECTION TEST ===\n";
    $conn = new mysqli(
        $config['host'],
        $config['user'],
        $config['pass'],
        $config['db']
    );
    
    if ($conn->connect_error) {
        echo "❌ CONNECTION FAILED\n";
        echo "Error Code: " . $conn->connect_errno . "\n";
        echo "Error Message: " . $conn->connect_error . "\n\n";
        
        echo "=== DIAGNOSTICS ===\n";
        
        // Test individual components
        echo "1. Testing host connectivity...\n";
        if (filter_var($config['host'], FILTER_VALIDATE_IP)) {
            if (@fsockopen($config['host'], 3306, $errno, $errstr, 2)) {
                echo "   ✅ Port 3306 reachable\n";
            } else {
                echo "   ❌ Port 3306 not reachable\n";
                echo "   Error: $errstr ($errno)\n";
            }
        } else {
            echo "   ✅ Hostname: " . $config['host'] . "\n";
        }
        
        echo "\n2. Checking credentials...\n";
        echo "   User: " . $config['user'] . "\n";
        echo "   Pass: " . (empty($config['pass']) ? "(empty)" : str_repeat("*", strlen($config['pass']))) . "\n";
        echo "   Database: " . $config['db'] . "\n";
        
        echo "\n3. MySQL Server Status...\n";
        // Try generic localhost connection
        $test = @new mysqli('localhost', 'root', '', 'mysql');
        if (!$test->connect_error) {
            echo "   ✅ MySQL appears to be running locally\n";
            $test->close();
        } else {
            echo "   ⚠️  Cannot connect to localhost MySQL\n";
        }
        
    } else {
        echo "✅ CONNECTION SUCCESSFUL\n";
        echo "Charset: " . $conn->character_set_name() . "\n\n";
        
        // Test Database.php class
        echo "=== TESTING Database CLASS ===\n";
        require 'Database.php';
        
        try {
            $db = new Database($config);
            echo "✅ Database class instantiated\n\n";
            
            // Test query
            echo "=== TESTING QUERIES ===\n";
            $result = $db->query("SELECT DATABASE() as db;");
            $row = $result->fetch_assoc();
            echo "Current Database: " . $row['db'] . "\n\n";
            
            // Test actual data
            echo "=== DATA TEST ===\n";
            $result = $db->query("SELECT COUNT(*) as total FROM data_cctv;");
            $row = $result->fetch_assoc();
            echo "Total cameras in database: " . $row['total'] . "\n\n";
            
            // Show table structure
            echo "=== TABLE STRUCTURE ===\n";
            $result = $db->query("DESCRIBE data_cctv;");
            while ($row = $result->fetch_assoc()) {
                echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
            }
            echo "\n";
            
            // Show sample data
            echo "=== SAMPLE DATA ===\n";
            $result = $db->query("SELECT * FROM data_cctv LIMIT 5;");
            while ($row = $result->fetch_assoc()) {
                echo "ID: {$row['id']}, Unit: {$row['nama_kebun']}, IP: {$row['ip_cctv']}\n";
            }
            echo "\n";
            
            // Test grouping query
            echo "=== GROUPING QUERY (Used in Dashboard) ===\n";
            $sql = "SELECT nama_kebun, COUNT(*) as jumlah_kamera FROM data_cctv GROUP BY nama_kebun ORDER BY nama_kebun ASC";
            $result = $db->query($sql);
            echo "Query Result:\n";
            while ($row = $result->fetch_assoc()) {
                echo "- " . $row['nama_kebun'] . ": " . $row['jumlah_kamera'] . " camera(s)\n";
            }
            echo "\n";
            
            echo "✅ ALL TESTS PASSED\n";
            
        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
        }
        
        $conn->close();
    }
    
} catch (Exception $e) {
    echo "❌ CONFIG ERROR: " . $e->getMessage() . "\n";
}

echo "</pre>";
echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>Fix connection issues noted above</li>";
echo "<li>Check PHP error log: /var/log/cctv/php_error.log</li>";
echo "<li>Check MySQL error log</li>";
echo "<li>Verify environment variables are set correctly</li>";
echo "</ul>";
?>
