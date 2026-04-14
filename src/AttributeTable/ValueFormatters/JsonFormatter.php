<?php

namespace Swis\Filament\Activitylog\AttributeTable\ValueFormatters;

use Illuminate\Contracts\Support\Htmlable;
use Stringable;
use Swis\Filament\Activitylog\AttributeTable\Builder;

class JsonFormatter extends BaseValueFormatter
{
    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Htmlable | Stringable | string | null
    {
        if (! is_array($value) && ! is_object($value)) {
            return null;
        }

        $result = json_encode($value);
        if ($result === false) {
            return null;
        }

        return $result;
    }
}
