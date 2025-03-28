<?php

namespace Swis\Filament\Activitylog\Facades;

use Illuminate\Support\Facades\Facade;
use Swis\Filament\Activitylog\FilamentActivitylogManager;

/**
 * @method static \Swis\Filament\Activitylog\AttributeTable\Builder attributeTableBuilder()
 * @method static void registerAttributeTableLabelProvider(\Closure | \Swis\Filament\Activitylog\AttributeTable\Contracts\LabelProvider $provider, int $priority = 0)
 * @method static void registerAttributeTableValueFormatter(\Closure | \Swis\Filament\Activitylog\AttributeTable\Contracts\ValueFormatter $formatter, int $priority = 0)
 */
class FilamentActivitylog extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FilamentActivitylogManager::class;
    }
}
