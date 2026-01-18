<div class="page-header">
    <h1 class="page-title">Gestión de Estudiantes</h1>
    <a class="btn btn-primary" href="/?action=create">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="16"></line>
            <line x1="8" y1="12" x2="16" y2="12"></line>
        </svg>
        Crear Estudiante
    </a>
</div>

<?php if (empty($students)): ?>
    <div class="alert alert-info">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>
        Aún no hay estudiantes registrados. ¡Crea el primero!
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th>Nombre</th>
                    <th>Correo Electrónico</th>
                    <th>Teléfono</th>
                    <th>Fecha de Registro</th>
                    <th style="width: 200px;" class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><span class="badge badge-primary">#<?= htmlspecialchars($student['id']) ?></span></td>
                        <td style="font-weight: 500;"><?= htmlspecialchars($student['name']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                        <td><?= htmlspecialchars($student['phone']) ?></td>
                        <td style="color: var(--text-secondary);"><?= date('d/m/Y H:i', strtotime($student['created_at'])) ?></td>
                        <td class="text-end">
                            <div class="btn-group" style="justify-content: flex-end;">
                                <a class="btn btn-sm btn-outline-secondary" href="/?action=show&id=<?= $student['id'] ?>" title="Ver detalles">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    Ver
                                </a>
                                <a class="btn btn-sm btn-outline-primary" href="/?action=edit&id=<?= $student['id'] ?>" title="Editar estudiante">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Editar
                                </a>
                                <form method="POST" action="/?action=destroy&id=<?= $student['id'] ?>" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar a este estudiante?');">
                                    <button class="btn btn-sm btn-outline-danger" type="submit" title="Eliminar estudiante">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path>
                                        </svg>
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div style="margin-top: 1.5rem; color: var(--text-secondary); font-size: 0.875rem;">
        <strong>Total de estudiantes:</strong> <?= count($students) ?>
    </div>
<?php endif; ?>
