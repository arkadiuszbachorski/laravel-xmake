<?php

return [
    'paths' => [
        // Path where stubs are expected to exist. This path affects vendors publishing too.
        'stubs' => '/resources/xmake/stubs',
        // Path where fields.php is expected to exist. This path affects vendors publishing too.
        'fields' => '/resources/xmake',
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
    ]
];