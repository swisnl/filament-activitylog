<?php

namespace Swis\Filament\Activitylog\AttributeTable\ValueFormatters;

use Stringable;
use Swis\Filament\Activitylog\AttributeTable\Builder;
use Swis\Filament\Activitylog\AttributeTable\Contracts\ValueFormatter;

class NullFormatter implements ValueFormatter
{
    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Stringable | string | null
    {
        if (! is_null($value)) {
            return null;
        }

        return __('filament-activitylog::activitylog.attributes_table.values.null');
    }
}
