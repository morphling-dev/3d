# Morphling 3D

Morphling 3D is a Laravel scaffolding engine that generates a clean **Domain-Driven Design (DDD)** module structure for enterprise-scale applications.

Use it when you want to stop writing repetitive boilerplate and instead move faster with architectural correctness built in.

---

## Quick Links

- [Installation](#/installation)
- [Architecture Overview](#/architecture/overview)
- [Commands (Generators)](#/commands/generators)
- [Commands (Management)](#/commands/management)
- [Feature: Auto-Discovery](#/features/auto-discovery)
- [Feature: Deep-Linking](#/features/deep-linking)

---

## Why Morphling 3D?

MVC-style CRUD scaffolding optimizes for speed of the first screen. In contrast, Morphling 3D optimizes for **long-term changeability**:

- **True Separation of Concerns**
  - Domain: business rules (Entities, Value Objects, Enums, Interfaces, Services)
  - Application: orchestration (Use Cases, DTOs)
  - Infrastructure: persistence & integration (Models, Repositories, Mappers, Providers, Migrations)
  - Delivery: the interface layer (Controllers, Routes, Requests, Resources, Views)

- **Zero-Config Discovery**
  Service providers, routes, and migrations inside your `modules/` are automatically wired through the package’s provider + autoload logic.

- **Data Integrity First**
  Generator outputs DTOs and mapping primitives so data transfer remains controlled between layers.

- **Better DX**
  The generated `Delivery/Views/index.blade.php` includes editor deep-linking for Cursor / VSCode / PHPStorm.

---

## Generated Module Layout

Every module follows a consistent 4-layer structure:

```text
modules/
└── {ModuleName}/
    ├── Application/        # DTOs, UseCases (Application Services)
    ├── Domain/             # Entities, Value Objects, Enums, Interfaces
    ├── Infrastructure/     # Eloquent Models, Repositories, Mappers, Jobs
    └── Delivery/           # Controllers, Routes, Requests, Resources, Views
```

