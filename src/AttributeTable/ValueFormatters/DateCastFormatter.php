<?php

namespace Swis\Filament\ActivityLog\AttributeTable\ValueFormatters;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Stringable;
use Swis\Filament\ActivityLog\AttributeTable\Builder;
use Swis\Filament\ActivityLog\AttributeTable\Contracts\ValueFormatter;

class DateCastFormatter implements ValueFormatter
{
    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Stringable | string | null
    {
        if (! is_a($recordClass, Model::class, true)) {
            return null;
        }

        $instance = new $recordClass;
        $casts = $instance->getCasts();

        if (
            ! array_key_exists($key, $casts) ||
            ! in_array($casts[$key], ['date', 'immutable_date'])
        ) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return CarbonImmutable::instance($value)->format(Table::$defaultDateDisplayFormat);
        }

        if (is_string($value) || is_int($value) || is_float($value)) {
            return CarbonImmutable::parse($value)->format(Table::$defaultDateDisplayFormat);
        }

        return null;
    }
}
