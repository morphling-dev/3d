# First Module Tutorial

This tutorial guides you step-by-step through creating your first Morphling 3D module, from module creation all the way to a working end-to-end flow.

The process covers: `module:new` → Entity / DTO / UseCase → Controller integration → Working module flow.

## Prerequisites

1. **Install Morphling 3D:**

   ```bash
   composer require morphling-dev/3d
   php artisan 3d:install
   ```

2. **Configure Autoloading:**

   Make sure your project’s `composer.json` is set up for PSR-4 autoloading with `Modules\\` (see `docs/installation.md`). Then run:

   ```bash
   composer dump-autoload
   ```

## Step 1: Create a new module

Generate the foundational structure for your module with:

```bash
php artisan module:new Transaction
```

This command scaffolds a complete 4-layer module under:

`modules/Transaction/`

You’ll see several generated files, including (by default):

- Delivery: `Delivery/Controllers/TransactionController.php`
- Delivery: `Delivery/Requests/CreateTransactionRequest.php`
- Application: `Application/DTOs/TransactionDto.php`
- Application: `Application/UseCases/GetTransactionListUseCase.php`
- Domain: `Domain/Entities/TransactionEntity.php`
- Domain: `Domain/Repositories/TransactionRepositoryInterface.php`
- Infrastructure: `Infrastructure/Repositories/EloquentTransactionRepository.php`
- Infrastructure: `Infrastructure/Models/TransactionModel.php`

## Step 2: Add validation rules with a FormRequest

Open the request validation file at:

`modules/Transaction/Delivery/Requests/CreateTransactionRequest.php`

Update the `rules()` method to specify the input validation you require:

```php
public function rules(): array
{
    return [
        'id' => ['required', 'integer'],
        'name' => ['required', 'string', 'min:1'],
    ];
}
```

## Step 3: Connect your business rule in the UseCase

Next, open:

`modules/Transaction/Application/UseCases/GetTransactionListUseCase.php`

Update the `execute()` method as follows so it:

1. Loads a domain entity using `TransactionRepositoryInterface`
2. Applies a business rule in the Domain (`rename`)
3. Saves the updated entity using the repository interface

```php
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

    // Persist the entity via repository
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
```

## Step 4: Wire up the Controller to your UseCase

Open your module’s controller at:

`modules/Transaction/Delivery/Controllers/TransactionController.php`

Modify the `index()` method so that it:

1. Accepts the validated request
2. Constructs the DTO via `TransactionDto::fromRequest($request)`
3. Passes the DTO to the UseCase
4. Returns a JSON response via `ApiResponse`

```php
use Illuminate\Http\JsonResponse;
use Modules\Shared\Infrastructure\Helpers\ApiResponse;
use Modules\Transaction\Application\DTOs\TransactionDto;
use Modules\Transaction\Application\UseCases\GetTransactionListUseCase;
use Modules\Transaction\Delivery\Requests\CreateTransactionRequest;

public function index(CreateTransactionRequest $request, GetTransactionListUseCase $useCase): JsonResponse
{
    $dto = TransactionDto::fromRequest($request);
    $result = $useCase->execute($dto);

    return ApiResponse::success(
        $result['data'] ?? $result,
        $result['message'] ?? 'Success'
    );
}
```

## Step 5: Run and verify your workflow

1. **Discover your module and register its service provider:**

   ```bash
   php artisan module:discover
   ```

2. **Start the development server:**

   ```bash
   php artisan serve
   ```

3. **Check available routes (suggested):**

   ```bash
   php artisan route:list --name=transaction
   ```

4. **Test your endpoint:**  
   Send a request including both `id` and `name`. Example:

   ```bash
   curl "http://localhost:8000/api/transaction/transaction/?id=1&name=Updated"
   ```

   **Expected response:**

   ```json
   {
     "is_success": true,
     "message": "Execution successful for Transaction",
     "data": {
       "id": "1",
       "name": "Updated"
     }
   }
   ```

## Tutorial Overview: Mapping to Morphling 3D Layers

- **Delivery:** Handles request validation (`CreateTransactionRequest`) and API responses (controller).
- **Application:** Orchestrates flow within `GetTransactionListUseCase::execute()`.
- **Domain:** Encapsulates business rules in methods such as `TransactionEntity::rename()`.
- **Infrastructure:** Persists and retrieves data using `EloquentTransactionRepository`.

## Next Steps

- [Learn about the Request Lifecycle →](./request-lifecycle.md)
