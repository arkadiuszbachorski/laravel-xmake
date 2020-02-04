# Laravel Xmake
Additional Laravel Artisan xmake command for faster resource creating and scaffolding. Created to speed up developing process and stop typing same things in various places.

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

- Scaffold your app quicker
- Create many related files with just one command: model, controller, request, resource, migration, seeder and factory
- Provide fields in one place, rest will be prepared or even filled for you
- Easily customize stubs for your needs

## Usage

Example:

```shell
php artisan xmake -i --fields=title,foo,bar --modelName=Foobar --model --request --controller --api
```

Result
  
_FoobarController.php_

```php
<?php

namespace App\Http\Controllers;

use App\Foobar;
use App\Http\Requests\FoobarRequest;

class FoobarController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
      $foobars = Foobar::all();

      return response()->json([]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
      return response()->json([]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  FoobarRequest  $request
   * @return Response
   */
  public function store(FoobarRequest $request)
  {
      Foobar::create($request->validated());

      return response()->json([]);
  }

  /**
   * Display the specified resource.
   *
   * @param  Foobar $foobar
   * @return \Illuminate\Http\Response
   */
  public function show(Foobar $foobar)
  {
      return response()->json([]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  Foobar $foobar
   * @return \Illuminate\Http\Response
   */
  public function edit(Foobar $foobar)
  {
      return response()->json([]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  FoobarRequest  $request
   * @param  Foobar $foobar
   * @return \Illuminate\Http\Response
   */
  public function update(FoobarRequest $request, Foobar $foobar)
  {
      $foobar->update($request->validated());

      return response()->json([]);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  Foobar $foobar
   * @return \Illuminate\Http\Response
   * @throws \Exception
   */
  public function destroy(Foobar $foobar)
  {
      $foobar->delete();

      return response()->json([]);
  }
}
```
  
_Foobar.php_

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Foobar extends Model
{
  protected $guarded = [];
}
```
  
_FoobarRequest.php_

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FoobarRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
      return false;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
      return [
          'title' => '',
          'foo' => '',
          'bar' => '',
      ];
  }
}
```

Pretty nice, huh? For more details and possibilities, see documentation.
  

## To-do list

- Guessing factory based on validation and migration field type
- Guessing migration field type based on validation

## Documentation

