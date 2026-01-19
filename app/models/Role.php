<?php
/**
 * Modelo Role
 * Representa la entidad de rol para control de acceso
 */

namespace App\Models;

use PDO;
use PDOException;

class Role
{
    private PDO $db;
    private string $table = 'roles';

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Obtiene todos los roles
     *
     * @return array Lista de roles
     */
    public function all(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY name");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            app_log("Error al obtener roles: " . $e->getMessage(), 'error');
            return [];
        }
    }

    /**
     * Busca un rol por ID
     *
     * @param int $id ID del rol
     * @return array|null Datos del rol o null
     */
    public function find(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch();
            
            return $result ?: null;
        } catch (PDOException $e) {
            app_log("Error al buscar rol: " . $e->getMessage(), 'error');
            return null;
        }
    }

    /**
     * Busca un rol por nombre
     *
     * @param string $name Nombre del rol
     * @return array|null Datos del rol o null
     */
    public function findByName(string $name): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE name = :name");
            $stmt->execute(['name' => $name]);
            $result = $stmt->fetch();
            
            return $result ?: null;
        } catch (PDOException $e) {
            app_log("Error al buscar rol por nombre: " . $e->getMessage(), 'error');
            return null;
        }
    }
}
