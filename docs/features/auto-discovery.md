# Auto-Discovery: The Zero-Config Engine

**Auto-Discovery** is the "magic glue" of Morphling 3D. In a standard Laravel project, adding a new feature often requires manual registration in `config/app.php` or `bootstrap/providers.php`. Morphling 3D eliminates this "Registration Tax" by automatically detecting and wiring your modules into the Laravel core.

---

## How Discovery Works

The discovery process follows a specific sequence to ensure your module's code is available to the framework as soon as it's created.



### 1. The Sync Phase
When you run `php artisan module:discover`, the **ProviderManager** performs a physical scan of the `modules/` directory. It looks for any class ending in `ServiceProvider.php` within the `Infrastructure/Providers` folder of each module.

### 2. The Registry Phase
The manager then synchronizes these found providers with Laravel's internal provider list (located in `bootstrap/providers.php` for Laravel 11+). This ensures that Laravel treats your module exactly like a first-party package.

### 3. The Registration Phase
Once registered, the module's own `ServiceProvider` takes over. It uses the `AutoloadManager` to:
* **Mount Routes:** Maps `Delivery/Routes/api.php` and `web.php`.
* **Namespace Views:** Links `Delivery/Views` to a slug (e.g., `transaction::index`).
* **Load Migrations:** (Optional) Connects `Infrastructure/Database/Migrations` to the global migration path.

---

## Anatomy of the Module Provider

Every module contains an Infrastructure-level provider that acts as its "External API" to Laravel.

```php
### modules/Transaction/Infrastructure/Providers/TransactionServiceProvider.php

public function boot(): void
{
    $this->registerRoutes();
    $this->registerViews();
}

protected function registerRoutes(): void
{
    // Automatically prefixes API routes with 'api/transaction'
    if (file_exists($apiPath = __DIR__ . '/../../Delivery/Routes/api.php')) {
        Route::prefix('api/transaction')
            ->middleware('api')
            ->group($apiPath);
    }
}
```

---

## Why This Matters

* **Consistency:** Every developer on your team follows the same routing and view conventions without discussion.
* **Decoupling:** You can delete a module folder, run `module:discover`, and the app will instantly stop trying to load its routes—no "Class not found" errors left in your config files.
* **Speed:** You go from `module:new` to a working API endpoint in under 5 seconds.

---

## Validation & Health Checks

To ensure your discovery is working correctly, use these two diagnostic commands:

| Command | What to look for |
| :--- | :--- |
| `php artisan module:list` | Ensure your module shows `Registered: Yes`. |
| `php artisan route:list` | Check for your module's prefix (e.g., `api/transaction/...`). |
| `php artisan view:cache` | (Production) Ensures all module views are compiled and ready. |

---

## Troubleshooting

### "Routes are 404ing after creating a module"
**Solution:** You likely forgot to run `php artisan module:discover`. The files exist, but Laravel hasn't been told to include the module's `Routes/api.php` yet.

### "View [module::index] not found"
**Solution:** Check the `registerViews()` method in your `ServiceProvider`. Ensure the path points correctly to `Delivery/Views`. Also, remember that Morphling uses **snake_case** for view aliases (e.g., `MyModule` becomes `my_module::index`).