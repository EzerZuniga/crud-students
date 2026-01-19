<?php
/**
 * User Model Test
 * Tests para el modelo User y sistema de autenticación
 */

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class UserTest extends TestCase
{
    private User $userModel;
    private Role $roleModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userModel = new User($this->db);
        $this->roleModel = new Role($this->db);
        
        $this->cleanTable('role_user');
        $this->cleanTable('users');
    }

    /** @test */
    public function it_can_create_a_user(): void
    {
        $data = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'full_name' => 'Test User',
            'is_active' => true
        ];

        $id = $this->userModel->create($data);

        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }

    /** @test */
    public function it_hashes_password_on_create(): void
    {
        $data = [
            'username' => 'secureuser',
            'email' => 'secure@test.com',
            'password' => 'mypassword',
            'full_name' => 'Secure User'
        ];

        $id = $this->userModel->create($data);
        $user = $this->userModel->find($id);

        $this->assertNotEquals('mypassword', $user['password']);
        $this->assertTrue(password_verify('mypassword', $user['password']));
    }

    /** @test */
    public function it_can_find_user_by_username(): void
    {
        $this->insertTestData('users', [
            'username' => 'johndoe',
            'email' => 'john@test.com',
            'password' => password_hash('test123', PASSWORD_DEFAULT),
            'full_name' => 'John Doe',
            'is_active' => 1
        ]);

        $user = $this->userModel->findByUsername('johndoe');

        $this->assertIsArray($user);
        $this->assertEquals('johndoe', $user['username']);
    }

    /** @test */
    public function it_can_find_user_by_email(): void
    {
        $this->insertTestData('users', [
            'username' => 'jane',
            'email' => 'jane@test.com',
            'password' => password_hash('test123', PASSWORD_DEFAULT),
            'full_name' => 'Jane Doe',
            'is_active' => 1
        ]);

        $user = $this->userModel->findByEmail('jane@test.com');

        $this->assertIsArray($user);
        $this->assertEquals('jane@test.com', $user['email']);
    }

    /** @test */
    public function it_can_authenticate_valid_credentials(): void
    {
        $password = 'correctpassword';
        
        $this->insertTestData('users', [
            'username' => 'authuser',
            'email' => 'auth@test.com',
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'full_name' => 'Auth User',
            'is_active' => 1
        ]);

        $user = $this->userModel->authenticate('authuser', $password);

        $this->assertIsArray($user);
        $this->assertEquals('authuser', $user['username']);
        $this->assertArrayNotHasKey('password', $user);
    }

    /** @test */
    public function it_returns_null_for_invalid_credentials(): void
    {
        $this->insertTestData('users', [
            'username' => 'testuser',
            'email' => 'test@test.com',
            'password' => password_hash('rightpassword', PASSWORD_DEFAULT),
            'full_name' => 'Test User',
            'is_active' => 1
        ]);

        $user = $this->userModel->authenticate('testuser', 'wrongpassword');

        $this->assertNull($user);
    }

    /** @test */
    public function it_returns_null_for_inactive_user(): void
    {
        $this->insertTestData('users', [
            'username' => 'inactive',
            'email' => 'inactive@test.com',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'full_name' => 'Inactive User',
            'is_active' => 0
        ]);

        $user = $this->userModel->authenticate('inactive', 'password');

        $this->assertNull($user);
    }

    /** @test */
    public function it_can_assign_role_to_user(): void
    {
        // Crear usuario
        $userId = $this->insertTestData('users', [
            'username' => 'roleuser',
            'email' => 'role@test.com',
            'password' => password_hash('test', PASSWORD_DEFAULT),
            'full_name' => 'Role User',
            'is_active' => 1
        ]);

        // Obtener rol de usuario
        $userRole = $this->roleModel->findByName('user');
        $this->assertNotNull($userRole, 'El rol "user" debe existir en la base de datos');

        // Asignar rol
        $result = $this->userModel->assignRole($userId, $userRole['id']);
        $this->assertTrue($result);

        // Verificar rol
        $hasRole = $this->userModel->hasRole($userId, 'user');
        $this->assertTrue($hasRole);
    }

    /** @test */
    public function it_can_check_user_permission(): void
    {
        // Crear usuario
        $userId = $this->insertTestData('users', [
            'username' => 'permuser',
            'email' => 'perm@test.com',
            'password' => password_hash('test', PASSWORD_DEFAULT),
            'full_name' => 'Permission User',
            'is_active' => 1
        ]);

        // Asignar rol user (que tiene permisos de students.view)
        $userRole = $this->roleModel->findByName('user');
        if ($userRole) {
            $this->userModel->assignRole($userId, $userRole['id']);

            $hasPermission = $this->userModel->hasPermission($userId, 'students.view');
            $this->assertTrue($hasPermission);
        } else {
            $this->markTestSkipped('El rol "user" no existe en la base de datos');
        }
    }

    /** @test */
    public function it_can_get_user_roles(): void
    {
        $userId = $this->insertTestData('users', [
            'username' => 'multirole',
            'email' => 'multi@test.com',
            'password' => password_hash('test', PASSWORD_DEFAULT),
            'full_name' => 'Multi Role User',
            'is_active' => 1
        ]);

        $userRole = $this->roleModel->findByName('user');
        if ($userRole) {
            $this->userModel->assignRole($userId, $userRole['id']);
            $roles = $this->userModel->getRoles($userId);

            $this->assertIsArray($roles);
            $this->assertCount(1, $roles);
            $this->assertEquals('user', $roles[0]['name']);
        } else {
            $this->markTestSkipped('El rol "user" no existe');
        }
    }

    /** @test */
    public function it_can_get_user_permissions(): void
    {
        $userId = $this->insertTestData('users', [
            'username' => 'permuser2',
            'email' => 'perm2@test.com',
            'password' => password_hash('test', PASSWORD_DEFAULT),
            'full_name' => 'Perm User 2',
            'is_active' => 1
        ]);

        $userRole = $this->roleModel->findByName('user');
        if ($userRole) {
            $this->userModel->assignRole($userId, $userRole['id']);
            $permissions = $this->userModel->getPermissions($userId);

            $this->assertIsArray($permissions);
            $this->assertNotEmpty($permissions);
        } else {
            $this->markTestSkipped('El rol "user" no existe');
        }
    }

    /** @test */
    public function it_can_update_user(): void
    {
        $userId = $this->insertTestData('users', [
            'username' => 'updateuser',
            'email' => 'update@test.com',
            'password' => password_hash('test', PASSWORD_DEFAULT),
            'full_name' => 'Original Name',
            'is_active' => 1
        ]);

        $updateData = [
            'email' => 'newemail@test.com',
            'full_name' => 'Updated Name'
        ];

        $result = $this->userModel->update($userId, $updateData);
        $this->assertTrue($result);

        $user = $this->userModel->find($userId);
        $this->assertEquals('newemail@test.com', $user['email']);
        $this->assertEquals('Updated Name', $user['full_name']);
    }
}
