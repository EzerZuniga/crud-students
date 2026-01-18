<?php
/**
 * Front Controller - Punto de entrada de la aplicación
 * Enruta todas las peticiones a los controladores correspondientes
 */

// Cargar el bootstrap de la aplicación
require_once __DIR__ . '/../config/bootstrap.php';

try {
    // Inicializar el controlador de estudiantes
    $controller = new StudentController($pdo);
    
    // Obtener la acción de la URL (por defecto 'index')
    $action = $_GET['action'] ?? 'index';
    
    // Obtener el ID si está presente
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

    // Enrutar a la acción correspondiente
    switch ($action) {
        case 'index':
            $controller->index();
            break;
            
        case 'show':
            if ($id === null) {
                $controller->abort(400, 'Falta el parámetro ID');
                break;
            }
            $controller->show($id);
            break;
            
        case 'create':
            $controller->create();
            break;
            
        case 'store':
            $controller->store();
            break;
            
        case 'edit':
            if ($id === null) {
                $controller->abort(400, 'Falta el parámetro ID');
                break;
            }
            $controller->edit($id);
            break;
            
        case 'update':
            if ($id === null) {
                $controller->abort(400, 'Falta el parámetro ID');
                break;
            }
            $controller->update($id);
            break;
            
        case 'destroy':
            if ($id === null) {
                $controller->abort(400, 'Falta el parámetro ID');
                break;
            }
            $controller->destroy($id);
            break;
            
        default:
            $controller->abort(404, 'Acción no encontrada');
    }
    
} catch (Exception $e) {
    // El manejo de excepciones ya se configuró en bootstrap.php
    throw $e;
}
