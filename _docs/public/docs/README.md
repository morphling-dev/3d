# Morphling 3D

Morphling 3D is a Laravel scaffolding engine that generates a clean, **Domain-Driven Design (DDD)**-style module structure for enterprise-scale applications.

Use Morphling 3D to eliminate repetitive boilerplate and accelerate your workflow, all while maintaining architectural correctness.

---

## Quick Links

- [Installation](#/installation)
- [Quick Start](#/quick-start)
- [First Module Tutorial](#/first-module)
- [Request Lifecycle](#/request-lifecycle)
- [Architectural Rules](#/rules)
- [Architecture Overview](#/architecture/overview)
- [Commands (Generators)](#/commands/generators)
- [Commands (Management)](#/commands/management)
- [Feature: Auto-Discovery](#/features/auto-discovery)
- [Feature: Deep-Linking](#/features/deep-linking)

---

## Why this matters

Traditional MVC-style CRUD scaffolding is great for quickly generating the first screen, but Morphling 3D is designed for **long-term changeability**:

- **True Separation of Concerns**
  - _Domain_: Business logic (Entities, Value Objects, Enums, Interfaces, Services)
  - _Application_: Application orchestration (Use Cases, DTOs)
  - _Infrastructure_: Persistence & integration (Models, Repositories, Mappers, Providers, Migrations)
  - _Delivery_: Interface layer (Controllers, Routes, Requests, Resources, Views)

- **Zero-Config Discovery**  
  Service providers, routes, and migrations inside your `modules/` folder are automatically registered using the package’s provider and autoload logic.

- **Data Integrity First**  
  All generator outputs include DTOs and mapping primitives to keep data transfer between layers controlled and safe.

- **Improved Developer Experience**  
  The generated `Delivery/Views/index.blade.php` file supports editor deep-linking for Cursor, VSCode, and PHPStorm.

---

## Generated Module Layout

Each module uses a consistent four-layer structure:

```text
modules/
└── {ModuleName}/
    ├── Application/        # DTOs, Use Cases (Application Services)
    ├── Domain/             # Entities, Value Objects, Enums, Interfaces
    ├── Infrastructure/     # Eloquent Models, Repositories, Mappers, Jobs
    └── Delivery/           # Controllers, Routes, Requests, Resources, Views
```

## How the layers connect

In Morphling 3D, the layers follow a strict execution flow:

- The Delivery layer receives the HTTP request and returns the response.
- The Application layer orchestrates the use case.
- The Domain layer enforces business rules through Entities.
- The Infrastructure layer performs persistence by implementing domain repository interfaces.

To see the complete end-to-end process, check the [Request Lifecycle](./request-lifecycle.md).

## Where to Start

- For the fastest setup: [Quick Start](./quick-start.md)
- For a full walkthrough: [First Module Tutorial](./first-module.md)
