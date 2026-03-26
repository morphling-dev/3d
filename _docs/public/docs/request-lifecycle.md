# Request Lifecycle

This document walks through the complete request flow in a Morphling 3D module, following the engine’s 4-layer structure:

```text
Domain → Application → Infrastructure → Delivery
```

## Overview Diagram

```text
HTTP Request
  -> Delivery: Route -> Controller
       -> Controller creates DTO from validated request
       -> Application: UseCase::execute(DTO)
            -> Domain: Entities enforce business rules
            -> Domain: RepositoryInterface (read/write)
                 -> Infrastructure: Eloquent{Module}Repository (persistence)
                      -> Infrastructure: {Module}Model + (optional) Mapper
            <- Application result
  <- Delivery: Response (ApiResponse / View / Resource)
```

## Step-by-Step Request Flow

1. **HTTP Request** hits the module’s route file in `Delivery/Routes/`.
1. **Delivery Controller** type-hints a generated `FormRequest` to validate input data.
1. **Controller** constructs a module DTO using `{{Module}}Dto::fromRequest($request)`.
1. **Application UseCase** executes its logic via `execute(mixed $dto = null)`.
1. **Application UseCase** applies **Domain rules** by invoking relevant Entity methods (e.g., `rename()`).
1. **Application UseCase** reads or persists state through the **Domain repository interface**.
1. **Infrastructure Repository** handles actual persistence, using the generated Eloquent repository and model.
1. **Delivery Controller** returns the constructed response to the client.

## Real Example: Transaction Module

Here’s a minimal end-to-end sample, showing how Morphling 3D’s conventions are used in practice.

### Delivery: Controller (HTTP boundary)

```php
<?php

namespace Modules\Transaction\Delivery\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Shared\Infrastructure\Helpers\ApiResponse;
use Modules\Transaction\Application\DTOs\TransactionDto;
use Modules\Transaction\Application\UseCases\GetTransactionListUseCase;
use Modules\Transaction\Delivery\Requests\CreateTransactionRequest;

class TransactionController extends \App\Http\Controllers\Controller
{
    public function index(
        CreateTransactionRequest $request,
        GetTransactionListUseCase $useCase
    ): JsonResponse {
        $dto = TransactionDto::fromRequest($request);
        $result = $useCase->execute($dto);

        return ApiResponse::success(
            $result['data'] ?? $result,
            $result['message'] ?? 'Success'
        );
    }
}
```

### Application: UseCase (orchestration)

```php
<?php

namespace Modules\Transaction\Application\UseCases;

use Modules\Shared\Application\UseCases\BaseUseCase;
use Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;

class GetTransactionListUseCase extends BaseUseCase
{
    public function __construct(
        protected TransactionRepositoryInterface $repository
    ) {}

    public function execute(mixed $dto = null): array
    {
        $id = $dto->data['id'] ?? null;
        $name = $dto->data['name'] ?? null;

        $entity = $this->repository->findById($id);

        if ($entity === null) {
            return [
                'is_success' => false,
                'message' => 'Transaction not found',
                'data' => null,
            ];
        }

        // Domain business rule
        $entity->rename($name);

        // Persist through Domain repository interface
        $this->repository->save($entity);

        return [
            'is_success' => true,
            'message' => 'Execution successful for Transaction',
            'data' => [
                'id' => $entity->getId(),
                'name' => $entity->getName(),
            ],
        ];
    }
}
```

### Domain: Entity & Repository Interface (business contract)

The generated `TransactionEntity` encapsulates business logic (such as `rename()`), while `TransactionRepositoryInterface` defines what operations the UseCase requires.

### Infrastructure: Repository Implementation (persistence)

`EloquentTransactionRepository` implements `TransactionRepositoryInterface` and saves Domain Entities using the generated Eloquent model.

## Why This Matters

Morphling 3D enforces a clear, testable, and predictable request lifecycle:

- **Delivery**: Handles validation and responses.
- **Application**: Orchestrates use case logic.
- **Domain**: Defines business rules and contracts.
- **Infrastructure**: Manages persistence and integrations.

## Next Steps

- [First Module Tutorial](./first-module.md)
- [Strict Architectural Rules](./rules.md)
- [Layer Responsibilities](./architecture/overview.md)
