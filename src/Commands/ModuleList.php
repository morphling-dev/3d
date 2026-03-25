<?php

namespace Morphling\ThreeD\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Composer\InstalledVersions;

class ModuleList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all generated modules and their status';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $modulesPath = base_path('modules');

        if (!File::isDirectory($modulesPath)) {
            $this->warn('No modules directory found at /modules');
            return;
        }

        $directories = File::directories($modulesPath);

        if (empty($directories)) {
            $this->info("Empty project. Run 'php artisan module:new' to get started.");
            return;
        }

        $rows = [];
        $engineVersion = $this->getEngineVersion();

        foreach ($directories as $dir) {
            $name = basename($dir);

            // Skip "Shared" module from the list of feature modules
            if ($name === 'Shared') {
                continue;
            }

            $hasProvider = File::exists($dir . "/Infrastructure/Providers/{$name}ServiceProvider.php");
            $hasRoutes = File::exists($dir . "/Delivery/Routes/web.php");

            $rows[] = [
                $name,
                $hasProvider ? '<fg=green>Registered</>' : '<fg=red>Missing Provider</>',
                $hasRoutes ? '<fg=blue>Active</>' : '<fg=gray>No Routes</>',
                $this->getRelativePath($dir),
            ];
        }

        $this->newLine();
        $this->info("Morphling 3D Engine <fg=gray>v{$engineVersion}</>");
        $this->table(
            ['Module Name', 'Provider Status', 'Route Status', 'Path'],
            $rows
        );
        $this->newLine();
    }

    /**
     * Generate the relative path for a given absolute path.
     *
     * @param  string  $path  Absolute module directory path
     * @return string  Relative module path from base path
     */
    protected function getRelativePath(string $path): string
    {
        return str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
    }

    /**
     * Get the current version of the Morphling 3D engine.
     *
     * @return string  Engine version as a string
     */
    protected function getEngineVersion(): string
    {
        try {
            $version = InstalledVersions::getPrettyVersion('morphling-dev/3d');
            return $version ?? '1.0.0-dev';
        } catch (\Exception $e) {
            return '1.0.0-dev';
        }
    }
}
