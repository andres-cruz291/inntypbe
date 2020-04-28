<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Foto;
use Faker\Generator as Faker;

$factory->define(Foto::class, function (Faker $faker) {
    return [
        'pizza_id' => $faker->numberBetween(1, 8),
        'path' => $faker->text(100)
    ];
});
