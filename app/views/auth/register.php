<?php
if (!auth_check() || !auth_is_admin()) {
    flash('error', 'No tienes permisos para acceder a esta página.');
    redirect_to(route('auth.login'));
    exit();
}

require_once __DIR__ . '/../layouts/header.php';

const FIELD_USERNAME = 'username';
const FIELD_EMAIL = 'email';
const FIELD_FULL_NAME = 'full_name';
const FIELD_PASSWORD = 'password';
const FIELD_PASSWORD_CONFIRMATION = 'password_confirmation';
const ERROR_GENERAL = 'general';

const LABEL_USERNAME = 'Nombre de Usuario';
const LABEL_EMAIL = 'Correo Electrónico';
const LABEL_FULL_NAME = 'Nombre Completo';
const LABEL_PASSWORD = 'Contraseña';
const LABEL_PASSWORD_CONFIRM = 'Confirmar Contraseña';

const PLACEHOLDER_USERNAME = 'Ej: juan_perez';
const PLACEHOLDER_EMAIL = 'ejemplo@correo.com';
const PLACEHOLDER_FULL_NAME = 'Juan Pérez García';
const PLACEHOLDER_PASSWORD = 'Mínimo 6 caracteres';
const PLACEHOLDER_PASSWORD_CONFIRM = 'Repite tu contraseña';

const HELP_USERNAME = '3-50 caracteres, solo letras, números y guiones bajos';
const HELP_PASSWORD = 'Mínimo 6 caracteres. Usa una contraseña fuerte';
const INFO_REQUIRED = 'Todos los campos marcados con <span class="text-danger">*</span> son obligatorios';

const TITLE_REGISTER = 'Registrar Nuevo Usuario';
const SUBTITLE_REGISTER = 'Crea una cuenta para un nuevo miembro del equipo';
const BUTTON_CREATE = 'Crear Usuario';
const BUTTON_CANCEL = 'Cancelar';
const BUTTON_BACK = 'Volver';

const MIN_LENGTH_USERNAME = 3;
const MAX_LENGTH_USERNAME = 50;
const MIN_LENGTH_FULL_NAME = 3;
const MAX_LENGTH_FULL_NAME = 100;
const MIN_LENGTH_PASSWORD = 6;
const MAX_LENGTH_PASSWORD = 255;

const PATTERN_USERNAME = '[a-zA-Z0-9_]+';

const ICON_USER = 'bi-person-plus-fill';
const ICON_BACK = 'bi-arrow-left';
const ICON_ERROR = 'bi-exclamation-octagon-fill';
const ICON_USERNAME = 'bi-person';
const ICON_EMAIL = 'bi-envelope';
const ICON_FULL_NAME = 'bi-person-badge';
const ICON_LOCK = 'bi-lock';
const ICON_LOCK_FILL = 'bi-lock-fill';
const ICON_EYE = 'bi-eye';
const ICON_CHECK = 'bi-person-check';
const ICON_CANCEL = 'bi-x-circle';
const ICON_INFO = 'bi-info-circle-fill';
const ICON_EXCLAMATION = 'bi-exclamation-circle';

function renderPageHeader(string $title, string $subtitle, string $backRoute, string $backText): void
{
    echo '<div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">
                    <i class="' . ICON_USER . ' text-primary"></i> ' . $title . '
                </h1>
                <p class="text-muted mb-0">' . $subtitle . '</p>
            </div>
            <a href="' . route($backRoute) . '" class="btn btn-outline-secondary">
                <i class="' . ICON_BACK . ' me-1"></i>' . $backText . '
            </a>
          </div>';
}

function renderGeneralError(array $errors): void
{
    if (!isset($errors[ERROR_GENERAL])) {
        return;
    }
    
    echo '<div class="alert alert-danger alert-dismissible" role="alert">
            <i class="' . ICON_ERROR . ' me-2"></i>
            <span>' . e($errors[ERROR_GENERAL]) . '</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
          </div>';
}

function renderFormField(string $id, string $type, string $label, string $placeholder, string $icon, array $errors, array $old, array $attributes = [], ?string $help = null): void
{
    $hasError = isset($errors[$id]);
    $errorClass = $hasError ? 'is-invalid' : '';
    $oldValue = e($old[$id] ?? '');
    
    echo '<div class="mb-3">
            <label for="' . $id . '" class="form-label">
                <i class="' . $icon . ' me-1"></i>' . $label . ' <span class="text-danger">*</span>
            </label>
            <input 
                type="' . $type . '" 
                class="form-control ' . $errorClass . '" 
                id="' . $id . '" 
                name="' . $id . '" 
                value="' . $oldValue . '"
                placeholder="' . $placeholder . '"';
    
    foreach ($attributes as $key => $value) {
        echo ' ' . $key . '="' . $value . '"';
    }
    
    echo ' aria-describedby="' . $id . 'Help">';
    
    if ($hasError) {
        echo '<div class="invalid-feedback d-block" id="' . $id . 'Help">
                <i class="' . ICON_EXCLAMATION . ' me-1"></i>' . e($errors[$id]) . '
              </div>';
    } elseif ($help) {
        echo '<small class="form-text text-muted">' . $help . '</small>';
    }
    
    echo '</div>';
}

