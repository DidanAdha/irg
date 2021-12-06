<?php

use Illuminate\Database\Seeder;
use App\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('adminadmin'),
                'phone_number' => '+6285685678567',
                'address' => 'Jl Admin',
                'roles_id' => 1,
                'img' => '/storage/user_img/default.png',
                'priv_admin' => 1
            ], [
                'name' => 'Admin Tech',
                'email' => 'admin2@admin.com',
                'password' => Hash::make('adminadmin'),
                'phone_number' => '+6285685678567',
                'address' => 'Jl admin',
                'roles_id' => 2,
                'img' => '/storage/user_img/default.png',
                'priv_admin' => 1
            ], [
                'name' => 'Customer Service',
                'email' => 'admin3@admin.com',
                'password' => Hash::make('adminadmin'),
                'phone_number' => '+6285685678567',
                'address' => 'Jl admin',
                'roles_id' => 3,
                'img' => '/storage/user_img/default.png',
                'priv_admin' => 1
            ], [
                'name' => 'Resto Owner',
                'email' => 'resto@resto.com',
                'password' => Hash::make('restoresto'),
                'phone_number' => '+6285685678567',
                'address' => 'Jl Resto',
                'roles_id' => 4,
                'img' => '/storage/user_img/default.png',
                'priv_admin' => 0
            ], [
                'name' => 'Resto Manager',
                'email' => 'resto2@resto.com',
                'password' => Hash::make('restoresto'),
                'phone_number' => '+6285685678567',
                'address' => 'Jl Resto',
                'roles_id' => 5,
                'img' => '/storage/user_img/default.png',
                'priv_admin' => 0
            ], [
                'name' => 'Resto Waiter',
                'email' => 'resto3@resto.com',
                'password' => Hash::make('restoresto'),
                'phone_number' => '+6285685678567',
                'address' => 'Jl Resto',
                'roles_id' => 6,
                'img' => '/storage/user_img/default.png',
                'priv_admin' => 0
            ], [
                'name' => 'Resto Cashier',
                'email' => 'resto4@resto.com',
                'password' => Hash::make('restoresto'),
                'phone_number' => '+6285685678567',
                'address' => 'Jl Resto',
                'roles_id' => 7,
                'img' => '/storage/user_img/default.png',
                'priv_admin' => 0
            ], [
                'name' => 'Customer',
                'email' => 'user@user.com',
                'password' => Hash::make('useruser'),
                'phone_number' => '+6285685678567',
                'address' => 'Jl user',
                'roles_id' => 8,
                'img' => '/storage/user_img/default.png',
                'priv_admin' => 0
            ]
        ];
        
        foreach($user as $i) {
            User::create($i);
        }
    }
}
