<?php

namespace Tests\Feature;

use App\Foto;
use App\Pizza;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    private function validateDeletingFotos($data, $quantity){
        foreach ($data['fotos'] as $foto) {
            $response = $this->json('DELETE',"/api/foto/{$foto['id']}",[
                'id' => $foto['id']
            ]);
            $response->assertStatus(201);
            $response->assertJsonStructure([
                'id', 'name', 'short_desc',
                'description', 'value_curr_dol', 'value_curr_eur',
                'status']);
            $quantity--;
            $response->assertJsonCount($quantity, 'fotos');
            if($quantity > 0){
                $response->assertJsonStructure([
                    'fotos'=>['*'=>[
                        'id', 'pizza_id', 'path'
                    ]]]);
            }
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
            $path = str_replace('storage/', '', $response->json()['fotos'][0]['path']);
            Storage::disk('public')->assertExists($path);

            $line = "Querying Pizza created";
            $data = $response->json();
            $response = $this->json('GET',"/api/pizza/{$data['id']}");
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
                'name' => $data['name'],
                'description' => $data['description'],
                'value_curr_dol' => $data['value_curr_dol'],
                'value_curr_eur' => $data['value_curr_eur'],
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
            $path = str_replace('storage/', '', $response->json()['fotos'][2]['path']);
            Storage::disk('public')->assertExists($path);

            $line = "Getting photo created";
            $data = $response->json();
            $response = $this->json('GET',"/api/foto/{$data['id']}");
            $response->assertStatus(200);
            $response->assertJsonStructure([
                'current_page', 'first_page_url', 'from',
                'last_page', 'last_page_url', 'next_page_url',
                'path', 'per_page', 'prev_page_url',
                'to', 'total','data'=>['*'=>[
                    'id', 'pizza_id', 'path'
                ]]]);
            $response->assertJsonCount(1, 'data');

            $line = "Modifying Pizza";
            $pizza = factory(Pizza::class)->make();
            $response = $this->json('PUT',"/api/pizza/{$data['id']}",[
                'id' => $data['id'],
                'name' => $pizza->name,
                'short_desc' => $pizza->short_desc,
                'description' => $data['description'],
                'value_curr_dol' => $data['value_curr_dol'],
                'value_curr_eur' => $data['value_curr_eur'],
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
                ($data['status']!='U')){
                throw new \Exception("The Pizza was not updated.");
            }

            $line = "Deleting Pizzas photo";
            $this->validateDeletingFotos($data, 3);
        }catch (\Exception $e){
            throw new \Exception("Pizza store with fotos [{$line}] ".$e->getMessage());
        }
    }

    private function validateStoreWithoutFotos(){
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
                'status'=>$pizza->status
            ]);
            $response->assertStatus(201);
            $response->assertJsonStructure([
                'id', 'name', 'short_desc',
                'description', 'value_curr_dol', 'value_curr_eur',
                'status']);
            $response->assertJsonCount(0, 'fotos');

            $line = "Querying Pizza created";
            $data = $response->json();
            $response = $this->json('GET',"/api/pizza/{$data['id']}");
            $response->assertStatus(200);
            $response->assertJsonStructure([
                'id', 'name', 'short_desc',
                'description', 'value_curr_dol', 'value_curr_eur',
                'status']);
            $response->assertJsonCount(0, 'fotos');

            $line = "Adding new Foto";
            $data = $response->json();
            $response = $this->json('PUT',"/api/pizza/{$data['id']}",[
                'id' => $data['id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'value_curr_dol' => $data['value_curr_dol'],
                'value_curr_eur' => $data['value_curr_eur'],
                'path:01'=>UploadedFile::fake()->image('random01.jpg'),
            ]);
            $response->assertStatus(201);
            $response->assertJsonStructure([
                'id', 'name', 'short_desc',
                'description', 'value_curr_dol', 'value_curr_eur',
                'status', 'fotos'=>['*'=>[
                    'id', 'pizza_id', 'path'
                ]]]);
            $response->assertJsonCount(1, 'fotos');
            $path = str_replace('storage/', '', $response->json()['fotos'][0]['path']);
            Storage::disk('public')->assertExists($path);

            $line = "Modifying Pizza";
            $pizza = factory(Pizza::class)->make();
            $response = $this->json('PUT',"/api/pizza/{$data['id']}",[
                'id' => $data['id'],
                'name' => $pizza->name,
                'short_desc' => $pizza->short_desc,
                'description' => $data['description'],
                'value_curr_dol' => $data['value_curr_dol'],
                'value_curr_eur' => $data['value_curr_eur']
            ]);
            $response->assertStatus(201);
            $response->assertJsonStructure([
                'id', 'name', 'short_desc',
                'description', 'value_curr_dol', 'value_curr_eur',
                'status', 'fotos'=>['*'=>[
                    'id', 'pizza_id', 'path'
                ]]]);
            $response->assertJsonCount(1, 'fotos');
            $data = $response->json();
            if(($data['name']!=$pizza->name)||($data['short_desc']!=$pizza->short_desc)){
                throw new \Exception("The Pizza was not updated.");
            }

            $line = "Deleting Pizzas photo";
            $this->validateDeletingFotos($data, 1);
        }catch (\Exception $e){
            throw new \Exception("Pizza store without fotos [{$line}] ".$e->getMessage().$response->getContent());
        }
    }

    public function testGetPizza()
    {
        $this->loadData();
        $this->validateIndex();
        $this->validateStoreWithFotos();
        $this->validateStoreWithoutFotos();
    }
}
