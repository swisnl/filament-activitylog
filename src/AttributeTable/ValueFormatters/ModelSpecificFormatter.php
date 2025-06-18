<?php

namespace Swis\Filament\ActivityLog\AttributeTable\ValueFormatters;

use Stringable;
use Swis\Filament\ActivityLog\AttributeTable\Builder;
use Swis\Filament\ActivityLog\AttributeTable\Contracts\ValueFormatter;

class ModelSpecificFormatter implements ValueFormatter
{
    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Stringable | string | null
    {
        if (! is_a($recordClass, ValueFormatter::class, true)) {
            return null;
        }

        $instance = new $recordClass;

        return $instance->formatAttributeTableValue($builder, $value, $key, $attributes, $recordClass);
    }
}
