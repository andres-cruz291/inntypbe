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
        // $this->call(UserSeeder::class);
        $bills = factory(Bill::class, 8)->create();
        $pizzas = factory(Pizza::class, 8)->create();
        $fotos = factory(Foto::class, 32)->create();
        $pizzasBill = factory(PizzaBill::class, 24)->create();
        $users = factory(User::class, 8)->create();
    }
}
