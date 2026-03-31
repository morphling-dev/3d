# Quick Start Guide

> **Prerequisites:** Laravel 10.x or 11.x and PHP 8.2+. Make sure your `composer.json` is writable.

This quick start will walk you through the minimal steps required to set up **Morphling 3D** and transition from a standard Laravel MVC structure to a robust, Domain-Driven Design modular architecture.

---

## 1. Installation

Install Morphling 3D via Composer, then run the installer to publish configuration and bootstrapping files.

```bash
# Install the package
composer require morphling-dev/3d

# Run the Morphling 3D installer
php artisan 3d:install
```

### PSR-4 Autoload Section

Morphling 3D works best when your modules are autoloaded under the `Modules\` namespace. Update your `composer.json` to include the following (add `"Modules\\": "modules/"`):

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Modules\\": "modules/"
    }
}
```

Then, regenerate the autoloader with:

```bash
composer dump-autoload
```

---

## 2. Create Your First Module

Generate a new module with Morphling 3D’s dedicated Artisan command (replace `Order` with your module name):

```bash
php artisan 3d:new Order
```

This will scaffold the full four-layer structure for the new module inside `modules/Order`:

```
modules/
└── Order/
    ├── Application/
    ├── Domain/
    ├── Infrastructure/
    └── Delivery/
```

---

## 3. Module Discovery

Morphling 3D automatically manages module registration. However, if you add or remove routes, providers, or modules, run the discovery command to refresh the modular map:

```bash
php artisan 3d:discover
```

---

## 4. Verify Everything Works

Start the local development server:

```bash
php artisan serve
```

By default, each new module includes a sample route in `modules/Order/Delivery/Routes/api.php`. You can view all registered routes with:

```bash
php artisan route:list
```

---

## Quick Reference: Core Commands

| Task             | Command                                  |
| :--------------- | :--------------------------------------- |
| **Install**      | `php artisan 3d:install`                 |
| **New Module**   | `php artisan 3d:new {ModuleName}`        |
| **Discover**     | `php artisan 3d:discover`                |
| **Delete Module**| `php artisan 3d:delete {ModuleName}`     |
| **List Modules** | `php artisan 3d:list`                    |
| **Route List**   | `php artisan 3d:route:list`              |

---

You’re ready to build enterprise-grade, modular Laravel apps with Morphling 3D!