<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Bill;
use Faker\Generator as Faker;

$factory->define(Bill::class, function (Faker $faker) {
    return [
        'user_id' => $faker->numberBetween(1, 8),
        'currency' => $faker->randomElement(['E', 'D']),
        'value_del' => $faker->numberBetween(10, 50),
        'status' => $faker->randomElement(['P', 'G', 'C']),
        'location' => $faker->address,
        'mobile' => $faker->text(30),
        'additional_dat' => $faker->text(100)
    ];
});
