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

## Navigation

- [Domain Responsibilities](#/architecture/domain)
- [Application Responsibilities](#/architecture/application)
- [Infrastructure Responsibilities](#/architecture/infrastructure)
- [Delivery Responsibilities](#/architecture/delivery)

