# The Shared Kernel

The **Shared Kernel (`modules/Shared`)** is the foundation of your Morphling 3D architecture. It contains the code that is globally applicable across all modules—such as base classes, common value objects, and infrastructure helpers. 

Without a well-defined Shared Kernel, you would find yourself duplicating code like API response structures or pagination logic in every single module.

---

## The "Golden Rule" of Shared
> **Shared must never depend on any other module.**

A module like `Transaction` can depend on `Shared`, but `Shared` can **never** import a class from `Transaction`. This prevents circular dependencies that would break your entire application.



---

## Directory Structure

| Sub-Directory | Content Type | Example |
| :--- | :--- | :--- |
| **Application** | Base orchestration logic. | `BaseUseCase`, `BaseDto` |
| **Domain** | Universal business logic. | `MoneyValueObject`, `BaseEntity` |
| **Infrastructure** | Technical framework tools. | `ApiResponse`, `BaseRepository` |

---

## Core Components Reference

### 1. Application: `BaseUseCase`
Every Use Case in your system should extend this abstract class. It ensures a consistent method signature and provides a place for global middleware-like logic (logging, DB transactions).

```php
namespace Modules\Shared\Application\UseCases;

abstract class BaseUseCase
{
    /**
     * The standard entry point for every business action.
     */
    abstract public function execute(mixed $dto = null): array;
}
```

### 2. Infrastructure: `ApiResponse`
To ensure your frontend receives a predictable JSON structure, use this helper in your Controllers.

```php
namespace Modules\Shared\Infrastructure\Helpers;

class ApiResponse
{
    public static function success($data = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'is_success' => true,
            'message'    => $message,
            'data'       => $data,
        ], $code);
    }
}
```

---

## Best Practices

* **Global Enums:** Place standard status enums here (e.g., `ActiveStatus`, `Gender`).
* **Common Traits:** If you have logic used by multiple Eloquent models (like `HasUuid`), put the Trait in `Shared/Infrastructure/Traits`.
* **Interfaces:** Global contracts that span the entire system belong here.

---

## When to Move Code into Shared
Don't move code into `Shared` just because you think you *might* need it later. Wait until at least **two separate modules** require the exact same logic. This prevents the Shared Kernel from becoming a "junk drawer" of unused code.

---

## Troubleshooting

### "Class Not Found"
If you add a file to `Shared` and it isn't recognized, check your `composer.json`. Ensure the `Modules\\` namespace points to the `modules/` directory and run:
```bash
composer dump-autoload
```