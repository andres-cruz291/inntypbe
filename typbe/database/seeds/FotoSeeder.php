<?php

use Illuminate\Database\Seeder;

class FotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $foto = 1;
        for ($i=0;$i<32;$i++){
            DB::table('fotos')->insert([[
                'pizza_id' => rand(1, 8),
                'path' => "storage\\uploads\\pizza".($foto++).".jpg",
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]]);
        }
    }
}
