<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userAdmin = User::create([
            'name' => 'Carmine',
            'email' => 'carmine@mail.com',
            'password' => 1234,
        ]);
        $userAdmin->assignRole(Role::first());

        $userClient = User::create([
            'name' => 'TW Group',
            'email' => 'hola@twgroup.cl',
            'password' => 1234,
        ]);
        $userClient->assignRole('Cliente');
    }
}
