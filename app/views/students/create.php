<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Page Header -->
        <div class="d-flex align-items-center mb-4">
            <a href="<?= route('students.index') ?>" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="h2 mb-1">
                    <i class="bi bi-person-plus text-primary"></i>
                    Crear Nuevo Estudiante
                </h1>
                <p class="text-muted mb-0">Completa el formulario para registrar un nuevo estudiante</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Información del Estudiante
                </h5>
            </div>
            <div class="card-body p-4">
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= e($errors['general']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= route('students.store') ?>" novalidate>
                    <?= csrf_field() ?>
                    
                    <!-- Nombre -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="name">
                            <i class="bi bi-person text-primary me-1"></i>
                            Nombre Completo <span class="text-danger">*</span>
                        </label>
                        <input 
                            class="form-control form-control-lg <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="<?= e(old('name', $old['name'] ?? '')) ?>" 
                            placeholder="Ej: Juan Pérez García"
                            required
                            autofocus
                        >
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                <?= e($errors['name']) ?>
                            </div>
                        <?php else: ?>
                            <small class="form-text text-muted">Mínimo 3 caracteres, máximo <?= MAX_NAME_LENGTH ?></small>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="email">
                            <i class="bi bi-envelope text-primary me-1"></i>
                            Correo Electrónico <span class="text-danger">*</span>
                        </label>
                        <input 
                            class="form-control form-control-lg <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="<?= e(old('email', $old['email'] ?? '')) ?>" 
                            placeholder="Ej: estudiante@ejemplo.com"
                            required
                        >
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                <?= e($errors['email']) ?>
                            </div>
                        <?php else: ?>
                            <small class="form-text text-muted">Formato válido de correo electrónico</small>
                        <?php endif; ?>
                    </div>

                    <!-- Teléfono -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="phone">
                            <i class="bi bi-telephone text-primary me-1"></i>
                            Número de Teléfono <span class="text-danger">*</span>
                        </label>
                        <input 
                            class="form-control form-control-lg <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                            type="tel" 
                            name="phone" 
                            id="phone" 
                            value="<?= e(old('phone', $old['phone'] ?? '')) ?>" 
                            placeholder="Ej: +52 123 456 7890"
                            required
                        >
                        <?php if (isset($errors['phone'])): ?>
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                <?= e($errors['phone']) ?>
                            </div>
                        <?php else: ?>
                            <small class="form-text text-muted">Formato: +código país seguido del número</small>
                        <?php endif; ?>
                    </div>

                    <!-- Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                        <a class="btn btn-outline-secondary px-4" href="<?= route('students.index') ?>">
                            <i class="bi bi-x-circle me-1"></i>
                            Cancelar
                        </a>
                        <button class="btn btn-primary px-4" type="submit">
                            <i class="bi bi-check-circle me-1"></i>
                            Guardar Estudiante
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Card -->
        <div class="card border-0 bg-light mt-3">
            <div class="card-body">
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Nota:</strong> Todos los campos marcados con <span class="text-danger">*</span> son obligatorios.
                </small>
            </div>
        </div>
    </div>
</div>
