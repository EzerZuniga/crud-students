<?php
/**
 * Helper Functions Test
 * Tests para funciones helper globales
 */

namespace Tests\Unit;

use Tests\TestCase;

class HelpersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Cargar helpers
        require_once __DIR__ . '/../../app/helpers/functions.php';
    }

    /** @test */
    public function it_escapes_html(): void
    {
        $dangerous = '<script>alert("XSS")</script>';
        $safe = e($dangerous);

        $this->assertStringNotContainsString('<script>', $safe);
        $this->assertStringContainsString('&lt;script&gt;', $safe);
    }

    /** @test */
    public function it_sanitizes_strings(): void
    {
        $dirty = '  <b>Test</b>  ';
        $clean = sanitize_string($dirty);

        $this->assertEquals('Test', $clean);
        $this->assertStringNotContainsString('<b>', $clean);
    }

    /** @test */
    public function it_sanitizes_email(): void
    {
        $email = '  TEST@EXAMPLE.COM  ';
        $clean = sanitize_email($email);

        $this->assertEquals('TEST@EXAMPLE.COM', $clean);
    }

    /** @test */
    public function it_formats_dates(): void
    {
        $date = '2024-01-15 14:30:00';
        $formatted = format_date($date, 'd/m/Y');

        $this->assertEquals('15/01/2024', $formatted);
    }

    /** @test */
    public function it_generates_urls(): void
    {
        define('APP_URL', 'http://localhost/crud-students/public');
        
        $url = url('/students/create');
        $this->assertStringContainsString('/students/create', $url);
    }

    /** @test */
    public function it_checks_string_starts_with(): void
    {
        $this->assertTrue(str_starts_with('Hello World', 'Hello'));
        $this->assertFalse(str_starts_with('Hello World', 'World'));
    }

    /** @test */
    public function it_checks_string_ends_with(): void
    {
        $this->assertTrue(str_ends_with('Hello World', 'World'));
        $this->assertFalse(str_ends_with('Hello World', 'Hello'));
    }

    /** @test */
    public function it_gets_array_value_with_default(): void
    {
        $array = ['name' => 'John'];
        
        $this->assertEquals('John', array_get($array, 'name'));
        $this->assertEquals('default', array_get($array, 'missing', 'default'));
    }

    /** @test */
    public function it_converts_to_json(): void
    {
        $data = ['name' => 'Test', 'value' => 123];
        $json = to_json($data);

        $this->assertJson($json);
        $decoded = json_decode($json, true);
        $this->assertEquals('Test', $decoded['name']);
    }

    /** @test */
    public function it_formats_phone_numbers(): void
    {
        $phone = '(123) 456-7890';
        $formatted = format_phone($phone);

        $this->assertEquals('1234567890', $formatted);
    }
}
