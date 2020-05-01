<?php

use Illuminate\Database\Seeder;

class PizzaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=1;$i<9;$i++){
            DB::table('pizzas')->insert([[
                'name' => "Pizza Number {$i}",
                'description' => "Description Pizza Number {$i}",
                'short_desc' => "Tags Pizza Number {$i}",
                'value_curr_dol' => rand(5, 30)+($i/10),
                'value_curr_eur' => rand(5, 30),
                'status' => 'A',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]]);
        }
    }
}
