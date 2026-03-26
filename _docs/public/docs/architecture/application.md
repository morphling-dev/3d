# Application

The **Application** layer coordinates use cases. It usually:

- orchestrates domain operations
- translates input (DTOs) into domain-friendly shapes
- returns response models / DTO-ready outputs

## What Morphling 3D Generates

- `module:make-usecase` generates `Application/UseCases/*`
- `module:make-dto` generates `Application/DTOs/*`

## Example: Use Case Output Shape

The Use Case stub returns a structured result (excerpt):

```php
public function execute(mixed $dto = null): array
{
    return [
        'is_success' => true,
        'message'    => 'Execution successful for {{ module }}',
        'data'       => [
            'module' => '{{ module }}',
            'action' => '{{ class }}',
        ],
    ];
}
```

## Why this matters

Application Use Cases are the orchestration boundary:

- They translate incoming DTO data into Domain actions
- They coordinate Entities and repository interfaces without containing business rules themselves
- They provide a consistent output contract to Delivery

## How this connects to other layers

- **Delivery** validates input and builds DTOs, then calls the UseCase.
- **Domain** owns the business rules via Entities and repository contracts.
- **Infrastructure** implements repository interfaces (Eloquent repositories) and supplies persistence.

## Example: Use Case using DTO + repository

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

        $entity = $this->repository->findById($id);

        if ($entity === null) {
            return [
                'is_success' => false,
                'message' => 'Transaction not found',
                'data' => null,
            ];
        }

        return [
            'is_success' => true,
            'message' => 'Execution successful for Transaction',
            'data' => $entity->toArray(),
        ];
    }
}
```

## Navigation

- [Domain Responsibilities](#/architecture/domain)
- [Infrastructure Responsibilities](#/architecture/infrastructure)
- [Delivery Responsibilities](#/architecture/delivery)
