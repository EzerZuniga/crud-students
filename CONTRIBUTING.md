# Guía de Contribución

¡Gracias por tu interés en contribuir a CRUD Students! Esta guía te ayudará a empezar.

## 🤝 Cómo Contribuir

### Reportar Bugs

Si encuentras un bug, por favor crea un issue con:

- Título descriptivo
- Pasos para reproducir el bug
- Comportamiento esperado vs actual
- Capturas de pantalla (si aplica)
- Versión de PHP y MySQL
- Sistema operativo

### Sugerir Mejoras

Para sugerir nuevas características:

- Usa un título claro y descriptivo
- Explica detalladamente la funcionalidad propuesta
- Proporciona ejemplos de uso
- Explica por qué sería útil

### Pull Requests

1. **Fork el proyecto**
   ```bash
   git clone https://github.com/tu-usuario/crud-students.git
   ```

2. **Crea una rama para tu feature**
   ```bash
   git checkout -b feature/mi-nueva-funcionalidad
   ```

3. **Haz tus cambios**
   - Sigue los estándares de código (ver abajo)
   - Añade comentarios PHPDoc
   - Actualiza la documentación si es necesario

4. **Commit tus cambios**
   ```bash
   git commit -m "feat: añadir nueva funcionalidad X"
   ```

5. **Push a tu fork**
   ```bash
   git push origin feature/mi-nueva-funcionalidad
   ```

6. **Abre un Pull Request**
   - Describe claramente los cambios
   - Referencia issues relacionados
   - Incluye capturas si hay cambios visuales

## 📝 Estándares de Código

### PHP

- Usar PHP 8.1+ features
- Seguir PSR-12 para estilo de código
- Type hints estrictos en todas las funciones
- Comentarios PHPDoc en todas las clases y métodos públicos

```php
/**
 * Descripción breve de la función
 *
 * @param string $param1 Descripción del parámetro
 * @param int $param2 Descripción del parámetro
 * @return bool Descripción del retorno
 */
public function miMetodo(string $param1, int $param2): bool
{
    // Implementación
}
```

### JavaScript

- Usar ES6+ features
- Funciones flecha cuando sea apropiado
- Comentarios JSDoc para funciones exportadas
- Código modular y reutilizable

### CSS

- Usar variables CSS para colores y valores repetidos
- Nombres de clases descriptivos
- Mobile-first approach
- Evitar !important

### SQL

- Nombres de tablas en minúsculas
- Nombres de campos descriptivos
- Siempre usar consultas preparadas
- Comentar consultas complejas

## 🧪 Testing

Antes de enviar un PR, asegúrate de:

- [ ] El código funciona correctamente
- [ ] No hay errores de PHP
- [ ] Las validaciones funcionan
- [ ] La UI es responsiva
- [ ] No hay warnings en el log

## 📋 Checklist para Pull Requests

- [ ] El código sigue los estándares establecidos
- [ ] He comentado mi código, especialmente en partes complejas
- [ ] He actualizado la documentación correspondiente
- [ ] Mis cambios no generan nuevos warnings
- [ ] He probado que todo funciona correctamente
- [ ] He actualizado el CHANGELOG.md si es necesario

## 🎨 Commits Semánticos

Usa prefijos en tus commits:

- `feat:` Nueva funcionalidad
- `fix:` Corrección de bug
- `docs:` Cambios en documentación
- `style:` Formateo, punto y coma faltantes, etc.
- `refactor:` Refactorización de código
- `test:` Añadir tests
- `chore:` Tareas de mantenimiento

Ejemplos:
```
feat: añadir búsqueda de estudiantes
fix: corregir validación de email
docs: actualizar README con nuevas instrucciones
```

## 🚀 Prioridades Actuales

### Alto
- [ ] Implementar paginación en listado
- [ ] Añadir búsqueda avanzada
- [ ] Exportar datos a CSV/PDF

### Medio
- [ ] Sistema de autenticación
- [ ] Roles y permisos
- [ ] Dashboard con estadísticas

### Bajo
- [ ] Modo oscuro
- [ ] Internacionalización (i18n)
- [ ] API REST

## ❓ Preguntas

Si tienes preguntas, puedes:

- Abrir un issue con la etiqueta "question"
- Contactar al mantenedor del proyecto

## 📜 Código de Conducta

Este proyecto adhiere a un código de conducta. Al participar, se espera que mantengas este código:

- Sé respetuoso con otros contribuidores
- Acepta críticas constructivas
- Enfócate en lo que es mejor para la comunidad
- Muestra empatía hacia otros miembros

---

¡Gracias por contribuir! 🎉
