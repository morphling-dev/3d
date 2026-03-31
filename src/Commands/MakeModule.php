<?php

namespace Morphling\ThreeD\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Morphling\ThreeD\Support\ProviderManager;

class MakeModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '3d:new {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new DDD module structure';

    /**
     * Execute the console command to create a new module.
     *
     * @return int Command exit status
     */
    public function handle(): int
    {
        $moduleName = Str::studly($this->argument('name'));
        $this->info("Creating Module: {$moduleName}...");

        (new ProviderManager())->add($moduleName);

        $this->generateDefaultFiles($moduleName);

        return static::SUCCESS;
    }

    /**
     * Generates the default boilerplate files for the given module.
     *
     * @param  string  $module  The module name
     * @return void
     */
    protected function generateDefaultFiles(string $module): void
    {
        $version = 'v1';
        $moduleName = Str::studly($module);

        // Delivery Layer: Controller and Route
        $this->call('3d:make-controller', [
            'name'   => "Api/{$version}/{$moduleName}Controller",
            'module' => $module,
            '--ver'  => $version,
        ]);
        $this->call('3d:make-controller', [
            'name'   => "Web/{$moduleName}Controller",
            'module' => $module,
        ]);

        $this->call('3d:make-route', [
            'name'   => 'api',
            'module' => $module,
            '--ver'  => $version
        ]);

        $this->call('3d:make-route', [
            'name'   => 'web',
            'module' => $module
        ]);
        $this->call('3d:make-view', ['name' => 'index', 'module' => $module]);
        $this->call('3d:make-service', ['name' => "{$module}DomainService", 'module' => $module]);

        // Application Layer: Use Case
        $this->call('3d:make-usecase', ['name' => "Get{$module}ListUseCase", 'module' => $module]);
        $this->call('3d:make-dto', ['name' => "{$module}Dto", 'module' => $module]);

        // Domain Layer: Repository Interface
        $this->call('3d:make-entity', ['name' => "{$module}Entity", 'module' => $module]);
        $this->call('3d:make-interface', ['name' => "{$module}RepositoryInterface", 'module' => $module]);
        $this->call('3d:make-vo', ['name' => "{$module}Status", 'module' => $module]);
        $this->call('3d:make-enum', ['name' => "{$module}StatusEnum", 'module' => $module]);

        // Infrastructure Layer: Model, Repository, Observer, Provider, Factory, Seeder
        $this->call('3d:make-model', ['name' => "{$module}Model", 'module' => $module]);
        $this->call('3d:make-repo', ['name' => "Eloquent{$module}Repository", 'module' => $module]);
        $this->call('3d:make-mapper', ['name' => "{$module}Mapper", 'module' => $module]);
        $this->call('3d:make-observer', ['name' => "{$module}Observer", 'module' => $module]);
        $this->call('3d:make-provider', ['module' => $module]);
        $this->call('3d:make-migration', ['name' => "create_" . Str::snake(Str::plural($module)) . "_table", 'module' => $module]);
        $this->call('3d:make-event', ['name' => "{$module}Created", 'module' => $module]);
        $this->call('3d:make-job', ['name' => "Process{$module}Job", 'module' => $module]);
        $this->call('3d:make-external', ['name' => "{$module}ApiService", 'module' => $module]);
        $this->call('3d:make-factory', ['name' => "{$module}Factory", 'module' => $module]);
        $this->call('3d:make-seeder', ['name' => "{$module}Seeder", 'module' => $module]);

        $this->info("All layers for module {$module} have been generated.");
    }

    /**
     * Retrieve module info such as path, namespace, and studly name.
     *
     * @param  string  $module  The module name
     * @return array   The module information
     */
    public function getModuleInfo(string $module): array
    {
        $module = Str::studly($module);
        $basePath = rtrim(config('3d.base_path', base_path('modules')), '/');
        $baseNamespace = trim(config('3d.base_namespace', 'Modules'), '\\');

        return [
            'name'                => $module,
            'name_snake'          => Str::snake($module),
            'name_snake_plural'   => Str::plural(Str::snake($module)),
            'name_kebab'          => Str::kebab($module),
            'base_namespace'      => $baseNamespace,
            'root_namespace'      => "{$baseNamespace}\\{$module}",
            'base_path'           => "{$basePath}/{$module}",
        ];
    }
}
