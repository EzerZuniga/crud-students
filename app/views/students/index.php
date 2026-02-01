<?php

declare(strict_types=1);

const PAGE_TITLE = 'Gestión de Estudiantes';
const PAGE_SUBTITLE = 'Administra el directorio de estudiantes';
const BTN_NEW_STUDENT = 'Nuevo Estudiante';

const STAT_TOTAL_LABEL = 'Total';
const STAT_TODAY_LABEL = 'Registrados Hoy';
const STAT_WEEK_LABEL = 'Esta Semana';
const STAT_MONTH_LABEL = 'Este Mes';

const SEARCH_PLACEHOLDER = 'Buscar estudiantes...';
const BTN_FILTER = 'Filtrar';
const BTN_CLEAN = 'Limpiar';

const EMPTY_NO_RESULTS = 'No se encontraron resultados';
const EMPTY_NO_MATCHES = 'No hay coincidencias para';
const EMPTY_VIEW_ALL = 'Ver todos los registros';
const EMPTY_NO_RECORDS = 'Sin registros';
const EMPTY_DB_EMPTY = 'La base de datos está vacía actualmente.';
const EMPTY_START_REGISTER = 'Comenzar registro';

const TABLE_HEADER_STUDENT = 'Estudiante';
const TABLE_HEADER_CONTACT = 'Contacto';
const TABLE_HEADER_PHONE = 'Teléfono';
const TABLE_HEADER_REGISTER = 'Registro';
const TABLE_HEADER_ACTIONS = 'Acciones';

const TOOLTIP_VIEW = 'Ver';
const TOOLTIP_EDIT = 'Editar';
const TOOLTIP_DELETE = 'Eliminar';
const CONFIRM_DELETE = '¿Confirmar eliminación?';

const ROUTE_INDEX = 'students.index';
const ROUTE_CREATE = 'students.create';
const ROUTE_SHOW = 'students.show';
const ROUTE_EDIT = 'students.edit';
const ROUTE_DELETE = 'students.delete';

const PERMISSION_CREATE = 'students.create';
const PERMISSION_EDIT = 'students.edit';
const PERMISSION_DELETE = 'students.delete';

const FIELD_SEARCH = 'search';
const FIELD_PER_PAGE = 'per_page';
const FIELD_SORT_BY = 'sort_by';
const FIELD_ID = 'id';

const SORT_ID = 'id';
const SORT_NAME = 'name';
const SORT_CREATED_AT = 'created_at';

const PER_PAGE_10 = 10;
const PER_PAGE_25 = 25;

const ICON_PLUS = 'bi bi-plus-lg me-1';
const ICON_PEOPLE = 'bi bi-people text-primary fs-4';
const ICON_CALENDAR_CHECK = 'bi bi-calendar-check text-success fs-4';
const ICON_GRAPH_UP = 'bi bi-graph-up text-info fs-4';
const ICON_CALENDAR = 'bi bi-calendar3 text-warning fs-4';
const ICON_SEARCH = 'bi bi-search';
const ICON_REFRESH = 'bi bi-arrow-counterclockwise';
const ICON_FOLDER = 'bi bi-folder2-open display-1';
const ICON_EYE = 'bi bi-eye';
const ICON_EDIT = 'bi bi-pencil-square';
const ICON_TRASH = 'bi bi-trash';

