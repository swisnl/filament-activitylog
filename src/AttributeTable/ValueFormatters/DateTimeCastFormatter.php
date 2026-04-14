<?php

namespace Swis\Filament\Activitylog\AttributeTable\ValueFormatters;

use Carbon\CarbonImmutable;
use Closure;
use DateTimeInterface;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Stringable;
use Swis\Filament\Activitylog\AttributeTable\Builder;

class DateTimeCastFormatter extends BaseValueFormatter
{
    protected string | Closure $dateTimeDisplayFormat = 'M j, Y H:i:s';

    public function dateTimeDisplayFormat(string | Closure $format): static
    {
        $this->dateTimeDisplayFormat = $format;

        return $this;
    }

    public function getDateTimeDisplayFormat(): string
    {
        return $this->evaluate($this->dateTimeDisplayFormat);
    }

    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Htmlable | Stringable | string | null
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
            return CarbonImmutable::instance($value)->format($this->getDateTimeDisplayFormat());
        }

        if (is_string($value) || is_int($value) || is_float($value)) {
            return CarbonImmutable::parse($value)->format($this->getDateTimeDisplayFormat());
        }

        return null;
    }
}
