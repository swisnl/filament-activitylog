<?php

namespace Swis\Filament\Activitylog\AttributeTable\ValueFormatters;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Stringable;
use Swis\Filament\Activitylog\AttributeTable\Builder;
use Swis\Filament\Activitylog\AttributeTable\Contracts\ValueFormatter;

class DateTimeCastFormatter implements ValueFormatter
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
            ! in_array($casts[$key], ['datetime', 'immutable_datetime'])
        ) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return CarbonImmutable::instance($value)->format(Table::$defaultDateTimeDisplayFormat);
        }

        if (is_string($value) || is_int($value) || is_float($value)) {
            return CarbonImmutable::parse($value)->format(Table::$defaultDateTimeDisplayFormat);
        }

        return null;
    }
}
