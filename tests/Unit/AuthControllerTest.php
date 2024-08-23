<?php

namespace Tests\Unit;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AuthControllerTest extends TestCase
{ 
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_it_should_login_a_user_with_valid_credentials()
    {
        // Arrange
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);

        $password = 'password123';
        
        $user = User::factory()->create([
            'name' => 'testTesting',
            'email' => 'test2@example.com',
            'password' => Hash::make($password),
        ]);

        // Ensure the role is assigned after the user is created
        $user->assignRole($role);

        // Act
        $response = $this->postJson('/api/login', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password,
        ]);

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }


    public function test_it_should_not_login_a_user_with_invalid_credentials()
    {
        // Arrange
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);

        $user = User::factory()->create([
            'name' => 'testTesting',
            'email' => 'test3@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Ensure the role is assigned after the user is created
        $user->assignRole($role);

        // Act
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        // Assert
        $response->assertStatus(401)
                 ->assertJson([
                     'error' => 'Unauthorized',
                     'message' => 'The provided credentials are incorrect.',
                 ]);
    }
}
