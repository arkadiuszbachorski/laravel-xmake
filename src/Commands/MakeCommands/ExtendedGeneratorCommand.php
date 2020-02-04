<?php


namespace ArkadiuszBachorski\Xmake\Commands\MakeCommands;

use ArkadiuszBachorski\Xmake\Commands\Prefix;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Arr;


class ExtendedGeneratorCommand extends GeneratorCommand {
    use Prefix;

    protected $fields = null;

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

    protected function getFields()
    {
        if ($this->fields === null) {
            $this->fields = new Fields($this->option('fields'));
        }

        return $this->fields;
    }
}