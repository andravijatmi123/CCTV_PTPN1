<?php
/**
 * Database Configuration
 * Support untuk development dan production environment
 */

// Detect environment
$env = getenv('APP_ENV') ?: 'development';

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
date_default_timezone_set($config['timezone']);

return $config;
?>
