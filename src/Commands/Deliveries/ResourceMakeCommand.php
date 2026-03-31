<?php

namespace Morphling\ThreeD\Commands\Deliveries;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class ResourceMakeCommand extends BaseGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '3d:make-resource {name} {module} {--ver=v1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new API Resource in the UI layer with versioning';

    /**
     * The type definition for the generator command.
     *
     * @var string
     */
    protected $type = 'resource';

    /**
     * Build the class with versioned stub replacements.
     *
     * @param  string  $name  The name of the resource class.
     * @return string  The generated class content with version placeholder replaced.
     */
    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);
        $version = strtolower($this->option('ver')); // e.g., v1, v2

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

        // Example result: Modules\Blog\Delivery\Resources\V1
        return $namespace . '\\' . $version;
    }
}
