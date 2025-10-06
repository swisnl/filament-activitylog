<?php

namespace Swis\Filament\Activitylog\AttributeTable\ValueFormatters;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Stringable;
use Swis\Filament\Activitylog\AttributeTable\Builder;

class HasLabelFormatter extends BaseValueFormatter
{
    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Htmlable | Stringable | string | null
    {
        if (! $value instanceof HasLabel) {
            return null;
        }

        return $value->getLabel();
    }
}
