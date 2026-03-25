# Generators (Artisan)

Generator commands create code inside `modules/{ModuleName}/...` using the stub templates under `stubs/`.

## Reference Table

> `module` always maps to the module folder name (ex: `Transaction` → `modules/Transaction/`).

| Command | Signature | Layer | Output |
|---|---|---|---|
| `module:new` | `module:new {name}` | All layers | Full module scaffold |
| `module:make-dto` | `module:make-dto {name} {module}` | Application | `Application/DTOs/{name}.php` |
| `module:make-usecase` | `module:make-usecase {name} {module}` | Application | `Application/UseCases/{name}.php` |
| `module:make-entity` | `module:make-entity {name} {module}` | Domain | `Domain/Entities/{name}.php` |
| `module:make-vo` | `module:make-vo {name} {module}` | Domain | `Domain/ValueObjects/{name}.php` |
| `module:make-enum` | `module:make-enum {name} {module}` | Domain | `Domain/Enums/{name}.php` |
| `module:make-interface` | `module:make-interface {name} {module}` | Domain | `Domain/Interfaces/{name}.php` |
| `module:make-service` | `module:make-service {name} {module}` | Domain | `Domain/Services/{name}.php` |
| `module:make-model` | `module:make-model {name} {module}` | Infrastructure | `Infrastructure/Models/{name}.php` |
| `module:make-repo` | `module:make-repo {name} {module}` | Infrastructure | `Infrastructure/Repositories/{name}.php` |
| `module:make-mapper` | `module:make-mapper {name} {module}` | Infrastructure | `Infrastructure/Mappers/{name}.php` |
| `module:make-observer` | `module:make-observer {name} {module}` | Infrastructure | `Infrastructure/Observers/{name}.php` |
| `module:make-provider` | `module:make-provider {module}` | Infrastructure | `Infrastructure/Providers/{Module}ServiceProvider.php` |
| `module:make-migration` | `module:make-migration {name} {module}` | Infrastructure | `Infrastructure/Database/Migrations/*_{name}.php` |
| `module:make-event` | `module:make-event {name} {module}` | Infrastructure | `Infrastructure/Events/{name}.php` |
| `module:make-listener` | `module:make-listener {name} {module}` | Infrastructure | `Infrastructure/Listeners/{name}.php` |
| `module:make-job` | `module:make-job {name} {module}` | Infrastructure | `Infrastructure/Jobs/{name}.php` |
| `module:make-notification` | `module:make-notification {name} {module}` | Infrastructure | `Infrastructure/Notifications/{name}.php` |
| `module:make-external` | `module:make-external {name} {module}` | Infrastructure | `Infrastructure/External/{name}.php` |
| `module:make-command` | `module:make-command {name} {module}` | Infrastructure | `Infrastructure/Commands/{name}.php` |
| `module:make-controller` | `module:make-controller {name} {module}` | Delivery | `Delivery/Controllers/{name}.php` |
| `module:make-request` | `module:make-request {name} {module}` | Delivery | `Delivery/Requests/{name}.php` |
| `module:make-resource` | `module:make-resource {name} {module}` | Delivery | `Delivery/Resources/{name}.php` |
| `module:make-route` | `module:make-route {name} {module}` | Delivery | `Delivery/Routes/{api|web}.php` |
| `module:make-view` | `module:make-view {name} {module}` | Delivery | `Delivery/Views/{name}.blade.php` |

## Example: DTO Output

DTOs are generated as `readonly` classes with `fromRequest()` (excerpt):

```php
readonly class {{ class }}
{
    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function fromRequest(mixed $request): static
    {
        return new static(
            $request->validated(),
        );
    }
}
```

## Typical Workflow

1. Create a module:
   - `php artisan module:new Transaction`
2. Add/adjust layer parts:
   - Domain: `module:make-entity`, `module:make-vo`
   - Application: `module:make-usecase`, `module:make-dto`
   - Infrastructure: `module:make-repo`, `module:make-migration`
   - Delivery: `module:make-controller`, `module:make-view`

For management commands, see [Management Commands](#/commands/management).

