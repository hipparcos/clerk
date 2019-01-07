<?php

use Faker\Generator as Faker;

$factory->define(App\Booking::class, function (Faker $faker) {
    return [
        'start' => new DateTime(),
        'end' => (new DateTime())->add(new DateInterval('PT1H')),
    ];
});
