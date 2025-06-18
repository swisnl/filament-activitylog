<?php

namespace Swis\Filament\ActivityLog\AttributeTable\ValueFormatters;

use Stringable;
use Swis\Filament\ActivityLog\AttributeTable\Builder;
use Swis\Filament\ActivityLog\AttributeTable\Contracts\ValueFormatter;

class BoolFormatter implements ValueFormatter
{
    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Stringable | string | null
    {
        if (! is_bool($value)) {
            return null;
        }

        return $value ? __('filament-activitylog::activitylog.attributes_table.values.yes') : __('filament-activitylog::activitylog.attributes_table.values.no');
    }
}
