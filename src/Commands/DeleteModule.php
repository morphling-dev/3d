<?php

namespace Morphling\ThreeD\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Morphling\ThreeD\Support\ProviderManager;

class DeleteModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '3d:delete {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a DDD module and unregister its service provider';

    /**
     * Execute the console command to delete a module and unregister its service provider.
     *
     * @return int Command exit status (SUCCESS or FAILURE)
     */
    public function handle(): int
    {
        $module = Str::studly($this->argument('name'));
        $path = base_path('modules/' . $module);

        // Prevent deletion of the protected "Shared" module
        if ($module === 'Shared') {
            $this->error("The 'Shared' module cannot be deleted.");
            return static::FAILURE;
        }

        // Ensure the target module folder exists
        if (!File::isDirectory($path)) {
            $this->warn("Module {$module} was not found at {$path}.");
            return static::FAILURE;
        }

        // Require user confirmation (double confirmation for extra safety)
        if (! $this->confirm(
            "Are you sure you want to permanently DELETE the {$module} module? This is irreversible.",
            false
        )) {
            $this->info("Deletion of module {$module} cancelled.");
            return static::SUCCESS;
        }

        if (! $this->confirm(
            "FINAL CONFIRMATION: Are you absolutely certain you want to DESTROY folder {$module}? This data cannot be recovered.",
            false
        )) {
            $this->info("Deletion of module {$module} cancelled.");
            return static::SUCCESS;
        }

        // Unregister the module from providers.php
        (new ProviderManager())->remove($module);

        // Attempt to delete the module directory
        try {
            File::deleteDirectory($path);
        } catch (\Throwable $e) {
            $this->error("Failed to delete module folder: {$e->getMessage()}");
            return static::FAILURE;
        }

        $this->info("Module {$module} was successfully deleted and is no longer registered.");
        return static::SUCCESS;
    }
}
