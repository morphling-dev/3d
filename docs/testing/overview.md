# **Testing Strategy Overview**

Testing in a **Morphling 3D** environment is significantly different from standard Laravel testing. Because we have separated our logic into four distinct layers, we can test our business rules (Domain) and orchestration (Application) without ever touching a database or booting the entire Laravel framework.

This results in a test suite that is **100x faster** and more reliable than traditional feature tests.

---

## **The Testing Pyramid**

In DDD, we invert the traditional Laravel testing approach. Instead of focusing on "Feature Tests" (which are slow and heavy), we focus on "Unit Tests" for our Domain and Application layers.



| Test Type | Layer Target | Focus | Speed |
| :--- | :--- | :--- | :--- |
| **Unit Test** | Domain | Business rules inside Entities & Value Objects. | ⚡ Instant |
| **Unit/Mock Test** | Application | Use Case orchestration (mocking Repositories). | ⚡ Fast |
| **Integration Test**| Infrastructure | Repository SQL queries and external API calls. | 🐢 Slow |
| **Feature Test** | Delivery | Routing, Validation, and JSON response shapes. | 🐢 Slow |

---

## **1. Testing the Domain (The "Pure" Test)**
Since the Domain layer is framework-independent, you don't need `TestCase`. You can use pure PHPUnit.

**Example: Testing an Entity**
```php
public function test_transaction_cannot_be_cancelled_if_already_completed()
{
    $entity = new TransactionEntity(id: '123', status: 'completed');
    
    $this->expectException(DomainException::class);
    
    $entity->cancel(); // Should throw exception
}
```

---

## **2. Testing Application Use Cases**
Use Cases depend on **Interfaces**, not concrete Eloquent models. This allows us to "Mock" the database entirely. We test that the Use Case calls the right methods in the right order.

**Example: Mocking the Repository**
```php
public function test_use_case_calls_save_on_repository()
{
    $mockRepo = $this->createMock(TransactionRepositoryInterface::class);
    $mockRepo->expects($this->once())->method('save');

    $useCase = new ProcessPaymentUseCase($mockRepo);
    $useCase->execute(new TransactionDto(['id' => '123']));
}
```

---

## **3. Infrastructure & Feature Tests**
We use these sparingly to ensure the "plumbing" works.
* **Infrastructure Tests:** Verify that your Eloquent Repository actually fetches data from a real (SQLite in-memory) database.
* **Feature Tests:** Verify that your `TransactionController` returns a `200 OK` and that your `FormRequest` validation rules are working.

---

## **Why This Strategy Matters**

1.  **Refactoring Safety:** You can change your database schema in Infrastructure, and as long as your **Mappers** still produce the same **Entity**, your Domain tests will still pass.
2.  **No "Database Fatigue":** You don't need to run `migrate:refresh` for every single test. 90% of your logic is tested with zero database hits.
3.  **Documentation:** Your tests act as a technical specification of how the business rules actually work.

---

## **Running Your Tests**

Morphling 3D modules are designed to be tested using the standard vendor binary:

```bash
# Run all tests
php artisan test

# Run tests for a specific module
php artisan test modules/Transaction/Tests
```