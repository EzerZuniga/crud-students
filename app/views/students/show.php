<?php

declare(strict_types=1);

const PAGE_TITLE = 'Detalles del Estudiante';
const PAGE_SUBTITLE = 'Información completa del registro';

const SECTION_CONTACT = 'Información de Contacto';
const SECTION_SYSTEM = 'Información del Sistema';

const LABEL_EMAIL = 'Correo Electrónico';
const LABEL_PHONE = 'Teléfono';
const LABEL_CREATED_AT = 'Fecha de Registro';
const LABEL_STUDENT_ID = 'ID del Estudiante';

const BTN_EDIT = 'Editar';
const BTN_DELETE = 'Eliminar';
const BTN_SEND_EMAIL = 'Enviar Correo';
const BTN_CALL = 'Llamar';
const BTN_EDIT_INFO = 'Editar Información';
const BTN_BACK_LIST = 'Volver a la lista';

const CONFIRM_DELETE_PREFIX = '¿Estás seguro de eliminar a';
const CONFIRM_DELETE_SUFFIX = '?';

const ROUTE_INDEX = 'students.index';
const ROUTE_EDIT = 'students.edit';
const ROUTE_DELETE = 'students.delete';

const FIELD_ID = 'id';
const FIELD_NAME = 'name';
const FIELD_EMAIL = 'email';
const FIELD_PHONE = 'phone';
const FIELD_CREATED_AT = 'created_at';

const ICON_BACK = 'bi bi-arrow-left';
const ICON_PERSON = 'bi bi-person-circle text-info';
const ICON_PENCIL = 'bi bi-pencil me-1';
const ICON_TRASH = 'bi bi-trash me-1';
const ICON_HASH = 'bi bi-hash';
const ICON_TELEPHONE_FILL = 'bi bi-telephone-fill me-2';
const ICON_INFO_CIRCLE = 'bi bi-info-circle-fill me-2';
const ICON_ENVELOPE_FILL = 'bi bi-envelope-fill text-primary me-2';
const ICON_PHONE_FILL = 'bi bi-telephone-fill text-success me-2';
const ICON_CALENDAR_CHECK = 'bi bi-calendar-check text-info me-2';
const ICON_HASH_SECONDARY = 'bi bi-hash text-secondary me-2';
const ICON_ENVELOPE = 'bi bi-envelope me-1';
const ICON_TELEPHONE = 'bi bi-telephone me-1';

const CLASS_ROW = 'row justify-content-center';
const CLASS_COL = 'col-lg-10';
const CLASS_HEADER_CONTAINER = 'd-flex justify-content-between align-items-center mb-4';
const CLASS_HEADER_LEFT = 'd-flex align-items-center';
const CLASS_BTN_BACK = 'btn btn-outline-secondary me-3';
const CLASS_TITLE = 'h2 mb-1';
const CLASS_SUBTITLE = 'text-muted mb-0';
const CLASS_BTN_GROUP = 'btn-group';
const CLASS_BTN_PRIMARY = 'btn btn-primary';
const CLASS_BTN_DANGER = 'btn btn-danger';
const CLASS_CARD = 'card border-0 shadow-sm mb-4';
const CLASS_CARD_HEADER = 'card-header bg-info text-white py-3';
const CLASS_CARD_BODY = 'card-body p-4';
const CLASS_AVATAR = 'avatar-lg bg-white text-info me-3';
const CLASS_SECTION_TITLE = 'text-muted mb-3';
const CLASS_FIELD_LABEL = 'small text-muted d-block mb-1';
const CLASS_FIELD_CONTAINER = 'd-flex align-items-center';
const CLASS_FIELD_GROUP = 'mb-3';
const CLASS_BADGE = 'badge bg-secondary';
const CLASS_ACTIONS_CONTAINER = 'd-flex gap-2 justify-content-center mb-4';
const CLASS_BTN_OUTLINE_PRIMARY = 'btn btn-outline-primary';
const CLASS_BTN_OUTLINE_SUCCESS = 'btn btn-outline-success';
const CLASS_BTN_OUTLINE_WARNING = 'btn btn-outline-warning';
const CLASS_BTN_LINK = 'btn btn-link';

const DATE_FORMAT_DETAIL = 'd/m/Y H:i:s';

const AVATAR_SIZE = '64px';
const AVATAR_BORDER_RADIUS = '50%';
const AVATAR_FONT_SIZE = '1.75rem';
const AVATAR_FONT_WEIGHT = '700';

function getStudentInitial(string $name): string
{
    return strtoupper(substr($name, 0, 1));
}

