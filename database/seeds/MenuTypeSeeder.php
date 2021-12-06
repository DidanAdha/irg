<?php

use Illuminate\Database\Seeder;
use App\MenuType as Category;

class MenuTypeSeeder extends Seeder
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
                'name' => 'Opening Course' 
            ], [
                'name' => 'Main Course'
            ], [
                'name' => 'Desert'
            ], [
                'name' => 'Drink'
            ], [
                'name' => 'Alcohol'
            ] 
        ];
        
        foreach($type as $i) {
            Category::create($i);
        }
    }
}
