<?php

namespace Swis\Filament\Activitylog\AttributeTable\ValueFormatters;

use Stringable;
use Swis\Filament\Activitylog\AttributeTable\Builder;
use Swis\Filament\Activitylog\AttributeTable\Contracts\HasValue;
use Swis\Filament\Activitylog\AttributeTable\Contracts\ValueFormatter;

class HasValueFormatter implements ValueFormatter
{
    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Stringable | string | null
    {
        if (! $value instanceof HasValue) {
            return null;
        }

        return $value->getAttributeTableValue();
    }
}
