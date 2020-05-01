<?php

use Illuminate\Database\Seeder;

class BillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bills')->insert([[
            'currency' => "E",
            'value_del' => rand(0, 15),
            'status' => 'G',
            'location' => 'First Location',
            'mobile' => '333 44 25',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]]);
        DB::table('bills')->insert([[
            'user_id' => rand(2, 3),
            'currency' => "E",
            'value_del' => rand(0, 15),
            'status' => 'G',
            'location' => 'Second Location',
            'mobile' => '333 876 278',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ],[
            'user_id' => rand(2, 3),
            'currency' => "E",
            'value_del' => rand(0, 15),
            'status' => 'G',
            'location' => 'Third Location',
            'mobile' => '333 446 965',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ],[
            'user_id' => rand(2, 3),
            'currency' => "E",
            'value_del' => rand(0, 15),
            'status' => 'G',
            'location' => 'Fourth Location',
            'mobile' => '333 07 46',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]]);
    }
}
