AI AGENT GUIDELINE FOR WRITING METHODS (PHP/Laravel Context)

1. Method Purpose:
   - Clearly state or infer the intent of the method before writing.
   - Use the docblock as a reference if provided and always document each method appropriately.

2. Naming:
   - Use descriptive, camelCase names for methods.
   - Method names should reflect the specific action or purpose, e.g., success(), calculateTotal(), findUserById().

3. Visibility:
   - Use the most restrictive visibility necessary (protected/private when possible; public only if needed).

4. Method Signature:
   - List all relevant arguments with explicit types when possible (e.g., string $name, ?int $id = null).
   - Set default values for optional parameters.
   - Define return types strictly (e.g., : JsonResponse, : array).

5. Docblocks:
   - Always write a clear docblock above the method following this structure:
     /**
      * [Short description of what the method does.]
      *
      * @param  type  $param  [description]
      * @param  type|null  $param2  [description]
      * @return return_type  [description]
      */

6. Return values:
   - Always return meaningful and relevant values.
   - For "success" or API response wrappers, return JsonResponse objects using Laravel’s response()->json helper.
   - Example:
     protected function success($data, ?string $message = null, int $code = 200)
     {
         return response()->json([
             'is_success' => true,
             'message' => $message,
             'data' => $data
         ], $code);
     }

7. Attribute & Property Use:
   - When defining class attributes/properties (e.g., $fillable), use docblocks and list all items descriptively.
   - Use English for all property names and docblocks.
   - Example:
     /**
      * The attributes that are mass assignable.
      *
      * @var array
      */
     protected $fillable = [
         'branch_id',
         'date',
         'is_backdated',
         'reference',
         'description',
         'journal_id',
         'created_by',
     ];

8. Language:
   - Code, comments, docblocks, and variable names should always be in English.

9. Formatting & Style:
   - Follow PSR-12 formatting guidelines (4 spaces per indentation, K&R braces, spaces after commas, etc.).
   - Organize arguments, array items, and return structures for clarity and readability.

10. Readability & Clarity:
    - Group related logic together and avoid unnecessary nesting.
    - Add comments to explain complex or non-obvious logic or business rules, but avoid redundant comments.

11. Emoji Usage:
    - Do not use emojis anywhere in the code, comments, docblocks, method names, or output. Emojis are strictly prohibited.

Apply these guidelines whenever you are generating a new method or property within a PHP/Laravel codebase.