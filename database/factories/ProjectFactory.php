<?php

/** @var Factory $factory */

use App\Project;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(
    Project::class,
    function (Faker $faker) {
        return [
            'title'       => $faker->sentence,
            'description' => $faker->paragraphs(rand(2, 5), true),
            'is_started'  => $faker->boolean,
        ];
    }
);
