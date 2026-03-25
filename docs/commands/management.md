# Management Commands

These commands manage the lifecycle of modules and the engine itself.

## Install

```bash
php artisan 3d:install
```

This creates the `modules/` base folder and sets up Shared Kernel templates.

## List Modules

```bash
php artisan module:list
```

Shows module folders and status such as provider registration and whether routes exist.

## Auto-Discovery Sync

```bash
php artisan module:discover
```

Runs the internal synchronization that ensures module service providers are registered in `bootstrap/providers.php`.

## Delete a Module

```bash
php artisan module:delete {name}
```

Deletes `modules/{name}/` and unregisters its service provider from `bootstrap/providers.php`.

