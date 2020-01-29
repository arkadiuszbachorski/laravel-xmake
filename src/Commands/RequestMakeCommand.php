<?php

namespace ArkadiuszBachorski\Xmake\Commands;

use Illuminate\Support\Arr;
use Symfony\Component\Console\Input\InputOption;

class RequestMakeCommand extends ExtendedGeneratorCommand
{
    use BuildValidation;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'xmake:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new request';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';

    protected $stubName = "request.stub";


    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $replace = [];

        $replace = $this->buildFieldsReplacements($replace);

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }


    protected function buildFieldsReplacements(array $replace)
    {
        if ($this->option('fields')) {
            $this->getFieldsDataIfEmpty();
            $validation = $this->buildValidation();
        } else {
            $validation = $this->prefix("//", 2, false);
        }

        return array_merge($replace, [
            'DummyValidation' => $validation,
        ]);

    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace(
            ['\\', '/'], '', $this->argument('name')
        );

        return $this->laravel->basePath()."/app/Http/Requests/{$name}.php";
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['fields', null, InputOption::VALUE_OPTIONAL, 'Get fields array, use comma as separator'],
        ];
    }
}
