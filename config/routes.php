<?php
/**
 * Archivo de rutas de la aplicación
 * Define todas las rutas disponibles
 */

use App\Core\Router;
use App\Controllers\StudentController;
use App\Controllers\AuthController;
use App\Core\AuthMiddleware;
use App\Core\PermissionMiddleware;

$router = new Router();

// ===========================
// Rutas de autenticación (públicas)
// ===========================
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/logout', [AuthController::class, 'logout']); // También por GET para facilidad

// ===========================
// Rutas protegidas con permisos específicos
// ===========================
$router->get('/', [StudentController::class, 'index'], [PermissionMiddleware::class, 'canViewStudents']);
$router->get('/students', [StudentController::class, 'index'], [PermissionMiddleware::class, 'canViewStudents']);
$router->get('/students/create', [StudentController::class, 'create'], [PermissionMiddleware::class, 'canCreateStudents']);
$router->post('/students', [StudentController::class, 'store'], [PermissionMiddleware::class, 'canCreateStudents']);
$router->get('/students/{id}', [StudentController::class, 'show'], [PermissionMiddleware::class, 'canViewStudents']);
$router->get('/students/{id}/edit', [StudentController::class, 'edit'], [PermissionMiddleware::class, 'canEditStudents']);
$router->post('/students/{id}', [StudentController::class, 'update'], [PermissionMiddleware::class, 'canEditStudents']);
$router->post('/students/{id}/delete', [StudentController::class, 'destroy'], [PermissionMiddleware::class, 'canDeleteStudents']);

// Manejador de 404
$router->notFound(function() {
    http_response_code(404);
    echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="text-center">
        <h1 class="display-1 fw-bold text-primary">404</h1>
        <p class="fs-3 text-muted">Página no encontrada</p>
        <a href="/" class="btn btn-primary">Volver al inicio</a>
    </div>
</body>
</html>';
});

return $router;
