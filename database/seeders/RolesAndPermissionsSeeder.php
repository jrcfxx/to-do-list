<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions - any change in permissions or roles will not be reflected immediately, unless the cache is cleared
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Creating permissions
        $permissions = [
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'view-tasks',
            'create-tasks',
            'edit-tasks',
            'delete-tasks',
            'view-taskchanges',
        ];

        // Creating records in the permissions table for each permission defined in the $permissions array
        // Inside the foreach loop, for each permission in the $permissions array, 
        // a record is created in the permissions table using the create method of the Permission model
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }

        // Creating roles
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        $userRole = Role::create(['name' => 'user', 'guard_name' => 'api']);
        $testRole = Role::create(['name' => 'test', 'guard_name' => 'api']);

        // Assign permissions to the 'admin' role
        $adminRole->givePermissionTo($permissions);

        // Assign permissions to the 'user' role
        $userRole->givePermissionTo([
            'view-tasks',
            'create-tasks',
            'edit-tasks',
            'delete-tasks',
            'view-taskchanges',
        ]);

        // Creating users and assigning roles
        $adminUsers = User::factory()->count(2)->create();
        foreach ($adminUsers as $user) {
            $user->assignRole($adminRole);
        }

        $normalUsers = User::factory()->count(1)->create();
        foreach ($normalUsers as $user) {
            $user->assignRole($userRole);
        }
        
    }
}