<?php

use Faker\Generator as Faker;

$factory->define(App\Task::class, function (Faker $faker) {
    return [
        'body' => $faker->sentence,
        'completed' => false,
        'project_id' => factory(\App\Project::class)
        // 'project_id' => function () {
        //     return factory(\App\Project::class)->create()->id;
        // }
    ];
});
