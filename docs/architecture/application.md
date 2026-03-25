# Application

The **Application** layer coordinates use cases. It usually:

- orchestrates domain operations
- translates input (DTOs) into domain-friendly shapes
- returns response models / DTO-ready outputs

## What Morphling 3D Generates

- `module:make-usecase` generates `Application/UseCases/*`
- `module:make-dto` generates `Application/DTOs/*`

## Example: Use Case Output Shape

The Use Case stub returns a structured result (excerpt):

```php
public function execute(mixed $dto = null): array
{
    return [
        'is_success' => true,
        'message'    => 'Execution successful for {{ module }}',
        'data'       => [
            'module' => '{{ module }}',
            'action' => '{{ class }}',
        ],
    ];
}
```

## Navigation

- [Domain Responsibilities](#/architecture/domain)
- [Infrastructure Responsibilities](#/architecture/infrastructure)
- [Delivery Responsibilities](#/architecture/delivery)

