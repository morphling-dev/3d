# **Testing Strategy Overview**

Testing in a **Morphling 3D** environment is designed to support modular, isolated, and fast feedback cycles—unlike monolithic, database-heavy traditional Laravel testing. By enforcing a clean 4-layer separation, most business and orchestration logic can be validated without a full Laravel application boot or real database access.

This enables a test suite that is substantially **faster** and more focused on business value than large feature tests alone.

---

## **The Testing Pyramid**

Morphling 3D flips the traditional Laravel testing emphasis. Rather than relying mainly on "feature" or "integration" tests (which are slow and require full application/system bootstrapping), the majority of testing effort is focused on **unit tests** in the Domain and Application layers.

| Test Type           | Layer Target   | Focus                                                  | Speed     |
| :------------------ | :-------------| :------------------------------------------------------| :-------- |
| **Unit Test**       | Domain        | Business rules inside Entities and Value Objects        | ⚡ Instant|
| **Unit/Mock Test**  | Application   | Use Case orchestration (mocking Repositories/Services) | ⚡ Fast    |
| **Integration Test**| Infrastructure| Actual DB queries, external dependencies (APIs, etc)   | 🐢 Slower |
| **Feature Test**    | Delivery      | HTTP endpoints, request validation, JSON structures    | 🐢 Slower |

**Guiding Principle:**  
> Most business logic changes and refactoring should be covered by fast, database-independent unit tests.

---

## **1. Testing the Domain (The "Pure" Test)**

The Domain layer in Morphling 3D is framework- and storage-agnostic. Testing here doesn't require Laravel's `TestCase` or database setup—just vanilla PHPUnit is enough.

**Example: Testing a Domain Entity**
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

Application Use Cases depend on abstractions (i.e., interfaces for repositories, services, etc.). This allows tests to **mock everything** outside the use case, removing infrastructure dependencies.

**Example: Mocking a Repository**
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

These tests require infrastructure or interact with the framework. They should be used sparingly, focusing only on integration points.

* **Infrastructure Tests:** Verify that your Eloquent Repository/implementations interact correctly with the database (commonly using in-memory SQLite).
* **Feature Tests:** Check that routing, HTTP status, validation, and response format in Delivery/Controller layers are as expected.

---

## **Why This Strategy?**

1. **Safe Refactoring:** As long as your mappers and contracts remain consistent, you can evolve schemas or even switch databases—without breaking core business logic tests.
2. **Faster Feedback:** 90% of logic runs in pure PHP tests (no database), reducing friction and bottlenecks.
3. **Documented Business Rules:** Tests at the Domain and Application layer become the executable specification of your rules and flows.
4. **Reduced Boilerplate:** Most tests do not require booting the whole Laravel framework or rolling back migrations between runs.

---

## **Running Your Tests**

Morphling 3D modules are fully compatible with Laravel's standard testing tools:

```bash
# Run all tests across your app
php artisan test

# Run tests for a specific module
php artisan test modules/Transaction/Tests
```

Tip: Organize your `Tests` directory inside each module according to its architecture (`Domain`, `Application`, etc.), following the structure you use for your main code.

---
