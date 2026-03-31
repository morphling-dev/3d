<?php

namespace Morphling\ThreeD;

use Composer\InstalledVersions;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
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
        Commands\ModuleMigrateCommand::class,
        Commands\ModuleRouteCommand::class,
        Commands\ModuleSeedCommand::class,
        Commands\ModuleTestCommand::class,

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
        Commands\Infrastructures\MakeCommand::class,
        Commands\Infrastructures\MapperMakeCommand::class,
        Commands\Infrastructures\ModelMakeCommand::class,
        Commands\Infrastructures\NotificationMakeCommand::class,
        Commands\Infrastructures\RepositoryMakeCommand::class,
        Commands\Infrastructures\MigrationMakeCommand::class,
        Commands\Infrastructures\ObserverMakeCommand::class,
        Commands\Infrastructures\ModuleServiceProviderMakeCommand::class,
        Commands\Infrastructures\FactoryMakeCommand::class,
        Commands\Infrastructures\SeederMakeCommand::class,

        // UI Layer
        Commands\Deliveries\ControllerMakeCommand::class,
        Commands\Deliveries\RequestMakeCommand::class,
        Commands\Deliveries\ResourceMakeCommand::class,
        Commands\Deliveries\RouteMakeCommand::class,
        Commands\Deliveries\ViewMakeCommand::class,
    ];

    /**
     * Bootstrap any package services including config publishing, console commands,
     * autoloading, view version sharing, and module route registration.
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

            // Register all commands
            $this->commands($this->commands);
        }

        // Automatic autoloading for modules (routes, migrations, etc)
        AutoloadManager::register($this);

        // Register all module routes if available
        $this->registerModuleRoutes();

        // Version sharing for Blade views
        $version = 'dev-main';

        try {
            // Use the actual Composer package name for version detection
            if (class_exists(InstalledVersions::class) && InstalledVersions::isInstalled('morphling-dev/3d')) {
                $version = InstalledVersions::getPrettyVersion('morphling-dev/3d');
            }
        } catch (\Exception $e) {
            $version = '1.0.0-dev';
        }

        // Share version to all Blade views
        View::share('threed_version', $version);
    }

    /**
     * Register package services and merge Morphling 3D configuration.
     *
     * Ensures config('3d') is always available, even if not published.
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
     * @param string $path The absolute path to the migrations directory.
     * @return void
     */
    public function registerModuleMigrations(string $path): void
    {
        $this->loadMigrationsFrom($path);
    }

    /**
     * Register module views from the given path and namespace.
     *
     * @param string $path      The absolute path to the views directory.
     * @param string $namespace The namespace for the module views.
     * @return void
     */
    public function registerModuleViews(string $path, string $namespace): void
    {
        $this->loadViewsFrom($path, $namespace);
    }

    /**
     * Discover and register API and Web routes for all installed modules.
     *
     * Automatically loads routes from each module's Delivery/Routes directory if available.
     *
     * @return void
     */
    protected function registerModuleRoutes(): void
    {
        // Base path to where modules are stored
        $basePath = base_path('Modules');

        // If the modules directory does not exist, abort route registration
        if (!File::exists($basePath)) {
            return;
        }

        // Iterate through each module directory in the Modules folder
        foreach (File::directories($basePath) as $modulePath) {
            $routesPath = $modulePath . DIRECTORY_SEPARATOR . 'Delivery' . DIRECTORY_SEPARATOR . 'Routes';

            // Skip if the module does not contain a Routes directory
            if (!File::exists($routesPath)) {
                continue;
            }

            // Recursively collect all PHP route files inside the Routes directory (supports subfolders, e.g., v1, v2)
            $routeFiles = File::allFiles($routesPath);

            foreach ($routeFiles as $file) {
                $fileName = $file->getFilename();
                $filePath = $file->getRealPath();

                // Register API routes with 'api' middleware if file name matches 'api.php'
                if ($fileName === 'api.php') {
                    Route::middleware('api')->group($filePath);
                }

                // Register Web routes with 'web' middleware if file name matches 'web.php'
                if ($fileName === 'web.php') {
                    Route::middleware('web')->group($filePath);
                }
            }
        }
    }
}
