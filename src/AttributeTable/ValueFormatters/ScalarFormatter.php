<?php

namespace Swis\Filament\Activitylog\AttributeTable\ValueFormatters;

use Stringable;
use Swis\Filament\Activitylog\AttributeTable\Builder;
use Swis\Filament\Activitylog\AttributeTable\Contracts\ValueFormatter;

class ScalarFormatter implements ValueFormatter
{
    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Stringable | string | null
    {
        if (! is_scalar($value)) {
            return null;
        }

        return (string) $value;
    }
}
