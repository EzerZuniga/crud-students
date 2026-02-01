<?php

declare(strict_types=1);

// Constantes locales específicas de configuración de la aplicación
const DEFAULT_ENV = ENV_DEVELOPMENT;
const DEFAULT_DEBUG = true;
const DEFAULT_URL = 'http://localhost:8000';

function getEnvValue(string $key, mixed $default = null): mixed
{
    return env($key, $default);
}

function getRootPath(): string
{
    return dirname(__DIR__);
}

function getAppPath(string $rootPath): string
{
    return $rootPath . '/app';
}

function getConfigPath(string $rootPath): string
{
    return $rootPath . '/config';
}

function getPublicPath(string $rootPath): string
{
    return $rootPath . '/public';
}

function getStoragePath(string $rootPath): string
{
    return $rootPath . '/storage';
}

function getViewsPath(string $appPath): string
{
    return $appPath . '/views';
}

function getLogsPath(string $storagePath): string
{
    return $storagePath . '/logs';
}

function getErrorLogPath(string $logsPath): string
{
    return $logsPath . '/' . ERROR_LOG_FILE;
}

function isProductionEnvironment(string $environment): bool
{
    return $environment === ENV_PRODUCTION;
}

function configureProductionErrors(string $errorLogPath): void
{
    error_reporting(PRODUCTION_ERROR_LEVEL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', $errorLogPath);
}

function configureDevelopmentErrors(): void
{
    error_reporting(DEVELOPMENT_ERROR_LEVEL);
    ini_set('display_errors', '1');
}

function configureErrorHandling(string $environment, string $errorLogPath): void
{
    if (isProductionEnvironment($environment)) {
        configureProductionErrors($errorLogPath);
    } else {
        configureDevelopmentErrors();
    }
}

function setApplicationTimezone(string $timezone): void
{
    date_default_timezone_set($timezone);
}

function buildPathsConfiguration(
    string $rootPath,
    string $appPath,
    string $configPath,
    string $publicPath,
    string $storagePath,
    string $viewsPath,
    string $logsPath
): array {
    return [
        'root' => $rootPath,
        'app' => $appPath,
        'config' => $configPath,
        'public' => $publicPath,
        'storage' => $storagePath,
        'views' => $viewsPath,
        'logs' => $logsPath,
    ];
}

function buildAppConfiguration(
    string $name,
    string $env,
    bool $debug,
    string $url,
    string $timezone
): array {
    return [
        'name' => $name,
        'env' => $env,
        'debug' => $debug,
        'url' => $url,
        'timezone' => $timezone,
    ];
}

function buildDatabaseConfiguration(
    string $driver,
    string $host,
    string $port,
    string $database,
    string $username,
    string $password,
    string $charset
): array {
    return [
        'driver' => $driver,
        'host' => $host,
        'port' => $port,
        'database' => $database,
        'username' => $username,
        'password' => $password,
        'charset' => $charset,
    ];
}

function buildSessionConfiguration(int $lifetime): array
{
    return [
        'lifetime' => $lifetime,
    ];
}

function buildPaginationConfiguration(int $itemsPerPage): array
{
    return [
        'items_per_page' => $itemsPerPage,
    ];
}

$rootPath = getRootPath();
$appPath = getAppPath($rootPath);
$configPath = getConfigPath($rootPath);
$publicPath = getPublicPath($rootPath);
$storagePath = getStoragePath($rootPath);
$viewsPath = getViewsPath($appPath);
$logsPath = getLogsPath($storagePath);

define('ROOT_PATH', $rootPath);
define('APP_PATH', $appPath);
define('CONFIG_PATH', $configPath);
define('PUBLIC_PATH', $publicPath);
define('STORAGE_PATH', $storagePath);
define('VIEWS_PATH', $viewsPath);
define('LOGS_PATH', $logsPath);

$appName = getEnvValue('APP_NAME', APP_DEFAULT_NAME);
$appEnv = getEnvValue('APP_ENV', DEFAULT_ENV);
$appDebug = getEnvValue('APP_DEBUG', DEFAULT_DEBUG);
$appUrl = getEnvValue('APP_URL', DEFAULT_URL);
$appTimezone = getEnvValue('APP_TIMEZONE', APP_DEFAULT_TIMEZONE);

define('APP_NAME', $appName);
define('APP_ENV', $appEnv);
define('APP_DEBUG', $appDebug);
define('APP_URL', $appUrl);
define('TIMEZONE', $appTimezone);

setApplicationTimezone($appTimezone);

$errorLogPath = getErrorLogPath($logsPath);
configureErrorHandling($appEnv, $errorLogPath);

$sessionLifetime = getEnvValue('SESSION_LIFETIME', SESSION_LIFETIME);
define('APP_SESSION_LIFETIME', $sessionLifetime);

$dbDriver = getEnvValue('DB_DRIVER', DB_DEFAULT_DRIVER);
$dbHost = getEnvValue('DB_HOST', DB_DEFAULT_HOST);
$dbPort = getEnvValue('DB_PORT', DB_DEFAULT_PORT);
$dbName = getEnvValue('DB_NAME', DB_DEFAULT_NAME);
$dbUser = getEnvValue('DB_USER', DB_DEFAULT_USER);
$dbPassword = getEnvValue('DB_PASSWORD', DB_DEFAULT_PASSWORD);
$dbCharset = getEnvValue('DB_CHARSET', DB_DEFAULT_CHARSET);

define('DB_DRIVER', $dbDriver);
define('DB_HOST', $dbHost);
define('DB_PORT', $dbPort);
define('DB_NAME', $dbName);
define('DB_USER', $dbUser);
define('DB_PASSWORD', $dbPassword);
define('DB_CHARSET', $dbCharset);

$itemsPerPage = getEnvValue('ITEMS_PER_PAGE', DEFAULT_ITEMS_PER_PAGE);
define('ITEMS_PER_PAGE', $itemsPerPage);

return [
    'app' => buildAppConfiguration(
        $appName,
        $appEnv,
        $appDebug,
        $appUrl,
        $appTimezone
    ),
    'database' => buildDatabaseConfiguration(
        $dbDriver,
        $dbHost,
        $dbPort,
        $dbName,
        $dbUser,
        $dbPassword,
        $dbCharset
    ),
    'paths' => buildPathsConfiguration(
        $rootPath,
        $appPath,
        $configPath,
        $publicPath,
        $storagePath,
        $viewsPath,
        $logsPath
    ),
    'session' => buildSessionConfiguration($sessionLifetime),
    'pagination' => buildPaginationConfiguration($itemsPerPage),
];
