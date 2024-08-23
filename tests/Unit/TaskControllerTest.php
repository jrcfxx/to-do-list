<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use App\Models\TaskChange;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_task_creation_requires_valid_data()
    {
         // Create a single admin user for authentication
        // The User::factory()->create() call must return a single instance of User Model, not a collection
        $adminUser = User::factory()->create();

        //dd($adminUser);

        $this->actingAs($adminUser, 'api'); // Authenticate the user for the API request, simulating a logged-in user


        // Simulate a request with invalid data to test validation
        $response = $this->postJson('/api/tasks', [
            "user_id" => " ",
            "title" => 4,
            "description" => 4,
            "priority" => "TEST",
            "due_date" => 5,
            "completeness_date" => 5,
            "delete_date" => 5
        ]);

        // Assert
        $response->assertStatus(400)
                 ->assertJsonStructure([
                     'error', // The error key in the response
                     'messages' => [ // The messages key containing validation error messages
                        "user_id",
                        "title",
                        "description",
                        "priority",
                        "due_date",
                        "completeness_date",
                        "delete_date",
                     ],
                 ]);
    }

    public function test_task_creation_successful()
    {
        // Create a user for authentication
        $adminUser = User::factory()->create();

        //dd($adminUser);

        $this->actingAs($adminUser, 'api');

        $currentDateTime = Carbon::now()->toDateTimeString();

        // Simulate a request with valid data to create a user
        $response = $this->postJson('/api/tasks', [
            "user_id" => $adminUser->id,
            "title" => "TEST",
            "description" => "TEST1111",
            "priority" => 4,
            "due_date" => $currentDateTime,
            "completeness_date" => $currentDateTime,
            "delete_date" => $currentDateTime,
        ]);

        //dd($response);

        // Assert
        $response->assertStatus(201)
                 ->assertJson([
                    "user_id" => $adminUser->id,
                    "title" => "TEST",
                    "description" => "TEST1111",
                    "priority" => 4,
                    "due_date" => $currentDateTime,
                    "completeness_date" => $currentDateTime,
                    "delete_date" => $currentDateTime,
                 ]);

        // Verify that the task was successfully created in the database
        $this->assertDatabaseHas('task', [
            "description" => "TEST1111",
        ]);
    }

}
