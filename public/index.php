<?php
/**
 * Front Controller - Punto de entrada de la aplicación
 * Enruta todas las peticiones a los controladores correspondientes
 */

// Cargar el bootstrap de la aplicación
require_once __DIR__ . '/../config/bootstrap.php';

try {
    // Cargar las rutas
    $router = require_once __DIR__ . '/../config/routes.php';
    
    // Obtener la URI y método de la petición
    $uri = $_SERVER['REQUEST_URI'];
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Despachar la ruta
    $router->dispatch($uri, $method);
    
} catch (Exception $e) {
    // El manejo de excepciones ya se configuró en bootstrap.php
    throw $e;
}
