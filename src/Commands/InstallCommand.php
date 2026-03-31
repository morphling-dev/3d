<?php

namespace Morphling\ThreeD\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '3d:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize the 3D Domain Driven Design architecture';

    /**
     * Execute the console command.
     *
     * @return int Command exit status (Command::SUCCESS)
     */
    public function handle(): int
    {
        $this->info('Initializing 3D DDD Architecture...');

        $this->publishConfig();
        $this->createModulesDirectory();
        $this->initializeSharedModule();
        $this->updateComposerAutoload();

        $this->info('Installation complete! You can now run: php artisan 3d:new [ModuleName]');

        return Command::SUCCESS;
    }

    /**
     * Publish the package configuration file.
     *
     * @return void
     */
    protected function publishConfig(): void
    {
        $this->info('Publishing configuration...');
        $this->call('vendor:publish', [
            '--provider' => 'Morphling\ThreeD\ThreeDServiceProvider',
            '--tag'      => '3d-config',
        ]);
    }

    /**
     * Create the base modules directory if it does not exist.
     *
     * @return void
     */
    protected function createModulesDirectory(): void
    {
        $modulesBasePath = config('3d.base_path', base_path('modules'));

        if (!File::isDirectory($modulesBasePath)) {
            File::makeDirectory($modulesBasePath, 0755, true);
            $this->line("Created directory: {$modulesBasePath}");
        }
    }

    /**
     * Initialize the Shared module and populate with base templates.
     *
     * @return void
     */
    protected function initializeSharedModule(): void
    {
        $modulesBasePath = config('3d.base_path', base_path('modules'));
        $sharedModulePath = $modulesBasePath . DIRECTORY_SEPARATOR . 'Shared';

        if (File::isDirectory($sharedModulePath)) {
            if (!$this->confirm('Shared module already exists. Do you want to skip initializing it?', true)) {
                $this->copySharedTemplates($sharedModulePath);
                $this->info('Shared module templates refreshed.');
            }
            return;
        }

        File::makeDirectory($sharedModulePath, 0755, true);
        $this->copySharedTemplates($sharedModulePath);
        $this->info('Shared module initialized with base classes.');
    }

    /**
     * Copy the shared templates from the package to the user project.
     *
     * @param  string  $targetPath  The destination path for shared templates.
     * @return void
     */
    protected function copySharedTemplates(string $targetPath): void
    {
        // Path sekarang mengarah ke root_package/stubs/Shared
        $sourcePath = __DIR__ . '/../../stubs/Shared';

        if (File::isDirectory($sourcePath)) {
            File::copyDirectory($sourcePath, $targetPath);
        } else {
            $this->warn('Source Shared templates not found in package. Please check your package structure.');
        }
    }

    /**
     * Ensure composer.json has the PSR-4 autoload for Modules\, and run dump-autoload.
     *
     * @return void
     */
    protected function updateComposerAutoload(): void
    {
        $composerPath = base_path('composer.json');

        if (!File::exists($composerPath)) {
            $this->warn("composer.json not found at {$composerPath}");
            return;
        }

        $composer = json_decode(File::get($composerPath), true);

        if (!isset($composer['autoload']['psr-4']['Modules\\'])) {
            $composer['autoload']['psr-4']['Modules\\'] = 'modules/';

            File::put($composerPath, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->info("📝 PSR-4 'Modules' namespace added to composer.json");

            // Run composer dump-autoload to apply the change
            shell_exec('composer dump-autoload');
        }
    }
}
