<?php

declare(strict_types=1);

const PAGE_TITLE = 'Crear Nuevo Estudiante';
const PAGE_SUBTITLE = 'Completa el formulario para registrar un nuevo estudiante';
const CARD_TITLE = 'Información del Estudiante';

const ROUTE_INDEX = 'students.index';
const ROUTE_STORE = 'students.store';

const FIELD_NAME = 'name';
const FIELD_EMAIL = 'email';
const FIELD_PHONE = 'phone';
const FIELD_GENERAL = 'general';

const LABEL_NAME = 'Nombre Completo';
const LABEL_EMAIL = 'Correo Electrónico';
const LABEL_PHONE = 'Número de Teléfono';

const PLACEHOLDER_NAME = 'Ej: Juan Pérez García';
const PLACEHOLDER_EMAIL = 'Ej: estudiante@ejemplo.com';
const PLACEHOLDER_PHONE = 'Ej: +52 123 456 7890';

const HELP_NAME = 'Mínimo 3 caracteres, máximo';
const HELP_EMAIL = 'Formato válido de correo electrónico';
const HELP_PHONE = 'Formato: +código país seguido del número';

const BTN_BACK = 'Volver';
const BTN_CANCEL = 'Cancelar';
const BTN_SAVE = 'Guardar Estudiante';

const NOTE_REQUIRED = 'Todos los campos marcados con';
const NOTE_REQUIRED_SUFFIX = 'son obligatorios.';
const NOTE_PREFIX = 'Nota:';

const ICON_BACK = 'bi bi-arrow-left';
const ICON_PERSON_PLUS = 'bi bi-person-plus text-primary';
const ICON_FILE = 'bi bi-file-earmark-text me-2';
const ICON_ERROR = 'bi bi-exclamation-triangle-fill me-2';
const ICON_PERSON = 'bi bi-person text-primary me-1';
const ICON_ENVELOPE = 'bi bi-envelope text-primary me-1';
const ICON_PHONE = 'bi bi-telephone text-primary me-1';
const ICON_EXCLAMATION = 'bi bi-exclamation-circle me-1';
const ICON_CANCEL = 'bi bi-x-circle me-1';
const ICON_SAVE = 'bi bi-check-circle me-1';
const ICON_INFO = 'bi bi-info-circle me-1';

const CLASS_ROW = 'row justify-content-center';
const CLASS_COL = 'col-lg-8';
const CLASS_HEADER_CONTAINER = 'd-flex align-items-center mb-4';
const CLASS_BTN_BACK = 'btn btn-outline-secondary me-3';
const CLASS_TITLE = 'h2 mb-1';
const CLASS_SUBTITLE = 'text-muted mb-0';
const CLASS_CARD = 'card border-0 shadow-sm';
const CLASS_CARD_HEADER = 'card-header bg-primary text-white';
const CLASS_CARD_BODY = 'card-body p-4';
const CLASS_CARD_TITLE = 'mb-0';
const CLASS_ALERT = 'alert alert-danger alert-dismissible fade show';
const CLASS_BTN_CLOSE = 'btn-close';
const CLASS_FORM_GROUP = 'mb-4';
const CLASS_LABEL = 'form-label fw-semibold';
const CLASS_REQUIRED = 'text-danger';
const CLASS_INPUT = 'form-control form-control-lg';
const CLASS_INPUT_INVALID = 'form-control form-control-lg is-invalid';
const CLASS_INVALID_FEEDBACK = 'invalid-feedback';
const CLASS_HELP_TEXT = 'form-text text-muted';
const CLASS_BUTTONS_CONTAINER = 'd-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top';
const CLASS_BTN_CANCEL = 'btn btn-outline-secondary px-4';
const CLASS_BTN_PRIMARY = 'btn btn-primary px-4';
const CLASS_HELP_CARD = 'card border-0 bg-light mt-3';

const REQUIRED_MARK = '*';
const ATTR_REQUIRED = 'required';
const ATTR_AUTOFOCUS = 'autofocus';
const ATTR_NOVALIDATE = 'novalidate';

const DEFAULT_MAX_NAME_LENGTH = 100;

function getMaxNameLength(): int
{
    return defined('MAX_NAME_LENGTH') ? (int)constant('MAX_NAME_LENGTH') : DEFAULT_MAX_NAME_LENGTH;
}

function getOldValue(string $field, array $old = []): string
{
    return old($field, $old[$field] ?? '');
}

function hasError(string $field, array $errors = []): bool
{
    return isset($errors[$field]);
}

function getInputClass(string $field, array $errors = []): string
{
    return hasError($field, $errors) ? CLASS_INPUT_INVALID : CLASS_INPUT;
}

function renderLabel(string $icon, string $label, string $for): string
{
    return sprintf(
        '<label class="%s" for="%s"><i class="%s"></i>%s <span class="%s">%s</span></label>',
        CLASS_LABEL,
        htmlspecialchars($for, ENT_QUOTES, 'UTF-8'),
        $icon,
        e($label),
        CLASS_REQUIRED,
        REQUIRED_MARK
    );
}

