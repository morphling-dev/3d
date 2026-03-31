# Tutorial: Building Your First Module

This hands-on guide teaches you how to create a **Transaction** module using Morphling 3D, showing how data flows cleanly through all four architectural layers. You'll see how business logic stays isolated from infrastructure, resulting in highly maintainable code.

---

## Executive Summary

We’ll build a "Rename Transaction" feature to demonstrate the structured, outside-in data flow:

1.  **Delivery:** Receives and validates the HTTP request.
2.  **Application:** Transforms the request into a DTO and invokes the relevant Use Case.
3.  **Domain:** Executes business logic (e.g., renaming a transaction).
4.  **Infrastructure:** Persists changes via repository interfaces.

> [!NOTE]
> **Status:** `Guided Tutorial` | **Time Required:** `10 Minutes`

---

## Prerequisites

Before starting, initialize Morphling 3D in your Laravel project:

```bash
composer require morphling-dev/3d
php artisan 3d:install
composer dump-autoload
```

---

## Step 1: Generate the Module Structure

Create the DDD skeleton for your `Transaction` module:

```bash
php artisan 3d:new Transaction
```

### Your Mental Model

This command scaffolds a modular and layered mini-application at `modules/Transaction`:

```
modules/
└── Transaction/
    ├── Application/
    ├── Domain/
    ├── Infrastructure/
    └── Delivery/
```

Each directory enforces architectural boundaries.

---

## Step 2: Define Validation (Delivery Layer)

In the Delivery layer, define what data is allowed into your module by creating a Form Request:

```php
// modules/Transaction/Delivery/Requests/CreateTransactionRequest.php

public function rules(): array
{
    return [
        'id'   => ['required', 'integer'],
        'name' => ['required', 'string', 'min:3', 'max:50'],
    ];
}
```

---

## Step 3: Orchestrate the Use Case (Application Layer)

The Use Case coordinates the flow, relying only on abstractions. It should not access controllers, requests, or concrete models—only interfaces and DTOs.

Example:

```php
// modules/Transaction/Application/UseCases/RenameTransactionUseCase.php

public function execute(TransactionDto $dto): array
{
    // 1. Extract data from the DTO
    $id = $dto->id;
    $name = $dto->name;

    // 2. Fetch Entity from the Repository Interface
    $entity = $this->repository->findById($id);

    if (!$entity) {
        return ['is_success' => false, 'message' => 'Transaction not found'];
    }

    // 3. Apply Domain Logic
    $entity->rename($name);

    // 4. Persist the updated Entity
    $this->repository->save($entity);

    return [
        'is_success' => true,
        'message'    => 'Transaction renamed successfully',
        'data'       => [
            'id'   => $entity->getId(),
            'name' => $entity->getName()
        ]
    ];
}
```
> **Note:** Use case naming should match its intention (e.g. `RenameTransactionUseCase`).

---

## Step 4: Wire Up the HTTP Entry Point (Controller)

The Controller’s job: translate HTTP input to a DTO and delegate to the Use Case.

```php
// modules/Transaction/Delivery/Controllers/TransactionController.php

public function rename(CreateTransactionRequest $request, RenameTransactionUseCase $useCase): JsonResponse
{
    // Convert Request -> DTO
    $dto = TransactionDto::fromRequest($request);
    
    // Handle business logic
    $result = $useCase->execute($dto);

    return ApiResponse::success(
        $result['data'] ?? [],
        $result['message']
    );
}
```
> **Note:** Controllers remain thin and never contain business logic.

---

## Step 5: Activation & Testing

### 1. Auto-Discovery

Ask Morphling to register new modules, routes, providers:

```bash
php artisan 3d:discover
```

### 2. Route Verification

Check that your new route(s) is active:

```bash
php artisan 3d:route:list --name=transaction
```

### 3. End-to-End Test

Use `curl` or Postman to test the full request flow:

```bash
curl "http://localhost:8000/api/transaction/rename?id=1&name=NewPayment"
```

---

## Technical Reference: Best Practices

| Do's                                               | Don'ts                                                 |
| :------------------------------------------------- | :----------------------------------------------------- |
| Use `TransactionDto` to pass data between layers.   | **Don't** inject `$request` directly into Use Cases.   |
| Rely on repository **interfaces** in the Use Case.  | **Don't** inject Eloquent models into Use Cases.       |
| Encapsulate rename logic inside the Entity.         | **Don't** place domain rules in controllers.           |

---

## Troubleshooting

### Common Pitfalls

* **Target Class Not Found:** Did you run `composer dump-autoload`? The PSR-4 mapping for `Modules\\` must be active.
* **404 Not Found:** Did you run `php artisan 3d:discover`? That registers your module routes.
* **Interface Binding Error:** Check `Infrastructure/Providers/TransactionServiceProvider.php` to ensure repository interfaces are bound to their implementations.

---