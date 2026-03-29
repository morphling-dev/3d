# Deep-Linking: From Browser to Code

**Deep-Linking** is a productivity feature built into the Morphling 3D **Delivery Layer**. It bridges the gap between the rendered UI in your browser and your development environment, allowing you to jump directly to a specific line of code with a single click.

-----

## Executive Summary

When you generate a new module, the default `index.blade.php` view includes a "Dev Toolbar" or an "Open in Editor" button. This uses custom URI schemes to communicate with your IDE (Integrated Development Environment).

> [\!NOTE]
> **Status:** `Developer Utility` | **Layer:** `Delivery (View)`

-----

## How the Handoff Works

The system uses a "Waterfall Fallback" strategy. It attempts to open the file using the most popular modern editors in order of preference.

### The Priority Sequence

1.  **Cursor (`cursor://`):** The primary target for AI-driven development.
2.  **VS Code (`vscode://`):** The industry standard fallback.
3.  **PHPStorm (`phpstorm://`):** The preferred heavy-duty IDE for Laravel.

### The Logic (JavaScript)

The view includes a small script that monitors the browser's `blur` event. If the browser loses focus after a protocol is triggered, the script assumes the editor opened successfully. If not, it tries the next protocol in the list after a 500ms timeout.

-----

## Why This Matters

  * **Instant Context:** In a modular system with hundreds of files, finding `modules/Transaction/Delivery/Views/index.blade.php` manually can be slow. Deep-linking takes you there in 0.5 seconds.
  * **Rapid Prototyping:** As you tweak your **Generators** or **Stubs**, you can instantly verify the output in the browser and jump back to the code to refine it.
  * **Reduced Friction:** It keeps you in the "Flow State" by removing the mental overhead of navigating folder trees.

-----

## Technical Implementation

This feature is injected via the `view.stub`. It automatically detects the server-side file path and passes it to the client-side JavaScript.

```php
{{-- Generated snippet in Delivery/Views/index.blade.php --}}
<button onclick="openCodeEditor('{{ $filePath }}', 1)">
    Open in Editor
</button>
```

> [\!TIP]
> **Pro-Tip:** You can customize the priority order or add your own custom editor protocol by editing the `stubs/view.stub` file in your root directory.

-----

## Troubleshooting

### "Nothing happens when I click the button"

  * **Protocol Registration:** Ensure your editor is registered as a URL handler in your Operating System. (e.g., In VS Code, ensure "Shell Command: Install 'code' command in PATH" has been run).
  * **Browser Permissions:** Some browsers (like Chrome) may show a popup asking: *"Allow this site to open the cursor link?"* You must click **Allow**.

### "It opens the wrong file"

  * **Environment Mismatch:** If you are developing inside a **Docker** container or a remote server (SSH), the file path on the server might not match the path on your local machine. You may need to configure "Path Mapping" in your IDE settings.

-----

## Next Steps

  * **[Auto-Discovery](/features/auto-discovery.md):** Learn how this view becomes reachable in the first place.
  * **[Generators](/features/generators.md):** See how to customize the `view.stub` to change the default look of your deep-link buttons.