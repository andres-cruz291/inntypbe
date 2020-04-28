<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\PizzaBill;
use Faker\Generator as Faker;

$factory->define(PizzaBill::class, function (Faker $faker) {
    return [
        'pizza_id' => $faker->numberBetween(1, 8),
        'bill_id' => $faker->numberBetween(1, 8),
        'quantity' => $faker->numberBetween(1, 15),
        'uni_value' => $faker->numberBetween(10, 100)
    ];
});
