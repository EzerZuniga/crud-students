<?php
/**
 * Modelo Student
 * Representa la entidad estudiante y maneja las operaciones de base de datos
 */

namespace App\Models;

use PDO;
use PDOException;
use App\Core\Paginator;

class Student
{
    private PDO $db;
    private string $table = 'students';

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Sanitiza los datos del estudiante
     *
     * @param array $data Datos a sanitizar
     * @return array Datos sanitizados
     */
    private function sanitize(array $data): array
    {
        return [
            'name' => sanitize_string($data['name'] ?? ''),
            'email' => sanitize_email($data['email'] ?? ''),
            'phone' => sanitize_string($data['phone'] ?? ''),
        ];
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
     * Obtiene estudiantes paginados
     *
     * @param int $page Página actual
     * @param int $perPage Items por página
     * @param string|null $search Término de búsqueda opcional
     * @param array $filters Filtros adicionales
     * @return Paginator Objeto paginador
     */
    public function paginate(int $page = 1, int $perPage = 10, ?string $search = null, array $filters = []): Paginator
    {
        try {
            // Construir query de conteo
            $countQuery = "SELECT COUNT(*) as total FROM {$this->table}";
            $dataQuery = "SELECT id, name, email, phone, created_at FROM {$this->table}";
            $params = [];
            $whereClauses = [];

            // Agregar filtro de búsqueda si existe
            if ($search) {
                $whereClauses[] = "(name LIKE :search OR email LIKE :search OR phone LIKE :search)";
                $params['search'] = "%{$search}%";
            }

            // Filtro por fecha (desde)
            if (!empty($filters['date_from'])) {
                $whereClauses[] = "DATE(created_at) >= :date_from";
                $params['date_from'] = $filters['date_from'];
            }

            // Filtro por fecha (hasta)
            if (!empty($filters['date_to'])) {
                $whereClauses[] = "DATE(created_at) <= :date_to";
                $params['date_to'] = $filters['date_to'];
            }

            // Filtro por email (contiene dominio específico)
            if (!empty($filters['email_domain'])) {
                $whereClauses[] = "email LIKE :email_domain";
                $params['email_domain'] = "%@{$filters['email_domain']}%";
            }

            // Construir WHERE clause
            if (!empty($whereClauses)) {
                $whereClause = " WHERE " . implode(" AND ", $whereClauses);
                $countQuery .= $whereClause;
                $dataQuery .= $whereClause;
            }

            // Contar total de registros
            $stmt = $this->db->prepare($countQuery);
            $stmt->execute($params);
            $total = (int) $stmt->fetch()['total'];

            // Calcular offset
            $offset = ($page - 1) * $perPage;

            // Determinar ordenamiento
            $orderBy = match($filters['sort_by'] ?? 'id') {
                'name' => 'name',
                'email' => 'email',
                'date' => 'created_at',
                default => 'id'
            };

            $orderDir = (isset($filters['sort_dir']) && strtoupper($filters['sort_dir']) === 'ASC') ? 'ASC' : 'DESC';

            // Obtener datos paginados
            $dataQuery .= " ORDER BY {$orderBy} {$orderDir} LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($dataQuery);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $items = $stmt->fetchAll();

            return new Paginator($items, $total, $perPage, $page);
        } catch (PDOException $e) {
            app_log("Error al paginar estudiantes: " . $e->getMessage(), 'error');
            return new Paginator([], 0, $perPage, $page);
        }
    }

    /**
     * Busca estudiantes por término de búsqueda
     *
     * @param string $search Término de búsqueda
     * @return array
     */
    public function search(string $search): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, name, email, phone, created_at 
                FROM {$this->table} 
                WHERE name LIKE :search 
                   OR email LIKE :search 
                   OR phone LIKE :search
                ORDER BY name ASC
                LIMIT 50
            ");
            $stmt->execute(['search' => "%{$search}%"]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            app_log("Error searching students: " . $e->getMessage(), 'error');
            return [];
        }
    }

    /**
     * Obtiene estadísticas de estudiantes
     *
     * @return array
     */
    public function getStats(): array
    {
        try {
            $stats = [];

            // Total de estudiantes
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
            $stats['total'] = (int) $stmt->fetch()['total'];

            // Estudiantes registrados hoy
            $stmt = $this->db->query("
                SELECT COUNT(*) as today 
                FROM {$this->table} 
                WHERE DATE(created_at) = CURDATE()
            ");
            $stats['today'] = (int) $stmt->fetch()['today'];

            // Estudiantes registrados esta semana
            $stmt = $this->db->query("
                SELECT COUNT(*) as week 
                FROM {$this->table} 
                WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)
            ");
            $stats['week'] = (int) $stmt->fetch()['week'];

            // Estudiantes registrados este mes
            $stmt = $this->db->query("
                SELECT COUNT(*) as month 
                FROM {$this->table} 
                WHERE YEAR(created_at) = YEAR(CURDATE()) 
                  AND MONTH(created_at) = MONTH(CURDATE())
            ");
            $stats['month'] = (int) $stmt->fetch()['month'];

            // Dominios de email más comunes
            $stmt = $this->db->query("
                SELECT SUBSTRING_INDEX(email, '@', -1) as domain, COUNT(*) as count
                FROM {$this->table}
                GROUP BY domain
                ORDER BY count DESC
                LIMIT 5
            ");
            $stats['email_domains'] = $stmt->fetchAll();

            return $stats;
        } catch (PDOException $e) {
            app_log("Error getting stats: " . $e->getMessage(), 'error');
            return [
                'total' => 0,
                'today' => 0,
                'week' => 0,
                'month' => 0,
                'email_domains' => []
            ];
        }
    }

    /**
     * Cuenta el total de estudiantes
     *
     * @return int
     */
    public function count(): int
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
            return (int) $stmt->fetch()['total'];
        } catch (PDOException $e) {
            app_log("Error al contar estudiantes: " . $e->getMessage(), 'error');
            return 0;
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
        $data = $this->sanitize($data);
        
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
        $data = $this->sanitize($data);
        
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
