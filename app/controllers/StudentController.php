<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Core\CSRF;
use App\Models\Student;
use PDO;
use Exception;

class StudentController extends Controller
{
    private const MSG_CREATED_SUCCESS = 'Estudiante creado exitosamente';
    private const MSG_UPDATED_SUCCESS = 'Estudiante actualizado exitosamente';
    private const MSG_DELETED_SUCCESS = 'Estudiante eliminado exitosamente';
    private const MSG_NOT_FOUND = 'Estudiante no encontrado';
    private const MSG_DELETE_ERROR = 'No se pudo eliminar el estudiante';
    private const MSG_GENERAL_ERROR = 'Error al procesar la solicitud. Intenta nuevamente.';
    private const MSG_INVALID_CSRF = 'Token CSRF inválido o expirado';
    private const MSG_EMAIL_EXISTS = 'Este correo ya está registrado';
    
    private const MIN_NAME_LENGTH = 3;
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_PER_PAGE = 10;
    private const MIN_PER_PAGE = 5;
    private const MAX_PER_PAGE = 50;
    private const DEFAULT_SORT_BY = 'id';
    private const DEFAULT_SORT_DIR = 'DESC';

    private Student $model;

    public function __construct(PDO $db)
    {
        $this->model = new Student($db);
    }

    public function index(): void
    {
        $params = $this->getIndexParams();
        $paginator = $this->model->paginate(
            $params['page'],
            $params['perPage'],
            $params['search'],
            $params['filters']
        );
        $stats = $this->model->getStats();

        $this->render('index', [
            'students' => $paginator->items(),
            'paginator' => $paginator,
            'search' => $params['search'],
            'filters' => $params['filters'],
            'stats' => $stats
        ]);
    }

    public function show(int $id): void
    {
        $student = $this->findStudentOrAbort($id);
        $this->render('show', ['student' => $student]);
    }

    public function create(): void
    {
        $this->renderStudentForm('create');
    }

    public function store(): void
    {
        if (!$this->verifyPostRequest()) {
            return;
        }

        $validator = $this->validateStudentData($_POST);

        if ($validator->fails()) {
            $this->renderCreateWithErrors($validator->errors(), $validator->validated());
            return;
        }

        $data = $validator->validated();
        
        // Verificar si el email ya existe
        if ($this->model->emailExists($data['email'])) {
            $this->renderCreateWithErrors(['email' => self::MSG_EMAIL_EXISTS], $data);
            return;
        }
        
        try {
            $id = $this->model->create($data);
            $this->logSuccess("Estudiante creado exitosamente con ID: {$id}");
            flash('success', self::MSG_CREATED_SUCCESS);
            clear_old();
            $this->redirect(route('students.index'));
        } catch (Exception $e) {
            $this->handleCreateError($e, $data);
        }
    }

    public function edit(int $id): void
    {
        $student = $this->findStudentOrAbort($id);
        $this->renderStudentForm('edit', [], $student);
    }

    public function update(int $id): void
    {
        if (!$this->verifyPostRequest()) {
            return;
        }

        $student = $this->findStudentOrAbort($id);
        $validator = $this->validateStudentData($_POST);

        if ($validator->fails()) {
            $this->renderEditWithErrors($validator->errors(), $student, $validator->validated());
            return;
        }

        $data = $validator->validated();
        
        // Verificar si el email ya existe (excluyendo el estudiante actual)
        if ($this->model->emailExists($data['email'], $id)) {
            $this->renderEditWithErrors(['email' => self::MSG_EMAIL_EXISTS], $student, $data);
            return;
        }
        
        try {
            $this->model->update($id, $data);
            $this->logSuccess("Estudiante actualizado exitosamente - ID: {$id}");
            flash('success', self::MSG_UPDATED_SUCCESS);
            $this->redirect(route('students.index'));
        } catch (Exception $e) {
            $this->handleUpdateError($e, $student, $data);
        }
    }

