# **Troubleshooting**

Even with a powerful engine, architectural shifts can lead to common "hiccups." Most issues in Morphling 3D are related to **PSR-4 naming conventions**, **Service Discovery**, or **Circular Dependencies**. 

Use this guide to diagnose and fix the most frequent hurdles.

---

## **1. "Class Not Found" Errors**
This is the #1 issue. It usually happens when the physical file path doesn't match the PHP Namespace.

* **The Cause:** You manually moved a file or renamed a folder without updating the `namespace` declaration at the top of the file.
* **The Fix:** 1. Ensure your folder is `modules/Transaction/Domain/Entities` and the file is `TransactionEntity.php`.
    2. The namespace **must** be `namespace Modules\Transaction\Domain\Entities;`.
    3. Run `composer dump-autoload` to refresh the class map.

---

## **2. "Route [name] not defined" or 404 Errors**
You’ve created the `Delivery/Routes/api.php` file, but Laravel doesn't see it.

* **The Cause:** The module's Service Provider hasn't been "discovered" or registered.
* **The Fix:**
    1. Run `php artisan module:discover`.
    2. Check `bootstrap/providers.php` to see if your `TransactionServiceProvider` is listed.
    3. Run `php artisan module:list` to verify the module's status.



---

## **3. Target Class [Interface] is not instantiable**
You are type-hinting an Interface in your Use Case, but Laravel doesn't know which concrete class to provide.

* **The Cause:** You forgot to "bind" the Interface to the Implementation in your Service Provider.
* **The Fix:**
    Open `Infrastructure/Providers/TransactionServiceProvider.php` and add the binding in the `register` method:
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
Module A depends on Module B, and Module B depends on Module A. This causes a crash or infinite loop during booting.

* **The Cause:** Poorly defined boundaries.
* **The Fix:**
    1. **Extract to Shared:** Move the shared logic/interface into `modules/Shared`.
    2. **Use Events:** Instead of Module A calling Module B directly, have Module A fire a **Domain Event** that Module B listens to.

---

## **5. Database Column "X" Not Found**
Your Repository is throwing an SQL error even though you "just added" the column.

* **The Cause:** The migration is inside the module, and Laravel's global `migrate` command needs to be told where to look.
* **The Fix:**
    Ensure your `ServiceProvider` has the migration loading logic:
    ```php
    $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    ```
    Then run `php artisan migrate`.

---

## **Quick Command "Reset"**
If everything feels broken, run this "Magic Sequence" to reset the engine's state:

```bash
composer dump-autoload
php artisan module:discover
php artisan config:clear
php artisan route:clear
```
