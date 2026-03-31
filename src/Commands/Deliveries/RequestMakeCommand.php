<?php

namespace Morphling\ThreeD\Commands\Deliveries;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class RequestMakeCommand extends BaseGeneratorCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = '3d:make-request {name} {module} {--ver=v1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Form Request in the UI layer with versioning';

    /**
     * The type of the generated class.
     *
     * @var string
     */
    protected $type = 'request';

    /**
     * Build the class with versioned stub replacements.
     *
     * @param  string  $name  The name of the request class.
     * @return string  The generated class content with version placeholder replaced.
     */
    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);
        $version = strtolower($this->option('ver'));

        return str_replace(
            ['{{ version }}', '{{version}}'],
            str($version)->title(),
            $stub
        );
    }

    /**
     * Get the default namespace for the class with versioning.
     *
     * @param  string  $rootNamespace  The root namespace (e.g., 'Modules').
     * @return string  The default namespace including version.
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        $namespace = parent::getDefaultNamespace($rootNamespace);
        $version = \Illuminate\Support\Str::studly($this->option('ver'));

        // e.g., Modules\Blog\Delivery\Requests\V1
        return $namespace . '\\' . $version;
    }
}
