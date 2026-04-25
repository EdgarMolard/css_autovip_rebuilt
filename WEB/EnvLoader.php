<?php
/**
 * Environment Variable Loader
 * Loads configuration from .env file
 */

class EnvLoader {
    
    private static $env_path = null;
    private static $env_vars = array();
    private static $loaded = false;
    
    /**
     * Load .env file
     * @param string $path Path to .env file
     */
    public static function load($path = '.env') {
        if (self::$loaded) {
            return true;
        }
        
        // Rechercher le fichier .env
        if (!file_exists($path)) {
            // Remonter d'un niveau si on est dans WEB/
            $path = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '.env';
            
            if (!file_exists($path)) {
                trigger_error('File .env not found. Please copy .env.example to .env', E_USER_WARNING);
                return false;
            }
        }
        
        self::$env_path = $path;
        return self::parse();
    }
    
    /**
     * Parse .env file
     */
    private static function parse() {
        $lines = file(self::$env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        if ($lines === false) {
            return false;
        }
        
        foreach ($lines as $line) {
            // Ignorer les commentaires
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Ignorer les lignes vides
            if (empty(trim($line))) {
                continue;
            }
            
            // Parser la ligne KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                
                $key = trim($key);
                $value = trim($value);
                
                // Supprimer les guillemets
                if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                    (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                    $value = substr($value, 1, -1);
                }
                
                self::$env_vars[$key] = $value;
                
                // Définir aussi comme variable d'environnement
                putenv($key . '=' . $value);
            }
        }
        
        self::$loaded = true;
        return true;
    }
    
    /**
     * Get environment variable
     * @param string $key Variable name
     * @param mixed $default Default value if not found
     * @return mixed Value or default
     */
    public static function get($key, $default = null) {
        if (isset(self::$env_vars[$key])) {
            return self::$env_vars[$key];
        }
        
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
    
    /**
     * Check if variable exists
     * @param string $key Variable name
     * @return bool
     */
    public static function has($key) {
        return isset(self::$env_vars[$key]) || getenv($key) !== false;
    }
    
    /**
     * Get all variables
     * @return array All loaded variables
     */
    public static function all() {
        return self::$env_vars;
    }
}
?>
