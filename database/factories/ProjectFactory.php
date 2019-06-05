<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Project;
use App\Models\Category;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/
$factory->define(Project::class, function (Faker $faker) {
    $category = App\Models\Category::inRandomOrder()->first();

    return [
        'title'       => $faker->sentence,
        'category_id' => function () use ($category) {
            return optional($category)->id ?? factory(Category::class)->create()->id;
        },
        'description' => $faker->paragraph,
        'image'       => 'projects/'.$faker->image(storage_path('app/public/projects'), 640, 480, null, false),
        'visible'     => $faker->boolean($chanceOfGettingTrue = 80),
        'order'       => $faker->randomDigit,
        'status'      => $faker->randomElement([
            'unknown',
            'open',
            'scheduled',
            'in_development',
            'completed',
            'cancelled',
        ]),
    ];
});