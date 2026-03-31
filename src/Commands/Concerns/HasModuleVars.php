<?php

namespace Morphling\ThreeD\Commands\Concerns;

use Illuminate\Support\Str;

trait HasModuleVars
{
    /**
     * Mapping of object types to module folder structure.
     * Mirrors the 'namespaces' mapping in the config, but converted to folder paths.
     */
    protected static array $typeToFolder = [
        // Delivery Layer
        'controller'    => 'Delivery/Controllers',
        'request'       => 'Delivery/Requests',
        'resource'      => 'Delivery/Resources',
        'route'         => 'Delivery/Routes',
        'view'          => 'Delivery/Views',

        // Domain Layer
        'entity'        => 'Domain/Entities',
        'value_object'  => 'Domain/ValueObjects',
        'service'       => 'Domain/Services',
        'interface'     => 'Domain/Interfaces',
        'enum'          => 'Domain/Enums',

        // Application Layer
        'use_case'      => 'Application/UseCases',
        'dto'           => 'Application/DTOs',

        // Infrastructure Layer
        'model'         => 'Infrastructure/Models',
        'repository'    => 'Infrastructure/Repositories',
        'mapper'        => 'Infrastructure/Mappers',
        'event'         => 'Infrastructure/Events',
        'listener'      => 'Infrastructure/Listeners',
        'job'           => 'Infrastructure/Jobs',
        'notification'  => 'Infrastructure/Notifications',
        'command'       => 'Infrastructure/Commands',
        'external'      => 'Infrastructure/External',
        'observer'      => 'Infrastructure/Observers',
        'provider'      => 'Infrastructure/Providers',
        'migration'     => 'Infrastructure/Database/Migrations',
        'factory'       => 'Infrastructure/Database/Factories',
        'seeder'        => 'Infrastructure/Database/Seeders',
    ];

    /**
     * Retrieve complete module information based on user input.
     *
     * @param  string  $moduleName  The name of the module provided by the user
     * @return array  An array containing various forms of the module name, namespace, and base path
     */
    protected function getModuleInfo(string $moduleName): array
    {
        $module = Str::studly($moduleName);
        $basePath = rtrim(config('3d.base_path', base_path('modules')), '/');
        $baseNamespace = trim(config('3d.base_namespace', 'Modules'), '\\');

        return [
            'name'                => $module,
            'name_snake'          => Str::snake($module),
            'name_snake_plural'   => Str::plural(Str::snake($module)),
            'name_kebab'          => Str::kebab($module),
            'base_namespace'      => $baseNamespace, // Namespace utama (Modules)
            'root_namespace'      => "{$baseNamespace}\\{$module}", // Namespace Modul (Modules\Order)
            'base_path'           => "{$basePath}/{$module}", // Path Modul (modules/Order)
        ];
    }

    /**
     * Get the specific namespace for a certain object type (e.g., 'use_case', 'entity').
     *
     * @param  string  $module  The module name
     * @param  string  $type    The object type for namespace retrieval
     * @return string  The fully qualified namespace for the requested type
     */
    protected function getLayerNamespace(string $module, string $type): string
    {
        $info = $this->getModuleInfo($module);

        // Prefer config mapping, fallback to internal mapping, else Plural Studly.
        $subNamespace = config("3d.namespaces.{$type}") ??
            static::$typeToFolder[$type] ??
            Str::studly(Str::plural($type));

        // Ensure backslash as namespace separator
        $subNamespace = str_replace(['/', '\\'], '\\', $subNamespace);

        return rtrim($info['root_namespace'], '\\') . '\\' . trim($subNamespace, '\\');
    }

    /**
     * Get the absolute file path for the file to be generated.
     *
     * @param  string  $module     The name of the module
     * @param  string  $type       The type of object (e.g., use case, entity)
     * @param  string  $className  The class name of the file to generate
     * @return string  The absolute file path for code generation
     */
    protected function getTargetFilePath(string $module, string $type, string $className): string
    {
        $info = $this->getModuleInfo($module);

        // Prefer config mapping, fallback to internal mapping, else Plural Studly.
        $subPath = config("3d.namespaces.{$type}") ??
            static::$typeToFolder[$type] ??
            Str::studly(Str::plural($type));

        // Convert namespace separators and double slashes to filesystem slashes
        $directory = rtrim($info['base_path'], '/') . '/' . str_replace(['\\', '//'], '/', trim($subPath, '\\/'));

        return "{$directory}/{$className}.php";
    }
}
