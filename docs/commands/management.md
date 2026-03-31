# Management Commands

These commands manage the lifecycle of your modular ecosystem in Morphling 3D. While generators create code and scaffolds, **Management Commands** handle orchestration: making sure Laravel recognizes your modules, registers their Service Providers and routes, and keeps the environment in sync.

---

## Executive Summary

Morphling 3D leverages a **Service Discovery** mechanism. Rather than manually adding every module Service Provider to `config/app.php`, the system uses management commands to reflect the contents of your `modules/` directory into Laravel's bootstrapping process.

> [!IMPORTANT]
> **Key Rule:** Always run `php artisan 3d:discover` after adding, renaming, or deleting a module (especially if done manually) to refresh Laravel’s internal providers map.

---

## The Management Toolkit

### 1. The Initializer
```bash
php artisan 3d:install
```
**Purpose:** Bootstrap your Morphling 3D environment. This command creates the `modules/` directory, scaffolds the `Shared` kernel, and publishes the core config (`config/3d.php`). Run it once per new project.

### 2. The Auditor
```bash
php artisan 3d:list
```
**Purpose:** Overview of all available modules. This command inspects the `modules/` directory and displays:
* **Registration Status:** Is the Service Provider recognized?
* **Routes:** Presence of `api.php` or `web.php` routes.
* **Views:** Whether a view namespace is registered.

### 3. The Synchronizer
```bash
php artisan 3d:discover
```
**Purpose:** Keeps Laravel in sync with your `modules/` directory. It scans for all `*ServiceProvider.php` files inside modules and updates Laravel’s service provider registry (e.g., in `bootstrap/providers.php` for Laravel 11+ or the relevant cache for earlier versions).

### 4. The Janitor
```bash
php artisan 3d:delete {ModuleName}
```
**Purpose:** Safely removes a module.
* **Cleanup:** Deletes the module’s folder and unregisters its Service Provider.
* **Safety:** Will not delete the foundational `Shared` module, preventing accidental system instability.

---

## Management Workflow: The "Safe Sync"

Whenever you bring your project onto a new machine or after significant module changes, use this sequence:

1. `composer install` (Installs project dependencies)
2. `php artisan 3d:discover` (Synchronizes module registration)
3. `php artisan route:list` (Sanity-check the endpoints are present)

---

## Troubleshooting

### "A module exists physically but isn't in `3d:list`"
**Solution:** Verify the folder name and its PSR-4 namespace match your `composer.json` autoload information. For example, a module in `Transaction` should contain classes like `Modules\Transaction\...`. Run `composer dump-autoload` if you rename modules.

### "I manually deleted a module and now Laravel errors at boot."
**Solution:** Laravel is trying to load a Service Provider that doesn’t exist anymore. Run `php artisan 3d:discover` to refresh the provider registry and clear dead links.

### "Can I temporarily disable a module without deleting it?"
**Solution:** Any valid folder inside `modules/` is treated as active. To disable one without deleting, move the folder out of `modules/` or rename its Service Provider (so it no longer matches `*ServiceProvider.php`)—it will be skipped by discovery on the next `3d:discover` run.
