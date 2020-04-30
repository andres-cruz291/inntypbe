<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Foto;
use Faker\Generator as Faker;

$factory->define(Foto::class, function (Faker $faker) {
    return [
        'pizza_id' => $faker->numberBetween(1, 8),
        'path' => "storage\\uploads\\".$faker->randomElement(['pizza1', 'pizza2', 'pizza3', 'pizza4',
                'pizza5', 'pizza6', 'pizza7', 'pizza8', 'pizza9', 'pizza10', 'pizza11', 'pizza12',
                'pizza13', 'pizza14', 'pizza15', 'pizza16', 'pizza17', 'pizza18', 'pizza19', 'pizza20',
                'pizza21', 'pizza22', 'pizza23', 'pizza24', 'pizza25', 'pizza26', 'pizza27', 'pizza28',
                'pizza29', 'pizza30', 'pizza31', 'pizza32']).".jpg"
    ];
});
