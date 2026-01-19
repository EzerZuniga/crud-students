<?php
/**
 * Permission Model
 * Gestiona los permisos del sistema
 */

namespace App\Models;

use PDO;
use PDOException;

class Permission
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Obtiene todos los permisos
     *
     * @return array
     */
    public function all(): array
    {
        try {
            $stmt = $this->db->query("
                SELECT * FROM permissions 
                ORDER BY name ASC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            app_log("Error getting all permissions: " . $e->getMessage(), 'error');
            return [];
        }
    }

    /**
     * Busca un permiso por ID
     *
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM permissions 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            app_log("Error finding permission: " . $e->getMessage(), 'error');
            return null;
        }
    }

    /**
     * Busca un permiso por nombre
     *
     * @param string $name
     * @return array|null
     */
    public function findByName(string $name): ?array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM permissions 
                WHERE name = ?
            ");
            $stmt->execute([$name]);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            app_log("Error finding permission by name: " . $e->getMessage(), 'error');
            return null;
        }
    }

    /**
     * Obtiene todos los permisos de un rol
     *
     * @param int $roleId
     * @return array
     */
    public function getByRole(int $roleId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT p.* 
                FROM permissions p
                INNER JOIN role_permission rp ON p.id = rp.permission_id
                WHERE rp.role_id = ?
                ORDER BY p.name ASC
            ");
            $stmt->execute([$roleId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            app_log("Error getting permissions by role: " . $e->getMessage(), 'error');
            return [];
        }
    }

    /**
     * Obtiene todos los permisos agrupados por categoría
     *
     * @return array
     */
    public function getAllGrouped(): array
    {
        $permissions = $this->all();
        $grouped = [];

        foreach ($permissions as $permission) {
            // Extraer categoría del nombre (ej: 'students.create' -> 'students')
            $parts = explode('.', $permission['name']);
            $category = $parts[0] ?? 'other';
            
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            
            $grouped[$category][] = $permission;
        }

        return $grouped;
    }

    /**
     * Crea un nuevo permiso
     *
     * @param array $data
     * @return int|false ID del permiso creado o false
     */
    public function create(array $data): int|false
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO permissions (name, display_name, description)
                VALUES (?, ?, ?)
            ");
            
            $stmt->execute([
                $data['name'],
                $data['display_name'],
                $data['description'] ?? null
            ]);

            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            app_log("Error creating permission: " . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Actualiza un permiso
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE permissions 
                SET display_name = ?, description = ?
                WHERE id = ?
            ");
            
            return $stmt->execute([
                $data['display_name'],
                $data['description'] ?? null,
                $id
            ]);
        } catch (PDOException $e) {
            app_log("Error updating permission: " . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Elimina un permiso
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM permissions WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            app_log("Error deleting permission: " . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Sincroniza los permisos de un rol
     *
     * @param int $roleId
     * @param array $permissionIds Array de IDs de permisos
     * @return bool
     */
    public function syncRolePermissions(int $roleId, array $permissionIds): bool
    {
        try {
            $this->db->beginTransaction();

            // Eliminar permisos existentes del rol
            $stmt = $this->db->prepare("DELETE FROM role_permission WHERE role_id = ?");
            $stmt->execute([$roleId]);

            // Insertar nuevos permisos
            if (!empty($permissionIds)) {
                $stmt = $this->db->prepare("
                    INSERT INTO role_permission (role_id, permission_id)
                    VALUES (?, ?)
                ");

                foreach ($permissionIds as $permissionId) {
                    $stmt->execute([$roleId, $permissionId]);
                }
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            app_log("Error syncing role permissions: " . $e->getMessage(), 'error');
            return false;
        }
    }
}
