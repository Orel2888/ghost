<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Client::class, function (Faker\Generator $faker) {

    $username = str_random(8);

    return [
        'name'          => $faker->name,
        'tg_chatid'     => $faker->randomNumber(),
        'tg_username'   => $username,
        'rating'        => $faker->randomFloat(4),
        'comment'       => $username
    ];
});

$factory->define(App\City::class, function (Faker\Generator $faker) {
    return [
        'name'  => $faker->city
    ];
});

$factory->define(App\Goods::class, function (Faker\Generator $faker) {
    return [
        'name'      => $faker->name,
    ];
});

$factory->define(App\Miner::class, function (Faker\Generator $faker) {
    return [
        'name'  => $faker->name,
        'ante'  => 150
    ];
});