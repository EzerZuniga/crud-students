<?php require_once __DIR__ . '/../layouts/auth_header.php'; ?>

<?php
const ALERT_SUCCESS = 'success';
const ALERT_WARNING = 'warning';
const ALERT_DANGER = 'danger';

const FIELD_USERNAME = 'username';
const FIELD_PASSWORD = 'password';
const FIELD_REMEMBER = 'remember';
const ERROR_GENERAL = 'general';

const PLACEHOLDER_USERNAME = 'ej. admin';
const LABEL_USERNAME = 'Usuario o Email';
const LABEL_PASSWORD = 'Contraseña';
const LABEL_REMEMBER = 'Recordarme';

const TITLE_LOGIN = 'Iniciar Sesión';
const SUBTITLE_LOGIN = 'Ingresa tus credenciales para acceder';
const BUTTON_LOGIN = 'Ingresar';
const FOOTER_TEXT = 'Sistema de Gestión de Estudiantes';

function renderAlert(string $type, string $message): void
{
    $alertClass = "alert alert-{$type} alert-dismissible fade show";
    echo "<div class=\"{$alertClass}\" role=\"alert\">" . e($message) . 
         '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
}

function renderFlashMessages(array $errors): void
{
    if ($successMsg = flash(ALERT_SUCCESS)) {
        renderAlert(ALERT_SUCCESS, $successMsg);
    }

    if ($warningMsg = flash(ALERT_WARNING)) {
        renderAlert(ALERT_WARNING, $warningMsg);
    }

    if (isset($errors[ERROR_GENERAL])) {
        renderAlert(ALERT_DANGER, $errors[ERROR_GENERAL]);
    }
}

function renderInputField(string $id, string $type, string $label, string $placeholder = '', bool $required = true, bool $autofocus = false, string $icon = ''): void
{
    $oldValue = old($id);
    $autofocusAttr = $autofocus ? 'autofocus' : '';
    $requiredAttr = $required ? 'required' : '';
    
    echo "<div class=\"mb-3\">
            <label for=\"{$id}\" class=\"form-label\">{$label}</label>
            <div class=\"input-group\">
                <span class=\"input-group-text\"><i class=\"bi {$icon}\"></i></span>
                <input type=\"{$type}\" 
                       class=\"form-control\" 
                       id=\"{$id}\" 
                       name=\"{$id}\" 
                       value=\"" . e($oldValue) . "\" 
                       placeholder=\"{$placeholder}\" 
                       {$requiredAttr} 
                       {$autofocusAttr}>
            </div>
          </div>";
}

function renderPasswordField(string $id, string $label): void
{
    echo "<div class=\"mb-4\">
            <div class=\"d-flex justify-content-between\">
                <label for=\"{$id}\" class=\"form-label\">{$label}</label>
            </div>
            <div class=\"input-group\">
                <span class=\"input-group-text\"><i class=\"bi bi-lock\"></i></span>
                <input type=\"password\" 
                       class=\"form-control\" 
                       id=\"{$id}\" 
                       name=\"{$id}\" 
                       placeholder=\"••••••••\"
                       required>
            </div>
          </div>";
}

function renderCheckbox(string $id, string $label): void
{
    echo "<div class=\"form-check\">
            <input class=\"form-check-input\" type=\"checkbox\" id=\"{$id}\" name=\"{$id}\">
            <label class=\"form-check-label text-sm\" for=\"{$id}\">{$label}</label>
          </div>";
}
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <i class="bi bi-mortarboard-fill" style="font-size: 3rem; margin-bottom: 0.5rem; display: block;"></i>
            <h1><?= TITLE_LOGIN ?></h1>
            <p><?= SUBTITLE_LOGIN ?></p>
        </div>
        
        <div class="auth-body">
            <?php renderFlashMessages($errors ?? []); ?>

            <form method="POST" action="<?= route('auth.login.post') ?>" class="needs-validation" novalidate autocomplete="on">
                <?= csrf_field() ?>
                
                <?php renderInputField(FIELD_USERNAME, 'text', LABEL_USERNAME, PLACEHOLDER_USERNAME, true, true, 'bi-person'); ?>
                <?php renderPasswordField(FIELD_PASSWORD, LABEL_PASSWORD); ?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <?php renderCheckbox(FIELD_REMEMBER, LABEL_REMEMBER); ?>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-right me-2"></i><?= BUTTON_LOGIN ?>
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center py-3" style="border-top: 1px solid #e5e7eb; margin-top: 1rem;">
            <small class="text-muted"><?= FOOTER_TEXT ?> &copy; <?= date('Y') ?></small>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/auth_footer.php'; ?>
