<?php

namespace App\Core;

class CSRF
{
    private const TOKEN_NAME = 'csrf_token';
    private const TOKEN_TIME_NAME = 'csrf_token_time';
    private const TOKEN_LIFETIME = 3600;
    private const TOKEN_LENGTH = 32;
    private const HTML_ENCODING = 'UTF-8';

    public static function generateToken(): string
    {
        self::ensureSessionStarted();

        $token = self::createRandomToken();
        self::storeToken($token);

        return $token;
    }

    public static function getToken(): string
    {
        self::ensureSessionStarted();

        if (!self::hasValidToken()) {
            return self::generateToken();
        }

        return $_SESSION[self::TOKEN_NAME];
    }

    public static function validateToken(?string $token): bool
    {
        self::ensureSessionStarted();

        if (!self::canValidateToken($token)) {
            return false;
        }

        if (self::isTokenExpired()) {
            self::destroyToken();
            return false;
        }

        return self::tokensMatch($_SESSION[self::TOKEN_NAME], $token);
    }

    public static function destroyToken(): void
    {
        self::ensureSessionStarted();
        self::removeTokenFromSession();
    }

    public static function field(): string
    {
        $token = self::getToken();
        return self::generateHiddenField($token);
    }

    public static function validate(): bool
    {
        $token = self::getTokenFromRequest();
        return self::validateToken($token);
    }

    private static function ensureSessionStarted(): void
    {
        if (!self::isSessionActive()) {
            session_start();
        }
    }

    private static function isSessionActive(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    private static function createRandomToken(): string
    {
        // @phpstan-ignore-next-line - random_bytes is a native PHP function
        $bytes = \random_bytes(self::TOKEN_LENGTH);
        return \bin2hex($bytes);
    }

    private static function storeToken(string $token): void
    {
        $_SESSION[self::TOKEN_NAME] = $token;
        $_SESSION[self::TOKEN_TIME_NAME] = time();
    }

    private static function hasValidToken(): bool
    {
        return isset($_SESSION[self::TOKEN_NAME]) && !self::isTokenExpired();
    }

    private static function isTokenExpired(): bool
    {
        if (!isset($_SESSION[self::TOKEN_TIME_NAME])) {
            return true;
        }

        return (time() - $_SESSION[self::TOKEN_TIME_NAME]) > self::TOKEN_LIFETIME;
    }

    private static function canValidateToken(?string $token): bool
    {
        return $token !== null && isset($_SESSION[self::TOKEN_NAME]);
    }

    private static function tokensMatch(string $storedToken, string $providedToken): bool
    {
        return hash_equals($storedToken, $providedToken);
    }

    private static function removeTokenFromSession(): void
    {
        unset($_SESSION[self::TOKEN_NAME], $_SESSION[self::TOKEN_TIME_NAME]);
    }

    private static function generateHiddenField(string $token): string
    {
        $escapedToken = self::escapeHtml($token);
        return '<input type="hidden" name="' . self::TOKEN_NAME . '" value="' . $escapedToken . '">';
    }

    private static function escapeHtml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, self::HTML_ENCODING);
    }

    private static function getTokenFromRequest(): ?string
    {
        return $_POST[self::TOKEN_NAME] ?? null;
    }
}
