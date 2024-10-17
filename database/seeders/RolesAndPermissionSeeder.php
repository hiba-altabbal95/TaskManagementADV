<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RolesAndPermissionSeeder extends Seeder
{

    /**
     * Run the user seeds.
     */
    public function run(): void
    {
        /** 
         * Create roles if they don't exist
         */
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);
       

        /** 
         * Create an admin account
         */
        $admin = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
        ]);
        $admin->assignRole('admin');


      

        // Optionally, create more users
        User::factory()->count(10)->create()->each(function ($user) {
            $user->assignRole('user'); // Assign default role to other users
        });
    }
    
}
