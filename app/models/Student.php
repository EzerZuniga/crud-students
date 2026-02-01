<?php

namespace App\Models;

use PDO;
use PDOException;
use App\Core\Paginator;

class Student
{
    private const TABLE = 'students';
    
    private const COLUMN_ID = 'id';
    private const COLUMN_NAME = 'name';
    private const COLUMN_EMAIL = 'email';
    private const COLUMN_PHONE = 'phone';
    private const COLUMN_CREATED_AT = 'created_at';
    
    private const DEFAULT_ORDER = 'id DESC';
    private const DEFAULT_PER_PAGE = 10;
    private const SEARCH_LIMIT = 50;
    
    private const SORT_BY_ID = 'id';
    private const SORT_BY_NAME = 'name';
    private const SORT_BY_EMAIL = 'email';
    private const SORT_BY_DATE = 'date';
    
    private const SORT_DIR_ASC = 'ASC';
    private const SORT_DIR_DESC = 'DESC';
    
    private const STAT_TOTAL = 'total';
    private const STAT_TODAY = 'today';
    private const STAT_WEEK = 'week';
    private const STAT_MONTH = 'month';
    private const STAT_EMAIL_DOMAINS = 'email_domains';
    
    private const LOG_ERROR_ALL = 'Error al obtener estudiantes: ';
    private const LOG_ERROR_PAGINATE = 'Error al paginar estudiantes: ';
    private const LOG_ERROR_SEARCH = 'Error searching students: ';
    private const LOG_ERROR_STATS = 'Error getting stats: ';
    private const LOG_ERROR_COUNT = 'Error al contar estudiantes: ';
    private const LOG_ERROR_FIND = 'Error al buscar estudiante ID %d: ';
    private const LOG_ERROR_EMAIL_CHECK = 'Error al verificar email: ';

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

