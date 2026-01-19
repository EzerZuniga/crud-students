-- ========================================
-- Migration: Permissions System
-- Description: Agrega sistema de permisos granulares
-- ========================================

USE crud_students;

-- Tabla de permisos
CREATE TABLE IF NOT EXISTS permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE COMMENT 'Nombre técnico del permiso (ej: students.create)',
    display_name VARCHAR(255) NOT NULL COMMENT 'Nombre legible del permiso',
    description TEXT NULL COMMENT 'Descripción detallada del permiso',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla pivote: roles - permisos (many-to-many)
CREATE TABLE IF NOT EXISTS role_permission (
    role_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    
    INDEX idx_role (role_id),
    INDEX idx_permission (permission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Permisos predefinidos para el sistema CRUD
-- ========================================

INSERT INTO permissions (name, display_name, description) VALUES
-- Permisos de Estudiantes
('students.view', 'Ver Estudiantes', 'Permite ver la lista y detalles de estudiantes'),
('students.create', 'Crear Estudiantes', 'Permite crear nuevos estudiantes'),
('students.edit', 'Editar Estudiantes', 'Permite modificar información de estudiantes'),
('students.delete', 'Eliminar Estudiantes', 'Permite eliminar estudiantes del sistema'),

-- Permisos de Usuarios (administración)
('users.view', 'Ver Usuarios', 'Permite ver lista de usuarios del sistema'),
('users.create', 'Crear Usuarios', 'Permite crear nuevos usuarios'),
('users.edit', 'Editar Usuarios', 'Permite modificar usuarios existentes'),
('users.delete', 'Eliminar Usuarios', 'Permite eliminar usuarios'),

-- Permisos de Roles (administración avanzada)
('roles.view', 'Ver Roles', 'Permite ver roles del sistema'),
('roles.create', 'Crear Roles', 'Permite crear nuevos roles'),
('roles.edit', 'Editar Roles', 'Permite modificar roles y asignar permisos'),
('roles.delete', 'Eliminar Roles', 'Permite eliminar roles'),

-- Permisos administrativos
('admin.access', 'Acceso Administrativo', 'Acceso completo al panel de administración'),
('system.settings', 'Configuración del Sistema', 'Permite modificar configuraciones globales');

-- ========================================
-- Asignar permisos al rol ADMIN (todos los permisos)
-- ========================================

INSERT INTO role_permission (role_id, permission_id)
SELECT 
    (SELECT id FROM roles WHERE name = 'admin') as role_id,
    id as permission_id
FROM permissions;

-- ========================================
-- Asignar permisos al rol USER (solo lectura/escritura de estudiantes)
-- ========================================

INSERT INTO role_permission (role_id, permission_id)
SELECT 
    (SELECT id FROM roles WHERE name = 'user') as role_id,
    id as permission_id
FROM permissions
WHERE name IN ('students.view', 'students.create', 'students.edit');

-- ========================================
-- Verificación de la migración
-- ========================================

SELECT 
    'Migration completed successfully!' as status,
    (SELECT COUNT(*) FROM permissions) as total_permissions,
    (SELECT COUNT(*) FROM role_permission) as total_role_permissions;

-- Ver permisos por rol
SELECT 
    r.name as role_name,
    COUNT(rp.permission_id) as permissions_count,
    GROUP_CONCAT(p.display_name SEPARATOR ', ') as permissions
FROM roles r
LEFT JOIN role_permission rp ON r.id = rp.role_id
LEFT JOIN permissions p ON rp.permission_id = p.id
GROUP BY r.id, r.name;
