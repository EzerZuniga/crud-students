<?php
/**
 * Router - Sistema de enrutamiento moderno
 * Maneja las rutas de la aplicación de forma limpia
 */

namespace App\Core;

class Router
{
    private array $routes = [];
    private $notFoundHandler = null;

    /**
     * Registra una ruta GET
     *
     * @param string $path Ruta de la URL
     * @param callable|array $handler Controlador y método
     * @param array|callable|null $middleware Middleware opcional
     * @return self
     */
    public function get(string $path, callable|array $handler, array|callable|null $middleware = null): self
    {
        $this->addRoute('GET', $path, $handler, $middleware);
        return $this;
    }

    /**
     * Registra una ruta POST
     *
     * @param string $path Ruta de la URL
     * @param callable|array $handler Controlador y método
     * @param array|callable|null $middleware Middleware opcional
     * @return self
     */
    public function post(string $path, callable|array $handler, array|callable|null $middleware = null): self
    {
        $this->addRoute('POST', $path, $handler, $middleware);
        return $this;
    }

    /**
     * Registra una ruta que acepta GET y POST
     *
     * @param string $path Ruta de la URL
     * @param callable|array $handler Controlador y método
     * @param array|callable|null $middleware Middleware opcional
     * @return self
     */
    public function any(string $path, callable|array $handler, array|callable|null $middleware = null): self
    {
        $this->addRoute('GET', $path, $handler, $middleware);
        $this->addRoute('POST', $path, $handler, $middleware);
        return $this;
    }

    /**
     * Agrega una ruta al registro
     *
     * @param string $method Método HTTP
     * @param string $path Ruta
     * @param callable|array $handler Manejador
     * @param array|callable|null $middleware Middleware
     * @return void
     */
    private function addRoute(string $method, string $path, callable|array $handler, array|callable|null $middleware = null): void
    {
        $pattern = $this->convertToRegex($path);
        $this->routes[$method][$pattern] = [
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    /**
     * Convierte una ruta a expresión regular
     *
     * @param string $path Ruta a convertir
     * @return string Expresión regular
     */
    private function convertToRegex(string $path): string
    {
        // Escapar caracteres especiales excepto { }
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = str_replace('/', '\/', $pattern);
        return '/^' . $pattern . '$/';
    }

    /**
     * Despacha la petición a la ruta correspondiente
     *
     * @param string $uri URI solicitada
     * @param string $method Método HTTP
     * @return mixed
     */
    public function dispatch(string $uri, string $method = 'GET'): mixed
    {
        // Limpiar la URI
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        // Buscar coincidencia
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $pattern => $route) {
                if (preg_match($pattern, $uri, $matches)) {
                    // Extraer parámetros
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    
                    // Ejecutar middleware si existe
                    if (isset($route['middleware']) && $route['middleware']) {
                        $this->runMiddleware($route['middleware']);
                    }
                    
                    return $this->callHandler($route['handler'], $params);
                }
            }
        }

        // No encontrado
        return $this->handleNotFound();
    }

    /**
     * Ejecuta el middleware antes de la ruta
     *
     * @param array|callable $middleware Middleware a ejecutar
     * @return void
     */
    private function runMiddleware(array|callable $middleware): void
    {
        if (is_callable($middleware)) {
            call_user_func($middleware);
        } elseif (is_array($middleware)) {
            [$class, $method] = $middleware;
            call_user_func([$class, $method]);
        }
    }

    /**
     * Llama al manejador de la ruta
     *
     * @param callable|array $handler Manejador
     * @param array $params Parámetros de la ruta
     * @return mixed
     */
    private function callHandler(callable|array $handler, array $params = []): mixed
    {
        if (is_array($handler)) {
            [$controller, $method] = $handler;
            
            // Obtener instancia del controlador desde el contenedor
            $instance = Container::resolve($controller);
            
            return call_user_func_array([$instance, $method], $params);
        }

        return call_user_func_array($handler, $params);
    }

    /**
     * Maneja cuando no se encuentra la ruta
     *
     * @return mixed
     */
    private function handleNotFound(): mixed
    {
        http_response_code(404);
        
        if ($this->notFoundHandler) {
            return call_user_func($this->notFoundHandler);
        }

        echo json_encode(['error' => 'Ruta no encontrada']);
        exit;
    }

    /**
     * Define el manejador de 404
     *
     * @param callable $handler Manejador
     * @return self
     */
    public function notFound(callable $handler): self
    {
        $this->notFoundHandler = $handler;
        return $this;
    }
}
