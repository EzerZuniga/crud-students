<?php

// Constantes específicas de helpers (las demás están en config/constants.php)
const DEFAULT_USER_NAME = 'Usuario';

const HEADER_X_REQUESTED_WITH = 'HTTP_X_REQUESTED_WITH';
const HEADER_AJAX_VALUE = 'xmlhttprequest';
const CONTENT_TYPE_JSON = 'Content-Type: application/json; charset=utf-8';

const ENV_TRUE_VALUES = ['true', '(true)'];
const ENV_FALSE_VALUES = ['false', '(false)'];
const ENV_NULL_VALUES = ['null', '(null)'];
const ENV_EMPTY_VALUES = ['empty', '(empty)'];

function app_log(string $message, string $level = LOG_LEVEL_INFO): void
{
    $logDir = __DIR__ . '/../../storage/logs';
    
    if (!is_dir($logDir)) {
        mkdir($logDir, LOG_DIR_PERMISSIONS, true);
    }
    
    $file = $logDir . '/' . LOG_FILENAME;
    $timestamp = date(LOG_TIMESTAMP_FORMAT);
    $levelUpper = strtoupper($level);
    
    file_put_contents(
        $file,
        "[{$timestamp}] [{$levelUpper}] {$message}" . PHP_EOL,
        FILE_APPEND
    );
}

function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function array_get(array $array, string $key, mixed $default = null): mixed
{
    return $array[$key] ?? $default;
}

if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}

if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        return $length === 0 || substr($haystack, -$length) === $needle;
    }
}

function format_date(string $date, string $format = DATE_FORMAT_DEFAULT): string
{
    return date($format, strtotime($date));
}

function url(string $path = '', array $params = []): string
{
    $base = rtrim(APP_URL ?? '', '/');
    $url = $base . '/' . ltrim($path, '/');
    
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    
    return $url;
}

function route(string $route, array $params = []): string
{
    $routes = getNamedRoutes();

    if (!isset($routes[$route])) {
        return '/';
    }

    $path = replaceRouteParams($routes[$route], $params);

    return url($path);
}

