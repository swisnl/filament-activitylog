<?php

namespace Swis\Filament\ActivityLog\AttributeTable\ValueFormatters;

use Illuminate\Database\Eloquent\Model;
use Stringable;
use Swis\Filament\ActivityLog\AttributeTable\Builder;
use Swis\Filament\ActivityLog\AttributeTable\Contracts\ValueFormatter;

class ModelFormatter implements ValueFormatter
{
    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Stringable | string | null
    {
        if (! $value instanceof Model) {
            return null;
        }

        $alias = $value->getMorphClass();
        $class = get_class($value);

        if ($alias === $class) {
            $alias = class_basename($class);
        }

        return $alias . ': ' . $value->getKey();
    }
}
