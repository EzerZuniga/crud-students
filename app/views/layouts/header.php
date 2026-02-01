<?php

declare(strict_types=1);

// Constantes específicas de UI del header (no duplicadas en constants.php)
const META_DESCRIPTION = 'CRUD de estudiantes con PHP - Gestión moderna y eficiente';
const META_AUTHOR = 'Jose';
const BS_THEME = 'light';

const NAVBAR_CLASS = 'navbar navbar-expand-lg';
const NAVBAR_BRAND_CLASS = 'navbar-brand';
const NAVBAR_TOGGLER_CLASS = 'navbar-toggler';
const NAVBAR_COLLAPSE_ID = 'navbarNav';
const NAV_LINK_ACTIVE_CLASS = 'nav-link active';
const NAV_LINK_CLASS = 'nav-link';
const NAV_LINK_DROPDOWN_CLASS = 'nav-link dropdown-toggle';

const ICON_MORTARBOARD = 'bi bi-mortarboard-fill';
const ICON_HOME = 'bi bi-house-fill me-1';
const ICON_PERSON_PLUS = 'bi bi-person-plus-fill me-1';
const ICON_PERSON_CIRCLE = 'bi bi-person-circle me-1';
const ICON_ENVELOPE = 'bi bi-envelope me-1';
const ICON_SHIELD_CHECK = 'bi bi-shield-fill-check';
const ICON_LOGOUT = 'bi bi-box-arrow-right me-1';
const ICON_LOGIN = 'bi bi-box-arrow-in-right me-1';
const ICON_SUCCESS = 'bi bi-check-circle-fill me-2';
const ICON_ERROR = 'bi bi-exclamation-triangle-fill me-2';

const BADGE_ADMIN_CLASS = 'badge bg-danger ms-1';
const DROPDOWN_MENU_CLASS = 'dropdown-menu dropdown-menu-end shadow-sm border-0';
const DROPDOWN_ITEM_TEXT_CLASS = 'dropdown-item-text';
const DROPDOWN_ITEM_CLASS = 'dropdown-item';
const DROPDOWN_ITEM_DANGER_CLASS = 'dropdown-item text-danger';
const DROPDOWN_DIVIDER_CLASS = 'dropdown-divider';

const ALERT_SUCCESS_CLASS = 'alert alert-success alert-dismissible fade show';
const ALERT_DANGER_CLASS = 'alert alert-danger alert-dismissible fade show';
const ALERT_CONTAINER_CLASS = 'container mt-3';

const BTN_CLOSE_CLASS = 'btn-close';
const BODY_CLASS = 'd-flex flex-column min-vh-100';
const MAIN_CLASS = 'flex-fill py-4';

const TEXT_INICIO = 'Inicio';
const TEXT_NUEVO_ESTUDIANTE = 'Nuevo Estudiante';
const TEXT_ADMIN = 'Admin';
const TEXT_REGISTRAR_USUARIO = 'Registrar Usuario';
const TEXT_CERRAR_SESION = 'Cerrar Sesión';
const TEXT_INICIAR_SESION = 'Iniciar Sesión';
const TEXT_TOGGLE_NAVIGATION = 'Toggle navigation';

const ROUTE_STUDENTS_INDEX = 'students.index';
const ROUTE_STUDENTS_CREATE = 'students.create';
const ROUTE_AUTH_REGISTER = 'auth.register';
const ROUTE_AUTH_LOGOUT = 'auth.logout';
const ROUTE_AUTH_LOGIN = 'auth.login';

if (!function_exists('getPageTitle')) {
    function getPageTitle(): string
    {
        $appName = defined('APP_NAME') ? constant('APP_NAME') : APP_DEFAULT_NAME;
        return sprintf('%s - %s', e($appName), APP_TITLE_SUFFIX);
    }
}

