# The Domain Layer: The Heart of Your Application

The **Domain Layer** is the core of a Morphling 3D module. It expresses your application's business concepts, logic, and rules—the essential truths that would remain the same even if you changed frameworks, UIs, or databases.

---

## Domain Layer in Morphling 3D: At the Core

In Morphling 3D, the Domain is the **centerpiece**. All other layers (Application, Infrastructure, Delivery) are structured around and point toward it. The Domain layer is completely decoupled—it knows nothing of Laravel, HTTP, storage, or any specific technology.

> [!NOTE]
> **Status:** `Pure Logic` | **Dependencies:** `None (Inward-Facing Only)`

---

## Key Principles

The Domain layer is where **business rules** live, translated directly from requirements and language shared with stakeholders. For example, if your business rule says, *"A transaction cannot be processed if the balance is negative,"* that logic should exist in a **Domain Entity**—not in a controller or service outside the Domain.

---

## Domain Artifacts: What Morphling 3D Provides

Morphling 3D helps you create a well-structured Domain with dedicated generators:

| Command | Generates | Purpose |
| :--- | :--- | :--- |
| `3d:make-entity` | `Domain/Entities/*` | Unique, identifiable business objects (e.g., `Order`, `User`). |
| `3d:make-vo` | `Domain/ValueObjects/*` | Attribute-defined objects (e.g., `Email`, `Money`). |
| `3d:make-enum` | `Domain/Enums/*` | Strict sets of possible states (e.g., `Status::PENDING`). |
| `3d:make-interface` | `Domain/Interfaces/*` | Contracts for repositories/services. |
| `3d:make-service` | `Domain/Services/*` | Domain logic involving multiple entities or coordination. |

---

## Example: A Domain Entity

A **Domain Entity** is a "Plain Old PHP Object" (POPO)—it models behavior and identity, not database columns or framework details.

```php
// modules/Transaction/Domain/Entities/TransactionEntity.php

class TransactionEntity
{
    private string $id;
    private string $status;

    public function __construct(string $id, string $status)
    {
        $this->id = $id;
        $this->status = $status;
    }

    /**
     * Business Rule: A transaction can only be cancelled if it is 'pending'.
     */
    public function cancel(): void
    {
        if ($this->status !== 'pending') {
            throw new \DomainException("Cannot cancel a transaction in status: {$this->status}.");
        }
        $this->status = 'cancelled';
    }
}
```

---

## Repository Interfaces: Domain Contracts

Repositories are defined as **interfaces** in the Domain. These describe what persistence operations the Domain expects. The Infrastructure layer provides concrete implementations—but the Domain never depends on them.

```php
// modules/Transaction/Domain/Repositories/TransactionRepositoryInterface.php

interface TransactionRepositoryInterface
{
    public function findById(string $id): ?TransactionEntity;
    public function save(TransactionEntity $entity): void;
}
```

---

## Domain Self-Check: "Pure Domain" Test

Ensure your Domain layer remains clean and decoupled by answering:

1. **Can I unit test this Entity without booting Laravel or any framework?** (Yes)
2. **Does this file import any `Illuminate\...` or framework classes?** (No)
3. **Would this file need to change if you switched your storage from MySQL to files, or even in-memory arrays?** (No)

---

## FAQ & Troubleshooting

### Where does validation belong?
- **Input Validation** (e.g., is this an email address?) goes in the **Delivery Layer** (`FormRequest` or input DTOs).
- **Business Validation** (e.g., can this user withdraw this amount?) belongs in the **Domain Layer** (Entities or Services).

### Why don’t Entities have a `save()` method?
Entities in the Domain layer do **not** save themselves (they are not Active Record models). Persist them using a **Repository** via a **Use Case** in the Application layer. This keeps persistence infrastructure outside of your core domain logic.