function getConfirmDeleteMessage(string $name): string
{
    return sprintf(
        '%s %s%s',
        CONFIRM_DELETE_PREFIX,
        e($name),
        CONFIRM_DELETE_SUFFIX
    );
}

function renderBackButton(): string
{
    return sprintf(
        '<a href="%s" class="%s"><i class="%s"></i></a>',
        htmlspecialchars(route(ROUTE_INDEX), ENT_QUOTES, 'UTF-8'),
        CLASS_BTN_BACK,
        ICON_BACK
    );
}

function renderPageHeader(): string
{
    $html = sprintf('<div class="%s">', CLASS_HEADER_LEFT);
    $html .= renderBackButton();
    $html .= '<div>';
    $html .= sprintf(
        '<h1 class="%s"><i class="%s"></i>%s</h1>',
        CLASS_TITLE,
        ICON_PERSON,
        e(PAGE_TITLE)
    );
    $html .= sprintf(
        '<p class="%s">%s</p>',
        CLASS_SUBTITLE,
        e(PAGE_SUBTITLE)
    );
    $html .= '</div></div>';
    
    return $html;
}

function renderActionButtons(string $studentId, string $studentName): string
{
    $editBtn = sprintf(
        '<a href="%s" class="%s"><i class="%s"></i>%s</a>',
        htmlspecialchars(route(ROUTE_EDIT, [FIELD_ID => $studentId]), ENT_QUOTES, 'UTF-8'),
        CLASS_BTN_PRIMARY,
        ICON_PENCIL,
        e(BTN_EDIT)
    );
    
    $deleteBtn = sprintf(
        '<form method="POST" action="%s" class="d-inline" onsubmit="return confirm(\'%s\');">%s<button type="submit" class="%s"><i class="%s"></i>%s</button></form>',
        htmlspecialchars(route(ROUTE_DELETE, [FIELD_ID => $studentId]), ENT_QUOTES, 'UTF-8'),
        htmlspecialchars(getConfirmDeleteMessage($studentName), ENT_QUOTES, 'UTF-8'),
        csrf_field(),
        CLASS_BTN_DANGER,
        ICON_TRASH,
        e(BTN_DELETE)
    );
    
    return sprintf(
        '<div class="%s">%s%s</div>',
        CLASS_BTN_GROUP,
        $editBtn,
        $deleteBtn
    );
}

function renderCardHeader(string $initial, string $name, string $id): string
{
    $html = sprintf('<div class="%s">', CLASS_CARD_HEADER);
    $html .= sprintf('<div class="%s">', CLASS_FIELD_CONTAINER);
    $html .= sprintf(
        '<div class="%s">%s</div>',
        CLASS_AVATAR,
        $initial
    );
    $html .= '<div>';
    $html .= sprintf('<h3 class="mb-1">%s</h3>', e($name));
    $html .= sprintf(
        '<p class="mb-0 opacity-75"><i class="%s"></i> ID: %s</p>',
        ICON_HASH,
        e($id)
    );
    $html .= '</div>';
    $html .= '</div></div>';
    
    return $html;
}

function renderFieldGroup(string $label, string $icon, string $value, bool $isLink = false, string $linkType = ''): string
{
    $html = sprintf('<div class="%s">', CLASS_FIELD_GROUP);
    $html .= sprintf('<label class="%s">%s</label>', CLASS_FIELD_LABEL, e($label));
    $html .= sprintf('<div class="%s">', CLASS_FIELD_CONTAINER);
    $html .= sprintf('<i class="%s"></i>', $icon);
    
    if ($isLink) {
        $href = $linkType === 'email' 
            ? sprintf('mailto:%s', e($value)) 
            : sprintf('tel:%s', e($value));
        $html .= sprintf(
            '<a href="%s" class="text-decoration-none">%s</a>',
            $href,
            e($value)
        );
    } else {
        $html .= sprintf('<span>%s</span>', e($value));
    }
    
    $html .= '</div></div>';
    
    return $html;
}

function renderIdBadge(string $id): string
{
    $html = sprintf('<div class="%s">', CLASS_FIELD_GROUP);
    $html .= sprintf('<label class="%s">%s</label>', CLASS_FIELD_LABEL, e(LABEL_STUDENT_ID));
    $html .= sprintf('<div class="%s">', CLASS_FIELD_CONTAINER);
    $html .= sprintf('<i class="%s"></i>', ICON_HASH_SECONDARY);
    $html .= sprintf('<span class="%s">%s</span>', CLASS_BADGE, e($id));
    $html .= '</div></div>';
    
    return $html;
}

