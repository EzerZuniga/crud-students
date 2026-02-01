/**
 * JavaScript Principal de la Aplicación
 * Maneja la interactividad y validación de formularios
 */

// Configuración
const CONFIG = {
    confirmDeleteMessage: '¿Estás seguro de eliminar este estudiante?\n\nEsta acción no se puede deshacer.',
    successTimeout: 5000,
    animationDuration: 300,
};

/**
 * Inicializa la aplicación
 */
document.addEventListener('DOMContentLoaded', function() {
    initFormValidation();
    enhanceDeleteConfirmations();
    initPasswordToggle();
    initSearchForm();
    autoHideAlerts();
    initTooltips();
    initAnimations();
    initInputEffects();
    initLoadingButtons();
});

/**
 * Inicializa la validación de formularios
 */
const initFormValidation = () => {
    const forms = document.querySelectorAll('.needs-validation');
    
    forms.forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
        
        // Eliminar clase de validación al modificar campos
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                if (input.classList.contains('is-invalid')) {
                    input.classList.remove('is-invalid');
                    const feedback = input.parentElement.querySelector('.invalid-feedback');
                    if (feedback) feedback.style.display = 'none';
                }
            });
        });
    });
};

/**
 * Mejora los diálogos de confirmación para eliminación
 */
const enhanceDeleteConfirmations = () => {
    const deleteForms = document.querySelectorAll('form[action*="destroy"]');
    
    deleteForms.forEach(form => {
        form.addEventListener('submit', event => {
            if (!confirm(CONFIG.confirmDeleteMessage)) {
                event.preventDefault();
            }
        });
    });
};

/**
 * Toggle password visibility
 */
const initPasswordToggle = () => {
    const toggleButtons = document.querySelectorAll('#togglePassword');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const passwordInput = this.closest('.input-group').querySelector('input');
            const eyeIcon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            }
        });
    });
};

/**
 * Init search form auto-submit on filter change
 */
const initSearchForm = () => {
    const filterForm = document.getElementById('filterForm');
    if (!filterForm) return;
    
    const selects = filterForm.querySelectorAll('select');
    selects.forEach(select => {
        select.addEventListener('change', () => {
            filterForm.submit();
        });
    });
};

/**
 * Auto-oculta alertas después de un tiempo
 */
const autoHideAlerts = () => {
    const alerts = document.querySelectorAll('.alert-dismissible');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, CONFIG.successTimeout);
    });
};

/**
 * Inicializa tooltips de Bootstrap
 */
const initTooltips = () => {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
};

/**
 * Inicializa animaciones de entrada para elementos
 */
const initAnimations = () => {
    // Animar cards al entrar en viewport
    const animatedElements = document.querySelectorAll('.card, .stat-card, .table tbody tr');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 50);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        observer.observe(el);
    });
};

/**
 * Efectos de focus e interacción en inputs
 */
const initInputEffects = () => {
    const inputs = document.querySelectorAll('.form-control, .form-select');
    
    inputs.forEach(input => {
        // Efecto de label flotante
        input.addEventListener('focus', function() {
            this.closest('.input-group, .mb-3, .mb-4')?.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.closest('.input-group, .mb-3, .mb-4')?.classList.remove('focused');
        });
        
        // Validación en tiempo real
        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.classList.add('has-value');
            } else {
                this.classList.remove('has-value');
            }
        });
    });
};

/**
 * Botones con estado de carga
 */
const initLoadingButtons = () => {
    const forms = document.querySelectorAll('form:not([data-no-loading])');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (!submitBtn || !this.checkValidity()) return;
            
            // Guardar texto original
            const originalContent = submitBtn.innerHTML;
            const originalWidth = submitBtn.offsetWidth;
            
            // Establecer ancho fijo y mostrar spinner
            submitBtn.style.width = originalWidth + 'px';
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Procesando...';
            submitBtn.disabled = true;
            
            // Restaurar después de un timeout (por si falla la redirección)
            setTimeout(() => {
                submitBtn.innerHTML = originalContent;
                submitBtn.disabled = false;
                submitBtn.style.width = '';
            }, 10000);
        });
    });
};

/**
 * Efecto ripple para botones
 */
document.addEventListener('click', function(e) {
    const button = e.target.closest('.btn-primary, .btn-success');
    if (!button) return;
    
    const ripple = document.createElement('span');
    const rect = button.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = e.clientX - rect.left - size / 2;
    const y = e.clientY - rect.top - size / 2;
    
    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s ease-out;
        left: ${x}px;
        top: ${y}px;
        pointer-events: none;
    `;
    
    button.style.position = 'relative';
    button.style.overflow = 'hidden';
    button.appendChild(ripple);
    
    setTimeout(() => ripple.remove(), 600);
});

// CSS para la animación ripple (se agrega dinámicamente)
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);