<?php

namespace Swis\Filament\Activitylog\AttributeTable\ValueFormatters;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Stringable;
use Swis\Filament\Activitylog\AttributeTable\Builder;

class ModelFormatter extends BaseValueFormatter
{
    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Htmlable | Stringable | string | null
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
