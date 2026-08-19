<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test Applicant',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'token',
                'payload',
                'user',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test Applicant',
        ]);
    }

    public function test_user_can_login(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => 'Test Applicant',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'payload',
                'user',
            ]);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => 'Test Applicant',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    }
}