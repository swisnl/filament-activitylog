<?php

namespace Swis\Filament\Activitylog\AttributeTable\ValueFormatters;

use Filament\Support\Components\Component;
use Swis\Filament\Activitylog\AttributeTable\Contracts\ValueFormatter;

abstract class BaseValueFormatter extends Component implements ValueFormatter
{
    protected string $evaluationIdentifier = 'valueFormatter';

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }
}