    public function destroy(int $id): void
    {
        if (!$this->verifyPostRequest()) {
            return;
        }

        try {
            $deleted = $this->model->delete($id);
            
            if ($deleted) {
                $this->logSuccess("Estudiante eliminado exitosamente - ID: {$id}");
                flash('success', self::MSG_DELETED_SUCCESS);
            } else {
                app_log("Intento de eliminar estudiante inexistente - ID: {$id}", 'warning');
                flash('error', self::MSG_DELETE_ERROR);
            }
        } catch (Exception $e) {
            $this->handleDeleteError($e);
        }
        
        $this->redirect(route('students.index'));
    }

    private function getIndexParams(): array
    {
        $page = (int) ($_GET['page'] ?? self::DEFAULT_PAGE);
        $perPage = (int) ($_GET['per_page'] ?? self::DEFAULT_PER_PAGE);
        $search = $_GET['search'] ?? null;

        $filters = [
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null,
            'email_domain' => $_GET['email_domain'] ?? null,
            'sort_by' => $_GET['sort_by'] ?? self::DEFAULT_SORT_BY,
            'sort_dir' => $_GET['sort_dir'] ?? self::DEFAULT_SORT_DIR,
        ];

        return [
            'page' => max(1, $page),
            'perPage' => max(self::MIN_PER_PAGE, min(self::MAX_PER_PAGE, $perPage)),
            'search' => $search,
            'filters' => $filters
        ];
    }

    private function findStudentOrAbort(int $id): array
    {
        $student = $this->model->find($id);
        
        if (!$student) {
            $this->abort(404, self::MSG_NOT_FOUND);
        }

        return $student;
    }

    private function verifyPostRequest(): bool
    {
        if (!$this->isMethod('POST')) {
            $this->redirect('/');
            return false;
        }

        if (!$this->validateCSRF()) {
            $this->abort(419, self::MSG_INVALID_CSRF);
            return false;
        }

        return true;
    }

    private function validateStudentData(array $data): Validator
    {
        $validator = new Validator($data);
        
        $validator
            ->required('name', 'El nombre es obligatorio')
            ->min('name', self::MIN_NAME_LENGTH, "El nombre debe tener al menos " . self::MIN_NAME_LENGTH . " caracteres")
            ->max('name', MAX_NAME_LENGTH, 'El nombre no debe exceder ' . MAX_NAME_LENGTH . ' caracteres')
            ->required('email', 'El correo es obligatorio')
            ->email('email', 'Formato de correo inválido')
            ->max('email', MAX_EMAIL_LENGTH, 'El correo no debe exceder ' . MAX_EMAIL_LENGTH . ' caracteres')
            ->required('phone', 'El teléfono es obligatorio')
            ->max('phone', MAX_PHONE_LENGTH, 'El teléfono no debe exceder ' . MAX_PHONE_LENGTH . ' caracteres');

        return $validator;
    }

    private function renderStudentForm(string $view, array $errors = [], array $oldData = []): void
    {
        $this->render($view, [
            'errors' => $errors,
            'old' => $oldData
        ]);
    }

    private function renderCreateWithErrors(array $errors, array $oldData): void
    {
        set_old($oldData);
        $this->renderStudentForm('create', $errors, $oldData);
    }

    private function renderEditWithErrors(array $errors, array $student, array $validatedData): void
    {
        $this->renderStudentForm('edit', $errors, array_merge($student, $validatedData));
    }

    private function handleCreateError(Exception $e, array $data): void
    {
        app_log("Error al crear estudiante: " . $e->getMessage(), 'error');
        set_old($data);
        $this->renderStudentForm('create', ['general' => self::MSG_GENERAL_ERROR], $data);
    }

    private function handleUpdateError(Exception $e, array $student, array $data): void
    {
        app_log("Error al actualizar estudiante: " . $e->getMessage(), 'error');
        $this->renderStudentForm('edit', ['general' => self::MSG_GENERAL_ERROR], array_merge($student, $data));
    }

    private function handleDeleteError(Exception $e): void
    {
        app_log("Error al eliminar estudiante: " . $e->getMessage(), 'error');
        flash('error', self::MSG_GENERAL_ERROR);
    }

    private function logSuccess(string $message): void
    {
        app_log($message, 'info');
    }
}
