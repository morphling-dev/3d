<?php

namespace Modules\Shared\Application\UseCases;

abstract class BaseUseCase
{
    /**
     * Main contract for all Use Cases.
     * Ensures every business workflow has a consistent entry point.
     *
     * @param  mixed|null  $dto  Data transfer object containing input data (if any)
     * @return mixed  Result of the use case execution
     */
    abstract public function execute(mixed $dto = null): mixed;
}
