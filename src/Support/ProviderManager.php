<?php

namespace Morphling\ThreeD\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProviderManager
{
    /**
     * Get the absolute path to the providers configuration file.
     *
     * @return string  The path to the providers.php file.
     */
    protected static function getPath(): string
    {
        return base_path('bootstrap/providers.php');
    }

    /**
     * Add a module's ServiceProvider to the providers.php array if not already present.
     *
     * @param  string  $module  The name of the module to add.
     * @return void
     */
    public function add(string $module): void
    {
        $path = self::getPath();

        if (!File::exists($path)) {
            return;
        }

        $providerClass = "Modules\\{$module}\\Infrastructure\\Providers\\{$module}ServiceProvider::class";
        $content = File::get($path);

        // Prevent duplicate entries
        if (Str::contains($content, $providerClass)) {
            return;
        }

        // Insert provider before the end of the array
        $newContent = Str::replaceLast(
            '];',
            "    {$providerClass},\n];",
            $content
        );

        File::put($path, $newContent);
    }

    /**
     * Remove a module's ServiceProvider from the providers.php array.
     *
     * @param  string  $module  The name of the module to remove.
     * @return void
     */
    public function remove(string $module): void
    {
        $path = self::getPath();

        if (!File::exists($path)) {
            return;
        }

        $providerClass = "Modules\\{$module}\\Infrastructure\\Providers\\{$module}ServiceProvider::class";
        $content = File::get($path);

        // Remove the provider line if it exists
        $newContent = str_replace("    {$providerClass},\n", '', $content);

        File::put($path, $newContent);
    }

    /**
     * Synchronize all modules' ServiceProviders with the providers.php array.
     * Adds any that are missing.
     *
     * @return void
     */
    public function sync(): void
    {
        $modulesPath = base_path('modules');

        if (!File::isDirectory($modulesPath)) {
            return;
        }

        $modules = File::directories($modulesPath);
        $path = self::getPath();
        $content = File::get($path);

        foreach ($modules as $moduleDir) {
            $moduleName = basename($moduleDir);
            $providerClass = "Modules\\{$moduleName}\\Infrastructure\\Providers\\{$moduleName}ServiceProvider::class";

            if (!Str::contains($content, $providerClass)) {
                $this->add($moduleName);
                // Refresh content after each addition to prevent duplicates
                $content = File::get($path);
            }
        }
    }
}
