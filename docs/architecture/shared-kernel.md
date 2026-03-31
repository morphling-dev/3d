# Shared Kernel

The **Shared Kernel**—found in `modules/Shared`—is the foundational layer of your Morphling 3D architecture. It contains code and abstractions that are reused across all modules, such as base classes, global value objects, infrastructure support, and system-wide helpers.

A well-designed Shared Kernel prevents duplication of code (like API response wrappers or pagination logic) and ensures consistency across modules.

---

## Architectural Principle: The Golden Rule

> **The Shared Kernel must NEVER depend on any feature or business module.**

For example:  
A module like `Transaction` **can** rely on code in `Shared`, but `Shared` must **never** import or reference code from `Transaction`—or any other module. This strictly prevents circular dependencies, which can break modularity and cause maintenance headaches.

---

## Typical Directory Structure

| Sub-Directory        | Contents                               | Examples                              |
|----------------------|----------------------------------------|---------------------------------------|
| **Application**      | Base orchestration, DTOs               | `BaseUseCase`, `BaseDto`              |
| **Domain**           | Universal entities, value objects      | `MoneyValueObject`, `BaseEntity`      |
| **Infrastructure**   | Framework-level helpers & traits       | `ApiResponse`, `BaseRepository`       |

> **Tip:** The files here should be free of business logic specific to any one bounded context or module.

---

## Key Shared Components

### Application Layer: `BaseUseCase`

Every UseCase in your application should extend this abstract class. It standardizes invocation and is the right place for common middleware-like logic (e.g., logging, transactions):

```php
namespace Modules\Shared\Application\UseCases;

abstract class BaseUseCase
{
    /**
     * The required entry point for every business action.
     */
    abstract public function execute(mixed $dto = null): array;
}
```

### Infrastructure Layer: `ApiResponse`

This helper ensures consistent API responses for your frontend consumers—from any controller across modules:

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

## Shared Kernel Best Practices

- **Enums:** Put global enums (e.g. `ActiveStatus`, `Gender`) here for status or categorization shared by all modules.
- **Traits:** Common traits for repetitive model behaviors (like `HasUuid`) belong in `Shared/Infrastructure/Traits`.
- **Interfaces:** Contracts that cut across the whole application—such as a `LoggerInterface`—should live here.
- **Do not pollute:** Only move code to Shared when there’s a proven need in at least **two or more modules**.
- **Keep it minimal:** Avoid turning Shared into a dumping ground for arbitrary utilities.

---

## When to Generalize Into Shared

**DON'T** add logic to `Shared` preemptively.  
**DO** move code only when real, identical needs appear in multiple modules.  
This avoids Shared becoming a maintenance burden or a "junk drawer" of assumptions and unused abstractions.

---

## Troubleshooting: Recognizing Shared Changes

### "Class Not Found" After Adding Shared Files?

1. Check your `composer.json` for:

    ```json
    "autoload": {
        "psr-4": {
            "Modules\\": "modules/"
        }
    }
    ```

2. Run the Composer autoloader to refresh class maps:

    ```bash
    composer dump-autoload
    ```

If you follow this, your newly added code in `Shared` should always be available to all other modules—without manual imports or hacks.