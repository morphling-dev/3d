# The Infrastructure Layer: Implementation Details

The **Infrastructure Layer** is the "Engine Room" of your module. While the Domain Layer declares *what* should happen, the Infrastructure Layer is responsible for *how* those things get done. This covers database access, file systems, external APIs, jobs/queueing, caching, and more.

---

## Executive Summary

In Morphling 3D, the Infrastructure layer is the only place where framework-dependent code (e.g., Eloquent, Laravel services) or third-party SDKs are permitted. The core purpose of this layer is to provide concrete implementations of contracts (Interfaces) defined in the **Domain** layer.

> [!NOTE]
> **Status:** `Implementation` | **Dependency:** `Depends on Domain`

---

## Key Concepts: The Adapter Pattern

The Infrastructure layer acts as an adapter, translating between your technology-agnostic Domain contracts and the real-world requirements of your stack.

- **Domain:** Defines an Interface (contract) for what must be done, with no knowledge of Laravel/Eloquent.
- **Infrastructure:** Implements that Interface, using tools like Eloquent, Guzzle, filesystems, or third-party APIs to do the actual work.

---

## What Morphling 3D Generates

Morphling 3D scaffolds a variety of Infrastructure artifacts to keep your Domain clean and framework-agnostic:

| Category          | Command                  | Output (Location)                                            |
|:------------------|:------------------------|:-------------------------------------------------------------|
| **Persistence**   | `3d:make-model`         | Eloquent Models & Migrations                                 |
| **Data/Repo**     | `3d:make-repo`          | Repository implementations using Eloquent or other drivers    |
| **Mapping**       | `3d:make-mapper`        | Mappers for Model <-> Entity conversion                      |
| **Bootstrapping** | `3d:make-provider`      | Service Providers for registering bindings, routes, etc.      |
| **Async/Events**  | `3d:make-job`           | Jobs (queues), events, event listeners                       |

---

## Technical Reference: Eloquent-Based Repository

A Repository translates between technical and Domain concerns. It retrieves models with Eloquent, then converts them into **Domain Entities** (not Eloquent models!) using a Mapper or "toDomain" method.

```php
// modules/Transaction/Infrastructure/Repositories/EloquentTransactionRepository.php

class EloquentTransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(protected TransactionModel $model) {}

    public function findById(string $id): ?TransactionEntity
    {
        $record = $this->model->find($id);

        // Always return a pure Domain Entity
        return $record ? $record->toDomain() : null;
    }

    public function save(TransactionEntity $entity): void
    {
        // Persist the Domain Entity, never expose Eloquent objects
        $this->model->updateOrCreate(
            ['id' => $entity->getId()],
            $entity->toPersistenceArray()
        );
    }
}
```

---

## The Role of Mappers

Mappers are crucial for keeping the Domain layer decoupled from technical concerns — no `created_at`, no Eloquent types, no database specifics.

* **To Domain:** Convert infrastructure models (like Eloquent models) into business-safe Domain Entities.
* **To Persistence:** Convert Domain Entities into arrays/structures suitable for storage or transmission (e.g., Eloquent's `create/update`).

---

## Module Service Providers

Each module includes a **Service Provider** in its Infrastructure layer. This is responsible for:

- **Route Registration:** Auto-registering Delivery-layer route files (`api.php`, `web.php`).
- **Binding Interfaces:** Telling Laravel to use your chosen Repository implementation for its respective Domain Interface.
- **View Namespaces:** Registering namespaced views for the Delivery layer (`view('transaction::index')`).

---

## Best Practices for Infrastructure

- **No Leakage:** Never return Eloquent models, helpers, or framework objects beyond Infrastructure; always use Entities or DTOs for Application/Domain interfaces.
- **Favor Composition:** Complex Infrastructure features (e.g., a PDF system or payment gateway) should be separated into small, testable classes.
- **Be Replaceable:** Repository implementations must be swappable for tests/fakes by relying only on Domain interfaces — never concrete classes.

---

## Troubleshooting

### "My migrations aren't running!"

By default, Morphling 3D stores migrations within the module directory. To use them, ensure you call `$this->registerMigrations()` in your module's ServiceProvider.

### "Why is my Repository getting crowded?"

If your repository grows too large (many query methods), separate **Query Logic** (fetching/filtering/sorting) from **Persistence Logic** (save/delete). Use Eloquent *scopes* or create dedicated *Query* services/objects.

---