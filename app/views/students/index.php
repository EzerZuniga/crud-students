<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 mb-1">
            <i class="bi bi-people-fill text-primary"></i>
            Gestión de Estudiantes
        </h1>
        <p class="text-muted mb-0">Administra y organiza los registros de estudiantes</p>
    </div>
    <?php if (can('students.create')): ?>
        <a class="btn btn-primary" href="<?= route('students.create') ?>">
            <i class="bi bi-person-plus-fill me-1"></i>
            Crear Estudiante
        </a>
    <?php endif; ?>
</div>

<!-- Estadísticas -->
<?php if (($stats['total'] ?? 0) > 0): ?>
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">Total</h6>
                            <h3 class="mb-0"><?= number_format($stats['total']) ?></h3>
                        </div>
                        <i class="bi bi-people fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">Hoy</h6>
                            <h3 class="mb-0"><?= number_format($stats['today']) ?></h3>
                        </div>
                        <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">Esta Semana</h6>
                            <h3 class="mb-0"><?= number_format($stats['week']) ?></h3>
                        </div>
                        <i class="bi bi-calendar-week fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">Este Mes</h6>
                            <h3 class="mb-0"><?= number_format($stats['month']) ?></h3>
                        </div>
                        <i class="bi bi-calendar-month fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Barra de búsqueda y filtros -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="<?= route('students.index') ?>" id="filterForm">
            <div class="row g-3">
                <!-- Búsqueda principal -->
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input 
                            type="text" 
                            class="form-control" 
                            name="search" 
                            placeholder="Buscar por nombre, email o teléfono..." 
                            value="<?= e($search ?? '') ?>"
                        >
                    </div>
                </div>
                
                <!-- Items por página -->
                <div class="col-md-2">
                    <select class="form-select" name="per_page">
                        <option value="10" <?= ($paginator->perPage() === 10) ? 'selected' : '' ?>>10/página</option>
                        <option value="25" <?= ($paginator->perPage() === 25) ? 'selected' : '' ?>>25/página</option>
                        <option value="50" <?= ($paginator->perPage() === 50) ? 'selected' : '' ?>>50/página</option>
                    </select>
                </div>

                <!-- Ordenar por -->
                <div class="col-md-2">
                    <select class="form-select" name="sort_by">
                        <option value="id" <?= ($filters['sort_by'] === 'id') ? 'selected' : '' ?>>Ordenar: ID</option>
                        <option value="name" <?= ($filters['sort_by'] === 'name') ? 'selected' : '' ?>>Ordenar: Nombre</option>
                        <option value="email" <?= ($filters['sort_by'] === 'email') ? 'selected' : '' ?>>Ordenar: Email</option>
                        <option value="date" <?= ($filters['sort_by'] === 'date') ? 'selected' : '' ?>>Ordenar: Fecha</option>
                    </select>
                </div>

                <!-- Dirección ordenamiento -->
                <div class="col-md-2">
                    <select class="form-select" name="sort_dir">
                        <option value="DESC" <?= ($filters['sort_dir'] === 'DESC') ? 'selected' : '' ?>>⬇ Descendente</option>
                        <option value="ASC" <?= ($filters['sort_dir'] === 'ASC') ? 'selected' : '' ?>>⬆ Ascendente</option>
                    </select>
                </div>
            </div>

            <!-- Filtros avanzados (colapsables) -->
            <div class="mt-3">
                <button 
                    class="btn btn-sm btn-outline-secondary" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#advancedFilters"
                >
                    <i class="bi bi-funnel"></i> Filtros Avanzados
                </button>
                <button class="btn btn-sm btn-primary" type="submit">
                    <i class="bi bi-search me-1"></i> Aplicar Filtros
                </button>
                <?php if ($search || array_filter($filters)): ?>
                    <a href="<?= route('students.index') ?>" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-x-circle"></i> Limpiar Todo
                    </a>
                <?php endif; ?>
            </div>

            <div class="collapse mt-3" id="advancedFilters">
                <div class="card card-body bg-light">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Fecha Desde</label>
                            <input 
                                type="date" 
                                class="form-control" 
                                name="date_from" 
                                value="<?= e($filters['date_from'] ?? '') ?>"
                            >
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha Hasta</label>
                            <input 
                                type="date" 
                                class="form-control" 
                                name="date_to" 
                                value="<?= e($filters['date_to'] ?? '') ?>"
                            >
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Dominio de Email</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                name="email_domain" 
                                placeholder="ej: gmail.com"
                                value="<?= e($filters['email_domain'] ?? '') ?>"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (empty($students)): ?>
    <!-- Empty State -->
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox display-1 text-muted mb-3"></i>
            <?php if ($search ?? null): ?>
                <h3 class="h5">No se encontraron resultados</h3>
                <p class="text-muted mb-4">No hay estudiantes que coincidan con "<?= e($search) ?>"</p>
                <a href="<?= route('students.index') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Ver todos los estudiantes
                </a>
            <?php else: ?>
                <h3 class="h5">No hay estudiantes registrados</h3>
                <p class="text-muted mb-4">¡Comienza agregando tu primer estudiante!</p>
                <?php if (can('students.create')): ?>
                    <a href="<?= route('students.create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Crear Primer Estudiante
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <!-- Students Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul me-2"></i>
                    Lista de Estudiantes
                </h5>
                <span class="badge bg-primary rounded-pill"><?= count($students) ?> registros</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 80px;">ID</th>
                            <th>Nombre</th>
                            <th>Correo Electrónico</th>
                            <th>Teléfono</th>
                            <th class="text-center">Fecha de Registro</th>
                            <th class="text-center" style="width: 220px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary">#<?= e($student['id']) ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary-subtle text-primary me-2">
                                            <?= strtoupper(substr($student['name'], 0, 1)) ?>
                                        </div>
                                        <span class="fw-semibold"><?= e($student['name']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <i class="bi bi-envelope me-1 text-muted"></i>
                                    <a href="mailto:<?= e($student['email']) ?>" class="text-decoration-none">
                                        <?= e($student['email']) ?>
                                    </a>
                                </td>
                                <td>
                                    <i class="bi bi-telephone me-1 text-muted"></i>
                                    <?= e($student['phone']) ?>
                                </td>
                                <td class="text-center text-muted">
                                    <small><?= format_date($student['created_at'], 'd/m/Y H:i') ?></small>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="<?= route('students.show', ['id' => $student['id']]) ?>" 
                                           class="btn btn-sm btn-outline-secondary" 
                                           title="Ver detalles"
                                           data-bs-toggle="tooltip">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if (can('students.edit')): ?>
                                            <a href="<?= route('students.edit', ['id' => $student['id']]) ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Editar"
                                               data-bs-toggle="tooltip">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (can('students.delete')): ?>
                                            <form method="POST" 
                                                  action="<?= route('students.delete', ['id' => $student['id']]) ?>" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Estás seguro de eliminar a <?= e($student['name']) ?>?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Eliminar"
                                                        data-bs-toggle="tooltip">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        <?= $paginator->info() ?>
                    </small>
                </div>
                <div class="col-md-6">
                    <?= $paginator->render() ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<style>
.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}
</style>
