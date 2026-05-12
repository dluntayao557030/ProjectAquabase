<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'first_name' => 'Juan',
                'last_name'  => 'Claudio',
                'email'      => 'juan.claudio@aquabase.com',
                'username'   => 'juanclaudio',
                'password'   => Hash::make('Admin@1234'),
                'role'       => 'admin',
                'status'     => 'active',
            ],
            [
                'first_name' => 'Maria',
                'last_name'  => 'Santos',
                'email'      => 'maria.santos@aquabase.com',
                'username'   => 'mariasantos',
                'password'   => Hash::make('Staff@1234'),
                'role'       => 'staff',
                'status'     => 'active',
            ],
            [
                'first_name' => 'Pedro',
                'last_name'  => 'Reyes',
                'email'      => 'pedro.reyes@aquabase.com',
                'username'   => 'pedroreyes',
                'password'   => Hash::make('Staff@5678'),
                'role'       => 'staff',
                'status'     => 'active',
            ],
            [
                'first_name' => 'Ana',
                'last_name'  => 'Bautista',
                'email'      => 'ana.bautista@aquabase.com',
                'username'   => 'anabautista',
                'password'   => Hash::make('Staff@9012'),
                'role'       => 'staff',
                'status'     => 'active',
            ],
            [
                'first_name' => 'Carlos',
                'last_name'  => 'Dela Cruz',
                'email'      => 'carlos.delacruz@aquabase.com',
                'username'   => 'carlosdc',
                'password'   => Hash::make('Staff@3456'),
                'role'       => 'staff',
                'status'     => 'inactive',
            ],
        ]);
    }
}