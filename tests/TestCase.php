<?php
/**
 * Base Test Case
 * Clase base para todos los tests
 */

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use PDO;

abstract class TestCase extends BaseTestCase
{
    protected ?PDO $db = null;

    /**
     * Configura el entorno de pruebas antes de cada test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
    }

    /**
     * Limpia el entorno después de cada test
     */
    protected function tearDown(): void
    {
        $this->db = null;
        parent::tearDown();
    }

    /**
     * Configura la base de datos de pruebas
     */
    protected function setupDatabase(): void
    {
        $host = getenv('DB_HOST') ?: 'localhost';
        $dbname = getenv('DB_NAME') ?: 'crud_students_test';
        $user = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: '';

        try {
            $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
            $this->db = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (\PDOException $e) {
            $this->markTestSkipped('No se pudo conectar a la base de datos de pruebas: ' . $e->getMessage());
        }
    }

    /**
     * Limpia una tabla
     */
    protected function cleanTable(string $table): void
    {
        if ($this->db) {
            $this->db->exec("TRUNCATE TABLE {$table}");
        }
    }

    /**
     * Inserta datos de prueba
     */
    protected function insertTestData(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $stmt = $this->db->prepare(
            "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})"
        );
        $stmt->execute(array_values($data));
        
        return (int) $this->db->lastInsertId();
    }
}