function renderContactSection(string $email, string $phone): string
{
    $html = '<div class="col-md-6">';
    $html .= sprintf(
        '<h5 class="%s"><i class="%s"></i>%s</h5>',
        CLASS_SECTION_TITLE,
        ICON_TELEPHONE_FILL,
        e(SECTION_CONTACT)
    );
    $html .= renderFieldGroup(LABEL_EMAIL, ICON_ENVELOPE_FILL, $email, true, 'email');
    $html .= renderFieldGroup(LABEL_PHONE, ICON_PHONE_FILL, $phone, true, 'tel');
    $html .= '</div>';
    
    return $html;
}

function renderSystemSection(string $createdAt, string $id): string
{
    $html = '<div class="col-md-6">';
    $html .= sprintf(
        '<h5 class="%s"><i class="%s"></i>%s</h5>',
        CLASS_SECTION_TITLE,
        ICON_INFO_CIRCLE,
        e(SECTION_SYSTEM)
    );
    $html .= renderFieldGroup(
        LABEL_CREATED_AT,
        ICON_CALENDAR_CHECK,
        format_date($createdAt, DATE_FORMAT_DETAIL)
    );
    $html .= renderIdBadge($id);
    $html .= '</div>';
    
    return $html;
}

function renderQuickActions(string $email, string $phone, string $studentId): string
{
    $emailBtn = sprintf(
        '<a href="mailto:%s" class="%s"><i class="%s"></i>%s</a>',
        e($email),
        CLASS_BTN_OUTLINE_PRIMARY,
        ICON_ENVELOPE,
        e(BTN_SEND_EMAIL)
    );
    
    $callBtn = sprintf(
        '<a href="tel:%s" class="%s"><i class="%s"></i>%s</a>',
        e($phone),
        CLASS_BTN_OUTLINE_SUCCESS,
        ICON_TELEPHONE,
        e(BTN_CALL)
    );
    
    $editBtn = sprintf(
        '<a href="%s" class="%s"><i class="%s"></i>%s</a>',
        htmlspecialchars(route(ROUTE_EDIT, [FIELD_ID => $studentId]), ENT_QUOTES, 'UTF-8'),
        CLASS_BTN_OUTLINE_WARNING,
        ICON_PENCIL,
        e(BTN_EDIT_INFO)
    );
    
    return sprintf(
        '<div class="%s">%s%s%s</div>',
        CLASS_ACTIONS_CONTAINER,
        $emailBtn,
        $callBtn,
        $editBtn
    );
}

function renderBackLink(): string
{
    return sprintf(
        '<div class="text-center"><a href="%s" class="%s"><i class="%s"></i>%s</a></div>',
        htmlspecialchars(route(ROUTE_INDEX), ENT_QUOTES, 'UTF-8'),
        CLASS_BTN_LINK,
        ICON_BACK,
        e(BTN_BACK_LIST)
    );
}

function renderAvatarStyles(): string
{
    return sprintf(
        '<style>.avatar-lg{width:%s;height:%s;border-radius:%s;display:inline-flex;align-items:center;justify-content:center;font-weight:%s;font-size:%s;}</style>',
        AVATAR_SIZE,
        AVATAR_SIZE,
        AVATAR_BORDER_RADIUS,
        AVATAR_FONT_WEIGHT,
        AVATAR_FONT_SIZE
    );
}

$student = $student ?? [];
$studentId = $student[FIELD_ID] ?? '';
$studentName = $student[FIELD_NAME] ?? '';
$studentEmail = $student[FIELD_EMAIL] ?? '';
$studentPhone = $student[FIELD_PHONE] ?? '';
$studentCreatedAt = $student[FIELD_CREATED_AT] ?? '';
$studentInitial = getStudentInitial($studentName);

?>
<div class="<?= CLASS_ROW ?>">
    <div class="<?= CLASS_COL ?>">
        <div class="<?= CLASS_HEADER_CONTAINER ?>">
            <?= renderPageHeader() ?>
            <?= renderActionButtons($studentId, $studentName) ?>
        </div>

        <div class="<?= CLASS_CARD ?>">
            <?= renderCardHeader($studentInitial, $studentName, $studentId) ?>
            <div class="<?= CLASS_CARD_BODY ?>">
                <div class="row g-4">
                    <?= renderContactSection($studentEmail, $studentPhone) ?>
                    <?= renderSystemSection($studentCreatedAt, $studentId) ?>
                </div>
            </div>
        </div>

        <?= renderQuickActions($studentEmail, $studentPhone, $studentId) ?>

        <?= renderBackLink() ?>
    </div>
</div>

<?= renderAvatarStyles() ?>