if (!function_exists('getBootstrapCssUrl')) {
    function getBootstrapCssUrl(): string
    {
        return sprintf(
            'https://cdn.jsdelivr.net/npm/bootstrap@%s/dist/css/bootstrap.min.css',
            BOOTSTRAP_VERSION
        );
    }
}

if (!function_exists('getBootstrapIconsUrl')) {
    function getBootstrapIconsUrl(): string
    {
        return sprintf(
            'https://cdn.jsdelivr.net/npm/bootstrap-icons@%s/font/bootstrap-icons.css',
            BOOTSTRAP_ICONS_VERSION
        );
    }
}

if (!function_exists('getFaviconDataUrl')) {
    function getFaviconDataUrl(): string
    {
        return "data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>";
    }
}

if (!function_exists('isCurrentRoute')) {
    function isCurrentRoute(string $uri): bool
    {
        return ($_SERVER['REQUEST_URI'] ?? '') === $uri;
    }
}

if (!function_exists('getNavLinkClass')) {
    function getNavLinkClass(string $uri): string
    {
        return isCurrentRoute($uri) ? NAV_LINK_ACTIVE_CLASS : NAV_LINK_CLASS;
    }
}

if (!function_exists('renderNavLink')) {
    function renderNavLink(string $route, string $icon, string $text, string $uri = '/'): string
    {
        $class = getNavLinkClass($uri);
        $url = htmlspecialchars(route($route), ENT_QUOTES, 'UTF-8');
        $escapedText = e($text);
    
        return sprintf(
            '<a class="%s" href="%s"><i class="%s"></i> %s</a>',
            $class,
            $url,
            $icon,
            $escapedText
        );
    }
}

if (!function_exists('renderUserEmail')) {
    function renderUserEmail(): string
    {
        $user = auth_user();
        $email = $user['email'] ?? '';
        
        return sprintf(
            '<small class="text-muted"><i class="%s"></i>%s</small>',
            ICON_ENVELOPE,
            e($email)
        );
    }
}

if (!function_exists('renderAdminBadge')) {
    function renderAdminBadge(): string
    {
        return sprintf(
            '<span class="%s"><i class="%s"></i> %s</span>',
            BADGE_ADMIN_CLASS,
            ICON_SHIELD_CHECK,
            TEXT_ADMIN
        );
    }
}

if (!function_exists('renderDropdownLink')) {
    function renderDropdownLink(string $route, string $icon, string $text, string $extraClass = ''): string
    {
        $class = $extraClass !== '' ? sprintf('%s %s', DROPDOWN_ITEM_CLASS, $extraClass) : DROPDOWN_ITEM_CLASS;
        $url = htmlspecialchars(route($route), ENT_QUOTES, 'UTF-8');
        
        return sprintf(
            '<a class="%s" href="%s"><i class="%s"></i> %s</a>',
            $class,
            $url,
            $icon,
            e($text)
        );
    }
}

if (!function_exists('renderFlashMessage')) {
    function renderFlashMessage(string $type, string $message, string $icon, string $alertClass): string
    {
        return sprintf(
            '<div class="%s"><div class="%s" role="alert"><i class="%s"></i>%s<button type="button" class="%s" data-bs-dismiss="alert" aria-label="Close"></button></div></div>',
            ALERT_CONTAINER_CLASS,
            $alertClass,
            $icon,
            e($message),
            BTN_CLOSE_CLASS
        );
    }
}

if (!function_exists('renderSuccessFlash')) {
    function renderSuccessFlash(): string
    {
        $success = flash(FLASH_SUCCESS);
        return $success ? renderFlashMessage(FLASH_SUCCESS, $success, ICON_SUCCESS, ALERT_SUCCESS_CLASS) : '';
    }
}

if (!function_exists('renderErrorFlash')) {
    function renderErrorFlash(): string
    {
        $error = flash(FLASH_ERROR);
        return $error ? renderFlashMessage(FLASH_ERROR, $error, ICON_ERROR, ALERT_DANGER_CLASS) : '';
    }
}

