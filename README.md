# CRUD Students

Sistema CRUD de estudiantes construido con PHP 8 y MySQL, siguiendo una arquitectura MVC ligera y orientada a entornos reales.

![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat&logo=mysql)
![PHPUnit](https://img.shields.io/badge/PHPUnit-10-8942A6?style=flat&logo=php)
![License](https://img.shields.io/badge/license-MIT-green)

## Tabla de contenido
- [Características](#características)
- [Stack tecnológico](#stack-tecnológico)
- [Arquitectura](#arquitectura)
- [Requisitos](#requisitos)
- [Configuración local](#configuración-local)
- [Gestión de la base de datos](#gestión-de-la-base-de-datos)
- [Variables de entorno](#variables-de-entorno)
- [Ejecución de pruebas](#ejecución-de-pruebas)
- [Estructura de directorios](#estructura-de-directorios)
- [Próximos pasos sugeridos](#próximos-pasos-sugeridos)
- [Licencia](#licencia)

## Características
- Control de acceso basado en roles, autenticación y protección CSRF para cada formulario.
- Validación centralizada con mensajes reutilizables y sanitización por capa de modelo.
- Paginación, filtros simples y flash messages listos para experiencias CRUD cuidada.
- Registro de auditoría en storage/logs/app.log y helpers para trazabilidad rápida.
- Plantillas responsivas sin dependencias externas pesadas, pensadas para personalización.

## Stack tecnológico
- PHP 8.1+ con PDO para acceso a datos y sesiones nativas para autenticación.
- MySQL 8.0+ o MariaDB 10.5+ como motor relacional principal.
- Composer para autoloading y gestión de dependencias.
- PHPUnit 10 para cobertura de pruebas unitarias y de integración ligera.

## Arquitectura
- **Controladores** en app/controllers orquestan solicitudes HTTP, aplican middlewares y delegan en modelos.
- **Modelos** en app/models abstraen el acceso a la base con PDO, incluyen reglas de validación y sanitización.
- **Core** en app/core encapsula infraestructura: router, validaciones, middlewares, paginación y contenedor DI minimalista.
- **Vistas** en app/views adoptan layouts reutilizables y componentes parciales, con escapes sistemáticos.
- **Assets** en assets/ concentran recursos estáticos listos para pipelines de frontend.

## Requisitos
- PHP 8.1 o superior con extensiones pdo y pdo_mysql habilitadas.
- Servidor de base de datos MySQL 8.0+ o MariaDB 10.5+.
- Composer 2.5+ instalado globalmente.
- Opcional: Make o Taskfile si se desea automatizar comandos (no incluido por defecto).

## Configuración local
```bash
git clone https://github.com/EzerZuniga/crud-students.git
cd crud-students
composer install
cp config/database.example.php config/database.php
php scripts/install.php
php -S localhost:8000 -t public public/index.php
```

- El script scripts/install.php crea las tablas y usuarios de ejemplo.
- El servidor embebido expone la aplicación en http://localhost:8000.
- Para entornos productivos se recomienda servir public/index.php desde Nginx o Apache.

## Gestión de la base de datos
- Migra manualmente ejecutando los archivos en database/ si necesitas un control más fino.
- Ajusta los seeds en scripts/install.php para poblar datos iniciales personalizados.
- El esquema incluye tablas para estudiantes, usuarios, roles y permisos.

## Variables de entorno
- config/database.php define host, puerto, credenciales y nombre de la base.
- Ajusta config/app.php para modificar clave de sesión, zona horaria y entorno (dev, prod, test).
- Para despliegues Docker, exporta las mismas variables y monta config/database.php como secreto.

## Ejecución de pruebas
```bash
vendor/bin/phpunit
```

- TestCase.php inicializa la base de datos de prueba definida en config/database.php.
- Aísla las pruebas ejecutando php scripts/install.php --test antes de la suite si requieres datos limpios.

## Estructura de directorios
```
crud-students/
|-- app/
|   |-- controllers/
|   |-- core/
|   |-- models/
|   |-- views/
|-- assets/
|-- config/
|-- public/
|-- scripts/
|-- tests/
|-- storage/
```

## Próximos pasos sugeridos
- Contenedizar el proyecto con Docker y docker-compose para paridad entre entornos.
- Integrar verificación continua (GitHub Actions) ejecutando composer validate y vendor/bin/phpunit.
- Agregar pruebas de integración sobre StudentController para cubrir flujos completos.
- Incorporar un sistema de migraciones (Phinx o Doctrine Migrations) para versionar cambios de esquema.

## Licencia

Distribuido bajo licencia MIT. Consulta LICENSE para más información.
