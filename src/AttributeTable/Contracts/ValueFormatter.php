<?php

namespace Swis\Filament\Activitylog\AttributeTable\Contracts;

use Stringable;
use Swis\Filament\Activitylog\AttributeTable\Builder;

interface ValueFormatter
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Stringable | string | null;
}
