# Laravel Xmake
Additional Laravel Artisan xmake command for faster resources creating. Created to speed up developing process and stop typing same things in various places.

## Table of contents

- [Getting started](#getting-started)
    - [Prerequisites](#prerequisites)
    - [Installation](#installation)
- [Features](#features)
- [Usage](#usage)
- [To-do list](#to-do-list)
- [Documentation](#documentation)

## Getting started

### Prerequisites

This package was developed for Laravel 5.8 and up. I haven't tested earlier versions yet.

### Installation

Require this package with composer for development only.

```shell
composer require arkadiuszbachorski/laravel-xmake --dev
```

Publish config

```shell
php artisan vendor:publish --tag=xmake-config
```

Change default resource paths if you would like to.

Publish resources

```shell
php artisan vendor:publish --tag=xmake-resources
```

## Features

- Create model with related controller, request, migration and factory with just one command
- Provide fields in one place, rest will be prepared or even filled for you
- Easily customize stubs for your needs

## Usage

Command:

```shell
php artisan xmake:controller FoobarController --model=Foobar --request=FoobarRequest --fields=title,foo,bar
```

Result in:

```php
$todo = "lorem ipsum";
```

```php
$todo = "lorem ipsum";
```

## To-do list

- "Resource" create command
- "Seed" create command
- Change method of fields data providing to array of objects
- Guessing factory based on validation and migration field type
- Guessing migration field type based on validation
- Creation wizard

## Documentation

- [Config](#config)
- [Stubs](#stubs)
- [Fields](#fields)
- [Commands](#commands)
    - [xmake:model](#xmakemodel)
    - [xmake:controller](#xmakecontroller)
    - [xmake:migration](#xmakemigration)
    - [xmake:request](#xmakerequest)
    - [xmake:factory](#xmakefactory)

### Config

_config/xmake.php_
```php
[
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
    ]
];
```

### Stubs

Stubs can be found in _/resources/xmake/stubs_ path by default. You will see a few text files there. All of them contain "Dummy..." content, that means it will be replaced by generated things. Everything what isn't prefixed with "Dummy..." can be customised by you.

### Fields

Fields file can be found in _/resources/xmake/fields.php_ path by default. It's a source of data about fields.

```php
[
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
```

You don't have to fill in all the data about each field. The only required one is the "name". If you enter unknown field to --fields option, it will be treated as the key is the name and the other parameters are empty.

This means you can completely ignore this file if you would like just list every field in created files.

### Commands

Every command has --help, so you can check available options. In examples below I assume _fields.php_ contains:

```php
[
    'foo' => [
        'name' => 'foo',
        'factory' => 'sentence(2)',
        'validation' => 'string|nullable',
        'database' => 'string(NAME, 20)->default("test")',
    ],
];
```

#### xmake:model

It simply creates model. However, it's the most complex command, because you can call every other with it. It's probably the one you'll use most often.

##### Available options

###### --fields
Option necessary for other commands.

###### --api
It's a flag that matters only if you create controller.

###### --factory -f
It calls xmake:factory with provided --fields and name based on model name.

###### --migration -m
It calls xmake:migration with provided --fields and name based on model name.

###### --request -r
It calls xmake:request with provided --fields and name based on model name.

###### --controller -c
It calls xmake:controller with provided --fields, --api flag, name based on model name, --model based on model and --request if you provided it.

###### -all -a
It's equivalent for -f -m -r -c.

#### xmake:controller

It creates controller with:
- basic CRUD operations for your model
- Blade views or REST API
- Validation inside or injected

##### Available options

###### --model -m
It's required. You must provide your models name.

###### --api
It's a flag that changes responses from Blade views to REST API.

###### --fields
You can provide fields for validation there.

###### --request -r 
It injects given request to create and update methods. If the file doesn't exist - it can be created with fields you provided. 

I.e.

```shell
php artisan xmake:controller FoobarController --api --request=FoobarRequest
```

Result:

```php
$todo = "lorem ipsum";
```

#### xmake:migration

It creates migration with given fields prepared or filled.

```shell
php artisan xmake:migration create_foobar_table --create=foobar --fields=foo,bar
```

Result:

```php
$todo = "lorem ipsum";
```


#### xmake:request

It creates request with given validation rules prepared or filled.

```shell
php artisan xmake:request FoobarRequest --fields=foo,bar
```

Result:

```php
$todo = "lorem ipsum";
```

#### xmake:factory

It creates factory with given factory rules prepared or filled.

```shell
php artisan xmake:factory FoobarFactory --fields=foo,bar
```

Result:

```php
$todo = "lorem ipsum";
```