<?php

return [
    // key used in  --fields option
    'title20ch' => [
        /*
            Field name used in Eloquent, database migration.
            I.e $model->title
        */
        'name' => 'title',
        /*
            Used in factory after $faker->
            I.e. 'title' => $faker->sentence(2)
        */
        'factory' => 'sentence(2)',
        'validation' => 'string|nullable',
        /*
            Migration, NAME will by replaced with actual name automatically
            I.e. string('title', 20)->default('test'),
        */
        'database' => 'string(NAME, 20)->default("test")',
    ],
];