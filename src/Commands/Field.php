<?php


namespace ArkadiuszBachorski\Xmake\Commands;


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
        $this->parseValidationToArray();
        $this->replaceDatabaseName();
        $this->addNullableToDatabaseIfAppearsInValidation();
    }

    protected function addNullableToDatabaseIfAppearsInValidation()
    {
        if (strpos($this->validation, 'nullable' !== false) && strpos($this->database, 'nullable()') === "false") {
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
        if ($validation) {
            if ($this->checkIfValidationIsArray($validation)) {
                $validation = "'$validation'";
            }
            $this->validation = $validation;
        }
    }

    protected function checkIfValidationIsArray($validation)
    {
        return preg_match('/^\[/m', $validation);
    }

    public function arraySyntax($key, $value)
    {
        return "'$key' => $value";
    }
}