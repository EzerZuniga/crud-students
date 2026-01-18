<?php
/**
 * Bootstrap de la aplicación
 * Carga todas las dependencias necesarias y inicializa la aplicación
 */

// Cargar helpers primero (incluye la función env())
require_once __DIR__ . '/../app/helpers/functions.php';

// Cargar configuración de la aplicación
require_once __DIR__ . '/app.php';

// Cargar conexión a base de datos
require_once __DIR__ . '/database.php';

// Cargar clases core
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Validator.php';

// Cargar modelos
require_once APP_PATH . '/models/Student.php';

// Cargar controladores
require_once APP_PATH . '/controllers/StudentController.php';

// Configurar el manejo de errores personalizado
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    $errorType = match($errno) {
        E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR => 'ERROR',
        E_WARNING, E_CORE_WARNING, E_COMPILE_WARNING, E_USER_WARNING => 'WARNING',
        E_NOTICE, E_USER_NOTICE => 'NOTICE',
        default => 'UNKNOWN'
    };
    
    app_log("{$errorType}: {$errstr} in {$errfile}:{$errline}", strtolower($errorType));
    
    if (APP_DEBUG) {
        echo "<div style='background:#fee;padding:1rem;margin:1rem;border-left:4px solid #f00;'>";
        echo "<strong>{$errorType}:</strong> {$errstr}<br>";
        echo "<small>File: {$errfile} | Line: {$errline}</small>";
        echo "</div>";
    }
    
    return true;
});

// Configurar el manejo de excepciones no capturadas
set_exception_handler(function($exception) {
    app_log('EXCEPTION: ' . $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine(), 'error');
    
    http_response_code(500);
    
    if (APP_DEBUG) {
        echo "<div style='background:#fee;padding:1rem;margin:1rem;border-left:4px solid #f00;'>";
        echo "<h3>Exception: " . get_class($exception) . "</h3>";
        echo "<p><strong>Message:</strong> " . $exception->getMessage() . "</p>";
        echo "<p><strong>File:</strong> " . $exception->getFile() . ":" . $exception->getLine() . "</p>";
        echo "<pre>" . $exception->getTraceAsString() . "</pre>";
        echo "</div>";
    } else {
        echo "<h1>Error del servidor</h1>";
        echo "<p>Ha ocurrido un error. Por favor, intenta más tarde.</p>";
    }
});

// Registrar función de cierre
register_shutdown_function(function() {
    $error = error_get_last();
    
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        app_log("FATAL ERROR: {$error['message']} in {$error['file']}:{$error['line']}", 'error');
        
        if (!APP_DEBUG) {
            http_response_code(500);
            echo "<h1>Error fatal</h1>";
            echo "<p>Ha ocurrido un error crítico. Por favor, contacta al administrador.</p>";
        }
    }
});

// Log de inicio de aplicación
app_log('Application bootstrapped successfully');
