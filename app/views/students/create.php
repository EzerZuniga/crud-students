<div style="max-width: 600px; margin: 0 auto;">
    <div class="page-header">
        <h1 class="page-title">Crear Nuevo Estudiante</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="/?action=store">
                <div class="mb-3">
                    <label class="form-label" for="name">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 0.25rem;">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Nombre Completo
                    </label>
                    <input 
                        class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="<?= htmlspecialchars($old['name'] ?? '') ?>" 
                        placeholder="Ej: Juan Pérez García"
                        required
                    >
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="email">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 0.25rem;">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        Correo Electrónico
                    </label>
                    <input 
                        class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>" 
                        placeholder="Ej: estudiante@ejemplo.com"
                        required
                    >
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="phone">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 0.25rem;">
                            <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"></path>
                        </svg>
                        Número de Teléfono
                    </label>
                    <input 
                        class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                        type="text" 
                        name="phone" 
                        id="phone" 
                        value="<?= htmlspecialchars($old['phone'] ?? '') ?>" 
                        placeholder="Ej: +52 123 456 7890"
                        required
                    >
                    <?php if (isset($errors['phone'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['phone']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="btn-group" style="margin-top: 2rem;">
                    <button class="btn btn-primary" type="submit">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Guardar Estudiante
                    </button>
                    <a class="btn btn-outline-secondary" href="/">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
