<?php

namespace Swis\Filament\Activitylog\AttributeTable\ValueFormatters;

use Carbon\CarbonImmutable;
use Closure;
use DateTimeInterface;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Stringable;
use Swis\Filament\Activitylog\AttributeTable\Builder;

class DateCastFormatter extends BaseValueFormatter
{
    protected string | Closure $dateDisplayFormat = 'M j, Y';

    public function dateDisplayFormat(string | Closure $format): static
    {
        $this->dateDisplayFormat = $format;

        return $this;
    }

    public function getDateDisplayFormat(): string
    {
        return $this->evaluate($this->dateDisplayFormat);
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
            ! in_array($casts[$key], ['date', 'immutable_date'])
        ) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return CarbonImmutable::instance($value)->format($this->getDateDisplayFormat());
        }

        if (is_string($value) || is_int($value) || is_float($value)) {
            return CarbonImmutable::parse($value)->format($this->getDateDisplayFormat());
        }

        return null;
    }
}
