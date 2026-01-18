# 🎓 CRUD Students - Sistema de Gestión de Estudiantes

Sistema web profesional de gestión de estudiantes desarrollado con PHP vanilla y arquitectura MVC. Diseño moderno, código limpio y siguiendo las mejores prácticas de desarrollo.

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=flat&logo=mysql)
![License](https://img.shields.io/badge/license-MIT-green)

## ✨ Características

- ✅ **CRUD Completo**: Crear, leer, actualizar y eliminar estudiantes
- 🎨 **Diseño Moderno**: UI/UX profesional y responsiva
- 🔒 **Seguridad**: Consultas preparadas (PDO), validación de datos, protección XSS
- 📝 **Validación Robusta**: Sistema de validación centralizado y reutilizable
- 🏗️ **Arquitectura MVC**: Código organizado y mantenible
- 📱 **Responsive**: Funciona perfectamente en móviles, tablets y escritorio
- 🚀 **Alto Rendimiento**: Sin frameworks pesados, PHP vanilla optimizado
- 📊 **Logging**: Sistema de logs para debugging y monitoreo
- 🔧 **Fácil Configuración**: Variables de entorno y configuración flexible

## 📋 Requisitos del Sistema

- PHP 8.1 o superior
- MySQL 8.0+ o MariaDB 10.5+
- Servidor web (Apache/Nginx) o PHP built-in server
- Extensiones PHP: PDO, pdo_mysql

## 🚀 Instalación Rápida

### 1. Clonar el repositorio

```bash
git clone https://github.com/EzerZuniga/crud-students.git
cd crud-students
```

### 2. Configurar la base de datos

Copia el archivo de configuración de ejemplo:

```bash
cp config/database.example.php config/database.php
```

Edita `config/database.php` y ajusta las credenciales:

```php
return [
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'crud_students',
    'username' => 'root',
    'password' => 'tu_contraseña',
    'charset' => 'utf8mb4',
];
```

### 3. Crear la base de datos

**Opción A: Script automatizado (Recomendado)**

```bash
php scripts/install.php
```

**Opción B: Manualmente con MySQL**

```bash
mysql -u root -p < database.sql
```

### 4. Iniciar el servidor

```bash
php -S localhost:8000 -t . public/index.php
```

Abre tu navegador en: **http://localhost:8000**

## 📁 Estructura del Proyecto

```
crud-students/
├── app/                          # Código de la aplicación
│   ├── controllers/              # Controladores
│   │   └── StudentController.php # Lógica de negocio de estudiantes
│   ├── models/                   # Modelos de datos
│   │   └── Student.php           # Modelo de estudiante
│   ├── views/                    # Vistas (presentación)
│   │   ├── layouts/              # Plantillas compartidas
│   │   │   ├── header.php        # Encabezado común
│   │   │   └── footer.php        # Pie de página común
│   │   └── students/             # Vistas de estudiantes
│   │       ├── index.php         # Lista de estudiantes
│   │       ├── create.php        # Formulario de creación
│   │       ├── edit.php          # Formulario de edición
│   │       └── show.php          # Detalles del estudiante
│   ├── core/                     # Clases núcleo
│   │   ├── Controller.php        # Controlador base
│   │   └── Validator.php         # Sistema de validación
│   └── helpers/                  # Funciones auxiliares
│       └── functions.php         # Helpers globales
├── assets/                       # Recursos estáticos
│   ├── css/
│   │   └── style.css            # Estilos personalizados
│   └── js/
│       └── main.js              # JavaScript principal
├── config/                       # Archivos de configuración
│   ├── app.php                  # Configuración de la app
│   ├── bootstrap.php            # Inicialización
│   ├── database.php             # Conexión a BD
│   └── database.example.php     # Ejemplo de configuración BD
├── public/                       # Directorio público
│   ├── index.php                # Front controller (punto de entrada)
│   └── .htaccess                # Configuración Apache
├── scripts/                      # Scripts de utilidad
│   └── install.php              # Instalador automatizado
├── storage/                      # Almacenamiento
│   └── logs/                    # Archivos de log
│       └── app.log              # Log de la aplicación
├── .env.example                  # Ejemplo de variables de entorno
├── .gitignore                    # Archivos ignorados por Git
├── database.sql                  # Script SQL de la BD
├── LICENSE                       # Licencia del proyecto
└── README.md                     # Este archivo
```

## 🔧 Configuración Avanzada

### Variables de Entorno

Crea un archivo `.env` basado en `.env.example`:

```bash
cp .env.example .env
```

Variables disponibles:

```env
# Aplicación
APP_NAME="CRUD Students"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de Datos
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=crud_students
DB_USER=root
DB_PASSWORD=tu_contraseña
DB_CHARSET=utf8mb4
```

### Configuración de Apache

Si usas Apache, el archivo `.htaccess` ya está configurado. Asegúrate de que `mod_rewrite` esté habilitado:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Configuración de Nginx

```nginx
server {
    listen 80;
    server_name localhost;
    root /path/to/crud-students/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location /assets {
        alias /path/to/crud-students/assets;
    }
}
```

## 🎯 Uso de la Aplicación

### Listar Estudiantes
- Navega a la página principal: `http://localhost:8000`
- Verás una tabla con todos los estudiantes registrados

### Crear Estudiante
1. Click en "Crear Estudiante" o "Nuevo Estudiante"
2. Completa el formulario con los datos requeridos
3. Click en "Guardar Estudiante"

### Ver Detalles
- Click en el botón "Ver" de cualquier estudiante
- Se mostrará toda la información detallada

### Editar Estudiante
1. Click en el botón "Editar"
2. Modifica los campos necesarios
3. Click en "Actualizar Estudiante"

### Eliminar Estudiante
- Click en el botón "Eliminar"
- Confirma la acción en el diálogo

## 🛠️ Desarrollo

### Agregar Nuevas Validaciones

Edita `app/core/Validator.php` y agrega nuevos métodos:

```php
public function unique(string $field, string $table, string $column): self
{
    // Tu lógica de validación única
    return $this;
}
```

### Crear Nuevos Controladores

1. Crea la clase extendiendo `Controller`:

```php
class MiControlador extends Controller
{
    public function index(): void
    {
        $this->render('mi-vista', ['datos' => $misDatos]);
    }
}
```

2. Registra las rutas en `public/index.php`

### Usar Helper Functions

```php
// Escapar HTML
echo e($variable);

// Generar URLs
$url = action_url('edit', 5); // /?action=edit&id=5

// Logging
app_log('Mensaje de log', 'info');

// Redireccionar
redirect_to('/otra-pagina');
```

## 📊 Base de Datos

### Esquema

```sql
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Migraciones Futuras

Para agregar campos:

```sql
ALTER TABLE students ADD COLUMN address VARCHAR(255) AFTER phone;
```

## 🔍 Testing

### Pruebas Manuales

1. Crear estudiante con datos válidos
2. Crear estudiante con datos inválidos (verificar validaciones)
3. Editar estudiante existente
4. Eliminar estudiante
5. Ver detalles de estudiante

### Logs

Los logs se guardan en `storage/logs/app.log`:

```bash
tail -f storage/logs/app.log
```

## 🚀 Despliegue en Producción

### Preparación

1. Cambia `APP_ENV` a `production` en `.env`
2. Establece `APP_DEBUG=false`
3. Configura credenciales seguras de BD
4. Asegura permisos correctos:

```bash
chmod -R 755 storage
chmod -R 755 storage/logs
```

### Optimizaciones

- Habilita OPcache en PHP
- Usa un servidor web de producción (Apache/Nginx)
- Configura SSL/HTTPS
- Implementa backups automáticos de BD
- Configura rotación de logs

## 🤝 Contribuir

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto está bajo la licencia MIT. Ver archivo `LICENSE` para más detalles.

## 👨‍💻 Autor

**Ezer Zuñiga**
- GitHub: [@EzerZuniga](https://github.com/EzerZuniga)

## 🙏 Agradecimientos

- Inspirado en las mejores prácticas de desarrollo PHP
- Diseño moderno basado en principios de UI/UX
- Comunidad de desarrolladores PHP

---

⭐ Si te gusta este proyecto, no olvides darle una estrella en GitHub!
