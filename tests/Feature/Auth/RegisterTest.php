<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        Role::create(['name' => 'citizen', 'guard_name' => 'api']);

        $response = $this->postJson('/api/citizen/register', [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'roles', 'permissions', 'email_verified_at'],
                    'tokens' => ['access_token', 'refresh_token', 'expires_in']
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@test.com',
        ]);
    }
}
