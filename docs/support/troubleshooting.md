# **Troubleshooting**

Morphling 3D provides powerful DDD patterns, but architectural rigor can increase the risk of certain "gotchas." Most issues encountered are related to **PSR-4 autoloading**, **module/service discovery**, or **dependency boundaries**.

Use this guide to identify and resolve the most common sticking points.

---

## **1. "Class Not Found" Errors**
This is the most common stumbling block. It generally means your file/directory structure does not match its PHP namespace, so Composer cannot autoload the class.

* **The Cause:** You moved/renamed a file or folder but didn’t update the `namespace` declaration.
* **The Fix:**
    1. Check your file is in e.g. `modules/Transaction/Domain/Entities/TransactionEntity.php`.
    2. The file’s namespace must be `namespace Modules\Transaction\Domain\Entities;`.
    3. Run `composer dump-autoload` to refresh Composer’s classmap.

---

## **2. "Route [name] not defined" or 404 Errors**
You added routes in `Delivery/Routes/api.php` but they're not being registered by Laravel.

* **The Cause:** The module’s Service Provider is not autoloaded/discovered.
* **The Fix:**
    1. Run `php artisan 3d:discover` (not `module:discover` - see [README](../../README.md) for correct command).
    2. Ensure your module’s Service Provider is registered in the right namespace (`modules/{Module}/Infrastructure/Providers/`).
    3. Verify the provider is being auto-discovered by running `php artisan 3d:list`.
    4. If creating a new module, use `php artisan 3d:new {ModuleName}` for guaranteed structure.

---

## **3. Target Class [Interface] is not instantiable**
Laravel cannot resolve the interface you're type-hinting, likely in a UseCase constructor.

* **The Cause:** The interface isn’t bound to its concrete class in the module’s Service Provider.
* **The Fix:**
    In your provider’s `register()` method (e.g. `Infrastructure/Providers/TransactionServiceProvider.php`):

    ```php
    public function register(): void
    {
        $this->app->bind(
            \Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface::class,
            \Modules\Transaction\Infrastructure\Repositories\EloquentTransactionRepository::class
        );
    }
    ```

---

## **4. Circular Dependency Detected**
If modules depend on each other (A → B → A), bootstrapping will break or loop infinitely.

* **The Cause:** Missing boundaries or shared logic placed in the wrong module.
* **The Fix:**
    1. **Extract to Shared Kernel**: Move shared interfaces/dtos/events into `modules/Shared/`.
    2. **Prefer Events**: Design interactions using Domain Events, so modules communicate indirectly.

---

## **5. Database Column "X" Not Found**
You added or renamed a column, but your code or repository throws a SQL "column not found" error.

* **The Cause:** Migration was placed within the module but not registered for auto-discovery; running `php artisan migrate` only runs global migrations.
* **The Fix:**
    1. In your module’s Service Provider, ensure migrations are loaded:
        ```php
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        ```
    2. Use `php artisan 3d:migrate` to run all module migrations.

---

## **Quick Reset Commands**
If you suspect classmap or config/cache issues, this sequence usually puts things right:

```bash
composer dump-autoload
php artisan 3d:discover
php artisan config:clear
php artisan route:clear
```

See [README](../../README.md) for full command reference and troubleshooting checklist.