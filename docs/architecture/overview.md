# Architecture Overview

Morphling 3D transforms your Laravel project into a scalable, maintainable **Domain-Driven Design (DDD)** and **Hexagonal Architecture** environment. Instead of organizing code by file type (like traditional MVC), it introduces a modular "feature-by-domain" structure—keeping codebases navigable as your system grows.

-----

## The 4-Layer Blueprint

Every Morphling 3D module is structured into four distinct layers. This enforces clean separation so core business rules ("The Domain") remain insulated from framework or persistence changes.

```text
modules/{ModuleName}/
├── Application/        # DTOs, UseCases — the orchestrator
├── Domain/             # Entities, Value Objects, Enums, Interfaces — the business brain
├── Infrastructure/     # Models, Repositories, Mappers — the implementor
└── Delivery/           # Controllers, Requests, Resources, Routes — the entry point (HTTP/UI/CLI)
```

_Note: Delivery is often presented last to highlight how user input reaches the system, but architecturally, request flow begins with Delivery and proceeds inward through Application, Domain, then out to Infrastructure._

-----

## Automated Scaffolding: What `3d:new` Builds

When you run `php artisan 3d:new {ModuleName}`, Morphling 3D does more than create folders—it generates a ready-to-use execution chain spanning all layers.

### The Delivery Layer

  * **Controller:** Handles user input, maps HTTP actions to UseCase classes.
  * **Routes:** Module routes (API/Web) are auto-registered.
  * **FormRequest:** Validates incoming request data.

### The Application Layer

  * **UseCase:** Coordinates a single application task (e.g., `CreateOrderUseCase`).
  * **DTO (Data Transfer Object):** Strongly-typed objects for safely carrying data across layers.

### The Domain Layer

  * **Entity:** Encapsulates core business concepts (e.g., `User`).
  * **Repository Interface:** Defines contracts for data access—domain stays unaware of implementations or SQL.

### The Infrastructure Layer

  * **Model:** Laravel Eloquent model, defines database interaction.
  * **Repository Implementation:** Fulfills domain contracts using Eloquent, APIs, or other infrastructure.

-----

## Core Advantages

### 1\. Separation of Concerns

The **Domain** layer is fully decoupled from **Infrastructure**. You can switch a data source (e.g., from MySQL to MongoDB or external APIs) without modifying your core business logic.

### 2\. Auto-Discovery

No manual registration: Morphling 3D automatically discovers and registers:

  * Service Providers
  * Routes (Web & API)
  * Blade View Namespaces
  * Database Migrations

### 3\. Consistent Developer Experience (DX)

Generated module views support **Deep-Linking:** browse to a module UI and links open corresponding code (Controller, UseCase, etc) directly in VSCode, Cursor, or PHPStorm.

-----

## Layer Navigation

| Layer | Focus | Key Question |
| :--- | :--- | :--- |
| [**Domain**](/architecture/domain.md) | Business Rules | *Is this a rule or logic of the business?* |
| [**Application**](/architecture/application.md) | Workflow/Use Cases | *How do we coordinate the task?* |
| [**Infrastructure**](/architecture/infrastructure.md) | Persistence/Integration | *How do we store or retrieve the data?* |
| [**Delivery**](/architecture/delivery.md) | User/System Interaction | *How does a user/system trigger this?* |

-----

## Next Steps

  - [Review the Request Lifecycle](/request-lifecycle.md) to see these layers in action.
  - [Read the Architectural Rules](/rules.md) to understand boundary constraints and best practices.
