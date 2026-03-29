# Artisan Generators: The Command Cheat Sheet

The Morphling 3D CLI is designed to eliminate boilerplate and ensure architectural consistency. Instead of manually creating folders and namespaces, use these generators to scaffold your components with the correct layer-specific stubs.

---

## The Master Command

Before diving into individual parts, remember the primary entry point:

```bash
php artisan module:new {ModuleName}
```
**What it does:** This is a "Macro" command. It calls multiple sub-generators to build a complete, functional 4-layer module in one second.

---

## Generator Reference Table

All commands follow the pattern: `php artisan module:make-{type} {Name} {Module}`.

| Layer | Command | Output Path |
| :--- | :--- | :--- |
| **Application** | `module:make-usecase` | `Application/UseCases/{Name}.php` |
| | `module:make-dto` | `Application/DTOs/{Name}.php` |
| **Domain** | `module:make-entity` | `Domain/Entities/{Name}.php` |
| | `module:make-vo` | `Domain/ValueObjects/{Name}.php` |
| | `module:make-enum` | `Domain/Enums/{Name}.php` |
| | `module:make-interface` | `Domain/Repositories/{Name}.php` |
| | `module:make-service` | `Domain/Services/{Name}.php` |
| **Infrastructure** | `module:make-model` | `Infrastructure/Models/{Name}.php` |
| | `module:make-repo` | `Infrastructure/Repositories/{Name}.php` |
| | `module:make-mapper` | `Infrastructure/Mappers/{Name}.php` |
| | `module:make-migration` | `Infrastructure/Database/Migrations/*.php` |
| | `module:make-provider` | `Infrastructure/Providers/{Module}ServiceProvider.php` |
| **Delivery** | `module:make-controller` | `Delivery/Controllers/{Name}.php` |
| | `module:make-request` | `Delivery/Requests/{Name}.php` |
| | `module:make-resource` | `Delivery/Resources/{Name}.php` |
| | `module:make-view` | `Delivery/Views/{Name}.blade.php` |

---

## Anatomy of a Generated DTO

Morphling 3D DTOs are `readonly` by default, ensuring data integrity as it passes from the Controller to the Use Case.

```php
readonly class UserDto
{
    public function __construct(
        public array $data
    ) {}

    /**
     * The 'Magic' Bridge: Converts validated request data 
     * into a type-safe object instantly.
     */
    public static function fromRequest(mixed $request): static
    {
        return new static($request->validated());
    }
}
```

---

## The "Perfect Workflow" Sequence

To build a new feature (e.g., "User Registration") within an existing module, follow this logical order:

1.  **Domain:** `module:make-entity` & `module:make-interface` (Define the rules and the contract).
2.  **Infrastructure:** `module:make-model` & `module:make-repo` (Build the persistence logic).
3.  **Application:** `module:make-dto` & `module:make-usecase` (Orchestrate the flow).
4.  **Delivery:** `module:make-request` & `module:make-controller` (Expose it to the web).

---

## Pro-Tips for Generators

> [!TIP]
> **Naming Convention:** Morphling automatically appends suffixes. If you run `module:make-usecase Register User`, it will create `RegisterUserUseCase.php`. No need to type "UseCase" in the command.

> [!WARNING]
> **Namespace Alignment:** If you move a generated file manually, you must update its namespace. Generators determine the namespace based on the folder path inside `modules/`.

---

## Troubleshooting

### "Command not found"
Ensure you have registered the Morphling service provider in your `app/Providers/AppServiceProvider.php` or that you ran `php artisan 3d:install` which handles the registration.

### "Stub file missing"
If you want to customize how code is generated, you can publish the stubs using `php artisan vendor:publish --tag=morphling-stubs`. You can then edit them in your root `stubs/` directory.