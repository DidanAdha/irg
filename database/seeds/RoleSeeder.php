<?php

use Illuminate\Database\Seeder;
use App\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = [
            [
                "name" => "Admin"
            ], [
                "name" => "Admin Tech"
            ], [
                "name" => "Customer Service"
            ], [
                "name" => "Resto Owner"
            ], [
                "name" => "Resto Manager"
            ], [
                "name" => "Resto Waiter"
            ], [
                "name" => "Resto Cashier"
            ], [
                "name" => "Customer"
            ] 
        ];
        
        foreach($role as $i) {
            Role::create($i);
        }
    }
}
