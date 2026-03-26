# Delivery

The **Delivery** layer is your module’s interface boundary: controllers, routes, requests, resources, and views.

The goal is to keep HTTP/UI concerns out of your Domain rules.

## What Morphling 3D Generates

- `module:make-controller` generates `Delivery/Controllers/*`
- `module:make-request` generates `Delivery/Requests/*`
- `module:make-resource` generates `Delivery/Resources/*`
- `module:make-route` generates route files under `Delivery/Routes/*`
- `module:make-view` generates Blade views under `Delivery/Views/*`

## Example: Controller -> View Contract

The controller stub returns a namespaced view alias derived from the module name (excerpt):

```php
public function index(Request $request)
{
    return view('{{ module_snake }}::index');
}
```

## Why this matters

Delivery is the interface boundary. Keeping it thin ensures that:

- Controllers and Requests remain focused on input validation and output formatting
- business rules stay in Domain and orchestration stays in Application
- Delivery changes (UI routes, API response format) do not require Domain rewrites

## How this connects to other layers

- **Requests** (Delivery) validate input and provide validated data to DTO creation.
- **UseCases** (Application) encapsulate orchestration and return structured outputs.
- **Entities** (Domain) encapsulate business rules that UseCases call.
- **Repositories** (Infrastructure) implement Domain repository interfaces.

## Example: Controller calling a UseCase (API-style)

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

## Navigation

- [Domain Responsibilities](#/architecture/domain)
- [Application Responsibilities](#/architecture/application)
- [Infrastructure Responsibilities](#/architecture/infrastructure)

