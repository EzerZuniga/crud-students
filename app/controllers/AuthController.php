<?php

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
    private const MSG_INVALID_CREDENTIALS = 'Credenciales incorrectas';
    private const MSG_INVALID_CSRF = 'Token CSRF inválido';
    private const MSG_PASSWORDS_MISMATCH = 'Las contraseñas no coinciden';
    private const MSG_USERNAME_EXISTS = 'El nombre de usuario ya está en uso';
    private const MSG_EMAIL_EXISTS = 'El correo electrónico ya está registrado';
    private const MSG_REGISTRATION_ERROR = 'Error al crear la cuenta. Intenta nuevamente.';
    private const MSG_NO_PERMISSION = 'No tienes permisos para realizar esta acción.';
    private const MSG_LOGIN_REQUIRED = 'Debes iniciar sesión para acceder a esta página.';
    private const MSG_ADMIN_REQUIRED = 'No tienes permisos para registrar nuevos usuarios.';
    
    private const MIN_USERNAME_LENGTH = 3;
    private const MAX_USERNAME_LENGTH = 50;
    private const MIN_PASSWORD_LENGTH = 6;
    private const DEFAULT_ROLE = 'user';

    private User $userModel;
    private Role $roleModel;

    public function __construct(PDO $db)
    {
        $this->userModel = new User($db);
        $this->roleModel = new Role($db);
    }

    public function showLogin(): void
    {
        if (auth_check()) {
            $this->redirect(route('students.index'));
            return;
        }

        $this->render('login', [
            'errors' => [],
            'old' => []
        ], 'auth');
    }

    public function login(): void
    {
        if (!$this->isMethod('POST')) {
            $this->redirect(route('auth.login'));
            return;
        }

        if (!$this->validateCSRF()) {
            $this->abort(419, self::MSG_INVALID_CSRF);
            return;
        }

        $validator = $this->validateLoginData($_POST);

        if ($validator->fails()) {
            $this->renderLoginWithErrors($validator->errors(), $validator->validated());
            return;
        }

        $data = $validator->validated();
        
        try {
            $user = $this->authenticateUser($data['username'], $data['password']);
            
            if (!$user) {
                $this->renderLoginWithErrors(
                    ['general' => self::MSG_INVALID_CREDENTIALS],
                    ['username' => $data['username']]
                );
                return;
            }

            $user = $this->loadUserData($user);
            $this->startUserSession($user);
            
        } catch (Exception $e) {
            app_log("Error en login: " . $e->getMessage(), 'error');
            $this->renderLoginWithErrors(
                ['general' => 'Error al procesar el inicio de sesión. Intenta nuevamente.'],
                ['username' => $data['username']]
            );
        }
    }

    public function logout(): void
    {
        $user = auth_user();
        $username = $user['username'] ?? 'unknown';
        
        auth_logout();
        
        app_log("Usuario {$username} cerró sesión", 'info');
        flash('success', 'Sesión cerrada correctamente');
        
        $this->redirect(route('auth.login'));
    }

    public function showRegister(): void
    {
        if (!auth_check()) {
            flash('warning', self::MSG_LOGIN_REQUIRED);
            $this->redirect(route('auth.login'));
            return;
        }

        if (!auth_is_admin()) {
            flash('error', self::MSG_ADMIN_REQUIRED);
            $this->redirect(route('students.index'));
            return;
        }

        $this->render('register', [
            'errors' => [],
            'old' => []
        ]);
    }

    public function register(): void
    {
        if (!$this->verifyAdminAccess()) {
            return;
        }

        if (!$this->isMethod('POST')) {
            $this->redirect(route('auth.register'));
            return;
        }

        if (!$this->validateCSRF()) {
            $this->abort(419, self::MSG_INVALID_CSRF);
            return;
        }

        $validator = $this->validateRegistrationData($_POST);

        if ($validator->fails()) {
            $this->renderRegisterWithErrors($validator->errors(), $validator->validated());
            return;
        }

        $data = $validator->validated();
        
        if (!$this->passwordsMatch($data['password'], $data['password_confirmation'])) {
            $this->renderRegisterWithErrors(
                ['password_confirmation' => self::MSG_PASSWORDS_MISMATCH],
                $data
            );
            return;
        }

        if ($this->usernameExists($data['username'])) {
            $this->renderRegisterWithErrors(
                ['username' => self::MSG_USERNAME_EXISTS],
                $data
            );
            return;
        }

        if ($this->emailExists($data['email'])) {
            $this->renderRegisterWithErrors(
                ['email' => self::MSG_EMAIL_EXISTS],
                $data
            );
            return;
        }

        try {
            $userId = $this->createUserWithRole($data);
            
            $this->logUserRegistration($data['username']);
            flash('success', "Usuario '{$data['username']}' creado exitosamente.");
            
            $this->redirect(route('students.index'));
        } catch (Exception $e) {
            app_log("Error al registrar usuario: " . $e->getMessage(), 'error');
            $this->renderRegisterWithErrors(
                ['general' => self::MSG_REGISTRATION_ERROR],
                $data
            );
        }
    }

    private function validateLoginData(array $data): Validator
    {
        $validator = new Validator($data);
        
        $validator
            ->required('username', 'El usuario es obligatorio')
            ->required('password', 'La contraseña es obligatoria');

        return $validator;
    }

    private function validateRegistrationData(array $data): Validator
    {
        $validator = new Validator($data);
        
        $validator
            ->required('username', 'El nombre de usuario es obligatorio')
            ->min('username', self::MIN_USERNAME_LENGTH, "El usuario debe tener al menos " . self::MIN_USERNAME_LENGTH . " caracteres")
            ->max('username', self::MAX_USERNAME_LENGTH, "El usuario no debe exceder " . self::MAX_USERNAME_LENGTH . " caracteres")
            ->required('email', 'El correo es obligatorio')
            ->email('email', 'Formato de correo inválido')
            ->required('full_name', 'El nombre completo es obligatorio')
            ->required('password', 'La contraseña es obligatoria')
            ->min('password', self::MIN_PASSWORD_LENGTH, "La contraseña debe tener al menos " . self::MIN_PASSWORD_LENGTH . " caracteres")
            ->required('password_confirmation', 'Debes confirmar la contraseña');

        return $validator;
    }

    private function authenticateUser(string $username, string $password): array|false
    {
        return $this->userModel->authenticate($username, $password);
    }

    private function loadUserData(array $user): array
    {
        $user['roles'] = $this->userModel->getRoles($user['id']);
        $user['permissions'] = $this->userModel->getPermissions($user['id']);
        
        return $user;
    }

    private function startUserSession(array $user): void
    {
        auth_login($user);
        
        app_log("Usuario {$user['username']} inició sesión", 'info');
        flash('success', "¡Bienvenido, {$user['full_name']}!");
        
        $this->redirect(route('students.index'));
    }

    private function verifyAdminAccess(): bool
    {
        if (!auth_check() || !auth_is_admin()) {
            flash('error', self::MSG_NO_PERMISSION);
            $this->redirect(route('auth.login'));
            return false;
        }
        
        return true;
    }

    private function passwordsMatch(string $password, string $confirmation): bool
    {
        return $password === $confirmation;
    }

    private function usernameExists(string $username): bool
    {
        return (bool) $this->userModel->findByUsername($username);
    }

    private function emailExists(string $email): bool
    {
        return (bool) $this->userModel->findByEmail($email);
    }

    private function createUserWithRole(array $data): int
    {
        $userId = $this->userModel->create($data);
        
        $userRole = $this->roleModel->findByName(self::DEFAULT_ROLE);
        if ($userRole) {
            $this->userModel->assignRole($userId, $userRole['id']);
        }
        
        return $userId;
    }

    private function logUserRegistration(string $username): void
    {
        $adminUser = auth_user();
        $adminName = $adminUser['full_name'] ?? 'Administrador';
        app_log("Usuario {$username} registrado por {$adminName}", 'info');
    }

    private function renderLoginWithErrors(array $errors, array $oldData): void
    {
        $this->render('login', [
            'errors' => $errors,
            'old' => $oldData
        ], 'auth');
    }

    private function renderRegisterWithErrors(array $errors, array $oldData): void
    {
        $this->render('register', [
            'errors' => $errors,
            'old' => $oldData
        ]);
    }
}
