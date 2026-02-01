<?php

namespace App\Core;

class PermissionMiddleware
{
    private const MSG_LOGIN_REQUIRED = 'Debes iniciar sesión';
    private const MSG_PERMISSION_DENIED = 'No tienes permisos para realizar esta acción';
    private const MSG_PERMISSIONS_DENIED = 'No tienes los permisos necesarios';
    
    private const LOGIN_ROUTE = 'auth.login';
    private const DEFAULT_REDIRECT = 'students.index';
    
    private const PERMISSION_STUDENTS_CREATE = 'students.create';
    private const PERMISSION_STUDENTS_EDIT = 'students.edit';
    private const PERMISSION_STUDENTS_DELETE = 'students.delete';
    private const PERMISSION_STUDENTS_VIEW = 'students.view';

    public static function check(string $permission): bool
    {
        self::ensureAuthenticated();

        if (!self::userHasPermission($permission)) {
            self::redirectWithError(self::MSG_PERMISSION_DENIED);
        }

        return true;
    }

    public static function checkAny(array $permissions): bool
    {
        self::ensureAuthenticated();

        if (!self::userHasAnyPermission($permissions)) {
            self::redirectWithError(self::MSG_PERMISSION_DENIED);
        }

        return true;
    }

    public static function checkAll(array $permissions): bool
    {
        self::ensureAuthenticated();

        if (!self::userHasAllPermissions($permissions)) {
            self::redirectWithError(self::MSG_PERMISSIONS_DENIED);
        }

        return true;
    }

    public static function canCreateStudents(): bool
    {
        return self::check(self::PERMISSION_STUDENTS_CREATE);
    }

    public static function canEditStudents(): bool
    {
        return self::check(self::PERMISSION_STUDENTS_EDIT);
    }

    public static function canDeleteStudents(): bool
    {
        return self::check(self::PERMISSION_STUDENTS_DELETE);
    }

    public static function canViewStudents(): bool
    {
        return self::check(self::PERMISSION_STUDENTS_VIEW);
    }

    private static function ensureAuthenticated(): void
    {
        if (!self::isAuthenticated()) {
            self::redirectToLogin();
        }
    }

    private static function isAuthenticated(): bool
    {
        return auth_check();
    }

    private static function userHasPermission(string $permission): bool
    {
        return can($permission);
    }

    private static function userHasAnyPermission(array $permissions): bool
    {
        return can_any($permissions);
    }

    private static function userHasAllPermissions(array $permissions): bool
    {
        return can_all($permissions);
    }

    private static function redirectToLogin(): void
    {
        flash('warning', self::MSG_LOGIN_REQUIRED);
        self::performRedirect(self::LOGIN_ROUTE);
    }

    private static function redirectWithError(string $message): void
    {
        flash('danger', $message);
        self::performRedirect(self::DEFAULT_REDIRECT);
    }

    private static function performRedirect(string $route): void
    {
        header('Location: ' . route($route));
        exit;
    }
}
