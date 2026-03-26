# Installation

## 1. Install the Package

Install Morphling 3D using Composer:

```bash
composer require morphling-dev/3d
```

## 2. Initialize the Modules Directory

Set up the base `modules/` folder and shared kernel files by running:

```bash
php artisan 3d:install
```

This creates:

- The base `modules/` directory
- Shared kernel templates in `modules/Shared`

## 3. Configure PSR-4 Autoloading

Add your modules namespace to your project's `composer.json` file under the `autoload.psr-4` key:

```json
"autoload": {
  "psr-4": {
    "Modules\\": "modules/"
  }
}
```

After updating, regenerate the autoload files:

```bash
composer dump-autoload
```

## 4. (Optional) Customize Base Path & Namespace

You can adjust the base modules path and root namespace in the `config/3d.php` file if needed. By default, the settings are:

```php
'base_path' => base_path('modules'),
'base_namespace' => 'Modules',
```

## Why These Steps Matter

Morphling 3D is more than a folder generator:

- The `php artisan 3d:install` command installs foundational utilities like `BaseUseCase` and `ApiResponse` under `modules/Shared`.
- PSR-4 autoloading makes sure Laravel and Composer can discover and use all your generated module classes automatically.

Without performing both the install and autoload steps, your modules may not be recognized, even if the file structure is present.

## How This Integrates with Each Layer

- **Application:** Generated UseCases extend `Modules\Shared\Application\UseCases\BaseUseCase`
- **Delivery:** Responses make use of `Modules\Shared\Infrastructure\Helpers\ApiResponse`
- **Infrastructure:** Module providers depend on unified paths and namespaces to automatically register important module components like routes and views

## 5. Verify Your Setup

After installation and configuration, create a module and verify everything is working as expected:

```bash
php artisan module:new Transaction
php artisan module:discover
php artisan module:list
```
