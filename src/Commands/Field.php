<?php


namespace ArkadiuszBachorski\Xmake\Commands;


use Illuminate\Support\Str;

class Field
{
    public $alias;

    public $name;

    public $factory;

    public $validation;

    public $database;

    public $filled;

    public function __construct($alias, array $data = [])
    {
        $this->alias = $alias;
        $this->name = $data['name'] ?? $alias;
        $this->factory = $data['factory'] ?? '';
        $this->validation = $data['validation'] ?? '';
        $this->database = $data['database'] ?? '';
        $this->filled = count($data) !== 0;

        $this->prepareFields();
    }

    protected function prepareFields()
    {
        $this->replaceDatabaseName();

        $this->parseValidationToArray();
        $this->guessValidationBasedOnDatabase();
        $this->addNullableToDatabaseIfAppearsInValidation();
    }

    protected function guessValidationBasedOnDatabase()
    {
        if (!config('xmake.validation.guessBasedOnDatabase')) return;

        $rules = [
            [
                'database' => 'string',
                'validation' => 'string',
            ],
            [
                'database' => 'date',
                'validation' => 'date',
            ],
            [
                'database' => 'boolean',
                'validation' => 'boolean',
            ],
            [
                'database' => 'integer',
                'validation' => 'integer',
            ],
            [
                'database' => 'Integer',
                'validation' => 'integer',
            ],
            [
                'database' => 'ipAddress',
                'validation' => 'ip',
            ],
            [
                'database' => 'json',
                'validation' => 'json',
            ],
            [
                'database' => 'url',
                'validation' => 'url',
            ],
            [
                'database' => 'uuid',
                'validation' => 'uuid',
            ],
        ];

        foreach ($rules as $rule) {
            if (Str::contains($this->database, $rule['database']) && !Str::contains($this->validation, $rule['validation'])) {
                $this->appendToValidation($rule['validation']);
            }
        }
    }

    protected function appendToValidation(string $append)
    {
        $validation = $this->validation;
        if ($this->checkIfValidationIsArray()) {
            $validation = Str::replaceLast("]", "", $validation);
            if ($validation !== "[" && !Str::endsWith($validation, ',')) {
                $validation .= ', ';
            }
            $validation .= "'$append']";
        } else {
            $validation = Str::replaceLast("'", "", $validation);
            if ($validation !== "'" && $validation !== "") {
                $validation .= '|';
            }
            $validation .= $append."'";
        }
        $this->validation = $validation;
    }

    protected function checkIfValidationIsArray()
    {
        return Str::startsWith($this->validation, '[');
    }

    protected function addNullableToDatabaseIfAppearsInValidation()
    {
        if (!config('xmake.database.addNullableIfAppearsInValidation')) return;

        if (Str::contains($this->validation, 'nullable') && !Str::contains($this->database, 'nullable()')) {
            $this->database.='->nullable()';
        }
    }

    protected function replaceDatabaseName()
    {
        $this->database = str_replace('NAME', "'{$this->name}'", $this->database);
    }

    protected function parseValidationToArray()
    {
        $validation = $this->validation;
        if (!$this->checkIfValidationIsArray()) {
            if (config('xmake.validation.parseArray')) {
                $validationFields = $validation !== "" ? explode("|", $validation) : [];
                $lastKey = array_key_last($validationFields);
                $newValidation = "[";
                foreach ($validationFields as $key => $validationField) {
                    $newValidation .= "'$validationField'";
                    if ($key !== $lastKey) {
                        $newValidation .= ', ';
                    }
                }
                $newValidation .= "]";
                $validation = $newValidation;
            } else {
                $validation = "'$validation'";
            }
        }
        $this->validation = $validation;
    }

    public function arraySyntax($key, $value)
    {
        return "'$key' => $value";
    }
}