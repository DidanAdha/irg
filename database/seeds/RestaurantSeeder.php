<?php

use Illuminate\Database\Seeder;
use App\Restaurant;
use Faker\Factory as Faker;
use Carbon\Carbon;

class RestaurantSeeder extends Seeder
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
            $resto = new Restaurant;
            $resto->users_id = $faker->numberBetween(4,4);
            // $resto->city_id = $faker->numberBetween(1,5);
            $resto->phone_number = $faker->e164PhoneNumber();
            $resto->name = $faker->name;
            $resto->desc = "Lorem ipsum dolor sit amet";
            $resto->latitude = $faker->latitude(-90, 90); 
            $resto->longitude = $faker->longitude(-180, 180);
            $resto->img = "/storage/resto_img/default.jpg";
            $resto->cities_id = $faker->numberBetween(3578,3579);
            $resto->start_price = $faker->numberBetween(1000,20000);
            $resto->end_price = $faker->numberBetween(25000,100000);
            $resto->address = $faker->address();
            $resto->open_at = $faker->time('H:i:s', $max = 'now');
            $resto->close_at= Carbon::createFromFormat('H:i:s', $resto->open_at)->addHours( $faker->numberBetween( 1, 8 ) );
            $resto->save();
        }
    }
}
