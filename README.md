# Morphling 3D (Domain-Driven Design)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/morphling-dev/3d.svg?style=flat-square)](https://packagist.org/packages/morphling-dev/3d)
[![Total Downloads](https://img.shields.io/packagist/dt/morphling-dev/3d.svg?style=flat-square)](https://packagist.org/packages/morphling-dev/3d)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

**Morphling 3D** is an architectural framework for Laravel that enables you to build scalable, maintainable, and structured applications using **Domain-Driven Design (DDD)** and **Hexagonal Architecture**.

It is designed for systems where business complexity matters — such as ERP, financial systems, and multi-module enterprise applications.

---

## Why Morphling 3D Exists

As applications grow, traditional Laravel structures often lead to:

- Bloated controllers and services
- Mixed responsibilities across layers
- Difficult-to-maintain business logic
- Inconsistent patterns between modules

Morphling 3D addresses these issues by enforcing a clear architectural structure and predictable development flow.

---

## Core Principles

Morphling 3D is built around a few strict principles:

- **Separation of Concerns**
  Each layer has a single responsibility and must not leak into others.

- **Explicit Application Flow**
  Every request follows a clear path:
```

Request → DTO → UseCase → Domain → Repository

````

- **Domain-Centric Design**
Business rules live inside the Domain layer, not in controllers or infrastructure.

- **Consistency Across Modules**
Every module follows the same structure, making large systems easier to navigate and maintain.

---

## What Morphling 3D Provides

Morphling 3D is not just a generator. It provides a structured system for building applications:

- **Modular Architecture**
Each feature is isolated into its own module with clear boundaries.

- **Layer Enforcement**
Domain, Application, Infrastructure, and Delivery layers are clearly separated.

- **DTO and Mapper Pattern**
Ensures data integrity when moving across layers.

- **Auto Discovery**
Modules automatically register routes, providers, and migrations.

- **Shared Kernel**
Includes reusable components such as:
- `ApiResponse`
- `BaseUseCase`
- `BaseModel`
- `HttpStatus`

---

## Module Structure

Every module follows a consistent 4-layer architecture:

```text
modules/
└── {ModuleName}/
  ├── Application/        # DTOs, UseCases
  ├── Domain/             # Entities, Value Objects, Enums, Interfaces
  ├── Infrastructure/     # Models, Repositories, Mappers
  └── Delivery/           # Controllers, Requests, Resources, Routes
````

---

## Installation

Install via Composer:

```bash
composer require morphling-dev/3d
```

Run the installer:

```bash
php artisan 3d:install
```

Register the modules namespace:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Modules\\": "modules/"
    }
}
```

Then run:

```bash
composer dump-autoload
```

---

## Getting Started

* Quick Start: [docs/quick-start.md](docs/quick-start.md)
* First Module Tutorial: [docs/first-module.md](docs/first-module.md)
* Architectural Rules: [docs/rules.md](docs/rules.md)

---

## Artisan Commands

Morphling 3D provides a comprehensive set of Artisan commands to manage modules and generate structured code across all layers.

---

### Core Commands

Initialize and manage the Morphling 3D environment:

```bash
php artisan 3d:install
```

---

### Module Management

Create, inspect, and manage modules:

```bash
php artisan 3d:new {ModuleName}
php artisan 3d:delete {ModuleName}
php artisan 3d:list
php artisan 3d:discover
```

---

### Module Operations

Run module-specific operations such as migrations, routes, and testing:

```bash
php artisan 3d:migrate
php artisan 3d:seed
php artisan 3d:route:list
php artisan 3d:test
```

---

### Application Layer

Generate application-level components:

```bash
php artisan 3d:make-dto
php artisan 3d:make-usecase
```

---

### Domain Layer

Generate core business logic components:

```bash
php artisan 3d:make-entity
php artisan 3d:make-vo
php artisan 3d:make-enum
php artisan 3d:make-interface
php artisan 3d:make-service
```

---

### Infrastructure Layer

Generate technical and persistence-related components:

```bash
php artisan 3d:make-model
php artisan 3d:make-repo
php artisan 3d:make-mapper
php artisan 3d:make-migration
php artisan 3d:make-seeder
php artisan 3d:make-factory
php artisan 3d:make-event
php artisan 3d:make-listener
php artisan 3d:make-job
php artisan 3d:make-notification
php artisan 3d:make-observer
php artisan 3d:make-external
php artisan 3d:make-provider
```

---

### Delivery Layer

Generate interface and HTTP-related components:

```bash
php artisan 3d:make-controller
php artisan 3d:make-request
php artisan 3d:make-resource
php artisan 3d:make-route
php artisan 3d:make-view
```

---

### Command Philosophy

Morphling 3D commands are designed to enforce architectural consistency.
Each generated file follows predefined conventions aligned with the 4-layer architecture.

> Commands are not just code generators — they help ensure your application remains maintainable, scalable, and aligned with best practices.

---

## Configuration

You can customize base paths and namespaces in `config/3d.php`:

```php
return [
    'base_path' => base_path('modules'),
    'base_namespace' => 'Modules',

    'namespaces' => [
        'use-case'     => 'Application/UseCases',
        'entity'       => 'Domain/Entities',
        'value-object' => 'Domain/ValueObjects',
        'repository'   => 'Infrastructure/Repositories',
    ],
];
```

---

## When to Use Morphling 3D

Morphling 3D is best suited for:

* Enterprise applications
* ERP systems
* Financial systems with approval workflows
* Systems with complex business rules
* Multi-team development environments

It may be unnecessary for small CRUD-based projects.

---

## Links

* GitHub: [https://github.com/morphling-dev/3d](https://github.com/morphling-dev/3d)
* Documentation: [https://morphling-3d-docs.vercel.app/](https://morphling-3d-docs.vercel.app/)
* Issues: [https://github.com/morphling-dev/3d/issues](https://github.com/morphling-dev/3d/issues)

---

## Contribution

Contributions are welcome. Please open a Pull Request to the `main` branch.

---

## License

MIT License

---

Created by **Indra Ranuh** & **Morphling Coding**