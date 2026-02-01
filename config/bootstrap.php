<?php

declare(strict_types=1);

use App\Core\Container;
use App\Models\Student;
use App\Controllers\StudentController;

// Cargar constantes centralizadas primero
require_once __DIR__ . '/constants.php';

const VENDOR_AUTOLOAD_PATH = '/../vendor/autoload.php';
const HELPERS_PATH = '/../app/helpers/functions.php';
const APP_CONFIG_PATH = '/app.php';
const DATABASE_CONFIG_PATH = '/database.php';

const ERROR_TITLE_SERVER = 'Error del servidor';
const ERROR_MESSAGE_SERVER = 'Ha ocurrido un error. Por favor, intenta más tarde.';
const ERROR_TITLE_FATAL = 'Error fatal';
const ERROR_MESSAGE_FATAL = 'Ha ocurrido un error crítico. Por favor, contacta al administrador.';

const LOG_BOOTSTRAP_SUCCESS = 'Application bootstrapped successfully';

const PDO_SERVICE_NAME = 'PDO';

const BOOTSTRAP_ERROR_DIV_STYLE = 'background:#fee;padding:1rem;margin:1rem;border-left:4px solid #f00;';

function getVendorAutoloadPath(string $baseDir): string
{
    return $baseDir . VENDOR_AUTOLOAD_PATH;
}

function getHelpersPath(string $baseDir): string
{
    return $baseDir . HELPERS_PATH;
}

function getAppConfigPath(string $baseDir): string
{
    return $baseDir . APP_CONFIG_PATH;
}

function getDatabaseConfigPath(string $baseDir): string
{
    return $baseDir . DATABASE_CONFIG_PATH;
}

function loadComposerAutoload(string $baseDir): void
{
    $autoloadPath = getVendorAutoloadPath($baseDir);
    
    if (file_exists($autoloadPath)) {
        require_once $autoloadPath;
    }
}

function loadHelpers(string $baseDir): void
{
    require_once getHelpersPath($baseDir);
}

function loadAppConfig(string $baseDir): void
{
    require_once getAppConfigPath($baseDir);
}

function isSessionNotStarted(): bool
{
    return session_status() === PHP_SESSION_NONE;
}

function startApplicationSession(): void
{
    if (isSessionNotStarted()) {
        session_start();
    }
}

function getErrorTypeName(int $errno): string
{
    return match($errno) {
        E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR => ERROR_TYPE_ERROR,
        E_WARNING, E_CORE_WARNING, E_COMPILE_WARNING, E_USER_WARNING => ERROR_TYPE_WARNING,
        E_NOTICE, E_USER_NOTICE => ERROR_TYPE_NOTICE,
        default => ERROR_TYPE_UNKNOWN
    };
}

function getLogLevelForErrorType(string $errorType): string
{
    return match($errorType) {
        ERROR_TYPE_ERROR => LOG_LEVEL_ERROR,
        ERROR_TYPE_WARNING => LOG_LEVEL_WARNING,
        ERROR_TYPE_NOTICE => LOG_LEVEL_NOTICE,
        default => LOG_LEVEL_ERROR
    };
}

function logError(string $errorType, string $message, string $file, int $line): void
{
    $logMessage = sprintf('%s: %s in %s:%d', $errorType, $message, $file, $line);
    $logLevel = getLogLevelForErrorType($errorType);
    app_log($logMessage, $logLevel);
}

function renderDebugError(string $errorType, string $message, string $file, int $line): void
{
    printf(
        '<div style="%s"><strong>%s:</strong> %s<br><small>File: %s | Line: %d</small></div>',
        BOOTSTRAP_ERROR_DIV_STYLE,
        htmlspecialchars($errorType, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($message, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($file, ENT_QUOTES, 'UTF-8'),
        $line
    );
}

function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
{
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    $errorType = getErrorTypeName($errno);
    logError($errorType, $errstr, $errfile, $errline);
    
    if (defined('APP_DEBUG') && APP_DEBUG) {
        renderDebugError($errorType, $errstr, $errfile, $errline);
    }
    
    return true;
}

function logException(Throwable $exception): void
{
    $logMessage = sprintf(
        'EXCEPTION: %s in %s:%d',
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    );
    app_log($logMessage, LOG_LEVEL_ERROR);
}

function renderDebugException(Throwable $exception): void
{
    printf(
        '<div style="%s"><h3>Exception: %s</h3><p><strong>Message:</strong> %s</p><p><strong>File:</strong> %s:%d</p><pre>%s</pre></div>',
        BOOTSTRAP_ERROR_DIV_STYLE,
        htmlspecialchars(get_class($exception), ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($exception->getFile(), ENT_QUOTES, 'UTF-8'),
        $exception->getLine(),
        htmlspecialchars($exception->getTraceAsString(), ENT_QUOTES, 'UTF-8')
    );
}

function renderProductionError(): void
{
    printf(
        '<h1>%s</h1><p>%s</p>',
        htmlspecialchars(ERROR_TITLE_SERVER, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars(ERROR_MESSAGE_SERVER, ENT_QUOTES, 'UTF-8')
    );
}

function handleException(Throwable $exception): void
{
    logException($exception);
    http_response_code(HTTP_STATUS_INTERNAL_ERROR);
    
    if (defined('APP_DEBUG') && APP_DEBUG) {
        renderDebugException($exception);
    } else {
        renderProductionError();
    }
}

function isFatalError(int $errorType): bool
{
    return in_array($errorType, FATAL_ERROR_TYPES, true);
}

function logFatalError(array $error): void
{
    $logMessage = sprintf(
        'FATAL ERROR: %s in %s:%d',
        $error['message'],
        $error['file'],
        $error['line']
    );
    app_log($logMessage, LOG_LEVEL_ERROR);
}

function renderFatalError(): void
{
    http_response_code(HTTP_STATUS_INTERNAL_ERROR);
    printf(
        '<h1>%s</h1><p>%s</p>',
        htmlspecialchars(ERROR_TITLE_FATAL, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars(ERROR_MESSAGE_FATAL, ENT_QUOTES, 'UTF-8')
    );
}

function handleShutdown(): void
{
    $error = error_get_last();
    
    if ($error !== null && isFatalError($error['type'])) {
        logFatalError($error);
        
        if (!defined('APP_DEBUG') || !APP_DEBUG) {
            renderFatalError();
        }
    }
}

function registerServices(PDO $pdo): void
{
    Container::instance(PDO_SERVICE_NAME, $pdo);
    
    Container::bind(Student::class, function($c) {
        return new Student($c::resolve(PDO_SERVICE_NAME));
    }, true);
    
    Container::bind(StudentController::class, function($c) {
        return new StudentController($c::resolve(PDO_SERVICE_NAME));
    });
}

function registerErrorHandlers(): void
{
    set_error_handler('handleError');
    set_exception_handler('handleException');
    register_shutdown_function('handleShutdown');
}

function logBootstrapSuccess(): void
{
    app_log(LOG_BOOTSTRAP_SUCCESS);
}

function bootstrapApplication(string $baseDir): void
{
    loadComposerAutoload($baseDir);
    loadHelpers($baseDir);
    loadAppConfig($baseDir);
    
    // Cargar base de datos y obtener conexión PDO
    $pdo = require getDatabaseConfigPath($baseDir);
    
    startApplicationSession();
    registerServices($pdo);
    registerErrorHandlers();
    logBootstrapSuccess();
}

$baseDir = __DIR__;
bootstrapApplication($baseDir);
