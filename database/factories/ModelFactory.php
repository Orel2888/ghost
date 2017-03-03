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

    $faker = Faker\Factory::create('ru_RU');

    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Client::class, function (Faker\Generator $faker) {

    $faker = Faker\Factory::create('ru_RU');

    $username = str_replace('.', '_', $faker->userName);

    return [
        'name'          => $faker->name,
        'tg_chatid'     => $faker->randomNumber(),
        'tg_username'   => $username,
        'rating'        => $faker->randomFloat(4),
        'comment'       => $username
    ];
});

$factory->define(App\City::class, function (Faker\Generator $faker) {

    $faker = Faker\Factory::create('ru_RU');

    return [
        'name'  => $faker->city
    ];
});

$factory->define(App\Goods::class, function (Faker\Generator $faker) {

    $faker = Faker\Factory::create('ru_RU');

    return [
        'name'      => $faker->word,
    ];
});

$factory->define(App\Miner::class, function (Faker\Generator $faker) {

    $faker = Faker\Factory::create('ru_RU');

    return [
        'name'  => $faker->name,
        'ante'  => 150
    ];
});

$factory->define(App\QiwiTransaction::class, function (Faker\Generator $faker) {

    $faker = Faker\Factory::create('ru_RU');

    return [
        'qiwi_id'   => $faker->randomNumber(),
        'purse'     => '79111111111',
        'provider'  => $faker->company,
        'qiwi_date' => $faker->date('d-m-Y H:i:s'),
        'type'      => 1
    ];
});