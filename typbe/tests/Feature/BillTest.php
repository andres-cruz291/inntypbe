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
    //use DatabaseMigrations;
    use RefreshDatabase;

    private function loadData(){
        try{
            $users = factory(User::class, 8)->create();
            $fotos = factory(Foto::class, 32)->create();
            $pizzas = factory(Pizza::class, 8)->create();
            $bills = factory(Bill::class, 8)->create();
            $pizzasBill = factory(PizzaBill::class, 24)->create();
        }catch (\Exception $e){
            throw new \Exception('Data was not loaded: '.$e->getMessage());
        }
    }

    private function validateIndex(){
        try{
            $response = $this->json('GET', '/api/bill');
            $response->assertStatus(200);
            $response->assertJsonCount(8);
            $response->assertJsonStructure([
                'id', 'user_id', 'currency',
                'value_del', 'status', 'location',
                'mobile', 'additional_dat', 'pizzas',
                'value', 'value_del', 'value_ord',
                'user'], $response->json()[0]);
        }catch (\Exception $e){
            throw new \Exception('Bills index was failed : '.$e->getMessage());
        }
    }

    private function validateBillFormat($response){
        try{
            $response->assertJsonStructure([
                'id', 'currency', 'value_del',
                'status', 'value', 'value_ord',
                'pizzas'=>[
                    '*'=>[
                        'id', 'bill_id', 'pizza_id',
                        'quantity', 'uni_value', 'pizza'=>[
                            'id', 'name', 'short_desc',
                            'description', 'value_curr_dol', 'value_curr_eur',
                            'status'
                        ]
                    ]
                ]
            ]);
        }catch (\Exception $e){
            throw new \Exception('Bills format is not correct: '.$e->getMessage());
        }
    }

    private function validateBillData($response, $currency, $status, $pos, $pizza_id, $quantity){
        try{
            $data = $response->json();
            if((!($data['id'])||($data['currency']!=$currency)||($data['status']!=$status))){
                throw new \Exception("The Bill was not created: ".
                    "ID: {$data['id']}; Currency: [{$data['currency']}][{$currency}]; ".
                    "Status: [{$data['status']}][{$status}]");
            }
            if((($data['pizzas'][$pos]['pizza_id']!=$pizza_id)||
                ($data['pizzas'][$pos]['bill_id']!=$data['id'])||
                ($data['pizzas'][$pos]['quantity']!=$quantity))){
                throw new \Exception('The detail Bill was not created ...');
            }
        }catch(\Exception $e){
            throw new \Exception('Bills format is not correct: '.$e->getMessage());
        }
    }

    private function validateBillEndData($response, $bill, $user_id){
        try{
            $data = $response->json();
            if($data['currency']!=$bill->currency){
                throw new \Exception("The Bill currency was not updated");
            }
            if($data['status']!=$bill->status){
                throw new \Exception("The Bill status was not updated");
            }
            if($data['location']!=$bill->location){
                throw new \Exception("The Bill location was not updated");
            }
            if($data['mobile']!=$bill->mobile){
                throw new \Exception("The Bill mobile was not updated");
            }
            if($data['additional_dat']!=$bill->additional_dat){
                throw new \Exception("The Bill additional data was not updated");
            }
            if($data['value_del']!=$bill->value_del){
                throw new \Exception("The Bill additional data was not updated");
            }
            if($user_id && $data['user_id']!=$user_id){
                throw new \Exception("The Bill user was not updated");
            }
        }catch(\Exception $e){
            throw new \Exception('Bills end format is not correct: '.$e->getMessage());
        }
    }

    private function validateStoreWithoutUser(){
        $line = "";
        try{
            $line = "Creating Bill";
            $pizzaBill = factory(PizzaBill::class)->make();
            $response = $this->json('POST','/api/bill',[
                'pizza_id' => $pizzaBill->pizza_id,
                'quantity' => $pizzaBill->quantity,
                'currency' => 'D'
            ]);
            $response->assertStatus(201);
            $this->validateBillFormat($response);
            $response->assertJsonCount(1, 'pizzas');
            $this->validateBillData($response, 'D', 'P', 0, $pizzaBill->pizza_id, $pizzaBill->quantity);
            $line = "Querying Bill created";
            $data = $response->json();
            $response = $this->json('GET',"/api/bill/{$data['id']}");
            $response->assertStatus(200);
            $this->validateBillFormat($response);
            $response->assertJsonCount(1, 'pizzas');
            $this->validateBillData($response, 'D', 'P', 0, $pizzaBill->pizza_id, $pizzaBill->quantity);
            $line = "Selecting new Pizza";
            $pizzaBill = factory(PizzaBill::class)->make();
            $data = $response->json();
            $response = $this->json('PUT',"/api/bill/{$data['id']}",[
                'id' => $data['id'],
                'pizza_id' => $pizzaBill->pizza_id,
                'quantity' => $pizzaBill->quantity,
                'currency' => 'E'
            ]);
            $response->assertStatus(201);
            $this->validateBillFormat($response);
            $response->assertJsonCount(2, 'pizzas');
            $this->validateBillData($response, 'E', 'P', 1, $pizzaBill->pizza_id, $pizzaBill->quantity);
            $line = "Ending Bill";
            $bill = factory(Bill::class)->make();
            $response = $this->json('PUT',"/api/bill/{$data['id']}",[
                'id' => $data['id'],
                'location' => $bill->location,
                'currency' => $bill->currency,
                'value_del' => $bill->value_del,
                'status' => $bill->status,
                'mobile' => $bill->mobile,
                'additional_dat' => $bill->additional_dat
            ]);
            $response->assertStatus(201);
            $this->validateBillFormat($response);
            $response->assertJsonCount(2, 'pizzas');
            $this->validateBillEndData($response, $bill, null);
            $response = $this->json('PUT',"/api/bill/{$data['id']}",[
                'id' => $data['id'],
                'user_id' => $bill->user_id
            ]);
            $this->validateBillEndData($response, $bill, $bill->user_id);
        }catch (\Exception $e){
            throw new \Exception("Bills store without user was failed [{$line}] ".$e->getMessage().$response->getContent());
        }
    }

    private function validateBillFormatUser($response){
        try{
            $response->assertJsonStructure([
                'id', 'currency', 'value_del',
                'status', 'value', 'value_ord',
                'user_id', 'pizzas'=>[
                    '*'=>[
                        'id', 'bill_id', 'pizza_id',
                        'quantity', 'uni_value', 'pizza'=>[
                            'id', 'name', 'short_desc',
                            'description', 'value_curr_dol', 'value_curr_eur',
                            'status'
                        ]
                    ]
                ], 'user' => [
                    'id', 'name', 'email', 'type'
                ]
            ]);
        }catch (\Exception $e){
            throw new \Exception('Bills format is not correct: '.$e->getMessage());
        }
    }

    private function validateStoreWithUser(){
        $line = "";
        try{
            $line = "Creating Bill";
            $pizzaBill = factory(PizzaBill::class)->make();
            $response = $this->json('POST','/api/bill',[
                'pizza_id' => $pizzaBill->pizza_id,
                'quantity' => $pizzaBill->quantity,
                'user_id' => '1'
            ]);
            $response->assertStatus(201);
            $this->validateBillFormatUser($response);
            $response->assertJsonCount(1, 'pizzas');
            $this->validateBillData($response, 'E', 'P', 0, $pizzaBill->pizza_id, $pizzaBill->quantity);
            $line = "Ending Bill";
            $bill = factory(Bill::class)->make();
            $data = $response->json();
            $response = $this->json('PUT',"/api/bill/{$data['id']}",[
                'id' => $data['id'],
                'location' => $bill->location,
                'currency' => $bill->currency,
                'value_del' => $bill->value_del,
                'status' => $bill->status,
                'mobile' => $bill->mobile,
                'additional_dat' => $bill->additional_dat
            ]);
            $response->assertStatus(201);
            $this->validateBillFormat($response);
            $response->assertJsonCount(1, 'pizzas');
            $this->validateBillEndData($response, $bill, 1);
        }catch (\Exception $e){
            throw new \Exception("Bills store with user was failed [{$line}] ".$e->getMessage());
        }
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetBill()
    {
        $this->loadData();
        $this->validateIndex();
        $this->validateStoreWithoutUser();
        $this->validateStoreWithUser();
    }
}
