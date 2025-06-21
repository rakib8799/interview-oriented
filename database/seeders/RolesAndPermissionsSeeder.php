<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User; // Import the User model
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache; // To clear permission cache

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $viewAllTasksPermission = Permission::firstOrCreate(['name' => 'view all tasks']);
        $manageTasksPermission = Permission::firstOrCreate(['name' => 'manage tasks']); // For update/delete/restore/forceDelete on any task

        // Create roles and assign created permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Assign permissions to roles
        $adminRole->givePermissionTo([$viewAllTasksPermission, $manageTasksPermission]);
        $managerRole->givePermissionTo([$manageTasksPermission]);
        $userRole->givePermissionTo([$viewAllTasksPermission]);
        // Regular users typically don't get specific permissions beyond what policies handle (ownership)
        // or global 'create task' which is handled by general auth check.

        // Create a test admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $adminUser->assignRole('admin');

        // Create a test regular manager
        $managerUser = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
            ]
        );
        $managerUser->assignRole('manager');

        // Create a test regular user
        $regularUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
            ]
        );
        $regularUser->assignRole('user');

        // Clear application cache after seeding roles and permissions
        Cache::flush();
        $this->command->info('Roles and permissions seeded successfully!');
    }
}