?><!DOCTYPE html>
<html lang="<?= APP_LANG ?>" data-bs-theme="<?= BS_THEME ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= META_DESCRIPTION ?>">
    <meta name="author" content="<?= META_AUTHOR ?>">
    <title><?= getPageTitle() ?></title>
    
    <link href="<?= getBootstrapCssUrl() ?>" rel="stylesheet" integrity="<?= BOOTSTRAP_CSS_INTEGRITY ?>" crossorigin="anonymous">
    
    <link rel="stylesheet" href="<?= getBootstrapIconsUrl() ?>">
    
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    
    <link rel="icon" type="image/svg+xml" href="<?= getFaviconDataUrl() ?>">
</head>
<body class="<?= BODY_CLASS ?>">
    <nav class="<?= NAVBAR_CLASS ?>">
        <div class="container">
            <a class="<?= NAVBAR_BRAND_CLASS ?>" href="<?= route(ROUTE_STUDENTS_INDEX) ?>">
                <i class="<?= ICON_MORTARBOARD ?>"></i>
                <span><?= APP_DEFAULT_NAME ?></span>
            </a>
            <button class="<?= NAVBAR_TOGGLER_CLASS ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?= NAVBAR_COLLAPSE_ID ?>" aria-controls="<?= NAVBAR_COLLAPSE_ID ?>" aria-expanded="false" aria-label="<?= TEXT_TOGGLE_NAVIGATION ?>">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="<?= NAVBAR_COLLAPSE_ID ?>">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <?= renderNavLink(ROUTE_STUDENTS_INDEX, ICON_HOME, TEXT_INICIO, '/') ?>
                    </li>
                    <li class="nav-item">
                        <a class="<?= NAV_LINK_CLASS ?>" href="<?= route(ROUTE_STUDENTS_CREATE) ?>">
                            <i class="<?= ICON_PERSON_PLUS ?>"></i> <?= TEXT_NUEVO_ESTUDIANTE ?>
                        </a>
                    </li>
                    
                    <?php if (auth_check()): ?>
                        <li class="nav-item dropdown">
                            <a class="<?= NAV_LINK_DROPDOWN_CLASS ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="<?= ICON_PERSON_CIRCLE ?>"></i>
                                <?= e(auth_name()) ?>
                            </a>
                            <ul class="<?= DROPDOWN_MENU_CLASS ?>">
                                <li>
                                    <span class="<?= DROPDOWN_ITEM_TEXT_CLASS ?>">
                                        <?= renderUserEmail() ?>
                                    </span>
                                </li>
                                <li><hr class="<?= DROPDOWN_DIVIDER_CLASS ?>"></li>
                                <?php if (auth_is_admin()): ?>
                                    <li>
                                        <span class="<?= DROPDOWN_ITEM_TEXT_CLASS ?>">
                                            <?= renderAdminBadge() ?>
                                        </span>
                                    </li>
                                    <li><hr class="<?= DROPDOWN_DIVIDER_CLASS ?>"></li>
                                    <li>
                                        <?= renderDropdownLink(ROUTE_AUTH_REGISTER, ICON_PERSON_PLUS, TEXT_REGISTRAR_USUARIO) ?>
                                    </li>
                                    <li><hr class="<?= DROPDOWN_DIVIDER_CLASS ?>"></li>
                                <?php endif; ?>
                                <li>
                                    <?= renderDropdownLink(ROUTE_AUTH_LOGOUT, ICON_LOGOUT, TEXT_CERRAR_SESION, 'text-danger') ?>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="<?= NAV_LINK_CLASS ?>" href="<?= route(ROUTE_AUTH_LOGIN) ?>">
                                <i class="<?= ICON_LOGIN ?>"></i> <?= TEXT_INICIAR_SESION ?>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <?= renderSuccessFlash() ?>

    <?= renderErrorFlash() ?>

    <main class="<?= MAIN_CLASS ?>">
        <div class="container">
