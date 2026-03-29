# Installation Guide

> [!IMPORTANT]
> **Compatibility:** Morphling 3D requires **Laravel 10.x/11.x** and **PHP 8.2+**. Ensure your environment meets these requirements before proceeding.

Morphling 3D transforms a standard Laravel installation into a modular, DDD-compliant powerhouse. The installation process is designed to be non-destructive, injecting the necessary scaffolding into a dedicated `modules/` directory.

---

## Executive Summary
Setting up Morphling 3D involves three main phases:
1.  **Dependency Injection:** Pulling the core engine via Composer.
2.  **Structural Bootstrapping:** Initializing the `modules/` root and shared primitives.
3.  **Namespace Registration:** Mapping the new directory to the Laravel autoloader.

---

## 1. Package Installation

Begin by adding the package to your development dependencies.

```bash
composer require morphling-dev/3d
```

---

## 2. Bootstrapping the Engine

Initialize the modular architecture. This command creates the necessary filesystem structure and publishes the core configuration.

```bash
php artisan 3d:install
```

### What happens under the hood?
* **Directory Creation:** Generates the `modules/` root folder.
* **Shared Layer:** Scaffolds `modules/Shared`, containing base classes like `BaseUseCase` and `ApiResponse`.
* **Configuration:** Publishes `config/3d.php` to your application's config directory.

---

## 3. Registering the Namespace (Critical)

For Laravel to "see" your modules, you must register the `Modules\` namespace in your `composer.json` file. This is a manual step required by PHP's PSR-4 standard.

### Edit `composer.json`
Add the following line to your `autoload` section:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Modules\\": "modules/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/"
    }
}
```

### Refresh the Autoloader
After saving the file, you **must** regenerate the symlinks:

```bash
composer dump-autoload
```

---

## 4. Configuration (Optional)

If your project requires a custom directory name (e.g., `src/` instead of `modules/`), modify `config/3d.php`:

| Parameter | Default | Description |
| :--- | :--- | :--- |
| `base_path` | `base_path('modules')` | The physical location of your modules. |
| `base_namespace` | `'Modules'` | The root PSR-4 namespace for all modules. |

---

## 5. Verification & First Run

To confirm the installation is successful, run the following verification sequence:

```bash
# 1. Generate a test module
php artisan module:new Sandbox

# 2. Register the module routes and providers
php artisan module:discover

# 3. List active modules
php artisan module:list
```

> [!NOTE]
> If `Sandbox` appears in the list, your architecture is correctly wired.

---

## Troubleshooting

### Class 'Modules\...' Not Found
**Cause:** Usually a missing `composer dump-autoload` or a typo in the `composer.json` PSR-4 block.
**Fix:** Verify the path in `composer.json` and run the dump command again.

### 3d:install Fails
**Cause:** Permission issues or an existing `modules/` directory that isn't empty.
**Fix:** Ensure the web server/user has write permissions to the root directory.
