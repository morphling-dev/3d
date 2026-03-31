# Deep-Linking: From Browser to Code

**Deep-Linking** is a developer feature available as part of Morphling 3D's **Delivery Layer**. It lets you jump directly from the browser—where your Blade views are rendered—to the exact file in your code editor, accelerating navigation and feedback cycles.

-----

## Executive Summary

When you generate a new module with Morphling 3D, the default `index.blade.php` view includes a "Dev Toolbar" (or a simple "Open in Editor" button). When clicked, this button triggers a custom URI link to open the server-side Blade file in your editor, precisely where the file lives.

> [!NOTE]
> **Status:** Developer Utility &rightarrow; **Layer:** Delivery (View)

-----

## How Deep-Linking Works

Morphling 3D uses a waterfall approach to maximize compatibility with common editors. The feature attempts to open files in your editor using a sequence of protocol handlers.

### Editor Protocol Priority

1. **Cursor (`cursor://`)** — First, tries Cursor for AI-assisted workflows.
2. **VS Code (`vscode://`)** — Next, falls back to Visual Studio Code, fully supported for Laravel development.
3. **PhpStorm (`phpstorm://`)** — Finally, tries PhpStorm for those using JetBrains tools.

### Browser-to-Editor Handoff

When the button is pressed, a tiny JavaScript snippet attempts to open the file with the top-priority protocol. If the editor doesn't claim focus (the browser doesn't "blur"), it waits about 500ms and tries the next. This maximizes your chance of success even with security restrictions or unregistered handlers.

-----

## Why Deep-Linking Is Important

  * **Direct Navigation:** In a modular structure like Morphling 3D's (`modules/Transaction/Delivery/Views/index.blade.php`), it can be tedious to locate deeply nested files. With deep-link, you jump straight there.
  * **Fast Iteration:** When customizing generated files (like stubs or view templates), iterate instantly—edit, save, refresh, repeat—without directory hunting.
  * **Low Mental Overhead:** Stay "in flow." Avoid context-switching to your file explorer when moving between browser and code.

-----

## How It’s Implemented

The deep-link button is part of the default `view.stub` used for new Delivery views. The system injects the PHP file path into the markup, which is picked up by the client-side script.

```php
{{-- Example output from Delivery/Views/index.blade.php --}}
<button onclick="openCodeEditor('{{ $filePath }}', 1)">
    Open in Editor
</button>
```

> [!TIP]
> **Customization:** Reorder or extend editor protocol preferences by tweaking the `stubs/view.stub` file in your project root. Add your own editor URI as needed.

-----

## Troubleshooting

### Clicking the button does nothing

  * **Protocol Handler Not Found:** Make sure your editor can open files via custom URIs. For VS Code, the "Shell Command: Install 'code' command in PATH" must be set.
  * **Browser Prompts:** Browsers like Chrome may prompt, e.g., “Allow this site to open the vscode link?” — click Allow.

### Opens an unexpected file or errors

  * **Path Mismatch:** On Docker, Vagrant, or remote SSH, the server’s file path may differ from your local system. Set up path mapping in your IDE so local and server file locations correspond.

-----

## Learn More

  * **[Auto-Discovery](/features/auto-discovery.md):** Discover how Morphling 3D wires up views automatically.
  * **[Generators](/features/generators.md):** Customize the look and behavior of deep-linking buttons by altering your stub templates.