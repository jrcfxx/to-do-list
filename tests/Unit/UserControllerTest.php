<?php

namespace Tests\Unit;

use App\Models\User; 
use Illuminate\Foundation\Testing\RefreshDatabase; 
use Spatie\Permission\Models\Role; 
use Tests\TestCase; 

class UserControllerTest extends TestCase
{
    use RefreshDatabase; // ensure the database is refreshed after each test, keeping tests isolated

    protected function setUp(): void
    {
        parent::setUp(); // ensure any setup in the TestCase class is executed
    }

    public function test_user_creation_requires_valid_data()
    {
        // Create a single admin user for authentication
        // The User::factory()->create() call must return a single instance of User Model, not a collection
        $adminUser = User::factory()->create();

        //dd($adminUser);

        $this->actingAs($adminUser, 'api'); // Authenticate the user for the API request, simulating a logged-in user


        // Simulate a request with invalid data to test validation
        $response = $this->postJson('/api/users', [
            'name' => '', 
            'email' => 'invalid-email', 
            'password' => '123',
        ]);

        // output the response
        //dd($response);

        // Assert
        $response->assertStatus(400)
                 ->assertJsonStructure([
                     'error', // The error key in the response
                     'messages' => [ // The messages key containing validation error messages
                         'name',
                         'email',
                         'password',
                     ],
                 ]);
    }

    public function test_user_creation_successful()
    {
        // Ensuring that the role user exists
        $roleUser = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'api']);

        // Create a user for authentication
        $adminUser = User::factory()->create();

        //dd($adminUser);

        $this->actingAs($adminUser, 'api');

        // Simulate a request with valid data to create a user
        $response = $this->postJson('/api/users', [
            'name' => 'Test User', 
            'email' => 'test1111@example.com', 
            'password' => 'password123', 
        ]);

        //dd($response);
        //dd($response->json());

        // Assert
        $response->assertStatus(201)
                 ->assertJson([
                     'name' => 'Test User',
                     'email' => 'test1111@example.com',
                 ]);

        // Verify that the user was successfully created in the database
        $this->assertDatabaseHas('users', [
            'email' => 'test1111@example.com',
        ]);
    }
}
