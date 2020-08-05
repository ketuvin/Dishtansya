<?php

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            'id' => 1,
            'name' => 'Beff Steak',
            'available_stock' => 1000
        ]);

        DB::table('products')->insert([
            'id' => 2,
            'name' => 'French Fries',
            'available_stock' => 1000
        ]);

        // DEFINED USERS
        DB::table('products')->insert([
            'id' => 3,
            'name' => 'Spaghetti',
            'available_stock' => 1000
        ]);

        // DEFINED USERS
        DB::table('products')->insert([
            'id' => 4,
            'name' => 'Chicken Joy',
            'available_stock' => 1000
        ]);

        // DEFINED USERS
        DB::table('products')->insert([
            'id' => 5,
            'name' => 'Minute Burger',
            'available_stock' => 1000
        ]);
    }
}
