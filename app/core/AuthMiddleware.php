<?php

namespace App\Core;

class AuthMiddleware
{
    private const MSG_LOGIN_REQUIRED = 'Debes iniciar sesión para acceder a esta sección';
    private const MSG_ADMIN_REQUIRED = 'No tienes permisos para acceder a esta sección';
    private const MSG_GUEST_ONLY = 'Ya has iniciado sesión';
    
    private const ROLE_ADMIN = 'admin';
    private const DEFAULT_REDIRECT = 'students.index';
    private const LOGIN_REDIRECT = 'auth.login';

    public static function handle(): bool
    {
        if (!self::isAuthenticated()) {
            self::redirectToLogin(self::MSG_LOGIN_REQUIRED, 'warning');
        }
        
        return true;
    }

    public static function hasRole(string $role): bool
    {
        if (!self::isAuthenticated()) {
            return false;
        }

        return self::userHasRole($role);
    }

    public static function admin(): bool
    {
        if (!self::isAuthenticated()) {
            self::redirectToLogin(self::MSG_LOGIN_REQUIRED, 'warning');
        }

        if (!self::hasRole(self::ROLE_ADMIN)) {
            self::redirectWithMessage(
                self::DEFAULT_REDIRECT,
                self::MSG_ADMIN_REQUIRED,
                'danger'
            );
        }

        return true;
    }

    public static function guest(): bool
    {
        if (self::isAuthenticated()) {
            self::redirectToDefault();
        }
        
        return true;
    }

    private static function isAuthenticated(): bool
    {
        return auth_check();
    }

    private static function userHasRole(string $role): bool
    {
        $user = auth_user();
        $roles = $user['roles'] ?? [];
        
        foreach ($roles as $userRole) {
            if ($userRole['name'] === $role) {
                return true;
            }
        }
        
        return false;
    }

    private static function redirectToLogin(string $message, string $type = 'warning'): void
    {
        self::redirectWithMessage(self::LOGIN_REDIRECT, $message, $type);
    }

    private static function redirectToDefault(): void
    {
        self::performRedirect(self::DEFAULT_REDIRECT);
    }

    private static function redirectWithMessage(string $route, string $message, string $type): void
    {
        flash($type, $message);
        self::performRedirect($route);
    }

    private static function performRedirect(string $route): void
    {
        header('Location: ' . route($route));
        exit;
    }
}
