<?php
/**
 * Database Configuration
 * Support untuk development dan production environment
 */

// Load .env file if exists
$env_file = __DIR__ . '/.env';
if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || !strpos($line, '=')) {
            continue;
        }
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, '\'"');
        putenv("$key=$value");
    }
}

// Detect environment
$env = getenv('APP_ENV');
if ($env === false || $env === '') {
    // Check if we're on production server based on hostname
    $hostname = gethostname();
    $env = (strpos($hostname, 'prod') !== false) ? 'production' : 'development';
}

if ($env === 'production') {
    // PRODUCTION SETTINGS
    $config = [
        'host'     => getenv('DB_HOST') ?: '10.100.11.220',
        'user'     => getenv('DB_USER') ?: 'cctv_user',
        'pass'     => getenv('DB_PASS') ?: 'TZdWyZUcsvtn18JTy626',
        'db'       => getenv('DB_NAME') ?: 'cctv_db',
        'charset'  => 'utf8mb4',
        'timezone' => 'Asia/Jakarta',
    ];
    
    // Production settings
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    error_reporting(E_ALL);
} else {
    // DEVELOPMENT SETTINGS (Laragon local)
    $config = [
        'host'     => 'localhost',
        'user'     => 'root',
        'pass'     => '',
        'db'       => 'cctv_db',
        'charset'  => 'utf8mb4',
        'timezone' => 'Asia/Jakarta',
    ];
    
    // Development settings
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    error_reporting(E_ALL);
}

// Set default timezone
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set($config['timezone']);
}

return $config;
?>
