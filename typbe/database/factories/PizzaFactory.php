<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Pizza;
use Faker\Generator as Faker;

$factory->define(Pizza::class, function (Faker $faker) {
    return [
        'name' => 'Pizza '.$faker->text(20),
        'short_desc' => $faker->text(100),
        'description' => $faker->paragraphs(2, true),
        'value_curr_dol' => $faker->randomFloat(2, 3, 30),
        'value_curr_eur' => $faker->randomFloat(2, 3, 30),
        'status' => $faker->randomElement(['A', 'U'])
    ];
});
