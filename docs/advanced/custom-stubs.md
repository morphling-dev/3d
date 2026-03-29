# **Customizing Stubs**

The power of Morphling 3D lies in its **Generators**. However, every team has its own coding style—perhaps you prefer `strict_types`, specific PHPDoc blocks, or different base classes. 

Instead of manually editing every file after it's generated, you can modify the "DNA" of your modules by customizing the **Stubs**.

---

## **How Stub Overriding Works**

When you run a command like `php artisan module:make-usecase`, the engine looks for the template in two places:
1.  **Local Folder:** `stubs/morphling/` (Your project root)
2.  **Package Folder:** `vendor/morphling/3d/stubs/` (The default)

If the engine finds a file in your local `stubs/` folder, it will use that instead of the default.

---

## **1. Publishing the Stubs**
To start customizing, you must first "export" the default stubs into your project so you can edit them.

```bash
php artisan vendor:publish --tag=morphling-stubs
```

This will create a new directory: `{project-root}/stubs/morphling/`.

---

## **2. Anatomy of a Stub File**
Open a file like `stubs/morphling/usecase.stub`. You will see placeholders wrapped in double curly braces:

```php
<?php

namespace {{ namespace }};

use Modules\Shared\Application\UseCases\BaseUseCase;

class {{ class }} extends BaseUseCase
{
    public function execute(mixed $dto = null): array
    {
        // Your custom boilerplate here...
        return [
            'is_success' => true,
            'message' => 'Action completed',
        ];
    }
}
```

### **Common Placeholders**
| Placeholder | Description | Example |
| :--- | :--- | :--- |
| `{{ namespace }}` | The PSR-4 namespace based on the layer. | `Modules\Transaction\Application\UseCases` |
| `{{ class }}` | The class name you provided + suffix. | `ProcessPaymentUseCase` |
| `{{ module }}` | The StudyCase name of the module. | `Transaction` |
| `{{ module_snake }}`| The snake_case version of the module name. | `transaction` |

---

## **3. Example: Adding `strict_types` Globally**
If your team requires type safety, you can edit every `.stub` file in your `stubs/morphling/` folder to include the declaration at the top:

```php
<?php

declare(strict_types=1);

namespace {{ namespace }};
...
```

Now, every single file generated from that point forward will include `declare(strict_types=1);` automatically.

---

## **Why This Matters**

* **Enforce Standards:** Ensure every developer uses the same base classes (e.g., `BaseEntity`) and the same return types.
* **Boilerplate Reduction:** If your project always requires a specific trait (like `HasAuditLogs`) on every Eloquent model, add it to `model.stub`.
* **Automation:** It turns the "Morphling way" into "Your team's way."

---

## **Best Practices**

* **Don't delete placeholders:** Removing `{{ namespace }}` or `{{ class }}` will cause the generator to produce broken PHP files.
* **Keep it generic:** Stubs should contain the *minimum* code needed for that component to function. Avoid adding specific business logic to a stub.
* **Version Control:** Commit your `stubs/morphling/` folder to Git so the entire team uses the same templates.

---

## **Troubleshooting**

### "I edited the stub, but the generated file hasn't changed."
1. Ensure the file is in `stubs/morphling/` and not just `stubs/`.
2. Check the filename matches exactly (e.g., `usecase.stub`, `entity.stub`).
3. If you have a config cache, run `php artisan config:clear`.