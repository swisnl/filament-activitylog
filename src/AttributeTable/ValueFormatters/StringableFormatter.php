<?php

namespace Swis\Filament\ActivityLog\AttributeTable\ValueFormatters;

use Stringable;
use Swis\Filament\ActivityLog\AttributeTable\Builder;
use Swis\Filament\ActivityLog\AttributeTable\Contracts\ValueFormatter;

class StringableFormatter implements ValueFormatter
{
    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Stringable | string | null
    {
        if (! $value instanceof Stringable) {
            return null;
        }

        return $value;
    }
}
