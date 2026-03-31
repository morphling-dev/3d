<?php

namespace Morphling\ThreeD\Commands\Deliveries;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;
use Illuminate\Support\Str;

class RouteMakeCommand extends BaseGeneratorCommand
{
    /**
     * The command signature to generate the route file.
     * Usage: php artisan 3d:make-route api Order --ver=v1
     *
     * @var string
     */
    protected $signature = '3d:make-route {name} {module} {--ver=v1}';

    /**
     * The description of this Artisan command.
     *
     * @var string
     */
    protected $description = 'Create a new Route file (api/web) in the UI layer with versioning';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'route';

    /**
     * Get the destination path for the generated route file, with versioning.
     *
     * @param  string  $name  The route file type (e.g., 'api' or 'web')
     * @return string  The fully qualified path to the route file
     */
    protected function getPath($name): string
    {
        $module = Str::studly($this->argument('module'));
        $fileName = Str::lower($this->argument('name'));
        $version = strtolower($this->option('ver') ?: 'v1');

        $subPath = str_replace('\\', '/', config('3d.namespaces.route', 'UI\Routes'));

        // Insert versioning into the subpath, e.g.: UI/Routes/v1
        $subPathWithVer = "{$subPath}/{$version}";

        return config('3d.base_path', base_path('modules')) . "/{$module}/{$subPathWithVer}/{$fileName}.php";
    }

    /**
     * Get the stub file path for the route being generated.
     * Determines stub file (api or web) depending on 'name' argument and includes version for advanced stub selection.
     *
     * @return string  The path to the stub file
     */
    protected function getStub(): string
    {
        $type = Str::lower($this->argument('name'));
        $version = strtolower($this->option('ver') ?: 'v1');
        // Try versioned stub first, fallback to non-versioned
        $customVersionedPath = base_path("stubs/modules/route-{$type}-{$version}.stub");
        $customPath = base_path("stubs/modules/route-{$type}.stub");

        if (file_exists($customVersionedPath)) {
            return $customVersionedPath;
        } elseif (file_exists($customPath)) {
            return $customPath;
        }

        $defaultVersioned = __DIR__ . "/../../../stubs/route-{$type}-{$version}.stub";
        $default = __DIR__ . "/../../../stubs/route-{$type}.stub";

        return file_exists($defaultVersioned) ? $defaultVersioned : $default;
    }

    /**
     * Build the processed route file content with replacements for version and module details.
     *
     * @param  string  $name  The route file name
     * @return string  The processed route file content
     */
    protected function buildClass($name): string
    {
        // Retrieve the base stub content from the parent
        $stub = parent::buildClass($name);

        $module = $this->argument('module');
        $version = strtolower($this->option('ver') ?? 'v1');
        $details = $this->getModuleInfo($module);

        // Prepare controller class and namespace for replacement
        $controllerClass = "{$details['name']}Controller";
        $controllerNamespace = $details['root_namespace'] . '\\Delivery\\Controllers\\Api\\' . Str::studly($version);

        // Prepare replacements array for stub variables
        $replacements = [
            '{{ controllerNamespace }}'    => $controllerNamespace,
            '{{ controllerClass }}'        => $controllerClass,
            '{{ version }}'                => str($version)->title(),
            '{{ module_snake }}'           => $details['name_snake'],
            '{{ module_snake_plural }}'    => $details['name_snake_plural'],
            '{{ module }}'                 => $details['name'],
        ];

        // Perform replacement and return the processed stub content
        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );
    }
}
