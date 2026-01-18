<?php
/**
 * Controlador de Estudiantes
 * Maneja todas las operaciones CRUD para la entidad Student
 */
class StudentController extends Controller
{
    private Student $model;

    public function __construct(PDO $db)
    {
        $this->model = new Student($db);
    }

    /**
     * Lista todos los estudiantes
     */
    public function index(): void
    {
        $students = $this->model->all();
        $this->render('index', ['students' => $students]);
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

        // Validar datos usando la clase Validator
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
            $this->redirect('/');
        } catch (Exception $e) {
            app_log("Error al crear estudiante: " . $e->getMessage(), 'error');
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
            $this->redirect('/');
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

        try {
            $deleted = $this->model->delete($id);
            
            if ($deleted) {
                app_log("Estudiante eliminado exitosamente - ID: {$id}", 'info');
            } else {
                app_log("Intento de eliminar estudiante inexistente - ID: {$id}", 'warning');
            }
        } catch (Exception $e) {
            app_log("Error al eliminar estudiante: " . $e->getMessage(), 'error');
        }
        
        $this->redirect('/');
    }
}
