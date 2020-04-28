<?php

namespace Tests\Feature;

use App\Foto;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Bill;
use App\Pizza;
use App\PizzaBill;

class BillTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetBill()
    {
        $bills = factory(Bill::class, 8)->create();
        $pizzas = factory(Pizza::class, 8)->create();
        $fotos = factory(Foto::class, 32)->create();
        $pizzasBill = factory(PizzaBill::class, 24)->create();
        $users = factory(User::class, 8)->create();

        $response = $this->json('GET', '/api/bill');
        $response->assertStatus(200)
            ->assertJsonStructure(['id', 'user_id', 'currency',
                'value_del', 'status', 'location',
                'mobile', 'additional_dat', 'pizzas' =>
                    ['*', [
                       'id', 'pizza_id', 'pizza'=>[
                           '*', [
                               'id', 'name', 'shor_desc', 'description', 'value_curr_dol', 'value_curr_eur', 'status'
                            ]
                        ]
                    ]], 'user'=>[
                        '*', ['id','name','email']
                ]]);
        $response->assertJsonCount(8, $response->json());
    }
}