const CLASS_HEADER_CONTAINER = 'd-flex justify-content-between align-items-center mb-4';
const CLASS_TITLE = 'h3 text-dark fw-bold mb-1';
const CLASS_SUBTITLE = 'text-secondary mb-0';
const CLASS_BTN_PRIMARY = 'btn btn-primary';
const CLASS_STAT_ROW = 'row g-3 mb-4';
const CLASS_STAT_COL = 'col-md-3';
const CLASS_CARD = 'card';
const CLASS_CARD_BODY = 'card-body';
const CLASS_FILTER_CARD = 'card mb-4';
const CLASS_FILTER_BODY = 'card-body p-3';
const CLASS_INPUT_GROUP = 'input-group';
const CLASS_INPUT_GROUP_TEXT = 'input-group-text bg-white text-muted border-end-0';
const CLASS_FORM_CONTROL = 'form-control border-start-0 ps-0';
const CLASS_FORM_SELECT = 'form-select';
const CLASS_BTN_FILTER = 'btn btn-primary w-100 w-md-auto';
const CLASS_BTN_CLEAN = 'btn btn-outline-secondary ms-1';
const CLASS_EMPTY_CONTAINER = 'card-body text-center py-5';
const CLASS_EMPTY_ICON = 'mb-3 text-muted opacity-25';
const CLASS_TABLE_RESPONSIVE = 'table-responsive';
const CLASS_TABLE = 'table table-hover align-middle mb-0';
const CLASS_TABLE_HEAD = '';
const CLASS_AVATAR = 'avatar-circle me-3';
const CLASS_BTN_GROUP = 'btn-group';
const CLASS_BTN_ACTION = 'btn-action btn-action-view';
const CLASS_BTN_ACTION_PRIMARY = 'btn-action btn-action-edit';
const CLASS_BTN_ACTION_DANGER = 'btn-action btn-action-delete';
const CLASS_CARD_FOOTER = 'card-footer bg-white py-3';

const DATE_FORMAT = 'd M, Y';
const ATTR_SELECTED = 'selected';

function hasStats(array $stats): bool
{
    return ($stats['total'] ?? 0) > 0;
}

function formatNumber(int $value): string
{
    return number_format($value);
}

function renderStatCard(string $label, int $value, string $icon, string $borderColor, string $bgColor): string
{
    return sprintf(
        '<div class="col-md-3"><div class="card stat-card h-100 %s"><div class="card-body"><div class="d-flex justify-content-between align-items-center"><div><span class="text-uppercase text-muted small fw-bold">%s</span><h2 class="mb-0">%s</h2></div><div class="%s p-2 rounded-3"><i class="%s"></i></div></div></div></div></div>',
        $borderColor,
        e($label),
        formatNumber($value),
        $bgColor,
        $icon
    );
}

function renderStatsSection(array $stats): string
{
    if (!hasStats($stats)) {
        return '';
    }
    
    $html = sprintf('<div class="%s">', CLASS_STAT_ROW);
    $html .= renderStatCard(STAT_TOTAL_LABEL, $stats['total'], ICON_PEOPLE, 'border-primary', 'bg-primary bg-opacity-10');
    $html .= renderStatCard(STAT_TODAY_LABEL, $stats['today'], ICON_CALENDAR_CHECK, 'border-success', 'bg-success bg-opacity-10');
    $html .= renderStatCard(STAT_WEEK_LABEL, $stats['week'], ICON_GRAPH_UP, 'border-info', 'bg-info bg-opacity-10');
    $html .= renderStatCard(STAT_MONTH_LABEL, $stats['month'], ICON_CALENDAR, 'border-warning', 'bg-warning bg-opacity-10');
    $html .= '</div>';
    
    return $html;
}

function renderSearchInput(string $search): string
{
    return sprintf(
        '<div class="%s"><span class="%s"><i class="%s"></i></span><input type="text" class="%s" name="%s" placeholder="%s" value="%s"></div>',
        CLASS_INPUT_GROUP,
        CLASS_INPUT_GROUP_TEXT,
        ICON_SEARCH,
        CLASS_FORM_CONTROL,
        FIELD_SEARCH,
        SEARCH_PLACEHOLDER,
        e($search)
    );
}

function renderPerPageSelect(int $currentPerPage): string
{
    $options = [
        PER_PAGE_10 => '10 registros',
        PER_PAGE_25 => '25 registros'
    ];
    
    $html = sprintf('<select class="%s" name="%s">', CLASS_FORM_SELECT, FIELD_PER_PAGE);
    foreach ($options as $value => $label) {
        $selected = $currentPerPage === $value ? ATTR_SELECTED : '';
        $html .= sprintf(
            '<option value="%d" %s>%s</option>',
            $value,
            $selected,
            e($label)
        );
    }
    $html .= '</select>';
    
    return $html;
}

