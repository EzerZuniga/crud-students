<?php
/**
 * Clase base para todos los controladores
 * Proporciona métodos comunes para renderizado, redirección y manejo de errores
 */

namespace App\Core;

abstract class Controller
{
    /**
     * Renderiza una vista con los datos proporcionados
     *
     * @param string $view Nombre de la vista (sin extensión .php)
     * @param array $data Datos a pasar a la vista
     * @param string $layout Layout a usar (por defecto 'default', también 'auth')
     * @return void
     */
    protected function render(string $view, array $data = [], string $layout = 'default'): void
    {
        // Extraer datos para usar como variables en la vista
        extract($data);
        
        // Determinar la ruta de la vista
        $viewPath = $this->getViewPath($view, $layout);
        
        if (!file_exists($viewPath)) {
            $this->abort(500, 'Vista no encontrada: ' . $view);
            return;
        }

        // Renderizar con el layout específico
        if ($layout === 'auth') {
            require __DIR__ . '/../views/layouts/auth_header.php';
            require $viewPath;
            require __DIR__ . '/../views/layouts/auth_footer.php';
        } else {
            require __DIR__ . '/../views/layouts/header.php';
            require $viewPath;
            require __DIR__ . '/../views/layouts/footer.php';
        }
    }

    /**
     * Redirige a una ruta específica
     *
     * @param string $path Ruta a la que redirigir
     * @param int $statusCode Código de estado HTTP
     * @return void
     */
    protected function redirect(string $path, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header('Location: ' . $path);
        exit;
    }

    /**
     * Redirige de vuelta a la página anterior
     *
     * @return void
     */
    protected function redirectBack(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    /**
     * Detiene la ejecución con un mensaje de error
     *
     * @param int $statusCode Código de estado HTTP
     * @param string $message Mensaje de error
     * @return void
     */
    public function abort(int $statusCode, string $message): void
    {
        http_response_code($statusCode);
        
        // Log del error
        if (function_exists('app_log')) {
            app_log("Error {$statusCode}: {$message}");
        }
        
        // Renderizar página de error
        echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error ' . htmlspecialchars((string)$statusCode) . '</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: #f8fafc;
            color: #1e293b;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: #ef4444;
            margin: 0;
        }
        .error-message {
            font-size: 1.5rem;
            margin: 1rem 0 2rem;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #4f46e5;
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 500;
        }
        .btn:hover {
            background: #4338ca;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-code">' . htmlspecialchars((string)$statusCode) . '</h1>
        <p class="error-message">' . htmlspecialchars($message) . '</p>
        <a href="/" class="btn">Volver al inicio</a>
    </div>
</body>
</html>';
        exit;
    }

    /**
     * Obtiene la ruta completa de una vista
     *
     * @param string $view Nombre de la vista
     * @param string $layout Layout a usar
     * @return string Ruta completa
     */
    private function getViewPath(string $view, string $layout = 'default'): string
    {
        // Para vistas de auth (login, register), buscar en auth/
        if ($layout === 'auth') {
            return __DIR__ . "/../views/auth/{$view}.php";
        }
        
        // Obtener el nombre del controlador desde la clase hija
        $className = (new \ReflectionClass($this))->getShortName();
        $controllerName = strtolower(str_replace('Controller', '', $className));
        
        return __DIR__ . "/../views/{$controllerName}s/{$view}.php";
    }

    /**
     * Valida si el método de petición es el esperado
     *
     * @param string $method Método esperado (GET, POST, etc.)
     * @return bool
     */
    protected function isMethod(string $method): bool
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) === strtoupper($method);
    }

    /**
     * Valida el token CSRF en peticiones POST
     *
     * @return bool True si el token es válido o no es necesario
     */
    protected function validateCSRF(): bool
    {
        if ($this->isMethod('POST')) {
            return CSRF::validate();
        }
        return true;
    }

    /**
     * Obtiene un valor de la petición con valor por defecto
     *
     * @param string $key Clave a buscar
     * @param mixed $default Valor por defecto
     * @param string $source Fuente (GET, POST, REQUEST)
     * @return mixed
     */
    protected function input(string $key, $default = null, string $source = 'REQUEST')
    {
        $data = match(strtoupper($source)) {
            'GET' => $_GET,
            'POST' => $_POST,
            default => $_REQUEST
        };

        return $data[$key] ?? $default;
    }
}
