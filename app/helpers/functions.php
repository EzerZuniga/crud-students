<?php
/**




































































<body class="auth-page"></head>    </style>        }            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);            transform: translateY(-2px);            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);        .btn-primary:hover {                }            font-weight: 600;            padding: 0.75rem;            border: none;            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);        .btn-primary {                }            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);            border-color: #667eea;        .form-control:focus {                }            padding: 2rem;        .auth-body {                }            margin: 0;            font-weight: bold;            font-size: 1.8rem;        .auth-header h1 {                }            text-align: center;            padding: 2rem;            color: white;            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);        .auth-header {                }            width: 100%;            max-width: 450px;            overflow: hidden;            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);            border-radius: 15px;            background: white;        .auth-card {                }            justify-content: center;            align-items: center;            display: flex;            min-height: 100vh;            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);        body.auth-page {    <style>        <link rel="stylesheet" href="<?= asset('css/style.css') ?>">    <!-- Custom CSS -->    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">    <!-- Bootstrap Icons -->    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">    <!-- Bootstrap CSS -->        <title><?= e($title ?? 'Sistema de Estudiantes') ?></title>    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <meta charset="UTF-8"><head> * Funciones helper globales
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
    $base = rtrim(APP_URL ?? '', '/');
    $url = $base . '/' . ltrim($path, '/');
    
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    
    return $url;
}

/**
 * Genera una URL de ruta nombrada
 *
 * @param string $route Nombre de la ruta
 * @param array $params Parámetros para la ruta
 * @return string
 */
function route(string $route, array $params = []): string
{
    $routes = [
        'students.index' => '/',
        'students.show' => '/students/{id}',
        'students.create' => '/students/create',
        'students.store' => '/students',
        'students.edit' => '/students/{id}/edit',
        'students.update' => '/students/{id}',
        'students.delete' => '/students/{id}/delete',
        'auth.login' => '/login',
        'auth.login.post' => '/login',
        'auth.register' => '/register',
        'auth.register.post' => '/register',
        'auth.logout' => '/logout',
    ];

    if (!isset($routes[$route])) {
        return '/';
    }

    $path = $routes[$route];

    // Reemplazar parámetros en la ruta
    foreach ($params as $key => $value) {
        $path = str_replace('{' . $key . '}', $value, $path);
    }

    return url($path);
}

/**
 * Genera una URL de asset (CSS, JS, imágenes)
 *
 * @param string $path Ruta del asset
 * @return string
 */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
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

/**
 * Genera el campo CSRF para formularios
 *
 * @return string HTML del campo oculto
 */
function csrf_field(): string
{
    return \App\Core\CSRF::field();
}

/**
 * Obtiene el token CSRF actual
 *
 * @return string Token CSRF
 */
function csrf_token(): string
{
    return \App\Core\CSRF::getToken();
}

/**
 * Valida el token CSRF de la petición
 *
 * @return bool True si el token es válido
 */
function csrf_validate(): bool
{
    return \App\Core\CSRF::validate();
}

/**
 * Sanitiza una cadena eliminando espacios y caracteres especiales
 *
 * @param string $value Valor a sanitizar
 * @return string
 */
function sanitize_string(string $value): string
{
    return trim(strip_tags($value));
}

/**
 * Sanitiza un email
 *
 * @param string $email Email a sanitizar
 * @return string
 */
function sanitize_email(string $email): string
{
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

/**
 * Muestra un mensaje flash de sesión
 *
 * @param string $key Clave del mensaje
 * @param string|null $message Mensaje a guardar
 * @return string|null
 */
function flash(string $key, ?string $message = null): ?string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);

    return $value;
}

/**
 * Obtiene o establece un valor antiguo del formulario
 *
 * @param string $key Clave del valor
 * @param mixed $default Valor por defecto
 * @return mixed
 */
function old(string $key, $default = '')
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return $_SESSION['old'][$key] ?? $default;
}

/**
 * Guarda los valores antiguos del formulario en la sesión
 *
 * @param array $data Datos a guardar
 * @return void
 */
function set_old(array $data): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['old'] = $data;
}

/**
 * Limpia los valores antiguos de la sesión
 *
 * @return void
 */
function clear_old(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    unset($_SESSION['old']);
}

/**
 * ==================================
 * FUNCIONES DE AUTENTICACIÓN
 * ==================================
 */

/**
 * Verifica si hay un usuario autenticado
 *
 * @return bool
 */
function auth_check(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return isset($_SESSION['auth_user']);
}

/**
 * Obtiene el usuario autenticado actual
 *
 * @return array|null
 */
function auth_user(): ?array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return $_SESSION['auth_user'] ?? null;
}

/**
 * Obtiene el ID del usuario autenticado
 *
 * @return int|null
 */
function auth_id(): ?int
{
    $user = auth_user();
    return $user['id'] ?? null;
}

/**
 * Inicia sesión de un usuario
 *
 * @param array $user Datos del usuario
 * @return void
 */
function auth_login(array $user): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['auth_user'] = $user;
    
    // Regenerar ID de sesión por seguridad
    session_regenerate_id(true);
}

/**
 * Cierra la sesión del usuario
 *
 * @return void
 */
function auth_logout(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    unset($_SESSION['auth_user']);
    session_destroy();
}

/**
 * Verifica si el usuario tiene un rol específico
 *
 * @param string $role Nombre del rol
 * @return bool
 */
function auth_has_role(string $role): bool
{
    if (!auth_check()) {
        return false;
    }

    $user = auth_user();
    $roles = $user['roles'] ?? [];

    foreach ($roles as $userRole) {
        if (is_array($userRole) && isset($userRole['name']) && $userRole['name'] === $role) {
            return true;
        }
        if (is_string($userRole) && $userRole === $role) {
            return true;
        }
    }

    return false;
}

/**
 * Verifica si el usuario es administrador
 *
 * @return bool
 */
function auth_is_admin(): bool
{
    return auth_has_role('admin');
}

/**
 * Obtiene el nombre completo del usuario autenticado
 *
 * @return string
 */
function auth_name(): string
{
    $user = auth_user();
    return $user['full_name'] ?? 'Usuario';
}

/**
 * ==================================
 * FUNCIONES DE PERMISOS
 * ==================================
 */

/**
 * Verifica si el usuario autenticado tiene un permiso específico
 *
 * @param string $permission Nombre del permiso (ej: 'students.create')
 * @return bool
 */
function can(string $permission): bool
{
    if (!auth_check()) {
        return false;
    }

    $user = auth_user();
    $permissions = $user['permissions'] ?? [];

    foreach ($permissions as $perm) {
        if (is_array($perm) && isset($perm['name']) && $perm['name'] === $permission) {
            return true;
        }
        if (is_string($perm) && $perm === $permission) {
            return true;
        }
    }

    return false;
}

/**
 * Verifica si el usuario NO tiene un permiso específico
 *
 * @param string $permission
 * @return bool
 */
function cannot(string $permission): bool
{
    return !can($permission);
}

/**
 * Verifica si el usuario tiene alguno de los permisos especificados
 *
 * @param array $permissions Array de nombres de permisos
 * @return bool
 */
function can_any(array $permissions): bool
{
    foreach ($permissions as $permission) {
        if (can($permission)) {
            return true;
        }
    }
    return false;
}

/**
 * Verifica si el usuario tiene todos los permisos especificados
 *
 * @param array $permissions Array de nombres de permisos
 * @return bool
 */
function can_all(array $permissions): bool
{
    foreach ($permissions as $permission) {
        if (!can($permission)) {
            return false;
        }
    }
    return true;
}
