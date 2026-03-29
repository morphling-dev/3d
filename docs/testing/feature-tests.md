# **Feature Tests (Delivery Layer)**

**Feature Tests** are the final piece of the Morphling 3D testing puzzle. While our other tests focused on logic and data, Feature Tests verify the **Entry Points**. They ensure that your routes are reachable, your validation rules are strict, and your JSON responses match the frontend's expectations.

---

## **The Goal of Feature Testing**
In a DDD architecture, the Controller is "thin," so the Feature Test should be "thin" too. We are testing the **HTTP Contract**:
1. Does the URL exist and have the correct Middleware (Auth, API)?
2. Does the `FormRequest` reject invalid input (e.g., missing email)?
3. Does the Controller correctly pass data to the Use Case and return the right Status Code (200, 201, 422)?



---

## **1. Testing Successful API Requests**
We want to verify that a valid request travels through the Controller and returns a successful `ApiResponse`.

```php
namespace Modules\Transaction\Tests\Feature\Delivery\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Transaction\Infrastructure\Models\TransactionModel;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_transaction_via_api()
    {
        $payload = [
            'amount' => 5000,
            'currency' => 'IDR',
            'description' => 'Payment for Invoice #101'
        ];

        // Perform the POST request to the auto-discovered route
        $response = $this->postJson('/api/transaction', $payload);

        // Assertions
        $response->assertStatus(200)
                 ->assertJsonPath('is_success', true)
                 ->assertJsonPath('message', 'Transaction created successfully');

        $this->assertDatabaseHas('transactions', ['amount' => 5000]);
    }
}
```

---

## **2. Testing Validation (Delivery Boundaries)**
This ensures your `Delivery/Requests` classes are doing their job before the logic even hits the Application layer.

```php
public function test_it_fails_validation_if_amount_is_missing()
{
    $response = $this->postJson('/api/transaction', [
        'currency' => 'IDR'
        // 'amount' is missing
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['amount']);
}
```

---

## **3. Testing View Delivery (Web)**
If your module has a UI, you test that the namespaced Blade view renders correctly.

```php
public function test_it_renders_the_transaction_index_page()
{
    $response = $this->get('/transaction');

    $response->assertStatus(200)
             ->assertViewIs('transaction::index')
             ->assertSee('Transaction List');
}
```

---

## **Why This is the Final Layer**

* **Security Check:** It’s where you verify that `auth:api` or `Sanctum` middleware is actually protecting your routes.
* **Documentation for Frontend:** Feature tests essentially define the API documentation. If the test passes, the frontend developer knows exactly what JSON to expect.
* **End-to-End Confidence:** It proves that the "Auto-Discovery" successfully wired the routes and that the Controller can talk to the Use Case.

---

## **Best Practices**

* **Don't Over-Test Logic:** If you've already tested the business rules in a Unit Test, don't repeat those assertions here. Just check that the API returns a success/fail code.
* **Use Named Routes:** Always use `route('transaction.store')` instead of hardcoded URLs to ensure your auto-discovery logic is working.
* **Mocking the Use Case (Optional):** If your Use Case triggers a very expensive process (like generating a 50MB PDF), you can mock the Use Case inside the Feature Test to keep the test fast.