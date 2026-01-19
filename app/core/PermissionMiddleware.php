<?php
/**
 * Permission Middleware
 * Middleware para verificar permisos específicos
 */

namespace App\Core;

class PermissionMiddleware
{
    /**
     * Verifica si el usuario tiene el permiso especificado
     *
     * @param string $permission Nombre del permiso requerido
     * @return bool
     */
    public static function check(string $permission): bool
    {
        if (!auth_check()) {
            flash('warning', 'Debes iniciar sesión');
            header('Location: ' . route('auth.login'));
            exit;
        }

        if (!can($permission)) {
            flash('danger', 'No tienes permisos para realizar esta acción');
            header('Location: ' . route('students.index'));
            exit;
        }

        return true;
    }

    /**
     * Verifica si el usuario tiene alguno de los permisos especificados
     *
     * @param array $permissions Array de permisos
     * @return bool
     */
    public static function checkAny(array $permissions): bool
    {
        if (!auth_check()) {
            flash('warning', 'Debes iniciar sesión');
            header('Location: ' . route('auth.login'));
            exit;
        }

        if (!can_any($permissions)) {
            flash('danger', 'No tienes permisos para realizar esta acción');
            header('Location: ' . route('students.index'));
            exit;
        }

        return true;
    }

    /**
     * Verifica si el usuario tiene todos los permisos especificados
     *
     * @param array $permissions Array de permisos
     * @return bool
     */
    public static function checkAll(array $permissions): bool
    {
        if (!auth_check()) {
            flash('warning', 'Debes iniciar sesión');
            header('Location: ' . route('auth.login'));
            exit;
        }

        if (!can_all($permissions)) {
            flash('danger', 'No tienes los permisos necesarios');
            header('Location: ' . route('students.index'));
            exit;
        }

        return true;
    }

    /**
     * Middleware para crear estudiantes
     */
    public static function canCreateStudents(): bool
    {
        return self::check('students.create');
    }

    /**
     * Middleware para editar estudiantes
     */
    public static function canEditStudents(): bool
    {
        return self::check('students.edit');
    }

    /**
     * Middleware para eliminar estudiantes
     */
    public static function canDeleteStudents(): bool
    {
        return self::check('students.delete');
    }

    /**
     * Middleware para ver estudiantes
     */
    public static function canViewStudents(): bool
    {
        return self::check('students.view');
    }
}
