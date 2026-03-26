# Domain

The **Domain** layer is where your business rules live. It should be:

- expressive (naming reflects intent)
- stable (less coupled to frameworks)
- testable (pure business logic)

## What Morphling 3D Generates

- `module:make-entity` generates `Domain/Entities/*`
- `module:make-vo` generates `Domain/ValueObjects/*`
- `module:make-enum` generates `Domain/Enums/*`
- `module:make-interface` generates `Domain/Interfaces/*`
- `module:make-service` generates `Domain/Services/*`

## Example: Entity Business Rules

The entity stub includes domain-centric methods like validation and state transitions (excerpt):

```php
public function rename(string $name): void
{
    // Example of basic business logic: name must not be empty
    if (trim($name) === '') {
        throw new \InvalidArgumentException('Name cannot be empty.');
    }
    $this->name = $name;
}
```

## Why this matters

Domain is the part of your system that should remain stable as your Laravel concerns evolve. By keeping business rules inside Entities (and other Domain primitives like Value Objects and Enums), you get:

- Predictable business behavior across Delivery and Infrastructure changes
- Easy unit testing of business rules without HTTP or database dependencies

## How this connects to other layers

- **Application (Use Cases)** invokes Entities to enforce rules and calls Domain repository interfaces for persistence.
- **Infrastructure (Repositories / Mappers)** implements those Domain interfaces using Eloquent models.
- **Delivery (Controllers / Requests)** converts HTTP input into DTOs and returns output; it must not contain business rule logic.

## Repository interfaces in the Domain layer

Morphling 3D generates module repository interfaces under `Domain/Repositories/`. The UseCase depends on the interface, not the Eloquent implementation.

Example shape (excerpt):

```php
interface TransactionRepositoryInterface
{
    public function findById(int|string $id): ?TransactionEntity;
    public function all(): array;
    public function save(TransactionEntity $entity): bool;
}
```

## Navigation

- [Application Responsibilities](#/architecture/application)
- [Infrastructure Responsibilities](#/architecture/infrastructure)
- [Delivery Responsibilities](#/architecture/delivery)

