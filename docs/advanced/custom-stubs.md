# **Customizing Stubs**

Morphling 3D's real power comes from its flexible and predictable **Generators**. But every team has its own standards — maybe your team uses `declare(strict_types=1);` everywhere, adds specific PHPDocs, or uses custom base classes or traits.

Rather than manually editing every file after it's generated, you can change the “DNA” of your modules by customizing the generator **Stubs**.

---

## **How Stub Overriding Works**

When you run a command such as `php artisan 3d:make-usecase`, the generator resolves the stub by searching these locations in order:
1.  **Local Project Folder:** `stubs/morphling/` (in your project root)
2.  **Package Defaults:** `vendor/morphling-dev/3d/stubs/` (the package's built-in stubs)

If a file with the exact same name exists under your local `stubs/morphling/`, Morphling 3D will always prefer your version over the default.

---

## **1. Publishing the Stubs**

To get started, publish the default stubs into your project for customization:

```bash
php artisan vendor:publish --tag=morphling-stubs
```

This will copy the stubs into `{project-root}/stubs/morphling/`.

---

## **2. Anatomy of a Stub File**

Open a stub such as `stubs/morphling/usecase.stub` and you’ll see placeholders wrapped in double curly braces:

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
| Placeholder           | Description                                   | Example                                    |
| :---                  | :---                                          | :---                                       |
| `{{ namespace }}`     | PSR-4 namespace for the file's layer          | `Modules\Accounting\Application\UseCases`   |
| `{{ class }}`         | The generated class name with suffix          | `ProcessInvoiceUseCase`                    |
| `{{ module }}`        | StudlyCase module name                        | `Accounting`                               |
| `{{ module_snake }}`  | Snake_case module name                        | `accounting`                               |

---

## **3. Example: Adding `strict_types` Globally**

If your project enforces strict types, just add the declaration to the top of every `.stub` in `stubs/morphling/`:

```php
<?php

declare(strict_types=1);

namespace {{ namespace }};
...
```

This way, every file you generate from now on will include `declare(strict_types=1);` automatically.

---

## **Why Customizing Stubs Is Important**

* **Standardization:** Guarantees every developer uses the same base classes, return types, PHPDocs, etc.
* **Reduced Boilerplate:** If every Eloquent model needs a trait (e.g., `HasAuditLogs`), just add it to `model.stub`.
* **Automation:** You define the exact boilerplate baked into every generated file so Morphling 3D matches *your* team standards.

---

## **Best Practices**

* **Never remove required placeholders:** Removing `{{ namespace }}` or `{{ class }}` will cause the generator to write invalid PHP files.
* **Stay generic:** Stubs should have only the minimum boilerplate needed — avoid adding module- or business-specific logic.
* **Commit your stubs folder:** Add `stubs/morphling/` to version control so all teammates get the same templates.

---

## **Troubleshooting**

### "I edited the stub, but the generated file hasn't changed."
1. Make sure you placed the file in `stubs/morphling/`, *not* `stubs/` only.
2. Double-check the filename matches exactly (`usecase.stub`, `entity.stub`, etc.).
3. If Laravel's config is cached, run `php artisan config:clear` to ensure changes take effect.