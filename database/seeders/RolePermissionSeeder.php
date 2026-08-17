<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Clear Permission Cache
        |--------------------------------------------------------------------------
        */

        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();


        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

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
                'guard_name' => 'web',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $platformAdmin = Role::firstOrCreate([
            'name' => 'platform_admin',
            'guard_name' => 'web',
        ]);

        $contentManager = Role::firstOrCreate([
            'name' => 'content_manager',
            'guard_name' => 'web',
        ]);

        $author = Role::firstOrCreate([
            'name' => 'author',
            'guard_name' => 'web',
        ]);

        $schoolAdmin = Role::firstOrCreate([
            'name' => 'school_admin',
            'guard_name' => 'web',
        ]);

        $teacher = Role::firstOrCreate([
            'name' => 'teacher',
            'guard_name' => 'web',
        ]);

        $student = Role::firstOrCreate([
            'name' => 'student',
            'guard_name' => 'web',
        ]);

        $individualSubscriber = Role::firstOrCreate([
            'name' => 'individual_subscriber',
            'guard_name' => 'web',
        ]);

        $finance = Role::firstOrCreate([
            'name' => 'finance',
            'guard_name' => 'web',
        ]);

        $support = Role::firstOrCreate([
            'name' => 'support',
            'guard_name' => 'web',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Assign Permissions
        |--------------------------------------------------------------------------
        */

        $superAdmin->syncPermissions(
            Permission::where(
                'guard_name',
                'web'
            )->get()
        );

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

        $author->syncPermissions([
            'manage resources',
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

        $support->syncPermissions([
            'manage users',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Clear Permission Cache Again
        |--------------------------------------------------------------------------
        */

        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();
    }
}