    public function paginate(int $page = 1, int $perPage = self::DEFAULT_PER_PAGE, ?string $search = null, array $filters = []): Paginator
    {
        try {
            $queryBuilder = $this->buildPaginationQueries($search, $filters);
            $total = $this->getTotalCount($queryBuilder['count'], $queryBuilder['params']);
            $items = $this->getPaginatedItems($queryBuilder['data'], $queryBuilder['params'], $page, $perPage, $filters);

            return new Paginator($items, $total, $perPage, $page);
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_PAGINATE, $e);
            return new Paginator([], 0, $perPage, $page);
        }
    }

    public function search(string $search): array
    {
        try {
            $stmt = $this->db->prepare($this->buildSearchQuery());
            $stmt->execute(['search' => "%{$search}%"]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_SEARCH, $e);
            return [];
        }
    }

    public function getStats(): array
    {
        try {
            return [
                self::STAT_TOTAL => $this->getTotalStudents(),
                self::STAT_TODAY => $this->getTodayStudents(),
                self::STAT_WEEK => $this->getWeekStudents(),
                self::STAT_MONTH => $this->getMonthStudents(),
                self::STAT_EMAIL_DOMAINS => $this->getTopEmailDomains(),
            ];
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_STATS, $e);
            return $this->getEmptyStats();
        }
    }

    public function count(): int
    {
        try {
            $stmt = $this->db->query($this->buildCountQuery());
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_COUNT, $e);
            return 0;
        }
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
        
        $stmt = $this->db->prepare($this->buildUpdateQuery());
        return $this->executeUpdate($stmt, $data, $id);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare($this->buildDeleteQuery());
        return $stmt->execute([self::COLUMN_ID => $id]);
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        try {
            $query = $excludeId !== null 
                ? $this->buildEmailExistsWithExclusionQuery() 
                : $this->buildEmailExistsQuery();
            
            $stmt = $this->db->prepare($query);
            $params = [self::COLUMN_EMAIL => $email];
            
            if ($excludeId !== null) {
                $params[self::COLUMN_ID] = $excludeId;
            }
            
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            $this->logError(self::LOG_ERROR_EMAIL_CHECK, $e);
            return false;
        }
    }

    private function sanitizeData(array $data): array
    {
        return [
            self::COLUMN_NAME => sanitize_string($data[self::COLUMN_NAME] ?? ''),
            self::COLUMN_EMAIL => sanitize_email($data[self::COLUMN_EMAIL] ?? ''),
            self::COLUMN_PHONE => sanitize_string($data[self::COLUMN_PHONE] ?? ''),
        ];
    }

    private function buildSelectAllQuery(): string
    {
        return "SELECT " . $this->getSelectColumns() . " FROM " . self::TABLE . " ORDER BY " . self::DEFAULT_ORDER;
    }

    private function buildFindByIdQuery(): string
    {
        return "SELECT " . $this->getSelectColumns() . " FROM " . self::TABLE . " WHERE " . self::COLUMN_ID . " = :" . self::COLUMN_ID;
    }

    private function buildInsertQuery(): string
    {
        return "INSERT INTO " . self::TABLE . " (" . self::COLUMN_NAME . ", " . self::COLUMN_EMAIL . ", " . self::COLUMN_PHONE . ", " . self::COLUMN_CREATED_AT . ")
                VALUES (:" . self::COLUMN_NAME . ", :" . self::COLUMN_EMAIL . ", :" . self::COLUMN_PHONE . ", NOW())";
    }

    private function buildUpdateQuery(): string
    {
        return "UPDATE " . self::TABLE . " 
                SET " . self::COLUMN_NAME . " = :" . self::COLUMN_NAME . ", 
                    " . self::COLUMN_EMAIL . " = :" . self::COLUMN_EMAIL . ", 
                    " . self::COLUMN_PHONE . " = :" . self::COLUMN_PHONE . " 
                WHERE " . self::COLUMN_ID . " = :" . self::COLUMN_ID;
    }

    private function buildDeleteQuery(): string
    {
        return "DELETE FROM " . self::TABLE . " WHERE " . self::COLUMN_ID . " = :" . self::COLUMN_ID;
    }

    private function buildCountQuery(): string
    {
        return "SELECT COUNT(*) FROM " . self::TABLE;
    }

    private function buildSearchQuery(): string
    {
        return "SELECT " . $this->getSelectColumns() . " 
                FROM " . self::TABLE . " 
                WHERE " . self::COLUMN_NAME . " LIKE :search 
                   OR " . self::COLUMN_EMAIL . " LIKE :search 
                   OR " . self::COLUMN_PHONE . " LIKE :search
                ORDER BY " . self::COLUMN_NAME . " ASC
                LIMIT " . self::SEARCH_LIMIT;
    }

    private function buildEmailExistsQuery(): string
    {
        return "SELECT COUNT(*) FROM " . self::TABLE . " WHERE " . self::COLUMN_EMAIL . " = :" . self::COLUMN_EMAIL;
    }

    private function buildEmailExistsWithExclusionQuery(): string
    {
        return "SELECT COUNT(*) FROM " . self::TABLE . " WHERE " . self::COLUMN_EMAIL . " = :" . self::COLUMN_EMAIL . " AND " . self::COLUMN_ID . " != :" . self::COLUMN_ID;
    }

    private function buildPaginationQueries(?string $search, array $filters): array
    {
        $countQuery = "SELECT COUNT(*) as " . self::STAT_TOTAL . " FROM " . self::TABLE;
        $dataQuery = "SELECT " . $this->getSelectColumns() . " FROM " . self::TABLE;
        $params = [];
        $whereClauses = [];

        if ($search) {
            $whereClauses[] = "(" . self::COLUMN_NAME . " LIKE :search OR " . self::COLUMN_EMAIL . " LIKE :search OR " . self::COLUMN_PHONE . " LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if (!empty($filters['date_from'])) {
            $whereClauses[] = "DATE(" . self::COLUMN_CREATED_AT . ") >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $whereClauses[] = "DATE(" . self::COLUMN_CREATED_AT . ") <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }

        if (!empty($filters['email_domain'])) {
            $whereClauses[] = self::COLUMN_EMAIL . " LIKE :email_domain";
            $params['email_domain'] = "%@{$filters['email_domain']}%";
        }

        if (!empty($whereClauses)) {
            $whereClause = " WHERE " . implode(" AND ", $whereClauses);
            $countQuery .= $whereClause;
            $dataQuery .= $whereClause;
        }

        return [
            'count' => $countQuery,
            'data' => $dataQuery,
            'params' => $params,
        ];
    }

    private function getTotalCount(string $query, array $params): int
    {
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return (int) $stmt->fetch()[self::STAT_TOTAL];
    }

    private function getPaginatedItems(string $query, array $params, int $page, int $perPage, array $filters): array
    {
        $offset = ($page - 1) * $perPage;
        $orderBy = $this->determineOrderBy($filters);
        $orderDir = $this->determineOrderDir($filters);

        $query .= " ORDER BY {$orderBy} {$orderDir} LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function determineOrderBy(array $filters): string
    {
        return match($filters['sort_by'] ?? self::SORT_BY_ID) {
            self::SORT_BY_NAME => self::COLUMN_NAME,
            self::SORT_BY_EMAIL => self::COLUMN_EMAIL,
            self::SORT_BY_DATE => self::COLUMN_CREATED_AT,
            default => self::COLUMN_ID
        };
    }

    private function determineOrderDir(array $filters): string
    {
        return (isset($filters['sort_dir']) && strtoupper($filters['sort_dir']) === self::SORT_DIR_ASC) 
            ? self::SORT_DIR_ASC 
            : self::SORT_DIR_DESC;
    }

    private function getTotalStudents(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as " . self::STAT_TOTAL . " FROM " . self::TABLE);
        return (int) $stmt->fetch()[self::STAT_TOTAL];
    }

    private function getTodayStudents(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as " . self::STAT_TODAY . " FROM " . self::TABLE . " WHERE DATE(" . self::COLUMN_CREATED_AT . ") = CURDATE()");
        return (int) $stmt->fetch()[self::STAT_TODAY];
    }

    private function getWeekStudents(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as " . self::STAT_WEEK . " FROM " . self::TABLE . " WHERE YEARWEEK(" . self::COLUMN_CREATED_AT . ", 1) = YEARWEEK(CURDATE(), 1)");
        return (int) $stmt->fetch()[self::STAT_WEEK];
    }

    private function getMonthStudents(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as " . self::STAT_MONTH . " FROM " . self::TABLE . " WHERE YEAR(" . self::COLUMN_CREATED_AT . ") = YEAR(CURDATE()) AND MONTH(" . self::COLUMN_CREATED_AT . ") = MONTH(CURDATE())");
        return (int) $stmt->fetch()[self::STAT_MONTH];
    }

    private function getTopEmailDomains(): array
    {
        $stmt = $this->db->query("SELECT SUBSTRING_INDEX(" . self::COLUMN_EMAIL . ", '@', -1) as domain, COUNT(*) as count FROM " . self::TABLE . " GROUP BY domain ORDER BY count DESC LIMIT 5");
        return $stmt->fetchAll();
    }

    private function getEmptyStats(): array
    {
        return [
            self::STAT_TOTAL => 0,
            self::STAT_TODAY => 0,
            self::STAT_WEEK => 0,
            self::STAT_MONTH => 0,
            self::STAT_EMAIL_DOMAINS => []
        ];
    }

    private function getSelectColumns(): string
    {
        return self::COLUMN_ID . ", " . self::COLUMN_NAME . ", " . self::COLUMN_EMAIL . ", " . self::COLUMN_PHONE . ", " . self::COLUMN_CREATED_AT;
    }

    private function executeInsert($stmt, array $data): void
    {
        $stmt->execute([
            self::COLUMN_NAME => $data[self::COLUMN_NAME],
            self::COLUMN_EMAIL => $data[self::COLUMN_EMAIL],
            self::COLUMN_PHONE => $data[self::COLUMN_PHONE],
        ]);
    }

    private function executeUpdate($stmt, array $data, int $id): bool
    {
        return $stmt->execute([
            self::COLUMN_ID => $id,
            self::COLUMN_NAME => $data[self::COLUMN_NAME],
            self::COLUMN_EMAIL => $data[self::COLUMN_EMAIL],
            self::COLUMN_PHONE => $data[self::COLUMN_PHONE],
        ]);
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
