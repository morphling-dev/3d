# Auto-Discovery: The Zero-Config Engine

**Auto-Discovery** is a core feature of Morphling 3D that removes the need for manual module registration in your Laravel application. Traditionally, adding a new feature often meant altering `config/app.php` or registering providers manually. Morphling 3D eliminates this "Registration Tax" by automatically detecting and integrating modules into the framework, so your code is always ready to run.

---

## How Discovery Works

Morphling 3D's discovery process ensures new module functionality is available to the framework immediately after creation, following a predictable, automated sequence:

### 1. The Sync Phase
When you run `php artisan 3d:discover`, Morphling 3D scans the `modules/` directory, searching for any class named `*ServiceProvider.php` inside each module's `Infrastructure/Providers` directory.

### 2. The Registry Phase
Discovered module providers are automatically synchronized with Laravel’s list of loaded providers. In recent versions of Laravel (11+), this means updating the `bootstrap/providers.php` file. As a result, your module is treated just like any Laravel package, with all providers recognized by the framework instantly.

### 3. The Registration Phase
With the provider registered, Morphling 3D hands off control to your module’s own `ServiceProvider`, which typically calls methods such as:
* **Mount Routes:** Registers API and web routes from `Delivery/Routes/api.php` and `Delivery/Routes/web.php`.
* **Namespace Views:** Exposes `Delivery/Views` using a slug-based alias (e.g., `transaction::index` for the Transaction module).
* **Load Migrations:** Optionally integrates migrations from `Infrastructure/Database/Migrations` into the global migration system.

---

## Anatomy of the Module Provider

Each module should define a Service Provider (typically in `Infrastructure/Providers`) that acts as its bridge to Laravel. Here’s a typical structure:

```php
// modules/Transaction/Infrastructure/Providers/TransactionServiceProvider.php

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

* **Consistency:** Every module follows the same conventions for routes, views, and migrations, reducing confusion and increasing team productivity.
* **Decoupling:** Remove a module and run `php artisan 3d:discover`—Morphling 3D will remove its registration automatically so there are no lingering errors or references.
* **Speed:** Go from `php artisan 3d:new ModuleName` to a working API endpoint in seconds, with zero configuration overhead.

---

## Validation & Health Checks

You can verify the discovery system with the following diagnostics:

| Command                        | What to check                           |
| :----------------------------- | :-------------------------------------- |
| `php artisan 3d:list`          | Module’s `Registered` column is `Yes`.  |
| `php artisan 3d:route:list`    | Routes for your module are present.     |
| `php artisan view:cache`       | (Production) Views compile successfully.|

---

## Troubleshooting

### "Routes are 404ing after creating a module"
**Solution:** You may have missed running `php artisan 3d:discover`. This command is required for Laravel to detect newly added modules and their routes.

### "View [module::index] not found"
**Solution:** Double-check the `registerViews()` method in your module’s Service Provider. Ensure the path to `Delivery/Views` is correct. Morphling 3D uses **snake_case** for view aliases, so `MyModule` becomes `my_module::viewname`.