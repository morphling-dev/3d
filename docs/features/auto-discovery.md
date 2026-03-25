# Auto-Discovery

Morphling 3D “wires” modules automatically so you don’t have to manually register providers, routes, or view namespaces.

## What happens during discovery

1. `php artisan module:discover`
2. The command triggers `ProviderManager->sync()`
3. `ProviderManager->sync()` scans `modules/` and ensures each module’s Service Provider is present in `bootstrap/providers.php`
4. `ThreeDServiceProvider` runs `AutoloadManager::register($this)` to load module-specific migrations and route files

## Provider responsibilities

Each generated module includes a Service Provider based on `stubs/provider.stub`.
That provider is responsible for routing + view namespacing.

Example excerpt (routes + views):

```php
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

protected function registerViews(): void
{
    $viewPath = __DIR__ . '/../../Delivery/Views';

    if (is_dir($viewPath)) {
        $this->loadViewsFrom($viewPath, '{{ module_snake }}');
    }
}
```

## Notes

- Route loading is conditional: only loads `Delivery/Routes/web.php` and/or `Delivery/Routes/api.php` if they exist.
- View loading uses a namespaced alias: `{{ module_snake }}::index`.

