<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    private function loadData(){
        try{
            $users = factory(User::class, 4)->create();
        }catch (\Exception $e){
            throw new \Exception('Users Data was not loaded: '.$e->getMessage());
        }
    }

    private function validateClientStore(){
        $line = "";
        try{
            $line = "Creating User Without Data";
            $user = factory(User::class)->make();
            $response = $this->json('POST', '/api/user', [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password
            ]);
            $response->assertStatus(501);

            $line = "Creating User Without Correct Passwords";
            $response = $this->json('POST', '/api/user', [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'password_confirm' => $user->password."#"
            ]);
            $response->assertStatus(501);

            $line = "Creating User";
            $response = $this->json('POST', '/api/user', [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'password_confirm' => $user->password
            ]);
            $response->assertStatus(201);
            $response->assertJsonStructure([
                'id', 'name', 'email', 'type'
            ], $response->json());
            if($response->json()['type']!='C'){
                throw new \Exception('User created is not a Client');
            }

            $line = "Searching User Created";
            $data = $response->json();
            $response = $this->json('GET', "/api/user/{$data['id']}");
            $response->assertStatus(200);
            $response->assertJsonStructure([
                'id', 'name', 'email', 'type'
            ], $response->json());

            $line = "Validating User Created: wrong email";
            $response = $this->json('POST', "/api/user/validate", [
                'email' => $user->email."x"
            ]);
            $response->assertStatus(201);
            $response->assertJsonStructure([
                'confirmed', 'error'
            ], $response->json());
            if($response->json()['confirmed']!== false){
                throw new \Exception('Error validating email existence');
            }

            $line = "Validating User Created: wrong password";
            $response = $this->json('POST', "/api/user/validate", [
                'email' => $user->email,
                'password' => $user->password."x"
            ]);
            $response->assertStatus(201);
            $response->assertJsonStructure([
                'confirmed', 'error'
            ], $response->json());
            if($response->json()['confirmed']!== false){
                throw new \Exception('Error validating password');
            }

            $line = "Validating User Created";
            $response = $this->json('POST', "/api/user/validate", [
                'email' => $user->email,
                'password' => $user->password
            ]);
            $response->assertStatus(201);
            $response->assertJsonStructure([
                'id', 'name', 'email', 'confirmed'
            ], $response->json());
            if($response->json()['confirmed']!== true){
                throw new \Exception('Error validating login');
            }
        }catch (\Exception $e){
            throw new \Exception("Users store as Client has failed [{$line}]: ".$e->getMessage());
        }
    }

    private function validateAdministratorStore(){
        $line = "";
        try{
            $user = factory(User::class)->make();
            $line = "Creating User";
            $response = $this->json('POST', '/api/user', [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'password_confirm' => $user->password,
                'type' => 'A'
            ]);
            $response->assertStatus(201);
            $response->assertJsonStructure([
                'id', 'name', 'email', 'type'
            ], $response->json());
            if($response->json()['type']!='A'){
                throw new \Exception('User created is not an Administrator');
            }

            $line = "Searching User Created";
            $data = $response->json();
            $response = $this->json('GET', "/api/user/{$data['id']}");
            $response->assertStatus(200);
            $response->assertJsonStructure([
                'id', 'name', 'email', 'type'
            ], $response->json());

            $line = "Validating User Created";
            $response = $this->json('POST', "/api/user/validate", [
                'email' => $user->email,
                'password' => $user->password
            ]);
            $response->assertStatus(201);
            $response->assertJsonStructure([
                'id', 'name', 'email', 'confirmed'
            ], $response->json());
            if($response->json()['confirmed']!== true){
                throw new \Exception('Error validating login');
            }
        }catch (\Exception $e){
            throw new \Exception("Users store as Administrator has failed [{$line}]: ".$e->getMessage());
        }
    }

    public function testUsers()
    {
        $this->loadData();
        $this->validateClientStore();
    }
}
