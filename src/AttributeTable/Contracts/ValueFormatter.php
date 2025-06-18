<?php

namespace Swis\Filament\ActivityLog\AttributeTable\Contracts;

use Stringable;
use Swis\Filament\ActivityLog\AttributeTable\Builder;

interface ValueFormatter
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Stringable | string | null;
}
