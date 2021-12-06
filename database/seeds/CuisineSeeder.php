<?php

use Illuminate\Database\Seeder;
use App\Cuisine;

class CuisineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cuisine = [
            [
                'name' => 'Middle East'
            ], [
                'name' => 'Chinese'
            ], [
                'name' => 'Indonesian'
            ], [
                'name' => 'Western'
            ], [
                'name' => 'Cafe'
            ]
        ];

        foreach($cuisine as $i) {
            Cuisine::create($i);
        }
    }
}
