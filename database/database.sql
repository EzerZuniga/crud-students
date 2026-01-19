-- Script de base de datos para crud-students
-- Ejecuta este archivo en MySQL Workbench o con `mysql -u <user> -p < database.sql`

CREATE DATABASE IF NOT EXISTS crud_students
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE crud_students;

CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos de ejemplo opcionales
INSERT INTO students (name, email, phone) VALUES
('Ada Lovelace', 'ada@example.com', '+44 1234 567890'),
('Alan Turing', 'alan@example.com', '+44 9876 543210');
