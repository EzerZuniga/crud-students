<div style="max-width: 800px; margin: 0 auto;">
    <div class="page-header">
        <h1 class="page-title">Detalles del Estudiante</h1>
        <div class="btn-group">
            <a class="btn btn-outline-secondary" href="/">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Volver
            </a>
            <a class="btn btn-primary" href="/?action=edit&id=<?= $student['id'] ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Editar
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--primary-light)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.25rem;">
                    <?= strtoupper(substr(htmlspecialchars($student['name']), 0, 1)) ?>
                </div>
                <div>
                    <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600;"><?= htmlspecialchars($student['name']) ?></h2>
                    <p style="margin: 0; color: var(--text-secondary); font-size: 0.875rem;">ID: #<?= htmlspecialchars($student['id']) ?></p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="detail-row">
                <div class="detail-label">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 0.5rem;">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Nombre Completo
                </div>
                <div class="detail-value"><?= htmlspecialchars($student['name']) ?></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 0.5rem;">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    Correo Electrónico
                </div>
                <div class="detail-value">
                    <a href="mailto:<?= htmlspecialchars($student['email']) ?>" style="color: var(--primary); text-decoration: none;">
                        <?= htmlspecialchars($student['email']) ?>
                    </a>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 0.5rem;">
                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"></path>
                    </svg>
                    Teléfono
                </div>
                <div class="detail-value">
                    <a href="tel:<?= htmlspecialchars($student['phone']) ?>" style="color: var(--primary); text-decoration: none;">
                        <?= htmlspecialchars($student['phone']) ?>
                    </a>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 0.5rem;">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    Fecha de Registro
                </div>
                <div class="detail-value">
                    <span class="badge badge-success">
                        <?= date('d/m/Y H:i:s', strtotime($student['created_at'])) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top: 1.5rem;" class="btn-group">
        <a class="btn btn-outline-secondary" href="/">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Volver al Listado
        </a>
        <a class="btn btn-primary" href="/?action=edit&id=<?= $student['id'] ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            Editar Estudiante
        </a>
        <form method="POST" action="/?action=destroy&id=<?= $student['id'] ?>" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar a este estudiante? Esta acción no se puede deshacer.');">
            <button class="btn btn-outline-danger" type="submit">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path>
                </svg>
                Eliminar Estudiante
            </button>
        </form>
    </div>
</div>
