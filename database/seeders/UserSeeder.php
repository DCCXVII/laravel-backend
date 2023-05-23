<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $adminRole = Role::findByName('admin');
        $instructorRole = Role::findByName('instructor');
        $clientRole = Role::findByName('client');

        //admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin'), 
        ])->assignRole($adminRole);

        //instructor User
        User::create([
            'name' => 'instructor',
            'email' => 'instructor@example.com',
            'password' => Hash::make('instructor'), 
        ])->assignRole($instructorRole);

        //Client User
        User::create([
            'name' => 'client',
            'email' => 'client@example.com',
            'password' => Hash::make('client'), 
        ])->assignRole($clientRole);

        //User Without Role
        User::create([
            'name' => 'normalclient',
            'email' => 'normalclient@example.com',
            'password' => Hash::make('instructor'), 
        ]);
    }
}
