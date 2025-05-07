<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Services\Permissions;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionsCreate = new Permissions;

        foreach ($permissionsCreate->adminPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        foreach ($permissionsCreate->clientPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $adminRole = Role::create([
            'name' => 'Administrador',
        ]);
        $adminRole->syncPermissions($permissionsCreate->adminPermissions);

        $userRole = Role::create([
            'name' => 'Cliente',
        ]);
        $userRole->syncPermissions($permissionsCreate->clientPermissions);
    }
}
