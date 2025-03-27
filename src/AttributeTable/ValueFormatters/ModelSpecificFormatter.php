<?php

namespace Swis\Filament\Activitylog\AttributeTable\ValueFormatters;

use Stringable;
use Swis\Filament\Activitylog\AttributeTable\Builder;
use Swis\Filament\Activitylog\AttributeTable\Contracts\ValueFormatter;

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
