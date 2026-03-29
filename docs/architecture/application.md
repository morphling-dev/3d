# The Application Layer: The Orchestrator

The **Application Layer** serves as the "Director" of your module. It doesn't perform business calculations itself, and it doesn't know how to save data to a database. Instead, it coordinates the movement of data between the outside world (**Delivery**) and the internal logic (**Domain**).

---

## Executive Summary
In Morphling 3D, the Application layer is responsible for **Use Cases**. A Use Case represents a single, specific action a user can take (e.g., `PlaceOrder`, `CancelSubscription`, `UpdateProfile`). It is the primary boundary where technical requirements meet business rules.

> [!NOTE]
> **Status:** `Orchestration` | **Dependency:** `Points to Domain`

---

## Key Concepts: The "Workflow Manager"
Think of the Application layer as a project manager. It takes a task (The DTO), gathers the necessary staff (The Repository), tells them what rules to follow (The Entity), and reports the final result back to the client.



---

## What Morphling 3D Generates

Morphling 3D streamlines the creation of these "Directors" and their "Instructions":

| Command | Generates | Purpose |
| :--- | :--- | :--- |
| `module:make-usecase` | `Application/UseCases/*` | The logic for a single specific business action. |
| `module:make-dto` | `Application/DTOs/*` | A type-safe container for data passing between layers. |

---

## Technical Reference: The Use Case

A Use Case typically follows a "Fetch -> Execute -> Save" pattern.

```php
### modules/Transaction/Application/UseCases/ProcessPaymentUseCase.php

class ProcessPaymentUseCase extends BaseUseCase
{
    public function __construct(
        protected TransactionRepositoryInterface $repository
    ) {}

    public function execute(mixed $dto = null): array
    {
        // 1. Extract data from the type-safe DTO
        $transactionId = $dto->id;

        // 2. Fetch the Entity through the Domain Interface
        $transaction = $this->repository->findById($transactionId);

        if (!$transaction) {
            return ['is_success' => false, 'message' => 'Not found'];
        }

        // 3. Trigger a Business Rule inside the Domain Entity
        $transaction->markAsPaid();

        // 4. Tell the Repository to persist the changed state
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

DTOs are critical in Morphling 3D because they prevent the Application layer from depending on HTTP `Request` objects. This allows you to run the same Use Case from a Web Controller, a CLI command, or a background Job.

```php
// Creating a DTO in the Controller
$dto = TransactionDto::fromRequest($request);

// Passing it to the Use Case
$useCase->execute($dto);
```

---

## Best Practices: The "Clean Orchestrator"

* **Keep it Thin:** If you see complex `if/else` logic regarding business rules, move that logic into the **Domain Entity**.
* **One Use Case, One Job:** Avoid "God Use Cases." If a class is handling both `Create` and `Delete`, split them into two separate files.
* **Result Consistency:** Always return a structured array or a standard Result Object (like the one provided in `modules/Shared`) to keep the **Delivery** layer predictable.

---

## Troubleshooting

### "Why can't I just use the Eloquent Model here?"
If you use Eloquent directly in the Use Case, your application logic becomes "coupled" to the database. By using the `RepositoryInterface`, you can swap the database or mock it during testing without breaking the Use Case.

### "My Use Case is getting too large."
Check if you are doing too much "plumbing." Consider moving complex data mapping into a **Mapper** (Infrastructure) or complex multi-entity logic into a **Domain Service**.