function renderInput(string $type, string $name, string $id, string $placeholder, string $value, string $class, bool $required = true, bool $autofocus = false): string
{
    $requiredAttr = $required ? ATTR_REQUIRED : '';
    $autofocusAttr = $autofocus ? ATTR_AUTOFOCUS : '';
    
    return sprintf(
        '<input class="%s" type="%s" name="%s" id="%s" value="%s" placeholder="%s" %s %s>',
        $class,
        htmlspecialchars($type, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($id, ENT_QUOTES, 'UTF-8'),
        e($value),
        htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8'),
        $requiredAttr,
        $autofocusAttr
    );
}

function renderError(string $error): string
{
    return sprintf(
        '<div class="%s"><i class="%s"></i>%s</div>',
        CLASS_INVALID_FEEDBACK,
        ICON_EXCLAMATION,
        e($error)
    );
}

function renderHelpText(string $text): string
{
    return sprintf(
        '<small class="%s">%s</small>',
        CLASS_HELP_TEXT,
        e($text)
    );
}

function renderFormField(string $type, string $field, string $icon, string $label, string $placeholder, string $helpText, array $old = [], array $errors = [], bool $autofocus = false): string
{
    $value = getOldValue($field, $old);
    $inputClass = getInputClass($field, $errors);
    $hasErr = hasError($field, $errors);
    
    $html = sprintf('<div class="%s">', CLASS_FORM_GROUP);
    $html .= renderLabel($icon, $label, $field);
    $html .= renderInput($type, $field, $field, $placeholder, $value, $inputClass, true, $autofocus);
    
    if ($hasErr) {
        $html .= renderError($errors[$field]);
    } else {
        $html .= renderHelpText($helpText);
    }
    
    $html .= '</div>';
    return $html;
}

function renderGeneralError(array $errors = []): string
{
    if (!hasError(FIELD_GENERAL, $errors)) {
        return '';
    }
    
    return sprintf(
        '<div class="%s" role="alert"><i class="%s"></i>%s<button type="button" class="%s" data-bs-dismiss="alert"></button></div>',
        CLASS_ALERT,
        ICON_ERROR,
        e($errors[FIELD_GENERAL]),
        CLASS_BTN_CLOSE
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
    $html = sprintf('<div class="%s">', CLASS_HEADER_CONTAINER);
    $html .= renderBackButton();
    $html .= '<div>';
    $html .= sprintf(
        '<h1 class="%s"><i class="%s"></i>%s</h1>',
        CLASS_TITLE,
        ICON_PERSON_PLUS,
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

function renderCardHeader(): string
{
    return sprintf(
        '<div class="%s"><h5 class="%s"><i class="%s"></i>%s</h5></div>',
        CLASS_CARD_HEADER,
        CLASS_CARD_TITLE,
        ICON_FILE,
        e(CARD_TITLE)
    );
}

function renderActionButtons(): string
{
    $cancelBtn = sprintf(
        '<a class="%s" href="%s"><i class="%s"></i>%s</a>',
        CLASS_BTN_CANCEL,
        htmlspecialchars(route(ROUTE_INDEX), ENT_QUOTES, 'UTF-8'),
        ICON_CANCEL,
        e(BTN_CANCEL)
    );
    
    $submitBtn = sprintf(
        '<button class="%s" type="submit"><i class="%s"></i>%s</button>',
        CLASS_BTN_PRIMARY,
        ICON_SAVE,
        e(BTN_SAVE)
    );
    
    return sprintf(
        '<div class="%s">%s%s</div>',
        CLASS_BUTTONS_CONTAINER,
        $cancelBtn,
        $submitBtn
    );
}

function renderHelpCard(): string
{
    return sprintf(
        '<div class="%s"><div class="card-body"><small class="text-muted"><i class="%s"></i><strong>%s</strong> %s <span class="%s">%s</span> %s</small></div></div>',
        CLASS_HELP_CARD,
        ICON_INFO,
        NOTE_PREFIX,
        NOTE_REQUIRED,
        CLASS_REQUIRED,
        REQUIRED_MARK,
        NOTE_REQUIRED_SUFFIX
    );
}

$errors = $errors ?? [];
$old = $old ?? [];

?>
<div class="<?= CLASS_ROW ?>">
    <div class="<?= CLASS_COL ?>">
        <?= renderPageHeader() ?>

        <div class="<?= CLASS_CARD ?>">
            <?= renderCardHeader() ?>
            <div class="<?= CLASS_CARD_BODY ?>">
                <?= renderGeneralError($errors) ?>

                <form method="POST" action="<?= route(ROUTE_STORE) ?>" <?= ATTR_NOVALIDATE ?>>
                    <?= csrf_field() ?>
                    
                    <?= renderFormField(
                        'text',
                        FIELD_NAME,
                        ICON_PERSON,
                        LABEL_NAME,
                        PLACEHOLDER_NAME,
                        HELP_NAME . ' ' . getMaxNameLength(),
                        $old,
                        $errors,
                        true
                    ) ?>

                    <?= renderFormField(
                        'email',
                        FIELD_EMAIL,
                        ICON_ENVELOPE,
                        LABEL_EMAIL,
                        PLACEHOLDER_EMAIL,
                        HELP_EMAIL,
                        $old,
                        $errors
                    ) ?>

                    <?= renderFormField(
                        'tel',
                        FIELD_PHONE,
                        ICON_PHONE,
                        LABEL_PHONE,
                        PLACEHOLDER_PHONE,
                        HELP_PHONE,
                        $old,
                        $errors
                    ) ?>

                    <?= renderActionButtons() ?>
                </form>
            </div>
        </div>

        <?= renderHelpCard() ?>
    </div>
</div>