function getNamedRoutes(): array
{
    return [
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
}

function replaceRouteParams(string $path, array $params): string
{
    foreach ($params as $key => $value) {
        $path = str_replace('{' . $key . '}', $value, $path);
    }
    
    return $path;
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function env(string $key, mixed $default = null): mixed
{
    $value = getenv($key);
    
    if ($value === false) {
        return $default;
    }
    
    return parseEnvValue($value);
}

function parseEnvValue(string $value): mixed
{
    $lower = strtolower($value);
    
    return match (true) {
        in_array($lower, ENV_TRUE_VALUES) => true,
        in_array($lower, ENV_FALSE_VALUES) => false,
        in_array($lower, ENV_NULL_VALUES) => null,
        in_array($lower, ENV_EMPTY_VALUES) => '',
        default => $value
    };
}

function format_phone(string $phone): string
{
    return preg_replace('/[^0-9+]/', '', $phone);
}

function redirect_to(string $url, int $statusCode = HTTP_STATUS_REDIRECT): never
{
    http_response_code($statusCode);
    header('Location: ' . $url);
    exit;
}

function current_url(): string
{
    $protocol = getProtocol();
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    
    return $protocol . '://' . $host . $uri;
}

function getProtocol(): string
{
    return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === HTTPS_ON 
        ? PROTOCOL_HTTPS 
        : PROTOCOL_HTTP;
}

function is_ajax(): bool
{
    return !empty($_SERVER[HEADER_X_REQUESTED_WITH]) &&
           strtolower($_SERVER[HEADER_X_REQUESTED_WITH]) === HEADER_AJAX_VALUE;
}

function to_json(mixed $data, int $options = JSON_UNESCAPED_UNICODE): string
{
    return json_encode($data, $options);
}

function json_response(mixed $data, int $statusCode = HTTP_STATUS_OK): never
{
    http_response_code($statusCode);
    header(CONTENT_TYPE_JSON);
    echo to_json($data);
    exit;
}

function csrf_field(): string
{
    return \App\Core\CSRF::field();
}

function csrf_token(): string
{
    return \App\Core\CSRF::getToken();
}

function csrf_validate(): bool
{
    return \App\Core\CSRF::validate();
}

function sanitize_string(string $value): string
{
    return trim(strip_tags($value));
}

function sanitize_email(string $email): string
{
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

function flash(string $key, ?string $message = null): ?string
{
    ensureSessionStarted();

    if ($message !== null) {
        $_SESSION[SESSION_KEY_FLASH][$key] = $message;
        return null;
    }

    $value = $_SESSION[SESSION_KEY_FLASH][$key] ?? null;
    unset($_SESSION[SESSION_KEY_FLASH][$key]);

    return $value;
}

function old(string $key, mixed $default = ''): mixed
{
    ensureSessionStarted();
    return $_SESSION[SESSION_KEY_OLD][$key] ?? $default;
}

function set_old(array $data): void
{
    ensureSessionStarted();
    $_SESSION[SESSION_KEY_OLD] = $data;
}

function clear_old(): void
{
    ensureSessionStarted();
    unset($_SESSION[SESSION_KEY_OLD]);
}

function ensureSessionStarted(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function auth_check(): bool
{
    ensureSessionStarted();
    return isset($_SESSION[SESSION_KEY_AUTH_USER]);
}

function auth_user(): ?array
{
    ensureSessionStarted();
    return $_SESSION[SESSION_KEY_AUTH_USER] ?? null;
}

function auth_id(): ?int
{
    $user = auth_user();
    return $user['id'] ?? null;
}

function auth_login(array $user): void
{
    ensureSessionStarted();
    $_SESSION[SESSION_KEY_AUTH_USER] = $user;
    session_regenerate_id(true);
}

function auth_logout(): void
{
    ensureSessionStarted();
    unset($_SESSION[SESSION_KEY_AUTH_USER]);
    session_destroy();
}

function auth_has_role(string $role): bool
{
    if (!auth_check()) {
        return false;
    }

    $user = auth_user();
    $roles = $user['roles'] ?? [];

    return hasRoleInList($roles, $role);
}

function auth_is_admin(): bool
{
    return auth_has_role(ROLE_ADMIN);
}

function auth_name(): string
{
    $user = auth_user();
    return $user['full_name'] ?? DEFAULT_USER_NAME;
}

function hasRoleInList(array $roles, string $role): bool
{
    foreach ($roles as $userRole) {
        if (matchesRole($userRole, $role)) {
            return true;
        }
    }
    
    return false;
}

function matchesRole(mixed $userRole, string $role): bool
{
    return (is_array($userRole) && isset($userRole['name']) && $userRole['name'] === $role)
        || (is_string($userRole) && $userRole === $role);
}

function can(string $permission): bool
{
    if (!auth_check()) {
        return false;
    }

    $user = auth_user();
    $permissions = $user['permissions'] ?? [];

    return hasPermissionInList($permissions, $permission);
}

function cannot(string $permission): bool
{
    return !can($permission);
}

function can_any(array $permissions): bool
{
    foreach ($permissions as $permission) {
        if (can($permission)) {
            return true;
        }
    }
    
    return false;
}

function can_all(array $permissions): bool
{
    foreach ($permissions as $permission) {
        if (!can($permission)) {
            return false;
        }
    }
    
    return true;
}

function hasPermissionInList(array $permissions, string $permission): bool
{
    foreach ($permissions as $perm) {
        if (matchesPermission($perm, $permission)) {
            return true;
        }
    }
    
    return false;
}

function matchesPermission(mixed $perm, string $permission): bool
{
    return (is_array($perm) && isset($perm['name']) && $perm['name'] === $permission)
        || (is_string($perm) && $perm === $permission);
}
