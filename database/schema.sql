
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- =====================================================
-- CREAR BASE DE DATOS
-- =====================================================

CREATE DATABASE IF NOT EXISTS `crud_students`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `crud_students`;

-- =====================================================
-- TABLA: students
-- Descripción: Almacena información de estudiantes
-- =====================================================

CREATE TABLE IF NOT EXISTS `students` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(120) NOT NULL,
    `phone` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_students_email` (`email`),
    INDEX `idx_students_name` (`name`),
    INDEX `idx_students_email` (`email`)
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Estudiantes registrados en el sistema';

-- =====================================================
-- TABLA: users
-- Descripción: Usuarios del sistema con autenticación
-- =====================================================

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `email` VARCHAR(120) NOT NULL,
    `password` VARCHAR(255) NOT NULL COMMENT 'Hash bcrypt',
    `full_name` VARCHAR(100) NOT NULL,
    `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_users_username` (`username`),
    UNIQUE KEY `uk_users_email` (`email`),
    INDEX `idx_users_is_active` (`is_active`)
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Usuarios del sistema';

-- =====================================================
-- TABLA: roles
-- Descripción: Roles disponibles en el sistema
-- =====================================================

CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL COMMENT 'Identificador interno',
    `display_name` VARCHAR(100) NOT NULL COMMENT 'Nombre visible',
    `description` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_roles_name` (`name`)
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Roles del sistema';

-- =====================================================
-- TABLA: permissions
-- Descripción: Permisos granulares del sistema
-- =====================================================

CREATE TABLE IF NOT EXISTS `permissions` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL COMMENT 'Ej: students.create',
    `display_name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_permissions_name` (`name`)
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Permisos del sistema';

-- =====================================================
-- TABLA: role_user (pivote)
-- Descripción: Relación usuarios-roles (N:M)
-- =====================================================

CREATE TABLE IF NOT EXISTS `role_user` (
    `user_id` INT UNSIGNED NOT NULL,
    `role_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`user_id`, `role_id`),
    
    CONSTRAINT `fk_role_user_user` 
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) 
        ON DELETE CASCADE,
    CONSTRAINT `fk_role_user_role` 
        FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) 
        ON DELETE CASCADE
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Asignación de roles a usuarios';

-- =====================================================
-- TABLA: role_permission (pivote)
-- Descripción: Relación roles-permisos (N:M)
-- =====================================================

CREATE TABLE IF NOT EXISTS `role_permission` (
    `role_id` INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`role_id`, `permission_id`),
    
    CONSTRAINT `fk_role_permission_role` 
        FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) 
        ON DELETE CASCADE,
    CONSTRAINT `fk_role_permission_permission` 
        FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) 
        ON DELETE CASCADE
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Asignación de permisos a roles';

-- =====================================================
-- DATOS INICIALES: Roles
-- =====================================================

INSERT INTO `roles` (`name`, `display_name`, `description`) VALUES
    ('admin', 'Administrador', 'Acceso completo al sistema'),
    ('user', 'Usuario', 'Acceso básico con permisos limitados')
ON DUPLICATE KEY UPDATE `display_name` = VALUES(`display_name`);

-- =====================================================
-- DATOS INICIALES: Permisos
-- =====================================================

INSERT INTO `permissions` (`name`, `display_name`, `description`) VALUES
    -- Estudiantes
    ('students.view', 'Ver Estudiantes', 'Ver lista y detalles de estudiantes'),
    ('students.create', 'Crear Estudiantes', 'Crear nuevos estudiantes'),
    ('students.edit', 'Editar Estudiantes', 'Modificar estudiantes'),
    ('students.delete', 'Eliminar Estudiantes', 'Eliminar estudiantes'),
    -- Usuarios
    ('users.view', 'Ver Usuarios', 'Ver lista de usuarios'),
    ('users.create', 'Crear Usuarios', 'Crear nuevos usuarios'),
    ('users.edit', 'Editar Usuarios', 'Modificar usuarios'),
    ('users.delete', 'Eliminar Usuarios', 'Eliminar usuarios'),
    -- Administración
    ('admin.access', 'Acceso Admin', 'Acceso al panel de administración')
ON DUPLICATE KEY UPDATE `display_name` = VALUES(`display_name`);

-- =====================================================
-- DATOS INICIALES: Permisos por rol
-- =====================================================

-- Admin: todos los permisos
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`)
SELECT r.`id`, p.`id`
FROM `roles` r, `permissions` p
WHERE r.`name` = 'admin';

-- User: permisos básicos de estudiantes
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`)
SELECT r.`id`, p.`id`
FROM `roles` r, `permissions` p
WHERE r.`name` = 'user' 
  AND p.`name` IN ('students.view', 'students.create', 'students.edit');

-- =====================================================
-- DATOS INICIALES: Usuario administrador
-- Credenciales: admin / admin123
-- ¡CAMBIAR EN PRODUCCIÓN!
-- =====================================================

INSERT INTO `users` (`username`, `email`, `password`, `full_name`) VALUES
    ('admin', 'admin@crudstudents.com', 
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Administrador')
ON DUPLICATE KEY UPDATE `full_name` = VALUES(`full_name`);

-- Asignar rol admin
INSERT IGNORE INTO `role_user` (`user_id`, `role_id`)
SELECT u.`id`, r.`id`
FROM `users` u, `roles` r
WHERE u.`username` = 'admin' AND r.`name` = 'admin';

-- =====================================================
-- DATOS DE EJEMPLO: Estudiantes
-- =====================================================

INSERT INTO `students` (`name`, `email`, `phone`) VALUES
    ('Ada Lovelace', 'ada@example.com', '+44 1234 567890'),
    ('Alan Turing', 'alan@example.com', '+44 9876 543210')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- =====================================================
-- RESTAURAR CONFIGURACIÓN
-- =====================================================

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- =====================================================
-- VERIFICACIÓN
-- =====================================================

SELECT 'Base de datos creada exitosamente' AS status;
