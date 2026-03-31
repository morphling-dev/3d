# Request Lifecycle

This guide explains the life of a request within a Morphling 3D module, clarifying how adherence to this unidirectional journey keeps your applications decoupled, maintainable, and testable.

---

## Executive Summary

Morphling 3D breaks a request's path into explicit layers—each with a defined responsibility:
* **Delivery:** Handles HTTP input, validation, and response logic.
* **Application:** Orchestrates workflow by coordinating use cases and DTOs.
* **Domain:** Contains core business rules and logic, isolated from framework and infrastructure.
* **Infrastructure:** Handles technical details like persistence, third-party APIs, and external services.

> [!NOTE]
> **Status:** `Core Architecture` | **Flow:** `Unidirectional (Outside-In)`

---

## The Request Journey

### 1. Entry Point: Delivery Layer

The request enters via routes defined at `modules/{Module}/Delivery/Routes/`.
* **Validation:** A `FormRequest` or custom request class validates input. If validation fails, a `422 Unprocessable Entity` is immediately returned.
* **Transformation:** The Controller translates the validated request into a DTO (**Data Transfer Object**), ensuring the inner layers never operate on HTTP objects.

### 2. Application Layer: Use Case Orchestration

Controllers call a UseCase’s `execute(DTO)` method.
* **Coordination:** The Use Case directs the workflow—fetching/creating Domain Entities, invoking their behaviors, and saving results via repositories. The Application layer should not contain core business logic.

### 3. Domain Layer: Business Logic

The Use Case triggers methods on Domain Entities or Domain Services.
* **Business Logic Only:** The domain layer is free from Laravel details, persistence concerns, or infrastructure dependencies. E.g., methods like `calculateTax()` or rules like `isEligibleForDiscount()` are enforced here.

### 4. Infrastructure Layer: Persistence & External Services

The Application layer relies on interfaces to communicate with infrastructure.
* **Implementation:** Typically, repositories (e.g., `EloquentRepository`) implement domain interfaces, handling ORM mapping, SQL queries, or external communication. Mappers ensure conversion between domain objects and persistence models.

---

## Code Implementation: Typical Example

### [Layer 1] Delivery: Controller

Defines HTTP boundaries and performs initial orchestration.

```php
// modules/Transaction/Delivery/Controllers/TransactionController.php
public function update(UpdateTransactionRequest $request, RenameTransactionUseCase $useCase): JsonResponse
{
    // Step 1: Transform HTTP request to DTO
    $dto = TransactionDto::fromRequest($request);

    // Step 2: Pass DTO to Application Layer
    $result = $useCase->execute($dto);

    return ApiResponse::success($result['data'], $result['message']);
}
```

### [Layer 2] Application: Use Case

Coordinates domain behavior without embedding business rules or infrastructure calls directly.

```php
// modules/Transaction/Application/UseCases/RenameTransactionUseCase.php
public function execute(mixed $dto = null): array
{
    // Step 1: Use repository interface to fetch the Entity
    $entity = $this->repository->findById($dto->id);

    // Step 2: Invoke Domain behavior
    $entity->rename($dto->name);

    // Step 3: Save changes through Repository
    $this->repository->save($entity);

    return [
        'is_success' => true,
        'message' => 'Renamed!',
        'data' => $entity->toArray()
    ];
}
```

---

## Comparison: Traditional MVC vs. Morphling 3D

| Feature            | Standard Laravel (MVC)      | Morphling 3D (DDD)                          |
| :----------------- | :------------------------- | :------------------------------------------ |
| Logic Location     | Controllers & Models        | Domain Entities & Services                  |
| Data Transfer      | Mixing raw `$request`/arrays| Explicit DTO classes                        |
| DB Interaction     | Eloquent everywhere         | Only through injected Repositories          |
| Testability        | Hard (DB/HTTP-bound tests)  | Easy (Domain & Use Cases are unit-testable) |

---

## Troubleshooting the Lifecycle

### "Why is my Repository returning null?"

> [!WARNING]
> Inspect your **Infrastructure Mappers**. When the database returns a result but the Repository returns null, ensure your Mapper correctly transforms database models to Domain Entities.

### "Can I call a Use Case from another Use Case?"

While technically possible, it's not recommended. Prefer flat Use Cases. If logic needs to be reused, refactor it into a **Domain Service** or a **Domain Entity** method. Avoid chaining Use Cases to preserve independence and clarity between flows.
