<?php

namespace Morphling\ThreeD\Commands;

use Illuminate\Console\GeneratorCommand;
use Morphling\ThreeD\Commands\Concerns\HasModuleVars;
use Morphling\ThreeD\Commands\Concerns\InteractsWithStubs;
use Morphling\ThreeD\Commands\MakeModule;
use Illuminate\Support\Str;

abstract class BaseGeneratorCommand extends GeneratorCommand
{
    use HasModuleVars, InteractsWithStubs;

    /**
     * The type of object to generate (e.g., 'Entity', 'Mapper', 'UseCase').
     * Should be set by the child class.
     *
     * @var string
     */
    protected $type = '';

    /**
     * Resolve the absolute path to the stub template file.
     *
     * @return string The absolute path to the stub file.
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath(Str::kebab($this->type));
    }

    /**
     * Build the fully qualified class name including the module namespace.
     *
     * @param  string $name The class name argument.
     * @return string The fully qualified class name within the module namespace.
     */
    protected function qualifyClass($name): string
    {
        $cleanName = ltrim($name, '\\/');
        $module = $this->argument('module');

        return $this->getLayerNamespace($module, Str::snake($this->type)) . '\\' . $cleanName;
    }

    /**
     * Get the file path where the generated class should be written.
     *
     * @param  string $name The fully qualified class name.
     * @return string The file path for the generated class.
     */
    protected function getPath($name): string
    {
        $baseNamespace = config('3d.base_namespace', 'Modules') . '\\';
        $nameWithoutBase = Str::replaceFirst($baseNamespace, '', $name);
        $basePath = config('3d.base_path', base_path('modules'));

        return $basePath . '/' . str_replace('\\', '/', $nameWithoutBase) . '.php';
    }

    /**
     * Get the details of the module using a MakeModule instance.
     *
     * @return array The processed module information as provided by MakeModule.
     */
    protected function getModuleDetails(): array
    {
        $moduleName = $this->argument('module');
        return (new MakeModule())->getModuleInfo($moduleName);
    }

    /**
     * Build the class contents by replacing module-related placeholders in the stub.
     *
     * @param  string $name The fully qualified class name.
     * @return string The built class content with module variables replaced.
     */
    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);

        // Call getModuleInfo to retrieve proper module details
        $moduleDetails = $this->getModuleInfo($this->argument('module'));

        $replacements = [
            '{{ module }}'              => $moduleDetails['name'],
            '{{ module_snake }}'        => $moduleDetails['name_snake'],
            '{{ module_snake_plural }}' => $moduleDetails['name_snake_plural'],
            '{{ root_namespace }}'      => $moduleDetails['root_namespace'],
            '{{ base_namespace }}'      => $moduleDetails['base_namespace'],
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );
    }

    /**
     * Determine the default namespace for the generated class based on module and type configuration.
     *
     * @param  string $rootNamespace The application's root namespace.
     * @return string The resolved default namespace for the generated class.
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        $details = $this->getModuleInfo($this->argument('module'));

        // SINKRONISASI: Ubah dash (-) jadi underscore (_) agar match dengan config
        $configKey = str_replace('-', '_', $this->type);

        $subNamespace = config("3d.namespaces.{$configKey}");

        if (!$subNamespace) {
            // Fallback: Studly Plural (contoh: use_case -> UseCases)
            $subNamespace = Str::studly(Str::plural($configKey));
        }

        $subNamespace = str_replace(['/', ' '], '\\', trim($subNamespace, '\\/ '));

        return $details['root_namespace'] . '\\' . $subNamespace;
    }
}