function renderPasswordField(string $id, string $label, string $placeholder, string $icon, array $errors, ?string $help = null): void
{
    $hasError = isset($errors[$id]);
    $errorClass = $hasError ? 'is-invalid' : '';
    $toggleId = 'toggle' . ucfirst($id);
    
    echo '<div class="mb-3">
            <label for="' . $id . '" class="form-label">
                <i class="' . $icon . ' me-1"></i>' . $label . ' <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <input 
                    type="password" 
                    class="form-control ' . $errorClass . '" 
                    id="' . $id . '" 
                    name="' . $id . '" 
                    placeholder="' . $placeholder . '"
                    autocomplete="new-password"
                    required
                    minlength="' . MIN_LENGTH_PASSWORD . '"
                    maxlength="' . MAX_LENGTH_PASSWORD . '"
                    aria-describedby="' . $id . 'Help"
                >
                <button 
                    class="btn btn-outline-secondary" 
                    type="button" 
                    id="' . $toggleId . '"
                    aria-label="Mostrar u ocultar contraseña"
                    title="Mostrar contraseña"
                >
                    <i class="' . ICON_EYE . '"></i>
                </button>
            </div>';
    
    if ($hasError) {
        echo '<div class="invalid-feedback d-block" id="' . $id . 'Help">
                <i class="' . ICON_EXCLAMATION . ' me-1"></i>' . e($errors[$id]) . '
              </div>';
    } elseif ($help) {
        echo '<small class="form-text text-muted d-block mt-1">' . $help . '</small>';
    }
    
    echo '</div>';
}

function renderInfoAlert(string $message): void
{
    echo '<div class="alert alert-info d-flex align-items-start mb-4" role="alert">
            <i class="' . ICON_INFO . ' me-2 flex-shrink-0 mt-1"></i>
            <small>' . $message . '</small>
          </div>';
}

function renderFormButtons(string $submitText, string $submitIcon, string $cancelRoute, string $cancelText): void
{
    echo '<div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="' . $submitIcon . ' me-2"></i>' . $submitText . '
            </button>
            <a href="' . route($cancelRoute) . '" class="btn btn-outline-secondary">
                <i class="' . ICON_CANCEL . ' me-1"></i>' . $cancelText . '
            </a>
          </div>';
}
?>

<?php renderPageHeader(TITLE_REGISTER, SUBTITLE_REGISTER, 'students.index', BUTTON_BACK); ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8 col-xl-7">
        <div class="card">
            <div class="card-body p-4">
                <?php renderGeneralError($errors ?? []); ?>

                <form method="POST" action="<?= route('auth.register.post') ?>" class="needs-validation" novalidate autocomplete="on">
                    <?= csrf_field() ?>
                    
                    <?php
                    $errors = $errors ?? [];
                    $old = $old ?? [];
                    
                    renderFormField(
                        FIELD_USERNAME,
                        'text',
                        LABEL_USERNAME,
                        PLACEHOLDER_USERNAME,
                        ICON_USERNAME,
                        $errors,
                        $old,
                        [
                            'autocomplete' => 'username',
                            'required' => 'required',
                            'autofocus' => 'autofocus',
                            'minlength' => MIN_LENGTH_USERNAME,
                            'maxlength' => MAX_LENGTH_USERNAME,
                            'pattern' => PATTERN_USERNAME
                        ],
                        HELP_USERNAME
                    );
                    
                    renderFormField(
                        FIELD_EMAIL,
                        'email',
                        LABEL_EMAIL,
                        PLACEHOLDER_EMAIL,
                        ICON_EMAIL,
                        $errors,
                        $old,
                        [
                            'autocomplete' => 'email',
                            'required' => 'required'
                        ]
                    );
                    
                    renderFormField(
                        FIELD_FULL_NAME,
                        'text',
                        LABEL_FULL_NAME,
                        PLACEHOLDER_FULL_NAME,
                        ICON_FULL_NAME,
                        $errors,
                        $old,
                        [
                            'autocomplete' => 'name',
                            'required' => 'required',
                            'minlength' => MIN_LENGTH_FULL_NAME,
                            'maxlength' => MAX_LENGTH_FULL_NAME
                        ]
                    );
                    
                    renderPasswordField(
                        FIELD_PASSWORD,
                        LABEL_PASSWORD,
                        PLACEHOLDER_PASSWORD,
                        ICON_LOCK,
                        $errors,
                        HELP_PASSWORD
                    );
                    
                    renderPasswordField(
                        FIELD_PASSWORD_CONFIRMATION,
                        LABEL_PASSWORD_CONFIRM,
                        PLACEHOLDER_PASSWORD_CONFIRM,
                        ICON_LOCK_FILL,
                        $errors
                    );
                    
                    renderInfoAlert(INFO_REQUIRED);
                    renderFormButtons(BUTTON_CREATE, ICON_CHECK, 'students.index', BUTTON_CANCEL);
                    ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
