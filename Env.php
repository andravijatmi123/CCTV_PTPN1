<?php
/**
 * Environment Loader Helper
 * Loads environment variables from .env file
 */

class Env {
    private static $env = [];
    
    /**
     * Load variables from .env file
     */
    public static function load($path = '.env') {
        if (!file_exists($path)) {
            return;
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parse line
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes
                if (in_array($value[0] ?? null, ['"', "'"])) {
                    $value = substr($value, 1, -1);
                }
                
                self::$env[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
    
    /**
     * Get environment variable
     */
    public static function get($key, $default = null) {
        return self::$env[$key] ?? getenv($key) ?: $default;
    }
}

// Auto-load .env file if exists
if (file_exists(__DIR__ . '/.env')) {
    Env::load(__DIR__ . '/.env');
}
?>
