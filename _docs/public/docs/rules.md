# Architectural Rules

Morphling 3D uses a strict 4-layer module structure. The following rules help you maintain this separation in your projects.

## Rule 1: Domain cannot depend on Laravel

Domain classes—including Entities, Value Objects, Enums, Interfaces, and Services—must not depend on any of the following:

- Eloquent models
- HTTP layer classes (like `Request`, `Response`, or `FormRequest`)
- Laravel facades or helpers

Domain logic should be focused purely on business rules and state transitions. If you need anything framework-specific, move that logic to the Application or Infrastructure layer instead.

A practical note about generated repository interfaces: By default, Morphling 3D’s repository interface stubs may include `Illuminate` types for things like pagination or collections (e.g., `LengthAwarePaginator`, `Collection`) in their method signatures. If you require a Domain layer free from framework dependencies, remember to update the generated stubs and their corresponding Infrastructure implementations to use framework-agnostic return types (for example, arrays).

## Rule 2: Application cannot use Eloquent

Application classes—such as UseCases and DTOs—must not:

- perform Eloquent queries
- instantiate Eloquent models
- call any Eloquent-specific APIs

The Application layer should orchestrate Domain behavior using only repository interfaces and DTOs.

## Rule 3: Delivery cannot contain business logic

Delivery classes—such as Controllers, Requests, Routes, Resources, and Views—should be limited to:

- validating input (using generated `FormRequest` classes)
- translating input into DTOs
- returning output (using Views or `ApiResponse`)

Put all business rules in the Domain layer and all orchestration in the Application UseCases.

## Rule 4: Infrastructure implements Domain interfaces

Infrastructure classes must implement Domain repository interfaces and handle the technical details, including:

- persistence using generated Eloquent repositories/models
- mapping between persistence objects and Domain entities (if needed)
- integration logic (like providers, events, jobs, or external calls)

This approach makes Infrastructure replaceable without breaking Domain or Application contracts.

## Why these rules matter

These guidelines help keep Morphling 3D modules maintainable and robust:

- The Domain layer stays stable and easy to test.
- The Application layer stays focused on orchestrating use cases.
- The Infrastructure layer remains swappable (changes in persistence or integration won’t affect higher layers).
- The Delivery layer stays thin, predictable, and simple.

## Enforcement checklist

1. Domain code must not import from `Illuminate\\` or `App\\Http\\` (except for shared, framework-agnostic base primitives).
2. Application code must not import or reference Eloquent models or query builders.
3. Delivery code must not call repositories directly; always use UseCases.
4. Every Domain repository interface must have an Infrastructure implementation, and it should be bound in the module’s Service Provider.
