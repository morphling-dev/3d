# Infrastructure

The **Infrastructure** layer implements technical details: persistence, integration points, and background work.

In a DDD module, this layer should depend on the Domain interfaces—not the other way around.

## What Morphling 3D Generates

- `module:make-model` generates `Infrastructure/Models/*`
- `module:make-repo` generates `Infrastructure/Repositories/*`
- `module:make-mapper` generates `Infrastructure/Mappers/*`
- `module:make-observer` generates `Infrastructure/Observers/*`
- `module:make-provider` generates a module Service Provider under `Infrastructure/Providers/*`
- `module:make-migration` generates migrations under `Infrastructure/Database/Migrations/*`
- `module:make-event`, `module:make-listener`, `module:make-job`, `module:make-notification`
- `module:make-external` and `module:make-command` for integrations and console utilities

## Example: Service Provider Bootstrapping

Module providers are responsible for registering routes and views (excerpt):

```php
public function boot(): void
{
    $this->registerRoutes();
    $this->registerViews();
    // Uncomment the following line if you want to load module migrations:
    // $this->registerMigrations();
}

protected function registerRoutes(): void
{
    $modulePath = base_path('modules/{{ module }}/Delivery/Routes');

    if (file_exists($modulePath . '/web.php')) {
        Route::middleware('web')->group($modulePath . '/web.php');
    }

    if (file_exists($modulePath . '/api.php')) {
        Route::prefix('api/{{ module_snake }}')
            ->middleware('api')
            ->group($modulePath . '/api.php');
    }
}
```

## Why this matters

Infrastructure is where technical concerns live:

- persistence and Eloquent integration
- repository implementations
- module service provider bootstrapping (routes + views)
- mappers and integration logic your module needs

When Infrastructure is isolated, you can change persistence or mapping without changing Domain rules or UseCase orchestration.

## How this connects to other layers

- Domain defines repository interfaces and Entities.
- Application depends on repository interfaces and never directly on Eloquent.
- Delivery calls UseCases and returns results to the client.
- Infrastructure implements the Domain contracts and binds them in the module’s Service Provider.

## Example: Eloquent Repository (Infrastructure)

Morphling 3D generates an Eloquent repository that implements the Domain repository interface. Example shape (excerpt):

```php
class EloquentTransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(protected TransactionModel $model) {}

    public function findById(int|string $id): ?TransactionEntity
    {
        $record = $this->query()->find($id);
        return $record ? $record->toDomain() : null;
    }

    public function save(TransactionEntity $entity): bool
    {
        $model = new TransactionModel($entity->toArray());
        return $model->save();
    }
}
```

## Example: Mapper Responsibilities (when used)

If you use a `{{ module }}Mapper`, keep conversions inside `Infrastructure/Mappers/`:

- `toDomain(object $raw)`: persistence object -> Domain Entity
- `toPersistence(object $entity): array`: Domain Entity -> persistence array

This prevents database/ORM details from leaking into Domain.

## Navigation

- [Domain Responsibilities](#/architecture/domain)
- [Application Responsibilities](#/architecture/application)
- [Delivery Responsibilities](#/architecture/delivery)

