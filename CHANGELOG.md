# Registro de Cambios

Todos los cambios notables en este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere a [Versionado Semántico](https://semver.org/lang/es/).

## [2.0.0] - 2026-01-17

### 🎉 Añadido
- **Arquitectura Mejorada**
  - Clase base `Controller` para reutilización de código
  - Sistema de validación centralizado con clase `Validator`
  - Helpers globales en `app/helpers/functions.php`
  - Archivo de configuración centralizado `config/app.php`
  - Bootstrap de aplicación en `config/bootstrap.php`

- **Nuevas Funcionalidades**
  - Sistema de logging mejorado con niveles (info, warning, error)
  - Manejo centralizado de errores y excepciones
  - Validación robusta con mensajes personalizables
  - Método de búsqueda en el modelo Student
  - Verificación de emails duplicados
  - Contador de estudiantes
  - Variables de entorno (.env)

- **UI/UX Mejorada**
  - Diseño completamente renovado y moderno
  - Sistema de colores profesional con CSS Variables
  - Iconos SVG integrados en toda la interfaz
  - Vista de detalles mejorada con avatar generado
  - Tabla responsiva con mejor legibilidad
  - Formularios con placeholders informativos
  - Footer con información de copyright

- **JavaScript Mejorado**
  - Validación de formularios en tiempo real
  - Prevención de doble envío
  - Contador de caracteres en inputs
  - Validación de email en el cliente
  - Confirmaciones mejoradas para eliminación
  - Búsqueda en vivo (preparado)

- **Documentación**
  - README completo y profesional con badges
  - CHANGELOG para seguimiento de versiones
  - Archivo .env.example con todas las variables
  - Comentarios PHPDoc en todo el código
  - Guías de configuración para Apache y Nginx

### 🔄 Cambiado
- **StudentController**
  - Ahora extiende de la clase base `Controller`
  - Usa el nuevo sistema de validación `Validator`
  - Mejorado manejo de errores con try-catch
  - Logging de todas las operaciones importantes

- **Student Model**
  - Añadidos comentarios PHPDoc
  - Métodos adicionales: `count()`, `search()`, `emailExists()`
  - Mejor manejo de errores en consultas
  - Logging de operaciones fallidas

- **Configuración de Base de Datos**
  - Mensaje de error más descriptivo
  - Opciones PDO mejoradas
  - Mejor manejo de entornos (desarrollo/producción)
  - Logging condicional según entorno

- **Front Controller (public/index.php)**
  - Usa el nuevo sistema de bootstrap
  - Mejor estructura del switch de rutas
  - Manejo de excepciones mejorado

### 🐛 Corregido
- Validación de emails más estricta
- Prevención de XSS con función `e()`
- Manejo correcto de errores de base de datos
- Rutas relativas corregidas en el autoloader

### 🔒 Seguridad
- Consultas preparadas en todas las operaciones de BD
- Escapado de HTML en todas las vistas
- Validación de entrada en servidor y cliente
- Configuración de errores según entorno
- Protección contra doble envío de formularios

### 📦 Estructura
- Nueva carpeta `app/core/` para clases núcleo
- Nueva carpeta `app/helpers/` para funciones auxiliares
- Mejor organización de archivos de configuración
- Separación clara de responsabilidades

## [1.0.0] - 2026-01-16

### Añadido
- Versión inicial del CRUD de estudiantes
- Funcionalidad básica: crear, leer, actualizar, eliminar
- Arquitectura MVC simple
- Integración con Bootstrap 5
- Sistema de logs básico
- Configuración de base de datos
- Script de instalación automática

---

**Nota**: Las fechas son referenciales. Este proyecto evoluciona constantemente.
