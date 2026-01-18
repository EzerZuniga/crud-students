<?php
/**
 * Funciones helper globales
 * Este archivo contiene funciones útiles reutilizables en toda la aplicación
 */

/**
 * Registra un mensaje en el log de la aplicación
 *
 * @param string $message Mensaje a registrar
 * @param string $level Nivel de log (info, warning, error)
 * @return void
 */
function app_log(string $message, string $level = 'info'): void
{
    $logDir = __DIR__ . '/../../storage/logs';
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }
    
    $file = $logDir . '/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $levelUpper = strtoupper($level);
    
    file_put_contents(
        $file,
        "[{$timestamp}] [{$levelUpper}] {$message}" . PHP_EOL,
        FILE_APPEND
    );
}

/**
 * Escapa HTML para prevenir XSS
 *
 * @param mixed $value Valor a escapar
 * @return string
 */
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Obtiene un valor de un array con valor por defecto
 *
 * @param array $array Array fuente
 * @param string $key Clave a buscar
 * @param mixed $default Valor por defecto
 * @return mixed
 */
function array_get(array $array, string $key, $default = null)
{
    return $array[$key] ?? $default;
}

/**
 * Verifica si una cadena comienza con otra
 *
 * @param string $haystack Cadena completa
 * @param string $needle Cadena a buscar
 * @return bool
 */
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}

/**
 * Verifica si una cadena termina con otra
 *
 * @param string $haystack Cadena completa
 * @param string $needle Cadena a buscar
 * @return bool
 */
if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        return $length === 0 || substr($haystack, -$length) === $needle;
    }
}

/**
 * Formatea una fecha de manera legible
 *
 * @param string $date Fecha a formatear
 * @param string $format Formato deseado
 * @return string
 */
function format_date(string $date, string $format = 'd/m/Y H:i'): string
{
    return date($format, strtotime($date));
}

/**
 * Genera una URL para la aplicación
 *
 * @param string $path Ruta relativa
 * @param array $params Parámetros query
 * @return string
 */
function url(string $path = '', array $params = []): string
{
    $base = '/';
    $url = $base . ltrim($path, '/');
    
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    
    return $url;
}

/**
 * Genera una URL de acción para el CRUD
 *
 * @param string $action Acción a ejecutar
 * @param int|null $id ID del recurso (opcional)
 * @return string
 */
function action_url(string $action, ?int $id = null): string
{
    $params = ['action' => $action];
    
    if ($id !== null) {
        $params['id'] = $id;
    }
    
    return url('', $params);
}

/**
 * Obtiene el valor de una variable de entorno
 *
 * @param string $key Clave de la variable
 * @param mixed $default Valor por defecto
 * @return mixed
 */
function env(string $key, $default = null)
{
    $value = getenv($key);
    
    if ($value === false) {
        return $default;
    }
    
    // Convertir valores especiales
    return match(strtolower($value)) {
        'true', '(true)' => true,
        'false', '(false)' => false,
        'null', '(null)' => null,
        'empty', '(empty)' => '',
        default => $value
    };
}

/**
 * Formatea un número de teléfono
 *
 * @param string $phone Número de teléfono
 * @return string
 */
function format_phone(string $phone): string
{
    // Eliminar caracteres no numéricos excepto +
    $cleaned = preg_replace('/[^0-9+]/', '', $phone);
    
    return $cleaned;
}

/**
 * Redirecciona a una URL específica
 *
 * @param string $url URL de destino
 * @param int $statusCode Código de estado HTTP
 * @return never
 */
function redirect_to(string $url, int $statusCode = 302): never
{
    http_response_code($statusCode);
    header('Location: ' . $url);
    exit;
}

/**
 * Obtiene la URL actual
 *
 * @return string
 */
function current_url(): string
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    
    return $protocol . '://' . $host . $uri;
}

/**
 * Verifica si la petición es AJAX
 *
 * @return bool
 */
function is_ajax(): bool
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Convierte un array a JSON
 *
 * @param mixed $data Datos a convertir
 * @param int $options Opciones de json_encode
 * @return string
 */
function to_json($data, int $options = JSON_UNESCAPED_UNICODE): string
{
    return json_encode($data, $options);
}

/**
 * Envía una respuesta JSON
 *
 * @param mixed $data Datos a enviar
 * @param int $statusCode Código de estado HTTP
 * @return never
 */
function json_response($data, int $statusCode = 200): never
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo to_json($data);
    exit;
}
