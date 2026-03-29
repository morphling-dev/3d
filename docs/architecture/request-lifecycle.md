# Request Lifecycle

This guide breaks down the "Path of a Request" within a Morphling 3D module. By following this strict unidirectional flow, your application remains decoupled, testable, and resistant to architectural decay.

---

## Executive Summary
In Morphling 3D, a request is like a traveler passing through a series of checkpoints. Each checkpoint (Layer) has a specific authority:
* **Delivery** checks your ID (Validation).
* **Application** decides which room you go to (Orchestration).
* **Domain** enforces the rules of the house (Business Logic).
* **Infrastructure** records your visit in the logbook (Persistence).

> [!NOTE]
> **Status:** `Core Architecture` | **Flow:** `Unidirectional (Outside-In)`

---

## The Request Journey



### 1. The Entry Point (Delivery)
The request enters through a route defined in `modules/{Module}/Delivery/Routes/`. 
* **The Guard:** A `FormRequest` validates the payload. If validation fails, the journey ends here with a `422 Unprocessable Entity`.
* **The Translator:** The Controller converts the raw `$request` into a **DTO (Data Transfer Object)**. This ensures the inner layers never "see" an HTTP object.

### 2. The Director (Application)
The Controller invokes the `UseCase::execute(DTO)`. 
* **The Orchestrator:** The Use Case coordinates the activity. It asks the **Repository** to find an **Entity**, tells the Entity to perform an action, and then tells the Repository to save the result.

### 3. The Heart (Domain)
The Use Case triggers methods on a **Domain Entity**. 
* **The Rulebook:** This is where pure business logic lives (e.g., `calculateTax()`, `isEligibleForDiscount()`). It has zero knowledge of Laravel, databases, or JSON.

### 4. The Foundation (Infrastructure)
The Application layer communicates with Infrastructure via **Interfaces**.
* **The Worker:** The `EloquentRepository` implements the Domain's Interface. It handles the "dirty work" of SQL queries and Eloquent model mapping.

---

## Code Implementation: A Real-World View

### [Layer 1] Delivery: Controller
The boundary between the web and your logic.

```php
// modules/Transaction/Delivery/Controllers/TransactionController.php
public function update(UpdateTransactionRequest $request, RenameTransactionUseCase $useCase): JsonResponse 
{
    // Step: Request -> DTO
    $dto = TransactionDto::fromRequest($request);
    
    // Step: Hand off to Application Layer
    $result = $useCase->execute($dto);

    return ApiResponse::success($result['data'], $result['message']);
}
```

### [Layer 2] Application: Use Case
The workflow coordinator.

```php
// modules/Transaction/Application/UseCases/RenameTransactionUseCase.php
public function execute(mixed $dto = null): array
{
    // Step: Dependency Injection of the Interface
    $entity = $this->repository->findById($dto->id);

    // Step: Trigger Domain Rule
    $entity->rename($dto->name);

    // Step: Persist via Infrastructure
    $this->repository->save($entity);

    return ['is_success' => true, 'message' => 'Renamed!', 'data' => $entity->toArray()];
}
```

---

## Comparison: MVC vs. Morphling 3D

| Feature | Standard Laravel (MVC) | Morphling 3D (DDD) |
| :--- | :--- | :--- |
| **Logic Location** | Often in Controller or Model. | Strictly in **Domain Entities**. |
| **Data Passing** | Raw `$request` or `$array`. | Type-safe **DTOs**. |
| **DB Interaction** | Direct Eloquent calls everywhere. | Abstracted via **Repositories**. |
| **Testability** | Hard (requires database/HTTP). | Easy (Unit test the Domain/Use Case). |

---

## Troubleshooting the Lifecycle

### "Why is my Repository returning null?"
> [!WARNING]
> Check your **Infrastructure Mappers**. If your Eloquent Model exists but the Repository returns null, the Mapper might be failing to convert the Database Row into a Domain Entity.

### "Can I call a Use Case from another Use Case?"
Technically yes, but it is a **Best Practice** to keep Use Cases "flat." If you need shared logic, move that logic into a **Domain Service** or a **Domain Entity** instead of nesting Use Cases.
