<?php require_once __DIR__ . '/../layouts/auth_header.php'; ?>

<div class="container">
    <div class="auth-card">
        <div class="auth-header">
            <i class="bi bi-person-circle" style="font-size: 3rem;"></i>
            <h1 class="mt-2">Iniciar Sesión</h1>
            <p class="mb-0">Accede a tu cuenta</p>
        </div>
        
        <div class="auth-body">
            <?php if (flash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?= e(flash('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (flash('warning')): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?= e(flash('warning')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-octagon me-2"></i><?= e($errors['general']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= route('auth.login.post') ?>" class="needs-validation" novalidate>
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="bi bi-person me-1"></i>Usuario o Email
                    </label>
                    <input 
                        type="text" 
                        class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                        id="username" 
                        name="username" 
                        value="<?= e($old['username'] ?? '') ?>"
                        placeholder="Ingresa tu usuario o email"
                        required
                        autofocus
                    >
                    <?php if (isset($errors['username'])): ?>
                        <div class="invalid-feedback d-block">
                            <?= e($errors['username']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-1"></i>Contraseña
                    </label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                            id="password" 
                            name="password" 
                            placeholder="Ingresa tu contraseña"
                            required
                        >
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback d-block">
                                <?= e($errors['password']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        Recordarme
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                </button>

                <div class="text-center">
                    <p class="mb-0">
                        ¿No tienes cuenta? 
                        <a href="<?= route('auth.register') ?>" class="text-decoration-none fw-bold">
                            Regístrate aquí
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
