<?php

use App\Bill;
use App\Foto;
use App\Pizza;
use App\PizzaBill;
use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(PizzaSeeder::class);
        $this->call(FotoSeeder::class);
        $this->call(BillsSeeder::class);
        $this->call(PizzaBillSeeder::class);
    }
}
