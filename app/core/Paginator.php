<?php

namespace App\Core;

class Paginator
{
    private const DEFAULT_ITEMS_PER_PAGE = 10;
    private const DEFAULT_CURRENT_PAGE = 1;
    private const MIN_ITEMS_PER_PAGE = 1;
    private const MIN_PAGE = 1;
    private const PAGES_ON_EACH_SIDE = 2;
    private const QUERY_PARAM_PAGE = 'page';

    private int $totalItems;
    private int $itemsPerPage;
    private int $currentPage;
    private int $totalPages;
    private array $items;

    public function __construct(
        array $items,
        int $totalItems,
        int $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE,
        int $currentPage = self::DEFAULT_CURRENT_PAGE
    ) {
        $this->items = $items;
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $this->normalizeItemsPerPage($itemsPerPage);
        $this->totalPages = $this->calculateTotalPages();
        $this->currentPage = $this->normalizeCurrentPage($currentPage);
    }

    public function items(): array
    {
        return $this->items;
    }

    public function total(): int
    {
        return $this->totalItems;
    }

    public function perPage(): int
    {
        return $this->itemsPerPage;
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function lastPage(): int
    {
        return $this->totalPages;
    }

    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->totalPages;
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > self::MIN_PAGE;
    }

    public function previousPage(): ?int
    {
        return $this->hasPreviousPage() ? $this->currentPage - 1 : null;
    }

    public function nextPage(): ?int
    {
        return $this->hasMorePages() ? $this->currentPage + 1 : null;
    }

    public function offset(): int
    {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }

    public function firstItem(): int
    {
        return $this->hasItems() ? $this->offset() + 1 : 0;
    }

    public function lastItem(): int
    {
        return min($this->offset() + $this->itemsPerPage, $this->totalItems);
    }

    public function pageRange(int $onEachSide = self::PAGES_ON_EACH_SIDE): array
    {
        if ($this->totalPages <= 1) {
            return [];
        }

        $start = max(self::MIN_PAGE, $this->currentPage - $onEachSide);
        $end = min($this->totalPages, $this->currentPage + $onEachSide);

        return range($start, $end);
    }

    public function render(string $baseUrl = ''): string
    {
        if ($this->totalPages <= 1) {
            return '';
        }

        return $this->buildPaginationHtml($baseUrl);
    }

    public function info(): string
    {
        if (!$this->hasItems()) {
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

    private function normalizeItemsPerPage(int $itemsPerPage): int
    {
        return max(self::MIN_ITEMS_PER_PAGE, $itemsPerPage);
    }

    private function calculateTotalPages(): int
    {
        return (int) ceil($this->totalItems / $this->itemsPerPage);
    }

    private function normalizeCurrentPage(int $currentPage): int
    {
        return max(self::MIN_PAGE, min($currentPage, max(self::MIN_PAGE, $this->totalPages)));
    }

    private function hasItems(): bool
    {
        return $this->totalItems > 0;
    }

    private function buildPaginationHtml(string $baseUrl): string
    {
        $html = $this->openNav();
        $html .= $this->renderPreviousButton($baseUrl);
        $html .= $this->renderPageNumbers($baseUrl);
        $html .= $this->renderNextButton($baseUrl);
        $html .= $this->closeNav();

        return $html;
    }

    private function openNav(): string
    {
        return '<nav aria-label="Navegación de páginas"><ul class="pagination justify-content-center mb-0">';
    }

    private function closeNav(): string
    {
        return '</ul></nav>';
    }

    private function renderPreviousButton(string $baseUrl): string
    {
        if ($this->hasPreviousPage()) {
            $url = $this->buildUrl($baseUrl, $this->previousPage());
            return $this->renderActiveLink('Anterior', $url, 'bi-chevron-left', true);
        }

        return $this->renderDisabledLink('Anterior', 'bi-chevron-left', true);
    }

    private function renderNextButton(string $baseUrl): string
    {
        if ($this->hasMorePages()) {
            $url = $this->buildUrl($baseUrl, $this->nextPage());
            return $this->renderActiveLink('Siguiente', $url, 'bi-chevron-right', false);
        }

        return $this->renderDisabledLink('Siguiente', 'bi-chevron-right', false);
    }

    private function renderActiveLink(string $text, string $url, string $icon, bool $iconBefore): string
    {
        $content = $iconBefore 
            ? "<i class=\"bi {$icon}\"></i> {$text}"
            : "{$text} <i class=\"bi {$icon}\"></i>";

        return "<li class=\"page-item\"><a class=\"page-link\" href=\"{$url}\">{$content}</a></li>";
    }

    private function renderDisabledLink(string $text, string $icon, bool $iconBefore): string
    {
        $content = $iconBefore 
            ? "<i class=\"bi {$icon}\"></i> {$text}"
            : "{$text} <i class=\"bi {$icon}\"></i>";

        return "<li class=\"page-item disabled\"><span class=\"page-link\">{$content}</span></li>";
    }

    private function renderPageNumbers(string $baseUrl): string
    {
        $html = '';
        $pageRange = $this->pageRange();

        $html .= $this->renderFirstPageIfNeeded($baseUrl, $pageRange);
        $html .= $this->renderMiddlePages($baseUrl, $pageRange);
        $html .= $this->renderLastPageIfNeeded($baseUrl, $pageRange);

        return $html;
    }

    private function renderFirstPageIfNeeded(string $baseUrl, array $pageRange): string
    {
        if (empty($pageRange) || $pageRange[0] <= 1) {
            return '';
        }

        $html = '<li class="page-item">';
        $html .= '<a class="page-link" href="' . $this->buildUrl($baseUrl, 1) . '">1</a>';
        $html .= '</li>';

        if ($pageRange[0] > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }

        return $html;
    }

    private function renderMiddlePages(string $baseUrl, array $pageRange): string
    {
        $html = '';

        foreach ($pageRange as $page) {
            $html .= $this->renderPageNumber($baseUrl, $page);
        }

        return $html;
    }

    private function renderPageNumber(string $baseUrl, int $page): string
    {
        if ($page === $this->currentPage) {
            return '<li class="page-item active" aria-current="page"><span class="page-link">' . $page . '</span></li>';
        }

        $url = $this->buildUrl($baseUrl, $page);
        return '<li class="page-item"><a class="page-link" href="' . $url . '">' . $page . '</a></li>';
    }

    private function renderLastPageIfNeeded(string $baseUrl, array $pageRange): string
    {
        if (empty($pageRange)) {
            return '';
        }

        $lastInRange = end($pageRange);

        if ($lastInRange >= $this->totalPages) {
            return '';
        }

        $html = '';

        if ($lastInRange < $this->totalPages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }

        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . $this->buildUrl($baseUrl, $this->totalPages) . '">' . $this->totalPages . '</a>';
        $html .= '</li>';

        return $html;
    }

    private function buildUrl(string $baseUrl, int $page): string
    {
        $baseUrl = $this->getBaseUrl($baseUrl);
        $queryParams = $this->getQueryParams($page);

        return $baseUrl . '?' . http_build_query($queryParams);
    }

    private function getBaseUrl(string $baseUrl): string
    {
        if (!empty($baseUrl)) {
            return $baseUrl;
        }

        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        return strtok($requestUri, '?');
    }

    private function getQueryParams(int $page): array
    {
        $params = $_GET ?? [];
        $params[self::QUERY_PARAM_PAGE] = $page;

        return $params;
    }
}
