<?php

namespace Swis\Filament\Activitylog\AttributeTable\ValueFormatters;

use Filament\Support\Contracts\HasLabel;
use Stringable;
use Swis\Filament\Activitylog\AttributeTable\Builder;
use Swis\Filament\Activitylog\AttributeTable\Contracts\ValueFormatter;

class HasLabelFormatter implements ValueFormatter
{
    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Stringable | string | null
    {
        if (! $value instanceof HasLabel) {
            return null;
        }

        return $value->getLabel();
    }
}
