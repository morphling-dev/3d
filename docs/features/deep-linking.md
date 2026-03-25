# Deep-Linking (Editor Open from Browser)

The generated UI view (`Delivery/Views/index.blade.php`) includes a button and a `openCodeEditor()` function that deep-links into your editor using URL protocols.

The template is sourced from `stubs/view.stub`.

## How it works

Inside `openCodeEditor()` the view builds a list of editor protocols in priority order:

```javascript
const protocols = [
    `cursor://file/${file}:${line}`,
    `vscode://file/${file}:${line}`,
    `phpstorm://open?file=${file}&line=${line}`
];

function tryNext() {
    if (index >= protocols.length || hasOpened) return;
    window.location.href = protocols[index];
    // If the browser loses focus, assume the editor was successfully opened
    const onBlur = () => {
        hasOpened = true;
        window.removeEventListener('blur', onBlur);
    };
    window.addEventListener('blur', onBlur);
    setTimeout(() => {
        window.removeEventListener('blur', onBlur);
        if (!hasOpened) {
            index++;
            tryNext();
        }
    }, 500);
}
```

## Expected behavior

- If `cursor://file/` is supported, the browser will hand off to Cursor immediately.
- If Cursor is not available, it falls back to `vscode://file/`, then `phpstorm://open`.

## Navigation

- [Feature: Auto-Discovery](#/features/auto-discovery)

