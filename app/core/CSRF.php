<?php
/**
 * Clase para manejo de tokens CSRF
 * Protección contra ataques Cross-Site Request Forgery
 */

namespace App\Core;

class CSRF
{
    private const TOKEN_NAME = 'csrf_token';
    private const TOKEN_TIME_NAME = 'csrf_token_time';
    private const TOKEN_LIFETIME = 3600; // 1 hora

    /**
     * Genera un nuevo token CSRF
     *
     * @return string Token generado
     */
    public static function generateToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION[self::TOKEN_NAME] = $token;
        $_SESSION[self::TOKEN_TIME_NAME] = time();

        return $token;
    }

    /**
     * Obtiene el token CSRF actual o genera uno nuevo
     *
     * @return string Token CSRF
     */
    public static function getToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[self::TOKEN_NAME]) || self::isTokenExpired()) {
            return self::generateToken();
        }

        return $_SESSION[self::TOKEN_NAME];
    }

    /**
     * Valida un token CSRF
     *
     * @param string|null $token Token a validar
     * @return bool True si el token es válido
     */
    public static function validateToken(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!$token || !isset($_SESSION[self::TOKEN_NAME])) {
            return false;
        }

        if (self::isTokenExpired()) {
            self::destroyToken();
            return false;
        }

        return hash_equals($_SESSION[self::TOKEN_NAME], $token);
    }

    /**
     * Verifica si el token ha expirado
     *
     * @return bool True si el token ha expirado
     */
    private static function isTokenExpired(): bool
    {
        if (!isset($_SESSION[self::TOKEN_TIME_NAME])) {
            return true;
        }

        return (time() - $_SESSION[self::TOKEN_TIME_NAME]) > self::TOKEN_LIFETIME;
    }

    /**
     * Destruye el token CSRF actual
     *
     * @return void
     */
    public static function destroyToken(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION[self::TOKEN_NAME], $_SESSION[self::TOKEN_TIME_NAME]);
    }

    /**
     * Genera el campo oculto HTML para el formulario
     *
     * @return string HTML del campo oculto
     */
    public static function field(): string
    {
        $token = self::getToken();
        return '<input type="hidden" name="' . self::TOKEN_NAME . '" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Valida el token desde la petición POST
     *
     * @return bool True si el token es válido
     */
    public static function validate(): bool
    {
        $token = $_POST[self::TOKEN_NAME] ?? null;
        return self::validateToken($token);
    }
}