- [Config](#config)
- [Stubs](#stubs)
- [Fields](#fields)
- [Commands](#commands)
    - [xmake](#xmake)
    - [xmake:model](#xmakemodel)
    - [xmake:controller](#xmakecontroller)
    - [xmake:resource](#xmakeresource)
    - [xmake:migration](#xmakemigration)
    - [xmake:request](#xmakerequest)
    - [xmake:factory](#xmakefactory)
    - [xmake:seeder](#xmakeseeder)
    - [Summary](#summary)

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

However Xmake has a few self-filling and data processing mechanisms as you could see in config. If you provide database field there is probability of getting at least some of repetitive things filled in validation and factory.

### Commands

Every command has --help, so you can check available options faster than checking here. 

In examples below I assume default config and file _fields.php_ contains:

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

#### xmake

It's a command that will be used most often, because it gives powerful scaffolding ability. It can call every single command listed below. 

Xmake by default runs interactive shell experience. If you would like to create resources even faster - use --instant flag. Results are almost the same, but you can't name your files independently. Probably you wouldn't do it anyway, because auto-naming works great.

##### Available options

###### --instant -i 
Use this flag if you want don't want to use interactive shell.

###### --modelName
This parameter is required, almost every command uses it.

###### --fields
Get fields keys array, use comma as separator

###### --all
Create everything based on your config. By default - it's literally everything.

###### --api
Create API version of Controller and enable resource creating

###### --model
Create model with given modelName

###### --migration
Create migration with given fields prepared or filled

###### --factory
Create factory with given fields prepared of filled

###### --seeder
Create seeder that invokes factory

###### --request
Create request with given fields prepared or filled

###### --resource
Create resource with given fields prepared of filled

###### --controller
Create controller with various options - request, resource and api, based on your previous choices.


#### xmake:model
It simply creates model.

<details>
<summary>Example</summary>

```shell
php artisan xmake:model Foobar
```

Result:

_Foobar.php_
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Foobar extends Model
{
  protected $guarded = [];
}
```

</details>

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

###### --resource -x
It injects given resource to index, store, show, edit and update returns. It does nothing when no --api flag provided.

###### --request -r 
It injects given request to create and update methods. If the file doesn't exist - it can be created with fields you provided. 

<details>
<summary>Example</summary>

```shell
php artisan xmake:controller FoobarController --api --fields=foo,bar --model=Foobar
```

Result:

```php
<?php

namespace App\Http\Controllers;

use App\Foobar;
use Illuminate\Http\Request;

class FoobarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $foobars = Foobar::all();

        return response()->json([]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->json([]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'foo' => 'string|nullable',
            'bar' => '',
        ]);

        Foobar::create($data);

        return response()->json([]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Foobar $foobar
     * @return \Illuminate\Http\Response
     */
    public function show(Foobar $foobar)
    {
        return response()->json([]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Foobar $foobar
     * @return \Illuminate\Http\Response
     */
    public function edit(Foobar $foobar)
    {
        return response()->json([]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Foobar $foobar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Foobar $foobar)
    {
        $data = $request->validate([
            'foo' => 'string|nullable',
            'bar' => '',
        ]);


        $foobar->update($data);

        return response()->json([]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Foobar $foobar
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Foobar $foobar)
    {
        $foobar->delete();

        return response()->json([]);
    }
}
```

</details>

#### xmake:resource

It creates resource with given fields filled. It makes fields name camelCase by default, however it can be changed in config.

##### Available options

###### --fields

<details>
<summary>Example</summary>

```shell
php artisan xmake:resource FoobarResource --fields=foo,bar,not_camel_case_field
```

Result:

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FoobarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'foo' => $this->foo,
            'bar' => $this->bar,
            'notCamelCaseField' => $this->not_camel_case_field,
        ];
    }
}
```

</details>




#### xmake:migration

It creates migration with given fields prepared or filled.

##### Available options

###### --create
Table name

###### --fields

<details>
<summary>Example</summary>

```shell
php artisan xmake:migration create_foobar_table --create=foobar --fields=foo,bar
```

Result:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoobarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foobar', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('foo', 20)->default("test")->nullable();
            $table->('bar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('foobar');
    }
}
```

</details>


#### xmake:request

It creates request with given validation rules prepared or filled.

<details>
<summary>Example</summary>

```shell
php artisan xmake:request FoobarRequest --fields=foo,bar
```

Result:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FoobarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'foo' => 'string|nullable',
            'bar' => '',
        ];
    }
}
```
</details>


#### xmake:factory

It creates factory with given factory rules prepared or filled.

<details>
<summary>Example</summary>

```shell
php artisan xmake:factory FoobarFactory --fields=foo,bar
```

Result:

```php
<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Foobar;
use Faker\Generator as Faker;

$factory->define(Foobar::class, function (Faker $faker) {
    return [
        'foo' => $faker->sentence(2),
        'bar' => $faker->,
    ];
});
```
</details>

#### xmake:seeder

It creates seeder with given model.

##### Available options

###### --model
Model name.

###### --amount
Number of models to be created in seeder. Default value can be changed in config.

<details>
<summary>Example</summary>

```shell
php artisan xmake:seeder FoobarSeeder --model=foo,bar --amount=33
```

Result:

```php
<?php

use Illuminate\Database\Seeder;
use App\Foobar;

class FoobarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Foobar::class, 33);
    }
}
```
</details>


#### Summary

All the above files can be created with just one command:

```shell
php artisan xmake -i --modelName=Foobar --all --api --fields=foo,bar
```