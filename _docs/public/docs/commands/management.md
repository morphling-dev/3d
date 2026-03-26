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

## Why this matters

Module lifecycle commands keep your runtime wiring consistent with your code:

- new modules become reachable via Delivery routes/views
- removed modules do not leave stale providers behind
- scaffolding remains discoverable after deployments and configuration changes

## How this connects to the request lifecycle

The request flow depends on Delivery being registered:

`HTTP request -> Delivery routes/controllers -> UseCase -> Domain -> Repository (Infrastructure) -> Response`

`php artisan module:discover` is what makes Delivery routes and views reachable at runtime.

## Notes and safe usage

- The `Shared` module is protected by design and cannot be deleted by `module:delete`.

