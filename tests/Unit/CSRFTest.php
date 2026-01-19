<?php
/**
 * CSRF Protection Test
 */

namespace Tests\Unit;

use Tests\TestCase;
use App\Core\CSRF;

class CSRFTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Limpiar token de sesión
        unset($_SESSION['csrf_token']);
    }

    /** @test */
    public function it_generates_csrf_token(): void
    {
        $token = CSRF::generateToken();

        $this->assertNotEmpty($token);
        $this->assertEquals(64, strlen($token));
    }

    /** @test */
    public function it_stores_token_in_session(): void
    {
        CSRF::generateToken();

        $this->assertArrayHasKey('csrf_token', $_SESSION);
    }

    /** @test */
    public function it_returns_existing_token(): void
    {
        $token1 = CSRF::generateToken();
        $token2 = CSRF::getToken();

        $this->assertEquals($token1, $token2);
    }

    /** @test */
    public function it_validates_correct_token(): void
    {
        $token = CSRF::generateToken();
        $_POST['csrf_token'] = $token;

        $this->assertTrue(CSRF::validate());
    }

    /** @test */
    public function it_rejects_invalid_token(): void
    {
        CSRF::generateToken();
        $_POST['csrf_token'] = 'invalid-token';

        $this->assertFalse(CSRF::validate());
    }

    /** @test */
    public function it_rejects_missing_token(): void
    {
        CSRF::generateToken();
        unset($_POST['csrf_token']);

        $this->assertFalse(CSRF::validate());
    }

    /** @test */
    public function it_generates_html_field(): void
    {
        $html = CSRF::field();

        $this->assertStringContainsString('input', $html);
        $this->assertStringContainsString('csrf_token', $html);
        $this->assertStringContainsString('hidden', $html);
    }
}
