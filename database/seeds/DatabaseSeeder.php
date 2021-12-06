<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RestaurantSeeder::class);
        $this->call(MenuTypeSeeder::class);
        $this->call(CuisineSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(FacilitySeeder::class);
    }
}
