# Architecture Overview

Morphling 3D generates a **DDD 4-layer module** designed to keep business rules isolated and integration code replaceable.

Each module is created under:

```text
modules/{ModuleName}/
```

## The 4 Layers

```text
modules/
└── {ModuleName}/
    ├── Application/        # DTOs, UseCases
    ├── Domain/             # Entities, Value Objects, Enums, Interfaces
    ├── Infrastructure/     # Persistence & integrations
    └── Delivery/           # Controllers, Routes, Views
```

## What `module:new` Actually Builds

`module:new` orchestrates the module scaffolding by calling the layer-specific generators.
For example (excerpt):

```php
// Delivery Layer
$this->call('module:make-controller', ['name' => "{$module}Controller", 'module' => $module]);
$this->call('module:make-view', ['name' => 'index', 'module' => $module]);

// Application Layer
$this->call('module:make-usecase', ['name' => "Get{$module}ListUseCase", 'module' => $module]);
$this->call('module:make-dto', ['name' => "{$module}Dto", 'module' => $module]);

// Domain Layer
$this->call('module:make-entity', ['name' => "{$module}Entity", 'module' => $module]);
$this->call('module:make-interface', ['name' => "{$module}RepositoryInterface", 'module' => $module]);

// Infrastructure Layer
$this->call('module:make-model', ['name' => "{$module}Model", 'module' => $module]);
$this->call('module:make-repo', ['name' => "Eloquent{$module}Repository", 'module' => $module]);
```

## Why this matters

The architecture is enforced by the generator workflow:

- `module:new` creates artifacts across all layers in a known location.
- Auto-discovery registers module service providers so Delivery routes/views become reachable.
- The UseCase + DTO pattern keeps request handling and orchestration separate from Domain rules.

## How this connects to Morphling 3D features

- **Auto-Discovery**: module providers register routes and view namespaces for each module.
- **Deep-Linking**: generated `Delivery/Views/index.blade.php` provides editor links to the relevant module code.

For the runtime execution path, see [Request Lifecycle](../request-lifecycle.md).

## Navigation

- [Domain Responsibilities](#/architecture/domain)
- [Application Responsibilities](#/architecture/application)
- [Infrastructure Responsibilities](#/architecture/infrastructure)
- [Delivery Responsibilities](#/architecture/delivery)

