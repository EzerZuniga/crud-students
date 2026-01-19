<?php
/**
 * Modelo User
 * Representa la entidad de usuario para autenticación
 */

namespace App\Models;

use PDO;
use PDOException;

class User
{
    private PDO $db;
    private string $table = 'users';

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Busca un usuario por ID
     *
     * @param int $id ID del usuario
     * @return array|null Datos del usuario o null
     */
    public function find(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, username, email, full_name, is_active, created_at, updated_at 
                 FROM {$this->table} 
                 WHERE id = :id"
            );
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch();
            
            return $result ?: null;
        } catch (PDOException $e) {
            app_log("Error al buscar usuario ID {$id}: " . $e->getMessage(), 'error');
            return null;
        }
    }

    /**
     * Busca un usuario por username
     *
     * @param string $username Username del usuario
     * @return array|null Datos del usuario o null
     */
    public function findByUsername(string $username): ?array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM {$this->table} WHERE username = :username"
            );
            $stmt->execute(['username' => $username]);
            $result = $stmt->fetch();
            
            return $result ?: null;
        } catch (PDOException $e) {
            app_log("Error al buscar usuario por username: " . $e->getMessage(), 'error');
            return null;
        }
    }

    /**
     * Busca un usuario por email
     *
     * @param string $email Email del usuario
     * @return array|null Datos del usuario o null
     */
    public function findByEmail(string $email): ?array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM {$this->table} WHERE email = :email"
            );
            $stmt->execute(['email' => $email]);
            $result = $stmt->fetch();
            
            return $result ?: null;
        } catch (PDOException $e) {
            app_log("Error al buscar usuario por email: " . $e->getMessage(), 'error');
            return null;
        }
    }

    /**
     * Crea un nuevo usuario
     *
     * @param array $data Datos del usuario
     * @return int ID del usuario creado
     * @throws PDOException
     */
    public function create(array $data): int
    {
        $data = $this->sanitize($data);
        
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (username, email, password, full_name, is_active) 
             VALUES (:username, :email, :password, :full_name, :is_active)"
        );
        
        $stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'full_name' => $data['full_name'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualiza un usuario
     *
     * @param int $id ID del usuario
     * @param array $data Nuevos datos
     * @return bool True si se actualizó
     * @throws PDOException
     */
    public function update(int $id, array $data): bool
    {
        $data = $this->sanitize($data);
        
        $sql = "UPDATE {$this->table} SET 
                username = :username, 
                email = :email, 
                full_name = :full_name";
        
        $params = [
            'id' => $id,
            'username' => $data['username'],
            'email' => $data['email'],
            'full_name' => $data['full_name'],
        ];
        
        // Solo actualizar password si se proporciona
        if (!empty($data['password'])) {
            $sql .= ", password = :password";
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Verifica las credenciales del usuario
     *
     * @param string $username Username o email
     * @param string $password Contraseña sin hash
     * @return array|null Usuario si las credenciales son válidas
     */
    public function authenticate(string $username, string $password): ?array
    {
        // Buscar por username o email
        $user = $this->findByUsername($username);
        
        if (!$user) {
            $user = $this->findByEmail($username);
        }
        
        if (!$user) {
            return null;
        }
        
        // Verificar contraseña
        if (!password_verify($password, $user['password'])) {
            return null;
        }
        
        // Verificar si está activo
        if (!$user['is_active']) {
            return null;
        }
        
        // No devolver el password
        unset($user['password']);
        
        return $user;
    }

    /**
     * Obtiene los roles de un usuario
     *
     * @param int $userId ID del usuario
     * @return array Lista de roles
     */
    public function getRoles(int $userId): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT r.* FROM roles r
                 INNER JOIN role_user ru ON r.id = ru.role_id
                 WHERE ru.user_id = :user_id"
            );
            $stmt->execute(['user_id' => $userId]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            app_log("Error al obtener roles del usuario: " . $e->getMessage(), 'error');
            return [];
        }
    }

    /**
     * Verifica si un usuario tiene un rol específico
     *
     * @param int $userId ID del usuario
     * @param string $roleName Nombre del rol
     * @return bool True si tiene el rol
     */
    public function hasRole(int $userId, string $roleName): bool
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM role_user ru
                 INNER JOIN roles r ON ru.role_id = r.id
                 WHERE ru.user_id = :user_id AND r.name = :role_name"
            );
            $stmt->execute([
                'user_id' => $userId,
                'role_name' => $roleName
            ]);
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            app_log("Error al verificar rol: " . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Asigna un rol a un usuario
     *
     * @param int $userId ID del usuario
     * @param int $roleId ID del rol
     * @return bool True si se asignó
     */
    public function assignRole(int $userId, int $roleId): bool
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT IGNORE INTO role_user (user_id, role_id) VALUES (:user_id, :role_id)"
            );
            return $stmt->execute([
                'user_id' => $userId,
                'role_id' => $roleId
            ]);
        } catch (PDOException $e) {
            app_log("Error al asignar rol: " . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Obtiene todos los permisos del usuario (a través de sus roles)
     *
     * @param int $userId
     * @return array
     */
    public function getPermissions(int $userId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT DISTINCT p.*
                FROM permissions p
                INNER JOIN role_permission rp ON p.id = rp.permission_id
                INNER JOIN role_user ru ON rp.role_id = ru.role_id
                WHERE ru.user_id = ?
                ORDER BY p.name ASC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            app_log("Error getting user permissions: " . $e->getMessage(), 'error');
            return [];
        }
    }

    /**
     * Verifica si el usuario tiene un permiso específico
     *
     * @param int $userId
     * @param string $permissionName
     * @return bool
     */
    public function hasPermission(int $userId, string $permissionName): bool
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM permissions p
                INNER JOIN role_permission rp ON p.id = rp.permission_id
                INNER JOIN role_user ru ON rp.role_id = ru.role_id
                WHERE ru.user_id = ? AND p.name = ?
            ");
            $stmt->execute([$userId, $permissionName]);
            $result = $stmt->fetch();
            
            return ($result['count'] ?? 0) > 0;
        } catch (PDOException $e) {
            app_log("Error checking user permission: " . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Verifica si el usuario tiene cualquiera de los permisos especificados
     *
     * @param int $userId
     * @param array $permissions
     * @return bool
     */
    public function hasAnyPermission(int $userId, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($userId, $permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica si el usuario tiene todos los permisos especificados
     *
     * @param int $userId
     * @param array $permissions
     * @return bool
     */
    public function hasAllPermissions(int $userId, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($userId, $permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Sanitiza los datos del usuario
     *
     * @param array $data Datos a sanitizar
     * @return array Datos sanitizados
     */
    private function sanitize(array $data): array
    {
        return [
            'username' => sanitize_string($data['username'] ?? ''),
            'email' => sanitize_email($data['email'] ?? ''),
            'password' => $data['password'] ?? '',
            'full_name' => sanitize_string($data['full_name'] ?? ''),
            'is_active' => (bool)($data['is_active'] ?? true),
        ];
    }

    /**
     * Obtiene todos los usuarios
     *
     * @return array Lista de usuarios
     */
    public function all(): array
    {
        try {
            $stmt = $this->db->query(
                "SELECT id, username, email, full_name, is_active, created_at 
                 FROM {$this->table} 
                 ORDER BY id DESC"
            );
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            app_log("Error al obtener usuarios: " . $e->getMessage(), 'error');
            return [];
        }
    }
}
