# **Unit Testing Entities (Domain Layer)**

Unit testing in the **Domain Layer** is the most rewarding part of Morphling 3D. Because Entities and Value Objects are "Pure PHP" (no Laravel dependencies), these tests run in milliseconds and provide an ironclad guarantee that your business rules are being followed.

---

## **The Goal of Entity Testing**
We are not testing if data can be saved to a database. We are testing **Invariants**—the rules that must *always* be true for a business object to be valid.



---

## **1. Testing Construction & Validation**
An Entity should never exist in an invalid state. We test that the constructor or "factory" methods throw exceptions when given bad data.

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
            amount: -100, // Invalid business rule
            status: 'pending'
        );
    }
}
```

---

## **2. Testing State Transitions**
Business logic often revolves around moving from one state to another (e.g., `Pending` -> `Completed`). We test that these transitions work correctly and fail when they shouldn't.

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

## **3. Testing Value Objects**
Value Objects (like `Email`, `Price`, or `Coordinates`) are immutable. We test their internal logic and equality checks.

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

## **Why This is Powerful**

* **Documentation by Example:** New developers can read `TransactionEntityTest.php` to understand the business rules of the module without reading a 50-page PRD.
* **Instant Feedback:** You can run hundreds of these tests in less than a second. 
* **Zero Side Effects:** Since there is no database involved, you don't have to worry about cleaning up state or "leaking" data between tests.

---

## **Best Practices**

* **Use `PHPUnit\Framework\TestCase`:** Avoid `Tests\TestCase` (Laravel's base test) for Domain units to keep them lightning-fast.
* **One Rule Per Test:** Keep your test methods small and focused on a single business scenario.
* **Mocking:** You should rarely need to mock anything in an Entity test. If you do, your Entity might be doing too much (consider a **Domain Service**).