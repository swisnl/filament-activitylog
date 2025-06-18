<?php

namespace Swis\Filament\ActivityLog\Facades;

use Illuminate\Support\Facades\Facade;
use Swis\Filament\ActivityLog\FilamentActivityLogManager;

/**
 * @method static \Swis\Filament\ActivityLog\AttributeTable\Builder attributeTableBuilder()
 * @method static \Swis\Filament\ActivityLog\EntryContent\EntryContentManager entryContentManager()
 * @method static void registerAttributeTableLabelProvider(\Closure | \Swis\Filament\ActivityLog\AttributeTable\Contracts\LabelProvider $provider, int $priority = 0)
 * @method static void registerAttributeTableValueFormatter(\Closure | \Swis\Filament\ActivityLog\AttributeTable\Contracts\ValueFormatter $formatter, int $priority = 0)
 * @method static void registerEntryContentViewResolver(\Closure | \Swis\Filament\ActivityLog\EntryContent\Contracts\ViewResolver $resolver, int $priority = 0)
 * @method static void registerEntryContentEventViewResolver(string $event, string $view, int $priority = 0)
 */
class FilamentActivityLog extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FilamentActivityLogManager::class;
    }
}
