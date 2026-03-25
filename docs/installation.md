# Installation

## Composer Setup

```bash
composer require morphling-dev/3d
```

## Initialize the Modules Folder

Run:

```bash
php artisan 3d:install
```

This command prepares:

- `modules/` (base directory)
- Shared Kernel templates (under `modules/Shared`)

## Add PSR-4 Autoload for `Modules\\`

Update your app’s `composer.json`:

```json
"autoload": {
  "psr-4": {
    "Modules\\": "modules/"
  }
}
```

Then:

```bash
composer dump-autoload
```

## Optional: Customize Base Path & Namespace

The generator reads defaults from `config/3d.php` (merged by the package service provider). For example:

```php
'base_path' => base_path('modules'),
'base_namespace' => 'Modules',
```

