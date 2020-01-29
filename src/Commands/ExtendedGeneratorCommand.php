<?php


namespace Quez\Xmake\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Arr;


class ExtendedGeneratorCommand extends GeneratorCommand {

    protected $parsedOptionFields = [];

    protected $fields = [];

    protected $type = "";

    protected $stubName = "";

    protected function getStubPath() {
        return strtolower($this->type);
    }

    protected function getStubDir()
    {
        $stubPath = $this->getStubPath();

        $userPath = base_path(config('xmake.paths.stubs') .'/'.$stubPath.'/'.$this->stubName);

        if (!file_exists($userPath)) {
            throw new \Error("Couldn't find stub file. Expected path: $userPath");
        }

        return $userPath;
    }

    protected function getStub()
    {
        return $this->getStubDir();
    }


    protected function includeFieldsConfigFile()
    {
        $path = base_path(config('xmake.paths.fields').'/fields.php');

        if (!file_exists($path)) {
            throw new \Error("Couldn't find fields file. You should check your config. Expected path: $path");
        }

        $this->fields = include $path;
    }

    protected function parseFieldsOption()
    {
        $array = [];
        foreach (explode(",", $this->option('fields')) as $item) {
            array_push($array, $item);
        }

        $this->parsedOptionFields = $array;
    }

    protected function getFieldsDataIfEmpty() {
        if (count($this->parsedOptionFields) === 0) {
            $this->parseFieldsOption();
        }
        if (count($this->fields) === 0) {
            $this->includeFieldsConfigFile();
        }
    }

    protected function getDefaultFieldsWhenElementNotFound(string $name) {
        return [
            'name' => $name,
            'database' => '',
            'validation' => '',
            'factory' => '',
            'filled' => false,
        ];
    }

    protected function getElementFromFields(string $name)
    {
        $this->getFieldsDataIfEmpty();

        $element = Arr::first($this->fields, function ($value, $key) use ($name) {
            return $key === $name;
        });

        if (!$element) {
            $element = $this->getDefaultFieldsWhenElementNotFound($name);
        } else {
            $element['filled'] = true;
        }

        return $element;
    }

    protected function prefix(string $text, int $tabs = 1, bool $lineBreak = true)
    {
        $prefix = "";

        for ($i = 0; $i < $tabs; $i++) {
            $prefix = "    $prefix";
        }

        $prefixed = "$prefix$text";

        if ($lineBreak) {
            $prefixed = "\r\n$prefixed";
        }

        return $prefixed;
    }
}