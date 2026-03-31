<?php

namespace Modules\Shared\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class BaseModel extends Model
{
    use HasFactory;

    /**
     * The attributes that are not mass assignable.
     *
     * Protects against mass assignment vulnerabilities by default.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Prepare a date for array / JSON serialization in a consistent ISO 8601 format.
     *
     * @param  \DateTimeInterface  $date  The date instance to format.
     * @return string  The serialized date string in 'Y-m-d H:i:s' format.
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
