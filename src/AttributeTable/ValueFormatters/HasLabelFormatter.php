<?php

namespace Swis\Filament\ActivityLog\AttributeTable\ValueFormatters;

use Filament\Support\Contracts\HasLabel;
use Stringable;
use Swis\Filament\ActivityLog\AttributeTable\Builder;
use Swis\Filament\ActivityLog\AttributeTable\Contracts\ValueFormatter;

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
