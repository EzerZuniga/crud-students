<?php
/**
 * Archivo de configuración de la aplicación
 * Define constantes y configuraciones globales
 */

// Definir constantes de rutas
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('VIEWS_PATH', APP_PATH . '/views');
define('LOGS_PATH', STORAGE_PATH . '/logs');

// Configuración de la aplicación
define('APP_NAME', env('APP_NAME', 'CRUD Students'));
define('APP_ENV', env('APP_ENV', 'development'));
define('APP_DEBUG', env('APP_DEBUG', true));
define('APP_URL', env('APP_URL', 'http://localhost:8000'));

// Configuración de zona horaria
define('APP_TIMEZONE', env('APP_TIMEZONE', 'America/Mexico_City'));
date_default_timezone_set(APP_TIMEZONE);

// Configuración de errores según el entorno
if (APP_ENV === 'production') {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', LOGS_PATH . '/php-errors.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// Configuración de sesión (si se necesita en el futuro)
define('SESSION_LIFETIME', 7200); // 2 horas

// Configuración de base de datos (se carga desde database.php)
// Estas constantes se pueden usar como respaldo
define('DB_DRIVER', env('DB_DRIVER', 'mysql'));
define('DB_HOST', env('DB_HOST', '127.0.0.1'));
define('DB_PORT', env('DB_PORT', '3306'));
define('DB_NAME', env('DB_NAME', 'crud_students'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASSWORD', env('DB_PASSWORD', ''));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

// Configuración de paginación (para futuras mejoras)
define('ITEMS_PER_PAGE', 10);

// Configuración de validación
define('MAX_NAME_LENGTH', 100);
define('MAX_EMAIL_LENGTH', 120);
define('MAX_PHONE_LENGTH', 50);

return [
    'app' => [
        'name' => APP_NAME,
        'env' => APP_ENV,
        'debug' => APP_DEBUG,
        'url' => APP_URL,
        'timezone' => APP_TIMEZONE,
    ],
    'database' => [
        'driver' => DB_DRIVER,
        'host' => DB_HOST,
        'port' => DB_PORT,
        'database' => DB_NAME,
        'username' => DB_USER,
        'password' => DB_PASSWORD,
        'charset' => DB_CHARSET,
    ],
    'paths' => [
        'root' => ROOT_PATH,
        'app' => APP_PATH,
        'config' => CONFIG_PATH,
        'public' => PUBLIC_PATH,
        'storage' => STORAGE_PATH,
        'views' => VIEWS_PATH,
        'logs' => LOGS_PATH,
    ],
];
