<?php
/**
 * Student Model Test
 * Tests para el modelo Student
 */

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Student;

class StudentTest extends TestCase
{
    private Student $studentModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->studentModel = new Student($this->db);
        $this->cleanTable('students');
    }

    /** @test */
    public function it_can_create_a_student(): void
    {
        $data = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'phone' => '1234567890'
        ];

        $id = $this->studentModel->create($data);

        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }

    /** @test */
    public function it_can_find_a_student_by_id(): void
    {
        $testData = [
            'name' => 'María García',
            'email' => 'maria@example.com',
            'phone' => '0987654321'
        ];

        $id = $this->insertTestData('students', $testData);
        $student = $this->studentModel->find($id);

        $this->assertIsArray($student);
        $this->assertEquals($testData['name'], $student['name']);
        $this->assertEquals($testData['email'], $student['email']);
    }

    /** @test */
    public function it_returns_null_when_student_not_found(): void
    {
        $student = $this->studentModel->find(99999);
        $this->assertNull($student);
    }

    /** @test */
    public function it_can_get_all_students(): void
    {
        $this->insertTestData('students', [
            'name' => 'Student 1',
            'email' => 'student1@test.com',
            'phone' => '111'
        ]);

        $this->insertTestData('students', [
            'name' => 'Student 2',
            'email' => 'student2@test.com',
            'phone' => '222'
        ]);

        $students = $this->studentModel->all();

        $this->assertIsArray($students);
        $this->assertCount(2, $students);
    }

    /** @test */
    public function it_can_update_a_student(): void
    {
        $id = $this->insertTestData('students', [
            'name' => 'Original Name',
            'email' => 'original@test.com',
            'phone' => '000'
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@test.com',
            'phone' => '999'
        ];

        $result = $this->studentModel->update($id, $updateData);
        $this->assertTrue($result);

        $student = $this->studentModel->find($id);
        $this->assertEquals('Updated Name', $student['name']);
        $this->assertEquals('updated@test.com', $student['email']);
    }

    /** @test */
    public function it_can_delete_a_student(): void
    {
        $id = $this->insertTestData('students', [
            'name' => 'To Delete',
            'email' => 'delete@test.com',
            'phone' => '000'
        ]);

        $result = $this->studentModel->delete($id);
        $this->assertTrue($result);

        $student = $this->studentModel->find($id);
        $this->assertNull($student);
    }

    /** @test */
    public function it_can_paginate_students(): void
    {
        // Insertar 15 estudiantes
        for ($i = 1; $i <= 15; $i++) {
            $this->insertTestData('students', [
                'name' => "Student {$i}",
                'email' => "student{$i}@test.com",
                'phone' => "100{$i}"
            ]);
        }

        $paginator = $this->studentModel->paginate(1, 10);

        $this->assertEquals(15, $paginator->total());
        $this->assertEquals(10, $paginator->perPage());
        $this->assertEquals(1, $paginator->currentPage());
        $this->assertEquals(2, $paginator->lastPage());
        $this->assertCount(10, $paginator->items());
    }

    /** @test */
    public function it_can_search_students(): void
    {
        $this->insertTestData('students', [
            'name' => 'Carlos López',
            'email' => 'carlos@test.com',
            'phone' => '111'
        ]);

        $this->insertTestData('students', [
            'name' => 'Ana Martínez',
            'email' => 'ana@test.com',
            'phone' => '222'
        ]);

        $results = $this->studentModel->search('Carlos');

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertEquals('Carlos López', $results[0]['name']);
    }

    /** @test */
    public function it_can_count_students(): void
    {
        $this->insertTestData('students', [
            'name' => 'Test 1',
            'email' => 'test1@test.com',
            'phone' => '111'
        ]);

        $this->insertTestData('students', [
            'name' => 'Test 2',
            'email' => 'test2@test.com',
            'phone' => '222'
        ]);

        $count = $this->studentModel->count();
        $this->assertEquals(2, $count);
    }

    /** @test */
    public function it_sanitizes_input_data(): void
    {
        $data = [
            'name' => '  <script>alert("XSS")</script>  Juan  ',
            'email' => '  JUAN@EXAMPLE.COM  ',
            'phone' => '  123-456-7890  '
        ];

        $id = $this->studentModel->create($data);
        $student = $this->studentModel->find($id);

        $this->assertStringNotContainsString('<script>', $student['name']);
        $this->assertStringNotContainsString('  ', $student['name']);
    }

    /** @test */
    public function it_gets_statistics(): void
    {
        $this->insertTestData('students', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'phone' => '123'
        ]);

        $stats = $this->studentModel->getStats();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('today', $stats);
        $this->assertArrayHasKey('week', $stats);
        $this->assertArrayHasKey('month', $stats);
        $this->assertArrayHasKey('email_domains', $stats);
        $this->assertEquals(1, $stats['total']);
    }
}
