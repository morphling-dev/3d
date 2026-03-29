# **Mocking Repositories (Application Layer)**

Testing the **Application Layer** is where we verify our **Use Cases**. Since a Use Case coordinates the flow between the Domain and the Database, we need a way to test that coordination without actually connecting to MySQL or PostgreSQL.

This is achieved by **Mocking** the Repository Interfaces defined in the Domain.

---

## **The Goal of Use Case Testing**
We aren't testing business rules (the Entity does that) or SQL queries (the Infrastructure does that). We are testing the **Orchestration**:
1. Does the Use Case fetch the correct Entity?
2. Does it trigger the right Domain method?
3. Does it call `save()` on the Repository afterward?



---

## **1. Setting Up the Mock**
Because our Use Cases depend on **Interfaces** (Constructor Injection), we can easily swap a real database implementation for a "Mock" object.

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
        // 1. Create a Mock of the Interface
        $repository = $this->createMock(TransactionRepositoryInterface::class);

        // 2. Prepare a dummy Entity
        $entity = new TransactionEntity(id: 'trx_1', amount: 500, status: 'pending');

        // 3. Define Expectations: findById should return our entity
        $repository->expects($this->once())
            ->method('findById')
            ->with('trx_1')
            ->willReturn($entity);

        // 4. Define Expectations: save should be called exactly once
        $repository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(TransactionEntity::class));

        // 5. Execute the Use Case
        $useCase = new ProcessPaymentUseCase($repository);
        $result = $useCase->execute((object)['id' => 'trx_1']);

        $this->assertTrue($result['is_success']);
    }
}
```

---

## **2. Testing "Not Found" Scenarios**
Mocks make it incredibly easy to test edge cases, like what happens when a record doesn't exist in the database.

```php
public function test_it_returns_failure_if_transaction_missing()
{
    $repository = $this->createMock(TransactionRepositoryInterface::class);

    // findById returns null
    $repository->method('findById')->willReturn(null);

    // save should NEVER be called
    $repository->expects($this->never())->method('save');

    $useCase = new ProcessPaymentUseCase($repository);
    $result = $useCase->execute((object)['id' => '999']);

    $this->assertFalse($result['is_success']);
    $this->assertEquals('Not found', $result['message']);
}
```

---

## **Why This is Essential**

* **Zero Database Latency:** These tests run in memory. You can run 50 Use Case tests in the time it takes to run one standard Laravel Feature test.
* **Total Control:** You can force a Repository to throw an exception, return an empty array, or return a specific object to see how your Application handles it.
* **Decoupled Logic:** You are testing the *logic* of the process, not the *storage* of the data.

---

## **Best Practices**

* **Mock only what you own:** Never mock third-party libraries; mock your own Interfaces.
* **Keep Assertions Focused:** Test that the Use Case returns the expected result shape (`is_success`, `message`, etc.).
* **Use `BaseUseCase`:** Ensure your Use Case tests extend a base that handles common shared setup logic if needed.
