<?php

namespace App\Models;

use PDO;
use PDOException;

class Permission
{
    private const TABLE = 'permissions';
    private const TABLE_ROLE_PERMISSION = 'role_permission';
    
    private const COLUMN_ID = 'id';
    private const COLUMN_NAME = 'name';
    private const COLUMN_DISPLAY_NAME = 'display_name';
    private const COLUMN_DESCRIPTION = 'description';
    
    private const COLUMN_ROLE_ID = 'role_id';
    private const COLUMN_PERMISSION_ID = 'permission_id';
    
    private const ORDER_BY_NAME = 'name ASC';
    
    private const DEFAULT_CATEGORY = 'other';
    private const CATEGORY_SEPARATOR = '.';
    
    private const LOG_ERROR_ALL = 'Error getting all permissions: ';
    private const LOG_ERROR_FIND = 'Error finding permission: ';
    private const LOG_ERROR_FIND_NAME = 'Error finding permission by name: ';
    private const LOG_ERROR_BY_ROLE = 'Error getting permissions by role: ';
    private const LOG_ERROR_CREATE = 'Error creating permission: ';
    private const LOG_ERROR_UPDATE = 'Error updating permission: ';
    private const LOG_ERROR_DELETE = 'Error deleting permission: ';
    private const LOG_ERROR_SYNC = 'Error syncing role permissions: ';

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
            $stmt->execute([$id]);
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
            $stmt->execute([$name]);
            return $this->fetchOneOrNull($stmt);
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_FIND_NAME, $e);
            return null;
        }
    }

    public function getByRole(int $roleId): array
    {
        try {
            $stmt = $this->db->prepare($this->buildGetByRoleQuery());
            $stmt->execute([$roleId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_BY_ROLE, $e);
            return [];
        }
    }

    public function getAllGrouped(): array
    {
        $permissions = $this->all();
        return $this->groupPermissionsByCategory($permissions);
    }

    public function create(array $data): int|false
    {
        try {
            $stmt = $this->db->prepare($this->buildInsertQuery());
            $this->executeInsert($stmt, $data);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_CREATE, $e);
            return false;
        }
    }

    public function update(int $id, array $data): bool
    {
        try {
            $stmt = $this->db->prepare($this->buildUpdateQuery());
            return $this->executeUpdate($stmt, $data, $id);
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_UPDATE, $e);
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare($this->buildDeleteQuery());
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_DELETE, $e);
            return false;
        }
    }

    public function syncRolePermissions(int $roleId, array $permissionIds): bool
    {
        try {
            $this->db->beginTransaction();
            $this->deleteRolePermissions($roleId);
            $this->insertRolePermissions($roleId, $permissionIds);
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->logError(self::LOG_ERROR_SYNC, $e);
            return false;
        }
    }

    private function buildSelectAllQuery(): string
    {
        return "SELECT * FROM " . self::TABLE . " ORDER BY " . self::ORDER_BY_NAME;
    }

    private function buildFindByIdQuery(): string
    {
        return "SELECT * FROM " . self::TABLE . " WHERE " . self::COLUMN_ID . " = ?";
    }

    private function buildFindByNameQuery(): string
    {
        return "SELECT * FROM " . self::TABLE . " WHERE " . self::COLUMN_NAME . " = ?";
    }

    private function buildGetByRoleQuery(): string
    {
        return "
            SELECT p.* 
            FROM " . self::TABLE . " p
            INNER JOIN " . self::TABLE_ROLE_PERMISSION . " rp ON p." . self::COLUMN_ID . " = rp." . self::COLUMN_PERMISSION_ID . "
            WHERE rp." . self::COLUMN_ROLE_ID . " = ?
            ORDER BY p." . self::COLUMN_NAME . " ASC
        ";
    }

    private function buildInsertQuery(): string
    {
        return "
            INSERT INTO " . self::TABLE . " (" . self::COLUMN_NAME . ", " . self::COLUMN_DISPLAY_NAME . ", " . self::COLUMN_DESCRIPTION . ")
            VALUES (?, ?, ?)
        ";
    }

    private function buildUpdateQuery(): string
    {
        return "
            UPDATE " . self::TABLE . " 
            SET " . self::COLUMN_DISPLAY_NAME . " = ?, " . self::COLUMN_DESCRIPTION . " = ?
            WHERE " . self::COLUMN_ID . " = ?
        ";
    }

    private function buildDeleteQuery(): string
    {
        return "DELETE FROM " . self::TABLE . " WHERE " . self::COLUMN_ID . " = ?";
    }

    private function fetchOneOrNull($stmt): ?array
    {
        $result = $stmt->fetch();
        return $result ?: null;
    }

    private function groupPermissionsByCategory(array $permissions): array
    {
        $grouped = [];

        foreach ($permissions as $permission) {
            $category = $this->extractCategory($permission[self::COLUMN_NAME]);
            $grouped[$category][] = $permission;
        }

        return $grouped;
    }

    private function extractCategory(string $name): string
    {
        $parts = explode(self::CATEGORY_SEPARATOR, $name);
        return $parts[0] ?? self::DEFAULT_CATEGORY;
    }

    private function executeInsert($stmt, array $data): void
    {
        $stmt->execute([
            $data[self::COLUMN_NAME],
            $data[self::COLUMN_DISPLAY_NAME],
            $data[self::COLUMN_DESCRIPTION] ?? null
        ]);
    }

    private function executeUpdate($stmt, array $data, int $id): bool
    {
        return $stmt->execute([
            $data[self::COLUMN_DISPLAY_NAME],
            $data[self::COLUMN_DESCRIPTION] ?? null,
            $id
        ]);
    }

    private function deleteRolePermissions(int $roleId): void
    {
        $stmt = $this->db->prepare(
            "DELETE FROM " . self::TABLE_ROLE_PERMISSION . " WHERE " . self::COLUMN_ROLE_ID . " = ?"
        );
        $stmt->execute([$roleId]);
    }

    private function insertRolePermissions(int $roleId, array $permissionIds): void
    {
        if (empty($permissionIds)) {
            return;
        }

        $stmt = $this->db->prepare(
            "INSERT INTO " . self::TABLE_ROLE_PERMISSION . " (" . self::COLUMN_ROLE_ID . ", " . self::COLUMN_PERMISSION_ID . ")
            VALUES (?, ?)"
        );

        foreach ($permissionIds as $permissionId) {
            $stmt->execute([$roleId, $permissionId]);
        }
    }

    private function logError(string $message, PDOException $e): void
    {
        app_log($message . $e->getMessage(), LOG_LEVEL_ERROR);
    }
}
