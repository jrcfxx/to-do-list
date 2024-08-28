<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignUserRole extends Command
{
    /**
     * The name and signature of the console command.
     * Define the command name and the expected arguments.
     *
     * @var string
     */
    protected $signature = 'app:assign-user-role {userId} {role}';

    /**
     * The console command description.
     * Provides a brief description of what the command does.
     *
     * @var string
     */
    protected $description = 'Assign a specific role to a user by their ID';

    /**
     * Execute the console command.
     * This method contains the logic that runs when the command is executed.
     *
     * @return int Returns 0 on success, or 1 on failure.
     */
    public function handle()
    {
        // Retrieve the userId argument from the command line input
        $userId = $this->argument('userId');

        // Retrieve the role argument from the command line input
        $roleName = $this->argument('role');

        // Finding the user by ID
        // Attempt to retrieve the User model instance for the given ID
        $user = User::find($userId);

        if (!$user) {
            // error message and return a failure code (1)
            $this->error("User with ID {$userId} not found.");
            return 1;
        } else {
        // Finding the role by name
        // Attempt to retrieve the Role model instance for the given role name
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            // If the role does not exist, display an error message and return a failure code (1)
            $this->error("Role '{$roleName}' not found.");
            return 1;
        }

        // assignRole method to assign the role to the user
        $user->assignRole($role);

        // Role assignment was successful
        $this->info("Role '{$roleName}' has been assigned to user ID {$userId}.");

        // Return success code (0)
        return 0;
        }
        
    }
}
