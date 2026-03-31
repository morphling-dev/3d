# **Unit Testing Entities (Domain Layer)**

Unit testing within the **Domain Layer** is foundational in Morphling 3D. Since Entities and Value Objects rely solely on pure PHP without any Laravel dependency, their tests execute extremely fast, and offer strong assurance that your critical business rules are properly enforced.

---

## **The Purpose of Entity Testing**
Entity tests **do not** verify persistence, storage, or database interaction. Instead, they verify that **invariants**—the fundamental rules that must *always* be true for your business objects—are upheld.

---

## **1. Construction & Validation**
An Entity must never be allowed to exist in an invalid state. Your tests should confirm that constructors or factory methods throw exceptions if invalid data is provided.

```php
namespace Modules\Transaction\Tests\Unit\Domain\Entities;

use PHPUnit\Framework\TestCase;
use Modules\Transaction\Domain\Entities\TransactionEntity;
use InvalidArgumentException;

class TransactionEntityTest extends TestCase
{
    public function test_it_cannot_be_created_with_negative_amount()
    {
        $this->expectException(InvalidArgumentException::class);

        new TransactionEntity(
            id: 'trx-001',
            amount: -100, // Violates business invariant
            status: 'pending'
        );
    }
}
```

---

## **2. State Transition Logic**
A major part of business logic involves changing between valid states (for example, from `Pending` to `Completed`). Your tests should ensure allowed transitions succeed, and forbidden transitions fail.

```php
public function test_it_can_be_marked_as_completed()
{
    $entity = new TransactionEntity('1', 500, 'pending');
    
    $entity->complete();
    
    $this->assertEquals('completed', $entity->getStatus());
}

public function test_it_cannot_be_cancelled_if_already_completed()
{
    $entity = new TransactionEntity('1', 500, 'completed');
    
    $this->expectException(\DomainException::class);
    $this->expectExceptionMessage("Cannot cancel a completed transaction");
    
    $entity->cancel();
}
```

---

## **3. Value Object Behavior**
Value Objects (such as `Email`, `Money`, or `Coordinates`) are always immutable. Test their validation logic and equality—which is central to DDD.

```php
public function test_money_value_objects_with_same_values_are_equal()
{
    $price1 = new Money(100, 'USD');
    $price2 = new Money(100, 'USD');
    $price3 = new Money(150, 'USD');

    $this->assertTrue($price1->equals($price2));
    $this->assertFalse($price1->equals($price3));
}
```

---

## **Why Entity Unit Testing is Valuable**

* **Living Documentation:** Entity tests (like `TransactionEntityTest.php`) serve as clear, executable examples of your business rules—new developers can quickly learn the essentials.
* **Ultra Fast Feedback:** With no need for the Laravel container, database, or external dependencies, these tests run nearly instantly.
* **Full Isolation:** There are zero side effects—no cleanup, no test pollution, and no need for faked or seeded data.

---

## **Best Practices**

* **Use `PHPUnit\Framework\TestCase`:** Stick to vanilla PHPUnit in the Domain layer. Do not use `Tests\TestCase` (the Laravel base test), which slows down test runs and ties your domain to Laravel.
* **Single Responsibility Per Test:** Write small, focused test methods that each validate one business rule or scenario.
* **Minimal Mocking:** Mocks are rarely needed for Entities. If you find yourself mocking collaborators often, your Entity might need to delegate to a Domain Service instead.