# **Feature Tests (Delivery Layer)**

Feature Tests are a critical part of Morphling 3D's test suite. While unit and integration tests focus on your application's logic and data flow, Feature Tests validate the application's **HTTP entry points**—making sure routes are available, validation is robust, and your API responses are predictable for frontend consumers.

---

## **Purpose of Feature Testing**
In Morphling 3D's architecture, Controllers in the Delivery Layer are intentionally thin, delegating logic to the lower layers. Therefore, Feature Tests should focus on ensuring that:
1. The correct routes exist and are protected by the appropriate middleware (e.g., `auth:api`, `sanctum`).
2. The `FormRequest` validation is enforced, and invalid inputs are rejected before reaching application logic.
3. Successful and failed requests return the expected HTTP status codes and JSON/API contract as per your frontend/backend agreement.

---

## **1. Testing a Successful API Request**

Feature Tests should confirm that a valid user request, when sent to the intended route, is routed through the Controller, passes validation, interacts with the Application layer, and responds with a standardized `ApiResponse`.

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
            'description' => 'Payment for Invoice #101',
        ];

        // Prefer named routes for discovery consistency
        $response = $this->postJson(route('transaction.store'), $payload);

        $response->assertStatus(200)
                 ->assertJsonPath('is_success', true)
                 ->assertJsonPath('message', 'Transaction created successfully');
        
        $this->assertDatabaseHas('transactions', ['amount' => 5000]);
    }
}
```

---

## **2. Testing Validation at the Delivery Boundary**

These tests make sure your `Delivery/Requests` classes stop invalid requests before they reach the domain/app logic.

```php
public function test_it_fails_validation_if_amount_is_missing()
{
    $response = $this->postJson(route('transaction.store'), [
        'currency' => 'IDR',
        // 'amount' is missing
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['amount']);
}
```

---

## **3. Testing View Rendering (for Web Delivery)**

If your module exposes Blade views, Feature Tests should check that the correct template renders and essential data is visible.

```php
public function test_it_renders_the_transaction_index_page()
{
    $response = $this->get(route('transaction.index'));

    $response->assertStatus(200)
             ->assertViewIs('transaction::index')
             ->assertSee('Transaction List');
}
```

---

## **Why Feature Testing Sits at the Top Layer**

* **Middleware Verification:** Confirms real-world access controls (`auth:api`, `sanctum`, etc.) are applied at the route.
* **API Contract Documentation:** Passing feature tests serve as live documentation for frontend/backend expectations.
* **System Wiring Confidence:** Verifies that Morphling 3D's auto-discovery has connected routes, controllers, and use cases as intended.

---

## **Best Practices**

* **Avoid Re-testing Business Logic:** Business rules are already covered by lower-layer tests. In feature tests, check only response shape and HTTP codes.
* **Favor Named Routes:** Use `route('transaction.store')` (or relevant) over hardcoded `/api/transaction`, ensuring route auto-discovery is tested.
* **Mock Heavy Use Cases (if needed):** For use cases with expensive external dependencies, consider mocking to keep feature tests fast and isolated.
