<?php

namespace Morphling\ThreeD\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ModuleMigrateCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = '3d:migrate {module?} {--rollback} {--fresh} {--seed}';

    /**
     * Description of the console command.
     *
     * @var string
     */
    protected $description = 'Run migrations for Morphling 3D modules';

    /**
     * Execute the console command.
     *
     * Runs migrations for a specific module or all modules as specified.
     * Optionally supports rollback, fresh migrations, and seeding.
     *
     * @return int  Exit status
     */
    public function handle(): int
    {
        $module = $this->argument('module');
        $basePath = config('3d.base_path', base_path('modules'));

        if ($module) {
            $moduleName = Str::studly($module);
            $this->migrateModule($moduleName, $basePath);
        } else {
            $this->info('Running migrations for all modules...');
            $modules = File::directories($basePath);

            foreach ($modules as $modulePath) {
                $this->migrateModule(basename($modulePath), $basePath);
            }
        }

        if ($this->option('seed')) {
            $this->call('3d:seed', [
                'module' => $module,
            ]);
        }

        return 0;
    }

    /**
     * Run migration command for a given module.
     *
     * @param  string  $name      Module name (studly case)
     * @param  string  $basePath  Base path containing all modules
     * @return void
     */
    protected function migrateModule(string $name, string $basePath): void
    {
        $migrationPath = "modules/{$name}/Infrastructure/Database/Migrations";
        $fullPath = base_path($migrationPath);

        if (!File::exists($fullPath)) {
            $this->warn("No migrations found for module: {$name}");
            return;
        }

        $action = 'migrate';
        if ($this->option('fresh')) {
            $action = 'migrate:fresh';
        } elseif ($this->option('rollback')) {
            $action = 'migrate:rollback';
        }

        $this->info("Module: {$name} | Action: {$action}");

        $this->call($action, [
            '--path' => $migrationPath,
            '--realpath' => true,
        ]);
    }
}
