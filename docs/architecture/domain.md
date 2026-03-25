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

## Navigation

- [Application Responsibilities](#/architecture/application)
- [Infrastructure Responsibilities](#/architecture/infrastructure)
- [Delivery Responsibilities](#/architecture/delivery)

