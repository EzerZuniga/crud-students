<?php
/**
 * Auth Middleware
 * Protege rutas que requieren autenticación
 */

namespace App\Core;

class AuthMiddleware
{
    /**
     * Verifica si el usuario está autenticado
     */
    public static function handle(): bool
    {
        if (!auth_check()) {
            flash('warning', 'Debes iniciar sesión para acceder a esta sección');
            header('Location: ' . route('auth.login'));
            exit;
        }
        
        return true;
    }

    /**
     * Verifica si el usuario tiene un rol específico
     */
    public static function hasRole(string $role): bool
    {
        if (!auth_check()) {
            return false;
        }

        $user = auth_user();
        $roles = $user['roles'] ?? [];
        
        foreach ($roles as $userRole) {
            if ($userRole['name'] === $role) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Restringe acceso solo a administradores
     */
    public static function admin(): bool
    {
        if (!auth_check()) {
            flash('warning', 'Debes iniciar sesión');
            header('Location: ' . route('auth.login'));
            exit;
        }

        if (!self::hasRole('admin')) {
            flash('danger', 'No tienes permisos para acceder a esta sección');
            header('Location: ' . route('students.index'));
            exit;
        }

        return true;
    }

    /**
     * Solo permite guest (no autenticados)
     */
    public static function guest(): bool
    {
        if (auth_check()) {
            header('Location: ' . route('students.index'));
            exit;
        }
        
        return true;
    }
}
