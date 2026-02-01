<?php

declare(strict_types=1);

use App\Core\Router;
use App\Controllers\StudentController;
use App\Controllers\AuthController;
use App\Core\PermissionMiddleware;

// Constantes locales para 404 (solo usadas aquí)
const NOT_FOUND_TITLE = '404 - Página no encontrada';
const NOT_FOUND_HEADING = '404';
const NOT_FOUND_MESSAGE = 'Página no encontrada';
const NOT_FOUND_BUTTON_TEXT = 'Volver al inicio';
const BOOTSTRAP_CSS_URL = 'https://cdn.jsdelivr.net/npm/bootstrap@%s/dist/css/bootstrap.min.css';
const PAGE_CLASS_NOT_FOUND = 'd-flex align-items-center justify-content-center min-vh-100 bg-light';

function createRouter(): Router
{
    return new Router();
}

function registerAuthRoutes(Router $router): void
{
    $router->get(ROUTE_LOGIN, [AuthController::class, 'showLogin']);
    $router->post(ROUTE_LOGIN, [AuthController::class, 'login']);
    $router->get(ROUTE_REGISTER, [AuthController::class, 'showRegister']);
    $router->post(ROUTE_REGISTER, [AuthController::class, 'register']);
    $router->post(ROUTE_LOGOUT, [AuthController::class, 'logout']);
    $router->get(ROUTE_LOGOUT, [AuthController::class, 'logout']);
}

function registerStudentRoutes(Router $router): void
{
    $router->get(
        ROUTE_ROOT,
        [StudentController::class, 'index'],
        [PermissionMiddleware::class, 'canViewStudents']
    );
    
    $router->get(
        ROUTE_STUDENTS,
        [StudentController::class, 'index'],
        [PermissionMiddleware::class, 'canViewStudents']
    );
    
    $router->get(
        ROUTE_STUDENTS_CREATE,
        [StudentController::class, 'create'],
        [PermissionMiddleware::class, 'canCreateStudents']
    );
    
    $router->post(
        ROUTE_STUDENTS,
        [StudentController::class, 'store'],
        [PermissionMiddleware::class, 'canCreateStudents']
    );
    
    $router->get(
        ROUTE_STUDENTS_SHOW,
        [StudentController::class, 'show'],
        [PermissionMiddleware::class, 'canViewStudents']
    );
    
    $router->get(
        ROUTE_STUDENTS_EDIT,
        [StudentController::class, 'edit'],
        [PermissionMiddleware::class, 'canEditStudents']
    );
    
    $router->post(
        ROUTE_STUDENTS_UPDATE,
        [StudentController::class, 'update'],
        [PermissionMiddleware::class, 'canEditStudents']
    );
    
    $router->post(
        ROUTE_STUDENTS_DELETE,
        [StudentController::class, 'destroy'],
        [PermissionMiddleware::class, 'canDeleteStudents']
    );
}

function getNotFoundBootstrapCssUrl(): string
{
    return sprintf(BOOTSTRAP_CSS_URL, BOOTSTRAP_VERSION);
}

function renderNotFoundPage(): string
{
    return sprintf(
        '<!DOCTYPE html><html lang="%s"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>%s</title><link href="%s" rel="stylesheet"></head><body class="%s"><div class="text-center"><h1 class="display-1 fw-bold text-primary">%s</h1><p class="fs-3 text-muted">%s</p><a href="%s" class="btn btn-primary">%s</a></div></body></html>',
        htmlspecialchars(APP_LANG, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars(NOT_FOUND_TITLE, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars(getNotFoundBootstrapCssUrl(), ENT_QUOTES, 'UTF-8'),
        htmlspecialchars(PAGE_CLASS_NOT_FOUND, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars(NOT_FOUND_HEADING, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars(NOT_FOUND_MESSAGE, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars(ROUTE_ROOT, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars(NOT_FOUND_BUTTON_TEXT, ENT_QUOTES, 'UTF-8')
    );
}

function handleNotFound(): void
{
    http_response_code(HTTP_STATUS_NOT_FOUND);
    echo renderNotFoundPage();
}

function registerNotFoundHandler(Router $router): void
{
    $router->notFound(function() {
        handleNotFound();
    });
}

function configureRoutes(): Router
{
    $router = createRouter();
    
    registerAuthRoutes($router);
    registerStudentRoutes($router);
    registerNotFoundHandler($router);
    
    return $router;
}

return configureRoutes();