function renderSortBySelect(string $currentSort): string
{
    $options = [
        SORT_ID => 'ID',
        SORT_NAME => 'Nombre',
        SORT_CREATED_AT => 'Fecha'
    ];
    
    $html = sprintf('<select class="%s" name="%s">', CLASS_FORM_SELECT, FIELD_SORT_BY);
    foreach ($options as $value => $label) {
        $selected = $currentSort === $value ? ATTR_SELECTED : '';
        $html .= sprintf(
            '<option value="%s" %s>%s</option>',
            htmlspecialchars($value, ENT_QUOTES, 'UTF-8'),
            $selected,
            e($label)
        );
    }
    $html .= '</select>';
    
    return $html;
}

function hasActiveFilters(string $search, array $filters): bool
{
    return !empty($search) || !empty(array_filter($filters));
}

function renderEmptyState(string $search = ''): string
{
    $html = sprintf('<div class="%s">', CLASS_CARD);
    $html .= '<div class="empty-state">';
    $html .= '<div class="empty-state-icon"><i class="bi bi-inbox"></i></div>';
    
    if ($search !== '') {
        $html .= sprintf('<h4 class="empty-state-title">%s</h4>', e(EMPTY_NO_RESULTS));
        $html .= sprintf('<p class="empty-state-text">%s "<strong>%s</strong>"</p>', e(EMPTY_NO_MATCHES), e($search));
        $html .= sprintf(
            '<a href="%s" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>%s</a>',
            htmlspecialchars(route(ROUTE_INDEX), ENT_QUOTES, 'UTF-8'),
            e(EMPTY_VIEW_ALL)
        );
    } else {
        $html .= sprintf('<h4 class="empty-state-title">%s</h4>', e(EMPTY_NO_RECORDS));
        $html .= sprintf('<p class="empty-state-text">%s</p>', e(EMPTY_DB_EMPTY));
        
        if (can(PERMISSION_CREATE)) {
            $html .= sprintf(
                '<a href="%s" class="%s"><i class="bi bi-plus-lg me-2"></i>%s</a>',
                htmlspecialchars(route(ROUTE_CREATE), ENT_QUOTES, 'UTF-8'),
                CLASS_BTN_PRIMARY,
                e(EMPTY_START_REGISTER)
            );
        }
    }
    
    $html .= '</div></div>';
    return $html;
}

function getStudentInitial(string $name): string
{
    return strtoupper(substr($name, 0, 1));
}

function renderStudentCell(array $student): string
{
    return sprintf(
        '<td class="ps-4"><div class="d-flex align-items-center"><div class="%s">%s</div><div><div class="fw-bold text-dark">%s</div><small class="text-muted">ID: %s</small></div></div></td>',
        CLASS_AVATAR,
        getStudentInitial($student['name']),
        e($student['name']),
        e($student['id'])
    );
}

function renderContactCell(string $email): string
{
    return sprintf(
        '<td><a href="mailto:%s" class="text-decoration-none text-secondary">%s</a></td>',
        e($email),
        e($email)
    );
}

function renderPhoneCell(string $phone): string
{
    return sprintf(
        '<td><span class="text-secondary">%s</span></td>',
        e($phone)
    );
}

function renderDateCell(string $createdAt): string
{
    return sprintf(
        '<td><span class="badge bg-light text-dark border fw-normal">%s</span></td>',
        format_date($createdAt, DATE_FORMAT)
    );
}

function renderActionButton(string $route, array $params, string $icon, string $tooltip, string $class): string
{
    return sprintf(
        '<a href="%s" class="%s" data-bs-toggle="tooltip" title="%s"><i class="%s"></i></a>',
        htmlspecialchars(route($route, $params), ENT_QUOTES, 'UTF-8'),
        $class,
        htmlspecialchars($tooltip, ENT_QUOTES, 'UTF-8'),
        $icon
    );
}

function renderDeleteButton(string $studentId): string
{
    return sprintf(
        '<form method="POST" action="%s" class="d-inline" onsubmit="return confirm(\'%s\');">%s<button type="submit" class="%s" data-bs-toggle="tooltip" title="%s"><i class="%s"></i></button></form>',
        htmlspecialchars(route(ROUTE_DELETE, [FIELD_ID => $studentId]), ENT_QUOTES, 'UTF-8'),
        CONFIRM_DELETE,
        csrf_field(),
        CLASS_BTN_ACTION_DANGER,
        htmlspecialchars(TOOLTIP_DELETE, ENT_QUOTES, 'UTF-8'),
        ICON_TRASH
    );
}

