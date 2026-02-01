<?php

declare(strict_types=1);

namespace App\Core;

use ReflectionClass;

abstract class Controller
{
    private const LAYOUT_DEFAULT = 'default';
    private const LAYOUT_AUTH = 'auth';
    private const HTTP_REDIRECT = 302;
    private const HTTP_SERVER_ERROR = 500;
    
    private const SOURCE_GET = 'GET';
    private const SOURCE_POST = 'POST';
    private const SOURCE_REQUEST = 'REQUEST';
    
    private const DEFAULT_REFERER = '/';
    private const VIEWS_PATH = __DIR__ . '/../views';

    /** @var array<string, mixed> Variables para pasar a la vista */
    private array $viewData = [];

    protected function render(string $view, array $data = [], string $layout = self::LAYOUT_DEFAULT): void
    {
        $this->viewData = $data;
        
        $viewPath = $this->getViewPath($view, $layout);
        
        if (!$this->viewExists($viewPath)) {
            $this->abort(self::HTTP_SERVER_ERROR, 'Vista no encontrada: ' . $view);
            return;
        }

        $this->renderWithLayout($viewPath, $layout);
    }

    protected function redirect(string $path, int $statusCode = self::HTTP_REDIRECT): void
    {
        http_response_code($statusCode);
        header('Location: ' . $path);
        exit;
    }

    protected function redirectBack(): void
    {
        $referer = $this->getReferer();
        $this->redirect($referer);
    }

    public function abort(int $statusCode, string $message): void
    {
        http_response_code($statusCode);
        $this->logError($statusCode, $message);
        $this->renderErrorPage($statusCode, $message);
        exit;
    }

    protected function isMethod(string $method): bool
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) === strtoupper($method);
    }

    protected function validateCSRF(): bool
    {
        if ($this->isMethod(self::SOURCE_POST)) {
            return CSRF::validate();
        }
        return true;
    }

    protected function input(string $key, mixed $default = null, string $source = self::SOURCE_REQUEST): mixed
    {
        $data = $this->getInputSource($source);
        return $data[$key] ?? $default;
    }

    private function getViewPath(string $view, string $layout): string
    {
        if ($layout === self::LAYOUT_AUTH) {
            return self::VIEWS_PATH . "/auth/{$view}.php";
        }
        
        $controllerName = $this->getControllerName();
        return self::VIEWS_PATH . "/{$controllerName}s/{$view}.php";
    }

    private function getControllerName(): string
    {
        $className = (new ReflectionClass($this))->getShortName();
        return strtolower(str_replace('Controller', '', $className));
    }

    private function viewExists(string $path): bool
    {
        return file_exists($path);
    }

    private function renderWithLayout(string $viewPath, string $layout): void
    {
        if ($layout === self::LAYOUT_AUTH) {
            $this->renderAuthLayout($viewPath);
        } else {
            $this->renderDefaultLayout($viewPath);
        }
    }

    private function renderAuthLayout(string $viewPath): void
    {
        extract($this->viewData);
        require self::VIEWS_PATH . '/layouts/auth_header.php';
        require $viewPath;
        require self::VIEWS_PATH . '/layouts/auth_footer.php';
    }

    private function renderDefaultLayout(string $viewPath): void
    {
        extract($this->viewData);
        require self::VIEWS_PATH . '/layouts/header.php';
        require $viewPath;
        require self::VIEWS_PATH . '/layouts/footer.php';
    }

    private function getReferer(): string
    {
        return $_SERVER['HTTP_REFERER'] ?? self::DEFAULT_REFERER;
    }

    private function logError(int $statusCode, string $message): void
    {
        if (function_exists('app_log')) {
            app_log("Error {$statusCode}: {$message}");
        }
    }

    private function renderErrorPage(int $statusCode, string $message): void
    {
        echo $this->getErrorPageHtml($statusCode, $message);
    }

    private function getErrorPageHtml(int $statusCode, string $message): string
    {
        $code = htmlspecialchars((string)$statusCode);
        $msg = htmlspecialchars($message);
        
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error {$code}</title>
    {$this->getErrorPageStyles()}
</head>
<body>
    <div class="error-container">
        <h1 class="error-code">{$code}</h1>
        <p class="error-message">{$msg}</p>
        <a href="/" class="btn">Volver al inicio</a>
    </div>
</body>
</html>
HTML;
    }

    private function getErrorPageStyles(): string
    {
        return <<<STYLE
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
STYLE;
    }

    private function getInputSource(string $source): array
    {
        return match(strtoupper($source)) {
            self::SOURCE_GET => $_GET,
            self::SOURCE_POST => $_POST,
            default => $_REQUEST
        };
    }
}
