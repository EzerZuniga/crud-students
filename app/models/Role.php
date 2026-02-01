<?php

namespace App\Models;

use PDO;
use PDOException;

class Role
{
    private const TABLE = 'roles';
    
    private const COLUMN_ID = 'id';
    private const COLUMN_NAME = 'name';
    
    private const ORDER_BY_NAME = 'name ASC';
    
    private const LOG_ERROR_ALL = 'Error al obtener roles: ';
    private const LOG_ERROR_FIND = 'Error al buscar rol: ';
    private const LOG_ERROR_FIND_NAME = 'Error al buscar rol por nombre: ';

    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        try {
            $stmt = $this->db->query($this->buildSelectAllQuery());
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_ALL, $e);
            return [];
        }
    }

    public function find(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare($this->buildFindByIdQuery());
            $stmt->execute([self::COLUMN_ID => $id]);
            return $this->fetchOneOrNull($stmt);
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_FIND, $e);
            return null;
        }
    }

    public function findByName(string $name): ?array
    {
        try {
            $stmt = $this->db->prepare($this->buildFindByNameQuery());
            $stmt->execute([self::COLUMN_NAME => $name]);
            return $this->fetchOneOrNull($stmt);
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_FIND_NAME, $e);
            return null;
        }
    }

    private function buildSelectAllQuery(): string
    {
        return "SELECT * FROM " . self::TABLE . " ORDER BY " . self::ORDER_BY_NAME;
    }

    private function buildFindByIdQuery(): string
    {
        return "SELECT * FROM " . self::TABLE . " WHERE " . self::COLUMN_ID . " = :" . self::COLUMN_ID;
    }

    private function buildFindByNameQuery(): string
    {
        return "SELECT * FROM " . self::TABLE . " WHERE " . self::COLUMN_NAME . " = :" . self::COLUMN_NAME;
    }

    private function fetchOneOrNull($stmt): ?array
    {
        $result = $stmt->fetch();
        return $result ?: null;
    }

    private function logError(string $message, PDOException $e): void
    {
        app_log($message . $e->getMessage(), LOG_LEVEL_ERROR);
    }
}
