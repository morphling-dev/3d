# **Mocking Repositories for Use Case Testing**

Testing in the **Application Layer** focuses on verifying the coordination logic of your **Use Cases**—not the underlying business rules (that resides in the Domain) or infrastructure/database concerns. In Morphling 3D, Use Cases interact with repository **Interfaces** (not implementations), which is what allows us to fully mock storage in unit tests.

---

## **Purpose of Application/Use Case Tests**

You are **not** testing:

- Business logic or domain rules (handled in Domain tests).
- SQL queries or Eloquent logic (handled in Infrastructure tests).

You **are** testing:

1. Does the Use Case load or persist the right entity through the repository?
2. Does it invoke the expected domain methods and actions?
3. Is the output/result as expected given certain repository responses?

---

## **1. Mocking Repository Interfaces**

Because Use Cases receive repository **interfaces** via constructor injection, you can swap the actual implementation for a mock in tests. This is essential for decoupled, fast, and reliable unit tests.

```php
namespace Modules\Transaction\Tests\Unit\Application\UseCases;

use PHPUnit\Framework\TestCase;
use Modules\Transaction\Application\UseCases\ProcessPaymentUseCase;
use Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;
use Modules\Transaction\Domain\Entities\TransactionEntity;

class ProcessPaymentUseCaseTest extends TestCase
{
    public function test_it_updates_and_saves_transaction()
    {
        // Arrange: Mock the repository interface
        $repository = $this->createMock(TransactionRepositoryInterface::class);

        // Arrange: Prepare a test entity
        $entity = new TransactionEntity(id: 'trx_1', amount: 500, status: 'pending');

        // Repository should find the entity by its ID
        $repository->expects($this->once())
            ->method('findById')
            ->with('trx_1')
            ->willReturn($entity);

        // Repository should save (persist) the updated entity
        $repository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(TransactionEntity::class));

        // Act: Execute the use case
        $useCase = new ProcessPaymentUseCase($repository);
        $result = $useCase->execute((object)['id' => 'trx_1']);

        // Assert: Outcome signals success
        $this->assertTrue($result['is_success']);
    }
}
```

---

## **2. Handling "Not Found" and Edge Cases**

Mocking allows you to easily simulate repository responses like missing data, exceptions, etc.

```php
public function test_it_returns_failure_if_transaction_missing()
{
    $repository = $this->createMock(TransactionRepositoryInterface::class);

    // Simulate: Not found in the repository
    $repository->method('findById')->willReturn(null);

    // save must never be called if nothing is found
    $repository->expects($this->never())->method('save');

    $useCase = new ProcessPaymentUseCase($repository);
    $result = $useCase->execute((object)['id' => '999']);

    $this->assertFalse($result['is_success']);
    $this->assertEquals('Not found', $result['message']);
}
```

---

## **Key Benefits**

- **In-memory, fast tests:** Completely isolated from the database (no MySQL/Postgres needed).
- **Full control of dependencies:** Make the repository return whatever you need—entities, `null`, exceptions.
- **Realistic application flow:** Validate that the Use Case reacts to all possible repository outcomes.

---

## **Testing Guidelines**

- **Mock your own interfaces only** (not 3rd party/vendor code).
- **Assert use case output, not internals:** Validate results like `is_success`, `message`, or anything returned to the caller.
- **Share common logic via `BaseUseCaseTest` if needed:** If your use case tests need common setup, create and use a test base class for consistency.

---
