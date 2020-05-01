<?php

use Illuminate\Database\Seeder;

class PizzaBillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i=1;$i<5;$i++){
            DB::table('pizza_bills')->insert([[
                'bill_id' => $i,
                'pizza_id' => rand(1, 8),
                'quantity' => rand(1, 4),
                'uni_value' => rand(5, 30),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]]);
            if(rand(1,2)==2){
                DB::table('pizza_bills')->insert([[
                    'bill_id' => $i,
                    'pizza_id' => rand(1, 8),
                    'quantity' => rand(1, 4),
                    'uni_value' => rand(5, 30),
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ]]);
            }
        }
    }
}
