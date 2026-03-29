# Quick Start Guide

> [!IMPORTANT]
> **Prerequisites:** Laravel 10.x or 11.x and PHP 8.2+. Ensure your `composer.json` is writable.

This guide provides the minimal steps to get **Morphling 3D** running in your environment. Follow these four steps to move from a standard MVC setup to a robust, DDD-ready modular architecture.

---

## 1. Installation

Install the engine via Composer and run the internal setup routine to publish configuration and core service providers.

```bash
# Install the package
composer require morphling-dev/3d

# Run the Morphling initializer
php artisan 3d:install
```

### PSR-4 Configuration
Morphling 3D expects a `Modules\` namespace. Verify your `composer.json` contains the following mapping to ensure the service discovery works correctly:

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

After updating, refresh the autoloader:
```bash
composer dump-autoload
```

---

## 2. Scaffold Your First Module

Morphling 3D uses a single command to generate the entire directory structure, including DTOs, Use Cases, Entities, and Repositories.

```bash
php artisan module:new Transaction
```

[Placeholder: Diagram of the generated 4-layer file structure]

---

## 3. Service Discovery

Morphling 3D features **Zero-Config Registration**. However, when you add new routes or providers within a module, you must trigger the discovery heart-beat to cache the modular map.

```bash
php artisan module:discover
```

---

## 4. Launch & Verify

Start your local development server to test the auto-generated endpoints.

```bash
php artisan serve
```

> [!NOTE]
> By default, the generator creates a sample route in `modules/Transaction/Delivery/Routes/api.php`. You can verify it by running `php artisan route:list`.

---

## Quick Reference: Commands

| Task | Command |
| :--- | :--- |
| **Install** | `php artisan 3d:install` |
| **New Module** | `php artisan module:new {Name}` |
| **Refresh Map** | `php artisan module:discover` |
| **Clear Cache** | `php artisan module:clear` |