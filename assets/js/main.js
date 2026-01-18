/**
 * JavaScript Principal de la Aplicación
 * Maneja la interactividad y validación de formularios
 */

// Configuración
const CONFIG = {
    confirmDeleteMessage: '¿Estás seguro de eliminar este estudiante? Esta acción no se puede deshacer.',
    successTimeout: 3000,
};

/**
 * Inicializa la validación de formularios
 */
const initFormValidation = () => {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', event => {
            // Validación HTML5
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        });
        
        // Eliminar clase de validación al modificar campos
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                if (input.classList.contains('is-invalid')) {
                    input.classList.remove('is-invalid');
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
 * Añade animación suave a los enlaces internos
 */
const smoothScrollToTop = () => {
    const links = document.querySelectorAll('a[href="#top"]');
    
    links.forEach(link => {
        link.addEventListener('click', event => {
            event.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
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
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, CONFIG.successTimeout);
    });
};

/**
 * Añade contador de caracteres a inputs con maxlength
 */
const addCharacterCounters = () => {
    const inputs = document.querySelectorAll('input[maxlength], textarea[maxlength]');
    
    inputs.forEach(input => {
        const maxLength = input.getAttribute('maxlength');
        
        if (maxLength) {
            const counter = document.createElement('small');
            counter.className = 'text-muted';
            counter.style.display = 'block';
            counter.style.marginTop = '0.25rem';
            
            const updateCounter = () => {
                const remaining = maxLength - input.value.length;
                counter.textContent = `${input.value.length}/${maxLength} caracteres`;
                counter.style.color = remaining < 20 ? '#ef4444' : '#64748b';
            };
            
            input.addEventListener('input', updateCounter);
            input.parentNode.appendChild(counter);
            updateCounter();
        }
    });
};

/**
 * Mejora la accesibilidad del teclado
 */
const enhanceKeyboardNavigation = () => {
    // Permitir cerrar modales/alertas con Escape
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            const closeButtons = document.querySelectorAll('[data-dismiss], .btn-close');
            closeButtons.forEach(btn => btn.click());
        }
    });
};

/**
 * Añade funcionalidad de búsqueda en tiempo real (si existe un campo de búsqueda)
 */
const initLiveSearch = () => {
    const searchInput = document.querySelector('#search-students');
    
    if (searchInput) {
        const table = document.querySelector('table tbody');
        const rows = Array.from(table.querySelectorAll('tr'));
        
        searchInput.addEventListener('input', event => {
            const searchTerm = event.target.value.toLowerCase();
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
};

/**
 * Valida el formato de email en tiempo real
 */
const validateEmailField = () => {
    const emailInputs = document.querySelectorAll('input[type="email"]');
    
    emailInputs.forEach(input => {
        input.addEventListener('blur', () => {
            if (input.value && !isValidEmail(input.value)) {
                input.classList.add('is-invalid');
                
                let feedback = input.nextElementSibling;
                if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    input.parentNode.appendChild(feedback);
                }
                feedback.textContent = 'Formato de correo electrónico inválido';
            } else {
                input.classList.remove('is-invalid');
            }
        });
    });
};

/**
 * Valida si un email tiene formato correcto
 */
const isValidEmail = email => {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
};

/**
 * Previene el doble envío de formularios
 */
const preventDoubleSubmit = () => {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', () => {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Procesando...';
                
                // Reactivar después de 3 segundos por si hay error
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.textContent = submitButton.getAttribute('data-original-text') || 'Guardar';
                }, 3000);
            }
        });
    });
};

/**
 * Función de inicialización principal
 */
const init = () => {
    console.log('🚀 CRUD Students - Aplicación iniciada');
    
    initFormValidation();
    enhanceDeleteConfirmations();
    smoothScrollToTop();
    addCharacterCounters();
    enhanceKeyboardNavigation();
    initLiveSearch();
    validateEmailField();
    preventDoubleSubmit();
    
    console.log('✅ Todos los módulos cargados correctamente');
};

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

// Exportar para uso en otros módulos si es necesario
export { init, isValidEmail };
