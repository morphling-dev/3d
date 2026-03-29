# The Domain Layer: The Heart of the System

The **Domain Layer** is the most critical part of a Morphling 3D module. It contains the "Reason for Being" for your software—the business rules, logic, and state that would remain true even if you switched from a web app to a mobile app or changed your database entirely.

---

## Executive Summary
In Morphling 3D, the Domain is a **"Protected Kingdom."** It sits at the center of the architecture, and all other layers (Delivery, Application, Infrastructure) must point toward it. It knows nothing about Laravel, SQL, or JSON.

> [!NOTE]
> **Status:** `Pure Logic` | **Dependency:** `None (Inward-Facing)`

---

## Key Concepts: The "Pure Truth"
The Domain layer is where you translate human business requirements into code. If a stakeholder says, *"A transaction cannot be processed if the balance is negative,"* that logic belongs in a **Domain Entity**, not a Controller.



---

## What Morphling 3D Generates

Morphling 3D provides specific generators to ensure your Domain is expressive and granular:

| Command | Generates | Purpose |
| :--- | :--- | :--- |
| `module:make-entity` | `Domain/Entities/*` | Objects with a unique identity (e.g., a specific Order). |
| `module:make-vo` | `Domain/ValueObjects/*` | Objects defined by their attributes (e.g., an Email or Price). |
| `module:make-enum` | `Domain/Enums/*` | Strict sets of states (e.g., `Status::PENDING`). |
| `module:make-interface` | `Domain/Interfaces/*` | Contracts that Infrastructure must follow. |
| `module:make-service` | `Domain/Services/*` | Logic that involves multiple Entities. |

---

## Technical Reference: The Domain Entity

Unlike an Eloquent Model, a **Domain Entity** is a "Plain Old PHP Object" (POPO). It focuses on behavior, not database columns.

```php
### modules/Transaction/Domain/Entities/TransactionEntity.php

class TransactionEntity 
{
    private string $id;
    private string $status;

    public function __construct(string $id, string $status) {
        $this->id = $id;
        $this->status = $status;
    }

    /**
     * Business Rule: A transaction can only be cancelled if it is 'pending'.
     */
    public function cancel(): void
    {
        if ($this->status !== 'pending') {
            throw new \DomainException("Cannot cancel a transaction that is already {$this->status}.");
        }
        $this->status = 'cancelled';
    }
}
```

---

## Repository Interfaces: The Contract

The Domain layer defines **how** it wants to be saved, but it doesn't care **where**. It creates an `Interface` that the Infrastructure layer must implement.

```php
### modules/Transaction/Domain/Repositories/TransactionRepositoryInterface.php

interface TransactionRepositoryInterface
{
    public function findById(string $id): ?TransactionEntity;
    public function save(TransactionEntity $entity): void;
}
```

---

## Best Practices: The "Pure Domain" Test

To ensure your Domain layer is healthy, ask yourself these three questions:
1.  **Can I run a unit test on this Entity without starting Laravel?** (Should be Yes).
2.  **Does this file import any `Illuminate\...` classes?** (Should be No, except for rare primitives).
3.  **If we changed from MySQL to a CSV file, would this file change?** (Should be No).

---

## Troubleshooting

### "Where do I put my validation?"
* **Input Validation** (Is this an email?) belongs in the **Delivery Layer** (`FormRequest`).
* **Business Validation** (Is this user allowed to spend this much?) belongs here in the **Domain Layer**.

### "My Entity is missing the `save()` method!"
That is intentional. Entities should not save themselves (Active Record pattern). Instead, pass the Entity to a **Repository** within a **Use Case**.
