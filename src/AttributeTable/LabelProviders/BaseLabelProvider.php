<?php

namespace Swis\Filament\Activitylog\AttributeTable\LabelProviders;

use Filament\Support\Components\Component;
use Swis\Filament\Activitylog\AttributeTable\Contracts\LabelProvider;

abstract class BaseLabelProvider extends Component implements LabelProvider
{
    protected string $evaluationIdentifier = 'labelProvider';

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }
}