function renderActionsCell(array $student): string
{
    $html = '<td class="text-end pe-4"><div class="' . CLASS_BTN_GROUP . '">';
    
    $html .= renderActionButton(
        ROUTE_SHOW,
        [FIELD_ID => $student['id']],
        ICON_EYE,
        TOOLTIP_VIEW,
        CLASS_BTN_ACTION
    );
    
    if (can(PERMISSION_EDIT)) {
        $html .= renderActionButton(
            ROUTE_EDIT,
            [FIELD_ID => $student['id']],
            ICON_EDIT,
            TOOLTIP_EDIT,
            CLASS_BTN_ACTION_PRIMARY
        );
    }
    
    if (can(PERMISSION_DELETE)) {
        $html .= renderDeleteButton($student['id']);
    }
    
    $html .= '</div></td>';
    return $html;
}

function renderStudentRow(array $student): string
{
    $html = '<tr>';
    $html .= renderStudentCell($student);
    $html .= renderContactCell($student['email']);
    $html .= renderPhoneCell($student['phone']);
    $html .= renderDateCell($student['created_at']);
    $html .= renderActionsCell($student);
    $html .= '</tr>';
    
    return $html;
}

$stats = $stats ?? ['total' => 0, 'today' => 0, 'week' => 0, 'month' => 0];
$search = $search ?? '';
$filters = $filters ?? [];
$students = $students ?? [];

?>
<div class="<?= CLASS_HEADER_CONTAINER ?>">
    <div>
        <h1 class="<?= CLASS_TITLE ?>">
            <?= e(PAGE_TITLE) ?>
        </h1>
        <p class="<?= CLASS_SUBTITLE ?>"><?= e(PAGE_SUBTITLE) ?></p>
    </div>
    <?php if (can(PERMISSION_CREATE)): ?>
        <a class="<?= CLASS_BTN_PRIMARY ?>" href="<?= route(ROUTE_CREATE) ?>">
            <i class="<?= ICON_PLUS ?>"></i>
            <?= e(BTN_NEW_STUDENT) ?>
        </a>
    <?php endif; ?>
</div>

<?= renderStatsSection($stats) ?>

<div class="<?= CLASS_FILTER_CARD ?>">
    <div class="<?= CLASS_FILTER_BODY ?>">
        <form method="GET" action="<?= route(ROUTE_INDEX) ?>" id="filterForm">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <?= renderSearchInput($search) ?>
                </div>
                
                <div class="col-md-2">
                    <?= renderPerPageSelect($paginator->perPage()) ?>
                </div>

                <div class="col-md-2">
                    <?= renderSortBySelect($filters[FIELD_SORT_BY] ?? SORT_ID) ?>
                </div>

                <div class="col-md-3 text-md-end">
                    <button class="<?= CLASS_BTN_FILTER ?>" type="submit">
                        <?= e(BTN_FILTER) ?>
                    </button>
                    <?php if (hasActiveFilters($search, $filters)): ?>
                    <a href="<?= route(ROUTE_INDEX) ?>" class="<?= CLASS_BTN_CLEAN ?>" title="<?= e(BTN_CLEAN) ?>">
                        <i class="<?= ICON_REFRESH ?>"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (empty($students)): ?>
    <?= renderEmptyState($search) ?>
<?php else: ?>
    <div class="<?= CLASS_CARD ?> overflow-hidden">
        <div class="<?= CLASS_TABLE_RESPONSIVE ?>">
            <table class="<?= CLASS_TABLE ?>">
                <thead class="<?= CLASS_TABLE_HEAD ?>">
                    <tr>
                        <th class="ps-4"><?= e(TABLE_HEADER_STUDENT) ?></th>
                        <th><?= e(TABLE_HEADER_CONTACT) ?></th>
                        <th><?= e(TABLE_HEADER_PHONE) ?></th>
                        <th><?= e(TABLE_HEADER_REGISTER) ?></th>
                        <th class="text-end pe-4"><?= e(TABLE_HEADER_ACTIONS) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <?= renderStudentRow($student) ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="<?= CLASS_CARD_FOOTER ?>">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="text-muted text-sm mb-2 mb-md-0">
                    <?= $paginator->info() ?>
                </div>
                <div>
                    <?= $paginator->render() ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
