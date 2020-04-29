<?php

namespace Tests\Feature;

use App\Foto;
use App\Pizza;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PizzaTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    private function loadData(){
        try{
            $fotos = factory(Foto::class, 32)->create();
            $pizzas = factory(Pizza::class, 8)->create();
        }catch (\Exception $e){
            throw new \Exception('Pizza Data was not loaded: '.$e->getMessage());
        }
    }

    private function validateIndex(){
        try{
            $response = $this->json('GET', '/api/pizza');
            $response->assertStatus(200);
            $response->assertJsonStructure([
                'id', 'name', 'short_desc',
                'description', 'value_curr_dol', 'value_curr_eur',
                'status', 'fotos'=>['*'=>[
                    'id', 'pizza_id', 'path'
                ]]], $response->json()[0]);
        }catch (\Exception $e){
            throw new \Exception('Pizzas index was failed : '.$e->getMessage());
        }
    }

    private function validateStoreWithFotos(){
        $line = "";
        try{
            $line = "Creating Pizza";
            $pizza = factory(Pizza::class)->make();
            $response = $this->json('POST','/api/pizza',[
                'name' => $pizza->name,
                'description' => $pizza->description,
                'short_desc'=>$pizza->short_desc,
                'value_curr_dol'=>$pizza->value_curr_dol,
                'value_curr_eur'=>$pizza->value_curr_eur,
                'status'=>$pizza->status,
                'path:01'=>UploadedFile::fake()->image('random01.jpg'),
                'path:02'=>UploadedFile::fake()->image('random02.jpg')
            ]);
            $response->assertStatus(201);
            $response->assertJsonStructure([
                'id', 'name', 'short_desc',
                'description', 'value_curr_dol', 'value_curr_eur',
                'status', 'fotos'=>['*'=>[
                    'id', 'pizza_id', 'path'
                ]]]);
            $response->assertJsonCount(2, 'fotos');
            $path = $response->json()['fotos'][0]['path'];
            Storage::disk('public')->assertExists($path);

            $line = "Querying Pizza created";
            $data = $response->json();
            $response = $this->json('GET',"/api/pizza/{$data['id']}",);
            $response->assertStatus(200);
            $response->assertJsonStructure([
                'id', 'name', 'short_desc',
                'description', 'value_curr_dol', 'value_curr_eur',
                'status', 'fotos'=>['*'=>[
                    'id', 'pizza_id', 'path'
                ]]]);
            $response->assertJsonCount(2, 'fotos');

            $line = "Adding new Foto";
            $data = $response->json();
            $response = $this->json('PUT',"/api/pizza/{$data['id']}",[
                'id' => $data['id'],
                'path:01'=>UploadedFile::fake()->image('random03.jpg'),
            ]);
            $response->assertStatus(201);
            $response->assertJsonStructure([
                'id', 'name', 'short_desc',
                'description', 'value_curr_dol', 'value_curr_eur',
                'status', 'fotos'=>['*'=>[
                    'id', 'pizza_id', 'path'
                ]]]);
            $response->assertJsonCount(3, 'fotos');
            $path = $response->json()['fotos'][0]['path'];
            Storage::disk('public')->assertExists($path);

            $line = "Modifying Pizza";
            $pizza = factory(Pizza::class)->make();
            $response = $this->json('PUT',"/api/bill/{$data['id']}",[
                'id' => $data['id'],
                'name' => $pizza->name,
                'short_desc' => $pizza->short_desc,
                'status' => 'U'
            ]);
            $response->assertStatus(201);
            $response->assertJsonStructure([
                'id', 'name', 'short_desc',
                'description', 'value_curr_dol', 'value_curr_eur',
                'status', 'fotos'=>['*'=>[
                    'id', 'pizza_id', 'path'
                ]]]);
            $response->assertJsonCount(3, 'fotos');
            $data = $response->json();
            if(($data['name']!=$pizza->name)||($data['short_desc']!=$pizza->short_desc)||
                ($data['status']!=$pizza->status)){
                throw new \Exception('The Pizza was not updated');
            }
        }catch (\Exception $e){
            throw new \Exception("Pizza store with fotos [{$line}] ".$e->getMessage().$response->getContent());
        }
    }


    public function testGetPizza()
    {
        $this->loadData();
        $this->validateIndex();
        $this->validateStoreWithFotos();
    }
}
