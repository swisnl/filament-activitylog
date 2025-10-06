<?php

namespace Swis\Filament\Activitylog\AttributeTable\ValueFormatters;

use Illuminate\Contracts\Support\Htmlable;
use Stringable;
use Swis\Filament\Activitylog\AttributeTable\Builder;

class NullFormatter extends BaseValueFormatter
{
    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Htmlable | Stringable | string | null
    {
        if (! is_null($value)) {
            return null;
        }

        return __('filament-activitylog::activitylog.attributes_table.values.null');
    }
}
