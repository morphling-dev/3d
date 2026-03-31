<?php

namespace Modules\Shared\Domain\Entities;

abstract class BaseEntity
{
    /**
     * Converts the entity's properties to an associative array for use in mappers or APIs.
     *
     * @return array The entity as an associative array.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
