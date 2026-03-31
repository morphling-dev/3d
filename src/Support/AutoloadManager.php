<?php

namespace Morphling\ThreeD\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class AutoloadManager
{
    /**
     * Register all components from each module according to configuration.
     *
     * @param  mixed  $provider  The service provider instance (usually a Laravel or custom provider)
     * @return void
     */
    public static function register($provider): void
    {
        $basePath = config('3d.base_path', base_path('modules'));

        if (!File::isDirectory($basePath)) {
            return;
        }

        $modules = File::directories($basePath);

        foreach ($modules as $modulePath) {
            $moduleName = basename($modulePath);

            // 1. Autoload Migrations
            $migrationPath = $modulePath . '/Infrastructure/Database/Migrations';
            if (File::isDirectory($migrationPath)) {
                $provider->registerModuleMigrations($migrationPath);
            }

            // 2. Autoload Routes (Delivery Layer)
            $routePath = $modulePath . '/Delivery/Routes';
            if (File::isDirectory($routePath)) {
                self::registerRoutes($routePath, $moduleName);
            }
        }
    }

    /**
     * Register API and Web routes for the given module, if the route files exist.
     *
     * @param  string  $path  The absolute path to the routes directory
     * @param  string  $moduleName The module's name
     * @return void
     */
    protected static function registerRoutes(string $path, string $moduleName): void
    {
        $prefix = Str::snake($moduleName);

        // Load api.php
        if (File::exists($path . '/api.php')) {
            Route::prefix("api/{$prefix}")
                ->middleware('api')
                ->group($path . '/api.php');
        }

        // Load web.php
        if (File::exists($path . '/web.php')) {
            Route::middleware('web')
                ->group($path . '/web.php');
        }
    }
}
