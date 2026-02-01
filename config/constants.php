<?php

declare(strict_types=1);

/**
 * Constantes Globales de la Aplicación
 * 
 * Este archivo centraliza todas las constantes compartidas del proyecto
 * para evitar duplicaciones y facilitar el mantenimiento.
 */

// ============================================================================
// APLICACIÓN
// ============================================================================
const APP_DEFAULT_NAME = 'CRUD Students';
const APP_TITLE_SUFFIX = 'Sistema de Gestión';
const APP_LANG = 'es';
const APP_CHARSET = 'UTF-8';
const APP_DEFAULT_TIMEZONE = 'America/Lima';

// ============================================================================
// ENTORNOS
// ============================================================================
const ENV_PRODUCTION = 'production';
const ENV_DEVELOPMENT = 'development';
const ENV_TESTING = 'testing';

// ============================================================================
// HTTP
// ============================================================================
const HTTP_METHOD_GET = 'GET';
const HTTP_METHOD_POST = 'POST';
const HTTP_STATUS_OK = 200;
const HTTP_STATUS_REDIRECT = 302;
const HTTP_STATUS_NOT_FOUND = 404;
const HTTP_STATUS_INTERNAL_ERROR = 500;

// ============================================================================
// RUTAS
// ============================================================================
const ROUTE_ROOT = '/';
const ROUTE_LOGIN = '/login';
const ROUTE_REGISTER = '/register';
const ROUTE_LOGOUT = '/logout';
const ROUTE_STUDENTS = '/students';
const ROUTE_STUDENTS_CREATE = '/students/create';
const ROUTE_STUDENTS_SHOW = '/students/{id}';
const ROUTE_STUDENTS_EDIT = '/students/{id}/edit';
const ROUTE_STUDENTS_UPDATE = '/students/{id}';
const ROUTE_STUDENTS_DELETE = '/students/{id}/delete';

// ============================================================================
// BASE DE DATOS
// ============================================================================
const DB_DEFAULT_DRIVER = 'mysql';
const DB_DEFAULT_HOST = '127.0.0.1';
const DB_DEFAULT_PORT = '3306';
const DB_DEFAULT_NAME = 'crud_students';
const DB_DEFAULT_USER = 'root';
const DB_DEFAULT_PASSWORD = '';
const DB_DEFAULT_CHARSET = 'utf8mb4';
const DB_DEFAULT_COLLATION = 'utf8mb4_unicode_ci';

// ============================================================================
// VALIDACIÓN
// ============================================================================
const MAX_NAME_LENGTH = 100;
const MAX_EMAIL_LENGTH = 120;
const MAX_PHONE_LENGTH = 50;

// ============================================================================
// PAGINACIÓN
// ============================================================================
const DEFAULT_ITEMS_PER_PAGE = 10;

// ============================================================================
// SESIÓN
// ============================================================================
const SESSION_KEY_FLASH = 'flash';
const SESSION_KEY_OLD = 'old';
const SESSION_KEY_AUTH_USER = 'auth_user';
const SESSION_LIFETIME = 7200;

// ============================================================================
// LOGGING
// ============================================================================
const LOG_LEVEL_INFO = 'info';
const LOG_LEVEL_WARNING = 'warning';
const LOG_LEVEL_ERROR = 'error';
const LOG_LEVEL_NOTICE = 'notice';
const LOG_DIR_PERMISSIONS = 0777;
const LOG_TIMESTAMP_FORMAT = 'Y-m-d H:i:s';
const LOG_FILENAME = 'app.log';
const ERROR_LOG_FILE = 'php-errors.log';

// ============================================================================
// ERRORES
// ============================================================================
const ERROR_TYPE_ERROR = 'ERROR';
const ERROR_TYPE_WARNING = 'WARNING';
const ERROR_TYPE_NOTICE = 'NOTICE';
const ERROR_TYPE_UNKNOWN = 'UNKNOWN';

const FATAL_ERROR_TYPES = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR];
const PRODUCTION_ERROR_LEVEL = E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT;
const DEVELOPMENT_ERROR_LEVEL = E_ALL;

// ============================================================================
// BOOTSTRAP CDN
// ============================================================================
const BOOTSTRAP_VERSION = '5.3.2';
const BOOTSTRAP_ICONS_VERSION = '1.11.2';
const BOOTSTRAP_CSS_INTEGRITY = 'sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN';

// ============================================================================
// ROLES Y PERMISOS
// ============================================================================
const ROLE_ADMIN = 'admin';
const ROLE_USER = 'user';

// ============================================================================
// FLASH MESSAGES
// ============================================================================
const FLASH_SUCCESS = 'success';
const FLASH_ERROR = 'error';
const FLASH_WARNING = 'warning';
const FLASH_INFO = 'info';

// ============================================================================
// FORMATOS
// ============================================================================
const DATE_FORMAT_DEFAULT = 'd/m/Y H:i';
const DATE_FORMAT_SHORT = 'd/m/Y';
const DATE_FORMAT_ISO = 'Y-m-d';

// ============================================================================
// PROTOCOLOS
// ============================================================================
const PROTOCOL_HTTP = 'http';
const PROTOCOL_HTTPS = 'https';
const HTTPS_ON = 'on';
