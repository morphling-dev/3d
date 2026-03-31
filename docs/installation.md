# Installation Guide

> [!IMPORTANT]
> **Compatibility:** Morphling 3D requires **Laravel 10.x or 11.x** and **PHP 8.2+**. Ensure your environment matches these requirements before proceeding.

Morphling 3D upgrades a standard Laravel app into a modular, domain-driven system following clear boundaries. Its installation is non-destructive and scaffolds the necessary structure into a dedicated `modules/` directory without touching your existing business code.

---

## Overview

Installing Morphling 3D has three essential steps:
1.  **Require the package via Composer**
2.  **Bootstrap the modular system**
3.  **Register the PSR-4 namespace in Composer**

---

## 1. Install the Composer Package

Require Morphling 3D in your project:

```bash
composer require morphling-dev/3d
```

---

## 2. Bootstrap the Modular Skeleton

Run the installer to initialize Morphling's architecture. This will create the required directories and publish configuration.

```bash
php artisan 3d:install
```

### This command will:
- **Create the `modules/` folder:** The home of all your modules.
- **Scaffold Shared primitives:** Generates `modules/Shared`, including foundational classes like `BaseUseCase`, `ApiResponse`, and other reusable components.
- **Publish configuration:** Places the config file at `config/3d.php` for customization options.

---

## 3. Register the Module Namespace

Laravel needs to know about your `Modules\` classes. Add it to the `composer.json` autoload section with PSR-4. This allows Laravel to automatically discover and autoload your modules.

**Edit your `composer.json`:**

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Modules\\": "modules/"
    }
}
```

If you have additional entries (e.g., for database factories or seeders), keep them as usual.

**Then update the autoloader:**

```bash
composer dump-autoload
```

---

## 4. Configuration (Optional)

If you want the module folder under a different name (example: `src/` instead of `modules/`), edit `config/3d.php`:

| Parameter | Default                  | Description                                |
|-----------|--------------------------|--------------------------------------------|
| `base_path`      | `base_path('modules')` | Filesystem location for modules.           |
| `base_namespace` | `'Modules'`             | PSR-4 namespace root for all modules.      |

Update your `composer.json` autoload PSR-4 to match any customizations.

---

## 5. Verify Your Installation

Check if Morphling 3D is set up correctly:

```bash
# 1. Create a demo module named Sandbox
php artisan 3d:new Sandbox

# 2. Discover and register module routes/providers
php artisan 3d:discover

# 3. List registered modules
php artisan 3d:list
```

> [!NOTE]
> If you see `Sandbox` in the output, your environment is ready for modular development.

---

## Troubleshooting

### Class 'Modules\...' Not Found

**Possible Causes**
- You forgot to run `composer dump-autoload` after editing `composer.json`
- Typo or incorrect path in the PSR-4 block of your `composer.json`

**Solution**
- Double-check your `composer.json`, then rerun:

```bash
composer dump-autoload
```

---

### `php artisan 3d:install` Fails

**Possible Causes**
- Permissions issue, or
- Pre-existing (non-empty) `modules/` directory

**Solution**
- Ensure your user or web server has write permissions to your project root.
- Remove or empty any pre-existing `modules/` directory, if needed.

---

