<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage platform',
            'manage schools',
            'manage users',
            'manage resources',
            'publish resources',
            'manage subscriptions',
            'manage payments',
            'manage classes',
            'assign resources',
            'view reports',
            'read resources',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
            ]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $platformAdmin = Role::firstOrCreate(['name' => 'platform_admin']);
        $contentManager = Role::firstOrCreate(['name' => 'content_manager']);
        $author = Role::firstOrCreate(['name' => 'author']);
        $schoolAdmin = Role::firstOrCreate(['name' => 'school_admin']);
        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        $student = Role::firstOrCreate(['name' => 'student']);
        $individualSubscriber = Role::firstOrCreate([
            'name' => 'individual_subscriber',
        ]);
        $finance = Role::firstOrCreate(['name' => 'finance']);
        $support = Role::firstOrCreate(['name' => 'support']);

        $superAdmin->syncPermissions(Permission::all());

        $platformAdmin->syncPermissions([
            'manage schools',
            'manage users',
            'manage resources',
            'manage subscriptions',
            'manage payments',
            'view reports',
        ]);

        $contentManager->syncPermissions([
            'manage resources',
            'publish resources',
        ]);

        $schoolAdmin->syncPermissions([
            'manage users',
            'manage classes',
            'assign resources',
            'view reports',
            'read resources',
        ]);

        $teacher->syncPermissions([
            'manage classes',
            'assign resources',
            'read resources',
        ]);

        $student->syncPermissions([
            'read resources',
        ]);

        $individualSubscriber->syncPermissions([
            'read resources',
        ]);

        $finance->syncPermissions([
            'manage subscriptions',
            'manage payments',
            'view reports',
        ]);
    }
}