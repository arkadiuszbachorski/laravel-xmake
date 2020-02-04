<?php


namespace ArkadiuszBachorski\Xmake\Commands;


trait Prefix
{
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