<?php

namespace Swis\Filament\Activitylog\AttributeTable\ValueFormatters;

use Illuminate\Contracts\Support\Htmlable;
use Stringable;
use Swis\Filament\Activitylog\AttributeTable\Builder;

class BoolFormatter extends BaseValueFormatter
{
    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Htmlable | Stringable | string | null
    {
        if (! is_bool($value)) {
            return null;
        }

        return $value ? __('filament-activitylog::activitylog.attributes_table.values.yes') : __('filament-activitylog::activitylog.attributes_table.values.no');
    }
}
