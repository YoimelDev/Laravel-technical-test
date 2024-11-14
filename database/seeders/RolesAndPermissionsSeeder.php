<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Enums\RoleType;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            // Company permissions
            'view companies',
            'create companies',
            'edit companies',
            'delete companies',
            // Activity permissions
            'view activities',
            'create activities',
            'edit activities',
            'delete activities',
            // Role management
            'manage roles',
            'process role requests',
            // Currency operations
            'convert currency',
            'view historical rates'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        Role::create(['name' => RoleType::BASIC->value])
            ->givePermissionTo(['view companies', 'view activities']);

        Role::create(['name' => RoleType::BUSINESS_OWNER->value])
            ->givePermissionTo([
                'view companies',
                'create companies',
                'edit companies',
                'view activities',
                'create activities',
                'convert currency',
                'view historical rates'
            ]);

        Role::create(['name' => RoleType::ADMIN->value])
            ->givePermissionTo(Permission::all());
    }
}