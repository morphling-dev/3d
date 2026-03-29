# Tutorial: Building Your First Module

This guide walks you through the practical implementation of a **Transaction** module. You will learn how to move data through the four layers of Morphling 3D, ensuring that your business logic remains protected from infrastructure changes.

---

## Executive Summary
In this tutorial, we implement a "Rename Transaction" feature. This demonstrates the "Outside-In" flow:
1.  **Delivery**: Captures and validates the HTTP request.
2.  **Application**: Converts the request into a DTO and calls the Use Case.
3.  **Domain**: Executes the actual business rule (renaming logic).
4.  **Infrastructure**: Saves the change to the database.

> [!NOTE]
> **Status:** `Guided Tutorial` | **Time to Complete:** `10 Minutes`

---

## Prerequisites

Before starting, ensure Morphling 3D is initialized in your Laravel project:

```bash
composer require morphling-dev/3d
php artisan 3d:install
composer dump-autoload
```

---

## Step 1: Generate the Module Structure

Run the generator to create the DDD directory skeleton for a `Transaction` feature:

```bash
php artisan module:new Transaction
```

### The Resulting Mental Model
The generator creates a "Mini-Application" inside your `modules/` folder. Each layer has a specific boundary.



---

## Step 2: Define Validation (Delivery Layer)

Navigate to `modules/Transaction/Delivery/Requests/CreateTransactionRequest.php`. We define exactly what data is allowed into our system.

```php
### modules/Transaction/Delivery/Requests/CreateTransactionRequest.php

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

The Use Case is the "Director." It doesn't know about the Database or the Web; it only knows how to coordinate the **Domain** and **Repository**.

Open `modules/Transaction/Application/UseCases/GetTransactionListUseCase.php`:

```php
### modules/Transaction/Application/UseCases/GetTransactionListUseCase.php

public function execute(mixed $dto = null): array
{
    // 1. Extract data from the DTO
    $id = $dto->data['id'];
    $name = $dto->data['name'];

    // 2. Fetch Entity from the Repository Interface
    $entity = $this->repository->findById($id);

    if (!$entity) {
        return ['is_success' => false, 'message' => 'Not found'];
    }

    // 3. Trigger Domain Logic (The Business Rule)
    $entity->rename($name);

    // 4. Persist via the Interface
    $this->repository->save($entity);

    return [
        'is_success' => true,
        'message'    => 'Transaction updated successfully',
        'data'       => ['id' => $entity->getId(), 'name' => $entity->getName()]
    ];
}
```

---

## Step 4: The Entry Point (Controller)

Now, we wire the HTTP request to our Use Case. The Controller's only job is to translate.

```php
### modules/Transaction/Delivery/Controllers/TransactionController.php

public function index(CreateTransactionRequest $request, GetTransactionListUseCase $useCase): JsonResponse
{
    // Convert Request -> DTO (Data Transfer Object)
    $dto = TransactionDto::fromRequest($request);
    
    // Execute the business logic
    $result = $useCase->execute($dto);

    return ApiResponse::success(
        $result['data'] ?? [],
        $result['message']
    );
}
```

---

## Step 5: Activation & Testing

### 1. Discovery
Tell Morphling to find your new routes and service providers:
```bash
php artisan module:discover
```

### 2. Verification
Check that your route is registered:
```bash
php artisan route:list --name=transaction
```

### 3. Execution
Test the flow using a simple `curl` command:
```bash
curl "http://localhost:8000/api/transaction?id=1&name=NewPayment"
```

---

## Technical Reference: Best Practices

| Do's | Don'ts |
| :--- | :--- |
| Use `TransactionDto` to move data between layers. | **Don't** pass `$request` into your Use Case. |
| Type-hint Repository **Interfaces** in the Use Case. | **Don't** type-hint Eloquent Models in the Use Case. |
| Keep `rename()` logic inside the `Entity`. | **Don't** put "if" statements for business rules in the Controller. |

---

## Troubleshooting

### Common Pitfalls

* **Target Class Not Found:** You likely skipped `composer dump-autoload`. Morphling modules require the PSR-4 `Modules\\` mapping to be active.
* **404 Not Found:** Ensure you ran `php artisan module:discover`. This command links your module's `routes.php` to the Laravel core.
* **Interface Binding Error:** If Laravel can't resolve the Repository, check `Infrastructure/Providers/TransactionServiceProvider.php` to ensure the Interface is bound to the Eloquent implementation.

---