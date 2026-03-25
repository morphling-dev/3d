<?php

namespace Morphling\ThreeD\Commands\Deliveries;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;
use Illuminate\Support\Str;

class RouteMakeCommand extends BaseGeneratorCommand
{
    /**
     * The command signature to generate the route file.
     * Usage: php artisan module:make-route api Order
     *
     * @var string
     */
    protected $signature = 'module:make-route {name} {module}';

    /**
     * The description of this Artisan command.
     *
     * @var string
     */
    protected $description = 'Create a new Route file (api/web) in the UI layer';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'route';

    /**
     * Get the destination path for the generated route file.
     *
     * @param  string  $name  The route file type (e.g., 'api' or 'web')
     * @return string  The fully qualified path to the route file
     */
    protected function getPath($name): string
    {
        $module = Str::studly($this->argument('module'));
        $fileName = Str::lower($this->argument('name')); // 'api' or 'web'

        $subPath = str_replace('\\', '/', config('3d.namespaces.route', 'UI\Routes'));

        return config('3d.base_path', base_path('modules')) . "/{$module}/{$subPath}/{$fileName}.php";
    }

    /**
     * Get the stub file path for the route being generated.
     * Determines stub file (api or web) depending on 'name' argument.
     *
     * @return string  The path to the stub file
     */
    protected function getStub(): string
    {
        $type = Str::lower($this->argument('name'));
        $customPath = base_path("stubs/modules/route-{$type}.stub");

        return file_exists($customPath)
            ? $customPath
            : __DIR__ . "/../../../stubs/route-{$type}.stub";
    }
}
