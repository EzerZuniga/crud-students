<?php
/**
 * Validator Test
 * Tests para el validador
 */

namespace Tests\Unit;

use Tests\TestCase;
use App\Core\Validator;

class ValidatorTest extends TestCase
{
    /** @test */
    public function it_validates_required_fields(): void
    {
        $data = ['name' => ''];
        $validator = new Validator($data);
        $validator->required('name', 'Name is required');

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors());
    }

    /** @test */
    public function it_passes_with_valid_required_field(): void
    {
        $data = ['name' => 'John'];
        $validator = new Validator($data);
        $validator->required('name', 'Name is required');

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_email_format(): void
    {
        $data = ['email' => 'invalid-email'];
        $validator = new Validator($data);
        $validator->email('email', 'Invalid email');

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function it_passes_with_valid_email(): void
    {
        $data = ['email' => 'test@example.com'];
        $validator = new Validator($data);
        $validator->email('email', 'Invalid email');

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_min_length(): void
    {
        $data = ['password' => '123'];
        $validator = new Validator($data);
        $validator->min('password', 6, 'Password too short');

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function it_validates_max_length(): void
    {
        $data = ['name' => str_repeat('a', 101)];
        $validator = new Validator($data);
        $validator->max('name', 100, 'Name too long');

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function it_can_chain_multiple_validations(): void
    {
        $data = [
            'name' => '',
            'email' => 'invalid',
            'password' => '123'
        ];

        $validator = new Validator($data);
        $validator
            ->required('name', 'Name required')
            ->email('email', 'Invalid email')
            ->min('password', 6, 'Password too short');

        $this->assertTrue($validator->fails());
        $errors = $validator->errors();
        
        $this->assertCount(3, $errors);
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('password', $errors);
    }

    /** @test */
    public function it_returns_validated_data(): void
    {
        $data = [
            'name' => 'John',
            'email' => 'john@test.com',
            'extra' => 'should not be included'
        ];

        $validator = new Validator($data);
        $validator->required('name')->email('email');

        $validated = $validator->validated();
        
        $this->assertArrayHasKey('name', $validated);
        $this->assertArrayHasKey('email', $validated);
    }
}
