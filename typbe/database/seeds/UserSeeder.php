<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([[
            'name' => 'Administrator',
            'email' => 'administrator@email.com',
            'password' => 'Administrator',
            'type' => 'A',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ], [
            'name' => 'First Client',
            'email' => 'client1@email.com',
            'password' => 'Client',
            'type' => 'C',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ], [
            'name' => 'Second Client',
            'email' => 'client2@email.com',
            'password' => 'Client',
            'type' => 'C',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]]);
    }
}
