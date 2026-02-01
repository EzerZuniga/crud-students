<?php

namespace App\Core;

class Router
{
    private const HTTP_METHOD_GET = 'GET';
    private const HTTP_METHOD_POST = 'POST';
    private const HTTP_NOT_FOUND = 404;
    private const DEFAULT_URI = '/';
    private const REGEX_PARAM_PATTERN = '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/';
    private const REGEX_PARAM_REPLACEMENT = '(?P<$1>[^/]+)';
    private const ERROR_NOT_FOUND = 'Ruta no encontrada';

    private array $routes = [];
    private $notFoundHandler = null;

    public function get(string $path, callable|array $handler, array|callable|null $middleware = null): self
    {
        $this->addRoute(self::HTTP_METHOD_GET, $path, $handler, $middleware);
        return $this;
    }

    public function post(string $path, callable|array $handler, array|callable|null $middleware = null): self
    {
        $this->addRoute(self::HTTP_METHOD_POST, $path, $handler, $middleware);
        return $this;
    }

    public function any(string $path, callable|array $handler, array|callable|null $middleware = null): self
    {
        $this->addRoute(self::HTTP_METHOD_GET, $path, $handler, $middleware);
        $this->addRoute(self::HTTP_METHOD_POST, $path, $handler, $middleware);
        return $this;
    }

    public function notFound(callable $handler): self
    {
        $this->notFoundHandler = $handler;
        return $this;
    }

    public function dispatch(string $uri, string $method = self::HTTP_METHOD_GET): mixed
    {
        $uri = $this->normalizeUri($uri);
        $route = $this->findRoute($uri, $method);

        if ($route === null) {
            return $this->handleNotFound();
        }

        $params = $this->extractParams($route['pattern'], $uri);
        $this->executeMiddleware($route['middleware']);

        return $this->callHandler($route['handler'], $params);
    }

    private function addRoute(string $method, string $path, callable|array $handler, array|callable|null $middleware = null): void
    {
        $pattern = $this->convertToRegex($path);
        $this->routes[$method][$pattern] = [
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    private function convertToRegex(string $path): string
    {
        $pattern = preg_replace(self::REGEX_PARAM_PATTERN, self::REGEX_PARAM_REPLACEMENT, $path);
        $pattern = str_replace('/', '\/', $pattern);
        return '/^' . $pattern . '$/';
    }

    private function normalizeUri(string $uri): string
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?? self::DEFAULT_URI;
        return rtrim($uri, '/') ?: self::DEFAULT_URI;
    }

    private function findRoute(string $uri, string $method): ?array
    {
        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $pattern => $route) {
            if (preg_match($pattern, $uri)) {
                return $route;
            }
        }

        return null;
    }

    private function extractParams(string $pattern, string $uri): array
    {
        if (!preg_match($pattern, $uri, $matches)) {
            return [];
        }

        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }

    private function executeMiddleware(array|callable|null $middleware): void
    {
        if ($middleware === null) {
            return;
        }

        if (is_callable($middleware)) {
            call_user_func($middleware);
            return;
        }

        if (is_array($middleware)) {
            $this->callArrayMiddleware($middleware);
        }
    }

    private function callArrayMiddleware(array $middleware): void
    {
        [$class, $method] = $middleware;
        call_user_func([$class, $method]);
    }

    private function callHandler(callable|array $handler, array $params = []): mixed
    {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        if (is_array($handler)) {
            return $this->callControllerHandler($handler, $params);
        }

        return null;
    }

    private function callControllerHandler(array $handler, array $params): mixed
    {
        [$controller, $method] = $handler;
        $instance = $this->resolveController($controller);

        return call_user_func_array([$instance, $method], $params);
    }

    private function resolveController(string $controller): object
    {
        return Container::resolve($controller);
    }

    private function handleNotFound(): mixed
    {
        http_response_code(self::HTTP_NOT_FOUND);

        if ($this->notFoundHandler !== null) {
            return call_user_func($this->notFoundHandler);
        }

        $this->defaultNotFoundResponse();
        return null;
    }

    private function defaultNotFoundResponse(): void
    {
        echo json_encode(['error' => self::ERROR_NOT_FOUND]);
        exit;
    }
}
