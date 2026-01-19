<?php
/**
 * Paginator - Sistema de paginación simple y eficiente
 */

namespace App\Core;

class Paginator
{
    private int $totalItems;
    private int $itemsPerPage;
    private int $currentPage;
    private int $totalPages;
    private array $items;

    /**
     * Constructor del paginador
     *
     * @param array $items Items a paginar
     * @param int $totalItems Total de items en la base de datos
     * @param int $itemsPerPage Items por página
     * @param int $currentPage Página actual
     */
    public function __construct(array $items, int $totalItems, int $itemsPerPage = 10, int $currentPage = 1)
    {
        $this->items = $items;
        $this->totalItems = $totalItems;
        $this->itemsPerPage = max(1, $itemsPerPage);
        $this->totalPages = (int) ceil($totalItems / $this->itemsPerPage);
        $this->currentPage = max(1, min($currentPage, max(1, $this->totalPages)));
    }

    /**
     * Obtiene los items de la página actual
     *
     * @return array
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * Obtiene el total de items
     *
     * @return int
     */
    public function total(): int
    {
        return $this->totalItems;
    }

    /**
     * Obtiene el número de items por página
     *
     * @return int
     */
    public function perPage(): int
    {
        return $this->itemsPerPage;
    }

    /**
     * Obtiene la página actual
     *
     * @return int
     */
    public function currentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Obtiene el total de páginas
     *
     * @return int
     */
    public function lastPage(): int
    {
        return $this->totalPages;
    }

    /**
     * Verifica si hay más páginas
     *
     * @return bool
     */
    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->totalPages;
    }

    /**
     * Verifica si hay una página anterior
     *
     * @return bool
     */
    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * Obtiene el número de la página anterior
     *
     * @return int|null
     */
    public function previousPage(): ?int
    {
        return $this->hasPreviousPage() ? $this->currentPage - 1 : null;
    }

    /**
     * Obtiene el número de la siguiente página
     *
     * @return int|null
     */
    public function nextPage(): ?int
    {
        return $this->hasMorePages() ? $this->currentPage + 1 : null;
    }

    /**
     * Obtiene el offset para la consulta SQL
     *
     * @return int
     */
    public function offset(): int
    {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }

    /**
     * Obtiene el número del primer item en la página
     *
     * @return int
     */
    public function firstItem(): int
    {
        return $this->totalItems > 0 ? $this->offset() + 1 : 0;
    }

    /**
     * Obtiene el número del último item en la página
     *
     * @return int
     */
    public function lastItem(): int
    {
        return min($this->offset() + $this->itemsPerPage, $this->totalItems);
    }

    /**
     * Genera un array de números de página para mostrar
     *
     * @param int $onEachSide Páginas a mostrar a cada lado de la actual
     * @return array
     */
    public function pageRange(int $onEachSide = 2): array
    {
        if ($this->totalPages <= 1) {
            return [];
        }

        $start = max(1, $this->currentPage - $onEachSide);
        $end = min($this->totalPages, $this->currentPage + $onEachSide);

        return range($start, $end);
    }

    /**
     * Renderiza los controles de paginación en HTML
     *
     * @param string $baseUrl URL base para los links
     * @return string HTML de la paginación
     */
    public function render(string $baseUrl = ''): string
    {
        if ($this->totalPages <= 1) {
            return '';
        }

        $html = '<nav aria-label="Navegación de páginas">';
        $html .= '<ul class="pagination justify-content-center mb-0">';

        // Botón anterior
        if ($this->hasPreviousPage()) {
            $prevUrl = $this->buildUrl($baseUrl, $this->previousPage());
            $html .= '<li class="page-item">';
            $html .= '<a class="page-link" href="' . $prevUrl . '" aria-label="Anterior">';
            $html .= '<i class="bi bi-chevron-left"></i> Anterior';
            $html .= '</a></li>';
        } else {
            $html .= '<li class="page-item disabled">';
            $html .= '<span class="page-link"><i class="bi bi-chevron-left"></i> Anterior</span>';
            $html .= '</li>';
        }

        // Números de página
        $pageRange = $this->pageRange(2);
        
        // Primera página
        if ($pageRange[0] > 1) {
            $html .= '<li class="page-item">';
            $html .= '<a class="page-link" href="' . $this->buildUrl($baseUrl, 1) . '">1</a>';
            $html .= '</li>';
            if ($pageRange[0] > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Páginas intermedias
        foreach ($pageRange as $page) {
            if ($page === $this->currentPage) {
                $html .= '<li class="page-item active" aria-current="page">';
                $html .= '<span class="page-link">' . $page . '</span>';
                $html .= '</li>';
            } else {
                $html .= '<li class="page-item">';
                $html .= '<a class="page-link" href="' . $this->buildUrl($baseUrl, $page) . '">' . $page . '</a>';
                $html .= '</li>';
            }
        }

        // Última página
        $lastInRange = end($pageRange);
        if ($lastInRange < $this->totalPages) {
            if ($lastInRange < $this->totalPages - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $html .= '<li class="page-item">';
            $html .= '<a class="page-link" href="' . $this->buildUrl($baseUrl, $this->totalPages) . '">' . $this->totalPages . '</a>';
            $html .= '</li>';
        }

        // Botón siguiente
        if ($this->hasMorePages()) {
            $nextUrl = $this->buildUrl($baseUrl, $this->nextPage());
            $html .= '<li class="page-item">';
            $html .= '<a class="page-link" href="' . $nextUrl . '" aria-label="Siguiente">';
            $html .= 'Siguiente <i class="bi bi-chevron-right"></i>';
            $html .= '</a></li>';
        } else {
            $html .= '<li class="page-item disabled">';
            $html .= '<span class="page-link">Siguiente <i class="bi bi-chevron-right"></i></span>';
            $html .= '</li>';
        }

        $html .= '</ul></nav>';

        return $html;
    }

    /**
     * Construye la URL con el número de página
     *
     * @param string $baseUrl
     * @param int $page
     * @return string
     */
    private function buildUrl(string $baseUrl, int $page): string
    {
        if (empty($baseUrl)) {
            $baseUrl = $_SERVER['REQUEST_URI'] ?? '/';
            $baseUrl = strtok($baseUrl, '?');
        }

        $queryParams = $_GET ?? [];
        $queryParams['page'] = $page;

        return $baseUrl . '?' . http_build_query($queryParams);
    }

    /**
     * Renderiza información de la paginación (ej: "Mostrando 1-10 de 50")
     *
     * @return string
     */
    public function info(): string
    {
        if ($this->totalItems === 0) {
            return 'No hay registros';
        }

        return sprintf(
            'Mostrando %d-%d de %d registro%s',
            $this->firstItem(),
            $this->lastItem(),
            $this->totalItems,
            $this->totalItems !== 1 ? 's' : ''
        );
    }
}
