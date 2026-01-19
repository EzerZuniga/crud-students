<?php
/**
 * Controlador de Estudiantes
 * Maneja todas las operaciones CRUD para la entidad Student
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Core\CSRF;
use App\Models\Student;
use PDO;
use Exception;

class StudentController extends Controller
{
    private Student $model;

    public function __construct(PDO $db)
    {
        $this->model = new Student($db);
    }

    /**
     * Lista todos los estudiantes con paginación y búsqueda
     */
    public function index(): void
    {
        // Obtener parámetros de la URL
        $page = (int) ($_GET['page'] ?? 1);
        $perPage = (int) ($_GET['per_page'] ?? 10);
        $search = $_GET['search'] ?? null;

        // Filtros avanzados
        $filters = [
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null,
            'email_domain' => $_GET['email_domain'] ?? null,
            'sort_by' => $_GET['sort_by'] ?? 'id',
            'sort_dir' => $_GET['sort_dir'] ?? 'DESC',
        ];

        // Validar parámetros
        $page = max(1, $page);
        $perPage = max(5, min(50, $perPage)); // Entre 5 y 50 items por página

        // Obtener datos paginados con filtros
        $paginator = $this->model->paginate($page, $perPage, $search, $filters);

        // Obtener estadísticas
        $stats = $this->model->getStats();

        $this->render('index', [
            'students' => $paginator->items(),
            'paginator' => $paginator,
            'search' => $search,
            'filters' => $filters,
            'stats' => $stats
        ]);
    }

    /**
     * Muestra los detalles de un estudiante
     */
    public function show(int $id): void
    {
        $student = $this->model->find($id);
        
        if (!$student) {
            $this->abort(404, 'Estudiante no encontrado');
            return;
        }

        $this->render('show', ['student' => $student]);
    }

    /**
     * Muestra el formulario para crear un estudiante
     */
    public function create(): void
    {
        $this->render('create', [
            'errors' => [],
            'old' => []
        ]);
    }

    /**
     * Almacena un nuevo estudiante
     */
    public function store(): void
    {
        if (!$this->isMethod('POST')) {
            $this->redirect('/');
            return;
        }

        // Validar CSRF
        if (!$this->validateCSRF()) {
            $this->abort(419, 'Token CSRF inválido o expirado');
            return;
        }

        // Validar datos
        $validator = new Validator($_POST);
        
        $validator
            ->required('name', 'El nombre es obligatorio')
            ->min('name', 3, 'El nombre debe tener al menos 3 caracteres')
            ->max('name', MAX_NAME_LENGTH, 'El nombre no debe exceder ' . MAX_NAME_LENGTH . ' caracteres')
            ->required('email', 'El correo es obligatorio')
            ->email('email', 'Formato de correo inválido')
            ->max('email', MAX_EMAIL_LENGTH, 'El correo no debe exceder ' . MAX_EMAIL_LENGTH . ' caracteres')
            ->required('phone', 'El teléfono es obligatorio')
            ->max('phone', MAX_PHONE_LENGTH, 'El teléfono no debe exceder ' . MAX_PHONE_LENGTH . ' caracteres');

        if ($validator->fails()) {
            set_old($validator->validated());
            $this->render('create', [
                'errors' => $validator->errors(),
                'old' => $validator->validated()
            ]);
            return;
        }

        $data = $validator->validated();
        
        try {
            $id = $this->model->create($data);
            app_log("Estudiante creado exitosamente con ID: {$id}", 'info');
            flash('success', 'Estudiante creado exitosamente');
            clear_old();
            $this->redirect(route('students.index'));
        } catch (Exception $e) {
            app_log("Error al crear estudiante: " . $e->getMessage(), 'error');
            set_old($data);
            $this->render('create', [
                'errors' => ['general' => 'Error al crear el estudiante. Intenta nuevamente.'],
                'old' => $data
            ]);
        }
    }

    /**
     * Muestra el formulario para editar un estudiante
     */
    public function edit(int $id): void
    {
        $student = $this->model->find($id);
        
        if (!$student) {
            $this->abort(404, 'Estudiante no encontrado');
            return;
        }

        $this->render('edit', [
            'errors' => [],
            'old' => $student
        ]);
    }

    /**
     * Actualiza un estudiante existente
     */
    public function update(int $id): void
    {
        if (!$this->isMethod('POST')) {
            $this->redirect('/');
            return;
        }

        // Validar CSRF
        if (!$this->validateCSRF()) {
            $this->abort(419, 'Token CSRF inválido o expirado');
            return;
        }

        $student = $this->model->find($id);
        
        if (!$student) {
            $this->abort(404, 'Estudiante no encontrado');
            return;
        }

        // Validar datos
        $validator = new Validator($_POST);
        
        $validator
            ->required('name', 'El nombre es obligatorio')
            ->min('name', 3, 'El nombre debe tener al menos 3 caracteres')
            ->max('name', MAX_NAME_LENGTH, 'El nombre no debe exceder ' . MAX_NAME_LENGTH . ' caracteres')
            ->required('email', 'El correo es obligatorio')
            ->email('email', 'Formato de correo inválido')
            ->max('email', MAX_EMAIL_LENGTH, 'El correo no debe exceder ' . MAX_EMAIL_LENGTH . ' caracteres')
            ->required('phone', 'El teléfono es obligatorio')
            ->max('phone', MAX_PHONE_LENGTH, 'El teléfono no debe exceder ' . MAX_PHONE_LENGTH . ' caracteres');

        if ($validator->fails()) {
            $this->render('edit', [
                'errors' => $validator->errors(),
                'old' => array_merge($student, $validator->validated())
            ]);
            return;
        }

        $data = $validator->validated();
        
        try {
            $this->model->update($id, $data);
            app_log("Estudiante actualizado exitosamente - ID: {$id}", 'info');
            flash('success', 'Estudiante actualizado exitosamente');
            $this->redirect(route('students.index'));
        } catch (Exception $e) {
            app_log("Error al actualizar estudiante: " . $e->getMessage(), 'error');
            $this->render('edit', [
                'errors' => ['general' => 'Error al actualizar el estudiante. Intenta nuevamente.'],
                'old' => array_merge($student, $data)
            ]);
        }
    }

    /**
     * Elimina un estudiante
     */
    public function destroy(int $id): void
    {
        if (!$this->isMethod('POST')) {
            $this->redirect('/');
            return;
        }

        // Validar CSRF
        if (!$this->validateCSRF()) {
            $this->abort(419, 'Token CSRF inválido o expirado');
            return;
        }

        try {
            $deleted = $this->model->delete($id);
            
            if ($deleted) {
                app_log("Estudiante eliminado exitosamente - ID: {$id}", 'info');
                flash('success', 'Estudiante eliminado exitosamente');
            } else {
                app_log("Intento de eliminar estudiante inexistente - ID: {$id}", 'warning');
                flash('error', 'No se pudo eliminar el estudiante');
            }
        } catch (Exception $e) {
            app_log("Error al eliminar estudiante: " . $e->getMessage(), 'error');
            flash('error', 'Error al eliminar el estudiante');
        }
        
        $this->redirect(route('students.index'));
    }
}
