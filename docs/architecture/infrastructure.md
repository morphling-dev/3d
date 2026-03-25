# Infrastructure

The **Infrastructure** layer implements technical details: persistence, integration points, and background work.

In a DDD module, this layer should depend on the Domain interfaces—not the other way around.

## What Morphling 3D Generates

- `module:make-model` generates `Infrastructure/Models/*`
- `module:make-repo` generates `Infrastructure/Repositories/*`
- `module:make-mapper` generates `Infrastructure/Mappers/*`
- `module:make-observer` generates `Infrastructure/Observers/*`
- `module:make-provider` generates a module Service Provider under `Infrastructure/Providers/*`
- `module:make-migration` generates migrations under `Infrastructure/Database/Migrations/*`
- `module:make-event`, `module:make-listener`, `module:make-job`, `module:make-notification`
- `module:make-external` and `module:make-command` for integrations and console utilities

## Example: Service Provider Bootstrapping

Module providers are responsible for registering routes and views (excerpt):

```php
public function boot(): void
{
    $this->registerRoutes();
    $this->registerViews();
    // Uncomment the following line if you want to load module migrations:
    // $this->registerMigrations();
}

protected function registerRoutes(): void
{
    $modulePath = base_path('modules/{{ module }}/Delivery/Routes');

    if (file_exists($modulePath . '/web.php')) {
        Route::middleware('web')->group($modulePath . '/web.php');
    }

    if (file_exists($modulePath . '/api.php')) {
        Route::prefix('api/{{ module_snake }}')
            ->middleware('api')
            ->group($modulePath . '/api.php');
    }
}
```

## Navigation

- [Domain Responsibilities](#/architecture/domain)
- [Application Responsibilities](#/architecture/application)
- [Delivery Responsibilities](#/architecture/delivery)

