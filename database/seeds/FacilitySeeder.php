<?php

use Illuminate\Database\Seeder;
use App\Facility;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = [
            [
                'name' => 'AC' 
            ], [
                'name' => 'Smoking Area'
            ], [
                'name' => 'Live Music'
            ], [
                'name' => 'Wifi Access'
            ], [
                'name' => 'Free Parking'
            ], [
                'name' => 'Buffet'
            ]
        ];
        
        foreach($type as $i) {
            Facility::create($i);
        }
    }
}
