<?php

namespace Morphling\ThreeD\Commands\Deliveries;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;
use Illuminate\Support\Str;

class ControllerMakeCommand extends BaseGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '3d:make-controller {name} {module} {--ver=v1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Controller (API/Web)';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'controller';

    /**
     * Resolve the appropriate stub file path for the controller being generated.
     *
     * Uses the 'api-controller' stub if the controller name contains 'api' (case-insensitive),
     * otherwise uses the default 'controller' stub.
     *
     * @return string The resolved stub file path.
     */
    protected function getStub(): string
    {
        $controllerName = $this->argument('name');

        if (Str::contains(strtolower($controllerName), 'api')) {
            return $this->resolveStubPath('api-controller');
        }

        return $this->resolveStubPath('controller');
    }

    /**
     * Build the controller class content by replacing relevant placeholders in the stub.
     *
     * Replaces the following placeholders in the stub:
     *   - '{{ version }}', '{{version}}': The --ver option in Title Case (e.g., 'V1')
     *   - '{{ resource }}', '{{resource}}': Resource/entity name in camelCase
     *   - '{{ Resource }}', '{{Resource}}': Resource/entity name in StudlyCaps
     *   - '{{ module }}', '{{module}}': The --module argument in Title Case
     *   - '{{ baseModule }}': The --module argument in StudlyCaps (used for namespaces)
     *
     * @param  string  $name  The fully-qualified class name being generated.
     * @return string The processed controller class content.
     */
    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);

        $version = str($this->option('ver'))->title();

        // Infer resource name by removing 'Controller' suffix if present.
        $classBase = class_basename($name);
        $resourceName = str_ends_with($classBase, 'Controller')
            ? substr($classBase, 0, -strlen('Controller'))
            : $classBase;
        $resourceStudly = Str::studly($resourceName);
        $resourceCamel = Str::camel($resourceName);

        // Get the module argument and prepare name variants.
        $moduleArg = $this->argument('module');
        $moduleTitle = str($moduleArg)->title();
        $moduleStudly = Str::studly($moduleArg);

        $placeholders = [
            '{{ version }}'    => $version,
            '{{version}}'      => $version,
            '{{ resource }}'   => $resourceCamel,
            '{{resource}}'     => $resourceCamel,
            '{{ Resource }}'   => $resourceStudly,
            '{{Resource}}'     => $resourceStudly,
            '{{ module }}'     => $moduleTitle,
            '{{module}}'       => $moduleTitle,
            '{{ baseModule }}' => $moduleStudly,
        ];

        return str_replace(
            array_keys($placeholders),
            array_values($placeholders),
            $stub
        );
    }
}
