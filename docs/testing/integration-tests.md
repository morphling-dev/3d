# **Integration Tests (Infrastructure Layer)**

While unit tests and mocks verify that your internal logic works, **integration tests** in Morphling 3D confirm that your code interacts properly with actual infrastructure — especially **Eloquent Repositories**, **Mappers**, and **Database Migrations**.

---

## **Purpose of Integration Tests**

Integration tests ensure:

1. Your Eloquent queries are generated as expected.
2. The database schema (via migrations) matches the data needs of your domain.
3. **Mappers** accurately translate database rows into **Domain Entities** (and vice versa).

---

## **1. Setting Up the Database Environment**

Unlike most unit tests, integration tests in Laravel **must** load the application and perform real database operations. For speed and isolation, use **SQLite in-memory** as the testing database.

```php
namespace Modules\Transaction\Tests\Integration\Infrastructure\Repositories;

use Tests\TestCase; // Extends Laravel's base TestCase
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Transaction\Infrastructure\Models\TransactionModel;
use Modules\Transaction\Infrastructure\Repositories\EloquentTransactionRepository;
use Modules\Transaction\Domain\Entities\TransactionEntity;

class EloquentTransactionRepositoryTest extends TestCase
{
    use RefreshDatabase; // Refreshes database between tests

    protected EloquentTransactionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        // Instantiate real repository with its concrete model
        $this->repository = new EloquentTransactionRepository(new TransactionModel());
    }
}
```

---

## **2. Testing "Save" and "Find" Methods**

Verify that saving a **Domain Entity** through the Repository accurately persists, and retrieval returns the correct data.

```php
public function test_it_can_save_and_retrieve_a_transaction_entity()
{
    // Arrange: build a Domain Entity (not a Model)
    $entity = new TransactionEntity(id: 'trx_100', amount: 1500, status: 'pending');

    // Act: persist it via Repository
    $this->repository->save($entity);

    // Act: retrieve it back
    $retrieved = $this->repository->findById('trx_100');

    // Assert: compare with original data
    $this->assertInstanceOf(TransactionEntity::class, $retrieved);
    $this->assertEquals(1500, $retrieved->getAmount());
    $this->assertEquals('pending', $retrieved->getStatus());
}
```

---

## **3. Testing Mappers (Translation Layer)**

If using dedicated **Mapper** classes, ensure that complex columns (e.g., JSON fields, dates) are correctly mapped to Domain Value Objects and vice versa.

```php
public function test_mapper_translates_json_column_to_value_object()
{
    $model = TransactionModel::factory()->create([
        'metadata' => json_encode(['ip' => '127.0.0.1', 'device' => 'mobile']),
    ]);

    $entity = $this->repository->findById($model->id);

    // The metadata Value Object should have its properties properly set
    $this->assertEquals('127.0.0.1', $entity->getMetadata()->ip);
    $this->assertEquals('mobile', $entity->getMetadata()->device);
}
```

---

## **Why Integration Tests Matter**

* **Catches DB Schema Issues:** Prevents mismatched column names or types from sneaking into production.
* **Mapper Validation:** Ensures changes to mapping logic don't cause silent breakage between infrastructure and domain layers.
* **Confidence:** Provides full assurance that your DDD and Hexagonal patterns are actually working across real boundaries.

---

## **Best Practices**

* **Test Boundaries Not Business Logic:** Reserve integration tests for verifying persistence, mapping, and schema. Business rules belong in unit tests.
* **Mock External Services:** For infrastructure code that calls third-party services (Stripe, AWS, etc.), use Laravel's **Http::fake()** or similar mocks in both unit and integration tests to avoid slow or non-repeatable network dependencies.
* **Leverage Model Factories:** Use Laravel factories to efficiently seed the database before each test case.