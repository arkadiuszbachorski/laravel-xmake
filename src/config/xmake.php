<?php

return [
    'paths' => [
        // Path where stubs are expected to exist. This path affects vendors publishing too.
        'stubs' => '/resources/xmake/stubs',
        // Path where fields.php is expected to exist. This path affects vendors publishing too.
        'fields' => '/resources/xmake',
    ],
    'database' => [
        // Flag that indicates whether ->nullable() should be automatically added if provided in validation
        'addNullableIfAppearsInValidation' => true,
    ],
    'controller' => [
        // You can change PHPDoc methods captions there
        'docs' => [
            'index' => 'Display a listing of the resource.',
            'create' => 'Show the form for creating a new resource.',
            'store' => 'Store a newly created resource in storage.',
            'show' => 'Display the specified resource.',
            'edit' => 'Show the form for editing the specified resource.',
            'update' => 'Update the specified resource in storage.',
            'destroy' => 'Remove the specified resource from storage.',
        ],
        // You can change CRUD methods names there
        'methods' => [
            'index' => 'index',
            'create' => 'create',
            'store' => 'store',
            'show' => 'show',
            'edit' => 'edit',
            'update' => 'update',
            'destroy' => 'destroy',
        ],
    ],
    'seeder' => [
        // Default amount used in seeders if not provided by --amount option
        'defaultAmount' => 50,
    ],
    'resource' => [
        // Flag that indicates whether resource fields should be parsed to camelCase
        'camelizeFields' => true,
    ],
    'validation' => [
        // It enables parsing pipe syntax (i.e. "string|nullable") to array
        'parseArray' => true,
        // It enables guessing validation based on database field. I.e. string('foobar') parses to 'string' validation.
        'guessBasedOnDatabase' => true,
    ],
    // You can change what will be created if you select "create everything"/"all" option
    'createEverything' => [
        'model' => true,
        'migration' => true,
        'factory' => true,
        'seeder' => true,
        'request' => true,
        'resource' => true,
        'controller' => true,
    ],
];