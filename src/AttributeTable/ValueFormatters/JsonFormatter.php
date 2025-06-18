<?php

namespace Swis\Filament\ActivityLog\AttributeTable\ValueFormatters;

use Stringable;
use Swis\Filament\ActivityLog\AttributeTable\Builder;
use Swis\Filament\ActivityLog\AttributeTable\Contracts\ValueFormatter;

class JsonFormatter implements ValueFormatter
{
    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Stringable | string | null
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
