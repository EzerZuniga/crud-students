<?php

declare(strict_types=1);

const EXAMPLE_DB_DRIVER = 'mysql';
const EXAMPLE_DB_HOST = '127.0.0.1';
const EXAMPLE_DB_PORT = '3306';
const EXAMPLE_DB_NAME = 'crud_students';
const EXAMPLE_DB_USERNAME = 'root';
const EXAMPLE_DB_PASSWORD = '';
const EXAMPLE_DB_CHARSET = 'utf8mb4';
const EXAMPLE_DB_COLLATION = 'utf8mb4_unicode_ci';

const EXAMPLE_DB_OPTIONS = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_PERSISTENT => false,
];

function buildExampleDatabaseConfiguration(): array
{
    return [
        'driver' => EXAMPLE_DB_DRIVER,
        'host' => EXAMPLE_DB_HOST,
        'port' => EXAMPLE_DB_PORT,
        'database' => EXAMPLE_DB_NAME,
        'username' => EXAMPLE_DB_USERNAME,
        'password' => EXAMPLE_DB_PASSWORD,
        'charset' => EXAMPLE_DB_CHARSET,
        'collation' => EXAMPLE_DB_COLLATION,
        'options' => EXAMPLE_DB_OPTIONS,
    ];
}

return buildExampleDatabaseConfiguration();
