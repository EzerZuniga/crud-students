<?php require_once __DIR__ . '/../layouts/auth_header.php'; ?>

<div class="container">
    <div class="auth-card">
        <div class="auth-header">
            <i class="bi bi-person-plus-fill" style="font-size: 3rem;"></i>
            <h1 class="mt-2">Crear Cuenta</h1>
            <p class="mb-0">Regístrate para comenzar</p>
        </div>
        
        <div class="auth-body">
            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-octagon me-2"></i><?= e($errors['general']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= route('auth.register.post') ?>" class="needs-validation" novalidate>
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="bi bi-person me-1"></i>Nombre de Usuario *
                    </label>
                    <input 
                        type="text" 
                        class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                        id="username" 
                        name="username" 
                        value="<?= e($old['username'] ?? '') ?>"
                        placeholder="Ej: juan_perez"
                        required
                        autofocus
                        minlength="3"
                        maxlength="50"
                    >
                    <?php if (isset($errors['username'])): ?>
                        <div class="invalid-feedback d-block">
                            <?= e($errors['username']) ?>
                        </div>
                    <?php else: ?>
                        <div class="form-text">Mínimo 3 caracteres, sin espacios</div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope me-1"></i>Correo Electrónico *
                    </label>
                    <input 
                        type="email" 
                        class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                        id="email" 
                        name="email" 
                        value="<?= e($old['email'] ?? '') ?>"
                        placeholder="ejemplo@correo.com"
                        required
                    >
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback d-block">
                            <?= e($errors['email']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="full_name" class="form-label">
                        <i class="bi bi-person-badge me-1"></i>Nombre Completo *
                    </label>
                    <input 
                        type="text" 
                        class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" 
                        id="full_name" 
                        name="full_name" 
                        value="<?= e($old['full_name'] ?? '') ?>"
                        placeholder="Juan Pérez García"
                        required
                    >
                    <?php if (isset($errors['full_name'])): ?>
                        <div class="invalid-feedback d-block">
                            <?= e($errors['full_name']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-1"></i>Contraseña *
                    </label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                            id="password" 
                            name="password" 
                            placeholder="Mínimo 6 caracteres"
                            required
                            minlength="6"
                        >
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback d-block">
                                <?= e($errors['password']) ?>
                            </div>
                        <?php else: ?>
                            <div class="form-text w-100">Usa al menos 6 caracteres</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">
                        <i class="bi bi-lock-fill me-1"></i>Confirmar Contraseña *
                    </label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control <?= isset($errors['password_confirmation']) ? 'is-invalid' : '' ?>" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            placeholder="Repite tu contraseña"
                            required
                            minlength="6"
                        >
                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                            <i class="bi bi-eye" id="eyeIconConfirm"></i>
                        </button>
                        <?php if (isset($errors['password_confirmation'])): ?>
                            <div class="invalid-feedback d-block">
                                <?= e($errors['password_confirmation']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    <small>Los campos marcados con * son obligatorios</small>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-person-check me-2"></i>Crear Cuenta
                </button>

                <div class="text-center">
                    <p class="mb-0">
                        ¿Ya tienes cuenta? 
                        <a href="<?= route('auth.login') ?>" class="text-decoration-none fw-bold">
                            Inicia sesión aquí
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (password.type === 'password') {
            password.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            password.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    });

    document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
        const password = document.getElementById('password_confirmation');
        const eyeIcon = document.getElementById('eyeIconConfirm');
        
        if (password.type === 'password') {
            password.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            password.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    });

    // Password match validation
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');

    passwordConfirm.addEventListener('input', function() {
        if (password.value !== passwordConfirm.value) {
            passwordConfirm.setCustomValidity('Las contraseñas no coinciden');
        } else {
            passwordConfirm.setCustomValidity('');
        }
    });

    // Bootstrap form validation
    (function() {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>

<?php require_once __DIR__ . '/../layouts/auth_footer.php'; ?>
