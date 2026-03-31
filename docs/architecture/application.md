# The Application Layer: The Orchestrator

The **Application Layer** in Morphling 3D is responsible for orchestrating the flow between external requests (**Delivery layer**) and the business logic (**Domain layer**). It does not implement business rules itself, nor does it directly interact with data persistence mechanisms—those responsibilities belong to the Domain and Infrastructure layers, respectively.

---

## Executive Summary

In Morphling 3D, the Application layer defines **Use Cases**: each Use Case encapsulates exactly one business action in your system (such as `PlaceOrder`, `CancelSubscription`, or `UpdateProfile`). It is the layer where incoming requests are transformed into actions, and results are returned in a predictable format.

> [!NOTE]
> **Role:** Orchestration  
> **Dependencies:** References Domain and Infrastructure (repositories), but is not aware of HTTP or persistence details.

---

## Key Concepts: The "Use Case Orchestrator"

Think of the Application layer like a workflow manager: it accepts a *DTO* (Data Transfer Object) from the outside world, gathers dependencies (such as repository interfaces), delegates business rules to the *Domain*, and coordinates saving changes by calling *Repositories*. It then returns the result in a stable structure for the **Delivery** layer.

---

## What Morphling 3D Generates

Morphling 3D generates Application layer boilerplate to enforce this separation:

| Command                  | Generates                      | Purpose                                                         |
|--------------------------|------------------------------- |-----------------------------------------------------------------|
| `3d:make-usecase`        | `Application/UseCases/*`       | Implements business use case logic (one Use Case = one class).   |
| `3d:make-dto`            | `Application/DTOs/*`           | Strongly-typed data transfer between layers (not tied to HTTP).  |

---

## Technical Reference: Example Use Case

A typical Use Case follows a "Fetch → Act → Persist" flow. The Use Case calls on the repository (infrastructure interface) to load domain entities, calls entity methods to enforce business rules, then asks the repository to persist the result.

```php
// modules/Transaction/Application/UseCases/ProcessPaymentUseCase.php

class ProcessPaymentUseCase extends BaseUseCase
{
    public function __construct(
        protected TransactionRepositoryInterface $repository
    ) {}

    public function execute(mixed $dto = null): array
    {
        // 1. Extract data from the DTO (not from Request, never from Model)
        $transactionId = $dto->id;

        // 2. Use repository interface to fetch a domain entity
        $transaction = $this->repository->findById($transactionId);

        if (!$transaction) {
            return ['is_success' => false, 'message' => 'Not found'];
        }

        // 3. Call domain entity methods to enforce business rules
        $transaction->markAsPaid();

        // 4. Persist state through repository
        $this->repository->save($transaction);

        return [
            'is_success' => true,
            'message'    => 'Payment processed successfully',
            'data'       => $transaction->toArray()
        ];
    }
}
```

---

## Data Transfer Objects (DTOs)

DTOs are crucial in the Application layer: they provide a strict boundary and decouple Application code from HTTP or Console layer specifics. This way, the same Use Case can be invoked from controllers, CLI commands, or jobs.

```php
// Example: Create a DTO from an HTTP request in a controller
$dto = TransactionDto::fromRequest($request);

// Pass DTO into the use case
$useCase->execute($dto);
```

---

## Best Practices

* **Keep Application Layer Thin**: Any complex business rules or branching logic must be implemented in **Domain Entities** or **Domain Services**, not in Use Cases.
* **One Use Case, One Responsibility**: Do not combine unrelated operations (e.g., avoid "God Classes").
* **Stable Return Types**: Always return structured arrays or standard Result Objects (e.g., from `modules/Shared`) to maintain API consistency.

---

## Common Pitfalls and Solutions

### "Can I use Eloquent models in my Use Cases?"

**No.** Use Cases must depend only on repository interfaces (as defined in Domain), not on any infrastructure-specific classes (like Eloquent or database models). This ensures your logic is persistence-agnostic and easily testable/mocked.

### "My Use Case is getting too big!"

Refactor! If data transformation or mapping becomes complex, introduce a **Mapper** in the Infrastructure layer. If you need cross-entity or workflow logic, consider a **Domain Service**. The Application layer should only coordinate the steps, not implement the internals.

---
