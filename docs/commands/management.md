# Management Commands

These commands manage the lifecycle of your modular ecosystem. While the generators build the files, the **Management Commands** handle the "wiring"—ensuring Laravel recognizes your modules, registers their routes, and cleans up the environment.

---

## Executive Summary
Morphling 3D uses a **Service Discovery** pattern. Instead of manually adding Service Providers to `config/app.php`, you use these commands to sync your physical `modules/` folder with Laravel's internal registry.

> [!IMPORTANT]
> **Key Rule:** Always run `php artisan module:discover` after creating a new module or deleting one manually to refresh the application map.

---

## The Management Toolkit

### 1. The Initializer
```bash
php artisan 3d:install
```
**Purpose:** The "Big Bang" command. It creates the `modules/` directory, scaffolds the `Shared` kernel, and publishes the core configuration. Run this once per project.

### 2. The Auditor
```bash
php artisan module:list
```
**Purpose:** Provides a high-level health check. It scans your `modules/` folder and reports:
* **Registration Status:** Is the Service Provider active?
* **Routes:** Does it have `api.php` or `web.php` defined?
* **Views:** Is the view namespace registered?

### 3. The Synchronizer
```bash
php artisan module:discover
```
**Purpose:** The most used management command. It automatically detects all `*ServiceProvider.php` files within your modules and injects them into `bootstrap/providers.php` (for Laravel 11+) or the internal cache. 



### 4. The Janitor
```bash
php artisan module:delete {name}
```
**Purpose:** Safely removes a module.
* **Cleanup:** It deletes the physical folder and automatically unregisters the Service Provider so your app doesn't crash looking for missing classes.
* **Safety:** It will refuse to delete the `Shared` module to prevent accidental architectural collapse.

---

## Management Workflow: The "Safe Sync"

Whenever you move your project to a new environment (e.g., CI/CD or a teammate's machine), follow this sequence:

1. `composer install` (Installs the engine)
2. `php artisan module:discover` (Wires the modules)
3. `php artisan route:list` (Verifies the endpoints are live)

---

## Troubleshooting

### "My module is in the folder but doesn't show in `module:list`"
**Solution:** Ensure the folder name matches the PSR-4 namespace in `composer.json`. If your folder is `Transaction`, the class inside should be `Modules\Transaction\...`. Run `composer dump-autoload` if you changed folder names manually.

### "I deleted a folder manually and now the app is broken."
**Solution:** Laravel is likely trying to load a Service Provider that no longer exists. Run `php artisan module:discover` to force a re-sync of the provider list.

### "Can I disable a module without deleting it?"
**Solution:** Currently, Morphling 3D treats any folder in `modules/` as an active candidate. To disable one without deleting, move it outside the `modules/` directory or rename its Service Provider file so it doesn't match the discovery pattern.