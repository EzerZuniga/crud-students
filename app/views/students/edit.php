<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center mb-4">
            <a href="<?= route('students.index') ?>" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="h2 mb-1">
                    <i class="bi bi-pencil-square text-warning"></i>
                    Editar Estudiante
                </h1>
                <p class="text-muted mb-0">Actualiza la información del estudiante</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Información del Estudiante #<?= e($old['id']) ?>
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

                <form method="POST" action="<?= route('students.update', ['id' => $old['id']]) ?>" novalidate>
                    <?= csrf_field() ?>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="name">
                            <i class="bi bi-person text-warning me-1"></i>
                            Nombre Completo <span class="text-danger">*</span>
                        </label>
                        <input 
                            class="form-control form-control-lg <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="<?= e($old['name'] ?? '') ?>" 
                            placeholder="Ej: Juan Pérez García"
                            required
                            autofocus
                        >
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                <?= e($errors['name']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="email">
                            <i class="bi bi-envelope text-warning me-1"></i>
                            Correo Electrónico <span class="text-danger">*</span>
                        </label>
                        <input 
                            class="form-control form-control-lg <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="<?= e($old['email'] ?? '') ?>" 
                            placeholder="Ej: estudiante@ejemplo.com"
                            required
                        >
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                <?= e($errors['email']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="phone">
                            <i class="bi bi-telephone text-warning me-1"></i>
                            Número de Teléfono <span class="text-danger">*</span>
                        </label>
                        <input 
                            class="form-control form-control-lg <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                            type="tel" 
                            name="phone" 
                            id="phone" 
                            value="<?= e($old['phone'] ?? '') ?>" 
                            placeholder="Ej: +52 123 456 7890"
                            required
                        >
                        <?php if (isset($errors['phone'])): ?>
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                <?= e($errors['phone']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                        <a class="btn btn-outline-secondary px-4" href="<?= route('students.index') ?>">
                            <i class="bi bi-x-circle me-1"></i>
                            Cancelar
                        </a>
                        <button class="btn btn-warning px-4" type="submit">
                            <i class="bi bi-check-circle me-1"></i>
                            Actualizar Estudiante
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
