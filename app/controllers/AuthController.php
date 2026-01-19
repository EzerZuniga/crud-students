<?php
/**
 * Auth Controller
 * Maneja la autenticación de usuarios
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Core\CSRF;
use App\Models\User;
use App\Models\Role;
use PDO;
use Exception;

class AuthController extends Controller
{
    private User $userModel;
    private Role $roleModel;

    public function __construct(PDO $db)
    {
        $this->userModel = new User($db);
        $this->roleModel = new Role($db);
    }

    /**
     * Muestra el formulario de login
     */
    public function showLogin(): void
    {
        // Si ya está autenticado, redirigir al inicio
        if (auth_check()) {
            $this->redirect(route('students.index'));
            return;
        }

        $this->render('login', [
            'errors' => [],
            'old' => []
        ], 'auth');
    }

    /**
     * Procesa el login
     */
    public function login(): void
    {
        if (!$this->isMethod('POST')) {
            $this->redirect(route('auth.login'));
            return;
        }

        // Validar CSRF
        if (!$this->validateCSRF()) {
            $this->abort(419, 'Token CSRF inválido');
            return;
        }

        // Validar datos
        $validator = new Validator($_POST);
        
        $validator
            ->required('username', 'El usuario es obligatorio')
            ->required('password', 'La contraseña es obligatoria');

        if ($validator->fails()) {
            $this->render('login', [
                'errors' => $validator->errors(),
                'old' => $validator->validated()
            ], 'auth');
            return;
        }

        $data = $validator->validated();
        
        // Intentar autenticar
        $user = $this->userModel->authenticate($data['username'], $data['password']);
        
        if (!$user) {
            $this->render('login', [
                'errors' => ['general' => 'Credenciales incorrectas'],
                'old' => ['username' => $data['username']]
            ], 'auth');
            return;
        }

        // Obtener roles del usuario
        $roles = $this->userModel->getRoles($user['id']);
        $user['roles'] = $roles;

        // Obtener permisos del usuario
        $permissions = $this->userModel->getPermissions($user['id']);
        $user['permissions'] = $permissions;

        // Guardar en sesión
        auth_login($user);
        
        app_log("Usuario {$user['username']} inició sesión", 'info');
        flash('success', "¡Bienvenido, {$user['full_name']}!");
        
        $this->redirect(route('students.index'));
    }

    /**
     * Procesa el logout
     */
    public function logout(): void
    {
        $username = auth_user()['username'] ?? 'unknown';
        auth_logout();
        
        app_log("Usuario {$username} cerró sesión", 'info');
        flash('success', 'Sesión cerrada correctamente');
        
        $this->redirect(route('auth.login'));
    }

    /**
     * Muestra el formulario de registro
     */
    public function showRegister(): void
    {
        // Si ya está autenticado, redirigir
        if (auth_check()) {
            $this->redirect(route('students.index'));
            return;
        }

        $this->render('register', [
            'errors' => [],
            'old' => []
        ], 'auth');
    }

    /**
     * Procesa el registro
     */
    public function register(): void
    {
        if (!$this->isMethod('POST')) {
            $this->redirect(route('auth.register'));
            return;
        }

        // Validar CSRF
        if (!$this->validateCSRF()) {
            $this->abort(419, 'Token CSRF inválido');
            return;
        }

        // Validar datos
        $validator = new Validator($_POST);
        
        $validator
            ->required('username', 'El nombre de usuario es obligatorio')
            ->min('username', 3, 'El usuario debe tener al menos 3 caracteres')
            ->max('username', 50, 'El usuario no debe exceder 50 caracteres')
            ->required('email', 'El correo es obligatorio')
            ->email('email', 'Formato de correo inválido')
            ->required('full_name', 'El nombre completo es obligatorio')
            ->required('password', 'La contraseña es obligatoria')
            ->min('password', 6, 'La contraseña debe tener al menos 6 caracteres')
            ->required('password_confirmation', 'Debes confirmar la contraseña');

        if ($validator->fails()) {
            $this->render('register', [
                'errors' => $validator->errors(),
                'old' => $validator->validated()
            ], 'auth');
            return;
        }

        $data = $validator->validated();
        
        // Verificar que las contraseñas coincidan
        if ($data['password'] !== $data['password_confirmation']) {
            $this->render('register', [
                'errors' => ['password_confirmation' => 'Las contraseñas no coinciden'],
                'old' => $data
            ], 'auth');
            return;
        }

        // Verificar que el username no exista
        if ($this->userModel->findByUsername($data['username'])) {
            $this->render('register', [
                'errors' => ['username' => 'El nombre de usuario ya está en uso'],
                'old' => $data
            ], 'auth');
            return;
        }

        // Verificar que el email no exista
        if ($this->userModel->findByEmail($data['email'])) {
            $this->render('register', [
                'errors' => ['email' => 'El correo electrónico ya está registrado'],
                'old' => $data
            ], 'auth');
            return;
        }

        try {
            // Crear usuario
            $userId = $this->userModel->create($data);
            
            // Asignar rol de usuario por defecto
            $userRole = $this->roleModel->findByName('user');
            if ($userRole) {
                $this->userModel->assignRole($userId, $userRole['id']);
            }
            
            app_log("Nuevo usuario registrado: {$data['username']}", 'info');
            flash('success', '¡Cuenta creada exitosamente! Ahora puedes iniciar sesión.');
            
            $this->redirect(route('auth.login'));
        } catch (Exception $e) {
            app_log("Error al registrar usuario: " . $e->getMessage(), 'error');
            $this->render('register', [
                'errors' => ['general' => 'Error al crear la cuenta. Intenta nuevamente.'],
                'old' => $data
            ], 'auth');
        }
    }
}
