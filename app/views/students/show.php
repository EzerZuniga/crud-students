<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <a href="<?= route('students.index') ?>" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bi bi-person-circle text-info"></i>
                        Detalles del Estudiante
                    </h1>
                    <p class="text-muted mb-0">Información completa del registro</p>
                </div>
            </div>
            <div class="btn-group">
                <a href="<?= route('students.edit', ['id' => $student['id']]) ?>" class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i>
                    Editar
                </a>
                <form method="POST" 
                      action="<?= route('students.delete', ['id' => $student['id']]) ?>" 
                      class="d-inline"
                      onsubmit="return confirm('¿Estás seguro de eliminar a <?= e($student['name']) ?>?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>
                        Eliminar
                    </button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white py-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-lg bg-white text-info me-3">
                        <?= strtoupper(substr($student['name'], 0, 1)) ?>
                    </div>
                    <div>
                        <h3 class="mb-1"><?= e($student['name']) ?></h3>
                        <p class="mb-0 opacity-75">
                            <i class="bi bi-hash"></i> ID: <?= e($student['id']) ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h5 class="text-muted mb-3">
                            <i class="bi bi-telephone-fill me-2"></i>
                            Información de Contacto
                        </h5>
                        <div class="mb-3">
                            <label class="small text-muted d-block mb-1">Correo Electrónico</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-envelope-fill text-primary me-2"></i>
                                <a href="mailto:<?= e($student['email']) ?>" class="text-decoration-none">
                                    <?= e($student['email']) ?>
                                </a>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted d-block mb-1">Teléfono</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-telephone-fill text-success me-2"></i>
                                <a href="tel:<?= e($student['phone']) ?>" class="text-decoration-none">
                                    <?= e($student['phone']) ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="text-muted mb-3">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Información del Sistema
                        </h5>
                        <div class="mb-3">
                            <label class="small text-muted d-block mb-1">Fecha de Registro</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-check text-info me-2"></i>
                                <span><?= format_date($student['created_at'], 'd/m/Y H:i:s') ?></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted d-block mb-1">ID del Estudiante</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-hash text-secondary me-2"></i>
                                <span class="badge bg-secondary"><?= e($student['id']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-center mb-4">
            <a href="mailto:<?= e($student['email']) ?>" class="btn btn-outline-primary">
                <i class="bi bi-envelope me-1"></i>
                Enviar Correo
            </a>
            <a href="tel:<?= e($student['phone']) ?>" class="btn btn-outline-success">
                <i class="bi bi-telephone me-1"></i>
                Llamar
            </a>
            <a href="<?= route('students.edit', ['id' => $student['id']]) ?>" class="btn btn-outline-warning">
                <i class="bi bi-pencil me-1"></i>
                Editar Información
            </a>
        </div>

        <div class="text-center">
            <a href="<?= route('students.index') ?>" class="btn btn-link">
                <i class="bi bi-arrow-left me-1"></i>
                Volver a la lista
            </a>
        </div>
    </div>
</div>

<style>
.avatar-lg {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.75rem;
}
</style>
