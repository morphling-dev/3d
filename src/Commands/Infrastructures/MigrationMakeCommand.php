<?php

namespace Morphling\ThreeD\Commands\Infrastructures;

use Illuminate\Support\Str;
use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class MigrationMakeCommand extends BaseGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '3d:make-migration {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration file in the module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'migration';

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name): string
    {
        $details = $this->getModuleInfo($this->argument('module'));
        $subPath = config("3d.namespaces.migration", "Infrastructure/Database/Migrations");
        $name = Str::snake($this->argument('name'));
        $timestamp = now()->format('Y_m_d_His');
        return $details['base_path'] . '/' . str_replace('\\', '/', $subPath) . '/' . $timestamp . '_' . $name . '.php';
    }

    /**
     * Override parseName agar tidak merusak nama class migrasi
     *
     * @param  string  $name
     * @return string
     */
    protected function parseName($name)
    {
        return $name;
    }

    /**
     * Get the stub file path for the migration.
     *
     * @return string  The path to the migration stub file.
     */
    protected function getStub(): string
    {
        return __DIR__ . '/../../../stubs/migration.stub';
    }
}
