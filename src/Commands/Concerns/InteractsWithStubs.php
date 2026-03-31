<?php

namespace Morphling\ThreeD\Commands\Concerns;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait InteractsWithStubs
{
    /**
     * Resolve the full path to a stub file.
     *
     * Checks for a user-defined stub in the project. Falls back to the package stub if none is found.
     *
     * @param  string  $stub  The name of the stub file (without .stub extension)
     * @return string  The resolved absolute path to the stub file
     */
    protected function resolveStubPath(string $stub): string
    {
        $stubName = Str::finish($stub, '.stub');
        $customPath = base_path("stubs/morphling-3d/{$stubName}");

        if (File::exists($customPath)) {
            return $customPath;
        }

        return __DIR__ . "/../../../stubs/{$stubName}";
    }

    /**
     * Replace all placeholders in the stub with provided data.
     *
     * Given an array of variables, replaces all instances of {{ variable }} and {{variable}} in the stub with actual values.
     *
     * @param  string  $stubPath  The path to the stub file
     * @param  array   $variables Associative array of variables for replacement
     * @return string  The stub content with placeholders replaced by their respective values
     */
    protected function buildStubContent(string $stubPath, array $variables): string
    {
        $content = File::get($stubPath);

        foreach ($variables as $key => $value) {
            // Support placeholder formats: {{ variable }} and {{variable }}
            $content = str_replace(
                [
                    '{{ ' . $key . ' }}',
                    '{{' . $key . '}}'
                ],
                $value,
                $content
            );
        }

        return $content;
    }
}
