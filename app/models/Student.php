<?php
/**
 * Modelo Student
 * Representa la entidad estudiante y maneja las operaciones de base de datos
 */
class Student
{
    private PDO $db;
    private string $table = 'students';

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Obtiene todos los estudiantes ordenados por ID descendente
     *
     * @return array Lista de estudiantes
     */
    public function all(): array
    {
        try {
            $stmt = $this->db->query(
                "SELECT id, name, email, phone, created_at 
                 FROM {$this->table} 
                 ORDER BY id DESC"
            );
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            app_log("Error al obtener estudiantes: " . $e->getMessage(), 'error');
            return [];
        }
    }

    /**
     * Busca un estudiante por ID
     *
     * @param int $id ID del estudiante
     * @return array|null Datos del estudiante o null si no existe
     */
    public function find(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, email, phone, created_at 
                 FROM {$this->table} 
                 WHERE id = :id"
            );
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch();
            
            return $result ?: null;
        } catch (PDOException $e) {
            app_log("Error al buscar estudiante ID {$id}: " . $e->getMessage(), 'error');
            return null;
        }
    }

    /**
     * Crea un nuevo estudiante
     *
     * @param array $data Datos del estudiante
     * @return int ID del estudiante creado
     * @throws PDOException
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (name, email, phone, created_at) 
             VALUES (:name, :email, :phone, NOW())"
        );
        
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualiza un estudiante existente
     *
     * @param int $id ID del estudiante
     * @param array $data Nuevos datos
     * @return bool True si se actualizó correctamente
     * @throws PDOException
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} 
             SET name = :name, email = :email, phone = :phone 
             WHERE id = :id"
        );
        
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);
    }

    /**
     * Elimina un estudiante
     *
     * @param int $id ID del estudiante
     * @return bool True si se eliminó correctamente
     * @throws PDOException
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Cuenta el total de estudiantes
     *
     * @return int Total de estudiantes
     */
    public function count(): int
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table}");
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            app_log("Error al contar estudiantes: " . $e->getMessage(), 'error');
            return 0;
        }
    }

    /**
     * Busca estudiantes por nombre o email
     *
     * @param string $search Término de búsqueda
     * @return array Lista de estudiantes encontrados
     */
    public function search(string $search): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, email, phone, created_at 
                 FROM {$this->table} 
                 WHERE name LIKE :search OR email LIKE :search
                 ORDER BY id DESC"
            );
            
            $searchTerm = '%' . $search . '%';
            $stmt->execute(['search' => $searchTerm]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            app_log("Error en búsqueda de estudiantes: " . $e->getMessage(), 'error');
            return [];
        }
    }

    /**
     * Verifica si existe un email
     *
     * @param string $email Email a verificar
     * @param int|null $excludeId ID a excluir (para updates)
     * @return bool
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        try {
            if ($excludeId !== null) {
                $stmt = $this->db->prepare(
                    "SELECT COUNT(*) FROM {$this->table} WHERE email = :email AND id != :id"
                );
                $stmt->execute(['email' => $email, 'id' => $excludeId]);
            } else {
                $stmt = $this->db->prepare(
                    "SELECT COUNT(*) FROM {$this->table} WHERE email = :email"
                );
                $stmt->execute(['email' => $email]);
            }
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            app_log("Error al verificar email: " . $e->getMessage(), 'error');
            return false;
        }
    }
}
