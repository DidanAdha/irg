<?php

use Illuminate\Database\Seeder;
use App\Menu;
use Faker\Factory as Faker;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        for($i=0;$i<=50;$i++) {
            $menu = new Menu;
            $menu->restaurants_id = $faker->numberBetween(1,50);
            $menu->menu_types_id = $faker->numberBetween(1,5);
            $menu->desc = "Lorem ipsum dolor sit amet";
            $menu->name = $faker->name;
            $menu->price = $faker->numberBetween(10000, 50000);
            $menu->img = "/storage/menu_img/default.jpg";
            $menu->save();
        }
    }
}
