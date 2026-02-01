<?php

namespace App\Models;

use PDO;
use PDOException;

class User
{
    private const TABLE = 'users';
    private const TABLE_ROLES = 'roles';
    private const TABLE_ROLE_USER = 'role_user';
    private const TABLE_PERMISSIONS = 'permissions';
    private const TABLE_ROLE_PERMISSION = 'role_permission';
    
    private const COLUMN_ID = 'id';
    private const COLUMN_USERNAME = 'username';
    private const COLUMN_EMAIL = 'email';
    private const COLUMN_PASSWORD = 'password';
    private const COLUMN_FULL_NAME = 'full_name';
    private const COLUMN_IS_ACTIVE = 'is_active';
    private const COLUMN_CREATED_AT = 'created_at';
    private const COLUMN_UPDATED_AT = 'updated_at';
    
    private const COLUMN_USER_ID = 'user_id';
    private const COLUMN_ROLE_ID = 'role_id';
    private const COLUMN_PERMISSION_ID = 'permission_id';
    private const COLUMN_NAME = 'name';
    private const COLUMN_COUNT = 'count';
    
    private const DEFAULT_IS_ACTIVE = true;
    private const DEFAULT_ORDER = 'id DESC';
    
    private const LOG_ERROR_FIND = 'Error al buscar usuario ID %d: ';
    private const LOG_ERROR_FIND_USERNAME = 'Error al buscar usuario por username: ';
    private const LOG_ERROR_FIND_EMAIL = 'Error al buscar usuario por email: ';
    private const LOG_ERROR_GET_ROLES = 'Error al obtener roles del usuario: ';
    private const LOG_ERROR_HAS_ROLE = 'Error al verificar rol: ';
    private const LOG_ERROR_ASSIGN_ROLE = 'Error al asignar rol: ';
    private const LOG_ERROR_GET_PERMISSIONS = 'Error getting user permissions: ';
    private const LOG_ERROR_CHECK_PERMISSION = 'Error checking user permission: ';
    private const LOG_ERROR_ALL = 'Error al obtener usuarios: ';

    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function find(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare($this->buildFindByIdQuery());
            $stmt->execute([self::COLUMN_ID => $id]);
            return $this->fetchOneOrNull($stmt);
        } catch (PDOException $e) {
            $this->logError(sprintf(self::LOG_ERROR_FIND, $id), $e);
            return null;
        }
    }

    public function findByUsername(string $username): ?array
    {
        try {
            $stmt = $this->db->prepare($this->buildFindByUsernameQuery());
            $stmt->execute([self::COLUMN_USERNAME => $username]);
            return $this->fetchOneOrNull($stmt);
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_FIND_USERNAME, $e);
            return null;
        }
    }

    public function findByEmail(string $email): ?array
    {
        try {
            $stmt = $this->db->prepare($this->buildFindByEmailQuery());
            $stmt->execute([self::COLUMN_EMAIL => $email]);
            return $this->fetchOneOrNull($stmt);
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_FIND_EMAIL, $e);
            return null;
        }
    }

    public function create(array $data): int
    {
        $data = $this->sanitizeData($data);
        
        $stmt = $this->db->prepare($this->buildInsertQuery());
        $this->executeInsert($stmt, $data);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $data = $this->sanitizeData($data);
        
        $query = $this->buildUpdateQuery($data);
        $params = $this->buildUpdateParams($id, $data);
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    public function authenticate(string $username, string $password): ?array
    {
        $user = $this->findUserByCredentials($username);
        
        if (!$user || !$this->verifyPassword($password, $user)) {
            return null;
        }
        
        if (!$this->isUserActive($user)) {
            return null;
        }
        
        return $this->removePasswordFromUser($user);
    }

    public function getRoles(int $userId): array
    {
        try {
            $stmt = $this->db->prepare($this->buildGetRolesQuery());
            $stmt->execute([self::COLUMN_USER_ID => $userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_GET_ROLES, $e);
            return [];
        }
    }

    public function hasRole(int $userId, string $roleName): bool
    {
        try {
            $stmt = $this->db->prepare($this->buildHasRoleQuery());
            $stmt->execute([
                self::COLUMN_USER_ID => $userId,
                'role_name' => $roleName
            ]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_HAS_ROLE, $e);
            return false;
        }
    }

    public function assignRole(int $userId, int $roleId): bool
    {
        try {
            $stmt = $this->db->prepare($this->buildAssignRoleQuery());
            return $stmt->execute([
                self::COLUMN_USER_ID => $userId,
                self::COLUMN_ROLE_ID => $roleId
            ]);
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_ASSIGN_ROLE, $e);
            return false;
        }
    }

    public function getPermissions(int $userId): array
    {
        try {
            $stmt = $this->db->prepare($this->buildGetPermissionsQuery());
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_GET_PERMISSIONS, $e);
            return [];
        }
    }

    public function hasPermission(int $userId, string $permissionName): bool
    {
        try {
            $stmt = $this->db->prepare($this->buildHasPermissionQuery());
            $stmt->execute([$userId, $permissionName]);
            $result = $stmt->fetch();
            return ($result[self::COLUMN_COUNT] ?? 0) > 0;
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_CHECK_PERMISSION, $e);
            return false;
        }
    }

    public function hasAnyPermission(int $userId, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($userId, $permission)) {
                return true;
            }
        }
        return false;
    }

    public function hasAllPermissions(int $userId, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($userId, $permission)) {
                return false;
            }
        }
        return true;
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

    private function sanitizeData(array $data): array
    {
        return [
            self::COLUMN_USERNAME => sanitize_string($data[self::COLUMN_USERNAME] ?? ''),
            self::COLUMN_EMAIL => sanitize_email($data[self::COLUMN_EMAIL] ?? ''),
            self::COLUMN_PASSWORD => $data[self::COLUMN_PASSWORD] ?? '',
            self::COLUMN_FULL_NAME => sanitize_string($data[self::COLUMN_FULL_NAME] ?? ''),
            self::COLUMN_IS_ACTIVE => (bool)($data[self::COLUMN_IS_ACTIVE] ?? self::DEFAULT_IS_ACTIVE),
        ];
    }

    private function buildFindByIdQuery(): string
    {
        return "SELECT " . $this->getPublicColumns() . " FROM " . self::TABLE . " WHERE " . self::COLUMN_ID . " = :" . self::COLUMN_ID;
    }

    private function buildFindByUsernameQuery(): string
    {
        return "SELECT * FROM " . self::TABLE . " WHERE " . self::COLUMN_USERNAME . " = :" . self::COLUMN_USERNAME;
    }

    private function buildFindByEmailQuery(): string
    {
        return "SELECT * FROM " . self::TABLE . " WHERE " . self::COLUMN_EMAIL . " = :" . self::COLUMN_EMAIL;
    }

    private function buildInsertQuery(): string
    {
        return "INSERT INTO " . self::TABLE . " (" . self::COLUMN_USERNAME . ", " . self::COLUMN_EMAIL . ", " . self::COLUMN_PASSWORD . ", " . self::COLUMN_FULL_NAME . ", " . self::COLUMN_IS_ACTIVE . ")
                VALUES (:" . self::COLUMN_USERNAME . ", :" . self::COLUMN_EMAIL . ", :" . self::COLUMN_PASSWORD . ", :" . self::COLUMN_FULL_NAME . ", :" . self::COLUMN_IS_ACTIVE . ")";
    }

    private function buildUpdateQuery(array $data): string
    {
        $sql = "UPDATE " . self::TABLE . " SET 
                " . self::COLUMN_USERNAME . " = :" . self::COLUMN_USERNAME . ", 
                " . self::COLUMN_EMAIL . " = :" . self::COLUMN_EMAIL . ", 
                " . self::COLUMN_FULL_NAME . " = :" . self::COLUMN_FULL_NAME;
        
        if (!empty($data[self::COLUMN_PASSWORD])) {
            $sql .= ", " . self::COLUMN_PASSWORD . " = :" . self::COLUMN_PASSWORD;
        }
        
        $sql .= " WHERE " . self::COLUMN_ID . " = :" . self::COLUMN_ID;
        
        return $sql;
    }

    private function buildGetRolesQuery(): string
    {
        return "SELECT r.* FROM " . self::TABLE_ROLES . " r
                INNER JOIN " . self::TABLE_ROLE_USER . " ru ON r." . self::COLUMN_ID . " = ru." . self::COLUMN_ROLE_ID . "
                WHERE ru." . self::COLUMN_USER_ID . " = :" . self::COLUMN_USER_ID;
    }

    private function buildHasRoleQuery(): string
    {
        return "SELECT COUNT(*) FROM " . self::TABLE_ROLE_USER . " ru
                INNER JOIN " . self::TABLE_ROLES . " r ON ru." . self::COLUMN_ROLE_ID . " = r." . self::COLUMN_ID . "
                WHERE ru." . self::COLUMN_USER_ID . " = :" . self::COLUMN_USER_ID . " AND r." . self::COLUMN_NAME . " = :role_name";
    }

    private function buildAssignRoleQuery(): string
    {
        return "INSERT IGNORE INTO " . self::TABLE_ROLE_USER . " (" . self::COLUMN_USER_ID . ", " . self::COLUMN_ROLE_ID . ") 
                VALUES (:" . self::COLUMN_USER_ID . ", :" . self::COLUMN_ROLE_ID . ")";
    }

    private function buildGetPermissionsQuery(): string
    {
        return "SELECT DISTINCT p.*
                FROM " . self::TABLE_PERMISSIONS . " p
                INNER JOIN " . self::TABLE_ROLE_PERMISSION . " rp ON p." . self::COLUMN_ID . " = rp." . self::COLUMN_PERMISSION_ID . "
                INNER JOIN " . self::TABLE_ROLE_USER . " ru ON rp." . self::COLUMN_ROLE_ID . " = ru." . self::COLUMN_ROLE_ID . "
                WHERE ru." . self::COLUMN_USER_ID . " = ?
                ORDER BY p." . self::COLUMN_NAME . " ASC";
    }

    private function buildHasPermissionQuery(): string
    {
        return "SELECT COUNT(*) as " . self::COLUMN_COUNT . "
                FROM " . self::TABLE_PERMISSIONS . " p
                INNER JOIN " . self::TABLE_ROLE_PERMISSION . " rp ON p." . self::COLUMN_ID . " = rp." . self::COLUMN_PERMISSION_ID . "
                INNER JOIN " . self::TABLE_ROLE_USER . " ru ON rp." . self::COLUMN_ROLE_ID . " = ru." . self::COLUMN_ROLE_ID . "
                WHERE ru." . self::COLUMN_USER_ID . " = ? AND p." . self::COLUMN_NAME . " = ?";
    }

    private function buildSelectAllQuery(): string
    {
        return "SELECT " . $this->getPublicColumns() . " FROM " . self::TABLE . " ORDER BY " . self::DEFAULT_ORDER;
    }

    private function getPublicColumns(): string
    {
        return self::COLUMN_ID . ", " . self::COLUMN_USERNAME . ", " . self::COLUMN_EMAIL . ", " . self::COLUMN_FULL_NAME . ", " . self::COLUMN_IS_ACTIVE . ", " . self::COLUMN_CREATED_AT . ", " . self::COLUMN_UPDATED_AT;
    }

    private function buildUpdateParams(int $id, array $data): array
    {
        $params = [
            self::COLUMN_ID => $id,
            self::COLUMN_USERNAME => $data[self::COLUMN_USERNAME],
            self::COLUMN_EMAIL => $data[self::COLUMN_EMAIL],
            self::COLUMN_FULL_NAME => $data[self::COLUMN_FULL_NAME],
        ];
        
        if (!empty($data[self::COLUMN_PASSWORD])) {
            $params[self::COLUMN_PASSWORD] = $this->hashPassword($data[self::COLUMN_PASSWORD]);
        }
        
        return $params;
    }

    private function executeInsert($stmt, array $data): void
    {
        $stmt->execute([
            self::COLUMN_USERNAME => $data[self::COLUMN_USERNAME],
            self::COLUMN_EMAIL => $data[self::COLUMN_EMAIL],
            self::COLUMN_PASSWORD => $this->hashPassword($data[self::COLUMN_PASSWORD]),
            self::COLUMN_FULL_NAME => $data[self::COLUMN_FULL_NAME],
            self::COLUMN_IS_ACTIVE => $data[self::COLUMN_IS_ACTIVE],
        ]);
    }

    private function findUserByCredentials(string $username): ?array
    {
        $user = $this->findByUsername($username);
        
        if (!$user) {
            $user = $this->findByEmail($username);
        }
        
        return $user;
    }

    private function verifyPassword(string $password, array $user): bool
    {
        return password_verify($password, $user[self::COLUMN_PASSWORD]);
    }

    private function isUserActive(array $user): bool
    {
        return (bool) $user[self::COLUMN_IS_ACTIVE];
    }

    private function removePasswordFromUser(array $user): array
    {
        unset($user[self::COLUMN_PASSWORD]);
        return $user;
    }

    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
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
