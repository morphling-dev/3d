# The Infrastructure Layer: The Implementation

The **Infrastructure Layer** is the "Engine Room" of your module. While the Domain Layer defines *what* should happen, the Infrastructure Layer handles the technical details of *how* it happens. This includes database queries, file storage, external API calls, and background jobs.

---

## Executive Summary
In Morphling 3D, the Infrastructure layer is the only place where framework-specific code (like Eloquent) and third-party SDKs are allowed to live. Its primary job is to fulfill the contracts (Interfaces) defined by the **Domain**.

> [!NOTE]
> **Status:** `Implementation` | **Dependency:** `Depends on Domain`

---

## Key Concepts: The "Adapter" Model
Think of the Infrastructure layer as a universal adapter. The Domain provides a standard "plug" (the Interface), and the Infrastructure provides the "socket" (the Eloquent Repository) that connects to the actual wall (the Database).



---

## What Morphling 3D Generates

Morphling 3D provides a comprehensive suite of generators for technical concerns:

| Category | Command | Generates |
| :--- | :--- | :--- |
| **Persistence** | `module:make-model` | Eloquent Models & Migrations. |
| **Data Flow** | `module:make-repo` | Eloquent Repository implementations. |
| **Translation** | `module:make-mapper` | Logic to convert Models to Entities. |
| **Bootstrapping**| `module:make-provider` | Service Providers for routes and views. |
| **Async/Events** | `module:make-job` | Background Jobs and Event Listeners. |

---

## Technical Reference: The Eloquent Repository

The Repository implementation is where the "magic" happens. It uses Eloquent to find data and a Mapper (or a method on the model) to return a **Domain Entity**.

```php
### modules/Transaction/Infrastructure/Repositories/EloquentTransactionRepository.php

class EloquentTransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(protected TransactionModel $model) {}

    public function findById(string $id): ?TransactionEntity
    {
        $record = $this->model->find($id);
        
        // Convert the database row into a pure Domain Entity
        return $record ? $record->toDomain() : null;
    }

    public function save(TransactionEntity $entity): void
    {
        // Convert the Entity back into a database-friendly array
        $this->model->updateOrCreate(
            ['id' => $entity->getId()],
            $entity->toPersistenceArray()
        );
    }
}
```

---

## The Role of Mappers

Mappers are the "secret sauce" that keep your Domain clean. They prevent your Domain Entities from knowing about database column names like `created_at` or `is_active_flg`.

* **To Domain:** Transforms an Eloquent Model into a Domain Entity.
* **To Persistence:** Transforms a Domain Entity into a raw array for Eloquent's `create()` or `update()`.

---

## Module Service Providers

Every module has its own **ServiceProvider**. This is the brain of the Infrastructure layer that tells Laravel how to handle the module.

* **Route Registration:** Automatically loads `api.php` and `web.php` from the **Delivery** layer.
* **Interface Binding:** Maps the `TransactionRepositoryInterface` to the `EloquentTransactionRepository`.
* **View Namespaces:** Allows you to call `view('transaction::index')` from anywhere.

---

## Best Practices: The "Replaceable" Rule

* **Zero Leakage:** Never let an Eloquent Model escape the Repository. Always return an Entity or a DTO to the Application layer.
* **Favor Composition:** If an Infrastructure service (like a PDF generator) gets too complex, break it down into smaller classes within the Infrastructure layer.
* **Mockable Implementation:** Ensure your Repository is easily swappable in tests by relying solely on the Domain Interface.

---

## Troubleshooting

### "My migrations aren't running!"
By default, Morphling 3D keeps migrations inside the module folder. You must uncomment `$this->registerMigrations()` in your module's `ServiceProvider` to enable them.

### "Why is my Repository getting crowded?"
If your repository has 20+ methods, you might be mixing **Query Logic** (filtering/sorting) with **Persistence Logic** (saving/deleting). Consider using **Scopes** on your Eloquent models or creating a specific **Query Service**.