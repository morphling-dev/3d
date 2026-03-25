<?php

namespace Morphling\ThreeD;

use Composer\InstalledVersions;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Morphling\ThreeD\Support\AutoloadManager;

class ThreeDServiceProvider extends ServiceProvider
{
    /**
     * The list of commands to be registered with Artisan.
     *
     * @var array
     */
    protected array $commands = [
        // Core Commands
        Commands\InstallCommand::class,
        Commands\MakeModule::class,
        Commands\DeleteModule::class,
        Commands\ModuleDiscover::class,
        Commands\ModuleList::class,

        // Application Layer
        Commands\Applications\DtoMakeCommand::class,
        Commands\Applications\UseCaseMakeCommand::class,

        // Domain Layer
        Commands\Domains\EntityMakeCommand::class,
        Commands\Domains\InterfaceMakeCommand::class,
        Commands\Domains\ServiceMakeCommand::class,
        Commands\Domains\ValueObjectMakeCommand::class,
        Commands\Domains\EnumMakeCommand::class,

        // Infrastructure Layer
        Commands\Infrastructures\EventMakeCommand::class,
        Commands\Infrastructures\ExternalMakeCommand::class,
        Commands\Infrastructures\JobMakeCommand::class,
        Commands\Infrastructures\ListenerMakeCommand::class,
        Commands\Infrastructures\MakeCommand::class, // Console Command generator
        Commands\Infrastructures\MapperMakeCommand::class,
        Commands\Infrastructures\ModelMakeCommand::class,
        Commands\Infrastructures\NotificationMakeCommand::class,
        Commands\Infrastructures\RepositoryMakeCommand::class,
        Commands\Infrastructures\MigrationMakeCommand::class,
        Commands\Infrastructures\ObserverMakeCommand::class,
        Commands\Infrastructures\ModuleServiceProviderMakeCommand::class,

        // UI Layer
        Commands\Deliveries\ControllerMakeCommand::class,
        Commands\Deliveries\RequestMakeCommand::class,
        Commands\Deliveries\ResourceMakeCommand::class,
        Commands\Deliveries\RouteMakeCommand::class,
        Commands\Deliveries\ViewMakeCommand::class,
    ];

    /**
     * Bootstrap any package services including config publishing and console commands.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            // Register Config Publishing
            $this->publishes([
                __DIR__ . '/../config/3d.php' => config_path('3d.php'),
            ], '3d-config');

            // Register All Commands
            $this->commands($this->commands);
        }

        // Register automatic autoloading for modules (Routes, Migrations, etc)
        AutoloadManager::register($this);

        // --- VERSION SHARING LOGIC ---
        $version = 'dev-main';

        try {
            // Ganti 'morphling-dev/laravel-3d' dengan nama asli package Bapak di composer.json
            if (class_exists(InstalledVersions::class) && InstalledVersions::isInstalled('morphling-dev/3d')) {
                $version = InstalledVersions::getPrettyVersion('morphling-dev/3d');
            }
        } catch (\Exception $e) {
            $version = '1.0.0-dev';
        }

        // Share ke semua Blade view
        View::share('threed_version', $version);
    }

    /**
     * Register package services.
     *
     * Merges the 3D configuration file so that users can access config('3d')
     * without needing to publish the configuration first.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/3d.php', '3d');
    }

    /**
     * Register module migrations from the given path.
     *
     * @param string $path
     * @return void
     */
    public function registerModuleMigrations(string $path)
    {
        $this->loadMigrationsFrom($path);
    }

    /**
     * Register module views from the given path and namespace.
     *
     * @param string $path
     * @param string $namespace
     * @return void
     */
    public function registerModuleViews(string $path, string $namespace)
    {
        $this->loadViewsFrom($path, $namespace);
    }
}
