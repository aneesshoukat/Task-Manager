<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\ValidationService;
use PHPUnit\Framework\TestCase;

class ValidationServiceTest extends TestCase
{
    private ValidationService $service;

    protected function setUp(): void
    {
        $this->service = new ValidationService();
    }

    public function testValidRegisterData(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $errors = $this->service->validateRegister($data);
        $this->assertEmpty($errors);
    }

    public function testInvalidRegisterData(): void
    {
        $data = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'different',
        ];

        $errors = $this->service->validateRegister($data);
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('password', $errors);
    }

    public function testValidTaskData(): void
    {
        $data = [
            'title' => 'Test Task',
            'description' => 'A description',
            'priority' => 'high',
            'due_date' => '2026-12-31',
        ];

        $errors = $this->service->validateTask($data);
        $this->assertEmpty($errors);
    }

    public function testInvalidTaskData(): void
    {
        $data = [
            'title' => '',
            'priority' => 'urgent',
        ];

        $errors = $this->service->validateTask($data);
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('title', $errors);
        $this->assertArrayHasKey('priority', $errors);
    }
}
