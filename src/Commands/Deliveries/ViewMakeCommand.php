<?php

namespace Morphling\ThreeD\Commands\Deliveries;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class ViewMakeCommand extends BaseGeneratorCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'module:make-view {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Blade view for the module';

    /**
     * The type of resource being generated.
     *
     * @var string
     */
    protected $type = 'view';

    /**
     * Get the destination path for the generated Blade view.
     *
     * Generates the full Blade file path with .blade.php extension,
     * transforming the given class-like namespace into a filesystem path.
     *
     * @param  string  $name  The fully qualified class-like name.
     * @return string  The resolved Blade view file path.
     */
    protected function getPath($name): string
    {
        $basePath = config('3d.base_path', base_path('modules'));
        $baseNamespace = config('3d.base_namespace', 'Modules');

        // Remove the base namespace from the name, convert to path
        $relativePath = str_replace(
            '\\',
            '/',
            \Illuminate\Support\Str::replaceFirst($baseNamespace . '\\', '', $name)
        );

        // Always use .blade.php as extension
        return $basePath . '/' . $relativePath . '.blade.php';
    }

    /**
     * Build the Blade view class contents.
     *
     * Stub content is returned after placeholder replacement.
     * This prevents enforcing PHP class structures (namespace, class keywords).
     *
     * @param  string  $name  The provided name argument.
     * @return string  The final stub content.
     */
    protected function buildClass($name): string
    {
        $stub = $this->files->get($this->getStub());

        return $this->replacePlaceholders($stub);
    }

    /**
     * Replace custom placeholders within the Blade stub.
     *
     * Fills out all custom stub placeholders such as module name, module kebab, and view name.
     *
     * @param  string  $stub  The Blade stub content.
     * @return string  The stub with placeholders replaced.
     */
    protected function replacePlaceholders(string $stub): string
    {
        $moduleDetails = $this->getModuleInfo($this->argument('module'));

        $replacements = [
            '{{ module }}'       => $moduleDetails['name'],
            '{{ module_kebab }}' => $moduleDetails['name_kebab'],
            '{{ view_name }}'    => $this->argument('name'),
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );
    }
}
