<?php
if (!defined('AUTH_HEADER_DEFAULT_TITLE')) {
    define('AUTH_HEADER_DEFAULT_TITLE', 'Autenticación');
}
if (!defined('AUTH_HEADER_DEFAULT_APP_NAME')) {
    define('AUTH_HEADER_DEFAULT_APP_NAME', 'CRUD Students');
}
const META_DESCRIPTION = 'Sistema de gestión de estudiantes - Autenticación de usuario';
const META_AUTHOR = 'Ezer B. Zuniga Chura';
const META_ROBOTS = 'noindex, nofollow';
const FAVICON_EMOJI = '🎓';
const CDN_HOST = '//cdn.jsdelivr.net';

if (!defined('BOOTSTRAP_VERSION')) {
    define('BOOTSTRAP_VERSION', '5.3.2');
}
if (!defined('BOOTSTRAP_ICONS_VERSION')) {
    define('BOOTSTRAP_ICONS_VERSION', '1.11.2');
}

if (!defined('BOOTSTRAP_CSS_INTEGRITY')) {
    define('BOOTSTRAP_CSS_INTEGRITY', 'sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN');
}
if (!defined('BOOTSTRAP_ICONS_URL')) {
    define('BOOTSTRAP_ICONS_URL', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@' . BOOTSTRAP_ICONS_VERSION . '/font/bootstrap-icons.css');
}

if (!function_exists('getPageTitle')) {
    function getPageTitle(): string
    {
        $title = htmlspecialchars($GLOBALS['title'] ?? AUTH_HEADER_DEFAULT_TITLE, ENT_QUOTES, 'UTF-8');
        $appName = htmlspecialchars(APP_NAME ?? AUTH_HEADER_DEFAULT_APP_NAME, ENT_QUOTES, 'UTF-8');
        return "{$title} - {$appName}";
    }
}

if (!function_exists('getFaviconDataUrl')) {
    function getFaviconDataUrl(string $emoji = '🎓'): string
    {
        return "data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>{$emoji}</text></svg>";
    }
}

if (!function_exists('getBootstrapCssUrl')) {
    function getBootstrapCssUrl(): string
    {
        return 'https://cdn.jsdelivr.net/npm/bootstrap@' . BOOTSTRAP_VERSION . '/dist/css/bootstrap.min.css';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= META_DESCRIPTION ?>">
    <meta name="author" content="<?= META_AUTHOR ?>">
    <meta name="robots" content="<?= META_ROBOTS ?>">
    <title><?= getPageTitle() ?></title>
    
    <link href="<?= getBootstrapCssUrl() ?>" rel="stylesheet" integrity="<?= BOOTSTRAP_CSS_INTEGRITY ?>" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= BOOTSTRAP_ICONS_URL ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= getFaviconDataUrl(FAVICON_EMOJI) ?>">
    <link rel="dns-prefetch" href="<?= CDN_HOST ?>">
</head>
<body>
