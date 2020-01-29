<?php


namespace Quez\Xmake\Commands;


trait BuildValidation
{
    protected function buildValidation()
    {
        $validation = '';
        foreach ($this->parsedOptionFields as $field) {
            $item = $this->getElementFromFields($field);
            if (!preg_match('/^\[/m',$item['validation'])) {
                $item['validation'] = "'{$item['validation']}'";
            }
            $validation .= $this->prefix("'{$field}' => {$item['validation']},", 3, true);
        }

        return $validation;
    }
}