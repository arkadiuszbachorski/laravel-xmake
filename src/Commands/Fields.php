<?php

namespace ArkadiuszBachorski\Xmake\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Fields {
    use Prefix;

    protected $fields = [];

    public function __construct($optionFields)
    {
        $this->fields = $this->setFields($optionFields);
    }

    protected function includeFieldsData()
    {
        $path = base_path(config('xmake.paths.fields').'/fields.php');

        if (!file_exists($path)) {
            throw new \Error("Couldn't find fields file. You should check your config. Expected path: $path");
        }

        return include $path;
    }

    protected function setFields($optionFields)
    {
        $fieldsData = $this->includeFieldsData();
        $parsedFieldsOption = explode(",", $optionFields);
        $fields = [];

        foreach ($parsedFieldsOption as $alias) {
            $fieldData = Arr::first($fieldsData, function ($value, $key) use ($alias) {
                return $key === $alias;
            }, []);

            $field = new Field($alias, $fieldData);
            array_push($fields, $field);
        }

        return $fields;
    }

    protected function loopThroughFields($callback)
    {
        $data = '';

        foreach ($this->fields as $field) {
            $data = $callback($field, $data);
        }

        return $data;
    }

    public function buildValidation($tabSize = 3)
    {
        return $this->loopThroughFields(function(Field $field, $validation) use ($tabSize) {
            return $validation.$this->prefix("'{$field->name}' => {$field->validation},", $tabSize, true);
        });
    }

    public function buildFactory()
    {
        return $this->loopThroughFields(function(Field $field, $factory) {
            return $factory.$this->prefix("'{$field->name}' => \$faker->{$field->factory},", 2, true);
        });
    }

    public function buildMigration()
    {
        return $this->loopThroughFields(function(Field $field, $migration) {
            if ($field->database) {
                $row = $field->database;
            } else {
                $row = "('$field->name')";
            }
            return $migration.$this->prefix("\$table->$row;", 3, true);
        });
    }

    public function buildRequest()
    {
        return $this->loopThroughFields(function(Field $field, $request) {
            $name = config('xmake.resource.camelizeFields') ? Str::camel($field->name) : $field->name;
            return $request.$this->prefix("'$name' => \$this->{$field->name};", 3, true);
        });
    }
}